<?php

/**
 * This processor handles a request for getting an address.
 */
class GetPhotobookDataProcessor extends AbstractProcessor {

  public function processRequest() {

    $addresses = $this->database->getMembersIntroduction();
    $output = '';
    $letter = '';
    $size = 80;
    foreach ($addresses as $ADR_id => $address) {

      foreach ($address->MEMBERS as $MEMBER_id => $member) {
        if ($letter !== strtoupper($member->ADR_familyname{0})) {
          $letter = strtoupper($member->ADR_familyname{0});
          $output .= "</td></tr><tr class='ui-widget-header'><td><h1>$letter</h1></td></tr><tr><td>";
        }

        $photo = ($member->MEMBER_photo != '') ? $member->MEMBER_photo : 'css/images/users/user_unknown.png';
        $location_small = 'includes/phpThumb/phpThumb.php?src=../../' . $photo . '&w=' . $size . '&h=' . $size . '&far=1&zc=1';
        $location_large = $photo;
        $member_fullname = $this->database->generateFullMemberName($member, false, true);
        $memberid = $member->MEMBER_id;

        $output .= "
          <div class='member gal-item'>";

        $output .= ($member->MEMBER_introduction !== '') ? "<div class='member introductionimg'><img width='16' height='16' data-src='css/images/icons/favorite.png'/></div>" : '';
        $output .= "<a class='colorbox_member' title='$member_fullname' data-id='$memberid'>
              <img width='$size' height='$size' title='$member_fullname' data-src='$location_small'/>
            </a>
          </div>";
      }
    }

    $output .= "";
    $contentPlaceholders = new stdClass;
    $contentPlaceholders->PHOTOBOOK = $output;
    $PHOTOBOOKtemplate = new TemplateParser("PHOTOBOOK", $contentPlaceholders, $this->database);

    print_r($PHOTOBOOKtemplate->parseOutput());
  }

}

?>