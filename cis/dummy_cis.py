#!/usr/bin/env python

import web
import sys
import json

urls = (
    '/(.*)', 'Hello'
)

LOAD = sys.argv[2]
print 'load is %s' % LOAD

app = web.application(urls, globals())

class Hello:
    def GET(self, request):
        if request == 'get_load':
            resp = {"load": LOAD}
            web.header('Content-Type', 'application/json')
            return json.dumps(resp)
    
    def POST(self, request):
        print web.data()

        return request

if __name__ == "__main__":
    app.run()
