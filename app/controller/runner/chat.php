<?php
require_once(SENECORE."runner_controller.php");
class Chat extends Runner_Controller {
  var $d_order_id = 109;
  var $c_produk_id = 1;
  var $message = '';
  var $mu = 0;
  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
    $this->url = base_url('api_mobile/');
    $this->mu = memory_get_usage(); //memory usage
  }
  private function __add(){
    $postdata['d_order_id'] = $this->d_order_id;
    $postdata['c_produk_id'] =  $this->c_produk_id;
    $postdata['message'] = $this->message;
    return $postdata;
  }
  public function index(){
    echo '<p></p><h3>Chat Test Index</h3>';
    echo '<ul>';
    echo '<li><a href="'.base_url("runner/chat/test").'">Test</a></li>';
    echo '</ul>';
  }
  public function test(){
    $memory = 0;
    $c_produk_id = 0;
    $this->__baseUrl("https://sellondev.thecloudalert.com/api_mobile/");
    $this->__resetURL();

    $this->d_order_id = 109;
    $this->c_produk_id = 1;

    $this->__vo("Chat Buyer Seller. Order ID: ".$this->d_order_id.', Produk ID: '.$this->c_produk_id);

    //start test send from buyer
    $i=1;
    $this->apisess = '65KMZDS';
    $this->__setApiSess($this->apisess);
    $this->message = 'Halo Bro!';
    $url = $this->url.'buyer/chat/send'.$this->url_aft;
    $this->__vu("Buyer (".$this->apisess.") Ask: ".$this->message,$url);
    $raw = $this->seme_curl->post($url,$this->__add());
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->memory)) $this->mu = $result->memory;
        if(isset($result->data->chat)){
          if(count($result->data->chat)>0){
            if(isset($result->data->chat[0]->id)){
              $c_produk_id = $result->data->chat[0]->id;
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error: ID object not found");
            }
          }else{
            $this->__vrh("Passed with empty result");
          }
        }else{
          $this->__vrr("Error: Chat object not found");
        }
      }else{
        $this->__vrr("Error: ".$result->message);
      }
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($this->mu);
    //end test send from buyer

    //start test send from buyer
    $i++;
    $this->apisess = '65KMZDS';
    $this->__setApiSess($this->apisess);
    $this->message = 'P';
    $url = $this->url.'buyer/chat/send'.$this->url_aft;
    $this->__vu("Buyer (".$this->apisess.") Ask: ".$this->message,$url);
    $raw = $this->seme_curl->post($url,$this->__add());
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->memory)) $this->mu = $result->memory;
        if(isset($result->data->chat)){
          if(count($result->data->chat)>0){
            if(isset($result->data->chat[0]->id)){
              $c_produk_id = $result->data->chat[0]->id;
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error: ID not found");
            }
          }else{
            $this->__vrh("Passed with empty result");
          }
        }else{
          $this->__vrr("Error: Chat object not found");
        }
      }else{
        $this->__vrr("Error: ".$result->message);
      }
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($this->mu);
    //end test send from buyer

    //start test send from buyer
    $i++;
    $this->apisess = '65KMZDS';
    $this->__setApiSess($this->apisess);
    $this->message = 'P';
    $url = $this->url.'buyer/chat/send'.$this->url_aft;
    $this->__vu("Buyer (".$this->apisess.") Ask: ".$this->message,$url);
    $raw = $this->seme_curl->post($url,$this->__add());
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->memory)) $this->mu = $result->memory;
        if(isset($result->data->chat)){
          if(count($result->data->chat)>0){
            if(isset($result->data->chat[0]->id)){
              $c_produk_id = $result->data->chat[0]->id;
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error: ID object not found");
            }
          }else{
            $this->__vrh("Passed with empty result");
          }
        }else{
          $this->__vrr("Error: Chat object not found");
        }
      }else{
        $this->__vrr("Error: ".$result->message);
      }
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($this->mu);
    //end test send from buyer

    //start test send from buyer
    $i++;
    $this->apisess = '65KMZDR';
    $this->__setApiSess($this->apisess);
    $this->message = 'I dont know!';
    $url = $this->url.'seller/chat/send'.$this->url_aft;
    $this->__vu("Seller (".$this->apisess.") Ask: ".$this->message,$url);
    $raw = $this->seme_curl->post($url,$this->__add());
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->memory)) $this->mu = $result->memory;
        if(isset($result->data->chat)){
          if(count($result->data->chat)>0){
            if(isset($result->data->chat[0]->id)){
              $c_produk_id = $result->data->chat[0]->id;
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error: ID object not found");
            }
          }else{
            $this->__vrh("Passed with empty result");
          }
        }else{
          $this->__vrr("Error: Chat object not found");
        }
      }else{
        $this->__vrr("Error: ".$result->message);
      }
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($this->mu);
    //end test send from buyer


    $this->__ve();
  }
}
