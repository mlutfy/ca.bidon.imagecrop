<?php

require_once 'imagecrop.civix.php';

define('IMAGECROP_SETTINGS_GROUP', 'ImageCrop Extension');

/**
 * Implementation of hook_civicrm_config
 */
function imagecrop_civicrm_config(&$config) {
  _imagecrop_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function imagecrop_civicrm_xmlMenu(&$files) {
  _imagecrop_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function imagecrop_civicrm_install() {
  return _imagecrop_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function imagecrop_civicrm_uninstall() {
  return _imagecrop_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function imagecrop_civicrm_enable() {
  return _imagecrop_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function imagecrop_civicrm_disable() {
  return _imagecrop_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function imagecrop_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _imagecrop_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function imagecrop_civicrm_managed(&$entities) {
  return _imagecrop_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * Add jCrop on selected forms.
 */
function imagecrop_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Contact_Form_Contact' || $formName == 'CRM_Profile_Form_Edit') {
    if (! $form->elementExists('image_URL')) {
      return;
    }

    $smarty = CRM_Core_Smarty::singleton();
    $imageURL = CRM_Utils_Array::value('imageURL', $smarty->_tpl_vars);

    if (! $imageURL) {
      return;
    }

    $entity_id = ($formName == 'CRM_Profile_Form_Edit' ? $smarty->_tpl_vars['id'] : $smarty->_tpl_vars['entityID']);
    imagecrop_civicrm_jcrop_enable('Contact', $entity_id, $imageURL, '.crm-contact_image a img', '.crm-contact_image');

    // Assign the cropped image as the normal profile image
    $cropped_imageURL = imagecrop_civicrm_get_cropped_image_url($imageURL);
    list($imageWidth, $imageHeight) = getimagesize($cropped_imageURL);

    $smarty->assign('imageURL', $cropped_imageURL);
    $smarty->assign('imageWidth', $imageWidth);
    $smarty->assign('imageHeight', $imageHeight);

    if (CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'change_thumbnail_size', NULL, FALSE)) {
      $croparea_x = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'croparea_x', NULL, 200);
      $croparea_y = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'croparea_y', NULL, 200);
      $smarty->assign('imageThumbWidth', $croparea_x);
      $smarty->assign('imageThumbHeight', $croparea_y);
    }
  }
}

/**
 * Implements hook_civicrm_pageRun().
 *
 * Add jCrop on selected forms.
 */
function imagecrop_civicrm_pageRun(&$page) {
  $class_name = get_class($page);

  if ($class_name == 'CRM_Contact_Page_View_Summary') {
    // TODO: add support for custom fields
    $smarty = CRM_Core_Smarty::singleton();

    $imageURL = CRM_Utils_Array::value('imageURL', $smarty->_tpl_vars);

    if (! $imageURL) {
      return;
    }

    $entity_id = $smarty->_tpl_vars['id'];
    imagecrop_civicrm_jcrop_enable('Contact', $entity_id, $imageURL, '.crm-contact_image a img', '.crm-contact_image');

    // Assign the cropped image as the normal profile image
    $cropped_imageURL = imagecrop_civicrm_get_cropped_image_url($imageURL);
    list($imageWidth, $imageHeight) = getimagesize($cropped_imageURL);

    $smarty->assign('imageURL', $cropped_imageURL);
    $smarty->assign('imageWidth', $imageWidth);
    $smarty->assign('imageHeight', $imageHeight);

    if (CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'change_thumbnail_size', NULL, FALSE)) {
      $croparea_x = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'croparea_x', NULL, 200);
      $croparea_y = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'croparea_y', NULL, 200);
      $smarty->assign('imageThumbWidth', $croparea_x);
      $smarty->assign('imageThumbHeight', $croparea_y);
    }
  }
  elseif ($class_name == 'CRM_Profile_Page_View') {
    // XXX hackish override of profile output. We don't have much to work with.
    $smarty = CRM_Core_Smarty::singleton();
    $groups = $smarty->_tpl_vars['profileGroups'];

    foreach ($groups as $key => $val) {
      if (preg_match('/contactImagePopUp.*img src="([^"]+)"/', $val['content'], $matches)) {
        $imageURL = $matches[1];

        $entity_id = $smarty->_tpl_vars['cid'];
        imagecrop_civicrm_jcrop_enable('Contact', $entity_id, $imageURL, '#row-image_URL .content a img', '#row-image_URL .content');

        // Assign the cropped image as the normal profile image
        $cropped_imageURL = imagecrop_civicrm_get_cropped_image_url($imageURL);
        list($imageWidth, $imageHeight) = getimagesize($cropped_imageURL);

        $groups[$key]['content'] = preg_replace("|$imageURL|", $cropped_imageURL, $val['content']);
        $groups[$key]['content'] = preg_replace("|contactImagePopUp\([^\)]+\)|", "contactImagePopUp(\"$cropped_imageURL\", $imageWidth, $imageHeight)", $groups[$key]['content']);

        if (CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'change_thumbnail_size', NULL, FALSE)) {
          $croparea_x = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'croparea_x', NULL, 200);
          $croparea_y = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'croparea_y', NULL, 200);
          // profile code is sloppy and has invalid html, i.e. has spaces instead of quotes..
          $groups[$key]['content'] = preg_replace('|width=["\'\s]\d+["\'\s]|', 'width="' . $croparea_x . '" ', $groups[$key]['content']);
          $groups[$key]['content'] = preg_replace('|height=["\'\s]\d+["\'\s]|', 'height="' . $croparea_y . '" ', $groups[$key]['content']);
        }

        $smarty->assign('profileGroups', $groups);
      }
    }
  }
}

