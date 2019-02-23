<?php

/**
 * An Address is a collection of members from the cvs file with the same address. As for now an address is represented
 * as a street / number pair.
 */
class Address {
  
//********************************* Attributes ***********************************************/
  private $members;
  public static $addressKeys = array('ADR_familyname' => '',
          'ADR_street' => '',
          'ADR_number' => '',
          'ADR_zip' => '',
          'ADR_city' => '',
          'ADR_telephone' => '',
          'ADR_email' => '');

//********************************* Constructors *********************************************/
  /**
   * Initializes a new Address.
   */
  public function __construct() {
    $this->members = array();
  }

//********************************* Methods **************************************************/
  /**
   * Ads a member to this Address.
   */
  public function addMember($member) {
    array_push($this->members, $member);
  }

  /**
   * Returns all members of this address.
   */
  public function getMembers() {
    return $this->members;
  }

  /**
   * Returns all addres info based on the joint information of the members.
   */
  public function getAddress() {
    $address = self::$addressKeys;
    foreach(array_keys($address) as $key) {
      foreach($this->members as $member) {
        if(!$address['ADR_familyname']) {
          $address['ADR_familyname'] = $member['MEMBER_familyname'];
        }
        if(!$address[$key] && isset($member[$key])) {
          $address[$key] = $member[$key];
        }
      }
    }
    return $address;
  }
}
?>