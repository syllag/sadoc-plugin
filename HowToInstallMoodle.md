# Introduction #

For more help see the video tutorial at [tutorial](https://www.youtube.com/watch?v=3Uxk0kM40hM)

# Details #

Step 1 : Install PHP5 with dependencies

`sudo apt-get install php5 php5-mysql php5-curl php5-xmlrpc php5-intl php5-gd`

Step 2 : Install MySQL

> http://doc.ubuntu-fr.org/mysql

Step 3 : Configure your MySQL

```
* sudo mysql -u root -p 
* CREATE DATABASE moodle DEFAULT CHARACTER SET utf8;
* GRANT ALL PRIVILEGES ON moodle.* to 'user'@localhost IDENTIFIED BY 'password';
* FLUSH PRIVILEGES;
```

Step 4 : Download the last official version **'Built Weekly'** of moodle [here](http://download.moodle.org/)

Step 5 : Follow these instructions
```
* cd /var/www
* sudo tar -xvf ~/Downloads/moodle-latest-24.tgz
* cd /var 
* sudo mkdir moodledata
* sudo chown -R www-data:www-data moodledata
* sudo service apache2 restart
```

Step 6 : Go to http://localhost/moodle and follow the instructions for installation

**/!\ Warning /!\ :During the installation the required "user" and "password" are those indicated on the step 3**