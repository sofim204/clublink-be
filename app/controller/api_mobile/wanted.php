<?php
class Wanted extends JI_Controller{
  public function __construct(){
    parent::__construct();
    $this->load("api_mobile/b_user_model",'bu');
		$this->load("api_mobile/b_user_productwanted_model",'bupwm');
  }

  public function index(){
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['wanteds'] = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if(empty($nation_code)){
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if(!$c){
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if(!isset($pelanggan->id)){
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
      die();
    }

    $wanteds = $this->bupwm->getAll($nation_code, $pelanggan->id);

    //building response
    $data['wanteds'] = $wanteds;

    //response message
    $this->status = 200;
    $this->message = 'Success';

    //render as json
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
  }

  public function baru(){
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
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
      die();
    }

		//check apikey
		$apikey = $this->input->get('apikey');
		$c = $this->apikey_check($apikey);
		if(!$c){
			$this->status = 400;
			$this->message = 'Missing or invalid API key';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
			die();
		}

		//check apisess
		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if(!isset($pelanggan->id)){
			$this->status = 401;
			$this->message = 'Missing or invalid API session';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
			die();
		}

    $this->status = 300;
    $this->message = 'Missing one or more parameters';

		//check $jenis
		$keyword_text = $this->input->post('keyword_text');
		if(strlen($keyword_text)<=0 || empty($keyword_text)){
			$this->status = 8021;
			$this->message = 'Keyword Text cannot empty';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
			die();
		}

    //sanitize
    $keyword_text = trim(strip_tags($keyword_text));

    //check not duplicate
    $bupwm = $this->bupwm->check($nation_code,$pelanggan->id,$keyword_text);
    if(isset($bupwm->id)){
			$this->status = 200;
			$this->message = 'Success';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
      die();
    }


		//start transaction
		$this->bupwm->trans_start();

		//get last id
		$current_id = $this->bupwm->getLastId($nation_code,$pelanggan->id);

		//collect input
    //Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
		$di = array();
		$di['nation_code'] = $nation_code;
		$di['b_user_id'] = $pelanggan->id;
		$di['id'] = $current_id;
    $di['keyword_text'] = $keyword_text;

		//insert into database
		$res = $this->bupwm->set($di);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
      $this->bupwm->trans_commit();
      $data['wanted'] = $this->bupwm->getById($nation_code,$pelanggan->id,$current_id);
		}else{
			$this->bupwm->trans_rollback();
			$this->status = 8005;
			$this->message = 'Failed insert data';
		}
		$this->bupwm->trans_end();

		//render
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
	}

  public function edit(){
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
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
      die();
    }

		//check apikey
		$apikey = $this->input->get('apikey');
		$c = $this->apikey_check($apikey);
		if(!$c){
			$this->status = 400;
			$this->message = 'Missing or invalid API key';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
			die();
		}

		//check apisess
		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if(!isset($pelanggan->id)){
			$this->status = 401;
			$this->message = 'Missing or invalid API session';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
			die();
		}

    $current_id = (int) $this->input->post('id');
    if($current_id<=0){
      $this->status = 8022;
      $this->message = 'Invalid ID';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
      die();
    }

    //check jenis
		$keyword_text = $this->input->post('keyword_text');
		if(strlen($keyword_text)<=0 || empty($keyword_text)){
			$this->status = 8024;
			$this->message = 'keyword_text cannot empty';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
			die();
		}

    //sanitize
    $keyword_text = trim(strip_tags($keyword_text));

    //check not duplicate
    $bupwm = $this->bupwm->check($nation_code,$pelanggan->id,$keyword_text);
    if(isset($bupwm->id)){
			$this->status = 200;
			$this->message = 'Success';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
      die();
    }

    //check not duplicate
    $bupwm = $this->bupwm->check($nation_code,$pelanggan->id,$keyword_text);
    if(isset($bupwm->id)){
			$this->status = 200;
			$this->message = 'Success';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
      die();
    }

		$wanted = $this->bupwm->getById($nation_code, $pelanggan->id,$current_id);
		if(!isset($wanted->id)){
			$this->status = 8023;
			$this->message = 'Data not found or has been deleted';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
			die();
		}

    //check $id

		//start transaction
		$this->bupwm->trans_start();

		//collect input
    $du = array();
		$du['keyword_text'] = $keyword_text;

		//insert into database
		$res = $this->bupwm->update($nation_code,$pelanggan->id,$current_id,$du);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
      $this->bupwm->trans_commit();
      $data['wanted'] = $this->bupwm->getById($nation_code,$pelanggan->id,$current_id);
		}else{
			$this->bupwm->trans_rollback();
			$this->status = 8011;
			$this->message = 'Failed updating data';
		}
		$this->bupwm->trans_end();
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
	}


	public function hapus(){
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
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
      die();
    }

		//check apikey
		$apikey = $this->input->get('apikey');
		$c = $this->apikey_check($apikey);
		if(!$c){
			$this->status = 400;
			$this->message = 'Missing or invalid API key';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
			die();
		}

		//check apisess
		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if(!isset($pelanggan->id)){
			$this->status = 401;
			$this->message = 'Missing or invalid API session';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
			die();
		}

    $current_id = (int) $this->input->post('id');
		if($current_id<=0){
			$this->status = 8022;
			$this->message = 'Invalid ID';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
			die();
		}

		//bypassing
		$wanted = $this->bupwm->getById($nation_code, $pelanggan->id, $current_id);
		if(!isset($wanted->id)){
			$this->status = 8023;
			$this->message = 'Data not found or has been deleted';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
			die();
		}

    $res = $this->bupwm->delete($nation_code,$pelanggan->id,$current_id);
    if($res){
      $this->status = 200;
      $this->message = 'Success';
    }else{
      $this->status = 8014;
      $this->message = 'Failed to delete data';
    }

		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
	}
  public function check(){
		//initial
		$dt = $this->__init();

		//default result
		$data = 0;

    //check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
    if(empty($nation_code)){
      $this->status = 101;
  		$this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
      die();
    }

		//check apikey
		$apikey = $this->input->get('apikey');
		$c = $this->apikey_check($apikey);
		if(!$c){
			$this->status = 400;
			$this->message = 'Missing or invalid API key';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
			die();
		}

		//check apisess
		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if(!isset($pelanggan->id)){
			$this->status = 401;
			$this->message = 'Missing or invalid API session';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
			die();
		}

    //check keyword_text
		$keyword_text = $this->input->post('keyword_text');
		if(strlen($keyword_text)<=0 || empty($keyword_text)){
			$this->status = 8024;
			$this->message = 'keyword_text cannot empty';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
			die();
		}
    $keyword_text = strtolower($keyword_text);

    $res = $this->bupwm->check($nation_code,$pelanggan->id,$keyword_text);
    if(isset($res->id)){
      $this->status = 200;
      // $this->message = 'Wanted';
      $this->message = 'Success';
      $data = 1;
    }else{
      $this->status = 200;
      // $this->message = 'Unwanted';
      $this->message = 'Success';
      $data = 0;
    }

		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wanted");
  }
}
