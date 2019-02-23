<div class="ui-widget-header block-header"><h1><img src="css/images/icons/group.png" class="imgInLine" height="24" width="24" alt="{t}Groups{/t}" />&nbsp;[+VALUE.GROUP_name+]</h1></div>
<div class="block-content">
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td>[+VALUE.GROUP_description+]<input name="GROUP_result" type="hidden" id="GROUP_result" value="1" /></td>
    </tr>
    <tr>
      <td><span class='textbuttons'>[+VALUE.CONTAIN_GROUPS+]</span></td>
    </tr>
  </table>
  <br />
  <div id="accordion">
    <h1><strong>{t}Settings{/t}</strong></h1>
    <div>
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tbody id="GROUP_details_tr">
          <tr>
            <th><label id="LABEL_GROUP_description" for="GROUP_description">{t}Group description{/t}</label></th>
            <td><textarea name="GROUP_description" rows="3" id="GROUP_description" onchange="editDetails('groups',this, '#LABEL_GROUP_description')" style="width:90%; height:50px" >[+VALUE.GROUP_description+]</textarea></td>
          </tr>
          <tr>
            <th width="270"><label id="LABEL_GROUP_options" for="LABEL_GROUP_options">{t}This group can contain{/t}</label></th>
            <td ><div id="GROUP_options">[+INPUT.GROUP_type+]</div></td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr class="ui-widget-header ">
            <th>{t}Display settings{/t}</th>
            <td></td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <th><label id="LABEL_GROUP_abbreviation" for="GROUP_abbreviation">{t}Abbreviation{/t}</label></th>
            <td><input name="GROUP_abbreviation" id="GROUP_abbreviation" onchange="editDetails('groups',this, '#LABEL_GROUP_abbreviation')" style="width:60px;" value="[+VALUE.GROUP_abbreviation+]" maxlength="10"></td>
          </tr>
          <tr>
            <th><label id="LABEL_GROUP_inyearbook" for="GROUP_inyearbook">{t}Show group abbreviation in address list{/t}</label></th>
            <td>[+INPUT.GROUP_inyearbook+] </td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr class="ui-widget-header ">
            <th>{t}Map settings{/t}</th>
            <td></td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <th><label id="LABEL_GROUP_onmap" for="GROUP_onmap">{t}This group is visable on the map{/t}</label></th>
            <td>[+INPUT.GROUP_onmap+]</td>
          </tr>
          <tr>
            <th><label id="LABEL_GROUP_marker" for="GROUP_marker">{t}Marker image{/t}</label></th>
            <td><input name="GROUP_marker" id="GROUP_marker" onchange="editDetails('groups',this, '#LABEL_GROUP_marker')" style="width:90%;" value="[+VALUE.GROUP_marker+]"><br />
              <img src="[+VALUE.GROUP_marker+]" alt="{t}Marker image{/t}" class="imgInLine" /></td>
          </tr>
          <tr>
            <th>&nbsp;</th>
            <td>{t}(default is set to css/images/googlemaps/marker1.png){/t}</td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
        </tbody>
      </table>
    </div>
    [+VALUE.MEMBERSANDADDRESSES+]
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
  $('button[name="MEMBERS_button"]').button();
  $('button[name="ADDRESS_button"]').button();
  $('button[name="GROUPS_button"]').button();

  $( "#accordion" ).accordion({heightStyle: "content",active: 1 });
  $("#GROUP_options").buttonset();
});
</script>