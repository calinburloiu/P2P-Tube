#!/usr/bin/env python

import sys
import config
import cis_exceptions


if __name__ == '__main__':
    transcoder = config.TRANSCODER_CLASS("../data/media/test.ogv")
    transcoder.transcode('ogg', "vorbis", "theora", a_bitrate="192k", a_samplingrate=44100, a_channels=2, v_bitrate="700k", v_framerate=25, v_resolution="800x600", v_dar="16:9")
