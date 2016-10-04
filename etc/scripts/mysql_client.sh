#!/bin/sh

# load common setting
. $(dirname $0)/common.sh

HOST=localhost

echo "Checking if MySQL is up."
if ! $DIR/mysql_ping.sh; then
    exit 1
fi

mysql -u $MYSQL_ADMIN_USER -p$MYSQL_ADMIN_PASSWD -h $HOST --protocol tcp

