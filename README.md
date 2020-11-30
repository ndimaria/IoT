# IoT CPE629 Final Project
## DIY Smart Nest Thermostat
### Description
I live in an apartment, and I am very often not there for long periods at a time. When I get home, especially during the summer it can get to over 80 degrees Fahrenheit in my room and I will turn the AC on full blast in the hopes that it cools down quickly. In addition, I play games on my PC which outputs a lot of heat, especially when I game for many hours.  
The overall goal of this project is to use the Raspberry Pi with a temperature sensor, as well as a smart outlet, a to create a Nest like Smart Thermostat. For example, if I am not home I can set the temperature to a reasonable temperature (around 72 degrees) and the AC will go on and off automatically to maintain that temperature.  
In addition, I plan on creating a web application that will be able to display the temperature in my room at all times, as well as allow me to turn on and off the AC via a button.

### Hardware Requirements
* Raspberry Pi Model 3 or 4
* DS18B20 Temperature sensor (DHT11 and DHT22 will work with some software adjustment)
* Smart Outlet (One used in this project can be found here: [Amazon](https://www.amazon.com/Outlet-Required-Gosund-Upgraded-Version/dp/B07GRLQV47/ref=sr_1_1_sspa?dchild=1&keywords=smart+outlet&qid=1606168791&sr=8-1-spons&psc=1&spLa=ZW5jcnlwdGVkUXVhbGlmaWVyPUEySVk2QVlWQ0NBVjc1JmVuY3J5cHRlZElkPUEwNDk1MzU1OUdBVlFZWlZPTlYyJmVuY3J5cHRlZEFkSWQ9QTAyNDE3NTQzRDROM1VFRTU0S0tSJndpZGdldE5hbWU9c3BfYXRmJmFjdGlvbj1jbGlja1JlZGlyZWN0JmRvTm90TG9nQ2xpY2s9dHJ1ZQ==))
* Breadboard
* 3 x Jumper Cables
  
Setup the breadboard as in the following image:
![Image of Breadboard setup](https://user-images.githubusercontent.com/49735811/100019751-99a0a900-2dac-11eb-9732-3744e1ec662b.jpg)
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
#### 1. Setup MariaDB database
````
sudo mysql -u root -p
CREATE DATABASE reviews;
USE reviews;
CREATE TABLE tempLog(datetime DATETIME NOT NULL, temperature FLOAT(5,2) NOT NULL);
CREATE TABLE info(ID int AUTO_INCREMENT, temp int NOT NULL, status varchar(15) NOT NULL, PRIMARY KEY (ID));
GRANT ALL ON reviews.* to review_site@localhost IDENTIFIED BY 'JxSLRkdutW';
````
* This last line creates a new user called "review_site" with the "JxSLRkdutW" as the password, which is used to connect to the database in the PHP script. 
#### 2. Schedule Temperature Readings
````
crontab -e
````
* Then add the following line: 
````
*/5 * * * * /home/pi/SmartNest/readTempSQL.py
````
* To make sure that the file is executable, type the following:
````
sudo chmod +x readTempSQL.py
./readTempSQL.py
````
#### 3. Create IFTTT applets
* Navigate to [IFTTT](https://ifttt.com/) and create an account if you do not have one already
* Create a new applet
  1. Make the "if" trigger "send IFTTT email tagged" 
  2. Set the trigger to "#off" 
  3. Make the "then" trigger to turn off the smart plug 
* Create a second applet 
  1. Make the "if" trigger "send IFTTT email tagged" 
  2. Set the trigger to "#on" 
  3. Make the "then" trigger to turn on the smart plug 
* Create a third applet
  1. Make the "if" trigger "Say a phrase with a number" for Google Assistant
  2. Enter the text that you want to be able to say to the Google Assistant as well as what you want it to reply. For example: "Set thermostat tempertaure to #" and the Google Assistant was set to reply "Setting thermostat temperature to #" 
  3. Set the the "then" trigger to "Make a web request"
  4. Type the URL as the following: http://(YOUR_PUBLIC_IP_ADDRESS):8088/?temp={{NumberField}}&status=TRANSITIONING
  * Your public IP address can be found at [WhatIsMyIp](https://www.whatismyip.com/)
  5. Set the "Method" to "Get"
#### 4. Setup On and Off Scipts
  * Open `turnOn.py` and `turnOff.py` 
  * Change "gmail_user" and "gmail_password" to your own
  * Generate the app password by using the following steps (credits to: [GitHub]https://github.com/kevinwlu/iot/tree/master/lesson1)
    1. My Account > Sign-in & security > Signing in to Google >
    2. 2-Step Verification > TURN ON > Select a second verification step > Authenticator app (Default)
    3. App passwords > Select the app (Mail) and device (Raspberry Pi) > GENERATE
  * Make the file executable:
  ````
  sudo chmod +x turnOn.py
  ````
  * Ensure the process works by running
  ````
  ./turnOn.py
  ````
#### 5. Setup PHP webpage
* Change lines 56 and 68 of `index.php` to the file path where your `turnOn.py` and `turnOff.py` scripts are located. These are the lines that read the following: 
````
$command = escapeshellcmd('/home/pi/NickIoT/SmartNest/turnOn.py');
````
````
cd IoT/SmartNest
mv PhpSimpleChart2.php /var/www/html
mv index.php /var/www/html
````
* Navigate to the IP address of your Raspberry Pi to see the PHP wepbage. Note: The `PhpSimpleChart2.php` needs to be in this folder because the web app makes a call to it to create the graph.
#### 6. Port forward for Google Assistant Voice Commands
* Port forwarding on every router is different. See this link to learn more about how and why to port forward [HowToGeek](https://www.howtogeek.com/66214/how-to-forward-ports-on-your-router/)
* The port that needs to forwarded is 8088 to the IP address of your Raspberry Pi in your home network.
#### 7. Python Server
* In the "send_mail_on" and "send_mail_off" methods of `server.py` change the `gmail_user` and `gmail_password` as in step 4
* Run the servery simply running 
````
python server.py
````
* Now the voice commands with Google Assistant can be tested as well as setting a thermostat temperature within the PHP web application. 
* This server makes it so that the PHP page does not have to be open at all times to have the thermostat work
