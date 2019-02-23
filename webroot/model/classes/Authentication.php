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
require_once CLASSES_PATH . 'User.php';

/**
 * Takes care of authentication.
 */
class Authentication extends AbstractProcessor {

  private $mode;
  private $loginurl;
  private $indexurl;
  private $sessiontime;
  private $api_running = false;
  private $user;
  protected $database;
  private $systemsettings;

  public function __construct($database) {
    $this->indexurl = BASE_URL . 'index.php';
    $this->sessiontime = 6 * 60 * 60; //Lifetime of the session defined in seconds.
    $this->database = $database;
    $this->systemsettings = $this->database->getSettings();
    $this->api_running = (defined('API_RUNNING')) ? true : false;

    $cookie_domain = ($this->systemsettings->cookie_domain !== '') ? $this->systemsettings->cookie_domain : '';
    $system_secure = $this->systemsettings->system_secure;

    session_name(($this->systemsettings->cookie_name));

    /* Set correct sessioncookie parameters */
    session_set_cookie_params(0, '/', $cookie_domain, $system_secure, true); // lifetime of the session is regulated by the class itself.
    session_start();

    $this->loginurl = isset($_SESSION['SESSION-INFO']['LOGINURL']) ? $_SESSION['SESSION-INFO']['LOGINURL'] : BASE_URL . 'login.php';

    /* Force the creation of a sessionid and initialize session if necessary */
    if (!$_SESSION || !isset($_SESSION['SESSION-INFO']) || !isset($_SESSION['SESSION-INFO']['initialized']) || $_SESSION['SESSION-INFO']['initialized'] !== true) {
      session_regenerate_id(true);
      $_SESSION['SESSION-INFO']['initialized'] = true;
      $_SESSION['SESSION-INFO']['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
      $_SESSION['SESSION-INFO']['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT'] . '$24!&9');
      $_SESSION['SESSION-INFO']['ID'] = session_id();
      $_SESSION['SESSION-INFO']['LAST_ACTIVITY'] = time(); // update last activity time stamp
      $_SESSION['SESSION-INFO']['LOGIN_MODE'] = "";
      $_SESSION['SESSION-INFO']['REMOTE_LOGIN'] = false;
      $_SESSION['SESSION-INFO']['MOBILE'] = defined('MOBILE') && MOBILE == true ? true : false;
      $_SESSION['ARCHIVE-MODE'] = false;
    }

    if (!isset($_SESSION['SYSTEMSETTINGS'])) {
      $_SESSION['SYSTEMSETTINGS'] = $this->systemsettings;
    }

    if ($_SESSION['SESSION-INFO']['MOBILE'] === true) {
      $this->loginurl = BASE_URL . 'mobile/login.php';
      $this->indexurl = BASE_URL . 'mobile/index.php';
    }

    if (defined('DOWNLOAD') && DOWNLOAD === true) {
      $_SESSION['SESSION-INFO']['REFERURL'] = $_SERVER['REQUEST_URI'];
    }
  }

  public function processRequest(){

  }

  /**
   * Validates current session and takes action if necessary
   */
  private function VerifyCurrentSession() {

    /* Test for User object */
    if (isset($_SESSION['USER'])) {
      if (!is_a($_SESSION['USER'], 'User')) {
        $_SESSION['error'] = __("User isn't currently in the session. Session is ended. Please login.");
        $this->ForceUserLogout();
      }
    } else { /* No User object available, so send client to login page */
      $this->GotoLogin();
    }

    /* Test if last request was more than the sessiontime ago */
    if (isset($_SESSION['SESSION-INFO']['LAST_ACTIVITY']) && (time() - $_SESSION['SESSION-INFO']['LAST_ACTIVITY'] > $this->sessiontime)) {
      $_SESSION['error'] = __("Current session is ended. Please login.");
      $this->ForceUserLogout();
    }
    /* update last activity time stamp */
    $_SESSION['SESSION-INFO']['LAST_ACTIVITY'] = time();

    /* Remove REFERURL*/
    $_SESSION['SESSION-INFO']['REFERURL'] = '';

    /* Check if site is in maintenance */
    if (!$this->api_running) {
      if ($this->systemsettings->maintenance) {
        if (($_SESSION['USER']->checkUserrights('view_admin'))) {
          /* Admin can still login */
        } else {
          $this->loginurl = BASE_URL . 'offline.php';
          $this->GotoLogin();
        }
      }
    }
  }

