<?php
require_once(SENECORE."runner_controller.php");
class Negara extends Runner_Controller {
  var $nation_code = '65';
  var $apikey = 'kmz373ac';
  var $apisess = '65KMZDS';
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
    echo 'runner negara index';
  }
  public function list(){
    $this->__vo("Negara/list");
    $i = 1;
    $url = $this->url.'alamat/negara'.$this->url_aft;
    $this->__vu("Negara List",$url);

    //start test
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->negara)){
        $ddata = $result->data->negara;
        if(is_array($ddata) && count($ddata)){
          if(isset($ddata[0]->id)){
            $dpass = 1;
            $dlast = $ddata[0];
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error");
          }
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
