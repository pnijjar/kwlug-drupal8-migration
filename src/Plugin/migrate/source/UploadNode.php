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
 *   id = "d6_upload_node"
 * )
 * Associate blogtags with blogs.
 */
class UploadNode extends D6Node { 

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {

    $nid = $row->getSourceProperty('nid');


    // This is copied from Upload.php
    $query = $this->select('upload', 'u')
      ->distinct()
      ->fields('u', array('fid', 'description', 'list'))
      ->condition('u.nid', $nid, '=');
    $row->setSourceProperty('upload', $query->execute()->fetchAll());


    // print_r($row);

    return parent::prepareRow($row);

  } // end prepareRow


  /**
   * {@inheritdoc}
   */
  public function fields() {
    /* Add a taxonomy_category field to find vidcasts.
     *
     */

    $orig_fields = parent::fields();

    $new_fields = array(
      'upload' => $this->t('Uploaded Files'),
    );

    $fields = array_merge($orig_fields, $new_fields);

    return $fields;

  } // end fields


} // end class
