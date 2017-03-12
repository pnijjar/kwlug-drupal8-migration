#!/bin/bash

# There had better be an argument that is the name of the migration to
# run. eg upgrade_d6_user_role

# There had better be a YAML file in the test folder under $srcdir

element=$1
srcdir=/home/linuxuser/kwlug-drupal-v05/web
testdir=$srcdir/modules/custom/kwlug_migrate/config/install

pushd .
cd $srcdir

time drush migrate-reset-status $element  --yes 
time drush config-import --partial --source=$testdir --yes
time drush migrate-rollback $element  --yes 
time drush migrate-import --execute-dependencies $element --yes --notify 

popd
