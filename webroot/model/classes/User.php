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

/**
 * Represents a user that has to authenticate to gain access to churchmembers.
 */
class User {

  //********************************* Attributes ***********************************************/
  /**
   * Unique id of this user.
   */
  private $id;

  /**
   * Username of this user.
   */
  private $username;

  /**
   * Authentication level for this user,:
   */
  private $level;

  /**
   * Usertype name for this user,:
   */
  private $usertypename;

  /**
   * Usertype description for this user,:
   */
  private $usertypedescription;

  /**
   * Rights for this user,:
   */
  private $userrights;

  /**
   * Template for this user,:
   */
  private $usertemplate;

  /**
   * User linked to this member (For API purposes):
   * Members are logged on on secondary site, and have
   * privilages of a certain User
   */
  private $userlinktomember;
  private $userlinktomember_fullname;

  //********************************* Constructors *********************************************/

  /**
   * Initializes a new Member object with given parameters.
   * @param object $user
   */
  function  __construct($user) {
    $this->id = $user->USER_id;
    $this->username = $user->USER_username;
    $this->level = $user->USERTYPE_id;
    $this->usertypename = $user->USERTYPE_name;
    $this->usertypedescription = $user->USERTYPE_description;
    $this->userrights = json_decode($user->USERTYPE_rights);
    $this->usertemplate = $user->USERTYPE_template;
    $this->userlinktomember = NULL;
  }

  //********************************* Methods **************************************************/
  /**
   * Returns the member id for this user.
   * @return int
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Returns the username.
   * @return string
   */
  public function getUsername() {
    return $this->username;
  }

  /**
   * Returns the level for this user:
   * @return int
   */
  public function getLevel() {
    return $this->level;
  }

  /**
   * Returns the usertype name for this user:
   * @return string
   */
  public function getUsertypename() {
    return $this->usertypename;
  }

  /**
   * Returns the usertype description for this user:
   * @return string
   */
  public function getUsertypedescription() {
    return $this->usertypedescription;
  }


  /**
   * Returns the rights for this user:
   * @return array
   */
  public function getUserrights() {
    return $this->userrights;
  }


  /**
   * Checks if the user has permission for a certain action. Returns true if the user has the rights
   * @param string $action
   * @return bool
   */
  public function checkUserrights($action) {
    if (array_key_exists(($action),$this->userrights)) {
      if ($this->userrights->$action === 1) return true;
    }

    return false;
  }

  /**
   * Returns the template for the user:
   * @return string
   */
  public function getUsertemplate() {
    return $this->usertemplate;
  }

  /**
   * Set member which is linked to user. User can edit this member:
   * @param object $member
   * @return object|bool
   */
  public function setUserlinktomember($member, $fullname) {
    if (is_object($member)) {
      $this->userlinktomember = $member;
    }

    $this->setUserlinktomemberFullname($fullname);

    return $this->userlinktomember;
  }

  /**
   * Returns the linked address_id:
   * @return int
   */
  public function getUserlinktomember() {
    if (isset($this->userlinktomember)){
      return $this->userlinktomember;
        } else {
      return false;
    }
  }

  /**
   * Returns the linked member_id:
   * @return int
   */
  public function getUserlinktomemberId() {
    if (isset($this->userlinktomember->MEMBER_id)){
       return $this->userlinktomember->MEMBER_id;
    } else {
      return false;
    }
  }

   /**
   * Returns the linked address_id:
   * @return int
   */
  public function getUserlinktomemberAdr_Id() {
    if (isset($this->userlinktomember->ADR_id)){
      return $this->userlinktomember->ADR_id;
        } else {
      return false;
    }
  }
      /**
   * Returns the linked member fullname:
   * @return string
   */
  public function setUserlinktomemberFullname($fullname) {
    return $this->userlinktomember_fullname = $fullname;
  }
    /**
   * Returns the linked member fullname:
   * @return string
   */
  public function getUserlinktomemberFullname() {
    if (isset($this->userlinktomember_fullname)){
       return $this->userlinktomember_fullname;
    } else {
      return false;
    }
  }
}
?>