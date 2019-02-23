<?php

/**
 * Base class for all the admin processors.
 */
class AdminProcessor extends AbstractProcessor {

  function __construct() {
    parent::__construct();
  }

  /**
   * Processes the request.
   */
  public function processRequest() {
    if (isset($_REQUEST['type'])) {
      $type = $_REQUEST['type'];

      switch ($type) {
        case 'template':

          switch($_REQUEST['template']) {

            case 'adminmenu':
              $_SESSION['CURRENT-VIEW']['ADMIN'] = "";
              $contentPlaceholders = "";
              $ADMINMENUtemplate = new TemplateParser("ADMINMENU", $contentPlaceholders, $this->database);
              print_r($ADMINMENUtemplate->parseOutput());
              break;

            case 'admincontent':
              $_SESSION['CURRENT-VIEW']['ADMIN'] = "";
              $contentPlaceholders = "";
              $ADMINtemplate = new TemplateParser("ADMIN", $contentPlaceholders, $this->database);
              print_r($ADMINtemplate->parseOutput());
              break;

            case 'userrights':
              $usertypeid = (isset($_REQUEST["id"])) ? $_REQUEST["id"] : false ;
              $usertype = $this->database->getUsertypeById($usertypeid);
              $_SESSION['CURRENT-VIEW']['ADMIN']['CURRENT_USERTYPE'] = $usertype;
              $userrights = json_decode($usertype->USERTYPE_rights, true);
              $contentPlaceholders = (object) array_merge((array) $usertype, $userrights);
              $TMPtemplate = new TemplateParser("USERRIGHTS", $contentPlaceholders, $this->database);
              print_r($TMPtemplate->parseOutput());
              break;

            case 'maintenance':
              $contentPlaceholders = "";
              $MAINTENANCE = new TemplateParser("MAINTENANCE", $contentPlaceholders, $this->database);
              print_r($MAINTENANCE->parseOutput());
              break;

            case 'settings':
              $contentPlaceholders = $_SESSION['SYSTEMSETTINGS'];
              $SETTINGS = new TemplateParser("SETTINGS", $contentPlaceholders, $this->database);
              print_r($SETTINGS->parseOutput());
              break;

            case 'failedlogin':
              $TMPtemplate = new TemplateParser("FAILEDLOGIN", "", $this->database);
              print_r($TMPtemplate->parseOutput());
              break;
          }

        case 'failedlogin':
          if (isset($_REQUEST["edit"])) {
            switch ($_REQUEST["edit"]) {
              case "delete":
                $this->database->deleteDataNoVerify($_REQUEST["id"], "FAILEDACCESS_id", "failedaccess");
                $TMPtemplate = new TemplateParser("FAILEDLOGIN", "", $this->database);
                print_r($TMPtemplate->parseOutput());
                break;
            }
          }

        case 'usertypes':
          if (isset($_REQUEST["process"])) {
            switch ($_REQUEST["process"]) {
              case "setuserrights":
                $usertypeid = $_SESSION['CURRENT-VIEW']['ADMIN']['CURRENT_USERTYPE']->USERTYPE_id;
                if (json_decode($_REQUEST['json']) != NULL) {
                  $result = $this->database->editDataNoVerify($usertypeid, 'USERTYPE_id', 'usertypes', 'USERTYPE_rights', $_REQUEST['json']);
                }

                $usertype = $this->database->getUsertypeById($usertypeid);
                $_SESSION['CURRENT-VIEW']['ADMIN']['CURRENT_USERTYPE'] = $usertype;
                $userrights = json_decode($usertype->USERTYPE_rights, true);
                $contentPlaceholders = (object) array_merge((array) $usertype, $userrights);
                $TMPtemplate = new TemplateParser("USERRIGHTS", $contentPlaceholders, $this->database);
                print_r($TMPtemplate->parseOutput());

                break;
            }
          }


        case "emptyorphans":
          if (isset($_REQUEST["table"])) {
            switch ($_REQUEST["table"]) {
              case "addresses":
                $emptyAddressCount = $this->database->deleteEmptyAddresses();
                print(sprintf(__("Result: %d empty addresses deleted"), $emptyAddressCount));
                break;


              case "members":
                $orphanMembersCount = $this->database->deleteOrphanMembers();
                print(sprintf(__("Result: %d members without addresses deleted"), $orphanMembersCount));
                break;
            }
          }
          break;

        case "emptytables":
          if (isset($_REQUEST["tables"])) {
            foreach ($_REQUEST["tables"] as $table) {
              $count = $this->database->emptyTable($table);
              printf(__("%d items deleted from table %s"), $count, $table);
            }
          } else {
            $TMPtemplate = new TemplateParser("EMPTYTABLES", "");
            print_r($TMPtemplate->parseOutput());
          }
          break;

        case "updatecoordinates":
          $start = (isset($_REQUEST['start'])) ? $_REQUEST['start'] : 0;
          $result = $this->database->updateAddressCoordinates($start);

          echo "<script>";
          if ($result['percentage'] < 100) {
            echo "updateCoordinates(" . $result['end'] . ",'#adminstatus');";
          } elseif ($result['percentage'] >= 100) {
            echo '$("#updateCoordsButton").button("enable");';
            $result['percentage'] = 0;
          }

          echo "updateProgressbar(" . $result['percentage'] . ");";
          echo "</script>";
//die();
          break;
      }
    }
  }

}

?>