<div class="ui-widget-header block-header"><h1>[+VALUE.MEMBER_fullname+]</h1></div>
<div id="StartContent" class="block-content">
  <!-- *********************   TAB 1 Personal   ************************************ -->
  <table border="0" cellpadding="0" cellspacing="0" id="Personal" width="100%">
    <tr>
      <th width="140">{t}Complete name{/t}<input name="MEMBER_result" type="hidden" id="MEMBER_result" VALUE="1" />
      </th>
      <td width="250">[+VALUE.MEMBER_fullname+]</td>
      <th rowspan="10" >
    <div style="float:right">
      <a class="colorbox" href="[+VALUE.MEMBER_photo+]">
        <img id="MEMBER_photo" src="includes/phpThumb/phpThumb.php?src=../../[+VALUE.MEMBER_photo+]&w=128&h=128&far=1&zc=1" width="128" alt="{t}Photo{/t}" class="ui-state-default ui-corner-all"/>
      </a>
      <br /><br />
      <div id="photo-uploader"></div>
      <button id="remove_photo" style="display:none">{t}Delete{/t}</button>
    </div>
    </th>
    <script type="text/javascript">[+INPUT.MEMBER_photobutton+]</script>
    </tr>
    <tr>
      <th>{t}Christian name{/t}</th>
      <td>[+VALUE.MEMBER_christianname+]</td>
    </tr>
    <tr>
      <th>{t}Birth date{/t}</th>
      <td>[+VALUE.MEMBER_birthdate+]</td>
    </tr>
    <tr>
      <th>{t}Marriage{/t}</th>
      <td>[+VALUE.MEMBER_mariagedate+]</td>
    </tr>
    <tr>
      <th>{t}Male/Female{/t}</th>
      <td>[+VALUE.MEMBER_gender+]</td>
    </tr>
    <tr>
      <th>{t}Membertype{/t}</th>
      <td>[+VALUE.MEMBERTYPE_name+]</td>
    </tr>
    <tr>
      <th>{t}Mobilenumber{/t}</th>
      <td>[+VALUE.MEMBER_mobilephone+]</td>
    </tr>
    <tr>
      <th>{t}Email{/t}</th>
      <td><a href="mailto:[+VALUE.MEMBER_email+]">[+VALUE.MEMBER_email+]</a></td>
    </tr>
    <tr>
      <th>&nbsp;</th>
      <td colspan="2"></td>
    </tr>
    <tr>
      <th>{t}Member of:{/t}</th>
      <td colspan="3"><div id="SmallMemberOfGroupTree"></div></td>
    </tr>
    <tr>
      <th>{t}Introduction{/t}</th>
      <td></td>
    </tr>
    <tr>
      <td width="390" colspan="2" >[+INPUT.MEMBER_introduction+]</td>
    </tr>
  </table>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    loadHTMLEditor();

    $('a.colorbox').colorbox({
      width:"60%",
      height:"60%",
      close: labelClose
    });

    $("#SmallMemberOfGroupTree").jstree({
      // List of active plugins
      "plugins" : ["themes","ui","json_data"],
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
      "json_data" : {
        "ajax": {
          "url": "model/classes/ProcessRequest.php",
          "data": function(n) {
            return {
              "id": n.attr ? n.attr("id").replace("node-", "") : 1,
              "action": "getdata",
              "type": "groups",
              "request": "currentmember",
              "nodes" : "members"
            };
          }
        }
      }
    })
  })
</script>