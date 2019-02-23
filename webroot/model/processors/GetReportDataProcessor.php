<?php

/**
 * This processor handles a request for generating the advanced search table
 */
class GetReportDataProcessor extends AbstractProcessor {

  private $availableColumns = array();
  private $requestedColumns = array();
  private $requestedFilters = array();
  private $queryFields = array();
  private $queryWhere = array();
  private $queryJoin = array();
  private $queryHaving = array();
  private $queryOrderBy = array();
  private $queryGroupBy = array();
  private $queryComplete;
  private $requestedData;
  private $tables = array();
  private $jsonData = array();
  private $csvData;
  private $result = array();
  public $requestedColumnsRaw = array();

  public function processRequest() {
    $this->request = isset($_REQUEST['request']) ? $_REQUEST['request'] : 'json';
    $this->showrows = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 25;
    $this->currentpage = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
    $this->totalpages = 1;
    $this->totalresults = 0;

    $this->requestedColumnsRaw = isset($_REQUEST['columns']) ? (explode(",", $_REQUEST['columns'])) : array();
    $this->setTableColumns();
    $this->setFilterOptions();
    $this->setQuery();
    //print($this->queryComplete);
    //exit();
    $this->getData();

    switch ($this->request) {
      case 'json':
        print($this->createJson());
        break;

      case 'xls' || 'csv':
        return $this->createCsv();
        break;
    }
  }

  private function setTableColumns() {
    $this->tables = array('addresses', 'groupmembers', 'groupaddresses', 'groups', 'members', 'membertypes', 'events', 'eventtypes');

    // Get all available columns
    foreach ($this->tables as $table) {
      $this->availableColumns[$table] = $this->database->getColumnNames($table);
      $this->requestedColumns[$table] = array();

      // Only save existing requested columns
      foreach ($this->requestedColumnsRaw as $columnNameRaw) {
        if (in_array($columnNameRaw, $this->availableColumns[$table])) {
          array_push($this->requestedColumns[$table], $columnNameRaw);
        }
      }
    }

    // Or replace them
    $this->requestedColumns['other'] = array();
    foreach ($this->requestedColumnsRaw as $columnNameRaw) {
      switch ($columnNameRaw) {
        case 'MEMBER_fullname':
          array_push($this->requestedColumns['members'], 'MEMBER_familyname', 'MEMBER_familyname_preposition', 'MEMBER_initials', 'MEMBER_firstname', 'MEMBER_christianname', 'MEMBER_gender', 'MEMBER_birthdate', 'MEMBER_membertype_id', 'MEMBER_mobilephone', 'MEMBER_parent', 'MEMBER_inyearbook', 'MEMBER_birthdateview', 'MEMBER_familynameview', 'MEMBER_archive');
          break;
        case 'MEMBER_age':
          array_push($this->requestedColumns['other'], 'DATE_FORMAT( NOW(), "%Y" ) - DATE_FORMAT(MEMBER_birthdate, "%Y") - ( DATE_FORMAT(NOW(), "00-%m-%d" ) < DATE_FORMAT(MEMBER_birthdate, "00-%m-%d" ) ) as MEMBER_age');
          break;
        case 'MEMBER_GROUPS':
          array_push($this->requestedColumns['groups'], array());
          array_push($this->requestedColumns['other'], 'GROUP_CONCAT(DISTINCT  `MEMBER_GROUPS_TB`.`GROUP_id`) as `MEMBER_GROUPS_IDs`', 'GROUP_CONCAT(DISTINCT  `MEMBER_GROUPS_TB`.`GROUP_name`) as `MEMBER_GROUPS`');
          break;
        case 'ADDRESS_GROUPS':
          array_push($this->requestedColumns['groups'], array());
          array_push($this->requestedColumns['other'], 'GROUP_CONCAT(DISTINCT  `ADDRESS_GROUPS_TB`.`GROUP_id`) as `ADDRESS_GROUPS_IDs`', 'GROUP_CONCAT(DISTINCT  `ADDRESS_GROUPS_TB`.`GROUP_name`) as `ADDRESS_GROUPS`');
          break;
        case 'EVENTS':
          array_push($this->requestedColumns['events'], 'EVENT_id');
          array_push($this->requestedColumns['other'], 'GROUP_CONCAT(DISTINCT  `events`.`EVENT_id` ) as `EVENT_id`', 'GROUP_CONCAT(DISTINCT  `eventtypes`.`EVENTTYPE_name` )  as `EVENTS`', 'GROUP_CONCAT(DISTINCT  DATE_FORMAT( `events`.`EVENT_date`, "%d-%m-%Y" ))  as `EVENT_date`', 'GROUP_CONCAT(DISTINCT  `events`.`EVENT_note`)  as `EVENT_note`');
          break;
        case 'ADR_fullfamilyname':
          array_push($this->requestedColumns['addresses'], 'ADR_familyname', 'ADR_familyname_preposition');
          break;
        case 'ADR_address':
          array_push($this->requestedColumns['addresses'], 'ADR_street', 'ADR_number', 'ADR_street_extra', 'ADR_zip', 'ADR_city', 'ADR_country');
          break;
        case 'ADR_addressing':
        case 'ADR_start':
          array_push($this->requestedColumns['members'], 'MEMBER_familyname', 'MEMBER_familyname_preposition', 'MEMBER_initials', 'MEMBER_gender');
          array_push($this->requestedColumns['addresses'], 'ADR_familyname', 'ADR_familyname_preposition');

          if (!empty($_REQUEST['Report_Addresses_Select'])) {
            array_push($this->requestedColumns['other'], 'COUNT(DISTINCT(`members`.`MEMBER_id`)) as `_COUNT`');
          }
          break;
      }
    }

    // Add default columns
    array_push($this->requestedColumns['members'], 'MEMBER_id', 'MEMBER_rank');
    array_push($this->requestedColumns['addresses'], 'ADR_id', 'ADR_familyname', 'ADR_familyname_preposition');
  }

