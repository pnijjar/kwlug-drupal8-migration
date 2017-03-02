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
 * This plugin shows the contents of a field, then pushes it down the pipeline.
 *
 * @MigrateProcessPlugin(
 *   id = "debug_contents"
 * )
 */
class DebugContents extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    if ($value) { 
      print_r($value);
      print_r("\n");
    } 
    return $value;
  }
}
