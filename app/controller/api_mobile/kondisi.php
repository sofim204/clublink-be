<?php
class Kondisi extends JI_Controller{
  public function __construct(){
    parent:: __construct();
    $this->load("api_mobile/b_user_model", 'bu');
    $this->load("api_mobile/b_kondisi_model","bkonm");
  }
  public function index(){
    //init
    $dt = $this->__init();

    //default result format
    $data = array();
    $data['kondisi'] = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if(empty($nation_code)){
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "kondisi");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $ca = $this->apikey_check($apikey);
    if(empty($ca)){
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "kondisi");
      die();
    }
    
    //by Donny Dennison - 15 february 2022 9:50
    //category product and category community have more than 1 language
    // check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
        $pelanggan = new stdClass();
        if($nation_code == 62){ //indonesia
            $pelanggan->language_id = 2;
        }else if($nation_code == 82){ //korea
            $pelanggan->language_id = 3;
        }else if($nation_code == 66){ //thailand
            $pelanggan->language_id = 4;
        }else {
            $pelanggan->language_id = 1;
        }
    }

    $this->status = 200;
    $this->message = "Success";

    $data = array();
    $data['kondisi'] = $this->bkonm->get($nation_code);
    foreach($data['kondisi'] as &$kondisi){
      if(isset($kondisi->icon)){
        if(strlen($kondisi->icon)<=4){
          $kondisi->icon = 'media/icon/default-icon.png';
        }
        $kondisi->icon = base_url($kondisi->icon);
      }
    }
    $this->status = 200;
    $this->message = "Success";
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "kondisi");
  }
}
