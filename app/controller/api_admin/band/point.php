<?php
class Point extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->lib("seme_purifier");
		// $this->load("api_admin/h_ticket_shop_model",'htsm');
		$this->current_parent = 'band';
		$this->current_page = 'band_point';
		$this->load("api_admin/i_group_point_policy_model","igppm");
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

	// ====== START Point Policy ==================
	// by Yopie Hidayat 22 January 2024 10:48 | 

	// ======= Create Club ==============
	public function create_club() {
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
		$id = 143;
		$code = "E17";
		$classified = "leaderboard_point";
		$codename = 'Club - Create club';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->igppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->igppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->igppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	// ======= END Create Club ==============

	// ======= Minimum Members ==============
	public function minimum_members() {
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
		$id = 144;
		$code = "E18";
		$classified = "leaderboard_point";
		$codename = 'Club - Minimum no. of members for a club is X to give SPT to the post creator(create post, word, photo, video)';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->igppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->igppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->igppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	// ======= END Minimum Members ==============

	// ======= Club - Create post(word) ==============
	public function create_post_word() {
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
		$id = 145;
		$code = "E19";
		$classified = "leaderboard_point";
		$codename = 'Club - Create post(word)';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->igppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->igppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->igppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	// ======= END Club - Create post(word) ==============

	// ======= Club - Create post additional(photo) ==============
	public function create_post_additional_photo() {
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
		$id = 146;
		$code = "E20";
		$classified = "leaderboard_point";
		$codename = 'Club - Create post additional(photo)';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->igppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->igppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->igppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	// ======= END Club - Create post additional(photo) ==============

	// ======= Club - Create post additional(video) ==============
	public function create_post_additional_video() {
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
		$id = 147;
		$code = "E21";
		$classified = "leaderboard_point";
		$codename = 'Club - Create post additional(video)';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->igppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->igppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->igppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	// ======= END Club - Create post additional(video) ==============

	// ======= Club - Create post additional(attendance sheet) ==============
	public function create_post_additional_attendance_sheet() {
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
		$id = 148;
		$code = "E22";
		$classified = "leaderboard_point";
		$codename = 'Club - Create post additional(attendance sheet)';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->igppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->igppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->igppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	// ======= END Club - Create post additional(attendance sheet) ==============

	// ======= Club - Create post additional(location) ==============
	public function create_post_additional_location() {
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
		$id = 149;
		$code = "E23";
		$classified = "leaderboard_point";
		$codename = 'Club - Create post additional(location)';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->igppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->igppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->igppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	// ======= END Club - Create post additional(location) ==============

	// ======= Club - Like post ==============
	public function like_post() {
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
		$id = 150;
		$code = "E24";
		$classified = "leaderboard_point";
		$codename = 'Club - Like post';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->igppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->igppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->igppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	// ======= END Club - Like post ==============

	// ======= Club - Comment post ==============
	public function comment_post() {
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
		$id = 151;
		$code = "E25";
		$classified = "leaderboard_point";
		$codename = 'Club - Comment post';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->igppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->igppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->igppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	// ======= END Club - Comment post ==============

	// ======= Club - Minimum video length to get spt ==============
	public function minimum_video_length() {
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
		$id = 152;
		$code = "E26";
		$classified = "leaderboard_point";
		$codename = 'Club - Minimum video length to get spt';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->igppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->igppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->igppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	// ======= END Club - Minimum video length to get spt ==============

	// ======= Club - Play/watch video ==============
	public function play_watch_video() {
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
		$id = 153;
		$code = "E27";
		$classified = "leaderboard_point";
		$codename = 'Club - Play/watch video';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->igppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->igppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->igppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	// ======= END Club - Play/watch video ==============

	// ======= Club - Invite member join club ==============
	public function invite_member() {
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
		$id = 154;
		$code = "E28";
		$classified = "leaderboard_point";
		$codename = 'Club - Invite member join club';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->igppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->igppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->igppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	// ======= END Club - Invite member join club ==============

	// ======= Club - Member join club ==============
	public function member_join_club() {
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
		$id = 155;
		$code = "E29";
		$classified = "leaderboard_point";
		$codename = 'Club - Member join club';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->igppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->igppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->igppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	// ======= END Club - Member join club ==============

	// ======= Club - Max club created each day ==============
	public function max_club_created_each_day() {
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
		$id = 156;
		$code = "E30";
		$classified = "leaderboard_point";
		$codename = 'Club - Max club created each day';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->igppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->igppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->igppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	// ======= END Club - Max club created each day ==============
	

	public function club_owner_got_commission_if_member_create_post() {
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
		$id = 158;
		$code = "E32";
		$classified = "leaderboard_point";
		$codename = 'Club - Owner group commission in % if member create post (text, photo, video)';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->igppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->igppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->igppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function limit_create_club() {
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
		$id = 159;
		$code = "E33";
		$classified = "leaderboard_point";
		$codename = 'Club - Create club limit';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->igppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->igppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->igppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	
	// ====== END Point Policy ==================
}
