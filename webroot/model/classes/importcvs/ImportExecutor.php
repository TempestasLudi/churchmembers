<?php
/**
 * Handles the import of data into the database.
 */
class ImportExecutor {

  private $database;
  private $addresses;
  private $homeless;

  public $countMembers;
  public $countAddresses;

  /**
   * Initializes a new ImportExecutor.
   */
  public function __construct($filePath, $separator, $fields, $offset) {
    $this->database = new Database();
    $this->addresses = array();
    $this->homeless = array();
    $this->countAddresses = 0;
    $this->countMembers = 0;
    $this->readFileData($filePath, $separator, $fields, $offset);
  }

  /**
   * Adds all members from the cvs file as uncorrelated members (they live on their own).
   */
  public function addMembersUncorrelated() {
    foreach($this->addresses as $address) {
      foreach($address->getMembers() as $member) {
        $this->addMemberUncorrelated($member);
      }
    }
    $this->addHomelessMembers();
  }

  /**
   * Adds all members from the cvs file as correlated, members with the same address live together.
   */
  public function addMembersCorrelated() {
    foreach($this->addresses as $address) {
      $addressArray = $address->getAddress();
      $addressId = $this->database->addAddressArray($addressArray);
      if($addressId) {
        $this->countAddresses++;
      }

      $addressKeys = Address::$addressKeys;
      foreach($address->getMembers() as $member) {
        foreach(array_keys($addressKeys) as $key) {
          if(isset($member[$key])) {
            unset($member[$key]);
          }
        }

        $memberId = $this->database->addMemberArray($addressId, $member);
        if($memberId) {
          $this->countMembers++;
        }
      }
    }
    $this->addHomelessMembers();
  }

  /**
   * Reads all lines from the cvs file and puts it into $lines as a 2D array.
   */
  private function readFileData($filePath, $separator, $fields, $offset) {
    if(file_exists($filePath)) {
      $file = file($filePath);
      for($i=$offset; $i<count($file); $i++) {
        $line = rtrim($file[$i]);
        $lineParts = explode($separator, $line);
        $member = array();

        for($j=0; $j<count($lineParts); $j++) {
          $member[$fields[$j]] = $lineParts[$j];
        }
        if(isset($member['ADR_street']) && isset($member['ADR_number'])) {
          $address = $member['ADR_street'].' '.$member['ADR_number'];
          if(!isset($this->addresses[$address])) {
            $this->addresses[$address] = new Address();
          }
          $this->addresses[$address]->addMember($member);
        } else {
          array_push($this->homeless, $member);
        }
      }
    }
  }

  /**
   * Adds the member with its own address.
   */
  private function addMemberUncorrelated($member) {
    $address = Address::$addressKeys;
    $address['ADR_familyname'] = $member['MEMBER_familyname'];
    foreach(array_keys($address) as $key) {
      if(isset($member[$key])) {
        $address[$key] = $member[$key];
        unset($member[$key]);
      }
    }

    $addressId = $this->database->addAddressArray($address);
    if($addressId) {
      $this->countAddresses++;
    }

    $memberId = $this->database->addMemberArray($addressId, $member);
    if($memberId) {
      $this->countMembers++;
    }
  }

  /**
   * Adds the homeless ones to the database.
   */
  private function addHomelessMembers() {
    foreach($this->homeless as $member) {
      $this->addMemberUncorrelated($member);
    }
  }
}
?>