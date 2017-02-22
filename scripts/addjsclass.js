// Add a class to indicate that a page has javascript enabled. 
(function ($) {

  "use strict";

  Drupal.behaviors.kwlugNoJS = {
    attach: function (context) {

      $('div.rm-block__inner').addClass('has-js');
    }
  };
}(jQuery));
