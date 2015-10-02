#!/bin/bash

# specify the directory where the app installed
APP=/var/www/ctfscore

# update levels
cd $APP
php oil r ctfscore:update_levels ./ctfadmin/batch/levels.php

