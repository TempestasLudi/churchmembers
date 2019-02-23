<?php

/**
 * This processor handles a request for getting an address.
 */
class GetEmailDataProcessor extends AbstractProcessor {

  private $member_emails = array();

  public function processRequest() {
    $this->request = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'default';

    switch ($this->request) {
      case 'sendemail':
        $sendmail = new Sendmail($this->database);

        $sender = array('EMAIL'=>$_SESSION['SEND_EMAIL']['SENDER']['EMAIL'], 'USERNAME' => $_SESSION['SEND_EMAIL']['SENDER']['USERNAME']);
        $sendmail->setSender($sender);
        $sendmail->setReceivers($_SESSION['SEND_EMAIL']['MAILINGLIST']);

        $sendmail->setSubject($this->database->getSetting('mail_subject')->SETTINGS_value . $_SESSION['SEND_EMAIL']['SUBJECT']);
        $sendmail->setBody($_SESSION['SEND_EMAIL']['MESSAGE']);

        print($sendmail->sendMail());
        break;

      case 'preview':
        $this->message = isset($_REQUEST['message']) ? $_REQUEST['message'] : false;
        $this->subject = isset($_REQUEST['subject']) ? $_REQUEST['subject'] : false;
        $this->receivers = isset($_REQUEST['receivers']) ? $_REQUEST['receivers'] : false;
        $this->receiverstext = isset($_REQUEST['receiverstext']) ? join(' | ', $_REQUEST['receiverstext']) : false;

        if ((!$this->message) || (strlen($this->message) < 100)) {
          $this->errormsg = __("No message given or message is to short");
          $this->returnError();
        } else {
          $_SESSION['SEND_EMAIL']['MESSAGE'] = '';
        }

        if ((!$this->subject) || (strlen($this->subject) < 5)) {
          $this->errormsg = __("No subject given or subject is to short");
          $this->returnError();
        } else {
          $this->subject = $this->subject;
          $_SESSION['SEND_EMAIL']['SUBJECT'] = $this->subject;
        }

        if (!$this->receivers) {
          $this->errormsg = __("No receivers");
          $this->returnError();
        } else {
          $this->getReceivers();
          usort($this->member_emails, "sortByFullname");
          $_SESSION['SEND_EMAIL']['MAILINGLIST'] = $this->member_emails;
        }

        if ($_SESSION['USER']->getUserlinktomemberId()) {
          $currentmember = $_SESSION['USER']->getUserlinktomember();
          $this->sender_username = $this->database->generateFullMemberName($currentmember, false, true);
          $this->sender_email = ($currentmember->MEMBER_email !== '') ? $currentmember->MEMBER_email : $this->database->getSetting('administrator_email')->SETTINGS_value;
        } else {
          $this->sender_username = $this->database->getSetting('administrator_email')->SETTINGS_value;
          $this->sender_email = $this->database->getSetting('administrator_email')->SETTINGS_value;
        }
        $_SESSION['SEND_EMAIL']['SENDER'] = array();
        $_SESSION['SEND_EMAIL']['SENDER']['USERNAME'] = $this->sender_username;
        $_SESSION['SEND_EMAIL']['SENDER']['EMAIL'] = $this->sender_email;

        $this->succesmsg = $this->previewEmail();
        $this->returnSucces();

        break;

      case 'template':
        $contentPlaceholders = new stdClass;
        $contentPlaceholders->MAIL_SUBJECT_TEMPLATE = $this->database->getSetting('mail_subject')->SETTINGS_value;
        $PHOTOBOOKtemplate = new TemplateParser("EMAIL", $contentPlaceholders, $this->database);

        print_r($PHOTOBOOKtemplate->parseOutput());
        break;
    }
  }

  private function previewEmail() {
    $contentPlaceholders = new stdClass;
    $contentPlaceholders->SUBJECT = $this->subject;
    $contentPlaceholders->MESSAGE = $this->message;
    $contentPlaceholders->USERNAME = $this->sender_username;
    $contentPlaceholders->EMAIL = $this->sender_email;
    $contentPlaceholders->RECEIVERS = $this->receiverstext;

    $EMAILMESSAGEtemplate = new TemplateParser("EMAILMESSAGE", $contentPlaceholders, $this->database);

    $_SESSION['SEND_EMAIL']['MESSAGE'] = $EMAILMESSAGEtemplate->parseOutput();
    return ($_SESSION['SEND_EMAIL']['MESSAGE']);
  }

  private function getReceivers() {
    if ((count($this->receivers['group']) > 0) && in_array('-1', $this->receivers['group'])) {
      // All members
      $this->member_emails = $this->database->getMembersEmail('all');
      $this->receiverstext = __('All members');
    } else {

      $this->member_ids = isset($this->receivers['member']) ? $this->receivers['member'] : false;
      if ($this->member_ids !== false) {
        $this->member_emails = $this->database->getMembersEmail("'" . join("','", $this->member_ids) . "'");
      }

      $this->group_ids = isset($this->receivers['group']) ? $this->receivers['group'] : false;
      if ($this->group_ids !== false) {
        $this->groupsprocessor = new GetGroupsDataProcessor();
        $this->groups = $this->database->getGroupTree();
        $this->groupshierarchy = $this->groupsprocessor->generateGroupHierarchy($this->groups, new stdClass, 1);
        $this->getMemberEmailsInGroups($this->groupshierarchy);
      }
    }
  }

  private function getMemberEmailsInGroups($groups, $addchild_members = false) {
    foreach ($groups as $GROUP) {
      if (in_array($GROUP->GROUP_id, $this->group_ids) || $addchild_members === true) {
        $emails = $this->database->getMembersEmailInGroup($GROUP->GROUP_id);
        if (count($emails) > 0) {
          $this->member_emails = $this->member_emails + $emails; // + applied to arrays acts as an union operator
        }
        if (count((array) $GROUP->children) > 0) {
          $this->getMemberEmailsInGroups($GROUP->children, true);
        }
      } else {
        if (count((array) $GROUP->children) > 0) {
          $this->getMemberEmailsInGroups($GROUP->children, false);
        }
      }
    }
  }

  private function returnSucces() {
    $this->jsonreturn = array('result' => 'succes', 'msg' => $this->succesmsg);
    header('Content-type: application/json');
    print_r(json_encode($this->jsonreturn));
    exit();
  }

  private function returnError() {
    $this->jsonreturn = array('result' => 'fail', 'msg' => $this->errormsg);
    header('Content-type: application/json');
    print_r(json_encode($this->jsonreturn));
    exit();
  }

}

function sortByFullname($a, $b) {
  return strcmp($a["SORT"], $b["SORT"]);
}
?>