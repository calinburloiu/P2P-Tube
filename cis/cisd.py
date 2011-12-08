#!/usr/bin/env python

import sys
import config
import os

from BaseLib.Core.API import *

def create_torrent(source):
    """ Creates a torrent file for the video source file. """

    if isinstance(source, unicode):
        usource = source
    else:
        usource = source.decode(sys.getfilesystemencoding())

    duration = config.AVINFO_CLASS.get_video_duration(source, True)

    print config.BT_TRACKER, duration, source

    tdef = TorrentDef()
    tdef.add_content(usource, playtime=duration)
    tdef.set_tracker(config.BT_TRACKER)

    tdef.set_piece_length(32768)

    tdef.finalize()
    tdef.save(source + '.tstream')


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
