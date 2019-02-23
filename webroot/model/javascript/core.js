/**
 * Globals
 */
var mapinitialized = false;
var sliderHeight = "150px";
var loadingItem = {
  label: labelSearch,
  loading: true
};
var olddialoghtml;
var oldsearchtext;
var archive = false;

/**
 * Jquery Elements
 */
$(document).ready(function() {
  olddialoghtml = $("#DialogDiv").html();
  oldsearchtext = $("#SearchInput").val();

  // If javascript is enabled hide error
  $("#hasjs").addClass("hasjs");

  $("#HomeButton, #AddressButton, #GroupsButton, #ReportButton, #MapsButton, #ListsButton, #PhotoButton, #EmailButton, #LogoutButton, #AdvancedSearch").button();

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
  $("#ListsButton").click(function(){
    getLists()
  })
  $("#PhotoButton").click(function(){
    getPhotobook()
  })
  $("#EmailButton").click(function(){
    getEmail()
  })
  $("#LogoutButton").click(function(){
    document.location.href = '?logout=logout'
  })

  $("#AdvancedSearch").click(function(){
    getReport();
  })

  $("#SearchSubmit").button({
    icons: {
      primary:'ui-icon-circle-zoomin'
    },
    text: false
  }).click(function(){
    $('#SearchInput').autocomplete('search');
  })

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

  $('#SearchInput').autocomplete({
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
          type : 'both'
        }
      }).success(response).error(function () {
        response([]);
      });
    },
    select: function (event, ui) {
      if (ui.item.loading) {
        event.preventDefault();
      } else if(ui.item.ADR_id){
        getAddress(ui.item.ADR_id, ui.item.MEMBER_id);
        $(this).val(oldsearchtext);
        return false;
      }
    },
    focus: function (event, ui) {
      if (ui.item.loading) {
        event.preventDefault();
      }
    },
    autoFocus: true,
    minLength: 2,
    delay: 250
  });

  // Start
  getHome()
})

/**
 * Toggles the visability of the submenu's in the ribbon.
 */
function toggleSubMenus(toggleButton){

  // Destoy Gmap before moving to new page (IE errors)
  if (mapinitialized){
    destroyMap();
  }

  $(".ActiveButton").removeClass("ui-state-focus");
  $(".ActiveButton").removeClass("ActiveButton");
  sendGooglePageView('pages/' + toggleButton);

  switch(toggleButton){
    case "home":
      $("#HomeButton").addClass("ActiveButton ui-state-focus");
      break;

    case "address":
      $("#AddressButton").addClass("ActiveButton ui-state-focus");
      break;

    case "group":
      $("#GroupsButton").addClass("ActiveButton ui-state-focus");
      break;

    case "report":
      $("#ReportButton").addClass("ActiveButton ui-state-focus");
      break;

    case "maps":
      $("#MapsButton").addClass("ActiveButton ui-state-focus");
      break;

    case "lists":
      $("#ListsButton").addClass("ActiveButton ui-state-focus");
      break;

    case "photobook":
      $("#PhotoButton").addClass("ActiveButton ui-state-focus");
      break;

    case "email":
      $("#EmailButton").addClass("ActiveButton ui-state-focus");
      break;

    case "admin":
      $("#AdminButton").addClass("ActiveButton ui-state-focus");
      break;
  }
}


function loadEditButtons(){
  if($.isFunction(window.setEditButtons)){
    setEditButtons();
  }
}

