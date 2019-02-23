<div id="StatisticsContainer" class="ui-widget ui-widget-content ui-corner-all block-left ">
  <div class="ui-widget-header block-header"><h1><img src="css/images/icons/group_icon_16x16.png" class="imgInLine" height="24" width="24" alt="{t}Select members{/t}" />&nbsp;{t}Select groups{/t}</h1></div>
  <div class="block-content">
    <div id="MailGroupTree"></div>
  </div><br />
  <div class="ui-widget-header block-header"><h1><img src="css/images/icons/search_male_user.png" class="imgInLine" height="24" width="24" alt="{t}Or pick a member{/t}" />&nbsp;{t}Or pick a member{/t}</h1></div>
  <div class="block-content">
    <input id="SearchReceiver" name="SearchReceiver" type="text" onclick="this.select(); $('#SearchReceiver').autocomplete('search');" maxlength="20"  style="width:100%;" value="{t}Search members{/t}" /><br /><br />
        <p><em>{t}Only members with an emailaddress are shown{/t}</em></p>
  </div>
</div>

<div id="EmailContainer" class="ui-widget ui-widget-content ui-corner-all block-right">
  <div class="ui-widget-header block-header"><h1><img src="css/images/icons/mail.png" class="imgInLine" height="24" width="24" alt="{t}Send email to members{/t}" />&nbsp;{t}Send email to members{/t}</h1></div>
  <div class="block-content">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
      <tr>
        <th width="80">{t}Subject{/t}</th>
        <td align='right'>[+VALUE.MAIL_SUBJECT_TEMPLATE+]</td>
        <td><input name="MAIL_subject" type="text" id="MAIL_subject" style="width:300px;" maxlength="30" /></td>
      </tr>
      <tr>
        <th>{t}Receiver's{/t}</th>
        <td colspan="2">{t}Select on the left side{/t}</td>
      </tr>
       <tr>
        <th>&nbsp;</th>
        <td colspan="2"><ul id="Receiverslist_groups" class="Receiverslist"></ul><ul id="Receiverslist_members" class="Receiverslist"></ul></td>
      </tr>
    </table>
  </div>

  <br />
  <div class="ui-widget-header block-header"><h1><img src="css/images/icons/edit.png" class="imgInLine" height="24" width="24" alt="{t}Message{/t}" />&nbsp;{t}Message{/t}</h1></div>
  <div class="block-content">
    <textarea name="MAIL_message" id="MAIL_message" style="width:100%;" rows="25"></textarea>
  </div>

  <div class="ui-widget-header block-header"><h1><img src="css/images/icons/next.png" class="imgInLine" height="24" width="24" alt="{t}Message{/t}" />&nbsp;{t}Send{/t}</h1></div>
  <div class="block-content">
    <button id="MAIL_send_button">{t}Send e-mail{/t}</button>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $('#MAIL_send_button').button({
    icons: {primary: "ui-icon-circle-triangle-e  "}
  }).click(function() {
    sendMail()
  })

$('#MAIL_message').tinymce({
      // Location of TinyMCE script
      script_url : 'includes/tiny_mce/tiny_mce.js',
      theme : "advanced",
      language : "nl",
      plugins : "autolink,lists,pagebreak,save,noneditable,visualchars,tabfocus,paste",
      valid_elements : "a[href|target=_blank],strong/b,div[align],br,em/i,u",
      theme_advanced_buttons1 : "fontsizeselect,bold,italic,underline,separator,cut,copy,paste,separator,strikethrough,justifyleft,justifycenter,justifyright,bullist,blockquote,separator,forecolor,separator,numlistundo,redo,link,unlink",
      theme_advanced_toolbar_location : "top",
      theme_advanced_toolbar_align : "left",
      theme_advanced_statusbar_location : "bottom",
      theme_advanced_resizing : true,
      theme_advanced_path : false,
      theme_advanced_more_colors : false,
      theme_advanced_text_colors : "#000000,CCCCCC,666666,6600CC,155AA2,FF0000,FF9900,FF0099",
      paste_auto_cleanup_on_paste : true,
      paste_remove_styles: true,
      paste_remove_styles_if_webkit: true,
      paste_strip_class_attributes: true
    });

    // Render GroupTree
    $("#MailGroupTree").jstree({
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
              "request": "email"
            };
          }
        }
      }
    }).bind('check_node.jstree', function(e, data) {
      $('#Receiverslist_groups').html('');
      $('#MailGroupTree .jstree-real-checkbox:checked').each(function() {
        addReceiver('group',$(this).val(),$(this).next().next().text().substring(2));
      });
    }).bind('uncheck_node.jstree', function(e, data) {
      $('#Receiverslist_groups').html('');
      $('#MailGroupTree .jstree-real-checkbox:checked').each(function() {
        addReceiver('group',$(this).val(),$(this).next().next().text().substring(2));
      });
    }).bind('loaded.jstree', function(e, data) {
      $('#MailGroupTree').jstree('uncheck_all')
      $('#MailGroupTree').find('li[rel!=members]').find('ins.jstree-checkbox:first').hide()
    }).bind("select_node.jstree", function (e, data) {
      // data.inst is the tree object, and data.rslt.obj is the node
      data.inst.toggle_node(data.rslt.obj);
      return false
  });


$('#SearchReceiver').autocomplete({
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
          type : 'email'
        }
      }).success(response).error(function () {
        response([]);
      });
    },
    select: function (event, ui) {
      if (ui.item.loading) {
        event.preventDefault();
      } else if(ui.item.MEMBER_id){
          addReceiver('member', ui.item.MEMBER_id, ui.item.value)
          $(this).val('');
          return false;
      }
    },
    focus: function (event, ui) {
      if (ui.item.loading) {
        event.preventDefault();
      }
    },
    autoFocus: true,
    minLength: 0,
    delay: 250
  });

  });
</script>