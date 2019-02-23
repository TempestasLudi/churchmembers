<?php
session_start();

if(isset($_REQUEST['ccadmin'])) {
    $_SESSION['ADMIN_USER'] = $_REQUEST['ccadmin'];
}
if(isset($_REQUEST['ccpassword'])) {
    $_SESSION['ADMIN_PASS'] = $_REQUEST['ccpassword'];
}
if(isset($_REQUEST['ccadminemail'])) {
    $_SESSION['ADMIN_EMAIL'] = $_REQUEST['ccadminemail'];
}

$failimg = '<img src="images/delete.png" class="imgInLine"/>';
$fail = _("<span style='font-weight:bold; color:red;'>FAIL!</span> <br/><br/>");
$tickimg = '<img src="images/accept.png" class="imgInLine"/>';
$tick = _("<span style='font-weight:bold; color:green;'>OK!</span>  <br/><br/>");

$output = '';
$error = false;

// PHP version
$text = '%s Checking if PHP version is at least 5.2: %s';
if (version_compare(phpversion(), '5.2.0', '<')) {
  $output .= sprintf($text, $failimg, $fail);
  $error = true;
} else {
  $output .= sprintf($text, $tickimg, $tick);
}

// PHP memory limit
$text = '%s Checking if memory limit is set to at least 24M: %s';
if (return_bytes(ini_get('memory_limit')) <= 25165824) {
  $output .= sprintf($text, $failimg, $fail);
  $error = true;
} else {
  $output .= sprintf($text, $tickimg, $tick);
}

function return_bytes ($size_str) {
  switch (substr ($size_str, -1)) {
    case 'M': case 'm': return (int)$size_str * 1048576;
    case 'K': case 'k': return (int)$size_str * 1024;
    case 'G': case 'g': return (int)$size_str * 1073741824;
    default: return $size_str;
  }
}

// Checking working sessions
$text = '%s Checking if sessions are properly configured: %s';
if (!isset($_SESSION) || (session_id() == '')) {
  $output .= sprintf($text, $failimg, $fail);
  $error = true;
} else {
  $output .= sprintf($text, $tickimg, $tick);
}

// Check working GD2
$text = '%s Checking if GD libary is available (required for generating thumbnails): %s';
if (!function_exists("gd_info") || !function_exists("imagecopyresized") || !function_exists("imagecopyresampled")) {
  $output .= sprintf($text, $failimg, $fail);
  $error = true;
} else {
  $output .= sprintf($text, $tickimg, $tick);
}

$text = '%s Apache module is enabled (optional, comment .htaccess on fail): %s';
// Check if mod_headers is enabled
if ((!in_array('mod_headers', apache_get_modules())) and (!in_array('headers_module', apache_get_modules()))) {
  $output .= sprintf($text, $failimg, $fail);
} else {
  $output .= sprintf($text, $tickimg, $tick);
}

// Directories writable/exist
$BASE_PATH = realpath(dirname(__FILE__) . '/../../') . '/';
$INCLUDES_PATH = $BASE_PATH . 'includes/';
$LOGS_PATH =  $BASE_PATH . 'model/logs/';
$PHOTO_PATH = $BASE_PATH . 'css/images/users/';
$TEMP_PATH =  $BASE_PATH . 'model/tmp/';
$BACKUP_PATH = $BASE_PATH . 'model/backup/';
$SETUP_PATH = $BASE_PATH . 'setup';

$dirs = array($INCLUDES_PATH . "phpThumb/cache/",$INCLUDES_PATH . "phpThumb/temp/", $INCLUDES_PATH . "fileuploader/server/uploads/", $PHOTO_PATH, $LOGS_PATH, $TEMP_PATH, $BACKUP_PATH, $SETUP_PATH);
$textexist = '%s Checking if %s exists: %s';
$textwritable = '%s Checking if %s is writable: %s';
clearstatcache();

foreach ($dirs as $dir) {
  if (file_exists($dir)) {
    $output .= sprintf($textexist, $tickimg, $dir, $tick);
  } else {
    $output .= sprintf($textexist, $failimg, $dir, $fail);
    $error = true;
  }

  if (is_writable($dir)) {
    $output .= sprintf($textwritable, $tickimg, $dir, $tick);
  } else {
    $output .= sprintf($textwritable, $failimg, $dir, $fail);
    $error = true;
  }

}

// Check Or Create config file
$CONFIGDIR =  $BASE_PATH . 'model/config/';
$CONFIGFILE =  $CONFIGDIR . 'config.php';
$textexist = '%s Checking if %s exists: %s';
$textcreate = '%s Trying to create %s: %s';
if (file_exists($CONFIGFILE)) {
  require_once $CONFIGFILE;
  $output .= sprintf($textexist, $tickimg, $CONFIGFILE, $tick);
} else {
  $output .= sprintf($textexist, $failimg, $CONFIGFILE, $fail);
  if(is_writable($CONFIGDIR)) {
    if($fh = fopen($CONFIGFILE, 'w')) {
      $output .= sprintf($textcreate, $tickimg, $CONFIGFILE, $tick);
      fclose($fh);
    } else {
      $output .= sprintf($textcreate, $failimg, $CONFIGFILE, $fail);
    }
  } else {
      $output .= sprintf($textwritable, $failimg, $CONFIGDIR, $fail);
      $error = true;
  }
}

$AES_KEY = (defined('AES_KEY') && AES_KEY != '') ? constant('AES_KEY') : rand_sha1(30);
$SECRET_API_KEY = (defined('SECRET_API_KEY') && SECRET_API_KEY != '') ? constant('SECRET_API_KEY') : rand_sha1(30);

