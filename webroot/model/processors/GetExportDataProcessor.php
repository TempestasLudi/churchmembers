<?php

/**
 * This processor handles a request for getting an address.
 */
class GetExportDataProcessor extends AbstractProcessor {

  private $document_type = '';
  private $title = '';
  private $footer = '';
  private $page_size = 'A4';
  private $page_orientation = 'portrait';
  private $filename = '';
  private $owner;

  public function __construct($request = 'template') {
    $this->request = $request;
  }

  /**
   * PARSE EXPORT TEMPLATE
   */
  public function processRequest() {

    if ($this->request === 'template') {
      $EXPORTtemplate = new TemplateParser("EXPORT", '', $this->database);
      print_r($EXPORTtemplate->parseOutput());
    } elseif ($this->request === 'exportfile') {

      if ($_SESSION['USER']->getUserlinktomemberId()) {
        $this->owner = $this->database->generateFullMemberName($_SESSION['USER']->getUserlinktomember(), false, true);
      }

      $this->createDownload();
    }
  }

  private function createDownload() {
    $this->requestfile = $_REQUEST['requestfile'];
    $document_type = $_REQUEST['doctype'];

    $this->setDocType($document_type);

    switch ($this->requestfile) {
      case 'yearbook':
        $this->content = stripslashes($this->YearbookContents());
        $this->title = __("Address list");
        $this->footer = __("Members &amp; addresses");
        break;
      case 'addresses':
        $this->content = stripslashes($this->AddressGroupsContents());
        $this->title = __("Addresses");
        $this->footer = __("Address list");
        $this->page_size = '29.7cm 21cm';
        break;
      case 'membergroupscomplete':
        $this->content = stripslashes($this->MemberGroupsContents('complete'));
        $this->title = __("Groups");
        $this->footer = __("Groups &amp; members");
        $this->page_size = '29.7cm 21cm';
        break;
      case 'membergroupssimple':
        $this->content = stripslashes($this->MemberGroupsContents('simple'));
        $this->title = __("Groups");
        $this->footer = __("Groups &amp; members");
        break;
      case 'allbirthdays':
        $this->content = stripslashes($this->AllBirthdaysContents());
        $this->title = __("Birthdates");
        break;
      case 'specialbirthdays': //childs and above > 65
        $this->content = stripslashes($this->AllBirthdaysContents(true, array(12, 65)));
        $this->title = __("Birthdates children (<=12 year) &amp; elderly persons (>=65 years)");
        break;
      case 'mariagedates':
        $this->content = stripslashes($this->AllMariagedatesContents());
        $this->title = __("All mariagedates");
        break;
      case 'photobook':
        $this->setDocType('pdf');
        $this->content = stripslashes($this->PhotobookContents());
        $this->title = __("Photobook");
        break;
      case 'reportdata':
        $reportdata = $this->excecuteProcessor('report', 'export');
        $this->content = $reportdata;
        $this->title = __("Report");
        break;
      case 'lastchanges':
        $this->content = stripslashes($this->lastChangesContents());
        $this->title = __("All changes in administration of last year");
        break;
      case 'backupdb':
        if ($_SESSION['USER']->checkUserrights('view_admin')) {
          $this->setDocType('sql');
          $this->content = $this->database->createBackup();
        } else {
          die();
        }
        break;
    }

    $this->finalizeOutput();
    //print($this->filecontents);
    //die();
    $getDownload = new GetDownloadProcessor;
    $this->result = $getDownload->createDownload($this->filecontents, $this->document_type);
    print_r(json_encode($this->result));
  }

  /**
   * Set requested document extension
   *
   * @param string     $document_type    Document extension
   */
  private function setDocType($document_type) {
    $this->document_type = $document_type === 'pdf' || $document_type === 'xls' || $document_type === 'doc' || $document_type === 'sql' ? $document_type : 'pdf';
  }

