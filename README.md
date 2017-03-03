KWLUG Drupal 8 Migration Setup
==============================

This repository hosts sample code that might be helpful to study for
your own Drupal 8 migrations. There is an enormous blog post at 
<http://pnijjar.freeshell.org/2017/drupal8-migrate/> . 

This migration code was used to migrate <https://kwlug.org> from
Drupal 6 to Drupal 8. 


Interesting Locations
---------------------

- `web/modules/custom/kwlug_migrate` : migration YAML configs and plugins
- `web/modules/custom/kwlug_content_types` : configuration for content
  types and views.
- `web/modules/custom/kwlug_rss_enclosures` : hack to insert enclosure
  elements into RSS feeds (so that podcast feeds would work). 
- `web/modules/custom/kwlug_menu` : module to generate a menu
- `web/themes/kwlug_theme` : Adaptive theme subtheme for the site
- `bin` : helper scripts for the migration 


(Non)Installation
----------------

You probably cannot run this code yourself since
you do not have the Drupal 6 KWLUG database. If you want to try then
here are the steps:

- Install Composer
- Clone the repo. Say you clone it into a folder called
  `kwlug-website` .
- Go into `kwlug-website` and type `composer install` . This should
  sit for a long time and eventually download the drupal dependencies.
- Get the Drupal 6 database and files folder set up.
- Make an empty database for your Drupal 8 installation. 
- Create `web/sites/default/settings.php` and
  `web/sites/default/settings.local.php` to reflect your settings. Use
  the blog post as a guide. 
- Modify the scripts in the `bin` folder to point to your installation
  and your database.
- Run `bin/script-d8demo-restart.sh`

If all goes well (it probably won't) the website will then install
itself and data from the old website will be migrated. 
