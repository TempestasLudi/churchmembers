<?php

/**
 * This processor handles a request for searching a specified address, used in the livesearch box.
 */
class LivesearchProcessor extends AbstractProcessor {

  /**
   * Maximum number of addresses returned from database
   */
  private $MAXIMUM = 30;

  public function processRequest() {

    $input = strtolower($_REQUEST["term"]);
    $table = $_REQUEST["type"];

    $result = array();

    switch ($table) {
      case "both" :
      case "addresses":
        $addresses = $this->database->getAddressesByAddressinfo($input, $this->MAXIMUM);

        if (count($addresses) > 0) {
          foreach ($addresses as $address) {
            $ADR_id = $address->ADR_id;
            $ADR_Familyname = $address->ADR_familyname;
            $ADR_Familyname_preposition = $address->ADR_familyname_preposition;
            $ADR_Street = $address->ADR_street;
            $ADR_number = $address->ADR_number;

            $start = $this->familynameStart($address);

            $fullname = $start . ' ' . htmlspecialchars($this->database->generateFullMemberName($address, false, true, '', false, true), ENT_QUOTES, 'UTF-8');

            array_push($result, array("ADR_id" => $ADR_id, "MEMBER_id" => false, "label" => $fullname . " (" . $ADR_Street . " " . $ADR_number . ")", "value" => $ADR_Familyname));

            if (count($result) > $this->MAXIMUM) {
              break;
            }
          }
        }
        if ($table === 'addresses')
          break;

      case "both" :
      case "members" :
        $addresses = $this->database->getMembersByName($input, $this->MAXIMUM);

        if (count($addresses) > 0) {
          foreach ($addresses as $address) {
            $ADR_id = $address->ADR_id;
            $MEMBER_id = $address->MEMBER_id;
            $MEMBER_firstname = $address->MEMBER_firstname;
            $MEMBER_initials = $address->MEMBER_initials;
            $MEMBER_familyname = $address->MEMBER_familyname;

            $fullname = $this->database->generateFullMemberName($address, false, true);
            array_push($result, array("ADR_id" => $ADR_id, "MEMBER_id" => $MEMBER_id, "label" => $fullname, "value" => $fullname));

            if (count($result) > $this->MAXIMUM) {
              break;
            }
          }
        }

        break;

      case "email" :
        $addresses = $this->database->getMembersByName($input, $this->MAXIMUM);

        if (count($addresses) > 0) {
          foreach ($addresses as $address) {
            $ADR_id = $address->ADR_id;
            $MEMBER_id = $address->MEMBER_id;
            $MEMBER_firstname = $address->MEMBER_firstname;
            $MEMBER_initials = $address->MEMBER_initials;
            $MEMBER_familyname = $address->MEMBER_familyname;

            $fullname = $this->database->generateFullMemberName($address, false, true);
            if ($address->MEMBER_email !== ''){
              array_push($result, array("ADR_id" => $ADR_id, "MEMBER_id" => $MEMBER_id, "label" => $fullname, "value" => $fullname));
            }

            if (count($result) > $this->MAXIMUM) {
              break;
            }
          }
        }

        break;
    }

    if (count($result) > 0) {
      echo json_encode($result);
    } else {
      $result = array(__("No results"));
      echo json_encode($result);
    }
  }

}

?>