#!/usr/bin/env python

import logger

# Make here all necessary imports required for API classes.
from api import ffmpeg
from api import ftp


# === GENERAL CONFIGURATIONS ===
LOG_LEVEL = logger.LOG_LEVEL_DEBUG
# Security features are experimental, incomplete and may not work.
SECURITY = False
# CIS periodically scans TORRENTS_PATH for new torrents at
# START_DOWNLOADS_INTERVAL seconds. Note that this is a backup measure, because
# CIS normally finds out about new torrents from start_torrents messages.
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
# Web server's URL for content ingestion completion. P2P-Tube uses
# http://<site>/video/cis_completion .
WS_COMPLETION = 'http://p2p-next-03.grid.pub.ro:8081/cis_completion'
#WS_COMPLETION = 'http://koala.cs.pub.ro/video/cis_completion'


# === CIS PATHS ===
# RAW_VIDEOS_PATH, THUMBS_PATH and MEDIA_PATH need not to be in distributed
# file system.
# Temporary directory for uploaded videos transfered from the web server.
RAW_VIDEOS_PATH = '/home/p2p/tmp/raw'
# Temporary directory for image thumbnails.
THUMBS_PATH = '/home/p2p/tmp/thumbs'
# Temporary directory for converted videos.
MEDIA_PATH = '/home/p2p/media'
# TORRENTS_PATH contains torrents files shared by all CIS machines and needs to
# be placed in a distributed file system.
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
# class to be used. It may be useful to complete this parameters if you
# compiled the third-party binaries from sources and you don't have
# administrative privileges to install them.
# Binary of a prgram which retrives audio/video information, like duration.
AVINFO_BIN = None
# Binary of a prgram which transcodes an audio/video file.
TRANSCODER_BIN = None
# Binary of a prgram which extracts thumbnail images from a file.
THUMB_EXTRACTOR_BIN = None
# Binary of a prgram which transfers files between Web Server and CIS.
FILE_TRANSFERER_BIN = None
