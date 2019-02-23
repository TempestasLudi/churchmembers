<div class="ui-widget-header block-header"><h1><img src="css/images/icons/group.png" class="imgInLine" height="24" width="24" alt="{t}Groups{/t}" />&nbsp;[+VALUE.GROUP_name+]</h1></div>
<div class="block-content">
  [+VALUE.GROUP_description+]<input name="GROUP_result" type="hidden" id="GROUP_result" value="1" />
  [+VALUE.CONTAIN_GROUPS+]
  <br />
  <div id="accordion">[+VALUE.MEMBERSANDADDRESSES+]</div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $('button[name="MEMBERS_button"]').button();
    $('button[name="ADDRESS_button"]').button();
    $('button[name="GROUPS_button"]').button();

    $( "#accordion" ).accordion();
  });
</script>