  /**
   * Finish output for corresponding document type adding header & footer,
   * and setting correct charset.
   */
  private function finalizeOutput() {

    if ($this->document_type === 'doc' || $this->document_type === 'xls') {
      $this->content = $this->setOfficeContainer();
      $this->content = ($this->requestfile !== 'photobook') ? $this->xmlEntities($this->content) : $this->content;

      // WORD & EXCEL doesnt support UTF-8, so convert the output to UTF-16LE
      $this->content = chr(255) . chr(254) . mb_convert_encoding($this->content, 'UTF-16LE', 'UTF-8');
    } elseif ($this->document_type === 'pdf') {
      $this->content = $this->setPDFContainer();
      $this->content = ($this->requestfile !== 'photobook') ? $this->xmlEntities($this->content) : $this->content;
    }

    if ($this->document_type === 'pdf') {
      $this->filecontents = $this->exportToPDF();
    } else {
      $this->filecontents = $this->content;
    }
  }

  /**
   * Export file to pdf using DocRaptor API
   */
  private function exportToPDF() {
    require_once INCLUDES_PATH . 'docraptor/DocRaptor.class.php';
    $docraptor = new DocRaptor($this->database->getSetting('export_docraptor_key')->SETTINGS_value);
    $docraptor->setDocumentContent($this->content);
    $docraptor->setDocumentType($this->document_type);
    $docraptor->setName($this->title);
    $docraptor->setTest(true);
    $result = $docraptor->fetchDocument(false);
    return $result;
  }

  public function xmlEntities($str) {
    $xml = array('&#34;', '&#38;', ' &#38; ', '&#38; ', '&#60;', '&#62;', '&#160;', '&#161;', '&#162;', '&#163;', '&#164;', '&#165;', '&#166;', '&#167;', '&#168;', '&#169;', '&#170;', '&#171;', '&#172;', '&#173;', '&#174;', '&#175;', '&#176;', '&#177;', '&#178;', '&#179;', '&#180;', '&#181;', '&#182;', '&#183;', '&#184;', '&#185;', '&#186;', '&#187;', '&#188;', '&#189;', '&#190;', '&#191;', '&#192;', '&#193;', '&#194;', '&#195;', '&#196;', '&#197;', '&#198;', '&#199;', '&#200;', '&#201;', '&#202;', '&#203;', '&#204;', '&#205;', '&#206;', '&#207;', '&#208;', '&#209;', '&#210;', '&#211;', '&#212;', '&#213;', '&#214;', '&#215;', '&#216;', '&#217;', '&#218;', '&#219;', '&#220;', '&#221;', '&#222;', '&#223;', '&#224;', '&#225;', '&#226;', '&#227;', '&#228;', '&#229;', '&#230;', '&#231;', '&#232;', '&#233;', '&#234;', '&#235;', '&#236;', '&#237;', '&#238;', '&#239;', '&#240;', '&#241;', '&#242;', '&#243;', '&#244;', '&#245;', '&#246;', '&#247;', '&#248;', '&#249;', '&#250;', '&#251;', '&#252;', '&#253;', '&#254;', '&#255;');
    $html = array('&quot;', '&amp;', ' & ', '& ', '&lt;', '&gt;', '&nbsp;', '&iexcl;', '&cent;', '&pound;', '&curren;', '&yen;', '&brvbar;', '&sect;', '&uml;', '&copy;', '&ordf;', '&laquo;', '&not;', '&shy;', '&reg;', '&macr;', '&deg;', '&plusmn;', '&sup2;', '&sup3;', '&acute;', '&micro;', '&para;', '&middot;', '&cedil;', '&sup1;', '&ordm;', '&raquo;', '&frac14;', '&frac12;', '&frac34;', '&iquest;', '&Agrave;', '&Aacute;', '&Acirc;', '&Atilde;', '&Auml;', '&Aring;', '&AElig;', '&Ccedil;', '&Egrave;', '&Eacute;', '&Ecirc;', '&Euml;', '&Igrave;', '&Iacute;', '&Icirc;', '&Iuml;', '&ETH;', '&Ntilde;', '&Ograve;', '&Oacute;', '&Ocirc;', '&Otilde;', '&Ouml;', '&times;', '&Oslash;', '&Ugrave;', '&Uacute;', '&Ucirc;', '&Uuml;', '&Yacute;', '&THORN;', '&szlig;', '&agrave;', '&aacute;', '&acirc;', '&atilde;', '&auml;', '&aring;', '&aelig;', '&ccedil;', '&egrave;', '&eacute;', '&ecirc;', '&euml;', '&igrave;', '&iacute;', '&icirc;', '&iuml;', '&eth;', '&ntilde;', '&ograve;', '&oacute;', '&ocirc;', '&otilde;', '&ouml;', '&divide;', '&oslash;', '&ugrave;', '&uacute;', '&ucirc;', '&uuml;', '&yacute;', '&thorn;', '&yuml;');
    $str = str_ireplace($html, $xml, $str);
    return $str;
  }