function rand_sha1($length) {
  $max = ceil($length / 40);
  $random = '';
  for ($i = 0; $i < $max; $i ++) {
    $random .= sha1(microtime(true).mt_rand(10000,90000));
  }
  return substr($random, 0, $length);
}

$DB_HOST = $_SESSION['DB_HOST'];
$DB_ADMIN_USERNAME = $_SESSION['DB_ADMIN_USERNAME'];
$DB_ADMIN_PASSWORD = $_SESSION['DB_ADMIN_PASSWORD'];
$DB_CORE_USERNAME = $_SESSION['DB_CORE_USERNAME'];
$DB_CORE_PASSWORD = $_SESSION['DB_CORE_PASSWORD'];
$TB_PREFIX = $_SESSION['TB_PREFIX'];
$DB_DATABASE = $_SESSION['DB_DATABASE'];
$BASEURL = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], "/setup")+1);

$configfilestr = "<?php
/*********************************************
 * This file contains configuration for the churchmembers user interface and database
 * Configuration is defined as constants in following manner: define(<key>, <value>);
 *
 * Description : configuration file
 */

/*********************************************/
/*	DATABASE SETTINGS
/*********************************************/
  define('DB_HOST', '$DB_HOST');
  define('DB_DATABASE', '$DB_DATABASE');

// ADMIN USER OF DATABASE
  define('DB_ADMIN_USERNAME', '$DB_ADMIN_USERNAME');
  define('DB_ADMIN_PASSWORD', '$DB_ADMIN_PASSWORD');

// CORE USER
// needs select privilages for all tables and insert privilages for table failedaccess
// SQL (GRANT INSERT ON churchmembers.failedaccess TO cc_core@'localhost';)
  define('DB_CORE_USERNAME', '$DB_CORE_USERNAME');
  define('DB_CORE_PASSWORD', '$DB_CORE_PASSWORD');

 /**
 * Churchmembers Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
define('TB_PREFIX', '$TB_PREFIX');

// AES key used for user encryption
define('AES_KEY', '$AES_KEY');

// API Key. This is a security measure. The remote end MUST know the secret too for us to act.
define('SECRET_API_KEY', '$SECRET_API_KEY');

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
/*  http://www.mydomain.com/churchmembers/ -> base_url = /churchmembers/
/**************************************************/
define('BASE_URL', '$BASEURL');

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
/* /usr/local/bin/mysqldump -uDbuser -pDbpass --databases Databasename | gzip > /home/xxx/domains/domainame/dirtobackup/db.`date +\"\%Y-\%m-\%d\"`.gz
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
define('LOGS_PATH',  BASE_PATH . 'model/logs/');
define('PHOTO_URL',  'css/images/users/');
define('TEMP_PATH',  BASE_PATH . 'model/tmp/');
define('TEMP_URL',  'model/tmp/');
define('DOWNLOAD_PATH',  BASE_PATH . 'assets/downloads/');
define('DOWNLOAD_URL',  'assets/downloads/');
define('BACKUP_PATH',  BASE_PATH . 'model/backup/');
?>";

if (is_writable($CONFIGFILE)) {
  $fh = fopen($CONFIGFILE, 'w');
  fwrite($fh, $configfilestr);
  fclose($fh);
  $output .= sprintf($textwritable, $tickimg, $CONFIGFILE, $tick);
  $textconfigfile = '%s Config file written %s: %s';
  $output .= sprintf($textconfigfile, $tickimg, $CONFIGFILE, $tick);
} else {
  $textwritable = '%s Could not write config file. Create %s by yourself %s';
  $output .= sprintf($textwritable, $failimg, $CONFIGFILE, $fail);
  $output .= "And add the following info:<br/><div id='code'>";
  $output .= highlight_string($configfilestr,true);
  $output .= "</div><br/>";
  $error = true;
}

// Database connection
$text = '%s Creating connection to the database: %s';

if ((!isset($_SESSION['DB_HOST'])) || (!isset($_SESSION['DB_ADMIN_USERNAME'])) || (!isset($_SESSION['DB_ADMIN_PASSWORD'])) || (!isset( $_SESSION['DB_DATABASE']))) {
  $output .= sprintf($text, $failimg, $fail) . ' Check connection info';
} else {
  $mysqli = @new mysqli($_SESSION['DB_HOST'], $_SESSION['DB_ADMIN_USERNAME'], $_SESSION['DB_ADMIN_PASSWORD']);
  if (mysqli_connect_error()) {
    $output .= sprintf($text, $failimg, $fail) . ' Check connection info';
    $error = true;
  } else {
    $output .= sprintf($text, $tickimg, $tick);
  }

// Database selection
  $text = '%s Select the database "'.$_SESSION['DB_DATABASE'].'": %s';
  if (!isset($_SESSION['DB_DATABASE']) || $_SESSION['DB_DATABASE']=='') {
    $output .= sprintf($text, $failimg, $fail);
    $error = true;
  } else {

    if($mysqli->select_db($_SESSION['DB_DATABASE'])) {
      $output .= sprintf($text, $tickimg, $tick);
    } else {
      $output .= sprintf($text, $failimg, $fail);
      $output .= 'Database will be created during install<br/>';
    }
    $mysqli->close();
  }
}

if ($error) {
  $output .= '<img src="images/refresh.png" class="imgInLine"/>&nbsp;<a href="javascript:void(0)" onclick="preInstall();">Try again</a>';
  print(json_encode(array('output'=> $output, 'result'=> -1)));
} else {
  print(json_encode(array('output'=> $output, 'result'=> 1)));
}
?>