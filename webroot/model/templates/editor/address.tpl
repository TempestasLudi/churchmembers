<div id='AddressContainer'>
  <div id='AddressMap' class=" block-left" >
    <img src="https://maps.googleapis.com/maps/api/staticmap?center=[+VALUE.ADR_lat+],[+VALUE.ADR_lng+]&zoom=16&size=300x190&sensor=true&scale=2&markers=color:blue|[+VALUE.ADR_lat+],[+VALUE.ADR_lng+]" alt="" height="190" width="300"/>
  </div>

  <div id='AddressDetails' class="ui-widget ui-widget-content ui-corner-all block-right">

    <div id='AddressHeader' class="ui-widget-header block-header">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td><h1>[+VALUE.FAMILYNAME+]&nbsp;[+BUTTONS.ADDRESS+]</h1></td>
          <td width="55"  align="right"  style="vertical-align:bottom;">
            <button id="ADR_prev" class="smallbutton" style="margin:0;">{t}Previous{/t}</button>
            <button id="ADR_next" class="smallbutton" style="margin:0;">{t}Next{/t}</button>
          </td>
        </tr>
      </table>
    </div>

    <div id='AddressContent' class="block-content">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <th width="250" colspan="2"><h2>{t}Address data{/t}</h2></th>
        <th><h2>{t}Member of{/t}</h2></th>
        </tr>
        <tr>
          <td width="30"><img src="css/images/icons/house_go.png" height="24" width="24" alt="{t}Address data{/t}" /></td>
          <td><input name="ADRRESS_result" type="hidden" id="ADRRESS_result" VALUE="1" />
            <a onclick="[+VALUE.ADR_COORDINATES+]">
              [+VALUE.ADR_street_extra_template+][+VALUE.ADR_street+]&nbsp;[+VALUE.ADR_number+]<br />
              [+VALUE.ADR_zip+]&nbsp;&nbsp;[+VALUE.ADR_city+][+VALUE.ADR_country_template+]
            </a>
          <td rowspan='3'><span class='list-of-groups'>[+VALUE.ADR_GROUPS+]<button id="buttonNewAddressGroupDialog" title="{t}Change{/t}">{t}Change{/t}</button></span></td>
        </tr>
        <tr>
          <td><img src="css/images/icons/telephone.png" height="24" width="24" alt="{t}Telephone{/t}" /></td>
          <td>[+VALUE.ADR_telephone+] </td>
        </tr>
        </tr>
        <tr>
          <td><img src="css/images/icons/mail.png" height="24" width="24" alt="{t}E-mail{/t}" /></td>
          <td>[+VALUE.ADR_email+] </td>
        </tr>
      </table>
    </div>

    <div id='MemberDetails'>
      <div id='MembersList' class='memberslist'>
        <button name="MEMBERSLIST_button_overview">{t}Overview{/t}</button>
        [+VALUE.MEMBERSLIST+]
      </div>
      <div id='MembersContent'>
        <div class="ui-widget-header block-header"><h1>{t}Members{/t}</h1></div>
        <div id="StartContent" class="block-content">
          <div id="MemberGrid">
            [+VALUE.MEMBERSPHOTOS+]
            [+BUTTONS.MEMBERSLIST+]
          </div>
        </div>
        <div style="clear:both"></div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {

    $('button[name="MEMBERS_button"]').button();
    $('button[name="GROUPS_button"]').button();
    $('button[name="MEMBERSLIST_button"]').button().click(function(){
      getMember($(this).attr("id").replace("member_", ""));
    });

    $('#buttonNewAddressGroupDialog').button({
      icons: {
        primary:'ui-icon-gear'
      },
      text:false
    }).click(function(){
      getDialog('group','address');
    });


    $('button[name="MEMBERSLIST_button_overview"]').button().click(function(){
      getAddress();
    });

    $('#ADR_prev').button({
      icons: {primary: "ui-icon-circle-triangle-w"},text: false
    }).click(function(){
      getAddress(false, false, "PREV_ADR");
    })
    $('#ADR_next').button({
      icons: {primary: "ui-icon-circle-triangle-e"},text: false
    }).click(function(){
      getAddress(false, false, "NEXT_ADR");
    })

    loadEditButtons();

    $("#MembersList").sortable({
      axis: "y",
      items: '> div',
      distance: 10,
      placeholder: 'ui-state-highlight',
      forcePlaceholderSize: true,
      containment: 'parent',
      update: function() {
        var sortresult = $("#MembersList").sortable('serialize',{
          key: 'Sortresult[]'
        });
        sorterMembers(sortresult, "#MembersList");
      }
    });

    createSplitButton("#ADR_action")

  });
</script>