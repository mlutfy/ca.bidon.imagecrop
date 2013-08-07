<?php

require_once 'imagecrop.civix.php';

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
      $smarty->assign('imageCropImageURL', $imageURL);
      imagecrop_civicrm_jcrop_enable();
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
}

