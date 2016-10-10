#!/bin/bash

# load common setting
. $(dirname $0)/common.sh

if [ -z $MYSQL_ADMIN_USER ] ; then
    echo "Failed to load username."
    exit 1
fi
if [ -z $MYSQL_ADMIN_PASSWD ]; then
    echo "Failed to load password."
    exit 1
fi

mysqladmin -u $MYSQL_ADMIN_USER -p$MYSQL_ADMIN_PASSWD -h $HOST --protocol tcp ping || exit 1

exit 0

