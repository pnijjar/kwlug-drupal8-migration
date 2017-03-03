#!/bin/bash

pushd .
cd /var/www/html/d8demo

time drush migrate-rollback --group=kwlug_migrate --yes 
time drush migrate-import --execute-dependencies --group=kwlug_migrate --yes --notify 

popd
