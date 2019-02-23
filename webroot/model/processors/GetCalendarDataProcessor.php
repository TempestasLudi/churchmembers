<?php

/**
 * This processor handles a request for getting an address.
 */
class GetCalendarDataProcessor extends AbstractProcessor {

  public function processRequest() {

    ///////////////////////// GET EVENTS FROM DATABASE /////////////////////////
    $startUnixDate = $_REQUEST['start'];
    $endUnixDate = $_REQUEST['end'];

    $members = $this->database->getBirthdayEvents($startUnixDate, $endUnixDate);
    $events = array();

    foreach ($members as $member) {

      $generatedmembername = $this->database->generateFullMemberName($member, false, true, '', false, true, true);
      if ($member->MEMBER_familynameview === 5 || $member->MEMBER_familynameview === 6 || $member->MEMBER_familynameview === 7 || $member->MEMBER_familynameview === 8 || !$member->MEMBER_firstname) {
        $membername = "<b>" . $generatedmembername . "</b>";
      } else {
        $membername = "<b>" . $member->MEMBER_firstname . "</b><br/>" . $generatedmembername;
      }

      $event = array("id" => $member->MEMBER_id, "title" => $membername, "start" => $member->eventDate, "url" => "#", "ADRid" => $member->ADR_id, "MEMBERid" => $member->MEMBER_id);
      array_push($events, $event);
    }
    print(json_encode($events));
  }

}

?>