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
    <link href="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.css"  rel="stylesheet" type="text/css"/>
    <script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
    <script src="http://code.jquery.com/ui/1.8.17/jquery-ui.min.js" type="text/javascript"></script>
	  <script src="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.js" type="text/javascript"></script>


    <script src="mobile.js" type="text/javascript"></script>
  </head>

  <body>

    <div data-role="page" data-theme="a" data-content-theme="b">

      <div data-role="header">
        <h1><?php print(__("Login")); ?></h1>
      </div><!-- /header -->

      <div data-role="content" class="content-primary">
        <form action="" method="post" name="login_Form" id="login_Form" data-ajax="false">
          <fieldset>
            <div data-role="fieldcontain">
              <?php
              if((isset($_SESSION['error'])) and ($_SESSION['error'] != "") ) {
                echo '<div class="ui-bar ui-bar-e">
                        <h3>'.$_SESSION['error'].'</h3>
                     </div>';
                $_SESSION['error'] = "";
              }
              ?>
              <input type="text" name="username" id="username" value="" placeholder="<?php print(__("Username")); ?>" />
              <input type="password" name="password" id="password" value="" placeholder="<?php print(__("Password")); ?>" />
              <button type="submit" data-theme="b" name="submit" value="submit-value" class="ui-btn-hidden" aria-disabled="false"><?php print(__("Login")); ?></button>

            </div>
          </fieldset>
        </form>

      </div><!-- /content -->

      <br/>
      <center><a title="<?php print(__("ChurchMembers")); ?>" href="http://www.churchmembers.org"> <?php print(__("ChurchMembers")); ?> - <?php print($_SESSION['SYSTEMSETTINGS']->system_version); ?></a></center>

    </div><!-- /page -->

  </body>
</html>