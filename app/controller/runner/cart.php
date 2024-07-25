<?php
require_once(SENECORE."runner_controller.php");
class Cart extends Runner_Controller {
  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
    $this->url = base_url('api_mobile/');
  }

  protected function __dataPayNow(){
    //because post data are in json format, we must craeted the object first :D
    $data = array();
    $d = new stdClass();
    $d->id = 1;
    $d->qty = 1;
    $data[] = $d;
    $d = new stdClass();
    $d->id = 2;
    $d->qty = 1;
    $data[] = $d;
    $d = new stdClass();
    $d->id = 3;
    $d->qty = 2;
    $data[] = $d;

    $d = new stdClass();
    $d->products = $data;

    //postdata
    $postdata = array();
    $postdata['post_data'] = json_encode($d);
    return $postdata;
  }
  public function index(){
    echo '<p></p><h3>Runner Cart Index</h3>';
    echo '<ul>';
    echo '<li><a href="'.base_url("runner/cart/paynow/").'">Paynow Test</a></li>';
    echo '<li><a href="'.base_url("runner/cart/bread/").'">Bread Test</a></li>';
    echo '</ul>';
  }
  public function paynow(){
    $memory = 0;
    $this->__vo("Cart Paynow Test");

    //start test
    $url = $this->url.'cart/paynow'.$this->url_page;
    $this->__vu("Cart Paynow",$url);
    $raw = $this->seme_curl->post($url,$this->__dataPayNow());
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
        if(isset($result->data->order)){
          if(isset($result->data->order->id)){
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: ID Order object not found");
          }
        }else{
          $this->__vrr("Error: Order object not found");
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

    $this->__ve();
  }

  public function bread(){
    $memory = 0;
    $this->__vo("Cart Bread test");

    $postdata = array("c_produk_id"=>1,"qty"=>1);
    $url = $this->url.'cart/tambah'.$this->url_page;
    $this->__vu("Cart Add",$url);
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->cart)){
          if(isset($result->data->cart->sellers)){
            $this->__vrp("Passed");
            foreach($result->data->cart->sellers as $seller){
              foreach($seller->products as $product){
                $cart_products[] = $product;
              }
            }
          }else{
            $this->__vrr("Error: sellers object not found");
          }
        }else{
          $this->__vrr("Error: Cart object not found");
        }
      }else{
        $this->__vrr("Error: ".$result->message);
      }
      if(isset($result->memory)) $memory = $result->memory;
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($memory);

    //stast test cart remove
    $this->__resetPage();
    $url = $this->url.'cart/hapus'.$this->url_page;
    $this->__vu("Cart Remove (1)",$url);
    if(count($cart_products)>0){
      //get first product
      if(isset($cart_products[0]->id)) $c_produk_id = $cart_products[0]->id;
      $raw = $this->seme_curl->post($url,array("c_produk_id"=>$c_produk_id));
      if($raw->header->http_code == 200){
        $result = json_decode($raw->body);
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error: ".$result->message);
        }
        if(isset($result->memory)) $memory = $result->memory;
        $this->__vd($raw->body);
      }else if($raw->header->http_code == 404){
        $this->__vrr("Not found");
      }else if($raw->header->http_code == 500){
        $this->__vrr("Error 500");
      }else{
        $this->__vrr("Error ".$raw->header->http_code);
      }
    }else{
      $this->__vrh("Skipped: Empty cart");
    }
    $this->__vb($memory);
    //end test cart remove

    //start test
    $postdata = array("c_produk_id"=>1,"qty"=>1);
    $url = $this->url.'cart/tambah'.$this->url_page;
    $this->__vu("Cart Add",$url);
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->cart)){
          if(isset($result->data->cart->sellers)){
            $this->__vrp("Passed");
            foreach($result->data->cart->sellers as $seller){
              foreach($seller->products as $product){
                $cart_products[] = $product;
              }
            }
          }else{
            $this->__vrr("Error: sellers object not found");
          }
        }else{
          $this->__vrr("Error: Cart object not found");
        }
      }else{
        $this->__vrr("Error: ".$result->message);
      }
      if(isset($result->memory)) $memory = $result->memory;
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

    //stast test cart remove
    $this->__resetPage();
    $url = $this->url.'cart/hapus'.$this->url_page;
    $this->__vu("Cart Remove (2)",$url);
    if(count($cart_products)>0){
      //get first product
      if(isset($cart_products[0]->id)) $c_produk_id = $cart_products[0]->id;
      $raw = $this->seme_curl->post($url,array("c_produk_id"=>$c_produk_id));
      if($raw->header->http_code == 200){
        $result = json_decode($raw->body);
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error: ".$result->message);
        }
        if(isset($result->memory)) $memory = $result->memory;
        $this->__vd($raw->body);
      }else if($raw->header->http_code == 404){
        $this->__vrr("Not found");
      }else if($raw->header->http_code == 500){
        $this->__vrr("Error 500");
      }else{
        $this->__vrr("Error ".$raw->header->http_code);
      }
    }else{
      $this->__vrh("Skipped: Empty cart");
    }
    $this->__vb($memory);
    //end test cart remove

    //start test
    $url = $this->url.'cart'.$this->url_page;
    $this->__vu("Cart List",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->cart)){
          if(isset($result->data->cart->sellers)){
            $this->__vrp("Passed");
            foreach($result->data->cart->sellers as $seller){
              foreach($seller->products as $product){
                $cart_products[] = $product;
              }
            }
          }else{
            $this->__vrr("Error: sellers object not found");
          }
        }else{
          $this->__vrr("Error: Cart object not found");
        }
      }else{
        $this->__vrr("Error: ".$result->message);
      }
      if(isset($result->memory)) $memory = $result->memory;
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
