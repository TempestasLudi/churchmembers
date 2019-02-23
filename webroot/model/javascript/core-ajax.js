/**
 * Globals
 */
var ajaxdelay;

/**
 * Sends a request to get home page
 */
function getHome() {
  toggleSubMenus("home");
  get("getdata", "home", "ContentDiv", false);
}

/**
 * Sends a request to get address info
 */
function getAddress(ADR_id, MEMBER_id, navigate_str) {
  toggleSubMenus("address");

  ADR_id = ADR_id || false;
  MEMBER_id = MEMBER_id || false;
  navigate_str = navigate_str || false;

  var parameters = {
    ADR_id: ADR_id,
    MEMBER_id: MEMBER_id,
    navigate_str: navigate_str
  };

  get("getdata", "address", "ContentDiv",
    parameters,
    function(){
      if (MEMBER_id){
        getMember(MEMBER_id);
      }

      if (archive){
        $("#buttonNewAddressDialog").hide();
      }
    });
}

/**
 * Sends a request to get group info.
 */
function getGroup(id) {
  toggleSubMenus("group");

  id = id || false;
  var parameters = {
    GROUP_id: id
  };

  if ($("#GroupsTreecontainer").length == 0 ){
    get("getdata", "groups", "ContentDiv", parameters,false);
  } else {
    get("getdata", "group", "Groupscontainer", parameters,function(id){
      $("#node-"+id).parentsUntil(".jstree",".jstree-closed").andSelf().each(function () {
        $("#GroupsTree").jstree("open_node", this);
      });
    });
  }

}

/**
 * Sends a request to get export page.
 */
function getLists() {
  toggleSubMenus("lists");
  get("export", "template", "ContentDiv",false);
}

/**
 * Sends a request to fetch the map.
 */
function getMaps(Lat,Lng) {
  toggleSubMenus("maps");
  $("#ContentDiv").html('<div id="GoogleMap"></div><div id="markersdata" class=""></div><div style="clear:both"></div>');

  if (Lat != null && Lng != null){
    initializeMap(Lat,Lng)
  } else {
    initializeMap()
  }
}

/**
 * Sends a request to get photobook page.
 */
function getPhotobook() {
  toggleSubMenus("photobook");
  get("getdata", "photobook", "ContentDiv",false);
}

/**
 * Sends a request to get email page.
 */
function getEmail() {
  toggleSubMenus("email");
  get("email", "template", "ContentDiv", false);
}

/**
 * Sends a request to fetch detailed information about a member.
 */
function getMember(id,callback) {
  id = id || false;
  var parameters = {
    id: id
  };

  get("getdata", "member", "MembersContent",parameters,
    function(){
      if (archive){
        $("#buttonNewMemberDialog").hide();
      }
    });
}

/**
 * Called when editing the details of a member.
 */
function editIntroduction() {
  content = $('#MEMBER_introduction').tinymce().getContent()
  responseelement = '#LABEL_MEMBER_introduction';
  $(responseelement).css("background-image", "url(css/images/icons/pending.gif)")

  $.post("model/classes/ProcessRequest.php",
  {
    type: "editintroduction",
    action: "editdata",
    table: 'members',
    value: content,
    field: 'MEMBER_introduction'
  },
  function(response) {
    animateElement(responseelement, parseScript(response))
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
 * Animates the provided element with flashing colors.
 */
function animateElement(responseelement, ajaxResponse) {

  if(parseScript(ajaxResponse)) {
    $(responseelement).css("background-image", "url(css/images/icons/error.gif)")
  } else {
    $(responseelement).css("background-image", "url(css/images/icons/succes.gif)")
  }
  var animatetimer;
  clearTimeout(animatetimer);
  animatetimer = setTimeout(function() {
    $(responseelement).css("background-image", "none")
  }, 5000);

}

/**
 * Run error scripts in Ajax response, and return rest of the response.
 */
function parseScript(_source) {
  var source = _source;
  var scripts = new Array();

  // Strip out tags
  if (source == undefined) return true;

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

  // Return the cleaned sources
  return source;
}