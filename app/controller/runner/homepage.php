<?php
require_once(SENECORE."runner_controller.php");
class Homepage extends Runner_Controller {
  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
    $this->url = base_url('api_mobile/');
  }

  public function index(){
    echo '<p></p><h3>Runner Homepage Index</h3>';
    echo '<ul>';
    echo '<li><a href="'.base_url("runner/homepage/test/").'">Test</a></li>';
    echo '</ul>';
  }
  public function test(){
    $memory = 0;
    $dcount = 0;
    $ddata = array();
    $dlast = new stdClass();
    $dpass = 0;
    $this->__vo("Homepage/test");
    /*free produk*/
    $url = $this->url.'homepage'.$this->url_page;
    $this->__vu("Free Product List",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if(isset($result->memory)) $memory = $result->memory;
        if($result->status == 200){
          if(isset($result->data->freeproducts)){
            if(isset($result->data->freeproducts[0])){
              $this->__vrp("Passed");
            }else{
              $this->__vrh("Passed with empty result");
            }
          }else{
            $this->__vrr("Error: Undefined freeproducts object");
          }
        }else{
          $this->__vrr("Error: $result->message");
        }
      }else{
        $this->__vrr("Error: Undefined status object");
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
    /*end free produk*/

    /*produk*/
    $url = $this->url.'homepage'.$this->url_page;
    $this->__vu("Product List",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if(isset($result->memory)) $memory = $result->memory;
        if($result->status == 200){
          if(isset($result->data->produk)){
            if(isset($result->data->produk[0])){
              $this->__vrp("Passed");
            }else{
              $this->__vrh("Passed with empty result");
            }
          }else{
            $this->__vrr("Error: Undefined produk object");
          }
        }else{
          $this->__vrr("Error: $result->message");
        }
      }else{
        $this->__vrr("Error: Undefined status object");
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
    /*end produk*/

    /*kategori*/
    $url = $this->url.'homepage'.$this->url_page;
    $this->__vu("Kategori List",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if(isset($result->memory)) $memory = $result->memory;
        if($result->status == 200){
          if(isset($result->data->kategori)){
            if(isset($result->data->kategori[0])){
              $this->__vrp("Passed");
            }else{
              $this->__vrh("Passed with empty result");
            }
          }else{
            $this->__vrr("Error: Undefined kategori object");
          }
        }else{
          $this->__vrr("Error: $result->message");
        }
      }else{
        $this->__vrr("Error: Undefined status object");
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
    /*end kategori*/

    /*banner*/
    $url = $this->url.'homepage'.$this->url_page;
    $this->__vu("Banner List",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if(isset($result->memory)) $memory = $result->memory;
        if($result->status == 200){
          if(isset($result->data->banner)){
            if(isset($result->data->banner[0])){
              $this->__vrp("Passed");
            }else{
              $this->__vrh("Passed with empty result");
            }
          }else{
            $this->__vrr("Error: Undefined free banner object");
          }
        }else{
          $this->__vrr("Error: $result->message");
        }
      }else{
        $this->__vrr("Error: Undefined status object");
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
    /*end banner*/
    $this->__ve();
  }
}
