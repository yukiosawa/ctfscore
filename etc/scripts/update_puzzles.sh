#!/bin/bash

# specify the directory where the app installed
APP=/var/www/ctfscore

# update ctf puzzles
cd $APP
php oil r ctfscore:update_puzzles ./ctfadmin/batch/puzzles.php

