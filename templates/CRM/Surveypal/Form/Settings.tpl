{*-------------------------------------------------------+
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
+-------------------------------------------------------*}

<table>
  <thead>
    <tr>
      <th></th>
      <th>Survey Name</th>
      <th>Survey ID</th>
      <th>Survey Token</th>
    </tr>
  </thead>
  <tbody>
    {foreach from=$slots item=slot_nr}
    <tr>
      {capture assign=slot_name}survey_name_{$slot_nr}{/capture}
      {capture assign=slot_id}survey_id_{$slot_nr}{/capture}
      {capture assign=slot_token}survey_token_{$slot_nr}{/capture}
      <td>#{$slot_nr}</td>
      <td>{$form.$slot_name.html}</td>
      <td>{$form.$slot_id.html}</td>
      <td>{$form.$slot_token.html}</td>
    </tr>
    {/foreach}
  <tbody>
</table>

  <!-- <div class="crm-section">
    <div class="label">{$form.$elementName.label}</div>
    <div class="content">{$form.$elementName.html}</div>
    <div class="clear"></div>
  </div> -->


<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
