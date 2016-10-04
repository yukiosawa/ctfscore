# app installed
APP=/var/www/ctfscore

DIR=$(dirname $0)
cd $DIR

echo "[$0 started by $(whoami)@$(hostname)]"

# read MySql password if already have been set up
PASSFILE=$DIR/.mysql_password
echo "Loading a password file: $PASSFILE"
if [ -e $PASSFILE ]; then
    . $PASSFILE
    echo "+Done."
else
    echo "+Not found."
fi


# functions
rm_password_file() {
    echo "Removing an old file: $PASSFILE"
    if [ -e $PASSFILE ]; then
	rm $PASSFILE
	echo "+Done."
    else
	echo "+Not found."
    fi
}

