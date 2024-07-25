<?php
require_once(SENECORE."runner_controller.php");
class Address extends Runner_Controller {
  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
    $this->url = base_url('api_mobile/');
    $this->load("api_mobile/b_user_alamat_model","buam");
  }

  public function index(){
    echo 'runner address index';
  }

  public function address_types(){
    $this->__vo("Address/alamat_jenis");

    //test
    $i=1; //iteration
    $url = $this->url.'pelanggan/alamat_jenis'.$this->url_page; //url call
    $this->__vu("Address Types",$url); //testing title

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

  public function alamat(){
    $this->__vo("Address/alamat");

    //test
    $i=1; //iteration
    $url = $this->url.'pelanggan/alamat'.$this->url_page; //url call
    $this->__vu("Address Types",$url); //testing title

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

  //Alamat Baru
  protected function __newAddressTrue(){
    $postdata = array();
      $postdata['address_status'] = "A0";
      $postdata['judul'] = "Office";
      $postdata['penerima_nama'] = "Fajar Dawn";
      $postdata['penerima_telp'] = "6576171717";
      $postdata['alamat'] = "13 Pasir Panjang";
      $postdata['alamat2'] = "Santosa Building lv 13";
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
  protected function __newAddressFalse(){
    $postdata = array();
      $postdata['address_status'] = "A0";
      $postdata['judul'] = "dasdada";
      $postdata['penerima_nama'] = "dasadasa";
      $postdata['penerima_telp'] = "111312312";
      $postdata['alamat'] = "13 Pasir Panjang";
      $postdata['alamat2'] = "Santosa Building lv 13";
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

  public function alamat_baru(){
    $this->__vo("Pelanggan/alamat_baru");

    //test
    $i=1; //iteration
    $url = $this->url.'pelanggan/alamat_baru'.$this->url_page; //url call
    $this->__vu("New Address",$url); //testing title

    $postdata = $this->__newAddressTrue();
    //start test
    $raw = $this->seme_curl->post($url,$postdata);
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

    //user login false
    $i++; //iteration
    $url = $this->url.'pelanggan/alamat_baru'.$this->url_page; //url call
    $this->__vu("New Address False",$url); //testing title

    $postdata = $this->__newAddressFalse();
    //start test
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status != 200){
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

  //Edit Alamat
  protected function __editAddressTrue(){
    $postdata = array();
      $postdata['address_status'] = "A0";
      $postdata['judul'] = "Home";
      $postdata['penerima_nama'] = "Sissy Anytime";
      $postdata['penerima_telp'] = "18818181";
      $postdata['alamat'] = "Tampines Street 11";
      $postdata['alamat2'] = "Jung Hunn Building Lv 8";
      $postdata['provinsi'] = "";
      $postdata['kabkota'] = "Singapore";
      $postdata['kecamatan'] = "District 18";
      $postdata['kelurahan'] = "Tampines";
      $postdata['kodepos'] = "521110";
      $postdata['latitude'] = "";
      $postdata['longitude'] = "";
      $postdata['catatan'] = "Lorem lipsum";
      $postdata['is_default'] = "1";
    return $postdata;
  }
  protected function __editAddressFalse(){
    $postdata = array();
      $postdata['address_status'] = "A0";
      $postdata['judul'] = "dasdada";
      $postdata['penerima_nama'] = "dasadasa";
      $postdata['penerima_telp'] = "111312312";
      $postdata['alamat'] = "13 Pasir Panjang";
      $postdata['alamat2'] = "Santosa Building lv 13";
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

  public function address_edit(){
    $this->__vo("Pelanggan/alamat_edit");

    //test
    $i=1; //iteration
    $url = $this->url.'pelanggan/alamat_edit/1'.$this->url_page; //url call
    $this->__vu("Edit Address",$url); //testing title

    $postdata = $this->__editAddressTrue();
    //start test
    $raw = $this->seme_curl->post($url,$postdata);
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

    //user login false
    $i++; //iteration
    $url = $this->url.'pelanggan/alamat_edit/'.$this->url_page; //url call
    $this->__vu("Edit Address False",$url); //testing title

    $postdata = $this->__editAddressFalse();
    //start test
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->status)){
        if($result->status != 200){
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

  public function alamat_hapus(){
    $this->__vo("Pelanggan/alamat_hapus");

    //test
    $i=1; //iteration
    $nation_code = 65;
    $b_user_id =1;
    $address = $this->buam->getLastId($nation_code,$b_user_id);
    $url = $this->url.'pelanggan/alamat_hapus/3'.$this->url_page; //url call
    $this->__vu("Address Delete",$url); //testing title

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

  public function alamat_default(){
    $this->__vo("Pelanggan/alamat_default");

    //test
    $i=1; //iteration
    $url = $this->url.'pelanggan/alamat_default/10'.$this->url_page; //url call
    $this->__vu("Address Default",$url); //testing title

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

  public function alamat_get_default(){
    $this->__vo("Pelanggan/alamat_default_get");

    //test
    $i=1; //iteration
    $url = $this->url.'pelanggan/alamat_default_get/'.$this->url_page; //url call
    $this->__vu("Address Get Default",$url); //testing title

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

  public function run(){
    $this->address_types();
    $this->alamat();
    $this->alamat_baru();
    $this->address_edit();
    $this->alamat_hapus();
    $this->alamat_default();
    $this->alamat_get_default();
  }


}
