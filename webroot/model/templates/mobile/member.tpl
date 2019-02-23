<div data-role="collapsible" data-collapsed="false" data-theme="b">
  <h1>[+VALUE.MEMBER_fullname+]</h1>
  <img id="MEMBER_photo" src="../includes/phpThumb/phpThumb.php?src=../../[+VALUE.MEMBER_photo+]&w=128&h=128&far=1&zc=1" width="128" alt="photo of user"/>
  <p>[+VALUE.MEMBER_christianname+]<br/>
    [+VALUE.MEMBER_birthdate+]<br/>
    [+VALUE.MEMBERTYPE_name+]<br/>
    [+VALUE.MEMBER_mobilephone+]<br/>
    <a href="mailto:[+VALUE.MEMBER_email+]">[+VALUE.MEMBER_email+]</a>
  </p>

  <h4>{t}Introduction{/t}</h4>
  <p>[+VALUE.MEMBER_introduction+]</p>

  <h4>{t}Member of:{/t}</h4>
  <p>[+VALUE.GROUP+]</p>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    RenderSmallMemberOfGroupTree();
  })
</script>