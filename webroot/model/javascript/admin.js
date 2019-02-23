// JavaScript document containing functions for admin interface functionality.
$(document).ready(function() {
  $("#AdminButton").button();

  /**
  * Shows and displays the admin overlay.
   */
  $("#AdminButton").click(function(){

    toggleSubMenus("admin");
    get("admin", "template", "ContentDiv", {
      template: "adminmenu"}, function(){
        getAdminContent("settings");
      }
    );

  })
});

/**
 * Sends a request to fetch detailed information about a member.
 */
function getAdminContent(content, id, callback) {
  id = id || false
  parameters = {
    template: content,
    id: id
  }
  get("admin", "template", "SettingsContent",
    parameters,
    function(){
      $(".button").button();
      if(callback) {
        callback();
      }
    });
}

/**
 * Deletes a log entry for the failed login attempt.
 */
function deleteFailedLogin(id) {
  parameters = {
    edit: "delete",
    id: id
  }
  get("admin", "failedlogin", 'SettingsContent', parameters)
}

function setUserTypeById(formId,responseDiv) {
  var json = '{"blank":0';
  $(formId + ' [name*=RIGHT]').each(function(index){
    ($(this).attr('checked')) ? val = 1 : val = 0

    json = json + ',"' + $(this).attr('id') + '"';
    json = json + ':' + val;
  })
  json = json + "}";

  adminPost("usertypes", responseDiv, "setuserrights&json=" + json);
}

function removeOrphans(table, responseDiv) {
  adminPost("emptyorphans", responseDiv, "table=" + table);
}

function emptyTablesForm(responseDiv) {
  adminPost("emptytables", responseDiv);
}

function emptyTables(responseDiv) {
  strtables = $("#EMPTYTABLES_form input:checkbox").serialize();
  adminPost("emptytables", responseDiv, strtables);
}

function updateCoordinates(start,responseDiv) {
  adminPost("updatecoordinates", responseDiv, "start="+start);
}

function updateProgressbar(completed){
  $('#progressbar').progressbar({
    value: completed
  });
}

function backupDB(responseDiv) {
  postForm('admin', 'backupdb', responseDiv, 'EMPTYTABLES_form');
}


function adminPost(type, responseDiv, extraParameters) {
  $.post("model/classes/ProcessRequest.php",
    "&action=admin&type=" + type + "&" + extraParameters,
    function(response) {
      $(responseDiv).html(parseScript(response));
    });
}