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

/**
 * Configurations
 */
class CRM_Surveypal_Config {

  /**
   * get surveys
   */
  public static function getSurveys() {
    return CRM_Core_BAO_Setting::getItem('be.aivl.surveypal', 'surveypal_serveys');
  }

  /**
   * store surveys in settings
   */
  public static function storeSurveys($surveys) {
    CRM_Core_BAO_Setting::setItem($surveys, 'be.aivl.surveypal', 'surveypal_serveys');
  }
}