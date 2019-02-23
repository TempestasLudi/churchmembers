<?php
if (file_exists('model/config/config.php')) {
  require_once 'model/config/config.php';
} else {
  print('No config file found');
}
if (file_exists('setup/index.php')) {
  header('Location: setup/index.php');
  die('Setup files are still there...');
}

//********************************* Includes ***********************************************/
require_once CLASSES_PATH . 'ProcessRequest.php';

//********************************* Executable code *****************************************/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <meta name="robots" content="noindex,nofollow" />
    <title><?php print(__($_SESSION['SYSTEMSETTINGS']->systemname)); ?></title>

    <link rel="shortcut icon" type="image/x-icon" href="css/images/favicon.ico" />
    <link type="text/css" href="includes/jquery/css/theme/jquery-ui-1.9.1.custom.css" rel="stylesheet" />
    <link href="css/style.css?time=<?php echo(filemtime("css/style.css"));?>" rel="stylesheet" type="text/css" id="churchstyle"/>
    <link type="text/css" href="includes/fileuploader/client/fileuploader.css" rel="stylesheet" />
    <link type="text/css" href="includes/multiselect/css/ui.multiselect.css" rel="stylesheet" />
    <link type="text/css" href="includes/colorbox/css/colorbox.css" rel="stylesheet" />
    <link rel='stylesheet' type='text/css' href='includes/fullcalendar/fullcalendar.css' />

    <script type="text/javascript" src="includes/jquery/js/jquery-1.8.2.min.js"></script>
    <script type="text/javascript" src="includes/jquery/js/jquery-ui-1.9.1.custom.min.js"></script>
    <script type="text/javascript" src="includes/jstree/jstree.js"></script>
    <script type="text/javascript" src="model/i18n/locale.js.php"></script>
    <script type="text/javascript" src="includes/fileuploader/client/fileuploader.js"></script>
    <script type="text/javascript" src="includes/multiselect/js/ui.multiselect.js"></script>
    <script type="text/javascript" src="includes/colorbox/js/jquery.colorbox-min.js"></script>
    <script type='text/javascript' src='includes/fullcalendar/fullcalendar.min.js'></script>
    <script type="text/javascript" src="includes/tiny_mce/jquery.tinymce.js"></script>
    <script type="text/javascript" src="model/javascript/jail.min.js"></script>
    <script type="text/javascript" src="model/javascript/core.js?time=<?php echo(filemtime("model/javascript/core.js"));?>"></script>
    <script type="text/javascript" src="model/javascript/core-ajax.js?time=<?php echo(filemtime("model/javascript/core-ajax.js"));?>"></script>

    <?php
    if ($_SESSION['USER']->checkUserrights('edit_mode')) {
      ?>
      <script type="text/javascript" src="model/javascript/editor.js?time=<?php echo(filemtime("model/javascript/editor.js"));?>"></script>
      <script type="text/javascript" src="model/javascript/editor-ajax.js?time=<?php echo(filemtime("model/javascript/editor-ajax.js"));?>"></script>

      <link type="text/css" href="includes/fileuploader/client/fileuploader.css" rel="stylesheet" />
      <script type="text/javascript" src="includes/fileuploader/client/fileuploader.js"></script>
      <?php
    }

    if ($_SESSION['USER']->checkUserrights('view_map')) {
      ?>
      <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
      <script type="text/javascript" src="includes/gmap3/gmap3.js?time=<?php echo(filemtime("includes/gmap3/gmap3.js"));?>"></script>
      <script type="text/javascript" src="model/javascript/googlemaps.js?time=<?php echo(filemtime("model/javascript/googlemaps.js"));?>"></script>
      <?php
    }

    if ($_SESSION['USER']->checkUserrights('view_report')) {
      ?>
      <link rel="stylesheet" href="includes/jqgrid/css/ui.jqgrid.css" type="text/css" media="screen" charset="utf-8" />
      <script type="text/javascript" src="includes/jqgrid/js/i18n/grid.locale-nl.js"></script>
      <script type="text/javascript" src="includes/jqgrid/js/jquery.jqGrid.min.js"></script>
      <script type="text/javascript" src="model/javascript/core-report.js?time=<?php echo(filemtime("model/javascript/core-report.js"));?>"></script>
      <?php
    }

    if ($_SESSION['USER']->checkUserrights('view_admin')) {
      ?>
      <script type="text/javascript" src="model/javascript/admin.js?time=<?php echo(filemtime("model/javascript/admin.js"));?>"></script>
      <?php
    }

    if ($_SESSION['SYSTEMSETTINGS']->google_analytics_accountid != "") {
      ?>
      <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', '<?php print($_SESSION['SYSTEMSETTINGS']->google_analytics_accountid); ?>']);
        _gaq.push(['_setDomainName', '<?php print(($_SESSION['SYSTEMSETTINGS']->google_analytics_domainname != '') ? $_SESSION['SYSTEMSETTINGS']->google_analytics_domainname : 'none'); ?>']);
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
      <div id="wrapper" class="ui-corner-all">
        <!-- *********************   Header with menu   ***************************************************** -->
        <div id="header" class="ui-widget-header">
          <div id="menu">
            <div id="PagesMenu" class="submenu">
              <button id="HomeButton"><img src="css/images/menuitems/home.png" alt="<?php print(__("Home")); ?>" /><br/><span class="buttontext"><?php print(__("Home")); ?></span></button>
              <?php if ($_SESSION['USER']->checkUserrights('view_address')) {
                ?>
                <button id="AddressButton"><img src="css/images/menuitems/address.png" alt="<?php print(__("Addresses")); ?>" /><br/><span class="buttontext"><?php print(__("Addresses")); ?></span></button>
                <?php
              }
              if ($_SESSION['USER']->checkUserrights('view_groups')) {
                ?>
                <button id="GroupsButton"><img src="css/images/menuitems/groups.png" alt="<?php print(__("Groups")); ?>" /><br/><span class="buttontext"><?php print(__("Groups")); ?></span></button>
                <?php
              }
              if ($_SESSION['USER']->checkUserrights('view_report')) {
                ?>
                <button id="ReportButton"><img src="css/images/menuitems/lists.png" alt="<?php print(__("Search")); ?>" /><br/><span class="buttontext"><?php print(__("Search")); ?></span></button>
                <?php
              }
              if (1) {
                ?>
                <button id="ListsButton"><img src="css/images/menuitems/printer.png" alt="<?php print(__("Lists")); ?>" /><br/><span class="buttontext"><?php print(__("Lists")); ?></span></button>
                <?php
              }
              if ($_SESSION['USER']->checkUserrights('view_map')) {
                ?>
                <button id="MapsButton"><img src="css/images/menuitems/map.png" alt="<?php print(__("Map")); ?>" /><br/><span class="buttontext"><?php print(__("Map")); ?></span></button>
                <?php
              }
              if (($_SESSION['USER']->checkUserrights('send_mail')) && ($_SESSION['SYSTEMSETTINGS']->mail_use === true)) {
                ?>
                <button id="EmailButton"><img src="css/images/menuitems/mail_send.png" alt="<?php print(__("Send mail")); ?>" /><br/><span class="buttontext"><?php print(__("Send mail")); ?></span></button>
                <?php
              }
              if (1) {
                ?>
                <button id="PhotoButton"><img src="css/images/menuitems/photo_camera.png" alt="<?php print(__("Photobook")); ?>" /><br/><span class="buttontext"><?php print(__("Photobook")); ?></span></button>
                <?php
              }
              if ($_SESSION['USER']->checkUserrights('view_archive')) {
                ?>
                <button class="<?php echo ($_SESSION['ARCHIVE-MODE'] == 1)  ? "archive" : '';?>" id="ArchiveButton">
                  <img src="css/images/menuitems/archive.png" alt="<?php print(__("Archive")); ?>"/><br/><span class="buttontext"><?php print(__("Archive")); ?></span>
                </button>
              <?php }
              ?>
              <button id="LogoutButton"><img src="css/images/menuitems/exit.png" alt="Logout" /><br/><span class="buttontext"><?php print(__("Logout")); ?></span></button>

            <?php
            if ($_SESSION['USER']->checkUserrights('view_admin')) {
              ?>
                <button id="AdminButton"><img src="css/images/menuitems/admin.png" alt="<?php print(__("Settings")); ?>" /><br/><span class="buttontext"><?php print(__("Settings")); ?></span></button>
              <?php
            }
            ?>
            </div>
          </div>

          <div id="loading"><img src="css/images/ajax-loader.gif" alt="<?php print(__("Loading data")); ?>" title="<?php print(__("Loading data")); ?>" /></div>

        </div>
        <!-- *********************   End of header   ***************************************************** -->

       <!-- *********************   Search div   ***************************************************** -->
        <div id="SearchContainer" class="ui-widget-header <?php echo ($_SESSION['ARCHIVE-MODE'] == 1)  ? "archive" : '';?>">
          <div id="SearchInputDiv">
            <input id="SearchInput" name="SearchInput" type="text" onclick="this.select(); $('#SearchInput').autocomplete('search');" maxlength="20" value="<?php echo __("Search members or addresses"); ?>" />
            <button id="SearchSubmit"><?php echo __("Search"); ?></button>
            <button id="AdvancedSearch"><?php echo __("Advanced search"); ?></button>
          </div>
        </div>
        <!-- *********************   End of Search div   ***************************************************** -->

        <!-- *********************   Content divs   ******************************* -->

        <div id="content">
          <div id="hasjs"><p class="nojs ui-state-error"><?php echo __("Javascript is disabled on your computer. You need Javascript to run this application");?></p></div>
          <div style="clear:both"></div>
          <div id="ContentDiv" class="block"></div>
          <div style="clear:both"></div>
        </div>
        <!-- *********************   End of content   ***************************************************** -->

        <!-- ***************************************** Status dialog *************************************** -->
        <div id="StatusDialog" title="<?php print(__("Are you sure?")); ?>" style="display:none"></div>
        <!-- ***************************************** End Status dialog *************************************** -->

        <!-- ***************************************** Dialog div *************************************** -->
        <div id="DialogDiv" style="display: none;">
          <img src="css/images/ajax-loader-large.gif" alt="<?php print(__("Loading data")); ?>" title="<?php print(__("Loading data")); ?>" />
        </div>
        <!-- ***************************************** End dialog div *************************************** -->

        <!-- ***************************************** Download dialog *************************************** -->
        <div id="DownloadDialog" title="<?php print(__("Export")); ?>" style="display:none; text-align: center;">
          <span id="DownloadText">
            <img src="css/images/ajax-loader-large.gif" alt="<?php print(__("Loading data")); ?>" title="<?php print(__("Loading data")); ?>" />
            <br/><br/><?php print(__("Selected file is prepared for download. Wait a second...")); ?>
          </span>
          <br/><br/>
          <span id="DownloadFile" style="display: none;">&nbsp;
            <input name="DownloadLink" type="text" id="DownloadLink" readonly="readonly" style="width:500px;" />
          </span>
        </div>
        <!-- ***************************************** End Download dialog *************************************** -->

      </div>

      <!-- *********************   Footer with about info  *************************** -->
      <div id="footer">
        <a title="<?php print(__("ChurchMembers")); ?>" href="http://www.churchmembers.org"> <?php print(__("ChurchMembers")); ?></a> <?php ($_SESSION['USER']->checkUserrights('view_admin')) ? print($processRequest->getVersionInfo())  : print($_SESSION['SYSTEMSETTINGS']->system_version) ; ?><br/>
      </div>
      <!-- *********************   End of Footer   ****************************************************** -->
  </body>
</html>