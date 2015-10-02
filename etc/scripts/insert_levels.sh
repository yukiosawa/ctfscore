#!/bin/bash

# specify the directory where the app installed
APP=/var/www/ctfscore

# insert levels
cd $APP
php oil r ctfscore:insert_levels ./ctfadmin/batch/levels.php

