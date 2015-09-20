#!/bin/bash

# specify the directory where the app installed
APP=/var/www/ctfscore

# update ctf start and end times
cd $APP
php oil r ctfscore:update_times ./ctfadmin/batch/times.php

