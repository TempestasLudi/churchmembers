<?php

/**
 * This processor handles a request for sorting the members.
 */
class SortMemberDataProcessor extends AbstractProcessor {

  public function processRequest() {

    $query = $_POST['query'];
    $Sortresult = explode("&", $query);

    foreach ($Sortresult as $i => $value) {
      $value = str_replace("Sortresult[]=", "", $value);
      $res = $this->database->editDataNoVerify($value, "MEMBER_id", "members", "MEMBER_rank", $i);
      if ($res === NULL) {
        trigger_error(serialize(array("errtype" => "E_USER_FAIL_SORT_MEMBERS")), E_USER_ERROR);
        break;
      }
    }
  }

}

?>