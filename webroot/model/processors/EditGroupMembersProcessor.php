<?php

/**
 * This processor handles a request for setting group membership of the specified member.
 */
class EditGroupMembersProcessor extends AbstractProcessor {

  public function processRequest() {

    if (isset($_REQUEST['groups'])) {

      switch ($_REQUEST['grouptype']) {

        case 'members':

          ///////////////////////// DELETE GROUPS FOR MEMBERS /////////////////////////
          $this->database->deleteGroupsForMember($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id);

          ///////////////////////// SET NEW GROUPS FOR MEMBERS /////////////////////////
          $newgroups = explode(",", $_REQUEST['groups']);
          foreach ($newgroups as $group) {
            if (strlen($group) > 0) {
              $this->database->createGroupMember($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id, $group);
            }
          }

          break;

        case 'addresses':

          ///////////////////////// DELETE GROUPS FOR ADDRESS /////////////////////////
          $this->database->deleteGroupsForAddress($_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id);

          ///////////////////////// SET NEW GROUPS FOR ADDRESS /////////////////////////
          $newgroups = explode(",", $_REQUEST['groups']);
          foreach ($newgroups as $group) {
            if (strlen($group) > 0) {
              $this->database->createGroupAddress($_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id, $group);
            }
          }

          break;
      }
    }
  }

}

?>