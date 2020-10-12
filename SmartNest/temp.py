#!/usr/bin/env python

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

coolingTemp=sys.argv[1]
print("cooling to" + coolingTemp)
air_on = False

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
        return temp_f

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
    global air_on
    air_on = True

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
    global air_on
    air_on = False

while True:
    print (read_temp())
    if read_temp() > int(coolingTemp) and air_on == False:
        print(read_temp())
        send_mail_on()
    if read_temp() < int(coolingTemp) and air_on == True:
        print(read_temp())
        send_mail_off()     
    time.sleep(1)
