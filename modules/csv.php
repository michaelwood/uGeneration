<?php

header('Content-Type:text/csv');
header('Content-Disposition: attachment; filename="Data_'.$_GET[name].$_GET[id].'_'.date("d-m-y").'.csv"');
include_once('../classes/utils.class.php');

if ($_GET[key] != "jiosdi90") { exit(); }

include_once('../classes/database.class.php'); 

$database = new database(); 
$data = $database->get_data($_GET[id],9999999999,"datetime");

echo "date time,value";
echo "\n";

foreach ($data as $row){

echo $row[2];
echo ",";
echo $row[1];
echo "\n";

}


?>
