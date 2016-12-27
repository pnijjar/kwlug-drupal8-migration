<?php

/**
 * @file
 * Contains \Drupal\kwlug_migrate\Plugin\migrate\process\DebugContents.
 */

// https://www.advomatic.com/blog/drupal-8-migrate-from-drupal-6-with-a-custom-process-plugin

namespace Drupal\kwlug_migrate\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;


/**
 * This plugin takes in an array that encloses a [value] and returns
 * it, because I cannot figure out the YML syntax to do this properly.
 *
 * @MigrateProcessPlugin(
 *   id = "extract_value"
 * )
 */
class ExtractValue extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    // I expect: $value with subindices [value] and [delta]

    $retval = $value['value'];

    return $retval;
  }
}
