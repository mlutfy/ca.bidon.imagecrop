
cj(function($) {
  imagecrop_civicrm_enable();
});

/**
 * Enables Jcrop on an image field.
 */
function imagecrop_civicrm_enable() {
  var selector_image = CRM.imagecrop.selector_image;
  var selector_link_location = CRM.imagecrop.selector_link_location;

  if (cj(selector_image).size() <= 0) {
    return;
  }

  var w = CRM.imagecrop.image_width;
  var h = CRM.imagecrop.image_height;

  // The excess CSS classes are mostly to there to display the links similarly to the "delete" link of civicrm
  cj(selector_link_location).append('<div class="crm-imagecrop-imagesize action-item action-item-first">' + w + 'x' + h + '</div>');
  cj(selector_link_location).append('<div class="crm-imagecrop-croplink"><a class="action-item action-item-first" href="#" onclick="imagecrop_civicrm_crop_image(\'' + selector_image + '\', \'' + selector_link_location + '\'); return false;">' + ts('Crop image', {domain: 'ca.bidon.imagecrop'}) + '</a></div>');
}

function imagecrop_civicrm_crop_image(selector_image, selector_link_location) {
  // c.f. https://github.com/tapmodo/Jcrop/blob/master/demos/tutorial3.html
  // Create variables (in this scope) to hold the API and image size
  var jcrop_api, boundx, boundy;

  // Grab some information about the preview pane
  var $preview = cj('.crm-imagecrop-dialog-preview-pane');
  var $pcnt = cj('.crm-imagecrop-dialog-preview-container');
  var $pimg = cj('.crm-imagecrop-dialog-preview-container img');

  var xsize = $pcnt.width();
  var ysize = $pcnt.height();

  // Popup image cropping feature when we click on the contact image.
  // The HTML for the popup itself was added by imagecrop.php in the page footer area.
  // cj('.crm-contact_image img').click(function() {
    var this_img = cj(selector_image);

    cj('.crm-imagecrop-dialog').dialog({
      title: ts("Image editor", {domain: "ca.bidon.imagecrop"}),
      width: 1000,
      height: 550,
      modal: true,
      open: function() {
        // Run only once. If the user has closed & re-opened the popup, it stays in the same state.
        if (cj('.crm-imagecrop-dialog .jcrop-holder').size() > 0) {
          return;
        }

        // Enable jcrop on the image shown
        jQuery('.crm-imagecrop-dialog .crm-imagecrop-dialog-main img').Jcrop({
          boxWidth: 640,
          boxHeight: 480,
          aspectRatio: xsize / ysize,
          // https://github.com/tapmodo/Jcrop/issues/36
          // in Chrome/webkit, on contact->edit, selecting an area to crop causes a weird scroll glitch.
          keySupport: false,
          minSize: [ CRM.imagecrop.croparea_x, CRM.imagecrop.croparea_y ],
          // TODO: if we had saved the cropped area in the DB, we could setSelect that area automatically
          // setSelect: [ 0, 0, 640, 480 ],
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

        // Close button
        cj('.crm-imagecrop-dialog .crm-imagecrop-close a').click(function() {
          cj('.crm-imagecrop-dialog').dialog('close');
        });

        // Crop submit button
        cj('#crm-imagecrop-form').ajaxForm({
          url: CRM.url('civicrm/imagecrop'),
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              cj('.crm-imagecrop-dialog').dialog('close');

              // Refresh the image and make sure we call the imagecrop handler.
              var d = new Date();
              var img_src = this_img.attr('src');
              img_src = img_src.replace('civicrm/contact/imagefile', 'civicrm/imagecrop/imagefile');
              img_src = img_src.replace(/\&t=[0-9]+/, '');
              img_src += '&t=' + d.getTime();

              this_img.hide();
              this_img.attr('src', img_src);
              this_img.fadeIn();

              // Update the size of the image
              cj(selector_link_location + ' .crm-imagecrop-imagesize').html(response.width + 'x' + response.height);
            }
          }
        });
      },
      close: function() {
      }
    });

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
  // });
}

