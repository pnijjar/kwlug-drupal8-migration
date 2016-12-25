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
    $DEBUG_NID_START = 1053;
    $DEBUG_NID_END = 1056;


    $nid = $row->getSourceProperty("nid");

    // Look for associated presentation topics in relativity table
    // This is almost identical to the FLOSS Fund lookup. 
    // Factor into a helper method?
    $query = $this->select('node', 'p')
      ->fields('p', ['nid','title'])
      ->condition('p.type', 'presentation');
    $query->join('relativity', 'r', 'r.nid = p.nid');
    $query->condition('r.parent_nid', $nid, '=');

    $query->join('node_revisions', 'nr', 'nr.nid = p.nid');
    $query->addField('nr', 'body');

    $presentation_info = $query->execute()
      ->fetchAll();

    if ($presentation_info) { 

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


        $body_so_far = $row->getSourceProperty('body');
        $pbody = $p['body'];

        if ($body_so_far) { 
          $body_so_far = $body_so_far . "\n\n" . $pbody;
        } else { 
          $body_so_far = $pbody;
        } // end if body

        $row->setSourceProperty('body', $body_so_far);
        $row->setDestinationProperty('body', $body_so_far);

      } // end foreach

      // print_r($row);

    } else { 
      $row->setSourceProperty('presentation_title', '');
    } // end if presentation_info exists


    if ($nid >= $DEBUG_NID_START  && $nid <= $DEBUG_NID_END ) { 
      print_r("\n\row is\n");
      print_r($row);
    } 

    // TODO: Look for associated FLOSS Fund nominees 
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
      'floss_fund_nominee_nid' => $this->t('FLOSS Fund Nominee NID'),
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

