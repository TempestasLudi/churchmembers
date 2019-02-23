/**
 * Globals
 */
var ReportResults = null;

/**
 * Jquery Elements
 */
$(document).ready(function() {
  $("#HomeButton, #AddressButton, #GroupsButton, #ReportButton, #MapsButton, #ExportButton, #LogoutButton").button();
  $("#HomeButton").click(function(){
    getHome()
  })
  $("#AddressButton").click(function(){
    getAddress()
  })
  $("#GroupsButton").click(function(){
    getGroup()
  })
  $("#ReportButton").click(function(){
    getReport()
  })
  $("#MapsButton").click(function(){
    getMaps()
  })
  $("#ExportButton").click(function(){
    getExport()
  })
  $("#LogoutButton").click(function(){
    document.location.href = '?logout=logout'
  })

  //Smoothscroll goto Top
  $('#top-link').click(function(e) {
    $('html,body').animate({
      scrollTop : 0
    },'slow');
  });

  setFixedItems();

  $.ajaxSetup({
    complete:function(xhrObj){
      parseScript(xhrObj.responseText);
      if (xhrObj.getResponseHeader("Errormsg")){
        alert(xhrObj.getResponseHeader("Errormsg"));
      }
      if (xhrObj.getResponseHeader("SetLocation")){
        window.location.replace(xhrObj.getResponseHeader("SetLocation"));
      }
    }
  });

  // Start
  getHome()
})

/**
 * Toggles the visability of the submenu's in the ribbon.
 */
function toggleSubMenus(toggleButton){
  $(".ActiveButton").removeClass("ui-state-focus");
  sendGooglePageView('pages/' + toggleButton);
  $("#GroupsMenu").hide();
  $("#MembersMenu").hide();

  switch(toggleButton){
    case "home":
      $("#HomeButton").addClass("ActiveButton ui-state-focus");
      break;
    case "address":
      $("#AddressButton").addClass("ActiveButton ui-state-focus");
      $("#MembersMenu").show();
      break;
    case "group":
      $("#GroupsButton").addClass("ActiveButton ui-state-focus");
      $("#GroupsMenu").show();
      break;
    case "modifications":
      $("#ModificationsButton").addClass("ActiveButton ui-state-focus");
      break;
    case "report":
      $("#ReportButton").addClass("ActiveButton ui-state-focus");
      break;
    case "maps":
      $("#MapsButton").addClass("ActiveButton ui-state-focus");
      break;
    case "export":
      $("#ExportButton").addClass("ActiveButton ui-state-focus");
      break;
    case "admin":
      $("#AdminButton").addClass("ActiveButton ui-state-focus");
      break;
    case "none":
      $(".ActiveButton").removeClass("ui-state-focus");
      break;
    default:
  }
}

function loadMembersList() {
  $('button[name="MEMBERS_button"]').button();
  $('#bottomLeftContentDiv').trigger('change');
  $("#bottomLeftContentDiv").show();
}

function loadTabs(element) {
  $(element).tabs ({});
}

/**
 * Opens a confirm dialog with the provided message in the StatusDialog div.
 */
function OpenStatusDialog(status) {
  OpenDialog("StatusDialog", status, 550);
}

/**
 * Opens the provided div as a dialog with the provided message.
 */
function OpenDialog(div, message, width) {
  var buttons = {};
  buttons[labelOk] = function() {
    $(this).dialog("close");
  };

  $("#"+div).dialog({
    height: 'auto',
    width: width,
    bgiframe: true,
    modal: true,
    buttons: buttons,
    zIndex:9999
  })

  if (message != ''){
    $("#"+div).dialog().html(message).height("auto");
  }

  $("#"+div).dialog("open");
}

