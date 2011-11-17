#!/usr/bin/env python

"""
Base classes for the external programs API.
"""

import cis_exceptions
import re

class BaseTranscoder:
    """
    Abstraction of the API class for the transcoder program. 
    """

    prog_bin = None
    input_file = None
    output_file = None
    dest_path = ''
    name = None

    # Recommended formats.
    containers = {
        "avi": None,
        "flv": None,
        "mp4": None,
        "ogg": None,
        "webm": None,
        "mpegts": None
    }
    # File extensions by container. First value is for audio files and the
    # second one is for (audio-)video files.
    extensions = {
        "avi": ["avi", "avi"],
        "flv": ["flv", "flv"],
        "mp4": ["mp4", "mp4"],
        "ogg": ["oga", "ogv"],
        "webm": ["webm", "webm"],
        "mpegts": ["mts", "mts"]
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

    def __init__(self, input_file, name=None, prog_bin=None):
        self.input_file = input_file
        if prog_bin is not None:
            self.prog_bin = prog_bin
        
        if name is None:
            if input_file.find('/') is not -1:
                name = input_file[(input_file.rindex('/')+1):]
            else:
                name = input_file
            if name.find('.') is not -1:
                name = name[:name.rindex('.')]

        self.name = name

    def transcode(self, container, a_codec=None, v_codec=None,
            a_bitrate=None, a_samplingrate=None, a_channels=None,
            v_bitrate=None, v_framerate=None, v_resolution=None, v_dar=None):
        """
        Transcodes the input file to an audio-video file.

        container: possible values are listed in containers member as keys
        a_codec: possible values are listed in a_codecs member as keys
        v_codec: possible values are listed in v_codecs member as keys
        a_bitrate: (numeric) audio bit rate
        a_samplingrate: (numeric) audio sampling rate in Hz
        a_channels: (numeric) number of audio channels
        v_bitrate: (numeric) video bit rate
        v_framerate: (numeric) number of frames per second for a video
        v_resolution: (string) video image size as <width>x<height>
        v_dar: video display aspect ratio as <den>x<num> or float
        """

        # Check parameters.
        if a_codec is None and v_codec is None:
            raise ValueError('No audio or video codec specified.')

        if a_codec is not None and type(a_codec) is not str:
            raise TypeError('Audio codec must be string.')

        if v_codec is not None and type(v_codec) is not str:
            raise TypeError('Video codec must be string.')

        if a_samplingrate is not None and type(a_samplingrate) is not int:
            raise TypeError('Audio sampling rate must be an integer.')

        if a_channels is not None and type(a_channels) is not int:
            raise TypeError('Audio channels parameter must be an integer.')

        if v_framerate is not None and type(v_framerate) is not int:
            raise TypeError('Video frate rate must be an integer.')

        if v_resolution is not None \
                and re.match('[\d]+x[\d]+', v_resolution) is None:
            raise ValueError('Video resolution must be a string like <width>x<height>.')

        if v_dar is not None and (type(v_dar) is not float \
                and re.match('[\d]+:[\d]+', v_dar) is None):
            raise ValueError('Video display aspect ratio must be a float or a string like <den>:<num>.')

        self.output_file = self.dest_path + self.name
        if v_resolution is not None:
            self.output_file += '_'
            self.output_file += v_resolution[(v_resolution.rindex('x')+1):]
            self.output_file += 'p'
        ext = self.tr_extension(container, (v_codec is not None))
        if ext is not None:
            self.output_file += '.' + ext

        self._transcode(self.tr_container(container),
                self.tr_a_codec(a_codec), self.tr_v_codec(v_codec),
                a_bitrate, a_samplingrate, a_channels,
                v_bitrate, v_framerate, v_resolution, v_dar)

    def _transcode(self, container, a_codec=None, v_codec=None,
            a_bitrate=None, a_samplingrate=None, a_channels=None,
            v_bitrate=None, v_framerate=None, v_resolution=None, v_dar=None):
        """
        Called by transcode; must be overridden by a child class which
        effectively transcodes the input file.
        """
        pass

    def tr_container(self, name):
        """ Translates container API name into external program identifier."""

        if not self.containers.has_key(name) or self.containers[name] is None:
            raise cis_exceptions.NotImplementedException("Container " + name)

        return self.containers[name]

    def tr_extension(self, name, video=True):
        """ Translates container API name into file extension."""

        if video is True:
            i = 1
        else:
            i = 0

        if not self.extensions.has_key(name) or self.extensions[name] is None:
            return None

        return self.extensions[name][i]

    def tr_a_codec(self, name):
        """ Translates audio codec API name into external program identifier."""

        if not self.a_codecs.has_key(name) or self.a_codecs[name] is None:
            raise cis_exceptions.NotImplementedException("Audio Codec " + name)

        return self.a_codecs[name]

    def tr_v_codec(self, name):
        """ Translates video codec API name into external program identifier."""

        if not self.v_codecs.has_key(name) or self.v_codecs[name] is None:
            raise cis_exceptions.NotImplementedException("Video Codec " + name)

        return self.v_codecs[name]
