
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
});

