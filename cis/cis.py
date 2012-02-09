#!/usr/bin/env python

import sys
import os
import fnmatch
import shutil
import time
import threading
from Queue import Queue
import web
import json
from web.wsgiserver import CherryPyWSGIServer

import config
import bt
import users
import logger

if config.SECURITY:
    CherryPyWSGIServer.ssl_certificate = "cacert.pem"
    CherryPyWSGIServer.ssl_private_key = "privkey.pem"


class CIWorker(threading.Thread):
    """
    Content Ingestion Worker. A class which executes content ingestion jobs
    on a separate thread.

    CIWorker shares a Queue with its master where jobs are submitted.
    """

    def __init__(self):
        """
        Initialize Content Ingestion Worker.
        """

        threading.Thread.__init__(self, name='CIWorker')

    def transfer_in(self, raw_video):
        """
        Transfers a raw video file from the Web Server.

        @param raw_video raw video file name
        """
        
        logger.log_msg('#%s: transfering in...' % self.job_id)
        
        file_transfer = config.FILE_TRANSFERER_CLASS( \
                config.RAW_VIDEOS_PATH, config.WS_UPLOAD_PATH)            
        file_transfer.get([raw_video])
        file_transfer.close()

    def transcode(self, input_video, video_name, transcode_configs):
        """
        Transcodes a video in each requested formats.

        @param input_video input video file name
        @param video_name a video name which must be a valid file name
        @param transcode_configs a list of dictionaries with format settings
        """

        logger.log_msg('#%s: transcoding...' % self.job_id)
        
        transcoder = config.TRANSCODER_CLASS( \
                input_file = os.path.join(config.RAW_VIDEOS_PATH, input_video), \
                name = video_name, prog_bin = config.TRANSCODER_BIN)
        transcoder.dest_path = config.MEDIA_PATH
        
        # Transcode the raw video in each requested format.
        # TODO report partial errors
        for transcode_config in transcode_configs:
            transcode_config['output_file'] = \
                    transcoder.transcode(**transcode_config)

    def extract_thumbs(self, input_video, video_name, thumbs):
        """
        Extracts thumbnail images from a video.

        @param input_video input video file name
        @param video_name a video name which must be a valid file name
        @param thumbs use 'random' to extract a thumbnail image from a random
        point of the video or use a positive integer n to extract n summary
        thumbnail
        """

        logger.log_msg('#%s: extracting image thumbnails...' % self.job_id)
        
        # TODO report partial errors
        thumb_extractor = config.THUMB_EXTRACTOR_CLASS( \
                input_file = os.path.join(config.RAW_VIDEOS_PATH, input_video), \
                name = video_name, \
                prog_bin = config.THUMB_EXTRACTOR_BIN)
        thumb_extractor.dest_path = config.THUMBS_PATH
        if thumbs == 'random':
            thumb_extractor.extract_random_thumb()
        elif type(thumbs) is int and thumbs > 0:
            thumb_extractor.extract_summary_thumbs(thumbs)

    def seed(self, transcode_configs):
        """
        Creates torrents from the videos passed and then stats seeding them.

        @param transcode_configs a list of dictionaries with format settings
        """
        
        logger.log_msg('#%s: creating torrents and starting seeding...' \
                % self.job_id)

        for transcode_config in transcode_configs:
            # * CREATE TORRENTS FOR EACH TRANSCODED VIDEO
            # Create torrent file.
            bt.create_torrent(transcode_config['output_file'])
            
            # The torrent file is created in the same directory with the
            # source file. Move it to the torrents directory.
            shutil.move(transcode_config['output_file'] + '.tstream', \
                    config.TORRENTS_PATH)

            output_file = transcode_config['output_file'] + '.tstream'
            output_file = output_file[(output_file.rindex('/') + 1):]

            # * SEED TORRENTS
            Server.bit_torrent.start_torrent( \
                    os.path.join(config.TORRENTS_PATH, output_file),
                    config.MEDIA_PATH)
                    
    def transfer_out(self, local_files, local_path, remote_path):
        """
        Transfers some local files to a remote path of the Web Server.

        @param local_files list local files to transfer
        @param remote_path destination path on the Web Server
        """
        
        logger.log_msg('#%s: transfering out...' % self.job_id)

        file_transfer = config.FILE_TRANSFERER_CLASS( \
                local_path, remote_path)
        file_transfer.put(local_files)
        file_transfer.close()

    def remove_files(self, files, path):
        """
        Deletes files from a specified path.
        """
        
        logger.log_msg('#%s: cleaning up...' % self.job_id)

        for f in files:
            os.unlink(os.path.join(path, f))

    def run(self):
        while True:
            job = Server.queue.get()
            self.job_id = job['id']

            # * TRANSFER RAW VIDEO IN
            try:
                self.transfer_in(job['raw_video'])
            except Exception as e:
                logger.log_msg('#%s: error while transferring in: %s' \
                        % (job['id'], str(e)), logger.LOG_LEVEL_FATAL) 
                continue

            # * TRANSCODE RAW VIDEO
            try:
                self.transcode(job['raw_video'], job['name'], \
                        job['transcode_configs'])
            except Exception as e:
                logger.log_msg('#%s: error while transcoding: %s' \
                        % (job['id'], str(e)), logger.LOG_LEVEL_FATAL) 
                continue

            # * EXTRACT THUMBNAIL IMAGES
            if job['thumbs'] != 0:
                try:
                    self.extract_thumbs(job['raw_video'], job['name'], \
                            job['thumbs'])
                except Exception as e:
                    logger.log_msg( \
                            '#%s: error while extracting thumbnail images: %s' \
                            % (job['id'], str(e)), logger.LOG_LEVEL_FATAL) 
                    continue

            # * CREATE TORRENTS AND START SEEDING OF TRANSCODED VIDEOS
            self.seed(job['transcode_configs'])

            # Torrent files.
            files = [f for f in os.listdir(config.TORRENTS_PATH) \
                    if os.path.isfile(os.path.join( \
                            config.TORRENTS_PATH, f))]
            torrent_files = fnmatch.filter(files, job['name'] + "_*")

            # Thumbnail images files.
            files = [f for f in os.listdir(config.THUMBS_PATH) \
                    if os.path.isfile(os.path.join( \
                            config.THUMBS_PATH, f))]
            thumb_files = fnmatch.filter(files, job['name'] + "_*")

            # * TRANSFER TORRENTS AND THUMBNAIL IMAGES OUT
            try:
                self.transfer_out(torrent_files, config.TORRENTS_PATH, \
                        config.WS_TORRENTS_PATH)
                self.transfer_out(thumb_files, config.THUMBS_PATH, \
                        config.WS_THUMBS_PATH)
            except Exception as e:
                logger.log_msg('#%s: error while transferring out: %s' \
                        % (job['id'], str(e)), logger.LOG_LEVEL_FATAL) 
                continue
            
            # * CLEANUP RAW VIDEOS AND THUMBNAIL IMAGES
            self.remove_files([ job['raw_video'] ], config.RAW_VIDEOS_PATH)
            self.remove_files(thumb_files, config.THUMBS_PATH)

            # * JOB FINISHED
            Server.queue.task_done()
            Server.load -= job['weight']


