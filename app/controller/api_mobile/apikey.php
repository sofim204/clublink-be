<?php
class Apikey extends JI_Controller{
	public function __construct(){
    	parent::__construct();
		//$this->setTheme('frontx');
		$this->load("api_mobile/a_apikey_model","aakm");
	}
	public function index(){
		$this->status = 200;
    	$this->message = 'Success';
		$data = array();
		// $this->__json_out($data);
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "apikey");
	}
	public function auth(){
		$data = array();
    	$data['apikey_plain'] = '';
		$nation_code = $this->input->request('nation_code');
		$username = $this->input->request('username');
		$password = $this->input->request('password');

		$enc = mb_detect_encoding($username,'UTF-8, ISO-8859-1');

		if(mb_strlen($username)<=0){
			$this->status = 352;
	    	$this->message = 'Invalid username';
			// $this->__json_out($data);
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "apikey");
		}

		if(mb_strlen($password)<=0){
			$this->status = 351;
	    	$this->message = 'Invalid password';
			// $this->__json_out($data);
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "apikey");
		}

		$this->status = 350;
    	$this->message = 'invalid username or password';

		$d = $this->aakm->auth($nation_code,$username,$password);
		if(isset($d->str)){
			$this->status = 200;
	    	$this->message = 'Success';
	    	$data['apikey_plain'] = $d->str;
		}

		// $this->__json_out($data);
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "apikey");
	}
	public function convert(){
		$this->status = 200;
		$this->message = 'Success';
		$data = array();
		$apikey = $this->input->request('apikey');
		$data['plain'] = $apikey;
		$data['apikey'] = hash('sha256',$apikey);
		// $this->__json_out($data);
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "apikey");
	}
}
