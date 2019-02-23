<div id="moveAddressDialogText">
  <h1>{t}Move address{/t}</h1>
  <p id="DialogStatus">{t}Fields marked with * are mandatory{/t}</p>
  <form id="DialogForm" name="moveAddressForm" method="post" action="">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tbody><tr>
          <th>&nbsp;</th>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <th>{t}Familyname{/t}*</th>
          <td>
            <input name="moveAddressFamilyname" type="text" class="text ui-widget-content ui-corner-all" id="moveAddressFamilyname" style="width:400px" maxlength="400" value="[+VALUE.ADR_familyname+]">
          </td>
        </tr>
        <tr>
          <th>{t}Preposition{/t}*</th>
          <td>
            <input name="moveAddressFamilyname_preposition" type="text" class="text ui-widget-content ui-corner-all" id="moveAddressFamilyname_preposition" style="width:80px" maxlength="80" value="[+VALUE.ADR_familyname_preposition+]">
          </td>
        </tr>
        <tr>
          <th>{t}Street{/t}*</th>
          <td>
            <input name="moveAddressStreet" type="text" class="text ui-widget-content ui-corner-all" id="moveAddressStreet" style="width:400px" maxlength="400" value="[+VALUE.ADR_street+]">
          </td>
        </tr>
        <tr>
          <th>{t}Street number{/t}*</th>
          <td>
            <input name="moveAddressNumber" type="text" class="text ui-widget-content ui-corner-all" id="moveAddressNumber" style="width:80px" maxlength="80" value="[+VALUE.ADR_number+]">
          </td>
        </tr>
        <tr>
          <th>{t}Additional streetinformation (room/building){/t}</th>
          <td>
            <input name="moveAddressStreet_extra" type="text" class="text ui-widget-content ui-corner-all" id="moveAddressStreet_extra" style="width:400px" maxlength="400" value="[+VALUE.ADR_street_extra+]">
          </td>
        </tr>
        <tr>
          <th>{t}Zipcode{/t}*</th>
          <td>
            <input name="moveAddressZip" type="text" class="text ui-widget-content ui-corner-all" id="moveAddressZip" style="width:80px" maxlength="80" value="[+VALUE.ADR_zip+]">
          </td>
        </tr>
        <tr>
          <th>{t}City{/t}*</th>
          <td>
            <input name="moveAddressCity" type="text" class="text ui-widget-content ui-corner-all" id="moveAddressCity" style="width:300px" maxlength="300" value="[+VALUE.ADR_city+]">
          </td>
        </tr>
        <tr>
          <th>{t}Country{/t}</th>
          <td>
            <input name="moveAddressCountry" type="text" class="text ui-widget-content ui-corner-all" id="moveAddressCountry" style="width:300px" maxlength="300" value="[+VALUE.ADR_country+]">
          </td>
        </tr>
        <tr>
          <th>{t}Telephone{/t}</th>
          <td>
            <input name="moveAddressTelephone" type="text" class="text ui-widget-content ui-corner-all" id="moveAddressTelephone" style="width:120px" maxlength="80" value="[+VALUE.ADR_telephone+]">
          </td>
        </tr>
        <tr>
          <th>{t}Email{/t}</th>
          <td>
            <input name="moveAddressEmail" type="text" class="text ui-widget-content ui-corner-all" id="moveAddressEmail" style="width:400px" maxlength="400" value="[+VALUE.ADR_email+]">
          </td>
        </tr>
        <tr>
          <th ></th>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2">{t}You are about to move this address. This action cannot be undone. Do you want to continue?{/t}</td>
        </tr>
        <tr>
          <th>&nbsp;</th>
          <td><div id="moveAddressDialogStatus"></div></td>
        </tr>
      </tbody>
    </table>
  </form>
</div>