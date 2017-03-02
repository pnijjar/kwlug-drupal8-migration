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
 *   id = "d6_presentation_node"
 * )
 */
class PresentationNode extends D6Node { 


  /**
   * {@inheritdoc}
   */
  public function query() {
    // Make a subquery of all the NIDs in the relativity table.
    // Return presentation nodes not in this set.
    $linked_presentations = $this->select('relativity', 'r')
      ->fields('r', array('nid'));

    $parent_q = parent::query();
    $parent_q->condition('n.type', 'presentation')
      ->condition('n.nid', $linked_presentations, 'NOT IN');
    
    return $parent_q;

  } // end query


} // end class