/**
 * Generic function to include/add jCrop.
 *
 * @param selector_image : jquery selector to find the image to crop.
 * @param selector_link_location : jquery selector to place/append the link "crop image" that triggers the popup.
 */
function imagecrop_civicrm_jcrop_enable($entity_type, $entity_id, $imageURL, $selector_image, $selector_link_location) {
  // Check if the user has permission to edit the entity
  if ($entity_type == 'Contact') {
    if (! CRM_Contact_BAO_Contact_Permission::allow($entity_id, CRM_Core_Permission::EDIT)) {
      return;
    }
  }
  else {
    throw new Exception($entity_type . ': unsupported entity_type for imagecrop');
  }

  CRM_Core_Resources::singleton()
    ->addScriptFile('ca.bidon.imagecrop', 'imagecrop.js')
    ->addStyleFile('ca.bidon.imagecrop', 'imagecrop.css');

  CRM_Core_Resources::singleton()
    ->addScriptFile('ca.bidon.imagecrop', 'jcrop/js/jquery.Jcrop.min.js')
    ->addStyleFile('ca.bidon.imagecrop', 'jcrop/css/jquery.Jcrop.min.css');

  // Image to use for cropping
  $smarty = CRM_Core_Smarty::singleton();
  $smarty->assign('imageCropImageURL', $imageURL);

  // Eventually, we will want to support different entities
  $smarty->assign('imageCropEntityType', $entity_type);
  $smarty->assign('imageCropEntityID', $entity_id);

  // Add the <div> with the required html for our popup
  CRM_Core_Region::instance('page-body')->add(array(
    'template' => 'CRM/ImageCrop/Ajax/popup.tpl',
  ));

  $croparea_x = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'croparea_x', NULL, 200);
  $croparea_y = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'croparea_y', NULL, 200);
  $resize = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'resize', NULL, FALSE);

  // If the image has not been cropped yet, it will show the original size
  $image_file = imagecrop_civicrm_get_cropped_image_path($imageURL);
  list($imageWidth, $imageHeight) = getimagesize($image_file);

  // Jcrop will set the aspect ratio of the crop area based on the size of the crop-area preview, specified here
  CRM_Core_Resources::singleton()->addStyle('.crm-imagecrop-dialog-preview-pane .crm-imagecrop-dialog-preview-container { width: ' . $croparea_x . 'px; height: ' . $croparea_y . 'px; overflow: hidden; }');
  CRM_Core_Resources::singleton()->addStyle('.crm-imagecrop-dialog .crm-imagecrop-buttons { top: ' . $croparea_y . 'px; }');

  // Expose settings to JS
  CRM_Core_Resources::singleton()->addSetting(array(
    'imagecrop' => array(
      'croparea_x' => $croparea_x,
      'croparea_y' => $croparea_y,
      'resize' => $resize,
      'selector_image' => $selector_image,
      'selector_link_location' => $selector_link_location,
      'image_width' => $imageWidth,
      'image_height' => $imageHeight,
    ),
  ));
}

