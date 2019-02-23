<?php
if (file_exists('../model/config/config.php')) {
  require_once '../model/config/config.php';
} else {
  print('No config file found');
}
if (file_exists('../setup/index.php')) {
  header('Location: setup/index.php');
  die('Setup files are still there...');
}

//********************************* Includes ***********************************************/
define('MOBILE', true);
require_once CLASSES_PATH . 'ProcessRequest.php';

//********************************* Executable code *****************************************/
?><!DOCTYPE html>
<html>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <title><?php print(__("ChurchMembers")); ?></title>
    <link type="text/css" href="../includes/jquery/css/theme/jquery-ui-1.8.20.custom.css" rel="stylesheet" />
    <link href="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.css"  rel="stylesheet" type="text/css"/>
    <link href="mobile.css" rel="stylesheet" type="text/css" id="churchstyle"/>
    <script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
    <script src="http://code.jquery.com/ui/1.8.17/jquery-ui.min.js" type="text/javascript"></script>
    <script src="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
    <script type="text/javascript" src="../includes/gmap3/gmap3.js"></script>
    <script type="text/javascript" src="googlemaps.js"></script>
    <script type="text/javascript" src="mobile.js"></script>
  </head>

  <body>

    <div data-role="page" id="indexdiv" data-title="<?php print(__("ChurchMembers")); ?>">

      <div data-role="header">
        <h1><?php print(__("ChurchMembers")); ?></h1>
        <div data-role="navbar">
          <ul>
            <li><a onclick="getHome()" class="ui-btn-active" data-icon="home"><?php print(__("Home")); ?></a></li>
            <li><a onclick="getAddress()" data-icon="grid"><?php print(__("Addresses")); ?></a></li>
            <li><a onclick="getMaps()" data-icon="star"><?php print(__("Map")); ?></a></li>
            <li><a href="login.php?logout=logout" data-icon="gear"><?php print(__("Logout")); ?></a></li>
          </ul>
        </div><!-- /navbar -->
      </div><!-- /header -->

      <div data-role="content" class="content-primary">
        <div id="searchdiv">
          <input type="search" name="search" id="search-index" value="<?php print(__("Search")); ?>" onclick="this.select();"/>
        </div>
        <div  id="ContentDiv"></div>
        <div  id="MembersDiv"></div>
        <div  id="MemberDiv"></div>
      </div><!-- /content -->

    </div><!-- /page -->

  </body>
</html>