# IoT CPE629 Final Project
## DIY Smart Nest Thermostat
### Description
I live in an apartment, and I am very often not there for long periods at a time. When I get home, especially during the summer it can get to over 80 degrees Fahrenheit in my room and I will turn the AC on full blast in the hopes that it cools down quickly. In addition, I play games on my PC which outputs a lot of heat, especially when I game for many hours.  
The overall goal of this project is to use the Raspberry Pi with a temperature sensor and a motion sensor, as well as a smart outlet, and some machine learning to create a Nest like Smart Thermostat. The goal is that the motion sensor will be used to train a machine learning model to learn the times I am often home and not home, so that it can turn the AC on accordingly. For example, if I am not home it may turn on the AC for short intervals to keep the room at a reasonable temperature (around 72 degrees). When I am home it will turn on for longer periods to get the temperature down to the temperature I like to sleep at (around 68 degrees).  
In addition, I plan on creating a web application that will be able to display the temperature in my room at all times, as well as allow me to turn on and off the AC via a button. This application could be built up to contain even more functionality, such as scheduling.

### Hardware Requirements
* Raspberry Pi Model 3 or 4
* DS18B20 Temperature sensor (DHT11 and DHT22 will work with some adjustment)
* 3 x Jumper Cables

### Software Requirements 
For my week-by-week setup see [GoogleSites](https://sites.google.com/stevens.edu/ee629/projects/diy-nest-smart-thermostat)  
  
For more general instructions see below:
* Install MariaDB 
````
$ sudo apt update  
$ sudo apt install mariadb-server mariadb-client  
$ sudo apt install python3-mysqldb  
$ sudo pip3 install -U mysqlclient  
$ sudo mysql_secure_installation  
````
* Install Apache
````
$ sudo apt update
$ sudo apt install apache2
$ sudo service apache2 restart
````
* Install PHP7.3
````
$ sudo apt install php7.3-mysql
````

### Software Setup 
* Setup MariaDB database
````
sudo mysql -u root -p
CREATE DATABASE temp_database;
USE temp_database;
CREATE TABLE tempLog(datetime DATETIME NOT NULL, temperature FLOAT(5,2) NOT NULL);
````

