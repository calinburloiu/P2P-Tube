
# CIS URLs
CIS_URLS = [ \
    'http://p2p-next-01.grid.pub.ro:31500/', \
    'http://p2p-next-02.grid.pub.ro:31500/', \
    'http://p2p-next-03.grid.pub.ro:31500/', \
    'http://p2p-next-04.grid.pub.ro:31500/', \
    'http://p2p-next-05.grid.pub.ro:31500/', \
    'http://p2p-next-06.grid.pub.ro:31500/', \
    'http://p2p-next-07.grid.pub.ro:31500/', \
    'http://p2p-next-08.grid.pub.ro:31500/', \
    'http://p2p-next-09.grid.pub.ro:31500/', \
    'http://p2p-next-10.grid.pub.ro:31500/' \
]
# Web server's URL for content ingestion errors. P2P-Tube uses
# http://<site>/video/cis_error .
WS_ERROR = 'http://p2p-next.cs.pub.ro/devel/video/cis_error'

import load_balancer.random_lb
LOAD_BALANCER = load_balancer.random_lb.RandomLoadBalancer

import logger

LOG_LEVEL = logger.LOG_LEVEL_DEBUG

# Number of threads which execute load balancing jobs through LBWorker class.
JOB_THREADS_COUNT = 5
# Number of threads controlled by job which make HTTP requests.
# NOTE: Total number of threads is JOB_THREADS_COUNT * HTTP_THREADS_COUNT.
HTTP_THREADS_COUNT = 5