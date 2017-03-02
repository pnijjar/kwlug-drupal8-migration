<?php

/**
 * @file
 * Contains plugins to 'flexinode-1' and 'flexinode-2'
 * nodes (ancient meeting nodes) into agenda nodes, 
 */

namespace Drupal\kwlug_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\node\Plugin\migrate\source\d6\Node as D6Node;
use \DateTime;
use \DateTimeZone;


/**
 * @MigrateSource(
 *   id = "d6_flexinode_agenda_node"
 * )
 */
class FlexinodeAgendaNode extends D6Node { 

  // Set start bigger than end to turn off debugging
  private $DEBUG_NID_START = 0; // was 484
  private $DEBUG_NID_END = 0;

  /*
   * Split a body with asterisks 
   * Takes in one string and returns another that has * * * inserted 
   * if the string is not empty.
   */
  private function insert_sep_if_necessary(&$content) { 
    // This should be a little helper function. 
    if (strlen($content) > 0) { 
      $content = $content . "\n*  *  *\n";
    } // end if strlen

  } // end insert_sep_if_necessary


  /* Add an array of elements to a body string.
   */
  private function append_to_body($indices, $info, &$content) { 


      foreach ($indices as $item) { 
        if ($info[$item]) { 

          // Empty fields are specified as 'N;'
          $fieldval = $info[$item]['textual_data'];

          if ($fieldval && $fieldval !== 'N;') { 

            $content .= $info[$item]['textual_data'];

          } // end if fieldval is not empty

        }  // end if info[$item]

      } // end foreach opening element 



  } // end append_to_body


