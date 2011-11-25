#!/usr/bin/env python

# Make here all necessary imports required for API classes.
from api import avhandling
from api import file_transfer

# External programs API classes.
TRANSCODER_CLASS = avhandling.FFmpegTranscoder
THUMB_EXTRACTOR_CLASS = avhandling.FFmpegThumbExtractor
BT_CLIENT_CLASS = None # TODO
FILE_TRANSFERER_CLASS = file_transfer.FTPFileTransferer

# External programs binary file. None means default.
TRANSCODER_BIN = None
THUMB_EXTRACTER_BIN = None
BT_CLIENT_BIN = None
FILE_TRANSFERER_BIN = None
