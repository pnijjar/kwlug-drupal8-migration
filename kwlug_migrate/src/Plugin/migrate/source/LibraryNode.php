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
 *   id = "d6_library_node"
 * )
 */
class LibraryNode extends D6Node { 

  /**
   * {@inheritdoc}
   * Look for associated presentations via node relativity table.
   * If they exist then incorporate them.
   */
  public function prepareRow(Row $row) {
    $nid = $row->getSourceProperty('nid');


    // Grab nodes from node, but join with other library related
    // tables.
    // Ignore borrower, cover, status, review
    $library_books = $this->select('library','l')
      ->fields('l', ['booktitle', 'publisher', 'copyright', 
	'isbn', 'price', 'pages', 'synopsis', 'contents', 'donator'])
      ->condition('l.nid', $nid, '=');
    $library_books->join('library_authors', 'a', 'a.nid = l.nid');
    $library_books->addField('a', 'author');

    $library_info = $library_books->execute()
      ->fetchAll();

    if ($library_info) { 
      // print_r("Library info is: \n");
      // print_r($library_info);

      foreach ($library_info as $l) { 
        $row->setSourceProperty('booktitle', $l['booktitle']);
        $row->setSourceProperty('publisher', $l['publisher']);
        $row->setSourceProperty('copyright', $l['copyright']);
        $row->setSourceProperty('isbn', $l['isbn']);
        $row->setSourceProperty('price', $l['price']);
        $row->setSourceProperty('pages', $l['pages']);
        $row->setSourceProperty('synopsis', $l['synopsis']);
        $row->setSourceProperty('contents', $l['contents']);
        $row->setSourceProperty('donator', $l['donator']);
        $row->setSourceProperty('author', $l['author']);

      } // end foreach

    } else { 
      throw new MigrateSkipRowException();
      
    } // end if library info
    

  } // end prepareRow
  /**
   * {@inheritdoc}
   */
  public function fields() {

    $orig_fields = parent::fields();

    $new_fields = array(
      'booktitle' => $this->t('Book title'),
      'author' => $this->t('Author'),
      'publisher' => $this->t('Publisher'),
      'copyright' => $this->t('Copyright'),
      'isbn' => $this->t('ISBN'),
      'price' => $this->t('Price'),
      'pages' => $this->t('Pages'),
      'synopsis' => $this->t('Synopsis'),
      'contents' => $this->t('Contents'),
      'donator' => $this->t('Donator'),
    );

    // What was that about a fractal of bad design? Addition works
    // in some cases and not in others. So use array_merge.

    $fields = array_merge($orig_fields, $new_fields);

    return $fields;

  } // end fields


} // end class
