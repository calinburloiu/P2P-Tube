#!/usr/bin/env python

"""
Base classes for the external programs API.
"""

import cis_exceptions

class BaseTranscoder:
    """
    Abstractization of the API class for the transcoder program. 
    """

    prog_bin = None
    input_file = None

    # Recommended formats.
    containers = {
        "avi": None,
        "flv": None,
        "mp4": None,
        "ogg": None,
        "webm": None,
        "mpegts": None
    }
    a_codecs = {
        "mp3": None,
        "vorbis": None
    }
    v_codecs = {
        "h264": None,
        "theora": None,
        "vp8": None
    }

    def __init__(self, input_file, prog_bin=None):
        self.input_file = input_file
        self.prog_bin = prog_bin

    def transcode(self, container, a_codec, v_codec,
            a_bitrate=None, a_samplingrate=None, a_channels=None,
            v_bitrate=None, v_fraterate=None, v_resolution=None, v_dar=None):
        """
        Transcodes the input file to an audio-video file.

        container: possible values are listed in containers member as keys
        a_codec: possible values are listed in a_codecs member as keys
        v_codec: possible values are listed in v_codecs member as keys
        """

        pass

    def transcode_audio(self, container, a_codec,
            a_bitrate=None, a_samplingrate=None, a_channels=None):
        pass

    def transcode_video(self, container, v_codec,
            v_bitrate=None, v_fraterate=None, v_resolution=None, v_dar=None):
        pass

    def tr_container(self, name):
        """ Translates container API name into external program identifier."""

        if not self.containers.has_key(name) or self.containers[name] == None:
            raise cis_exceptions.NotImplementedException("Container " + name)

        return self.containers[name]
