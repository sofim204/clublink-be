<?php
require_once(SENECORE."runner_controller.php");
class Alamat extends Runner_Controller {
  var $nation_code = '65';
  var $apikey = 'kmz373ac';
  var $apisess = '65KMZDR';
  var $cart_products = '';
  var $products = '';
  var $product = '';
  var $mu = '';
  var $order = '';
  var $is_not_pass = 0;

  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
    $this->apisess = '65KMZDS';
    $this->cart_products = array();
    $this->product = new stdClass();
    $this->order = new stdClass();
    $this->ordered_products = array();
    $this->mu = memory_get_usage(); //memory usage
  }
  
  private function __testPelangganAlamat(){
    //start test
    $this->__resetPage();
    $url = $this->url.'pelanggan/alamat'.$this->url_page; //url call
    $this->__vu("Get Current User Address",$url); //testing title
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          if(isset($result->data->alamat)){
            $total = count($result->data->alamat);
            if($total>0){
              $rand = (mt_rand(1,$total))-1;
              if(isset($result->data->alamat[$rand]->id)){
                $this->__vrp("Passed");
                $this->destination = $result->data->alamat[$rand];
              }else{
                $this->__vrr("Error: Object on array alamat incorrect format");
              }
            }else{
              $this->__vrr("Error: Object alamat is empty array");
            }
          }else{
            $this->__vrr("Error: Object alamat not found");
          }
          if(isset($result->memory)) $this->mu = $result->memory;
        }else{
          $this->__vrr("Error: ".$result->message);
        }
      }
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
      $this->__vdf($raw->body);
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($this->mu);
    //end test
  }

  public function index(){
    //initial
    $this->apisess = '65KMZDT';
    $this->__setApiSess($this->apisess);

    //override endpoint
    $this->__baseUrl("https://sellon.thecloudalert.com/api_mobile/");
    //$this->__baseUrl("https://sellondev.thecloudalert.com/api_mobile/");
    $this->__resetURL();

    //open test
    $this->__vo("My Account: Address");
    $this->__testPelangganAlamat();

    //close test
    $this->__ve();
  }
}