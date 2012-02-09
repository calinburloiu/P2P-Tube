#!/usr/bin/env python

from BaseLib.Core.API import *
import tempfile
import random
import config
import logger

def create_torrent(source):
    """
    Creates a torrent file for the video source file.
    """

    if isinstance(source, unicode):
        usource = source
    else:
        usource = source.decode(sys.getfilesystemencoding())

    duration = config.AVINFO_CLASS.get_video_duration(source, True)

    tdef = TorrentDef()
    tdef.add_content(usource, playtime=duration)
    tdef.set_tracker(config.BT_TRACKER)

    tdef.set_piece_length(32768)

    tdef.finalize()
    tdef.save(source + '.tstream')


class BitTorrent:
    """
    Implementation of BitTorrent operations that uses Next-Share library.
    """

    def __init__(self):

        port = random.randint(10000, 65535)
        
        # setup session
        sscfg = SessionStartupConfig()
        statedir = tempfile.mkdtemp()
        sscfg.set_state_dir(statedir)
        sscfg.set_listen_port(port)
        sscfg.set_megacache(False)
        sscfg.set_overlay(False)
        sscfg.set_dialback(True)
        sscfg.set_internal_tracker(False)
        
        self.session = Session(sscfg)

    def start_torrent(self, torrent, output_dir='.'):
        """
        Download (leech or seed) a file via BitTorrent.
        
        The code is adapted from Next-Share's 'BaseLib/Tools/cmdlinedl.py'.

        @param torrent .torrent file or URL
        """

        # setup and start download
        dscfg = DownloadStartupConfig()
        dscfg.set_dest_dir(output_dir)

        if torrent.startswith("http") or torrent.startswith(P2PURL_SCHEME):
            tdef = TorrentDef.load_from_url(torrent)
        else: 
            tdef = TorrentDef.load(torrent)
        if tdef.get_live():
            raise ValueError("CIS does not support live torrents")
        
        new_download = True
        try:
            d = self.session.start_download(tdef, dscfg)
        except DuplicateDownloadException:
            new_download = False
        #d.set_state_callback(state_callback, getpeerlist=False)
        
        if new_download:
            logger.log_msg('download of torrent "%s" started' % torrent)
        #else:
            #logger.log_msg('download of torrent "%s" already started' \
                    #% torrent, logger.LOG_LEVEL_DEBUG)
    
    def stop_torrent(self, torrent, remove_content=False):
        """
        Stop leeching or seeding a file via BitTorrent.
        
        !!! Only tested with torrents started with .tstream files. Not tested
        for torrents started with URLs.
        
        @param torrent .torrent file or URL
        @param remove_content removes downloaded file
        """
        
        downloads = self.session.get_downloads()
        
        for dl in downloads:
            tdef = dl.get_def()
            if torrent.find(tdef.get_name()) == 0:
                self.session.remove_download(dl, remove_content)
                logger.log_msg('torrent "%s" stopped' % torrent)
                break
