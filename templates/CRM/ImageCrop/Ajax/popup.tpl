<div style="display: none;" class="crm-imagecrop-dialog crm-container">
  <div class="crm-imagecrop-dialog-preview-pane"><div class="crm-imagecrop-dialog-preview-container"><img src="{$imageCropImageURL}" /></div></div>
  <div class="crm-imagecrop-dialog-main"><img src="{$imageCropImageURL}" /></div>
  <form id="crm-imagecrop-form">
    <input type="hidden" name="x1"/>
    <input type="hidden" name="x2"/>
    <input type="hidden" name="y1"/>
    <input type="hidden" name="y2"/>
    <input type="hidden" name="h"/>
    <input type="hidden" name="w"/>
    <div class="crm-imagecrop-buttons">
      <input type="submit" name="crm-imagecrop-crop" value="{ts domain='ca.bidon.imagecrop'}Crop{/ts}" />
    </div>
  </form>
  <div class="crm-imagecrop-close"><a href="#">{ts}Close{/ts}</a></div>
</div>
