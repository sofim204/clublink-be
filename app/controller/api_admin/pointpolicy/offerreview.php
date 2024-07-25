<?php
class Offerreview extends JI_Controller {

    public function __construct(){
		parent::__construct();
		$this->load("api_admin/common_code_model",'ccm');
	}

	public function index(){

	}

	public function eq(){
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
		$id = 116;
		$code = "EQ";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ccm->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ccm->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function er(){
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
		$id = 117;
		$code = "ER";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ccm->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ccm->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function es(){
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
		$id = 118;
		$code = "ES";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ccm->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ccm->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function et(){
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
		$id = 119;
		$code = "ET";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ccm->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ccm->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

}
