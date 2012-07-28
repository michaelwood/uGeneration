<?php

require_once('./classes/site_config.class.php');
require_once('./classes/database.class.php');
require_once('./classes/utils.class.php');

session_name('loggedin');
session_start();  

$site_config = new site_config;
$database = new database;
 


switch ($_GET['page']) { 

	case 'logout':
	$content = utils::logout(); 
	break;

	case 'about':
	$content = 'modules/about.php';
	break; 
	
	case 'public':
	$content = 'modules/public.php';
	break; 
	
	case 'register':
	$content = 'modules/register.php';
	break; 

	case 'preferences':
	$content = 'modules/preferences.php';
	break;
	
	case 'mydevices':
	$content = 'modules/mydevices.php';
	break;
		
	case 'mydata':
	$content = 'modules/mydata.php';
	break;	
	
	default: 
	$content = 'blank.html';
	break; 

} 

 


include_once("header.php");
include_once($site_config->get_install_dir()."$content");
include_once("footer.php");

?>