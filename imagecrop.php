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
    // CRM_Core_Resources::singleton()->addScript('jQuery(function($) { $(\'input[name="image_URL"');
    // CRM_Core_Resources::singleton()->addScript('alert("hello");');
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

  $croparea_x = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'croparea_x');
  $croparea_y = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP, 'croparea_y');

  // Jcrop will set the aspect ratio of the crop area based on the size of the thumbnail preview, specified here
  CRM_Core_Resources::singleton()->addStyle('.crm-imagecrop-dialog-preview-pane .crm-imagecrop-dialog-preview-container { width: ' . $croparea_x . 'px; height: ' . $croparea_y . 'px; overflow: hidden; }');
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

