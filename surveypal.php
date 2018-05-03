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

require_once 'surveypal.civix.php';
use CRM_Surveypal_ExtensionUtil as E;

/**
 * Hook implementation: New Tokens
 */
function surveypal_civicrm_tokens( &$tokens ) {
  $surveys = CRM_Surveypal_Config::getSurveys();
  if (!empty($surveys)) {
    $survey_tokens = array();
    foreach ($surveys as $survey) {
      $survey_tokens["surveypal.survey_{$survey['id']}"] = 'Survey: ' . $survey['name'];
    }
    $tokens['surveypal'] = $survey_tokens;
  }
}

/**
 * Hook implementation: New Tokens
 */
function surveypal_civicrm_tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
  // error_log("CALL: " . json_encode($cids) . ' / ' . json_encode($tokens));
  if (empty($tokens['surveypal'])) return;

  // basic data
  $url_template = 'https://my.surveypal.com/app/form/ext?_d=0&_sid=%s&_k=%s&externalid=%s&email=%s&meta=%s';
  $fields = array(
    'cid'        => 'SRdonorcontactid',
    'last_name'  => 'lastname',
    'first_name' => 'firstname');

  // create a list survey_id => survey
  $survey_index = array();
  $surveys = CRM_Surveypal_Config::getSurveys();
  if (!empty($surveys)) {
    foreach ($surveys as $survey) {
      $survey_index["survey_{$survey['id']}"] = $survey;
    }
  }

  // load data
  $cid_list = implode(',', $cids);
  $query = CRM_Core_DAO::executeQuery("
    SELECT
     civicrm_contact.id         AS cid,
     civicrm_contact.first_name AS first_name,
     civicrm_contact.last_name  AS last_name,
     civicrm_email.email        AS email
    FROM civicrm_contact
    LEFT JOIN civicrm_email ON civicrm_email.contact_id = civicrm_contact.id AND civicrm_email.is_primary=1
    WHERE civicrm_contact.id IN ({$cid_list});");
  while ($query->fetch()) {
    // gather metadata
    $metadata = array();
    foreach ($fields as $source_field => $field_key) {
      $metadata[] = array(
        'key'   => $field_key,
        'value' => $query->$source_field
      );
    }
    $metadata_string = urlencode(json_encode($metadata));

    foreach ($tokens['surveypal'] as $key => $value) {
      // there seems to be a difference between indivudual and mass mailings:
      $token = $job ? $key : $value;

      // get survey
      $survey = $survey_index[$token];

      // set value
      $values[$query->cid]["surveypal.{$token}"] = sprintf($url_template,
        $survey['id'],
        $survey['token'],
        $query->cid,
        urlencode($query->email),
        $metadata_string);
    }
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function surveypal_civicrm_config(&$config) {
  _surveypal_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function surveypal_civicrm_xmlMenu(&$files) {
  _surveypal_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function surveypal_civicrm_install() {
  _surveypal_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function surveypal_civicrm_postInstall() {
  _surveypal_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function surveypal_civicrm_uninstall() {
  _surveypal_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function surveypal_civicrm_enable() {
  _surveypal_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function surveypal_civicrm_disable() {
  _surveypal_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function surveypal_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _surveypal_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function surveypal_civicrm_managed(&$entities) {
  _surveypal_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function surveypal_civicrm_caseTypes(&$caseTypes) {
  _surveypal_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function surveypal_civicrm_angularModules(&$angularModules) {
  _surveypal_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function surveypal_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _surveypal_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 */
function surveypal_civicrm_entityTypes(&$entityTypes) {
  _surveypal_civix_civicrm_entityTypes($entityTypes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function surveypal_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function surveypal_civicrm_navigationMenu(&$menu) {
  _surveypal_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _surveypal_civix_navigationMenu($menu);
} // */
