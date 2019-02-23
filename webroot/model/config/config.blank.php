<?php
/*********************************************
 * This file contains configuration for the churchmembers user interface and database
 * Configuration is defined as constants in following manner: define(<key>, <value>);
 * 
 * Description : configuration file
 */

/*********************************************/
/*	DATABASE SETTINGS
/*********************************************/
  define('DB_HOST', 'localhost');
  define('DB_DATABASE', 'churchmembers');
	
// ADMIN USER OF DATABASE
  define('DB_ADMIN_USERNAME', '');
  define('DB_ADMIN_PASSWORD', '');
	
// CORE USER
// needs select privilages for all tables and insert privilages for table failedaccess
// SQL (GRANT INSERT ON churchmembers.failedaccess TO cc_core@'localhost';)
  define('DB_CORE_USERNAME', '');
  define('DB_CORE_PASSWORD', '');

 /**
 * Churchmembers Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
define('TB_PREFIX', '');

// AES key used for user encryption
define('AES_KEY', '');

// API Key. This is a security measure. The remote end MUST know the secret too for us to act.
define('SECRET_API_KEY', '');

/**
 * Churchmembers Localized Language, defaults to Dutch.
 *
 * Change this to localize Churchmembers. A corresponding MO file for the chosen
 * language must be installed to model/i18n. For example, install
 * de_DE.mo to model/i18n/de_DE/ and set LANG to 'de_DE' to enable German
 * language support.
 */
define ('LANG', 'nl_NL');
define ('TIMEZONE', 'Europe/Amsterdam');
setlocale(LC_ALL, 'Dutch_Netherlands', 'Dutch', 'nl_NL', 'nl', 'nl_NL.ISO8859-1');

/**************************************************/
/*  BASEURL
/*  http://www.mydomain.com/churchmembers/ -> $base_url = /churchmembers/
/**************************************************/
define('BASE_URL', '');

/**************************************************/
/*  BASEPATH (NEED SET MANUALY IF USING API
/*  This is the absolute path of the folder on the server,
/*  if the script doesn't work, uncomment the following line and enter the absolute path to the churchmembers directory
/**************************************************/
# define('BASE_PATH', '/var/www/churchmembers');
define('BASE_PATH', realpath(dirname(__FILE__) . '/../../') . '/');

/*********************************************/
/*	BACKUP DATABASE
/* To make backups of the db set the following cronjob (if supported by your hoster)
/* Example cronjob:
/* /usr/local/bin/mysqldump -uDbuser -pDbpass --databases Databasename | gzip > /home/xxx/domains/domainame/dirtobackup/db.`date +"\%Y-\%m-\%d"`.gz
/* Note that there is no space between -u and Dbuser, -p and Dppass 
/*********************************************/

/**************************************************/
/*	ADVANCED : DO NOT CHANGE THESE VARIABLES BELOW
/**************************************************/
define('CLASSES_PATH', BASE_PATH . 'model/classes/');
define('PROCESSOR_PATH', BASE_PATH . 'model/processors/');
define('TEMPLATES_PATH', BASE_PATH . 'model/templates/');
define('I18N_PATH', BASE_PATH . 'model/i18n/');
define('CONFIG_PATH', BASE_PATH . 'model/config/');
define('INCLUDES_PATH', BASE_PATH . 'includes/');
define('LOGS_PATH',  BASE_PATH . 'assets/logs/');
define('PHOTO_URL',  'assets/userimages/users/');
define('TEMP_PATH',  BASE_PATH . 'assets/tmp/');
define('TEMP_URL',  'assets/tmp/');
define('DOWNLOAD_PATH',  BASE_PATH . 'assets/downloads/');
define('DOWNLOAD_URL',  'assets/downloads/');
define('BACKUP_PATH',  BASE_PATH . 'model/backup/');
?>