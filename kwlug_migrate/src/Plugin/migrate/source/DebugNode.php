<?php

/**
 * @file
 * Contains plugins to migrate presentation topics that 
 * are NOT associated with meeting agendas.
 */

namespace Drupal\kwlug_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\node\Plugin\migrate\source\d6\Node as D6Node;


/**
 * @MigrateSource(
 *   id = "d6_debug_node"
 * )
 */
class DebugNode extends D6Node { 

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
