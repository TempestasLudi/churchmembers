<?php

/**
 * This processor handles a request for getting a list of members & addresses in groups.
 *
 */
// $request = 'default', 'currentmember', 'currentaddress', 'select', 'search'
// $nodes =  'members', 'addresses', 'all'
// $id = ajaxcall, first call id = 1

class GetGroupsDataProcessor extends AbstractProcessor {

  public function processRequest() {
    $this->request = isset($_REQUEST['request']) ? $_REQUEST['request'] : 'default';
    $this->groupid = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : false;
    $this->nodes = isset($_REQUEST['nodes']) ? $_REQUEST['nodes'] : 'all';

    if (is_array($this->nodes)) {
      if (count($this->nodes) > 1) {
        $this->nodes = 'all';
      } else {
        $this->nodes = $this->nodes[0];
      }
    }

    if ($this->request && $this->groupid && $this->nodes) {

      $memberid = (isset($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id)) ? $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id : 0;

      switch ($this->request) {
        case 'default':
          $this->groups = $this->database->getGroupTree();
          $this->groupshierarchy = $this->generateGroupHierarchy($this->groups, new stdClass, $this->groupid);
          $this->grouptree = $this->generateGroupTree($this->groupshierarchy);
          break;

        case 'currentmember':
          $this->groups = $this->database->getMemberInGroup($memberid);
          $this->groupshierarchy = $this->generateGroupHierarchy($this->groups, new stdClass, 1);
          $this->grouptree = $this->createSmallGroupTree($this->groupshierarchy);
          break;

        case 'select':
          $addressid = (isset($_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id)) ? $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id : 0;
          switch ($this->nodes) {
            case 'addresses':
              $this->groups = $this->database->getAddressInGroup($addressid);
              break;
            case 'members':
              $this->groups = $this->database->getMemberInGroup($memberid);
              break;
          }

          $this->groupshierarchy = $this->generateGroupHierarchy($this->groups, new stdClass, 1);
          $this->grouptree = $this->createGroupTreeWithCheckbox($this->groupshierarchy);
          break;

        case 'search':
          $this->membergroups = $this->database->getMemberInGroup('%');
          $this->addressgroups = $this->database->getAddressInGroup('%');
          $this->groups = array_merge($this->membergroups, $this->addressgroups);
          $this->groupshierarchy = $this->generateGroupHierarchy($this->groups, new stdClass, $this->groupid);
          $this->grouptree = $this->createGroupTreeWithCheckbox($this->groupshierarchy);
          break;

        case 'email':
          $this->groups = $this->database->getMemberInGroup($memberid);
          $this->groupshierarchy = $this->generateGroupHierarchy($this->groups, new stdClass, 1);
          $allmembersnode = (object) array('GROUP_id' => -1, 'GROUP_name'=> __('All members'), 'GROUP_parent_id' =>1, 'GROUP_type'=> 'members', '_IN_GROUP' => 0, 'children' =>array());
          $this->groupshierarchy->{-1} =  $allmembersnode;
          $this->grouptree = $this->createGroupTreeWithCheckbox($this->groupshierarchy);
          break;
      }


      header('Content-type: application/json');
      print(json_encode($this->grouptree));
    } else {
      // GET TEMPLATE
      $contentPlaceholders = new stdClass;
      $GROUPSTREEtemplate = new TemplateParser("GROUPSTREE", $contentPlaceholders, $this->database);
      print_r($GROUPSTREEtemplate->parseOutput());
    }
  }

  /**
   * Generates a array as treeview for groups and members
   * @param object $groups
   * @param object $members
   * @param array $return
   * @param int $parent
   * @param int $level
   * @return array
   */
  public function generateGroupTree($groups, $parent = 1) {
    $return = array();
    foreach ($groups as $GROUP) { // Loop through each item of the list array
      $item = array("data" => array("title" => $GROUP->GROUP_name, "icon" => BASE_URL . "/css/images/icons/group_16x16.png"), "attr" => array("id" => "node-" . $GROUP->GROUP_id, "rel" => $GROUP->GROUP_type), "state" => "closed");

      if (count((array) $GROUP->children) > 0) {
        $item['state'] = "closed";
        $item['children'] = $this->generateGroupTree($GROUP->children, $parent);
      }

      if ($GROUP->GROUP_id === $parent || $GROUP->GROUP_parent_id === $parent) {
        $item['state'] = "open";
      }

      array_push($return, $item);
    }
    return $return;
  }

  /**
   * Generates an object with a group tree
   * @param object $object
   * @param object $return
   * @param int $parent
   * @return object
   */
  public function generateGroupHierarchy($object, $return, $parent = 1) {

    foreach ($object as $GROUP) { // Loop through each item of the list array
      if ($GROUP->GROUP_parent_id === $parent) {
        $return->{$GROUP->GROUP_id} = $GROUP;
        $return->{$GROUP->GROUP_id}->children = new stdClass;
        $this->generateGroupHierarchy($object, $return->{$GROUP->GROUP_id}->children, $GROUP->GROUP_id);
      }
    }
    return $return;
  }

  /**
   * Create a list of Groups as checkboxes with hierarchy
   */
  function createGroupTreeWithCheckbox($groups, $parent = 1) {
    $return = array();
    foreach ($groups as $GROUP) { // Loop through each item of the list array
      if ($GROUP->GROUP_parent_id === $parent) {

        $item = array("data" => array("title" => $GROUP->GROUP_name, "icon" => BASE_URL . "/css/images/icons/group_16x16.png"), "attr" => array("id" => "node-" . $GROUP->GROUP_id, "rel" => $GROUP->GROUP_type), "state" => "closed");

        if (count((array) $GROUP->children) > 0) {
          if ($GROUP->_IN_GROUP === 1) {
            $item['attr']['class'] = "jstree-checked";
            $item['state'] = "open";
          } else {
            $item['state'] = "closed";
          }
          $item['children'] = $this->CreateGroupTreeWithCheckbox($GROUP->children, $GROUP->GROUP_id);
        } else {
          $item['state'] = "leaf";
        }

        array_push($return, $item);
      }
    }
    return $return;
  }

  /**
   * Create a list of Groups of member
   */
  function createSmallGroupTree($groups, $parent = 1) {
    $return = array();
    foreach ($groups as $GROUP) { // Loop through each item of the list array
      if ($GROUP->GROUP_parent_id === $parent) {

        $item = array("data" => array("title" => $GROUP->GROUP_name, "icon" => BASE_URL . "/css/images/icons/group_16x16.png"), "attr" => array("id" => "node-" . $GROUP->GROUP_id, "rel" => $GROUP->GROUP_type), "state" => "open", "children" => false);

        if (count((array) $GROUP->children) > 0) {
          $item['children'] = $this->createSmallGroupTree($GROUP->children, $GROUP->GROUP_id);
        }

        $a = count((array) $item['children']);

        if (($GROUP->_IN_GROUP === 1) || ($item['children'] !== false)) {
          if ($item['children'] === false) {
            $item['state'] = "leaf";
          } else {
            $item['state'] = "open";
          }

          if ($GROUP->_IN_GROUP === 1) {
            $item['attr']['class'] = "jstree-checked";
          } else {
            $item['attr']['class'] = "jstree-undetermined";
          }

          array_push($return, $item);
        }
      }
    }


    return (count($return) > 0) ? $return : false;
  }

}

?>