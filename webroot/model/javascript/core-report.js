var reportdelay;
var grouping = true;

/**
 * Sends a request to fetch the lists.
 */
function getReport() {
  toggleSubMenus("report");
  getReportOptions();
}

/**
 * Sends a request to fetch advanced search options .
 */
function getReportOptions() {
  get("getdata", "search", "ContentDiv",false);
}

/**
 * Enables report options
 */
function EnableReportOptions() {
  $("#Report_Groups_Select,#Report_Events_Select,#Report_Memberstype_Select,#Report_Gender_Select,#Report_City_Select,#Report_Archive_Select").buttonset('enable').bind('change',function(){
    updateReportSearchResults()
  });
  $("#Report_Addresses_Select").button().bind('change',function(){
    updateReportSearchResults();
  });
}

/**
 * Sends a request to fetch advanced search results.
 */
function updateReportSearchResults() {
  var groups = "";
  $("#SearchGroupTree").jstree("get_checked", null, true).each(function(index, value) {
    groups += $(this).attr("id").replace("node-", ",");
  });

  var element = '#ReportResultsTable';
  $(element).setGridParam({
    url:'model/classes/ProcessRequest.php?action=report&type=updatedata&' + $("#Report_Options_Form").serialize() + '&groups=' + groups + '&columns=' +  getColumns(element)
  });

  //delay for sending data request
  clearTimeout(reportdelay);
  tabledelayfunction = function() {
    sendGooglePageView('searchresult');
    $(element).trigger("reloadGrid",[{
      page:1
    }])
  };
  reportdelay = setTimeout(tabledelayfunction,500);
}

/**
 * Toggles report option element
 */
function setReportToggle(element,value){
  $('#Report_'+element+'_Not_Include, #Report_'+element+'_Include').removeClass('checked');
  if (value == 1){
    $('#Report_'+element+'_Include').addClass('checked');
  } else {
    $('#Report_'+element+'_Not_Include').addClass('checked');
  }
  $('#Report_'+element+'_Toggle_Value').val(value);
  updateReportSearchResults()
}

/**
 * Creates a DateSelector for reports.
 */
function createDateSelector(divID1, divID2,minDate,maxDate){

  var dates = $( "#"+divID1 + ", #" + divID2 ).datepicker({
    minDate: createJSdate(minDate),
    maxDate: createJSdate(maxDate),
    changeMonth: true,
    changeYear: true,
    dateFormat: 'dd-mm-yy',
    regional: 'nl',
    showButtonPanel: true,
    onSelect: function( selectedDate ) {
      var option = "";
      if (this.id.indexOf('_From') > 1){
        option = "minDate"
      } else {
        option = "maxDate"
      }

      var instance = $( this ).data( "datepicker" )
      var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings );
      dates.not( this ).datepicker( "option", option, date );
      if ((dates[0].value != "") && (dates[1].value != "")){
        updateReportSearchResults();
      }

    }
  });
}

function switchGrouping(element){
  if (grouping){
    $(element).jqGrid('groupingRemove', true);
    $(element).jqGrid('setGridParam', {
      sortname: 'ADR_id, MEMBER_id',
      sortorder: 'asc'
    }).trigger('reloadGrid', [{
      page: 1
    }]);
    $('#GroupingButton').text(labelAddGrouping)
  } else {
    $(element).jqGrid('groupingGroupBy',['ADR_sort']);
    $('#GroupingButton').text(labelRemoveGrouping)
  }
  grouping = (!grouping);
}

function exportExcel(element) {
  var postData = $(element).jqGrid('getGridParam','postData');
  var cols = $(element).getGridParam('colModel');
  var colNames=new Array();
  var colTitle=new Array();
  var ii=0;

  for (var col in cols){
    if ((cols[col].hidden != 1) & (cols[col].name != 'MEMBER_photo')) {
      colNames[ii]=cols[col].name;
      colTitle[ii]=cols[col].label;
      ii++;
    }
  }    // capture col names

  var groups = "";
  $("#SearchGroupTree").jstree("get_checked", null, true).each(function(index, value) {
    groups += $(this).attr("id").replace("node-", ",");
  });


  parameters = '&' + $.param(postData) + '&columns=' + colNames + '&' + $.param({
    columnnames: colTitle
  })+"&"+ $("#Report_Options_Form").serialize() + '&request=xls&groups=' + groups;

  Export('reportdata','xls', parameters)

  sendGooglePageView('export/search/');
}


/**
 * Generates js date of string ( yyyy-mm-dd -> date() ).
 */
function createJSdate(string){
  var date = new Date();
  var parts = String(string).split(/[-]/);
  date.setFullYear(parts[0]);
  date.setMonth(parts[1] - 1);
  date.setDate(parts[2]);
  return date;
}

/**
 * Generates js date of string ( yyyy-mm-dd -> date() ).
 */
function getColumns(element){
  var cols = $(element).getGridParam('colModel');
  var colNames=new Array();
  var ii=0;

  for (var col in cols){
    if (cols[col].hidden != 1) {
      colNames[ii++]=cols[col].name;
    }
  }

  return colNames;
}