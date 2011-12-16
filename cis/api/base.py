#!/usr/bin/env python

"""
Base classes for the external programs API.
"""

import api_exceptions
import re
import cis_util
import random

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
            name = cis_util.get_name(input_file)

        self.name = name

    def transcode(self, container, a_codec=None, v_codec=None,
            a_bitrate=None, a_samplingrate=None, a_channels=None,
            v_bitrate=None, v_framerate=None, v_resolution=None, v_dar=None):
        """
        Transcodes the input file to an audio-video file.

        @param container: possible values are listed in containers member 
        as keys
        @param a_codec possible values are listed in a_codecs member as keys
        @param v_codec possible values are listed in v_codecs member as keys
        @param a_bitrate (numeric) audio bit rate
        @param a_samplingrate (numeric) audio sampling rate in Hz
        @param a_channels (numeric) number of audio channels
        @param v_bitrate (numeric) video bit rate
        @param v_framerate (numeric) number of frames per second for a video
        @param v_resolution (string) video image size as <width>x<height>
        @param v_dar video display aspect ratio as <den>x<num> or float
        @return output file name
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

        return self._transcode(self.tr_container(container),
                self.tr_a_codec(a_codec), self.tr_v_codec(v_codec),
                a_bitrate, a_samplingrate, a_channels,
                v_bitrate, v_framerate, v_resolution, v_dar)

    def _transcode(self, container, a_codec=None, v_codec=None,
            a_bitrate=None, a_samplingrate=None, a_channels=None,
            v_bitrate=None, v_framerate=None, v_resolution=None, v_dar=None):
        """
        Called by transcode; must be overridden by a child class which
        effectively transcodes the input file.

        @return output file name
        """
        pass

    def tr_container(self, name):
        """ Translates container API name into external program identifier."""

        if not self.containers.has_key(name) or self.containers[name] is None:
            raise api_exceptions.NotImplementedException("Container " + name)

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
            raise api_exceptions.NotImplementedException("Audio Codec " + name)

        return self.a_codecs[name]

    def tr_v_codec(self, name):
        """ Translates video codec API name into external program identifier."""

        if not self.v_codecs.has_key(name) or self.v_codecs[name] is None:
            raise api_exceptions.NotImplementedException("Video Codec " + name)

        return self.v_codecs[name]


class BaseThumbExtractor:
    """
    Abstraction of the API class for the thumbnail extraction program. 

    Thumbnail extracted are in JPEG format.
    """

    prog_bin = None
    input_file = None
    dest_path = ''
    name = None
    
    def __init__(self, input_file, name=None, prog_bin=None):
        self.input_file = input_file
        if prog_bin is not None:
            self.prog_bin = prog_bin
        
        if name is None:
            name = cis_util.get_name(input_file)

        self.name = name

    def extract_thumb(self, seek_pos, resolution="120x90", index=0):
        """
        Extracts a thumbnail from the video from a specified position
        expressed in seconds (int/float).

        index: an index appended to the image name in order to avoid
        overwriting.
        """
        pass

    def extract_random_thumb(self, resolution="120x90", index=0):
        """
        Extracts a thumbnail from the video from a random position.
        """
        duration = self.get_video_duration()
        seek_pos = random.random() * duration
        self.extract_thumb(seek_pos, resolution, index)

    def extract_summary_thumbs(self, count, resolution="120x90"):
        """
        Extracts a series of thumbnails from a video by taking several
        snapshots.

        The snapshots are taken from equally spaced positions such that
        `count` thumbs are extracted.
        """
        duration = self.get_video_duration()
        interval = duration / (count + 1)
        
        n_thumbs_extracted = 0
        seek_pos = interval
        for index in range (0, count):
            thumb_extracted = True
            try:
                self.extract_thumb(seek_pos, resolution, n_thumbs_extracted)
            except api_exceptions.ThumbExtractionException as e:
                thumb_extracted = False

            if thumb_extracted:
                n_thumbs_extracted += 1

            seek_pos += interval

        return n_thumbs_extracted

    def get_output_file_name(self, index):
        """ Returns the name required as output file name based on index. """
        output_file_name = self.dest_path + self.name \
                + '_t' + ("%02d" % index) + '.jpg'
        return output_file_name

    def get_video_duration(self):
        pass


class BaseFileTransferer:
    """
    Ensures file transfer from the Web Server to the CIS (here).
    
    Several implementations can be done by extending this class for
    file transfer protocol such as FTP, SCP, RSYNC, HTTP etc.
    """
    
    local_path = ''
    remote_path = ''
    
    def __init__(self, local_path='', remote_path=''):
        """ Initialize by setting local and remote paths for file transfer. """
        self.local_path = local_path
        self.remote_path = remote_path

    def __del__(self):
        self.close()

    def get(self, files):
        """
        Transfers files locally from the Web Server.

        files: a list of file name strings
        """
        pass

    def put(self, files):
        """
        Transfers files from the Web Server locally.

        files: a list of file name strings
        """
        pass

    def close(self):
        """
        This method should be called when the instance is no longer required.

        Class's destructor calls this method.
        """
        pass


class BaseAVInfo:
    @staticmethod
    def get_video_duration(input_file, formated=False):
        """
        Returns the number of seconds of a video (int/float) if formated is
        False and a string for duration formated as [HH:]:mm:ss otherwise.
        """
        pass
