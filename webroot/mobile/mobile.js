/**
 * Globals
 */
var ajaxdelay;

/**
 * Sends a request to get home page
 */
function getHome() {
  get("getdata", "home", "ContentDiv", false, function(){
    $('#MembersDiv').html('');
    $('#MemberDiv').html('');
    $('#searchdiv').show()
    loadAddressLiveSearch('#search-index');
    renderPage();
  });
}

/**
 * Sends a request to get address info
 */
function getAddress(ADR_id, MEMBER_id, navigate_str) {
  $('#searchdiv').show()

  ADR_id = ADR_id || false;
  MEMBER_id = MEMBER_id || false;
  navigate_str = navigate_str || false;

  var addressId = {
    ADR_id: ADR_id,
    MEMBER_id: MEMBER_id,
    navigate_str: navigate_str
  };

  get("getdata", "address", "ContentDiv", addressId, function(){
    getMembersList();
    renderPage();
    $("#addresslist").swiperight(function() {
    getAddress(false, false, 'PREV_ADR');
    });
    $("#addresslist").swipeleft(function() {
    getAddress(false, false, 'NEXT_ADR');
    });
  });
}

/**
 * Sends a request to fetch a list of members belonging to an address or group.
 */
function getMembersList() {
  $('#searchdiv').show()
  $('#MemberDiv').html('');
  $('#MembersDiv').html('');
  get("getdata", "members", "MembersDiv", false, function(){
    renderPage();
  });
}

/**
 * Sends a request to fetch detailed information about a member.
 */
function getMember(id) {
  var memberId = false;
  if (id){
    memberId = {
      id: id
    };
  }
  get("getdata", "member", "MemberDiv", memberId, function(){
    $('#addresslist').trigger('collapse')
    $('#memberslist').trigger('collapse')
    renderPage();
  });
}

/**
 * Sends a request to fetch the map.
 */
function getMaps(Lat,Lng) {
  $('#searchdiv').hide()

  var useragent = navigator.userAgent;

  if (useragent.indexOf('iPhone') != -1 || useragent.indexOf('Android') != -1 ) {
     $("#ContentDiv").html('<br/><div id="GoogleMap" style="width:100%; height:225px;"></div>');
       } else {
     $("#ContentDiv").html('<br/><div id="GoogleMap" style="width:100%; height:500px;"></div>');
  }

  if (Lat != null && Lng != null){
    initializeMap(Lat,Lng)
  } else {
    initializeMap()
  }
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
    "../model/classes/ProcessRequest.php",
    requestParameters,
    function(response) {
      $("#"+target).html(parseScript(response));

      ajaxdelay = setTimeout("$('#loading').hide();$('#content').animate({opacity: 1},'fast')",250);

      if(callback) {
        callback();
      }

    });
}

function loadAddressLiveSearch(divId) {
  $(divId).autocomplete({
    source: "../model/classes/ProcessRequest.php?action=livesearch&type=livesearchaddress&livesearchtable=members",
    autoFocus: true,
    minLength: 2,
    delay: 250,
    search: function(event, ui){
      $(divId).autocomplete( "option", "source", "../model/classes/ProcessRequest.php?action=livesearch&type=livesearchaddress&livesearchtable=members");
    },
    select: function(event, ui) {
      if(ui.item.ADR_id){
        getAddress(ui.item.ADR_id, ui.item.MEMBER_id);
      }
    }
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

function renderPage(){
  $('[data-role=collapsible]').collapsible();
  $('[data-role=listview]').listview();
  $('[data-role=controlgroup]').controlgroup();
  $('[data-role=button]').button();
  $('#search-basic').textinput();
}

$("#indexdiv").live('pageinit',function() {
  getHome();
});

