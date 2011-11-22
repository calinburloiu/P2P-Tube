#!/usr/bin/env python

"""
Useful functions for CIS.
"""

def get_name(file_name):
    if file_name.find('/') is not -1:
        name = file_name[(file_name.rindex('/')+1):]
    else:
        name = file_name
    if name.find('.') is not -1:
        name = name[:name.rindex('.')]

    return name
