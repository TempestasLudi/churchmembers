<?php
/*
  ChurchMembers is a php/ajax webbased crm application targeting churches.
  Through it they can administer members, addresses and groups.
  Copyright (C) 2011  goblin47 & thelionnl

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>
*/

/**
 * Takes care of error handling, prevents default php error handler kicking in.
 */
class ErrorHandler {

  function __construct() {
    set_error_handler(array(&$this, "handleError"));
  }

  /**
   * Handles errors in the application
   * @param int $errno
   * @param string $errstr
   * @param string $errfile
   * @param int $errline
   * @param mixed $errcontext
   * @return bool
   */
  function handleError($errno, $errstr, $errfile, $errline, $errcontext) {
    if (!(error_reporting() & $errno)) {
      // This error code is not included in error_reporting
      return;
    }

    switch ($errno) {
      case E_USER_ERROR:
        $errstr = unserialize($errstr);

        switch ($errstr["errtype"]) {

          case "E_DB_CONNECTIONFAIL":
            $message = '<b>[' . $errstr['errno'] . '] Error by connecting to DB:</b>';
            $detailsmessage = '[' . $errstr['errno'] . '] Error by connecting to DB on line ' . $errline . ' in file ' . basename($errfile) . '<br/> ' . $errstr['error'];
            break 2;

          case "E_USER_FAILWRITEFAIL":
            $message = '<b>File is not writable</b>';
            $detailsmessage = 'File is not writable ' . $errline . ' in file ' . basename($errfile);
            echo $this->CreateStatusDialog($message, $detailsmessage, $errcontext);
            break 2;

          case "E_USER_FAILPREPAREQUERY":
            $message = '<b>[' . $errstr['errno'] . '] Error by preparing SQL query:</b>';
            $detailsmessage = '[' . $errstr['errno'] . '] Error by preparing SQL query on line ' . $errline . ' in file ' . basename($errfile) . '<br/> ' . $errstr['error'];
            echo $this->CreateStatusDialog($message, $detailsmessage, $errcontext);
            break 2;

          case "E_USER_FAILEXECUTEQUERY":
            $message = '<b>[' . $errstr['errno'] . '] Error by excecuting SQL query</b>';
            $detailsmessage = '[' . $errstr['errno'] . '] Error by excecuting SQL query on line ' . $errline . ' in file ' . basename($errfile) . '<br/> ' . $errstr['error'];
            echo $this->CreateStatusDialog($message, $detailsmessage, $errcontext);
            break 2;

          case "E_USER_FAILEDITDATA":
            $message = $errstr["field"] . __("is <b>not</b> successful changed<br/><br/>");
            $detailsmessage = '[' . $errno . '] Error by editing data on line ' . $errline . ' in file ' . basename($errfile);
            echo $this->CreateStatusDialog($message, $detailsmessage, $errcontext);
            break 2;

          case "E_USER_FAILMOVE_MEMBER":
            $message = __("Member is <b>not</b> successful moved");
            $detailsmessage = '[' . $errno . '] Error by moving member on line ' . $errline . ' in file ' . basename($errfile);
            echo $this->CreateStatusDialog($message, $detailsmessage, $errcontext);
            break 2;

          case "E_USER_FAILEDITDATA_ADDRESS":
            $message = __("Address is <b>not</b> successful moved");
            $detailsmessage = '[' . $errno . '] Error by editing/moving address on line ' . $errline . ' in file ' . basename($errfile);
            echo $this->CreateStatusDialog($message, $detailsmessage, $errcontext);
            break 2;

          case "E_USER_FAILARCHIVE_MEMBER":
            $message = __("Member is <b>not</b> successful archived");
            $detailsmessage = '[' . $errno . '] Error by archiving member on line ' . $errline . ' in file ' . basename($errfile);
            echo $this->CreateStatusDialog($message, $detailsmessage, $errcontext);
            break 2;

          case "E_USER_FAILARCHIVE_ADDRESS":
            $message = __("Address is <b>not</b> successful archived");
            $detailsmessage = '[' . $errno . '] Error by archiving address on line ' . $errline . ' in file ' . basename($errfile);
            echo $this->CreateStatusDialog($message, $detailsmessage, $errcontext);
            break 2;

          case "E_USER_FAILDELETE_MEMBER":
            $message = __("Member is <b>not</b> successful deleted");
            $detailsmessage = '[' . $errno . '] Error by deleting member on line ' . $errline . ' in file ' . basename($errfile);
            echo $this->CreateStatusDialog($message, $detailsmessage, $errcontext);
            break 2;

          case "E_USER_FAIL_DELETE_ADDRESS":
            $message = __("Address is <b>not</b> successful deleted");
            $detailsmessage = '[' . $errno . '] Error by deleting address on line ' . $errline . ' in file ' . basename($errfile);
            echo $this->CreateStatusDialog($message, $detailsmessage, $errcontext);
            break 2;

          case "E_USER_FAIL_ADD_EVENT":
            $message = __("Event is <b>not</b> successful added. Try again.");
            $detailsmessage = '[' . $errno . '] Error by adding a event on line ' . $errline . ' in file ' . basename($errfile);
            echo $this->CreateStatusDialog($message, $detailsmessage, $errcontext);
            break 2;

          case "E_USER_FAIL_SORT_MEMBERS":
            $message = __("Members are <b>not</b> successful sorted ");
            $detailsmessage = '[' . $errno . '] Error by sorting members on line ' . $errline . ' in file ' . basename($errfile);
            echo $this->CreateStatusDialog($message, $detailsmessage, $errcontext);
            break 2;

          case "E_USER_FAIL_ADD_ADDRESS":
            $message = __("Address is <b>not</b> successful added");
            $detailsmessage = '<script type="text/javascript">$("#newAddress").dialog("close");</script>';
            $detailsmessage .= '[' . $errno . '] Error by adding a address on line ' . $errline . ' in file ' . basename($errfile);
            echo $this->CreateStatusDialog($message, $detailsmessage, $errcontext);
            break 2;

          case "E_USER_FAIL_ADD_MEMBER":
            $message = __("Member is <b>not</b> successful added");
            $detailsmessage = '<script type="text/javascript">$("#newMember").dialog("close");</script>';
            $detailsmessage .= '[' . $errno . '] Error by adding a member on line ' . $errline . ' in file ' . basename($errfile);
            echo $this->CreateStatusDialog($message, $detailsmessage, $errcontext);
            break 2;

          case "E_USER_FAIL_ADD_GROUP":
            $message = __("Group is <b>not</b>successful added.");
            $detailsmessage = '<script type="text/javascript">$("#newGroup").dialog("close");</script>';
            $detailsmessage .= '[' . $errno . '] Error by adding a group on line ' . $errline . ' in file ' . basename($errfile);
            echo $this->CreateStatusDialog($message, $detailsmessage, $errcontext);
            break 2;

          case "E_USER_FAIL_MEMBER_DELETE_GROUPS":
            $message = __("Member is <b>not</b> successful removed from the groups");
            $detailsmessage .= '[' . $errno . '] Error by deleting a group on line ' . $errline . ' in file ' . basename($errfile);
            echo $this->CreateStatusDialog($message, $detailsmessage, $errcontext);
            break 2;

          case "E_USER_FAIL_MEMBER_ADD_GROUPS":
            $message = __("Member is <b>not</b> successful added to the groups");
            $detailsmessage .= '[' . $errno . '] Error by deleting a group on line ' . $errline . ' in file ' . basename($errfile);
            echo $this->CreateStatusDialog($message, $detailsmessage, $errcontext);
            break 2;

          case "E_USER_FAIL_THUMB":
            $messsage = __("Creation of thumbnail has failed");
            echo $this->CreateStatusDialog($message, '', $errcontext);
            die();


          default:
            $message = "Error";
            $detailsmessage = '[' . $errno . '] Error by editing data on line ' . $errline . ' in file ' . basename($errfile);
            echo $this->CreateStatusDialog($message, $detailsmessage, $errcontext);
            break 2;
        }



      case E_USER_WARNING:
        $message = "WARNNG - $errstr";
        $detailsmessage = '[' . $errno . '] Warning on line ' . $errline . ' in file ' . basename($errfile);
        echo $this->CreateStatusDialog($message, $detailsmessage, $errcontext);
        break;

      case E_USER_NOTICE:
        $message = "NOTICE  - $errstr";
        $detailsmessage = '[' . $errno . '] Warning on line ' . $errline . ' in file ' . basename($errfile);
        echo $this->CreateStatusDialog($message, $detailsmessage, $errcontext);
        break;

      default:
        $message = "Unkown Error  - $errstr";
        $detailsmessage = '[' . $errno . '] Warning on line ' . $errline . ' in file ' . basename($errfile);
        echo $this->CreateStatusDialog($message, $detailsmessage, $errcontext);
        break;
    }
    $user = (isset($_SESSION['USER']) && (is_a($_SESSION['USER'], 'User'))) ? $_SESSION['USER']->getUsername() : 'unknown';
    $logmsg = "[" . date("Y/m/d H:i:s", time()) . " | " . $user  ."] ". $message . "\n" . $detailsmessage . "\n" . print_r($errcontext,true) . "\n";
    $this->logToFile($logmsg);

    /* Don't execute PHP internal error handler */
    die();
  }