function loadAddressLiveSearch(divId) {
  $('#LiveSearchCheckbox').buttonset();
  $(divId).autocomplete({
    source: "model/classes/ProcessRequest.php?action=livesearch&type=livesearchaddress&livesearchtable=" + $("input[name=LiveSearchTable]:checked").val(),
    autoFocus: true,
    minLength: 2,
    delay: 250,
    search: function(event, ui){
      $(divId).autocomplete( "option", "source", "model/classes/ProcessRequest.php?action=livesearch&type=livesearchaddress&livesearchtable=" + $("input[name=LiveSearchTable]:checked").val() );
      sendGooglePageView('search/'+ $("input[name=LiveSearchTable]:checked").val());
    },
    select: function(event, ui) {
      if(ui.item.ADR_id){
        getAddress(ui.item.ADR_id, ui.item.MEMBER_id);
      }
    }
  });
}

/**
 * Export function
 */
function Export(requestfile,filetype){
  sendGooglePageView('export/'+requestfile+'.'+filetype);
  OpenDialog("DownloadDialog", '', 550);
  var ownbuttons = {};
  $("#DownloadDialog").dialog("option", 'buttons', ownbuttons)

  $.ajax({
    type: "GET",
    url: "model/classes/ProcessRequest.php?action=export&type=exportfile&doctype="+filetype+"&requestfile="+requestfile,
    dataType: "json",
    cache: false,
    success: function(data) {
      var oldhtml = $("#DownloadDialog").html();
      $("#DownloadDialog").html(data.msg).height("auto");

      ownbuttons[labelCancel] = function() {
        $("#DownloadDialog").html(oldhtml);
        $(this).dialog("destroy");
      };
      ownbuttons[labelDownload] = function() {
        window.open (data.url,"Download");
        $("#DownloadDialog").html(oldhtml);
        $(this).dialog("destroy");
      };

      $("#DownloadDialog").dialog("option", 'buttons', ownbuttons)
    }
  });
}

/**
 * Creates a button to upload a photo, and remove photo.
 */
function createPhotoButton() {
  var uploader = new qq.FileUploader({
    element: document.getElementById("photo-uploader"),
    action: "includes/fileuploader/server/php.php",
    allowedExtensions: ["jpg","jpeg", "JPG", "png"],
    onComplete: function(id, fileName, response){
      if(response.success) {
        $.post("model/classes/ProcessRequest.php",
        {
          action: "photoupload",
          type: "photoupload",
          PHOTO_FILE: fileName
        },
        function(response) {
          getMember();
        }
        );
      }
    }
  });

  $('.qq-upload-button').button();

  if ($('#MEMBER_photo').attr('src') == "includes/phpThumb/phpThumb.php?src=../../css/images/users/user_unknown.png&w=128"){
    $("#remove_photo").remove()
  } else {
    $("#remove_photo").button()
    $('#remove_photo').show()
    $("#remove_photo").click(function(){
      deletePhoto()
    })
  }
}

/**
 * Function for Goto Top link
 */
function setFixedItems(){
  /* set variables locally for increased performance */
  var scroll_timer;
  var displayed = false;
  var $message = $('#top-link');
  $message.css({
    opacity: 0.8
  });
  var $window = $(window);
  var top = $(document.body).children(0).position().top;

  /* react to scroll event on window */
  $window.scroll(function () {
    window.clearTimeout(scroll_timer);
    scroll_timer = window.setTimeout(function () { // use a timer for performance
      if($window.scrollTop() <= top) // hide if at the top of the page
      {
        displayed = false;
        $message.fadeOut(500);
      }
      else if(displayed == false) // show if scrolling down
      {
        displayed = true;
        $message.stop(true, true).show().click(function () {
          $message.fadeOut(500);
        });
      }
    }, 100);

    $message.css({
      top:($(window).scrollTop()+$(window).height())+"px"
    })
  });
}

function sendGooglePageView(url){
  if (typeof _gaq !== "undefined" && _gaq !== null) {
    _gaq.push(['_trackPageview', labelChurchmembers + '/' + url]);
  }
}