  private function setFilterOptions() {
    $this->requestedFilters = array();

    if (!empty($_REQUEST['Report_Addresses_Select'])) {
      $this->requestedFilters['ADR_only'] = array(
          'filtertype' => 'limiter',
          'selected' => (bool) $_REQUEST['Report_Addresses_Select']);
    }
    if (!empty($_REQUEST['Report_Birthday_Month_From']) && !empty($_REQUEST['Report_Birthday_Month_To'])) {
      $this->requestedFilters['MEMBER_birthdate'] = array(
          'filtertype' => 'date',
          'dateFrom' => $this->database->setDate($_REQUEST['Report_Birthday_Month_From']),
          'dateTo' => $this->database->setDate($_REQUEST['Report_Birthday_Month_To']),
          'toggle' => $_REQUEST['Report_Birthday_Month_Toggle_Value']);
    }
    if (!empty($_REQUEST['Report_Baptismdate_From']) && !empty($_REQUEST['Report_Baptismdate_To'])) {
      $this->requestedFilters['MEMBER_baptismdate'] = array(
          'filtertype' => 'date',
          'dateFrom' => $this->database->setDate($_REQUEST['Report_Baptismdate_From']),
          'dateTo' => $this->database->setDate($_REQUEST['Report_Baptismdate_To']),
          'toggle' => $_REQUEST['Report_Baptismdate_Toggle_Value']);
    }
    if (!empty($_REQUEST['Report_Confessiondate_From']) && !empty($_REQUEST['Report_Confessiondate_To'])) {
      $this->requestedFilters['MEMBER_confessiondate'] = array(
          'filtertype' => 'date',
          'dateFrom' => $this->database->setDate($_REQUEST['Report_Confessiondate_From']),
          'dateTo' => $this->database->setDate($_REQUEST['Report_Confessiondate_To']),
          'toggle' => $_REQUEST['Report_Confessiondate_Toggle_Value']);
    }
    if (!empty($_REQUEST['Report_Mariagedate_From']) && !empty($_REQUEST['Report_Mariagedate_To'])) {
      $this->requestedFilters['MEMBER_mariagedate'] = array(
          'filtertype' => 'date',
          'dateFrom' => $this->database->setDate($_REQUEST['Report_Mariagedate_From']),
          'dateTo' => $this->database->setDate($_REQUEST['Report_Mariagedate_To']),
          'toggle' => $_REQUEST['Report_Mariagedate_Toggle_Value']);
    }
    if (!empty($_REQUEST['Report_Memberstype_Select_Box'])) {
      $this->requestedFilters['MEMBER_membertype_id'] = array(
          'filtertype' => 'range',
          'toggle' => $_REQUEST['Report_Memberstype_Toggle_Value'],
          'selected' => implode(",", $_REQUEST['Report_Memberstype_Select_Box']));
    }
    if (!empty($_REQUEST['Report_Gender_Select_Box'])) {
      $this->requestedFilters['MEMBER_gender'] = array(
          'filtertype' => 'range',
          'selected' => implode(",", $_REQUEST['Report_Gender_Select_Box']));
    }
    if (!empty($_REQUEST['Report_City_Select_Box'])) {

      $this->requestedFilters['ADR_city'] = array(
          'filtertype' => 'range',
          'selected' => implode(",", $_REQUEST['Report_City_Select_Box']),
          'toggle' => $_REQUEST['Report_City_Toggle_Value']);
    }

    $this->requestedFilters['MEMBER_archive'] = array(
        'filtertype' => 'range',
        'selected' => (!empty($_REQUEST['Report_Archive_Select_Box'])) ? implode(",", $_REQUEST['Report_Archive_Select_Box']) : '0');

    if (!empty($_REQUEST['Report_Events_Select_Box'])) {
      $this->requestedFilters['EVENTTYPE_name'] = array(
          'filtertype' => 'range',
          'toggle' => $_REQUEST['Report_Events_Toggle_Value'],
          'selected' => implode(",", $_REQUEST['Report_Events_Select_Box']));
      if (empty($this->requestedColumns['events']))
        $this->requestedColumns['events'] = array();
      array_push($this->requestedColumns['events'], 'EVENT_id');
    }
    if (!empty($_REQUEST['Report_Events_Select_From']) && !empty($_REQUEST['Report_Events_Select_To'])) {
      $this->requestedFilters['EVENT_date'] = array(
          'filtertype' => 'date',
          'dateFrom' => $this->database->setDate($_REQUEST['Report_Events_Select_From']),
          'dateTo' => $this->database->setDate($_REQUEST['Report_Events_Select_To']),
          'toggle' => $_REQUEST['Report_Events_Toggle_Value']);
    }
    if (!empty($_REQUEST['groups'])) {
      $this->requestedFilters['GROUP_id'] = array(
          'filtertype' => 'group',
          'selected' => $_REQUEST['groups'],
          'toggle' => $_REQUEST['Report_Groups_Toggle_Value']);

      if (empty($this->requestedColumns['groups']))
        $this->requestedColumns['groups'] = array();
    }
    if ($_REQUEST['Report_Birthday_Slider_Values']) {
      $dateValues = explode(',', $_REQUEST['Report_Birthday_Slider_Values']);

      if ($dateValues[0] != 0 || $dateValues[1] != 120) {
        $this->requestedFilters['MEMBER_age'] = array(
            'filtertype' => 'member_age',
            'dateFrom' => $dateValues[0],
            'dateTo' => $dateValues[1],
            'toggle' => $_REQUEST['Report_Birthday_Toggle_Value']);
      }
    }
  }

