<?php	

$redirect = $_GET['redirect']; 

session_name('loggedin');
session_start();

session_unset();
$_SESSION[loggedin] = false; 
$_SESSION = array(); 

header("location:$redirect"); 

exit();
	
?>