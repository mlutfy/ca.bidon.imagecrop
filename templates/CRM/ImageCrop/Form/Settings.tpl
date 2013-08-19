<div class="crm-block crm-form-block">

<h3>{ts domain='ca.bidon.imagecrop'}Image crop output{/ts}</h3>

<table class="form-layout-compressed crm-imagecrop-form-block">
  <tr>
    <td class="label">{ts domain='ca.bidon.imagecrop'}Minimum crop area{/ts}</td>
    <td>{$form.croparea_x.html} X {$form.croparea_y.html} {ts domain='ca.bidon.imagecrop'}pixels (width x height){/ts}
      <div class="description">{ts domain='ca.bidon.imagecrop'}Minimum size of the cropped area. It will also be used for the aspect ratio of the selection.{/ts}</div>
    </td>
  </tr>
  <tr>
    <td class="label">{$form.resize.label}</td>
    <td>{$form.resize.html}
      <div class="description">{ts domain='ca.bidon.imagecrop'}If the user selects an area greater than the minimum crop area above, the image will be automatically resized to this size.{/ts}</div>
    </td>
  </tr>
</table>

<h3>{ts domain='ca.bidon.imagecrop'}Upload guidelines{/ts}</h3>

<table class="form-layout-compressed crm-imagecrop-form-block">
  <tr>
    <td class="label">{ts domain='ca.bidon.imagecrop'}Output image size{/ts}</td>
    <td>{$form.croparea_x.html} X {$form.croparea_y.html} {ts domain='ca.bidon.imagecrop'}pixels (width x height){/ts}
      <div class="description">{ts domain='ca.bidon.imagecrop'}Size of the resulting cropped image. It will also be used for the aspect ratio of the selection.{/ts}</div>
    </td>
  </tr>
  <tr>
    <td class="label">{ts domain='ca.bidon.imagecrop'}Minimum upload size{/ts}</td>
    <td>{$form.min_width.html} X {$form.min_height.html} {ts domain='ca.bidon.imagecrop'}pixels (width x height){/ts}
      <div class="description">{ts}Enforces a minimum size file upload. If the image the user uploads is too small, it may be unusable (ex: for event badges).{/ts}</div>
    </td>
  </tr>
  <tr>
    <td class="label">{ts domain='ca.bidon.imagecrop'}Maximum upload size{/ts}</td>
    <td>{$form.max_width.html} X {$form.max_height.html} {ts domain='ca.bidon.imagecrop'}pixels (width x height){/ts}
      <div class="description">{ts domain='ca.bidon.imagecrop'}Enforces a maximum image file size that may be under your <a href="{crmURL p='civicrm/admin/setting/misc' q='reset=1'}">file upload size</a>. This is just for convenience, because original images are always kept, so they will use space on the server. However, keep in mind that most users don't know how to resize an image.{/ts}</div>
    </td>
  </tr>
</table>

<div>
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

</div>