class Server:
    """
    Implementation of the RESTful web service which constitutes the interface
    with the client (web server).
    """

    #def __init__(self):
        #pass
        
    #def __del__(self):
        #pass
    
    def GET(self, request):
        #web.header('Cache-Control', 'no-cache')

        if request == 'get_load':
            resp = {"load": Server.load}
            web.header('Content-Type', 'application/json')
            return json.dumps(resp)
        #elif request == 'shutdown':
            #sys.exit(0)
        elif request == 'test':
            return ''
        else:
            web.badrequest()
            return ""
        

    def POST(self, request):
        if request == 'ingest_content':
            # Read JSON parameters.
            json_data = web.data()
            data = json.loads(json_data)

            # Authenticate user.
            if config.SECURITY and \
                    not self.authenticate(data["username"], data["password"]):
                return "Authentication failed!"

            # Add job weight to CIS load.
            Server.load += data["weight"]

            # Submit job.
            Server.queue.put(data)

            return 'Job submitted.'
        elif request == 'start_torrents':
            # Read JSON parameters.
            json_data = web.data()
            data = json.loads(json_data)
            
            # TODO Verify data
            Server.start_torrents(data)
        elif request == 'stop_torrents':
            # Read JSON parameters.
            json_data = web.data()
            data = json.loads(json_data)
            
            # TODO Verify data
            Server.stop_torrents(data)
        elif request == 'remove_torrents':
            # Read JSON parameters.
            json_data = web.data()
            data = json.loads(json_data)
            
            # TODO Verify data
            Server.stop_torrents(data, True)
        else:
            web.badrequest()
            return ""
    
    @staticmethod
    def start_torrents(torrents=None):
        """
        Scans torrent path for files in order to start download for the files
        that are not already started.
        """
        
        # All torrent files.
        if torrents == None:
            files = [f for f in os.listdir(config.TORRENTS_PATH) \
                    if os.path.isfile(os.path.join( \
                            config.TORRENTS_PATH, f))]
            torrents = fnmatch.filter(files, "*.tstream")

        for torrent_file in torrents:
            Server.bit_torrent.start_torrent( \
                    os.path.join(config.TORRENTS_PATH, torrent_file),
                    config.MEDIA_PATH)
    
    @staticmethod
    def stop_torrents(torrents, remove_content=False):
        for torrent_file in torrents:
            Server.bit_torrent.stop_torrent( \
                    torrent_file, remove_content)

    def authenticate(self, username, password):
        if not config.SECURITY:
            return True
        if users.users[username] == password:
            return True
        else:
            web.forbidden()
            return False


if __name__ == '__main__':
    # The BitTorrent object implements a NextShare (Tribler) BitTorrent
    # client for seeding, downloading etc.
    Server.bit_torrent = bt.BitTorrent()
    Server.queue = Queue()
    Server.load = 0
    
    Server.start_torrents()
    t = threading.Timer(config.START_DOWNLOADS_INTERVAL, \
            Server.start_torrents)
    t.daemon = True
    t.start()
    
    # Worker thread.
    ci_worker = CIWorker()
    ci_worker.daemon = True
    ci_worker.start()

    # Web service.
    urls = ('/(.*)', 'Server')
    app = web.application(urls, globals())
    app.run()
