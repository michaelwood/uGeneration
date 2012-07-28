<?php
if (!$_POST['deviceid']) { exit(); }
if (!$_POST['sensorid']) { exit(); }

require_once('../classes/database.class.php');
require_once('../classes/utils.class.php');

$database = new database;

//make a manual adjustment to run tests converts ADREL to volts 
if ($_POST[deviceid] == 5462) { $_POST[data] = $_POST[data]/19820.61; }

if (mysql_num_rows(mysql_query("SELECT id FROM devices WHERE id = $_POST[deviceid]")) == 1) {
// devicekey match 
//echo "data=$windspeed&deviceid=$deviceid&sensorid=$sensorid"

mysql_query("INSERT INTO data (value,datetime,parent_sensor_id) VALUES ($_POST[data],NOW(),$_POST[sensorid])");

}  

?>
