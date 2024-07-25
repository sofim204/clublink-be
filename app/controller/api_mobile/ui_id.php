<?php
class Ui_id extends JI_Controller{

	public function __construct(){
  		parent::__construct();

    	$this->lib("seme_log");
		//$this->setTheme('frontx');
		$this->load("api_mobile/a_ui_id_model","auim");

	}

	public function baru(){
		$dt = $this->__init();
		$data = new stdClass();

		$ui_id = $this->input->post('ud_id');

		if(!$ui_id){
			$this->status = 1001;
			$this->message = 'Missing ui_id';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "alamat");
			die();
		}

		$checkUIID = $this->auim->check($ui_id);
		if(isset($checkUIID->ui_id)){
			$this->status = 200;
			$this->message = 'Success';
			$data = $checkUIID;
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "alamat");
			die();
		}

		$nama = $this->input->post('nama');

		$last_id = $this->auim->getLastId();

		$di= array();
		$di["id"] = $last_id;
		$di["ui_id"] = $ui_id;
		$di["nama"] = $nama;
		$this->auim->set($di);

		$data = $di;

		$this->status = 200;
		$this->message = 'Success';
		// $this->__json_out($data);
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "alamat");
		die();
	}

	public function check(){
    	//init
		$dt = $this->__init();

    	//default result format
		$data = new stdClass();

		$ui_id = $this->input->post('ui_id');

		if(!$ui_id){
			$this->status = 1002;
			$this->message = 'Missing ud_id / ud_id not exist in db';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "alamat");
			die();
		}

		$checkUIID = $this->auim->check($ui_id);
		if(!isset($checkUIID->ui_id)){
			$this->status = 1002;
			$this->message = 'Missing ud_id / ud_id not exist in db';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "alamat");
			die();
		}

		$data = $checkUIID;

		//default response
		$this->status = 200;
		$this->message = "Success";
		//render
		$this->__json_out($data);
	}

}