  /**
   * Create Error/Status Dialog.
   * @param string $message
   * @param string $detailsmessage
   * @param string $errcontext
   * @param int $width
   * @return string
   */
  function CreateStatusDialog($message, $detailsmessage, $errcontext, $width = 500) {

    $dialog = '<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 100px 0;"></span>' . $message . "<br/><br/><em>$detailsmessage</em>";
    $viewrights = (isset($_SESSION['USER']) && (is_a($_SESSION['USER'], 'User'))) ? $_SESSION['USER']->checkUserrights('view_admin') : false;
    if ((isset($errcontext) and isset($detailsmessage)) and (isset($_SESSION['USER'])) and ($viewrights)) {
      $dialog .= '<br/><br/><span class="ui-icon ui-icon-triangle-1-s" style="float:left;margin:0 7px 7px 0;"></span><a onclick=$("#error_details").toggle();><strong>Error details</strong></a><br /><br />';

      // Create visual  dump of error context
      ob_start();
      $this->do_dump($errcontext);
      $errcontext = ob_get_contents();
      ob_end_clean();

      $dialog .= '<div id="error_details" style="display:none">' . $detailsmessage . '<br/><br/>' . $errcontext  . '</div>';
    }

    $dialog = str_replace('"',"'",$dialog);
    $dialog = str_replace(array( "\t", "\o", "\xOB", "\r", "\n"), '', $dialog);
    $dialog = '[ERROR]OpenDialog("StatusDialog", "' . $dialog . '", ' . $width . ')[/ERROR] error';

    return $dialog;
  }

