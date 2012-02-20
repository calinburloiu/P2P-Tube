import sys
import datetime

LOG_LEVEL_ALL = 0
LOG_LEVEL_DEBUG = 1
LOG_LEVEL_INFO = 2
LOG_LEVEL_WARNING = 3
LOG_LEVEL_ERROR = 4
LOG_LEVEL_FATAL = 5
LOG_LEVEL_OFF = 6

LOG_LEVEL_NAMES = { \
    LOG_LEVEL_DEBUG: 'DEBUG', \
    LOG_LEVEL_INFO: 'INFO', \
    LOG_LEVEL_WARNING: 'WARNING', \
    LOG_LEVEL_ERROR: 'ERROR', \
    LOG_LEVEL_FATAL: 'FATAL', \
}

import config

def log_msg(msg, level=LOG_LEVEL_INFO):
    """
    Prints log messages based on the log level.
    """
    
    if level == LOG_LEVEL_ALL or level == LOG_LEVEL_OFF:
        return
        
    if level < config.LOG_LEVEL:
        return
    
    if level >= LOG_LEVEL_ERROR:
        f = sys.stderr
    else:
        f = sys.stdout
    
    now = datetime.datetime.now()
    date_time = now.strftime('%Y-%m-%d %H:%M:%S')
        
    f.write('[%s][%s] %s\n' % (LOG_LEVEL_NAMES[level], date_time, msg))