<div class="ui-widget-header block-header"><h1><img src="css/images/icons/computer_process.png" class="imgInLine" height="24" width="24" alt="{t}System settings{/t}" />&nbsp;{t}System settings{/t}</h1></div>
<div class="block-content">
  <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <thead>
    </thead>
    <tbody>
      <tr class="ui-widget-header ">
        <th>{t}Site settings{/t}</th>
        <td></td>
      </tr>
      <tr>
        <th><label id="SETTINGS_systemname" for="systemname">{t}Systemname{/t}</label></th>
        <td><input name="systemname" type="text" id="systemname" onchange="editDetails('settings',this, '#SETTINGS_systemname')"  VALUE="[+VALUE.systemname+]" style="width:350px;" /></td>
      </tr>
      <tr>
        <td colspan="2">{t}Default name:{/t}&nbsp; {t}ChurchMembers{/t}</td>
      </tr>
      <tr>
        <th><label id="SETTINGS_maintenance" for="maintenance">{t}Maintenance activated{/t}</label></th>
        <td>[+INPUT.maintenance+]</td>
      </tr>
      <tr>
        <td colspan="2">{t}Only administrator can login{/t}</td>
      </tr>
      <tr>
        <th><label id="SETTINGS_login_mail" for="login_mail">{t}Send mail on Login{/t}</label></th>
        <td>[+INPUT.login_mail+]</td>
      </tr>
      <tr>
        <th><label id="SETTINGS_administrator_email" for="administrator_email">{t}Administrator email{/t}</label></th>
        <td><input name="administrator_email" type="text" id="administrator_email" onchange="editDetails('settings',this, '#SETTINGS_administrator_email')"  VALUE="[+VALUE.administrator_email+]" style="width:350px;" /></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr class="ui-widget-header ">
        <th>{t}Cookie settings{/t}</th>
        <td></td>
      </tr>
      <tr>
        <th><label id="SETTINGS_cookie_name" for="cookie_name">{t}Cookiename{/t}</label></th>
        <td><input name="cookie_name" type="text" id="cookie_name" onchange="editDetails('settings',this, '#SETTINGS_cookie_name')"  VALUE="[+VALUE.cookie_name+]" style="width:200px;" /></td>
      </tr>
      <tr>
        <th><label id="SETTINGS_cookie_domain" for="cookie_domain">{t}Cookiedomain{/t}</label></th>
        <td><input name="cookie_domain" type="text" id="cookie_domain" onchange="editDetails('settings',this, '#SETTINGS_cookie_domain')"  VALUE="[+VALUE.cookie_domain+]" style="width:200px;" /></td>
      </tr>
      <tr>
        <td colspan="2">{t}Sets cookie on a specific domain{/t}</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr class="ui-widget-header ">
        <th>{t}Security settings{/t}</th>
        <td></td>
      </tr>
      <tr>
        <th><label id="SETTINGS_login_maxattempts" for="login_maxattempts">{t}Maximum number of login attemps{/t}</label></th>
        <td><input name="login_maxattempts" type="text" id="login_maxattempts" onchange="editDetails('settings',this, '#SETTINGS_login_maxattempts')"  VALUE="[+VALUE.login_maxattempts+]" style="width:20px;" /></td>
      </tr>
      <tr>
        <th><label id="SETTINGS_system_secure" for="system_secure">{t}Use secure SSL connection (recommended){/t}</label></th>
        <td>[+INPUT.system_secure+]</td>
      </tr>
      <tr>
        <th><label id="SETTINGS_system_secure_port" for="system_secure_port">{t}Set secure SSL port{/t}</label></th>
        <td><input name="system_secure_port" type="text" id="system_secure_port" onchange="editDetails('settings',this, '#SETTINGS_system_secure_port')"  VALUE="[+VALUE.system_secure_port+]" style="width:100px;" /></td>
      </tr>
      <tr>
        <td colspan="2">{t}Default port is 443{/t}</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr class="ui-widget-header ">
        <th>{t}Extern authentication{/t}</th>
        <td></td>
      </tr>
      <tr>
        <th><label id="SETTINGS_auth_enabled" for="auth_enabled">{t}Use extern authentication{/t}</label></th>
        <td>[+INPUT.auth_enabled+]</td>
      </tr>
      <tr>
        <th><label id="SETTINGS_auth_validationurl" for="auth_validationurl">{t}Validation url{/t}</label></th>
        <td><input name="auth_validationurl" type="text" id="auth_validationurl" onchange="editDetails('settings',this, '#SETTINGS_auth_validationurl')"  VALUE="[+VALUE.auth_validationurl+]" style="width:375px;" /></td>
      </tr>
      <tr>
        <td colspan="2">{t}The URL that checks if user is already loged in, and gives USER_username and optional MEMBER_id where user is linked. 'USER_username|MEMBER_id'{/t}</td>
      </tr>
      <tr>
        <th><label id="SETTINGS_auth_loginurl" for="auth_loginurl">{t}Login url{/t}</label></th>
        <td><input name="auth_loginurl" type="text" id="auth_loginurl" onchange="editDetails('settings',this, '#SETTINGS_auth_loginurl')"  VALUE="[+VALUE.auth_loginurl+]" style="width:375px;" /></td>
      </tr>
      <tr>
        <th><label id="SETTINGS_auth_logouturl" for="auth_logouturl">{t}Logout url{/t}</label></th>
        <td><input name="auth_logouturl" type="text" id="auth_logouturl" onchange="editDetails('settings',this, '#SETTINGS_auth_logouturl')"  VALUE="[+VALUE.auth_logouturl+]" style="width:375px;" /></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr class="ui-widget-header ">
        <th>{t}Locale settings{/t}</th>
        <td></td>
      </tr>
      <tr>
        <th><label id="SETTINGS_locale_officecode_visible" for="locale_officecode_visible">{t}Show the local officecode{/t}</label></th>
        <td>[+INPUT.locale_officecode_visible+]</td>
      </tr>
      <tr>
        <th><label id="SETTINGS_locale_officecode" for="locale_officecode">{t}Local officecode{/t}</label></th>
        <td><input name="locale_officecode" type="text" id="locale_officecode" onchange="editDetails('settings',this, '#SETTINGS_locale_officecode')"  VALUE="[+VALUE.locale_officecode+]" style="width:100px;" /></td>
      </tr>
      <tr>
        <td colspan="2">{t}The office code of phonenumbers (not needed){/t}</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr class="ui-widget-header ">
        <th>{t}Export settings{/t}</th>
        <td></td>
      </tr>
      <tr>
        <th><label id="SETTINGS_export_docraptor_enabled" for="export_docraptor_enabled">{t}Enable PDF export (Using Docraptor){/t}</label></th>
        <td>[+INPUT.export_docraptor_enabled+]</td>
      </tr>
      <tr>
        <th><label id="SETTINGS_export_docraptor_key" for="export_docraptor_key">{t}Docraport API key{/t}</label></th>
        <td><input name="export_docraptor_key" type="text" id="export_docraptor_key" onchange="editDetails('settings',this, '#SETTINGS_export_docraptor_key')"  VALUE="[+VALUE.export_docraptor_key+]" style="width:100px;" /></td>
      </tr>
      <tr>
        <td colspan="2">{t}This key is needed for pdf export. Request a key via http://docraptor.com/{/t}</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr class="ui-widget-header ">
        <th>{t}Google analytics account{/t}</th>
        <td></td>
      </tr>
      <tr>
        <th><label id="SETTINGS_google_analytics_accountid" for="google_analytics_accountid">{t}Google analytics account id{/t}</label></th>
        <td><input name="google_analytics_accountid" type="text" id="google_analytics_accountid" onchange="editDetails('settings',this, '#SETTINGS_google_analytics_accountid')"  VALUE="[+VALUE.google_analytics_accountid+]" style="width:100px;" /></td>
      </tr>
      <tr>
        <td colspan="2">{t}Set this is to you GA id : something like UE-XXXXX-Y. This will append the Async Loading code to churchmembers{/t}</td>
      </tr>
      <tr>
        <th><label id="SETTINGS_google_analytics_domainname" for="google_analytics_domainname">{t}Google analytics domain name{/t}</label></th>
        <td><input name="google_analytics_domainname" type="text" id="google_analytics_domainname" onchange="editDetails('settings',this, '#SETTINGS_google_analytics_domainname')"  VALUE="[+VALUE.google_analytics_domainname+]" style="width:200px;" /></td>
      </tr>
      <tr>
        <td colspan="2">{t}Default is 'none'. If you want the trackers across multiple subdomains enter '.domain.com'{/t}</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
            <tr class="ui-widget-header ">
        <th>{t}Add extra markers to map{/t}</th>
        <td></td>
      </tr>
      <tr>
        <th><label id="SETTINGS_map_externaljson" for="map_externaljson">{t}Url to JSON file{/t}</label></th>
        <td><input name="map_externaljson" type="text" id="map_externaljson" onchange="editDetails('settings',this, '#SETTINGS_map_externaljson')"  VALUE="[+VALUE.map_externaljson+]" style="width:375px;" /></td>
      </tr>
      <tr>
        <td colspan="2">{t}Set this to a JSON file if you want extra markers added to the map{/t}</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
 <tr class="ui-widget-header ">
        <th>{t}Mail{/t}</th>
        <td></td>
      </tr>
      <tr>
        <th><label id="SETTINGS_mail_use" for="mail_use">{t}Enable mailing members{/t}</label></th>
        <td>[+INPUT.mail_use+]</td>
      </tr>
      <tr>
        <th><label id="SETTINGS_mail_subject" for="mail_subject">{t}Mail subject prefix{/t}</label></th>
        <td><input name="mail_subject" type="text" id="mail_subject" onchange="editDetails('settings',this, '#SETTINGS_mail_subject')"  VALUE="[+VALUE.mail_subject+]" style="width:375px;" /></td>
      </tr>
      <tr>
        <th><label id="SETTINGS_smtp_host" for="smtp_host">{t}SMTP host{/t}</label></th>
        <td><input name="smtp_host" type="text" id="smtp_host" onchange="editDetails('settings',this, '#SETTINGS_smtp_host')"  VALUE="[+VALUE.smtp_host+]" style="width:375px;" /></td>
      </tr>
      <tr>
        <th><label id="SETTINGS_smtp_port" for="smtp_port">{t}SMTP port{/t}</label></th>
        <td><input name="smtp_port" type="text" id="smtp_port" onchange="editDetails('settings',this, '#SETTINGS_smtp_port')"  VALUE="[+VALUE.smtp_port+]" style="width:50px;" /></td>
      </tr>
      <tr>
        <th><label id="SETTINGS_smtp_ssl" for="smtp_ssl">{t}SMTP security{/t}</label></th>
        <td><input name="smtp_ssl" type="text" id="smtp_ssl" onchange="editDetails('settings',this, '#SETTINGS_smtp_ssl')"  VALUE="[+VALUE.smtp_ssl+]" style="width:50px;" /></td>
      </tr>
      <tr>
        <th><label id="SETTINGS_smtp_username" for="smtp_username">{t}SMTP username{/t}</label></th>
        <td><input name="smtp_username" type="text" id="smtp_username" onchange="editDetails('settings',this, '#SETTINGS_smtp_username')"  VALUE="[+VALUE.smtp_username+]" style="width:375px;" /></td>
      </tr>
      <tr>
        <th><label id="SETTINGS_smtp_password" for="smtp_password">{t}SMTP password{/t}</label></th>
        <td><input name="smtp_password" type="text" id="smtp_password" onchange="editDetails('settings',this, '#SETTINGS_smtp_password')"  VALUE="[+VALUE.smtp_password+]" style="width:375px;" /></td>
      </tr>
    </tbody>
  </table>
</div>