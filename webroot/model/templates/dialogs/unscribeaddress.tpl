<div id="unscribeAddressDialogText">
  <h1>{t}Unsubscribe address{/t}</h1>
  <p id="DialogStatus">{t}Fields marked with * are mandatory{/t}</p>
  <form id="DialogForm" name="unscribeAddressForm" method="post" action="">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tbody>
        <tr >
          <th>&nbsp;</th>
          <td>&nbsp;</td>
        </tr>         
        <tr>
          <th>{t}Send to archive{/t}</th>
          <td >
            <input type="radio" name="unscribeADDRESS_action" id="unscribeADDRESS_action_archive" value="archiveAddress" class="checkbox ui-widget-content ui-corner-all" checked="checked">{t}Archive this address{/t}<br/>
            <input type="radio" name="unscribeADDRESS_action" id="unscribeADDRESS_action_delete" value="deleteAddress" class="checkbox ui-widget-content ui-corner-all">{t}Delete address{/t}</td>
        </tr>
        <tr>
          <th ></th>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <td colspan="2">{t}You are about to delete this address. This action cannot be undone. Do you want to continue?{/t}</td>
        </tr>
        <tr >
          <th>&nbsp;</th>
          <td><div id="unscribeAddressDialogStatus"></div></td>
        </tr>
      </tbody>
    </table>
  </form>
</div>