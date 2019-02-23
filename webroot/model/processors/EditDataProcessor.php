<?php

/**
 * This processor handles a request for editing data.
 */
class EditDataProcessor extends AbstractProcessor {

  public function processRequest() {

    if (isset($_REQUEST['table'])) {
      $table = $_REQUEST['table'];
      $field = $_REQUEST['field'];

      switch ($table) {
        case "addresses":
          $RefField = "ADR_id";
          $Refid = $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id;
          break;

        case "members":
          $RefField = "MEMBER_id";
          $Refid = $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id;

          if ($field == 'MEMBER_photo') {
            $photo = $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_photo;
            @unlink(BASE_PATH . $photo);
          }

          break;

        case "events":
          $RefField = "EVENT_id";
          $Refid = $_SESSION['CURRENT-VIEW']['CURRENT_EVENT']->EVENT_id;
          $Ref_parent_id = $_SESSION['CURRENT-VIEW']['CURRENT_EVENT']->EVENT_parent_id;

          // edit parent events
          if ($_SESSION['USER']->checkUserrights('edit_mode')) {
            if (isset($table) && isset($RefField) && isset($Refid) && isset($field) && isset($_REQUEST['value'])) {
              $this->database->editDataVerify($Refid, "EVENT_parent_id", $table, $field, $_REQUEST['value']);
              $this->database->editDataVerify($Ref_parent_id, "EVENT_id", $table, $field, $_REQUEST['value']);

              if ($Ref_parent_id !== 0) {
                $this->database->editDataVerify($Ref_parent_id, "EVENT_parent_id", $table, $field, $_REQUEST['value']);
              }
            }
          }
          break;

        case "groups":
          $RefField = "GROUP_id";
          $Refid = (isset($_REQUEST['groupid'])) ? $_REQUEST['groupid'] : $_SESSION['CURRENT-VIEW']['CURRENT_GROUP']->GROUP_id;

          //$result = array("status" => 1);
          //print(json_encode($result));

          break;

        case "usertypes":
          $RefField = "USERTYPE_id";
          $Refid = $_SESSION['CURRENT-VIEW']['ADMIN']['CURRENT_USERTYPE']->USERTYPE_id;
          break;

        case "settings":
          $RefField = "SETTINGS_name";
          $Refid = $_REQUEST['field'];
          $field = "SETTINGS_value";
          break;
      }
    }

    $result = false;

    if ($_SESSION['USER']->checkUserrights('view_admin')) {
      if (isset($table) && isset($RefField) && isset($Refid) && isset($field) && isset($_REQUEST['value'])) {
        $result = $this->database->editDataVerify($Refid, $RefField, $_REQUEST['table'], $field, $_REQUEST['value']);
      }
    } elseif (($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->ADR_id == $_SESSION['USER']->getUserlinktomemberAdr_Id() && $table == 'members') ||
            $_SESSION['USER']->checkUserrights('edit_mode')) {
      if (isset($table) && isset($RefField) && isset($Refid) && isset($field) && isset($_REQUEST['value'])) {
        $result = $this->database->editDataVerify($Refid, $RefField, $_REQUEST['table'], $field, $_REQUEST['value']);
      }
    }
  }

}

?>