  private function setQuery() {
    $this->setQueryFields();
    $this->setQueryJoin();
    $this->setQueryWhere();
    $this->setQueryOrderBy();
    $this->setQueryGroupBy();
    $this->createQuery();
  }

  private function setQueryFields() {
    foreach ($this->requestedColumns as $table => $fields) {
      foreach ($fields as $requestedfield) {
        if (($requestedfield !== '') && (!is_array($requestedfield))) {
          if ($table !== 'other') {
            array_push($this->queryFields, "`$table`.`$requestedfield`");
          } else {
            array_push($this->queryFields, "$requestedfield");
          }
        }
      }
    }
  }

  private function setQueryWhere() {
    foreach ($this->requestedFilters as $fieldname => $values) {
      switch ($values['filtertype']) {
        case 'date':
          $format = ($fieldname === 'MEMBER_birthdate') ? '%m-%d': '%y-%m-%d';
          $this->createDateQuery($fieldname, $values, $format);
          break;
        case 'range':
          $this->createRangeQuery($fieldname, $values);
          break;
        case 'member_age':
          $this->createMemberAgeQuery($fieldname, $values);
          break;
        case 'group':
          $this->createGroupQuery($fieldname, $values);
        case 'limiter':
          break;
      }
    }
  }

  private function createDateQuery($fieldname, $values, $format = '%y-%m-%d') {
    if (($values['dateFrom'] !== false) and ($values['dateTo'] !== false) and ($values['toggle'] !== false)) {
      if ($values['toggle'] === '1') {
        $dateQuery = "DATE_FORMAT(`" . $fieldname . "`, '" . $format . "') BETWEEN DATE_FORMAT('" . $values['dateFrom'] . "','" . $format . "') AND DATE_FORMAT('" . $values['dateTo'] . "', '" . $format . "') ";
      } else {
        $dateQuery = "DATE_FORMAT(`" . $fieldname . "`, '" . $format . "') NOT BETWEEN DATE_FORMAT('" . $values['dateFrom'] . "','" . $format . "') AND DATE_FORMAT('" . $values['dateTo'] . "', '" . $format . "') ";
      }
    }

    if (isset($dateQuery))
      array_push($this->queryWhere, $dateQuery);
  }

