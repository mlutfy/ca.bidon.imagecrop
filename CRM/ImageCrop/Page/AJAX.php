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
      'success' => 0,
      'width' => 0,
      'height' => 0,
    );

    // TODO: support other types of entities
    $entity_type = CRM_Utils_Array::value('entity_type', $_POST);
    $entity_id = CRM_Utils_Array::value('entity_id', $_POST);

    if ($entity_type == 'Contact') {
      if (! CRM_Contact_BAO_Contact_Permission::allow($entity_id, CRM_Core_Permission::EDIT)) {
        $response['error'] = 'Permission denied.';
      }
    }
    else {
      $response['error'] = $entity_type . ': unsupported entity_type for imagecrop';
    }

    if (! empty($response['error'])) {
      echo json_encode($response);
      CRM_Utils_System::civiExit();
    }

    $image_URL = self::getContactField($entity_id, 'image_URL');

    if (preg_match('/imagefile\?photo=(.*)/', $image_URL, $matches)) {
      $image_URL = $matches[1];
    }

    // The output will default to the selected area in the original size
    // unless the site admin has configured to resize to a specific size.
    $targ_w = $_POST['w'];
    $targ_h = $_POST['h'];

    if (CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'resize', NULL, FALSE)) {
      $targ_w = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'output_x', NULL, $targ_w);
      $targ_h = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'output_y', NULL, $targ_h);
    }

    $response['width'] = $targ_w;
    $response['height'] = $targ_h;

    // TODO: add civicrm settings
    $jpeg_quality = 90;

    $config = CRM_Core_Config::singleton();

    // mostly from jCrop demo
    $img_r = imagecreatefromjpeg($config->customFileUploadDir . DIRECTORY_SEPARATOR . $image_URL);
    $dst_r = imagecreatetruecolor($targ_w, $targ_h);

    imagecopyresampled($dst_r, $img_r, 0, 0, $_POST['x1'], $_POST['y1'], $targ_w, $targ_h, $_POST['w'], $_POST['h']);

    $cropDirectoryName = imagecrop_civicrm_get_directory();
    $filename = $cropDirectoryName . DIRECTORY_SEPARATOR . basename($image_URL);
    imagejpeg($dst_r, $filename , $jpeg_quality);

    // Return the URL of the image.
    // We can only guess it's either the existing image_URL with imagecache appended in the path.
    $t = $image_URL;

    // If the URL does not have "imagecrop/" in it already, add it.
    if (! preg_match('/\/imagecrop\//', $t)) {
      $b = basename($image_URL);
      $t = preg_replace('/' . $b . '/', 'imagecrop/' . $b, $t);
    }

    // Add a random bit after the URL to force the browser to reload
    $t .= '?t=' . time();

    $response['filename'] = $t;
    $response['success'] = 1;

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
