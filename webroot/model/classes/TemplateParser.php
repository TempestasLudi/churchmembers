<?php

/*
  ChurchMembers is a php/ajax webbased crm application targeting churches.
  Through it they can administer members, addresses and groups.
  Copyright (C) 2011  goblin47 & thelionnl

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>
 */

class TemplateParser extends AbstractProcessor {

  public $templateOutput;

  public function processRequest() {

  }

  public function __construct($template, $contentPlaceholders, $database) {
    $this->database = $database;
    $this->templateOutput;
    $this->templateLangPlaceholders = array();
    $this->templatePlaceholders = array();
    $this->contentPlaceholders = $contentPlaceholders;

    $templatedir = $_SESSION['USER']->getUsertemplate();

    switch ($template) {

      case "ADDMEMBER":
      case "MOVEMEMBER":
      case "UNSCRIBEMEMBER":
      case "ADDADDRESS":
      case "MOVEADDRESS":
      case "UNSCRIBEADDRESS":
      case "ADDRESSGROUPS":
      case "EVENTS":
        $templatedir = "dialogs";
        $templatefile = strtolower($template);
        break;

      case "ADMINMENU":
      case "FAILEDLOGIN":
      case "EMPTYTABLES":
      case "USERRIGHTS":
      case "MAINTENANCE":
      case "SETTINGS":
        $templatedir = "admin";
        $templatefile = strtolower($template);
        break;

      case 'HOME':
      case 'PHOTOBOOK':
      case 'EMAIL':
      case 'EMAILMESSAGE':
        $templatedir = "default";
        $templatefile = strtolower($template);
        break;

      case '':
        print_r("No template input");
        exit();
        break;

      default:
        $templatefile = strtolower($template);
        break;
    }

    if (isset($_SESSION['SESSION-INFO']['MOBILE']) && $_SESSION['SESSION-INFO']['MOBILE'] === true) {
      $templatedir = 'mobile';
    }
    $templatefile = $templatedir . '/' . $templatefile . ".tpl";
    $this->templateFile = TEMPLATES_PATH . $templatefile;
  }

  /**
   * Remove unused placeholders from documentOutput ($this->documentOutput)
   */
  private function clearPlaceholders() {
    if (strpos($this->templateOutput, '[+') > -1) {
      $matches = array();
      preg_match_all('~\[\+(.*?)\+\]~', $this->templateOutput, $matches);
      if ($matches[0])
        $this->templateOutput = str_replace($matches[0], '', $this->templateOutput);
    }
  }