  private function createRangeQuery($fieldname, $values) {

    if ($values['selected'] !== false) {
      $values['selected'] = (($fieldname === 'MEMBER_archive')) ? $values['selected'] : '0,' . $values['selected'];
      $stringValues = str_replace(",", "'),LOWER('", $values['selected']);

      if (isset($values['toggle']) && ($values['toggle'] === 0)) {
        $rangeQuery = "LOWER(`" . $fieldname . "`) NOT IN (LOWER('$stringValues')) ";
      } else {
        $rangeQuery = "LOWER(`" . $fieldname . "`) IN (LOWER('$stringValues')) ";
      }
    }

    if (isset($rangeQuery))
      array_push($this->queryWhere, $rangeQuery);
  }

  private function createGroupQuery($fieldname, $values) {

    if ($values['selected'] !== false) {
      $stringValues = str_replace(",", "'),LOWER('", $values['selected']);

      if (isset($values['toggle']) && ($values['toggle'] === 0)) {
        $rangeQuery = "LOWER(`ADDRESS_GROUPS_TB`.`GROUP_id`) NOT IN (LOWER('$stringValues')) OR LOWER(`MEMBER_GROUPS_TB`.`GROUP_id`) NOT IN (LOWER('$stringValues')) ";
      } else {
        $rangeQuery = "LOWER(`ADDRESS_GROUPS_TB`.`GROUP_id`) IN (LOWER('$stringValues')) OR LOWER(`MEMBER_GROUPS_TB`.`GROUP_id`) IN (LOWER('$stringValues')) ";
      }
    }

    if (isset($rangeQuery))
      array_push($this->queryWhere, $rangeQuery);
  }

  private function createMemberAgeQuery($fieldname, $values) {

    if ($values['toggle'] === '1') {
      $havingQuery = "`MEMBER_age` >= " . $values['dateFrom'] . " AND `MEMBER_age` <= " . $values['dateTo'] . " ";
    } else {
      $havingQuery = "`MEMBER_age` < " . $values['dateFrom'] . " OR `MEMBER_age` > " . $values['dateTo'] . " ";
    }

    if (isset($havingQuery))
      array_push($this->queryHaving, $havingQuery);
  }

