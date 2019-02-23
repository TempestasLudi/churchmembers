<div id="SearchOptionsContainer" class="ui-widget ui-corner-all block-left" >
  <!-- *********************   Report Options   ******************************* -->
  <div class='ui-widget-header block-header'><h1><img src="css/images/icons/process.png" class="imgInLine" height="24" width="24" alt="{t}Filters{/t}" />&nbsp;{t}Filters{/t}&nbsp;<span style='font-size:0.6em; font-weight:normal'>&nbsp;<a id='optionreset' onclick='getReport()'>{t}Reset{/t}</a></span></h1></div>

    <form id="Report_Options_Form" name="Report_Options_Form" action=""  method="post">

    <div id="Report_Options_Accordion">
      <!-- *********************   First Tab   ******************************* -->
      <div class="search-option-header">
        <a><strong>{t}Birth date / etc..{/t}</strong></a>
        <div id="Report_Birthday_Toggle" class="ReportToggleSwitch notSelect TitleSwitch">
          <span id="Report_Birthday_Include" class="checked" onclick="setReportToggle('Birthday',1)">{t}Include{/t}</span>
          <span id="Report_Birthday_Not_Include" class="" onclick="setReportToggle('Birthday',0)">{t}Exclude{/t}</span>
          <input type="hidden" name="Report_Birthday_Toggle_Value" id="Report_Birthday_Toggle_Value" value="1"/>
        </div>

      </div>
      <div>
        <!-- *********************   Report Birthday Slider   ******************************* -->
        <table width="100%"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td colspan="2"><input type="hidden" name="Report_Birthday_Slider_Values" id="Report_Birthday_Slider_Values"/><span id="Report_Birthday_Slider_Value"></span>
            </td>
          </tr>
          <tr>
            <td colspan="2"><div id="Report_Birthday_Slider"></div>
            </td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td><strong>{t}Birth date in period{/t}</strong></td>
            <td width="70">
              <div id="Report_Birthday_Month_Toggle" class="ReportToggleSwitch notSelect">
                <span id="Report_Birthday_Month_Include" class="checked" onclick="setReportToggle('Birthday_Month',1)">{t}Include{/t}</span>
                <span id="Report_Birthday_Month_Not_Include" class="" onclick="setReportToggle('Birthday_Month',0)">{t}Exclude{/t}</span>
                <input type="hidden" name="Report_Birthday_Month_Toggle_Value" id="Report_Birthday_Month_Toggle_Value" value="1"/>
              </div>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <input type="text" id="Report_Birthday_Month_From" name="Report_Birthday_Month_From" style="width:80px;"/>&nbsp;<label for="Report_Birthday_Month_To">{t}Till{/t}&nbsp;</label><input type="text" id="Report_Birthday_Month_To" name="Report_Birthday_Month_To" style="width:80px;"/>
            </td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <!-- *********************   End Report Birthday Slider   ******************************* -->

          <!-- *********************   Report Select Baptismdate   ******************************* -->
          <tr >
            <td>
              <strong>{t}Baptised{/t}</strong>
            </td>
            <td width="70">
              <div id="Report_Baptismdate_Toggle" class="ReportToggleSwitch notSelect">
                <span id="Report_Baptismdate_Include" class="checked" onclick="setReportToggle('Baptismdate',1)">{t}Include{/t}</span>
                <span id="Report_Baptismdate_Not_Include" class="" onclick="setReportToggle('Baptismdate',0)">{t}Exclude{/t}</span>
                <input type="hidden" name="Report_Baptismdate_Toggle_Value" id="Report_Baptismdate_Toggle_Value" value="1"/>
              </div>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <input type="text" id="Report_Baptismdate_From" name="Report_Baptismdate_From" style="width:80px;"/><label for="Report_Baptismdate_To">{t}Till{/t}</label>&nbsp;<input type="text" id="Report_Baptismdate_To" name="Report_Baptismdate_To" style="width:80px;"/>
            </td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>


          <!-- *********************   End Report Select Baptismdate   ******************************* -->

          <!-- *********************   Report Select Confessiondate    ******************************* -->
          <tr >
            <td><strong>{t}Confession{/t}</strong>
            </td>
            <td width="70">
              <div id="Report_Confessiondate_Toggle" class="ReportToggleSwitch notSelect">
                <span id="Report_Confessiondate_Include" class="checked" onclick="setReportToggle('Confessiondate',1)">{t}Include{/t}</span>
                <span id="Report_Confessiondate_Not_Include" class="" onclick="setReportToggle('Confessiondate',0)">{t}Exclude{/t}</span>
                <input type="hidden" name="Report_Confessiondate_Toggle_Value" id="Report_Confessiondate_Toggle_Value" value="1"/>
              </div>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <input type="text" id="Report_Confessiondate_From" name="Report_Confessiondate_From" style="width:80px;"/><label for="Report_Confessiondate_To">{t}Till{/t}</label>&nbsp;<input type="text" id="Report_Confessiondate_To" name="Report_Confessiondate_To" style="width:80px;"/>
            </td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <!-- *********************   End Report SelectConfessiondate   ******************************* -->

          <!-- *********************   Report Select Mariagedate    ******************************* -->
          <tr >
            <td><strong>{t}Marriage{/t}</strong>
            </td>
            <td width="70">
              <div id="Report_Mariagedate_Toggle" class="ReportToggleSwitch notSelect">
                <span id="Report_Mariagedate_Include" class="checked" onclick="setReportToggle('Mariagedate',1)">{t}Include{/t}</span>
                <span id="Report_Mariagedate_Not_Include" class="" onclick="setReportToggle('Mariagedate',0)">{t}Exclude{/t}</span>
                <input type="hidden" name="Report_Mariagedate_Toggle_Value" id="Report_Mariagedate_Toggle_Value" value="1"/>
              </div>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <input type="text" id="Report_Mariagedate_From" name="Report_Mariagedate_From" style="width:80px;"/><label for="Report_Mariagedate_To">{t}Till{/t}</label>&nbsp;<input type="text" id="Report_Mariagedate_To" name="Report_Mariagedate_To" style="width:80px;"/>
            </td>
          </tr>
        </table>
        <!-- *********************   End Report Select Mariagedate    ******************************* -->
      </div>

      <!-- *********************   Second Tab   ******************************* -->
      <div class="search-option-header">
        <a><strong>{t}Groups{/t}</strong></a>
        <div id="Report_Groups_Toggle" class="ReportToggleSwitch notSelect TitleSwitch">
          <span id="Report_Groups_Include" class="checked" onclick="setReportToggle('Groups',1)">{t}Include{/t}</span>
          <span id="Report_Groups_Not_Include" class="" onclick="setReportToggle('Groups',0)">{t}Exclude{/t}</span>
          <input type="hidden" name="Report_Groups_Toggle_Value" id="Report_Groups_Toggle_Value" value="1"/>
        </div>
      </div>
      <!-- *********************   Report Select Groups   ******************************* -->
      <div>
        <div id="Report_Groups_Select"><div id="SearchGroupTree"></div></div>
      </div>

      <!-- *********************   End Report Select Groups   ******************************* -->

      <!-- *********************   Thirth Tab   ******************************* -->
      <div class="search-option-header">
        <a><strong>{t}Event type{/t}</strong></a>
        <div id="Report_Events_Toggle" class="ReportToggleSwitch notSelect  TitleSwitch">
          <span id="Report_Events_Include" class="checked" onclick="setReportToggle('Events',1)">{t}Include{/t}</span>
          <span id="Report_Events_Not_Include" class="" onclick="setReportToggle('Events',0)">{t}Exclude{/t}</span>
          <input type="hidden" name="Report_Events_Toggle_Value" id="Report_Events_Toggle_Value" value="1"/>
        </div>
      </div>

      <!-- *********************   Report Select Events   ******************************* -->
      <div >
        <table width="100%"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td colspan="2">[+REPORTOPTIONS.EVENTLIST+]
            </td>
          </tr>
          <tr><td>&nbsp;</td></tr>
          <tr>
            <td><strong>{t}Period{/t}</strong>
            </td>
            <td width="70">
              <div id="Report_Events_Date_Toggle" class="ReportToggleSwitch notSelect  TitleSwitch">
                <span id="Report_Events_Date_Include" class="checked" onclick="setReportToggle('Events_Date',1)">{t}Include{/t}</span>
                <span id="Report_Events_Date_Not_Include" class="" onclick="setReportToggle('Events_Date',0)">{t}Exclude{/t}</span>
                <input type="hidden" name="Report_Events_Date_Toggle_Value" id="Report_Events_Date_Toggle_Value" value="1"/>
              </div>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <input type="text" id="Report_Events_Select_From" name="Report_Events_Select_From" style="width:80px;"/><label for="Report_Events_Select_To">{t}Till{/t}</label>&nbsp;<input type="text" id="Report_Events_Select_To" name="Report_Events_Select_To" style="width:80px;"/>
            </td>
          </tr>
        </table>
      </div>
      <!-- *********************   End Report Select Events   ******************************* -->

      <!-- *********************   Fourth Tab   ******************************* -->
      <div class="search-option-header">
        <a><strong>{t}Membertype{/t}</strong></a>
        <div id="Report_Memberstype_Toggle" class="ReportToggleSwitch notSelect TitleSwitch">
          <span id="Report_Memberstype_Include" class="checked" onclick="setReportToggle('Memberstype',1)">{t}Include{/t}</span>
          <span id="Report_Memberstype_Not_Include" class="" onclick="setReportToggle('Memberstype',0)">{t}Exclude{/t}</span>
          <input type="hidden" name="Report_Memberstype_Toggle_Value" id="Report_Memberstype_Toggle_Value" value="1"/>
        </div>
      </div>
      <!-- *********************   Report Select Memberstype   ******************************* -->
      <div>
        <div id="Report_Memberstype_Select">
          [+REPORTOPTIONS.MEMBERTYPELIST+]
        </div>
      </div>
      <!-- *********************   End Report Select Memberstype   ******************************* -->

      <!-- *********************   Fifth Tab   ******************************* -->
      <div class="search-option-header"><a><strong>{t}Male/Female{/t}</strong></a></div>
      <!-- *********************   Report Select Gender   ******************************* -->
      <div>
        <div id="Report_Gender_Select">
          <input type="checkbox" id="Report_Gender_Select_Box_Male" name="Report_Gender_Select_Box[]"  value="male" /><label for="Report_Gender_Select_Box_Male" title="Male"  style="width:100%; margin-bottom:5px;">{t}Male{/t}</label>
          <input type="checkbox" id="Report_Gender_Select_Box_Female" name="Report_Gender_Select_Box[]"  value="female" /><label for="Report_Gender_Select_Box_Female" title="Female"  style="width:100%; margin-bottom:5px;" >{t}Female{/t}</label>
        </div>
      </div>
      <!-- *********************   End Report Select Gender   ******************************* -->

      <!-- *********************   Sixth Tab   ******************************* -->
      <div class="search-option-header">
        <a><strong>{t}City{/t}</strong></a>
        <div id="Report_City_Toggle" class="ReportToggleSwitch notSelect TitleSwitch">
          <span id="Report_City_Include" class="checked" onclick="setReportToggle('City',1)">{t}Include{/t}</span>
          <span id="Report_City_Not_Include" class="" onclick="setReportToggle('City',0)">{t}Exclude{/t}</span>
          <input type="hidden" name="Report_City_Toggle_Value" id="Report_City_Toggle_Value" value="1"/>
        </div>
      </div>
      <!-- *********************   Report Select City   ******************************* -->
      <div>
        <div id="Report_City_Select">[+REPORTOPTIONS.CITYLIST+]</div>
      </div>
      <!-- *********************   End Report Select City   ******************************* -->

      <!-- *********************   Seventh Tab   ******************************* -->
      <div class="search-option-header"><a><strong>{t}Archive{/t}</strong></a></div>
      <!-- *********************   Report Select Archive   ******************************* -->
      <div>
        <div id="Report_Archive_Select">
          <input type="checkbox" id="Report_Archive_Select_notin" name="Report_Archive_Select_Box[]" value="0" checked="checked"/><label for="Report_Archive_Select_notin" style="width:100%; margin-bottom:5px;">{t}Not in archive{/t}</label>
          <input type="checkbox" id="Report_Archive_Select_in" name="Report_Archive_Select_Box[]" value="1" /><label for="Report_Archive_Select_in" style="width:100%; margin-bottom:5px;">{t}In archive{/t}</label>
        </div>
      </div>
      <!-- *********************   End Report Select Archive   ******************************* -->


      <!-- *********************   Eighth Tab   ******************************* -->
      <div class="search-option-header"><a><strong>{t}Only Addresses{/t}</strong></a></div>
      <!-- *********************   Report Select Addresses Only   ******************************* -->
      <div>
        <input type="checkbox" id="Report_Addresses_Select" name="Report_Addresses_Select"  value="1" /><label for="Report_Addresses_Select" title="{t}Only Addresses{/t}"  style="width:100%; margin-bottom:5px;">{t}Only Addresses{/t}</label>
      </div>
      <!-- *********************   End Report Select Addresses Only   ******************************* -->


    </div>
  </form>
