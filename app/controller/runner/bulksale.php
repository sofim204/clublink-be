<?php
require_once(SENECORE."runner_controller.php");
class BulkSale extends Runner_Controller {
  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
    $this->url = base_url('api_mobile/');
    $this->url = "https://sellon.thecloudalert.com/api_mobile/";
    $this->url = "https://cms-sgmaster.sellon.net/api_mobile/";
  }

  protected function __add(){
    $postdata = array();
    $postdata['b_user_alamat_id'] = 1;
    $postdata['name'] = "saefullah";
    $postdata['phone'] = 1032442233030;
    $postdata['description_long'] = "t is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).";
    $postdata['agent_name'] = "ujang";
    $postdata['agent_licence'] = "agent01";
    $postdata['company_name'] = "company";
    $postdata['foto1'] = curl_file_create(SENEROOT."skin/admin/img/logo-sellon.png");
    $postdata['foto2'] = curl_file_create(SENEROOT."skin/admin/img/logo-sellon.png");
    $postdata['foto3'] = curl_file_create(SENEROOT."skin/admin/img/logo-sellon.png");
    $postdata['foto4'] = curl_file_create(SENEROOT."skin/admin/img/logo-sellon.png");
    $postdata['foto5'] = curl_file_create(SENEROOT."skin/admin/img/logo-sellon.png");
    return $postdata;
  }

  protected function __addGuest(){
    $postdata = array();
    $postdata['b_user_alamat_id'] = 1;
    $postdata['name'] = "guest";
    $postdata['phone'] = 86713321;
    $postdata['description_long'] = "1 desk 3 chairs";
    $postdata['agent_name'] = "";
    $postdata['agent_licence'] = "";
    $postdata['company_name'] = "";
    $postdata['foto1'] = curl_file_create(SENEROOT."skin/admin/img/logo-sellon.png");
    $postdata['foto2'] = curl_file_create(SENEROOT."skin/admin/img/logo-sellon.png");
    $postdata['foto3'] = curl_file_create(SENEROOT."skin/admin/img/logo-sellon.png");
    $postdata['foto4'] = curl_file_create(SENEROOT."skin/admin/img/logo-sellon.png");
    $postdata['foto5'] = curl_file_create(SENEROOT."skin/admin/img/logo-sellon.png");
    return $postdata;
  }

  protected function __edit(){
    $postdata = array();
    $postdata['b_user_alamat_id'] = 1;
    $postdata['name'] = "saadullah";
    $postdata['phone'] = 1032442233030;
    $postdata['description_long'] = "t is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).";
    return $postdata;
  }

  protected function __edit2(){
    $postdata = array();
    $postdata['b_user_alamat_id'] = 1;
    $postdata['name'] = "Abdilah";
    $postdata['phone'] = 1032442233030;
    $postdata['description_long'] = "t is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).";
    return $postdata;
  }

  protected function __addPhoto(){
    $postdata['foto'] = curl_file_create(SENEROOT."skin/admin/img/logo-sellon.png");
    return $postdata;
  }

  public function index(){
    echo '<p></p><h3>Bulksale  / Buy It All : </h3>';
    echo '<ul>';
    echo '<li><a href="'.base_url("runner/bulksale/list/").'">List</a></li>';
    echo '<li><a href="'.base_url("runner/bulksale/bread/").'">Bread</a></li>';
    echo '</ul>';
  }
  public function list(){
    $memory = 0;
    $dcount = 0;
    $ddata = array();
    $dlast = new stdClass();
    $dpass = 0;
    $this->__vo("Buy It All");
    $i=1;

    /*bulksale list*/
    $url = $this->url.'bulksale'.$this->url_page;
    $this->__vu("Buy It All List",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      $dcount = $result->data->bulksale_total;
      if(isset($dcount)){
        if($dcount>0){
          $ddata = $result->data->bulksale;
          if(is_array($ddata) && count($ddata)){
            if(isset($ddata[0]->id)){
              $dpass = 1;
              $dlast = $ddata[0];
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error");
            }
          }
        } else {
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
    /*end bulksale list*/

    /*bulksale add*/
    $url = $this->url.'bulksale/baru'.$this->url_aft; //url call
    $this->__vu("Add New bulksale (Guest)",$url); //testing title
    $raw = $this->seme_curl->post($url,$this->__addBulksaleGuest());
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
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
      $this->__vdf($raw->body);
    }else{
      $this->__vrr("Error ".$raw->header->http_code);
    }
    $this->__vb($memory);
    /*end bulksale add*/

    /*bulksale edit*/
    $url = $this->url.'bulksale/edit/'.$dlast->id.$this->url_aft; //url call
    $this->__vu("Edit bulksale ID: $dlast->id",$url); //testing title
    $postdata = $this->__editBulksale();
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
    /*end bulksale edit*/

    /*bulksale add image*/
    $url = $this->url.'bulksale/image_add/1'.$this->url_aft; //url call
    $this->__vu("Add Image bulksale",$url); //testing title
    $postdata = $this->__addPhoto();
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
    /*end bulksale add image*/

    /*bulksale hapus*/
    $url = $this->url.'bulksale/hapus/12'.$this->url_aft; //url call
    $this->__vu("Delete bulksale",$url); //testing title
    $raw = $this->seme_curl->get($url);
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
    $this->__vb($memory);
    /*end bulksale hapus*/

    /*bulksale hapus image*/

    $url = $this->url.'bulksale/image_delete/2/1'.$this->url_aft; //url call
    $this->__vu("Delete image bulksale",$url); //testing title
    $raw = $this->seme_curl->get($url);
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
    $this->__vb($memory);
    /*end bulksale hapus*/

    /*bulksale image default*/
    $url = $this->url.'bulksale/image_default/3/1'.$this->url_aft; //url call
    $this->__vu("Delete image bulksale",$url); //testing title

    //start test
    $raw = $this->seme_curl->get($url);
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
    /*end bulksale image default*/

    /*kategori detail*/
    $url = $this->url.'bulksale/detail/'.$dlast->id.$this->url_aft;
    $this->__vu("Bulksale Detail ID: $dlast->id",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $memory = $result->memory;
      $ddata = $result->data->bulksale;
      if(is_array($ddata) && count($ddata)){
        if(isset($ddata[0]->id)){
          $dpass = 1;
          $dlast = $ddata[0];
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error");
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
    /*end kategori detail*/

    $this->__ve();
  }
  public function bread(){
    $this->__setApiSess('65KMZDR');
    $memory = 0;
    $bulksale_id = 0;
    $this->__vo("BulkSale / Bread");

    //start test
    $i=1;
    $url = $this->url.'bulksale/baru'.$this->url_aft;
    $this->__vu("Produk Baru",$url);
    $raw = $this->seme_curl->post($url,$this->__add());
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
        if(isset($result->data->bulksale)){
          if(isset($result->data->bulksale->id)){
            $bulksale_id = $result->data->bulksale->id;
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: ID bulksale object not found");
          }
        }else{
          $this->__vrr("Error: Produk object not found");
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
    $url = $this->url.'bulksale/edit/'.$bulksale_id.$this->url_aft;
    $this->__vu("Bulksale Edit",$url);
    $raw = $this->seme_curl->post($url,$this->__edit($bulksale_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
        if(isset($result->data->bulksale)){
          if(isset($result->data->bulksale->id)){
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: ID produk object not found");
          }
        }else{
          $this->__vrr("Error: Produk object not found");
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
    $url = $this->url.'bulksale/edit/'.$bulksale_id.$this->url_aft;
    $this->__vu("Bulksale Edit 2",$url);
    $raw = $this->seme_curl->post($url,$this->__edit2($bulksale_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
        if(isset($result->data->bulksale)){
          if(isset($result->data->bulksale->id)){
            $is_passed = 0;
            $errmsg = '';
            $bulksale_id = $result->data->bulksale->id;
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Error: ID produk object not found");
          }
        }else{
          $this->__vrr("Error: Produk object not found");
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

    $this->__ve();
  }
}
