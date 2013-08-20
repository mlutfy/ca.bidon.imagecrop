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
  if ($formName == 'CRM_Contact_Form_Contact') {
    // imagecrop_civicrm_jcrop_enable();
  }
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * Add jCrop on selected forms.
 */
function imagecrop_civicrm_pageRun(&$page) {
  $class_name = get_class($page);

  if ($class_name == 'CRM_Contact_Page_View_Summary') {
    // TODO: enable for a specific selector. Currently enables on '.crm-contact_image a img'.
    $smarty = CRM_Core_Smarty::singleton();
    $imageURL = $smarty->_tpl_vars['imageURL'];

    if ($imageURL) {
      // Assign the cropped image as the normal profile image
      // We don't have a civi config for the "custom" directory URL, since image_URL stores the
      // full URL in the civicrm_contact table. So we preg to insert our "imagecache" suffix in there.
      $cropDirectoryName = imagecrop_civicrm_get_directory();
      $filename = basename($imageURL);
      $croppedfilename = $cropDirectoryName . DIRECTORY_SEPARATOR . basename($imageURL);
      if (file_exists($croppedfilename)) {
        $cropped_imageURL = preg_replace("/$filename/", "imagecrop/$filename", $imageURL);
        $smarty->assign('imageURL', $cropped_imageURL);
      }

      // Image to use for cropping
      $smarty->assign('imageCropImageURL', $imageURL);
      imagecrop_civicrm_jcrop_enable();

      // Eventually, we will want to support different entities
      $smarty->assign('imageCropEntityID', $smarty->_tpl_vars['id']);
      $smarty->assign('imageCropEntityType', 'Contact');
    }
  }
}

/**
 * Generic function to include/add jCrop.
 *
 */
function imagecrop_civicrm_jcrop_enable() {
  CRM_Core_Resources::singleton()
    ->addScriptFile('ca.bidon.imagecrop', 'imagecrop.js')
    ->addStyleFile('ca.bidon.imagecrop', 'imagecrop.css');

  CRM_Core_Resources::singleton()
    ->addScriptFile('ca.bidon.imagecrop', 'jcrop/js/jquery.Jcrop.min.js')
    ->addStyleFile('ca.bidon.imagecrop', 'jcrop/css/jquery.Jcrop.min.css');

  CRM_Core_Region::instance('page-body')->add(array(
    'template' => 'CRM/ImageCrop/Ajax/popup.tpl',
  ));

  $croparea_x = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'croparea_x', NULL, 200);
  $croparea_y = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'croparea_y', NULL, 200);
  $resize = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'resize', NULL, FALSE);

  // Jcrop will set the aspect ratio of the crop area based on the size of the crop-area preview, specified here
  CRM_Core_Resources::singleton()->addStyle('.crm-imagecrop-dialog-preview-pane .crm-imagecrop-dialog-preview-container { width: ' . $croparea_x . 'px; height: ' . $croparea_y . 'px; overflow: hidden; }');
  CRM_Core_Resources::singleton()->addStyle('.crm-imagecrop-dialog .crm-imagecrop-buttons { top: ' . $croparea_y . 'px; width: ' . $croparea_x . 'px; }');

  // Expose settings to JS
  CRM_Core_Resources::singleton()->addSetting(array(
    'imagecrop' => array(
      'croparea_x' => $croparea_x,
      'croparea_y' => $croparea_y,
      'resize' => $resize,
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

