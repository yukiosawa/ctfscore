#!/bin/sh

if [ -z $1 ]; then
  echo "Usage: $(basename $0) file"
  exit 1
fi
file=$1

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

echo "Restoring MySQL databases."

# gunzip and cut the .gz extension if it's a gzip file
filetype=$(file $file | awk '{print $2}')
if [ $filetype = "gzip" ]; then
  gunzip $file
  file=$(echo $file | sed 's/\.gz$//')
fi

mysql -u $MYSQL_ADMIN_USER -p$MYSQL_ADMIN_PASSWD -h $HOST --protocol tcp < $file

if [ $? -eq 0 ]; then
  echo "+Done. Restored from $file"
  exit 0
else
  exit 1
fi

