import threading
import urllib

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
            
            while len(urls) != 0:
                cis = self.choose(urls)
                
                # Request is forwarded to the chosen CIS.
                try:
                    urllib.urlopen(cis + request, data)
                except IOError:
                    logger.log_msg('Failed to forward request to %s' % cis, \
                            logger.LOG_LEVEL_ERROR)
                    continue
                
                logger.log_msg('Request forwarded to %s' % cis, \
                        logger.LOG_LEVEL_INFO)
                break
            
            self.queue.task_done()
        
    def choose(self, urls):
        """
        Implement load balancing policy in this method for child classes which
        choses a CIS from urls parameter. The chosen URL should be deleted from
        urls list.
        """        
        pass