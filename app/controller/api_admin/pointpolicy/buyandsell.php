<?php
class Buyandsell extends JI_Controller {
    public $is_log = 1;

    public function __construct(){
		parent::__construct();
		$this->load("api_admin/g_pointpolicybuyandsell_model",'ppbm_model');
	}

	public function index(){

	}

	public function product_creation(){
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
		$id = 100;
		$code = "EA";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppbm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppbm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function product_creation_perday(){
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
		$id = 112;
		$code = "EM";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppbm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppbm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function product_share_total(){
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
		$id = 102;
		$code = "EC";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppbm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppbm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function product_share(){
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
		$id = 103;
		$code = "ED";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppbm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppbm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function product_qa_reply(){
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
		$id = 101;
		$code = "EB";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppbm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppbm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function product_upload_video(){
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
		$id = 114;
		$code = "EO";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppbm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppbm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function product_upload_video_daily_limit(){
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
		$id = 133;
		$code = "E8";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppbm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppbm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function product_creation_daily_limit(){
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
		$id = 135;
		$code = "E10";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppbm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppbm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
}
