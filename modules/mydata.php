<?php

utils::please_log_in();

$selected_sensor[id] = -1;
$selected_sensor[name] = 'Please select a sensor';

if($_POST[submit]) { 
//so we don't have to query this information again from the database and set them to some readable variables 
$selected_sensor = explode(",",$_POST['selected_sensor']);
$selected_sensor[id] = $selected_sensor[0]; 
$selected_sensor[parent_device_name] = $selected_sensor[1];
$selected_sensor[name] = $selected_sensor[2];
$selected_sensor[unit] = $selected_sensor[3];


$selected_product = explode(",",$_POST['selected_product']);
$selected_product[id] = $selected_product[0];
$selected_product[manufacturer] = $selected_product[1];
$selected_product[model] = $selected_product[2];
$selected_product[cost] = $selected_product[3];
$selected_product[formula] = $selected_product[4];

require_once($site_config->get_install_dir().'classes/economics.class.php'); 
$economics = new economics($database); //we need to use database functions in economics class so we're passing it to it

if ($_POST[adjustment_formula]) {
$new_mean = $economics->perform_adjustment($_POST[old_mean],$_POST[adjustment_formula]);  
}
}


if ($_POST[ppkwh]) {
$ppkwh = $_POST[ppkwh]; 
} else { $ppkwh = 0.30; }

if ($_POST[cost]) {
//over ride database entry for cost
$selected_product[cost] = $_POST[cost]; 
}

if ($_POST[limit]) {
$limit = $_POST[limit]; 
} else { $limit = 100; }

?>
<!--[if IE]><script language="javascript" type="text/javascript" src="./flot/excanvas.min.js"></script><![endif]-->
<!-- Javascript Flot - Copyright (c) 2007-2009 IOLA and Ole Laursen -->
<script language="javascript" type="text/javascript" src="./flot/jquery.js"></script>
<script language="javascript" type="text/javascript" src="./flot/jquery.flot.js"></script>
<script language="javascript" type="text/javascript" src="./flot/jquery.flot.selection.js"></script>


<link href="./modules/rss.xml.php" type="application/atom+xml" rel="alternate" title="My Data ATOM Feed" />
<h2>Sensor Data</h2>
<form action="" method="post">
<label for="yourdevices" >Your devices</label><select id="yourdevices" name="selected_sensor">

<?php 

if ($_POST[submit]) { //hacky way to put current viewing sensor at the top of the list 
	echo "<option value=\"$selected_sensor[id],$selected_sensor[parent_device_name],$selected_sensor[name]\" >$selected_sensor[parent_device_name] - $selected_sensor[name]</option>";
	}

$devices = $database->get_devices();
if ($devices) {

//"You devices option"
foreach ($devices as $device) {
	$sensors = $database->get_sensors($device[0]); 	
	if ($sensors) {
		foreach($sensors as $sensor) { 
		if ($sensor[0] != $selected_sensor[id]) { 
		echo "<option value=\"$sensor[0],$device[1],$sensor[2],$sensor[5]\" > $device[1] - $sensor[2]</option>";
		}
		} 
	}

} 
} else { echo '<p class="error">Please go to "My devices" and define at least one device with some sensors</p>'; }

?>
</select> 
<label for="product">Product to estimate against</label>
<select id="product" name="selected_product" >
<?php
if ($_POST[submit]){ echo "<option value=\"$selected_product[0],$selected_product[1],$selected_product[2],$selected_product[3],$selected_product[4],$selected_product[5]\">$selected_product[1] $selected_product[2] - $selected_product[5]</option>\n"; }
foreach ($database->get_products() as $product_details) {
	if ($product_details[0] != $selected_product[0]) {
 	echo "<option value=\"$product_details[0],$product_details[1],$product_details[2],$product_details[3],$product_details[4],$product_details[5]\">$product_details[1] $product_details[2] - $product_details[5]</option>\n";
 	} 
}


?>
</select>

<input type="submit" value="View" name="submit" ><br />
</form><?php if(!$_POST[submit]) { utils::close_page(); } ?>
<h3><?php echo "$selected_sensor[parent_device_name] - $selected_sensor[name]" ?></h3><a href="./modules/csv.php?key=jiosdi90&id=<?php echo $selected_sensor[id]; ?>&name=<?php echo $selected_sensor[name]; ?>" title="export data as csv">Export as CSV</a>
<table width="100%"><tr>
<!-- columns table -->
<td><table>
<th>Value <?php echo $selected_sensor[unit]; ?></th><th>Date Time</th>
<?php 

$num_rows = 0;
	//$sensor_id,$limit,$order_by
	$data = $database->get_data($selected_sensor[id],$limit,"datetime");
if($data) {

foreach ($data as $row) {
	echo '<tr>';
		// for input to Flot grapher 
		$graph_csv .= '['.strtotime($row[2])*1000;
		$graph_csv .= ','.$row[1].'],'; 

		echo "<td>".round($row[1],3)."</td>";
		echo "<td>$row[2]</td>";  
	echo '</tr>';
}

} //end if data
?>
</table></td>

<td><table class="inner_table">
<tr>
<th>Metrics</th><th>Value</th>
</tr>
<tr><td>Mean <?php echo $selected_sensor[unit]; ?></td><td>

