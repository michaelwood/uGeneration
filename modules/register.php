<?php

if (isset($_POST['submit']) && $_POST['antispam'] == '6') {
	if (utils::check_all_fields($_POST)) {
	$return_message = $database->add_user($_POST['username'],$_POST['realname'],$_POST['email'],$_POST['password']);
	echo "<p>$return_message</p>"; 
	}
}


?>
<h2>Registration</h2>
<p>Once registered you will be able to manage your account and view data that your sensors have uploaded</p>
<p>Please use the form below to register:</p>
<form action="" method="POST" >
<fieldset>
<label for="username1">Username: <input name="username" id="username1" type="text" /><br />
<label for="realname">Real name: <input name="realname" id="realname" type="text" /><br />
<label for="email">Email: <input name="email" id="email" type="text" /><br />
<label for="password1">Password: <input name="password" id="password1" type="password"  /><br />
<label for="anti-spam">Anti-Spam: 2 plus 4 is ?<input name="antispam" id="anti-spam" type="text" />
<br />
<input type="hidden" value="register" name="origin" />
<input type="submit" name="submit" value="Register"  >
</fieldset>
</form>
