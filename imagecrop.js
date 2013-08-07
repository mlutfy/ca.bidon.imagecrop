
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

  // Popup image cropping feature when we click on the contact image.
  // The HTML for the popup itself was added by imagecrop.php in the page footer area.
  cj('.crm-contact_image img').click(function() {
    cj('.crm-imagecrop-dialog').dialog({
      title: ts("Test title", {domain: "ca.bidon.imagecrop"}),
      width: 1000,
      height: 550,
      modal: true,
      open: function() {
        cj('.crm-imagecrop-dialog .crm-imagecrop-close a').click(function() {
          cj('.crm-imagecrop-dialog').dialog('close');
        });
        cj('#crm-imagecrop-form').ajaxForm({
          url: CRM.url('civicrm/imagecrop'),
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              cj('.crm-imagecrop-dialog').dialog('close');

              // http://stackoverflow.com/questions/3715047/how-to-reload-a-page-using-javascript
              // If we needed to pull the document from
              // the web-server again (such as where the document contents
              // change dynamically) we would pass the argument as 'true'.
              window.location.reload(true);
            }
          }
        });
      },
      close: function() {
      }
    });
  
    // c.f. https://github.com/tapmodo/Jcrop/blob/master/demos/tutorial3.html
    // Create variables (in this scope) to hold the API and image size
    var jcrop_api, boundx, boundy,

    // Grab some information about the preview pane
    $preview = cj('.crm-imagecrop-dialog-preview-pane'),
    $pcnt = cj('.crm-imagecrop-dialog-preview-container'),
    $pimg = cj('.crm-imagecrop-dialog-preview-container img'),
  
    xsize = $pcnt.width(),
    ysize = $pcnt.height();
  
    function crmImageCropUpdatePreview(c) {
      // Save coords for form submit
      cj('form#crm-imagecrop-form input#crm-imagecrop-x1').val(c.x);
      cj('form#crm-imagecrop-form input#crm-imagecrop-x2').val(c.x2);
      cj('form#crm-imagecrop-form input#crm-imagecrop-y1').val(c.y);
      cj('form#crm-imagecrop-form input#crm-imagecrop-y2').val(c.y2);
      cj('form#crm-imagecrop-form input#crm-imagecrop-w').val(c.w);
      cj('form#crm-imagecrop-form input#crm-imagecrop-h').val(c.h);

      // Update preview
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

