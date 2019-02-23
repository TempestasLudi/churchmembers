<div id="ExportContainer" class="ui-widget ui-widget-content ui-corner-all">
  <div class="ui-widget-header block-header">
    <h1><img src="css/images/icons/download_to_computer.png" class="imgInLine" height="24" width="24" alt="{t}Export{/t}" />&nbsp;{t}Export{/t}</h1>
  </div>
  <div class="block-content">
    <p>{t}Export intro{/t}</p>
    <br/>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <th>{t}Yearbook{/t}</th>
      <td>&nbsp;</td>
      <td width="32">[+INPUT.yearbook_pdf+]</td>
      <td width="32"><a  onclick="Export('yearbook','xls')"><img src="css/images/export/xls.png" alt="{t}Export to XLS{/t}" title="{t}Export to XLS{/t}" /></a></td>
      <td width="32"><a  onclick="Export('yearbook','doc')"><img src="css/images/export/doc.png" alt="{t}Export to DOC{/t}" title="{t}Export to DOC{/t}" /></a></td>
      </tr>
      <tr>
        <th>{t}Addresses{/t}</th>
      <td>&nbsp;</td>
      <td width="32">[+INPUT.addresses_pdf+]</td>
      <td width="32"><a  onclick="Export('addresses','xls')"><img src="css/images/export/xls.png" alt="{t}Export to XLS{/t}" title="{t}Export to XLS{/t}" /></a></td>
      <td width="32"><a  onclick="Export('addresses','doc')"><img src="css/images/export/doc.png" alt="{t}Export to DOC{/t}" title="{t}Export to DOC{/t}" /></a></td>
      </tr>
      <tr>
        <th>{t}Groups with members{/t}</th>
      <td>&nbsp;</td>
      <td>[+INPUT.membergroupscomplete_pdf+]</td>
      <td><a  onclick="Export('membergroupscomplete','xls')"><img src="css/images/export/xls.png" alt="{t}Export to XLS{/t}" title="{t}Export to XLS{/t}" /></a></td>
      <td><a  onclick="Export('membergroupscomplete','doc')"><img src="css/images/export/doc.png" alt="{t}Export to DOC{/t}" title="{t}Export to DOC{/t}" /></a></td>
      </tr>
      <tr>
        <th>{t}Groups with members (name, address, telephone){/t}</th>
      <td>&nbsp;</td>
      <td>[+INPUT.membergroupssimple_pdf+]</td>
      <td><a  onclick="Export('membergroupssimple','xls')"><img src="css/images/export/xls.png" alt="{t}Export to XLS{/t}" title="{t}Export to XLS{/t}" /></a></td>
      <td><a  onclick="Export('membergroupssimple','doc')"><img src="css/images/export/doc.png" alt="{t}Export to DOC{/t}" title="{t}Export to DOC{/t}" /></a></td>
      </tr>
      <tr>
        <th>{t}Birthdates{/t}</th>
      <td>&nbsp;</td>
      <td>[+INPUT.allbirthdays_pdf+]</td>
      <td><a  onclick="Export('allbirthdays','xls')"><img src="css/images/export/xls.png" alt="{t}Export to XLS{/t}" title="{t}Export to XLS{/t}" /></a></td>
      <td><a  onclick="Export('allbirthdays','doc')"><img src="css/images/export/doc.png" alt="{t}Export to DOC{/t}" title="{t}Export to DOC{/t}" /></a></td>
      </tr>
      <tr>
        <th>{t}Birthdates children (<=12 year) &amp; elderly persons (>=65 years){/t}</th>
      <td>&nbsp;</td>
      <td>[+INPUT.specialbirthdays_pdf+]</td>
      <td><a  onclick="Export('specialbirthdays','xls')"><img src="css/images/export/xls.png" alt="{t}Export to XLS{/t}" title="{t}Export to XLS{/t}" /></a></td>
      <td><a  onclick="Export('specialbirthdays','doc')"><img src="css/images/export/doc.png" alt="{t}Export to DOC{/t}" title="{t}Export to DOC{/t}" /></a></td>
      </tr>
      <tr>
        <th>{t}All mariagedates{/t}</th>
      <td>&nbsp;</td>
      <td>[+INPUT.mariagedates_pdf+]</td>
      <td><a  onclick="Export('mariagedates','xls')"><img src="css/images/export/xls.png" alt="{t}Export to XLS{/t}" title="{t}Export to XLS{/t}" /></a></td>
      <td><a  onclick="Export('mariagedates','doc')"><img src="css/images/export/doc.png" alt="{t}Export to DOC{/t}" title="{t}Export to DOC{/t}" /></a></td>
      </tr>
      <tr>
        <th>{t}All changes in administration of last year{/t}</th>
      <td>&nbsp;</td>
      <td>[+INPUT.lastchanges_pdf+]</td>
      <td><a  onclick="Export('lastchanges','xls')"><img src="css/images/export/xls.png" alt="{t}Export to XLS{/t}" title="{t}Export to XLS{/t}" /></a></td>
      <td><a  onclick="Export('lastchanges','doc')"><img src="css/images/export/doc.png" alt="{t}Export to DOC{/t}" title="{t}Export to DOC{/t}" /></a></td>
      </tr>
      <tr>
        <th>{t}Photobook{/t}</th>
      <td>&nbsp;</td>
      <td>[+INPUT.photobook_pdf+]</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      </tr>
    </table>
    &nbsp;</div>
</div>