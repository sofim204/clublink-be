<?php
require_once(SENECORE."runner_controller.php");
class Produk_Wanted extends Runner_Controller {
  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
    $this->url = base_url('api_mobile/');
  }

  protected function __addProduct(){
    $postdata = array();
    $postdata['keyword_text'] = "Sofa";
    return $postdata;
  }
  protected function __editProduct(){
    $postdata = array();
    $postdata['id'] = 2;
    $postdata['keyword_text'] = "Sofa";
    return $postdata;
  }

  public function index(){
    echo 'runner product wanted index';
  }
  public function list(){
    $dcount = 0;
    $ddata = array();
    $dlast = new stdClass();
    $dpass = 0;
    $this->__vo("Product Wanted");
    $i=1;
    /*product list*/
    $url = $this->url.'wanted'.$this->url_aft;
    $this->__vu("Product Wanted List",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->wanteds)){
        $ddata = $result->data->wanteds;
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
    /*end product list*/

    /*product add*/
    $i++;
    $url = $this->url.'wanted/baru'.$this->url_aft; //url call
    $this->__vu("Add Product Wanted",$url); //testing title

    $postdata = $this->__addProduct();
    //start test
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          $memory = $result->memory;
          $this->__vrp("Passed");
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
    /*end product add*/

    /*product edit*/
    $i++;
    $url = $this->url.'wanted/edit'.$this->url_aft; //url call
    $this->__vu("Edit Product Wanted",$url); //testing title

    $postdata = $this->__editProduct();
    //start test
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          $memory = $result->memory;
          $this->__vrp("Passed");
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
    /*end product edit*/

    /*product hapus*/
    $i++;
    $url = $this->url.'wanted/hapus'.$this->url_aft; //url call
    $this->__vu("Delete Product Wanted",$url); //testing title

    $postdata['id'] = 1;
    //start test
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          $memory = $result->memory;
          $this->__vrp("Passed");
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
    /*end product hapus*/
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
