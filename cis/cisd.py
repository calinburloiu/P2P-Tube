#!/usr/bin/env python

import sys
import config

#
# !! Imports required for create_torrent
#
from BaseLib.Core.API import *
#
#
#

def create_torrent(input_):
    tdef = TorrentDef()
    tdef.add_content(input_, config.AVINFO_CLASS.get_video_duration(input_))
    tdef.set_tracker(config.BT_TRACKER)

    tdef.set_piece_length(32768)

    tdef.finalize()
    tdef.save(input_ + ".tstream")

    print 'READY!', config.BT_TRACKER, config.AVINFO_CLASS.get_video_duration(input_)

if __name__ == '__main__':
    pass
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

    create_torrent(sys.argv[1])
