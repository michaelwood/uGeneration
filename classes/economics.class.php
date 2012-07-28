<?php

class economics {

private $database; 
	

private function find_smallest_difference($value,$product_values) {
	//finds the value with the smallest difference and returns the kwh which causes the smallest difference 
		foreach ($product_values as $pvalue) {
		//php doesn't support unsigned int so we need to switch the values around depending on which is largest to get the real difference
			if ($pvalue > $value) { 		
			$diff[$pvalue] = $pvalue - $value;  
			} else { $diff[$pvalue] = $value - $pvalue; }
		}		
		//sort the array in numerical order 
		asort($diff,SORT_NUMERIC);
		foreach (array_keys($diff) as $first) {
					$smallest_diff_caused_by = $first;
					break;
		}	
		return $smallest_diff_caused_by; 
 		
} 
		
	
public function get_mean_kwh($mean,$product_id) {

	$product_info = $this->database->get_products($product_id);

	if ($product_info[0][4] == ""){ //no product formula 

		$product_values = $this->database->product_value_lookup($product_id);
		$nearest_value = $this->find_smallest_difference($mean,$product_values);
		$yeild[Hourly] = $this->database->get_hourly_yield($nearest_value,$product_id);
	}
	else {	$yeild[Hourly] = $product_info[0][4]*$mean; } //formula
	
	$yeild[Daily] = $yeild[Hourly]*24;
	$yeild[Monthly] = $yeild[Daily]*30.5;
	$yeild[Annual] = $yeild[Daily]*365;
		
	return $yeild;
	
}

public function perform_adjustment($mean,$formula) { 

//fix the order of the formula to allow for replace of ^ for pow 

$formula1 = explode('^',$formula);
$exponent = $formula1[1]; 

if ($exponent) {

$formula_exp = explode('*',$formula1[0]);  
if ($formula_exp[1]) {
	$formula = 'pow('.$formula_exp[1].','.$exponent.')*'.$formula_exp[0];
}
}

//replace the token 
$formula = str_replace("[old_mean]",$mean,$formula);
//more tokens could be added here

eval("\$new_mean=" . $formula . ";" ); 

return $new_mean;


}

public function __construct(database $adatabase) {

$this->database = $adatabase; 	

}


} //end class 

?>
