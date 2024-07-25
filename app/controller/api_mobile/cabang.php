<?php
class Cabang extends JI_Controller{

	public function __construct(){
    parent::__construct();
		//$this->setTheme('frontx');
		$this->load("api_mobile/a_company_model","acm");
	}
	public function index(){
		$dt = $this->__init();
		$data = array();

		$apikey = $this->input->get('apikey');

		//by Donny Dennison - 18 august 2020 11:25
		// fix some missing apikey didnt return status 400
		// $this->status = 199;
		$this->status = 400;

		$this->message = 'Missing or invalid apikey';
		$c = $this->apikey_check($apikey);
		$data['cabang'] = array();
		if($c){
			$this->status = 200;
			$this->message = 'Success';
			$data['cabang'] = $this->acm->get('cabang');
		}
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cabang");
	}
	public function pusat(){
		$dt = $this->__init();
		$data = array();

		$apikey = $this->input->get('apikey');

		//by Donny Dennison - 18 august 2020 11:25
		// fix some missing apikey didnt return status 400
		// $this->status = 199;
		$this->status = 400;

		$this->message = 'Missing or invalid apikey';
		$c = $this->apikey_check($apikey);
		$data['pusat'] = array();
		if($c){
			$this->status = 200;
			$this->message = 'Success';
			$data['pusat'] = $this->acm->get('pusat');
		}
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "cabang");
	}
}
