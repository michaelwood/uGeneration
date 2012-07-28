<?php 

header("Content-Type:text/xml"); 

require_once('../classes/site_config.class.php');
require_once('../classes/database.class.php'); 

if ($_GET['key'] != "54554594231515") { exit(); }  

$site_config = new site_config; 
$database = new database;

$selected_sensor_id = $_GET[sid];   

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<rss version="2.0" xmlns:blogChannel="http://backend.userland.com/blogChannelModule" xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#">
<channel>
<title>MicroGen Data feed</title>
<link><?php echo $site_config->get_base_url(); ?></link>
<description>MicroGen sensor readings</description>
<guid><?php echo $site_config->get_base_url(); ?></guid>
<generator uri="<?php echo $site_config->get_base_url(); ?>">MicroGen RSS by Michael Wood</generator>
<webMaster>ee06mmw@brunel.ac.uk</webMaster>	
	
<?php 	
	//$sensor_id,$limit,$order_by

	array ($data);
	$data = $database->get_data($selected_sensor_id,1,"datetime");
if($data) {

foreach ($data as $row) {
	   echo '<item>';
		echo '<title>Latest reading from sensor</title>';
		echo '<pubDate>'.substr($row[2],0,-9).' '.substr($row[2],11).' +0000</pubDate>';
		echo "<description>$row[1]</description>"; 
		echo '</item>';
}
}
?>
</channel>
</rss>
<?php unset($site_config);
unset($database); ?>