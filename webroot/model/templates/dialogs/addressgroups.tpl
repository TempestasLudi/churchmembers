<div id="AddressGroupsDialogText">
  <h1>{t}Select address groups{/t}</h1>
  <p id="DialogStatus"></p>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tbody>
        <tr >
          <th>&nbsp;</th>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <th><label id="LABEL_ADDRESS_GROUP">{t}Select groups{/t}</label></th>
          <td><div id="AddressOfGroupTree"></div></td>
        </tr>
        <tr >
          <th>&nbsp;</th>
          <td><div id="AddressGroupsDialogStatus"></div></td>
        </tr>
      </tbody>
    </table>
</div>
 <script type="text/javascript">
  $(document).ready(function() {
  $("#AddressOfGroupTree").jstree({
    // List of active plugins
    "plugins" : ["themes","ui","types","json_data","checkbox"],
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
    "types" : {
      "types" : {
        "group" : {
          "select_node" : function(e) {
            this.toggle_node(e);
            return false;
          }
        }
      }
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
              "nodes": "addresses"
          };
        }
      }
    }
  }).bind('check_node.jstree', function(e, data) {
    editGroupMembers('#AddressOfGroupTree','#LABEL_ADDRESS_GROUP','addresses');
    data.inst.refresh(data.inst._get_parent(data.rslt.oc));
  }).bind('uncheck_node.jstree', function(e, data) {
    editGroupMembers('#AddressOfGroupTree','#LABEL_ADDRESS_GROUP','addresses');
    data.inst.refresh(data.inst._get_parent(data.rslt.oc));
  }).bind("loaded.jstree", function(event, data) {
    $('#AddressOfGroupTree').find('li[rel!=addresses]').find('ins.jstree-checkbox:first').hide()
  })
    });
</script>