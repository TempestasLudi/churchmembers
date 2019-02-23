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

class Sendmail {

  //********************************* Attributes ***********************************************/
  public $_config;
  public $_transport;
  public $_zendlibarypath;
  private $_subject = '';
  private $_receivers = array();
  private $_sender = array();
  private $_body = '';

  //********************************* Constructors *********************************************/

  public function __construct($database) {
    $this->_zendlibarypath = BASE_PATH . 'includes/Zend/library/';
    set_include_path(implode(PATH_SEPARATOR, array($this->_zendlibarypath, get_include_path(),)));
    require_once $this->_zendlibarypath . '/Zend/Loader.php';
    Zend_Loader::loadClass('Zend_Mail');
    Zend_Loader::loadClass('Zend_Mail_Transport_Smtp');

    $this->database = $database;

    // Create transport
    $this->_config = array(
        'name' => $this->database->getSetting('smtp_host')->SETTINGS_value,
        'auth' => 'login',
        'username' => $this->database->getSetting('smtp_username')->SETTINGS_value,
        'password' => $this->database->getSetting('smtp_password')->SETTINGS_value,
        'ssl' => $this->database->getSetting('smtp_ssl')->SETTINGS_value,
        'port' => $this->database->getSetting('smtp_port')->SETTINGS_value);

    $_transport = new Zend_Mail_Transport_Smtp($this->_config['name'], $this->_config);
  }

  public function setSubject($subject) {
    return $this->_subject = $subject;
  }

  public function setBody($body) {
    return $this->_body = $body;
  }

  public function setReceivers($receivers) {
    return $this->_receivers = $receivers;
  }

  public function setSender($sender) {
    return $this->_sender = $sender;
  }

  public function sendMail() {
    Zend_Mail::setDefaultFrom($this->_sender['EMAIL'], $this->_sender['USERNAME']);
    Zend_Mail::setDefaultReplyTo($this->_sender['EMAIL'], $this->_sender['USERNAME']);

    $this->mail = new Zend_Mail_InlineImages('utf-8');

    $this->mail->addTo($this->_sender['EMAIL'], $this->_sender['USERNAME']);
    foreach ($this->_receivers as $member) {
      $this->mail->addBcc($member['EMAIL'], $member['FULLNAME']);
    }

    $this->mail->setSubject($this->_subject);
    $this->mail->setBodyText($this->_body);
    $this->mail->setBodyHtml($this->_body);

    $this->mail->send($this->_transport);
    // Reset defaults
    Zend_Mail::clearDefaultFrom();
    Zend_Mail::clearDefaultReplyTo();

    print(__('The mail has been sent successfully'));
  }

}

?>