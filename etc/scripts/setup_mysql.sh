#!/bin/bash

# root password
MYSQL_ROOT_USER=root
MYSQL_ROOT_PASSWD=ChangeMyPassword

# administrator
MYSQL_ADMIN_USER=ChangeMyName
MYSQL_ADMIN_PASSWD=ChangeMyPassword

# app installed
APP=/var/www/ctfscore
# production config
PRO=$APP/fuel/app/config/production/db.php
# development config
DEV=$APP/fuel/app/config/development/db.php


# set root password
# if already being set up, just ignore the error message
mysqladmin password $MYSQL_ROOT_PASSWD -u root > /dev/null 2>&1

# allow administrator to access via network
mysql -u $MYSQL_ROOT_USER -p$MYSQL_ROOT_PASSWD -e "GRANT ALL PRIVILEGES ON *.* TO $MYSQL_ADMIN_USER@'%' IDENTIFIED BY '$MYSQL_ADMIN_PASSWD' WITH GRANT OPTION;"

# fuelphp db setting
sed -i -e"s/ChangeMyName/$MYSQL_ADMIN_USER/" $PRO
sed -i -e"s/ChangeMyPassword/$MYSQL_ADMIN_PASSWD/" $PRO
sed -i -e"s/ChangeMyName/$MYSQL_ADMIN_USER/" $DEV
sed -i -e"s/ChangeMyPassword/$MYSQL_ADMIN_PASSWD/" $DEV

