#!/usr/bin/env python

"""
Classes that facilitate file transfer (between Web Server and CIS).

They may extend BaseFileTransferer class.
"""

import sys
from ftplib import FTP_TLS
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

        self.ftp = FTP_TLS(ftp_config.FTP_HOST, ftp_config.FTP_USER,
                ftp_config.FTP_PASSWD, ftp_config.FTP_ACCT)
        self.ftp.set_pasv(True)

    def get(self, files):
        for crt_file in files:
            crt_file = os.path.join(self.local_path, crt_file)
            try:
                file_local = open(crt_file, 'wb')
            except IOError as e:
                raise api_exceptions.FileTransferException( \
                        "Could not open local file '%s' for writing: %s" \
                        % (crt_file, repr(e)))

            try:
                self.ftp.cwd(self.remote_path)
                self.ftp.retrbinary('RETR %s' % crt_file, file_local.write)
                file_local.close()
            except ftplib.error_perm as e:
                raise api_exceptions.FileTransferException( \
                        "Could not get file '%s' from Web Server: %s" \
                        % (crt_file, repr(e)))

    def put(self, files):
        for crt_file in files:
            crt_file = os.path.join(self.local_path, crt_file)

            try:
                file_local = open(crt_file, 'rb')
            except IOError as e:
                raise api_exceptions.FileTransferException( \
                        "Could not open local file '%s' for reading: %s" \
                        % (crt_file, repr(e)))
                
            try:
                self.ftp.cwd(self.remote_path)
                self.ftp.storbinary('STOR %s' % crt_file, file_local)
                file_local.close()
            except ftplib.error_perm as e:
                raise api_exceptions.FileTransferException( \
                        "Could not get file '%s' from Web Server: %s" \
                        % (crt_file, repr(e)))

    def close(self):
        if self.ftp is not None:
            try:
                self.ftp.quit()
            except:
                pass
