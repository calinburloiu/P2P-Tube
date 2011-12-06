#!/usr/bin/env python

# Make here all necessary imports required for API classes.
from api import avhandling
from api import file_transfer

# BitTorrent configurations.
BT_TRACKER = "http://p2p-next-10.grid.pub.ro:6969/announce"

# External programs API classes.
AVINFO_CLASS = avhandling.FFmpegAVInfo
TRANSCODER_CLASS = avhandling.FFmpegTranscoder
THUMB_EXTRACTOR_CLASS = avhandling.FFmpegThumbExtractor
FILE_TRANSFERER_CLASS = file_transfer.FTPFileTransferer

# External programs binary file. None means default.
TRANSCODER_BIN = None
THUMB_EXTRACTER_BIN = None
FILE_TRANSFERER_BIN = None
