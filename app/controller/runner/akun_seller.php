<?php
require_once(SENECORE."runner_controller.php");
class Akun_Seller extends Runner_Controller {
  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
    $this->url = base_url('api_mobile/');
  }

  public function index(){
    echo '<p></p>';
    echo '<h3>Akun: Seller</h3>';
    echo '<ul>';
    echo '<li><a href="'.base_url("runner/akun_seller/list/").'">List</a></li>';
    echo '<li><a href="'.base_url("runner/akun_seller/bread/").'">bread</a></li>';
    echo '</ul>';
  }
  public function list(){
    $dcount = 0;
    $ddata = array();
    $dlast = new stdClass();
    $dpass = 0;
    $c_produk_id = 0;
    $d_order_id = 0;
    $this->__vo("Account/Seller");
    $i=1;

    //changed to seller session
    $this->__setApiSess('65KMZDR');
    $this->__resetURL();

    /*produkDraft*/
    $url = $this->url.'seller/produk/draft'.$this->url_page;
    $this->__vu("Product: Draft",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->produk_total)){
        $dcount = (int) $result->data->produk_total;
        if($dcount>0){
          $ddata = $result->data->produks;
          if(is_array($ddata) && count($ddata)){
            $err = '';
            $dpass=0;
            if(isset($ddata[0]->id)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'Missing ID';
            }
            if($dpass>0){
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error ".$err);
            }
          }else{
            $this->__vrh("Passed with empty result");
          }
        }else{
          $this->__vrh("Passed with empty result");
        }
      }else{
        $this->__vrh("Passed with empty result");
      }
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    /*end produkDraft*/

    /*produkActive*/
    $i++;
    $url = $this->url.'seller/produk/active'.$this->url_page;
    $this->__vu("Product: Active",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->produk_total)){
        $dcount = (int) $result->data->produk_total;
        if($dcount>0){
          $ddata = $result->data->produks;
          if(is_array($ddata) && count($ddata)){
            $err = '';
            $dpass=0;
            if(isset($ddata[0]->id)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'Missing ID';
            }
            if($dpass>0){
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error ".$err);
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
    /*end produkActive*/

    /*free product*/
    $i++;
    $url = $this->url.'produk_gratis/seller'.$this->url_page;
    $this->__vu("Free Product",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->produk_total)){
        $dcount = (int) $result->data->produk_total;
        if($dcount>0){
          $ddata = $result->data->produks;
          if(is_array($ddata) && count($ddata)){
            $err = '';
            $dpass=0;
            if(isset($ddata[0]->id)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'Missing ID';
            }
            if($dpass>0){
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error ".$err);
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
    /*end free product*/

    /*orderNew*/
    $dlast = new stdClass();
    $i++;
    $url = $this->url.'seller/order/new'.$this->url_aft;
    $this->__vu("List of New Ordered Product",$url);
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
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
              $d_order_id = $ddata[0]->d_order_id;
              $c_produk_id = $ddata[0]->c_produk_id;
            }else{
              $err = 'Missing D_ORDER_ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'Missing d_order_cdate';
            }
            if($dpass>=2){
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error ".$err);
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

    /*order process*/
    $i++;
    $url = $this->url.'seller/order/process'.$this->url_aft;
    $this->__vu("List Processed Order",$url);
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
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
              $d_order_id = $ddata[0]->d_order_id;
              $c_produk_id = $ddata[0]->c_produk_id;
            }else{
              $err = 'Missing D_ORDER_ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'Missing d_order_cdate';
            }
            if($dpass>=2){
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error ".$err);
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
    /*end process*/

    /*order delivered*/
    $i++;
    $url = $this->url.'seller/order/delivered'.$this->url_aft;
    $this->__vu("List Delivered Order",$url);
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
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
              $d_order_id = $ddata[0]->d_order_id;
              $c_produk_id = $ddata[0]->c_produk_id;
            }else{
              $err = 'Missing D_ORDER_ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'Missing d_order_cdate';
            }
            if($dpass>=2){
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error ".$err);
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
    /*end delivered*/

    /*order received*/
    $i++;
    $url = $this->url.'seller/order/received'.$this->url_aft;
    $this->__vu("List Received Order",$url);
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
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
              $d_order_id = $ddata[0]->d_order_id;
              $c_produk_id = $ddata[0]->c_produk_id;
            }else{
              $err = 'Missing D_ORDER_ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'Missing d_order_cdate';
            }
            if($dpass>=2){
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error ".$err);
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
    /*end received*/

    /*order succeed*/
    $i++;
    $url = $this->url.'seller/order/succeed'.$this->url_aft;
    $this->__vu("List Succeed Order",$url);
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
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
              $d_order_id = $ddata[0]->d_order_id;
              $c_produk_id = $ddata[0]->c_produk_id;
            }else{
              $err = 'Missing D_ORDER_ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'Missing d_order_cdate';
            }
            if($dpass>=2){
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error ".$err);
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
    /*end succeed*/

    /*order rejected*/
    $i++;
    $url = $this->url.'seller/order/listrejected'.$this->url_aft;
    $this->__vu("List Rejected Order",$url);
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
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
              $d_order_id = $ddata[0]->d_order_id;
              $c_produk_id = $ddata[0]->c_produk_id;
            }else{
              $err = 'Missing D_ORDER_ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'Missing d_order_cdate';
            }
            if($dpass>=2){
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error ".$err);
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
    /*end rejected*/

    /*order expired*/
    $i++;
    $url = $this->url.'seller/order/expired'.$this->url_aft;
    $this->__vu("List Expired Order",$url);
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
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
              $d_order_id = $ddata[0]->d_order_id;
              $c_produk_id = $ddata[0]->c_produk_id;
            }else{
              $err = 'Missing D_ORDER_ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'Missing d_order_cdate';
            }
            if($dpass>=2){
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error ".$err);
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
    /*end succeed*/


    /*order detail*/
    $fd = array();
    $fd['d_order_id'] = $d_order_id;
    $fd['c_produk_id'] = $c_produk_id;
    $url = $this->url.'seller/order/detail'.$this->url_aft;
    $this->__vu("Order Detail: $d_order_id - $c_produk_id",$url);
    $raw = $this->seme_curl->post($url,$fd);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          if(isset($result->data->order->d_order_id)){
            $this->__vrp("Passed");
          }else{
            $this->__vrh("Passed with empty result");
          }
        }else{
          $this->__vrr("Error: ".$result->message);
        }
      }else{
        $this->__vrr("Undefined status");
      }
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    /*end order detail*/

    $this->__ve();
  }

  public function bread(){
    $dpass = 0;
    $dlast = new stdClass();
    $dlast->id = 0;

    echo '---<br />';
    echo '--Runner for Produk/bread--<br />';
    echo '==========================================<br /><br />';

    $i=1;
    $url = $this->url.'produk/baru'.$this->url_aft;
    echo $i.'. Produk Baru<br />';
    echo '-> Calling: '.$url.'<br />';
    echo '-> Result: ';
    $postdata = $this->__add();
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      echo 'Success<br />';
      $response = json_decode($raw->body);
      if(isset($response->status)){
        if($response->status == '200' || $response->status == 200){
          $dpass=1;
          $dlast = $response->data->produk;
          echo 'Produk tambah passed<br />';
        }else{
          echo 'Produk tambah error<br />';
        }
      }else{
        echo 'Wrong response happened<br />';
        echo '<pre>'.print_r($response).'</pre>';
      }
    }else if($raw->header->http_code == 404){
      echo 'Not Found<br />';
    }else if($raw->header->http_code == 500){
      echo 'Error 500<br />';
      echo '<pre>'.$raw->body.'</pre><br />';
    }else{
      echo 'Error occured with code: '.$raw->header->http_code.'<br />';
      echo 'When call api -> '.$url.'<br />';
    }

    $i++;
    $url = $this->url.'produk/detail/'.$dlast->id.$this->url_aft;
    echo $i.'. Produk Detail<br />';
    echo '-> Calling: '.$url.'<br />';
    echo '-> Result: ';
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      echo 'Success<br />';
      $data = json_decode($raw->body);
      if(isset($data->data->status)){
        if($data->data->status == 200) $dpass=1;
      }
      if($dpass){
        echo 'Product detail passed<br />';
      }else{
        echo 'Cannot get product detail<br />';
      }
    }else if($raw->header->http_code == 404){
      echo 'Not Found<br />';
    }else if($raw->header->http_code == 500){
      echo 'Error 500<br />';
      echo '<pre>'.$raw->body.'</pre><br />';
    }else{
      echo 'Error occured with code: '.$raw->header->http_code.'<br />';
      echo 'When call api -> '.$url.'<br />';
    }

    $i++;
    $url = $this->url.'produk/edit/'.$dlast->id.$this->url_aft;
    echo $i.'. Produk Detail<br />';
    echo '-> Calling: '.$url.'<br />';
    echo '-> Result: ';
    $postdata = $this->__edit();
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      echo 'Success<br />';
      $data = json_decode($raw->body);
      if(isset($data->data->status)){
        if($data->data->status == 200) $dpass=1;
      }
      if($dpass){
        echo 'Product edit passed<br />';
      }else{
        echo 'Cannot get product detail<br />';
      }
    }else if($raw->header->http_code == 404){
      echo 'Not Found<br />';
    }else if($raw->header->http_code == 500){
      echo 'Error 500<br />';
      echo '<pre>'.$raw->body.'</pre><br />';
    }else{
      echo 'Error occured with code: '.$raw->header->http_code.'<br />';
      echo 'When call api -> '.$url.'<br />';
    }
  }

}
