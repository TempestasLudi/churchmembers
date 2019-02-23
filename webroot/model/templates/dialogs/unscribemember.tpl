<div id="unscribeMemberDialogText">
  <h1>{t}Unsubscribe member{/t}</h1>
  <p id="DialogStatus">{t}Fields marked with * are mandatory{/t}</p>
  <form id="DialogForm" name="unscribeMemberForm" method="post" action="">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tbody>
        <tr >
          <th>&nbsp;</th>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <th>{t}Send to archive{/t}</th>
          <td>
            <input type="radio" name="unscribeMEMBER_action" id="unscribeMEMBER_action_archive" value="archiveMember" class="checkbox ui-widget-content ui-corner-all" checked="checked">{t}Archive member{/t}<br/>
            <input type="radio" name="unscribeMEMBER_action" id="unscribeMEMBER_action_delete" value="deleteMember" class="checkbox ui-widget-content ui-corner-all">{t}Delete member(not archived!){/t}
          </td>
        </tr>
        <tr>
          <th ></th>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <td colspan="2">{t}You are about to delete this member. This action cannot be undone. Do you want to continue?{/t}</td>
        </tr>
        <tr >
          <th>&nbsp;</th>
          <td><div id="unscribeMemberDialogStatus"></div></td>
        </tr>
      </tbody>
    </table>
  </form>
</div>