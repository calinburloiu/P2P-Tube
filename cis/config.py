#!/usr/bin/env python

import logger

# Make here all necessary imports required for API classes.
from api import ffmpeg
from api import ftp


# === GENERAL CONFIGURATIONS ===
LOG_LEVEL = logger.LOG_LEVEL_DEBUG
SECURITY = False
START_DOWNLOADS_INTERVAL = 24 * 3600.0 # Once a day


# === FILE TRANSFER CONFIGURATIONS ===
# Path from the Web Server where the raw input video file is stored.
WS_UPLOAD_PATH = 'devel/data/upload'
# Path from the Web Server where the output torrent files will be stored.
WS_TORRENTS_PATH = 'devel/data/torrents'
# Path from the Web Server where the output thumbnail image files will be
# stored.
WS_THUMBS_PATH = 'devel/data/thumbs'


# === URLS ===
#BT_TRACKER = "http://p2p-next-10.grid.pub.ro:6969/announce"
# BitTorrent tracker URL.
BT_TRACKER = 'http://p2p-next-10.grid.pub.ro:6969/announce'
# Web server's URL for content ingestion completion.
WS_COMPLETION = 'http://p2p-next-03.grid.pub.ro:8081/cis_completion'
#WS_COMPLETION = 'http://koala.cs.pub.ro/video/cis_completion'


# === CIS PATHS ===
RAW_VIDEOS_PATH = 'tmp/raw'
THUMBS_PATH = 'tmp/thumbs'
MEDIA_PATH = '/home/p2p/export/p2p-tube/media'
# In a distributed file system for multi-CIS.
TORRENTS_PATH = '/home/p2p/export/p2p-tube/torrents'


# === EXTERNAL PROGRAMS API CLASSES ===
# API class for a prgram which retrives audio/video information, like duration.
AVINFO_CLASS = ffmpeg.FFprobeAVInfo
# API class for a prgram which transcodes an audio/video file.
TRANSCODER_CLASS = ffmpeg.FFmpegTranscoder
# API class for a prgram which extracts thumbnail images from a file.
THUMB_EXTRACTOR_CLASS = ffmpeg.FFmpegThumbExtractor
# API class for a prgram which transfers files between Web Server and CIS.
FILE_TRANSFERER_CLASS = ftp.FTPFileTransferer


# === EXTERNAL PROGRAMS BINARY FILES ===
# Set this values to None if you want default values provided by the API
# class to be used.
# Binary of a prgram which retrives audio/video information, like duration.
AVINFO_BIN = None
# Binary of a prgram which transcodes an audio/video file.
TRANSCODER_BIN = None
# Binary of a prgram which extracts thumbnail images from a file.
THUMB_EXTRACTOR_BIN = None
# Binary of a prgram which transfers files between Web Server and CIS.
FILE_TRANSFERER_BIN = None
