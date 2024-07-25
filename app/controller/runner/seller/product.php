<?php
require_once(SENECORE."runner_controller.php");
class Product extends Runner_Controller {
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

  private function __testProductActive(){
    $url = $this->url.'seller/produk/active'.$this->url_aft;
    $this->__vu("Produk Active",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->produks)){
          if(isset($result->data->produks[0])){
            $max = count($result->data->produks);
            $idx = rand(0,$max-1);
            $this->produk = $result->data->produks[$idx];
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: c_produk_id object not found");
          }
        }else{
          $this->__vrr("Error: produks object not found");
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

  private function __testProductDetail(){
    $url = $this->url.'produk/detail/'.$this->produk->id.$this->url_aft;
    $this->__vu("Produk Detail",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->produk)){
          if(isset($result->data->produk->id)){
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: id object not found");
          }
        }else{
          $this->__vrr("Error: produk object not found");
        }
      }else{
        $this->__vrr("Error: ".$result->message);
      }
      if(isset($result->memory)) $this->mu = $result->memory;
      $this->__vdf($raw->body);
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
    $this->apisess = '65KMZDR';
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
    $this->__vo("Seller: Produk active");

    //start call test procedure
    $this->__testProductActive();
    $this->__testProductDetail();
    //finish call test procedure

    //close test
    $this->__ve();
  }
}
