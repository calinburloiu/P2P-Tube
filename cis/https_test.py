import web
from web.wsgiserver import CherryPyWSGIServer

CherryPyWSGIServer.ssl_certificate = "cacert.pem"
CherryPyWSGIServer.ssl_private_key = "privkey.pem"

urls = ("/.*", "hello")
app = web.application(urls, globals())


class hello:
    def GET(self):
        return "Hello, world!"

    def POST(self):
        print web.data()


if __name__ == "__main__":
    app.run()
