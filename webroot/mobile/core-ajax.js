/**
 * Globals
 */
var ajaxdelay;

/**
 * Sends a request to get home page
 */
function getHome() {
  toggleSubMenus("home");
  get("getdata", "home", "topContentDiv", false, function(){
    createSliders(150);

    $('#birthdaycalendar').fullCalendar({
      theme: true,
      header: {
        left: 'prev',
        center: 'title',
        right: 'next'
      },
      defaultView: 'basicWeek',
      firstDay: 0,
      editable : false,
      height: 300,
      cache: true,
      allDayDefault: true,
      columnFormat: {
        week: 'ddd d-M'
      },
      titleFormat: {
        week: "d[ MMMM yyyy]{ '&#8212;' d MMMM yyyy}" // September 7 - 13 2009
      },
      events: {
        url: 'model/classes/ProcessRequest.php',
        type: 'POST',
        data: {
          action: 'getdata',
          type: 'calendar'
        }
      },
      eventClick: function(calEvent, jsEvent, view) {
        getAddress(calEvent.ADRid,calEvent.MEMBERid);
      },
      eventRender: function(event, element) {
        element.find('span.fc-event-title').html(element.find('span.fc-event-title').text());
      },
      monthNames: ['januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli',
      'augustus', 'september', 'oktober', 'november', 'december'],
      dayNamesShort :['Zo', 'Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za']
    });

  });
  $("#bottomLeftContentDiv").hide();
  $("#bottomRightContentDiv").hide();

}

/**
 * Sends a request to get address info
 */
function getAddress(ADR_id, MEMBER_id, navigate_str) {
  ADR_id = ADR_id || false;
  MEMBER_id = MEMBER_id || false;
  navigate_str = navigate_str || false;

  toggleSubMenus("address");

  var addressId = {
    ADR_id: ADR_id,
    MEMBER_id: MEMBER_id,
    navigate_str: navigate_str
  };

  get("getdata", "address", "topContentDiv",
    addressId,
    function(){
      if($("#ADRRESS_result").val() != undefined) {
        getMembersList();
        getMember();

        $('#ADR_prev').button({
          icons: {
            primary: "ui-icon-circle-triangle-w"
          },
          text: false
        }).click(function(){
          getAddress(false, false, "PREV_ADR");
        })
        $('#ADR_next').button({
          icons: {
            primary: "ui-icon-circle-triangle-e"
          },
          text: false
        }).click(function(){
          getAddress(false, false, "NEXT_ADR");
        })
      } else {
        $("#bottomContentDiv").html("");
      }
    });
}

/**
 * Sends a request to get group info.
 */
function getGroup(id) {
  id = id || false;
  toggleSubMenus("group");

  var groupId = {
    GROUP_id: id
  };

  get("getdata", "group", "topContentDiv",
    groupId,
    function(){
      if($("#GROUP_result").val() != undefined) {
        getMembersList();
        getMember();

        createSliders(40);
      } else {
        $("#bottomContentDiv").html("");
      }
    });
}

/**
 * Sends a request to get export page.
 */
function getExport() {
  toggleSubMenus("export");
  $("#bottomLeftContentDiv").hide();
  $("#bottomRightContentDiv").hide();

  get("export", "template", "topContentDiv",
    false ,
    function(){


      if($("#GROUP_result").val() != undefined) {

      } else {

    }
    });
}

/**
 * Sends a request to fetch a list of members belonging to an address or group.
 */
function getMembersList() {

  get("getdata", "members", "bottomLeftContentDiv",
    false,
    function(){
      loadMembersList();
    });
}

/**
 * Sends a request to fetch detailed information about a member.
 */
function getMember(id,callback) {
  var memberId = false;
  if (id){
    memberId = {
      id: id
    };
  }
  get("getdata", "member", "bottomRightContentDiv",
    memberId,
    function(){
      $('#bottomRightContentDiv').trigger('change');
      $("#bottomRightContentDiv").show();
      $('a.colorbox').colorbox();
      if(callback) {
        callback();
      }

    });
}

/**
 * Sends a get request to the server and fills the target with the response. The
 * callback function is executed after the request has returned.
 */
function get(action, type, target, parameters, callback) {

  if (target != "Events"){
    clearTimeout(ajaxdelay);
    $('#content').css({
      opacity: 0.2
    })
    $('#loading').show();
  }

  var requestParameters =
  $.param({
    action: action,
    type: type
  });
  if(parameters) {
    requestParameters += "&" + $.param(parameters);
  }

  $.get(
    "model/classes/ProcessRequest.php",
    requestParameters,
    function(response) {
      $("#"+target).html(parseScript(response));

      ajaxdelay = setTimeout("$('#loading').hide();$('#content').animate({opacity: 1},'fast')",250);

      if(callback) {
        callback();
      }

    });
}

/**
 * Sends a get request and opens the target as a jQuery dialog filled with the response.
 */
function getInDialog(action, type, target, id, width, height,title) {
  var buttons = {};
  buttons[labelOk] = function() {
    $(this).dialog("close");
  };

  get(action, type, target,
  {
    id: id
  },
  function() {
    height = height ? height : 'auto'
    width = width ? width : 'auto'
    $("#" + target).dialog({
      height: height,
      width: width,
      minWidth: 200,
      minHeight: 200,
      bgiframe: true,
      modal: true,
      buttons: buttons,
      title:title,
      zIndex:500,
      close: function(event, ui) {
        $(this).dialog("destroy")
      }
    });
  });
}

/**
 * Run error scripts in Ajax response, and return rest of the response.
 */
function parseScript(_source) {
  var source = _source;
  var scripts = new Array();

  // Strip out tags
  while(source.indexOf("[ERROR") > -1 || source.indexOf("[/ERROR") > -1) {
    var s = source.indexOf("[ERROR");
    var s_e = source.indexOf("]", s);
    var e = source.indexOf("[/ERROR", s);
    var e_e = source.indexOf("]", e);

    // Add to scripts array
    scripts.push(source.substring(s_e+1, e));
    // Strip from source
    source = source.substring(0, s) + source.substring(e_e+1);
  }

  // Loop through every script collected and eval it
  for(var i=0; i<scripts.length; i++) {
    try {
      eval(scripts[i]);
      sendGooglePageView('jserror(executed)');
    }
    catch(ex) {
      // do what you want here when a script fails
      sendGooglePageView('jserror');
    }
  }

  // Return the cleaned source
  return source;
}