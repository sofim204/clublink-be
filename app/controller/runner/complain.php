<?php
require_once(SENECORE."runner_controller.php");
class Complain extends Runner_Controller {
  var $nation_code = '65';
  var $apikey = 'kmz373ac';
  var $apisess = '65KMZDR';
  var $url = '';
  var $url_aft = '';
  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
  }
  private function __dataCreate($d_order_id,$c_produk_id){
    $postdata = array();
    $postdata['d_order_id'] = $d_order_id;
    $postdata['c_produk_id'] = $c_produk_id;
    $postdata['alasan'] = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
    return $postdata;
  }
  private function __dataChange($d_order_id,$c_produk_id){
    $postdata = array();
    $postdata['d_order_id'] = $d_order_id;
    $postdata['c_produk_id'] = $c_produk_id;
    $postdata['alasan'] = "Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ";
    return $postdata;
  }
  public function index(){
    echo '<p></p>';
    echo '<h3>Complain Runner</h3>';
    echo '<ul>';
    echo '<li><a href="'.base_url("runner/complain/seller/").'">Seller</a></li>';
    echo '<li><a href="'.base_url("runner/complain/buyer/").'">Buyer</a></li>';
    echo '</ul>';
  }
  public function seller(){
    $this->__setApiSess('65KMZDR');
    $memory = 0;
    $d_order_id = 0;
    $c_produk_id = 0;

    $this->__vo("Seller Complain Test");

    //start test
    $url = $this->url.'seller/order/new'.$this->url_page;
    $this->__vu("Get new ordered product",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
        if(isset($result->data->orders[0])){
          if(isset($result->data->orders[0]->d_order_id)){
            $d_order_id = $result->data->orders[0]->d_order_id;
            $c_produk_id = $result->data->orders[0]->c_produk_id;
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: ID Order object not found");
          }
        }else{
          $this->__vrh("Passed: with empty result");
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
    $this->__vb($memory);
    //end test

    if(empty($d_order_id) || empty($c_produk_id)){
      $this->__vrh("Cannot continue, one of d_order or c_produk is empty");
      $this->__ve();
      die();
    }

    //start test
    $url = $this->url.'seller/complain/create'.$this->url_page;
    $this->__vu("Create complain for new ordered $d_order_id / $c_produk_id",$url);
    $raw = $this->seme_curl->post($url,$this->__dataCreate($d_order_id,$c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error: ".$result->message);
        }
      }else{
        $this->__vrr("Error: result status on API response not found");
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
    $this->__vb($memory);
    //end test

    //start test
    $url = $this->url.'seller/complain/change'.$this->url_page;
    $this->__vu("Change complain for $d_order_id / $c_produk_id",$url);
    $raw = $this->seme_curl->post($url,$this->__dataChange($d_order_id,$c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error: ".$result->message);
        }
      }else{
        $this->__vrr("Error: result status on API response not found");
      }
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
  public function buyer(){
    $this->__setApiSess('65KMZDS');
    $memory = 0;
    $d_order_id = 0;
    $c_produk_id = 0;

    $this->__vo("Buyer Complain Test");

    //start test
    $url = $this->url.'buyer/order/confirmation'.$this->url_aft;
    $this->__vu("Get waiting for confirmation order by seller",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
        if(isset($result->data->orders[0])){
          $max = count($result->data->orders);
          $rand = rand(0,($max-1));
          if(isset($result->data->orders[$rand]->d_order_id)){
            $d_order_id = $result->data->orders[$rand]->d_order_id;
            $c_produk_id = $result->data->orders[$rand]->c_produk_id;
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: ID Order object not found");
          }
        }else{
          $this->__vrh("Passed: with empty result");
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
    $this->__vb($memory);
    //end test

    if(empty($d_order_id) || empty($c_produk_id)){
      $this->__vrh("Cannot continue, one of d_order or c_produk is empty");
      $this->__ve();
      die();
    }

    //start test
    $url = $this->url.'buyer/complain/create'.$this->url_page;
    $this->__vu("Create complain for new ordered $d_order_id / $c_produk_id",$url);
    $raw = $this->seme_curl->post($url,$this->__dataCreate($d_order_id,$c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error: ".$result->message);
        }
      }else{
        $this->__vrr("Error: result status on API response not found");
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
    $this->__vb($memory);
    //end test

    //start test
    $url = $this->url.'buyer/complain/change'.$this->url_page;
    $this->__vu("Change complain for $d_order_id / $c_produk_id",$url);
    $raw = $this->seme_curl->post($url,$this->__dataChange($d_order_id,$c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error: ".$result->message);
        }
      }else{
        $this->__vrr("Error: result status on API response not found");
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
    $this->__vb($memory);
    //end test

    $this->__ve();
  }
}
