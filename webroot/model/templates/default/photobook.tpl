<div id="PhotobookContainer" class="ui-widget ui-widget-content ui-corner-all">
  <div class="ui-widget-header block-header"><h1><img src="css/images/icons/photo_camera.png" class="imgInLine" height="24" width="24" alt="{t}Photobook{/t}" />&nbsp;{t}Photobook{/t}</h1></div>
  <div class="block-content" id="Photobook">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td>[+VALUE.PHOTOBOOK+]</td>
      </tr>
    </table>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function() {

    $('.gal-item img').jail({
      placeholder : "css/images/loading.gif",
      event : "load+scroll"
    });

    $('.colorbox_member').on('click', function () {
      $.colorbox({
        href:"model/classes/ProcessRequest.php?action=getdata&type=member&id=" + $(this).attr('data-id'),
        width: '700px',
        maxHeight: '60%',
        close: labelClose
      });
    })

  });
</script>