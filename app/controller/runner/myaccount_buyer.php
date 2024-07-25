<?php
require_once(SENECORE."runner_controller.php");
class MyAccount_Buyer extends Runner_Controller {
  var $nation_code = '65';
  var $apikey = 'kmz373ac';
  var $apisess = '65KMZDR';
  var $url = '';
  var $url_aft = '';
  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
    $this->url = base_url('api_mobile/');
    $this->url_aft .= '/';
    $this->url_aft .= '?nation_code='.$this->__encURICom($this->nation_code);
    $this->url_aft .= '&apikey='.$this->__encURICom($this->apikey);
    $this->url_aft .= '&apisess='.$this->__encURICom($this->apisess);
  }
  public function index(){
    echo 'Mantaps :D';
  }
  public function pending(){
    $cart_products = array();
    $c_produk_id = 0;
    $d_order_id = 0;
    $memory = memory_get_usage();

    $this->__vo("My Account / Buyer");

    //start test
    $i=1;
    $url = $this->url.'buyer/order/pending'.$this->url_page;
    $this->__vu("Order: Pending",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->order_total)){
          $max = (int) $result->data->order_total;
          if($max>0){
            if(isset($result->data->orders[0]->d_order_id)){
              $this->__vrp("Passed");
            }else{
              $this->__vrp("Failed: Object d_order_id on array orders not found");
            }
          }else{
            $this->__vrh("Passed with empty result");
          }
        }else{
          $this->__vrr("Error: Cart object not found");
        }
      }else{
        $this->__vrr("Error: ".$result->message);
      }
      $memory = $result->memory;
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($memory);
    //end test

    $this->__ve();
  }
}
