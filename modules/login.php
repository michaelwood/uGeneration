<?php 

require_once('../classes/database.class.php'); 
session_name('loggedin');
session_start();

$database = new database;
$redirect = $_POST['redirect'];

if (database::login($_POST['username'],$_POST['password']) == 1) {
  
header("location:$redirect");

} else { header("location:$redirect?page=loginfailed"); };

unset($database);

exit();

?>