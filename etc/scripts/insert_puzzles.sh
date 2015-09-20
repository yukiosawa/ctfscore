#!/bin/bash

# specify the directory where the app installed
APP=/var/www/ctfscore

# insert ctf puzzles
cd $APP
php oil r ctfscore:insert_puzzles ./fuel/app/ctfadmin/batch/puzzles.php

