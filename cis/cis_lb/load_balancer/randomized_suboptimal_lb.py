import sys
import random
import urllib
import threading
import Queue
import json

from base import LoadBalancer
import config
import logger

class HTTPReqWorker(threading.Thread):
    """
    Worker thread which requests load of a CIS.
    """
    
    def __init__(self, id, queue_in, queue_out):

        threading.Thread.__init__(self, \
                name = '%s%02d' % (self.__class__.__name__, id))
        
        self.queue_in = queue_in
        self.queue_out = queue_out
    
    def run(self):
        
        while True:
            url = self.queue_in.get()
            
            try:
                f = urllib.urlopen(url + 'get_load')
                r = f.read()
                parsed_r = json.loads(r)
            except IOError:
                self.queue_out.put( (url, None) )
                logger.log_msg('%s: Failed to request load to %s' \
                        % (self.name, url), \
                        logger.LOG_LEVEL_ERROR)
                continue
            
            # Put response load to the output queue.
            self.queue_out.put( (url, parsed_r['load']) )
            logger.log_msg('%s: Received load %s from %s' \
                        % (self.name, parsed_r['load'], url), \
                    logger.LOG_LEVEL_INFO)

class RandomizedSuboptimalLoadBalancer(LoadBalancer):
    
    def __init__(self, id, queue):
        
        LoadBalancer.__init__(self, id, queue)
        
        # Number of CIS machines that are going to be asked about their load.
        self.k = config.RANDOMIZED_SUBOPTIMAL_LB_K
        # Queue of load request tasks for HTTPReqWorker.
        self.tasks_queue = Queue.Queue()
        # Queue of CIS loads populated by HTTPReqWorker-s.
        self.loads_queue = Queue.Queue()
        
        # Start HTTPReqWorker-s.
        self.http_req_workers = []
        for i in range(0, config.HTTP_THREADS_COUNT):
            http_req_worker = HTTPReqWorker(i, self.tasks_queue, \
                    self.loads_queue)
            http_req_worker.daemon = True
            http_req_worker.start()
            self.http_req_workers.append(http_req_worker)
    
    
    def choose(self, urls):
        
        self.tasks_queue.queue.clear()
        self.loads_queue.queue.clear()
        
        while len(urls) != 0:
            # Choose k CIS machines.
            k_urls = self.subset(urls)
            
            # Find out their load by giving tasks to HTTPReqWorker-s.
            for url in k_urls:
                self.tasks_queue.put(url)
            
            # Wait for load answers from HTTPReqWorker-s and choose the least
            # loaded CIS machine.
            best_url = None
            best_load = sys.maxint
            for i in range(0, self.k):
                (url, load) = self.loads_queue.get()
                
                if load == None:
                    continue
                else:
                    load = int(load)
                
                if load < best_load:
                    logger.log_msg('Got %s %s' % (url, load), \
                            logger.LOG_LEVEL_DEBUG)
                    best_load = load
                    best_url = url
            
            if best_url != None:
                break
        
        #del( urls[ urls.index(best_url) ] )
        
        logger.log_msg('Returning best_url = "%s"' % best_url, \
                logger.LOG_LEVEL_DEBUG)
        return best_url
    
    
    def subset(self, _set):
        """
        Returns a subset of _set with at most self.k items and deletes those
        items from _set.
        """
        
        _subset = []
        
        for i in range(0, self.k):
            if len(_set) == 0:
                break
            
            index = random.randint(0, len(_set) - 1)
            item = _set[index]
            _subset.append(item)
            del(_set[index])
        
        return _subset
    