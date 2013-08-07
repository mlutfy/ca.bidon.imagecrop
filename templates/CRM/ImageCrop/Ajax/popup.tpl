<div style="display: none;" class="crm-imagecrop-dialog crm-container">
  <div class="crm-imagecrop-dialog-preview-pane"><div class="crm-imagecrop-dialog-preview-container"><img src="{$imageCropImageURL}" /></div></div>
  <form method="post" id="crm-imagecrop-form">
    <input type="hidden" name="entity_id" value="{$imageCropEntityID}" />
    <input type="hidden" name="x1" id="crm-imagecrop-x1" />
    <input type="hidden" name="x2" id="crm-imagecrop-x2" />
    <input type="hidden" name="y1" id="crm-imagecrop-y1" />
    <input type="hidden" name="y2" id="crm-imagecrop-y2" />
    <input type="hidden" name="h" id="crm-imagecrop-h" />
    <input type="hidden" name="w" id="crm-imagecrop-w" />
    <div class="crm-imagecrop-buttons">
      <input type="submit" value="{ts domain='ca.bidon.imagecrop'}Crop{/ts}" />
    </div>
  </form>
  <div class="crm-imagecrop-dialog-main"><img src="{$imageCropImageURL}" /></div>
  <div class="crm-imagecrop-close"><a href="#">{ts}Close{/ts}</a></div>
</div>
