<?php

/**
 * @file
 * Contains plugins to migrate meeting agenda nodes, 
 * merging them with presentation topics where applicable.
 */

namespace Drupal\kwlug_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\node\Plugin\migrate\source\d6\Node as D6Node;
use \DateTime;
use \DateTimeZone;


// no need for query() and getIds()? 
// How do you convert date, nodereference, filereference fields?
// We should be able to UPLOAD stuff

/**
 * @MigrateSource(
 *   id = "d6_agenda_node"
 * )
 */
class AgendaNode extends D6Node { 

  // Set start bigger than end to turn off debugging
  private $DEBUG_NID_START = 1026; // was 484
  private $DEBUG_NID_END = 1025;
  private $TARGET_TERM = 'vidcast';

  /**
   * {@inheritdoc}
   * Look for associated presentations via node relativity table.
   * If they exist then incorporate them.
   */
  public function prepareRow(Row $row) { 

    // This should not be hardcoded?
    $LOCAL_TIMEZONE = 'America/Toronto';
    $DEFAULT_MEETING_TIME = "19:00:00";
    $EMPTY_MEETING_TIME = "00:00:00";



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

      $p_nids = array();

      foreach ($presentation_info as $p) { 

        $title_so_far = $row->getSourceProperty('presentation_title');
        $ptitle = $p['title'];
        // Should I trust this? Maybe this is dangerous
        if ($title_so_far) { 
          $title_so_far = $title_so_far . "; " . $ptitle;
        } else { 
          $title_so_far = $ptitle;
        } // end if 
        $row->setSourceProperty('presentation_title', $title_so_far);

        // How do I add multiple NIDs?
        // An array, I guess.

        array_push($p_nids, $p['nid']);

        $body_so_far = $row->getSourceProperty('body');
        $pbody = $p['body'];

        if ($body_so_far) { 
          $body_so_far = $body_so_far . "\n\n* * *\n" . $pbody;
        } else { 
          $body_so_far = $pbody;
        } // end if body

        $row->setSourceProperty('body', $body_so_far);
        $row->setDestinationProperty('body', $body_so_far);

      } // end foreach

      $row->setSourceProperty('presentation_nid', $p_nids);

    } else { 
      $row->setSourceProperty('presentation_title', '');
    } // end if presentation_info exists


    // I do not know why this stuff doesn't migrate itself, 
    // but whatever.
    $query_agenda = $this->select('content_type_agenda','c')
      ->fields('c', ['field_emcee_uid', 'field_date_value', 
          'field_location_nid'])
      ->condition('c.nid', $nid, '=');
    $agenda_info = $query_agenda->execute()
      ->fetchAll();

    if ($agenda_info) { 

      // There SHOULD be only one row. I guess we are taking the last 
      // value if there are multiple. 
      // Lots of these will be NULL, though. 
      // Also we just want to append to the body if there is an emcee.
      foreach ($agenda_info as $a) { 
        $row->setSourceProperty('meeting_date', $a['field_date_value']);
        $row->setSourceProperty('emcee_uid', $a['field_emcee_uid']);
        $row->setSourceProperty('meeting_location_nid', $a['field_location_nid']);

        if ($a['field_emcee_uid']) { 
          $body_so_far = $row->getSourceProperty('body');


          $query_emcee = $this->select('users','u')
            ->fields('u', ['name'])
            ->condition('u.uid', $a['field_emcee_uid']);

          $emcee_info = $query_emcee->execute()->fetchAssoc();
          $emcee_name = "User " . $a['field_emcee_uid'];

          if ($emcee_info) { 
            $emcee_name = $emcee_info['name'];
          } // end emcee_info

          $extra_text = "\n\nThe host for this meeting was " 
              . $emcee_name
              . ".";

          if ($body_so_far) { 
            $body_so_far = $body_so_far . "\n* * *\n" . $extra_text;
          } else { 
            $body_so_far = $extra_text;
          } // end if body_so_far

          $row->setSourceProperty('body', $body_so_far);


        } // end if emcee exists


        // Ugh. Times get stored at 00:00:00, then Drupal does 
        // timezone magic to make the time incorrect. So munge the 
        // dates. 
        if ($a['field_date_value']) { 

          list($date, $time) = explode('T', $a['field_date_value']);
          // This should look like 2016-12-26T00:00:00
          
          if ((!$time) || ($time === $EMPTY_MEETING_TIME)) { 
            $target_time = $DEFAULT_MEETING_TIME;
          } else { 
            $target_time = $time;
          } // end set time

          $localdate = new DateTime( $date . "T" . $target_time,
              new DateTimeZone($LOCAL_TIMEZONE));

          $localdate->setTimeZone(new DateTimeZone('UTC'));
          
          $munged_date = $localdate->format('Y-m-d\TH:i:s');
          $row->setSourceProperty('meeting_date', $munged_date);

        } // end if field_date_value exists



      } // end loop $a
    } // end if agenda_info

