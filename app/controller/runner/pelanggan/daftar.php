<?php
require_once(SENECORE."runner_controller.php");
class Daftar extends Runner_Controller {
  var $nation_code = '65';
  var $apikey = 'kmz373ac';
  var $apisess = '65KMZDR';
  var $cart_products = '';
  var $products = '';
  var $product = '';
  var $mu = '';
  var $email = '';
  var $password = '';

  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
    $this->lib("conumtext");
    $this->apisess = '65KMZDS';
    $this->cart_products = array();
    $this->product = new stdClass();
    $this->ordered_products = array();
    $this->mu = memory_get_usage(); //memory usage
  }

  //Pelanggan Baru
  protected function __dataPelangganLogin(){
    $postdata = array();
    $postdata['username'] = $this->email;
    $postdata['password'] = $this->password;
    $postdata['device'] = "android";
    $postdata['fcm_token'] = "";
    return $postdata;
  }

  //Pelanggan Baru
  protected function __dataPelangganBaru(){
    $postdata = array();
    $postdata['google_id'] = "";
    $postdata['fb_id'] = "";
    $postdata['email'] = $this->conumtext->genRand($type="mixed",$min=6,$max=16)."@somein.co.id";
    $postdata['password'] = "Password123";
    $postdata['telp'] = rand(8000,9999)." ".rand(1000,9999);
    $postdata['fnama'] = "Runner ".$this->conumtext->genRand($type="mixed",$min=3,$max=9);
    $postdata['fcm_token'] = "";
    $this->email = $postdata['email'];
    $this->password = $postdata['password'];
    return $postdata;
  }

  protected function __dataPelangganEdit(){
    $postdata = array();
    $postdata['google_id'] = "";
    $postdata['fb_id'] = "";
    $postdata['email'] = $this->conumtext->genRand($type="mixed",$min=3,$max=9)."@somein.co.id";
    $postdata['password'] = "Password123";
    $postdata['telp'] = rand(8000,9999)." ".rand(1000,9999);
    $postdata['fnama'] = "Runner E. ".$this->conumtext->genRand($type="mixed",$min=3,$max=9);
    $postdata['fcm_token'] = "";
    $this->email = $postdata['email'];
    $this->password = $postdata['password'];
    return $postdata;
  }

  private function __testPelangganDaftar(){
    $this->__setSortCol("id");
    $this->__setSortDir("asc");
    $url = $this->url.'pelanggan/daftar'.$this->url_page;
    $this->__vu("Pelanggan Daftar",$url);
    $raw = $this->seme_curl->post($url,$this->__dataPelangganBaru());
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->pelanggan)){
        if(isset($result->data->pelanggan->nation_code)){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error: ".$result->message);
        }
      }else{
        $this->__vrr("Error: Missing pelanggan object");
      }
      if(isset($result->memory)) $this->mu = $result->memory;
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vr("Error 500");
      $this->__vdf($raw->body);
    }else{
      $this->__vr("Error ".$raw->header->http_code);
    }
    $this->__vb($this->mu);
  }

  private function __testPelangganLogin(){
    //start test produk list
    $this->__setSortCol("id");
    $this->__setSortDir("asc");
    $url = $this->url.'pelanggan/login'.$this->url_page;
    $this->__vu("Pelanggan Login: ".$this->email." - ".$this->password,$url);
    $raw = $this->seme_curl->post($url,$this->__dataPelangganLogin());
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->pelanggan)){
        if(isset($result->data->pelanggan->nation_code)){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error: ".$result->message);
        }
      }else{
        $this->__vrr("Error: Missing pelanggan object");
      }
    }else if($raw->header->http_code == 404){
      $this->__vr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vr("Error 500");
      $this->__vdf($raw->body);
    }else{
      $this->__vr("Error ".$raw->header->http_code);
    }
    $this->__vb($this->mu);
    //end test product list
  }

  public function index(){
    //initial
    $this->apisess = '65KMZDS';
    $this->__setApiSess($this->apisess);

    //override endpoint
    //$this->__baseUrl("https://sellon.thecloudalert.com/api_mobile/");
    //$this->__baseUrl("https://sellondev.thecloudalert.com/api_mobile/");
    $this->__resetURL();

    //declare variable
    $this->alamat = new stdClass();

    //open test
    $this->__vo("Pelanggan Register and Login");

    //start call test procedure
    $this->__testPelangganDaftar();
    $this->__testPelangganLogin();
    //finish call test procedure

    //close test
    $this->__ve();
  }
}