  public function base64_encode_image($imgbinary = string, $filetype = string) {
    if ($imgbinary) {
      return 'data:image/' . $filetype . ';base64,' . base64_encode($imgbinary);
    }
  }

  private function PhotobookContents() {
    $addresses = $this->database->getMembersIntroduction();
    $contents = "<table width='100%' cellpadding='0' cellspacing='0' border='0'>\n";

    require_once INCLUDES_PATH . 'phpThumb/phpthumb.class.php';
    $phpThumb = new phpThumb();

    foreach ($addresses as $ADR_id => $address) {
      $adr_fullname = ($address->ADDRESS->ADR_familyname_preposition === "") ? $address->ADDRESS->ADR_familyname : $address->ADDRESS->ADR_familyname_preposition . " " . $address->ADDRESS->ADR_familyname;
      $contents .= "<tbody class='address' style='page-break-inside:avoid;'><tr><td colspan='2'><h3>" . $this->xmlEntities($adr_fullname) . "</h3></td></tr>\n";

      foreach ($address->MEMBERS as $MEMBER_id => $member) {

        $imagelocation = BASE_PATH . $member->MEMBER_photo;
        if (file_exists($imagelocation) && ($member->MEMBER_photo !== '')) {

          $phpThumb->resetObject();
          $phpThumb->setSourceData(file_get_contents($imagelocation));
          $width = 128;
          $height = 128;
          $phpThumb->setParameter('w', $width);
          $phpThumb->setParameter('h', $height);
          $phpThumb->setParameter('q', 100);
          $phpThumb->setParameter('zc', 1);
          $phpThumb->setParameter('config_output_format', 'jpg');

          // generate & output thumbnail
          if ($phpThumb->GenerateThumbnail() & $phpThumb->RenderOutput()) { // this line is VERY important, do not remove it!
            $contents .= '<tr><td width="' . ($width + 10) . '"><img width="' . $width . '" height="' . $height . '"  src="';
            $contents .= $this->base64_encode_image($phpThumb->outputImageData, $phpThumb->config_output_format);
            $contents .= '" /></td>';
            $contents .= '<td >' . $this->database->generateFullMemberName($member, false, true) . "</td></tr>\n";
          }
        }
      }

      $contents .= "<tr><td colspan='2'>&nbsp;</td></tr></tbody>";
    }

    $contents .= '</table>';
    return $contents;
  }

  private function AllMariagedatesContents() {
    $contents = '<table width="100%" cellpadding="0" cellspacing="0" border="0">';

    // Get Mariagedates of all months.
    $members = $this->database->getMariageEvents(0, 31449600);
    $curMonth = 0;

    foreach ($members as $member) {

      if ($member->eventMonth !== $curMonth) {
        $curMonth = $member->eventMonth;
        $contents .= '<tr><td colspan="4">&nbsp;</td></tr><tr><td colspan="4"><h1>' . strftime('%B', mktime(0, 0, 0, $member->eventMonth)) . '</h1></td></tr>';
      }
      $members_ids = explode(',', $member->MEMBERS_ids);

      if (count($members_ids) == 2) {
        $contents .= '<tr >';
        $contents .= '<td ><strong>' . date('j', strtotime($member->MEMBER_mariagedate)) . '</strong></td>';
        $contents .= '<td >';
        foreach ($members_ids as $id) {
          $contents .= $this->database->generateFullMemberName($this->database->getMemberById($id), false, true) . '<br/>';
        }
        $contents .= '</td><td>' . $member->MEMBER_mariagedate . '</td>';
        $contents .= '<td>' . $member->MEMBER_mariageage . '</td>';
        $contents .= '</tr>';
      }
    }

    $contents .= '</table>';
    return $contents;
  }

