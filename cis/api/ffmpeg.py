#!/usr/bin/env python

import base

class FFmpegTranscoder(base.BaseTranscoder):
    prog_bin = 'ffmpeg'
    input_file = None

    def transcode(self, container, a_codec, v_codec,
            a_bitrate=None, a_samplingrate=None, a_channels=None,
            v_bitrate=None, v_fraterate=None, v_resolution=None, v_dar=None):
        pass

    def transcode_audio(self, container, a_codec,
            a_bitrate=None, a_samplingrate=None, a_channels=None):
        pass

    def transcode_video(self, container, v_codec,
            v_bitrate=None, v_fraterate=None, v_resolution=None, v_dar=None):
        pass