function loadHTMLEditor() {
  if ($('#MEMBER_introduction').length){
    $('#MEMBER_introduction').tinymce({
      // Location of TinyMCE script
      script_url : 'includes/tiny_mce/tiny_mce.js',
      theme : "advanced",
      language : "nl",
      plugins : "autolink,lists,pagebreak,save,noneditable,visualchars,tabfocus",
      valid_elements : "a[href|target=_blank],strong/b,div[align],br,em/i,u",
      theme_advanced_buttons1 : "bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist",
      theme_advanced_buttons2 : "undo,redo,link,unlink,separator,save",
      theme_advanced_buttons3 : "",
      theme_advanced_toolbar_location : "top",
      theme_advanced_toolbar_align : "left",
      theme_advanced_statusbar_location : "bottom",
      theme_advanced_resizing : true,
      theme_advanced_path : false,
      paste_auto_cleanup_on_paste : true,
      paste_remove_styles: true,
      paste_remove_styles_if_webkit: true,
      paste_strip_class_attributes: true,
      save_onsavecallback : "editIntroduction",
      save_enablewhendirty : false
    });
  }
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

/**
 * Export function
 */
function Export(requestfile,filetype, parameters){
  parameters = parameters || '';

  sendGooglePageView('export/'+requestfile+'.'+filetype);
  OpenDialog("DownloadDialog", '', 550);
  var ownbuttons = {};
  $("#DownloadDialog").dialog("option", 'buttons', ownbuttons)

  $.ajax({
    type: "GET",
    url: "model/classes/ProcessRequest.php?action=export&type=exportfile&doctype="+filetype+"&requestfile="+requestfile + parameters,
    dataType: "json",
    cache: false,
    success: function(data) {
      var oldhtml = $("#DownloadDialog").html();
      $("#DownloadText").html(data.msg).height("auto");
      $("#DownloadLink").val(data.url);
      $("#DownloadFile").show();

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

function addReceiver(type, id, text){
  if (type == 'group'){
    $('#Receiverslist_groups').append('<li rel="'+type+'" id="'+id+'"><button class="receivers_groups_button">'+text+'</button></li>')
  } else if (type == 'member'){
    $('#Receiverslist_members').append('<li rel="'+type+'" onclick="$(this).remove()" id="'+id+'"><button class="receivers_button">'+text+'</button></li>')
  }

  $('.receivers_groups_button').button({
    icons: {
      primary: "ui-icon-bullet"
    }
  })

  $('.receivers_button').button({
    icons: {
      primary: "ui-icon-circle-close"
    }
  })
}

function sendMail(){
  var tag = $("#DialogDiv");
  var receivers = getReceivers();
  var buttons = {};

  buttons[labelCancel] = function() {
    $("#DialogDiv").html(olddialoghtml);
    $(this).dialog('destroy');
  };

  $.ajax({
    type: "POST",
    url: "model/classes/ProcessRequest.php?action=email&type=preview&"+receivers,
    data: {
      message:  $('#MAIL_message').tinymce().getContent({
        format : 'raw'
      }),
      subject: $('#MAIL_subject').val()
    },
    success: function(data) {
      datamsg = data.msg
      height = 450;
      width = 900;
      if (data.result == 'fail'){
      height = 200;
      width = 300;
      } else if (data.result == 'succes'){
        buttons[labelSend] = function() {
          $(".ui-dialog-buttonpane button").button("disable");
          $("#DialogDiv").html(olddialoghtml);
          $.ajax({
            type: "POST",
            url: "model/classes/ProcessRequest.php?action=email&type=sendemail",
            success: function(data) {
              tag.dialog( "destroy" );
              tag.html(data).dialog({
                modal: true,
                title: labelSendMail,
                 width : 300,
                height : 150,
                buttons: {
                  Ok: function() {
                    $("#DialogDiv").html(olddialoghtml);
                    $( this ).dialog( "destroy" );
                    getEmail();
                  }
                }
              })
            }
          });
        };
      }
      tag.html(datamsg).dialog({
        bgiframe: true,
        autoOpen: true,
        height: height,
        width: width,
        position: ['center',50],
        modal: true,
        buttons: buttons,
        title: labelPreviewMail,
        close: function() {
          $("#DialogDiv").html(olddialoghtml);
          $(this).dialog('destroy');
        }
      });
    }
  });
}

function getReceivers(){
  var str = '';
  var text = '';

  $("ul#Receiverslist_groups, ul#Receiverslist_members").children().each(function(i) {
    var li = $(this);
    str += 'receivers['+li.attr('rel')+'][]=' + li.attr('id') + '&';
    text += 'receiverstext[]=' + li.text() + '&'
  });

  return str + text;
}
/**
 * Creates a button to upload a photo, and remove photo.
 */
function createPhotoButton() {
  var uploader = new qq.FileUploader({
    element: document.getElementById("photo-uploader"),
    action: "includes/fileuploader/server/php.php",
    allowedExtensions: ["jpg","jpeg", "JPG", "png"],
    sizeLimit: 0, // max size
    debug: false,
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
        )
      }
    }
  });

  if ($('#MEMBER_photo').attr('src') == "includes/phpThumb/phpThumb.php?src=../../css/images/users/user_unknown.png&f=png&w=128"){
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
 * Sends a request to delete the photo..
 */
function deletePhoto() {
  $.post("model/classes/ProcessRequest.php",   {
    type: "deletephoto",
    action: "editdata",
    table: "members",
    field: "MEMBER_photo",
    value: ""
  },
  function(response){
    parseScript(response)
    getMember();
  });
}

function createSliders(height){
  sliderHeight = height + "px";
  $('.readmore').each(function () {
    var current = $(this);
    current.attr("box_h", current.height() + 10);
    if (current.height()< sliderHeight) {
      return true; // next iteration
    }
    current.css("height", sliderHeight);

    var $newdiv = $('<div class="ui-widget-header readmore_link"/>');
    current.after($newdiv);

    $newdiv.html('<span class="ui-icon ui-icon-triangle-1-s" style="float:left;margin:0 7px 7px 0;"></span><a>' + labelReadMore + '</a>');
    $newdiv.find('a').click(function() {
      openSlider(current);
    })
  });
}

function openSlider(Slider){
  var open_height = Slider.attr("box_h") + "px";
  Slider.animate({
    "height": open_height
  }, {
    duration: "slow"
  });
  var $readmore_link = Slider.next();
  $readmore_link.html('<span class="ui-icon ui-icon-triangle-1-n" style="float:left;margin:0 7px 7px 0;"></span><a>' + labelClose + '</a>');
  $readmore_link.find('a').click(function() {
    closeSlider(Slider)
  })
}

function closeSlider(Slider){
  Slider.animate({
    "height": sliderHeight
  }, {
    duration: "slow"
  });

  var $readmore_link = Slider.next();
  $readmore_link.html('<span class="ui-icon ui-icon-triangle-1-s" style="float:left;margin:0 7px 7px 0;"></span><a>' + labelReadMore + '</a>');
  $readmore_link.find('a').click(function() {
    openSlider(Slider)
  })
}

function sendGooglePageView(url){
  if (typeof _gaq !== "undefined" && _gaq !== null) {
    _gaq.push(['_trackPageview', labelChurchmembers + '/' + url]);
    _gaq.push(['_trackEvent', labelChurchmembers, url]);
  }
}

/*
 * jQuery UI Autocomplete Select First Extension
 *
 * Copyright 2010, Scott González (http://scottgonzalez.com)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * http://github.com/scottgonzalez/jquery-ui-extensions
 */
(function($) {

  $( ".ui-autocomplete-input" ).live( "autocompleteopen", function() {
    var autocomplete = $( this ).data( "autocomplete" ),
    menu = autocomplete.menu;

    if ( !autocomplete.options.selectFirst ) {
      return;
    }

    menu.activate( $.Event({
      type: "mouseenter"
    }), menu.element.children().first() );
  });

}( jQuery ));