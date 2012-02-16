#!/usr/bin/env python

import sys

from api import ffmpeg

if len(sys.argv) != 4:
    sys.stderr.write('usage: ' + sys.argv[0] + ' input_video_file dest_path thumbs_count\n')
    exit(1)
    
if __name__ == '__main__':
    fn = sys.argv[1]
    thumbs_count = int(sys.argv[3])

    video_name = fn[0:fn.rindex('_')]
    video_name = video_name[video_name.rindex('/')+1:]
    
    fte = ffmpeg.FFmpegThumbExtractor(input_file = fn, name = video_name)
    fte.dest_path = sys.argv[2]
    
    fte.extract_summary_thumbs(thumbs_count)