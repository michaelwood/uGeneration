<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<meta name="author" content="michael"/>
<!-- Michael Wood 2009-2010  -->
<link rel="stylesheet" type="text/css" href="style.css" />
 <!--[if LTE IE 7]>
<style>
.item {
margin: 5px;
padding: 10px;
}

#loginarea {
	position: absolute;
	margin: 50px 5px 0 0;
	left: 700px;
	right: 3px;
	float: right
	text-align: right;
	padding: 2px 0 0 0;
}
</style>
<![EndIf]-->
<title>Microgeneration</title>
</head>
<body>
<div id="container">
<div id="headerblock">
<img src="images/logo.png" alt="MicroGeneration" />
<div id="loginarea">
<?php 
		if ($_GET['page'] == 'loginfailed') { echo 'Login failed'; }
		if (utils::check_logged_in() == 0) {  //if not logged in then provide the login form 
		echo '<form method="post" action="modules/login.php" >
		<label for="username" >Username </label><input type="text" name="username"  id="username" /><br />
		<label for="password" >Password </label><input type="password" name="password" id="password"  /><br />
		<input type="hidden" name="redirect" value="'.$site_config->get_base_url().'" /> 
		<input type="submit" value="login" />
		</form><small>
		<a href="./register" title="register">Register</a></small>';
		 } else {
		  echo "Welcome, $_SESSION[realname]";
		  echo ' <a href="'.$site_config->get_base_url().'modules/logout.php?redirect='.$site_config->get_base_url().'" title="logout" >[logout]</a>'; } 
?>
</div><!-- end loginarea -->
</div> <!-- end headerblock -->
<div id="menuarea" >
 <a href="<?php echo $site_config->get_base_url(); ?>" >Home</a> | 
<a href="./about" >About</a> |
<a href="./public" >Public data</a> |
<?php
//if logged in give more menu items
if (utils::check_logged_in() == 1) {
echo '<a href="./preferences" title="preferences" > Preferences</a> | <a href="./mydata" title="My data" >My data </a> | <a href="./mydevices" title="My devices" >My devices </a>'; 
} 
 
if ($_GET[sub]) { echo '<br /><a href="#">Sub</a> |'; }
?>
</div> <!-- end menuarea -->

<div id="content" >
