<?php
require_once(SENECORE."runner_controller.php");
class Bank extends Runner_Controller {
  var $nation_code = '65';
  var $apikey = 'kmz373ac';
  var $apisess = '65KMZDR';
  var $cart_products = '';
  var $products = '';
  var $product = '';
  var $mu = '';

  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
    $this->apisess = '65KMZDS';
    $this->cart_products = array();
    $this->product = new stdClass();
    $this->ordered_products = array();
    $this->mu = memory_get_usage(); //memory usage
  }

  protected function __dataBank($a_bank_id){
    $postdata = array();
    $postdata['a_bank_id'] = $a_bank_id;
    $postdata['nomor'] = rand(10000,99999);
    $postdata['nama'] = rand(10000,99999);
    return $postdata;
  }

  private function __testBankList(){
    $url = $this->url.'bank/list'.$this->url_page;
    $this->__vu("Bank List",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->bank)){
          if(isset($result->data->bank[0]->id)){
            $idx = rand(0,count($result->data->bank)-1);
            $this->__vrp("Passed");
            $this->bank = $result->data->bank[$idx];
          }else{
            $this->__vrr("Error: sellers object not found");
          }
        }else{
          $this->__vrr("Error: Cart object not found");
        }
      }else{
        $this->__vrr("Error: ".$result->message);
      }
      if(isset($result->memory)) $this->mu = $result->memory;
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
  }

  private function __testBankAccount(){
    $url = $this->url.'bank'.$this->url_page;
    $this->__vu("Bank Account",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        $this->__vrp("Passed");
      }else{
        $this->__vrr("Error: ".$result->message);
      }
      if(isset($result->memory)) $this->mu = $result->memory;
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
  }

  private function __testBankAccountSet(){
    $url = $this->url.'bank/set'.$this->url_page;
    $this->__vu("Bank Set",$url);
    $raw = $this->seme_curl->post($url,$this->__dataBank($this->bank->id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data)){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error: Cart object not found");
        }
      }else{
        $this->__vrr("Error: ".$result->message);
      }
      if(isset($result->memory)) $this->mu = $result->memory;
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
  }

  public function index(){
    //initial
    $this->__setApiSess($this->apisess);

    //override endpoint
    $this->__baseUrl("https://sellon.thecloudalert.com/api_mobile/");
    //$this->__baseUrl("https://sellondev.thecloudalert.com/api_mobile/");
    $this->__resetURL();

    //declare variable
    $this->cart_products = array();
    $this->products = array();
    $this->c_produk_id = 0;
    $this->d_order_id = 0;
    $this->order = new stdClass();
    $this->ordered_products = array();
    $this->destination = new stdClass();

    //open test
    $this->__vo("Bank Account Full Test");

    //start call test procedure
    $this->__testBankList();
    $this->__testBankAccount();
    $this->__testBankAccountSet();
    $this->__testBankAccount();
    //finish call test procedure

    //close test
    $this->__ve();
  }
}
