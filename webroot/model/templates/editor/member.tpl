<div class="ui-widget-header block-header"><h1>[+VALUE.MEMBER_fullname+]&nbsp;[+BUTTONS.MEMBER+]</h1></div>
<div id="member-tabs" >
  <ul>
    <li><a href="#member-tabs-1">{t}Personal data{/t}</a></li>
    <li><a href="#member-tabs-2">{t}Church data{/t}</a></li>
    <li><a href="#member-tabs-3">{t}Groups{/t}</a></li>
    <li><a href="#member-tabs-4" onmouseover="getMemberEvents()">{t}History{/t}</a></li>
    <li><a href="#member-tabs-5">{t}Settings{/t}</a></li>
  </ul>

  <!-- *********************   TAB 1 Personal   ************************************ -->
  <div id="member-tabs-1">
    <table border="0" cellpadding="0" cellspacing="0" id="Personal" width="100%">
      <tr>
        <th width="140"><label id="LABEL_MEMBER_firstname" for="MEMBER_firstname">{t}Firstname{/t}</label>
          <input name="MEMBER_result" type="hidden" id="MEMBER_result" VALUE="1" /><input name="MEMBER_archive" type="hidden" id="MEMBER_archive" VALUE="[+VALUE.MEMBER_archive+]" />
        </th>
        <td width="250"><input name="MEMBER_firstname" type="text" id="MEMBER_firstname" onchange="editDetails('members',this, '#LABEL_MEMBER_firstname')"  VALUE="[+VALUE.MEMBER_firstname+]"  style="width:250px;" /></td>
        <td rowspan="12">
          <div style="float:right">
            <a class="colorbox" href="[+VALUE.MEMBER_photo+]">
              <img id="MEMBER_photo" src="includes/phpThumb/phpThumb.php?src=../../[+VALUE.MEMBER_photo+]&w=128&h=128&far=1&zc=1" width="128" alt="{t}Photo{/t}" class="ui-state-default ui-corner-all"/>
            </a>
            <br /><br />
            <div id="photo-uploader"></div>
            <button id="remove_photo">{t}Delete{/t}</button>
          </div>
        </td>
      <script type="text/javascript">[+INPUT.MEMBER_photobutton+]</script>
      </tr>
      <tr>
        <th><label id="LABEL_MEMBER_christianname" for="MEMBER_christianname">{t}Christian name{/t}</label></th>
        <td><input name="MEMBER_christianname" type="text" id="MEMBER_christianname" onchange="editDetails('members',this, '#LABEL_MEMBER_christianname')"  VALUE="[+VALUE.MEMBER_christianname+]"  style="width:250px;" /></td>
      </tr>
      <tr>
        <th><label id="LABEL_MEMBER_initials" for="MEMBER_initials">{t}Initials{/t}</label></th>
        <td><input name="MEMBER_initials" type="text" id="MEMBER_initials" onchange="editDetails('members',this, '#LABEL_MEMBER_initials')"  VALUE="[+VALUE.MEMBER_initials+]"  style="width:250px;" /></td>
      </tr>
      <tr>
        <th><label id="LABEL_MEMBER_familyname" for="MEMBER_familyname">{t}Familyname{/t}</label></th>
        <td><input name="MEMBER_familyname" type="text" id="MEMBER_familyname" onchange="editDetails('members',this, '#LABEL_MEMBER_familyname');"  value="[+VALUE.MEMBER_familyname+]"  style="width:250px;" /></td>
      </tr>
      <tr>
        <th><label id="LABEL_MEMBER_familyname_preposition" for="MEMBER_familyname_preposition">{t}Preposition{/t}</label></th>
        <td><input name="MEMBER_familyname_preposition" type="text" id="MEMBER_familyname_preposition" onchange="editDetails('members',this, '#LABEL_MEMBER_familyname_preposition')"  value="[+VALUE.MEMBER_familyname_preposition+]"  style="width:250px;" /></td>
      </tr>
      <tr>
        <th><label id="LABEL_MEMBER_birthdate" for="MEMBER_birthdate">{t}Birth date{/t}</label></th>
        <td><input name="MEMBER_birthdate" type="text" id="MEMBER_birthdate"  onclick="CreateCalendar(this.id)" onchange="editDetails('members',this, '#LABEL_MEMBER_birthdate')"  VALUE="[+VALUE.MEMBER_birthdate+]"  style="width:80px;" readonly="readonly"  /></td>
      </tr>
      <tr>
        <th><label id="LABEL_MEMBER_birthplace" for="MEMBER_birthplace">{t}City{/t}</label></th>
        <td><input name="MEMBER_birthplace" type="text" id="MEMBER_birthplace"  VALUE="[+VALUE.MEMBER_birthplace+]"  onchange="editDetails('members',this, '#LABEL_MEMBER_birthplace'); $(this).val($(this).val().toUpperCase()); " style="width:250px;"/></td>
      </tr>
      <tr>
        <th><label id="LABEL_MEMBER_gender">{t}Male/Female{/t}</label></th>
        <td>[+INPUT.GENDER+]</td>
      </tr>
      <tr>
        <th><label id="LABEL_MEMBER_mobilephone" for="MEMBER_mobilephone">{t}Mobilenumber{/t}</label></th>
        <td><input name="MEMBER_mobilephone" type="text" id="MEMBER_mobilephone" onchange="editDetails('members',this, '#LABEL_MEMBER_mobilephone')"  VALUE="[+VALUE.MEMBER_mobilephone+]"  style="width:220px;  float:left;"/><button id="add_phone" class="add_field_button">{t}Add field{/t}</button></td>
      </tr>
      <tr id="MEMBER_business_phone_tr" style="display:none">
        <th><label id="LABEL_MEMBER_business_phone" for="MEMBER_business_phone">{t}Telephone (work){/t}</label></th>
        <td><input name="MEMBER_business_phone" type="text" id="MEMBER_business_phone" onchange="editDetails('members',this, '#LABEL_MEMBER_business_phone')"  VALUE="[+VALUE.MEMBER_business_phone+]"  style="width:220px;"/></td>
      </tr>
      <tr>
        <th><label id="LABEL_MEMBER_email" for="MEMBER_email">{t}Email{/t}</label></th>
        <td><input name="MEMBER_email" type="text" id="MEMBER_email" onchange="editDetails('members',this, '#LABEL_MEMBER_email')"  VALUE="[+VALUE.MEMBER_email+]"  style="width:220px; float:left;"/><button id="add_email" class="add_field_button">{t}Add field{/t}</button></td>
      </tr>
      <tr id="MEMBER_business_email_tr" style="display:none">
        <th><label id="LABEL_MEMBER_business_email" for="MEMBER_business_email">{t}Email (work){/t}</label></th>
        <td><input name="MEMBER_business_email" type="text" id="MEMBER_business_email" onchange="editDetails('members',this, '#LABEL_MEMBER_business_email')"  VALUE="[+VALUE.MEMBER_business_email+]"  style="width:220px;"/></td>
      </tr>
      <tr>
        <th><label id="LABEL_MEMBER_introduction" for="MEMBER_introduction">{t}Introduction{/t}</label></th>
        <td colspan="2"><textarea name="MEMBER_introduction" id="MEMBER_introduction" onchange="editDetails('members',this, '#LABEL_MEMBER_introduction')"  style="width:250px;heigth:80px" rows="6" >[+VALUE.MEMBER_introduction+]</textarea></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td colspan="2"></td>
      </tr>
      <tr>
        <th><label id="LABEL_MEMBER_notes" for="MEMBER_notes">{t}Note{/t}</label></th>
        <td colspan="2"><textarea name="MEMBER_notes" id="MEMBER_notes" onchange="editDetails('members',this, '#LABEL_MEMBER_notes')"  style="width:250px;heigth:40px" rows="3" >[+VALUE.MEMBER_notes+]</textarea></td>
      </tr>
    </table>
  </div>

  <!-- *********************   TAB 2 Churchinfo   *********************************** -->
  <div id="member-tabs-2">
    <table border="0" cellpadding="0" cellspacing="0" id="Church" width="100%">
      <tr>
        <th width="140"><label id="LABEL_MEMBER_type">{t}Membertype{/t}</label></th>
        <td colspan="3">[+INPUT.MEMBER_TYPE+]</td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td >&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <th>{t}Date{/t}</th>
        <th>{t}City{/t}</th>
        <th>{t}Church{/t}</th>
      </tr>
      <tr>
        <th ><label id="LABEL_MEMBER_baptism">{t}Baptised{/t}</label></th>
        <td ><input name="MEMBER_baptismdate" type="text" id="MEMBER_baptismdate"  onclick="CreateCalendar(this.id)" onchange="editDetails('members',this, '#LABEL_MEMBER_baptism')" VALUE="[+VALUE.MEMBER_baptismdate+]" maxlength="10" readonly="readonly" style="width:80px;" />            </td>
        <td><input name="MEMBER_baptismcity" type="text" id="MEMBER_baptismcity" onchange="editDetails('members',this, '#LABEL_MEMBER_baptism');$(this).val($(this).val().toUpperCase()); "  VALUE="[+VALUE.MEMBER_baptismcity+]"  style="width:165px;" /></td>
        <td><input name="MEMBER_baptismchurch" type="text" id="MEMBER_baptismchurch" onchange="editDetails('members',this, '#LABEL_MEMBER_baptism')"  VALUE="[+VALUE.MEMBER_baptismchurch+]"  style="width:120px;"/></td>
      </tr>
      <tr>
        <th ><label id="LABEL_MEMBER_confession">{t}Confession{/t}</label></th>
        <td ><input name="MEMBER_confessiondate" type="text" id="MEMBER_confessiondate"  onclick="CreateCalendar(this.id)" onchange="editDetails('members',this, '#LABEL_MEMBER_confession')" VALUE="[+VALUE.MEMBER_confessiondate+]" maxlength="10" readonly="readonly" style="width:80px;" /></td>
        <td><input name="MEMBER_confessioncity" type="text" id="MEMBER_confessioncity" onchange="editDetails('members',this, '#LABEL_MEMBER_confession');$(this).val($(this).val().toUpperCase()); " VALUE="[+VALUE.MEMBER_confessioncity+]"  style="width:165px;" /></td>
        <td><input name="MEMBER_confessionchurch" type="text" id="MEMBER_confessionchurch" onchange="editDetails('members',this, '#LABEL_MEMBER_confession')"  VALUE="[+VALUE.MEMBER_confessionchurch+]"  style="width:120px;"/></td>
      </tr>
      <tr>
        <th ><label id="LABEL_MEMBER_mariage">{t}Marriage{/t}</label></th>
        <td ><input name="MEMBER_mariagedate" type="text" id="MEMBER_mariagedate"  onclick="CreateCalendar(this.id)" onchange="editDetails('members',this, '#LABEL_MEMBER_mariage')" VALUE="[+VALUE.MEMBER_mariagedate+]" maxlength="10" readonly="readonly" style="width:80px;" /></td>
        <td><input name="MEMBER_mariagecity" type="text" id="MEMBER_mariagecity" onchange="editDetails('members',this, '#LABEL_MEMBER_mariage');$(this).val($(this).val().toUpperCase()); "  VALUE="[+VALUE.MEMBER_mariagecity+]"  style="width:165px;" /></td>
        <td><input name="MEMBER_mariagechurch" type="text" id="MEMBER_mariagechurch" onchange="editDetails('members',this, '#LABEL_MEMBER_mariage')"  VALUE="[+VALUE.MEMBER_mariagechurch+]"  style="width:120px;"/></td>
      </tr>
    </table>
  </div>

  <!-- *********************   TAB 3 Groups   *********************************** -->
  <div id="member-tabs-3">
    <table border="0" cellpadding="0" cellspacing="0" id="Groups" width="100%">
      <tr>
        <th  width="140"><label id="LABEL_MEMBER_OF_GROUP">{t}Member of:{/t}</label></th>
        <td><div id="SmallMemberOfGroupTree"></div></td>
      </tr>
      <tr>
        <th >&nbsp;</th>
        <td></td>
      </tr>
      <tr>
        <th >{t}Select groups{/t}</th>
        <td><div id="MemberOfGroupTree"></div></td>
      </tr>
    </table>

  </div>
  <!-- *********************   TAB 4 Events   *********************************** -->
  <div id="member-tabs-4">
    <table border="0" cellpadding="0" cellspacing="0" id="EventsTable" width="100%">
      <tr>
        <td colspan="2" ><div id="Events" style="width:100%;"></div></td>
      </tr>

    </table>
  </div>

  <!-- *********************   TAB 5 Options   ************************************** -->
  <div id="member-tabs-5">
    <table border="0" cellpadding="0" cellspacing="0" id="ActionsTable" width="100%">
      <tr>
        <th><label id="LABEL_MEMBER_ACTIONS">{t}Settings{/t}</label></th>
      </tr>
      <tr>
        <td>
          <div id="MEMBER_ACTIONS">
            <label for="MEMBER_parent">{t}Parent/Married{/t}</label>[+INPUT.MEMBER_parent+]
            <label for="MEMBER_inyearbook">{t}List member in yearbook{/t}</label>[+INPUT.MEMBER_inyearbook+]
          </div>
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <th><label for="LABEL_MEMBER_birthdateview">{t}Birth date view{/t}</label></th>
      </tr>
      <tr>
        <td>[+INPUT.MEMBER_birthdateview+]</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <th><label for="LABEL_MEMBER_familynameview">{t}Familyname view{/t}</label></th>
      </tr>
      <tr>
        <td>[+INPUT.MEMBER_familynameview+]</td>
      </tr>
    </table>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    $("#MEMBER_ACTIONS, #MEMBER_YEARBOOK, #MEMBER_membertype_id_checkbox, .MEMBER_GROUPS_checkbox, #MEMBER_gender_checkbox").buttonset();

    $("#add_email, #add_phone").button({
      icons: {primary: "ui-icon-circle-plus"},
      text: false
    })

    if ($('#MEMBER_business_phone').val()){
      $("#MEMBER_business_phone_tr").show();
      $("#add_phone").remove()
    }

    if ($('#MEMBER_business_email').val()){
      $("#MEMBER_business_email_tr").show();
      $("#add_email").remove()
    }

    $("#add_phone").click(function(){$("#MEMBER_business_phone_tr").show();$("#add_phone").remove()});
    $("#add_email").click(function(){$("#MEMBER_business_email_tr").show();$("#add_email").remove()});

    if (document.getElementById("ADRRESS_result") != null){
      createSplitButton("#MEMBER_action")
    } else {
      $('.splitbutton').remove();
    }

    loadEditButtons();
    loadHTMLEditor();


    $('a.colorbox').colorbox({
      width:"60%",
      height:"60%",
      close: labelClose
    });

    $("#SmallMemberOfGroupTree").jstree({
      // List of active plugins
      "plugins" : ["themes","ui","json_data"],
      "core" : {
        "html_titles" : true,
        "limit": 1
      },
      "ui" : {
        "select_limit" : 1,
        "selected_parent_close" : "select_parent"
      },
      "themes" : {
        "theme" : "default",
        "url" : "includes/jstree/themes/default/style.css"
      },
      "json_data" : {
        "ajax": {
          "url": "model/classes/ProcessRequest.php",
          "data": function(n) {
            return {
              "id": n.attr ? n.attr("id").replace("node-", "") : 1,
          "action": "getdata",
          "type": "groups",
          "request": "currentmember",
          "nodes" : "members"
            };
          }
        }
      }
    })

    RenderMemberOfGroupTree();
    $( "#member-tabs").tabs();
  })
</script>