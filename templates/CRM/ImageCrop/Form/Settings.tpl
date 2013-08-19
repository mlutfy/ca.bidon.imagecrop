<div class="crm-block crm-form-block">
  <h3>{ts domain='ca.bidon.imagecrop'}Image output{/ts}</h3>

  <table class="form-layout-compressed crm-imagecrop-form-block">
  <tr>
    <td class="label">{$form.aspect_ratio.label}</td>
    <td>{$form.aspect_ratio.html}
      <div class="description">{ts domain='ca.bidon.imagecrop'}Optional: enforce an aspect ratio when selecting a crop area or resizing. Example formats: 1:1, 3:2, 4:3, 16:9, 16:10, 1.6:1, 1.85:1. This will also automatically define the size of the minimum crop area setting or resize size if you select one.{/ts}</div>
    </td>
  </tr>
  <tr>
    <td class="label">{ts domain='ca.bidon.imagecrop'}Minimum crop area{/ts}</td>
    <td>{$form.croparea_x.html} x {$form.croparea_y.html} {ts domain='ca.bidon.imagecrop'}pixels (width x height){/ts}
      <div class="description">{ts domain='ca.bidon.imagecrop'}Minimum size of the cropped area. If set, it will be used for the aspect ratio of the selection.{/ts}</div>
    </td>
  </tr>
  <tr class="crm-imagecrop-resize-question-row">
    <td class="label">{$form.resize.label}</td>
    <td>{$form.resize.html}
      <div class="description">{ts domain='ca.bidon.imagecrop'}If the user selects an area greater than the minimum crop area above, the image will be automatically resized to this size.{/ts}</div>
    </td>
  </tr>
  <tr class="crm-imagecrop-resize-output-row">
    <td class="label">{ts domain='ca.bidon.imagecrop'}Output image size{/ts}</td>
    <td>{$form.output_x.html} x {$form.output_y.html} {ts domain='ca.bidon.imagecrop'}pixels (width x height){/ts}</td>
  </tr>
  </table>

  <h3>{ts domain='ca.bidon.imagecrop'}Upload guidelines{/ts}</h3>

  <table class="form-layout-compressed crm-imagecrop-form-block">
  <tr>
    <td class="label">{ts domain='ca.bidon.imagecrop'}Minimum upload size{/ts}</td>
    <td>{$form.min_width.html} x {$form.min_height.html} {ts domain='ca.bidon.imagecrop'}pixels (width x height){/ts}
      <div class="description">{ts}Enforces a minimum size file upload. If the image the user uploads is too small, it may be unusable (ex: for event badges).{/ts}</div>
    </td>
  </tr>
  <tr>
    <td class="label">{ts domain='ca.bidon.imagecrop'}Maximum upload size{/ts}</td>
    <td>{$form.max_width.html} x {$form.max_height.html} {ts domain='ca.bidon.imagecrop'}pixels (width x height){/ts}
      <div class="description">{ts domain='ca.bidon.imagecrop'}Enforces a maximum image file size that may be under your <a href="{crmURL p='civicrm/admin/setting/misc' q='reset=1'}">file upload size</a>. This is just for convenience, because original images are always kept, so they will use space on the server. However, keep in mind that most users don't know how to resize an image.{/ts}</div>
    </td>
  </tr>
  </table>

  <div>
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>

