#!/usr/bin/env python
# -*- coding: utf-8 -*-

# Running webserver that displays current temperature in celsius
#
# Requires:
# - Running Raspberry Pi with Python
# - ADS1015 Analog/Digital Convertor: http://www.adafruit.com/products/1083
# - Adafruit Library: https://github.com/adafruit/Adafruit-Raspberry-Pi-Python-Code/tree/master/Adafruit_ADS1x15
# - TI LM35 Temperature Sensor: http://www.mikrocontroller.net/part/LM35
# - Flask Webframework: http://flask.pocoo.org/

import time, signal, sys
from flask import Flask
import subprocess
from Adafruit_ADS1x15 import ADS1x15

app = Flask(__name__)

@app.route('/')
def index():

    ADS1015 = 0x00
    adc = ADS1x15(address=0x49, ic=ADS1015, debug=True)
    
    INPUT = 2
    milli = adc.readADCSingleEnded(INPUT, 6144, 250)
    celsius = (milli-50)/10

    html = """
        <!doctype html>
        <html>
        <head>
            <title>Fichtl Büro Temperatur</title>      
            <style>
                @import url(http://fonts.googleapis.com/css?family=Open+Sans:400,800,600);
                p { font:600 200px "Open sans"; text-align:center; line-height:100%; z-index:20; position:relative; }
            </style>
            <script src="static/chart.js"></script>
        </head>
        <body><p>"""
    html += str(round(celsius, 1)) + ' °C'

    return html

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=8080)
