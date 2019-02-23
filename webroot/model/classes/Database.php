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
require_once CONFIG_PATH . 'config.php';

/**
 * Represents a database, several functions can be called to get desired objects.
 */
class Database extends mysqli {

  /**
   * Initializes a new Database, sets up a connection.
   */
  public function __construct() {

    if (isset($_SESSION['USER']) && (is_a($_SESSION['USER'], 'User'))) {
      if (($_SESSION['USER']->getLevel()) === 999) {
        @parent::__construct(DB_HOST, DB_CORE_USERNAME, DB_CORE_PASSWORD, DB_DATABASE);
      } else {

        @parent::__construct(DB_HOST, DB_ADMIN_USERNAME, DB_ADMIN_PASSWORD, DB_DATABASE);
      }
    } else {
      // @: suppresses connection errors. Suppression is needed for not showing the DB_username or DB_DATABASE if there is a connection error.
      @parent::__construct(DB_HOST, DB_CORE_USERNAME, DB_CORE_PASSWORD, DB_DATABASE);
    }

    if (mysqli_connect_error()) {
      print(__("Database error: Cannot connect to database"));
      trigger_error(serialize(array("errtype" => "E_DB_CONNECTIONFAIL", "errno" => mysqli_connect_errno(), "error" => mysqli_connect_error())), E_USER_ERROR);
    }

    $this->executeQuery("SET NAMES 'utf8'");

    if (((!isset($this->tables)) || (!isset($this->tablesreplacement))) && (TB_PREFIX !== '')) {
      $tablelist = array();
      $tablesreplacement = array();
      $tables = array('addresses', 'failedaccess', 'groupmembers', 'groups', 'members', 'membertypes', 'events', 'eventtypes', 'settings', 'users', 'usertypes');
      foreach ($tables as $table) {
        array_push($tablelist, "/`$table`/");
        array_push($tablesreplacement, "`" . TB_PREFIX . $table . "`");
      }
      $this->tables = $tablelist;
      $this->tablesreplacement = $tablesreplacement;
    }
  }

  /**
   * Returns a user from the database as an object with membernames from table column-names.
   * @param string $username
   * @param string $password
   * @return object
   */
  public function getOneUserByName($username, $password) {
    $query = 'SELECT `USER_id`, `USER_username`, `users`.`USERTYPE_id`, `USERTYPE_name`, `USERTYPE_description`, `USERTYPE_rights`, `USERTYPE_template`
		FROM `users`
		LEFT JOIN `usertypes` ON `users`.`USERTYPE_id` = `usertypes`.`USERTYPE_id`
		WHERE `USER_username`= ? AND AES_DECRYPT(`USER_password`, ?) = ?';

    $users = $this->requestDataQuery($query, array('sss', $username, AES_KEY, $password));

    if (count($users) > 1) {
      die(__("Database error: multiple users with the same username."));
    } elseif (count($users) > 0) {
      return $users[0];
    } else {
      return false;
    }
  }

  /**
   * Returns a user from the database as an object with membernames from table column-names.
   * @param string $username
   * @return object
   */
  public function getOneUserByNameRemote($username) {
    $query = 'SELECT `USER_id`, `USER_username`, `users`.`USERTYPE_id`, `USERTYPE_name`, `USERTYPE_description`, `USERTYPE_rights`, `USERTYPE_template`
		FROM `users`
		LEFT JOIN `usertypes` ON `users`.`USERTYPE_id` = `usertypes`.`USERTYPE_id`
		WHERE `USER_username`= ?';

    $users = $this->requestDataQuery($query, array('s', $username));

    if (count($users) > 1) {
      die(__("Database error: multiple users with the same username."));
    } elseif (count($users) > 0) {
      return $users[0];
    } else {
      return false;
    }
  }

  /**
   * Deletes all groups for a certain member.
   * @param int $memberId
   * @return int
   */
  public function deleteGroupsForMember($memberId) {
    $query = 'DELETE FROM `groupmembers` WHERE `GROUPMEMBERS_memberid` = ?';
    return $this->changeDataQuery($query, array('i', $memberId));
  }

  /**
   * Deletes all groups for a certain address.
   * @param int $addressId
   * @return int
   */
  public function deleteGroupsForAddress($addressId) {
    $query = 'DELETE FROM `groupaddresses` WHERE `GROUPADDRESSES_addressid` = ?';
    return $this->changeDataQuery($query, array('i', $addressId));
  }

  /**
   * Deletes all empty addresses from database and returns the number of deleted ones.
   * @return int
   */
  public function deleteEmptyAddresses() {
    $query = 'DELETE `addresses`.* FROM `addresses`
      LEFT JOIN `members` ON `addresses`.`ADR_id` = `members`.`ADR_id`
      WHERE `members`.`ADR_id` IS NULL
      AND `addresses`.`ADR_id` != 0';
    return $this->changeDataQuery($query);
  }

  /**
   * Deletes all members without an address.
   * @return int
   */
  public function deleteOrphanMembers() {
    $query = 'DELETE `members`.* FROM `members`
       LEFT JOIN `addresses` ON `members`.`ADR_id` = `addresses`.`ADR_id`
       WHERE `addresses`.`ADR_id` IS NULL';
    return $this->changeDataQuery($query);
  }

  /**
   * Empties the defined table.
   * @param string $table
   * @return int
   */
  public function emptyTable($table) {
    $query = "DELETE FROM ?";
    if ($table === 'addresses') {
      $query .= "WHERE `addresses`.`ADR_id` != 0;";
    } else {
      $query .= ";";
    }
    return $this->changeDataQuery($query, array('s', $table));
  }

  /**
   * Add a Member to a Group
   *
   * @param int $memberId
   * @param int $groupId
   * @return int
   */
  public function createGroupMember($memberId, $groupId) {
    $query = "INSERT INTO `groupmembers` (
                     `GROUPMEMBERS_id`,
                     `GROUPMEMBERS_groupid`,
                     `GROUPMEMBERS_memberid`)
                     values (NULL, ?, ?);";
    $result = $this->changeDataQuery($query, array('ii', $groupId, $memberId));

    if (($result === -1) || ($result === NULL)) {
      trigger_error(serialize(array("errtype" => "E_USER_FAIL_MEMBER_ADD_GROUPS")), E_USER_ERROR);
    }