  /**
   * Checks if the provided user with password is a valid one,
   * Maybe for safety add something as regex authenticatie.
   * @param string $username
   * @param string $password
   */
  public function UserLogin($username, $password) {
    /* Reset User info */
    $this->user = NULL;
    $_SESSION['USER'] = "";

    /* Check if user or client is blocked */
    if ($this->ipBlocked()) {
      $_SESSION['error'] = __("To many failed login attempts are made. Try again within a hour.");
      $this->GotoLogin();
    }

    /* Receive User information from DB */
    $userFromDb = $this->database->getOneUserByName($username, $password);

    if ($userFromDb !== false) {
      return $this->AddUserToSession($userFromDb);
    } else { /* User isn't found, add failed login attempt to DB */
      $_SESSION['error'] = __("Username or password is invalid");
      $this->database->failedLoginAttempt($_SERVER['REMOTE_ADDR'], $username, $password);
      return $this->GotoLogin();
    }
  }

  /**
   * Checks if the user is logged on on a remote app/site,
   * if yes it returns a User object, if not it return null.
   * @param string $user
   * @param int $linked_member_id
   */
  public function RemoteUserLogin($user = '', $linked_member_id = '') {

    /* Reset User info */
    $_SESSION['USER'] = "";
    $_SESSION['SESSION-INFO']['REMOTE_LOGIN'] = "ATTEMPT";

    /* Check if user or client is blocked */
    if ($this->ipBlocked()) {
      $_SESSION['error'] = __("To many failed login attempts are made. Try again within a hour.");
      $this->GotoLogin();
    }

    /* Gets login info if the user is logged on on a remote app/site
     * The URL should gives USER_username and optional MEMBER_id where user is linked.
     * USER_username|MEMBER_id
     */

    if ($user === '') {
      if ($this->systemsettings->auth_validationurl !== '') {
        $cookie = (isset($_SERVER['HTTP_COOKIE'])) ? $_SERVER['HTTP_COOKIE'] : '';
        $opts = array('http' => array('header' => 'Cookie: ' . $cookie . "\r\n" . "Connection: close\r\n"));
        $context = stream_context_create($opts);
        $validationstr = file_get_contents($this->systemsettings->auth_validationurl, false, $context);
        if ($validationstr) {
          $loginstr = explode("|", $validationstr);
        } else {
          $_SESSION['SESSION-INFO']['REMOTE_LOGIN'] = "FAILED";
          $_SESSION['error'] = sprintf(__("%s is not found. Set auth_validationurl in systemsettings."), $this->systemsettings->auth_validationurl);
          $this->ForceUserLogout();
        }
      } else {
        $_SESSION['SESSION-INFO']['REMOTE_LOGIN'] = "FAILED";
        $_SESSION['error'] = __("auth_validationurl is not valid");
        $this->ForceUserLogout();
      }

      $user = $loginstr[0];
      $linked_member_id = $loginstr[1];
    }

    if ($user !== '') {
      /* Receive User information from DB */
      $userFromDb = $this->database->getOneUserByNameRemote($user);

      if ($userFromDb !== false) {
        $_SESSION['SESSION-INFO']['REMOTE_LOGIN'] = true;
        if ($linked_member_id !== '') {
          $this->AddUserToSession($userFromDb, $linked_member_id);
        } else {
          $this->AddUserToSession($userFromDb);
        }
      } else {
        /* User isn't found, add failed login attempt to DB
          /* And send user to login page */
        $this->database->failedLoginAttempt($_SERVER['REMOTE_ADDR'], $user);
        $_SESSION['error'] = __("You are not logged in. Please login.");
        $_SESSION['SESSION-INFO']['REMOTE_LOGIN'] = "FAILED";
        $this->GotoLogin();
      }
    }
  }

  /**
   *  Add user to session
   * @param object $userFromDb
   * @param int $setUserlinktomember_id
   */
  private function AddUserToSession($userFromDb, $setUserlinktomember_id = false) {
    $this->user = new User($userFromDb);

    /* Check if user is blocked */
    if ($this->userBlocked($this->user->getUsername())) {
      $_SESSION['error'] = __("This username is blocked. Try again within a hour.");
      $this->GotoLogin();
    }

    /* Add user to session */
    $_SESSION['USER'] = $this->user;
    $member = '';
    if ($setUserlinktomember_id) {
      $member = $this->database->getMemberById($setUserlinktomember_id);
      $username = $this->database->generateFullMemberName($member, false, true);
    } else {
      $username = $this->user->getUsername();
    }
    $this->user->setUserlinktomember($member, $username );

    /* Send mail */
    if ($this->systemsettings->login_mail) {
      $this->sendMail();
    }

    /* Set event types */
    if (!isset($_SESSION['EVENTTYPES'])) {
      $_SESSION['EVENTTYPES'] = $this->database->getEventtypes();
    }

    return $this->GotoIndex();
  }

  /**
   * Destroys session and set cookie expire date in the past. Send client to loginpage
   */
  public function UserLogout() {
    if (!$this->api_running) {
      setcookie(session_name(), session_id(), time() - 42000);
      session_destroy();
      $this->GotoLogin();
    }
  }

