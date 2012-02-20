#!/usr/bin/env python

import sys
import urllib
import web
import time
import threading
from Queue import Queue

# Located in the parent directory; execute from that location or put it in PYTHONPATH
import logger
import config


class LBWorker(threading.Thread):
    """
    Load Balancing Worker: chooses a CIS where the request should be forwarded
    or broadcasts the requests to all CIS machines if required.
    """
    
    def __init__(self, id):
        """
        Initialize Load Balancing Worker.
        """

        threading.Thread.__init__(self, name = 'LBWorker%02d' % id)
    
    def run(self):
        
        while True:
            job = Server.queue.get()
            
            print '%s is working at %s...' % (self.name, repr(job))
            time.sleep(10)
            
            Server.queue.task_done()


class Server:
    
    def POST(self, request):
        
        Server.queue.put( (request, web.data()) )
        
        #return web.data()

if __name__ == '__main__':
    
    Server.queue = Queue()
    
    # Create job threads.
    lb_workers = []
    for i in range(0, config.JOB_THREADS_COUNT):
        lb_worker = config.LOAD_BALANCER(i, Server.queue)
        lb_worker.daemon = True
        lb_worker.start()
        lb_workers.append(lb_worker)
    
    # Web service.
    urls = ('/(.*)', 'Server')
    app = web.application(urls, globals())
    app.run()