  private function AllBirthdaysContents($special = false, $selection = array()) {
    $contents = '<table width="100%" cellpadding="0" cellspacing="0" border="0">';

    // Get Birthdays of all months.
    $members = $this->database->getBirthdayEvents(0, 31449600);
    $curMonth = 0;

    foreach ($members as $member) {

      if ($member->eventMonth !== $curMonth) {
        $curMonth = $member->eventMonth;
        $contents .= '<tr><td colspan="4">&nbsp; </td></tr><tr><td colspan="4"><h1>' . strftime('%B', mktime(0, 0, 0, $member->eventMonth)) . '</h1></td></tr>';
      }

      if ($special == true) {
        if (($member->MEMBER_age > $selection[0]) & ($member->MEMBER_age < $selection[1])) {
          continue;
        }
      }

      $contents .= '<tr >';
      $contents .= '<td ><strong>' . date('j', strtotime($member->MEMBER_birthdate)) . '</strong></td>';
      $contents .= '<td >' . $this->database->generateFullMemberName($member, false, true) . '</td>';
      $contents .= '<td >' . htmlspecialchars($this->database->getBirthdday($member->MEMBER_birthdate, $member->MEMBER_birthdateview), ENT_QUOTES, 'UTF-8') . '</td>';
      $contents .= '<td >' . $member->MEMBER_age . '</td>';
      $contents .= '</tr>';
    }

    $contents .= '</table>';
    return $contents;
  }

  private function AddressGroupsContents() {
    $orderGroups = new stdClass;
    $groups = $this->database->getAddressInGroup('%');
    $groupdataprocessor = new GetGroupsDataProcessor;
    $groupdataprocessor->nodes = 'addresses';
    $groupshierarchy = $groupdataprocessor->generateGroupHierarchy($groups, new stdClass, 1);
    $contents = '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
    $contents .= $this->AddressGroupsCreateList($groupshierarchy, '', 0);
    $contents .= '</table>';
    return $contents;
  }

  private function AddressGroupsCreateList($object, $input, $level = 0) {
    $colspan = 10;

    foreach ($object as $GROUP) {

      if ((is_object($GROUP)) && ($GROUP->GROUP_type === 'addresses')) {
        $input .= '<tr><td colspan="' . $colspan . '"><strong>' . $GROUP->GROUP_name . '</strong></td></tr>';

        $addressesingroup = $this->database->getAddressesForGroup($GROUP->GROUP_id);

        foreach ($addressesingroup as $address) {
          ob_start();
          ?>
          <tr>
            <td width="42" align="right" >
              <?php
              $gender = ($address->MEMBER_gender == 'male') ? __("Mr") : __("Ms");
              echo ($address->COUNT > 1) ? __("Fam.") : $gender;
              ?>
            </td>
            <td width="5"></td>
            <td><?php
          echo htmlspecialchars($this->database->generateFullMemberName($address, false, true, '', false, true), ENT_QUOTES, 'UTF-8');
              ?></td>
            <td  width="200"><?php
          // Extra address information
          if ($address->ADR_street_extra !== '') {
            $rows = explode(',', $address->ADR_street_extra);
            foreach ($rows as $row) {
              echo htmlspecialchars($row, ENT_QUOTES, 'UTF-8') . '<br />';
            }
          }

          echo htmlspecialchars($address->ADR_street, ENT_QUOTES, 'UTF-8');
              ?></td>
            <td ><?php echo htmlspecialchars($address->ADR_number, ENT_QUOTES, 'UTF-8'); ?></td>
            <td  width="70"><?php echo htmlspecialchars($address->ADR_zip, ENT_QUOTES, 'UTF-8') ?></td>
            <td  width="7">&nbsp;</td>
            <td ><?php echo ucwords(strtolower(htmlspecialchars($address->ADR_city, ENT_QUOTES, 'UTF-8'))); ?></td>
            <td ><?php echo htmlspecialchars($address->ADR_country, ENT_QUOTES, 'UTF-8'); ?></td>
            <td ><?php echo htmlspecialchars($address->ADR_telephone, ENT_QUOTES, 'UTF-8'); ?></td>
          </tr>
          <?php
          $input .= ob_get_clean();
        }

        $input .= '<tr><td colspan="' . $colspan . '">&nbsp; </td></tr>';
      }
      if (count((array) $GROUP->children) > 0) {
        $level++;
        $input = $this->AddressGroupsCreateList($GROUP->children, $input, $level);
        $level--;
      }
    }

    return $input;
  }

