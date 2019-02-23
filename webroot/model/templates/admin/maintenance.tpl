<div class="ui-widget-header block-header"><h1><img src="css/images/icons/calendar.png" class="imgInLine" height="24" width="24" alt="{t}Maintenance{/t}" />&nbsp;{t}Maintenance{/t}</h1></div>
<div class="block-content">
  <div id="progressbar"></div>
  <div id="adminstatus"></div><br/>
  <table width="100%">
    <thead>
      <tr class="ui-widget-header ">
        <th>{t}Administration{/t}</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>{t}Hier moet nog een beschrijving over de knoppen komen.{/t}</td>
      </tr>
      <tr>
        <td><button id="updateCoordsButton" class='button' title="{t}Update coordinates addresses{/t}">{t}Update coordinates addresses{/t}</button><button id="removeAddressOrphansButton" class='button' title="{t}Delete empty addresses{/t}">{t}Delete empty addresses{/t}</button><button id="removeMembersOrphansButton" class='button' title="{t}Delete members without addresses{/t}">{t}Delete members without addresses{/t}</button></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr class="ui-widget-header ">
        <th>{t}Database{/t}</th>
      </tr>
      <tr>
        <td>{t}Hier moet nog een beschrijving over de knoppen komen.{/t}</td>
      </tr>
      <tr>
        <td><button id="emptyTablesFormButton" class='button' title="{t}Truncate Tables{/t}">{t}Truncate Tables{/t}</button><button id="backupDBButton" class='button' title="{t}Create backup{/t}">{t}Create backup{/t}</button></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr class="ui-widget-header ">
        <th>{t}Import CSV{/t}</th>
      </tr>
      <tr>
        <td>{t}Hier moet nog een beschrijving over de knoppen komen.{/t}</td>
      </tr>
      <tr>
        <td>
          <div id="importcsv_div">
            <div id="file-uploader"></div>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</div>
<script type="text/javascript">
    $(document).ready(function() {
      $("#removeAddressOrphansButton").click(function(){removeOrphans('addresses', '#adminstatus')})
      $("#removeMembersOrphansButton").click(function(){removeOrphans('members', '#adminstatus')})
      $("#emptyTablesFormButton").click(function(){emptyTablesForm('#adminstatus')})
      $("#updateCoordsButton").click(function(){updateCoordinates(0,'#adminstatus'); $("#updateCoordsButton").button("disable")})

      $("#backupDBButton").click(function(){Export('backupdb','sql')})
      updateProgressbar(0);
    });

    var uploader = new qq.FileUploader({
      element: document.getElementById("file-uploader"),
      action: "includes/fileuploader/server/php.php",
      allowedExtensions: ["csv"],
      onComplete: function(id, fileName, response){
        if(response.success) {
          $.post("model/classes/ProcessRequest.php",
          {
            action: "admin",
            type: "uploadcsv",
            CSV_FILE: fileName
          },
          function(response) {
            $("#adminstatus").html(response);
          });
        }
      }
    });

    $('.qq-upload-button').button();
  </script>