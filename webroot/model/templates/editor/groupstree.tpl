<div id="GroupsTreecontainer" class="ui-widget ui-widget-content ui-corner-all block-left">
  <div class="ui-widget-header block-header">
    <h1>{t}Groups{/t}</h1>
  </div>
  <div class="block-content">
    <div id="GroupsTree"></div>
  </div>
</div>
<div id="Groupscontainer" class="ui-widget ui-widget-content ui-corner-all block-right"></div>
<script type="text/javascript">
  $(document).ready(function() {
    $("#GroupsTree").jstree({
      "plugins" : ["themes","ui","json_data","contextmenu","crrm","dnd"],
      "core" : {
        "html_titles" : true
      },
      "ui" : {
        "select_limit" : 1,
        "selected_parent_close" : "select_parent"
      },
      "themes" : {
        "theme" : "default",
        "url" : "includes/jstree/themes/default/style.css"
      },
      "crrm" : {
        "move" : {
          "check_move" : function (m) {
            if(m.o.attr("rel") == 'group') {
              return true;
            }
            return false;
          }
        }
      },
      contextmenu: {items: customMenu},
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
    })
    .bind("create.jstree", function (e, data) {
      if (data.rslt.parent.attr("rel") == "group"){
        $.ajax({
          url: "model/classes/ProcessRequest.php",
          dataType: "json",
          data : {
            "action" : "dialog",
            "dialog" : "group",
            "type" : 'add',
            "parentid" : data.rslt.parent.attr("id").replace("node-",""),
            "title" : data.rslt.name
          },
          success : function (jsondata) {
             if (jsondata.result == 'fail'){
                $.jstree.rollback(data.rlbk);
                alert(jsondata.error.msg);
            } else if (jsondata.result == 'succes'){
                $(data.rslt.oc).attr("id", "node-" + jsondata.GROUP_id);
                alert(jsondata.msg);
            }
            var tree = jQuery.jstree._reference("#GroupsTree");
            var currentNode = tree._get_node(null, false);
            tree.refresh(currentNode);
          }
        })
      } else {
        $.jstree.rollback(data.rlbk);
      }
    })
    .bind("remove.jstree", function (e, data) {
      data.rslt.obj.each(function () {
        $.ajax({
          type: "POST",
          dataType: "json",
          url: "model/classes/ProcessRequest.php",
          data : {
            "action" : "dialog",
            "dialog" : "group",
            "type" : 'delete',
            "groupid" : $(this).attr("id").replace("node-","")
          },
          success : function (jsondata) {
             if (jsondata.result == 'fail'){
                alert(jsondata.error.msg);
            } else if (jsondata.result == 'succes'){
                alert(jsondata.msg);
            }
            var tree = jQuery.jstree._reference("#GroupsTree");
            var currentNode = tree._get_node(null, false);
            tree.refresh(currentNode);
          }
        })

      });
    })
    .bind("rename.jstree", function (e, data) {
      $.ajax({
        url: "model/classes/ProcessRequest.php",
        data : {
          "action" : "editdata",
          "type" : "editdetails",
          "table" : "groups",
          "field" : "GROUP_name",
          "groupid" : data.rslt.obj.attr("id").replace("node-",""),
          "value" : data.rslt.new_name
        },
        success : function (r) {
          if(!r.status) {
            $.jstree.rollback(data.rlbk);
          }
          data.inst.refresh(data.inst._get_parent(data.rslt.oc));
        }
      });
    })
    .bind("move_node.jstree", function (e, data) {
      data.rslt.o.each(function (i) {
        $.ajax({
          url: "model/classes/ProcessRequest.php",
          data : {
            "action" : "editdata",
            "type" : "editdetails",
            "table" : "groups",
            "field" : "GROUP_parent_id",
            "groupid" : $(this).attr("id").replace("node-",""),
            "value" : data.rslt.cr === -1 ? 1 : data.rslt.np.attr("id").replace("node-","") // parentid
          },
          success : function (r) {
            if(!r.status) {
              $.jstree.rollback(data.rlbk);
            }
            data.inst.refresh(data.inst._get_parent(data.rslt.oc));
          }
        });
      });
    }).bind("select_node.jstree", function (e, data) {
      // data.inst is the tree object, and data.rslt.obj is the node
      data.inst.toggle_node(data.rslt.obj);
      getGroup(data.rslt.obj.attr("id").replace("node-", ""));
      return false
    }).bind('loaded.jstree', function(e, data) {
      getGroup();
  });
});
</script>