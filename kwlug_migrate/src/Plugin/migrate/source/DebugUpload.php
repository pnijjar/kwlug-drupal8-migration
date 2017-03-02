<?php

/**
 * @file
 * Contains plugins to migrate presentation topics that 
 * are NOT associated with meeting agendas.
 */

namespace Drupal\kwlug_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\file\Plugin\migrate\source\d6\Upload as D6Upload;


/**
 * @MigrateSource(
 *   id = "d6_debug_upload"
 * )
 */
class DebugUpload extends D6Upload { 

  /**
   * {@inheritdoc}
   * Look for associated presentations via node relativity table.
   * If they exist then incorporate them.
   */
  public function prepareRow(Row $row) {
    drush_print_r($row);
    drush_print_r("\n");

    return parent::prepareRow($row);

  } // end prepareRow

  

} // end class
