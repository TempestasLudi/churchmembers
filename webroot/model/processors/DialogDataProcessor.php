<?php

/**
 * This processor handles the dialogs.
 */
class DialogDataProcessor extends AbstractProcessor {

  private $dialog;
  private $type;
  private $check;
  private $dialogtemplate;
  public $jsonreturn;
  public $succesmsg;
  public $nexturl;
  private $eventtypes;
  private $callback = array('ADR_id' => false, 'MEMBER_id' => false);

  public function processRequest() {
    $this->dialog = $_REQUEST['dialog'];
    $this->type = $_REQUEST['type'];
    $this->check = ($_REQUEST['check'] === '1') ? true : false;

    switch ($this->dialog) {
      case 'member':
        switch ($this->type) {
          case 'add':
            $this->addMemberDialog();
            break;
          case 'move':
            $this->moveMemberDialog();
            break;
          case 'unscribe':
            $this->unscribeMemberDialog();
            break;
        }
        break;

      case 'address':
        switch ($this->type) {
          case 'add':
            $this->addAddressDialog();
            break;
          case 'move':
            $this->moveAddressDialog();
            break;
          case 'unscribe':
            $this->unscribeAddressDialog();
            break;
        }
        break;

      case 'group':
        switch ($this->type) {
          case 'add':
            $this->addGroup();
            break;
          case 'delete':
            $this->deleteGroup();
            break;
          case 'address':
            $this->changeAddressGroupsDialog();
            break;
        }
        break;

      case 'event':
        switch ($this->type) {
          case 'delete':
            $this->deleteEvent();
            break;
          default;
            $this->eventDialog($this->type);
            break;
        }
    }
  }

