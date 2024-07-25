<?php
require_once(SENECORE."runner_controller.php");
class Tnc extends Runner_Controller {
  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
    $this->url = base_url('api_mobile/');
  }

  public function index(){
    echo 'runner address index';
  }

  //FAQ
  public function tnc(){
    $this->__vo("tnc/");

    //test
    $i=1; //iteration
    $url = $this->url.'tnc/'.$this->url_page; //url call
    $this->__vu("Term and Condition",$url); //testing title

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
    //End FAQ
}
