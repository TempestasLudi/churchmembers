<?php
session_start();
//churchmembers version
$_SESSION['version'] = is_readable('../.version') ? trim(file_get_contents('../.version')) : '?';
 
$version = phpversion();
if (version_compare($version, '5.2.0', '<')) {
  die ("Minimum required version is PHP 5.2.0, PHP 5.2 or higher recommended when using foreign language");
}

if (!function_exists('gettext') || !function_exists('_') ) {
  die('PHP module "gettext" module is not enabled or the alias _() is not available');
}

//********************************* Includes ***********************************************/
if (file_exists('../model/config/config.php')) {
  require_once '../model/config/config.php';
  $_SESSION['CONFIGFILE'] = '../model/config/config.php';
} else {
  $_SESSION['CONFIGFILE'] = false;
}
//********************************* Executable code *****************************************/
$DB_HOST = defined('DB_HOST') ? DB_HOST : 'localhost';
$DB_DATABASE = defined('DB_DATABASE') ? DB_DATABASE : '';
$DB_ADMIN_USERNAME = defined('DB_ADMIN_USERNAME') ? DB_ADMIN_USERNAME : '';
$DB_ADMIN_PASSWORD = defined('DB_ADMIN_PASSWORD') ? DB_ADMIN_PASSWORD : '';
$DB_CORE_USERNAME = defined('DB_CORE_USERNAME') ? DB_CORE_USERNAME : '';
$DB_CORE_PASSWORD = defined('DB_CORE_PASSWORD') ? DB_CORE_PASSWORD : '';
$TB_PREFIX = defined('TB_PREFIX') ? TB_PREFIX : 'cc_';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <meta name="robots" content="noindex,nofollow" />
    <meta HTTP-EQUIV="Pragma" CONTENT="no-cache" />

    <title><?php print(_("ChurchMembers - Install"));?></title>
    <link rel="shortcut icon" type="image/x-icon" href="../css/images/favicon.ico" />
    <link href="../css/style.css" rel="stylesheet" type="text/css" />
    <link type="text/css" href="../includes/jquery/css/theme/jquery-ui-1.9.1.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="../includes/jquery/js/jquery-1.8.2.min.js"></script>
    <script type="text/javascript" src="../includes/jquery/js/jquery-ui-1.9.1.custom.min.js"></script>
    <script type="text/javascript" src="include/form.wizard/jquery.form.js"></script>
    <script type="text/javascript" src="include/form.wizard/jquery.validate.js"></script>
    <script type="text/javascript" src="include/form.wizard/bbq.js"></script>
    <script type="text/javascript" src="include/form.wizard/jquery.form.wizard.js"></script>

    <script type="text/javascript">
      function checkDBconnection(){
        $.getJSON('scripts/checkdb.php?action=checkconnection&db='+$('#dbase').val()+'&user='+$('#databaseuser').val()+'&pass='+$('#databasepassword').val()+'&ccuser='+$('#databasecoreuser').val()+'&ccpass='+$('#databasecorepassword').val()+'&host='+$('#databaseserver').val() + '&prefix='+$('#tableprefix').val(), function(data) {
          $('#cc-db-step1-msg').show();
          if (data['result']){
            $('#connect-msg').html(data['result'])
            $('#cc-db-client-version').html(data['clientversion']);
            if (data['next'] == true){
              $('#cc-db-server-version').html(data['serverversion']);
              $('#dbresult').val(data['resultdb']);
            } else {
              $('#dbresult').val('');
            }
          }
        })
      }

      function preInstall(){
        $.getJSON('scripts/preinstall.php?db='+$('#dbase').val()+'&user='+$('#databaseuser').val()+'&pass='+$('#databasepassword').val()+'&host='+$('#databaseserver').val(), function(data) {
          if (data['result']){
            $('#conditions').html(data['output'])
            if (data['result'] == '1'){
              $('#preinstallresult').val(1);
            } else {
              $('#preinstallresult').val('');
            }
          }
        })
      }

      function Install(){
        $.getJSON('scripts/install.php', function(data) {
          $('#progress').html(data['output'])
          if (data['result'] == '1'){
            $('#result').val(1);
            $('#deletesetup').show();
          } else {
            $('#result').val('');
          }
        })
      }

      $(document).ready(function() {
        $("#LoginButton").button();
        $("#LoginButton").click(function() { $("#loginForm").submit(); });
        $(document).ready(function(){
          $('#nojs').hide();
          $('#hasjs').show();
        });
        $("#installForm").formwizard({
          formPluginEnabled: false,
          validationEnabled: true,
          focusFirstInput : true,
          historyEnabled : true,
          textNext: '<?php print(_("Next")); ?>',
          textBack: '<?php print(_("Previous")); ?>',
          textSubmit: '<?php print(_("Close")); ?>',
          validationOptions : {
            rules: {
              installmode: "required",
              databaseuser: "required",
              databaseserver: "required",
              databasecoreuser: "required",
              dbase: "required",
              ccadmin: "required",
              ccadminemail: {
                required: true,
                email: true
              },
              ccpassword: "required",
              ccpasswordconfirm: "required",
              dbresult: "required",
              preinstallresult: "required",
              installresult: "required"
            },
            messages: {
              installmode: "Please specify your installation choice",
              ccadminemail: {
                required: "Please specify your email",
                email: "Correct format is name@domain.com"
              }
            }
          },
          remoteAjax : {
            "adminuser" : { // add a remote ajax call when moving next from the second step
              url : 'scripts/preinstall.php?db='+$('#dbase').val()+'&user='+$('#databaseuser').val()+'&pass='+$('#databasepassword').val()+'&host='+$('#databaseserver').val(),
              dataType : 'json',
              success: function(data){
                if (data['result']){
                  $('#conditions').html(data['output'])
                  if (data['result'] == '1'){
                    $('#preinstallresult').val(1);
                  } else {
                    $('#preinstallresult').val('');
                  }
                }
                return true;
              }
            },
            "preinstall" : { // add a remote ajax call when moving next from the second step
              url : 'scripts/install.php',
              dataType : 'json',
              success: function(data){
                $('#progress').html(data['output'])
                if (data['result'] == '1'){
                  $('#installresult').val(1);
                  $('#deletesetup').show();
                } else {
                  $('#installresult').val('');
                }
                return true;
              }
            },
            "tryinstall" : { // add a remote ajax call when moving next from the second step
              url : 'scripts/install.php',
              dataType : 'json',
              success: function(data){
                $('#progress').html(data['output'])
                if (data['result'] == '1'){
                  $('#installresult').val(1);
                  $('#deletesetup').show();
                } else {
                  $('#installresult').val('');
                }
                return true;
              }
            }
          }
        }
      );

      });
    </script>
  </head>
  <body>
    <div id="ctr" align="center">
      <noscript><br/><p class="nojs"><span class="ui-state-error"><?php print(_("Javascript is disabled on your computer. You need Javascript to run this application")); ?></span></p></noscript>
      <div id="install">
        <div id="formimage">
          <div class="ctr"><img src="images/computer_process.png" alt="<?php print(_("Welcome")); ?>" /></div>
        </div>
        <div id="form">

          <form id="installForm" method="post" action="scripts/install.php" class="bbq" style="border:0">

            <div id="fieldWrapper">
              <div class="step" id="start">
                <h2><?php print(_("Welcom to the ChurchMembers installation")) . " (". $_SESSION['version'] . ")"; ?></h2><br/>
                <p><?php print(_("This program will guide you through the rest of the installation."));