  private function MemberGroupsContents($version = 'simple') {
    $orderGroups = new stdClass;
    $groups = $this->database->getMemberInGroup('%');
    $groupdataprocessor = new GetGroupsDataProcessor;
    $groupdataprocessor->nodes = 'members';
    $groupshierarchy = $groupdataprocessor->generateGroupHierarchy($groups, new stdClass, 1);
    $contents = '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
    $contents .= $this->MemberGroupsCreateList($groupshierarchy, '', 0, $version);
    $contents .= '</table>';
    return $contents;
  }

  private function MemberGroupsCreateList($object, $input, $level = 0, $version) {
    $colspan = ($version == 'complete') ? 17 : 6;

    foreach ($object as $GROUP) {

      if ((is_object($GROUP)) && ($GROUP->GROUP_type === 'members')) {
        $input .= '<tr><td colspan="' . $colspan . '"><strong>' . $GROUP->GROUP_name . '</strong></td></tr>';

        $membersingroup = $this->database->getMembersForGroup($GROUP->GROUP_id);

        foreach ($membersingroup as $member) {
          ob_start();
          ?>
          <tr >
            <td width="250"><?php echo $this->database->generateFullMemberName($member, false, true, '', false, false); ?></td>
            <td width="2">&nbsp; </td>

            <?php if ($version === 'complete') { ?>
              <td align="center"><?php echo strtolower(substr((($member->MEMBER_gender === 'male') ? __("Male") : __("Female")), 0, 1)); ?></td>
              <td width="5">&nbsp; </td>
              <td align="center"><?php echo htmlspecialchars($member->MEMBERTYPE_abbreviation, ENT_QUOTES, 'UTF-8') ?></td>
              <td width="5">&nbsp; </td>
              <td width="80" align="right"><?php echo htmlspecialchars($this->database->getBirthdday($member->MEMBER_birthdate, $member->MEMBER_birthdateview), ENT_QUOTES, 'UTF-8') ?></td>
              <td width="5">&nbsp; </td>
            <?php } ?>

            <td><?php echo htmlspecialchars($member->ADR_street, ENT_QUOTES, 'UTF-8') ?>&nbsp;<?php echo htmlspecialchars($member->ADR_number, ENT_QUOTES, 'UTF-8') ?></td>
            <td width="5"></td>

            <?php if ($version === 'complete') { ?>
              <td width="70"><?php echo htmlspecialchars($member->ADR_zip, ENT_QUOTES, 'UTF-8') ?></td>
              <td width="5">&nbsp; </td>
              <td><?php echo htmlspecialchars($member->ADR_city, ENT_QUOTES, 'UTF-8') ?></td>
              <td width="5">&nbsp; </td>
              <?php
            }

            if ($version === 'complete') {
              echo '<td width="95" align="right">';
              $phone = (($member->MEMBER_business_phone) and (!$member->MEMBER_mobilephone)) ? $member->MEMBER_business_phone : $member->MEMBER_mobilephone;
              echo preg_replace("'\s+'", '', htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'));
              echo '<td>';
            } else {
              echo '<td width="120">' . htmlspecialchars($member->MEMBER_mobilephone, ENT_QUOTES, 'UTF-8') . '</td>';
              echo '<td width="95">' . htmlspecialchars($member->ADR_telephone, ENT_QUOTES, 'UTF-8') . '</td>';
            }

            if ($version === 'complete') {
              ?>
              <td width="5">&nbsp; </td>
              <td align="right"><?php echo htmlspecialchars($member->MEMBER_email, ENT_QUOTES, 'UTF-8') ?></td>
            <?php } ?>

          </tr>
          <?php
          $input .= ob_get_clean();
        }

        $input .= '<tr><td colspan="' . $colspan . '">&nbsp; </td></tr>';
      }
      if (count((array) $GROUP->children) > 0) {
        $level++;
        $input = $this->MemberGroupsCreateList($GROUP->children, $input, $level, $version);
        $level--;
      }
    }

    return $input;
  }

  private function lastChangesContents() {
    ob_start();

    $memberTypes = $this->database->getMembertypeStats();
    $input = "";
    if (count($memberTypes)) {
      echo '<h2>' . __('Distribution members') . '</h2><table width="100%" cellpadding="0" cellspacing="0" border="0">';
      foreach ($memberTypes as &$MEMBERTYPE) {
        echo '<tr><td >' . $MEMBERTYPE->MEMBERTYPE_name . '</td><td >' . $MEMBERTYPE->TOTAL_MEMBERTYPES . '</td></tr>';
      }

      $totals = $this->database->getMemberStats();
      echo '<tr><th ></th><td >&nbsp;</td></tr>';
      echo '<tr><th >' . __('Total addresses') . '</th><td >' . $totals->TOTAL_ADDRESSES . '</td></tr>';
      echo '<tr><th >' . __('Total members') . '</th><td >' . $totals->TOTAL_MEMBERS . '</td></tr>';
      echo '</table><br/>';
    }

    $getEventStats = $this->database->getEventStats();
    $input = "";
    if (count($getEventStats)) {
      echo '<h2>' . __('Statistics since') . ' ' . date('d-m-Y', strtotime('-1 year')) . '</h2><table width="100%" cellpadding="0" cellspacing="0" border="0">';
      foreach ($getEventStats as &$EVENT) {
        echo '<tr><td >' . $this->database->getEventTypeTranslation($EVENT->EVENTTYPE_name) . '</td><td >' . $EVENT->TOTAL_EVENTS . '</td></tr>';
      }
      echo '</table><br/>';
    }

    $orderedevents = $this->database->getLastChanges();
    $not_displayed_events = array('EVENT_CONTINUE_GUESTMEMBERSHIP', 'EVENT_MOVED', 'EVENT_CHANGED_HOME_PHONE', 'EVENT_CHANGED_HOME_EMAIL', 'EVENT_CHANGED_BUSINESS_PHONE', 'EVENT_CHANGED_BUSINESS_EMAIL', 'EVENT_CHANGED_PHONE', 'EVENT_CONTINUE_TRAVEL_TESTIMONY', 'EVENT_CHANGED_EMAIL', 'EVENT_SICK', 'EVENT_CONTINUE_STAY_TESTIMONY');
    $old_name = '';
    ?>
    <h2><?php echo __('All changes in administration since') . ' ' . date('d-m-Y', strtotime('-1 year')); ?></h2>
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
    <?php
    foreach ($orderedevents as $eventtype => $events) {
      if (!in_array($eventtype, $not_displayed_events)) {
        echo '<tr><td colspan="5"><strong>' . $this->database->getEventTypeTranslation($eventtype) . '</strong></td></tr>';

        foreach ($events as $members) {
          if (is_array($members)) {
            $names = '';
            foreach ($members as $_member) {
              $member = $this->database->getMemberById($_member->EVENT_MEMBER_id);
              if (count($member) > 0) {
                $name = $this->database->generateFullMemberName($member, false, true);
              } else {
                $name = $_member->EVENT_MEMBER_fullname;
              }

              if ($name === '')
                $name = '?';
              $names .= $name . '<br/>';
            }
            ?>
              <tr>
                <td ><?php echo $_member->EVENT_date; ?></td>
                <td width="10"></td>
                <td ><?php echo $names; ?></td>
                <td  width="7">&nbsp;</td>
                <td></td>
              </tr>
            <?php
          }
        }
        echo '<tr><td colspan="5">&nbsp;</td></tr>';
      }
    }
    ?>
    </table>
      <?php
      return ob_get_clean();
    }

    private function YearbookContents() {
      $addresses = $this->database->getAddressesWithMembers();
      ob_start();
      ?>
    <table width="100%" cellpadding="0" cellspacing="0" border="0" >
    <?php foreach ($addresses as $ADR_id => $address) { ?>
        <?php foreach ($address->MEMBERS as $MEMBER_id => $member) { ?>
          <tr>
            <td><?php
          echo ($member->MEMBER_parent) ? "<strong> " : "";
          $membername = $this->database->generateFullMemberName($member);
          echo (strlen(str_replace('&nbsp;', '', $membername)) >= 37) ? str_replace(".", '', $membername) : $membername;
          echo ($member->MEMBER_parent) ? "</strong> " : "";
          ?></td>
            <td align="right"><?php
        if ($member->MEMBER_GROUPS) {
          echo htmlspecialchars($member->MEMBER_GROUPS, ENT_QUOTES, 'UTF-8');
        }
          ?></td>
            <td align="right" width="3">&nbsp;</td>
            <td align="center"><?php echo strtolower(substr((($member->MEMBER_gender === 'male') ? __("Male") : __("Female")), 0, 1)); ?></td>
            <td align="right" width="3">&nbsp;</td>
            <td><?php echo htmlspecialchars($member->MEMBERTYPE_abbreviation, ENT_QUOTES, 'UTF-8') ?></td>
            <td align="right"><?php echo htmlspecialchars($this->database->getBirthdday($member->MEMBER_birthdate, $member->MEMBER_birthdateview), ENT_QUOTES, 'UTF-8') ?></td>
            <td align="right" width="7">&nbsp;</td>
            <td align="right"><?php
        $phone = (($member->MEMBER_business_phone) and (!$member->MEMBER_mobilephone)) ? $member->MEMBER_business_phone : $member->MEMBER_mobilephone;
        echo preg_replace("'\s+'", '', htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'));
          ?></td>
            <td align="right"><?php echo htmlspecialchars($member->MEMBER_email, ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
        <?php
      } // End each Member
      ?>
        <tr>
          <td>
      <?php
      // Extra address information
      if ($address->ADDRESS->ADR_street_extra !== '') {
        $rows = explode(',', $address->ADDRESS->ADR_street_extra);
        foreach ($rows as $row) {
          echo htmlspecialchars($row, ENT_QUOTES, 'UTF-8') . '<br />';
        }
      }

      // Street
      echo htmlspecialchars($address->ADDRESS->ADR_street, ENT_QUOTES, 'UTF-8') . '&nbsp;';
      // Number
      echo htmlspecialchars($address->ADDRESS->ADR_number, ENT_QUOTES, 'UTF-8') . '<br />';

      // ZIP
      echo htmlspecialchars($address->ADDRESS->ADR_zip, ENT_QUOTES, 'UTF-8') . '&nbsp;&nbsp;';

      // CITY
      echo ucwords(strtolower(htmlspecialchars($address->ADDRESS->ADR_city, ENT_QUOTES, 'UTF-8')));

      // COUNTRY
      echo ($address->ADDRESS->ADR_country !== '') ? '<br />' . ucwords(strtolower(htmlspecialchars($address->ADDRESS->ADR_country, ENT_QUOTES, 'UTF-8'))) : '';

      //EMAIL
      if ($address->ADDRESS->ADR_email) {
        echo htmlspecialchars($address->ADDRESS->ADR_email, ENT_QUOTES, 'UTF-8');
      }
      ?>
          </td>
          <td align="right" valign="top" colspan="6"><?php
      echo htmlspecialchars($address->ADDRESS->ADDRESS_GROUPS, ENT_QUOTES, 'UTF-8');
      ?></td>
          <td>&nbsp;</td>
          <td align="right" valign="top"><strong><?php
      if ($address->ADDRESS->ADR_telephone !== '') {
        $rows = explode('/', $address->ADDRESS->ADR_telephone);

        foreach ($rows as $row) {
          $fullphonenumber = (strpos($row, '(') !== false) ? $row : '(' . $_SESSION['SYSTEMSETTINGS']->locale_officecode . ') ' . $row;
          echo htmlspecialchars($fullphonenumber, ENT_QUOTES, 'UTF-8') . "<br />";
        }
      }
      ?></strong></td>
          <td align="right">&nbsp;</td>
        </tr>
        <tr><td colspan="10" style="line-height:10px">&nbsp;</td></tr>
      <?php
    }// End each Address
    ?>
    </table>
      <?php
      return ob_get_clean();
    }

    private function setOfficeContainer() {
      ob_start();
      ?>
    <html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns:x="urn:schemas-microsoft-com:office:excel"
          xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns='http://www.w3.org/TR/REC-html40' Content-Type="UTF-16LE">
      <head><xml><w:WordDocument><w:View>Print</w:View><w:Zoom>100</w:Zoom></w:WordDocument></xml>
      <style type="text/css">
        @charset "UTF-16LE";

        div.Section1{
          page:Section1;
        }

        table,td {
          border:none; mso-cellspacing:0
        }

        body{
          text-align:left;
          color:#000000;
          font-size: 11pt;
          font-family: Arial,Helvetica,sans-serif,Verdana;
          line-height:1.0;
          letter-spacing:-0.3;
          mso-line-height-rule:exactly;
        }

        @page {
          size:<?php print($this->page_size); ?>;
          mso-page-orientation: <?php print($this->page_orientation); ?>;
          mso-mirror-margins:yes;
          margin-top: 35pt;
          margin-bottom: 80pt;
          margin-left: 15pt;
          margin-right: 20pt;
        }

        h1 { prince-bookmark-level: 1;	margin-bottom:5px; margin-top:0; font-size:1.5em;font-weight:bold; text-transform:uppercase; line-height:20.0pt; }
        h2 { prince-bookmark-level: 2 }
        h3 { prince-bookmark-level: 3 }

        .address { page-break-inside: avoid; }
        tbody { page-break-inside: avoid; }
      </style>
    </head>
    <body>
      <div class='Section1'>
    <?php
    print($this->content);
    ?>
      </div>
    </body>
    </html>
    <?php
    return ob_get_clean();
  }

  private function setPDFContainer() {
    ob_start();
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
        <title><?php print($this->title); ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="author" content="Herengrachtkerk"/>
        <meta name="date" content="<?php print(date("d-m-Y")); ?>"/>

        <style type="text/css">
          @charset "utf-8";
          /*  ## RESET BROWSER DEFAULT CSS  ---------------------------------------------------*/
          html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, big, cite, code, del, dfn, em, font, img, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, b, u, i, center, dl, dt, dd, ol, ul, li, fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td {
            border: 0;
            font-size: 100%;
            line-height: 1.3;
            margin: 0;
            outline: 0;
            padding: 0;
          }

          body{
            text-align:left;
            color:#000000;
            font-size: 14.5px;
            font-family: Arial,Helvetica,sans-serif,Verdana;
          }

          @page {
            size:<?php print($this->page_size); ?>;
            margin-top: 35pt;
            margin-bottom: 35pt;
            margin-inside: 10pt;
            margin-outside: 25pt;

            @top-center {
            content: "";
          }
          @bottom-left {
            content: counter(page) " <?php print($this->footer); ?>";
            font-style: italic;
            font-variant:small-caps;
            color:#000000;
            font-size: 12px;
            font-weight:bold;
            font-family: Arial,Helvetica,sans-serif,Verdana;
          }
          @bottom-center {
            content: "" ;
          }

          @bottom-right {
            content: "<?php print($this->owner); ?>";
            color:#000000;
            font-size: 9px;
            font-weight:normal;
            font-family: Arial,Helvetica,sans-serif,Verdana;
          }

          }
          h1 { prince-bookmark-level: 1;	margin-bottom:5px; margin-top:0; font-size:1.5em;font-weight:bold; text-transform:uppercase; }
          h2 { prince-bookmark-level: 2 }
          h3 { prince-bookmark-level: 3 }

          .address { page-break-inside: avoid }

        </style>
      </head>
      <body>
        <h1><?php print($this->title); ?></h1>
    <?php
    print($this->content);
    ?>
      </body>
    </html>
    <?php
    return ob_get_clean();
  }

}
?>