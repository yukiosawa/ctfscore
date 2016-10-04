#!/bin/sh

# load common setting
. $(dirname $0)/common.sh

HOST=localhost

mysqladmin -u $MYSQL_ADMIN_USER -p$MYSQL_ADMIN_PASSWD -h $HOST --protocol tcp ping || exit 1

exit 0

