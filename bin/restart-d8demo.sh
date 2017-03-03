#!/bin/bash

INSTALLPATH=/home/linuxuser/kwlug-drupal-v05/web
BINPATH=/home/linuxuser/kwlug-drupal-v05/vendor/drush/drush
PATH=$BINPATH:$PATH

pushd .
cd $INSTALLPATH 
# chown -R www-data: .

time drush site-install --account-pass=stupid-password  \
    --site-name="D8 Demo " --yes 

# time drush en migrate_tools migrate_drupal migrate_plus --yes
# https://github.com/drush-ops/drush/issues/1625
time drush cset system.site uuid 3112d604-7bb2-4dba-b418-f4f542f2682c --yes

time drush config-import --partial \
    --source=modules/custom/kwlug_content_types/premigrate_settings --yes
time drush en kwlug_migrate --yes
time drush en kwlug_dependencies --yes
# time drush dl adaptivetheme --yes

time drush en kwlug_theme --yes
time drush migrate-import --verbose --execute-dependencies --group=kwlug_migrate  \
    --yes 
time drush config-import --partial \
    --source=modules/custom/kwlug_content_types/postmigrate_settings --yes
time drush en kwlug_menu --yes

drush user-unblock --name=pnijjar --notify

popd
date