<?php 

if ($new_mean) { 
 	$old_mean = $database->get_mean_value($selected_sensor[id]);
	$mean = $new_mean; 
	} else { 
	  $mean = $database->get_mean_value($selected_sensor[id]);;
	  $old_mean = $mean; 
}   
 	echo  round($mean,3);
 ?>
 </td></tr>
<tr><td colspan="2"><b>Mean kWh</b></td><?php $mean_kwh = $economics->get_mean_kwh($mean,$selected_product[id]);

foreach($mean_kwh as $key => $val) {

echo "<tr><td>$key</td><td>".round($val,2)."</td></tr>";

}
echo '<tr><td colspan="2"><b>Income</b></td></tr>';
echo "<tr><td >Annual income</td><td>&pound;".round($annual_income = $mean_kwh[Annual]*$ppkwh, 2)."</td></tr>";
if ($annual_income == 0) {
$breakeven = "NEVER";
} else { $breakeven = ceil($selected_product[cost]/$annual_income); } 
echo "<tr><td>Years until break even</td><td>".$breakeven."</td></tr>";
echo "<tr><td>Product cost</td><td>&pound;".$selected_product[cost]."</td></tr>";
 ?>
 </table></td>
 <td>
 
<table><form action="" method="POST">
<tr><td><b>Manual Adjustments</b></td></tr>
<tr><td><label for="adjustments">New Mean = </label>
<textarea id="adjustments" name="adjustment_formula"><?php if ($_POST['adjustment_formula']) { echo $_POST['adjustment_formula']; } else {echo '[old_mean]'; } ?> </textarea>
<p>e.g. [old_mean]*(15/[old_mean])^0.2 would adjust the mean windspeed to 15m</p>
<label for="ppkwh">Price Per kWh </label><input type="text" name="ppkwh" size="5%" id="ppkwh" value="<?php echo $ppkwh; ?>" />
<label for="cost">Product Cost</label><input size="6%" type="text" name="cost" id="cost" value="<?php echo $selected_product['cost']; ?>" />
<input type="hidden" name="selected_sensor" value="<?php echo $_POST['selected_sensor']; ?>" />
<input type="hidden" name="selected_product" value="<?php echo $_POST['selected_product']; ?>" />
<input type="hidden" name="old_mean" value="<?php echo $old_mean; ?>" />
<br /><input type="submit" value="recalculate" name="submit" />
<br />
<?php  ?>
</td></tr>
<!-- Graph -->
<tr><td><b>Historical readings</b></td></tr>
<tr><td><div id="placeholder" style="width:500px;height:300px;"> </div> 
 <div id="overview" style="margin-left:50px;margin-top:20px;width:400px;height:50px"></div></td>

</tr><tr><td><label for="limit">Max sample size </label><input type="text" id="limit" size="5" name="limit" value="<?php echo $limit; ?>"  />
<input type="submit" name="submit" value="redraw" />
</form></td></tr>
<!-- END Graph -->
</table>
</td>
</tr>
</table><!-- END columns table -->
<!-- START Javascript Flot - Copyright (c) 2007-2009 IOLA and Ole Laursen until end comment-->
<script id="source" language="javascript" type="text/javascript">
$(function () {

var d = [<?php echo $graph_csv; ?>];

    function weekendAreas(axes) {
        var markings = [];
        var d = new Date(axes.xaxis.min);
        // go to the first Saturday
        d.setUTCDate(d.getUTCDate() - ((d.getUTCDay() + 1) % 7))
        d.setUTCSeconds(0);
        d.setUTCMinutes(0);
        d.setUTCHours(0);
        var i = d.getTime();
        do {
            // when we don't set yaxis, the rectangle automatically
            // extends to infinity upwards and downwards
            markings.push({ xaxis: { from: i, to: i + 2 * 24 * 60 * 60 * 1000 } });
            i += 7 * 24 * 60 * 60 * 1000;
        } while (i < axes.xaxis.max);

        return markings;
    }
    
    var options = {
        xaxis: { mode: "time" },
        selection: { mode: "x" },
        series: { 
        			color: 3
        				},
        grid: { markings: weekendAreas }
    };
    
    var plot = $.plot($("#placeholder"), [d], options);
    
    var overview = $.plot($("#overview"), [d], {
        series: {
            lines: { show: true, lineWidth: 1 },
            shadowSize: 0,
            color: 3
        },
        xaxis: { ticks: [], mode: "time" },
        yaxis: { ticks: [], min: 0, autoscaleMargin: 0.1 },
        selection: { mode: "x" }
    });

    // now connect the two
    
    $("#placeholder").bind("plotselected", function (event, ranges) {
        // do the zooming
        plot = $.plot($("#placeholder"), [d],
                      $.extend(true, {}, options, {
                          xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
                      }));

        // don't fire event on the overview to prevent eternal loop
        overview.setSelection(ranges, true);
    });
    
    $("#overview").bind("plotselected", function (event, ranges) {
        plot.setSelection(ranges);
    });
});
<!-- END Javascript Flot - Copyright (c) 2007-2009 IOLA and Ole Laursen -->
</script>
