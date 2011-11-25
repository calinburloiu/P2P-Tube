#!/usr/bin/env python


"""
Classes derived from BaseTranscoder and BaseThumbExtractor for transcoding of
videos and thumbnail extraction from videos using FFmpeg CLI program.
"""

import base
import api_exceptions
import subprocess
import re
import os

class FFmpegTranscoder(base.BaseTranscoder):
    """
    FFmpeg CLI API for video transcoding.
    """

    prog_bin = "ffmpeg"

    log_file = 'log/FFmpegTranscoder.log'

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

        args = self.prog_bin + ' -y -i "' + self.input_file + '" -f ' + container
        
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
            
        # READ handler for process's output.
        p = subprocess.Popen(args, shell=True, 
                stdout=subprocess.PIPE, stderr=subprocess.STDOUT)
        pipe = p.stdout

        # WRITE handler for logging.
        log = open(self.log_file, 'w')
        log.write(args + '\n')

        while True:
            line = pipe.readline()
            if len(line) == 0:
                break
            log.write(line)

        exit_code = p.wait()
        if exit_code > 0:
            raise api_exceptions.TranscodingException( \
                    'FFmpeg exited with code ' + str(exit_code) + '.')

        log.close()


class FFmpegThumbExtractor(base.BaseThumbExtractor):
    """
    FFmpeg CLI API for video thumbnail extraction.
    """

    prog_bin = "ffmpeg"
    info_prog_bin = "ffprobe"

    log_file = 'log/FFmpegThumbExtractor.log'

    def extract_thumb(self, seek_pos, resolution="120x90", index=0):
        output_file = self.get_output_file_name(index)

        args = self.prog_bin + ' -y -i "' + self.input_file \
                + '" -f rawvideo -vcodec mjpeg' + (' -ss ' + str(seek_pos)) \
                + " -vframes 1 -an -s " + resolution + ' "' \
                + output_file + '"'

        # READ handler for process's output.
        p = subprocess.Popen(args, shell=True,
                stdout=subprocess.PIPE, stderr=subprocess.STDOUT)
        pipe = p.stdout
        
        # WRITE handler for logging.
        log = open(self.log_file, 'w')
        log.write(args + '\n')

        while True:
            line = pipe.readline()
            if len(line) == 0:
                break
            log.write(line)

        exit_code = p.wait()
        if exit_code > 0:
            raise api_exceptions.ThumbExtractionException( \
                    'FFmpeg exited with code ' + str(exit_code) + '.')

        # FFmpeg bug: when no key frame is found from seek_pos to the
        # end of file an empty image file is created.
        if os.path.getsize(output_file) == 0L:
            os.unlink(output_file)
            raise api_exceptions.ThumbExtractionException( \
                    'FFmpeg created an empty file.')

    def get_video_duration(self):
        args = self.info_prog_bin + ' -show_format "' \
                + self.input_file + '"'

        # READ handler for process's output.
        p = subprocess.Popen(args, shell=True,
                stdout=subprocess.PIPE, stderr=open(os.devnull, 'w'))
        pipe = p.stdout

        # Parse ffprobe's output.
        while True:
            line = pipe.readline()
            if len(line) == 0:
                break
            
            # Search for the line which contains duration information.
            m = re.match(r"duration=([\d\.]+)", line)
            if m is not None:
                return float(m.group(1))

        exit_code = p.wait()
        if exit_code > 0:
            raise api_exceptions.ThumbExtractionException( \
                    'FFmpeg exited with code ' + str(exit_code) + '.')

