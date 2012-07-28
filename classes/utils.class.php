<?php 

//require_once('./classes/site_config.class.php');
//$site_config = new site_config; 

class utils {

public static function check_logged_in() {
//check to see if a user is logged in or not

	if ($_SESSION[loggedin] == true) {
	return 1;	
	} else { return 0; }
}

public static function logout() {
	//session_name('loggedin');
//	session_start();
	session_unset();
	$_SESSION = array(); 
	return  'blank.html';

}


public static function please_log_in() {
	
	if ($_SESSION[loggedin] != true) {
	echo '<p>Please log in</p>'; require_once('./footer.php'); exit(); 
	}
} 

public static function close_page() {
	require_once('./footer.php'); exit(); 
}


public static function check_all_fields($fields) {

	foreach ($fields as $field) { 
		if($field == "") { 
			echo '<p class="error">Please fill in all the fields</p>';
			return 0;
			break;
		} 
	}  return 1;
}

} //end class 

?>