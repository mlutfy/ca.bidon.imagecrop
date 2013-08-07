
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
  var submit_text = ts('Crop', {context: 'ca.bidon.imagecrop'});

  cj('.crm-contact_image').append('<div style="display: none;" class="crm-imagecrop-dialog crm-container"></div>');
  cj('.crm-contact_image .crm-imagecrop-dialog').append('<div class="crm-imagecrop-dialog-preview-pane"><div class="crm-imagecrop-dialog-preview-container"><img src="' + imgsrc + '" /></div></div>');
  cj('.crm-contact_image .crm-imagecrop-dialog').append('<div class="crm-imagecrop-buttons"><input type="submit" name="crm-imagecrop-submit" value="' + submit_text + '" /></div>');
  cj('.crm-contact_image .crm-imagecrop-dialog').append('<div class="crm-imagecrop-close"><a href="#">' + ts('Close') + '</a></div>');
  cj('.crm-contact_image .crm-imagecrop-dialog').append('<div class="crm-imagecrop-dialog-main"><img src="' + imgsrc + '" /></div>');

  cj('.crm-contact_image img').click(function() {
    // c.f. https://github.com/tapmodo/Jcrop/blob/master/demos/tutorial3.html
    // Create variables (in this scope) to hold the API and image size
    var jcrop_api, boundx, boundy,
  
    // Grab some information about the preview pane
    $preview = cj('.crm-imagecrop-dialog-preview-pane'),
    $pcnt = cj('.crm-imagecrop-dialog-preview-container'),
    $pimg = cj('.crm-imagecrop-dialog-preview-container img'),
  
    xsize = $pcnt.width(),
    ysize = $pcnt.height();

    cj('.crm-imagecrop-dialog').dialog({
      title: ts("Test title", {domain: "ca.bidon.imagecrop"}),
      width: 1000,
      height: 550,
      modal: true
    });
  
    cj('.crm-imagecrop-dialog .crm-imagecrop-close a').click(function() {
      cj('.crm-imagecrop-dialog').dialog('close');
    });
  
    function crmImageCropUpdatePreview(c) {
      if (parseInt(c.w) > 0) {
        var rx = xsize / c.w;
        var ry = ysize / c.h;
  
        $pimg.css({
          width: Math.round(rx * boundx) + 'px',
          height: Math.round(ry * boundy) + 'px',
          marginLeft: '-' + Math.round(rx * c.x) + 'px',
          marginTop: '-' + Math.round(ry * c.y) + 'px'
        });
      }
    }

    // Enable jcrop on the image shown
    jQuery('.crm-imagecrop-dialog .crm-imagecrop-dialog-main img').Jcrop({
      boxWidth: 640,
      boxHeight: 480,
      aspectRatio: xsize / ysize,
      onChange: crmImageCropUpdatePreview,
      onSelect: crmImageCropUpdatePreview
    }, function(){
      // Use the API to get the real image size
      var bounds = this.getBounds();
      boundx = bounds[0];
      boundy = bounds[1];
      // Store the API in the jcrop_api variable
      jcrop_api = this;

      // Move the preview into the jcrop container for css positioning
      $preview.appendTo(jcrop_api.ui.holder);
    });
  });
}

