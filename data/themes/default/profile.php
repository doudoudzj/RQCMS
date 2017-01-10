<?php
if(!defined('RQ_ROOT')) exit('Access Denied');
include RQ_DATA."/themes/{$theme}/header.php";
print <<<EOT
<div id=main>
<div id=fullbox>
<div class=full>
<form action="profile.php" method="post">
<input type="hidden" name="action" value="modpro" />
<div class="formbox">
  <p>
    <label for="oldpassword">旧密码(*):<br />
	<input name="oldpassword" id="oldpassword" type="password" size="54" maxlength="20" tabindex="1" value="" class="formfield" />
    </label>
  </p>
  <p>
    <label for="newpassword">新密码(*):<br />
	<input name="newpassword" id="newpassword" type="password" size="54" maxlength="20" tabindex="2" value="" class="formfield" />
    </label>
  </p>
  <p>
    <label for="confirmpassword">确认密码(*):<br />
	<input name="confirmpassword" id="confirmpassword" type="password" size="54" maxlength="20" tabindex="3" value="" class="formfield" />
    </label>
  </p>
  <p>
    <button type="submit" class="formbutton">确定</button>
  </p>
</div>
</form>
</div>
</div>
</div>

EOT;
include RQ_DATA."/themes/{$theme}/footer.php";
?>