  /**
   * {@inheritdoc}
   * By default we get nodes of type 'flexinode-2' (which are meeting
   * agendas). We need to merge 'flexinode-1' nodes, AND grab 
   * field data from the flexinode_field and flexinode_data. 
   * 
   */
  public function prepareRow(Row $row) { 

    /* 
     *  mysql> select field_id,ctype_id,label,field_type from flexinode_field;
     *  +----------+----------+------------------------+--------------+
     *  | field_id | ctype_id | label                  | field_type   |
     *  +----------+----------+------------------------+--------------+
     *  |        2 |        1 | Abstract               | textarea     |
     *  |        3 |        1 | Presentation Material  | textarea     |
     *  |        4 |        1 | Reference material     | url          |
     *  |        5 |        1 | Attachment             | file         |
     *  |       11 |        2 | Pre-meeting Topic      | presentation |
     *  |       12 |        2 | Location               | textarea     |
     *  |       10 |        2 | Presentation Topic     | presentation |
     *  |       13 |        2 | Meeting host / emcee   | usergroup    |
     *  |       14 |        2 | Pre-meeting activities | textfield    |
     *  |       15 |        2 | Introduction           | textarea     |
     *  +----------+----------+------------------------+--------------+
     * 
     *  Body: 
     *  12: location
     *  11: pre-meeting
     *  15: introduction
     *  2: abstract
     *  3: presentation material
     *  4: reference material
     *  13: host/emcee
     *  5: attachment
     */




    // This should not be hardcoded?
    $LOCAL_TIMEZONE = 'America/Toronto';
    $DEFAULT_MEETING_TIME = "19:00:00";
    $EMPTY_MEETING_TIME = "00:00:00";

    // Magic numbers make me squirm.
    $F_ABSTRACT = 2;
    $F_PRESENTATION_MATERIAL = 3;
    $F_REFERENCE_MATERIAL = 4;
    $F_ATTACHMENT = 5; 
    $F_RELATIVITY = 10;
    $F_PREMEETING = 11;
    $F_LOCATION = 12;
    $F_EMCEE = 13;
    $F_PREMEETING_ACTIVITIES = 14;
    $F_INTRODUCTION = 15;


    $nid = $row->getSourceProperty("nid");

    // I guess I can get 10-15 via this nid. Then life gets hard
    // because there are sometimes multiple presentations associated
    // with a meeting. 
    $query = $this->select('flexinode_data', 'fd')
      ->fields('fd', ['nid','field_id','textual_data','numeric_data'])
      ->condition('fd.nid', $nid, '=');
    $agenda_info = $query->execute()->fetchAllAssoc('field_id');

    // There will be many records for one NID. (But there should only
    // be one agenda per NID.) 

    if ($agenda_info) { 


      $body_so_far = "";
      if ($agenda_info[$F_EMCEE]) { 
        // This is the one field that is numeric. suck.
        $emcee_uid = $agenda_info[$F_EMCEE]['numeric_data'];
        $row->setSourceProperty('emcee_uid', $emcee_uid);
      } //end if emcee

      
      $opening_elements = array($F_LOCATION, 
          $F_PREMEETING, $F_PREMEETING_ACTIVITIES, $F_INTRODUCTION);

      $this->append_to_body($opening_elements,
          $agenda_info, $body_so_far);

        

      if ($agenda_info[$F_RELATIVITY]) { 

        // Now we need to unserialize the data.
        $ser_relations = $agenda_info[$F_RELATIVITY]['textual_data'];
        $relations = unserialize($ser_relations);

        // Now we need to subquery to get each of the presentation
        // topics. 

        $presentation_nids = array();
        $presentation_title = "";
        
        if ($relations) { 

          foreach ($relations as $p_nid) { 

            $pquery = $this->select('flexinode_data', 'fd')
              ->fields('fd', ['nid','field_id','textual_data','numeric_data'])
              ->condition('fd.nid', $p_nid, '=');
            $presentation_info = $pquery->execute()->fetchAllAssoc('field_id');

            array_push($presentation_nids, $p_nid); 

            $presentation_elements = array(
              $F_ABSTRACT,
              $F_PRESENTATION_MATERIAL,
              $F_REFERENCE_MATERIAL,
            ); 

            $this->insert_sep_if_necessary($body_so_far); 

            $this->append_to_body($presentation_elements,
                $presentation_info, $body_so_far);


            // Get the node title
            $tquery = $this->select('node', 'n')
              ->fields('n', ['title'])
              ->condition('n.nid', $p_nid, '=');
            $t = $tquery->execute()->fetch();

            if ($t) { 

              if ($presentation_title) { 
                $presentation_title .= "; ";
              } // end if presentation_title

              $presentation_title .= $t['title'];

            } // end if t



          } // end foreach relations

        } // end if relations



      } // end if relativity exists

      $row->setSourceProperty('body', $body_so_far);
      $row->setSourceProperty('presentation_title', $presentation_title);
      $row->setSourceProperty('presentation_', $presentation_nids);


    } else { 

      throw new MigrateSkipRowException();

    } // end if agenda_info



    if ($nid >= $this->DEBUG_NID_START  && $nid <= $this->DEBUG_NID_END ) { 
  
      print_r("\n row is\n");
      print_r($row);

    } // end if debug

    // TODO: Look for associated FLOSS Fund nominees 
    return parent::prepareRow($row);

  } // end prepareRow


  /**
   * {@inheritdoc}
   */
  public function fields() { 
    /* We need a few extra fields but not as many as for
     * regular agenda nodes (since there were no podcasts/
     * FF nominees/vidcasts back then). 
     */

    $orig_fields = parent::fields();

    $new_fields = array(
      'presentation_title' => $this->t('Presentation Title'), 
      'presentation_nid' => $this->t('Presentation Node(s) (ugh)'), 
      'floss_fund_nominee_nid' => $this->t('FLOSS Fund Nominee NID'),
      'meeting_date' => $this->t('Meeting Date'),
      'meeting_location_nid' => $this->t('Meeting Location NID'),
      'emcee_uid' => $this->t('Meeting MC UID'),
      'podcast' => $this->t('Podcast'),
      'vidcast' => $this->t('Vidcast'),
    );

    // What was that about a fractal of bad design? Addition works 
    // in some cases and not in others. So use array_merge.

    $fields = array_merge($orig_fields, $new_fields);

    return $fields;

  } // end fields

} // end class

