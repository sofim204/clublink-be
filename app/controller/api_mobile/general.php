<?php
class General extends JI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->lib("seme_log");
    $this->load("api_mobile/b_user_model", 'bu');
    $this->load("api_mobile/e_chat_participant_model", 'ecpm');
    $this->load("api_mobile/d_pemberitahuan_model", 'dpem');

  }

  // public function index()
  // {
  //     //initial
  //     $dt = $this->__init();

  //     //default result
  //     $data = array();
  //     $data['chat_room_total'] = 0;
  //     $data['chat_room'] = array();

  //     //check nation_code
  //     $nation_code = $this->input->get('nation_code');
  //     $nation_code = $this->nation_check($nation_code);
  //     if (empty($nation_code)) {
  //         $this->status = 101;
  //         $this->message = 'Missing or invalid nation_code';
  //         $this->__json_out($data);
  //         die();
  //     }

  //     //check apikey
  //     $apikey = $this->input->get('apikey');
  //     $c = $this->apikey_check($apikey);
  //     if (!$c) {
  //         $this->status = 400;
  //         $this->message = 'Missing or invalid API key';
  //         $this->__json_out($data);
  //         die();
  //     }

  //     //check apisess
  //     $apisess = $this->input->get('apisess');
  //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  //     if (!isset($pelanggan->id)) {
  //         $this->status = 401;
  //         $this->message = 'Missing or invalid API session';
  //         $this->__json_out($data);
  //         die();
  //     }

  //     //default output
  //     $this->status = 200;
  //     $this->message = 'Success';

  //     //render as json
  //     $this->__json_out($data);
  // }

  public function unreadcountchatandnotification(){
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['pemberitahuan_count'] = 0;
    $data['chat_count'] = 0;

    //default output
    $this->status = 200;
    $this->message = 'Success';

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if(empty($nation_code)){
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data);
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if(!$c){
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data);
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if(!isset($pelanggan->id)){
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data);
      die();
    }

    $data['chat_count'] = (int) $this->ecpm->countUnread($nation_code,$pelanggan->id);
    $data['pemberitahuan_count'] = (int) $this->dpem->countUnRead($nation_code, $pelanggan->id);

    //render as json
    $this->__json_out($data);
  }

}