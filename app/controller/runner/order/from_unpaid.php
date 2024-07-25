<?php
require_once(SENECORE."runner_controller.php");
class From_UnPaid extends Runner_Controller {
  var $nation_code = '65';
  var $apikey = 'kmz373ac';
  var $apisess = '65KMZDR';
  var $cart_products = '';
  var $products = '';
  var $product = '';
  var $mu = '';
  var $session = '';
  var $order = '';

  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
    $this->apisess = '65KMZDS';
    $this->cart_products = array();
    $this->product = new stdClass();
    $this->ordered_products = array();
    $this->mu = memory_get_usage(); //memory usage
    $this->session = '65KMZDS';
    $this->order = new stdClass();
  }

  private function __dataCart($datas){
    //building native object
    $ppd = new stdClass();
    $ppd->products = array();
    $postdata = array();

    if(is_array($datas) && count($datas)){
      foreach($datas as $data){
        $p = new stdClass();
        $p->id = $data->c_produk_id;
        $p->qty = 1;
        $ppd->products[] = $p;
      }
    }else{
      $p = new stdClass();
      $p->id = 6;
      $p->qty = 1;
      $ppd->products[] = $p;
    }

    //post to server in json format
    $postdata['post_data'] = json_encode($ppd);
    return $postdata;
  }

  protected function __dataShipping($d_order_id,$c_produk_id,$shipment_service="QXpress",$shipment_type="Next Day"){
    $postdata = array();
    $postdata['d_order_id'] = $d_order_id;
    $postdata['c_produk_id'] = $c_produk_id;
    $postdata['shipment_service'] = $shipment_service;
    $postdata['shipment_type'] = $shipment_type;
    return $postdata;
  }

  protected function __dataCheckout($d_order_id,$ordered_products){
    $pds = new stdClass();
    $pds->products = array();
    if(true){
      foreach($ordered_products as $op){
        $pt = new stdClass();
        $pt->id = $op->id;
        $pt->shipment_type = $op->shipment_type;
        $pt->shipment_service = $op->shipment_service;
        $pt->shipment_cost = $op->shipment_cost;
        $pt->shipment_cost_add = $op->shipment_cost_add;
        $pds->products[] = $pt;
      }
    }else if(false){
      $pt = new stdClass();
      $pt->id = 6;
      $pt->shipment_type = "Next Day";
      $pt->shipment_service = "Gogovan";
      $pt->shipment_cost = 6;
      $pt->shipment_cost_add = 0;
      $pds->products[] = $pt;
    }

    $postdata = array();
    $postdata['d_order_id'] = $d_order_id;
    $postdata['post_data'] = json_encode($pds);
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

  private function __testOrderDetail(){
    $url = $this->url.'buyer/order/detail/'.$this->order->id.$this->url_aft;
    $this->__vu("Order Detail",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->order)){
          if(isset($result->data->order->d_order_id)){
            $this->order = $result->data->order;
            $this->order->id = $this->order->d_order_id;
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: d_order_id object not found");
          }
        }else{
          $this->__vrr("Error: order object not found");
        }
      }else{
        $this->__vrr("Error: ".$result->message);
      }
      if(isset($result->memory)) $this->mu = $result->memory;
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
      $this->__vdf($raw->body);
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($this->mu);
  }

  private function __testOrderUnpaidList(){
    $this->__resetPage();
    $url = $this->url.'buyer/order/waiting'.$this->url_page;
    $this->__vu("Order: Unpaid List",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->orders)){
          if(isset($result->data->orders[0]->d_order_id)){
            $idx = count($result->data->orders)-1;
            $this->order = $result->data->orders[$idx];
            $this->order->id = $this->order->d_order_id;
            $this->__vrp("Passed");
          }else{
            $this->__vrh("Passed with empty result");
          }
        }else{
          $this->__vrr("Error: orders object not found");
        }
      }else{
        $this->__vrr("Error: ".$result->message);
      }
      if(isset($result->memory)) $this->mu = $result->memory;
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
      $this->__vdf($raw->body);
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($this->mu);
  }

  private function __testPelangganAlamat(){
    //start test
    $this->__resetPage();
    $url = $this->url.'pelanggan/alamat'.$this->url_page; //url call
    $this->__vu("Get Current User Address",$url); //testing title
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
                $this->destination = $result->data->alamat[$rand];
              }else{
                $this->__vrr("Error: Object on array alamat incorrect format");
              }
            }else{
              $this->__vrr("Error: Object alamat is empty array");
            }
          }else{
            $this->__vrr("Error: Object alamat not found");
          }
          if(isset($result->memory)) $this->mu = $result->memory;
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
    $this->__vb($this->mu);
    //end test
  }

  private function __testCheckoutAddress(){
    //start test
    $url = $this->url.'checkout/shipping'.$this->url_aft;
    $this->__vu("Checkout: Set Shipping address",$url);
    $raw = $this->seme_curl->post($url,array("d_order_id"=>$this->order->id,"b_user_alamat_id"=>$this->destination->id));
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
        if(isset($result->memory)) $this->mu = $result->memory;
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
    $this->__vb($this->mu);
    //end test
  }

  private function __testCheckoutShipmentRate($c_produk_id,$shipment_service="QXpress",$shipment_type="Next Day"){
    $d_order_id = $this->order->id;
    $this->ordered_products = array();
    $url = $this->url.'shipment'.$this->url_page;
    $this->__vu("Shipment Rate ($d_order_id / $c_produk_id)",$url);
    $raw = $this->seme_curl->post($url,$this->__dataShipping($d_order_id,$c_produk_id,$shipment_service,$shipment_type));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          if(isset($result->memory)) $this->mu = $result->memory;
          if(isset($result->data->shipping_rates)){
            if(isset($result->data->shipping_rates->shipment_service)){
              $op = $result->data->shipping_rates;
              $op->id = $c_produk_id;
              $op->c_produk_id = $c_produk_id;
              $this->ordered_products[] = $op;
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error: shipment_service on shipping_rates object not found");
            }
          }else{
            $this->__vrr("Error: shipping_rates object not found");
          }
        }else{
          $this->__vrr("Error: ".$result->message);
        }
      }else{
        $this->__vrr("Error: JSON result not encoded properly");
      }
      $this->__vdf(json_encode($this->ordered_products));
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
      $this->__vdf($raw->body);
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($this->mu);
  }

  private function __testCheckoutPayNow(){
    $url = $this->url.'checkout/paynow'.$this->url_page;
    $this->__vu("Checkout PayNow",$url);
    $raw = $this->seme_curl->post($url,$this->__dataCheckout($this->order->id,$this->ordered_products));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          if(isset($result->memory)) $this->mu = $result->memory;
          if(isset($result->data->order)){
            if(isset($result->data->order->id)){
              $this->order = $result->data->order;
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error: object id on order object not found");
            }
          }else{
            $this->__vrr("Error: order object not found");
          }
        }else{
          $this->__vrr("Error: ".$result->message);
        }
      }else{
        $this->__vrr("Error: JSON result not encoded properly");
      }
      $this->__vdf($raw->body);
      //$this->__vdf(json_encode($this->__dataCheckout($this->order->id,$this->ordered_products)));
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($this->mu);
  }

  private function __testPrePayment(){
    $url = $this->url.'payment/pre'.$this->url_page;
    $this->__vu("Payment Pre Check",$url);
    $raw = $this->seme_curl->post($url,array("d_order_id"=>$this->order->id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed: ".$result->message);
        }else{
          $this->__vrr("Error: ".$result->message);
        }
      }else{
        $this->__vrr("Error: JSON result not encoded properly");
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
  }

  private function __testPayment(){
    $url = $this->url.'payment/process'.$this->url_page;
    $this->__vu("Payment Process",$url);
    $raw = $this->seme_curl->post($url,$this->__dataPayment($this->order->id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed: ".$result->message);
        }else{
          $this->__vrr("Error: ".$result->message);
        }
      }else{
        $this->__vrr("Error: JSON result not encoded properly");
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
  }

  private function __testCartListAfterPayment(){
    $is_passed = 0;
    $result_data = array();
    $url = $this->url.'cart'.$this->url_page;
    $this->__vu("Check Cart After Paynow",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->cart)){
          $result_data = $result->data->cart;
        }else{
          $this->__vrr("Error: Cart object not found");
        }
      }else{
        $this->__vrr("Error: ".$result->message);
      }
      if(isset($result->memory)) $this->mu = $result->memory;
      $this->__vdf(json_encode($result->data->cart));
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
      $this->__vdf($raw->body);
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }

    if(count($result_data->sellers)==0){
      $this->__vrp("Passed: Product not found in cart");
    }else{
      $prds = array();
      foreach($this->order->sellers as $seller){
        foreach($seller->products as $product){
          $prds[$product->id] = $product;
        }
      }
      $is_passed = 1;
      foreach($this->cart_products as $cp){
        if(isset($prds[$cp->c_produk_id])){
          $is_passed = 0;
          break;
        }
      }
      if($is_passed){
        $this->__vrp("Passed: Product not found in cart");
      }else{
        $this->__vrr("Error: Product found in cart");
      }
    }
    $this->__vb($this->mu);
  }

  public function index(){
    //initial
    $this->session = '65KMZDS'; //change to buyer session
    $this->__setApiSess($this->session);

    //override endpoint
    //$this->__baseUrl("https://sellon.thecloudalert.com/api_mobile/");
    //$this->__baseUrl("https://sellondev.thecloudalert.com/api_mobile/");
    $this->__resetURL();

    //declare variable
    $this->cart_products = array();
    $this->products = array();
    $this->c_produk_id = 0;
    $this->d_order_id = 0;
    $this->order = new stdClass();
    $this->ordered_products = array();
    $this->destination = new stdClass();

    //open test
    $this->__vo("Order: From UnPaid -> Paid");

    //start call test procedure
    $this->__testOrderUnpaidList();
    $this->__testOrderDetail();
    $this->__testPelangganAlamat();
    $this->__testCheckoutAddress();
    foreach($this->order->sellers as $seller){
      foreach($seller->products as $product){
        $this->__testCheckoutShipmentRate($product->id,'QXpress','Same Day');
      }
    }
    $this->__testCheckoutPayNow();
    $this->__testPrePayment();
    $this->__testPayment();
    $this->__testCartListAfterPayment();
    //finish call test procedure

    //close test
    $this->__ve();
  }
}
