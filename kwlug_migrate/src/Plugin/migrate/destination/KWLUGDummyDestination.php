<?php

namespace Drupal\kwlug_migrate\Plugin\migrate\destination;

use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Plugin\migrate\destination\DestinationBase;
use Drupal\migrate\Row;

/**
 * @MigrateDestination(
 *   id = "kwlug_dummy",
 *   requirements_met = true
 * )
 */
class KWLUGDummyDestination extends DestinationBase {
  // This is a copy and paste of the DummyDestination from the testing
  // suite. 

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['value']['type'] = 'string';
    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  public function fields(MigrationInterface $migration = NULL) {
    return ['value' => 'Dummy value'];
  }

  /**
   * {@inheritdoc}
   */
  public function import(Row $row, array $old_destination_id_values = array()) {
    
    $val = ['value' => $row->getDestinationProperty('value')];

    // print_r("In kwlug_dummy import. value is: ");
    // print_r($val);

    return($val);
  }

}
