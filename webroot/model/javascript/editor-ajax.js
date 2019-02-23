/**
 * Sends a request to get the events data for a particular member.
 */
function getMemberEvents(memberId) {
  var eventId = false;
  if (memberId){
    eventId = {
      EVENT_id: memberId
    };
  }

  get("getdata", "event", "Events",
    eventId,
    function() {
      loadEventLiveSearch("#newEventPartnerName","newEventPartnerId");
      $('.removeEventButton').button({
        icons: {
          primary: "ui-icon-trash"
        },
        text: false
      })
    })
}

/**
 * Toggles the archive mode.
 */
function toggleArchive() {
  $.get(
    "model/classes/ProcessRequest.php",
    {
      action: "togglearchive",
      type: "togglearchive"
    },
    function(response) {
      if (response == 1){
        $("#SearchContainer").addClass("archive");
        $("#ArchiveButton").addClass("archive");
        archive = true;
      } else {
        $("#SearchContainer").removeClass("archive");
        $("#ArchiveButton").removeClass("archive");
        archive = false;
      }
      getAddress();
    });
}


/**
 * Sends a post request with data from the provided form and fills the responseDiv with the response.
 */
function postForm(action, type, responseDiv, formId) {
  $.post(
    "model/classes/ProcessRequest.php",
    $("#" +formId).serialize() + "&action=" + action + "&type=" + type,
    function(response) {
      $("#"+responseDiv).html(parseScript(response));
    });
}

/**
 * Called when editing the details of a member.
 */
function editDetails(table, element, responseelement) {
  var value = element.type == "checkbox" ? element.checked : element.value;
  var field = element.name;

  $(responseelement).css("background-image", "url(css/images/icons/pending.gif)")

  $.post("model/classes/ProcessRequest.php",
  {
    type: "editdetails",
    action: "editdata",
    table: table,
    field: field,
    value: value
  },
  function(response) {
    animateElement(responseelement, parseScript(response))
  });
}

/**
 * Called when editing a event of a member.
 */
function editEventDate(table, date, divId, responseelement) {

  $(responseelement).css("background-image", "url(css/images/icons/pending.gif)")

  $.post("model/classes/ProcessRequest.php",
  {
    type: "editDetails",
    action: "editData",
    table: table,
    field: "EVENT_date",
    value: date
  },
  function(response) {
    animateElement(responseelement, parseScript(response));
    getMemberEvents();
  });
}

/**
 * Edits the groups a member belongs to.
 */
function editGroupMembers(element, responseelement, grouptype) {
  // Get the selected elements from groupsSelectElement
  var allVals = [];
  $(element + ' .jstree-real-checkbox:checked').each(function() {
    allVals.push($(this).val());
  });

  $(responseelement).css("background-image", "url(css/images/icons/pending.gif)")

  $.post("model/classes/ProcessRequest.php",
  {
    action: "editData",
    type: "editGroupMembers",
    grouptype: grouptype,
    groups: allVals.join(",")
  },
  function(response) {
    animateElement($(responseelement), parseScript(response));
    if ($("#AddressOfGroupTree").length != 0){
      var tree = jQuery.jstree._reference("#AddressOfGroupTree");
      var currentNode = tree._get_node(null, false);
      tree.refresh();
    } else {
      if ($("#CompleteGroupsTree").length != 0){
        var tree = jQuery.jstree._reference("#CompleteGroupsTree");
        var currentNode = tree._get_node(null, false);
        tree.refresh(currentNode.parent());
      }
      if ($("#SmallMemberOfGroupTree").length != 0){
        var tree = jQuery.jstree._reference("#SmallMemberOfGroupTree");
        var currentNode = tree._get_node(null, false);
        tree.refresh();
      }
    }
  });
}

/**
 * Sorts members, it sets the order of the member.
 */
function sorterMembers(query, responseelement){
  $(responseelement).css("background-image", "url(css/images/icons/pending.gif)")

  $.post("model/classes/ProcessRequest.php",
  {
    action: "editData",
    type: "sortMemberData",
    query: query
  },
  function(response) {
    animateElement(responseelement, parseScript(response));
  }
  );
}

/**
 * Sends a request to update the address coordinates..
 */
function updateAddress() {
  var requestParamaters = {
    action: "editdata",
    type: "coordinates"
  };

  $.post("model/classes/ProcessRequest.php", requestParamaters,
    function(){
      getAddress();
    });
}

/**
 * Render the MemberOfGroupTree
 */
function RenderMemberOfGroupTree() {
  $("#MemberOfGroupTree").jstree({
    // List of active plugins
    "plugins" : ["themes","ui","json_data","checkbox"],
    "core" : {
      "html_titles" : true,
      "limit": 1
    },
    "ui" : {
      "select_limit" : 1,
      "selected_parent_close" : "select_parent"
    },
    "themes" : {
      "theme" : "default",
      "url" : "includes/jstree/themes/default/style.css"
    },
    "checkbox": {
      real_checkboxes: true,
      real_checkboxes_names: function (n) {
        var nid = 0;
        $(n).each(function (data) {
          nid = $(this).attr("id").replace("node-", "");
        });
        return (["GROUP_id", nid]);
      },
      two_state: true
    },
    "json_data" : {
      "ajax": {
        "url": "model/classes/ProcessRequest.php",
        "data": function(n) {
          return {
            "id": n.attr ? n.attr("id").replace("node-", "") : 1,
            "action": "getdata",
            "type": "groups",
            "request": "select",
            "nodes": "members"
          };
        }
      }
    }
  }).bind('check_node.jstree', function(e, data) {
    editGroupMembers('#MemberOfGroupTree','#LABEL_MEMBER_OF_GROUP','members');
    data.inst.refresh(data.inst._get_parent(data.rslt.oc));
  }).bind('uncheck_node.jstree', function(e, data) {
    editGroupMembers('#MemberOfGroupTree','#LABEL_MEMBER_OF_GROUP','members');
    data.inst.refresh(data.inst._get_parent(data.rslt.oc));
  }).bind("loaded.jstree", function(event, data) {
    $('#MemberOfGroupTree').find('li[rel!=members]').find('ins.jstree-checkbox:first').hide()
  }).bind("select_node.jstree", function (e, data) {
      // data.inst is the tree object, and data.rslt.obj is the node
      data.inst.toggle_node(data.rslt.obj);
      return false
  });
}

/**
 * Render the customMenu
 */
function customMenu(node) {
  // The default set of all items
  var items = {
    createItem: { // The "rename" menu item
      label: labelCreateGroup,
      action: function (obj) {
        this.create(obj);
      }
    },
    renameItem: { // The "rename" menu item
      label: labelRenameGroup,
      action: function (obj) {
        this.rename(obj);
      }
    },
    deleteItem: { // The "delete" menu item
      label: labelDeleteGroup,
      action: function (obj) {
        this.remove(obj);
      }
    }
  };

  if ($(node).hasClass("folder")) {
    // Delete the "delete" menu item
    delete items.deleteItem;
  }

  return items;
}