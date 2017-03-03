#!/bin/bash

# Uninstall modules used only for migrations. Order is important!

INSTALLPATH=/home/linuxuser/kwlug-drupal-v05/web

pushd .
cd $INSTALLPATH

PACKAGES=" \
  kwlug_migrate
  migrate_upgrade
  migrate_tools
  migrate_plus
  migrate_source_csv
"

for i in $PACKAGES
do
  drush pm-uninstall ${i} --yes
done

popd
