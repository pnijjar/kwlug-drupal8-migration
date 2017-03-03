// Eliminate FOIT (Flash of Invisible Text) caused by web fonts loading slowly
// using font events with Font Face Observer.
(function ($) {

  "use strict";

  Drupal.behaviors.atFFOI = {
    attach: function (context) {

      $('html').addClass('fa-loading');

      var fontawesome = new FontFaceObserver('FontAwesome', {});

      // Because we are checking an icon font we need a unicode code point to check,
      // SEE https://github.com/bramstein/fontfaceobserver/issues/34
      fontawesome.check('\uf22d').then(function () {
        $('html').removeClass('fa-loading').addClass('fa-loaded');
      }, function() {
        $('html').removeClass('fa-loading').addClass('fa-unavailable');
      });

    }
  };
}(jQuery));
