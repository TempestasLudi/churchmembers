<div id='SettingsMenu' class="block-left" >
  <div class="adminlist" style='position:initial;'>
    <button class="button" onclick="getAdminContent('settings')" title="{t}System settings{/t}">{t}System settings{/t}</button><br/>
    <button class="button" onclick="getAdminContent('userrights')" title="{t}Userrights{/t}">{t}Userrights{/t}</button><br/>
    <button class="button" onclick="getAdminContent('failedlogin')" title="{t}Failed logins{/t}">{t}Failed logins{/t}</button><br/>
    <button class="button" onclick="getAdminContent('maintenance')" title="{t}Maintenance{/t}">{t}Maintenance{/t}</button><br/>
    </div>
</div>

<div id='SettingsDetails' class="ui-widget ui-widget-content ui-corner-all block-right">
  <div id='SettingsContent'>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $(".button").button();
  });
</script>