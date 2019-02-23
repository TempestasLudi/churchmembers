<?php

/**
 * This processor handles a request for getting all data from a specific member.
 */
class GetMemberDataProcessor extends AbstractProcessor {

  public function processRequest() {

    if (isset($_SESSION['CURRENT-VIEW']['CURRENT_GROUP']->GROUP_id))
      $groupId = $_SESSION['CURRENT-VIEW']['CURRENT_GROUP']->GROUP_id;
    if (isset($_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id))
      $addressId = $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id;

    /* Fill CURRENT_MEMBER */
    if (isset($_REQUEST['id']) && ($_REQUEST['id'] !== '' ) && $_REQUEST['id'] !== 'false') {
      $memberId = $_REQUEST['id'];
      $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER'] = $this->database->getMemberById($memberId);
    } elseif (isset($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id)) {
      $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER'] = $this->database->getMemberById($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id);
      $memberId = $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id;
    } else {

      if (isset($groupId)) {
        $memberIdFromDb = $this->database->getFirstMemberIdOfGroup($groupId);
      } elseif (isset($addressId)) {
        $memberIdFromDb = $this->database->getFirstMemberIdOfAddress($addressId);
      } else {
        $memberIdFromDb = false;
      }

      if ($memberIdFromDb) {
        $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER'] = $this->database->getMemberById($memberIdFromDb->MEMBER_id);
      } else {
        $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER'] = "";
        ?>

        <div class="ui-widget-header block-header"><h1><?php print(__("No members available")); ?></h1></div>
        <div class="block-content">
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr >
              <td height="40"><?php print(__("No members available")); ?></td>
            </tr>
          </table>
        </div>

        <?php
        exit;
      }
    }

    ///////////////////////// IF CURRENT ADDRESS IS NOT MEMBER ADDRESS, USED WHEN IN LIST A MEMBER IS SELECTED /////////////////////////
    //if ((!isset($_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS'])) or ((!isset($groupId)) and ($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->ADR_id != $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS']->ADR_id)) ){
    if (!isset($_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS'])) {
      $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS'] = $this->database->getAddressById($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->ADR_id);
      $_SESSION['CURRENT-VIEW']['MEMBERS_IN_ADDRESS'] = $this->database->getMembersForAddress($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->ADR_id, $_SESSION['ARCHIVE-MODE']);
    }

    $_SESSION['CURRENT-VIEW']['EVENTS_MEMBER'] = "";
    $_SESSION['CURRENT-VIEW']['CURRENT_EVENT'] = "";
    $this->loadPhoto();
    $this->setPhotoUploadButton();

    $contentPlaceholders = $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER'];
    $MEMBERtemplate = new TemplateParser("MEMBER", $contentPlaceholders, $this->database);
    print_r($MEMBERtemplate->parseOutput());
  }

  private function loadPhoto() {
    if ($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_photo === "" ||
            !file_exists(BASE_PATH . $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_photo)) {
      $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_photo = 'css/images/users/user_unknown.png';
    }
  }

  private function setPhotoUploadButton() {

    if ($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->ADR_id === $_SESSION['USER']->getUserlinktomemberAdr_Id() ||
            $_SESSION['USER']->checkUserrights('edit_mode')) {
      $_SESSION['CURRENT-VIEW']['PHOTO_BUTTON'] = 'createPhotoButton();';
    } else {
      $_SESSION['CURRENT-VIEW']['PHOTO_BUTTON'] = '$("#remove_photo").remove()';
    }
  }

}
?>