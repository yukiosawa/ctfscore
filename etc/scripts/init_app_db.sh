#!/bin/sh

# load common setting
. $(dirname $0)/common.sh

# initialize application database
cd $APP
php oil r ctfscore:create_database
php oil r ctfscore:init_all_tables
php oil r session:create

