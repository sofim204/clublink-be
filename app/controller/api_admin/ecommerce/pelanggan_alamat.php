<?php
class Pelanggan_Alamat extends JI_Controller{
  public function __construct(){
    parent::__construct();
    $this->load("api_admin/b_user_alamat_model",'buam');
  }
  public function index(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
  }
  public function list($b_user_id){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
    $this->status = 200;
    $this->message = 'Success';
    $nation_code = $d['sess']->admin->nation_code;
    $data = $this->buam->getByUserId($nation_code, $b_user_id);
    $this->__json_out($data);
  }
}
