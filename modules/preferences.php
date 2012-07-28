<?php 

utils::please_log_in();

if (isset($_POST['submit'])) {
	if (utils::check_all_fields($_POST)) {

	echo "<p>$return_message</p>"; 
	}
}


?>
<h2>Preferences</h2>
<p>Please use the form below to edit your preferences:</p>
<form action="" method="POST" >
<fieldset>

<label for="realname">Real name:</label><input name="realname" id="realname" type="text" value="<?php echo $_SESSION['realname']; ?>" /><br />

<label for="email">Email address:</label><input name="email" id="email" type="text" value="<?php echo $_SESSION['email']; ?>" />
<br />
<input type="hidden" value="preferences" name="origin" />
<input type="submit" name="submit" value="Save"  >
</fieldset>
</form>
