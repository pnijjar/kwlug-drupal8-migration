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
 *   id = "d6_blog_node"
 * )
 * Associate blogtags with blogs.
 */
class BlogNode extends UploadNode { 

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {

    $nid = $row->getSourceProperty('nid');

    // It is stupid to hardcode the tid but I will do it. 
    // I could also do an extra join in the query.
    $target_tids = array(
      'vidcast' => 259,
    );

    // Run a query to determine whether this node was tagged. If so, 
    // set the taxonomy category. 

    $query = $this->select('term_node', 'tn')
      ->fields('tn', array('tid'))
      ->condition('tn.nid', $nid, '=')
      ->condition('tn.tid', $target_tids, 'IN');
    
    $associated_tids = $query->execute()->fetchAll();

    if ($associated_tids) { 

      // Hrm. If there are multiple categories we are hosed. 
      // But there should NEVER be multiple categories. Let's leave
      // this up to the implementor to guarantee.
      
      $classification_tag = NULL;
      foreach ($associated_tids as $t) { 
        $tag = array_search($t['tid'], $target_tids);
        $classification_tag = $tag;
      } // end foreach

      $row->setSourceProperty('taxonomy_category', $classification_tag);

    } // end if associated_tids

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
      'taxonomy_category' => $this->t('Taxonomy Category'),
    );

    $fields = array_merge($orig_fields, $new_fields);

    return $fields;

  } // end fields


} // end class
