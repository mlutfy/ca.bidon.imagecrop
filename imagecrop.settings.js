
cj(function($) {
  /**
   * Show/hide function for the optional resize options.
   *
   * @param origin String = { 'click' or 'initial' }
   */
  function civicrm_imagecrop_resize_show_hide(origin) {
    var v = parseInt($('.crm-imagecrop-resize-question-row input.form-radio:checked').val());

    if (v) {
      if (! $('.crm-imagecrop-resize-output-row').is(':visible')) {
        $('.crm-imagecrop-resize-output-row').fadeIn();
      }
    }
    else {
      if (origin == 'click') {
        $('.crm-imagecrop-resize-output-row').fadeOut();
      }
      else {
        $('.crm-imagecrop-resize-output-row').hide();
      }
    }
  }

  civicrm_imagecrop_resize_show_hide('initial');

  $('.crm-imagecrop-resize-question-row input.form-radio').change(function() {
    civicrm_imagecrop_resize_show_hide('click');
  });

  /**
   * Apply the aspect ratio to other relevant seetings.
   */
  function civicrm_imagecrop_parse_aspect_ratio() {
    var ratio = $('input#aspect_ratio').val();
    var x = 0;
    var y = 0;

    // Ex: 16:9, 1.78 or 1.78:1
    var found = ratio.match(/^(\d+)\:(\d+)|(\d+)\.(\d+)+(\:(\d+))?$/);

    if (found[1] && found[2]) {
      // found 16:9
      x = parseInt(found[1]);
      y = parseInt(found[2]);

    }
    else if (found[3] && found[4]) {
      // found 1.78 or 1.78:1
      var x1 = parseInt(found[1]);
      var x2 = parseInt(found[2]) / 100;

      x = x1 * x2;
      y = 1;

      if (found[3]) {
        y = parseInt(found[3]);
      }
    }

    // round up to 3 decimals
    return Math.round(x / y * 1000) / 1000;
  }

  $('input#croparea_x').change(function() {
    var ratio = civicrm_imagecrop_parse_aspect_ratio();

    if (ratio) {
      console.log('yay!');
      console.log(ratio);
    }
  });
});

