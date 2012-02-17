
# Number of threads which execute load balancing jobs through LBWorker class.
JOB_THREADS_COUNT = 5
# Number of threads controlled by job which make HTTP requests.
# NOTE: Total number of threads is JOB_THREADS_COUNT * HTTP_THREADS_COUNT.
HTTP_THREADS_COUNT = 5