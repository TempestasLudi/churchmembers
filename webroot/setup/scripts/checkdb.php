<?php
session_start();
$error = false;
$return = array();

$DB_HOST = isset($_REQUEST['host']) ? $_REQUEST['host'] : '';
$DB_ADMIN_USERNAME = isset($_REQUEST['user']) ? $_REQUEST['user'] : '';
$DB_ADMIN_PASSWORD = isset($_REQUEST['pass']) ? $_REQUEST['pass'] : '';
$DB_CORE_USERNAME = isset($_REQUEST['ccuser']) ? $_REQUEST['ccuser'] : '';
$DB_CORE_PASSWORD = isset($_REQUEST['ccpass']) ? $_REQUEST['ccpass'] : '';
$DB_DATABASE = isset($_REQUEST['db']) ? $_REQUEST['db'] : '';
$TB_PREFIX = isset($_REQUEST['prefix']) ? $_REQUEST['prefix'] : '';

$_SESSION['DB_HOST'] = '';
$_SESSION['DB_ADMIN_USERNAME'] = '';
$_SESSION['DB_ADMIN_PASSWORD'] = '';
$_SESSION['DB_CORE_USERNAME'] = '';
$_SESSION['DB_CORE_PASSWORD'] = '';
$_SESSION['DB_DATABASE'] = '';
$_SESSION['TB_PREFIX'] = '';

$mysqli = @new mysqli($DB_HOST, $DB_ADMIN_USERNAME, $DB_ADMIN_PASSWORD);

if ($mysqli->connect_error) {
  $return = array('result'=>"<span style='font-weight:bold; color:red;'>"._("Database error: Cannot connect to databaseserver with admin user") . '</span><br/>' . $mysqli->connect_error, 'resultdb'=>-1, 'next'=>false, 'clientversion'=> $mysqli->get_client_info());
  $error = true;
}

if ($error == false) {
  $mysqli = @new mysqli($DB_HOST, $DB_CORE_USERNAME, $DB_CORE_PASSWORD);

  if ($mysqli->connect_error) {
    $return = array('result'=>"<span style='font-weight:bold; color:red;'>"._("Database error: Cannot connect to databaseserver with core user") . '</span><br/>' . $mysqli->connect_error, 'resultdb'=>-1, 'next'=>false, 'clientversion'=> $mysqli->get_client_info());
    $error = true;
  }
}

if ($DB_DATABASE == '' && $error == false){
    $return = array('result'=>"<span style='font-weight:bold; color:red;'>"._("No database selected") . '</span><br/>' . $mysqli->connect_error, 'resultdb'=>-1, 'next'=>false, 'clientversion'=> $mysqli->get_client_info());
    $error = true;
}

if ($error == false) {
  if($mysqli->select_db($DB_DATABASE) ) {
    $return = array('result'=>"<span style='font-weight:bold; color:green;'>". _("Database connection succeeded")  . '</span><br/>', 'resultdb'=>1, 'next'=>true, 'clientversion'=> $mysqli->get_client_info(), 'serverversion'=> $mysqli->server_info);
  } else {
    $return = array('result'=>"<span style='font-weight:bold; color:orange;'>". _("Database connection succeeded. But database is not found an will be created.")  . '</span><br/>', 'resultdb'=>0, 'next'=>true, 'clientversion'=> $mysqli->get_client_info(), 'serverversion'=> $mysqli->server_info);
  }

  $_SESSION['DB_HOST'] = $DB_HOST;
  $_SESSION['DB_ADMIN_USERNAME'] = $DB_ADMIN_USERNAME;
  $_SESSION['DB_ADMIN_PASSWORD'] = $DB_ADMIN_PASSWORD;
  $_SESSION['DB_CORE_USERNAME'] = $DB_CORE_USERNAME;
  $_SESSION['DB_CORE_PASSWORD'] = $DB_CORE_PASSWORD;
  $_SESSION['DB_DATABASE'] = $DB_DATABASE;
  $_SESSION['TB_PREFIX'] = $TB_PREFIX;
}

print(json_encode($return));
?>