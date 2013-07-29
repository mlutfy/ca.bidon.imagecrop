
cj(function($) {
  imagecrop_civicrm_enable('.crm-contact_image');
});

/**
 * Enables Jcrop on an image field.
 */
function imagecrop_civicrm_enable(selector) {
  // Take the 'img' out of its parent 'a', to disable the default popup function of civicrm
  // FIXME: this should still be in a 'a', for sanity.
  cj('.crm-contact_image a img').appendTo('.crm-contact_image');

  // Create a hidden dialog that will popup when we click on the contact image
  var imgsrc = cj('.crm-contact_image img').attr('src');
  cj('.crm-contact_image').append('<div style="display: none;" class="crm-imagecrop-dialog crm-container"></div>');
  cj('.crm-contact_image .crm-imagecrop-dialog').append('<div class="crm-imagecrop-dialog-preview"><img style="width: 150px; height: 150px;" src="' + imgsrc + '" /><input type="submit" name="Submit" /></div>');
  cj('.crm-contact_image .crm-imagecrop-dialog').append('<div class="crm-imagecrop-dialog-main"><img src="' + imgsrc + '" /></div>');

  cj('.crm-contact_image img').click(function() {
    cj('.crm-imagecrop-dialog').dialog({
      title: ts("Test title", {domain: "ca.bidon.imagecrop"}),
      width: 1000,
      height: 500,
      modal: true
    });

    // Enable jcrop on the image shown
    jQuery('.crm-imagecrop-dialog .crm-imagecrop-dialog-main img').Jcrop({
      boxWidth: 640,
      boxHeight: 480
    });
  });
}