  private function addMemberDialog() {

    if ($this->check) {
      //Check input, on error it exits
      $this->checkLength('newMemberFirstName', 3, 60);
      $this->checkLength('newMemberFamilyname', 3, 60);
      $this->checkLength('newMemberGender', 1, 60);
      $this->checkLength('newParent', 1, 60);

      //Add member
      $result = $this->database->addMember($_REQUEST['newMemberFirstName'], $_REQUEST['newMemberChristianName'], $_REQUEST['newMemberInitials'], $_REQUEST['newMemberFamilyname'], $_REQUEST['newMemberFamilyname_preposition'], $_REQUEST['newMemberBirthdate'], $_REQUEST['newMemberBirthplace'], $_REQUEST['newMemberGender'], $_REQUEST['newMemberMobile'], $_REQUEST['newMemberEmail'], $_REQUEST['newParent'], $_REQUEST['newMembertype_id']);

      if ($result) {
        //Send succes message

        $this->callback['ADR_id'] = $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->ADR_id;
        $this->callback['MEMBER_id'] = $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id;

        $this->succesmsg = __("Member is successful added");
        $this->nexturl = array('dialog' => 'event', 'change' => 'addmember');
        $this->returnSucces();
      } else {
        //Send error message
        $this->error = array('field' => '', 'msg' => __("Event is <b>not</b> successful added. Try again."));
        $this->nexturl = '';
        $this->returnError();
      }
    } else {
      $this->printTemplate("ADDMEMBER", $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']);
    }
  }

  private function moveMemberDialog() {

    if ($this->check) {
      $this->checkLength("moveMemberFamilyname", 3, 60);
      $this->checkLength("moveMemberToAddressId", 1, 60);

      //Move member
      $result = $this->moveMember();

      if ($result) {
        //Send succes message
        $this->succesmsg = __("Member is successful moved");
        $this->nexturl = array('dialog' => 'event', 'change' => 'movemember');
        $this->returnSucces();
      } else {
        //Send error message
        $this->error = array('field' => '', 'msg' => __("Event is <b>not</b> successful added. Try again."));
        $this->returnError();
      }
    } else {
      $this->printTemplate("MOVEMEMBER", $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']);
    }
  }

  private function unscribeMemberDialog() {

    if ($this->check) {

      if ($_REQUEST['unscribeMEMBER_action'] === 'deleteMember') {
        //Check event data
        $eventcheck = $this->eventDialog('deletemember');

        $result = $this->deleteMember();

        if ($result) {
          $this->succesmsg = __("Member is successful deleted");
          $this->callback['ADR_id'] = $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id;
          $this->callback['MEMBER_id'] = false;
          $this->nexturl = '';
          $this->returnSucces();
        } else {
          //Send error message
          $this->callback['ADR_id'] = $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id;
          $this->callback['MEMBER_id'] = false;
          $this->error = array('field' => '', 'msg' => __("Member is <b>not</b> successful deleted"));
          $this->returnError();
        }
      } else if ($_REQUEST['unscribeMEMBER_action'] === 'archiveMember') {
        //Check event data
        $eventcheck = $this->eventDialog('unscribemember');

        //Archive member + address if needed
        $result = $this->database->setArchiveStatus('members', $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id, 'MEMBER_id', "MEMBER_archive", 1);

        if ($result) {
          $this->nexturl = '';
          $this->returnSucces();
        } else {
          //Send error message
          $this->error = array('field' => '', 'msg' => __("Member is <b>not</b> successful archived"));
          $this->returnError();
        }
      }
    } else {
      $this->printTemplate("UNSCRIBEMEMBER", $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']);
      $this->eventDialog('unscribemember');
    }
  }

  private function addAddressDialog() {

    if ($this->check) {
      $this->checkLength("newAddressFamilyname", 3, 60);
      $this->checkLength("newAddressStreet", 3, 60);
      $this->checkLength("newAddressNumber", 1, 10);
      $this->checkLength("newAddressZip", 6, 7);
      $this->checkLength("newAddressCity", 3, 40);
      if ($_REQUEST['newAddressEmail'] !== '')
        $this->checkMail("newAddressEmail");

      //add address
      $result = $this->database->addAddress($_REQUEST['newAddressFamilyname'], $_REQUEST['newAddressFamilyname_preposition'], $_REQUEST['newAddressStreet'], $_REQUEST['newAddressNumber'], $_REQUEST['newAddressStreet_extra'], $_REQUEST['newAddressZip'], $_REQUEST['newAddressCity'], $_REQUEST['newAddressCountry'], $_REQUEST['newAddressTelephone'], $_REQUEST['newAddressEmail']);

      if ($result) {
        //Send succes message
        $this->succesmsg = __("Address is successful added");
        $this->nexturl = array('dialog' => 'member', 'change' => 'add');
        $this->callback['ADR_id'] = $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id;
        $this->callback['MEMBER_id'] = $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id;
        $this->returnSucces();
      } else {
        //Send error message
        $this->error = array('field' => '', 'msg' => __("Event is <b>not</b> successful added. Try again."));
        $this->returnError();
      }
    } else {
      $this->printTemplate("ADDADDRESS", $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']);
    }
  }

  private function moveAddressDialog() {

    if ($this->check) {
      $this->checkLength("moveAddressFamilyname", 3, 60);
      $this->checkLength("moveAddressStreet", 3, 60);
      $this->checkLength("moveAddressNumber", 1, 10);
      $this->checkLength("moveAddressZip", 6, 7);
      $this->checkLength("moveAddressCity", 3, 40);

      //Move address
      $result = $this->moveAddress();

      if ($result) {
        //Send succes message
        $this->succesmsg = __("Address is successful moved");
        $this->nexturl = array('dialog' => 'event', 'change' => 'moveaddress');
        $this->returnSucces();
      } else {
        //Send error message
        $this->error = array('field' => '', 'msg' => __("Event is <b>not</b> successful added. Try again."));
        $this->returnError();
      }
    } else {
      $this->printTemplate("MOVEADDRESS", $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']);
    }
  }

  private function unscribeAddressDialog() {

    if ($this->check) {
      if ($_REQUEST['unscribeADDRESS_action'] === 'deleteAddress') {
        //Check event data
        $eventcheck = $this->eventDialog('deleteaddress');

        $result = $this->deleteAddress();

        if ($result) {
          //Send succes message
          $this->succesmsg = __("Data successful deleted");
          $this->nexturl = '';
          $this->callback['ADR_id'] = false;
          $this->callback['MEMBER_id'] = false;
          $this->returnSucces();
        } else {
          //Send error message
          $this->error = array('field' => '', 'msg' => __("Address is <b>not</b> successful deleted"));
          $this->returnError();
        }
      } else if ($_REQUEST['unscribeADDRESS_action'] === 'archiveAddress') {
        //Check event data
        $eventcheck = $this->eventDialog('unscribeaddress');

        //Set archivestatus address + members if needed
        if (!$_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_archive)
          $result = $this->database->setArchiveStatus("addresses", $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id, "ADR_id", "ADR_archive", 1);

        if ($result) {
          //Send succes message
          $this->succesmsg = __("Address is successful archived");
          $this->nexturl = array('dialog' => '', 'change' => '');
          $this->callback['ADR_id'] = false;
          $this->callback['MEMBER_id'] = false;
          $this->returnSucces();
        } else {
          //Send error message
          $this->error = array('field' => '', 'msg' => __("Address is <b>not</b> successful archived"));
          $this->returnError();
        }
      }
    } else {
      $this->printTemplate("UNSCRIBEADDRESS", $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']);
      $this->eventDialog('unscribeaddress');
    }
  }

  private function eventDialog($eventtype) {

    switch ($eventtype) {
      case 'addevent':
        $this->eventtypes = array('EVENT_ADD_GUESTMEMBERSHIP', 'EVENT_ADD_BIRTH_TESTIMONY', 'EVENT_ADD_CONFESSION_TESTIMONY',
            'EVENT_CONTINUE_STAY_TESTIMONY', 'EVENT_CONTINUE_GUESTMEMBERSHIP', 'EVENT_SICK',
            'EVENT_BAPTISED', 'EVENT_CONFESSION', 'EVENT_MARRIAGE');
        break;

      case 'addmember':
        $this->eventtypes = array(
            'EVENT_ADD_BIRTH_TESTIMONY', 'EVENT_ADD_TRAVEL_TESTIMONY', 'EVENT_ADD_STAY_TESTIMONY',
            'EVENT_ADD_CONFESSION_TESTIMONY', 'EVENT_ADD_GUESTMEMBERSHIP', 'EVENT_ADD_MEMBERSHIP',
            'EVENT_ADD_NEWMEMBER', 'EVENT_BIRTH');
        break;

      case 'movemember':
        $this->eventtypes = array('EVENT_MOVED', 'EVENT_MARRIAGE', 'EVENT_DIVORCE');
        break;

      case 'unscribemember':
      case 'deletemember':
        $this->eventtypes = array(
            'EVENT_MOVED_BIRTH_TESTIMONY', 'EVENT_MOVED_TRAVEL_TESTIMONY', 'EVENT_MOVED_STAY_TESTIMONY',
            'EVENT_MOVED_CONFESSION_TESTIMONY', 'EVENT_MOVED_GUESTMEMBERSHIP', 'EVENT_END_GUESTMEMBERSHIP',
            'EVENT_MOVED_ABROAD', 'EVENT_GONE', 'EVENT_DIED', 'EVENT_NOREASON');
        break;

      case 'moveaddress':
        $this->eventtypes = array('EVENT_MOVED', 'EVENT_CHANGED_HOME_EMAIL', 'EVENT_CHANGED_HOME_PHONE', 'EVENT_NOREASON');
        break;

      case 'unscribeaddress':
      case 'deleteaddress':
        $this->eventtypes = array(
            'EVENT_MOVED_TRAVEL_TESTIMONY', 'EVENT_MOVED_STAY_TESTIMONY', 'EVENT_MOVED_CONFESSION_TESTIMONY',
            'EVENT_MOVED_GUESTMEMBERSHIP', 'EVENT_END_GUESTMEMBERSHIP', 'EVENT_MOVED_ABROAD',
            'EVENT_GONE', 'EVENT_DIED', 'EVENT_NOREASON');
        break;
    }

    if ($this->check) {

      //Check data;
      $this->checkLength("newEventType", 1, 60);

      if ($_REQUEST['newEventType'] !== "EVENT_NOREASON")
        $this->checkLength("newEventDate", 10, 10, tipElementId);

      if (in_array($_REQUEST['newEventType'], array('EVENT_CONFESSION', 'EVENT_BAPTISED', 'EVENT_MARRIAGE')))
        $this->checkLength("newEventCity", 1, 256);

      if (in_array($_REQUEST['newEventType'], array('EVENT_CONFESSION', 'EVENT_BAPTISED', 'EVENT_MARRIAGE')))
        $this->checkLength("newEventChurch", 1, 256);

      if (in_array($_REQUEST['newEventType'], array('EVENT_DIVORCE', 'EVENT_MARRIAGE')))
        $this->checkLength("newEventPartnerName", 1, 256);

      // Add event
      $this->eventreason = $eventtype;
      $result = $this->addEvent();

      // Add callback
      switch ($eventtype) {
        case 'addevent':
          $this->callback['ADR_id'] = $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->ADR_id;
          $this->callback['MEMBER_id'] = $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id;
          break;

        case 'addmember':
        case 'movemember':
          $this->callback['ADR_id'] = $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->ADR_id;
          $this->callback['MEMBER_id'] = $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id;
          break;

        case 'unscribemember':
        case 'deletemember':
          $this->callback['ADR_id'] = $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id;
          break;
      }

      if ($eventtype === 'unscribeaddress' || $eventtype === 'unscribemember' || $eventtype === 'deleteaddress' || $eventtype === 'deletemember') {
        return true;
      }

      if ($result) {
        //Send succes message
        $this->succesmsg = __("Event is successful added");
        $this->nexturl = ($eventtype === 'addmember') ? array('dialog' => 'member', 'change' => 'add') : '';
        $this->returnSucces();
      } else {
        //Send error message
        $this->error = array('field' => '', 'msg' => __("Event is <b>not</b> successful added. Try again."));
        $this->returnError();
      }
    } else {
      //Set eventtype names
      $this->eventtypenames = array();
      foreach ($this->eventtypes as $event_type) {
        $this->eventtypenames[$event_type] = $this->getEventTypeTranslation($event_type);
      }

      $this->printTemplate("EVENTS", $this->eventtypenames);
    }
  }

  private function changeAddressGroupsDialog() {

    if ($this->check) {

      //Send succes message
      $this->succesmsg = __("Your changes are saved.");
      $this->nexturl = array('dialog' => '', 'change' => '');
      $this->callback['ADR_id'] = $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->ADR_id;
      $this->callback['MEMBER_id'] = $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id;
      $this->returnSucces();
    } else {
      $this->printTemplate("ADDRESSGROUPS", $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']);
    }
  }

  private function moveMember() {
    // Get current member
    $memberId = $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id;
    $addressId = $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id;

    // Move member to new address
    $result = $this->database->editDataNoVerify($memberId, "MEMBER_id", "members", "ADR_id", $_REQUEST['moveMemberToAddressId']);
    if (!$result === 1) {
      return false;
    }
    $this->database->updateEventsMember($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER'], true);

    // If member is first on address, set parent to 1
    $firstmember = $this->database->getFirstMemberIdOfAddress($_REQUEST['moveMemberToAddressId']);
    if (isset($firstmember->MEMBER_id) && $firstmember->MEMBER_id === $memberId) {
      $result = $this->database->editDataNoVerify($memberId, "MEMBER_id", "members", "MEMBER_parent", 1);
    }

    // Delete old address if empty
    $membersonaddress = $this->database->getMembersForAddress($addressId, 0);
    $membersonaddress_archive = $this->database->getMembersForAddress($addressId, 1);

    if ((count($membersonaddress) === 0) && (count($membersonaddress_archive) === 0)) {
      $this->deleteAddress($addressId);
    } elseif ((count($membersonaddress) === 0) && (count($membersonaddress_archive) > 0)) {
      $result = $this->database->editDataNoVerify($addressId, "ADR_id", "addresses", "ADR_archive", 1);
    }

    // Update current session information
    $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER'] = $this->database->getMemberById($memberId);
    $_SESSION['CURRENT-VIEW']['MEMBERS_IN_ADDRESS'] = $this->database->getMembersForAddress($_REQUEST['moveMemberToAddressId']);

    // Set callback
    $this->callback['ADR_id'] = $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->ADR_id;
    $this->callback['MEMBER_id'] = $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id;

    return true;
  }

  private function moveAddress() {
    //If Edit/Move Address if necessary move address
    if ($_REQUEST['moveAddressFamilyname'] != $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_familyname ||
            $_REQUEST['moveAddressFamilyname_preposition'] != $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_familyname_preposition ||
            $_REQUEST['moveAddressStreet'] != $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_street ||
            $_REQUEST['moveAddressNumber'] != $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_number ||
            $_REQUEST['moveAddressStreet_extra'] != $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_street_extra ||
            $_REQUEST['moveAddressZip'] != $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_zip ||
            $_REQUEST['moveAddressCity'] != $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_city ||
            $_REQUEST['moveAddressCountry'] != $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_country ||
            $_REQUEST['moveAddressTelephone'] != $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_telephone ||
            $_REQUEST['moveAddressEmail'] != $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_email
    ) {

      $result = $this->database->editDataAddress($_REQUEST['moveAddressFamilyname'], $_REQUEST['moveAddressFamilyname_preposition'], $_REQUEST['moveAddressStreet'], $_REQUEST['moveAddressNumber'], $_REQUEST['moveAddressStreet_extra'], $_REQUEST['moveAddressZip'], $_REQUEST['moveAddressCity'], $_REQUEST['moveAddressCountry'], $_REQUEST['moveAddressTelephone'], $_REQUEST['moveAddressEmail']);

      if (!$result === 1) {
        return false;
      }

      // Set callback
      $this->callback['ADR_id'] = $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id;
    }

    return true;
  }

  private function deleteMember() {
    // set fullnames in events table
    $this->database->updateEventsMember($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']);

    // Delete member
    if ($result = $this->database->deleteDataNoVerify($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id, 'MEMBER_id', 'members')) {
      // Delete linked groups
      $this->database->deleteDataNoVerify($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id, "GROUPMEMBERS_memberid", 'groupmembers');
      $_SESSION['CURRENT-VIEW']['MEMBERS_IN_ADDRESS'] = $this->database->getMembersForAddress($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->ADR_id, $_SESSION['ARCHIVE-MODE']);

      // Delete old address if empty
      $membersonaddress = $this->database->getMembersForAddress($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->ADR_id, 0);
      $membersonaddress_archive = $this->database->getMembersForAddress($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->ADR_id, 1);

      if ((count($membersonaddress) === 0) && (count($membersonaddress_archive) === 0)) {
        $this->deleteAddress();
      } elseif ((count($membersonaddress) === 0) && (count($membersonaddress_archive) > 0)) {
        $result = $this->database->editDataNoVerify($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->ADR_id, "ADR_id", "addresses", "ADR_archive", 1);
      }

      return true;
    } else {
      return false;
    }
  }

  private function deleteAddress($adr_id = false) {

    if (!$adr_id) {
      // Delete members in address
      $members = $_SESSION['CURRENT-VIEW']['MEMBERS_IN_ADDRESS'];

      foreach ($members as $member) {
        // Set fullnames in events table
        $this->database->updateEventsMember($member);

        // Delete member
        $deleteMemberResult = $this->database->deleteDataNoVerify($member->MEMBER_id, "MEMBER_id", "members");
        if ($deleteMemberResult <= 0) {
          trigger_error(serialize(array("errtype" => "E_USER_FAILDELETE_MEMBER")), E_USER_ERROR);
        } else {
          // Delete linked groups
          $this->database->deleteDataNoVerify($member->MEMBER_id, "GROUPMEMBERS_memberid", 'groupmembers');
        }
      }
    }

    $address = ($adr_id) ? $this->database->getAddressById($adr_id) : $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS'];
    // Set address in events table
    $this->database->updateEventsAddress($address);

    // Delete linked address groups
    $this->database->deleteDataNoVerify($address->ADR_id, "GROUPADDRESSES_addressid", 'groupaddresses');

    // Delete address
    return $this->database->deleteDataNoVerify($address->ADR_id, 'ADR_id', 'addresses');
  }

  private function addEvent() {
    $newEventType = $_REQUEST['newEventType'];
    $newEventDate = isset($_REQUEST['newEventDate']) ? $_REQUEST['newEventDate'] : date("Y-m-d");
    $newEventCity = $_REQUEST['newEventCity'];
    $newEventChurch = $_REQUEST['newEventChurch'];
    $newEventMembertype_id = $_REQUEST['newMembertype_id'];
    $newEventPartnerId = $_REQUEST['newEventPartnerId'];
    $newEventNote = $_REQUEST['newEventNote'];
    $newEventReason = $this->eventreason;

    // Depending of the eventType, some fields need to be set
    // Ref fields
    $RefField = "MEMBER_id";
    $Refid = $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id;
    $member_adr_id = $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id;

    switch ($newEventType) {

      case 'EVENT_BIRTH':
        $this->database->editDataVerify($Refid, $RefField, 'members', 'MEMBER_birthdate', $newEventDate);
        if (isset($newEventCity)) {
          $this->database->editDataVerify($Refid, $RefField, 'members', 'MEMBER_birthplace', $newEventCity);
        }
        $newEventNote = $newEventCity;
        break;

      case 'EVENT_CONFESSION':
        $this->database->editDataVerify($Refid, $RefField, 'members', 'MEMBER_confessiondate', $newEventDate);
        $this->database->editDataVerify($Refid, $RefField, 'members', 'MEMBER_confessionchurch', $newEventChurch);
        $this->database->editDataVerify($Refid, $RefField, 'members', 'MEMBER_confessioncity', $newEventCity);
        $this->database->editDataVerify($Refid, $RefField, 'members', 'MEMBER_membertype_id', $newEventMembertype_id);
        $newEventNote = $newEventChurch . " (" . $newEventCity . ")";
        break;

      case 'EVENT_BAPTISED':
        $this->database->editDataVerify($Refid, $RefField, 'members', 'MEMBER_baptismdate', $newEventDate);
        $this->database->editDataVerify($Refid, $RefField, 'members', 'MEMBER_baptismchurch', $newEventChurch);
        $this->database->editDataVerify($Refid, $RefField, 'members', 'MEMBER_baptismcity', $newEventCity);
        $this->database->editDataVerify($Refid, $RefField, 'members', 'MEMBER_membertype_id', $newEventMembertype_id);
        $newEventNote = $newEventChurch . " (" . $newEventCity . ")";
        break;

      case 'EVENT_MARRIAGE':
        $this->database->editDataVerify($Refid, $RefField, 'members', 'MEMBER_mariagedate', $newEventDate);
        $this->database->editDataVerify($Refid, $RefField, 'members', 'MEMBER_mariagechurch', $newEventChurch);
        $this->database->editDataVerify($Refid, $RefField, 'members', 'MEMBER_mariagecity', $newEventCity);

        // If eventtype is marriage, move partner to new address
        if ($newEventPartnerId !== '') {
          // Also set field for partner
          $this->database->editDataVerify($newEventPartnerId, $RefField, 'members', 'MEMBER_mariagedate', $newEventDate);
          $this->database->editDataVerify($newEventPartnerId, $RefField, 'members', 'MEMBER_mariagechurch', $newEventChurch);
          $this->database->editDataVerify($newEventPartnerId, $RefField, 'members', 'MEMBER_mariagecity', $newEventCity);

          $partner = $this->database->getMemberById($newEventPartnerId);

          if ($partner->ADR_id !== $member_adr_id) {
            $result = $this->database->editDataVerify($newEventPartnerId, "MEMBER_id", "members", "ADR_id", $member_adr_id);
            $this->database->updateEventsMember($partner, true);

            // Delete address of partner is empty
            $membersonaddress_partner = $this->database->getMembersForAddress($partner->ADR_id);
            $membersonaddress_partner_archive = $this->database->getMembersForAddress($partner->ADR_id, 1);

            if ((count($membersonaddress_partner) === 0) && (count($membersonaddress_partner_archive) === 0)) {
              $this->deleteAddress($partner->ADR_id);
            } elseif ((count($membersonaddress_partner) === 0) && (count($membersonaddress_partner_archive) > 0)) {
              $result = $this->database->editDataNoVerify($partner->ADR_id, "ADR_id", "addresses", "ADR_archive", 1);
            }
          }
          // Add event for partner
          $event_parent_id = $this->database->addEvent($newEventType, 0, $newEventDate, $newEventNote, $newEventPartnerId, $member_adr_id);
        }

        $newEventNote = $newEventChurch . " (" . $newEventCity . ")";
        break;
    }

    //Add event
    switch ($newEventReason) {
      case 'unscribeaddress': //Add event for members in address if reason is archive address
      case 'deleteaddress': //Add event for members in address if reason is delete address
      case 'moveaddress': // Add event for members in address if reason is move address

        $members = $_SESSION['CURRENT-VIEW']['MEMBERS_IN_ADDRESS'];
        $event_parent_id = '';
        foreach ($members as $member) {
          $result = $this->database->addEvent($newEventType, $event_parent_id, $newEventDate, $newEventNote, $member->MEMBER_id, $member->ADR_id);
          if ($event_parent_id == '') {
            $event_parent_id = $result;
          }
          if ($result <= 0) {
            trigger_error(serialize(array("errtype" => "E_USER_FAIL_ADD_EVENT")), E_USER_ERROR);
          }
        }
        break;
      case 'unscribemember':
      case 'deletemember':
      case 'addmember':
      case 'movemember':
      case 'addevent':
        if ($newEventPartnerId !== '') {
          $result = $this->database->addEvent($newEventType, $event_parent_id, $newEventDate, $newEventNote);
        } else {
          $result = $this->database->addEvent($newEventType, 0, $newEventDate, $newEventNote);
        }

        break;
    }

    return $result;
  }

  private function deleteEvent() {
    if ($this->check) {
      $Refid = $_SESSION['CURRENT-VIEW']['CURRENT_EVENT']->EVENT_id;

      $result = $this->database->deleteDataNoVerify($Refid, 'EVENT_id', 'events');
      $result_related = $this->database->deleteDataNoVerify($Refid, 'EVENT_parent_id', 'events');

      $this->callback['ADR_id'] = $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id;
      $this->callback['MEMBER_id'] = $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id;

      if ($result) {
        //Send succes message
        $this->succesmsg = __("Data successful deleted");
        $this->nexturl = array();
        $this->returnSucces();
      } else {
        //Send error message
        $this->error = array('field' => '', 'msg' => __("Event is <b>not</b> successful deleted. Try again."));
        $this->nexturl = '';
        $this->returnError();
      }
    } else {
      print(__("You are about to delete this event is. This action cannot be undone. Do you want to continue?"));
      // show delete
    }
  }

  private function addGroup() {
    //Add Group

    $title = isset($_REQUEST['title']) ? $_REQUEST['title'] : false;
    $parentid = isset($_REQUEST['parentid']) ? $_REQUEST['parentid'] : false;

    if ($title && $parentid)
      $result = $this->database->addGroup($title, $parentid);

    if ($result) {
      //Send succes message
      $this->callback['GROUP_id'] = $_SESSION['CURRENT-VIEW']['CURRENT_GROUP']->GROUP_id;
      $this->succesmsg = __("Group is successful added");
      $this->nexturl = array('dialog' => 'event', 'change' => 'addmember');
      $this->returnSucces();
    } else {
      //Send error message
      $this->error = array('field' => '', 'msg' => __("Group is <b>not</b> successful added. Try again."));
      $this->nexturl = '';
      $this->returnError();
    }
  }

  private function deleteGroup() {
    $Refid = $_REQUEST['groupid'];
    $parentgroups = $this->database->getGroupsFromParent($Refid);

    if (count($parentgroups) === 0) {
      // Delete group
      $result = $this->database->deleteDataNoVerify($Refid, "GROUP_id", 'groups');

      if ($result) {
        // Delete member-group events
        $resultmembers = $this->database->deleteDataNoVerify($Refid, "GROUPMEMBERS_groupid", 'groupmembers');

        if ($result) {
          //Send succes message
          $this->succesmsg = __("Group is successful deleted");
          $this->nexturl = array();
          $this->returnSucces();
        } else {
          //Send error message
          $this->error = array('field' => '', 'msg' => __("Not all members are removed from the group. Try again."));
          $this->nexturl = '';
          $this->returnError();
        }
      } else {
        //Send error message
        $this->error = array('field' => '', 'msg' => __("Group is <b>not</b> successful deleted. Try again."));
        $this->nexturl = '';
        $this->returnError();
      }
    } else {
      //Send error message
      $this->error = array('field' => '', 'msg' => __("This group is not empty. Delete all other groups first."));
      $this->nexturl = '';
      $this->returnError();
    }
  }

  private function printTemplate($template, $placeholders) {
    $this->dialogtemplate = new TemplateParser($template, $placeholders, $this->database);
    return print_r($this->dialogtemplate->parseOutput());
  }

  private function returnSucces() {
    $this->jsonreturn = array('result' => 'succes',
        'msg' => $this->succesmsg,
        'dialog' => isset($this->nexturl['dialog']) ? $this->nexturl['dialog'] : '',
        'action' => isset($this->nexturl['change']) ? $this->nexturl['change'] : '',
        'ADR_id' => isset($this->callback['ADR_id']) ? $this->callback['ADR_id'] : false,
        'MEMBER_id' => isset($this->callback['MEMBER_id']) ? $this->callback['MEMBER_id'] : false,
        'GROUP_id' => isset($this->callback['GROUP_id']) ? $this->callback['GROUP_id'] : false);
    header('Content-type: application/json');
    print_r(json_encode($this->jsonreturn));
    exit();
  }

  private function returnError() {
    $this->jsonreturn = array('result' => 'fail', 'error' => $this->error);
    header('Content-type: application/json');
    print_r(json_encode($this->jsonreturn));
    exit();
  }

  /**
   * Checks whether the provided data has the appropriate length.
   * If not a tooltip is shown. wrongFieldSize is localized
   * parameter.
   */
  public function checkLength($field, $min, $max) {
    $errfieldlength = __("Use between %s and %s characters");
    $length = strlen($_REQUEST[$field]);

    if ($length > $max || $length < $min) {
      $this->error = array('field' => $field, 'msg' => sprintf($errfieldlength, $min, $max));
      $this->returnError();
      return true;
    }

    return false;
  }

  /**
   * Checks whether the provided data has the appropriate format.
   * If not a tip is shown.
   */
  public function checkMail($field) {
    $pregresult = preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/", $_REQUEST[$field]);

    if ($pregresult !== 1) {
      $this->error = array('field' => $field, 'msg' => __("Please use a valid emailaddress. ex: 'example@domain.com'"));
      $this->returnError();
      return true;
    }

    return false;
  }

}

?>