<div id="StatisticsContainer" class="ui-widget ui-widget-content ui-corner-all block-left ">
    <div class="ui-widget-header block-header"><h1><img src="css/images/icons/info.png" class="imgInLine" height="24" width="24" alt="{t}Welcome{/t}" />&nbsp;{t}Welcome{/t}</h1></div>
  <div class="block-content">
    [+VALUE.MEMBER_photo+]
    <div style="clear:both"></div>
  </div>

  <div class="ui-widget-header block-header"><h1><img src="css/images/icons/chart.png" class="imgInLine" height="24" width="24" alt="{t}Statistics{/t}" />&nbsp;{t}Statistics{/t}</h1></div>
  <div class="block-content readmore">
    <table width="100%">
      <tbody>
        <tr><th >{t}Total addresses{/t}</th><td >[+VALUE.TOTAL_ADDRESSES+]</td></tr>
        <tr><th >{t}Total members{/t}</th><td >[+VALUE.TOTAL_MEMBERS+]</td></tr>
        <tr class="ui-widget-header"><th >{t}Distribution members{/t}</th><td >&nbsp;</td></tr>
        [+VALUE.TOTAL_MEMBERSTYPES+]
        <tr class="ui-widget-header"><th >{t}Statistics till last year{/t}</th><td >&nbsp;</td></tr>
        [+VALUE.YEARLY_EVENTS+]
      </tbody>
    </table>
  </div>

  <br/>

  <div id="logo" class="block-content" style="padding:0">
    <img src="css/images/logo.png" width="240" height="50" align="middle" alt="Logo" title="Herengrachtkerk" />
  </div>
</div>

<div id="HomeContainer" class="ui-widget ui-widget-content ui-corner-all block-right">
  <div class="ui-widget-header block-header"><h1><img src="css/images/icons/phone_book_edit.png" class="imgInLine" height="24" width="24" alt="{t}Recent mutations{/t}" />&nbsp;{t}Recent mutations{/t}</h1></div>
  <div class="block-content lastmutationlist">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">[+VALUE.LAST_EVENTS+]</table>
  </div>

  <div class="ui-widget-header block-header"><h1><img src="css/images/icons/calendar.png" class="imgInLine" height="24" width="24" alt="{t}Birthdaycalender{/t}" />&nbsp;{t}Birthdaycalender{/t}</h1></div>
  <div class="block-content">
    <div id='birthdaycalendar'></div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    createSliders(150);

    $('button[name="MEMBERS_button"]').button();

    $('.member_link').on('click', function () {
       getAddress($(this).attr('address-id'),$(this).attr('member-id'))
    })

    $('a.colorbox').colorbox({
      width:"60%",
      height:"60%",
      close: labelClose
    });

    $('#birthdaycalendar').fullCalendar({
      theme: true,
      header: {
        left: 'prev',
        center: 'title',
        right: 'next'
      },
      defaultView: 'basicWeek',
      firstDay: 0,
      editable : false,
      height: 300,
      cache: true,
      allDayDefault: true,
      columnFormat: {
        week: 'ddd d-M'
      },
      titleFormat: {
        week: "d[ MMMM yyyy]{ '&#8212;' d MMMM yyyy}" // September 7 - 13 2009
      },
      events: {
        url: 'model/classes/ProcessRequest.php',
        type: 'POST',
        data: {
          action: 'getdata',
          type: 'calendar'
        }
      },
      eventClick: function(calEvent, jsEvent, view) {
        getAddress(calEvent.ADRid,calEvent.MEMBERid);
      },
      eventRender: function(event, element) {
        element.find('span.fc-event-title').html(element.find('span.fc-event-title').text());
      },
      monthNames: ['januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli',
        'augustus', 'september', 'oktober', 'november', 'december'],
      dayNamesShort :['Zo', 'Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za']
    });
  });
</script>