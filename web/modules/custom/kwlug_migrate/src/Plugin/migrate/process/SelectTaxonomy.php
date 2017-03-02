<?php

/**
 * @file
 * Contains * \Drupal\kwlug_migrate\Plugin\migrate\process\SelectTaxonomy
 */

namespace Drupal\kwlug_migrate\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * This plugin picks out the taxonomy vocabularies we want to keep.
 * returns the taxonomy term if successful, FALSE otherwise.
 *
 * @MigrateProcessPlugin(
 *   id = "select_taxonomy"
 * )
 */
class SelectTaxonomy extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    $allowed_taxonomies = array(
      'blogtags' => 9,
    );

    // print_r("\n taxonomy term is: ");
    // print_r($value);

    if (in_array($value, $allowed_taxonomies)) { 
      // print_r("...Allowed taxonomy. ");
      return $value;
    } // end if
    
    return FALSE;
    

  } // end transform
} // end class
