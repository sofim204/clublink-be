<?php
class Exception_user_force_id extends JI_Controller{

	public function __construct(){
  		parent::__construct();

    	$this->lib("seme_log");
		//$this->setTheme('frontx');
		$this->load("api_mobile/a_exception_user_force_id_model","aeufim");

	}

	// public function baru(){
	// 	$dt = $this->__init();
	// 	$data = new stdClass();

	// 	$ui_id = $this->input->post('ud_id');

	// 	if(!$ui_id){
	// 		$this->status = 1001;
	// 		$this->message = 'Missing ui_id';
	// 		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "exception_user_force_id");
	// 		die();
	// 	}

	// 	$checkUIID = $this->auim->check($ui_id);
	// 	if(isset($checkUIID->ui_id)){
	// 		$this->status = 200;
	// 		$this->message = 'Success';
	// 		$data = $checkUIID;
	// 		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "exception_user_force_id");
	// 		die();
	// 	}

	// 	$nama = $this->input->post('nama');

	// 	$last_id = $this->auim->getLastId();

	// 	$di= array();
	// 	$di["id"] = $last_id;
	// 	$di["ui_id"] = $ui_id;
	// 	$di["nama"] = $nama;
	// 	$this->auim->set($di);

	// 	$data = $di;

	// 	$this->status = 200;
	// 	$this->message = 'Success';
	// 	// $this->__json_out($data);
	// 	$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "exception_user_force_id");
	// 	die();
	// }

	public function check(){
    	//init
		$dt = $this->__init();

    	//default result format
		$data = new stdClass();

		$id = $this->input->post('id');
		if(!$id){
			$this->status = 1001;
			$this->message = 'Missing id / id not exist in db';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "exception_user_force_id");
			die();
		}

		$existInDB = $this->aeufim->check($id);
		if(!isset($existInDB->id)){
			$this->status = 1002;
			$this->message = 'user not in exception';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "exception_user_force_id");
			die();
		}

		$data = $existInDB;

		$this->status = 200;
		$this->message = "Success";

		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "exception_user_force_id");
	}
}
