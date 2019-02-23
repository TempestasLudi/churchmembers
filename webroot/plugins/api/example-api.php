<?php
echo "<h1>API TEST</h1><pre>";

require_once '../../model/config/config.php';
require_once "../../model/classes/API.php";
$api_key = "";

$churchmembers = new API($api_key);

//LOGIN
//$churchmembers->API_Login($user, $pass);
echo "<h2>Login</h2>";
$churchmembers->API_Login('core', 'core');
print_r($_SESSION);

//MEMBERINFO
echo "<h2>Memberinfo (1)</h2>";
$user = $churchmembers->database->getMemberById(2);
print_r($user);


//MEMBERGROUP
echo "<h2>Groupinfo (1)</h2>";
$group = $churchmembers->database->getMemberInGroup(2);
print_r($group);


//LOG OUT
$churchmembers->API_Logout();

echo " </pre>";
?>