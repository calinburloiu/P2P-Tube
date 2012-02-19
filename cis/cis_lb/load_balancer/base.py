import threading
import urllib

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
            
            cis = self.choose()
            logger.log_msg('Forwarding to %s' % cis, logger.LOG_LEVEL_DEBUG)
            urllib.urlopen(cis + request, data)
            
            self.queue.task_done()
        
    def choose(self):
        """
        Implement load balancing policy in this method for child classes which
        choses a CIS from config.CIS_URLS .
        """        
        pass