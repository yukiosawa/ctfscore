#!/bin/sh

# load common setting
. $(dirname $0)/common.sh

HOST=localhost

echo "Dumpping MySQL databases."

# dump without lock (lock only for taking snapshot)
MYSQL_DUMP_OPT='--single-transaction --all-databases --events'
# lock all tables during dump
#MYSQL_DUMP_OPT='--lock-all-tables --all-databases --events'
MYSQL_BKUP_DIR="$DIR/backup/mysql"
MYSQL_BKUP_FILE=$(date +%F-%H%M%S).sql.gz

if [ ! -e $MYSQL_BKUP_DIR ]; then
  mkdir -p $MYSQL_BKUP_DIR
fi

# gzip a dump file to save disk space
mysqldump -u $MYSQL_ADMIN_USER -p$MYSQL_ADMIN_PASSWD -h $HOST --protocol tcp $MYSQL_DUMP_OPT | gzip > $MYSQL_BKUP_DIR/$MYSQL_BKUP_FILE

if [ $? -ne 0 ]; then
  exit 1
fi

# change the permittion so that others can't read it
chmod 700 $MYSQL_BKUP_DIR/$MYSQL_BKUP_FILE

if [ $? -eq 0 ]; then
  echo "+Done. Saved as $MYSQL_BKUP_DIR/$MYSQL_BKUP_FILE"
  exit 0
else
  exit 1
fi

