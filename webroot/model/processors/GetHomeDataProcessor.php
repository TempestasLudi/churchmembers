<?php

/**
 * This processor handles a request for getting an address.
 */
class GetHomeDataProcessor extends AbstractProcessor {

  public function processRequest() {

    ///////////////////////// PARSE HOME TEMPLATE /////////////////////////
    if ($contentPlaceholders = $this->database->getMemberStats()) {
      $user = $_SESSION['USER']->getUserlinktomember();
      $memberphotos_html = '';

      if ($user !== false) {
        // 5-8: same as 1-4 but with only initials and no firstname
        $generatedmembername = $this->database->generateFullMemberName($user, false);
        $fullname = $generatedmembername . (($user->MEMBER_parent === 0) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : '');

        if ($user->MEMBER_familynameview === 5 || $user->MEMBER_familynameview === 6 || $user->MEMBER_familynameview === 7 || $user->MEMBER_familynameview === 8 || !$user->MEMBER_firstname) {
          $buttonname = $generatedmembername;
        } else {
          $buttonname = '<h1>' . $user->MEMBER_firstname . '</h1>';
          $buttonname .= ($generatedmembername !== $user->MEMBER_firstname) ? $generatedmembername : '';
        }

        $size = 80;

        if ($user->MEMBER_photo) {
          $usericon = BASE_URL . "includes/phpThumb/phpThumb.php?w=$size&h=$size&far=1&zc=1&src=../../" . $user->MEMBER_photo;
        } else {
          $usericon = ($user->MEMBER_gender === "male") ? BASE_URL . "includes/phpThumb/phpThumb.php?w=$size&h=$size&far=1&zc=1&f=png&src=../../css/images/icons/list_male_user.png" : BASE_URL . "includes/phpThumb/phpThumb.php?w=$size&h=$size&far=1&zc=1&f=png&src=../../css/images/icons/list_female_user.png";
          $usericon = ($user->MEMBER_parent) ? BASE_URL . "includes/phpThumb/phpThumb.php?w=$size&h=$size&far=1&zc=1&f=png&src=../../css/images/icons/list_group.png" : $usericon;
        }

        $memberphotos_html .= '<div class="memberingrid" style="width:100%">';
        $memberphotos_html .= '<button name="MEMBERS_button" style="width:100%" onclick="getAddress(' . $user->ADR_id . ',' . $user->MEMBER_id . ')" title="' . $generatedmembername . '">';
        $memberphotos_html .= '<div class="member_icon"><img src="' . $usericon . '" width="' . $size . '" height="' . $size . '" alt="" /></div><div class="member_name">' . $buttonname . '</div></button>';
        $memberphotos_html .= '</div>';
      }
      $contentPlaceholders->MEMBER_photo = $memberphotos_html;

      $HOMEtemplate = new TemplateParser("HOME", $contentPlaceholders, $this->database);
      print_r($HOMEtemplate->parseOutput());
    } else {
      print(__("No data found"));
    }
  }

}

?>