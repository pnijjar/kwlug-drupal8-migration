<?php

/**
 * @file
 * Contains plugins to migrate meeting agenda nodes, 
 * merging them with presentation topics where applicable.
 */

namespace Drupal\kwlug_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\node\Plugin\migrate\source\d6\Node as D6Node;


// no need for query() and getIds()? 
// How do you convert date, nodereference, filereference fields?
// We should be able to UPLOAD stuff

/**
 * @MigrateSource(
 *   id = "d6_agenda_node"
 * )
 */
class AgendaNode extends D6Node { 

  /**
   * {@inheritdoc}
   * Look for associated presentations via node relativity table.
   * If they exist then incorporate them.
   */
  public function prepareRow(Row $row) { 
    $nid = $row->getSourceProperty("nid");

    // Look for associated presentation topics in relativity table
    // This is almost identical to the FLOSS Fund lookup. 
    // Factor into a helper method?
    $query = $this->select('node', 'p')
      ->fields('p', ['nid','title'])
      ->condition('p.type', 'presentation');
    $query->join('relativity', 'r', 'r.nid = p.nid');
    $query->condition('r.parent_nid', $nid, '=');


    $presentation_info = $query->execute()
      ->fetchAll();

    if ($presentation_info) { 
      // What does the body look like?

      foreach ($presentation_info as $p) { 

        $title_so_far = $row->getSourceProperty('presentation_title');
        $ptitle = $p['title'];

        // Should I trust this? Maybe this is dangerous
        if ($title_so_far) { 
          $title_so_far = $title_so_far . ", " . $ptitle;
        } else { 
          $title_so_far = $ptitle;
        } // end if 
        $row->setSourceProperty('presentation_title', $ptitle);

        // How do I add multiple NIDs?
        $row->setSourceProperty('presentation_nid', $p['nid']);

      } // end foreach


      // print_r($row);

    } else { 
      $row->setSourceProperty('presentation_title', 'NO_TITLE');

    } // end if presentation_info exists



    // Look for associated FLOSS Fund nominees 

    return parent::prepareRow($row);

  } // end prepareRow


  /**
   * {@inheritdoc}
   */
  public function fields() { 
    /* extra fields: 
     * - presentation title
     * - presentation body -- merge?
     * - url path - merge? append?
     * 
     * We also do NOT need to map the node relativity thing now.
     */

    $orig_fields = parent::fields();

    $new_fields = array(
      'presentation_title' => $this->t('Presentation Title'), 
      'presentation_nid' => $this->t('Presentation Node (ugh)'), 
      'floss_fund_nominee' => $this->t('FLOSS Fund Nominee'),
      'podcast' => $this->t('Podcast'),
      'vidcast' => $this->t('Vidcast'),
    );

    // What was that about a fractal of bad design? Addition works 
    // in some cases and not in others. So use array_merge.

    $fields = array_merge($orig_fields, $new_fields);

    print_r("fields are: ");
    print_r($fields);

    return $fields;

  } // end fields

} // end class