</div>
<!-- *********************   End Report Options   ******************************* -->

<!-- *********************   Report Result Table   ******************************* -->
<div id='SearchResultsContainer' class="ui-widget ui-widget-content ui-corner-all block-right">
  <div id='ReportResultsDiv'>
    <div class='ui-widget-header block-header'>
      <h1>
        <img src="css/images/icons/search_image.png" class="imgInLine" height="24" width="24" alt="{t}Filters{/t}" />&nbsp;{t}Results{/t}&nbsp;
        <span style="font-size:0.6em; font-weight:normal">
          <a id="vcol">{t}Show/hide columns{/t}</a>&nbsp;|&nbsp;
          <a id="GroupingButton" onclick="switchGrouping('#ReportResultsTable')">{t}Remove grouping{/t}</a>&nbsp;|&nbsp;
          <a onclick="exportExcel('#ReportResultsTable')">{t}Export{/t}</a>
        </span>
      </h1>
    </div>
    <div>
      <table id="ReportResultsTable"></table>
      <div id="pager"></div>
    </div>
  </div>
</div>
<!-- *********************   End Report Result Table   ******************************* -->

<!-- *********************   Javascript Report Options   ******************************* -->
<script type="text/javascript">
  $(document).ready(function() {

    // Render Report
    $('#Report_Options_Accordion').accordion({
      header: 'div.search-option-header',
      autoHeight: false,
      navigation: false,
      active: false,
      collapsible: true
    });

    // Create Birthday Slider
    $( "#Report_Birthday_Slider" ).slider({
      range: true,
      min: 0,
      max: 120,
      values: [0, 120],
      slide: function( event, ui ) {
        $( "#Report_Birthday_Slider_Value" ).text( ui.values[ 0 ] + " jaar - " + ui.values[ 1 ] + " jaar" );
      },
      change: function(event,ui){
        $( "#Report_Birthday_Slider_Values").val(ui.values);
        updateReportSearchResults()
      }
    });

    // Set Birthday Slider Text
    $("#Report_Birthday_Slider_Value").text( $("#Report_Birthday_Slider").slider("values", 0) + " jaar - " + $("#Report_Birthday_Slider").slider("values", 1) + " jaar");
    $("#Report_Birthday_Slider_Values").val($("#Report_Birthday_Slider").slider("values"));

    // Create Buttonssets
    $("#Report_Events_Select,#Report_Memberstype_Select,#Report_Gender_Select,#Report_City_Select,#Report_Archive_Select").buttonset().bind('change',function(){
      updateReportSearchResults()
    });
    $("#Report_Addresses_Select").button().bind('change',function(){
      updateReportSearchResults();
    });

    // Render GroupTree
    $("#SearchGroupTree").jstree({
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
      "checkbox" : {
        "two_state" : true
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
              "request": "search"
            };
          }
        }
      }
    }).bind('check_node.jstree', function(e, data) {
      updateReportSearchResults();
    }).bind('uncheck_node.jstree', function(e, data) {
      updateReportSearchResults();
    }).bind('loaded.jstree', function(e, data) {
      $('#SearchGroupTree').jstree('uncheck_all')
    }).bind("select_node.jstree", function (e, data) {
      // data.inst is the tree object, and data.rslt.obj is the node
      data.inst.toggle_node(data.rslt.obj);
      return false
  });

    // Create Dateselectors
    createDateSelector("Report_Birthday_Month_From", "Report_Birthday_Month_To","[+VALUE.startyear+]","[+VALUE.endyear+]");
    createDateSelector("Report_Baptismdate_From", "Report_Baptismdate_To","[+VALUE.MEMBER_baptismdate_min+]","[+VALUE.MEMBER_baptismdate_max+]");
    createDateSelector("Report_Confessiondate_From", "Report_Confessiondate_To","[+VALUE.MEMBER_confessiondate_min+]","[+VALUE.MEMBER_confessiondate_max+]");
    createDateSelector("Report_Mariagedate_From", "Report_Mariagedate_To","[+VALUE.MEMBER_mariagedate_min+]","[+VALUE.MEMBER_mariagedate_max+]");
    createDateSelector("Report_Events_Select_From", "Report_Events_Select_To","1900-01-01","2030-01-01");

  })
