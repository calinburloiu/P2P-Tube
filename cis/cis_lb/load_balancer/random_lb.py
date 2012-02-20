import random

from base import LoadBalancer

class RandomLoadBalancer(LoadBalancer):
    
    def choose(self, urls):
        index = random.randint(0, len(urls) - 1)
        cis = urls[index]
        
        del(urls[index])
        
        return cis