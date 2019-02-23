<div id="SearchBox" class="ui-widget ui-widget-content ui-corner-all block-left ">
  [+TEMPLATE.SEARCHBOX+]
</div>

<div id="AddressBox" class="ui-widget ui-widget-content ui-corner-all block-right">
  <div class="ui-widget-header block-header"><h1><img src="css/images/icons/group.png" class="imgInLine" height="24" width="24" alt="{t}Groups{/t}" />&nbsp;{t}Groups{/t}</h1></div>
  <div class="block-content">
    <table width="550" border="0" cellpadding="0" cellspacing="0">

      <tr>
        <th width="180"  >{t}Select group{/t}<input name="GROUP_result" type="hidden" id="GROUP_result" value="1" /></th>
        <td>[+INPUT.GROUP_ID+]</td>
      </tr>
      <tr>
        <th>{t}Group description{/t}</th>
        <td>[+VALUE.GROUP_description+]</td>
      </tr>
    </table>
  </div>
</div>