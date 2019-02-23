<div id="newMemberDialogText">
  <h1>{t}Add member{/t}</h1>
  <p id="DialogStatus">{t}Fields marked with * are mandatory{/t}</p>
  <form id="DialogForm" name="newMemberForm" method="post" action="">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tbody><tr >
          <th>&nbsp;</th>
          <td>&nbsp;</td>
        </tr>
        <tr style="display:none;">
          <th>&nbsp;</th>
          <td><input name="newAddressId" type="text" style="display: none" id="newAddressId" value=""></td>
        </tr>
        <tr >
          <th>{t}Firstname{/t}*</th>
          <td>
            <input name="newMemberFirstName" type="text" class="text ui-widget-content ui-corner-all" id="newMemberFirstName" style="width:400px" maxlength="400">
          </td>
        </tr>
        <tr >
          <th>{t}Christian name{/t}</th>
          <td>
            <input name="newMemberChristianName" type="text" class="text ui-widget-content ui-corner-all" id="newMemberChristianName" style="width:400px" maxlength="400">
          </td>
        </tr>
        <tr >
          <th>{t}Initials{/t}</th>
          <td>
            <input name="newMemberInitials" type="text" class="text ui-widget-content ui-corner-all" id="newMemberInitials" style="width:80px" maxlength="80">
          </td>
        </tr>
        <tr >
          <th>{t}Familyname{/t}*</th>
          <td>
            <input name="newMemberFamilyname" type="text" class="text ui-widget-content ui-corner-all" id="newMemberFamilyname" style="width:400px" maxlength="400" value="[+VALUE.ADR_familyname+]">
          </td>
        </tr>
        <tr >
          <th>{t}Preposition{/t}</th>
          <td>
            <input name="newMemberFamilyname_preposition" type="text" class="text ui-widget-content ui-corner-all" id="newMemberFamilyname_preposition" style="width:80px" maxlength="80" value="[+VALUE.ADR_familyname_preposition+]">
          </td>
        </tr>
        <tr>
          <th>{t}Birth date{/t}</th>
          <td>
            <input type="text" id="newMemberBirthdate" name="newMemberBirthdate" maxlength="10" style="width:80px" class="text ui-widget-content ui-corner-all" readonly="readonly" onfocus="CreateCalendar(this.id)">
            {t}City{/t}
            <input name="newMemberBirthplace" type="text" class="text ui-widget-content ui-corner-all" id="newMemberBirthplace" style="width:292px">
          </td>
        </tr>
        <tr  id="newMemberGenderTR">
          <th>{t}Male/Female{/t}</th>
          <td>
            <select name="newMemberGender" id="newMemberGender" class="text ui-widget-content ui-corner-all" style="width:70px">
              <option value="male">{t}Male{/t}</option>
              <option value="female">{t}Female{/t}</option>
            </select>
          </td>
        </tr>
        <tr >
          <th>{t}Mobilenumber{/t}</th>
          <td>
            <input name="newMemberMobile" type="text" class="text ui-widget-content ui-corner-all" id="newMemberMobile" style="width:80px" maxlength="80">
          </td>
        </tr>
        <tr >
          <th>{t}Email{/t}</th>
          <td>
            <input name="newMemberEmail" type="text" class="text ui-widget-content ui-corner-all" id="newMemberEmail" style="width:400px" maxlength="400">
          </td>
        </tr>
        <tr  id="newParentTR">
          <th>{t}Parent/Married{/t}*</th>
          <td><select name="newParent" id="newParent" class="text ui-widget-content ui-corner-all" style="width:150px">
              <option value="1">{t}Parent/Married{/t}</option>
              <option value="0">{t}Child{/t}</option>
            </select></td>
        </tr>
        <tr  id="newMembertype_idTR">
          <th>{t}Membertype{/t}*</th>
          <td>[+INPUT.DIALOG_MEMBER_TYPE+]</td>
        </tr>
        <tr>
          <th ></th>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <th>&nbsp;</th>
          <td><div id="newMemberDialogStatus"></div></td>
        </tr>
      </tbody></table>
  </form>
</div>