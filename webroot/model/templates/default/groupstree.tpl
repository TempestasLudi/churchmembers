<div id="GroupsTreecontainer" class="ui-widget ui-widget-content ui-corner-all block-left">
  <div class="ui-widget-header block-header"><h1>{t}Groups{/t}</h1></div>
  <div class="block-content">
    <div id="GroupsTree"></div>
  </div>
</div>
<div id="Groupscontainer" class="ui-widget ui-widget-content ui-corner-all block-right"></div>

<script type="text/javascript">
$(document).ready(function() {

  $("#GroupsTree").jstree({
    "plugins" : ["themes","ui","json_data"],
    "core" : {
      "html_titles" : true,
      "animation": 0,
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
    "json_data" : {
      "ajax": {
        "url": "model/classes/ProcessRequest.php",
        "data": function(n) {
          return {
            "id": n.attr ? n.attr("id").replace("node-", "") : 1,
            "action": "getdata",
            "type": "groups",
            "request": "default"
          };
        }
      }
    }
  }).bind("select_node.jstree", function (e, data) {
      // data.inst is the tree object, and data.rslt.obj is the node
      data.inst.toggle_node(data.rslt.obj);
      getGroup(data.rslt.obj.attr("id").replace("node-", ""));
      return false
  }).bind('loaded.jstree', function(e, data) {
      getGroup();
  });
})
</script>