    return $result;
  }

  /**
   * Add a Address to a Group
   *
   * @param int $addressId
   * @param int $groupId
   * @return int
   */
  public function createGroupAddress($addressId, $groupId) {
    $query = "INSERT INTO `groupaddresses` (
                     `GROUPADDRESSES_id`,
                     `GROUPADDRESSES_groupid`,
                     `GROUPADDRESSES_addressid`)
                     values (NULL, ?, ?);";
    $result = $this->changeDataQuery($query, array('ii', $groupId, $addressId));

    if (($result === -1) || ($result === NULL)) {
      trigger_error(serialize(array("errtype" => "E_USER_FAIL_MEMBER_ADD_GROUPS")), E_USER_ERROR);
    }

    return $result;
  }

  /**
   * Adds an address to the database with specified data, returns the id of the newly inserted address or -1 when failed.
   * @param string $familyname
   * @param string $familyname_preposition
   * @param string $street
   * @param string $number
   * @param string $zip
   * @param string $city
   * @param string $phone
   * @param string $email
   * @return int
   */
  public function addAddress($familyname, $familyname_preposition, $street, $number, $street_extra, $zip, $city, $country, $phone, $email) {
    $query = "INSERT INTO `addresses` (
                  `ADR_id` ,
                  `ADR_familyname` ,
		  `ADR_familyname_preposition`,
                  `ADR_street` ,
                  `ADR_number` ,
                  `ADR_street_extra`,
                  `ADR_zip` ,
                  `ADR_city`,
                  `ADR_country`,
                  `ADR_telephone`,
                  `ADR_email`,
		  `ADR_lat`,
		  `ADR_lng`)
                  VALUES (NULL , ? , ? , ? , ? , ?, ? , ? , ? , ? , ? , ?, ?);";

    $coordinates = $this->getLatLon($street, $number, strtoupper($zip), strtoupper($city));

    $result = $this->changeDataQuery($query, array('ssssssssssss', $familyname, $familyname_preposition, $street, $number, $street_extra, strtoupper($zip), strtoupper($city), strtoupper($country), $this->formatPhone($phone), strtolower($email), $coordinates['lat'], $coordinates['lon']));

    if ($result > 0) {
      $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS'] = $this->getAddressById($this->insert_id);
      $_SESSION['CURRENT-VIEW']['MEMBERS_IN_ADDRESS'] = $this->getMembersForAddress($_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id);
      $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER'] = $this->getFirstMemberIdOfAddress($_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id);
    } else {
      trigger_error(serialize(array("errtype" => "E_USER_FAIL_ADD_ADDRESS")), E_USER_ERROR);
    }

    return $result;
  }

  /**
   * Adds an address to the database, tries to parse a SQL query from the provided address. Returns the newly created
   * address id or -1 if failed.
   *
   * TODO: rewrite SQL query
   *
   * @param array $address
   * @return int
   */
  public function addAddressArray($address) {
    if (isset($address['ADR_zip'])) {
      $address['ADR_zip'] = strtoupper($address['ADR_zip']);
    }
    if (isset($address['ADR_city'])) {
      $address['ADR_city'] = strtoupper($address['ADR_city']);
    }
    if (isset($address['ADR_city']) && isset($address['ADR_zip']) && isset($address['ADR_number']) && isset($address['ADR_street'])) {
      $coordinates = $this->getLatLon($address['ADR_street'], $address['ADR_number'], $address['ADR_zip'], $address['ADR_city']);
      $address['ADR_lat'] = $coordinates['lat'];
      $address['ADR_lng'] = $coordinates['lon'];
    }

    $keys = array_keys($address);
    $values = array_values($address);
    $queryColumns = '';
    $queryValues = '';
    $typeString = '';

    for ($i = 0; $i < count($address); $i++) {
      $queryColumns .= '`' . $keys[$i] . '`, ';
      $queryValues .= '?, ';
      $typeString .= 's';
    }
    $queryColumns = rtrim($queryColumns, ', ');
    $queryValues = rtrim($queryValues, ', ');


    $query = 'INSERT INTO `addresses` (' . $queryColumns . ') VALUES (' . $queryValues . ');';
    array_unshift($values, $typeString);

    $this->changeDataQuery($query, $values);

    return $this->insert_id;
  }

  /**
   * Adds a new group to the database with specified info, returns the id of the newly inserted address or -1 when failed.
   * @param string $name
   * @param int $parentid
   * @return int
   */
  public function addGroup($name, $parentid = 1) {
    $query = "INSERT INTO `groups` (
                        `GROUP_id` ,
                        `GROUP_name` ,
                        `GROUP_parent_id`,
                        `GROUP_type`)
                        VALUES  (NULL , ?, ?, 'members');";

    $result = $this->changeDataQuery($query, array('ss', $name, $parentid));

    if ($result > 0) {
      $_SESSION['CURRENT-VIEW']['CURRENT_GROUP'] = $this->getGroupById($this->insert_id);
      $_SESSION['CURRENT-VIEW']['MEMBERS_IN_GROUP'] = $this->getMembersForGroup($_SESSION['CURRENT-VIEW']['CURRENT_GROUP']->GROUP_id);
      $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER'] = $this->getFirstMemberIdOfGroup($_SESSION['CURRENT-VIEW']['CURRENT_GROUP']->GROUP_id);
    } else {
      trigger_error(serialize(array("errtype" => "E_USER_FAIL_ADD_GROUP")), E_USER_ERROR);
    }

    return $result;
  }

  /**
   * Adds a member to the database, tries to parse a SQL query from the provided member. Returns the newly created member
   * id or -1 if failed.
   * @param int $addressId
   * @param array $member
   * @return int
   */
  public function addMemberArray($addressId, $member) {
    $keys = array_keys($member);
    $values = array_values($member);
    $queryColumns = '';
    $queryValues = '';

    for ($i = 0; $i < count($member); $i++) {
      $queryColumns .= '`' . $keys[$i] . '`, ';
      $queryValues .= '"' . $values[$i] . '", ';
    }

    $query = 'INSERT INTO `members` (' . $queryColumns . '`ADR_id`) VALUES (' . $queryValues . ' "' . $addressId . '");';
    $result = $this->changeDataQuery($query);
    return $this->insert_id;
  }

  /**
   * Adds a member to the database with specified info, returns bool.
   * @param string $firstName
   * @param string $christianName
   * @param string $initials
   * @param string $familyName
   * @param string $familyname_preposition
   * @param string $birthDate
   * @param string $birthplace
   * @param string $gender
   * @param string $mobile
   * @param string $email
   * @param string $parent
   * @param int $membertype_id
   * @return bool
   */
  public function addMember($firstName, $christianName, $initials, $familyName, $familyname_preposition, $birthDate, $birthplace, $gender, $mobile, $email, $parent, $membertype_id) {

    $birthDate = $this->setDate($birthDate);

    $query = "INSERT INTO `members` (
                     `MEMBER_id` ,
                     `MEMBER_familyname` ,
										 `MEMBER_familyname_preposition`,
                     `MEMBER_initials` ,
                     `MEMBER_firstname` ,
                     `MEMBER_christianname` ,
                     `MEMBER_gender` ,
                     `MEMBER_mobilephone` ,
                     `MEMBER_email` ,
                     `MEMBER_birthdate` ,
                     `MEMBER_birthplace` ,
										 `MEMBER_membertype_id` ,
                     `ADR_id` ,
                     `MEMBER_parent`
                     )
                     VALUES (
                     NULL , ? , ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , ?
                     );";

    $result = $this->changeDataQuery($query, array('ssssssssssiii', $familyName, $familyname_preposition, trim(strtoupper($initials)), $firstName, $christianName, $gender, $this->formatPhone($mobile), strtolower($email), $birthDate, strtoupper($birthplace), $membertype_id, $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id, $parent));

    if ($result > 0) {
      $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER'] = $this->getMemberById($this->insert_id);
      $_SESSION['CURRENT-VIEW']['MEMBERS_IN_ADDRESS'] = $this->getMembersForAddress($_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id);
    } else {
      trigger_error(serialize(array("errtype" => "E_USER_FAIL_ADD_MEMBER")), E_USER_ERROR);
      return false;
    }

    return true;
  }

  /**
   * Adds a new event to the database with specified info.
   * @param string $eventType
   * @param int $partnerId
   * @param string $date
   * @param string $note
   * @param int $memberid
   * @return int
   */
  public function addEvent($eventType, $parentid = 0, $date, $note, $memberid = NULL, $addressid = NULL) {

    if ($eventType === "EVENT_NOREASON") {
      return true;
    }

    if ($memberid === NULL)
      $memberid = $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id;

    if ($addressid === NULL)
      $addressid = $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id;

    $date = $this->setDate($date);

    $query = "INSERT INTO `events` (
                       `EVENT_id` ,
                       `EVENT_parent_id` ,
                       `EVENT_MEMBER_id` ,
                       `EVENT_MEMBER_adr_id` ,
                       `EVENTTYPE_id` ,
                       `EVENT_date` ,
                       `EVENT_note`)
                       VALUES ('' , ?, ?, ?, ?, ?, ?);";

    $eventType_Id = $_SESSION['EVENTTYPES']->$eventType->EVENTTYPE_id;

    $result = $this->changeDataQuery($query, array('iiisss', $parentid, $memberid, $addressid, $eventType_Id, $date, $note));

    if ($result > 0) {
      $_SESSION['CURRENT-VIEW']['EVENTS_MEMBER'] = $this->getEventsForMember($memberid);
      return $this->last_insert_id;
    } else {
      trigger_error(serialize(array("errtype" => "E_USER_FAIL_ADD_EVENT")), E_USER_ERROR);
    }

    return $result;
  }

  /**
   * Sets the fullname of a member in the eventstable
   * Needed if a member is deleted from the system
   *
   * @param int $memberid
   * @return boolean
   */
  public function updateEventsMember($member, $updateadr = false) {

    $fullname = ($updateadr) ? '' : $this->generateFullMemberName($member, false, true);

    $query = "UPDATE `events` SET `EVENT_MEMBER_fullname` = ?, `EVENT_MEMBER_adr_id` = ? WHERE `events`.`EVENT_MEMBER_id` = ?;";
    $this->changeDataQuery($query, array('sii', $fullname, $member->ADR_id, $member->MEMBER_id));

    return true;
  }

  /**
   * Sets the last address of a member in the eventstable
   * Needed if an address is deleted from the system
   *
   * @param int $memberid
   * @return boolean
   */
  public function updateEventsAddress($address) {

    // Extra address information
    if ($address->ADR_street_extra !== '') {
      $rows = explode(',', $address->ADR_street_extra);
      foreach ($rows as $row) {
        $fulladdress .= htmlspecialchars($row, ENT_QUOTES, 'UTF-8') . '<br />';
      }
    }

    // Street
    $fulladdress .= $address->ADR_street . '&nbsp;';
    $fulladdress .= $address->ADR_number . '<br />';
    $fulladdress .= $address->ADR_zip . '&nbsp;&nbsp;';
    $fulladdress .= ucwords(strtolower($address->ADR_city));
    $fulladdress .= ($address->ADR_country !== '') ? '<br />' . ucwords(strtolower($address->ADR_country)) : '';

    $query = "UPDATE `events` SET `EVENT_MEMBER_address` =  ? WHERE `events`.`EVENT_MEMBER_adr_id` = ?;";
    $this->changeDataQuery($query, array('si', $fulladdress, $address->ADR_id));

    return true;
  }

  /**
   * Modifies data in the database without verifying the field values. It returns the result of the query.
   * @param int $refId
   * @param string $refField
   * @param string $table
   * @param string $field
   * @param string|int|bool $value
   * @return int|bool
   */
  public function editDataNoVerify($refId, $refField, $table, $field, $value) {
    $refField = mysqli_real_escape_string($this, $refField);
    $table = mysqli_real_escape_string($this, $table);
    $field = mysqli_real_escape_string($this, $field);

    // If Email or Phone is changed, log it
    if (($field === "MEMBER_mobilephone") || ($field === "MEMBER_email")) {
      $eventtype = ($field === "MEMBER_mobilephone") ? "EVENT_CHANGED_PHONE" : "EVENT_CHANGED_EMAIL";
      $addEventResult = $this->addEvent($eventtype, 0, $this->setDate(date("Y-m-d", time())), "");

      if ($addEventResult <= 0) {
        trigger_error(serialize(array("errtype" => "E_USER_FAIL_ADD_EVENT")), E_USER_ERROR);
      }
    }

    // Update Field
    $query = "UPDATE `$table` SET `$field` = ? WHERE `$table`.`$refField` = ? LIMIT 1;";

    if (is_int($value)) {
      return $this->changeDataQuery($query, array('ii', $value, $refId));
    } elseif (is_string($value)) {
      return $this->changeDataQuery($query, array('si', $value, $refId));
    } else {
      trigger_error(serialize(array("errtype" => "E_USER_FAILPREPAREQUERY", "field" => $field)), E_USER_ERROR);
      return false;
    }
  }

  /**
   * Modifies data in the database verifying field according to date format and true or false.
   * @param int $refId
   * @param string $refField
   * @param string $table
   * @param string $field
   * @param string|int|bool $value
   * @return int|bool
   */
  public function editDataVerify($refId, $refField, $table, $field, $value) {
    $refField = mysqli_real_escape_string($this, $refField);
    $table = mysqli_real_escape_string($this, $table);
    $field = mysqli_real_escape_string($this, $field);
    $value = mysqli_real_escape_string($this, $value);

    switch ($field) {
      case "ADR_zip":
      case "ADR_city":
      case "ADR_country":
      case "MEMBER_initials":
      case "MEMBER_birthplace":
      case "MEMBER_baptismcity":
      case "MEMBER_confessioncity":
      case "MEMBER_mariagecity":
        $value = strtoupper($value);
        break;

      case "MEMBER_email":
      case "MEMBER_business_email":
      case "ADR_email":
        $value = strtolower($value);
        break;

      case "MEMBER_mobilephone":
      case "MEMBER_business_phone":
      case "ADR_telephone":
        $value = ($value !== '' ) ? $this->formatPhone($value) : '';
        break;

      case "MEMBER_birthdate":
      case "MEMBER_baptismdate":
      case "MEMBER_confessiondate":
      case "MEMBER_mariagedate":
      case "EVENT_date":
      case "HISTORY_date":
        $value = ($value !== '' ) ? $this->setDate($value) : NULL;
        break;

      case "MEMBER_parent":
      case "MEMBER_inyearbook":
      case "GROUP_allowusers":
      case "GROUP_inyearbook":
      case "GROUP_onmap":
        $value = ($value === 'true') ? 1 : 0;
        break;

      case "SETTINGS_value":
        switch ($refId) {
          case 'export_docraptor_enabled':
          case 'locale_officecode_visible':
          case 'system_secure':
          case 'auth_enabled':
          case 'maintenance':
          case 'login_mail':
          case 'mail_use':
            $value = ($value === 'true') ? 'true' : 'false';
            break;
        }

        break;
    }

    // If Email or Phone is changed, log it
    if (($field === "MEMBER_mobilephone") || ($field === "MEMBER_business_phone") || ($field === "MEMBER_email") || ($field == "MEMBER_business_email")) {
      switch ($field) {
        case "MEMBER_mobilephone":
          $eventtype = "EVENT_CHANGED_PHONE";
          break;

        case "MEMBER_business_phone":
          $eventtype = "EVENT_CHANGED_BUSINESS_PHONE";
          break;

        case "MEMBER_email":
          $eventtype = "EVENT_CHANGED_EMAIL";
          break;

        case "MEMBER_business_email":
          $eventtype = "EVENT_CHANGED_BUSINESS_EMAIL";
          break;
      }

      $addEventResult = $this->addEvent($eventtype, 0, $this->setDate(date("Y-m-d", time())), "");

      if ($addEventResult <= 0) {
        trigger_error(serialize(array("errtype" => "E_USER_FAIL_ADD_EVENT")), E_USER_ERROR);
      }
    }

    // Update Field
    $query = "UPDATE `$table` SET `$field` = ? WHERE `$refField` = ?";

    if (is_int($value) || is_bool($value)) {
      $result = $this->changeDataQuery($query, array('is', $value, $refId));
    } elseif ((is_string($value)) || ($value === NULL)) {
      $result = $this->changeDataQuery($query, array('ss', $value, $refId));
    } else {
      trigger_error(serialize(array("errtype" => "E_USER_FAILPREPAREQUERY")), E_USER_ERROR);
      $result = false;
    }

    if (($result === -1) || ($result === NULL)) {
      trigger_error(serialize(array("errtype" => "E_USER_FAILEDITDATA", "field" => $field)), E_USER_ERROR);
    }

    return $result;
  }

  /**
   * Modifies Address data in the database.
   * @param string $familyname
   * @param string $familyname_preposition
   * @param string $street
   * @param string $number
   * @param string $zip
   * @param string $city
   * @param string $phone
   * @param string $email
   * @return int
   */
  public function editDataAddress($familyname, $familyname_preposition, $street, $number, $street_extra, $zip, $city, $country, $phone, $email) {

    $ADR_id = $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id;
    $query = "UPDATE `addresses` SET `ADR_familyname` = ?, `ADR_familyname_preposition` = ?,  `ADR_street` = ?, `ADR_number` = ?, `ADR_street_extra` = ?, `ADR_zip` = ?, `ADR_city` = ?, `ADR_country` = ?, `ADR_telephone` = ?, `ADR_email` = ?, `ADR_lat` = ?, `ADR_lng` = ? WHERE `addresses`.`ADR_id` = ? LIMIT 1;";
    $coordinates = $this->getLatLon($street, $number, $zip, $city);
    $result = $this->changeDataQuery($query, array('ssssssssssssi', $familyname, $familyname_preposition, $street, $number, $street_extra, strtoupper($zip), strtoupper($city), strtoupper($country), $this->formatPhone($phone), strtolower($email), $coordinates['lat'], $coordinates['lon'], $ADR_id));

    if (($result === -1) || ($result === NULL)) {
      trigger_error(serialize(array("errtype" => "E_USER_FAILEDITDATA_ADDRESS")), E_USER_ERROR);
    }

    return $result;
  }

  /**
   * Handles a request for setting the archive status to true.
   * @param string $table
   * @param int $refid
   * @param string $reffield
   * @param string $field
   * @param string|int|bool $value
   * @return true
   */
  public function setArchiveStatus($table, $refid, $reffield, $field, $value) {

    if ($table === 'members') {
      $member = $this->getMemberById($refid);
      if ($member !== NULL) {
        $result = $this->editDataNoVerify($refid, $reffield, $table, $field, $value);
        if ($result > 0) {
          $remain = $this->getMembersForAddress($member->ADR_id, $member->MEMBER_archive);
          if (!count($remain) > 0) {
            $resultarchiveaddress = $this->editDataNoVerify($member->ADR_id, "ADR_id", "addresses", "ADR_archive", $value);
            if ($resultarchiveaddress <= 0) {
              trigger_error(serialize(array("errtype" => "E_USER_FAILARCHIVE_ADDRESS", "ADR_id" => $member->ADR_id)), E_USER_ERROR);
            }
          }
        } else {
          trigger_error(serialize(array("errtype" => "E_USER_FAILARCHIVE_MEMBER", "MEMBER_id" => $member->MEMBER_id)), E_USER_ERROR);
        }
      } else {
        trigger_error(serialize(array("errtype" => "E_USER_FAILARCHIVE_MEMBER", "MEMBER_id" => $member->MEMBER_id)), E_USER_ERROR);
      }
    } else if ($table === 'addresses') {
      $not_archived = $this->getMembersForAddress($refid, !$value);
      foreach ($not_archived as $member) {
        $result = $this->editDataNoVerify($member->MEMBER_id, "MEMBER_id", "members", "MEMBER_archive", $value);
        if (($result <= 0) || ($result === NULL)) {
          trigger_error(serialize(array("errtype" => "E_USER_FAILARCHIVE_MEMBER", "MEMBER_id" => $member->MEMBER_id)), E_USER_ERROR);
        }
      }

      $not_archived = $this->getMembersForAddress($refid, !$value);
      if (count($not_archived) === 0) {
        $result = $this->editDataNoVerify($refid, $reffield, $table, $field, $value);
        if (($result <= 0) || ($result === NULL)) {
          trigger_error(serialize(array("errtype" => "E_USER_FAILARCHIVE_ADDRESS", "ADR_id" => $refid)), E_USER_ERROR);
        }
      }
    }

    return true;
  }

  /**
   * Deletes data from the database without verifying any field values or table value. It returns the result of the query.
   * @param int $refId
   * @param string $refField
   * @param string $table
   * @return int
   */
  public function deleteDataNoVerify($refId, $refField, $table) {
    $refField = mysqli_real_escape_string($this, $refField);
    $table = mysqli_real_escape_string($this, $table);

    $query = "DELETE FROM `$table` WHERE `$table`.`$refField` = ? LIMIT 1;";
    return $this->changeDataQuery($query, array('i', $refId));
  }

  /**
   * Returns the address with the specified id as an object. Returns NULL when no address is found.
   * @param int $id
   * @return object|NULL
   */
  public function getAddressById($id) {
    $query = "SELECT `addresses`.*, `members`.`MEMBER_gender`, COUNT(`members`.`MEMBER_id`) AS _COUNT
                  FROM `addresses`
                  LEFT JOIN `members` ON `addresses`.`ADR_id` =  `members`.`ADR_id`
                  AND (
                    `members`.`MEMBER_archive` = ?
                    OR  `members`.`MEMBER_archive` IS NULL
                  )
                  WHERE `addresses`.`ADR_id` = ? AND `addresses`.`ADR_archive`= ?
                  GROUP BY `addresses`.`ADR_id`
                  ORDER BY `addresses`.`ADR_familyname`, `members`.`MEMBER_parent` DESC, `members`.`MEMBER_rank` ASC, `members`.`MEMBER_initials` ASC  LIMIT 1";

    $result = $this->requestDataQuery($query, array('iii', $_SESSION['ARCHIVE-MODE'], $id, $_SESSION['ARCHIVE-MODE']));

    if ($result) {
      return $result[0];
    }

    return $result;
  }

  /**
   * Returns the previous address as an object. Returns NULL when no address is found.
   * @param int $adr_id
   * @param string $familyname
   * @return object|NULL
   */
  public function getPrevAddressByFamilyname($adr_id, $familyname) {
    $query = "SELECT  `ADR_familyname`, `ADR_id`, CONCAT(`ADR_familyname`, `ADR_id`)
								FROM  `addresses`
								WHERE CONCAT(`ADR_familyname`, `ADR_id`) < CONCAT(?,  ?)
								AND `ADR_archive` = ?
								ORDER BY CONCAT(`ADR_familyname`, `ADR_id` ) DESC
								LIMIT 1;";
    $result = $this->requestDataQuery($query, array('sii', $familyname, $adr_id, $_SESSION['ARCHIVE-MODE']));

    if ($result) {
      return $result[0];
    }

    return $result;
  }

  /**
   * Returns the previous address id. Returns NULL when no address is found.
   * @param int $adr_id
   * @param string $familyname
   * @return object|NULL
   */
  public function getNextAddressByFamilyname($adr_id, $familyname) {
    $query = "SELECT `ADR_familyname`, `ADR_id`, CONCAT(`ADR_familyname`, `ADR_id` )
							FROM  `addresses`
							WHERE CONCAT(`ADR_familyname`, `ADR_id`) > CONCAT(?,  ?)
							AND `ADR_archive` = ?
							ORDER BY CONCAT(`ADR_familyname`, `ADR_id` ) ASC
							LIMIT 1;";
    $result = $this->requestDataQuery($query, array('sii', $familyname, $adr_id, $_SESSION['ARCHIVE-MODE']));


    if ($result) {
      return $result[0];
    }

    return $result;
  }

  /**
   * Returns an array of address objects.
   * @param int $archive
   * @param bool $orderbylocation
   * @return object
   */
  public function getAddressesWithMembers($archive = 0, $orderbylocation = false) {
    $query = "SELECT *,
      DATE_FORMAT(`MEMBER_birthdate`, '%d-%m-%Y') as 'MEMBER_birthdate',
      GROUP_CONCAT(DISTINCT  `MEMBER_GROUPS_TB`.`GROUP_abbreviation` SEPARATOR ', ') as `MEMBER_GROUPS`,
      GROUP_CONCAT(DISTINCT  `ADDRESS_GROUPS_TB`.`GROUP_abbreviation` SEPARATOR ', ') as `ADDRESS_GROUPS`
      FROM `addresses`
      LEFT JOIN `members` ON `addresses`.`ADR_id` = `members`.`ADR_id`
      LEFT JOIN `membertypes` ON `membertypes`.`MEMBERTYPE_id` = `members`.`MEMBER_membertype_id`
      LEFT JOIN `groupmembers` ON `members`.`MEMBER_id` = `groupmembers`.`GROUPMEMBERS_memberid`
      LEFT JOIN `groupaddresses` ON `members`.`ADR_id` = `groupaddresses`.`GROUPADDRESSES_addressid`
      LEFT JOIN `groups` AS `MEMBER_GROUPS_TB` ON (`groupmembers`.`GROUPMEMBERS_groupid` = `MEMBER_GROUPS_TB`.`GROUP_id`) AND `MEMBER_GROUPS_TB`.`GROUP_inyearbook` = 1
      LEFT JOIN `groups` AS `ADDRESS_GROUPS_TB` ON (`groupaddresses`.`GROUPADDRESSES_groupid` = `ADDRESS_GROUPS_TB`.`GROUP_id`) AND `ADDRESS_GROUPS_TB`.`GROUP_inyearbook` = 1
      WHERE `addresses`.`ADR_archive` = ?
      AND `members`.`MEMBER_archive` = ?
      GROUP BY `members`.`MEMBER_id`";

    if ($orderbylocation) {
      $query .= "ORDER BY `addresses`.`ADR_lat`, `addresses`.`ADR_lng`, `addresses`.`ADR_familyname` ASC, `members`.`MEMBER_parent` DESC, `members`.`MEMBER_rank` ASC, `members`.`MEMBER_initials` ASC";
    } else {
      $query .= "ORDER BY `addresses`.`ADR_familyname` ASC, `members`.`MEMBER_parent` DESC, `members`.`MEMBER_rank` ASC, `members`.`MEMBER_initials` ASC";
    }

    $result = $this->requestDataQuery($query, array('ii', $archive, $archive));

    $addresses = new stdClass();

    foreach ($result as $member => $value) {
      $ADR_id = $value->ADR_id;
      $MEMBER_id = $value->MEMBER_id;

      if (!isset($addresses->$ADR_id)) {
        $addresses->$ADR_id = new stdClass();
        $addresses->$ADR_id->ADDRESS = $value;
      }

      if (!isset($addresses->$ADR_id->MEMBERS)) {
        $addresses->$ADR_id->MEMBERS = new stdClass();
      }

      if (!isset($addresses->$ADR_id->MEMBERS->$MEMBER_id)) {
        $addresses->$ADR_id->MEMBERS->$MEMBER_id = new stdClass();
      }

      $addresses->$ADR_id->MEMBERS->$MEMBER_id = $value;
    }

    return $addresses;
  }

  public function getAddressGroupsWithMembers() {
    $query = "SELECT * FROM `groups`
LEFT JOIN `groupaddresses` ON `groups`.`GROUP_id` = `groupaddresses`.`GROUPADDRESSES_groupid`
LEFT JOIN `addresses` ON `groupaddresses`.`GROUPADDRESSES_addressid` = `addresses`.`ADR_id`
LEFT JOIN `members` ON `groupaddresses`.`GROUPADDRESSES_addressid` =  `members`.`ADR_id`
WHERE `GROUP_onmap` = 1
GROUP BY `members`.`MEMBER_id`
HAVING `addresses`.`ADR_archive` = ?
AND `members`.`MEMBER_archive` = ?
ORDER BY `addresses`.`ADR_familyname` ASC, `members`.`MEMBER_parent` DESC, `members`.`MEMBER_rank` ASC, `members`.`MEMBER_initials` ASC";

    $result = $this->requestDataQuery($query, array('ii', $_SESSION['ARCHIVE-MODE'], $_SESSION['ARCHIVE-MODE']));

    $addresses = new stdClass();

    foreach ($result as $member => $value) {
      $ADR_id = $value->ADR_id;
      $MEMBER_id = $value->MEMBER_id;

      if (!isset($addresses->$ADR_id)) {
        $addresses->$ADR_id = new stdClass();
        $addresses->$ADR_id->ADDRESS = $value;
      }

      if (!isset($addresses->$ADR_id->MEMBERS)) {
        $addresses->$ADR_id->MEMBERS = new stdClass();
      }

      if (!isset($addresses->$ADR_id->MEMBERS->$MEMBER_id)) {
        $addresses->$ADR_id->MEMBERS->$MEMBER_id = new stdClass();
      }

      $addresses->$ADR_id->MEMBERS->$MEMBER_id = $value;
    }

    return $addresses;
  }

  /**
   * Returns an object with addresses with matching streetname/zipcode as specified.
   * Maximum is limitited to specified number. If no addresses are found NULL is returned.
   * @param string $search
   * @param int $limit
   * @return object
   */
  public function getAddressesByAddressinfo($search, $limit) {
    $query = "SELECT `addresses`.*, COUNT(`members`.`MEMBER_id`) AS `_COUNT`
              FROM `addresses`
              LEFT JOIN `members` ON `addresses`.`ADR_id` =  `members`.`ADR_id`
              AND (
                `members`.`MEMBER_archive` = ?
                OR  `members`.`MEMBER_archive` IS NULL
              )
              WHERE (
                     LOWER(CONCAT(`ADR_street`, ' ', `ADR_number`)) LIKE LOWER(?)
                     OR LOWER(REPLACE(`ADR_zip`, ' ', '')) LIKE LOWER(REPLACE(?, ' ', ''))
                     OR REPLACE(`ADR_telephone`, ' ', '') LIKE REPLACE(?, ' ', '')
                     OR LOWER(REPLACE(CONCAT(`ADR_familyname_preposition`, `ADR_familyname`), ' ', '')) LIKE LOWER(REPLACE(?, ' ', ''))
              )
              AND `ADR_archive` = ?
              GROUP BY `addresses`.`ADR_id`
              ORDER BY `addresses`.`ADR_familyname`, `members`.`MEMBER_parent` DESC, `members`.`MEMBER_rank` ASC, `members`.`MEMBER_initials` ASC
              LIMIT ?";
    $result = $this->requestDataQuery($query, array('issssii', $_SESSION['ARCHIVE-MODE'], $search . "%", $search . "%", $search . "%", "%" . $search . "%", $_SESSION['ARCHIVE-MODE'], $limit));

    return $result;
  }

  /**
   * Returns an address based on streetname and number or NULL if no address is found.
   * @param string $streetname
   * @param string $number
   * @return object
   */
  public function getAddress($streetname, $number) {
    $query = "SELECT * FROM `addresses`
                      WHERE LOWER(`ADR_street`) LIKE LOWER(?)
                      AND `ADR_number` LIKE ?
                      AND `ADR_archive` = ?";
    $result = $this->requestDataQuery($query, array('ssi', $streetname . "%", $number . "%", $_SESSION['ARCHIVE-MODE']));

    return $result;
  }

  /**
   * Returns the id of the first address or NULL if no address is found.
   * @return int|NULL
   */
  public function getFirstIdOfAddress() {
    $query = "SELECT `ADR_id` FROM `addresses` WHERE `ADR_archive` = ? ORDER BY `ADR_familyname` ASC, `ADR_id` LIMIT 1;";
    $result = $this->requestDataQuery($query, array('i', $_SESSION['ARCHIVE-MODE']));

    if ($result) {
      return $result[0]->ADR_id;
    }

    return $result;
  }

  /**
   * Returns the id of the first group or NULL if no group is found.
   * @return int|NULL
   */
  public function getFirstIdOfGroups() {

    $query = "SELECT `GROUP_id` FROM `groups` WHERE `GROUP_parent_id` = 1 ORDER BY `GROUP_id` ASC LIMIT 1;";
    $result = $this->requestDataQuery($query);

    if ($result) {
      return $result[0]->GROUP_id;
    }

    return $result;
  }

  /**
   * Returns the group with specified id as an object. Returns NULL in case of failure.
   * @param int $id
   * @return int|NULL
   */
  public function getGroupById($id) {
    $query = "SELECT * FROM `groups` WHERE `GROUP_id` = ? LIMIT 1";
    $result = $this->requestDataQuery($query, array('i', $id));

    if ($result) {
      return $result[0];
    }

    return $result;
  }

  /**
   * Returns all groups. Returns NULL in case of failure.
   * @return int|NULL
   */
  public function getGroups() {
    $query = "SELECT * FROM `groups`";
    $result = $this->requestDataQuery($query);
    return $result;
  }

  /**
   * Returns the member with specified id and archive to true or false as an object. Returns NULL when member is not found.
   * @param int $id
   * @return object|NULL
   */
  public function getMemberById($id, $includearchive = false) {

    $query = "SELECT *,
                        DATE_FORMAT(`MEMBER_birthdate`, '%d-%m-%Y') as 'MEMBER_birthdate' ,
                        DATE_FORMAT(`MEMBER_confessiondate`, '%d-%m-%Y') as 'MEMBER_confessiondate' ,
                        DATE_FORMAT(`MEMBER_baptismdate`, '%d-%m-%Y') as 'MEMBER_baptismdate' ,
                        DATE_FORMAT(`MEMBER_mariagedate`, '%d-%m-%Y') as 'MEMBER_mariagedate'
                        FROM `members`
                        LEFT JOIN `membertypes` ON `membertypes`.`MEMBERTYPE_id` = `members`.`MEMBER_membertype_id`
			LEFT JOIN `addresses` ON `members`.`ADR_id` = `addresses`.`ADR_id`
                        WHERE `members`.`MEMBER_id` = ? ";

    if ($includearchive === false) {
      $query .= "AND `members`.`MEMBER_archive` = ? LIMIT 1";
      $result = $this->requestDataQuery($query, array('ii', $id, $_SESSION['ARCHIVE-MODE']));
    } else {
      $result = $this->requestDataQuery($query, array('i', $id));
    }

    if ($result) {
      return $result[0];
    }

    return $result;
  }

  /**
   * Returns all members as an object. Returns NULL when no members are found.
   * @param int $archive
   * @return object|NULL
   */
  public function getMembers($archive = 0) {

    $query = "SELECT *, DATE_FORMAT(`MEMBER_birthdate`, '%d-%m-%Y') as 'MEMBER_birthdate'
                        FROM `members`
                        LEFT JOIN `membertypes` ON `membertypes`.`MEMBERTYPE_id` = `members`.`MEMBER_membertype_id`
												LEFT JOIN `addresses` ON `members`.`ADR_id` = `addresses`.`ADR_id`  AND `addresses`.`ADR_archive` = ?
                        WHERE `members`.`MEMBER_archive` = ?
                        ORDER BY `addresses`.`ADR_familyname` ASC, `members`.`MEMBER_parent` DESC, `members`.`MEMBER_rank` ASC, `members`.`MEMBER_initials` ASC";

    return $this->requestDataQuery($query, array('ii', $archive, $archive));
  }

  /**
   * Returns an object of member objects with matching familyname or MEMBER_firstname specified.
   * Maximum is limitited to specified number. If no members are found NULL is returned.
   * @param string $name
   * @param int $limit
   * @return object|NULL
   */
  public function getMembersByName($name, $limit) {
    $query = "SELECT *
              FROM `members`
              LEFT JOIN `addresses` ON `members`.`ADR_id` = `addresses`.`ADR_id`
              WHERE `members`.MEMBER_archive = ?
              AND (
                LOWER(CONCAT(`MEMBER_firstname`,`MEMBER_familyname_preposition`,`MEMBER_familyname`)) LIKE REPLACE(LOWER(?), ' ', '')
                OR LOWER(CONCAT(`ADR_familyname_preposition`,`ADR_familyname`)) LIKE REPLACE(LOWER(?), ' ', '')
              )
              ORDER BY `addresses`.`ADR_familyname`
              LIMIT ?";
    return $this->requestDataQuery($query, array('issi', $_SESSION['ARCHIVE-MODE'], "%" . $name . "%", "%" . $name . "%", $limit));
  }

  public function getMembersEmail($memberids) {
    $query = "SELECT *, CONCAT(`addresses`.`ADR_familyname`, `members`.`MEMBER_parent`, `members`.`MEMBER_rank`, `members`.`MEMBER_initials`) AS `_SORT`
              FROM `members`
              LEFT JOIN `addresses` ON `addresses`.`ADR_id` =  `members`.`ADR_id` ";
    $query .= ($memberids === 'all') ? "WHERE 1 " : "WHERE `members`.`MEMBER_id` IN ($memberids) ";
    $query .= "AND `members`.`MEMBER_email` != ''
              AND `members`.`MEMBER_archive` = 0
              ORDER BY `members`.`MEMBER_email`";
    $result = $this->requestDataQuery($query, array());

    $return = array();

    foreach ($result as $member) {
      $return[$member->MEMBER_id]['EMAIL'] = $member->MEMBER_email;
      $return[$member->MEMBER_id]['FULLNAME'] = $this->generateFullMemberName($member, false, true);
      $return[$member->MEMBER_id]['SORT'] = $member->_SORT;
    }

    return $return;
  }

  public function getMembersEmailInGroup($groupid) {
    $query = "SELECT *, CONCAT(`addresses`.`ADR_familyname`, `members`.`MEMBER_parent`, `members`.`MEMBER_rank`, `members`.`MEMBER_initials`) AS `_SORT`
                FROM `groupmembers`
                LEFT JOIN `members` ON `groupmembers`.`GROUPMEMBERS_memberid` = `members`.`MEMBER_id`
                LEFT JOIN `addresses` ON `addresses`.`ADR_id` =  `members`.`ADR_id`
                WHERE `groupmembers`.`GROUPMEMBERS_groupid` = ?
                AND `members`.`MEMBER_email` != ''
                AND `members`.`MEMBER_archive` = 0
                ORDER BY `members`.`MEMBER_email`";
    $result = $this->requestDataQuery($query, array("i", $groupid));

    $return = array();

    foreach ($result as $member) {
      $return[$member->MEMBER_id]['EMAIL'] = $member->MEMBER_email;
      $return[$member->MEMBER_id]['FULLNAME'] = $this->generateFullMemberName($member, false, true);
      $return[$member->MEMBER_id]['SORT'] = $member->_SORT;
    }

    return $return;
  }

  /**
   * Returns the id of the first member of a group or NULL if no memberid is found.
   * @param int $groupId
   * @return int|NULL
   */
  public function getFirstMemberIdOfGroup($groupId) {
    $groupId = mysqli_real_escape_string($this, $groupId);

    $query = "SELECT `members`.`MEMBER_id`
                       FROM `members`
                       INNER JOIN `groupmembers` ON `members`.`MEMBER_id` = `groupmembers`.`GROUPMEMBERS_memberid`
                       WHERE `groupmembers`.`GROUPMEMBERS_groupid` = ?
                       AND `members`.MEMBER_archive = ?
                       ORDER BY `members`.`MEMBER_familyname` ASC LIMIT 1;";

    $result = $this->requestDataQuery($query, array('ii', $groupId, $_SESSION['ARCHIVE-MODE']));

    if ($result) {
      return $result[0];
    }

    return $result;
  }

  /**
   * Returns the id of the first member of an address or NULL if no memberid is found.
   * @param int $addressId
   * @return int
   */
  public function getFirstMemberIdOfAddress($addressId) {

    if ($addressId) {
      $query = "SELECT `MEMBER_id`
                        FROM `members`
                        WHERE `ADR_id` = ?
                        AND MEMBER_archive = ?
                        ORDER BY `MEMBER_rank` ASC LIMIT 1;";
      $result = $this->requestDataQuery($query, array('ii', $addressId, $_SESSION['ARCHIVE-MODE']));
    } elseif ((!$addressId) and ($_SESSION['ARCHIVE-MODE'])) { // Archive mode, Members without address in archive
      $query = "SELECT `MEMBER_id`
                        FROM `members`
                        LEFT JOIN `addresses` ON `members`.`ADR_id` = `addresses`.`ADR_id`
                        WHERE `members`.`MEMBER_archive` = '1'
                        AND `addresses`.`ADR_archive` = '0'
                        ORDER BY `members`.`MEMBER_rank` ASC LIMIT 1;";

      $result = $this->requestDataQuery($query);
    }

    if ($result) {
      return $result[0];
    }

    return $result;
  }

  /**
   * Returns a complete table as an object or NULL if table is empty.
   * @param string $table
   * @return object|NULL
   */
  public function getTable($table) {
    $table = $this->escape_string($table);
    $query = "SELECT * FROM `$table`";
    return $this->requestDataQuery($query);
  }

  /**
   * Returns all groups for a member, `MEMBER_IN_GROUP` = 1 if member is in group, 0 if not
   * @param int $memberId
   * @return object
   */
  public function getMemberInGroup($memberId) {
    $query = "SELECT `groups`.*, IF(`groupmembers`.`GROUPMEMBERS_memberid`=?, 1, 0) AS _IN_GROUP
		FROM `groups`
		LEFT JOIN `groupmembers` ON `groups`.`GROUP_id` = `groupmembers`.`GROUPMEMBERS_groupid` AND `GROUPMEMBERS_memberid` = ?
                ORDER BY  `groups`.`GROUP_name` ASC";
    return $this->requestDataQuery($query, array('ii', $memberId, $memberId));
  }

  /**
   * Returns all groups for an address, `ADDRESS_IN_GROUP` = 1 if address is in group, 0 if not
   * @param int $addressId
   * @return object
   */
  public function getAddressInGroup($addressId) {
    $query = "SELECT `groups`.*, IF(`groupaddresses`.`GROUPADDRESSES_addressid`=?, 1, 0) AS _IN_GROUP
		FROM `groups`
		LEFT JOIN `groupaddresses` ON `groups`.`GROUP_id` = `groupaddresses`.`GROUPADDRESSES_groupid` AND `GROUPADDRESSES_addressid` = ?
                ORDER BY  `groups`.`GROUP_name` ASC
								";
    return $this->requestDataQuery($query, array('ii', $addressId, $addressId));
  }

  /**
   * Returns an object of memberobjects from the database for the specified group. If no members are found NULL is returned.
   * @param int $groupId
   * @return object|NULL
   */
  public function getMembersForGroup($groupId) {
    $archive = ($_SESSION['ARCHIVE-MODE'] === true) ? 1 : 0;

    $query = "SELECT *
                  FROM `members`
                  INNER JOIN `groupmembers` ON `members`.`MEMBER_id` = `groupmembers`.`GROUPMEMBERS_memberid`
		  LEFT JOIN `addresses` ON `members`.`ADR_id` = `addresses`.`ADR_id`
                  LEFT JOIN `membertypes` ON `membertypes`.`MEMBERTYPE_id` = `members`.`MEMBER_membertype_id`
                  WHERE `members`.`MEMBER_archive`= ?
                  HAVING `groupmembers`.`GROUPMEMBERS_groupid` = ?
                  ORDER BY `addresses`.`ADR_familyname`, `members`.`ADR_id`, `members`.`MEMBER_rank` ASC, `members`.`MEMBER_parent` ASC;";

    return $this->requestDataQuery($query, array('ii', $archive, $groupId));
  }

  /**
   * Returns an object of addressobjects from the database for the specified group. If no addresses are found NULL is returned.
   * @param int $groupId
   * @return object|NULL
   */
  public function getAddressesForGroup($groupId) {
    $archive = ($_SESSION['ARCHIVE-MODE'] === true) ? 1 : 0;

    $query = "SELECT *, COUNT(`members`.`MEMBER_id`) AS _COUNT
                  FROM `addresses`
                  INNER JOIN `groupaddresses` ON `addresses`.`ADR_id` = `groupaddresses`.`GROUPADDRESSES_addressid`
                  LEFT JOIN `members` ON `addresses`.`ADR_id` =  `members`.`ADR_id`
                  WHERE `groupaddresses`.`GROUPADDRESSES_groupid` = ?
                  AND `addresses`.`ADR_archive`= ?
                  GROUP BY `addresses`.`ADR_id`
                  HAVING `members`.`MEMBER_archive` = ?
                  ORDER BY `addresses`.`ADR_familyname`, `members`.`MEMBER_parent` DESC, `members`.`MEMBER_rank` ASC, `members`.`MEMBER_initials` ASC";


    return $this->requestDataQuery($query, array('iii', $groupId, $archive, $archive));
  }

  /**
   * Returns an object of memberobjects from the database for the specified address. If no members are found NULL is returned.
   * @param int $addressId
   * @param int $archive
   * @return object|NULL
   */
  public function getMembersForAddress($addressId, $archive = NULL) {
    if ($archive === NULL) {
      $archive = $_SESSION['ARCHIVE-MODE'];
    }

    $archive = ($_SESSION['ARCHIVE-MODE'] === true) ? 1 : 0;

    if ($archive === 0) {
      if ($addressId) {  // Non - Archive mode
        $query = "SELECT *, DATE_FORMAT(`MEMBER_birthdate`, '%d-%m-%Y') as 'MEMBER_birthdate'
	FROM `members`
	LEFT JOIN `addresses` ON `members`.`ADR_id` = `addresses`.`ADR_id`
	WHERE `members`.`ADR_id` = ?
	AND `members`.MEMBER_archive = 0
	ORDER BY `members`.`ADR_id`, `members`.`MEMBER_rank` ASC, `members`.`MEMBER_parent` DESC";

        $result = $this->requestDataQuery($query, array('i', $addressId));
      }
    } elseif ($archive === 1) {

      if (($addressId) and ($addressId !== 0)) {  // Archive mode, Members with own address in archive, addressid of Archive is = 0
        $query = "SELECT *, DATE_FORMAT(`MEMBER_birthdate`, '%d-%m-%Y') as 'MEMBER_birthdate'
								 FROM `members`
								 LEFT JOIN `addresses` ON `members`.`ADR_id` = `addresses`.`ADR_id`
								 WHERE `members`.`ADR_id` = ?
								 AND `members`.MEMBER_archive = 1
								 ORDER BY `members`.`ADR_id`, `members`.`MEMBER_rank` ASC, `members`.`MEMBER_parent` DESC";

        $result = $this->requestDataQuery($query, array('i', $addressId));
      } else { // Archive mode, Members without address in archive, addressid of Archive is = 0
        $query = "SELECT *, DATE_FORMAT(`MEMBER_birthdate`, '%d-%m-%Y') as 'MEMBER_birthdate'
                        FROM `members`
                        LEFT JOIN `addresses` ON `members`.`ADR_id` = `addresses`.`ADR_id`
                        WHERE `members`.MEMBER_archive = '1'
                        AND (`addresses`.`ADR_archive` =  '0' OR  `addresses`.`ADR_id` IS NULL)
                        ORDER BY `members`.`MEMBER_familyname` ASC, `members`.`MEMBER_parent` DESC, `members`.`MEMBER_rank` ASC ";

        $result = $this->requestDataQuery($query);
      }
    }
    return $result;
  }

  /**
   * Returns an object of all MemberTypes in database
   * @return object
   */
  public function getMemberTypesList() {
    $query = 'SELECT `MEMBERTYPE_id`, `MEMBERTYPE_name` FROM `membertypes`';
    return $this->requestDataQuery($query);
  }

  /**
   * Returns a object of the cities available in the table
   * @return object
   */
  public function getCityList() {
    $archive = ($_SESSION['USER']->checkUserrights('view_archive')) ? "0,1" : "0";
    $query = "SELECT `ADR_city` FROM `addresses` WHERE `ADR_city` != '' and `ADR_archive` IN(?) GROUP BY `ADR_city`";

    return $this->requestDataQuery($query, array("s", $archive));
  }

  /**
   * Returns an object of the events  available in the table
   * @return object
   */
  public function getEventsList() {
    $query = "SELECT * FROM `events`
		LEFT JOIN `eventtypes` ON `events`.`EVENTTYPE_id` = `eventtypes`.`EVENTTYPE_id`
		GROUP BY `events`.`EVENTTYPE_id`";

    return $this->requestDataQuery($query);
  }

  /**
   * Returns an object with groups selected from parentid
   * @param int $groupparentid
   * @return object
   */
  public function getGroupsFromParent($groupparentid = 1) {
    $query = "SELECT `groups`.*
		FROM `groups`
                WHERE `groups`.`GROUP_parent_id` = ?
                ORDER BY  `groups`.`GROUP_name` ASC";

    return $this->requestDataQuery($query, array("i", $groupparentid));
  }

  /**
   * Returns an object as tree with groups, members and addresses
   * @param int $groupparentid
   * @return object
   */
  public function getGroupTree() {

    $query = "SELECT `groups`.* FROM `groups` ORDER BY  `groups`.`GROUP_name` ASC";
    $result = $this->requestDataQuery($query, array());

    $sortedgroups = new stdClass;
    foreach ($result as &$GROUP) {
      $sortedgroups->{$GROUP->GROUP_id} = $GROUP;
    }

    return $sortedgroups;
  }

  /**
   * Returns a object with members, photo, introduction, age for each address
   * @param int $archive
   * @return object
   */
  public function getMembersIntroduction($archive = 0) {
    $query = "SELECT `MEMBER_id`,
                `MEMBER_familyname`,
                `MEMBER_familyname_preposition`,
                `MEMBER_initials`,
                `MEMBER_firstname`,
                `MEMBER_christianname`,
                `MEMBER_gender`,
                `MEMBER_membertype_id`,
                `MEMBER_photo`,
                `MEMBER_introduction`,
                `MEMBER_parent`,
                `MEMBER_rank`, DATE_FORMAT(`MEMBER_birthdate`, '%d-%m-%Y') as 'MEMBER_birthdate',
                `MEMBER_familynameview`,
                `addresses`.`ADR_familyname`,
                `addresses`.`ADR_familyname_preposition`,
                `addresses`.`ADR_id`
                 FROM `addresses`
                 LEFT JOIN `members` ON `addresses`.`ADR_id` = `members`.`ADR_id`
                 LEFT JOIN `membertypes` ON `membertypes`.`MEMBERTYPE_id` = `members`.`MEMBER_membertype_id`
                 WHERE `addresses`.`ADR_archive` = ?
                 AND `members`.`MEMBER_archive` = ?
                 AND (`members`.`MEMBER_photo` != '' OR `members`.`MEMBER_introduction` != '')
                 GROUP BY `members`.`MEMBER_id`
                 ORDER BY `addresses`.`ADR_familyname` ASC, `members`.`MEMBER_parent` DESC, `members`.`MEMBER_rank` ASC, `members`.`MEMBER_initials` ASC
                 ";
    $result = $this->requestDataQuery($query, array('ii', $archive, $archive));

    $addresses = new stdClass();

    foreach ($result as $member => $value) {
      $ADR_id = $value->ADR_id;
      $MEMBER_id = $value->MEMBER_id;

      if (!isset($addresses->$ADR_id)) {
        $addresses->$ADR_id = new stdClass();
        $addresses->$ADR_id->ADDRESS = $value;
      }

      if (!isset($addresses->$ADR_id->MEMBERS)) {
        $addresses->$ADR_id->MEMBERS = new stdClass();
      }

      if (!isset($addresses->$ADR_id->MEMBERS->$MEMBER_id)) {
        $addresses->$ADR_id->MEMBERS->$MEMBER_id = new stdClass();
      }

      $addresses->$ADR_id->MEMBERS->$MEMBER_id = $value;
    }

    return $addresses;
  }

  /**
   * Returns all events for the specified member as an object. Returns NULL if no events are found.
   * @param int $memberId
   * @return object|NULL
   */
  public function getEventsForMember($memberId) {
    $query = "SELECT
              `EVENT_id`,
              `EVENT_parent_id`,
              `EVENT_MEMBER_id`,
              `EVENT_MEMBER_fullname`,
              `EVENTTYPE_name` as EVENTTYPE_name,
              `events`.`EVENT_note`,
              DATE_FORMAT( `events`.`EVENT_date` , '%d-%m-%Y' ) as  EVENT_date
              FROM `events`
              LEFT JOIN `eventtypes` ON `events`.`EVENTTYPE_id` = `eventtypes`.`EVENTTYPE_id`
              WHERE `events`.`EVENT_id` in (SELECT `EVENT_parent_id` FROM `events` WHERE `EVENT_MEMBER_id` = ?)
              OR `events`.`EVENT_parent_id` in (SELECT `EVENT_parent_id` FROM `events` WHERE `EVENT_MEMBER_id` = ? AND `EVENT_parent_id` != 0)
              OR `events`.`EVENT_parent_id` in (SELECT `EVENT_id` FROM `events` WHERE `EVENT_MEMBER_id` = ?)
              OR `EVENT_MEMBER_id` = ?
              ORDER BY `events`.`EVENT_date` DESC";

    $result = $this->requestDataQuery($query, array('iiii', $memberId, $memberId, $memberId, $memberId));
    $events = array();

    foreach ($result as $unordered_event) {
      if (!array_key_exists($unordered_event->EVENT_id, $events) && !array_key_exists($unordered_event->EVENT_parent_id, $events)) {
        $key = ($unordered_event->EVENT_parent_id) ? $unordered_event->EVENT_parent_id : $unordered_event->EVENT_id;
        $events[$key] = array(
            'EVENT_id' => ($unordered_event->EVENT_id),
            'EVENTTYPE_name' => ($unordered_event->EVENTTYPE_name),
            'EVENT_note' => ($unordered_event->EVENT_note),
            'EVENT_date' => ($unordered_event->EVENT_date),
            '_MEMBERS' => array());
      }

      if ($unordered_event->EVENT_parent_id === 0 || $unordered_event->EVENTTYPE_name === 'EVENT_DIVORCE') {
        array_push($events[$unordered_event->EVENT_id]['_MEMBERS'], $unordered_event);
      } else {
        array_push($events[$unordered_event->EVENT_parent_id]['_MEMBERS'], $unordered_event);
      }
    }

    //remove empty rows
    return array_filter($events);
  }

  /**
   * Returns 1 event for the specified member. Returns NULL if no events are found.
   * @param int $eventId
   * @return object|NULL
   */
  public function getEventForMember($eventId) {
    $query = "SELECT
              `EVENT_id`,
              `EVENT_parent_id`,
              `EVENT_MEMBER_id`,
              `EVENT_MEMBER_fullname`,
              `EVENTTYPE_name` as EVENTTYPE_name,
              `events`.`EVENT_note`,
              DATE_FORMAT( `events`.`EVENT_date` , '%d-%m-%Y' ) as  EVENT_date
              FROM `events`
              LEFT JOIN `eventtypes` ON `events`.`EVENTTYPE_id` = `eventtypes`.`EVENTTYPE_id`
              WHERE `events`.`EVENT_id` = ?
              ORDER BY `events`.`EVENT_date` DESC LIMIT 1
				";
    $result = $this->requestDataQuery($query, array('i', $eventId));

    if ($result) {
      return $result[0];
    }

    return $result;
  }

  /**
   * Returns first event for the specified member. Returns NULL if no events are found.
   * @param int $memberId
   * @return object|NULL
   */
  public function getFirstEventForMember($memberId) {
    $query = "SELECT  `events`.`EVENT_id`
                FROM `events`
                WHERE `events`.`EVENT_MEMBER_id` = ?
                ORDER BY `events`.`EVENT_id` ASC LIMIT 1";
    $result = $this->requestDataQuery($query, array('i', $memberId));


    if ($result) {
      return $this->getEventForMember($result[0]->EVENT_id);
    } else {
      return $result;
    }
  }

  /**
   * Returns all Eventstypes with corresponding keys.
   * @return object
   */
  public function getEventtypes() {
    $eventtypesTable = $this->getTable("eventtypes");

    $Events = new stdClass();
    foreach ($eventtypesTable as $eventtype) {
      $Events->{$eventtype->EVENTTYPE_name} = $eventtype;
      $Events->{$eventtype->EVENTTYPE_name}->translation = $this->getEventTypeTranslation($eventtype->EVENTTYPE_name);
    };

    return $Events;
  }

  /**
   * Returns translation of Eventstype.
   * @param string $event_type
   * @return string
   */
  public function getEventTypeTranslation($event_type = '') {

    switch ($event_type) {
      case 'EVENT_ADD_BIRTH_TESTIMONY':
        return sprintf(__("Entered with %s"), __("Birth testimony"));
        break;
      case 'EVENT_ADD_CONFESSION_TESTIMONY':
        return sprintf(__("Entered with %s"), __("Confession testimony"));
        break;
      case 'EVENT_ADD_GUESTMEMBERSHIP':
        return sprintf(__("Entered with %s"), __("Guest membership"));
        break;
      case 'EVENT_ADD_MEMBERSHIP':
        return sprintf(__("Entered with %s"), __("Membership"));
        break;
      case 'EVENT_ADD_STAY_TESTIMONY':
        return sprintf(__("Entered with %s"), __("Stay testimony"));
        break;
      case 'EVENT_ADD_TRAVEL_TESTIMONY':
        return sprintf(__("Entered with %s"), __("Travel testimony"));
        break;
      case 'EVENT_ADD_NEWMEMBER':
        return sprintf(__("Became a member of the church"));
        break;
      case 'EVENT_BAPTISED':
        return __("Baptised");
        break;
      case 'EVENT_BIRTH':
        return __("Birth");
        break;
      case 'EVENT_CHANGED_BUSINESS_EMAIL':
        return __("Changed email (work)");
        break;
      case 'EVENT_CHANGED_BUSINESS_PHONE':
        return __("Changed phone number (work)");
        break;
      case 'EVENT_CHANGED_EMAIL':
        return __("Changed email");
        break;
      case 'EVENT_CHANGED_HOME_EMAIL':
        return __("Changed email (home)");
        break;
      case 'EVENT_CHANGED_HOME_PHONE':
        return __("Changed phone number (home)");
        break;
      case 'EVENT_CHANGED_PHONE':
        return __("Changed phone number");
        break;
      case 'EVENT_CONFESSION':
        return __("Confession");
        break;
      case 'EVENT_CONTINUE_GUESTMEMBERSHIP':
        return sprintf(__("Elongation of %s"), __("Guest membership"));
        break;
      case 'EVENT_CONTINUE_STAY_TESTIMONY':
        return sprintf(__("Elongation of %s"), __("Stay testimony"));
        break;
      case 'EVENT_CONTINUE_TRAVEL_TESTIMONY':
        return sprintf(__("Elongation of %s"), __("Travel testimony"));
        break;
      case 'EVENT_DIED':
        return __("Died");
        break;
      case 'EVENT_DIVORCE':
        return __("Divorce");
        break;
      case 'EVENT_END_GUESTMEMBERSHIP':
        return sprintf(__("End of %s"), __("Guest membership"));
        break;
      case 'EVENT_GONE':
        return __("Gone");
        break;
      case 'EVENT_MARRIAGE':
        return __("Marriage");
        break;
      case 'EVENT_MOVED':
        return __("Changed of address");
        break;
      case 'EVENT_MOVED_ABROAD':
        return sprintf(__("Moved to %s"), __("Abroard"));
        break;
      case 'EVENT_MOVED_BIRTH_TESTIMONY':
        return sprintf(__("Left with %s"), __("Birth testimony"));
        break;
      case 'EVENT_MOVED_CONFESSION_TESTIMONY':
        return sprintf(__("Left with %s"), __("Confession testimony"));
        break;
      case 'EVENT_MOVED_GUESTMEMBERSHIP':
        return sprintf(__("Left with %s"), __("Guest membership"));
        break;
      case 'EVENT_MOVED_STAY_TESTIMONY':
        return sprintf(__("Left with %s"), __("Stay testimony"));
        break;
      case 'EVENT_MOVED_TRAVEL_TESTIMONY':
        return sprintf(__("Left with %s"), __("Travel testimony"));
        break;
      case 'EVENT_SICK':
        return __("Sick");
        break;
      case 'EVENT_NOREASON':
        return __("No reason / wont keep");
        break;
    }

    return 'ERROR';
  }

  /**
   * Returns the the first usertype
   * @return object|NULL
   */
  public function getFirstUsertypeById() {
    $query = "SELECT `usertypes`.* FROM `usertypes` ORDER BY `USERTYPE_id` DESC LIMIT 1";
    $result = $this->requestDataQuery($query);

    if ($result) {
      return $result[0];
    }

    return $result;
  }

  /**
   * Returns all eventdata of last month
   * @return object
   */
  public function getLastEvents() {
    $query = "SELECT
              `EVENT_id`,
              `EVENT_parent_id`,
              `EVENT_MEMBER_id`,
              `EVENT_MEMBER_fullname`,
              `EVENT_MEMBER_adr_id`,
              `EVENT_MEMBER_address`,
              `EVENTTYPE_name` as EVENTTYPE_name,
              DATE_FORMAT( `events`.`EVENT_date` , '%d-%m-%Y' ) as  EVENT_date
              FROM `events`
              LEFT JOIN `eventtypes` ON `events`.`EVENTTYPE_id` = `eventtypes`.`EVENTTYPE_id`
              WHERE `events`.`EVENT_date` >= date_sub(now(), interval 1 month)
              ORDER BY `events`.`EVENT_date` DESC, `EVENT_id` ASC";

    $result = $this->requestDataQuery($query);
    $lastevents = array();

    foreach ($result as $unordered_event) {
      if (!array_key_exists($unordered_event->EVENT_id, $lastevents) && !array_key_exists($unordered_event->EVENT_parent_id, $lastevents)) {
        $lastevents[$unordered_event->EVENT_id] = array('EVENTTYPE_name' => ($unordered_event->EVENTTYPE_name),
            'EVENT_date' => ($unordered_event->EVENT_date),
            '_MEMBERS' => array());
      }

      if ($unordered_event->EVENT_parent_id === 0 || $unordered_event->EVENTTYPE_name === 'EVENT_DIVORCE') {
        array_push($lastevents[$unordered_event->EVENT_id]['_MEMBERS'], $unordered_event);
      } else {
        array_push($lastevents[$unordered_event->EVENT_parent_id]['_MEMBERS'], $unordered_event);
      }
    }

    //remove empty rows
    return array_filter($lastevents);
  }

  /**
   * Returns all eventdata of a period
   * @return object
   */
  public function getLastChanges() {
    $query = "SELECT
              `EVENT_id`,
              `EVENT_parent_id`,
              `EVENT_MEMBER_id`,
              `EVENT_MEMBER_fullname`,
              `EVENTTYPE_name` as EVENTTYPE_name,
              DATE_FORMAT( `events`.`EVENT_date` , '%d-%m-%Y' ) as  EVENT_date
              FROM `events`
              LEFT JOIN `eventtypes` ON `events`.`EVENTTYPE_id` = `eventtypes`.`EVENTTYPE_id`
              WHERE `events`.`EVENT_date` >= date_sub(now(), interval 1 year)
              ORDER BY `events`.`EVENTTYPE_id` ASC, `events`.`EVENT_date` DESC, `EVENT_id` ASC";

    $result = $this->requestDataQuery($query);
    $lastevents = new stdClass();

    foreach ($result as $unordered_event) {
      $event_name = $unordered_event->EVENTTYPE_name;
      if (!isset($lastevents->$event_name))
        $lastevents->{$event_name} = new stdClass();

      $parent_id = $unordered_event->EVENT_parent_id;
      $event_id = $unordered_event->EVENT_id;
      if (!isset($lastevents->$event_name->$event_id) && !isset($lastevents->$event_name->$parent_id)) {
        $lastevents->$event_name->$event_id = array();
      }

      if ($parent_id === 0 || $event_name === 'EVENT_DIVORCE') {
        array_push($lastevents->$event_name->$event_id, $unordered_event);
      } else {
        array_push($lastevents->$event_name->$parent_id, $unordered_event);
      }
    }

    foreach ($lastevents as $event_name) {
      $count = count((array) $event_name);
      $event_name->_total = $count;
    }
    //remove empty rows
    return $lastevents;
  }

  /**
   * Returns Member/Address statistics
   * @return object
   */
  public function getMemberStats() {
    $query = "SELECT
                COUNT(DISTINCT(`addresses`.`ADR_id`)) as `TOTAL_ADDRESSES` ,
                COUNT(DISTINCT(`members`.`MEMBER_id`))   as `TOTAL_MEMBERS`
                FROM `addresses`
                LEFT JOIN `members` ON `addresses`.`ADR_id` = `members`.`ADR_id`
                AND `members`.`MEMBER_archive` = 0
                WHERE `addresses`.`ADR_archive` = 0
                ORDER BY `addresses`.`ADR_familyname` ASC;";

    $result = $this->requestDataQuery($query);

    if ($result) {
      return $result[0];
    }

    return false;
  }

  /**
   * Returns Membertypes statistics
   * @return object
   */
  public function getMembertypeStats() {
    $query = "SELECT `membertypes`.`MEMBERTYPE_name`, COUNT(`members`.`MEMBER_id`) as `TOTAL_MEMBERTYPES`
							FROM `membertypes`
							LEFT JOIN `members` ON `membertypes`.`MEMBERTYPE_id` = `members`.`MEMBER_membertype_id`
							WHERE `members`.`MEMBER_archive` = 0
							GROUP BY `membertypes`.`MEMBERTYPE_name`;
							";
    return $this->requestDataQuery($query);
  }

  /**
   * Returns Event statistics
   * @return object
   */
  public function getEventStats() {
    $query = "SELECT `EVENTTYPE_name`, count(`events`.`EVENTTYPE_id`) AS `TOTAL_EVENTS`
							FROM `eventtypes`
							LEFT JOIN `events` ON `eventtypes`.`EVENTTYPE_id` = `events`.`EVENTTYPE_id`
							WHERE `EVENT_date` >= date_sub(now(), interval 1 year)
							GROUP BY `EVENTTYPE_name`;
							";
    return $this->requestDataQuery($query);
  }

  /**
   * Returns the userrights for the provided usertype.
   * @param int $usertypeid
   * @return object|NULL
   */
  public function getUsertypeById($usertypeid = false) {
    if ($usertypeid === false || $usertypeid === 'false') {
      $query = "SELECT `usertypes`.* FROM `usertypes` ORDER BY `USERTYPE_id` DESC LIMIT 1;";
      $result = $this->requestDataQuery($query);
    } else {
      $query = "SELECT `usertypes`.* FROM `usertypes` WHERE `USERTYPE_id`=?;";
      $result = $this->requestDataQuery($query, array("i", $usertypeid));
    }

    if ($result) {
      return $result[0];
    }

    return $result;
  }

  /**
   * Returns the failed login attempts.
   * @return object
   */
  public function getFailedLoginAttempts() {
    $query = "SELECT `failedaccess`.*,AES_DECRYPT(`FAILEDACCESS_pass`, ?) AS `FAILEDACCESS_pass_decrypt` FROM `failedaccess` ORDER BY `FAILEDACCESS_timestamp` DESC;";
    return $this->requestDataQuery($query, array("s", AES_KEY));
  }

  /**
   * Returns the number of failed login attempts for the provided username.
   * @param string $username
   * @return int
   */
  public function getFailedUserAttempts($username) {
    $query = "SELECT COUNT(*) FROM `failedaccess` WHERE `FAILEDACCESS_loginname`=? AND `FAILEDACCESS_timestamp` > UNIX_TIMESTAMP(now() - INTERVAL 1 HOUR);";
    $result = $this->requestDataQuery($query, array("s", $username), "array");
    $count = array_values($result[0]);
    return $count[0];
  }

  /**
   * Returns the number of failed login attempts for the provided ip address.
   * @param string $ip
   * @return int
   */
  public function getFailedIpAttempts($ip) {
    $query = "SELECT COUNT(*) FROM `failedaccess` WHERE `FAILEDACCESS_ip`=? AND `FAILEDACCESS_timestamp` > UNIX_TIMESTAMP(now() - INTERVAL 1 HOUR);";
    $result = $this->requestDataQuery($query, array("s", $ip), "array");
    $count = array_values($result[0]);
    return $count[0];
  }

  /**
   * Puts a failed login attempt into the database.
   * @param string $ip
   * @param string $username
   * @param string $pass
   */
  public function failedLoginAttempt($ip, $username = "", $pass = "") {
    $query = "INSERT INTO `failedaccess` (
                `FAILEDACCESS_ip`,
                `FAILEDACCESS_loginname`,
								`FAILEDACCESS_pass`,
                `FAILEDACCESS_timestamp`)
                values (?, ?, AES_ENCRYPT(?,?), ?);";
    $result = $this->changeDataQuery($query, array('ssssi', $ip, $username, $pass, AES_KEY, time()));
  }

  /**
   * Returns the members with birthday between two dates
   * @param string $startUnixDate
   * @param string $endUnixDate
   * @param int $archive
   * @return object
   */
  public function getBirthdayEvents($startUnixDate, $endUnixDate, $archive = 0) {
    $query = "SELECT *, UNIX_TIMESTAMP(DATE_FORMAT(`MEMBER_birthdate`, CONCAT(YEAR(FROM_UNIXTIME(?)),'-%m-%d'))) AS 'eventDate',
      DATE_FORMAT( NOW(), '%Y' ) - DATE_FORMAT(MEMBER_birthdate, '%Y') - ( DATE_FORMAT(NOW(), '00-%m-%d' ) < DATE_FORMAT(MEMBER_birthdate, '00-%m-%d' ) ) as MEMBER_age,
      DATE_FORMAT(`MEMBER_birthdate`, '%d-%m-%Y') as 'MEMBER_birthdate',
      DATE_FORMAT(`MEMBER_birthdate`, '%c') as 'eventMonth'
      FROM `members` LEFT JOIN `addresses` ON `members`.`ADR_id` = `addresses`.`ADR_id`
      WHERE  `members`.`MEMBER_archive` = ?
      AND MEMBER_birthdateview = 1
      AND (DATE_FORMAT(MEMBER_birthdate,'%m-%d') BETWEEN DATE_FORMAT(FROM_UNIXTIME(?),'%m-%d') AND DATE_FORMAT(FROM_UNIXTIME(?),'%m-%d'))
      ORDER BY DATE_FORMAT(`MEMBER_birthdate`, '%m-%d') ";

    return $this->requestDataQuery($query, array("siss", $startUnixDate, $archive, $startUnixDate, $endUnixDate));
  }

  /**
   * Returns the members with mariagedaye between two dates
   * @param string $startUnixDate
   * @param string $endUnixDate
   * @param int $archive
   * @return object
   */
  public function getMariageEvents($startUnixDate, $endUnixDate, $archive = 0) {
    $query = "SELECT *, UNIX_TIMESTAMP(DATE_FORMAT(`MEMBER_mariagedate`, CONCAT(YEAR(FROM_UNIXTIME(?)),'-%m-%d'))) AS 'eventDate',
      DATE_FORMAT( NOW(), '%Y' ) - DATE_FORMAT(MEMBER_mariagedate, '%Y') - ( DATE_FORMAT(NOW(), '00-%m-%d' ) < DATE_FORMAT(MEMBER_mariagedate, '00-%m-%d' ) ) as MEMBER_mariageage,
      DATE_FORMAT(`MEMBER_mariagedate`, '%d-%m-%Y') as 'MEMBER_mariagedate',
      DATE_FORMAT(`MEMBER_mariagedate`, '%c') as 'eventMonth',
      GROUP_CONCAT(DISTINCT  `members`.`MEMBER_id`) as `MEMBERS_ids`
      FROM `members` LEFT JOIN `addresses` ON `members`.`ADR_id` = `addresses`.`ADR_id`
      WHERE  `members`.`MEMBER_archive` = ?
      AND MEMBER_birthdateview = 1
      AND (DAYOFYEAR(MEMBER_mariagedate) BETWEEN DAYOFYEAR(FROM_UNIXTIME(?)) AND DAYOFYEAR(FROM_UNIXTIME(?)))
      OR  (DAYOFYEAR(MEMBER_mariagedate) - DAYOFYEAR( FROM_UNIXTIME(?))  > 351)
      GROUP BY `members`.`ADR_id`
      ORDER BY DATE_FORMAT(`MEMBER_mariagedate`, '%m-%d') ";

    return $this->requestDataQuery($query, array("sisss", $startUnixDate, $archive, $startUnixDate, $endUnixDate, $endUnixDate));
  }

  /**
   * Returns the min and max age of the members in the tabel
   * @return object
   */
  public function getMinMaxDates() {
    $query = "SELECT min(`MEMBER_baptismdate`) as MEMBER_baptismdate_min, max(`MEMBER_baptismdate`) as MEMBER_baptismdate_max, min(`MEMBER_confessiondate`) as MEMBER_confessiondate_min, max( `MEMBER_confessiondate`)  as MEMBER_confessiondate_max, min(`MEMBER_mariagedate`)  as MEMBER_mariagedate_min, max(`MEMBER_mariagedate`) as MEMBER_mariagedate_max FROM `members`";
    $MinMaxDates = $this->requestDataQuery($query);
    return($MinMaxDates[0]);
  }

  /**
   * Generates a complete membername for a member
   * @param object $member
   * @param bool $tabs
   * @param bool $forcefullname
   * @param string $prefix
   * @param bool $familynameview
   * @param bool $hidefirstname
   * @param bool $hideinitials
   * @return string
   */
  public function generateFullMemberName($member, $tabs = true, $forcefullname = false, $prefix = '', $familynameview = false, $hidefirstname = false, $hideinitials = false) {
    $familynameview = ($familynameview === false) ? $member->{$prefix . 'MEMBER_familynameview'} : $familynameview;

    $familynamePart = "";
    $namePart = "";
    $member_age = isset($member->{$prefix . 'MEMBER_age'}) ? $member->{$prefix . 'MEMBER_age'} : $this->getAge($member->{$prefix . 'MEMBER_birthdate'});

    // if under 20 and membertype =! 'belijdend lid' or `gast belijdend lid' and only member in address, force complete name with familyname;
    if (($member_age < 20 && $member_age !== false) and ($member->{$prefix . 'MEMBER_membertype_id'} != 2) and ($member->{$prefix . 'MEMBER_membertype_id'} != 4) and ($forcefullname == false)) { //
      if (count($this->getMembersForAddress($member->{$prefix . 'ADR_id'}, 0)) == 1)
        $forcefullname = true;
    }

    // if under 20 and membertype =! 'belijdend lid' or `gast belijdend lid', membername is only MEMBER_firstname
    if (($member_age < 20 && $member_age !== false) and ($member->{$prefix . 'MEMBER_membertype_id'} != 2) and ($member->{$prefix . 'MEMBER_membertype_id'} != 4) and ($forcefullname == false)) {
      $namePart = $member->{$prefix . 'MEMBER_firstname'} . " ";

      // else if familynameview is set for only MEMBER_initials
    } elseif (($familynameview === 5 || $familynameview === 6 || $familynameview === 7 || $familynameview === 8 || $hidefirstname === true) && $hideinitials === false) {
      $namePart = $member->{$prefix . 'MEMBER_initials'} . " ";

      // else membername is MEMBER_initials & MEMBER_firstname
    } else {
      $firstname = ($member->{$prefix . 'MEMBER_firstname'} !== "") ? $member->{$prefix . 'MEMBER_firstname'} . " " : "";
      if ($hideinitials === false) {
        if ($member->{$prefix . 'MEMBER_firstname'} !== "") {
          if ($member->{$prefix . 'MEMBER_initials'} !== "") {
            $namePart = $firstname . "(" . $member->{$prefix . 'MEMBER_initials'} . ") ";
          } else {
            $namePart = $firstname . " ";
          }
        } else {
          $namePart = $member->{$prefix . 'MEMBER_initials'} . ' ';
        }
      }
    }

    /* Solve the familynamePart by looking at the `MEMBER_familynameview` value
      // 1: default – most used
      //			[adr_familyname]-[member_familyname_preposition][member_familyname], $namePart [adr_familyname_preposition]
      // 2: only own familyname
      //			[member_familyname], $namePart [member_familyname_preposition]
      // 3: own familyname – familyname_partner
      //			[member_familyname]-[adr_familyname_preposition] [adr_familyname], $namePart [member_familyname_preposition]
      // 4: familyname_partner
      //			[adr_familyname], $namePart	[adr_familyname_preposition]
      // 5-8: same as 1-4 but with only initials and no firstname
     */

    if (($member->{$prefix . 'ADR_familyname'} !== $member->{$prefix . 'MEMBER_familyname'}) && ($member->{$prefix . 'MEMBER_parent'} === 0)) {
      $familynameview = 2;
      $forcefullname = true;
    }

    // if user is a child and no fullname is forced, add no familyname
    if (($member_age < 20 && $member_age !== false) and ($member->{$prefix . 'MEMBER_membertype_id'} !== 2) and ($member->{$prefix . 'MEMBER_membertype_id'} !== 4) and ($forcefullname === false)) {

    } else {

      switch ($familynameview) {

        case 1:
        case 5:
          $namePart .= $member->{$prefix . 'ADR_familyname_preposition'};
          $familynamePart = $member->{$prefix . 'ADR_familyname'} . "-";
          if ($member->{$prefix . 'MEMBER_familyname_preposition'}) {
            $familynamePart .= $member->{$prefix . 'MEMBER_familyname_preposition'} . " ";
          }
          $familynamePart .= $member->{$prefix . 'MEMBER_familyname'} . ", ";
          break;

        case 2:
        case 6:
          $namePart .= $member->{$prefix . 'MEMBER_familyname_preposition'};
          $familynamePart = $member->{$prefix . 'MEMBER_familyname'} . ", ";
          break;

        case 3:
        case 7:
          $namePart .= $member->{$prefix . 'MEMBER_familyname_preposition'};
          $familynamePart = $member->{$prefix . 'MEMBER_familyname'} . "-";
          if ($member->{$prefix . 'ADR_familyname_preposition'}) {
            $familynamePart .= $member->{$prefix . 'ADR_familyname_preposition'} . " ";
          }
          $familynamePart .= $member->{$prefix . 'ADR_familyname'} . ", ";
          break;

        case 0:
        case 4:
        case 8:
          $namePart .= $member->{$prefix . 'ADR_familyname_preposition'};
          $familynamePart = $member->{$prefix . 'ADR_familyname'} . ", ";
          break;
      }
    }

    // if the user is a child and the output should contain tabs, add tabs to create a nice alignment with the parents
    $tab = "";
    if (($member->{$prefix . 'MEMBER_parent'} === 0) & ($tabs === true)) {
      $tab = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    }

    // remove last ', ' of familyname if needed
    if (!$namePart) {
      $familynamePart = substr($familynamePart, -2, 2) === ', ' ? substr($familynamePart, 0, -2) : $familynamePart;
    }
    return stripslashes(trim($tab . $familynamePart . $namePart));
  }

  /**
   * Gets the value of setting from the database
   * @param string $setting_name
   * @return object
   */
  public function getSetting($setting_name) {
    $query = "SELECT * FROM `settings` WHERE `SETTINGS_name` = ? LIMIT 1;";
    $setting_value = $this->requestDataQuery($query, array("s", $setting_name));
    $value = $setting_value[0]->SETTINGS_value;
    if ($setting_value[0]->SETTINGS_value === 'true') {
      $value = true;
    } elseif ($setting_value[0]->SETTINGS_value === 'false') {
      $value = false;
    }
    $setting_value[0]->SETTINGS_value = $value;

    return $setting_value[0];
  }

  /**
   * Gets all systemsettings from the database
   * @return object
   */
  public function getSettings() {
    $query = "SELECT * FROM `settings`";
    $setting_values = $this->requestDataQuery($query);

    $return_setting_values = new stdClass();
    foreach ($setting_values as $id => $setting) {
      $value = $setting->SETTINGS_value;
      if ($setting->SETTINGS_value === 'true') {
        $value = true;
      } elseif ($setting->SETTINGS_value === 'false') {
        $value = false;
      }
      $return_setting_values->{$setting->SETTINGS_name} = $value;
    }
    return $return_setting_values;
  }

  /**
   * Returns an array of table column names or NULL if table is not found.
   * @param string $table
   * @return array|NULL
   */
  public function getColumnNames($table) {
    $query = "SHOW COLUMNS FROM  `$table`";
    $totalResults = $this->requestDataQuery($query, array(), "array");
    if (count($totalResults) > 0) {
      $result = array();
      for ($i = 0; $i < count($totalResults); $i++) {
        $result[$i] = $totalResults[$i]['Field'];
      }
      return $result;
    }
    return NULL;
  }

  /**
   * Returns an object with all table names.
   * @return object
   */
  public function getTableNames() {
    $query = "SHOW TABLES";
    return $this->requestDataQuery($query);
  }

  /**
   * Returns a formatted date
   * @param string $date
   * @return string
   */
  public function setDate($date) {
    if (!$date) {
      return NULL;
    } else {
      $pieces = explode("-", $date);
      if ((count($pieces) === 3) and (strlen($pieces[2]) === 4 ) and (strlen($pieces[1]) === 2 ) and (strlen($pieces[0]) === 2 )) { // 10-12-2010
        $date = $pieces[2] . "-" . $pieces[1] . "-" . $pieces[0];
      } elseif ((count($pieces) === 3) and (strlen($pieces[0]) === 4 ) and (strlen($pieces[1]) === 2 ) and (strlen($pieces[2]) === 2 )) { // 2010-12-10
        $date = $pieces[0] . "-" . $pieces[1] . "-" . $pieces[2];
      } else {
        return false;
      }
      return $date;
    }
  }

  /**
   * Returns a formatted phonenumber
   * @param string $phone
   * @param bool $convert
   * @param bool $trim
   * @return string
   */
  public function formatPhone($phone = '', $convert = false, $trim = true) {

    // If we have not entered a phone number just return empty
    if (empty($phone)) {
      return '';
    }

    // Strip out any extra characters that we do not need only keep letters and numbers
    $phone = preg_replace("/[^0-9]/", "", $phone);

    // Do we want to convert phone numbers with letters to their number equivalent?
    // Samples are: 1-800-TERMINIX, 1-800-FLOWERS, 1-800-Petmeds
    if ($convert === true) {
      $replace = array('2' => array('a', 'b', 'c'),
          '3' => array('d', 'e', 'f'),
          '4' => array('g', 'h', 'i'),
          '5' => array('j', 'k', 'l'),
          '6' => array('m', 'n', 'o'),
          '7' => array('p', 'q', 'r', 's'),
          '8' => array('t', 'u', 'v'),
          '9' => array('w', 'x', 'y', 'z'));

      // Replace each letter with a number
      // Notice this is case insensitive with the str_ireplace instead of str_replace
      foreach ($replace as $digit => $letters) {
        $phone = str_ireplace($letters, $digit, $phone);
      }
    }

    // Perform phone number formatting here
    if (strlen($phone) === 7) {
      return preg_replace("/([0-9a-zA-Z]{3})([0-9a-zA-Z]{2})([0-9a-zA-Z]{2})/", "$1 $2 $3", $phone);
    } elseif (strlen($phone) === 10) {
      if (strpos($phone, "06") === false) {
        return preg_replace("/([0-9a-zA-Z]{3})([0-9a-zA-Z]{3})([0-9a-zA-Z]{4})/", "($1) $2 $3", $phone);
      } else {
        return preg_replace("/([0-9a-zA-Z]{2})([0-9a-zA-Z]{2})([0-9a-zA-Z]{2})([0-9a-zA-Z]{2})([0-9a-zA-Z]{2})/", "$1-$2 $3 $4 $5", $phone);
      }
    }

    // Return original phone if not 7, 10 or 11 digits long
    return $phone;
  }

  /**
   * Get the age of a person in years at a given time
   * @param string $dob  Date Of Birth
   * @return int      The number of years
   */
  public function getAge($dob) {
    if ($dob && $dob !== '00-00-0000') {
      //explode the date to get month, day and year
      $birthDate = explode("-", $dob);
      //get age from date or birthdate
      $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y") - $birthDate[2]) - 1) : (date("Y") - $birthDate[2]));
      return $age;
    } else {
      return false;
    }
  }

  /**
   * Get the formatted birthday of a member
   * @param string $dob
   * @param int $birthdateview
   * @return string|bool
   */
  public function getBirthdday($dob, $birthdateview = 1) {
    if ($dob && $dob !== "00-00-0000" && $dob !== "0000-00-00" && $birthdateview) {
      switch ($birthdateview) {
        case 0:
          $birthDate = "";
          break;
        case 1:
          $birthDate = $dob;
          break;
        case 2:
          //explode the date to get month, day and year
          $birthDate = explode("-", $dob);
          $birthDate = date("Y", mktime(0, 0, 0, $birthDate[1], $birthDate[0], $birthDate[2]));
          break;
      }

      return $birthDate;
    } else {
      return false;
    }
  }

  /**
   * refValues
   * @param array $arr
   * @return array
   */
  public function refValues($arr) {
    if (strnatcmp(phpversion(), '5.3') >= 0) { //Reference is required for PHP 5.3+
      $refs = array();
      foreach ($arr as $key => $value)
        $refs[$key] = &$arr[$key];
      return $refs;
    }
    return $arr;
  }

  /**
   * Executes the reportdata query
   * @param string $sql
   * @return object|NULL
   */
  public function getReportData($sql) {
    return $this->requestDataQuery($sql);
  }

  /**
   * Executes a query to change data in the database (i.e. INSERT, DELETE, UPDATE).
   * @param string $sql
   * @param array $params
   * @return int
   */
  private function changeDataQuery($sql, $params = array()) {
    $stmt = $this->executeQuery($sql, $params);
    $result = $stmt->affected_rows;
    if ($stmt->insert_id !== 0) {
      $this->last_insert_id = $stmt->insert_id;
    }
    $stmt->close();
    return $result;
  }

  /**
   * Executes a query to request data from the database.
   * @param string $sql
   * @param array $params
   * @param string $returnas
   * @return object|array|NULL
   */
  private function requestDataQuery($sql, $params = array(), $returnas = "object") {
    $stmt = $this->executeQuery($sql, $params);
    if (isset($_SESSION['num_query'])) {
      $_SESSION['num_query'] = $_SESSION['num_query'] + 1;
    }
    $results = array();
    $meta = $stmt->result_metadata();

    while ($field = $meta->fetch_field()) {
      $parameters[] = &$row[$field->name];
    }

    call_user_func_array(array($stmt, 'bind_result'), $this->refValues($parameters));

    while ($stmt->fetch()) {
      $x = array();
      foreach ($row as $key => $val) {
        $x[$key] = $val;
      }
      if ($returnas === "object") {
        $results[] = (object) $x;
      } else {
        $results[] = $x;
      }
    }

    if (count($results > 0)) {
      $result = $results;
    } else { // No Result
      $result = NULL;
    }

    if ($stmt->insert_id !== 0) {
      $this->last_insert_id = $stmt->insert_id;
    }

    $stmt->close();
    return $result;
  }

  /**
   * Prepares Query and executes the query and returns the result if necessary
   * @param string $sql
   * @param array $params
   * @return int|object|bool
   */
  private function executeQuery($sql, $params = array()) {
    // SET autocommit FALSE
    $this->autocommit(FALSE);

    //set prefix for tables
    if (isset($this->tables) && isset($this->tablesreplacement)) {
      $sql = preg_replace($this->tables, $this->tablesreplacement, $sql);
    }

    $stmt = $this->prepare($sql);

    if (!$stmt) {// Test if query is succesfull prepared
      trigger_error(serialize(array("errtype" => "E_USER_FAILPREPAREQUERY", "errno" => $this->errno, "error" => $this->error)), E_USER_ERROR);
    }

    if (count($params)) {
      call_user_func_array(array($stmt, 'bind_param'), $this->refValues($params));
    }

    // Run the query (temporarily) and check results
    if (!$stmt->execute()) {
      $this->rollback(); //ROLLBACK or COMMIT
      trigger_error(serialize(array("errtype" => "E_USER_FAILEXECUTEQUERY", "errno" => $this->errno, "error" => $this->error)), E_USER_ERROR);
    }

    return $stmt;
  }

  /**
   * Retrieves the lat / lon values from Google maps.
   * @param string $street
   * @param string $number
   * @param string $zip
   * @param string $city
   * @return bool|array
   */
  public function getLatLon($street, $number, $zip, $city) {
    require_once CLASSES_PATH . 'GoogleMaps.php';
    $map = new GoogleMaps;

    $searchaddress = $street . " " . $number . ", ";
    $zipcity = strtoupper($zip) . ", " . strtoupper($city);

    $coordinates = ($map->geoGetCoords($searchaddress, $zipcity));

    if ($coordinates === NULL) {
      $coordinates['lat'] = number_format(0, 7, '.', '');
      $coordinates['lon'] = number_format(0, 7, '.', '');
    }

    return $coordinates;
  }

  /**
   * Updates the geocode lat/lon points from addresses in database in pieces
   * @param int $start
   * @param int $limit
   * @return array
   */
  public function updateAddressCoordinates($start = 0, $limit = 10) {
    require_once CLASSES_PATH . 'GoogleMaps.php';
    $map = new GoogleMaps('Addresses');

    $query = "SELECT count(*) AS 'total' FROM `addresses` WHERE `ADR_archive` = 0";
    $totaladdresses = $this->requestDataQuery($query);
    $total = $totaladdresses[0]->total;

    $end = $start + $limit;
    $percentage = round(((100 * $end) / $total), 0);

    $query = "SELECT `addresses`.* FROM `addresses` WHERE `ADR_archive` = 0 LIMIT ? , ?";
    $addresses = $this->requestDataQuery($query, array('ii', $start, $limit));


    foreach ($addresses as $address) {
      $zipcity = $address->ADR_zip . ", " . $address->ADR_city;
      $searchaddress = $address->ADR_street . " " . $address->ADR_number;

      $coordinates = $map->geoGetCoords($searchaddress, $zipcity);

      if ($coordinates === NULL) {
        $coordinates['lat'] = number_format(0, 7, '.', '');
        $coordinates['lon'] = number_format(0, 7, '.', '');
      }

      $query = "UPDATE `addresses` SET `ADR_lat` = ?, `ADR_lng` = ? WHERE `addresses`.`ADR_id` = ? LIMIT 1 ;";
      $result = $this->changeDataQuery($query, array('ssi', $coordinates["lat"], $coordinates["lon"], $address->ADR_id));
    }

    usleep(500000); // wait .5 sec
    return array("end" => $end, "percentage" => $percentage, "total" => $total);
  }

  /**
   * getVersionInfo returns a formatted string with the current version
   * @return string
   */
  public function getVersionInfo() {
    $versionfile = 'http://churchmembers.svn.sourceforge.net/svnroot/churchmembers/churchmembers/.version';
    // this is the version of the deployed script
    $currentversion = $this->getSetting('system_version')->SETTINGS_value;
    $output = $currentversion;

    $remoteVersion = trim(file_get_contents($versionfile));
    $update = version_compare($remoteVersion, $currentversion); //returns -1 if the first version is lower than the second, 0 if they are equal, and 1 if the second is lower.

    if ($update === 1) {
      $output .= ' ' . sprintf(__("(<a href='http://sourceforge.net/projects/churchmembers/' target='_blank'>You are using an older version. Update now to version %s !</a>)"), $remoteVersion);
    } else {
      $output .= ' ' . __("(This is the latest version)");
    }

    return $output;
  }

  /**
   * Creates a backup of all tables OR just a table as an sql dump.
   * @param string $tables
   * @return string
   */
  public function createBackup($tables = '*') {
    $return = '';

    //get all of the tables
    if ($tables === '*') {
      $tables = array();
      $result = $this->requestDataQuery("SHOW TABLES", array());
      foreach ($result as $row) {
        foreach ($row as $field => $value) {
          $tables[] = $value;
        }
      }
    } else {
      $tables = is_array($tables) ? $tables : explode(',', $tables);
    }


    //cycle through
    foreach ($tables as $table) {
      $result = $this->requestDataQuery('SELECT * FROM ' . $table, array(), 'array');
      if (isset($result[0])) {
        $num_fields = count($result[0]);
        $return.= "--\n-- Structure for table `" . $table . "`\n--\n";
        $return.= 'DROP TABLE ' . $table . ";\n";
        $tablestructure = $this->requestDataQuery('SHOW CREATE TABLE ' . $table, array(), 'array');
        $return.= $tablestructure[0]['Create Table'] . ";\n\n";

        for ($i = 0; $i < $num_fields; $i++) {
          foreach ($result as $row) {

            $return.= 'INSERT INTO ' . $table . ' VALUES("';
            //
            $cleanvalues = array();
            foreach ($row as $field => $value) {
              $cleanvalues[] = preg_replace("/\n/", "\\n", addslashes($value));
            }
            $strvalues = implode('", "', $cleanvalues);

            $return.= $strvalues . "\");\n";
          }
        }
        $return.="\n\n\n";
      }
    }

    return $return;
  }

}

?>