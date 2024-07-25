<?php
require_once(SENECORE."runner_controller.php");
class Create_Shipment extends Runner_Controller {
  var $nation_code = '65';
  var $apikey = 'kmz373ac';
  var $apisess = '65KMZDR';
  var $cart_products = '';
  var $products = '';
  var $product = '';
  var $mu = '';

  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
    $this->apisess = '65KMZDS';
    $this->cart_products = array();
    $this->product = new stdClass();
    $this->ordered_products = array();
    $this->mu = memory_get_usage(); //memory usage
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
    if(true){
      $ppd->products = array();
      $p = new stdClass();
      $p->id = 35;
      $p->qty = 2;
      $ppd->products[] = $p;
      $p = new stdClass();
      $p->id = 36;
      $p->qty = 1;
      //$ppd->products[] = $p;
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

  private function __testProductActive(){
    $url = $this->url.'seller/produk/active'.$this->url_aft;
    $this->__vu("Produk Active",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->produks)){
          if(isset($result->data->produks[0])){
            $max = count($result->data->produks);
            $idx = rand(0,$max-1);
            $this->product = $result->data->produks[$idx];
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: c_produk_id object not found");
          }
        }else{
          $this->__vrr("Error: produks object not found");
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

  private function __testCartList(){
    $url = $this->url.'cart'.$this->url_page;
    $this->__vu("Cart List",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          if(isset($result->data->cart)){
            if(isset($result->data->cart->sellers)){
              $this->__vrp("Passed");
              foreach($result->data->cart->sellers as $seller){
                foreach($seller->products as $product){
                  $this->cart_products[] = $product;
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
        if(isset($result->memory)) $this->mu = $result->memory;
        $this->__vd($raw->body);
      }else{
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
    $this->__vb($this->mu);
  }

  private function __testProdukListStok(){
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
          $max = count($ddata);
          if(is_array($ddata) && $max){
            $this->products = $ddata;
            if(isset($ddata[0])){
              $idx = rand(0,$max-1);
              $this->product = $ddata[$idx];
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error");
            }
          }else{
            $this->__vrr("Error: array products not found ");
          }
        }else{
          $this->__vr("Passed with empty result");
        }
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
    //end test product list
  }

  private function __testProdukList(){
    //start test produk list
    $this->__setSortCol("id");
    $this->__setSortDir("asc");
    $url = $this->url.'produk'.$this->url_page;
    $this->__vu("Get Produk",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->produk_total)){
        $dcount = (int) $result->data->produk_total;
        if($dcount>0){
          $ddata = $result->data->produks;
          $max = count($ddata);
          if(is_array($ddata) && $max){
            $this->products = $ddata;
            if(isset($ddata[0])){
              $idx = rand(0,$max-1);
              $this->product = $ddata[$idx];
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error");
            }
          }else{
            $this->__vrr("Error: array products not found ");
          }
        }else{
          $this->__vr("Passed with empty result");
        }
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
    //end test product list
  }

  private function __testCartTambah(){
    //start cart add testing
    $url = $this->url.'cart/tambah'.$this->url_page;
    $this->__vu("Cart Tambah",$url);
    $raw = $this->seme_curl->post($url,array("c_produk_id"=>$this->product->id, "qty"=>1));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $this->mu = $result->memory;
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
    $this->__vb($this->mu);
    //end cart add testing
  }
  private function __testCartRemove(){
    //start test cart remove
    $this->__resetPage();
    $url = $this->url.'cart/hapus'.$this->url_page;
    $this->__vu("Cart Remove",$url);
    //get first product

    $raw = $this->seme_curl->post($url,array("c_produk_id"=>$this->product->id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $this->mu = $result->memory;
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
    $this->__vb($this->mu);

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
    //end test cart list
  }
  private function __testCartPayNow(){
    //start test
    $url = $this->url.'cart/paynow'.$this->url_aft;
    $this->__vu("Cart PayNow",$url);
    $raw = $this->seme_curl->post($url,$this->__dataCart($this->cart_products));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->order)){
          if(isset($result->data->order->id)){
            $d_order_id = $result->data->order->id;
            $this->order = $result->data->order;
            $this->ordered_products = $result->data->order->sellers;
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: ID Order object not found");
          }
        }else{
          $this->__vrr("Error: Order object not found");
        }
        if(isset($result->memory)) $this->mu = $result->memory;
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
    $this->__vb($this->mu);
    //end test
  }

  private function __testCartListAfterPayNow(){
    $is_passed = 0;
    $url = $this->url.'cart'.$this->url_page;
    $this->__vu("Check Cart After Paynow",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->cart)){
          if(isset($result->data->cart->sellers)){
            foreach($result->data->cart->sellers as $seller){
              foreach($seller->products as $product){
                $this->cart_products[] = $product;
              }
            }
            $is_passed = 1;
          }else{
            $this->__vrr("Error: sellers object not found");
          }
        }else{
          $this->__vrr("Error: Cart object not found");
        }
      }else{
        $this->__vrr("Error: ".$result->message);
      }
      if(isset($result->memory)) $this->mu = $result->memory;
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
      $this->__vdf($raw->body);
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }

    if($is_passed){
      $is_passed = 0;
      $prds = array();
      foreach($this->order->sellers as $seller){
        foreach($seller->products as $product){
          $prds[$product->c_produk_id] = $product;
        }
      }
      foreach($this->cart_products as $cp){
        if(isset($prds[$cp->c_produk_id])){
          $is_passed = 1;
          break;
        }
      }
      if($is_passed){
        $this->__vrp("Passed: Product found in cart");
      }else{
        $this->__vrr("Error: Product not found in cart");
      }
    }else{
      $this->__vrr("Error: cannot get cart list after paynow");
    }
    //$this->__vdf(json_encode($this->order->sellers));
    $this->__vb($this->mu);
  }
  private function __testGetOrderDetail($d_order_id){
    $is_passed = 0;
    $url = $this->url.'buyer/order/detail/'.$d_order_id.$this->url_aft;
    $this->__vu("Order Detail: ".$d_order_id,$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->data->order)){
          if(isset($result->data->order->sellers)){
            $this->order = $result->data->order;
            foreach($result->data->order->sellers as $seller){
              foreach($seller->products as $product){
                $this->ordered_products[] = $product;
              }
            }
            $is_passed = 1;
          }else{
            $this->__vrr("Error: sellers object not found");
          }
        }else{
          $this->__vrr("Error: Cart object not found");
        }
      }else{
        $this->__vrr("Error: ".$result->message);
      }
      if(isset($result->memory)) $this->mu = $result->memory;
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
      $this->__vdf($raw->body);
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }

    if($is_passed){
      $is_passed = 0;
      $prds = array();
      foreach($this->order->sellers as $seller){
        foreach($seller->products as $product){
          $prds[$product->id] = $product;
        }
      }
      foreach($this->cart_products as $cp){
        if(isset($prds[$cp->c_produk_id])){
          $is_passed = 1;
          break;
        }
      }
      if($is_passed){
        $this->__vrp("Passed: Product found in cart");
      }else{
        $this->__vrr("Error: Product not found in cart");
      }
    }else{
      $this->__vrr("Error: cannot get cart list after paynow");
    }
    //$this->__vdf(json_encode($this->order->sellers));
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
      if(isset($result->status)){
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
      }else{
        $this->__vrr("Error: Output is not in json format");
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
        $this->__vd(json_encode($this->ordered_products));
      }else{
        $this->__vrr("Error: JSON result not encoded properly");
        $this->__vdf($this->ordered_products);
      }

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
      $this->__vdf($raw->body);
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
      $this->__vdf($raw->body);
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
      $this->__vd($raw->body);
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
    $this->apisess = '65KMZDS';
    $this->__setApiSess($this->apisess);

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
    $this->__vo("Order: Create Shipment");

    //start call test procedure
    $this->__testCartList();
    if(count($this->cart_products)==0){
      $this->__testProdukList();
      $this->__testCartTambah();
      $this->__testCartRemove();
    }
    //$this->__testProdukListStok();

    //get product from certain user only
    $this->apisess = '65KMZDR'; //change to seller session
    $this->__setApiSess($this->apisess);
    $this->__testProductActive();

    //change to buyer session
    $this->apisess = '65KMZDS';
    $this->__setApiSess($this->apisess);
    $this->__testCartTambah();
    $this->__testProdukList();
    $this->__testCartTambah();
    $this->__testProdukList();
    $this->__testCartTambah();
    $this->__testCartList();
    $this->__testCartPayNow();
    $this->__testCartListAfterPayNow();
    $this->__testGetOrderDetail($this->order->id);
    $this->__testPelangganAlamat();
    $this->__testCheckoutAddress();
    foreach($this->order->sellers as $seller){
      $this->__testCheckoutShipmentRate($seller->c_produk_id,'QXpress','Same Day');
    }
    //$this->__testCheckoutPayNow();
    //$this->__testPrePayment();
    //$this->__testPayment();
    //$this->__testCartListAfterPayment();
    //finish call test procedure

    //close test
    $this->__ve();
  }
}
