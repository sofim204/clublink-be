<?php
require_once(SENECORE."runner_controller.php");
class Address extends Runner_Controller {
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

  //Alamat Baru
  protected function __dataAlamatBaru(){
    $postdata = array();
    $postdata['address_status'] = "A0";
    $postdata['judul'] = "Office";
    $postdata['penerima_nama'] = "Runner Dawn";
    $postdata['penerima_telp'] = "8765 7471";
    $postdata['alamat'] = rand(1,19)." Pasir Panjang";
    $postdata['alamat2'] = "#".rand(0,99);
    $postdata['provinsi'] = "";
    $postdata['kabkota'] = "Singapore";
    $postdata['kecamatan'] = "District 06";
    $postdata['kelurahan'] = "Pasir Panjang";
    $postdata['kodepos'] = "238800";
    $postdata['latitude'] = "1.3016794";
    $postdata['longitude'] = "103.8358879";
    $postdata['catatan'] = "Fajar Down";
    $postdata['is_default'] = "1";
    return $postdata;
  }

  protected function __dataAlamatEdit(){
    $postdata = array();
      $postdata['address_status'] = "A0";
      $postdata['judul'] = "dasdada";
      $postdata['penerima_nama'] = "dasadasa";
      $postdata['penerima_telp'] = "111312312";
      $postdata['alamat'] = rand(1,19)." Burn rd";
      $postdata['alamat2'] = "#".rand(0,99);
      $postdata['provinsi'] = "";
      $postdata['kabkota'] = "Singapore";
      $postdata['kecamatan'] = "District 06";
      $postdata['kelurahan'] = "Pasir Panjang";
      $postdata['kodepos'] = "369977";
      $postdata['latitude'] = "1.3016794";
      $postdata['longitude'] = "103.8358879";
      $postdata['catatan'] = "Fajar Down";
      $postdata['is_default'] = "1";
      return $postdata;
  }

  private function __testAlamatJenis(){
    $this->__setSortCol("id");
    $this->__setSortDir("asc");
    $url = $this->url.'pelanggan/alamat_jenis'.$this->url_page;
    $this->__vu("Jenis Alamat",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->alamat_jenis[0])){
        if(isset($result->data->alamat_jenis[0]->nation_code)){
          $this->__vrp("Passed");
        }else{
          $this->__vrr("Error");
        }
      }else{
        $this->__vr("Passed with empty result");
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
  }

  private function __testAlamatList(){
    //start test produk list
    $this->__setSortCol("id");
    $this->__setSortDir("asc");
    $url = $this->url.'pelanggan/alamat'.$this->url_page;
    $this->__vu("Alamat List",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->alamat)){
        if(isset($result->data->alamat[0]->nation_code)){
          $this->alamat = $result->data->alamat[0];
          $this->__vrp("Passed");
        }else{
          $this->__vr("Passed with empty result");
        }
      }else{
        $this->__vrr("Error");
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

  private function __testAlamatTambah(){
    $url = $this->url.'pelanggan/alamat_baru'.$this->url_page;
    $this->__vu("Alamat Baru",$url);
    $raw = $this->seme_curl->post($url,$this->__dataAlamatBaru());
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

  private function __testAlamatEdit(){
    $url = $this->url.'pelanggan/alamat_edit/'.$this->alamat->id.$this->url_page;
    $this->__vu("Alamat Edit: ".$this->alamat->id,$url);
    $raw = $this->seme_curl->post($url,$this->__dataAlamatEdit());
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

  private function __testAlamatHapus(){
    //start test cart remove
    $this->__resetPage();
    $url = $this->url.'pelanggan/alamat_hapus/'.$this->alamat->id.$this->url_page;
    $this->__vu("Alamat Hapus",$url);
    //get first product

    $raw = $this->seme_curl->get($url);
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
  }

  private function __testAlamatDefaultGet(){
    //start test cart remove
    $this->__resetPage();
    $url = $this->url.'pelanggan/alamat_default_get/'.$this->url_page;
    $this->__vu("Get Alamat Default",$url);
    //get first product

    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $this->mu = $result->memory;
      if($result->status == 200){
        if(isset($result->data->alamat_default->id)){
          $this->__vrp("Passed");
          $this->alamat = $result->data->alamat_default;
        }else{
          $this->__vrr("Error: Object alamat default not found");
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
    $this->__vb($this->mu);
  }

  private function __testAlamatDefaultSet(){
    //start test cart remove
    $this->__resetPage();
    $url = $this->url.'pelanggan/alamat_default/'.$this->alamat->id.$this->url_page;
    $this->__vu("Set Alamat Default: ".$this->alamat->id,$url);
    //get first product

    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->memory)) $this->mu = $result->memory;
      if($result->status == 200){
        if(isset($result->data->alamat)){
          if(isset($result->data->alamat[0]->id)){
            $this->__vrp("Passed");
            $this->alamat = $result->data->alamat[0];
          }else{
            $this->__vrh("Passed with empty result");
          }
        }else{
          $this->__vrr("Error: Object alamat not found");
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
    $this->alamat = new stdClass();

    //open test
    $this->__vo("User Address");

    //start call test procedure
    $this->__testAlamatJenis();
    $this->__testAlamatList();
    if(!isset($this->alamat->id)){
      $this->__testAlamatTambah();
      $this->__testAlamatList();
    }
    $this->__testAlamatEdit();
    $this->__testAlamatDefaultGet();
    if(!isset($this->alamat->id)){
      $this->__testAlamatTambah();
      $this->__testAlamatList();
      $this->__testAlamatDefaultSet();
    }
    $this->__testAlamatHapus();
    $this->__testAlamatList();
    //finish call test procedure

    //close test
    $this->__ve();
  }
}
