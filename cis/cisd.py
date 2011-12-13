#!/usr/bin/env python

import sys
import os
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

    def __init__(self, queue):
        threading.Thread.__init__(self)

        self.queue = queue

    def run(self):
        while True:
            job = self.queue.get()

            # * TRANSFER RAW VIDEO IN
            file_transfer = config.FILE_TRANSFERER_CLASS( \
                    'tmp/raw', config.INPUT_PATH)
            file_transfer.get([job.raw_video])
            file_transfer.close()

            # * TRANSCODE RAW VIDEO
            transcoder = config.TRANSCODER_CLASS(input_file = job.raw_video, \
                    name = job.name, prog_bin = config.TRANSCODER_BIN)
            
            # Transcode the raw video in each requested format.
            for transcode_config in job.transcode_configs:
                transcode_config['output_file'] = transcoder.transcode( \
                       container = transcode_config.container, \ 
                       a_codec = transcode_config.a_codec, \
                       a_bitrate = transcode_config.a_bitrate, \ 
                       a_samplingrate = transcode_config.a_samplingrate, \ 
                       a_channels = transcode_config.a_channels, \ 
                       v_codec = transcode_config.v_codec, \ 
                       v_bitrate = transcode_config.v_bitrate, \ 
                       v_framerate = transcode_config.v_framerate, \ 
                       v_resolution = transcode_config.v_resolution, \ 
                       v_dar = transcode_config.dar)

            # * EXTRACT THUMBNAIL IMAGES
            thumb_extractor = config.THUMB_EXTRACTOR_CLASS( \
                    input_file = job.raw_video, name = job.name, \
                    prog_bin = config.THUMB_EXTRACTOR_BIN)
            # TODO thumbnail extraction type must be got from input
            thumb_extractor.extract_random_thumb()
            print thumb_extractor.extract_summary_thumbs(5)


            queue.task_done()


class TranscodeConfig:
    """
    Structure that contains parameters for a transcoding procedure.
    """

    def __init__(self, container, a_codec, v_codec,
            a_bitrate, a_samplingrate, a_channels,
            v_bitrate, v_framerate, v_resolution, v_dar):

        self.container = container
        self.a_codec = a_codec
        self.v_codec = v_codec
        self.a_bitrate = a_bitrate
        self.a_samplingrate = a_samplingrate
        self.a_channels = a_channels
        self.v_bitrate = v_bitrate
        self.v_framerate = v_framerate
        self.v_resolution = v_resolution
        self.v_dar = v_dar



class Job:
    """
    Structure that contains information about a job.

    Members are documented in the constructor.
    """

    def __init__(self, raw_video, name, transcode_configs):
        """
        @param raw_video the input raw video file name transfered from WS
        @param name video name (must be a valid file name)
        @param transcode_configs a list of TranscodeConfig instances
        """

        self.raw_video = raw_video
        self.name = name
        self.transcode_configs


if __name__ == '__main__':
    # Jobs queue.
    queue = Queue()

    # Worker thread.
    ci_worker = CIWorker(queue)
    ci_worker.daemon = True
    ci_worker.start()

    while True:
        raw_video = sys.stdin.readline().strip()
        if raw_video == 'x':
            break

        job = Job(raw_video)
        queue.put(job)

    queue.join()




#    transcoder = config.TRANSCODER_CLASS(sys.argv[1])
#    transcoder.transcode('webm', "vorbis", "vp8", a_bitrate="128k", a_samplingrate=22050, a_channels=2, v_bitrate="256k", v_framerate=15, v_resolution="320x240", v_dar="4:3")
    
#    thumb_extractor = config.THUMB_EXTRACTOR_CLASS(sys.argv[1])
#    #print thumb_extractor.get_video_duration()
#    #thumb_extractor.extract_random_thumb()
#    print thumb_extractor.extract_summary_thumbs(5)

#    file_transfer = config.FILE_TRANSFERER_CLASS()
#    file_transfer.get(['vim_config.tar.gz'])
#    #file_transfer.put(['cisd.py'])
#    file_transfer.close()

#    create_torrent(sys.argv[1])

#    bt_inst = bt.BitTorrent()
#
#    bt_inst.download(sys.argv[1], '/tmp')
#    bt_inst.download(sys.argv[2], '/tmp')
#
#    print threading.active_count(), threading.enumerate()
#    time.sleep(30)
