<?php

/**
 * This processor handles a request for getting an address.
 */
class GetGroupDataProcessor extends AbstractProcessor {

  public function processRequest() {
    ///////////////////////// FIRST TRY TO GET CURRENT GROUP /////////////////////////
    if (!isset($_REQUEST['GROUP_id']) || ($_REQUEST['GROUP_id'] === '') || ($_REQUEST['GROUP_id'] === 0) || ($_REQUEST['GROUP_id'] === 'false')) {
      $groupId = isset($_SESSION['CURRENT-VIEW']['CURRENT_GROUP']->GROUP_id) ? $_SESSION['CURRENT-VIEW']['CURRENT_GROUP']->GROUP_id : $this->database->getFirstIdOfGroups();
      $groupId = $this->database->getFirstIdOfGroups();
    } else {
      $groupId = $_REQUEST['GROUP_id'];
    }

    $_SESSION['CURRENT-VIEW']['CURRENT_GROUP'] = $this->database->getGroupById($groupId);

    ///////////////////////// SECOND TRY TO GET CURRENT GROUP /////////////////////////
    if (count($_SESSION['CURRENT-VIEW']['CURRENT_GROUP']) < 1) {
      $groupId = $this->database->getFirstIdOfGroups();
      if (count($groupId) > 0) {
        $_SESSION['CURRENT-VIEW']['CURRENT_GROUP'] = $this->database->getGroupById($groupId->GROUP_id);
      }
    }

    ///////////////////////// IF STILL NO GROUP IS AVAILABLE THAN EXIT SCRIPT /////////////////////////
    if (count($_SESSION['CURRENT-VIEW']['CURRENT_GROUP']) < 1) {
      print(__("No data found"));
      die();
    }

    ///////////////////////// IF CURRENT GROUP IS SET, GET MEMBERS AND ADDRESSES IN GROUP /////////////////////////
    $_SESSION['CURRENT-VIEW']['MEMBERS_IN_GROUP'] = $this->database->getMembersForGroup($groupId);
    $_SESSION['CURRENT-VIEW']['ADDRESSES_IN_GROUP'] = $this->database->getAddressesForGroup($groupId);
    $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER'] = array();
    $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS'] = array();
    $_SESSION['CURRENT-VIEW']['MEMBERS_IN_ADDRESS'] = array();

    ///////////////////////// CREATE MEMBER BUTTONS /////////////////////////
    if (count($_SESSION['CURRENT-VIEW']['MEMBERS_IN_GROUP']) > 0) {
      $member_html = '<h1><strong>' . __("Members") . '</strong></h1><div><div id="overview"><div id="MemberGrid">';

      foreach ($_SESSION['CURRENT-VIEW']['MEMBERS_IN_GROUP'] as $member) {
        // 5-8: same as 1-4 but with only initials and no firstname
        $generatedmembername = $this->database->generateFullMemberName($member, false);
        $fullname = $generatedmembername . (($member->MEMBER_parent === 0) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : '');

        if ($member->MEMBER_familynameview === 5 || $member->MEMBER_familynameview === 6 || $member->MEMBER_familynameview === 7 || $member->MEMBER_familynameview === 8 || !$member->MEMBER_firstname) {
          $buttonname = $generatedmembername;
        } else {
          $buttonname = '<h1>' . $member->MEMBER_firstname . '</h1>';
          $buttonname .= ($generatedmembername !== $member->MEMBER_firstname) ? $generatedmembername : '';
        }

        $size = 80;

        if ($member->MEMBER_photo) {
          $usericon = BASE_URL . "includes/phpThumb/phpThumb.php?w=$size&h=$size&far=1&zc=1&src=../../" . $member->MEMBER_photo;
        } else {
          $usericon = ($member->MEMBER_gender === "male") ? BASE_URL . "includes/phpThumb/phpThumb.php?w=$size&h=$size&far=1&zc=1&f=png&src=../../css/images/icons/list_male_user.png" : BASE_URL . "includes/phpThumb/phpThumb.php?w=$size&h=$size&far=1&zc=1&f=png&src=../../css/images/icons/list_female_user.png";
          $usericon = ($member->MEMBER_parent) ? BASE_URL . "includes/phpThumb/phpThumb.php?w=$size&h=$size&far=1&zc=1&f=png&src=../../css/images/icons/list_group.png" : $usericon;
        }

        $member_html .= '<div class="memberingrid">';
        $member_html .= '<button name="MEMBERS_button" onclick="getAddress(' . $member->ADR_id . ',' . $member->MEMBER_id . ')" style="width:100%" title="' . $generatedmembername . '">';
        $member_html .= '<div class="member_icon"><img src="' . $usericon . '" width="' . $size . '" height="' . $size . '" alt="" /></div><div class="member_name">' . $buttonname . '</div></button>';
        $member_html .= '</div>';
      }

      $member_html .= '</div></div></div>';
    }

    ///////////////////////// CREATE ADDRESSES BUTTONS /////////////////////////
    if (count($_SESSION['CURRENT-VIEW']['ADDRESSES_IN_GROUP']) > 0) {
      $addresses_html = '<h1><strong>' . __("Addresses") . '</strong></h1><div><div id="overview"><div id="MemberGrid">';

      foreach ($_SESSION['CURRENT-VIEW']['ADDRESSES_IN_GROUP'] as $address) {
        $start = $this->familynameStart($address);
        $familyname = htmlspecialchars($this->database->generateFullMemberName($address, false, true, '', false, true), ENT_QUOTES, 'UTF-8');

        $buttonname = $start . ' ' . $familyname . '';

        $size = 40;
        $usericon = BASE_URL . "includes/phpThumb/phpThumb.php?w=$size&h=$size&far=1&zc=1&f=png&src=../../css/images/icons/home.png";

        $addresses_html .= '<div class="addressingrid">';
        $addresses_html .= '<button name="ADDRESS_button" onclick="getAddress(' . $address->ADR_id . ')" style="width:100%" title="' . $familyname . '">';
        $addresses_html .= '<div class="member_icon"><img src="' . $usericon . '" width="' . $size . '" height="' . $size . '" alt="" /></div><div class="member_name">' . $buttonname . '</div></button>';
        $addresses_html .= '</div>';
      }

      $addresses_html .= '</div></div></div>';
    }

    ///////////////////////// PARSE GROUP TEMPLATE /////////////////////////
    if ($contentPlaceholders = $_SESSION['CURRENT-VIEW']['CURRENT_GROUP']) {
      $contentPlaceholders->MEMBERSANDADDRESSES = $member_html . $addresses_html;
      $contentPlaceholders->GROUP_selected = $_SESSION['CURRENT-VIEW']['CURRENT_GROUP']->GROUP_id;
      $contentPlaceholders->GROUP_parent_id = $_SESSION['CURRENT-VIEW']['CURRENT_GROUP']->GROUP_parent_id;
      $GROUPtemplate = new TemplateParser("GROUP", $contentPlaceholders, $this->database);
      print_r($GROUPtemplate->parseOutput());
    } else {
      print(__("No data found"));
    }
  }

}

?>