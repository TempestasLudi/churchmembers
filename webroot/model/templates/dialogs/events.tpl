<div id="newEventDialogText">
  <h1>{t}Add event{/t}</h1>
  <p id="DialogStatus"></p>
  <form id="newEventForm" name="newEventForm" method="post" action="">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tbody>
        <tr >
          <th>&nbsp;</th>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <th>{t}History event{/t}</th>
          <td>[+INPUT.DIALOG_EVENTS_TYPES+]</td>
        </tr>
        <tr >
          <th>&nbsp;</th>
          <td>&nbsp;</td>
        </tr>
        <tr id="newDateTR">
          <th>{t}Date{/t}*</th>
          <td><input name="newEventDate" type="text" id="newEventDate" onfocus="CreateCalendar(this.id)" style="width:80px" maxlength="10" class="text ui-widget-content ui-corner-all" readonly="readonly"></td>
        </tr>
        <tr id="newEventPartnerTR" style="display: none; ">
          <th>{t}Partner{/t}</th>
          <td>
            <input name="newEventPartnerName" type="text" id="newEventPartnerName" style="width:300px" class="text ui-widget-content ui-corner-all ui-autocomplete-input" onclick="this.select(); $('#newEventPartnerName').autocomplete('search');" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true">
            <input name="newEventPartnerId" type="text" id="newEventPartnerId" style="display: none">
          </td>
        </tr>
        <tr  id="newMembertypeTR" style="display: none; ">
          <th>{t}New membertype{/t}</th>
          <td>[+INPUT.DIALOG_MEMBER_TYPE+]
          </td>
        </tr>
        <tr id="newEventCityTR" style="display: none; ">
          <th>{t}City{/t}</th>
          <td><input name="newEventCity" type="text" id="newEventCity" style="width:300px" class="text ui-widget-content ui-corner-all"></td>
        </tr>
        <tr id="newEventChurchTR" style="display: none; ">
          <th>{t}Church{/t}</th>
          <td><input name="newEventChurch" type="text" id="newEventChurch" style="width:300px" class="text ui-widget-content ui-corner-all"></td>
        </tr>
        <tr id="newEventNoteTR" style="">
          <th>{t}Note{/t}</th>
          <td><textarea name="newEventNote" style="width:300px" rows="3" id="newEventNote" class="text ui-widget-content ui-corner-all" cols="4"></textarea></td>
        </tr>
        <tr>
          <th width="150"  >&nbsp;</th>
          <td><input name="newEventReason" type="text" style="display: none" id="newEventReason" value=""></td>
        </tr>
        <tr>
          <th ></th>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <th>&nbsp;</th>
          <td><div id="newEventDialogStatus"></div></td>
        </tr>
      </tbody>
    </table>
  </form>
</div>
<script type="text/javascript">
  $(document).ready(function() {
  updateEventDialog($('#newEventType').val());
  loadEventLiveSearch("#newEventPartnerName","newEventPartnerId");
})
</script>