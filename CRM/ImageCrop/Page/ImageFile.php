<?php

/**
 * Re-implements 'run' from CRM_Contact_Page_ImageFile
 * so that we can load cropped images.
 */

class CRM_ImageCrop_Page_ImageFile extends CRM_Contact_Page_ImageFile {
  /**
   * @var int Time to live (seconds).
   *
   * 12 hours: 12 * 60 * 60 = 43200
   */
  private $ttl = 43200;

  public function run() {
    $photo = CRM_Utils_Request::retrieve('photo', 'String');

    if (!preg_match('/^[^\/]+\.(jpg|jpeg|png|gif)$/i', $photo)) {
      CRM_Core_Error::fatal('Malformed photo name');
    }

    // FIXME Optimize performance of image_url query
    $sql = "SELECT id FROM civicrm_contact WHERE image_url like %1;";
    $params = array(
      1 => array("%" . $photo, 'String'),
    );

    $dao = CRM_Core_DAO::executeQuery($sql, $params);
    while ($dao->fetch()) {
      $cid = $dao->id;
    }

    if ($cid) {
      $photo = imagecrop_civicrm_get_cropped_image_path($photo);

      $config = CRM_Core_Config::singleton();
      $this->download(
        $photo,
        'image/' . pathinfo($photo, PATHINFO_EXTENSION),
        $this->ttl
      );
      CRM_Utils_System::civiExit();
    }
    else {
      CRM_Core_Error::fatal('Photo does not exist');
    }
  }
}