  /**
   * Log to file using php error_log function
   * @param string $logmsg
   */
  function logToFile($logmsg) {
    error_log($logmsg, 3, LOGS_PATH . "churchmembers.log");
  }

  /**
   * Better GI than print_r or var_dump -- but, unlike var_dump, you can only dump one variable.
   * Added htmlentities on the var content before echo, so you see what is really there, and not the mark-up.
   *
   * Also, now the output is encased within a div block that sets the background color, font style, and left-justifies it
   * so it is not at the mercy of ambient styles.
   *
   * Inspired from:     PHP.net Contributions
   * Stolen from:       [highstrike at gmail dot com]
   * Modified by:       stlawson *AT* JoyfulEarthTech *DOT* com
   *
   * @param mixed $var  -- variable to dump
   * @param string $var_name  -- name of variable (optional) -- displayed in printout making it easier to sort out what variable is what in a complex output
   * @param string $indent -- used by internal recursive call (no known external value)
   * @param unknown_type $reference -- used by internal recursive call (no known external value)
   */
  function do_dump(&$var, $var_name = NULL, $indent = NULL, $reference = NULL) {
    $do_dump_indent = "<span style='color:#666666;'>|</span> &nbsp;&nbsp; ";
    $reference = $reference.$var_name;
    $keyvar = 'the_do_dump_recursion_protection_scheme';
    $keyname = 'referenced_object_name';

    // So this is always visible and always left justified and readable
    echo "<div style='text-align:left; background-color:white; font: 100% monospace; color:black;'>";

    if (is_array($var) && isset($var[$keyvar])) {
      $real_var = &$var[$keyvar];
      $real_name = &$var[$keyname];
      $type = ucfirst(gettype($real_var));
      echo "$indent$var_name <span style='color:#666666'>$type</span> = <span style='color:#e87800;'>&amp;$real_name</span><br/>";
    }
    else {
      $var = array($keyvar => $var, $keyname => $reference);
      $avar = &$var[$keyvar];

      $type = ucfirst(gettype($avar));
      if($type == "String") $type_color = "<span style='color:green'>";
      elseif($type == "Integer") $type_color = "<span style='color:red'>";
      elseif($type == "Double") {
        $type_color = "<span style='color:#0099c5'>";
        $type = "Float";
      }
      elseif($type == "Boolean") $type_color = "<span style='color:#92008d'>";
      elseif($type == "NULL") $type_color = "<span style='color:black'>";

      if(is_array($avar)) {
        $count = count($avar);
        echo "$indent" . ($var_name ? "$var_name => ":"") . "<span style='color:#666666'>$type ($count)</span><br/>$indent(<br/>";
        $keys = array_keys($avar);
        foreach($keys as $name) {
          $value = &$avar[$name];
          $this->do_dump($value, "['$name']", $indent.$do_dump_indent, $reference);
        }
        echo "$indent)<br/>";
      }
      elseif(is_object($avar)) {
        echo "$indent$var_name <span style='color:#666666'>$type</span><br/>$indent(<br/>";
        foreach($avar as $name=>$value) $this->do_dump($value, "$name", $indent.$do_dump_indent, $reference);
        echo "$indent)<br/>";
      }
      elseif(is_int($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> $type_color".htmlentities($avar)."</span><br/>";
      elseif(is_string($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> $type_color\"".htmlentities($avar)."\"</span><br/>";
      elseif(is_float($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> $type_color".htmlentities($avar)."</span><br/>";
      elseif(is_bool($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> $type_color".($avar == 1 ? "TRUE":"FALSE")."</span><br/>";
      elseif(is_null($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> {$type_color}NULL</span><br/>";
      else echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> ".htmlentities($avar)."<br/>";
      
      $var = $var[$keyvar];
    }

    echo "</div>";
  }
}
?>