<?php

/**
 * @file
 * Contains plugins to migrate only Contributing Users
 */


namespace Drupal\kwlug_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\user\Plugin\migrate\source\d6\User as D6User;

/**
 * extend user to filter out users that have never 
 * authored a node. These are "Contributing" users.
 * 
 * 
 * @MigrateSource(
 *   id = "d6_contributing_user"
 * )
 */
class ContributingUser extends D6User { 

  // I think we just need to specify prepareRow as the filter. 
  // There are other functions, though: 
  // fields(), query(), getIDs()
  // but I hope these are inherited

  /** 
   * {@inheritdoc}
   */
  public function query() { 
    // Make a subquery of all the UIDs who have authored nodes.
    $node_authors = $this->select('node','n')
      ->fields('n', array('uid'));

    return $this->select('users',u)
      ->fields('u', array_keys($this->baseFields()))
      ->condition('u.uid', 0, '>')
      ->condition('u.uid', $node_authors, 'IN');

  } // end query


  /**
   * {@inheritdoc}
   */
/**********
   public function prepareRow(Row $row) { 
     $uid = $row->getSourceProperty("uid");

     // range(0,1) is like LIMIT(1)
     $authored_nid = $this->select('node', 'n')
         ->condition('n.uid', $uid, '=')
         ->range(0,1)
         ->fields('n', ['nid'])
         ->execute()
         ->fetchField();

     // How to tell if query is empty?
     // Look at the result?
     if (! $authored_nid) { 
       return FALSE;
     } 

     return parent::prepareRow($row);
   } // end prepareRow
***********/



} // end class
