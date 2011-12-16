#!/usr/bin/env python

# Make here all necessary imports required for API classes.
from api import avhandling
from api import file_transfer


# === FILE TRANSFER CONFIGURATIONS ===
# Path from the Web Server where the raw input video file is stored.
INPUT_PATH = 'Downloads'
# Path from the Web Server where the output torrent files will be stored.
OUTPUT_TORRENTS_PATH = 'Downloads/tmp1'
# Path from the Web Server where the output thumbnail image files will be
# stored.
OUTPUT_THUMBS_PATH = 'Downloads/tmp2'


# === BITTORRENT CONFIGURATIONS ===
#BT_TRACKER = "http://p2p-next-10.grid.pub.ro:6969/announce"
BT_TRACKER = "http://localhost:6969/announce"


# === EXTERNAL PROGRAMS API CLASSES ===
# API class for a prgram which retrives audio/video information, like duration.
AVINFO_CLASS = avhandling.FFmpegAVInfo
# API class for a prgram which transcodes an audio/video file.
TRANSCODER_CLASS = avhandling.FFmpegTranscoder
# API class for a prgram which extracts thumbnail images from a file.
THUMB_EXTRACTOR_CLASS = avhandling.FFmpegThumbExtractor
# API class for a prgram which transfers files between Web Server and CIS.
FILE_TRANSFERER_CLASS = file_transfer.FTPFileTransferer


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