  /**
   * In case of a unauthorized request Forces a user to logout,
   * Destroy all session data & cookie.
   * If a errormessage is set, send it with the headers.
   */
  private function ForceUserLogout() {
    $errormsg = isset($_SESSION['error']) ? $_SESSION['error'] : __("Current session is ended. Please login.");

    /* Destroy session data and set cookie expire date in the past */
    if (isset($_SESSION)) {
      setcookie(session_name(), session_id(), time() - 42000);
      session_destroy();
      session_unset();
    }

    header('Errormsg: ' . $errormsg);

    /* If available Ajax handler reads SetLocation to set the location of the complete window.
     * In case Ajaxhandler is not available, set also Location header and print error
     */
    header('SetLocation: ' . $this->loginurl);
    if (basename($_SERVER['REQUEST_URI']) !== "login.php")
      header('Refresh: 0.5; url="' . $this->loginurl . '"');
    exit();
  }

  /**
   * Send user to login page.
   * @return true
   */
  protected function GotoLogin() {
    session_write_close();

    if ($this->api_running === false) {
      header('SetLocation: ' . $this->loginurl);
      header('Location: ' . $this->loginurl);
      exit();
    }

    return true;
  }

  /**
   * Send user to index page if user and session is validated.
   * @return true
   */
  protected function GotoIndex() {
    $_SESSION['SESSION-INFO']['LAST_ACTIVITY'] = time();
    $referurl = empty($_SESSION['SESSION-INFO']['REFERURL']) ? $this->indexurl : $_SESSION['SESSION-INFO']['REFERURL'];
    $_SESSION['SESSION-INFO']['REFERURL'] = '';
    session_write_close();

    if (!$this->api_running) {
      header('Location: ' . $referurl);
      exit();
    }

    return true;
  }

  /**
   * Checks if user is blocked
   * @param string $username
   * @return bool
   */
  private function userBlocked($username) {
    return $this->database->getFailedUserAttempts($username) >= $this->systemsettings->login_maxattempts;
  }

  /**
   * Checks if user is blocked based on ip
   * @return bool
   */
  private function ipBlocked() {
    return $this->database->getFailedIpAttempts($_SERVER['REMOTE_ADDR']) >= $this->systemsettings->login_maxattempts;
  }

  /**
   * Sends a mail to administrator email when user logs in
   * @return bool
   */
  private function sendMail() {
    $sendmail = new Sendmail($this->database);

    $sender = array('EMAIL'=>$this->systemsettings->administrator_email, 'USERNAME' => $this->systemsettings->administrator_email);
    $sendmail->setSender($sender);
    $sendmail->setSubject($this->database->getSetting('mail_subject')->SETTINGS_value . 'Login ' . $this->user->getUserlinktomemberFullname());
    $sendmail->setBody($this->user->getUserlinktomemberFullname());

    return($sendmail->sendMail());
  }

  /**
   * Sets new loginurl
   * @param string $url
   */
  public function setLoginurl($url) {
    $this->loginurl = $url;
    $_SESSION['SESSION-INFO']['LOGINURL'] = $url;
  }

  /**
   * Start Authentication. API starts authentication by itself
   */
  protected function startAuth() {

    /* Login user if login form is send */
    if (isset($_REQUEST['username']) && isset($_REQUEST['password'])) {
      $_SESSION['SESSION-INFO']['LOGIN_MODE'] = "default";
      $this->UserLogin($_REQUEST['username'], $_REQUEST['password']);
    }


    /* Automatic log in user if remote login is set
     * only if users are directly send to index.php
     * login php can still be used for default login
     */

    if ($this->systemsettings->auth_enabled && (isset($this->api_running) && $this->api_running === false)) {
      if (($_SESSION['SESSION-INFO']['LOGIN_MODE'] !== 'default') && (basename($_SERVER['REQUEST_URI']) !== "login.php") &&
              ($_SESSION['SESSION-INFO']['REMOTE_LOGIN'] !== true)) {

        $_SESSION['SESSION-INFO']['LOGIN_MODE'] = "remote";
        $this->setLoginurl($this->systemsettings->auth_loginurl);
        $this->RemoteUserLogin();
      } elseif ($_SESSION['SESSION-INFO']['REMOTE_LOGIN'] === true) {
        if ($this->systemsettings->auth_loginurl !== "")
          $this->setLoginurl($this->systemsettings->auth_loginurl);

        if (basename($_SERVER['REQUEST_URI']) === "login.php") {
          $this->GotoIndex();
        }
      }
    }

    if (isset($_GET['logout'])) {
      $this->UserLogout();
    }

    if ((isset($this->api_running) && $this->api_running === false) && (basename($_SERVER['REQUEST_URI']) !== "login.php")) {
      $this->VerifyCurrentSession();
    }
  }

}

?>