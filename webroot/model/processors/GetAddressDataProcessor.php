<?php

/**
 * This processor handles a request for getting an address.
 */
class GetAddressDataProcessor extends AbstractProcessor {

  public function processRequest() {
    ///////////////////////// FIRST TRY TO GET CURRENT ADDRESS /////////////////////////
    if (!isset($_REQUEST['ADR_id']) || ($_REQUEST['ADR_id'] === '') || ($_REQUEST['ADR_id'] === 'false') || ($_REQUEST['ADR_id'] === 0)) {
      $addressId = isset($_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id) ? $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id : $addressId = $this->database->getFirstIdOfAddress();
    } else {
      $addressId = $_REQUEST['ADR_id'];
    }

    $address = $this->database->getAddressById($addressId);
    $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS'] = $address;

    ///////////////////////// IF NAVIGATE STRING IS SET, GET NEW CURRENT ADDRESS /////////////////////////
    if (isset($_REQUEST['navigate_str']) and ($_REQUEST['navigate_str'] !== 'false')) {
      if ($_REQUEST['navigate_str'] === "PREV_ADR")
        $newAddressId = $this->database->getPrevAddressByFamilyname($addressId, $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_familyname);
      if ($_REQUEST['navigate_str'] === "NEXT_ADR")
        $newAddressId = $this->database->getNextAddressByFamilyname($addressId, $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_familyname);

      if (isset($newAddressId->ADR_id)) {
        $addressId = $newAddressId->ADR_id;
        $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS'] = $this->database->getAddressById($addressId);
      }
    }

    ///////////////////////// SECOND TRY TO GET CURRENT ADDRESS /////////////////////////
    if (count($_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']) < 1) {
      $addressId = $this->database->getFirstIdOfAddress();
      if (count($addressId) > 0) {
        $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS'] = $this->database->getAddressById($addressId);
      }
    }

    ///////////////////////// IF STILL NO ADDRESS IS AVAILABLE THAN EXIT SCRIPT /////////////////////////
    if (count($_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']) < 1) {
      print(__("No data found"));
      die();
    }

    ///////////////////////// IF CURRENT ADDRESS IS SET, GET MEMBERS IN ADDRESS /////////////////////////
    $_SESSION['CURRENT-VIEW']['MEMBERS_IN_ADDRESS'] = $this->database->getMembersForAddress($addressId, $_SESSION['ARCHIVE-MODE']);

    ///////////////////////// IF MEMBERS IN ADDRESS IS SET, SET MEMBER ID /////////////////////////
    if (count($_SESSION['CURRENT-VIEW']['MEMBERS_IN_ADDRESS']) > 0) {
      if ((!isset($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->ADR_id)) and !isset($_REQUEST['MEMBER_id'])) {
        $memberId = $this->database->getFirstMemberIdOfAddress($_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id)->MEMBER_id;
      } elseif (isset($_REQUEST['MEMBER_id'])) {
        $memberId = $_REQUEST['MEMBER_id'];
      } else {

        if ($_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id !== $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->ADR_id) {
          $memberId = $this->database->getFirstMemberIdOfAddress($_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id)->MEMBER_id;
        } else {
          $memberId = $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id;
        }
      }
      $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER'] = $this->database->getMemberById($memberId);
    } else {
      $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER'] = "";
    }

    ///////////////////////// CREATE LIST OF MEMBERS ON ADDRESS /////////////////////////

    $memberlist_html = '';
    $memberphotos_html = '';
    $i = 0;

    if (count($_SESSION['CURRENT-VIEW']['MEMBERS_IN_ADDRESS']) > 0) {
      foreach ($_SESSION['CURRENT-VIEW']['MEMBERS_IN_ADDRESS'] as $member) {
        $i++;

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

        $memberphotos_html .= '<div class="memberingrid">';
        $memberphotos_html .= '<button name="MEMBERS_button" style="width:100%" onclick="getAddress(' . $member->ADR_id . ',' . $member->MEMBER_id . ')" title="' . $generatedmembername . '">';
        $memberphotos_html .= '<div class="member_icon"><img src="' . $usericon . '" width="' . $size . '" height="' . $size . '" alt="" /></div><div class="member_name">' . $buttonname . '</div></button>';
        $memberphotos_html .= '</div>';

        $editbutton = '<span class="ui-icon ui-icon-arrowthick-2-n-s imgInLine sortableicon"></span>';

        $memberlist_html .= '<div id="MembersList_' . $member->MEMBER_id . '" style="position:relative; width:100%">';
        $memberlist_html .= "<button name='MEMBERSLIST_button' id='member_" . $member->MEMBER_id . "'>$fullname</button>";
        $memberlist_html .= (($_SESSION['USER']->checkUserrights('sort_members')) ?  $editbutton : '');
        $memberlist_html .= '</div>';
      }
    } else {
      $memberlist_html = __("No members available");
    }

    ///////////////////////// PARSE ADDRESS TEMPLATE /////////////////////////
    if ($contentPlaceholders = $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']) {// Get one address with $addressId
      $start = $this->familynameStart($_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']);

      $familyname = htmlspecialchars($this->database->generateFullMemberName($_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS'], false, true, '', false, true), ENT_QUOTES, 'UTF-8');

      $contentPlaceholders->FAMILYNAME = $start . ' ' . $familyname;
      $contentPlaceholders->MEMBERSLIST = $memberlist_html;
      $contentPlaceholders->MEMBERSPHOTOS = $memberphotos_html;
      $ADDRESStemplate = new TemplateParser("ADDRESS", $contentPlaceholders, $this->database);
      print_r($ADDRESStemplate->parseOutput());
    } else {

      print(__("No data found"));
    }
  }

}

?>