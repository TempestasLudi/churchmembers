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

//********************************* Includes ***********************************************/
require_once dirname(__FILE__) . '/../config/config.php';

//********************************* Functions **********************************************/

/**
 * Autoloads the required <class>.php to prevent endless includes.
 * @param string $className
 */
function __autoload($className) {
  $objectDirs = array(dirname(__FILE__), PROCESSOR_PATH, PROCESSOR_PATH . 'admin', CLASSES_PATH, CLASSES_PATH . 'importcvs');
  foreach ($objectDirs as $dir) {
    $path = $dir . '/' . $className . '.php';

    if (file_exists($path)) {
      require_once $path;
      break;
    }
  }
}

//$time_start = getmicrotime();
//$time_taken = round(getmicrotime() - $time_start, 4);
function getmicrotime() {
  list($usec, $sec) = explode(" ", microtime());
  return ((float) $usec + (float) $sec);
}

/**
 * Translates string to selected locale.
 * Uses Zend Translate module with gettext adapter
 * @global class Localization $locatization
 * @param string $string
 * @return string
 */
function __($string) {
  global $locatization;
  if (!isset($locatization)) {
    $locatization = new Localization();
  }
  return $locatization->getTranslation($string);
}

//********************************* Class **********************************************/
class ProcessRequest {

  public $locatization;
  protected $errorhandler;
  protected $authentication;
  protected $database;
  private $processor = null;
  private $action = null;
  private $type = null;

  public function __construct() {
    $this->errorhandler = new ErrorHandler();
    $this->database = new Database();

    $this->authentication = new Authentication($this->database);
    $this->authentication->startAuth();

    if (!isset($_SESSION['ARCHIVE-MODE'])) {
      $_SESSION['ARCHIVE-MODE'] = false;
    }

    if (isset($_REQUEST['action']) && isset($_REQUEST['type'])) {
      if (strtolower($_REQUEST['action']) === 'getdata' && strtolower($_REQUEST['type']) === 'download') {
        define('DOWNLOAD', true);
      }

      $this->action = strtolower($_REQUEST['action']);
      $this->type = strtolower($_REQUEST['type']);
      $this->setProcessor();
      $this->startProcessor();
    }
  }

  /**
   * Execute Proccessor from an external script
   * @param string $action
   * @param string $type
   */
  public function excecuteProcessor($action = false, $type = false) {
    $this->action = ($action) ? $action : $this->action;
    $this->type = ($type) ? $type : $this->type;
    $this->setProcessor();
    return $this->startProcessor();
  }

