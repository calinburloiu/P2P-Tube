#!/usr/bin/env python

import sys
import config
import cis_exceptions


if __name__ == '__main__':
#    transcoder = config.TRANSCODER_CLASS(sys.argv[1])
#    transcoder.transcode('webm', "vorbis", "vp8", a_bitrate="128k", a_samplingrate=22050, a_channels=2, v_bitrate="256k", v_framerate=15, v_resolution="320x240", v_dar="4:3")
    
    thumb_extractor = config.THUMB_EXTRACTOR_CLASS(sys.argv[1])
    #print thumb_extractor.get_video_duration()
    #thumb_extractor.extract_random_thumb()
    print thumb_extractor.extract_summary_thumbs(5)
