#!/bin/bash

# This script updates dependencies of your projects and performs necessary migration.
# You should call this script after you perform git pull


# Update dependencies
if [ -f composer.json ]; then
   php composer.phar update
fi

# Execute database migration
(cd doc; ./update.sh)
