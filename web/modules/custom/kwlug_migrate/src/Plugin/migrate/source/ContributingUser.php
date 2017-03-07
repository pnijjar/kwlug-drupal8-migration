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

  // I think we just need to specify query() for the filter.
  // If you do prepareRow() then a bunch of entries are listed as 
  // "not processed". 
  // There are other functions, though: 
  // fields(), getIDs() but I hope these are inherited

  /** 
   * {@inheritdoc}
   */
  public function query() { 
    // Make a subquery of all the UIDs who have authored nodes.
    $node_authors = $this->select('node','n')
      ->fields('n', array('uid'));
      

    $comment_authors = $this->select('comments','c')
      ->fields('c', array('uid'));

    $node_authors->union($comment_authors);

    return $this->select('users','u')
      ->fields('u', array_keys($this->baseFields()))
      ->condition('u.uid', 0, '>')
      ->condition('u.uid', $node_authors, 'IN');


  } // end query


} // end class
