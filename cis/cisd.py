#!/usr/bin/env python

import sys
import config
import cis_exceptions


if __name__ == '__main__':
    transcoder = config.TRANSCODER_CLASS("file")
#    transcoder.transcode()
    try:
        print transcoder.tr_container("avi")
    except cis_exceptions.NotImplementedException as e:
        sys.stderr.write(e.value)
