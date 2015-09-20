#!/bin/bash

# specify the directory where the app installed
APP=/var/www/ctfscore

# insert ctf users
cd $APP
php oil r ctfscore:insert_users ./fuel/app/ctfadmin/batch/users.php

