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

/**
 * Takes care of localization.
 * ********************************************
 * 	DEFAULT LANGUAGE AND TIME SETTINGS
 *  Check i18n folder for available values.
 * ********************************************
 */
class Localization {

  public $locale = LANG;
  public $timezone = TIMEZONE;
  public $availablelocales = array();
  public $adapter;

  public function __construct() {
    $zendlibarypath = BASE_PATH . 'includes/Zend/library/';
    set_include_path(implode(PATH_SEPARATOR, array($zendlibarypath, get_include_path(),)));
    require_once $zendlibarypath . '/Zend/Loader.php';
    Zend_Loader::loadClass('Zend_Translate');

    $this->adapter = new Zend_Translate(
                    array('adapter' => 'gettext',
                        'content' => I18N_PATH . $this->locale . "/" . $this->locale . ".mo",
                        'locale' => $this->locale
                    )
    );

    $this->adapter->setLocale($this->locale);
    date_default_timezone_set($this->timezone);

    mb_internal_encoding("UTF-8");
    mb_http_output("UTF-8");
    mb_language('uni');
  }

  public function getTranslation($string) {
    return $this->adapter->translate($string);
  }
}
?>