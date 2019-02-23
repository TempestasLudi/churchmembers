<div class="ui-widget-header block-header"><h1><img src="css/images/icons/edit_male_user.png" class="imgInLine" height="24" width="24" alt="{t}Userrights{/t}" />&nbsp;{t}Userrights{/t}</h1></div>
<div class="block-content">
  <form id="USERTYPE_form" action="">
    <table width="100%">
      <tbody>
        <tr>
          <th width="150"><label>{t}Select{/t} {t}Role{/t}</label></th>
          <td colspan="3">[+INPUT.USERTYPELIST+]</td>
        </tr>
        <tr>
          <th valign="top"><label id="LABEL_USERTYPE_description">{t}Description{/t}</label></th>
          <td colspan="3"><textarea name="USERTYPE_description" rows="3" id="USERTYPE_description" onchange="editDetails('usertypes',this, '#LABEL_USERTYPE_description')" style="width:350px;" cols="3">[+VALUE.USERTYPE_description+]</textarea></td>
        </tr>
        <tr class="ui-widget-header ">
          <th>{t}Userrights{/t}</th><th>{t}Value{/t}</th>
          <th width="150">{t}Userrights{/t}</th>
          <th>{t}Value{/t}</th>
        </tr>
        <tr>
          <th><label id="LABEL_view_address">{t}Show addresses{/t}</label></th>
          <th>[+INPUT.view_address+]</th>
          <th><label id="LABEL_edit_mode">{t}Edit mode{/t}</label></th>
          <th>[+INPUT.edit_mode+]</th>
        </tr>
        <tr>
          <th><label id="LABEL_view_groups">{t}Show groups{/t}</label></th>
          <th>[+INPUT.view_groups+]</th>
          <th><label id="LABEL_add_address">{t}Add addresses{/t}</label></th>
          <th>[+INPUT.add_address+]</th>
        </tr>
        <tr>
          <th><label id="LABEL_view_mutations">{t}Show mutations{/t}</label></th>
          <th>[+INPUT.view_mutations+]</th>
          <th><label id="LABEL_add_group">{t}Add groups{/t}</label></th>
          <th>[+INPUT.add_group+]</th>
        </tr>
        <tr>
          <th><label id="LABEL_view_report">{t}Create reports{/t}</label></th>
          <th>[+INPUT.view_report+]</th>
          <th><label id="LABEL_add_member">{t}Add members{/t}</label></th>
          <th>[+INPUT.add_member+]</th>
        </tr>
        <tr>
          <th><label id="LABEL_view_map">{t}Show map{/t}</label></th>
          <th>[+INPUT.view_map+]</th>
          <th><label id="LABEL_add_relation">{t}Add relations{/t}</label></th>
          <th>[+INPUT.add_relation+]</th>
        </tr>
        <tr>
          <th><label id="LABEL_view_archive">{t}View archive{/t}</label></th>
          <th>[+INPUT.view_archive+]</th>
          <th><label id="LABEL_add_data">{t}Add data{/t}</label></th>
          <th>[+INPUT.add_data+]</th>
        </tr>
        <tr>
          <th><label id="LABEL_view_admin">{t}Show administration{/t}</label></th>
          <th>[+INPUT.view_admin+]</th>
          <th><label id="LABEL_sort_members">{t}Sort members{/t}</label></th>
          <th>[+INPUT.sort_members+]</th>
        </tr>
        <tr>
          <th>&nbsp;</th>
          <th>&nbsp;</th>
          <th><label id="LABEL_delete_data">{t}Delete data{/t}</label></th>
          <th>[+INPUT.delete_data+]</th>
        </tr>
      </tbody>
    </table>
  </form>
</div>