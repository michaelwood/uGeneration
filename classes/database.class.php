<?php

class database {

	protected $db_username = ''; 
	protected $db_name = '';
	protected $db_password = '';
	protected $db_host = '';

public function get_test() {

		mysql_query("SELECT 0");	
}

public function login($user,$pass) {

	$user_lookup = mysql_query("SELECT * FROM users WHERE username = \"$user\" AND password = PASSWORD(\"$pass\")");   
	//check if user exists with those details 	
	if (mysql_num_rows($user_lookup) == 1) {
	//user found, populate $_SESSION 
	$details = mysql_fetch_array($user_lookup, MYSQL_ASSOC);
	$_SESSION['uid'] = $details['id'];			
	$_SESSION['username']  = $details['username']; 
	$_SESSION['realname'] = $details['realname'];
	$_SESSION['email'] = $details['email']; 	
	$_SESSION['loggedin'] = true;
	
			
	return 1;
	} 
	//user doesn't exist
	return 0; 

}

private function table_arrays($mysql_query){
//create a multi dimensional array of the results in the form array[row_n] = array(value1,value2,value3) 
	$query = mysql_query($mysql_query);

	while ($row = mysql_fetch_array($query, MYSQL_NUM)) {
			$table_arrays[] = $row; //automatically creates a new array instance with the array $row 
	}

	return $table_arrays;
} 

public function add_user($username,$realname,$email,$password) {
	if (mysql_num_rows(mysql_query("SELECT username FROM users WHERE username = \"$username\"")) > 0) { return 'Sorry username already exists'; }
	mysql_query("INSERT INTO users (username,realname,email,password) VALUES(\"$username\",\"$realname\",\"$email\",PASSWORD(\"$password\"))");
}

public function get_devices() {

$device = $this->table_arrays("SELECT id,name,datetime FROM devices WHERE owner_id = $_SESSION[uid]"); 
return $device;
}


public function get_sensors($parent_device_id) {
		$sensors = $this->table_arrays("SELECT sensors.id,sensors.sensor_id,sensors.name,sensor_types.name AS sensor_type_name,sensors.location,sensor_types.value_metric FROM sensors INNER JOIN sensor_types on sensors.type_id=sensor_types.id WHERE parent_device_id = $parent_device_id");
		return $sensors;

}

public function add_device($id,$name,$datetime) { 

	mysql_query("INSERT INTO devices (id, owner_id, name, datetime) VALUES ($id, $_SESSION[uid], \"$name\", \"$datetime\")");
	
}

public function delete_by_id($id,$table) {
	mysql_query("DELETE FROM $table WHERE id=$id LIMIT 1");
} 

public function add_sensor($parent_device_id,$id,$name,$location,$type) {

	if (mysql_num_rows(mysql_query("SELECT id FROM sensors WHERE parent_device_id = $parent_device_id")) <= 5 ) {   	
	mysql_query("INSERT INTO sensors (parent_device_id,name,location,type_id,sensor_id) VALUES ($parent_device_id,\"$name\",\"$location\",$type,$id)"); 
	} else { echo '<p class="error">Maximum number of sensors reached</p>'; } 
	
}

public function get_data($parent_sensor_id,$limit,$order_by) {

		$data_array = $this->table_arrays("SELECT id,value,datetime FROM data WHERE parent_sensor_id = $parent_sensor_id  ORDER BY $order_by DESC LIMIT $limit");
		return $data_array; 
}

public function get_mean_value($id) { 

	$result = mysql_query("SELECT value FROM data WHERE parent_sensor_id = $id");
	$total_rows = mysql_num_rows($result);
	while($value = mysql_fetch_array($result, MYSQL_NUM)){
	$meani += $value[0];
	}
	$meani=$meani/$total_rows;

	return round($meani,3);
	

}

public function get_sensor_types($id) {

 if ($id) {
 	$data = $this->table_arrays("SELECT id,name FROM sensor_types WHERE id = $id"); 
 	} else {
	$data = $this->table_arrays("SELECT id,name FROM sensor_types"); 
	}
	
	return $data;  
}

public function get_products($id) {
	if ($id) {
	$products_array = $this->table_arrays("SELECT products.id,manufacturer,model,cost,formula,sensor_types.name AS category_type_name, type_id FROM products INNER JOIN sensor_types on products.type_id=sensor_types.id WHERE products.id = $id");
	} else {
	$products_array = $this->table_arrays("SELECT products.id,manufacturer,model,cost,formula,sensor_types.name AS category_type_name, type_id FROM products INNER JOIN sensor_types on products.type_id=sensor_types.id ");
	} 
	return $products_array;
}

public function product_value_lookup($id) {
		
			$query = mysql_query("SELECT value FROM product_data_lookup WHERE parent_product_id = $id")	;
			$i = 0;
			for ($i=0; $i < mysql_num_rows($query); $i++) {			
			$values[] = mysql_result($query,$i);
			} 
			return $values;
}

public function get_hourly_yield($value,$id) {
		
			$query = mysql_query("SELECT hourly_yeild FROM product_data_lookup WHERE parent_product_id = $id AND value = $value")	;
			$yeild = mysql_result($query,0);
			//$yield = mysql_result($query,0);
			//echo mysql_num_rows($query);
			return $yeild;
}

public function __construct() {

 mysql_connect($this->db_host,$this->db_username,$this->db_password) or die ('Could not connect: ' . mysql_error());
 mysql_select_db($this->db_name);

}

} //end class



?>
