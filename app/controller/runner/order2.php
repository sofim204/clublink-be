<?php
require_once(SENECORE."runner_controller.php");
class Order2 extends Runner_Controller {
  var $nation_code = '65';
  var $apikey = 'kmz373ac';
  var $apisess = '65KMZDR';
  var $url = '';
  var $url_aft = '';
  var $shipment = '';

  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
    $shipment =  new stdClass();
    $shipment->type = 'Next Day';
    $shipment->service = 'QXpress';
    $this->shipment = $shipment;
  }

  public function index(){
    echo '<p></p>';
    echo '<h3>Order v.0.2.0 Runner</h3>';
    echo '<ul>';
    echo '<li><a href="'.base_url("runner/order2/checkout/").'">Checkout</a></li>';
    echo '<li><a href="'.base_url("runner/order2/pre_payment/").'">Pre Payment Check</a></li>';
    echo '<li><a href="'.base_url("runner/order2/payment/").'">Payment</a></li>';
    echo '<li><a href="'.base_url("runner/order2/confirmed_by_seller/").'">Confirmed By Seller</a></li>';
    echo '<li><a href="'.base_url("runner/order2/reject_by_seller/").'">Reject By Seller</a></li>';
    echo '<li><a href="'.base_url("runner/order2/reject_by_buyer/").'">Reject By Buyer</a></li>';
    echo '<li><a href="'.base_url("runner/order2/seller_delivered/").'">Delivered By Seller</a></li>';
    echo '<li><a href="'.base_url("runner/order2/completed_test/").'">Completed Test Order (confirmed by Buyer)</a></li>';
    echo '</ul>';
  }

  private function __dataCart(){
    //building native object
    $ppd = new stdClass();
    $ppd->products = array();
    $p = new stdClass();
    $p->id = 6;
    $p->qty = 1;
    $ppd->products[] = $p;
    $postdata = array();

    //post to server in json format
    $postdata['post_data'] = json_encode($ppd);
    return $postdata;
  }
  private function __dataCheck($d_order_id){
    $postdata = array();
    $postdata['d_order_id'] = $d_order_id;
    return $postdata;
  }

  private function __randShip(){
    $ss = array();
    $s = new stdClass();
    $s->service = 'qxpress';
    $s->type = 'same day';
    $ss[] = $s;
    $s = new stdClass();
    $s->service = 'qxpress';
    $s->type = 'next day';
    $ss[] = $s;
    $s = new stdClass();
    $s->service = 'gogovan';
    $s->type = 'next day';
    $ss[] = $s;
    $s = new stdClass();
    $s->service = 'gogovan';
    $s->type = 'same day';
    $ss[] = $s;
    $key = rand(0,(count($ss)-1));
    return $ss[$key];
  }

  private function __dataOngkir($c_produk_id,$qty="",$b_user_alamat_id=""){
    $this->shipment = $this->__randShip();
    $postdata['c_produk_id'] = $c_produk_id;
    $postdata['qty'] = 1;
    $postdata['shipment_service'] = $this->shipment->service;
    $postdata['shipment_type'] = $this->shipment->type;
    $postdata['b_user_alamat_id'] = 1;
    if(strlen($qty) && !empty($qty)) $postdata['qty'] = $qty;
    if(strlen($b_user_alamat_id) && !empty($b_user_alamat_id)) $postdata['b_user_alamat_id'] = $b_user_alamat_id;
    return $postdata;
  }

  private function __dataOrder($c_produk_id="",$shipping_cost="",$shipping_cost_add=""){
    //building native object
    $ppd = new stdClass();
    $ppd->products = array();
    $p = new stdClass();
    $p->id = 6;
    $p->qty = 1;
    $p->shipment_service = "QXpress";
    $p->shipment_type = "Next Day";
    $p->shipment_cost = "6";
    $p->shipment_cost_add = "0";
    if(strlen($c_produk_id) && !empty($c_produk_id)) $p->id = $c_produk_id;
    if(strlen($shipping_cost) && !empty($shipping_cost)) $p->shipment_cost = $shipping_cost;
    if(strlen($shipping_cost_add) && !empty($shipping_cost_add)) $p->shipping_cost_add = $shipping_cost_add;
    $ppd->products[] = $p;
    $postdata = array();

    //post to server in json format
    $postdata['b_user_alamat_id_billing'] = 1;
    $postdata['b_user_alamat_id_shipping'] = 1;
    $postdata['post_data'] = json_encode($ppd);
    return $postdata;
  }
  public function __dataPayNow($d_order_id){
    $delivery_date = date("Y-m-d",strtotime("+2 days"));
    $delivery_time = '12:00:00';

    $ppd = new stdClass();
    $ppd->products = array();

    $p = new stdClass();
    $p->id = 6;
    $p->shipment_type = "Next Day";
    $p->shipment_service = "QXpress";
    $p->delivery_date = $delivery_date;
    $p->delivery_time = $delivery_time;
    $p->shipment_cost = 6;
    $p->shipment_cost_add = 0;
    $ppd->products[] = $p;

    $postdata = array();
    $postdata['d_order_id'] = $d_order_id;
    $postdata['post_data'] = json_encode($ppd);
    return $postdata;
  }
  private function __dataPayment($d_order_id){
    $pd = array();
    $pd['d_order_id'] = $d_order_id;
    $pd['payment_gateway'] = '2c2p';
    $pd['payment_method'] = 'Credit Card or Debit Card';
    $pd['payment_status'] = 'paid';
    $pd['payment_date'] = date("Y-m-d H:i:00");
    $pd['payment_tranid'] = 'SM'.date("Ymd-His");
    $pd['payment_response'] = "From Seme Runner";
    $pd['payment_confirmed'] = 1;
    return $pd;
  }

  public function checkout(){
    $this->__setApiSess('65KMZDS');
    $this->__resetURL();
    //$this->__baseUrl("https://sellon.thecloudalert.com/api_mobile/");
    $cart_products = array();
    $c_produk_id = 0;
    $d_order_id = 0;
    $memory = memory_get_usage();

    $this->__vo("Order v.0.2.0: Checkout (Unpaid Order)");

    //start test produk list
    $this->__setSortCol("stok");
    $this->__setSortDir("desc");
    $url = $this->url.'produk'.$this->url_page;
    $this->__vu("Get Full Stok Product",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->produk_total)){
        $dcount = (int) $result->data->produk_total;
        if($dcount>0){
          $ddata = $result->data->produks;
          if(is_array($ddata) && count($ddata)){
            if(isset($ddata[0]->id)){
              $c_produk_id = $ddata[0]->id;
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error");
            }
          }
        }else{
          $this->__vr("Passed with empty result");
        }
      }
      if(isset($result->memory)) $memory = $result->memory;
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vr("Error 500");
      $this->__vdf($raw->body);
    }else{
      $this->__vr("Error ".$raw->header->http_code);
    }
    $this->__vb($memory);
    //end test product list

    //start cart add testing
    $url = $this->url.'cart/tambah'.$this->url_page;
    $this->__vu("Cart Add",$url);
    $raw = $this->seme_curl->post($url,array("c_produk_id"=>$c_produk_id, "qty"=>1));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if($result->status == 200){
        $this->__vrp("Passed");
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
    //end cart add testing

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
      if(isset($result->memory)) if(isset($result->memory)) $memory = $result->memory;
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

    //stast test cart remove
    $this->__resetPage();
    $url = $this->url.'cart/hapus'.$this->url_page;
    $this->__vu("Cart Remove",$url);
    if(count($cart_products)>0){
      //get first product
      if(isset($cart_products[0]->id)) $c_produk_id = $cart_products[0]->id;
      $raw = $this->seme_curl->post($url,array("c_produk_id"=>$c_produk_id));
      if($raw->header->http_code == 200){
        $result = json_decode($raw->body);
        if(isset($result->memory)) $memory = $result->memory;
        if($result->status == 200){
          $this->__vrp("Passed");
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
    }else{
      $this->__vrh("Skipped: Empty cart");
    }
    $this->__vb($memory);
    //end test cart remove

    //start test cart list
    $url = $this->url.'cart'.$this->url_page;
    $this->__vu("Cart List",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->cart)){
          if(isset($result->data->cart->product_count)){
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: product_count object not found");
          }
        }else{
          $this->__vrr("Error: Cart object not found");
        }
        if(isset($result->memory)) $memory = $result->memory;
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
    //end test cart list

    //start test user alamat
    $b_user_alamat_id = 0;
    $this->__resetPage();
    $url = $this->url.'pelanggan/alamat'.$this->url_page; //url call
    $this->__vu("Get User Address",$url); //testing title
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          if(isset($result->data->alamat)){
            $total = count($result->data->alamat);
            if($total>0){
              $rand = (mt_rand(1,$total))-1;
              if(isset($result->data->alamat[$rand]->id)){
                $this->__vrp("Passed");
                $b_user_alamat_id = $result->data->alamat[$rand]->id;
              }else{
                $this->__vrr("Error: Object on array alamat incorrect format");
              }
            }else{
              $this->__vrr("Error: Object alamat is empty array");
            }
          }else{
            $this->__vrr("Error: Object alamat not found");
          }
          if(isset($result->memory)) $memory = $result->memory;
        }else{
          $this->__vrr("Error: ".$result->message);
        }
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
    //end test  user alamat

    $shipping_cost = 0;
    $shipping_cost_add = 0;
    //start shipment cost
    $url = $this->url.'shipment/rates'.$this->url_aft;
    $this->__vu("Get Shipment Rates",$url);
    $raw = $this->seme_curl->post($url,$this->__dataOngkir($c_produk_id,1,$b_user_alamat_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->shipping_rates)){
          if(isset($result->data->shipping_rates->shipment_cost)){
            $shipping_cost = $result->data->shipping_rates->shipment_cost;
            $shipping_cost_add = $result->data->shipping_rates->shipment_cost_add;
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: ID Order object not found");
          }
        }else{
          $this->__vrr("Error: Order object not found");
        }
        if(isset($result->memory)) $memory = $result->memory;
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

    //start test
    $url = $this->url.'checkout/order'.$this->url_aft;
    $this->__vu("Checkout Order",$url);
    $raw = $this->seme_curl->post($url,$this->__dataOrder($c_produk_id,$shipping_cost,$shipping_cost_add));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->order)){
          if(isset($result->data->order->id)){
            $d_order_id = $result->data->order->id;
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: ID Order object not found");
          }
        }else{
          $this->__vrr("Error: Order object not found");
        }
        if(isset($result->memory)) $memory = $result->memory;
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

  public function pre_payment(){
    $this->__setApiSess('65KMZDS');
    $this->__resetURL();
    //$this->__baseUrl("https://sellon.thecloudalert.com/api_mobile/");
    $cart_products = array();
    $c_produk_id = 0;
    $d_order_id = 0;
    $memory = memory_get_usage();

    $this->__vo("Order v.0.2.0: Pre Payment Check");

    //start test produk list
    $this->__setSortCol("stok");
    $this->__setSortDir("desc");
    $url = $this->url.'buyer/order/waiting'.$this->url_page;
    $this->__vu("Get Unpaid Order",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            if(isset($ddata[0]->d_order_id)){
              $d_order_id = $ddata[0]->d_order_id;
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error");
            }
          }
        }else{
          $this->__vr("Passed with empty result");
        }
      }
      if(isset($result->memory)) $memory = $result->memory;
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vr("Error 500");
      $this->__vdf($raw->body);
    }else{
      $this->__vr("Error ".$raw->header->http_code);
    }
    $this->__vb($memory);
    //end test product list

    if(empty($d_order_id)){
      $this->__ve();
      die();
    }

    //start cart add testing
    $url = $this->url.'buyer/order/detail/'.$d_order_id.$this->url_page;
    $this->__vu("Order Detail: ".$d_order_id,$url);
    $raw = $this->seme_curl->get($url,array("c_produk_id"=>$c_produk_id, "qty"=>1));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->order->id)){
        $this->__vrp("Passed");
      }else{
        $this->__vrr("Error: Object Order->id not found");
      }
      if(isset($result->memory)) $memory = $result->memory;
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
    //end cart add testing

    //start test
    $url = $this->url.'payment/pre'.$this->url_aft;
    $this->__vu("Pre Payment",$url);
    $raw = $this->seme_curl->post($url,$this->__dataCheck($d_order_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        $this->__vrp("Passed");
        if(isset($result->memory)) $memory = $result->memory;
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

  public function payment(){
    $this->__setApiSess('65KMZDS');
    $this->__resetURL();
    //$this->__baseUrl("https://sellon.thecloudalert.com/api_mobile/");
    $cart_products = array();
    $c_produk_id = 0;
    $d_order_id = 0;
    $memory = memory_get_usage();

    $this->__vo("Order v.0.2.0: After Payment / Forward to Seller");

    //start test produk list
    $this->__setSortCol("stok");
    $this->__setSortDir("desc");
    $url = $this->url.'buyer/order/waiting'.$this->url_page;
    $this->__vu("Get Unpaid Order",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            if(isset($ddata[0]->d_order_id)){
              $d_order_id = $ddata[0]->d_order_id;
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error");
            }
          }
        }else{
          $this->__vr("Passed with empty result");
        }
      }
      if(isset($result->memory)) $memory = $result->memory;
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vr("Error 500");
      $this->__vdf($raw->body);
    }else{
      $this->__vr("Error ".$raw->header->http_code);
    }
    $this->__vb($memory);
    //end test product list

    if(empty($d_order_id)){
      $this->__ve();
      die();
    }

    //start cart add testing
    $url = $this->url.'buyer/order/detail/'.$d_order_id.$this->url_page;
    $this->__vu("Order Detail: ".$d_order_id,$url);
    $raw = $this->seme_curl->get($url,array("c_produk_id"=>$c_produk_id, "qty"=>1));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->order->id)){
        $this->__vrp("Passed");
      }else{
        $this->__vrr("Error: Object Order->id not found");
      }
      if(isset($result->memory)) $memory = $result->memory;
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
    //end cart add testing

    //start test
    $url = $this->url.'payment/pre'.$this->url_aft;
    $this->__vu("Pre Payment",$url);
    $raw = $this->seme_curl->post($url,$this->__dataCheck($d_order_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        $this->__vrp("Passed");
        if(isset($result->memory)) $memory = $result->memory;
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

    //start test
    $url = $this->url.'payment/process'.$this->url_aft;
    $this->__vu("Checkout: Payment Process",$url);
    $raw = $this->seme_curl->post($url,$this->__dataPayment($d_order_id));
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
      $this->__vdf($raw->body);
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($memory);
    //end test

    $this->__ve();
  }

  public function completed_test(){
    $this->__setApiSess('65KMZDS');
    $this->__resetURL();
    //$this->__baseUrl("https://sellon.thecloudalert.com/api_mobile/");
    $this->__baseUrl("https://sellondev.thecloudalert.com/api_mobile/");
    $cart_products = array();
    $c_produk_id = 0;
    $d_order_id = 0;
    $memory = memory_get_usage();

    $this->__vo("Order v.0.2.0: Completed Test");

    //start test produk list
    $this->__setSortCol("stok");
    $this->__setSortDir("desc");
    $url = $this->url.'produk'.$this->url_page;
    $this->__vu("Get Full Stok Product",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->produk_total)){
        $dcount = (int) $result->data->produk_total;
        if($dcount>0){
          $ddata = $result->data->produks;
          if(is_array($ddata) && count($ddata)){
            if(isset($ddata[0]->id)){
              $c_produk_id = $ddata[0]->id;
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error");
            }
          }
        }else{
          $this->__vr("Passed with empty result");
        }
      }
      if(isset($result->memory)) $memory = $result->memory;
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vr("Error 500");
      $this->__vdf($raw->body);
    }else{
      $this->__vr("Error ".$raw->header->http_code);
    }
    $this->__vb($memory);
    //end test product list

    //start cart add testing
    $url = $this->url.'cart/tambah'.$this->url_page;
    $this->__vu("Cart Add",$url);
    $raw = $this->seme_curl->post($url,array("c_produk_id"=>$c_produk_id, "qty"=>1));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if($result->status == 200){
        $this->__vrp("Passed");
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
    //end cart add testing

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
      if(isset($result->memory)) if(isset($result->memory)) $memory = $result->memory;
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

    //stast test cart remove
    $this->__resetPage();
    $url = $this->url.'cart/hapus'.$this->url_page;
    $this->__vu("Cart Remove",$url);
    if(count($cart_products)>0){
      //get first product
      if(isset($cart_products[0]->id)) $c_produk_id = $cart_products[0]->id;
      $raw = $this->seme_curl->post($url,array("c_produk_id"=>$c_produk_id));
      if($raw->header->http_code == 200){
        $result = json_decode($raw->body);
        if(isset($result->memory)) $memory = $result->memory;
        if($result->status == 200){
          $this->__vrp("Passed");
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
    }else{
      $this->__vrh("Skipped: Empty cart");
    }
    $this->__vb($memory);
    //end test cart remove

    //start test cart list
    $url = $this->url.'cart'.$this->url_page;
    $this->__vu("Cart List",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->cart)){
          if(isset($result->data->cart->product_count)){
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: product_count object not found");
          }
        }else{
          $this->__vrr("Error: Cart object not found");
        }
        if(isset($result->memory)) $memory = $result->memory;
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
    //end test cart list

    //start test
    $b_user_alamat_id = 0;
    $this->__resetPage();
    $url = $this->url.'pelanggan/alamat'.$this->url_page; //url call
    $this->__vu("Get User Address",$url); //testing title
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->status)){
        if($result->status == 200){
          if(isset($result->data->alamat)){
            $total = count($result->data->alamat);
            if($total>0){
              $rand = (mt_rand(1,$total))-1;
              if(isset($result->data->alamat[$rand]->id)){
                $this->__vrp("Passed");
                $b_user_alamat_id = $result->data->alamat[$rand]->id;
              }else{
                $this->__vrr("Error: Object on array alamat incorrect format");
              }
            }else{
              $this->__vrr("Error: Object alamat is empty array");
            }
          }else{
            $this->__vrr("Error: Object alamat not found");
          }
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

    //start test
    $shipping_cost = 0;
    $shipping_cost_add = 0;
    $url = $this->url.'shipment/rates'.$this->url_aft;
    $this->__vu("Get Shipping rates",$url);
    $raw = $this->seme_curl->post($url,$this->__dataOngkir($c_produk_id,1,$b_user_alamat_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if($result->status == 200){
        if(isset($result->data->shipping_rates)){
          if(isset($result->data->shipping_rates->shipment_cost)){
            $shipping_cost = $result->data->shipping_rates->shipment_cost;
            $shipping_cost_add = $result->data->shipping_rates->shipment_cost_add;
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: Address on Order object not found");
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

    //start test
    $url = $this->url.'checkout/order'.$this->url_aft;
    $this->__vu("Checkout: Order",$url);
    $raw = $this->seme_curl->post($url,$this->__dataOrder($c_produk_id,$shipping_cost,$shipping_cost_add));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->order)){
          if(isset($result->data->order->id)){
            $d_order_id = $result->data->order->id;
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: ID Order object not found");
          }
        }else{
          $this->__vrr("Error: Order object not found");
        }
        if(isset($result->memory)) $memory = $result->memory;
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
    $this->__vb($memory);
    //end test

    //start test
    $url = $this->url.'payment/pre'.$this->url_aft;
    $this->__vu("Payment Pre Check",$url);
    $raw = $this->seme_curl->post($url,$this->__dataCheck($d_order_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if($result->status == 200){
        $this->__vrp("Passed");
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

    //start test
    $url = $this->url.'payment/process'.$this->url_aft;
    $this->__vu("Payment Process",$url);
    $raw = $this->seme_curl->post($url,$this->__dataPayment($d_order_id));
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
      $this->__vdf($raw->body);
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($memory);
    //end test

    //change session to seller
    $this->__setApiSess('65KMZDR');
    $this->__resetURL();

    /*orderNew*/
    $dlast = new stdClass();
    $url = $this->url.'seller/order/new'.$this->url_aft;
    $this->__vu("Get New Ordered Product",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            $err = '';
            $dpass=0;
            if(isset($ddata[0]->c_produk_id)){
              $dpass++;
              $dlast = $ddata[0];
              $d_order_id = $ddata[0]->d_order_id;
              $c_produk_id = $ddata[0]->c_produk_id;
              $this->__vrp("Passed");
            }else{
              $err = 'Missing D_ORDER_ID';
            }
          }else{
            $this->__vrh("Passed with empty result");
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
    $this->__vb($memory);
    /*end orderNew*/


    /*order detail seller*/
    $dlast = new stdClass();
    $url = $this->url.'seller/order/detail'.$this->url_aft;
    $this->__vu("Detail Order: $d_order_id - $c_produk_id",$url);
    $raw = $this->seme_curl->post($url,array("d_order_id"=>$d_order_id,"c_produk_id"=>$c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            $err = '';
            $dpass=0;
            if(isset($ddata[0]->c_produk_id)){
              $dpass++;
              $dlast = $ddata[0];
              $c_produk_id = $ddata[0]->c_produk_id;
              $this->__vrp("Passed");
            }else{
              $err = 'Missing D_ORDER_ID';
            }
          }else{
            $this->__vrh("Passed with empty result");
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
    $this->__vb($memory);
    /*end order detail seller*/

    /*start confirm order by seller*/
    $this->__resetURL();
    $url = $this->url.'seller/order/confirmed'.$this->url_aft;
    $this->__vu("Confirm Order: $d_order_id - $c_produk_id",$url);
    $raw = $this->seme_curl->post($url,array("d_order_id"=>$d_order_id,"c_produk_id"=>$c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error ".$result->message);
        }
      }else{
        $this->__vrr("Error status not found");
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
    /*end confirm order by seller*/

    /*start download waybill*/
    $url = $this->url.'seller/waybill/print/'.$d_order_id.'/'.$c_produk_id.'/'.$this->url_aft;
    $this->__vu("Download WayBill",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $this->__vrp("Passed");
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    /*end download waybill*/

    /*start Delivery Process By Seller*/
    $this->__resetURL();
    $url = $this->url.'seller/order/delivery_process/'.$d_order_id.'/'.$c_produk_id.'/'.$this->url_aft;
    $this->__vu("Delivery Process",$url);
    $raw = $this->seme_curl->post($url,array("d_order_id"=>$d_order_id,"c_produk_id"=>$c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error ".$result->message);
        }
      }else{
        $this->__vrr("Error status not found");
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
    /*end Delivery Process By Seller*/

    //change session to buyer
    $this->__setApiSess('65KMZDS');
    $this->__resetURL();

    /*start Confirm Delivery by Buyer*/
    $url = $this->url.'buyer/delivery/confirmed/'.$this->url_aft;
    $this->__vu("Confirm Delivery by Buyer",$url);
    $raw = $this->seme_curl->post($url,array("d_order_id"=>$d_order_id,"c_produk_id"=>$c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error ".$result->message);
        }
      }else{
        $this->__vrr("Error status not found");
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
    /*end Confirm Delivery by Buyer*/

    $this->__ve();
  }

  public function confirmed_by_seller(){
    $this->__setApiSess('65KMZDS');
    $this->__resetURL();
    //$this->__baseUrl("https://sellon.thecloudalert.com/api_mobile/");
    $cart_products = array();
    $c_produk_id = 0;
    $d_order_id = 0;
    $memory = memory_get_usage();

    $this->__vo("Order v.0.2.0: Reject by Seller");

    //change session to seller
    $this->__setApiSess('65KMZDR');
    $this->__resetURL();

    /*orderNew*/
    $dlast = new stdClass();
    $url = $this->url.'seller/order/new'.$this->url_aft;
    $this->__vu("[Seller] Get New Ordered Product",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            $err = '';
            $dpass=0;
            if(isset($ddata[0]->c_produk_id)){
              $dpass++;
              $dlast = $ddata[0];
              $d_order_id = $ddata[0]->d_order_id;
              $c_produk_id = $ddata[0]->c_produk_id;
              $this->__vrp("Passed");
            }else{
              $err = 'Missing D_ORDER_ID';
            }
          }else{
            $this->__vrh("Passed with empty result");
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
    /*end orderNew*/

    /*start reject order by seller*/
    $this->__resetURL();
    $url = $this->url.'seller/order/confirmed'.$this->url_aft;
    $this->__vu("[Seller] Confirm Order: $d_order_id - $c_produk_id",$url);
    $raw = $this->seme_curl->post($url,array("d_order_id"=>$d_order_id,"c_produk_id"=>$c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error ".$result->message);
        }
        $this->__vd($raw->body);
      }else{
        $this->__vrr("Error status not found");
        $this->__vdf($raw->body);
      }

    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
      $this->__vdf($raw->body);
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    /*end confirm order by seller*/

    $this->__ve();
  }

  public function reject_by_seller(){
    $this->__setApiSess('65KMZDS');
    $this->__resetURL();
    //$this->__baseUrl("https://sellon.thecloudalert.com/api_mobile/");
    $cart_products = array();
    $c_produk_id = 0;
    $d_order_id = 0;
    $memory = memory_get_usage();

    $this->__vo("Order v.0.2.0: Reject by Seller");

    //change session to seller
    $this->__setApiSess('65KMZDR');
    $this->__resetURL();

    /*orderNew*/
    $dlast = new stdClass();
    $url = $this->url.'seller/order/new'.$this->url_aft;
    $this->__vu("[Seller] Get New Ordered Product",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            $err = '';
            $dpass=0;
            if(isset($ddata[0]->c_produk_id)){
              $dpass++;
              $dlast = $ddata[0];
              $d_order_id = $ddata[0]->d_order_id;
              $c_produk_id = $ddata[0]->c_produk_id;
              $this->__vrp("Passed");
            }else{
              $err = 'Missing D_ORDER_ID';
            }
          }else{
            $this->__vrh("Passed with empty result");
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
    /*end orderNew*/

    /*start reject order by seller*/
    $this->__resetURL();
    $url = $this->url.'seller/order/rejected'.$this->url_aft;
    $this->__vu("[Seller] Reject Order",$url);
    $raw = $this->seme_curl->post($url,array("d_order_id"=>$d_order_id,"c_produk_id"=>$c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error ".$result->message);
        }
      }else{
        $this->__vrr("Error status not found");
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
    /*end confirm order by seller*/

    /*start download waybill*/
    $url = $this->url.'seller/waybill/print/'.$d_order_id.'/'.$c_produk_id.'/'.$this->url_aft;
    $this->__vu("Download WayBill",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $this->__vrp("Passed");
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    /*end download waybill*/

    /*start Delivery Process By Seller*/
    $this->__resetURL();
    $url = $this->url.'seller/order/delivery_process/'.$dlast->d_order_id.'/'.$dlast->c_produk_id.'/'.$this->url_aft;
    $this->__vu("Delivery Process",$url);
    $raw = $this->seme_curl->post($url,array("d_order_id"=>$dlast->d_order_id,"c_produk_id"=>$dlast->c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error ".$result->message);
        }
      }else{
        $this->__vrr("Error status not found");
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
    /*end Delivery Process By Seller*/

    //change session to buyer
    $this->__setApiSess('65KMZDS');
    $this->__resetURL();

    /*start Confirm Delivery by Buyer*/
    $url = $this->url.'buyer/delivery/confirmed/'.$this->url_aft;
    $this->__vu("Confirm Delivery by Buyer",$url);
    $raw = $this->seme_curl->post($url,array("d_order_id"=>$dlast->d_order_id,"c_produk_id"=>$dlast->c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error ".$result->message);
        }
      }else{
        $this->__vrr("Error status not found");
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
    /*end reject order by seller*/

    $this->__ve();
  }

  public function reject_by_buyer(){
    $this->__setApiSess('65KMZDS');
    $this->__resetURL();
    //$this->__baseUrl("https://sellon.thecloudalert.com/api_mobile/");
    $cart_products = array();
    $c_produk_id = 0;
    $d_order_id = 0;
    $memory = memory_get_usage();

    $this->__vo("Order Test: Reject by Buyer");

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
      if(isset($result->memory)) if(isset($result->memory)) $memory = $result->memory;
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
    $this->__vu("Cart Remove",$url);
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

    //start test produk list
    $this->__setSortCol("stok");
    $this->__setSortDir("desc");
    $url = $this->url.'produk'.$this->url_page;
    $this->__vu("Produk with many Stok",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->produk_total)){
        $dcount = (int) $result->data->produk_total;
        if($dcount>0){
          $ddata = $result->data->produks;
          if(is_array($ddata) && count($ddata)){
            if(isset($ddata[0]->id)){
              $c_produk_id = $ddata[0]->id;
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error");
            }
          }
        }else{
          $this->__vr("Passed with empty result");
        }
      }
      if(isset($result->memory)) $memory = $result->memory;
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vr("Error 500");
    }else{
      $this->__vr("Error ".$raw->header->http_code);
    }
    $this->__vb($memory);
    //end test product list

    //start cart add testing
    $url = $this->url.'cart/tambah'.$this->url_page;
    $this->__vu("Cart Add",$url);
    $raw = $this->seme_curl->post($url,array("c_produk_id"=>$c_produk_id, "qty"=>1));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if($result->status == 200){
        $this->__vrp("Passed");
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
    //end cart add testing

    //start test cart list
    $url = $this->url.'cart'.$this->url_page;
    $this->__vu("Cart List",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->cart)){
          if(isset($result->data->cart->product_count)){
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: product_count object not found");
          }
        }else{
          $this->__vrr("Error: Cart object not found");
        }
        if(isset($result->memory)) $memory = $result->memory;
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
    //end test cart list

    //start test
    $url = $this->url.'cart/paynow'.$this->url_aft;
    $this->__vu("Cart PayNow",$url);
    $raw = $this->seme_curl->post($url,$this->__dataCart());
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->order)){
          if(isset($result->data->order->id)){
            $d_order_id = $result->data->order->id;
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: ID Order object not found");
          }
        }else{
          $this->__vrr("Error: Order object not found");
        }
        if(isset($result->memory)) $memory = $result->memory;
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

    //start test
    $b_user_alamat_id = 0;
    $this->__resetPage();
    $url = $this->url.'pelanggan/alamat'.$this->url_page; //url call
    $this->__vu("Get User Address",$url); //testing title
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          if(isset($result->data->alamat)){
            $total = count($result->data->alamat);
            if($total>0){
              $rand = (mt_rand(1,$total))-1;
              if(isset($result->data->alamat[$rand]->id)){
                $this->__vrp("Passed");
                $b_user_alamat_id = $result->data->alamat[$rand]->id;
              }else{
                $this->__vrr("Error: Object on array alamat incorrect format");
              }
            }else{
              $this->__vrr("Error: Object alamat is empty array");
            }
          }else{
            $this->__vrr("Error: Object alamat not found");
          }
          if(isset($result->memory)) $memory = $result->memory;
        }else{
          $this->__vrr("Error: ".$result->message);
        }
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
    $url = $this->url.'checkout/shipping'.$this->url_aft;
    $this->__vu("Checkout: Set Shipping address",$url);
    $raw = $this->seme_curl->post($url,array("d_order_id"=>$d_order_id,"b_user_alamat_id"=>$b_user_alamat_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->order)){
          if(isset($result->data->order->addresses)){
            if(isset($result->data->order->addresses->shipping)){
              if(isset($result->data->order->addresses->shipping->d_order_id)){
                $this->__vrp("Passed");
              }else{
                $this->__vrr("Error: d_order_id on shipping address object not found");
              }
            }else{
              $this->__vrr("Error: shipping object on address object not found");
            }
          }else{
            $this->__vrr("Error: Address on Order object not found");
          }
        }else{
          $this->__vrr("Error: Order object not found");
        }
        if(isset($result->memory)) $memory = $result->memory;
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

    //start test
    $url = $this->url.'checkout/paynow'.$this->url_aft;
    $this->__vu("Checkout: Paynow",$url);
    $raw = $this->seme_curl->post($url,$this->__dataPaynow($d_order_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->order)){
          if(isset($result->data->order->addresses)){
            if(isset($result->data->order->addresses->shipping)){
              if(isset($result->data->order->addresses->shipping->d_order_id)){
                $this->__vrp("Passed");
              }else{
                $this->__vrr("Error: d_order_id on shipping address object not found");
              }
            }else{
              $this->__vrr("Error: shipping object on address object not found");
            }
          }else{
            $this->__vrr("Error: Address on Order object not found");
          }
        }else{
          $this->__vrr("Error: Order object not found");
        }
        if(isset($result->memory)) $memory = $result->memory;
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

    //start test
    $url = $this->url.'checkout/payment_process/'.$this->url_aft;
    $this->__vu("Checkout: Payment Process",$url);
    $raw = $this->seme_curl->post($url,$this->__dataPayment($d_order_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if($result->status == 200){
        $this->__vrp("Passed");
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

    //change session to seller
    $this->__setApiSess('65KMZDR');
    $this->__resetURL();

    /*orderNew*/
    $dlast = new stdClass();
    $url = $this->url.'seller/order/new'.$this->url_aft;
    $this->__vu("List of New Ordered Product",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            $err = '';
            $dpass=0;
            if(isset($ddata[0]->c_produk_id)){
              $dpass++;
              $dlast = $ddata[0];
              $c_produk_id = $ddata[0]->c_produk_id;
              $this->__vrp("Passed");
            }else{
              $err = 'Missing D_ORDER_ID';
            }
          }else{
            $this->__vrh("Passed with empty result");
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
      $this->__vdf($raw->body);
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($memory);
    /*end orderNew*/

    /*start confirm order by seller*/
    $this->__resetURL();
    $url = $this->url.'seller/order/confirmed'.$this->url_aft;
    $this->__vu("Confirm Order",$url);
    $raw = $this->seme_curl->post($url,array("d_order_id"=>$d_order_id,"c_produk_id"=>$c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error ".$result->message);
        }
      }else{
        $this->__vrr("Error status not found");
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
    /*end confirm order by seller*/

    /*start download waybill*/
    $url = $this->url.'seller/waybill/print/'.$d_order_id.'/'.$c_produk_id.'/'.$this->url_aft;
    $this->__vu("Download WayBill",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $this->__vrp("Passed");
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
      $this->__vdf($raw->body);
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    /*end download waybill*/

    /*start Delivery Process By Seller*/
    $this->__resetURL();
    $url = $this->url.'seller/order/delivery_process/'.$dlast->d_order_id.'/'.$dlast->c_produk_id.'/'.$this->url_aft;
    $this->__vu("Delivery Process",$url);
    $raw = $this->seme_curl->post($url,array("d_order_id"=>$dlast->d_order_id,"c_produk_id"=>$dlast->c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error ".$result->message);
        }
      }else{
        $this->__vrr("Error status not found");
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
    /*end Delivery Process By Seller*/

    //change session to buyer
    $this->__setApiSess('65KMZDS');
    $this->__resetURL();

    /*start Reject Delivery by Buyer*/
    $url = $this->url.'buyer/delivery/rejected'.$this->url_aft;
    $this->__vu("Reject Delivery by Buyer",$url);
    $raw = $this->seme_curl->post($url,array("d_order_id"=>$dlast->d_order_id,"c_produk_id"=>$dlast->c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error ".$result->message);
        }
      }else{
        $this->__vrr("Error status not found");
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
    /*end Reject Delivery by Buyer*/

    $this->__ve();
  }

  public function seller_delivered(){
    $this->__setApiSess('65KMZDS');
    $this->__resetURL();
    //$this->__baseUrl("https://sellon.thecloudalert.com/api_mobile/");
    $cart_products = array();
    $c_produk_id = 0;
    $d_order_id = 0;
    $memory = memory_get_usage();

    $this->__vo("Order Test: Seller Delivered");

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
      if(isset($result->memory)) if(isset($result->memory)) $memory = $result->memory;
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
    $this->__vu("Cart Remove",$url);
    if(count($cart_products)>0){
      //get first product
      if(isset($cart_products[0]->id)) $c_produk_id = $cart_products[0]->id;
      $raw = $this->seme_curl->post($url,array("c_produk_id"=>$c_produk_id));
      if($raw->header->http_code == 200){
        $result = json_decode($raw->body);
        if(isset($result->memory)) $memory = $result->memory;
        if($result->status == 200){
          $this->__vrp("Passed");
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
    }else{
      $this->__vrh("Skipped: Empty cart");
    }
    $this->__vb($memory);
    //end test cart remove

    //start test produk list
    $this->__setSortCol("stok");
    $this->__setSortDir("desc");
    $url = $this->url.'produk'.$this->url_page;
    $this->__vu("Produk with many Stok",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->produk_total)){
        $dcount = (int) $result->data->produk_total;
        if($dcount>0){
          $ddata = $result->data->produks;
          if(is_array($ddata) && count($ddata)){
            if(isset($ddata[0]->id)){
              $c_produk_id = $ddata[0]->id;
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error");
            }
          }
        }else{
          $this->__vr("Passed with empty result");
        }
      }
      if(isset($result->memory)) $memory = $result->memory;
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vr("Error 500");
    }else{
      $this->__vr("Error ".$raw->header->http_code);
    }
    $this->__vb($memory);
    //end test product list

    //start cart add testing
    $url = $this->url.'cart/tambah'.$this->url_page;
    $this->__vu("Cart Add",$url);
    $raw = $this->seme_curl->post($url,array("c_produk_id"=>$c_produk_id, "qty"=>1));
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
    $this->__vb($memory);
    //end cart add testing

    //start test cart list
    $url = $this->url.'cart'.$this->url_page;
    $this->__vu("Cart List",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->cart)){
          if(isset($result->data->cart->product_count)){
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: product_count object not found");
          }
        }else{
          $this->__vrr("Error: Cart object not found");
        }
        if(isset($result->memory)) $memory = $result->memory;
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
    $this->__vb($memory);
    //end test cart list

    //start test
    $url = $this->url.'cart/paynow'.$this->url_aft;
    $this->__vu("Cart PayNow",$url);
    $raw = $this->seme_curl->post($url,$this->__dataCart());
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->order)){
          if(isset($result->data->order->id)){
            $d_order_id = $result->data->order->id;
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: ID Order object not found");
          }
        }else{
          $this->__vrr("Error: Order object not found");
        }
        if(isset($result->memory)) $memory = $result->memory;
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
    $this->__vb($memory);
    //end test

    //start test
    $b_user_alamat_id = 0;
    $this->__resetPage();
    $url = $this->url.'pelanggan/alamat'.$this->url_page; //url call
    $this->__vu("Get User Address",$url); //testing title
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->status)){
        if($result->status == 200){
          if(isset($result->data->alamat)){
            $total = count($result->data->alamat);
            if($total>0){
              $rand = (mt_rand(1,$total))-1;
              if(isset($result->data->alamat[$rand]->id)){
                $this->__vrp("Passed");
                $b_user_alamat_id = $result->data->alamat[$rand]->id;
              }else{
                $this->__vrr("Error: Object on array alamat incorrect format");
              }
            }else{
              $this->__vrr("Error: Object alamat is empty array");
            }
          }else{
            $this->__vrr("Error: Object alamat not found");
          }
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

    //start test
    $url = $this->url.'checkout/shipping'.$this->url_aft;
    $this->__vu("Checkout: Set Shipping address",$url);
    $raw = $this->seme_curl->post($url,array("d_order_id"=>$d_order_id,"b_user_alamat_id"=>$b_user_alamat_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if($result->status == 200){
        if(isset($result->data->order)){
          if(isset($result->data->order->addresses)){
            if(isset($result->data->order->addresses->shipping)){
              if(isset($result->data->order->addresses->shipping->d_order_id)){
                $this->__vrp("Passed");
              }else{
                $this->__vrr("Error: d_order_id on shipping address object not found");
              }
            }else{
              $this->__vrr("Error: shipping object on address object not found");
            }
          }else{
            $this->__vrr("Error: Address on Order object not found");
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

    //start test
    $url = $this->url.'checkout/paynow'.$this->url_aft;
    $this->__vu("Checkout: Paynow",$url);
    $raw = $this->seme_curl->post($url,$this->__dataPaynow($d_order_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->order)){
          if(isset($result->data->order->addresses)){
            if(isset($result->data->order->addresses->shipping)){
              if(isset($result->data->order->addresses->shipping->d_order_id)){
                $this->__vrp("Passed");
              }else{
                $this->__vrr("Error: d_order_id on shipping address object not found");
              }
            }else{
              $this->__vrr("Error: shipping object on address object not found");
            }
          }else{
            $this->__vrr("Error: Address on Order object not found");
          }
        }else{
          $this->__vrr("Error: Order object not found");
        }
        if(isset($result->memory)) $memory = $result->memory;
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

    //start test
    $url = $this->url.'checkout/payment_process/'.$this->url_aft;
    $this->__vu("Checkout: Payment Process",$url);
    $raw = $this->seme_curl->post($url,$this->__dataPayment($d_order_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if($result->status == 200){
        $this->__vrp("Passed");
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

    //change session to seller
    $this->__setApiSess('65KMZDR');
    $this->__resetURL();

    /*orderNew*/
    $dlast = new stdClass();
    $url = $this->url.'seller/order/new'.$this->url_aft;
    $this->__vu("List of New Ordered Product",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            $err = '';
            $dpass=0;
            if(isset($ddata[0]->c_produk_id)){
              $dpass++;
              $dlast = $ddata[0];
              $c_produk_id = $ddata[0]->c_produk_id;
              $this->__vrp("Passed");
            }else{
              $err = 'Missing D_ORDER_ID';
            }
          }else{
            $this->__vrh("Passed with empty result");
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
    $this->__vb($memory);
    /*end orderNew*/

    /*start confirm order by seller*/
    $this->__resetURL();
    $url = $this->url.'seller/order/confirmed'.$this->url_aft;
    $this->__vu("Confirm Order",$url);
    $raw = $this->seme_curl->post($url,array("d_order_id"=>$d_order_id,"c_produk_id"=>$c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error ".$result->message);
        }
      }else{
        $this->__vrr("Error status not found");
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
    /*end confirm order by seller*/

    /*start download waybill*/
    $url = $this->url.'seller/waybill/print/'.$d_order_id.'/'.$c_produk_id.'/'.$this->url_aft;
    $this->__vu("Download WayBill",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $this->__vrp("Passed");
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb(0);
    /*end download waybill*/

    /*start Delivery Process By Seller*/
    $this->__resetURL();
    $url = $this->url.'seller/order/delivery_process/'.$dlast->d_order_id.'/'.$dlast->c_produk_id.'/'.$this->url_aft;
    $this->__vu("Delivery Process",$url);
    $raw = $this->seme_curl->post($url,array("d_order_id"=>$dlast->d_order_id,"c_produk_id"=>$dlast->c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error ".$result->message);
        }
      }else{
        $this->__vrr("Error status not found");
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
    /*end Delivery Process By Seller*/

    $this->__ve();
  }
}
