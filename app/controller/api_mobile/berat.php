<?php
class Berat extends JI_Controller{
  public function __construct(){
    parent:: __construct();
    $this->load("api_mobile/b_berat_model","bbm");
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
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "berat");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if(!$c){
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "berat");
      die();
    }

    $this->status = 200;
    $this->message = "Success";

    $data = array();
    $data['berat'] = $this->bbm->get($nation_code);
    foreach($data['berat'] as &$berat){
      if(isset($berat->icon)){
        if(strlen($berat->icon)<=4){
          $berat->icon = 'media/icon/default-icon.png';
        }
        $berat->icon = base_url($berat->icon);
      }
    }
    $this->status = 200;
    $this->message = "Success";
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "berat");
  }
}
