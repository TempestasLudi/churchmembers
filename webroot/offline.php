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
require_once 'model/classes/Localization.php';

//********************************* Executable code *****************************************/
function __($string) {
  global $locatization;
  if (!isset($locatization)) {
    $locatization = new Localization();
  }
  return $locatization->getTranslation($string);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <meta name="robots" content="noindex,nofollow" />
    <meta HTTP-EQUIV="Pragma" CONTENT="no-cache" />

    <title><?php print(__("ChurchMembers - Offline")); ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="css/images/favicon.ico" />
    <link href="css/style.css?time=<?php echo(filemtime("css/style.css"));?>" rel="stylesheet" type="text/css" id="churchstyle"/>
    <link type="text/css" href="includes/jquery/css/theme/jquery-ui-1.9.1.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="includes/jquery/js/jquery-1.8.2.min.js"></script>
    <script type="text/javascript" src="includes/jquery/js/jquery-ui-1.9.1.custom.min.js"></script>

    <script type="text/javascript">
      $(document).ready(function() {
        $("#LoginButton").button();
        $("#LoginButton").click(function() { $("#loginForm").submit(); });

        var url = window.location.pathname;
        var filename = url.substring(url.lastIndexOf('/')+1);

        if (filename != 'offline.php'){
          document.location.href = '<?php print(BASE_URL); ?>offline.php';
        }
      });
    </script>

    <?php if ((defined('GOOGLEANALYTICS_ACCOUNT')) and (GOOGLEANALYTICS_ACCOUNT != "")) { ?>
      <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', '<?php print(GOOGLEANALYTICS_ACCOUNT); ?>']);
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
          <h2><?php print(__("Maintenance")); ?></h2>
          <p></p>
          <div id="hasjs">
            <form action="login.php" method="post" name="login_Form" id="login_Form">
              <div class="form-block">
                <br/>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td>&nbsp;</td>
                    <td><?php print(__("Churchmembers is currently under maintenance and therefore not available. Please try again next hour.")); ?></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td></td>
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
          <div class="ctr"><img src="css/images/maintenance.png" alt="<?php print(__("Maintenance")); ?>" /></div>
        </div>
        <div id="clr"></div>
      </div>
    </div>
  </body>
</html>