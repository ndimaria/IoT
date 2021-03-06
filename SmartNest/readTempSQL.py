#!/usr/bin/env python3
import os
import time
import datetime
import glob
import MySQLdb
from time import strftime
 
os.system('modprobe w1-gpio')
os.system('modprobe w1-therm')
base_dir = '/sys/bus/w1/devices/'
device_folder = glob.glob(base_dir + '28*')[0]
device_file = device_folder + '/w1_slave'

# Variables for MySQL
db = MySQLdb.connect(host="localhost", user="review_site",passwd="JxSLRkdutW", db="reviews")
cur = db.cursor()
 
def tempRead():
    t = open(device_file, 'r')
    lines = t.readlines()
    t.close()
 
    temp_output = lines[1].find('t=')
    if temp_output != -1:
        temp_string = lines[1].strip()[temp_output+2:]
        temp_c = float(temp_string)/1000.0
        temp_f = temp_c * 9.0 / 5.0 + 32.0
    return round(temp_f,1)
 
while True:
    temp = tempRead()
    print(temp)
    datetimeWrite = (time.strftime("%Y-%m-%d ") + time.strftime("%H:%M:%S"))
    print(datetimeWrite)
    sql = ("""INSERT INTO tempLog (datetime,temperature) VALUES (%s,%s)""",(datetimeWrite,temp))
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
 
    cur.close()
    db.close()
    break
