<?php

/**
 * This processor handles a request for generating the advanced search table
 */
class GetMapMarkersProcessor extends AbstractProcessor {

  public function processRequest() {

    $allmembers = $this->database->getAddressGroupsWithMembers();

    $output = array();
    $location = array();

    $i = 0;
    $oldaddressid = "";
    $oldaddresslat = "";
    $oldaddresslng = "";
    foreach ($allmembers as $ADR_id => $ADDRESS) {

      if (($ADDRESS->ADDRESS->ADR_lat !==  '0.0000000') && ($ADDRESS->ADDRESS->ADR_lng !== '0.0000000')) {

        $members = array();
        foreach ($ADDRESS->MEMBERS as $MEMBER_id => $MEMBER) {
          $photo = ($MEMBER->MEMBER_photo) ? $MEMBER->MEMBER_photo : "css/images/users/user_unknown.png";
          $member = array(
              "id" => $MEMBER->MEMBER_id,
              "name" => $this->database->generateFullMemberName($MEMBER, false),
              "photo" => $photo,
              "church" => ""
          );
          array_push($members, $member);
        }

        $address = array(
            "id" => $ADDRESS->ADDRESS->ADR_id,
            "name" => (($ADDRESS->ADDRESS->ADR_familyname_preposition) ? ucfirst($ADDRESS->ADDRESS->ADR_familyname_preposition) . ' ' . $ADDRESS->ADDRESS->ADR_familyname : $ADDRESS->ADDRESS->ADR_familyname),
            "street" => $ADDRESS->ADDRESS->ADR_street,
            "number" => $ADDRESS->ADDRESS->ADR_number,
            "zip" => $ADDRESS->ADDRESS->ADR_zip,
            "city" => $ADDRESS->ADDRESS->ADR_city,
            "phone" => $ADDRESS->ADDRESS->ADR_telephone,
            "members" => $members
        );

        $i++;

        //MOBILE OR DESKTOP OUTPUT
        if (isset($_SESSION['SESSION-INFO']['MOBILE']) && $_SESSION['SESSION-INFO']['MOBILE'] === true) {
          $icon = ($ADDRESS->ADDRESS->GROUP_marker !== '') ? '../' . $ADDRESS->ADDRESS->GROUP_marker : '../css/images/googlemaps/marker1.png';
        } else {
          $icon = ($ADDRESS->ADDRESS->GROUP_marker !== '') ? $ADDRESS->ADDRESS->GROUP_marker : 'css/images/googlemaps/marker1.png';
        }


        $location = array(
            'lat' => ((float) $ADDRESS->ADDRESS->ADR_lat),
            'lng' => ((float) $ADDRESS->ADDRESS->ADR_lng),
            'data' => array(
                'address' => $address
            ),
            'options' => array(
                'icon' => $icon
            )
        );

        if ($i > 0)
          array_push($output, $location);
      }
    }

    if ($this->database->getSetting('map_externaljson')->SETTINGS_value !== ''){
    $externaldata = array();
    $externalfile = @file_get_contents($this->database->getSetting('map_externaljson')->SETTINGS_value);
    $externaldata = @json_decode($externalfile);

    $output = @array_merge($output, $externaldata);
    }


    header('Content-type: application/json');
    print(json_encode($output));
  }

}

?>