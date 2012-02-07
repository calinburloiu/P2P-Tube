#!/usr/bin/env python

"""
Classes that facilitate file transfer (between Web Server and CIS).

They may extend BaseFileTransferer class.
"""

import sys
import ftplib
import base
import ftp_config
import socket
import api_exceptions
import os


class FTPFileTransferer(base.BaseFileTransferer):
    """
    FTPS implementation for file transfering between Web Server and CIS.
    """

    ftp = None

    def __init__(self, local_path='', remote_path=''):
        base.BaseFileTransferer.__init__(self, local_path, remote_path)

        self.ftp = ftplib.FTP_TLS(ftp_config.FTP_HOST, ftp_config.FTP_USER,
                ftp_config.FTP_PASSWD, ftp_config.FTP_ACCT)
        self.ftp.set_pasv(True)

    def get(self, files):
        try:
            self.ftp.cwd(self.remote_path)
        except ftplib.error_perm as e:
            raise api_exceptions.FileTransferException( \
                    "Could not change remote directory '%s': %s" \
                    % (self.remote_path, repr(e)))


        for crt_fn in files:
            local_fn = os.path.join(self.local_path, crt_fn)
            remote_fn = os.path.join(self.remote_path, crt_fn)
            try:
                file_local = open(local_fn, 'wb')
            except IOError as e:
                raise api_exceptions.FileTransferException( \
                        "Could not open local file '%s' for writing: %s" \
                        % (local_fn, repr(e)))

            try:
                self.ftp.retrbinary('RETR %s' % crt_fn, file_local.write)
                file_local.close()
            except ftplib.error_perm as e:
                raise api_exceptions.FileTransferException( \
                        "Could not get file '%s' from Web Server: %s" \
                        % (remote_fn, repr(e)))

    def put(self, files):
        try:
            self.ftp.cwd(self.remote_path)
        except ftplib.error_perm as e:
            raise api_exceptions.FileTransferException( \
                    "Could not change remote directory '%s': %s" \
                    % (self.remote_path, repr(e)))

        for crt_fn in files:
            local_fn = os.path.join(self.local_path, crt_fn)

            try:
                file_local = open(local_fn, 'rb')
            except IOError as e:
                raise api_exceptions.FileTransferException( \
                        "Could not open local file '%s' for reading: %s" \
                        % (local_fn, repr(e)))
                
            try:
                self.ftp.storbinary('STOR %s' % crt_fn, file_local)
                file_local.close()
            except ftplib.error_perm as e:
                raise api_exceptions.FileTransferException( \
                        "Could not put file '%s' to Web Server: %s" \
                        % (local_fn, repr(e)))

    def close(self):
        if self.ftp is not None:
            try:
                self.ftp.quit()
            except:
                pass
