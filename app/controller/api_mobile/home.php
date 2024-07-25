<?php
class Home extends JI_Controller{

	public function __construct(){
    parent::__construct();
		//$this->setTheme('frontx');
	}
	public function index(){
		$this->status = '404';
		header("HTTP/1.0 404 Not Found");
		$data = array();
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "home");
	}
}
