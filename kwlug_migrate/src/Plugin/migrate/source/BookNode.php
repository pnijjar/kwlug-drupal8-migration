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
 *   id = "d6_book_node"
 * )
 * Add book title to node body.
 */
class BookNode extends UploadNode { 

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {

    $nid = $row->getSourceProperty('nid');
    $body_so_far = $row->getSourceProperty('body');

    // Find the book title of this book. 
    $query = $this->select('book', 'b')
      ->fields('b', array('bid'))
      ->condition('b.nid', $nid, '=');

    $query->join('node', 'nb', 'nb.nid = b.bid');
    $query->addField('nb', 'title');

    $book_info = $query->execute()->fetchAll();

    if ($book_info) { 


      foreach ($book_info as $b) { 
        $title = $b['title'];

        // TODO: Figure out how to set taxonomy term based 
        // on the title. Is this a process plugin?
        $row->setSourceProperty('book_title', $title);
        $row->setSourceProperty('book_id', $b['bid']);

        $body_so_far = "<p>Part of the book: <strong>" . 
          $title . "</strong></p>\n\n" .
          $body_so_far;

      } // end foreach 

      $row->setSourceProperty('body', $body_so_far);

    } // end if book info

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
      'book_id' => $this->t('NID of associated book'),
      'book_title' => $this->t('Title of associated book'),
    );

    $fields = array_merge($orig_fields, $new_fields);

    return $fields;

  } // end fields


} // end class
