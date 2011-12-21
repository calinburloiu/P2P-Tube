#!/usr/bin/env python

import sys
import os
import fnmatch
import shutil
import time
import threading
from Queue import Queue

import config
import bt


class CIWorker(threading.Thread):
    """
    Content Ingestion Worker. A class which executes content ingestion jobs
    on a separate thread.

    CIWorker shares a Queue with its master where jobs are submitted.
    """

    raw_videos_dir = 'tmp/raw'
    transcoded_videos_dir = 'tmp/media'
    thumbs_dir = 'tmp/thumbs'
    torrents_dir = 'tmp/torrents'

    def __init__(self, queue, bit_torrent):
        """
        Initialize Content Ingestion Worker.

        @param queue a list of dictionaries with the following keys:
        <ul>
            <li>raw_video</li>
            <li>name: a video name which must be a valid file name</li>
            <li>transcode_configs: a list of transcode configuration
            dictionaries having the keys as the parameters of
            api.BaseTranscoder.transcode(...)</li>
            <li>thumbs: string 'random' for extracting a thumbnail
            image from a random video position or a positive integer which
            represents the number of summary thumbnails to be extracted</li>
        </ul>
        """

        threading.Thread.__init__(self, name='CIWorker')

        self.queue = queue
        self.bit_torrent = bit_torrent

    def transfer_in(self, raw_video):
        """
        Transfers a raw video file from the Web Server.

        @param raw_video raw video file name
        """

        file_transfer = config.FILE_TRANSFERER_CLASS( \
                self.raw_videos_dir, config.INPUT_PATH)
        file_transfer.get([raw_video])
        file_transfer.close()

        print '** Transfering in finished.'

    def transcode(self, input_video, video_name, transcode_configs):
        """
        Transcodes a video in each requested formats.

        @param input_video input video file name
        @param video_name a video name which must be a valid file name
        @param transcode_configs a list of dictionaries with format settings
        """

        transcoder = config.TRANSCODER_CLASS( \
                input_file = os.path.join(self.raw_videos_dir, input_video), \
                name = video_name, prog_bin = config.TRANSCODER_BIN)
        transcoder.dest_path = self.transcoded_videos_dir
        
        # Transcode the raw video in each requested format.
        # TODO report partial errors
        for transcode_config in transcode_configs:
            transcode_config['output_file'] = \
                    transcoder.transcode(**transcode_config)

        print '** Transcoding finished.'

    def extract_thumbs(self, input_video, video_name, thumbs):
        """
        Extracts thumbnail images from a video.

        @param input_video input video file name
        @param video_name a video name which must be a valid file name
        @param thumbs use 'random' to extract a thumbnail image from a random
        point of the video or use a positive integer n to extract n summary
        thumbnail
        """

        # TODO report partial errors
        thumb_extractor = config.THUMB_EXTRACTOR_CLASS( \
                input_file = os.path.join(self.raw_videos_dir, input_video), \
                name = video_name, \
                prog_bin = config.THUMB_EXTRACTOR_BIN)
        thumb_extractor.dest_path = self.thumbs_dir
        if thumbs == 'random':
            thumb_extractor.extract_random_thumb()
        elif type(thumbs) is int and thumbs > 0:
            thumb_extractor.extract_summary_thumbs(thumbs)

        print '** Extracting thumbs finished.'

    def seed(self, transcode_configs):
        """
        Creates torrents from the videos passed and then stats seeding them.

        @param transcode_configs a list of dictionaries with format settings
        """

        for transcode_config in transcode_configs:
            # * CREATE TORRENTS FOR EACH TRANSCODED VIDEO
            # Create torrent file.
            bt.create_torrent(transcode_config['output_file'])
            
            # The torrent file is created in the same directory with the
            # source file. Move it to the torrents directory.
            shutil.move(transcode_config['output_file'] + '.tstream', \
                    self.torrents_dir)

            output_file = transcode_config['output_file'] + '.tstream'
            output_file = output_file[(output_file.rindex('/') + 1):]

            # * SEED TORRENTS
            bit_torrent.start_download( \
                    os.path.join(self.torrents_dir, output_file),
                    self.transcoded_videos_dir)

        print '** Creating torrents and seeding finished.'

    def transfer_out(self, local_files, local_path, remote_path):
        """
        Transfers some local files to a remote path of the Web Server.

        @param local_files list local files to transfer
        @param remote_path destination path on the Web Server
        """

        file_transfer = config.FILE_TRANSFERER_CLASS( \
                local_path, remote_path)
        file_transfer.put(local_files)
        file_transfer.close()

        print '** Creating torrents and seeding finished.'

    def remove_files(self, files, path):
        """
        Deletes files from a specified path.
        """

        for f in files:
            os.unlink(os.path.join(path, f))

        print '** Cleaning up finished.'

    def run(self):
        while True:
            job = self.queue.get()

            # * TRANSFER RAW VIDEO IN
            self.transfer_in(job['raw_video'])

            # * TRANSCODE RAW VIDEO
            self.transcode(job['raw_video'], job['name'], \
                    job['transcode_configs'])

            # * EXTRACT THUMBNAIL IMAGES
            if job['thumbs'] != 0:
                self.extract_thumbs(job['raw_video'], job['name'], \
                        job['thumbs'])

            # * CREATE TORRENTS AND START SEEDING OF TRANSCODED VIDEOS
            self.seed(job['transcode_configs'])

            # Torrent files.
            files = [f for f in os.listdir(self.torrents_dir) \
                    if os.path.isfile(os.path.join( \
                            self.torrents_dir, f))]
            torrent_files = fnmatch.filter(files, name + "_*")

            # Thumbnail images files.
            files = [f for f in os.listdir(self.thumbs_dir) \
                    if os.path.isfile(os.path.join( \
                            self.thumbs_dir, f))]
            thumb_files = fnmatch.filter(files, name + "_*")
                
            # Raw video files.
            raw_files = [f for f in os.listdir(self.raw_videos_dir) \
                    if os.path.isfile(os.path.join( \
                            self.raw_videos_dir, f))]

            # * TRANSFER TORRENTS AND THUMBNAIL IMAGES OUT
            self.transfer_out(torrent_files, self.torrents_dir, \
                    config.OUTPUT_TORRENTS_PATH)
            self.transfer_out(thumb_files, self.thumbs_dir, \
                    config.OUTPUT_THUMBS_PATH)
            
            # * CLEANUP RAW VIDEOS AND THUMBNAIL IMAGES
            self.remove_files(raw_files, self.raw_videos_dir)
            self.remove_files(thumb_files, self.thumbs_dir)

            # * JOB FINISHED
            queue.task_done()


if __name__ == '__main__':
    # Jobs queue.
    queue = Queue()

    # The BitTorrent object implements a NextShare (Tribler) BitTorrent client
    # for seeding, downloading etc.
    bit_torrent = bt.BitTorrent()

    # Worker thread.
    ci_worker = CIWorker(queue, bit_torrent)
    ci_worker.daemon = True
    ci_worker.start()

    while True:
        raw_video = sys.stdin.readline().strip()
        if raw_video == 'x':
            break

        container = 'webm'
        a_codec = 'vorbis'
        a_bitrate = '128k'
        v_codec = 'vp8'
        v_bitrate = '480k'
        v_resolution = '640x480'
        
        name = raw_video[:raw_video.rindex('.')]
        transcode_config = {
            'container': container,
            'a_codec': a_codec,
            'a_bitrate': a_bitrate,
            'v_codec': v_codec,
            'v_bitrate': v_bitrate,
            'v_resolution': v_resolution
        }
        thumbs = 4

        job = {
            'raw_video': raw_video,
            'name': name,
            'transcode_configs': [transcode_config],
            'thumbs': thumbs
        }
        
        queue.put(job)

    queue.join()
