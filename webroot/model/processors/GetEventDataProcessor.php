<?php

/**
 * This processor handles a request for getting a event between 2 members.
 */
class GetEventDataProcessor extends AbstractProcessor {

  public function processRequest() {
    if (!isset($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id))
      die();

    $MEMBER_id = $_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id;
    $events = $_SESSION['CURRENT-VIEW']['EVENTS_MEMBER'] = $this->database->getEventsForMember($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id);

    if (isset($_REQUEST['EVENT_id'])) {
      $_SESSION['CURRENT-VIEW']['CURRENT_EVENT'] = $this->database->getEventForMember($_REQUEST['EVENT_id']);
      die();
    } else {
      $_SESSION['CURRENT-VIEW']['CURRENT_EVENT'] = $this->database->getFirstEventForMember($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id);
    }

    if ($events) {
      ?>
      <div id="EventAccordion">
        <?php
        foreach ($events as $event) {
          ?>
          <h3>
            <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><a title="<?php echo $event['EVENT_id']; ?>"><?php echo $event['EVENT_date'] . " - " . $this->database->getEventTypeTranslation($event['EVENTTYPE_name']); ?></a></td>
                <td align="right" width="20"><a onclick="getDialog('event','delete')" title='<?php echo __("Delete event"); ?>' alt='<?php echo __("Delete event"); ?>'><span class="ui-icon ui-icon-closethick"></span></a></td>
              </tr>
            </table>
          </h3>
          <div>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <th >
                  <label id="LABEL_EVENT_date_<?php echo $event['EVENT_id']; ?>" for="EVENT_date_<?php echo $event['EVENT_id']; ?>"><?php print(__("Date")); ?></label>
                </th>
                <td>
                  <input name="EVENT_date" type="text" id="EVENT_date_<?php echo $event['EVENT_id']; ?>"  onclick="CreateCalendar(this.id)" onchange="editDetails('events',this, '#LABEL_EVENT_date_<?php echo $event['EVENT_id']; ?>')" VALUE="<?php print($event['EVENT_date']); ?>" readonly="readonly" style="width:80px"/>
                </td>
              </tr>
              <?php
              if (count($event['_MEMBERS']) > 1) {
                ?>
                <tr >
                  <th >
                    <label id="LABEL_EVENT_INVOLVED"><?php print(__("Involved")); ?></label>
                  </th>
                  <td>
                    <?php
                    foreach ($event['_MEMBERS'] as $_member) {
                      $MEMBER = $this->database->getMemberById($_member->EVENT_MEMBER_id, true);
                      if (count($MEMBER) > 0) {
                        $userlink = '<a onclick="getAddress(' . $MEMBER->ADR_id . ',' . $MEMBER->MEMBER_id . ')">' . $this->database->generateFullMemberName($MEMBER, false, true) . "</a>";
                      } else {
                        $userlink = '<a>' . $_member->EVENT_fullname . "</a>";
                      }
                      echo $userlink . "<br/>";
                    }
                    ?>
                  </td>
                </tr>
                <?php
              }
              ?>
              <tr>
                <th   >
                  <label id="LABEL_EVENT_note_<?php echo $event['EVENT_id']; ?>" for="EVENT_note_<?php echo $event['EVENT_id']; ?>"><?php print(__("Note")); ?></label>
                </th>
                <td>
                  <textarea id="EVENT_note_<?php echo $event['EVENT_id']; ?>" name="EVENT_note" style="width:300px;height:50px" rows="3" onchange="editDetails('events',this,'#LABEL_EVENT_note_<?php echo $event['EVENT_id']; ?>')" cols="3" ><?php echo $event['EVENT_note']; ?></textarea>
                </td>
              </tr>
            </table>
          </div>
          <?php
        }
        ?>
      </div>
      <?php
    } else {
      print(__("No data found"));
    }
  }

}
?>