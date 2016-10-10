#!/bin/sh

# load common setting
. $(dirname $0)/common.sh

echo "Setting up MySQL databases."

if [ -e $PASSFILE ]; then
    echo "+MySQL has been already set up: $PASSFILE"
    cat $PASSFILE
    exit
fi

# root password
echo "Setting up MySQL root user."
if [ -z "$MYSQL_ROOT_PASSWD" ]; then
    echo "+Generating a random password."
    MYSQL_ROOT_PASSWD="$(head -n 10 /dev/urandom | base64 | fold -w 32 | head -n 1)"
fi
echo "MYSQL_ROOT_PASSWD=$MYSQL_ROOT_PASSWD"
echo "MYSQL_ROOT_PASSWD=$MYSQL_ROOT_PASSWD" > $PASSFILE

# set root password
# if already being set up, just ignore the error message
/usr/bin/mysqladmin password $MYSQL_ROOT_PASSWD -u root > /dev/null 2>&1
echo "+Done."

# administrator
echo "Setting up MySQL user for our web application."
if [ -z "$MYSQL_ADMIN_USER" ]; then
    MYSQL_ADMIN_USER="admin"
fi
echo "MYSQL_ADMIN_USER=$MYSQL_ADMIN_USER"
echo "MYSQL_ADMIN_USER=$MYSQL_ADMIN_USER" >> $PASSFILE
if [ -z "$MYSQL_ADMIN_PASSWD" ]; then
    echo "+Generating a random password."
    MYSQL_ADMIN_PASSWD="$(head -n 10 /dev/urandom | base64 | fold -w 32 | head -n 1)"
fi
echo "MYSQL_ADMIN_PASSWD=$MYSQL_ADMIN_PASSWD"
echo "MYSQL_ADMIN_PASSWD=$MYSQL_ADMIN_PASSWD" >> $PASSFILE

# change the permittion so that others can't read it
chmod 400 $PASSFILE

# allow administrator to access via network
mysql -u root -p$MYSQL_ROOT_PASSWD -e "GRANT ALL PRIVILEGES ON *.* TO $MYSQL_ADMIN_USER@'%' IDENTIFIED BY '$MYSQL_ADMIN_PASSWD' WITH GRANT OPTION;"
echo "+Done."

echo "Saved a password file: $PASSFILE"

# fuelphp db setting
echo "Setting up db config in fuelphp."
PRO=$APP/fuel/app/config/production/db.php
DEV=$APP/fuel/app/config/development/db.php
sed -i -e"s!ChangeMyName!$MYSQL_ADMIN_USER!" $PRO
sed -i -e"s!ChangeMyPassword!$MYSQL_ADMIN_PASSWD!" $PRO
sed -i -e"s!ChangeMyName!$MYSQL_ADMIN_USER!" $DEV
sed -i -e"s!ChangeMyPassword!$MYSQL_ADMIN_PASSWD!" $DEV
echo "+Done."

