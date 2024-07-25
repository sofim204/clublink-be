<?php
require_once(SENECORE."runner_controller.php");
class Free_Product extends Runner_Controller {
  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
    $this->url = base_url('api_mobile/');
  }

  //add Free Product
  protected function __addFreeProductTrue(){
    $postdata = array();
      $postdata['b_user_alamat_id'] = "1";
      $postdata['brand'] = "Runner Industries";
      $postdata['nama'] = "Seme Runner ".date("Y-m-d");
      $postdata['telp'] = "01010101";
      $postdata['deskripsi'] = "Runner ullamco laboris nisi ut aliquip ex ea commodo consequat. sunt in culpa qui officia deserunt mollit anim id est laborum.";
      $postdata['berat'] = "1";
      $postdata['stok'] = "";
      $postdata['satuan'] = "Set";
      $postdata['dimension_long'] = "1";
      $postdata['dimension_width'] = "1";
      $postdata['dimension_height'] = "1";
      $postdata['is_published'] = "1";
      $postdata['foto1'] = curl_file_create(SENEROOT."media/produk/2019/05/65-1-1.jpg");
      $postdata['foto2'] = curl_file_create(SENEROOT."media/produk/2019/05/65-2-1.jpg");
      $postdata['foto3'] = curl_file_create(SENEROOT."media/produk/2019/05/65-3-1.jpg");
      $postdata['foto4'] = "";
      $postdata['foto5'] = "";

    return $postdata;
  }
  protected function __addFreeProductFalse(){
    $postdata = array();
      $postdata['b_user_alamat_id'] = "1";
      $postdata['brand'] = "afafasfs";
      $postdata['nama'] = "afnasas dnkands";
      $postdata['telp'] = "12345678";
      $postdata['deskripsi'] = "Free England Royal Tea Cup is Lorem lipsum dolor sit amet. What is lorem lipsum? Lorem lipsum is dolor sit amet";
      $postdata['berat'] = "1";
      $postdata['stok'] = "";
      $postdata['satuan'] = "Set";
      $postdata['dimension_long'] = "1";
      $postdata['dimension_width'] = "1";
      $postdata['dimension_height'] = "1";
      $postdata['is_published'] = "1";
      $postdata['foto1'] = "";
      $postdata['foto2'] = "";
      $postdata['foto3'] = "";
      $postdata['foto4'] = "";
      $postdata['foto5'] = "";
    return $postdata;
  }

  //Change Free Product
  protected function __changeFreeProductTrue(){
    $postdata = array();
      $postdata['b_user_alamat_id'] = "1";
      $postdata['brand'] = "SomeIn Corporation";
      $postdata['telp'] = "0818181881";
      $postdata['nama'] = "Furniture Edit API";
      $postdata['deskripsi'] = "Test edit dari API untuk user drosanda@outlook.co.id dan ID PRODUK 224";
      $postdata['berat'] = "1";
      $postdata['stok'] = "1";
      $postdata['satuan'] = "Pcs";
      $postdata['dimension_long'] = "1";
      $postdata['dimension_width'] = "1";
      $postdata['dimension_height'] = "1";
      $postdata['is_published'] = "1";
    return $postdata;
  }
  protected function __changeFreeProductFalse(){
    $postdata = array();
    $postdata['b_user_alamat_id'] = "0";
    $postdata['brand'] = "sadadaasd";
    $postdata['telp'] = "0818181888";
    $postdata['nama'] = "dsadasdad";
    $postdata['deskripsi'] = "Test edit";
    $postdata['berat'] = "1";
    $postdata['stok'] = "1";
    $postdata['satuan'] = "Pcs";
    $postdata['dimension_long'] = "1";
    $postdata['dimension_width'] = "1";
    $postdata['dimension_height'] = "1";
    $postdata['is_published'] = "1";
    return $postdata;
  }

  public function index(){
    echo '<p></p><h3>Free Product Runner / Unit Test Index</h3>';
    echo '<ul>';
    echo '<li><a href="'.base_url("runner/free_product/full_test/").'">Free Product Full Test</a></li>';
    echo '</ul>';
  }

  public function full_test(){
    $memory = 0;
    $this->__baseUrl("https://sellon.thecloudalert.com/api_mobile/");
    $this->__vo("Free Product Full Test");

    //test
    $url = $this->url.'produk_gratis/baru'.$this->url_page; //url call
    $this->__vu("Add Free product",$url); //testing title
    $postdata = $this->__addFreeProductTrue();
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error: ".$result->message);
        }
      }else{
        $this->__vr("Error: Undefined result status object");
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
    $url = $this->url.'produk_gratis'.$this->url_page; //url call
    $this->__vu("Get List",$url); //testing title
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->status)){
        if($result->status == 200){
          if(isset($result->data->produks)){
            if($result->data->produks[0]->id){
              $c_freeproduct_id = $result->data->produks[0]->id;
              $this->__vrp("Passed");
            }else{
              $this->__vrh("Passed with empty result");
            }
          }else{
            $this->__vrr("Error: Undefined produks object");
          }
        }else{
          $this->__vrr("Error: ".$result->message);
        }
      }else{
        $this->__vr("Error: Undefined result status object");
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
    $url = $this->url.'produk_gratis/detail/'.$c_freeproduct_id.$this->url_page; //url call
    $this->__vu("Get Detail",$url); //testing title
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->status)){
        if($result->status == 200){
          if($result->data->produk->id){
            $this->__vrp("Passed");
          }else{
            $this->__vrh("Passed with empty result");
          }
        }else{
          $this->__vrr("Error: ".$result->message);
        }
      }else{
        $this->__vr("Error: Undefined result status object");
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
    $url = $this->url.'produk_gratis/seller'.$this->url_page; //url call
    $this->__vu("Get My Free Product",$url); //testing title
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->status)){
        if($result->status == 200){
          if(isset($result->data->produks)){
            if($result->data->produks[0]->id){
              $c_freeproduct_id = $result->data->produks[0]->id;
              $this->__vrp("Passed");
            }else{
              $this->__vrh("Passed with empty result");
            }
          }else{
            $this->__vrr("Error: Undefined produks object");
          }
        }else{
          $this->__vrr("Error: ".$result->message);
        }
      }else{
        $this->__vr("Error: Undefined result status object");
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

    //test
    $url = $this->url."produk_gratis/edit/$c_freeproduct_id".$this->url_page; //url call
    $this->__vu("Edit Free Product ID: $c_freeproduct_id",$url);
    $postdata = $this->__changeFreeProductTrue();
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error: ".$result->message);
        }
      }else{
        $this->__vrr("Error: Undefined result status object");
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

  public function add(){
    $memory = 0;
    $this->__vo("Free Product Add");

    //test
    $url = $this->url.'produk_gratis/baru'.$this->url_page; //url call
    $this->__vu("True scenario",$url); //testing title
    $postdata = $this->__addFreeProductTrue();
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error: ".$result->message);
        }
      }else{
        $this->__vr("Error: Undefined result status object");
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

    $url = $this->url.'produk_gratis/baru'.$this->url_page; //url call
    $this->__vu("False scenario",$url); //testing title
    $postdata = $this->__addFreeProductFalse();
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
    if(isset($result->memory)) $memory = $result->memory;
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status != 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error: ".$result->message);
        }
      }else{
        $this->__vr("Error: Undefined result status object");
      }
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vrr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vrr("Error 500");
      $this->__vdf($raw->body);
    }else{
      $this->__vr("Error ".$raw->header->http_code);
    }
    $this->__vb($memory);
    //end test

    $this->__ve();
  }
  //end add free Product


  public function change(){
    $memory = 0;
    $c_freeproduct_id = 4;
    $this->__vo("Free Product Edit: ID $c_freeproduct_id");

    //test
    $url = $this->url."produk_gratis/edit/$c_freeproduct_id".$this->url_page; //url call
    $this->__vu("True scenario",$url);
    $postdata = $this->__changeFreeProductTrue();
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error: ".$result->message);
        }
      }else{
        $this->__vrr("Error: Undefined result status object");
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

    $url = $this->url."produk_gratis/edit/$c_freeproduct_id".$this->url_page; //url call
    $this->__vu("False scenario",$url);
    $postdata = $this->__changeFreeProductFalse();
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      if(isset($result->status)){
        if($result->status != 200){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error: ".$result->message);
        }
      }else{
        $this->__vrr("Error: Undefined result status object");
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
    //End Change Free Product

    //free_product_delete
  public function delete(){
    $this->__vo("Produk_gratis/hapus");

    //test
    $i=1; //iteration
    $url = $this->url.'produk_gratis/hapus/25/'.$this->url_page; //url call
    $this->__vu("Free Product Delete",$url); //testing title

    //start test
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
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
    //end test

    $this->__ve();
  }
  //End free_product_delete

  //free_product_image_add
  protected function __freeProductImageAdd(){
    $postdata['foto'] = curl_file_create(SENEROOT."media/free/2019/07/sosis.jpg");
    return $postdata;
  }

  public function free_product_image_add(){
    $this->__vo("produk_gratis/image_add");

    //test
    $i=1; //iteration
    $url = $this->url.'produk_gratis/image_add/1/'.$this->url_page; //url call
    $this->__vu("Free product image add",$url); //testing title

    $postdata = $this->__freeProductImageAdd();
    //start test
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          $this->__vr("Passed");
        }else{
          $this->__vr("Error: ".$result->message);
        }
      }
      $this->__vd($raw->body);
    }else if($raw->header->http_code == 404){
      $this->__vr("Not found");
    }else if($raw->header->http_code == 500){
      $this->__vr("Error 500");
    }else{
      $this->__vr("Error ".$raw->header->http_code);
    }
    //end test

    $this->__ve();
  }
  //End free_product_image_add

  //free_product_image_delete
  public function free_product_image_delete(){
    $this->__vo("produk_gratis/image_delete");

    //test
    $i=1; //iteration
    $url = $this->url.'produk_gratis/image_delete/224/1090/'.$this->url_page; //url call
    $this->__vu("Free Product Image Delete",$url); //testing title

    //start test
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
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
    //end test

    $this->__ve();
  }
    //End free_product_image_delete

    //free_product_image_default
  public function free_product_image_default(){
    $this->__vo("produk_gratis/image_default");

    //test
    $i=1; //iteration
    $url = $this->url.'produk_gratis/image_default/1/1/'.$this->url_page; //url call
    $this->__vu("Free Product Image Default",$url); //testing title

    //start test
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
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
    //end test

    $this->__ve();
  }
    //End free_product_image_default

    //free_product_list
  public function free_product_list(){
    $this->__vo("produk_gratis/");

    //test
    $i=1; //iteration
    $url = $this->url.'produk_gratis/'.$this->url_page; //url call
    $this->__vu("Free Product List",$url); //testing title

    //start test
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
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
    //end test

    $this->__ve();
  }
  //End free_product_list

  //free_product_detail
  public function detail(){
    $memory = 0;
    //
    $this->__vo("Produk Gratis");

    //test
    $i=1; //iteration
    $url = $this->url.'produk_gratis/detail/1/'.$this->url_page; //url call
    $this->__vu("Free Product Detail",$url); //testing title

    //start test
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status == 200){
          $memory = $result->memory;
          $is_passed = 0;
          if(isset($result->data->produk->id)){
            $is_passed++;
          }
          if(isset($result->data->produk->nama)){
            $is_passed++;
          }
          if(isset($result->data->produk->b_user_email_seller)){
            $is_passed++;
          }
          if($is_passed>=3){
            $this->__vrp("Passed");
          }else if($is_passed>0){
            $this->__vrh("Passed with missing key");
          }else{
            $this->__vrr("Error with missing key");
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

    $this->__ve();
  }
    //End free_product_detail
}
