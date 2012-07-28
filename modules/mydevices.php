<?php

if(substr($_SERVER[PHP_SELF],-13) == "mydevices.php") { 
//if we're being called directly 
require_once('../classes/utils.class.php');
require_once('../classes/database.class.php');

$database = new database;

if ($_GET['delete']) {
	if	($_GET['sensor_id']) {  $database->delete_by_id($_GET['sensor_id'],'sensors'); }
	if ($_GET['device_id']) {	 
	//should also delete associated sensors	
	$database->delete_by_id($_GET['device_id'],'devices'); }
}

unset($database);
header("location:$_GET[return]");
exit();

}

utils::please_log_in();

if ($_POST && utils::check_all_fields($_POST)) {

	if ($_POST['origin'] == "addsensor") { //parent_device_id,sensor_id,name,location,type
	$database->add_sensor($_POST['parent_device_id'],$_POST['sensor_id'],$_POST['name'],$_POST['location'],$_POST['type']); 
	} 

	if($_POST['origin'] == "adddevice") {//id,name,datetime
	echo '<p>Device added</p>';
	$database->add_device($_POST['identifier'],$_POST['name'],$_POST['date'].' '.$_POST['time']);
	}

}

//as it's the same for all sensors we generate the options list here
$all_sensor_types = $database->get_sensor_types(NULL);
foreach ($all_sensor_types as $sensor_type) {
	$sensor_options .="<option value=\"$sensor_type[0]\">$sensor_type[1]</option>";
}

?>

<h2>My Data logger Devices</h2>
<p>On this page you can manage your devices</p>
<table width="100%">
<tr><th><label for="id">Identifier</label></th><th><label for="name">Name</label></th><th><label for="datetime">Start date time</label></th><th>Sensors</th><th></th></tr>
<tr>
<?php 
// Draw the table data:
$devices = $database->get_devices();
if ($devices) {

foreach ($devices as $device) {
		echo '<tr>';
		foreach ($device as $device_detail) { 
		echo "<td>$device_detail</td>"; 
	}
	echo '<td>
				<table width="100%" class="inner_table"><form action="" method="post">
				<tr><th>Identifier</th><th>Name</th><th>Type</th><th>Location</th><th>Map</th><th> </th></tr>';
				$sensors = $database->get_sensors($device[0]);
				if ($sensors) {
					foreach($sensors as $sensor) {
						echo '<tr>';
						echo "<td>$sensor[1]</td>";
						echo "<td>$sensor[2]</td>";
						echo "<td>$sensor[3]</td>";
						echo "<td>$sensor[4]</td>";
		
						echo "<td><a href=\"http://maps.google.com/?q=$sensor[4]\" target=\"_blank\">Map</a></td>";
						echo '<td><a href="javascript: if (confirm(\'Really Delete?\')) { window.location.href=\'./modules/mydevices.php?delete=1&sensor_id='.$sensor[0].'&return='.$site_config->get_base_url().'mydevices'.'\' } else { void(\'\') }; " title="Delete sensor" ><img border="0" src="./images/trash.png" alt="trash" /></a></td>';

					}
				}		

						echo '<tr>
						<td><input size="5%" type="text" name="sensor_id" id="id" /></td>
						<td><input size="10%" type="text" name="name" id="name"/></td>
						<td><select name="type" id="type">"'.$sensor_options.'"</select></td>
						<td><input type="text" name="location" id="location"/></td>
									<td colspan="2"> </td>
									</tr>
									</table> 
									<input type="hidden" name="parent_device_id" value="'.$device[0].'" />
									<input type="hidden" name="origin" value="addsensor" />
									<span class="right"><small><label for="'.$device[0].'" class="right">Add sensor</label></small><input id="'.$device[0].'" type="image" src="./images/add.png" alt="add sensor" title="Add sensor" ></form></span>		 
									</td> 
									<td><a href="javascript: if (confirm(\'Really Delete?\')) { window.location.href=\'./modules/mydevices.php?delete=1&device_id='.$device[0].'&return='.$site_config->get_base_url().'mydevices'.'\' } else { void(\'\') };" title="Delete device" ><img border="0" src="./images/trash.png" alt="trash" /></a></td>
									</tr>';
}
}
?>
<form action="" method="post">
<tr>
<td><input id="id" type="text" name="identifier" size="11" /></td>
<td><input id="name" name="name" type="text" /></td>
<td><input id="datetime" name="date" type="text" size="6"  /><input size="6" type="text" name="time" id="datetime" /></td>
<td>	<span class="right"><small><label for="adddevice" class="right">Add device</label></small>
<input type="image" src="./images/add.png" alt="add device" title="Add device" value="Submit" id="adddevice">
<input type="hidden" name="origin" value="adddevice" ></span></td>
</tr>
</form>
</table>
