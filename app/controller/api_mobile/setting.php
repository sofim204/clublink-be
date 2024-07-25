<?php
class Setting extends JI_Controller{

	public function __construct(){
    parent::__construct();
    $this->load("api_mobile/b_user_model","bu");
    $this->load("api_mobile/common_code_model","ccm");
    $this->load("api_mobile/b_user_setting_model","busm");
		//$this->setTheme('frontx');
	}
	public function index(){
		$this->status = '404';
		header("HTTP/1.0 404 Not Found");
		$data = array();
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "setting");
    die();
	}

	public function notification(){
		//initial
		$dt = $this->__init();

		//default result
		$data = array();
		$data['settings'] = new stdClass();
		$data['settings']->general = array();
		$data['settings']->buyer = array();
		$data['settings']->seller = array();

    //check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
    if(empty($nation_code)){
      $this->status = 101;
  		$this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "setting");
      die();
    }

		//check apikey
		$apikey = $this->input->get('apikey');
		$c = $this->apikey_check($apikey);
		if(!$c){
			$this->status = 400;
			$this->message = 'Missing or invalid API key';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "setting");
			die();
		}

		//check apisess
		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if(!isset($pelanggan->id)){
			$this->status = 401;
			$this->message = 'Missing or invalid API session';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "setting");
			die();
		}
		//get form database
		$settings = $this->ccm->getNotificationSetting($nation_code);
    $user_values = $this->busm->getNotificationValue($nation_code, $pelanggan->id);

		//manipulator
		$user = new stdClass();

		if($user_values != NULL){
			foreach($user_values as $uv){
				// $idx = $uv->classified.'|'.$uv->code;
				if(!isset($user->{$uv->classified})) $user->{$uv->classified} = new stdClass();
				if(!isset($user->{$uv->classified}->{$uv->code})) $user->{$uv->classified}->{$uv->code} = '';
				$user->{$uv->classified}->{$uv->code} = $uv->setting_value;
			}
			unset($user_values);
			unset($uv);
		}
		//put user config to list
		foreach($settings as &$st){
			$classified = $st->classified;
			if(isset($user->{$classified}->{$st->code})){
				$st->user_value = $user->{$st->classified}->{$st->code};
			}else{
				$st->user_value = '';
			}
			if($st->user_value==""){
				$dix = array();
				$dix['nation_code'] = $nation_code;
				$dix['b_user_id'] = $pelanggan->id;
				$dix['classified'] = $st->classified;
				$dix['code'] = $st->code;
				$dix['setting_value'] = "1";
				$check = $this->busm->check($nation_code,$pelanggan->id,$st->classified,$st->code);
				if(empty($check)){
					$dix['id'] = (int) $this->busm->getLastId($nation_code,$pelanggan->id);
					$this->busm->set($dix);
				}
				$st->user_value = "1";
			}
			//grouping list
			if($classified == 'setting_notification_buyer'){
				$data['settings']->buyer[] = $st;
			}else if($classified == 'setting_notification_seller'){
				$data['settings']->seller[] = $st;
			}else{
				$data['settings']->general[] = $st;
			}
		}

    $this->status = 200;
    $this->message = 'Success';
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "setting");
	}

	public function notificationcustom($nation_code, $apikey, $apisess){
		//initial
		$dt = $this->__init();

		//default result
		// $data = array();

    //check nation_code
		$nation_code = $this->nation_check($nation_code);
    if(empty($nation_code)){
    	return (object) array(
	    	'status' => 101,
	    	'message' => 'Missing or invalid nation_code'
	    );
    }

		//check apikey
		$c = $this->apikey_check($apikey);
		if(!$c){
    	return (object) array(
	    	'status' => 400,
	    	'message' => 'Missing or invalid API key'
	    );
		}

		//check apisess
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if(!isset($pelanggan->id)){
    	return (object) array(
	    	'status' => 401,
	    	'message' => 'Missing or invalid API session'
	    );
		}
		//get form database
		$settings = $this->ccm->getNotificationSetting($nation_code);
    $user_values = $this->busm->getNotificationValue($nation_code, $pelanggan->id);

		//manipulator
		$user = new stdClass();

		if($user_values != NULL){
			foreach($user_values as $uv){
				// $idx = $uv->classified.'|'.$uv->code;
				if(!isset($user->{$uv->classified})) $user->{$uv->classified} = new stdClass();
				if(!isset($user->{$uv->classified}->{$uv->code})) $user->{$uv->classified}->{$uv->code} = '';
				$user->{$uv->classified}->{$uv->code} = $uv->setting_value;
			}
			unset($user_values);
			unset($uv);
		}
		//put user config to list
		foreach($settings as &$st){
			$classified = $st->classified;
			if(isset($user->{$classified}->{$st->code})){
				$st->user_value = $user->{$st->classified}->{$st->code};
			}else{
				$st->user_value = '';
			}
			if($st->user_value==""){
				$dix = array();
				$dix['nation_code'] = $nation_code;
				$dix['b_user_id'] = $pelanggan->id;
				$dix['classified'] = $st->classified;
				$dix['code'] = $st->code;
				$dix['setting_value'] = "1";
				$check = $this->busm->check($nation_code,$pelanggan->id,$st->classified,$st->code);
				if(empty($check)){
					$dix['id'] = (int) $this->busm->getLastId($nation_code,$pelanggan->id);
					$this->busm->set($dix);
				}
				$st->user_value = "1";
			}
		}

    return (object) array(
    	'status' => 200,
    	'message' => 'Success'
    );

	}

  public function notification_change(){
		//initial
		$dt = $this->__init();

		//default result
		$data = array();
		$data['settings'] = new stdClass();
		$data['settings']->general = array();
		$data['settings']->buyer = array();
		$data['settings']->seller = array();

    //check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
    if(empty($nation_code)){
      $this->status = 101;
  		$this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "setting");
      die();
    }

		//check apikey
		$apikey = $this->input->get('apikey');
		$c = $this->apikey_check($apikey);
		if(!$c){
			$this->status = 400;
			$this->message = 'Missing or invalid API key';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "setting");
			die();
		}

		//check apisess
		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if(!isset($pelanggan->id)){
			$this->status = 401;
			$this->message = 'Missing or invalid API session';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "setting");
			die();
		}

		//check setting_code
		$classified = $this->input->post('classified');
		if(empty($classified)) $classified = '';
		if(strlen($classified)<=0){
			$this->status = 2100;
			$this->message = 'Missing or invalid classified';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "setting");
			die();
		}

		//check code
		$code = $this->input->post('code');
		if(empty($code)) $code = '';
		if(strlen($code)<=0 || strlen($code)>2){
			$this->status = 2101;
			$this->message = 'Missing or invalid code';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "setting");
			die();
		}

		//check $setting object
		$setting = $this->ccm->getByClassifiedAndCode($nation_code,$classified,$code);
		if(!isset($setting->id)){
			$this->status = 2102;
			$this->message = 'Data not found or deleted';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "setting");
			die();
		}

		//check users_value
		$check = $this->busm->check($nation_code,$pelanggan->id,$classified,$code);
		if(empty($check)){
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['b_user_id'] = $pelanggan->id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['setting_value'] = "";
			$di['id'] = (int) $this->busm->getLastId($nation_code,$pelanggan->id);
			$res = $this->busm->set($di);
			if(empty($res)){
				$this->status = 2104;
				$this->message = 'Cant insert user setting to database';
				$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "setting");
				die();
			}
		}

		//change setting value
		$value = $this->input->post("value");
		$res = $this->busm->change($nation_code,$pelanggan->id,$classified,$code,$value);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 2007;
			$this->message = 'Failed change setting to database';
		}

		//get nation_code
		$settings = $this->ccm->getNotificationSetting($nation_code);
    $user_values = $this->busm->getNotificationValue($nation_code, $pelanggan->id);

		//manipulator
		$user = new stdClass();
		foreach($user_values as $uv){
			// $idx = $uv->classified.'|'.$uv->code;
			if(!isset($user->{$uv->classified})) $user->{$uv->classified} = new stdClass();
			if(!isset($user->{$uv->classified}->{$uv->code})) $user->{$uv->classified}->{$uv->code} = '';
			$user->{$uv->classified}->{$uv->code} = $uv->setting_value;
		}

		//put user config to list
		foreach($settings as &$st){
			$classified = $st->classified;
			if(isset($user->{$classified}->{$st->code})) $st->user_value = $user->{$st->classified}->{$st->code};
			//grouping list
			if($classified == 'setting_notification_buyer'){
				$data['settings']->buyer[] = $st;
			}else if($classified == 'setting_notification_seller'){
				$data['settings']->seller[] = $st;
			}else{
				$data['settings']->general[] = $st;
			}
		}
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "setting");
  }

  //START by Donny Dennison - 13 october 2022 15:52
  //get point policy point or limit
	public function pointorlimit(){
		//initial
		$dt = $this->__init();

		//default result
		$data = array();

		//check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
		if(empty($nation_code)){
	  	$this->status = 101;
			$this->message = 'Missing or invalid nation_code';
		  $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "setting");
		  die();
		}

		//check apikey
		$apikey = $this->input->get('apikey');
		$c = $this->apikey_check($apikey);
		if(!$c){
			$this->status = 400;
			$this->message = 'Missing or invalid API key';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "setting");
			die();
		}

		//check apisess
		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if(!isset($pelanggan->id)){
		  $pelanggan = new stdClass();
		}

		$codes = array(
			"E1",
			"E2",
			"E3",
			"EE",
			"EH",
			"EJ",
			"EY",
			"EZ",
			"E5"
		);

		foreach($codes AS $code){

			$getData = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $code);

			if(isset($getData->remark)){
				$data[] = array(
					"code" => $code,
					"value" => $getData->remark
				);
			}else{
				$data[] = array(
					"code" => $code,
					"value" => ""
				);
			}

		}

		$this->status = 200;
		$this->message = 'Success';
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "setting");
	}
  //END by Donny Dennison - 13 october 2022 15:52
  //get point policy point or limit

}