/**
 * Returns the path to where the cropped images are stored.
 * (FIXME: no point in having one function per setting, fix later when we have more settings.)
 */
function imagecrop_civicrm_get_directory() {
  $config = CRM_Core_Config::singleton();
  $cropDirectoryName = $config->customFileUploadDir . DIRECTORY_SEPARATOR . 'imagecrop';

  // Only creates the directory if it does not exist
  CRM_Utils_File::createDir($cropDirectoryName);

  return $cropDirectoryName;
}

/**
 * Returns the URL for a cropped image, if it exists. If not, returns the same URL.
 */
function imagecrop_civicrm_get_cropped_image_url($imageURL) {
  // We don't have a civi config for the "custom" directory URL, since image_URL stores the
  // full URL in the civicrm_contact table. So we preg to insert our "imagecache" suffix in there.
  $cropDirectoryName = imagecrop_civicrm_get_directory();
  $filename = basename($imageURL);
  $croppedfilename = $cropDirectoryName . DIRECTORY_SEPARATOR . basename($imageURL);
  if (file_exists($croppedfilename)) {
    $cropped_imageURL = preg_replace("/$filename/", "imagecrop/$filename", $imageURL);
    return $cropped_imageURL;
  }

  return $imageURL;
}

/**
 * Returns the file path for a cropped image, if it exists. If not, returns the path to the original image.
 */
function imagecrop_civicrm_get_cropped_image_path($imageURL) {
  // We don't have a civi config for the "custom" directory URL, since image_URL stores the
  // full URL in the civicrm_contact table. So we preg to insert our "imagecache" suffix in there.
  $cropDirectoryName = imagecrop_civicrm_get_directory();
  $filename = basename($imageURL);
  $croppedfilename = $cropDirectoryName . DIRECTORY_SEPARATOR . basename($imageURL);
  if (file_exists($croppedfilename)) {
    return $croppedfilename;
  }

  // return the path to the original image
  $config = CRM_Core_Config::singleton();
  return $config->customFileUploadDir . DIRECTORY_SEPARATOR . $filename;
}

/**
 * Implementation of hook_civicrm_validateForm().
 *
 * Validate the size of the image files.
 */
function imagecrop_civicrm_validateForm($formName, &$fields, &$files, &$form, &$errors) {
  // Taken from civicrm/CRM/Contact/BAO/Contact.php
  $mimeType = array(
    'image/jpeg',
    'image/jpg',
    'image/png',
    'image/bmp',
    'image/p-jpeg',
    'image/gif',
    'image/x-png',
  );

  $minWidth = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'min_width');
  $minHeight = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'min_height');
  $maxWidth = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'max_width');
  $maxHeight = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'max_height');

  if ($formName == 'CRM_Profile_Form_Edit' || $formName == 'CRM_Contact_Form_Contact') {
    foreach ($files as $key => $val) {
      if (in_array($val['type'], $mimeType)) {
        list($width, $height, $type, $attr) = getimagesize($val['tmp_name']);

        // It may be annoying to check only width, then complain about height
        // Hopefully, most people upload images with a reasonable aspect ratio?
        // I don't feel like forcing admins to enter both a min height/width
        if ($minWidth && $width < $minWidth) {
          $errors[$key] = ts('The image size is too small (%1 px). Please upload an image at least %2 pixels large.', array(1 => $width, 2 => $minWidth, 'domain' => 'ca.bidon.imagecrop'));
        }
        elseif ($minHeight && $height < $minHeight) {
          $errors[$key] = ts('The image size is too small (%1 px). Please upload an image at least %2 pixels high.', array(1 => $height, 2 => $minHeight, 'domain' => 'ca.bidon.imagecrop'));
        }

        if ($maxWidth && $width > $maxWidth) {
          $errors[$key] = ts('The image size is too large (%1 px). Please upload an image less than %2 pixels large.', array(1 => $width, 2 => $maxWidth, 'domain' => 'ca.bidon.imagecrop'));
        }
        elseif ($maxHeight && $height > $maxHeight) {
          $errors[$key] = ts('The image size is too large (%1 px). Please upload an image less than %2 pixels high.', array(1 => $height, 2 => $maxHeight, 'domain' => 'ca.bidon.imagecrop'));
        }
      }
    }
  }
}

