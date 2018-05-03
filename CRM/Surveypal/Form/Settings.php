<?php
/*-------------------------------------------------------+
| SurveyPal Tokens                                       |
| Copyright (C) 2018 SYSTOPIA                            |
| Author: B. Endres (endres@systopia.de)                 |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/

use CRM_Surveypal_ExtensionUtil as E;

define('SURVEY_SLOTS', 10);
/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Surveypal_Form_Settings extends CRM_Core_Form {


  public function buildQuickForm() {

    $this->assign('slots', range(1, SURVEY_SLOTS));

    for ($i=1; $i <= SURVEY_SLOTS; $i++) {
      // add ID field
      $this->add(
        'text',
        "survey_name_{$i}",
        'Survey Name',
        FALSE
      );

      $this->add(
        'text',
        "survey_id_{$i}",
        'Survey ID',
        FALSE
      );

      // add ID field
      $this->add(
        'text',
        "survey_token_{$i}",
        'Survey Key',
        array('class' => 'huge'),
        FALSE
      );
    }

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => E::ts('Save'),
        'isDefault' => TRUE,
      ),
    ));

    parent::buildQuickForm();
  }


  /**
   * set the default (=current) values in the form
   */
  public function setDefaultValues() {
    $surveys = CRM_Surveypal_Config::getSurveys();
    $default_values = array();
    for ($i=1; $i <= count($surveys); $i++) {
      $survey = $surveys[$i-1];
      $default_values["survey_name_{$i}"]  = $survey['name'];
      $default_values["survey_id_{$i}"]    = $survey['id'];
      $default_values["survey_token_{$i}"] = $survey['token'];
    }
    return $default_values;
  }


  public function postProcess() {
    $values = $this->exportValues();

    // extract surveys
    $surveys = array();

    for ($i=1; $i <= SURVEY_SLOTS; $i++) {
      if (!empty($values["survey_id_{$i}"]) && !empty($values["survey_token_{$i}"])) {
        $surveys[] = array(
          'id'    => $values["survey_id_{$i}"],
          'token' => $values["survey_token_{$i}"],
          'name'  => $values["survey_name_{$i}"]
        );
      }
    }

    // store
    CRM_Surveypal_Config::storeSurveys($surveys);

    // tell the user
    CRM_Core_Session::setStatus(E::ts('%1 surveys stored.', array(1 => count($surveys))));
    parent::postProcess();
  }
}
