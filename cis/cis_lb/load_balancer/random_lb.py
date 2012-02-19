import random

from base import LoadBalancer
import config

class RandomLoadBalancer(LoadBalancer):
    
    def choose(self):
        return config.CIS_URLS[random.randint(0, len(config.CIS_URLS) - 1)]