  /**
   * Sets the processor for the specified action & type
   */
  private function setProcessor() {
    switch ($this->action) {

      case 'getdata':
        switch ($this->type) {
          case 'home':
            $this->processor = new GetHomeDataProcessor();
            break;
          case 'address':
            $this->processor = new GetAddressDataProcessor();
            break;
          case 'group':
            $this->processor = new GetGroupDataProcessor();
            break;
          case 'member':
            $this->processor = new GetMemberDataProcessor();
            break;
          case 'groups':
            $this->processor = new GetGroupsDataProcessor();
            break;
          case 'event':
            $this->processor = new GetEventDataProcessor();
            break;
          case 'modifications':
            $this->processor = new GetModificationDataProcessor();
            break;
          case 'modificationaddressdetails':
            $this->processor = new GetModificationAddressDetailsProcessor();
            break;
          case 'modificationmemberdetails':
            $this->processor = new GetModificationMemberDetailsProcessor();
            break;
          case 'search':
            $this->processor = new GetSearchDataProcessor();
            break;
          case 'photobook':
            $this->processor = new GetPhotobookDataProcessor();
            break;
          case 'markers':
            $this->processor = new GetMapMarkersProcessor();
            break;
          case 'calendar':
            $this->processor = new GetCalendarDataProcessor();
            break;
          case 'download':
            $this->processor = new GetDownloadProcessor();
            break;
        }
        break;

      case 'photoupload':
        if ($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->ADR_id === $_SESSION['USER']->getUserlinktomemberAdr_Id() ||
                $_SESSION['USER']->checkUserrights('edit_mode')) {
          $this->processor = new PhotoUploadProcessor();
        }
        break;

      case 'livesearch':
        $this->processor = new LivesearchProcessor();
        break;

      case 'report':
        switch ($this->type) {
          case 'updatedata':
            $this->processor = new GetReportDataProcessor();
            break;
          case 'export':
            $this->processor = new GetReportDataProcessor();
            break;
        }
        break;

      case 'togglearchive':
        if ($_SESSION['USER']->checkUserrights('view_archive')) {
          $this->processor = new ArchiveProcessor();
        }
        break;

      case 'deletedata':
        switch ($this->type) {
          //case 'data':
          case 'deletegroup':
            if ($_SESSION['USER']->checkUserrights('delete_data')) {
              $this->processor = new DeleteDataProcessor();
            }
            break;
        }
        break;

      case 'editdata':
        switch ($this->type) {
          case 'editdetails':
            $this->processor = ($_SESSION['USER']->checkUserrights('edit_mode')) ? new EditDataProcessor() : null;
            break;
          case 'editgroupmembers':
            $this->processor = ($_SESSION['USER']->checkUserrights('edit_mode')) ? new EditGroupMembersProcessor() : null;
            break;
          case 'sortmemberdata':
            $this->processor = ($_SESSION['USER']->checkUserrights('edit_mode')) ? new SortMemberDataProcessor() : null;
            break;
          case 'coordinates':
            $this->processor = ($_SESSION['USER']->checkUserrights('edit_mode')) ? new EditCoordinatesProcessor() : null;
            break;
          case 'deletephoto':
            if ($_SESSION['USER']->checkUserrights('edit_mode') || (($_REQUEST['field'] === 'MEMBER_photo') && ($_REQUEST['table'] === 'members'))) {
              $this->processor = new EditDataProcessor();
            }
            break;
          case 'editintroduction':
            if ($_SESSION['USER']->checkUserrights('edit_mode') || (($_REQUEST['field'] === 'MEMBER_introduction') && ($_REQUEST['table'] === 'members'))) {
              $this->processor = new EditDataProcessor();
            }
            break;
        }
        break;

      case 'dialog':
        if ($_SESSION['USER']->checkUserrights('add_data')) {
          $this->processor = new DialogDataProcessor();
        }
        break;

      case 'admin':
        if ($_SESSION['USER']->checkUserrights('view_admin')) {
          switch ($this->type) {
            case 'template':
            case 'failedlogin':
            case 'backupdb':
            case 'usertypes':
            case 'emptyorphans':
            case 'emptytables':
            case 'updatecoordinates':
              $this->processor = new AdminProcessor();
              break;
            case 'uploadcsv':
              $this->processor = new ImportCsvProcessor();
              break;
          }
        }
        break;

      case 'export':
        switch ($this->type) {
          case 'template':
            $this->processor = new GetExportDataProcessor("template");
            break;
          case 'exportfile':
            $this->processor = new GetExportDataProcessor("exportfile");
            break;
        }
        break;

      case 'email':
        switch ($this->type) {
          case 'template':
          case 'preview':
          case 'sendemail':
            $this->processor = new GetEmailDataProcessor();
            break;
        }
        break;

      default :
        die();
    }

    $this->processor->database = $this->database;
  }

  /**
   * Excecute the processor
   */
  private function startProcessor() {
    if ($this->processor !== null) {
      header("Content-Type: text/html; charset=utf-8");
      return $this->processor->processRequest();
    } else {
      die();
    }
  }

  /**
   * Gets the translation of Eventstype
   *
   * @param string $event_type
   * @return string
   */
  public function getEventTypeTranslation($event_type) {
    return $this->database->getEventTypeTranslation($event_type);
  }

  /**
   * Gets a list of all MemberTypes in database
   * @return object
   */
  public function getMemberTypesList() {
    return $this->database->getMemberTypesList();
  }

  /**
   * Gets the value of setting from the database
   * @param string $setting_name
   * @return string
   */
  public function getSetting($setting_name) {
    return $this->database->getSetting($setting_name)->SETTINGS_value;
  }

  /**
   * Returns a formatted string with the current version
   * @return string
   */
  public function getVersionInfo() {
    return $this->database->getVersionInfo();
  }

  /**
   * Returns the loaded Authentication class
   * @return class
   */
  public function getAuthentication() {
    return $this->authentication;
  }

}

//********************************* Executable code ****************************************/
$processRequest = new ProcessRequest();
session_write_close();
?>