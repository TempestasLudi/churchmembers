<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Emailbericht</title>
    <style>
      body{
        text-align:left;
        color:#333333;
        font-size: 100%;
        font-family: arial,helvetica,"Liberation Sans","DejaVu Sans Condensed",sans-serif;
        background-color: #DADADA;
        margin:20px;
      }
      a{
        color:#111111;
        text-decoration: underline;
      }
    </style>
  </head>
  <body leftmargin="10" marginwidth="10" topmargin="10" marginheight="10" offset="0" bgcolor='#DADADA' style="margin:20px">
    <div class="block-content">
      <table border="0" cellpadding="0" cellspacing="0" width="800">
        <tr style='background:#155AA2;'>
          <th width="120" align="right">&nbsp;</th>
          <td width="10">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <th bgcolor="#FFFFFF">&nbsp;</th>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
        </tr>
        <tr align="left">
          <th colspan="3" align="left" bgcolor="#FFFFFF" style='text-align:left;'><img src="https://leden.gkvleiden.nl/assets/emailtemplate/logo.png" alt="Herengrachtkerk" width="475" height="105" /></th>
        </tr>
        <tr>
          <th bgcolor="#FFFFFF">&nbsp;</th>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
        </tr>
        <tr>
          <th align="right" bgcolor="#FFFFFF" style='text-align:right;' valign="top">{t}From{/t}</th>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF" valign="top"><a href='mailto:[+VALUE.EMAIL+]'>[+VALUE.USERNAME+]</a></td>
        </tr>
        <tr>
          <th align="right" bgcolor="#FFFFFF" style='text-align:right;' valign="top">{t}To{/t}</th>
          <td bgcolor="#FFFFFF"></td>
          <td bgcolor="#FFFFFF" valign="top">[+VALUE.RECEIVERS+]</td>
        </tr>
        <tr>
          <th bgcolor="#FFFFFF">&nbsp;</th>
          <td bgcolor="#FFFFFF"></td>
          <td bgcolor="#FFFFFF"></td>
        </tr>
        <tr>
          <th bgcolor="#FFFFFF" valign="top"></th>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF" valign="top"><h2>[+VALUE.SUBJECT+]</h2></td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF"></td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF" valign="top">[+VALUE.MESSAGE+]</td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF"></td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
        </tr>

        <tr>
          <td bgcolor="#FFFFFF"></td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="3" align="center" height="30" style='text-align:center;'><a href="http://www.herengrachtkerk.nl/" target="_blank" style='font-size:12px; color:#666;'>www.herengrachtkerk.nl</a></td>
        </tr>
      </table>
    </div>
  </body>
</html>