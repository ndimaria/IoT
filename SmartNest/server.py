from BaseHTTPServer import BaseHTTPRequestHandler, HTTPServer
from urlparse import parse_qs
import cgi
import glob
import MySQLdb
import multiprocessing


import os
import glob
import time
import subprocess
import smtplib
import socket
import os
import sys
from email.mime.text import MIMEText
import datetime

os.system('modprobe w1-gpio')
os.system('modprobe w1-therm')
 
base_dir = '/sys/bus/w1/devices/'
device_folder = glob.glob(base_dir + '28*')[0]
device_file = device_folder + '/w1_slave'

# Variables for MySQL
db = MySQLdb.connect(host="localhost", user="review_site",passwd="JxSLRkdutW", db="reviews")
cur = db.cursor()
coolingTemp = 0
air_on = "ON"

class GP(BaseHTTPRequestHandler):
    def _set_headers(self):
        self.send_response(200)
        self.send_header('Content-type', 'text/html')
        self.end_headers()
    def do_HEAD(self):
        self._set_headers()
    def do_GET(self):
        self._set_headers()
        print(self.path)
        parsed = parse_qs(self.path[2:])
        if(parsed):
            print(parsed["temp"])
            temp = parsed["temp"]
            
            print(parsed["status"])
            status = parsed ["status"]
            
            sql=("""INSERT INTO info (temp, status) VALUES (%s,%s)""",(int(temp[0]),status))
            try:
                print("Writing to database...")
                # Execute the SQL command
                cur.execute(*sql)
                # Commit your changes in the database
                db.commit()
                print("Write Complete")
 
            except:
                # Rollback in case there is any error
                db.rollback()
                print("Failed writing to database")
 
            
        self.wfile.write("<html><body><h1>Get Request Received!</h1></body></html>")
        
    def do_POST(self):
        self._set_headers()
        form = cgi.FieldStorage(
            fp=self.rfile,
            headers=self.headers,
            environ={'REQUEST_METHOD': 'POST'}
        )
    
        print(form.getvalue("foo"))
        print(form.getvalue("bin"))
        self.wfile.write("<html><body><h1>POST Request Received!</h1></body></html>")

def run(server_class=HTTPServer, handler_class=GP, port=8088):
    server_address = ('', port)
    httpd = server_class(server_address, handler_class)
    print('Server running at localhost:8088...')
    httpd.serve_forever()

def read_temp_raw():
    f = open(device_file, 'r')
    lines = f.readlines()
    f.close()
    return lines

def read_temp():
    lines = read_temp_raw()
    while lines[0].strip()[-3:] != 'YES':
        time.sleep(0.2)
        lines = read_temp_raw()
    equals_pos = lines[1].find('t=')
    if equals_pos != -1:
        temp_string = lines[1][equals_pos+2:]
        temp_c = float(temp_string) / 1000.0
        temp_f = temp_c * 9.0 / 5.0 + 32.0
        return temp_c
    
def send_mail_on():
    print("Sending mail")
    # Change to your own account information
    to = 'trigger@applet.ifttt.com'
    gmail_user = 'ndsoccerstar55@gmail.com'
    gmail_password = 'ifffssdgjvfbhqoi'
    smtpserver = smtplib.SMTP('smtp.gmail.com', 587)
    smtpserver.ehlo()
    smtpserver.starttls()
    smtpserver.ehlo
    smtpserver.login(gmail_user, gmail_password)
    mail_body = "Turning on air conditioning"
    msg = MIMEText(mail_body)
    msg['Subject'] = "#on"
    msg['From'] = gmail_user
    msg['To'] = to
    smtpserver.sendmail(gmail_user, [to], msg.as_string())
    smtpserver.quit()
    updateDB("ON")

def send_mail_off():
    print("Sending mail")
    # Change to your own account information
    to = 'trigger@applet.ifttt.com'
    gmail_user = 'ndsoccerstar55@gmail.com'
    gmail_password = 'ifffssdgjvfbhqoi'
    smtpserver = smtplib.SMTP('smtp.gmail.com', 587)
    smtpserver.ehlo()
    smtpserver.starttls()
    smtpserver.ehlo
    smtpserver.login(gmail_user, gmail_password)
    mail_body = "Turning off air conditioning"
    msg = MIMEText(mail_body)
    msg['Subject'] = "#off"
    msg['From'] = gmail_user
    msg['To'] = to
    smtpserver.sendmail(gmail_user, [to], msg.as_string())
    smtpserver.quit()
    updateDB("OFF")
    
def updateDB(status):
    global air_on
    air_on = status
    sql = "INSERT INTO info (temp, status) VALUES (%s, %s)"
    val = (coolingTemp, status)
    cur.execute(sql, val)
    
def getDB():
    cur.execute("SELECT * FROM info ORDER BY ID DESC LIMIT 1")
    row = cur.fetchone()
    global coolingTemp
    coolingTemp = int(row[1])
    
    global air_on
    air_on = row[2]
    
def monitorTemp():
    while True:
       print(read_temp())
       print(air_on)
       print(coolingTemp)
       getDB()
       if read_temp() > int(coolingTemp) and (air_on == "OFF" or air_on =="TRANSITIONING") :
            print(read_temp())
            send_mail_on()
       if read_temp() < int(coolingTemp) and (air_on == "ON" or air_on == "TRANSITIONING"):
            print(read_temp())
            send_mail_off()     
       time.sleep(1)


if __name__ == '__main__':
    p1 = multiprocessing.Process(name='p1', target=run)
    p = multiprocessing.Process(name='p', target=monitorTemp)
    p1.start()
    p.start()