print("<br/><span style='font-weight:bold; color:orange;'>". _("After installation remove the setup directory."). "</span>");?></p>
                <div class="form-block"><br/>
<?php print(_("Please select the `Next` button to continue:"));?>
                </div>
              </div>
              <div class="step" id="options" >
                <h2> <?php print(_("Install Options")); ?></h2>
                <br/>
                <table class="options">
                  <tbody>
                    <tr>
                      <th style="width: 43%;" valign="top">
                        <label>
                          <input type="radio" name="installmode" id="installmode" value="0" checked="checked" /><?php print(_("New Installation")); ?>
                        </label>
                      </th>
                      <td><?php print(_("Install a new copy of  Churchmembers - <strong>Please note this option may overwrite any data inside your database.</strong>")); ?>
                      </td>
                    </tr>
                    <tr style="display:none;">
                      <th>
                        <label>
                          <input type="radio" name="installmode" id="installmode" value="1" disabled="disabled"  /><?php print(_("Upgrade Existing Install")); ?>
                        </label>
                      </th>
                      <td><?php print(_("Upgrade your current files and database.")); ?></td>
                    </tr>
                  </tbody>
                </table>


              </div>
              <div id="connection" class="step">
                <h2><?php print(_("Connection Information")); ?></h2>
                <h3><?php print(_("Database connection and login information"));?></h3>
                <p><?php print(_("Please enter the following information to connect to your ChurchMember database. If there is no database yet, the installer will attempt to create it for you. (This may fail if your database configuration or the database user permissions do not allow it.)"));?></p>
                <p class="error"></p>
                <br/>
                <div class="labelHolder">
                  <label for="databaseserver"><?php print(_("Database host:"))?></label>
                  <input id="databaseserver" value="<?php print($DB_HOST);?>" name="databaseserver" />
                  &nbsp;<span class="field_error" id="database-server-error"></span>
                </div>
                <div class="labelHolder">
                  <label for="databaseuser"><?php print(_("Database admin/editor login name:"))?></label>
                  <input id="databaseuser" name="databaseuser" value="<?php print($DB_ADMIN_USERNAME);?>"/>
                  &nbsp;<span class="field_error" id="database-user-error"></span>
                </div>
                <div class="labelHolder">
                  <label for="databasepassword"><?php print(_("Database admin/editor password:"))?></label>
                  <input id="databasepassword" type="text" name="databasepassword" value="<?php print($DB_ADMIN_PASSWORD);?>" />
                  &nbsp;<span class="field_error" id="database-password-error"></span>
                </div>
                <div class="labelHolder">
                  <label for="databasecoreuser"><?php print(_("Database core login name:"))?></label>
                  <input id="databasecoreuser" name="databasecoreuser" value="<?php print($DB_CORE_USERNAME);?>"/>
                  &nbsp;<span class="field_error" id="database-user-error"></span>
                </div>
                <div class="labelHolder">
                  <label for="databasecorepassword"><?php print(_("Database core password:"))?></label>
                  <input id="databasecorepassword" type="text" name="databasecorepassword" value="<?php print($DB_CORE_PASSWORD);?>" />
                  &nbsp;<span class="field_error" id="database-password-error"></span>
                </div>
                <div class="labelHolder">
                  <label for="dbase"><?php print(_("Database name:"))?></label>
                  <input id="dbase" value="<?php print($DB_DATABASE);?>" name="dbase" />
                  &nbsp;<span class="field_error" id="dbase-error"></span>
                </div>
                <div class="labelHolder">
                  <label for="tableprefix"><?php print(_("Table prefix:"))?></label>
                  <input id="tableprefix" value="<?php print($TB_PREFIX);?>" name="tableprefix" />
                  &nbsp;<span class="field_error" id="tableprefix_error"></span>
                </div>
                <br/>
                <p>&rarr;&nbsp;<a href="javascript:void(0);" id="cc-testconn" onclick="checkDBconnection()"><?php print(_("Test database server connection & create database if necessary."));?></a></p>

                <div id="cc-db-step1-msg" class="cc-hidden2" style="display: none;"><br/>
                  <span><?php print(_("Connecting to database server:"))?></span>&nbsp;<span id="connect-msg" style="font-weight: bold;"></span>

                  <p id="cc-db-info">
                    <br />- <?php print(_("Checking MySQL server version:"))?>&nbsp;<span id="cc-db-server-version"></span>
                    <br />- <?php print(_("Checking MySQL client version:"))?>&nbsp;<span id="cc-db-client-version"></span>
                  </p>
                  <input id="dbresult" name="dbresult" type="hidden"/>
                </div>
                <br />
              </div>

              <div id="adminuser" class="step">
                <h2><?php print(_("Default Admin User")); ?></h2>
                <p><?php print(_("Now you&#39;ll need to enter some details for the main administrator account. You can fill in your own name here, and a password you&#39;re not likely to forget. You&#39;ll need these to log into Admin once setup is complete."));?></p>
                <div id="cc-db-step3" class="cc-hidden">

                  <br/>
                  <div class="labelHolder">
                    <label for="ccadmin"><?php print(_("Administrator username:"))?></label>
                    <input type="text" name="ccadmin" id="ccadmin" value="" />
                    &nbsp;<span class="field_error" id="ccadmin_error"></span>
                  </div>
                  <div class="labelHolder">
                    <label for="ccpassword"><?php print(_("Administrator password:"))?></label>
                    <input type="text" id="ccpassword" name="ccpassword" value="" />
                    &nbsp;<span class="field_error" id="ccpassword_error"></span>
                  </div>
                  <div class="labelHolder">
                    <label for="ccadminemail"><?php print(_("Administrator email:"))?></label>
                    <input type="text" name="ccadminemail" id="ccadminemail" value="" />
                    &nbsp;<span class="field_error" id="ccadminemaill_error"></span>
                  </div>

                </div>
              </div>

              <div id="preinstall" class="step">
                <h2><?php print(_("Installation conditions & creating directories")); ?></h2>
                <p></p>
                <div id="conditions"></div>
                <input id="preinstallresult" name="preinstallresult" type="hidden"/>

              </div>

              <div id="tryinstall" class="step">
                <h2><?php print(_("Install Churchmembers")); ?></h2>
                <p></p>
                <div id="progress"><?php print(_("Installing Churchmembers...")); ?></div>
                <div id="deletesetup" style="display:none;">
                  <h2><span style='font-weight:bold; color:green;'><?php print(_("Installation is successful.")); ?></span></h2>
                </div>
                <input id="installresult" name="installresult" type="hidden"/>
              </div>

              <div id="finish" class="step">
                <h2><?php print(_("Remove setup files")); ?></h2>
                <p><?php print(_("Do you want to remove the setupfiles? This is nessecary to start the churchmembers application.")); ?>
                  <br/><br/>
                  <label>
                    <input type="checkbox" name="deletesetup" id="deletesetup" value="1" checked="checked" /><?php print(_("Yes, delete the setup files")); ?>
                  </label></p>
                <div id="result"></div>
                <input id="finalstep" name="finalstep" type="hidden" value="1"/>
              </div>

            </div>
            <div id="demoNavigation">
              <br/>
              <input class="navigation_button" id="back" value="Next" type="reset" />
              <input class="navigation_button" id="next" value="Back" type="submit" />
            </div>
          </form>
        </div>
        <div id="clr"></div>
      </div>

    </div>
  </body>
</html>