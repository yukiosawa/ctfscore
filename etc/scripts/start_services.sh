#!/bin/bash

# specify the directory where the app installed
APP=/var/www/ctfscore

# start redis on background
#/usr/bin/redis-server &

# start nodejs on background
cd $APP/nodejs && sudo sh -c "/usr/bin/node app.js >> /var/log/nodejs.log" &

# start apache2 daemon on background
#source /etc/apache2/envvars
#/usr/sbin/apache2 -D FOREGROUND &

# start mysql daemon on foreground
#/usr/bin/mysqld_safe

