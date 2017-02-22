<?php

/**
 * @file
 * Lists available colors and color schemes for the AT Starterkit.
 * By default the color scheme relies almost entirely on color
 * shift, where the user changes the base color and all other
 * unspecified colors shift relative to the new base.
 */

$info = array(
  // Available colors and color labels used in theme.
  'fields' => array(
    'base' => t('Base'),
    'text' => t('Text color'),
    'link' => t('Link color'),
  ),
  // Pre-defined color schemes.
  'schemes' => array(
    'default' => array(
      'title' => t('Carbon Neutral'),
      'colors' => array(
        'base' => '#000000',
        'text' => '#555555', // $text-color
        'link' => '#0066cc', // $link-color
      ),
    ),
    'goodearth' => array(
      'title' => t('The Good Earth'),
      'colors' => array(
        'base' => '#4ba943',
        'text' => '#555555',
        'link' => '#8dd087',
      ),
    ),
    'tippingpoint' => array(
      'title' => t('Tipping Point'),
      'colors' => array(
        'base' => '#e65200',
        'text' => '#1b1918',
        'link' => '#ff792f',
      ),
    ),
  ),

  // CSS files (excluding @import) to rewrite with new color scheme.
  'css' => array(
    'styles/css/components/color.css',
    'styles/css/components/tabs.css',
  ),

  // Files to copy.
  'copy' => array(
    'logo.svg',
    'logo.png',
  ),

  // Gradient definitions.
  'gradients' => array(),
);
