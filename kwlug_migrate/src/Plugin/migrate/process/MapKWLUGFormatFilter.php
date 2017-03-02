<?php

/**
 * @file
 * Contains \Drupal\kwlug_migrate\Plugin\migrate\process\MapKWLUGFormatFilter.
 */

// https://www.advomatic.com/blog/drupal-8-migrate-from-drupal-6-with-a-custom-process-plugin

namespace Drupal\kwlug_migrate\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;


/**
 * This plugin manually maps old filter formats (php_code,
 * filtered_html, etc) and maps them to new ones. It deliberately
 * throws away a bunch of information about the old formats.
 *
 * @MigrateProcessPlugin(
 *   id = "map_kwlug_format_filter"
 * )
 */
class MapKWLUGFormatFilter extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    $filter_mapping = array(
      0 => 'restricted_html', // unknown but it exists
      1 => 'restricted_html', // filtered_html
      2 => 'full_html',       // php_code
      3 => 'full_html',       // full_html
      4 => 'restricted_html', // unknown. Some image format that has been lost. 
      5 => 'plain_text',      // messaging plain text. Unused.
    ); 

    if ($value != 1) { 

      print_r("Format filter input is ");
      print_r($value);
      print_r(" returning value ");
      print_r($filter_mapping[$value]);
      print_r("\n");

    } // end if


    $retval = $filter_mapping[$value];

    if (!$retval) { 
      $retval = 'restricted_html';
    } 

    return $retval;
  } // end transform
} // end class
