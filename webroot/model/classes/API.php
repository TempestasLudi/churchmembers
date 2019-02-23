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
define('API_RUNNING', true);

//********************************* Class ****************************************/

/*
 * Takes care of API requests.
 */
class API {

  private $private_key;
  public $api_running;
  private $database;
  private $processRequest;

  public function __construct($key = '') {

    $this->private_key = SECRET_API_KEY;
    $this->API_Authenticate($key);

    if (isset($_SESSION)) {
      $this->oldsessionid = session_id();
      $this->oldsessionvalue = $_SESSION;
      $this->oldsessionparam = session_get_cookie_params();
      $this->oldsessionname = session_name();
      $_SESSION = array();
      session_write_close();
    }
    require_once CLASSES_PATH . 'ProcessRequest.php';
    $this->processRequest = $processRequest;
    $this->authentication = $this->processRequest->getAuthentication();
  }

  public function API_close($destroysession = true) {
    if ($destroysession) {
      session_start(); // Session is currently in closed state
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
      session_destroy();
    } else {
      session_write_close();
    }

    if ($this->oldsessionname) {
      session_id($this->oldsessionid);
      session_name($this->oldsessionname);
      session_set_cookie_params(0, $this->oldsessionparam['path'], $this->oldsessionparam['domain'], $this->oldsessionparam['secure'], $this->oldsessionparam['httponly']); // lifetime of the session is regulated by the class itself.
      session_start();
      $_SESSION = $this->oldsessionvalue;
    }
  }

  private function API_Authenticate($key) {
    if (!$key) {
      die('NO API-KEY DEFINED');
    } elseif ($key === $this->private_key) {

    } else {
      die('NO VALID API-KEY DEFINED. YOU CAN FIND THE API-KEY IN THE CONFIG FILE');
    }
  }

  public function API_Login($username = '', $password = '') {
    $result = $this->authentication->UserLogin($username, $password);
    $result ? ($this->database = new Database) : die($_SESSION["error"]);
    return $result;
  }

  public function API_RemoteLogin($user = '', $linked_member_id = '') {
    $value = $this->authentication->RemoteUserLogin($user, $linked_member_id);
    return $value;
  }

  public function API_Logout() {
    $this->authentication->UserLogout();
  }

  public function API_getMembersIntroduction() {
    $getmembersintroduction = $this->database->getMembersIntroduction(0);
    return $getmembersintroduction;
  }

  public function API_generateFullMemberName($member, $tabs = true, $forcefullname = false, $prefix = '', $familynameview = false, $hidefirstname = false, $hideinitials = false) {
    $getmemberfullname = $this->database->generateFullMemberName($member, $tabs, $forcefullname, $prefix, $familynameview, $hidefirstname, $hideinitials);
    return $getmemberfullname;
  }

  public function API_getSettings() {
    $getsettings = $this->database->getSettings();
    return $getsettings;
  }

  public function API_getMembers() {
    $getmembers = $this->database->getMembers();
    return $getmembers;
  }

  public function API_getMarkers() {
    $this->processRequest->excecuteProcessor('getdata', 'markers');
  }

  public function API_Userlinktomember($memberid) {
    $userlinktomember = $this->database->getMemberById($memberid);
    $_SESSION['USER']->setUserlinktomember($userlinktomember);
  }

  public function API_setLoginurl($loginurl = '') {
    $loginurl = ($loginurl === '') ? BASE_URL . 'login.php' : $loginurl;
    $userlinktomember = $this->authentication->setLoginurl($loginurl);
  }

}

?>