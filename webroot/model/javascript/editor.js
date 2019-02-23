/**
 * Globals
 */
var olddialoghtml;

$(document).ready(function() {
  $("#ArchiveButton").button().click(function(){
    toggleArchive()
  })
})


function setEditButtons(){
  $("#buttonNewAddressDialog").click(function(){
    getDialog('address','add');
  })

  $("#buttonMoveAddressDialog").click(function(){
    getDialog('address','move');
  })

  $("#buttonUnscribeAddressDialog").click(function(){
    getDialog('address','unscribe');
  })

  $("#buttonNewMemberDialog").button().click(function(){
    getDialog('member','add');
  })

  $("#buttonMoveMemberDialog").click(function(){
    getDialog('member','move');
  })

  $("#buttonUnscribeMemberDialog").click(function(){
    getDialog('member','unscribe');
  })

  $("#buttonNewEventDialog").click(function(){
    getDialog('event','addevent');
  })

}

function loadEventLiveSearch(divId,valueId) {
  if ($(divId).val() == "") $(valueId).val("")

  $(divId).autocomplete({
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
          type : 'members'
        }
      }).success(response).error(function () {
        response([]);
      });
    },
    select: function (event, ui) {
      if (ui.item.loading) {
        event.preventDefault();
      } else if(ui.item.MEMBER_id){
        document.getElementById(valueId).value = (ui.item.MEMBER_id);
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

  if (divId == "#newEventPartnerName"){
    $("#EventAccordion").accordion({
      autoHeight: false
    });
    $('#EventAccordion >div').css('height', 'auto');
    $("h3", "#EventAccordion").click(function(e) {
      var value = $(this).find("a").attr("title");

      $.get("model/classes/ProcessRequest.php",
      {
        action: "getdata",
        type: "event",
        EVENT_id: value
      });
    });
  }
}

/**
 * Displays a dialog for adding new members to the database.
 */
function getDialog(dialogname, action){
  //  Check if there is an address available to add a new member to.
  if (document.getElementById("ADRRESS_result") != null) {
    var buttons = {};

    buttons[labelCancel] = function() {
      $("#DialogDiv").html(olddialoghtml);
      $("#DialogDiv").dialog("destroy");
    };

    buttons[labelNext] = function() {
      updateDialog(dialogname, action);
    };

    $("#DialogDiv").load('model/classes/ProcessRequest.php?action=dialog&dialog=' + dialogname + '&type=' + action).dialog({
      bgiframe: true,
      autoOpen: true,
      height: "auto",
      width: 620,
      position: ['center',100],
      modal: true,
      buttons: buttons,
      close: function() {
        $("#DialogDiv").html(olddialoghtml);
        $("#DialogDiv").dialog("destroy");
      }
    }).height("auto");
  } else {
    OpenStatusDialog(errorAddMember);
  }
}

function updateDialog(dialogname, action){
  var closeButton = {};
  var buttons = {};

  closeButton[labelClose] = function() {
    $("#DialogDiv").html(olddialoghtml);
    $("#DialogDiv").dialog("destroy");
  };

  var formdata = $("#DialogForm").serialize();
  if ($("#newEventForm").length > 0){
    formdata += '&' + $("#newEventForm").serialize();
  }

  $.ajax({
    type: "POST",
    url: "model/classes/ProcessRequest.php?action=dialog&check=1&" + formdata  + '&dialog=' + dialogname + '&type=' + action,
    dataType: "json",
    cache: false,
    success: function(data) {
      $(".ui-state-error").removeClass("ui-state-error");

      if (data.result == 'fail'){
        if (data.error.field != ''){
          $("#"+data.error.field).addClass("ui-state-error");
        }
        $("#DialogStatus").text(data.error.msg).animate({
          backgroundColor: "#CD0A0A",
          color: "#fff"
        }, 500);
      } else if (data.result == 'succes'){
        $("#DialogDiv").text(data.msg);

        if (data.dialog == ''){
          getAddress(data.ADR_id, data.MEMBER_id);
          $("#DialogDiv").dialog({
            buttons: closeButton
          });
        } else {
          getAddress(data.ADR_id, data.MEMBER_id);
          $("#DialogDiv").load('model/classes/ProcessRequest.php?action=dialog&dialog=' + data.dialog + '&type=' + data.action).dialog();
          buttons[labelCancel] = function() {
            $("#DialogDiv").html(olddialoghtml);
            $("#DialogDiv").dialog("destroy");
          };
          buttons[labelNext] = function() {
            updateDialog(data.dialog, data.action);
          };
          $("#DialogDiv").dialog({
            buttons: buttons
          });

        }
      }
    }
  });
}

/**
* Displays a datepicker item for easy selection of a date.
*/
function CreateCalendar(divId){

  $('#'+divId).datepicker({
    changeMonth: true,
    changeYear: true,
    dateFormat: 'dd-mm-yy',
    regional: 'nl',
    yearRange: '1900:2020',
    showButtonPanel: true,
    onClose: function() {
      $('#'+divId).datepicker("destroy");
    }
  });

  $('#'+divId).datepicker("show");
}

/**
* Updates Event Dialog
*/
function updateEventDialog(eventType) {
  if (eventType == 'EVENT_NOREASON'){
    $('#newDateTR').hide()
  } else {
    $('#newDateTR').show()
  }

  if (eventType == 'EVENT_CONFESSION' || eventType == 'EVENT_BIRTH' || eventType == 'EVENT_MARRIAGE' || eventType == 'EVENT_BAPTISED'){
    $('#newEventCityTR').show()
  } else {
    $('#newEventCityTR').hide()
  }

  if (eventType == 'EVENT_CONFESSION' || eventType == 'EVENT_MARRIAGE' || eventType == 'EVENT_BAPTISED'){
    $('#newEventChurchTR').show()
  } else {
    $('#newEventChurchTR').hide()
  }

  if (eventType == 'EVENT_CONFESSION' || eventType == 'EVENT_BIRTH' || eventType == 'EVENT_MARRIAGE' || eventType == 'EVENT_BAPTISED' || eventType == 'EVENT_NOREASON'){
    $('#newEventNoteTR').hide()
  } else {
    $('#newEventNoteTR').show()
  }

  if (eventType == 'EVENT_CONFESSION' || eventType == 'EVENT_BAPTISED'){
    // update membertype in event dialog with current member membertype
    $('#newMembertype').val($("#MEMBER_membertype_id_checkbox :radio:checked").val())
    $('#newMembertypeTR').show()

  } else {
    $('#newMembertypeTR').hide()
  }

  if (eventType == 'EVENT_DIVORCE' || eventType == 'EVENT_MARRIAGE'){
    $('#newEventPartnerTR').show()
  } else {
    $('#newEventPartnerTR').hide()
  }
}

/**
* Creates a splitbutton
*/
function createSplitButton(element){
  $(element).button({
    text: false,
    icons: {
      primary: "ui-icon-circle-triangle-s"
    }
  })
  .click(function() {
    var menu = $( this ).parent().next().show().position({
      my: "left top",
      at: "left bottom",
      of: this
    });
    $( document ).one( "click", function() {
      menu.hide();
    });
    return false;
  })
  .parent()
  .buttonset()
  .next()
  .hide()
  .menu();
}
