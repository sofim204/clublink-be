<?php
require_once(SENECORE."runner_controller.php");
class OrderList extends Runner_Controller {
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
  private function __testOrderListNew(){
    $url = $this->url.'seller/order/new'.$this->url_aft;
    $this->__vu("New Ordered Product",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $err = '';
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            $dpass=0;
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing d_order_cdate';
            }
            if($dpass>=2){
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error ".$err);
            }
          }
        }else{
          $this->__vrh("Passed with empty result");
        }
      }
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
  }
  private function __testOrderListProcess(){
    $url = $this->url.'seller/order/process'.$this->url_aft;
    $this->__vu("Order In Process",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $err = '';
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            $dpass=0;
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing d_order_cdate';
            }
            if($dpass>=2){
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error ".$err);
            }
          }
        }else{
          $this->__vrh("Passed with empty result");
        }
      }
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
  }
  private function __testOrderListDelivered(){
    $url = $this->url.'seller/order/delivered'.$this->url_aft;
    $this->__vu("Order Delivered",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $err = '';
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            $dpass=0;
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing d_order_cdate';
            }
            if($dpass>=2){
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error ".$err);
            }
          }
        }else{
          $this->__vrh("Passed with empty result");
        }
      }
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
  }
  private function __testOrderListReceived(){
    $url = $this->url.'seller/order/received'.$this->url_aft;
    $this->__vu("Order Received",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $err = '';
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            $dpass=0;
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing d_order_cdate';
            }
            if($dpass>=2){
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error ".$err);
            }
          }
        }else{
          $this->__vrh("Passed with empty result");
        }
      }
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
  }
  private function __testOrderListRejected(){
    $url = $this->url.'seller/order/listrejected'.$this->url_aft;
    $this->__vu("Order Rejected",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $err = '';
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            $dpass=0;
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing d_order_cdate';
            }
            if($dpass>=2){
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error ".$err);
            }
          }
        }else{
          $this->__vrh("Passed with empty result");
        }
      }
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
  }
  private function __testOrderListExpired(){
    $url = $this->url.'seller/order/expired'.$this->url_aft;
    $this->__vu("Order Expired",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $err = '';
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            $dpass=0;
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing d_order_cdate';
            }
            if($dpass>=2){
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error ".$err);
            }
          }
        }else{
          $this->__vrh("Passed with empty result");
        }
      }
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
  }
  private function __testOrderListSucceed(){
    $url = $this->url.'seller/order/succeed'.$this->url_aft;
    $this->__vu("Order Succeed",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $err = '';
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            $dpass=0;
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing d_order_cdate';
            }
            if($dpass>=2){
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error ".$err);
            }
          }
        }else{
          $this->__vrh("Passed with empty result");
        }
      }
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
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
    $this->__vo("Seller: Order List");

    //start call test procedure
    $this->__testOrderListNew();
    $this->__testOrderListProcess();
    $this->__testOrderListDelivered();
    $this->__testOrderListReceived();
    $this->__testOrderListSucceed();
    $this->__testOrderListRejected();
    $this->__testOrderListExpired();
    //finish call test procedure

    //close test
    $this->__ve();
  }
}
