#!/bin/bash

# Uninstall modules used only for migrations. Order is important!
# BEWARE: This will also drop migrate_map_* and migrate_message_* .

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

migratemap=`drush sqlq "show tables like 'migrate_map_%'"`
migratemsg=`drush sqlq "show tables like 'migrate_message_%'"`

for i in $migratemap $migratemsg
do
  echo Dropping table $i
  drush sqlq "drop table $i"
done

popd
