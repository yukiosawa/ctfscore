#!/bin/bash

# specify the directory where the app installed
APP=/var/www/ctfscore

# insert ctf random texts
cd $APP
php oil r ctfscore:insert_random_texts ./ctfadmin/batch/random_texts.php

