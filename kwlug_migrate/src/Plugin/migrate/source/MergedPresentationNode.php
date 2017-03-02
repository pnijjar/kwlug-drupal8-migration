<?php

/**
 * @file
 * Contains plugins to migrate presentation topics that 
 * ARE associated with meeting agendas. Basically, I am making a map. 
 */

namespace Drupal\kwlug_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\node\Plugin\migrate\source\d6\Node as D6Node;


/**
 * @MigrateSource(
 *   id = "d6_merged_presentation_node"
 * )
 */
class MergedPresentationNode extends D6Node { 


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
      ->condition('n.nid', $linked_presentations, 'IN');
    
    return $parent_q;

  } // end query

  /**
   * {@inheritdoc}
   * Look for associated presentations via node relativity table.
   * If they exist then incorporate them.
   */
  public function prepareRow(Row $row) {
    $nid = $row->getSourceProperty('nid');

    $linked_presentation_query = $this->select('relativity', 'r')
      ->fields('r', array('parent_nid'))
      ->condition('r.nid', $nid, '=');

    $linked_presentations = $linked_presentation_query->execute()
      ->fetchAll();

    if ($linked_presentations) { 
      // print_r("Linked presentations are");
      // print_r($linked_presentations);

      foreach ($linked_presentations as $p) { 
        $row->setSourceProperty('agenda_nid', $p['parent_nid']);
        $row->setDestinationProperty('agenda_nid', $p['parent_nid']);
      } // end foreach p

    } else {
      throw new MigrateSkipRowException();
    } // end if linked_presentations

    // print_r($row);

    return parent::prepareRow($row);

  } // end prepareRow


  /**
   * {@inheritdoc}
   */
  public function fields() {

    $orig_fields = parent::fields();

    $new_fields = array(
      'agenda_nid' => $this->t('Associated Agenda node'),
    );

    $fields = array_merge($orig_fields, $new_fields);

    return $fields;

  } // end fields




} // end class
