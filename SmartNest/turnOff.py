#!/usr/bin/env python
import os
import glob
import time
import subprocess
import smtplib
import socket
import os
from email.mime.text import MIMEText
import datetime

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
    mail_body = "Turning off air conditioning"
    msg = MIMEText(mail_body)
    msg['Subject'] = "#off"
    msg['From'] = gmail_user
    msg['To'] = to
    smtpserver.sendmail(gmail_user, [to], msg.as_string())
    smtpserver.quit()
    

send_mail_on()