  private function setQueryJoin() {
    foreach ($this->requestedColumns as $tablename => $columns) {
      if (count($columns) > 0 || ($tablename === 'groups') && (isset($this->requestedFilters['GROUP_id']))) {
        switch ($tablename) {
          case 'addresses':
            array_push($this->queryJoin, 'LEFT JOIN `addresses` ON `members`.`ADR_id` = `addresses`.`ADR_id`');
            break;
          case 'membertypes':
            array_push($this->queryJoin, 'LEFT JOIN `membertypes` ON `members`.`MEMBER_membertype_id` = `membertypes`.`MEMBERTYPE_id`');
            break;
          case 'groups':
            array_push($this->queryJoin, 'LEFT JOIN `groupmembers` ON `members`.`MEMBER_id` = `groupmembers`.`GROUPMEMBERS_memberid`');
            array_push($this->queryJoin, 'LEFT JOIN `groupaddresses` ON `members`.`ADR_id` = `groupaddresses`.`GROUPADDRESSES_addressid`');
            array_push($this->queryJoin, 'LEFT JOIN `groups` AS `MEMBER_GROUPS_TB` ON (`groupmembers`.`GROUPMEMBERS_groupid` = `MEMBER_GROUPS_TB`.`GROUP_id`)');
            array_push($this->queryJoin, 'LEFT JOIN `groups` AS `ADDRESS_GROUPS_TB` ON (`groupaddresses`.`GROUPADDRESSES_groupid` = `ADDRESS_GROUPS_TB`.`GROUP_id`)');
            break;
          case 'events':
            array_push($this->queryJoin, 'LEFT JOIN `events` ON `members`.`MEMBER_id` = `events`.`EVENT_MEMBER_id`');
            array_push($this->queryJoin, 'LEFT JOIN `eventtypes` ON `events`.`EVENTTYPE_id` = `eventtypes`.`EVENTTYPE_id`');
            break;
        }
      }
    }
  }

  private function setQueryGroupBy() {
    if ($this->requestedFilters['ADR_only']['selected']) {
      $groupbyQuery = "`ADR_id`";
      array_push($this->requestedColumns['members'], 'MIN(`members`.MEMBER_rank)');
    } else {
      $groupbyQuery = "`MEMBER_id`";
    }
    array_push($this->queryGroupBy, $groupbyQuery);
  }

  private function setQueryOrderBy() {
    $this->requestedOrder['fields'] = (isset($_REQUEST['sidx']) ? explode(',', $_REQUEST['sidx']) : false);
    $this->requestedOrder['sortorder'] = (isset($_REQUEST['sord']) ? $_REQUEST['sord'] : false);

    if ($this->requestedOrder['fields'] !== false) {
      foreach ($this->requestedOrder['fields'] as $fieldname) {
        switch ($fieldname) {
          case (strpos($fieldname, 'ADR_familyname')):
            $fieldname = $fieldname . ', ADR_id asc';
            break;
          case (strpos($fieldname, 'ADR_sort')):
            $fieldname = 'ADR_familyname asc, ADR_id asc';
            break;
        }

        array_push($this->queryOrderBy, $fieldname);
      }
    }
  }

  private function createQuery() {
    $this->queryComplete = "SELECT SQL_CALC_FOUND_ROWS \n\r";

    $this->queryComplete .= implode(", \n\r", $this->queryFields) . " \n\r";
    $this->queryComplete .= "FROM `members` \n\r";

    if (count($this->queryJoin) > 0) {
      $this->queryComplete .= implode(" \n\r", $this->queryJoin) . " \n\r";
    }

    $this->queryComplete .= 'WHERE 1 ';

    if (count($this->queryWhere) > 0) {
      $this->queryComplete .= 'AND ' . implode(" AND \n\r", $this->queryWhere) . " \n\r";
    }

    if (count($this->queryGroupBy) > 0) {
      $this->queryComplete .= 'GROUP BY ' . implode(" AND \n\r", $this->queryGroupBy) . " \n\r";
    }

    if (count($this->queryHaving) > 0) {
      $this->queryComplete .= 'HAVING ' . implode(" AND \n\r", $this->queryHaving) . " \n\r";
    }

    if (($this->requestedOrder['fields']) !== false) {
      $this->queryComplete .= 'ORDER BY ' . implode(", ", $this->queryOrderBy) . ' ' . $this->requestedOrder['sortorder'];
    }
  }