  /**
   * Fill placeholders in $this->templateOutput.
   * Catches all placeholders in template and puts it in $this->$Placeholders
   * $this->$Placeholders[i] = one placeholder found in template
   */
  private function fillPlaceholders() {
    //First all language patterns
    $pattern = '/{t}(.*?){\/t}/';
    if (preg_match_all($pattern, $this->templateOutput, $this->templateLangPlaceholders)) {
      $this->templateLangPlaceholders = array_unique($this->templateLangPlaceholders[0]);
      foreach ($this->templateLangPlaceholders as &$LangPlaceholder) {
        $LangplaceholderInfo = array();
        $pattern = '/{t}(.*?){\/t}/';
        preg_match($pattern, $LangPlaceholder, $LangplaceholderInfo);
        $this->setPlaceholder($LangPlaceholder, __("$LangplaceholderInfo[1]")); // set localized text
      }
    }


    //Second all others patterns
    $pattern = '/\[\+[a-zA-Z0-9._]*\+\]/';
    if (preg_match_all($pattern, $this->templateOutput, $this->templatePlaceholders)) {
      $this->templatePlaceholders = array_unique($this->templatePlaceholders[0]);

      foreach ($this->templatePlaceholders as &$placeholder) {

        $placeholderInfo = array(); // $placeholderInfo[0] = [+type.value+], $placeholderInfo[1] = type, $placeholderInfo[2]= value
        $pattern = '/\[\+([\w]+).([\w]+)[^\+]*\+\]/';
        preg_match($pattern, $placeholder, $placeholderInfo);
        switch ($placeholderInfo[1]) {
          case 'TEMPLATE':
            $input = "";
            $contentPlaceholders = "";

            switch ($placeholderInfo[2]) {
              case 'SEARCHBOX':
                break;

              case 'USERRIGHTS':
                $usertype = $this->database->getFirstUsertypeById();
                $_SESSION['CURRENT-VIEW']['ADMIN']['CURRENT_USERTYPE'] = $usertype;
                $userrights = json_decode($usertype->USERTYPE_rights, true);
                $contentPlaceholders = (object) array_merge((array) $usertype, $userrights);
                break;

              case 'SETTINGS':
                $settings = $this->database->getSettings();
                $contentPlaceholders = $settings;
                break;
            }

            $TMPtemplate = new TemplateParser($placeholderInfo[2], $contentPlaceholders, $this->database);
            $input = $TMPtemplate->parseOutput();
            $this->setPlaceholder($placeholderInfo[0], stripslashes($input));

            break;

          case 'VALUE':
            switch ($placeholderInfo[2]) {
              case 'MEMBER_birthdate':
                if (!($_SESSION['USER']->checkUserrights('edit_mode'))) {
                  $output = $this->database->getBirthdday($this->contentPlaceholders->MEMBER_birthdate, $this->contentPlaceholders->MEMBER_birthdateview);
                } else {
                  $output = $this->database->getBirthdday($this->contentPlaceholders->MEMBER_birthdate, 1);
                }

                $this->setPlaceholder($placeholderInfo[0], $output);
                break;

              case 'ADR_fullname':
                $fullname = ($this->contentPlaceholders->ADR_familyname_preposition === '') ? $this->contentPlaceholders->ADR_familyname : $this->contentPlaceholders->ADR_familyname_preposition . " " . $this->contentPlaceholders->ADR_familyname;
                $this->setPlaceholder($placeholderInfo[0], $fullname);
                break;

              case 'ADR_street_extra_template':
                $input = ($this->contentPlaceholders->ADR_street_extra !== '') ? $this->contentPlaceholders->ADR_street_extra . '<br />' : '';

                $this->setPlaceholder($placeholderInfo[0], $input);
                break;

              case 'ADR_country_template':
                $input = ($this->contentPlaceholders->ADR_country !== '') ? '<br />' . $this->contentPlaceholders->ADR_country : '';

                $this->setPlaceholder($placeholderInfo[0], $input);
                break;

              case 'ADR_COORDINATES':
                $coords = $this->contentPlaceholders->ADR_lat . ", " . $this->contentPlaceholders->ADR_lng;
                if (($this->contentPlaceholders->ADR_lat === '0.0000000') or ($this->contentPlaceholders->ADR_lng === '0.0000000')) {
                  $coordinates = 'void(0)';
                } else {
                  $coordinates = 'getMaps(' . $coords . ')';
                }
                $this->setPlaceholder($placeholderInfo[0], $coordinates);
                break;

              case 'MEMBER_fullname':
                $this->setPlaceholder($placeholderInfo[0], stripslashes($this->database->generateFullMemberName($this->contentPlaceholders, false, true)));
                break;
              case 'MEMBER_gender':
                $this->setPlaceholder($placeholderInfo[0], ($this->contentPlaceholders->$placeholderInfo[2] == 'male') ? __("Male") : __("Female"));
                break;
              case 'MEMBER_introduction':
                $introduction_plain = $this->contentPlaceholders->$placeholderInfo[2];
                $introduction_text = preg_replace('/\v+|\\\[rn]/', '<br/>', $introduction_plain);

                if ($introduction_text === '') {
                  $introduction_text = __('Profile is empty');
                }

                $this->setPlaceholder($placeholderInfo[0], $introduction_text);
                break;

              case 'ADR_GROUPS':
                $addressGroups = $this->database->getAddressInGroup($this->contentPlaceholders->ADR_id);
                $input = "";
                foreach ($addressGroups as $GROUP) {
                  if (($GROUP->_IN_GROUP == 1)) {
                    $input .= '<button name="GROUPS_button" onclick="getGroup(' . $GROUP->GROUP_id . ')">' . $GROUP->GROUP_name . '</button>&nbsp;';
                  }
                }
                $this->setPlaceholder($placeholderInfo[0], $input);
                break;

              case 'CONTAIN_GROUPS':
                $groups = $this->database->getGroupsFromParent($this->contentPlaceholders->GROUP_id);
                $input = (count($groups) > 1 ) ? '<br /><strong>' . __('Contains the following groups') . '</strong><br />' : '';

                foreach ($groups as $GROUP) {
                  $input .= '<button name="GROUPS_button" onclick="getGroup(' . $GROUP->GROUP_id . ')">' . $GROUP->GROUP_name . '</button>&nbsp;';
                }
                $this->setPlaceholder($placeholderInfo[0], $input);
                break;

              //
              case 'GROUP':
                $memberGroups = $this->database->getMemberInGroup($this->contentPlaceholders->MEMBER_id);
                $input = "";
                if (count($memberGroups)) {
                  foreach ($memberGroups as $GROUP) {
                    $input .= ($GROUP->MEMBER_IN_GROUP == 1) ? $GROUP->GROUP_name . ", " : '';
                  }
                }
                $this->setPlaceholder($placeholderInfo[0], rtrim($input, ", "));
                break;

              case 'GROUP_marker':
                $this->setPlaceholder($placeholderInfo[0], ($this->contentPlaceholders->$placeholderInfo[2] === '') ? 'css/images/googlemaps/marker1.png' : $this->contentPlaceholders->$placeholderInfo[2]);
                break;

              case 'TOTAL_MEMBERSTYPES':
                $memberTypes = $this->database->getMembertypeStats();
                $input = "";
                if (count($memberTypes)) {
                  foreach ($memberTypes as &$MEMBERTYPE) {

                    $input .= '<tr><th >' . $MEMBERTYPE->MEMBERTYPE_name . '</th><td >' . $MEMBERTYPE->TOTAL_MEMBERTYPES . '</td></tr>';
                  }
                }
                $this->setPlaceholder($placeholderInfo[0], $input);
                break;

              case 'YEARLY_EVENTS':
                $getEventStats = $this->database->getEventStats();
                $input = "";
                if (count($getEventStats)) {
                  foreach ($getEventStats as &$EVENT) {

                    $input .= '<tr><th >' . $this->database->getEventTypeTranslation($EVENT->EVENTTYPE_name) . '</th><td >' . $EVENT->TOTAL_EVENTS . '</td></tr>';
                  }
                }
                $this->setPlaceholder($placeholderInfo[0], $input);
                break;

              case 'LAST_EVENTS':
                $lastEvents = $this->database->getLastEvents();
                $input = "";

                if (count($lastEvents)) {

                  foreach ($lastEvents as $event) {
                    // Header table
                    $eventtranslation = $_SESSION['EVENTTYPES']->{$event['EVENTTYPE_name']}->translation;
                    $date = $event['EVENT_date'];
                    $input .= '<tr class="ui-widget-header"><th colspan="2">' . $date . ' | ' . $eventtranslation . '</th></tr>';
                    $userstext = '';
                    $userlink = '';
                    $size = (isset($_SESSION['SESSION-INFO']['MOBILE']) && $_SESSION['SESSION-INFO']['MOBILE'] === true) ? '80' : '32';

                    // Get members
                    foreach ($event['_MEMBERS'] as $_member) {
                      $MEMBER = $this->database->getMemberById($_member->EVENT_MEMBER_id, true);
                      $event['MEMBERS'][] = $MEMBER;

                      if (count($MEMBER) > 0) {
                        // Set Usericon

                        $userphoto_full = 'css/images/users/user_unknown.png';
                        if ($MEMBER->MEMBER_photo) {
                          $usericon = BASE_URL . "includes/phpThumb/phpThumb.php?w=$size&h=$size&far=1&zc=1&src=../../" . $MEMBER->MEMBER_photo;
                          $userphoto_full = $MEMBER->MEMBER_photo;
                        } else {
                          $usericon = BASE_URL . "includes/phpThumb/phpThumb.php?w=$size&h=$size&far=1&zc=1&f=png&src=../../css/images/users/user_unknown.png";
                        }

                        $userlink = '<a class="member_link" member-id="' . $MEMBER->MEMBER_id . '" address-id="' . $MEMBER->ADR_id . '">' . $this->database->generateFullMemberName($MEMBER, false, true) . "</a>";
                        $userstext .= '<div class="ui-button-text lastevents_row">
                            <div class="lastevents_icon"><a class="colorbox" href="' . $userphoto_full . '"><img src=' . $usericon . '  width="' . $size . '" height="' . $size . '" alt=""/></a></div>
                            <div class="lastevents_name">' . stripslashes($userlink) . '</div>
                            </div>';
                      } else {
                        $userlink = '<a>' . $_member->EVENT_MEMBER_fullname . "</a>";
                        $usericon = BASE_URL . "includes/phpThumb/phpThumb.php?w=$size&h=$size&far=1&zc=1&f=png&src=../../css/images/users/user_unknown.png";
                        $userstext .= '<div class="ui-button-text lastevents_row">
                            <div class="lastevents_icon"><img src=' . $usericon . '  width="' . $size . '" height="' . $size . '" alt="" /></div>
                            <div class="lastevents_name">' . stripslashes($userlink) . '</div>
                            </div>';
                      }
                    }

                    switch ($event['EVENTTYPE_name']) {
                      case "EVENT_CHANGED_PHONE":
                        $value = $event['MEMBERS'][0]->MEMBER_mobilephone;
                        break;

                      case "EVENT_CHANGED_BUSINESS_PHONE":
                        $value = $event['MEMBERS'][0]->MEMBER_business_phone;
                        break;

                      case "EVENT_CHANGED_HOME_PHONE":
                        $value = $event['MEMBERS'][0]->ADR_telephone;
                        break;

                      case "EVENT_CHANGED_EMAIL":
                        $value = $event['MEMBERS'][0]->MEMBER_email;
                        break;

                      case "EVENT_CHANGED_BUSINESS_EMAIL":
                        $value = $event['MEMBERS'][0]->PARTNER1_MEMBER_business_email;
                        break;

                      case "EVENT_CHANGED_HOME_EMAIL":
                        $value = $event['MEMBERS'][0]->ADR_email;
                        break;

                      case 'EVENT_GONE':
                        $value = '';
                        break;

                      default:
                        if (is_array($event['MEMBERS'][0])) {
                          if (($event['_MEMBERS'][0]->EVENT_MEMBER_address === '') && ($event['_MEMBERS'][0]->EVENT_MEMBER_adr_id)) {
                            $address = $this->database->getAddressById($event['_MEMBERS'][0]->EVENT_MEMBER_adr_id);
                            if ($address !== NULL) {
                              $value = '<a onclick="getAddress(' . $address->ADR_id . ')"> ';
                              $value .= ($address->ADR_street_extra !== '') ? $address->ADR_street_extra . '<br/>' : '';
                              $value .= $address->ADR_street . "&nbsp;" . $address->ADR_number . "<br/>" . $address->ADR_zip . "&nbsp;&nbsp;" . $address->ADR_city;
                              $value .= ($address->ADR_country !== '') ? '<br/>' . $address->ADR_country : '';
                              $value .= '</a>';
                            } else {
                              $value = $event['_MEMBERS'][0]->EVENT_MEMBER_address;
                            }
                          } else {
                            $value = $event['_MEMBERS'][0]->EVENT_MEMBER_address;
                          }
                        } else {
                          // Set Address
                          $value = '<a onclick="getAddress(' . $event['MEMBERS'][0]->ADR_id . ')"> ';
                          $value .= ($event['MEMBERS'][0]->ADR_street_extra !== '') ? $event['MEMBERS'][0]->ADR_street_extra . '<br/>' : '';
                          $value .= $event['MEMBERS'][0]->ADR_street . "&nbsp;" . $event['MEMBERS'][0]->ADR_number . "<br/>" . $event['MEMBERS'][0]->ADR_zip . "&nbsp;&nbsp;" . $event['MEMBERS'][0]->ADR_city;
                          $value .= ($event['MEMBERS'][0]->ADR_country !== '') ? '<br/>' . $event['MEMBERS'][0]->ADR_country : '';
                          $value .= '</a>';
                        }
                        break;
                    }

                    $input .= '<tr><td>' . $userstext . '</td><td>' . stripslashes($value) . ' </td></tr>';
                  }
                } else {
                  $input .= '<tr><td colspan="2">' . __("No recent mutations") . '</td></tr>';
                }
                $this->setPlaceholder($placeholderInfo[0], $input);
                break;

              case 'system_version':
                $input = $this->database->getVersionInfo();

                $this->setPlaceholder($placeholderInfo[0], stripslashes($input));
                break;

              default:
                $this->setPlaceholder($placeholderInfo[0], stripslashes($this->contentPlaceholders->$placeholderInfo[2]));
                break;
            }
            break;
          case 'LABEL':
            die('error!!!');
            break;
          case 'INPUT':
            $input = '';
            switch ($placeholderInfo[2]) {

              case 'GENDER':
                $maleSelected = ($this->contentPlaceholders->MEMBER_gender === 'male') ? 'checked = "checked"' : '';
                $femaleSelected = ($this->contentPlaceholders->MEMBER_gender === 'female') ? 'checked = "checked"' : '';

                $input = '<div id="MEMBER_gender_checkbox">';
                $input .='<label for="MEMBER_gender_male">' . __("Male") . '</label>
			  <input name="MEMBER_gender" id="MEMBER_gender_male" type="radio" value="male" ' . $maleSelected . 'onchange="editDetails(\'members\',this, \'#LABEL_MEMBER_gender\')">';
                $input .='<label for="MEMBER_gender_female">' . __("Female") . '</label>
			  <input name="MEMBER_gender" id="MEMBER_gender_female" type="radio" value="female" ' . $femaleSelected . 'onchange="editDetails(\'members\',this, \'#LABEL_MEMBER_gender\')">';
                $input .='</div>	';
                break;

              case 'MEMBER_TYPE':
                $res = $this->database->getTable("membertypes");
                $input = '<div id="MEMBER_membertype_id_checkbox">';

                foreach ($res as $MEMBER_TYPE) {
                  $membertypeChecked = ($this->contentPlaceholders->MEMBER_membertype_id === $MEMBER_TYPE->MEMBERTYPE_id) ? 'checked = "checked"' : '';
                  $input .= '<label for="MEMBER_membertype_id_' . $MEMBER_TYPE->MEMBERTYPE_id . '">' . $MEMBER_TYPE->MEMBERTYPE_name . '</label><input name="MEMBER_membertype_id" id="MEMBER_membertype_id_' . $MEMBER_TYPE->MEMBERTYPE_id . '" type="radio" value="' . $MEMBER_TYPE->MEMBERTYPE_id . '" ' . $membertypeChecked . 'onchange="editDetails(\'members\',this, \'#LABEL_MEMBER_type\')">';
                }
                $input .= '</div>';
                break;

              case 'MEMBER_parent':
              case 'MEMBER_inyearbook':
                $checked = ($this->contentPlaceholders->$placeholderInfo[2] === 1) ? 'checked="checked"' : '';
                $input .='<input name="' . $placeholderInfo[2] . '" type="checkbox" id="' . $placeholderInfo[2] . '" value="1" ' . $checked . 'onchange="editDetails(\'members\',this, \'#LABEL_MEMBER_ACTIONS\')" />';
                break;

              case 'MEMBER_birthdateview':
                $MEMBER_birthdateview = $this->contentPlaceholders->$placeholderInfo[2];
                $input = '<select id="MEMBER_birthdateview" name="MEMBER_birthdateview" onchange="editDetails(\'members\',this, \'#LABEL_MEMBER_birthdateview\')" style="width:375px;">';
                $viewSelected = 1;
                $input .= '<option VALUE="1" ' . (($MEMBER_birthdateview === 1) ? 'selected = "selected"' : '') . '>' . __("Show complete Birth date") . '</option>';
                $input .= '<option VALUE="2" ' . (($MEMBER_birthdateview === 2) ? 'selected = "selected"' : '') . '>' . __("Show only birth year") . '</option>';
                $input .= '<option VALUE="0" ' . (($MEMBER_birthdateview === 0) ? 'selected = "selected"' : '') . '>' . __("Hide birth date") . '</option>';
                $input .= '</select>';
                break;

              case 'MEMBER_familynameview':
                $familynameview = $this->contentPlaceholders->$placeholderInfo[2];
                $input = '<select id="MEMBER_familynameview" name="MEMBER_familynameview" onchange="editDetails(\'members\',this, \'#LABEL_MEMBER_familynameview\')" style="width:375px;">';
                if (($this->contentPlaceholders->MEMBER_familyname != $this->contentPlaceholders->ADR_familyname) and ($this->contentPlaceholders->MEMBER_parent == 1 )) {
                  $input .= '<option VALUE="1" ' . (($familynameview === 1) ? 'selected = "selected"' : '') . '>' . stripslashes($this->database->generateFullMemberName($this->contentPlaceholders, false, false, '', 1)) . '</option>';
                  $input .= '<option VALUE="5" ' . (($familynameview === 5) ? 'selected = "selected"' : '') . '>' . stripslashes($this->database->generateFullMemberName($this->contentPlaceholders, false, false, '', 5)) . '</option>';

                  $input .= '<option VALUE="3" ' . (($familynameview === 3) ? 'selected = "selected"' : '') . '>' . stripslashes($this->database->generateFullMemberName($this->contentPlaceholders, false, false, '', 3)) . '</option>';
                  $input .= '<option VALUE="7" ' . (($familynameview === 7) ? 'selected = "selected"' : '') . '>' . stripslashes($this->database->generateFullMemberName($this->contentPlaceholders, false, false, '', 7)) . '</option>';

                  $input .= '<option VALUE="4" ' . (($familynameview === 4 || $familynameview == 0) ? 'selected = "selected"' : '') . '>' . stripslashes($this->database->generateFullMemberName($this->contentPlaceholders, false, false, '', 4)) . '</option>';
                  $input .= '<option VALUE="8" ' . (($familynameview === 8) ? 'selected = "selected"' : '') . '>' . stripslashes($this->database->generateFullMemberName($this->contentPlaceholders, false, false, '', 8)) . '</option>';
                }

                $input .= '<option VALUE="2" ' . (($familynameview === 2) ? 'selected = "selected"' : '') . '>' . stripslashes($this->database->generateFullMemberName($this->contentPlaceholders, false, false, '', 2)) . '</option>';

                // if under 20 and membertype =! 'belijdend lid' or `gast belijdend lid', membername is only shown
                $member_age = isset($this->contentPlaceholders->MEMBER_age) ? $this->contentPlaceholders->MEMBER_age : $this->database->getAge($this->contentPlaceholders->MEMBER_birthdate);
                if (($member_age > 20) or ($this->contentPlaceholders->MEMBER_membertype_id == 2) or ($this->contentPlaceholders->MEMBER_membertype_id == 4)) {
                  $input .= '<option VALUE="6" ' . (($familynameview === 6) ? 'selected = "selected"' : '') . '>' . stripslashes($this->database->generateFullMemberName($this->contentPlaceholders, false, false, '', 6)) . '</option>';
                }

                $input .= '</select>';
                break;

              case 'GROUP_inyearbook':
              case 'GROUP_onmap':
                $checked = ($this->contentPlaceholders->$placeholderInfo[2] === 1) ? 'checked="checked"' : '';
                $input .='<input name="' . $placeholderInfo[2] . '" type="checkbox" id="' . $placeholderInfo[2] . '" value="1" ' . $checked . 'onchange="editDetails(\'groups\',this, \'#LABEL_GROUP_options\')"/>';
                break;

              case 'GROUP_type':
                $val = $this->contentPlaceholders->$placeholderInfo[2];
                $input .='<input name="GROUP_TYPE" type="radio" id="GROUP_TYPE_members" value="members" ' . (($val === 'members') ? 'checked="checked" ' : '') . 'onchange="editDetails(\'groups\',this, \'#LABEL_GROUP_options\')"/><label for="GROUP_TYPE_members">' . __('Members') . '</label>';
                $input .='<input name="GROUP_TYPE" type="radio" id="GROUP_TYPE_addresses" value="addresses" ' . (($val === 'addresses') ? 'checked="checked" ' : '') . 'onchange="editDetails(\'groups\',this, \'#LABEL_GROUP_options\')"/><label for="GROUP_TYPE_addresses">' . __('Addresses') . '</label>';
                break;

              case 'view_address':
              case 'view_groups':
              case 'view_mutations':
              case 'view_report':
              case 'view_map':
              case 'view_archive':
              case 'edit_mode':
              case 'add_address':
              case 'add_group':
              case 'add_member':
              case 'add_event':
              case 'add_data':
              case 'view_admin':
              case 'sort_members':
              case 'delete_data':

                $checked = ($this->contentPlaceholders->$placeholderInfo[2] === 1) ? 'checked="checked"' : '';
                $input .='<input name="RIGHT[' . $placeholderInfo[2] . ']" class="button" type="checkbox" id="' . $placeholderInfo[2] . '" value="1" ' . $checked . 'onchange="setUserTypeById(\'#USERTYPE_form\',\'#admintabs-1\')" /><label for="' . $placeholderInfo[2] . '" value="1">' . __("Allowed") . '</label>';
                break;

              case 'USERTYPELIST':
                $input = '<select id="USERTYPELIST_selectbox" name="USERTYPE_id" onchange="getAdminContent(\'userrights\',this.value)" style="width:375px;">';
                $allUsertypes = $this->database->getTable('usertypes');
                foreach ($allUsertypes as &$Usertype) {
                  $UsertypeSelected = ($Usertype->USERTYPE_id === $this->contentPlaceholders->USERTYPE_id) ? 'selected = "selected"' : '';
                  $input .= '<option VALUE="' . $Usertype->USERTYPE_id . '" ' . $UsertypeSelected . '>' . $Usertype->USERTYPE_name . '</option>';
                }

                $input .= '</select>';
                break;

              case 'MEMBER_photobutton':
                $input = $_SESSION['CURRENT-VIEW']['PHOTO_BUTTON'];
                break;

              case 'MEMBER_introduction':
                $introduction_plain = $this->contentPlaceholders->MEMBER_introduction;
                if ($_SESSION['USER']->checkUserrights('edit_mode')) {
                  $introduction_text = preg_replace('/\v+|\\\[rn]/', "\n", $introduction_plain);
                } else {
                  $introduction_text = preg_replace('/\v+|\\\[rn]/', '<br/>', $introduction_plain);
                }



                if ($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id === $_SESSION['USER']->getUserlinktomemberId()) {
                  if ($introduction_text === '') {
                    $introduction_text = __('Your profile is empty');
                  }
                  $input = '<textarea id="MEMBER_introduction" name="MEMBER_introduction" style="width: 100%;">' . $introduction_text . '</textarea>';
                } else {
                  if ($introduction_text === '') {
                    $introduction_text = __('Profile is empty');
                  }
                  $input = $introduction_text;
                }

                break;

              case 'export_docraptor_enabled':
              case 'locale_officecode_visible':
              case 'system_secure':
              case 'auth_enabled':
              case 'maintenance':
              case 'login_mail':
              case 'mail_use':
                $currentvalue = $this->database->getSetting($placeholderInfo[2])->SETTINGS_value;
                $checked = ($currentvalue == true) ? 'checked="checked"' : '';
                $input .='<input name="' . $placeholderInfo[2] . '" type="checkbox" id="' . $placeholderInfo[2] . '" value="1" ' . $checked . ' onchange="editDetails(\'settings\',this, \'#SETTINGS_' . $placeholderInfo[2] . '\')"/>';
                break;

              case 'yearbook_pdf':
              case 'addresses_pdf':
              case 'membergroupscomplete_pdf':
              case 'membergroupssimple_pdf':
              case 'allbirthdays_pdf':
              case 'specialbirthdays_pdf':
              case 'mariagedates_pdf':
              case 'photobook_pdf':
              case 'lastchanges_pdf':
                if ($this->database->getSetting('export_docraptor_enabled')->SETTINGS_value === true && $this->database->getSetting('export_docraptor_key')->SETTINGS_value !== '') {
                  $elements = explode("_", $placeholderInfo[2]); // $elements[0] = yearbook
                  $input = '<a onclick="Export(\'' . $elements[0] . '\',\'pdf\')"><img src="css/images/export/pdf.png" alt="' . __("Export to PDF") . '" title="' . __("Export to PDF") . '" /></a>';
                }

                break;

              case 'DIALOG_MEMBER_TYPE':
                $res = $this->database->getTable("membertypes");
                $input = '<select name="newMembertype_id" id="newMembertype_id" class="text ui-widget-content ui-corner-all" style="width:150px">';

                foreach ($res as $MEMBER_TYPE) {
                  $input .= '<option value="' . $MEMBER_TYPE->MEMBERTYPE_id . '">' . $MEMBER_TYPE->MEMBERTYPE_name . '</option>';
                }
                $input .= '</select>';
                break;

              case 'DIALOG_EVENTS_TYPES':
                $input = '<select name="newEventType" id="newEventType" class="text ui-widget-content ui-corner-all" style="width:350px" onchange="updateEventDialog($(this).val())">';

                foreach ($this->contentPlaceholders as $eventtype => $translation) {
                  $input .= '<option value="' . $eventtype . '">' . $translation . '</option>';
                }
                $input .= '</select>';
                break;
            }
            $this->setPlaceholder($placeholderInfo[0], stripslashes($input));
            break;

          case 'REPORTOPTIONS':
            $input = '';
            switch ($placeholderInfo[2]) {

              case 'GROUPLIST':
                $groupslist = $this->database->getGroups();
                if (count($groupslist) > 0) {
                  $orderGroups = new stdClass;
                  $groupslist = $this->database->GenerateObjectGroupTree($groupslist, $orderGroups);
                  $input .= $this->CreateGroupSearchList($groupslist, $input);
                }
                break;

              case 'EVENTLIST':
                $eventslist = $this->database->getEventsList();
                if (count($eventslist) > 0) {

                  $input .= '<div id="Report_Events_Select">';

                  $eventtypes = array('EVENT_ADD_GUESTMEMBERSHIP', 'EVENT_ADD_BIRTH_TESTIMONY', 'EVENT_ADD_CONFESSION_TESTIMONY',
                  'EVENT_CONTINUE_STAY_TESTIMONY', 'EVENT_CONTINUE_GUESTMEMBERSHIP', 'EVENT_SICK',
                  'EVENT_BAPTISED', 'EVENT_CONFESSION', 'EVENT_MARRIAGE', 'EVENT_ADD_STAY_TESTIMONY',
                  'EVENT_ADD_MEMBERSHIP', 'EVENT_ADD_NEWMEMBER', 'EVENT_BIRTH', 'EVENT_MOVED', 'EVENT_MARRIAGE',
                  'EVENT_DIVORCE', 'EVENT_CHANGED_HOME_EMAIL', 'EVENT_CHANGED_HOME_PHONE','EVENT_DIED');

                  foreach ($eventslist as $event) {
                    if (in_array($event->EVENTTYPE_name, $eventtypes)){
                      $input .= '<input type="checkbox" id="Report_Events_Select_Box_' . $event->EVENTTYPE_id . '" value="' . $event->EVENTTYPE_name . '" name="Report_Events_Select_Box[]"/>';
                      $input .= '<label for="Report_Events_Select_Box_' . $event->EVENTTYPE_id . '" title="' . $this->database->getEventTypeTranslation($event->EVENTTYPE_name) . '"  style="width:98%; margin-bottom:5px;" >';
                      $input .= $this->database->getEventTypeTranslation($event->EVENTTYPE_name) . '</label>';
                    }
                  }
                  $input .= '</div>';
                }
                break;

              case 'CITYLIST':
                $citylist = $this->database->getCityList();
                if (count($citylist) > 0) {

                  $i = 0;
                  foreach ($citylist as $city) {
                    $i++;
                    $input .= '<input type="checkbox" id="Report_City_Select_Box_' . $i . '" name="Report_City_Select_Box[]" value="' . $city->ADR_city . '"/>';
                    $input .= '<label for="Report_City_Select_Box_' . $i . '" title="' . $city->ADR_city . '"  style="width:98%; margin-bottom:5px;" >';
                    $input .= ucwords(strtolower($city->ADR_city)) . '</label>';
                  }
                }
                break;

              case 'MEMBERTYPELIST':
                $membertypeslist = $this->database->getMemberTypesList();
                if (count($membertypeslist) > 0) {

                  foreach ($membertypeslist as $membertype) {
                    $input .= '<input type="checkbox" id="Report_Memberstype_Select_Box_' . $membertype->MEMBERTYPE_id . '" value="' . $membertype->MEMBERTYPE_id . '" name="Report_Memberstype_Select_Box[]"/>';
                    $input .= '<label for="Report_Memberstype_Select_Box_' . $membertype->MEMBERTYPE_id . '" title="' . $membertype->MEMBERTYPE_name . '"  style="width:98%; margin-bottom:5px;" >';
                    $input .= $membertype->MEMBERTYPE_name . '</label>';
                  }
                }
                break;
            }
            $this->setPlaceholder($placeholderInfo[0], stripslashes($input));
          case 'ADMIN':
            $input = '';
            switch ($placeholderInfo[2]) {

              case 'FAILEDLOGIN':
                $failures = $this->database->getFailedLoginAttempts();
                foreach ($failures as $failure) {
                  $input .= "<tr>
                              <td>$failure->FAILEDACCESS_loginname</td>
                              <td>$failure->FAILEDACCESS_pass_decrypt</td>
                              <td>$failure->FAILEDACCESS_ip</td>
                              <td>" . date("d-m-Y H:i:s", $failure->FAILEDACCESS_timestamp) . "</td>
                              <td><a onclick='deleteFailedLogin($failure->FAILEDACCESS_id)'>delete</a></td>
                            </tr>";
                }
                break;

              case 'EMPTYTABLES':
                $tables = $this->database->getTableNames();
                foreach ($tables as $table) {
                  if ($table->Tables_in_churchmembers !== "users" && $table->Tables_in_churchmembers !== "usertypes") {
                    $input .= "<tr><th>" . $table->Tables_in_churchmembers . "</th>
                      <td><input type='checkbox' name='tables[]' value='" . $table->Tables_in_churchmembers . "' /></td></tr>";
                  }
                }
                break;
            }

            $this->setPlaceholder($placeholderInfo[0], stripslashes($input));
            break;

          case 'BUTTONS':
            $input = '';
            switch ($placeholderInfo[2]) {

              case 'ADDRESS':
                if ($_SESSION['USER']->checkUserrights('add_address')) {
                  $archive = ($_SESSION['ARCHIVE-MODE'] === 1) ? ' style="display:none"' : '';


                  $input = '<div class="splitbutton">
                  <div><button id="ADR_action" class="smallbutton">' . __("Select an action") . '</button></div>
                <ul>
                    <li><a id="buttonNewAddressDialog" title="' . __("Add address") . '"' . $archive . '><h3>' . __("Add address") . '</h3></a></li>
                    <li><a id="buttonMoveAddressDialog" title="' . __("Move address") . '"><h3>' . __("Move address") . '</h3></a></li>
                    <li><a id="buttonUnscribeAddressDialog" title="' . __("Unsubscribe address") . '"><h3>' . __("Unsubscribe address") . '</h3></a></li>
                  </ul>
                </div>';
                }
                break;

              case 'MEMBERSLIST':
                if ($_SESSION['USER']->checkUserrights('add_member')) {
                  $archive = ($_SESSION['ARCHIVE-MODE'] === 1) ? ' style="display:none"' : '';
                  $input .= "<div class='memberingrid' $archive >";
                  $input .= '<button id="buttonNewMemberDialog" name="MEMBERS_button" title="' . __("Add member") . '" style="width:100%">';
                  $input .= '<div class="member_icon"><img src="' . BASE_URL . 'includes/phpThumb/phpThumb.php?w=80&h=80&far=1&zc=1&f=png&src=../../css/images/icons/user_add.png" width="80" height="80" alt="" /></div><div class="member_name"><h1>' . __("Add member") . '</h1></div></button>';
                }
                break;

              case 'MEMBER':
                if ($_SESSION['USER']->checkUserrights('add_member')) {
                  $input = '<div class="splitbutton">
                  <div><button id="MEMBER_action" class="smallbutton">' . __("Select an action") . '</button></div>
                <ul>
                    <li><a id="buttonNewEventDialog" title="' . __("Add event") . '"><h3>' . __("Add event") . '</h3></a></li>
                    <li><a id="buttonMoveMemberDialog" title="' . __("Move member") . '"' . $archive . '><h3>' . __("Move member") . '</h3></a></li>
                    <li><a id="buttonUnscribeMemberDialog" title="' . __("Unsubscribe member") . '"><h3>' . __("Unsubscribe member") . '</h3></a></li>
                  </ul>
                </div>';
                }
                break;
            }

            $this->setPlaceholder($placeholderInfo[0], stripslashes($input));
            break;
        }
      }

      unset($placeholder); // break the reference with the last element
    } else {
      return false;
    }
  }

  /**
   * Fill placeholder in $this->templateOutput
   * @param string $placeholder
   * @param mixed $value
   */
  private function setPlaceholder($placeholder, $value) {
    $this->templateOutput = str_replace($placeholder, stripslashes($value), $this->templateOutput);
  }

  /**
   * Opens template located in '..' and puts it in $this->templateOutput.
   * @return bool
   */
  private function openTemplate() {
    if (file_exists($this->templateFile)) {
      $this->templateOutput = file_get_contents($this->templateFile);
      return true;
    } else {
      return false;
    }
  }

  /**
   *  Print template.
   * @return string
   */
  public function parseOutput() {
    if ($this->openTemplate()) {
      $this->fillPlaceholders();
      $this->clearPlaceholders();
      $this->templateOutput = $this->templateOutput;
      return $this->templateOutput;
    } else {
      return 'Template ' . $this->templateFile . ' is not found';
    }
  }

}

?>