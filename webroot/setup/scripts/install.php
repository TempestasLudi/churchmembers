<?php
session_start();

$BASE_PATH = realpath(dirname(__FILE__) . '/../../') . '/';
$CONFIGFILE =  $BASE_PATH . 'model/config/config.php';
require_once $CONFIGFILE;

$textcreate = '%s Trying to create %s: %s';

$failimg = '<img src="images/delete.png" class="imgInLine"/>';
$fail = _("<span style='font-weight:bold; color:red;'>FAIL!</span> <br/><br/>");
$tickimg = '<img src="images/accept.png" class="imgInLine"/>';
$tick = _("<span style='font-weight:bold; color:green;'>OK!</span>  <br/><br/>");

$error = false;
$output = '';


if (isset($_REQUEST['finalstep']) && $_REQUEST['finalstep'] == '1') {
  if (isset($_REQUEST['deletesetup']) && $_REQUEST['deletesetup'] == '1') {
    recursive_remove_directory(realpath(dirname(__FILE__) . '/../'));
  }
  header('Location: ' . BASE_URL);
  die();
}

//Create db if nessecary
$textdbcreate = '%s Creating Database: %s';
$textdbexists = '%s Database is available %s';
$textdbfilenotfound = '%s Database dump %s is not found %s';
$texttablescreate = '%s Creating tables %s';

$dbdump = '../database/database.sql';
$sqldump = file_get_contents($dbdump);

//set prefix for tables
$tablelist = array();
$tablesreplacement = array();
$tables = array('addresses','failedaccess','groupmembers','groups', 'members' , 'membertypes','relations','relationtypes', 'settings', 'users',  'usertypes');
foreach($tables as $table) {
  array_push($tablelist, "/`$table`/");
  array_push($tablesreplacement, "`". TB_PREFIX .$table. "`");
}

$sqldump = preg_replace($tablelist, $tablesreplacement, $sqldump);

if ($sqldump != false) {
  $mysqli = new mysqli(DB_HOST, DB_ADMIN_USERNAME, DB_ADMIN_PASSWORD);

  if ($mysqli->select_db(DB_DATABASE)) {
    $output .= sprintf($textdbexists, $tickimg, $tick);
    $query = 'DROP DATABASE ' . DB_DATABASE;
    $mysqli->query($query);
  }

  $query = 'CREATE DATABASE IF NOT EXISTS ' . DB_DATABASE;
  if ($mysqli->query($query)) {
    $output .= sprintf($textdbcreate, $tickimg, $tick);

    $mysqli->select_db(DB_DATABASE);
    if ($mysqli->multi_query($sqldump)) {
      $i = 0;
      do {
        $i++;
      } while ($mysqli->more_results() && $mysqli->next_result());

    }
    if ($mysqli->errno) {
      $output .= sprintf($texttablescreate, $failimg, $fail);
      $output .= "Batch execution prematurely ended on statement $i.\n <br/>";
      $output .= var_export($mysqli->error, true) . "</br>";
      $error = true;
    } else {
      $output .= sprintf($texttablescreate, $tickimg, $tick);
    }

  } else {
    $output .= sprintf($textdbcreate, $failimg, $fail);
    $output .= var_export($mysqli->error, true) . "</br>";
    $error = true;
  }

} else {
  $output .= sprintf($textdbfilenotfound, $failimg, $dbdump, $fail);
  $error = true;
}

if (!$error) {
//Creating admin/core & editor users
  $textusercreate = '%s Creating user <strong>%s</strong> (password: <strong>%s</strong>): %s';
  $users = array($_SESSION['ADMIN_USER']=>array('pass'=>$_SESSION['ADMIN_PASS'], 'level'=>1), 'editor'=>array('pass'=>'editor'.date('B'), 'level'=>10), 'core'=>array('pass'=>'core'.date('B'), 'level'=>999));
  foreach($users as $user=>$properties) {
    $query = "INSERT INTO  `" . TB_PREFIX . "users` (`USER_username`, `USER_password`, `USERTYPE_id`) VALUES ('$user',  AES_ENCRYPT('".$properties['pass']."','".AES_KEY."'),  '".$properties['level']."');";
    if ($mysqli->query($query)) {
      $output .= sprintf($textusercreate, $tickimg, $user, $properties['pass'], $tick);
    } else {
      $output .= sprintf($textusercreate, $failimg, $user, $properties['pass'], $fail);
      $error = true;
    }
  }
}

if (!$error) {
//setting cookiename
  $textusercreate = '%s Setting cookiename <strong>CM-$%s</strong>: %s';
  $cookie = rand_sha1(5);
  $query = "UPDATE  `" . TB_PREFIX . "settings` SET  `SETTINGS_value` =  'CM-$" . $cookie . "' WHERE  `" . TB_PREFIX . "settings`.`SETTINGS_name` = 'cookie_name';";
  if ($mysqli->query($query)) {
    $output .= sprintf($textusercreate, $tickimg, $cookie, $tick);
  } else {
    $output .= sprintf($textusercreate, $failimg, $cookie, $fail);
    $error = true;
  }
}

if (!$error && $_SESSION['version'] != '?') {
  //setting version number
  $version = $_SESSION['version'];
  $query = "UPDATE  `" . TB_PREFIX . "settings` SET  `SETTINGS_value` =  '$version' WHERE  `" . TB_PREFIX . "settings`.`SETTINGS_name` = 'system_version';";
  $mysqli->query($query);
}

if ($error) {
  $output .= '<img src="images/refresh.png" class="imgInLine"/>&nbsp;<a href="javascript:void(0)" onclick="Install()">Try again</a>';
  print(json_encode(array('output'=> $output, 'result'=> -1)));
} else {
  print(json_encode(array('output'=> $output, 'result'=> 1)));
}

function rand_sha1($length) {
  $max = ceil($length / 40);
  $random = '';
  for ($i = 0; $i < $max; $i ++) {
    $random .= sha1(microtime(true).mt_rand(10000,90000));
  }
  return substr($random, 0, $length);
}

// ------------ lixlpixel recursive PHP functions -------------
// recursive_remove_directory( directory to delete, empty )
// expects path to directory and optional TRUE / FALSE to empty
// ------------------------------------------------------------
function recursive_remove_directory($directory, $empty=FALSE) {
  if(substr($directory,-1) == '/') {
    $directory = substr($directory,0,-1);
  }
  if(!file_exists($directory) || !is_dir($directory)) {
    return FALSE;
  }elseif(is_readable($directory)) {
    $handle = opendir($directory);
    while (FALSE !== ($item = readdir($handle))) {
      if($item != '.' && $item != '..') {
        $path = $directory.'/'.$item;
        if(is_dir($path)) {
          recursive_remove_directory($path);
        }else {
          unlink($path);
        }
      }
    }
    closedir($handle);
    if($empty == FALSE && is_writeable($directory)) {
      if(!rmdir($directory)) {
        return FALSE;
      }
    }
  }
  return TRUE;
}
?>