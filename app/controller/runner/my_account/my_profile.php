<?php
require_once(SENECORE."runner_controller.php");
class My_Profile extends Runner_Controller {
  var $nation_code = '65';
  var $apikey = 'kmz373ac';
  var $session = '65KMZDR';
  var $mu = 0; //memory usage

  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
    $this->session = '65KMZDS';
    $this->mu = memory_get_usage();
  }
  private function __test001(){
    //start test
    $url = $this->url.'pelanggan'.$this->url_page;
    $this->__vu("Get current session profile",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) if(isset($result->memory)) $this->mu = $result->memory;
      if($result->status == 200){
        if(isset($result->data->pelanggan)){
          if(isset($result->data->pelanggan->nation_code)){
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: pelanggan->nation_code object not found");
          }
        }else{
          $this->__vrr("Error: pelanggan object not found");
        }
      }else{
        $this->__vrr("Error: ".$result->message);
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
    $this->__setApiSess($this->session);
    $this->__resetURL();
    $memory = memory_get_usage();

    //override endpoint
    //$this->__baseUrl("https://sellon.thecloudalert.com/api_mobile/");
    //$this->__baseUrl("https://devsellon.thecloudalert.com/api_mobile/");

    //declare variable
    $this->cart_products = array();
    $this->c_produk_id = 0;
    $this->d_order_id = 0;

    $this->__vo("My Account: My Profile ($this->session)");
    //start test
    $this->__test001();
    //end test

    $this->__ve();
  }
}
