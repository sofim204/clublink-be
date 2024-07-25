<?php
  class Credit_Card extends JI_Controller{
    public function __construct(){
      parent::__construct();
      $this->load("api_mobile/b_user_model",'bu');
  		$this->load("api_mobile/b_user_card_model",'buc');
    }

    public function index(){
      //initial
      $dt = $this->__init();

      //default result
      $data = array();
      $data['credit_card'] = new stdClass();

      //check nation_code
      $nation_code = $this->input->get('nation_code');
      $nation_code = $this->nation_check($nation_code);
      if(empty($nation_code)){
        $this->status = 101;
        $this->message = 'Missing or invalid nation_code';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
        die();
      }

      //check apikey
      $apikey = $this->input->get('apikey');
      $c = $this->apikey_check($apikey);
      if(!$c){
        $this->status = 400;
        $this->message = 'Missing or invalid apikey';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
        die();
      }

      //check apisess
      $apisess = $this->input->get('apisess');
      $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
      if(!isset($pelanggan->id)){
        $this->status = 401;
        $this->message = 'Missing or invalid apisess';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
        die();
      }

      $cards = $this->buc->getAll($nation_code, $pelanggan->id);

      //building response
      $data['credit_card'] = $cards;

      //response message
      $this->status = 200;
      $this->message = 'Success';

      //render as json
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
    }

    public function credit_card_baru(){
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
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
        die();
      }

  		//check apikey
  		$apikey = $this->input->get('apikey');
  		$c = $this->apikey_check($apikey);
  		if(!$c){
  			$this->status = 400;
  			$this->message = 'Missing or invalid apikey';
  			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
  			die();
  		}

  		//check apisess
  		$apisess = $this->input->get('apisess');
  		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  		if(!isset($pelanggan->id)){
  			$this->status = 401;
  			$this->message = 'Missing or invalid apisess';
  			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
  			die();
  		}

  		//check $jenis
  		$jenis = $this->input->post('jenis');
  		if(strlen($jenis)<=0){
  			$this->status = 1731;
  			$this->message = 'Card Type cannot empty';
  			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
  			die();
  		}

  		//check $bank
  		$bank = $this->input->post('bank');
  		if(strlen($bank)<=0){
  			$this->status = 1734;
  			$this->message = 'Bank Name cannot be empty';
  			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
  			die();
  		}

  		//check $no_telp
  		$nomor = $this->input->post('nomor');
  		if(strlen($nomor)<=0){
  			$this->status = 1735;
  			$this->message = 'Card Number cannot be empty';
  			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
  			die();
  		}

  		//check $token_result
  		$token_result = $this->input->post('token_result');
  		if(strlen($token_result)<=0){
  			$this->status = 1736;
  			$this->message = 'Invalid Token Result';
  			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
  			die();
  		}

  		//start transaction
  		$this->buc->trans_start();

  		//get last id
  		$b_user_card_result_id = $this->buc->getLastId($nation_code,$pelanggan->id);

  		//collect input
      //Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
  		$di = array();
  		$di['nation_code'] = $nation_code;
  		$di['id'] = $b_user_card_result_id;
  		$di['b_user_id'] = $pelanggan->id;
      $di['jenis'] = $jenis;
  		$di['bank'] = $bank;
  		$di['nomor'] = $nomor;
  		$di['token_result'] = $token_result;

  		//insert into database
  		$res = $this->buc->set($di);
  		if($res){
      $this->buc->trans_commit();
  			$this->status = 200;
  			$this->message = 'Success';
  		}else{
  			$this->buc->trans_rollback();
  			$this->status = 1742;
  			$this->message = 'Failed insert user address';
  		}
  		$this->buc->trans_end();

  		//render
  		$this->__json_out($di);
  	}

    public function edit(){
  		//initial
  		$dt = $this->__init();

  		//default result
  		$data = array();
  		$data['credit_card'] = array();

      //check nation_code
  		$nation_code = $this->input->get('nation_code');
  		$nation_code = $this->nation_check($nation_code);
      if(empty($nation_code)){
        $this->status = 101;
    		$this->message = 'Missing or invalid nation_code';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
        die();
      }

  		//check apikey
  		$apikey = $this->input->get('apikey');
  		$c = $this->apikey_check($apikey);
  		if(!$c){
  			$this->status = 400;
  			$this->message = 'Missing or invalid apikey';
  			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
  			die();
  		}

  		//check apisess
  		$apisess = $this->input->get('apisess');
  		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  		if(!isset($pelanggan->id)){
  			$this->status = 401;
  			$this->message = 'Missing or invalid apisess';
  			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
  			die();
  		}

  		$credit_card = $this->buc->getById($nation_code, $pelanggan->id);
  		if(!isset($credit_card->id)){
  			$this->status = 818;
  			$this->message = 'Product ID not found or has been deleted';
  			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
  			die();
  		}

      //check $id
      $card_id = (int) $this->input->post('id');
      if($card_id<=0){
        $this->status = 824;
        $this->message = 'Invalid Card ID';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
        die();
      }

      //check $jenis
  		$jenis = $this->input->post('jenis');
  		if(strlen($jenis)<=0){
  			$this->status = 1731;
  			$this->message = 'Card Type cannot empty';
  			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
  			die();
  		}

  		//check $bank
  		$bank = $this->input->post('bank');
  		if(strlen($bank)<=0){
  			$this->status = 1734;
  			$this->message = 'Bank Name cannot be empty';
  			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
  			die();
  		}

  		//check $no_telp
  		$nomor = $this->input->post('nomor');
  		if(strlen($nomor)<=0){
  			$this->status = 1735;
  			$this->message = 'Card Number cannot be empty';
  			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
  			die();
  		}

  		//check $token_result
  		$token_result = $this->input->post('token_result');
  		if(strlen($token_result)<=0){
  			$this->status = 1736;
  			$this->message = 'Invalid Token Result';
  			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
  			die();
  		}


  		//start transaction
  		$this->buc->trans_start();

  		//collect input
      //Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
  		$du = array();
  		$du['nation_code'] = $nation_code;
  		$du['id'] = $card_id;
  		$du['b_user_id'] = $credit_card->id;
      $du['jenis'] = $jenis;
  		$du['bank'] = $bank;
  		$du['nomor'] = $nomor;
  		$du['token_result'] = $token_result;

  		//insert into database
  		$res = $this->buc->update($nation_code,$credit_card->b_user_id,$card_id,$du);
  		if($res){
      $this->buc->trans_commit();
  			$this->status = 200;
  			$this->message = 'Success';
  		}else{
  			$this->buc->trans_rollback();
  			$this->status = 1742;
  			$this->message = 'Failed insert user address';
  		}
  		$this->buc->trans_end();
  		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
  	}


  	public function hapus(){
  		//initial
  		$dt = $this->__init();

  		//default result
  		$data = array();
  		$data['card_count'] = 0;
  		$data['card'] = array();

      //check nation_code
  		$nation_code = $this->input->get('nation_code');
  		$nation_code = $this->nation_check($nation_code);
      if(empty($nation_code)){
        $this->status = 101;
    		$this->message = 'Missing or invalid nation_code';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
        die();
      }

  		//check apikey
  		$apikey = $this->input->get('apikey');
  		$c = $this->apikey_check($apikey);
  		if(!$c){
  			$this->status = 400;
  			$this->message = 'Missing or invalid apikey';
  			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
  			die();
  		}

  		//check apisess
  		$apisess = $this->input->get('apisess');
  		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  		if(!isset($pelanggan->id)){
  			$this->status = 401;
  			$this->message = 'Missing or invalid apisess';
  			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
  			die();
  		}

      $card_id = (int) $this->input->post('id');
  		if($card_id<=0){
  			$this->status = 824;
  			$this->message = 'Invalid Card ID';
  			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
  			die();
  		}

  		//bypassing
  		if(false){
  			$produk = $this->buc->getById($nation_code, $pelanggan->id);
  			if(!isset($produk->id)){
  				$this->status = 818;
  				$this->message = 'Product ID not found or has been deleted';
  				$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
  				die();
  			}
  		}

      $res = $this->buc->delete($nation_code,$pelanggan->id,$card_id);
      if($res){
        $this->status = 200;
        $this->message = 'Success';
      }else{
        $this->status = 826;
        $this->message = 'Failed deleting product from cart';
      }
      
  		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "credit_card");
  	}
  }
