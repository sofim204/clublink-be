<?php
class Community extends JI_Controller {
    public $is_log = 1;

    public function __construct(){
		parent::__construct();
		$this->load("api_admin/g_pointpolicycommunity_model",'ppcm_model');
	}

	public function index(){

	}

	public function community_creation_total(){
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
		$id = 104;
		$code = "EE";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppcm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppcm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function community_creation_first_time(){
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
		$id = 105;
		$code = "EF";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppcm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppcm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function community_creation(){
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
		$id = 106;
		$code = "EG";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppcm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppcm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function community_creation_perday(){
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
		$id = 111;
		$code = "EL";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppcm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppcm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function community_reply_total(){
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
		$id = 107;
		$code = "EH";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppcm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppcm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function community_reply(){
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
		$id = 108;
		$code = "EI";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppcm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppcm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function community_like_total(){
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
		$id = 109;
		$code = "EJ";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppcm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppcm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function community_like(){
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
		$id = 110;
		$code = "EK";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppcm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppcm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function community_group_chat_more_than_equal(){
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
		$config = $this->ppcm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppcm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function community_group_chat_get_point(){
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
		$id = 113;
		$code = "EN";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppcm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppcm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function community_upload_video(){
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
		$id = 115;
		$code = "EP";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppcm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppcm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function community_share_total(){
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
		$id = 122;
		$code = "EW";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppcm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppcm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function community_share(){
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
		$id = 123;
		$code = "EX";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppcm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppcm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	
	public function community_upload_video_daily_limit(){
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
		$id = 134;
		$code = "E9";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppcm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppcm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function community_upload_image(){
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
		$id = 139;
		$code = "E13";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppcm_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppcm_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
}
