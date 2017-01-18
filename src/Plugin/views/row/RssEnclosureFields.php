<?php

namespace Drupal\kwlug_enclosure_rss\Plugin\views\row;


use Drupal\views\Plugin\views\row\RssFields as RssFields;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Renders an RSS item based on fields.
 *
 * @ViewsRow(
 *   id = "rss_enclosure_fields",
 *   title = @Translation("Fields with Enclosures"),
 *   help = @Translation("Display fields as RSS items, including
 *   an enclosure."),
 *   theme = "views_view_row_enclosure_rss",
 *   display_types = {"feed"}
 * )
 */
class RssEnclosureFields extends RssFields {

  /**
   * 
   * Does the row plugin support to add fields to its output.
   * @var bool
   */
  protected $usesFields = TRUE;

  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['enclosure_field_options']['contains']['enclosure_field_url'] = array('default' => '');
    $options['enclosure_field_options']['contains']['enclosure_field_length'] = array('default' => '');
    $options['enclosure_field_options']['contains']['enclosure_field_type'] = array('default' => '');


    return $options;
  }

  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $initial_labels = array('' => $this->t('- None -'));
    $view_fields_labels = $this->displayHandler->getFieldLabels();
    $view_fields_labels = array_merge($initial_labels, $view_fields_labels);


    $form['enclosure_field_options'] = array(
      '#type' => 'details',
      '#title' => $this->t('Enclosure settings'),
      '#open' => TRUE,
    );

    $form['enclosure_field_options']['enclosure_field_url'] = array(
      '#type' => 'select',
      '#title' => $this->t('Enclosure URL'),
      '#description' => $this->t('The fully-qualified URL for this
      enclosure (eg
      https://archive.org/download/kwlug-2017-01-09-vagrant/kwlug-2017-01-09-vagrant.mp3)'),
      '#options' => $view_fields_labels,
      '#default_value' =>
      $this->options['enclosure_field_options']['enclosure_field_url'],
      '#required' => FALSE,
    );

    $form['enclosure_field_options']['enclosure_field_length'] = array(
      '#type' => 'select',
      '#title' => $this->t('Enclosure Length'),
      '#description' => $this->t('The length of this enclosure, in
      bytes (!)'),
      '#options' => $view_fields_labels,
      '#default_value' =>
      $this->options['enclosure_field_options']['enclosure_field_length'],
      '#required' => FALSE,
    );

    $form['enclosure_field_options']['enclosure_field_type'] = array(
      '#type' => 'select',
      '#title' => $this->t('Enclosure Type'),
      '#description' => $this->t('The MIME type of this content (eg
      audio/mpeg )'),
      '#options' => $view_fields_labels,
      '#default_value' =>
      $this->options['enclosure_field_options']['enclosure_field_type'],
      '#required' => FALSE,
    );


  }

  // @todo: validate that either all three fields are empty or all
  // three fields are nonempty. 

  public function render($row) {
  
    $build = parent::render($row);


    static $row_index;
    if (!isset($row_index)) {
      $row_index = 0;
    }

    if ($row_index == -1) { 
      dsm("Before setting row\n");
      dpm($build['#row']->title);
    } 


    $item = $build['#row'];

    $enc_url = $this->getField($row_index, 
      $this->options['enclosure_field_options']['enclosure_field_url']);
    $enc_length = $this->getField($row_index, 
      $this->options['enclosure_field_options']['enclosure_field_length']);
    $enc_type = $this->getField($row_index, 
      $this->options['enclosure_field_options']['enclosure_field_type']);

    if ($enc_url) { 
      $item->enclosure = array ( 
        'url' => $enc_url,
        'length' => $enc_length,
        'type' => $enc_type,
      ); 

      $item->enclosure_url = $enc_url;
     
    } // end if url exists


    // There is a bug when you use paths for links. 
    // https://www.drupal.org/node/2423913 has the fix that breaks
    // things.
    $abs_link = Url::fromUserInput(
      $this->getField($row_index, $this->options['link_field']),
      $options = array('absolute'=>TRUE)
    );
    $item->link = $abs_link->toString();

    // The same bug affects the GUID. Why?
    $abs_link = Url::fromUserInput(
      $this->getField(
        $row_index, 
        $this->options['guid_field_options']['guid_field']), 
      $options = array('absolute'=>TRUE)
    );

    $elem_index = 0;
    foreach ($item->elements as $e) { 
      if ($e['key'] == 'guid') { 
        $item->elements[$elem_index]['value'] = $abs_link->toString();
      } // end if 
      $elem_index ++;
    } // end foreach elements

    $build['#row'] = $item;

    if ($row_index == 1) { 
      dsm("After setting row 16 \n");
      foreach ($build['#row'] as $i) { 
        dpm($i); 
      } 
    } // end if row_index == 1

    $row_index++;

    return $build;
  }

}
