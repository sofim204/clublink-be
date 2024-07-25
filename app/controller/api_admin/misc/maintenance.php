<?php
class Maintenance extends JI_Controller{

	public function __construct(){
		parent::__construct();
		$this->load("api_admin/g_maintenance_model", 'maintenance_model');
	}

	public function app_config(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
		$this->status = 400;
		$this->message = 'Authorization required';
		header("HTTP/1.0 400 Authorization required");
		$this->__json_out($data);
		die();
		}
		$nation_code = $d['sess']->admin->nation_code;

		//declare config variable
		$id = 62;
		$code = "C2";
		$classified = "app_config";
		$codename = 'maintenance';
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->maintenance_model->getByClassifiedAndCode($nation_code,$classified,$code);
		if(isset($config->remark)){
		$du = array();
		$du['remark'] = $remark;
		$this->maintenance_model->update($nation_code,$id,$du);
		}else{
		$di = array();
		$di['nation_code'] = $nation_code;
		$di['id'] = $id;
		$di['classified'] = $classified;
		$di['code'] = $code;
		$di['codename'] = $codename;
		$di['remark'] = $remark;
		$this->maintenance_model->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
}
