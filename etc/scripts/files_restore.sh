#!/bin/sh

if [ -z $1 ]; then
  echo "Usage: sudo $(basename $0) file"
  exit 1
fi
file=$1

# load common setting
. $(dirname $0)/common.sh

echo "Restoring contents files."

ORG_CWD=$(pwd)
cd $APP

# extract files
tar zxvf $ORG_CWD/$file
if [ $? -eq 0 ]; then
    echo "+Done. Restored from $file"
    exit 0
else
    exit 1
fi
