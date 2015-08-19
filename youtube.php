<?php
include_once plugin_dir_path(__FILE__).'/lib/Youtube.php';


	
class Youtube_Flux{
	public function __construct(){
		$youtube = new Youtube();
		
		var_dump($youtube->get_videoList());
	}
}

?>