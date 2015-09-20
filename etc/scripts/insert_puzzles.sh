#!/bin/bash

# specify the directory where the app installed
APP=/var/www/ctfscore

# insert ctf puzzles
cd $APP
php oil r ctfscore:insert_puzzles ./ctfadmin/batch/puzzles.php

