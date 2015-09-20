#!/bin/bash

# specify the directory where the app installed
APP=/var/www/ctfscore

# initialize application database
cd $APP
php oil r ctfscore:create_database
php oil r ctfscore:init_all_tables

