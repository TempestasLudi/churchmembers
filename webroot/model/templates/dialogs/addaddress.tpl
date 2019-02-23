<div id="newAddressDialogText">
  <h1>{t}New address{/t}</h1>
  <p id="DialogStatus">{t}Fields marked with * are mandatory{/t}</p>
  <form id="DialogForm" name="newAddressForm" method="post" action="">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tbody>
        <tr>
          <th>&nbsp;</th>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <th>{t}Familyname{/t}*</th>
          <td>
            <input name="newAddressFamilyname" type="text" class="text ui-widget-content ui-corner-all" id="newAddressFamilyname" style="width:400px" maxlength="400">
          </td>
        </tr>
        <tr>
          <th>{t}Preposition{/t}</th>
          <td>
            <input name="newAddressFamilyname_preposition" type="text" class="text ui-widget-content ui-corner-all" id="newAddressFamilyname_preposition" style="width:100px" maxlength="100">
          </td>
        </tr>
        <tr>
          <th>{t}Street{/t}*</th>
          <td>
            <input name="newAddressStreet" type="text" class="text ui-widget-content ui-corner-all" id="newAddressStreet" style="width:400px" maxlength="400">
          </td>
        </tr>
        <tr>
          <th>{t}Street number{/t}*</th>
          <td>
            <input name="newAddressNumber" type="text" class="text ui-widget-content ui-corner-all" id="newAddressNumber" style="width:50px" maxlength="50">
          </td>
        </tr>
        <tr>
          <th>{t}Additional streetinformation (room/building){/t}</th>
          <td>
            <input name="newAddressStreet_extra" type="text" class="text ui-widget-content ui-corner-all" id="newAddressStreet_extra" style="width:400px" maxlength="400">
          </td>
        </tr>
        <tr>
          <th>{t}Zipcode{/t}*</th>
          <td>
            <input name="newAddressZip" type="text" class="text ui-widget-content ui-corner-all" id="newAddressZip" style="width:60px" maxlength="10" >
          </td>
        </tr>
        <tr>
          <th>{t}City{/t}*</th>
          <td>
            <input name="newAddressCity" type="text" class="text ui-widget-content ui-corner-all" id="newAddressCity" style="width:200px" maxlength="200">
          </td>
        </tr>
         <tr>
          <th>{t}Country{/t}</th>
          <td>
            <input name="newAddressCountry" type="text" class="text ui-widget-content ui-corner-all" id="newAddressCountry" style="width:200px" maxlength="200">
          </td>
        </tr>
        <tr>
          <th>{t}Telephone{/t}</th>
          <td>
            <input name="newAddressTelephone" type="text" class="text ui-widget-content ui-corner-all" id="newAddressTelephone" style="width:80px" maxlength="80">
          </td>
        </tr>
        <tr>
          <th>{t}Email{/t}</th>
          <td>
            <input name="newAddressEmail" type="text" class="text ui-widget-content ui-corner-all" id="newAddressEmail" style="width:400px" maxlength="400">
          </td>
        </tr>
        <tr>
          <th ></th>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <th>&nbsp;</th>
          <td><div id="newAddressDialogStatus"></div></td>
        </tr>
      </tbody>
    </table>
  </form>
</div>