  private function getData() {
    $this->requestedData = $this->database->getReportData($this->queryComplete);
    $this->totalresults = count($this->requestedData);
    $this->totalpages = ceil($this->totalresults / $this->showrows);

    $rownumber = 1;
    $startrow = (($this->currentpage - 1) * $this->showrows) + 1;
    $endrow = ($this->currentpage) * $this->showrows;

    // Add additional data if requested and remove helper columns
    foreach ($this->requestedData as $row) {

      if ((($rownumber >= $startrow) && ($rownumber <= $endrow)) || (($this->request === 'xls') || ($this->request === 'csv'))) {

        $resultrow['MEMBER_id'] = $row->MEMBER_id;
        if ($this->request === 'json') {
          $resultrow['ADR_sort'] = $row->ADR_familyname . '<div style="display:none">' . $row->ADR_id . '</div>';
        }

        foreach ($this->requestedColumnsRaw as $fieldname) {
          switch ($fieldname) {

            case 'MEMBER_fullname':
              $resultrow['MEMBER_fullname'] = $this->database->generateFullMemberName($row, false, true);
              break;

            case 'ADR_address':
              $resultrow['ADR_address'] = ($row->ADR_street_extra !== '') ? $row->ADR_street_extra . "\n" : '';
              $resultrow['ADR_address'] .= $row->ADR_street . " " . $row->ADR_number . "\n" . $row->ADR_zip . "  " . $row->ADR_city;
              $resultrow['ADR_address'] .= ($row->ADR_country !== '') ? "\n" . $row->ADR_country : '';
              $resultrow['ADR_address'] .= ($row->ADR_telephone) ? "\n" . $row->ADR_telephone : '';
              break;

            case 'ADR_fullfamilyname':
              $resultrow['ADR_fullfamilyname'] = ($row->ADR_familyname_preposition == '') ? $row->ADR_familyname : $row->ADR_familyname_preposition . ' ' . $row->ADR_familyname;
              break;

            case 'MEMBER_photo':
              $resultrow['MEMBER_photo'] = ($row->MEMBER_photo) ? "<img src='includes/phpThumb/phpThumb.php?w=40&h=40&far=1&zc=1&src=../../" . $row->MEMBER_photo . "' alt='" . __("Photo") . "' height='40'/>" : "<img src='includes/phpThumb/phpThumb.php?w=40&h=40&far=1&f=png&zc=1&src=../../css/images/users/user_unknown.png' alt='" . __("Photo") . "' height='40'/>";
              break;

            case 'MEMBER_gender':
              $resultrow['MEMBER_gender'] = ($row->MEMBER_gender == 'male') ? __("Male") : __("Female");
              break;

            case 'ADR_addressing':
                $start = $this->familynameStart($row);
                $name = $this->database->generateFullMemberName($row, false, true, '', false, true);
                $resultrow['ADR_addressing'] = $start . ' ' . $name;
              break;

            case 'ADR_start':
                $resultrow['ADR_start'] = $this->familynameStart($row);
              break;

            case 'EVENTS':
              $events = explode(',', $row->EVENTS);
              $translated = '';
              foreach ($events as $event) {
                $translated .= $this->database->getEventTypeTranslation($event) . ', ';
              }

              $resultrow['EVENTS'] = rtrim($translated, ', ');
              break;

            default:
              if (isset($row->{$fieldname})) {
                $resultrow[$fieldname] = $row->{$fieldname};
              } else {
                $resultrow[$fieldname] = '';
              }
              break;
          }
        }

        array_push($this->result, $resultrow);
      }
      $rownumber++;
    }
  }

  private function createJson() {
    // Set headers
    header('Content-type: application/json');
    $this->jsonData = array('total' => $this->totalpages, 'page' => $this->currentpage, 'records' => $this->totalresults, 'rows' => (array) $this->result);
    return json_encode($this->jsonData);
  }

  private function createCsv() {
    // Export Column names
    $columnnames = $_REQUEST['columnnames'];
    $this->csvData = "<table width='100%' cellpadding='0' cellspacing='0' border='0'>\n";
    $this->csvData .= '<tr><td><strong>' . implode("</strong></td><td><strong>", $columnnames) . "</strong></td></tr>\r\n";

    // Export Rows
    foreach ($this->result as $row) {
      unset($row['MEMBER_id']);
      unset($row['MEMBER_photo']);
      $this->csvData .= '<tr><td>' . implode("</td><td>", $row) . "</td></tr>\r\n";
    }

    //Return data
    return $this->csvData;
  }

}

?>