<div id="moveMemberDialogText">
  <h1>{t}Move member{/t}</h1>
  <p id="DialogStatus">{t}Fields marked with * are mandatory{/t}</p>
  <form id="DialogForm" name="moveMemberForm" method="post" action="">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tbody><tr >
          <th>&nbsp;</th>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <th>{t}Current address{/t}</th>
          <td><input name="currentMemberFamilyname" type="text" id="currentMemberFamilyname" style="width:300px" class="text ui-widget-content ui-corner-all" disabled="disabled" readonly="" value="[+VALUE.ADR_familyname+]"></td>
        </tr>
        <tr>
          <th>{t}New address{/t}</th>
          <td>
            <input name="moveMemberFamilyname" type="text" id="moveMemberFamilyname" style="width:300px" class="text ui-widget-content ui-corner-all" onClick="this.select(); $('#moveMemberFamilyname').autocomplete('search');"  />
            <input name="moveMemberToAddressId" type="text" style="display: none" id="moveMemberToAddressId" />
            <div id="searchresult" style="position:relative"></div>
          </td>
        </tr>
        <tr style="height:130px">
          <th>&nbsp;</th>
          <td></td>
        </tr>
        <tr>
          <th ></th>
          <td>&nbsp;</td>
        </tr>
        <tr >
          <th>&nbsp;</th>
          <td><div id="moveMemberDialogStatus"></div></td>
        </tr>
      </tbody>
    </table>
  </form>
</div>
<script type="text/javascript">
$(document).ready(function() {

 $('#moveMemberFamilyname').autocomplete({
    source: function (request, response) {
      response([loadingItem]);

      $.ajax({
        url: "model/classes/ProcessRequest.php",
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
          term : request.term,
          action : 'livesearch',
          type : 'addresses'
        }
      }).success(response).error(function () {
        response([]);
      });
    },
    select: function (event, ui) {
      if (ui.item.loading) {
        event.preventDefault();
      } else if(ui.item.ADR_id){
        $("#moveMemberToAddressId").val(ui.item.ADR_id);
      } else {
        return false;
      }
    },
    focus: function (event, ui) {
      if (ui.item.loading) {
        event.preventDefault();
      }
    },
    autoFocus: true,
    minLength: 0,
    delay: 250,
    appendTo: $("#searchresult")
  });


});
</script>