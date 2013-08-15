#!/usr/bin/python

from BaseHTTPServer import BaseHTTPRequestHandler, HTTPServer

PORT_NUMBER = 8080
HOST = '0.0.0.0'

class myHandler(BaseHTTPRequestHandler):
    
    def do_GET(self):
        self.send_response(200)
        self.send_header('Content-type','text/html')
        self.end_headers()

        self.wfile.write("Hello World !")
        return
try:
    server = HTTPServer((HOST, PORT_NUMBER), myHandler)
    print 'started server on {0}:{1}'.format(HOST, PORT_NUMBER)
    server.serve_forever()

except KeyboardInterrupt:
    print '^C received, shutting down the web server'
    server.socket.close()








