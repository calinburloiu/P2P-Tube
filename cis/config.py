#!/usr/bin/env python

# Make here all necessary imports required for API classes.
from api import ffmpeg

# External programs API classes.
TRANSCODER_CLASS = ffmpeg.FFmpegTranscoder
THUMB_EXTRACTER_CLASS = None # TODO
BT_CLIENT_CLASS = None # TODO
FILE_TRANSFERER_CLASS = None # TODO

# External programs binary file. None means default.
TRANSCODER_BIN = None
THUMB_EXTRACTER_BIN = None
BT_CLIENT_BIN = None
FILE_TRANSFERER_BIN = None
