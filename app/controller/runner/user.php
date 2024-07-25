<?php
require_once(SENECORE."runner_controller.php");
class User extends Runner_Controller {
  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
    $this->url = base_url('api_mobile/');
  }

  protected function __userTrue(){
    $postdata = array();
    $postdata['username'] = "daeng@somein.co.id";
    $postdata['password'] = "Password123";
    $postdata['fcm_token'] = 'eUo1qkkhESU:APA91bHyWqRONPiZU780X5J5iBjUWtPbTJ52tiAxxbt67buvDXQMEboq7Mdhx9h1pg09gDtvZ4CbEiSSdWSKEURxz5PRdzzuF3BgiNVwRR29XYTYEFYtaPzRCiI71_HEZn5mls86s32k';
    return $postdata;
  }
  protected function __userFalse(){
    $postdata = array();
      $postdata['username'] = "asdasdasd@dadsasd.com";
      $postdata['password'] = "Password12345";
      $postdata['fcm_token'] = 'eUo1qkkhESU:APA91bHyWqRONPiZU780X5J5iBjUWtPbTJ52tiAxxbt67buvDXQMEboq7Mdhx9h1pg09gDtvZ4CbEiSSdWSKEURxz5PRdzzuF3BgiNVwRR29XYTYEFYtaPzRCiI71_HEZn5mls86s32k';
      return $postdata;
  }

  public function index(){
    echo 'runner login index';
  }
  public function login(){
    $memory = memory_get_usage();
    $this->__vo("User/login");

    //test
    $i=1; //iteration
    $url = $this->url.'pelanggan/login'.$this->url_page; //url call
    $this->__vu("User Login",$url); //testing title

    $postdata = $this->__userTrue();
    //start test
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          $memory = $result->memory;
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error: ".$result->message);
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
    $this->__vb($memory);
    //end test

    //user login false
    $i++; //iteration
    $url = $this->url.'pelanggan/login'.$this->url_page; //url call
    $this->__vu("User Login False",$url); //testing title

    $postdata = $this->__userFalse();
    //start test
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status != 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error: ".$result->message);
        }
        $memory = $result->memory;
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

  protected function __userDaftarTrue(){
    $postdata = array();
      $postdata['google_id'] = "";
      $postdata['fb_id'] = "";
      $postdata['email'] = "iqbal@somein.co.id";
      $postdata['password'] = "Password123";
      $postdata['telp'] = "85861264301";
      $postdata['fnama'] = "Rezza Instagram";
      $postdata['fcm_token'] = "";
    return $postdata;
  }
  protected function __userDaftarFalse(){
    $postdata = array();
      $postdata['google_id'] = "";
      $postdata['fb_id'] = "";
      $postdata['email'] = "asdaasda@fakls.com";
      $postdata['password'] = "Password12345";
      $postdata['telp'] = "85861264301dasa";
      $postdata['fnama'] = "Rezza Instagram";
      $postdata['fcm_token'] = "";
    return $postdata;
  }

  public function daftar(){
    $this->__vo("User/daftar");

    //test
    $i=1; //iteration
    $url = $this->url.'pelanggan/login'.$this->url_page; //url call
    $this->__vu("User Daftar False",$url); //testing title

    $postdata = $this->__userDaftarFalse();
    //start test
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status != 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error: ".$result->message);
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
    //end test

    $this->__ve();
  }

  protected function __userSosmedTrue(){
    $postdata = array();
      $postdata['google_id'] = "";
      $postdata['fb_id'] = "1074270938";
      $postdata['email'] = "daeng@somein.co.id";
      $postdata['telp'] = "";
      $postdata['password'] = "Password123";
    return $postdata;
  }
  protected function __userSosmedFalse(){
    $postdata = array();
      $postdata['google_id'] = "";
      $postdata['fb_id'] = "1074270938";
      $postdata['email'] = "dkjlaks@dasdna.com";
      $postdata['telp'] = "";
      $postdata['password'] = "Password12345";
    return $postdata;
  }

  public function login_sosmed(){
    $this->__vo("User/login_sosmed");

    //test
    $i=1; //iteration
    $url = $this->url.'pelanggan/login_sosmed'.$this->url_page; //url call
    $this->__vu("User Login Sosmed",$url); //testing title

    $postdata = $this->__userSosmedTrue();
    //start test
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error: ".$result->message);
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
    //end test

    //user login false
    $i++; //iteration
    $url = $this->url.'pelanggan/login_sosmed'.$this->url_page; //url call
    $this->__vu("User Login Sosmed False",$url); //testing title

    $postdata = $this->__userSosmedFalse();
    //start test
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status != 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error: ".$result->message);
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
    //end test

    $this->__ve();
  }

  public function notif(){
    $this->__vo("Pelanggan/pemberitahuan");

    //test
    $i=1; //iteration
    $url = $this->url.'pelanggan/pemberitahuan'.$this->url_page; //url call
    $this->__vu("Pemberitahuan",$url); //testing title

    //start test
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error: ".$result->message);
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
    //end test

    $this->__ve();
  }

}
