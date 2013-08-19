
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
   * Get a numeric value for the aspect ratio.
   */
  function civicrm_imagecrop_parse_aspect_ratio() {
    var ratio = cj('input#aspect_ratio').val();
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
      var x1 = parseInt(found[3]);
      var x2 = parseInt(found[4]) / 100;

      x = x1 + x2; // ex: 1 + 0.78
      y = 1;

      if (found[6]) {
        y = parseInt(found[6]);
      }
    }

    // round up to 3 decimals
    return Math.round(x / y * 1000) / 1000;
  }

  function civicrm_imagecrop_calculate_x_from_y(selector_x, selector_y) {
    $(selector_x).change(function() {
      var ratio = civicrm_imagecrop_parse_aspect_ratio();

      if (ratio) {
        var x = parseInt($(this).val());
        var y = parseInt($(selector_y).val());

        // If the field was blanked out, do not do anything
        if (! x) {
          return;
        }

        // The value was already entered, and user might be adjusting the rounding.
        if (y) {
          return;
        }

        y = Math.round(x / ratio);
        $(selector_y).val(y);
      }
    });
  }

  function civicrm_imagecrop_calculate_y_from_x(selector_x, selector_y) {
    $(selector_y).change(function() {
      var ratio = civicrm_imagecrop_parse_aspect_ratio();

      if (ratio) {
        var y = parseInt($(this).val());
        var x = parseInt($(selector_x).val());

        // If the field was blanked out, do not do anything
        if (! y) {
          return;
        }

        // The value was already entered, and user might be adjusting the rounding.
        if (x) {
          return;
        }

        x = Math.round(y * ratio);
        $(selector_x).val(x);
      }
    });
  }

  function civicrm_imagecrop_calculate_aspect_ratio(selector_x, selector_y) {
    civicrm_imagecrop_calculate_x_from_y(selector_x, selector_y);
    civicrm_imagecrop_calculate_y_from_x(selector_x, selector_y);
  }

  civicrm_imagecrop_calculate_aspect_ratio('input#croparea_x', 'input#croparea_y');
  civicrm_imagecrop_calculate_aspect_ratio('input#output_x', 'input#output_y');
  civicrm_imagecrop_calculate_aspect_ratio('input#min_width', 'input#min_height');
  civicrm_imagecrop_calculate_aspect_ratio('input#max_width', 'input#max_height');
});

