<?php
//die("You are not allowed to see this page (comment first line of the file to access it!)");
require_once '../model/config/config.php';
require_once '../model/classes/API.php';

$api_key = 'gf%245a059_9ga89^jh*';
$churchmembers = new API($api_key);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Churchmembers Diagnostic Tool</title>
    <style type="text/css">
      body {background-color:#f1f1ef;margin:0;padding:20px;}
      * {font-family:arial, sans-serif;font-size:11px;color:#006}
      h1 {font-size: 20px; color:black}
      thead tr{background-color: #ccc; font-weight:bold;}
      tr.dump{background-color: #ee9;}
      tr.passed{background-color: #ae9;}
      tr.failed{background-color: #ea9;}
      tr.warning{background-color: #f90;}
      td,th  {padding: 3px 6px;text-align:left;}
      td.col{font-weight: bold;}
    </style>
  </head>

  <body>
    <h1>Churchmembers Diagnostic Tool</h1>
    <table width='700' border='0' cellpadding='0' cellspacing='1'>
      <thead><tr><td>Name</td><td>Result</td><td>Info</td></tr>
      </thead>
      <tbody>
        <?php
        $class="passed";
        $text = "";
        if(isset($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) == "on") {
          $text = "Https protocol detected. ";
          if ($_SESSION['SYSTEMSETTINGS']->system_secure) {
            $class="warning";
            $text="Enable the system_secure setting in the admin panel! This is needed for secure cookies";
          }

        }else {
          $class="warning";
          $text = "You are not using SSL encryption, or it was not detected by the server. Be aware that it is strongly recommended to secure all communication of data over the network.";
        }
        ?>
        <tr class="<?php echo $class;?>">
          <th >SSL Encryption</th>
          <td><?php echo $class;?></td>
          <td><?php echo $text; ?></td>
        </tr>

        <?php
// PHP VERSION
        $version = phpversion();
        $error = "";
        $class="passed";
        if (version_compare($version, '5.2.0', '<')) {
          $error = "<br/>Minimum required version is PHP 5.2.0, PHP 5.2 or higher recommended when using foreign language";
          $class="failed";
        }
        ?>
        <tr class="<?php echo $class;?>">
          <th >PHP version </th>
          <td><?php echo $class;?></td>
          <td><?php echo $version . '<br/>Server software ' . $_SERVER['SERVER_SOFTWARE'] . $error; ?></td>
        </tr>

        <?php
// GD VERSION
        $error = "";
        $class="passed";
        $text = "Yes";
        if (!function_exists("gd_info") || !function_exists("imagecopyresized") || !function_exists("imagecopyresampled")) {
          $class="warning";
          $text = "No";
          $error = "<br/>GD is required for generating thumbnails";
        }
        ?>
        <tr class="<?php echo $class;?>">
          <th >PHP GD version </th>
          <td><?php echo $class;?></td>
          <td><?php echo $text . $error; ?></td>
        </tr>

        <?php
        $class="passed";
        $text = INCLUDES_PATH . "phpThumb/cache/ - is writable";
        if (!is_writable(INCLUDES_PATH . "phpThumb/cache/")) {
          $class = "failed";
          $text = INCLUDES_PATH . "phpThumb/cache/ - is NOT writable";
        }
        ?>
        <tr class="<?php echo $class;?>">
          <th>phpThumb cache</th>
          <td><?php echo $class;?></td>
          <td><?php echo $text; ?></td>
        </tr>

        <?php
        $class="passed";
        $text = INCLUDES_PATH . "phpThumb/temp/  - is writable";
        if (!is_writable(INCLUDES_PATH . "phpThumb/temp/")) {
          $class = "failed";
          $text = INCLUDES_PATH . "phpThumb/temp/ - is NOT writable";
        }
        ?>
        <tr class="<?php echo $class;?>">
          <th>phpThumb temp dir</th>
          <td><?php echo $class;?></td>
          <td><?php echo $text; ?></td>
        </tr>

        <?php
        $class="passed";
        $text = INCLUDES_PATH . "fileuploader/server/uploads/ - is writable";
        if (!is_writable(INCLUDES_PATH . "fileuploader/server/uploads/")) {
          $class = "failed";
          $text = INCLUDES_PATH . "fileuploader/server/uploads/ - is NOT writable";
        }
        ?>
        <tr class="<?php echo $class;?>">
          <th>FileUploader temp dir</th>
          <td><?php echo $class;?></td>
          <td><?php echo $text; ?></td>
        </tr>

        <?php
// APACHE MODULE MOD_headers
        $error = "";
        $class="passed";
        $text = "Yes";
        if ((!in_array('mod_headers', apache_get_modules())) and (!in_array('headers_module', apache_get_modules()))) {
          $error = '<br/>Apache module "mod_headers/headers_module" is not enabled';
          $class = "failed";
          $text = "No";
        }
        ?>
        <tr class="<?php echo $class;?>">
          <th >Mod_headers enabled</th>
          <td><?php echo $class;?></td>
          <td><?php echo $text . $error; ?></td>
        </tr>
        <?php

// PHP gettext function
        $error = "";
        $class="passed";
        $text = "Yes";
        if (!function_exists('gettext') || !function_exists('_') ) {
          $error = '<br/>PHP module "gettext" module is not enabled or the alias _() is not available ';
          $class = "failed";
          $text = "No";
        }
        ?>
        <tr class="<?php echo $class;?>">
          <th >PHP gettext module enabled</th>
          <td><?php echo $class;?></td>
          <td><?php echo $text . $error; ?></td>
        </tr>

        <?php
        $class="dump";
        $error = "";
        ?>
        <tr class="dump">
          <th >BASE_PATH</th>
          <td><?php echo $class;?></td>
          <td><?php echo BASE_PATH ?></td>
        </tr>
        <tr class="dump">
          <th >BASE_URL</th>
          <td><?php echo $class;?></td>
          <td><?php echo BASE_URL ?></td>
        </tr>
        <tr class="dump">
          <th >CLASSES_PATH</th>
          <td><?php echo $class;?></td>
          <td><?php echo CLASSES_PATH ?></td>
        </tr>
        <tr class="dump">
          <th >PROCESSOR_PATH</th>
          <td><?php echo $class;?></td>
          <td><?php echo PROCESSOR_PATH ?></td>
        </tr>
        <tr class="dump">
          <th >TEMPLATES_PATH</th>
          <td><?php echo $class;?></td>
          <td><?php echo TEMPLATES_PATH ?></td>
        </tr>
        <tr class="dump">
          <th >I18N_PATH</th>
          <td><?php echo $class;?></td>
          <td><?php echo I18N_PATH ?></td>
        </tr>
        <tr class="dump">
          <th >CONFIG_PATH</th>
          <td><?php echo $class;?></td>
          <td><?php echo CONFIG_PATH ?></td>
        </tr>
        <tr class="dump">
          <th >INCLUDES_PATH</th>
          <td><?php echo $class;?></td>
          <td><?php echo INCLUDES_PATH ?></td>
        </tr>
        <?php
        $class="passed";
        $text = PHOTO_URL . " - is writable";
        if (!is_writable('../'. PHOTO_URL)) {
          $class = "failed";
          $text = PHOTO_URL . " - is NOT writable";
        }
        ?>
        <tr class="<?php echo $class;?>">
          <th >PHOTO_URL</th>
          <td><?php echo $class;?></td>
          <td><?php echo $text; ?></td>
        </tr>
        <?php
        $class="passed";
        $text = LOGS_PATH . " - is writable";
        if (!is_writable(LOGS_PATH)) {
          $class = "failed";
          $text = LOGS_PATH . " - is NOT writable";
        }
        ?>
        <tr class="<?php echo $class;?>">
          <th >LOGS_PATH</th>
          <td><?php echo $class;?></td>
          <td><?php echo $text; ?></td>
        </tr>
        <?php
        $class="passed";
        $text = BACKUP_PATH . " - is writable";
        if (!is_writable(BACKUP_PATH)) {
          $class = "failed";
          $text = BACKUP_PATH . " - is NOT writable";
        }
        ?>
        <tr class="<?php echo $class;?>">
          <th >BACKUP_PATH</th>
          <td><?php echo $class;?></td>
          <td><?php echo $text; ?></td>
        </tr>
        <?php
        $class="passed";
        $text = TEMP_PATH . " - is writable";
        if (!is_writable(TEMP_PATH)) {
          $class = "failed";
          $text = TEMP_PATH . " - is NOT writable";
        }
        ?>
        <tr class="<?php echo $class;?>">
          <th >TEMP_PATH</th>
          <td><?php echo $class;?></td>
          <td><?php echo $text; ?></td>
        </tr>
        <tr class="dump">
          <th >PHP Limits variables</th>
          <td><?php echo $class;?></td>
          <td><?php
            echo "Upload Max Size: " . ini_get("upload_max_filesize") . "<br/>";
            echo "Memory Limit: " . ini_get("memory_limit") . "<br/>";
            echo "Max execution time: " . ini_get("max_execution_time") . "<br/>";
            echo "Safe Mode: ". ini_get("safe_mode") . "<br/>";
            echo "Safe Mode GID: ". ini_get("safe_mode_gid") . "<br/>";
            echo "Xml parser enabled: ". function_exists("xml_parser_create") . "<br/>";
            ?></td>
        </tr>
        <tr class="dump">
          <th >Magic quotes</th>
          <td><?php echo $class;?></td>
          <td><?php echo get_magic_quotes_gpc()? "Yes":"No" ?></td>
        </tr>
        <tr class="dump">
          <th >Client Browser </th>
          <td><?php echo $class;?></td>
          <td><?php echo $_SERVER['HTTP_USER_AGENT']; ?></td>
        </tr>
        <tr class="dump">
          <th>&nbsp;</th>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr class="dump">
          <th>Remote Validation</th>
          <td><?php echo $class;?></td>
          <td><pre><?php echo $_SESSION['SYSTEMSETTINGS']->auth_enabled ? "Yes":"No"?></pre></td>
        </tr>
        <tr class="dump">
          <th>Remote Validation Options</th>
          <td><?php echo $class;?></td>
          <td><pre><?php isset($_SESSION['SYSTEMSETTINGS']->auth_validationurl) ? print_r($_SESSION['SYSTEMSETTINGS']->auth_validationurl) :  ''?></pre></td>
        </tr>
        <tr class="dump">
          <th>Remote Validation Url Contents</th>
          <td><?php echo $class;?></td>
          <td><pre><?php
              if (isset($auth_driver_options['VALIDATION_URL'])) {
                $opts = array('http' => array('header'=> 'Cookie: ' . $_SERVER['HTTP_COOKIE']."\r\n"));
                $context = stream_context_create($opts);
                $validationstr = file_get_contents($auth_driver_options['VALIDATION_URL'], false, $context);
                var_dump($validationstr); 
              }
              ?></pre></td>
        </tr>
        <tr class="dump">
          <th>SESSION variables</th>
          <td><?php echo $class;?></td>
          <td><pre><?php print_r($_SESSION);?></pre></td>
        </tr>
        <tr class="dump">
          <th>SERVER variables</th>
          <td><?php echo $class;?></td>
          <td><pre><?php print_r($_SERVER);?></pre></td>
        </tr>       
      </tbody>
    </table>
  </body>
</html>
<?php
$churchmembers->API_Logout();
?>