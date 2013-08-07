<?php

class CRM_ImageCrop_Page_AJAX {
  /**
   * Crop an image. Uses the entity_id to fetch the original image.
   * It will save the cropped image with the same name, but in a 'imagecache'
   * subdirectory of customFileUploadDir.
   *
   * NB: we use POST parameters to reduce the risks of CSRF.
   */
  static function imageCrop() {
    $response = array(
      'success' => 1,
    );

    // TODO: support other types of entities
    $entity_id = CRM_Utils_Array::value('entity_id', $_POST);
    $image_URL = self::getContactField($entity_id, 'image_URL');

    // mostly from jCrop demo
    $targ_w = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'croparea_x');
    $targ_h = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'croparea_y');

    // TODO: add civicrm settings
    $jpeg_quality = 90;

    $img_r = imagecreatefromjpeg($image_URL);
    $dst_r = imagecreatetruecolor($targ_w, $targ_h);

    imagecopyresampled($dst_r, $img_r, 0, 0, $_POST['x1'], $_POST['y1'], $targ_w, $targ_h, $_POST['w'], $_POST['h']);

    $cropDirectoryName = imagecrop_civicrm_get_directory();
    $filename = $cropDirectoryName . DIRECTORY_SEPARATOR . basename($image_URL);
    imagejpeg($dst_r, $filename , $jpeg_quality);

    echo json_encode($response);
    CRM_Utils_System::civiExit();
  }

  /**
   * Returns the contact_type of a Contact.
   *
   * @param cid Integer Contact ID.
   */
  static function getContactField($cid, $field) {
    $result = civicrm_api('Contact', 'getsingle', array('version' => 3, 'id' => $cid, 'return.' . $field => 1));

    if (! empty($result['is_error'])) {
      CRM_Core_Error::fatal(ts('API Error: %1', array(1 => $result['error_message'])));
    }

    return $result[$field];
  }
}
