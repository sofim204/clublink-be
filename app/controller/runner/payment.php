<?php
require_once(SENECORE."runner_controller.php");
class Payment extends Runner_Controller {
  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
    $this->url = base_url('api_mobile/');
  }

  public function index(){
    echo '<p></p><h3>Payment Unit Test</h3>';
    echo '<ul>';
    echo '<li><a href="'.base_url("runner/payment/process/").'">Payment Process</a></li>';
    echo '</ul>';
  }
  public function list(){
    $memory = 0;
    $dcount = 0;
    $ddata = array();
    $dlast = new stdClass();
    $dpass = 0;
    $i=1;

    //overide base_url
    //$this->__baseUrl("https://sellon.thecloudalert.com/api_mobile/");
    //$this->__resetURL();

    //title
    $this->__vo("Payment Process");

    /*waiting4payment*/
    $url = $this->url.'buyer/order/waiting'.$this->url_aft;
    $this->__vu("Order Waiting For Payment (Unpaid)",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $err = '';
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing d_order_cdate';
            }
            if($dpass>=2){
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error ".$err);
            }
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
    /*end waiting4payment*/

    /*order detail*/
    $last_id = 0;
    $i++;
    if(isset($dlast->d_order_id)) $last_id = $dlast->d_order_id;
    $url = $this->url.'buyer/order/detail/'.$last_id.'/'.$this->url_aft;
    $this->__vu("Order Detail",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->order)){
        $ddata = $result->data->order;
        $dpass = 0;
        $err = '';
        if(isset($ddata->d_order_id)){
          $dpass++;
        }else{
          $err = 'Missing D_ORDER_ID';
        }
        if(isset($ddata->d_order_cdate)){
          $dpass++;
        }else{
          $err = 'Missing d_order_cdate';
        }
        if($dpass>=2){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error ".$err);
        }
      }else{
        $this->__vrh("Passed with empty result ".$err);
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

    /*Pending*/
    $url = $this->url.'buyer/order/pending'.$this->url_aft;
    $this->__vu("Order Pending",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $err = '';
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing d_order_cdate';
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
    /*end pending*/

    /*orderConfirmation*/
    $i++;
    $url = $this->url.'buyer/order/confirmation'.$this->url_aft;
    $this->__vu("Order Seller Confirmation",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing d_order_cdate';
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
    /*orderConfirmation*/

    /*processOrder*/
    $i++;
    $url = $this->url.'buyer/order/process'.$this->url_aft;
    $this->__vu("Order Process",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing d_order_cdate';
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
    /*end processOrder*/

    /*sent delivered*/
    $i++;
    $url = $this->url.'buyer/order/delivered'.$this->url_aft;
    $this->__vu("Order Sent",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing d_order_cdate';
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
    /*end order delivered*/

    /*order received*/
    $i++;
    $url = $this->url.'buyer/order/received'.$this->url_aft;
    $this->__vu("Order Received",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing d_order_cdate';
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
    /*end order received*/

    /*order rejected*/
    $i++;
    $url = $this->url.'buyer/order/listrejected'.$this->url_aft;
    $this->__vu("Order Rejected",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing d_order_cdate';
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
    /*end order rejected*/

    /*order expired*/
    $i++;
    $url = $this->url.'buyer/order/expired'.$this->url_aft;
    $this->__vu("Order Expired",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing d_order_cdate';
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
    /*end order expired*/

    /*order succeed*/
    $i++;
    $url = $this->url.'buyer/order/succeed'.$this->url_aft;
    $this->__vu("Order Succeed",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->order_total)){
        $dcount = (int) $result->data->order_total;
        if($dcount>0){
          $ddata = $result->data->orders;
          if(is_array($ddata) && count($ddata)){
            if(isset($ddata[0]->d_order_id)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing ID';
            }
            if(isset($ddata[0]->d_order_cdate)){
              $dpass++;
              $dlast = $ddata[0];
            }else{
              $err = 'missing d_order_cdate';
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
    /*end order succeed*/
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
