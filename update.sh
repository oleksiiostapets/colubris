#!/bin/bash

# This script updates dependencies of your projects and performs necessary migration.
# You should call this script after you perform git pull

RED=$(printf "\x1b[31m")
NORMAL=$(printf "\x1b[0m")

function say {
    echo -n " =>" $RED
    echo -n $*
    echo $NORMAL
}


say "Updating main repository"

git pull

say "Calling Composer PHP to update dependencies"

# Update dependencies
if [ -f composer.json ]; then
   [ -f composer.phar ] || curl -sS https://getcomposer.org/installer | php
   php composer.phar update
fi

say "Updating Agile Toolkit"
( cd atk4; git pull origin 4.3 )

say "Updating Agile Toolkit Old Addons"
( cd atk4-addons; git pull origin master )

say "You are up-to-date!!!";
