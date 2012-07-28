<?php

class site_config {

protected $base_url = 'http://example.com/';
protected $install_dir = '/var/www/example.com';


public function get_base_url() {
		return $this->base_url; 
		} 
public function get_install_dir() {
		return $this->install_dir;
		}

};



?>
