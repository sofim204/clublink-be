<?php
require_once(SENECORE."runner_controller.php");
class Process extends Runner_Controller {
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
    $this->apisess = '65KMZDR';
    $this->cart_products = array();
    $this->product = new stdClass();
    $this->ordered_products = array();
    $this->mu = memory_get_usage(); //memory usage
  }

  private function __testOrderProcess(){
    $url = $this->url.'seller/order/process'.$this->url_aft;
    $this->__vu("Order Detail",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->orders)){
          if(isset($result->data->orders[0])){
            $max = count($result->data->orders);
            $idx = rand(0,$max-1);
            $this->order = $result->data->orders[$idx];
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: d_order_id object not found");
          }
        }else{
          $this->__vrr("Error: Orders object not found");
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

  private function __testOrderDetail(){
    $url = $this->url.'seller/order/detail'.$this->url_aft;
    $this->__vu("Order Detail",$url);
    $raw = $this->seme_curl->post($url,array("d_order_id"=>$this->order->d_order_id,"c_produk_id"=>$this->order->c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->order)){
          if(isset($result->data->order->d_order_id)){
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: d_order_id object not found");
          }
        }else{
          $this->__vrr("Error: Orders object not found");
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

  private function __testOrderConfirmed(){
    $url = $this->url.'seller/order/confirmed'.$this->url_aft;
    $this->__vu("Order Confirm",$url);
    $raw = $this->seme_curl->post($url,array("d_order_id"=>$this->order->d_order_id,"c_produk_id"=>$this->order->c_produk_id));
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

  private function __testOrderWaybill(){
    /*start download waybill*/
    $url = $this->url.'seller/waybill/print/'.$this->order->d_order_id.'/'.$this->order->c_produk_id.'/'.$this->url_aft;
    $this->__vu("Download WayBill",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $this->__vrp("Passed");
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    /*end download waybill*/
  }

  private function __testOrderDelivery(){
    $url = $this->url.'seller/order/delivery_process'.$this->url_aft;
    $this->__vu("Order Confirm",$url);
    $raw = $this->seme_curl->post($url,array("d_order_id"=>$this->order->d_order_id,"c_produk_id"=>$this->order->c_produk_id));
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

  public function index(){
    //initial
    $this->apisess = '65KMZDR';
    $this->__setApiSess($this->apisess);

    //override endpoint
    //$this->__baseUrl("https://sellon.thecloudalert.com/api_mobile/");
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
    $this->__vo("Seller: Order Process -> Delivery");

    //start call test procedure
    $this->__testOrderProcess();
    $this->__testOrderDetail();
    $this->__testOrderWaybill();
    $this->__testOrderDelivery();
    //finish call test procedure

    //close test
    $this->__ve();
  }
}
