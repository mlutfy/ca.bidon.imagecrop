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

    if (! CRM_Utils_Array::value('resize', $defaults)) {
      $defaults['resize'] = FALSE;
    }

    return $defaults;
  }

  function buildQuickForm() {
    CRM_Core_Resources::singleton()->addStyleFile('ca.bidon.imagecrop', 'imagecrop.settings.css');
    CRM_Core_Resources::singleton()->addScriptFile('ca.bidon.imagecrop', 'imagecrop.settings.js');

    $this->applyFilter('__ALL__', 'trim');

    $this->add('text', 'aspect_ratio', ts('Aspect ratio', array('domain' => 'ca.bidon.imagecrop')));

    $this->add('text', 'croparea_x', ts('Crop area X', array('domain' => 'ca.bidon.imagecrop')));
    $this->add('text', 'croparea_y', ts('Crop area Y', array('domain' => 'ca.bidon.imagecrop')));
    $this->add('text', 'output_x', ts('Output X', array('domain' => 'ca.bidon.imagecrop')));
    $this->add('text', 'output_y', ts('Output Y', array('domain' => 'ca.bidon.imagecrop')));

    $this->addRule('croparea_x', ts('Value should be a positive number'), 'positiveInteger');
    $this->addRule('croparea_y', ts('Value should be a positive number'), 'positiveInteger');
    $this->addRule('output_x', ts('Value should be a positive number'), 'positiveInteger');
    $this->addRule('output_y', ts('Value should be a positive number'), 'positiveInteger');

    $this->add('text', 'min_width', ts('Minimum width', array('domain' => 'ca.bidon.imagecrop')));
    $this->add('text', 'min_height', ts('Minimum height', array('domain' => 'ca.bidon.imagecrop')));
    $this->add('text', 'max_width', ts('Maximum width', array('domain' => 'ca.bidon.imagecrop')));
    $this->add('text', 'max_height', ts('Maximum height', array('domain' => 'ca.bidon.imagecrop')));

    $this->addRule('min_width', ts('Value should be a positive number'), 'positiveInteger');
    $this->addRule('min_height', ts('Value should be a positive number'), 'positiveInteger');
    $this->addRule('max_width', ts('Value should be a positive number'), 'positiveInteger');
    $this->addRule('max_height', ts('Value should be a positive number'), 'positiveInteger');

    $this->addYesNo('resize', ts('Automatically resize?', array('domain' => 'ca.bidon.imagecrop')));

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    parent::buildQuickForm();
  }

  // Based on CRM/Core/Form.php validate()
  // Not 100% this is the recommended way.
  function validate() {
    parent::validate();

    $values = $this->exportValues();

    if ($values['aspect_ratio']) {
      // Ex: 16:9, 1.78 or 1.78:1
      // http://en.wikipedia.org/wiki/Aspect_ratio_%28image%29
      if (! preg_match('/^\d+\:\d+|\d+\.\d+(:\d+)?$/', $values['aspect_ratio'])) {
        $this->_errors['aspect_ratio'] = ts('The aspect ratio is not a valid format.', array('domain' => 'ca.bidon.reporterror'));
      }
    }

    return (0 == count($this->_errors));
  }

  function postProcess() {
    $values = $this->exportValues();
    $fields = array('aspect_ratio', 'croparea_x', 'croparea_y', 'resize', 'output_x', 'output_y', 'min_width', 'min_height', 'max_width', 'max_height');

    foreach ($fields as $field) {
      $result = CRM_Core_BAO_Setting::setItem($values[$field], IMAGECROP_SETTINGS_GROUP, $field);
    }

    // we will return to this form by default
    CRM_Core_Session::setStatus(ts('Settings saved.', array('domain' => 'ca.bidon.imagecrop')), '', 'success');

    parent::postProcess();
  }
}

