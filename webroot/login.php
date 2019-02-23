<?php
if (file_exists('model/config/config.php')) {
  require_once 'model/config/config.php';
} else {
  print('No config file found');
}
if (file_exists('setup/index.php')) {
  header('Location: setup/index.php?');
  die('Setup files are still there...');
}

//********************************* Includes ***********************************************/
require_once CLASSES_PATH . 'ProcessRequest.php';

//********************************* Executable code *****************************************/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <meta name="robots" content="noindex,nofollow" />
    <meta HTTP-EQUIV="Pragma" CONTENT="no-cache" />

    <title><?php print(__("ChurchMembers - Login"));?></title>
    <link rel="shortcut icon" type="image/x-icon" href="css/images/favicon.ico" />
    <link href="css/style.css?time=<?php echo(filemtime("css/style.css"));?>" rel="stylesheet" type="text/css" id="churchstyle"/>
    <link type="text/css" href="includes/jquery/css/theme/jquery-ui-1.9.1.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="includes/jquery/js/jquery-1.8.2.min.js"></script>
    <script type="text/javascript" src="includes/jquery/js/jquery-ui-1.9.1.custom.min.js"></script>

    <script type="text/javascript">
      $(document).ready(function() {
        $("#LoginButton").button();
        $("#LoginButton").click(function() { $("#loginForm").submit(); });
        $(document).ready(function(){
          $('#nojs').hide();
          $('#hasjs').show();
        });

        var url = window.location.pathname;
        var filename = url.substring(url.lastIndexOf('/')+1);

        if (filename != 'login.php'){
          document.location.href = '<?php print(BASE_URL); ?>login.php';
        }
      });
    </script>
    <?php if ((defined('GOOGLEANALYTICS_ACCOUNT')) and (GOOGLEANALYTICS_ACCOUNT != "") ) {?>
    <script type="text/javascript">
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', '<?php print(GOOGLEANALYTICS_ACCOUNT) ;?>']);
      _gaq.push(['_setDomainName', 'none']);
      _gaq.push(['_setAllowLinker', true]);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = 'https://ssl.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
    </script>
      <?php } ?>
  </head>

  <body>
    <div id="ctr" align="center">
      <div id="login">
        <div id="form">
          <h2><?php print(__("User Login")); ?></h2>
          <p><?php print(__("Welcome")); ?></p>
          <noscript><br/><p class="nojs"><span class="ui-state-error"><?php print(__("Javascript is disabled on your computer. You need Javascript to run this application")); ?></span></p></noscript>
          <div id="hasjs" style="display:none">
            <form action="" method="post" name="login_Form" id="login_Form">
              <div class="form-block"><?php	if((isset($_SESSION['error'])) and ($_SESSION['error'] != "") ) {
                  print('<br/><p><span class="ui-state-error">' . $_SESSION['error'] . '</span></p>');
                  $_SESSION['error'] = "";
                } ?>
                <br/>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td><span class="inputlabel"><?php print(__("Username"));?></span></td>
                    <td><span class="inputlabel"><input name="username" type="text" style="width:200px"/></span></td>
                  </tr>
                  <tr>
                    <td><span class="inputlabel"><?php print(__("Password"));?></span></td>
                    <td><span class="inputlabel"><input name="password" type="password" style="width:200px" /></span></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td><button id="LoginButton" type="submit"><?php print(__("Login")); ?></button></td>
                  </tr>
                </table>
              </div>
            </form>
          </div>
        </div>
        <div id="formimage">
          <div class="ctr"><img src="css/images/login/key.png" alt="<?php print(__("Welcome")); ?>" /></div>
        </div>
        <div id="clr"></div>
      </div>
    </div>
  </body>
</html>