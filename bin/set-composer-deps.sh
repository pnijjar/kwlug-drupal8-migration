#!/bin/bash

# Set project dependencies via composer and not via my
# kwlug_dependencies module, because ugh.

INSTALLPATH=/home/linuxuser/kwlug-drupal-v05
PACKAGES=" \
  admin_toolbar
  redirect
  migrate_upgrade
  migrate_plus
  migrate_tools
  riddler
  adaptivetheme
"

for i in $PACKAGES
do
  # composer --no-update --update-with-dependencies require drupal/${i}
  composer --update-with-dependencies require drupal/${i}
done


