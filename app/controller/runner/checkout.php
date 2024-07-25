<?php
require_once(SENECORE."runner_controller.php");
class Checkout extends Runner_Controller {
  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
    $this->url = base_url('api_mobile/');
  }
  protected function __dataOrderedProducts($d_order_id,$c_produk_id){
    return array(
      "d_order_id"=>$d_order_id,
      "c_produk_id"=>$c_produk_id
    );
  }
  protected function __dataShipping($d_order_id,$b_user_alamat_id){
    return array(
      "d_order_id"=>$d_order_id,
      "b_user_alamat_id"=>$b_user_alamat_id
    );
  }
  protected function __dataShipment($d_order_id){
    $postdata = array();
    $postdata['d_order_id'] = $d_order_id;
    $postdata['c_produk_id'] = 2;
    $postdata['delivery_date'] = date("Y-m-d",strtotime("+2 day"));
    $postdata['delivery_time'] = date("H:i:00");
    return $postdata;
  }

  protected function __dataCartPayNow(){
    //because post data are in json format, we must craeted the object first :D
    $data = array();
    $d = new stdClass();
    $d->id = 6;
    $d->qty = 1;
    $data[] = $d;

    $d = new stdClass();
    $d->products = $data;

    //postdata
    $postdata = array();
    $postdata['post_data'] = json_encode($d);
    return $postdata;
  }

  protected function __dataCartPayNow2(){
    //because post data are in json format, we must craeted the object first :D
    $data = array();
    $d = new stdClass();
    $d->id = 1;
    $d->qty = 2;
    $data[] = $d;
    $d = new stdClass();
    $d->id = 2;
    $d->qty = 3;
    $data[] = $d;
    $d = new stdClass();
    $d->id = 3;
    $d->qty = 4;
    $data[] = $d;
    $d = new stdClass();
    $d->id = 4;
    $d->qty = 2;
    $data[] = $d;
    $d = new stdClass();
    $d->id = 5;
    $d->qty = 1;
    $data[] = $d;
    $d = new stdClass();
    $d->id = 6;
    $d->qty = 1;
    $data[] = $d;
    $d = new stdClass();
    $d->id = 7;
    $d->qty = 1;
    $data[] = $d;

    $d = new stdClass();
    $d->products = $data;

    //postdata
    $postdata = array();
    $postdata['post_data'] = json_encode($d);
    return $postdata;
  }
  protected function __dataPayNow($d_order_id,$postdata_products){
    $pdata = array("products"=>$postdata_products);

    //postdata
    $postdata = array();
    $postdata['d_order_id'] = $d_order_id;
    $postdata['post_data'] = json_encode($pdata);
    return $postdata;
  }
  private function __dataPaymentProcess($d_order_id){
    $postdata = array();
    $postdata['d_order_id'] = $d_order_id;
    $postdata['payment_gateway'] = "RUNNER";
    $postdata['payment_method'] = "Credit Card";
    $postdata['payment_status'] = "paid";
    $postdata['payment_date'] = date("Y-m-d H:i:00");
    $postdata['payment_tranid'] = strtotime("now");
    $postdata['payment_response'] = "a";
    $postdata['payment_confirmed'] = 1;
    $postdata['payment_method'] = "RUNNER";
    $postdata['payment_method'] = "RUNNER";
    return $postdata;
  }
  public function index(){
    echo '<p></p><h3>Runner Checkout Index</h3>';
    echo '<ul>';
    echo '<li><a href="'.base_url("runner/checkout/create_pending/").'">Create pending</a></li>';
    echo '<li><a href="'.base_url("runner/checkout/create_payment_success/").'">Create Payment Success</a></li>';
    echo '<li><a href="'.base_url("runner/checkout/create_pending2/").'">Create Pending with 4 Products</a></li>';
    echo '</ul>';
  }

  public function create_pending2(){
    //$this->__baseUrl("https://sellon.thecloudalert.com/api_mobile/");
    //$this->__resetURL();
    $memory = 0;
    $d_order_id = 0;
    $products = array();
    $this->__vo("Create Order: Pending (Before Payment)");

    //start test
    $url = $this->url.'cart/paynow'.$this->url_page;
    $this->__vu("Cart: Paynow",$url);
    $raw = $this->seme_curl->post($url,$this->__dataCartPayNow2());
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(!isset($result->status)){
        $result = new stdClass();
        $result->status = 0;
      }
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
        $this->__vd($raw->body);
      }else{
        $this->__vrr("Error: ".$result->message);
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
    $this->__vb($memory);
    //end test

    //start test
    $url = $this->url.'buyer/order/detail/'.$d_order_id.$this->url_page;
    $this->__vu("Get Order Detail: $d_order_id",$url);
    $raw = $this->seme_curl->get($url,array("d_order_id"=>$d_order_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
        if(isset($result->data->order)){
          if(isset($result->data->order->id)){
            $d_order_id = $result->data->order->id;
            foreach($result->data->order->sellers as $seller){
              $products[] = $seller;
            }
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
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($memory);
    //end test

    //start test
    $url = $this->url.'checkout/shipping'.$this->url_page;
    $this->__vu("Checkout: Set Shipping Address",$url);
    $raw = $this->seme_curl->post($url,$this->__dataShipping($d_order_id,1));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
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
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($memory);
    //end test

    $this->__vu("Product List",'');
    $this->__vd(json_encode($products));

    //start test
    $postdata_products = array();
    $ij = 1;
    foreach($products as $product){
      $url = $this->url.'shipment'.$this->url_page;
      $this->__vu("Checkout: Get Shipping Rates - $ij",$url);
      $raw = $this->seme_curl->post($url,$this->__dataOrderedProducts($product->d_order_id,$product->c_produk_id));
      if($raw->header->http_code == 200){
        $result = json_decode($raw->body);
        if(isset($result->status)){
          if($result->status == 200){
            if(isset($result->memory)) $memory = $result->memory;
            if(isset($result->data->shipping_rates)){
              if(isset($result->data->shipping_rates->shipment_service)){
                $pdp = new stdClass();
                $pdp->id = $product->c_produk_id;
                $pdp->shipment_service = $result->data->shipping_rates->shipment_service;
                $pdp->shipment_type = $result->data->shipping_rates->shipment_type;
                $pdp->shipment_cost = $result->data->shipping_rates->shipment_cost;
                $pdp->shipment_cost_add = $result->data->shipping_rates->shipment_cost_add;
                $pdp->shipment_cost_sub = $result->data->shipping_rates->shipment_cost_sub;
                $postdata_products[] = $pdp;
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
        $this->__vdf($raw->body);
      }else if($raw->header->http_code == 404){
        $this->__vrr("Not found");
      }else if($raw->header->http_code == 500){
        $this->__vrr("Error 500");
        $this->__vdf($raw->body);
      }else{
        $this->__vrr("Error ".$raw->header->http_code);
      }
      $this->__vb($memory);
      $ij++;
    }
    //end test

    //start test
    $url = $this->url.'checkout/paynow'.$this->url_page;
    $this->__vu("Checkout: Paynow",$url);
    $raw = $this->seme_curl->post($url,$this->__dataPayNow($d_order_id,$postdata_products));
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

  public function create_pending(){
    //$this->__baseUrl("https://sellon.thecloudalert.com/api_mobile/");
    //$this->__resetURL();
    $memory = 0;
    $d_order_id = 0;
    $products = array();
    $this->__vo("Create Order: Pending (Before Payment)");

    //start test
    $url = $this->url.'cart/paynow'.$this->url_page;
    $this->__vu("Cart: Paynow",$url);
    $raw = $this->seme_curl->post($url,$this->__dataCartPayNow());
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(!isset($result->status)){
        $result = new stdClass();
        $result->status = 0;
      }
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
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
      }else{
        $this->__vrr("Error: ".$result->message);
      }
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
      $this->__vd($raw->body);
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($memory);
    //end test

    //start test
    $url = $this->url.'checkout'.$this->url_page;
    $this->__vu("Checkout: Get Quote",$url);
    $raw = $this->seme_curl->post($url,array("d_order_id"=>$d_order_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
        if(isset($result->data->order)){
          if(isset($result->data->order->id)){
            $d_order_id = $result->data->order->id;
            foreach($result->data->order->sellers as $seller){
              foreach($seller->products as $p){
                $products[] = $p;
              }
            }
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
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($memory);
    //end test

    //start test
    $url = $this->url.'checkout/shipping'.$this->url_page;
    $this->__vu("Checkout: Set Shipping Address",$url);
    $raw = $this->seme_curl->post($url,$this->__dataShipping($d_order_id,1));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
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
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($memory);
    //end test

    //start test
    $postdata_products = array();
    $ij = 1;
    foreach($products as $product){
      $url = $this->url.'shipment'.$this->url_page;
      $this->__vu("Checkout: Get Shipping Rates - $ij",$url);
      $raw = $this->seme_curl->post($url,$this->__dataOrderedProducts($product->d_order_id,$product->c_produk_id));
      if($raw->header->http_code == 200){
        $result = json_decode($raw->body);
        if(isset($result->status)){
          if($result->status == 200){
            if(isset($result->memory)) $memory = $result->memory;
            if(isset($result->data->shipping_rates)){
              if(isset($result->data->shipping_rates->shipment_service)){
                $pdp = new stdClass();
                $pdp->id = $product->c_produk_id;
                $pdp->shipment_service = $result->data->shipping_rates->shipment_service;
                $pdp->shipment_type = $result->data->shipping_rates->shipment_type;
                $pdp->shipment_cost = $result->data->shipping_rates->shipment_cost;
                $pdp->shipment_cost_add = $result->data->shipping_rates->shipment_cost_add;
                $pdp->shipment_cost_sub = $result->data->shipping_rates->shipment_cost_sub;
                $postdata_products[] = $pdp;
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
        $this->__vdf($raw->body);
      }else if($raw->header->http_code == 404){
        $this->__vrr("Not found");
      }else if($raw->header->http_code == 500){
        $this->__vrr("Error 500");
      }else{
        $this->__vrr("Error ".$raw->header->http_code);
      }
      $this->__vb($memory);
      $ij++;
    }
    //end test

    //start test
    $url = $this->url.'checkout/paynow'.$this->url_page;
    $this->__vu("Checkout: Paynow",$url);
    $raw = $this->seme_curl->post($url,$this->__dataPayNow($d_order_id,$postdata_products));
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

  public function create_payment_success(){
    $memory = 0;
    $d_order_id = 0;
    $c_produk_id = 0;
    $this->__vo("Create Order: Forward to Seller (After Payment)");

    //start test
    $url = $this->url.'cart/paynow'.$this->url_page;
    $this->__vu("Cart: Paynow",$url);
    $raw = $this->seme_curl->post($url,$this->__dataCartPayNow());
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(!isset($result->status)){
        $result = new stdClass();
        $result->status = 0;
      }
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
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
      }else{
        $this->__vrr("Error: ".$result->message);
      }
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
      $this->__vd($raw->body);
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($memory);
    //end test

    //start test
    $url = $this->url.'checkout'.$this->url_page;
    $this->__vu("Checkout: Get Quote",$url);
    $raw = $this->seme_curl->post($url,array("d_order_id"=>$d_order_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
        if(isset($result->data->order)){
          if(isset($result->data->order->id)){
            $d_order_id = $result->data->order->id;
            foreach($result->data->order->sellers as $seller){
              foreach($seller->products as $p){
                $products[] = $p;
              }
            }
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
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($memory);
    //end test

    //start test
    $url = $this->url.'checkout/shipping'.$this->url_page;
    $this->__vu("Checkout: Set Shipping Address",$url);
    $raw = $this->seme_curl->post($url,$this->__dataShipping($d_order_id,1));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
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
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($memory);
    //end test

    //start test
    $postdata_products = array();
    $ij = 1;
    foreach($products as $product){
      $url = $this->url.'shipment'.$this->url_page;
      $this->__vu("Checkout: Get Shipping Rates - $ij",$url);
      $raw = $this->seme_curl->post($url,$this->__dataOrderedProducts($product->d_order_id,$product->c_produk_id));
      if($raw->header->http_code == 200){
        $result = json_decode($raw->body);
        if(isset($result->status)){
          if($result->status == 200){
            if(isset($result->memory)) $memory = $result->memory;
            if(isset($result->data->shipping_rates)){
              if(isset($result->data->shipping_rates->shipment_service)){
                $pdp = new stdClass();
                $pdp->id = $product->c_produk_id;
                $pdp->shipment_service = $result->data->shipping_rates->shipment_service;
                $pdp->shipment_type = $result->data->shipping_rates->shipment_type;
                $pdp->shipment_cost = $result->data->shipping_rates->shipment_cost;
                $pdp->shipment_cost_add = $result->data->shipping_rates->shipment_cost_add;
                $pdp->shipment_cost_sub = $result->data->shipping_rates->shipment_cost_sub;
                $postdata_products[] = $pdp;
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
        $this->__vdf($raw->body);
      }else if($raw->header->http_code == 404){
        $this->__vrr("Not found");
      }else if($raw->header->http_code == 500){
        $this->__vrr("Error 500");
      }else{
        $this->__vrr("Error ".$raw->header->http_code);
      }
      $this->__vb($memory);
      $ij++;
    }
    //end test

    //start test
    $url = $this->url.'checkout/paynow'.$this->url_page;
    $this->__vu("Checkout: Paynow",$url);
    $raw = $this->seme_curl->post($url,$this->__dataPayNow($d_order_id,$postdata_products));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
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
    $url = $this->url.'checkout/payment_process'.$this->url_aft;
    $this->__vu("Checkout: Payment Process",$url);
    $raw = $this->seme_curl->post($url,$this->__dataPaymentProcess($d_order_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrp("Error: ".$result->message);
        }
      }else{
        $this->__vrr("Error: Undefined result status");
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