    // Look for linked nominees now.
    // Look for associated presentation topics in relativity table
    // This SHOULD be a helper method. Suck. 
    // Oops. The agendas are the CHILDREN nodes. Suck.
    $query = $this->select('node', 'p')
      ->fields('p', ['nid','title'])
      ->condition('p.type', 'nominee');
    $query->join('relativity', 'r', 'r.parent_nid = p.nid');
    $query->condition('r.nid', $nid, '=');

    $query->join('node_revisions', 'nr', 'nr.nid = p.nid');
    $query->addField('nr', 'body');

    $nominee_info = $query->execute()
      ->fetchAll();

    if ($nominee_info) { 

      foreach ($nominee_info as $n) { 
        $row->setSourceProperty('floss_fund_nominee', $n['nid']);

      } // end each nominee_info
    } // end if nominee info



    // Look for attachments. This is tricky because we need to look
    // for attachments in ALL of the nids.
    $associated_nids = $row->getSourceProperty('presentation_nid');
    // Oh boy maybe I am screwing up $p_nids. But it should be
    // committed already.
    if (!$associated_nids) { 
      $associated_nids = array();
    } 
    array_push($associated_nids, $nid); 

    // This is copied from Upload.php
    $query = $this->select('upload', 'u')
      ->distinct()
      ->fields('u', array('fid', 'description', 'list'))
      ->condition('u.nid', $associated_nids, 'IN');
    $row->setSourceProperty('upload', $query->execute()->fetchAll());


    $meeting_date_raw = $row->getSourceProperty('meeting_date');
    // Looks like: 2016-03-07T00:00:00 
    
    $is_match = preg_match('/^\d\d\d\d-\d\d/', $meeting_date_raw,
      $substr_array);

    // There is NO WAY that there should not be a match, because
    // all (post-flexinode) agendas have a date.
    // HOWEVER, some podcasts should not be associated with some
    // agendas (laptop rescue missions). Unfortunately this ruins
    // SFD podcasts, which need to be added manually.
    if ($is_match && $row->getSourceProperty('presentation_nid')) { 

      $meeting_YYmm = $substr_array[0] . ":%";



      // Look for vidcasts 
      $query = $this->select('node', 'n')
        ->fields('n', array('nid'));
      $query->condition('n.title', $meeting_YYmm, 'LIKE');
      $query->join('term_node', 'tn', 'tn.nid = n.nid');
      $query->join('term_data', 'td', 'tn.tid = td.tid');
      $query->condition('td.name', 'vidcast', '=');

      $row->setSourceProperty('vidcast_nid', $query->execute()->fetchAll());

      // Look for podcasts
      $query = $this->select('node', 'n')
        ->fields('n', array('nid'));
      $query->condition('n.title', $meeting_YYmm, 'LIKE');
      $query->condition('n.type', 'podcast');
      $row->setSourceProperty('podcast_nid', $query->execute()->fetchAll());
      

    } // end if is_match



    if ($nid >= $this->DEBUG_NID_START  && $nid <= $this->DEBUG_NID_END ) { 
  
      print_r("\n row is\n");
      print_r($row);

      print_r("associated_nids: ");
      print_r($associated_nids);
      print_r("\n");

    } // end if debug

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
      'podcast_nid' => $this->t('Podcast NIDs'), 
      'vidcast_nid' => $this->t('Vidcast NIDs'), 
      'meeting_date' => $this->t('Meeting Date'),
      'meeting_location_nid' => $this->t('Meeting Location NID'),
      'emcee_uid' => $this->t('Meeting MC UID'),
      'podcast' => $this->t('Podcast'),
      'vidcast' => $this->t('Vidcast'),
      'upload' => $this->t('Array of attachments'),
    );

    // What was that about a fractal of bad design? Addition works 
    // in some cases and not in others. So use array_merge.

    $fields = array_merge($orig_fields, $new_fields);

    return $fields;

  } // end fields

} // end class