</script>
<!-- *********************   End Javascript Report Options   ******************************* -->

<!-- *********************   Javascript Report Table   ******************************* -->
<script type="text/javascript">
  $(document).ready(function() {
    $("#ReportResultsTable").jqGrid({
      url:"model/classes/ProcessRequest.php?action=report&type=updatedata&" + $("#Report_Options_Form").serialize() + '&columns=MEMBER_id,MEMBER_photo,MEMBER_fullname,MEMBER_gender,MEMBERTYPE_name,MEMBER_age,ADR_address',
      mtype: 'POST',
      datatype : 'json',
      jsonReader : {
        root: "rows",
        page: "page",
        total: "total",
        records: "records",
        repeatitems: false,
        id: 0
      },
      cmTemplate: {sortable:false},
      sortname: "MEMBER_rank",
      sortorder: "asc",
      colModel : [
        {name:'MEMBER_id', width:0, hidden:true, hidedlg:true, label:"id"},
        {name:'MEMBER_photo', width:40, sortable:false, align:'center', resizable:false, hidedlg:true , label:" "},
        {name:'MEMBER_fullname', width:180, hidden:false, hidedlg:true, label:"Naam"},
        {name:'MEMBER_christianname', width:90, hidden:true, label:"Doopnaam"},
        {name:'MEMBER_firstname', width:90, hidden:true, label:"Voornaam"},
        {name:'MEMBER_preposition', width:90, hidden:true, firstsortorder:"asc", label:"Tussenvoegsel (Lid)"},
        {name:'MEMBER_familyname', width:90, hidden:true, firstsortorder:"asc", label:"Achternaam (Lid)"},
        {name:'MEMBER_gender', width:60, align:'center', hidden:false, label:"M/V"},
        {name:'MEMBERTYPE_name', width:80, align:'center', hidden:false, label:"Lid"},
        {name:'MEMBER_age', width:60, align:'center', hidden:false, label:"Leeftijd"},
        {name:'MEMBER_email', width:200, hidden:true, label:"Email (Lid)"},
        {name:'MEMBER_mobilephone', width:90, hidden:true, label:"Mobielnummer"},
        {name:'MEMBER_notes', width:200, sortable:false, hidden:true, label:"Opmerkingen"},
        {name:'MEMBER_birthdate', width:90, align:'center', hidden:true, formatter:'date', formatoptions:{srcformat:"Y-m-d",newformat:"d-m-Y"}, label:"Geboortedatum"},
        {name:'MEMBER_baptismdate', width:90, align:'center', hidden:true, label:"Doopdatum"},
        {name:'MEMBER_confessiondate', width:90, align:'center', hidden:true, label:"Belijdenis"},
        {name:'MEMBER_mariagedate', width:90, align:'center', hidden:true, label:"Trouwdatum"},
        {name:'MEMBER_rank', width:0, hidden:true, hidedlg:true, label:"Rank"},
        {name:'ADR_id', width:0, hidden:true, hidedlg:true, label:"ADR_id"},
        {name:'ADR_sort', width:0, hidden:true, hidedlg:true, label:"ADR_sort"},
        {name:'ADR_start', width:40, hidden:true, label:"Aanhef"},
        {name:'ADR_addressing', width:150, hidden:true, label:"Adressering"},
        {name:'ADR_fullfamilyname', width:130, hidden:true, label:"Achternaam (met tussenvoegsel)"},
        {name:'ADR_familyname', width:130, hidden:true, label:"Achternaam"},
        {name:'ADR_familyname_preposition', width:130, hidden:true, label:"Tussenvoegsel"},
        {name:'ADR_address', width:200, hidden:false, label:"Adres"},
        {name:'ADR_street', width:170, hidden:true, label:"Straat"},
        {name:'ADR_number', width:20, hidden:true, label:"Huisnummer"},
        {name:'ADR_street_extra', width:170, hidden:true, label:"Extra adresregel"},
        {name:'ADR_zip', width:50, align:'center', hidden:true, label:"Postcode"},
        {name:'ADR_city', width:90, hidden:true, label:"Stad"},
        {name:'ADR_country', width:90, hidden:true, label:"Land"},
        {name:'ADR_telephone', width:90, hidden:true, label:"Telefoon"},
        {name:'ADR_email', width:200, hidden:true, label:"Emailadres"},
        {name:'MEMBER_GROUPS', width:90, sortable:false, hidden:true, label:"Leden groepen"},
        {name:'ADDRESS_GROUPS', width:90, sortable:false, hidden:true, label:"Adres groepen"},
        {name:'EVENTS', width:90, sortable:false, hidden:true, label:"Relaties"}
      ],
      grouping: true,
      groupingView : {
        groupField : ['ADR_sort'],
        groupColumnShow : [false],
        groupText : ['<b>{0} - ({1})</b>'],
        groupCollapse : false,
        groupOrder: ['asc'],
        groupDataSorted : true
      },
      viewrecords: true,
      autowidth:false,
      shrinkToFit: false,
      height: 500,
      width: 679,
      rowNum:25,
      scroll: 1,
      hidegrid: false,
      hoverrows: true,
      loadui: "block",
      pager: '#pager',
      toppager: false,
      onSelectRow: function(rowId){
        var member_id = rowId;
        $.colorbox({
          href:"model/classes/ProcessRequest.php?action=getdata&type=member&id="+member_id,
          width: '700px',
          maxHeight: '60%',
          close: labelClose
        });
      },
      loadError: function(error){
        parseScript(error['responseText']);
        if ( typeof console !== "undefined" && console.error && console.warn ) {
          console.debug(status);
          console.debug(error);
        }
      },
      onSortCol: function(index,iCol,sortorder){
        grouping = true;
        switchGrouping("#ReportResultsTable");
      }
    })

    $("#vcol").click(function (){
      $("#ReportResultsTable").jqGrid('columnChooser', {
        width: 650,
        done : function (perm) {
          if (perm) {
            // "OK" button are clicked
            this.jqGrid("remapColumns", perm, true);
            updateReportSearchResults();
          } else {
            // we can do some action in case of "Cancel" button clicked
          }
        }
      });
    });

  });
</script>
<!-- *********************   End Javascript Report Table   ******************************* -->