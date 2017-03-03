<?php

namespace Drupal\kwlug_enclosure_rss\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\views\Plugin\views\style\Rss as Rss;

/**
 * Default style plugin to render an RSS feed.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "kwlug_rss",
 *   title = @Translation("KWLUG RSS Feed"),
 *   help = @Translation("Generates an RSS feed from a view with
 *   hardcoded channel elements"),
 *   theme = "views_view_kwlug_rss",
 *   display_types = {"feed"}
 * )
 */
class KWLUGRss extends Rss {

}
