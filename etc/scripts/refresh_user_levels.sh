#!/bin/bash

# specify the directory where the app installed
APP=/var/www/ctfscore

# refresh all levels users have gained.
cd $APP
php oil r ctfscore:refresh_gained_levels

