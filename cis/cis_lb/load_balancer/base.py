import threading
import urllib
import json

import config
import logger


class LoadBalancer(threading.Thread):
    
    def __init__(self, id, queue):
        """
        Initialize Load Balancer,
        """

        threading.Thread.__init__(self, \
                name = '%s%02d' % (self.__class__.__name__, id))
        
        self.queue = queue
    
    def run(self):
        
        while True:
            (request, data) = self.queue.get()
            urls = config.CIS_URLS[:]
            code = json.loads(data)['code']
            success = False
            
            while len(urls) != 0:
                cis = self.choose(urls)
                
                # Request is forwarded to the chosen CIS.
                try:
                    urllib.urlopen(cis + request, data)
                except IOError:
                    logger.log_msg('#%s: Failed to forward request to %s' \
                            % (code, cis), \
                            logger.LOG_LEVEL_ERROR)
                    continue
                
                success = True
                logger.log_msg('#%s: Request forwarded to %s' \
                            % (code, cis), \
                        logger.LOG_LEVEL_INFO)
                break
            
            if len(urls) == 0 and not success:
                logger.log_msg('#%s: Failed to forward request to any CIS' \
                            % code, \
                            logger.LOG_LEVEL_FATAL)
                self.notify_error(code)
            
            self.queue.task_done()
    
    def notify_error(self, code):
        logger.log_msg('#%s: notifying web server about the error...'\
                % code)
        
        if config.WS_ERROR[len(config.WS_ERROR) - 1] == '/':
            url = config.WS_ERROR + code
        else:
            url = config.WS_ERROR + '/' + code
        url = url + '/' + 'unreachable'
        
        f = urllib.urlopen(url)
        f.read()
        
    def choose(self, urls):
        """
        Implement load balancing policy in this method for child classes which
        choses a CIS from urls parameter. The chosen URL should be deleted from
        urls list.
        """        
        pass