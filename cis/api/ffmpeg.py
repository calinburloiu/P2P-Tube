#!/usr/bin/env python

"""
Classes derived from BaseTranscoder and BaseThumbExtractor for transcoding of
videos and thumbnail extraction from videos using FFmpeg CLI program.
"""

import base
import cis_exceptions
import subprocess
import os

class FFmpegTranscoder(base.BaseTranscoder):
    """
    FFmpeg CLI API for video transcoding.
    """

    prog_bin = "ffmpeg"

    log_file = 'log/ffmpeg.log'

    containers = {
        "avi": "avi",
        "flv": "flv",
        "mp4": "mp4",
        "ogg": "ogg",
        "webm": "webm",
        "mpegts": "mpegts"
    }
    a_codecs = {
        "mp3": "libmp3lame",
        "vorbis": "libvorbis"
    }
    v_codecs = {
        "h264": "libx264",
        "theora": "libtheora",
        "vp8": "libvpx"
    }

    def _transcode(self, container, a_codec=None, v_codec=None,
            a_bitrate=None, a_samplingrate=None, a_channels=None,
            v_bitrate=None, v_framerate=None, v_resolution=None, v_dar=None):

        args = self.prog_bin + ' -i "' + self.input_file + '" -f ' + container
        
        # Audio
        if a_codec != None:
            args += ' -acodec ' + a_codec
            if a_bitrate != None:
                args += ' -ab ' + str(a_bitrate)
            if a_samplingrate != None:
                args += ' -ar ' + str(a_samplingrate)
            if a_channels != None:
                args += ' -ac ' + str(a_channels)
        
        # Video
        if v_codec != None:
            args += ' -vcodec ' + v_codec
            # Video codec specific options.
            if v_codec == 'libx264':
                args += ' -vpre normal'
            if v_bitrate != None:
                args += ' -b ' + str(v_bitrate)
            if v_framerate != None:
                args += ' -r ' + str(v_framerate)
            if v_resolution != None:
                args += ' -s ' + v_resolution
            if v_dar != None:
                args += ' -aspect ' + v_dar
        
        # Output file.
        args += ' "' + self.output_file + '"'
        try:
            os.unlink(self.output_file)
        except OSError:
            pass
            
        # READ handler for process's output.
        p = subprocess.Popen(args, shell=True, 
                stdout=subprocess.PIPE, stderr=subprocess.STDOUT)
        pipe = p.stdout

        # WRITE handler for logging.
        log = open(self.log_file, 'w')

        while True:
            line = pipe.readline()
            if len(line) == 0:
                break
            log.write(line)

        if p.poll() > 0:
            raise cis_exceptions.TranscodingException

        log.close()
