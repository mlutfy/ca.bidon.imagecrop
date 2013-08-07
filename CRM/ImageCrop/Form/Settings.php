<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_ImageCrop_Form_Settings extends CRM_Core_Form {

  function preProcess() {
    // Needs to be here as form is build before default values are set
    $this->_values = CRM_Core_BAO_Setting::getItem(IMAGECROP_SETTINGS_GROUP);
  }

  function setDefaultValues() {
    $defaults = $this->_values;

    // Only set defaults for the crop area. Other settings do not need a default value.
    if (! CRM_Utils_Array::value('croparea_x', $defaults)) {
      $defaults['croparea_x'] = 200;
    }
    if (! CRM_Utils_Array::value('croparea_y', $defaults)) {
      $defaults['croparea_y'] = 200;
    }

    return $defaults;
  }

  function buildQuickForm() {
    CRM_Core_Resources::singleton()->addStyleFile('ca.bidon.imagecrop', 'imagecrop.settings.css');

    $this->applyFilter('__ALL__', 'trim');

    // TODO: ts domain='ca.bidon.imagecrop'
    $this->add('text', 'croparea_x', ts('Crop area X'));
    $this->add('text', 'croparea_y', ts('Crop area Y'));
    $this->add('text', 'min_width', ts('Minimum width'));
    $this->add('text', 'min_height', ts('Minimum height'));
    $this->add('text', 'max_width', ts('Maximum width'));
    $this->add('text', 'max_height', ts('Maximum height'));

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    parent::buildQuickForm();
  }

  function postProcess() {
    $values = $this->exportValues();
    $fields = array('croparea_x', 'croparea_y', 'min_width', 'min_height', 'max_width', 'max_height');

    foreach ($fields as $field) {
      $value = intval($values[$field]);
      $result = CRM_Core_BAO_Setting::setItem($value, IMAGECROP_SETTINGS_GROUP, $field);
    }

    // we will return to this form by default
    CRM_Core_Session::setStatus(ts('Settings saved.', array('domain' => 'ca.bidon.imagecrop')), '', 'success');

    parent::postProcess();
  }
}
