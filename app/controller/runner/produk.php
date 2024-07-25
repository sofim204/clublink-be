<?php
require_once(SENECORE."runner_controller.php");
class Produk extends Runner_Controller {
  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
    $this->url = base_url('api_mobile/');
  }
  protected function __add(){
    $postdata = array();
    $postdata['b_kategori_id'] = 1;
    $postdata['b_kondisi_id'] = 1;
    $postdata['b_berat_id'] = 1;
    $postdata['b_user_alamat_id'] = 1;
    $postdata['brand'] = 'Runner';
    $postdata['nama'] = 'Seblak Favorit Runner';
    $postdata['harga_jual'] = 7.5;
    $postdata['stok'] = 999;
    $postdata['deskripsi'] = 'Seblak is favourite avanger team cracker.

Combined with Super Tea cup from Lorem lipsum dolor sit amet.

<h1>Lorem is lipsum</h1>
Lorem lipsum is dolor sit amet. Amet is dolor, eue.';
    $postdata['satuan'] = 'Pcs';
    $postdata['courier_services'] = "QXpress";
    $postdata['services_duration'] = "NextDay";
    $postdata['vehicle_types'] = "Regular";
    $postdata['dimension_long'] = 100;
    $postdata['dimension_width'] = 50;
    $postdata['dimension_height'] = 10;
    $postdata['berat'] = 1;
    $postdata['is_include_delivery_cost'] = 1;
    $postdata['is_published'] = 1;
    return $postdata;
  }
  protected function __edit($c_produk_id){
    $postdata = array();
    $postdata['c_produk_id'] = $c_produk_id;
    $postdata['b_kategori_id'] = 1;
    $postdata['b_kondisi_id'] = 1;
    $postdata['b_berat_id'] = 1;
    $postdata['b_user_alamat_id'] = 1;
    $postdata['brand'] = 'Runner';
    $postdata['nama'] = 'Edited Seblak Favorit Runner';
    $postdata['harga_jual'] = 12.5;
    $postdata['stok'] = 99;
    $postdata['deskripsi'] = 'Seblak is EDited favourite avanger team cracker.

Combined with Super Tea cup from Lorem lipsum dolor sit amet.

<h1>Lorem is lipsum</h1>
Lorem lipsum is dolor sit amet. Amet is dolor, eue.';
    $postdata['satuan'] = 'Pcs';
    $postdata['courier_services'] = "Gogovan";
    $postdata['services_duration'] = "Sameday";
    $postdata['vehicle_types'] = "Regular";
    $postdata['dimension_long'] = 100;
    $postdata['dimension_width'] = 50;
    $postdata['dimension_height'] = 10;
    $postdata['berat'] = 1;
    $postdata['is_include_delivery_cost'] = 1;
    $postdata['is_published'] = 1;
    return $postdata;
  }
  protected function __editQXpressSameDay($id){
    $postdata = array();
    $postdata['c_produk_id'] = $id;
    $postdata['b_kategori_id'] = 1;
    $postdata['b_kondisi_id'] = 1;
    $postdata['b_berat_id'] = 1;
    $postdata['b_user_alamat_id'] = 1;
    $postdata['brand'] = 'Runner';
    $postdata['nama'] = 'QXPress Same Day Test';
    $postdata['harga_jual'] = 12.5;
    $postdata['stok'] = 99;
    $postdata['deskripsi'] = 'This product has QXpress and same day';
    $postdata['satuan'] = 'Pcs';
    $postdata['courier_services'] = "QXpress";
    $postdata['services_duration'] = "Sameday";
    $postdata['vehicle_types'] = "Regular";
    $postdata['dimension_long'] = 99;
    $postdata['dimension_width'] = 50;
    $postdata['dimension_height'] = 10;
    $postdata['berat'] = 29;
    $postdata['is_include_delivery_cost'] = 1;
    $postdata['is_published'] = 1;
    return $postdata;
  }
  protected function __editQXpressNextDay($id){
    $postdata = array();
    $postdata['c_produk_id'] = $id;
    $postdata['b_kategori_id'] = 1;
    $postdata['b_kondisi_id'] = 1;
    $postdata['b_berat_id'] = 1;
    $postdata['b_user_alamat_id'] = 1;
    $postdata['brand'] = 'Runner';
    $postdata['nama'] = 'QXPress Next Day Test';
    $postdata['harga_jual'] = 12.5;
    $postdata['stok'] = 99;
    $postdata['deskripsi'] = 'This product has QXpress and next day';
    $postdata['satuan'] = 'Pcs';
    $postdata['courier_services'] = "QXpress";
    $postdata['services_duration'] = "NextDay";
    $postdata['vehicle_types'] = "Regular";
    $postdata['dimension_long'] = 99;
    $postdata['dimension_width'] = 140;
    $postdata['dimension_height'] = 10;
    $postdata['berat'] = 29;
    $postdata['is_include_delivery_cost'] = 1;
    $postdata['is_published'] = 1;
    return $postdata;
  }
  protected function __editGogovanRegular(){
    $postdata = array();
    $postdata['b_kategori_id'] = 1;
    $postdata['b_kondisi_id'] = 1;
    $postdata['b_berat_id'] = 1;
    $postdata['b_user_alamat_id'] = 1;
    $postdata['brand'] = 'Runner';
    $postdata['nama'] = 'Gogovan Regular Test';
    $postdata['harga_jual'] = 12.5;
    $postdata['stok'] = 99;
    $postdata['deskripsi'] = 'This product has Gogovan Regular';
    $postdata['satuan'] = 'Pcs';
    $postdata['dimension_long'] = 99;
    $postdata['dimension_width'] = 140;
    $postdata['dimension_height'] = 10;
    $postdata['berat'] = 32;
    $postdata['is_include_delivery_cost'] = 1;
    $postdata['is_published'] = 1;
    return $postdata;
  }
  protected function __editGogovanLorry10(){
    $postdata = array();
    $postdata['b_kategori_id'] = 1;
    $postdata['b_kondisi_id'] = 1;
    $postdata['b_berat_id'] = 1;
    $postdata['b_user_alamat_id'] = 1;
    $postdata['brand'] = 'Runner';
    $postdata['nama'] = 'Gogovan Regular Test';
    $postdata['harga_jual'] = 12.5;
    $postdata['stok'] = 99;
    $postdata['deskripsi'] = 'This product has Gogovan Lorry 10 Feet / below 3 meter';
    $postdata['satuan'] = 'Pcs';
    $postdata['dimension_long'] = 270;
    $postdata['dimension_width'] = 140;
    $postdata['dimension_height'] = 10;
    $postdata['berat'] = 29;
    $postdata['is_include_delivery_cost'] = 1;
    $postdata['is_published'] = 1;
    return $postdata;
  }
  protected function __editGogovanLorry14(){
    $postdata = array();
    $postdata['b_kategori_id'] = 1;
    $postdata['b_kondisi_id'] = 1;
    $postdata['b_berat_id'] = 1;
    $postdata['b_user_alamat_id'] = 1;
    $postdata['brand'] = 'Runner';
    $postdata['nama'] = 'Gogovan Regular Test';
    $postdata['harga_jual'] = 12.5;
    $postdata['stok'] = 99;
    $postdata['deskripsi'] = 'This product has Gogovan Lorry 14 Feet / below 4,2 meter';
    $postdata['satuan'] = 'Pcs';
    $postdata['dimension_long'] = 270;
    $postdata['dimension_width'] = 400;
    $postdata['dimension_height'] = 10;
    $postdata['berat'] = 29;
    $postdata['is_include_delivery_cost'] = 1;
    $postdata['is_published'] = 1;
    return $postdata;
  }
  protected function __editGogovanLorry24(){
    $postdata = array();
    $postdata['b_kategori_id'] = 1;
    $postdata['b_kondisi_id'] = 1;
    $postdata['b_berat_id'] = 1;
    $postdata['b_user_alamat_id'] = 1;
    $postdata['brand'] = 'Runner';
    $postdata['nama'] = 'Gogovan Regular Test';
    $postdata['harga_jual'] = 12.5;
    $postdata['stok'] = 99;
    $postdata['deskripsi'] = 'This product has Gogovan Lorry 24 Feet / below 7,2 meter';
    $postdata['satuan'] = 'Pcs';
    $postdata['dimension_long'] = 270;
    $postdata['dimension_width'] = 400;
    $postdata['dimension_height'] = 710;
    $postdata['berat'] = 29;
    $postdata['is_include_delivery_cost'] = 1;
    $postdata['is_published'] = 1;
    return $postdata;
  }

  public function index(){
    echo '<p></p><h3>Runner Produk Index</h3>';
    echo '<ul>';
    echo '<li><a href="'.base_url("runner/produk/paging/").'">Paging</a></li>';
    echo '<li><a href="'.base_url("runner/produk/paging_stok/").'">Paging Stok</a></li>';
    echo '<li><a href="'.base_url("runner/produk/bread/").'">Bread</a></li>';
    echo '<li><a href="'.base_url("runner/produk/list/").'">List</a></li>';
    echo '<li><a href="'.base_url("runner/produk/qxpress_nextday/").'">qxpress_nextday</a></li>';
    echo '<li><a href="'.base_url("runner/produk/qxpress_sameday/").'">qxpress_sameday</a></li>';
    echo '<li><a href="'.base_url("runner/produk/edit_test/").'">Produk/Edit check Nextday &amp; Sameday</a></li>';
    echo '</ul>';
  }
  public function paging(){
    $dcount = 0;
    $ddata = array();
    $dlast = new stdClass();
    $dpass = 0;

    //set endpoint
    //$this->__baseUrl("https://sellondev.thecloudalert.com/api_mobile/");
    //$this->__resetURL();
    
    $this->__vo("Produk/paging");
    $url = $this->url.'produk'.$this->url_page;
    $this->__vu("Produk List",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->produk_total)){
        $dcount = (int) $result->data->produk_total;
        if($dcount>0){
          $ddata = $result->data->produks;
          if(is_array($ddata) && count($ddata)){
            if(isset($ddata[0]->id)){
              $dpass = 1;
              $dlast = $ddata[0];
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error");
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
    $this->__vb();
    //end test

    if($dpass){
      $i++;
      $this->__pageNext();
      $url = $this->url.'produk'.$this->url_page;
      $this->__vu("Produk List 2",$url);
      $raw = $this->seme_curl->get($url);
      if($raw->header->http_code == 200){
        $result = json_decode($raw->body);
        if(isset($result->data->produk_total)){
          $ddata = $result->data->produks;
          if(is_array($ddata) && count($ddata)){
            if(isset($ddata[0]->id)){
              if($dlast->id != $ddata[0]->id){
                $this->__vrp("Passed");
              }else{
                $this->__vrr("Not Passed: Product has same ID");
              }
              $this->__vd($raw->body);
            }else{
              $this->__vrr("Not Passed, cant get produk ID");
            }
          }else{
            $this->__vrr("Not Passed, data not array or empty");
          }
        }else{
          $this->__vrr("Not Passed: Empty list");
        }
      }else if($raw->header->http_code == 404){
        $this->__vrr("Not found");
      }else if($raw->header->http_code == 500){
        $this->__vrr("Error 500");
      }else{
        $this->__vrr("Error ".$raw->header->http_code);
      }
      $this->__vb();
      //end test

      $i++;
      $url = $this->url.'produk/detail/'.$dlast->id.''.$this->url_page;
      $this->__vu("Produk Detail",$url);
      $raw = $this->seme_curl->get($url);
      if($raw->header->http_code == 200){
        $result = json_decode($raw->body);
        if(isset($result->data->produk)){
          $ddata = $result->data->produk;
          if(isset($ddata->id)){
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Not Passed, cannot get produk detail");
          }
        }
        $this->__vdf($raw->body);
      }else if($raw->header->http_code == 404){
        $this->__vrr("Not found");
      }else if($raw->header->http_code == 500){
        $this->__vrr("Error 500");
      }else{
        $this->__vrr("Error ".$raw->header->http_code);
      }
    }else{
      $this->__vc('Cannot continue to next test');
    }
    $this->__ve();
  }
  public function paging_stok(){
    $this->__setSortCol("stok");
    $this->__setSortDir("asc");
    $dcount = 0;
    $ddata = array();
    $dlast = new stdClass();
    $dpass = 0;

    //start test
    $i=1;
    $this->__vo("Produk/paging");
    $url = $this->url.'produk'.$this->url_page;
    $this->__vu("Produk List with stok sort",$url);
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->produk_total)){
        $dcount = (int) $result->data->produk_total;
        if($dcount>0){
          $ddata = $result->data->produks;
          if(is_array($ddata) && count($ddata)){
            if(isset($ddata[0]->id)){
              $dpass = 1;
              $dlast = $ddata[0];
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error");
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
    $this->__vb();
    //end test

    if($dpass){
      $i++;
      $this->__pageNext();
      $url = $this->url.'produk'.$this->url_page;
      $this->__vu("Produk List 2",$url);
      $raw = $this->seme_curl->get($url);
      if($raw->header->http_code == 200){
        $result = json_decode($raw->body);
        if(isset($result->data->produk_total)){
          $ddata = $result->data->produks;
          if(is_array($ddata) && count($ddata)){
            if(isset($ddata[0]->id)){
              if($dlast->id != $ddata[0]->id){
                $this->__vrp("Passed");
              }else{
                $this->__vrr("Not Passed: Product has same ID");
              }
              $this->__vd($raw->body);
            }else{
              $this->__vrr("Not Passed, cant get produk ID");
            }
          }else{
            $this->__vrr("Not Passed, data not array or empty");
          }
        }else{
          $this->__vrr("Not Passed: Empty list");
        }
      }else if($raw->header->http_code == 404){
        $this->__vrr("Not found");
      }else if($raw->header->http_code == 500){
        $this->__vrr("Error 500");
      }else{
        $this->__vrr("Error ".$raw->header->http_code);
      }
      $this->__vb();
      //end test

      $i++;
      $url = $this->url.'produk/detail/'.$dlast->id.''.$this->url_page;
      $this->__vu("Produk Detail",$url);
      $raw = $this->seme_curl->get($url);
      if($raw->header->http_code == 200){
        $result = json_decode($raw->body);
        if(isset($result->data->produk)){
          $ddata = $result->data->produk;
          if(isset($ddata->id)){
            $this->__vrp("Passed");
          }else{
            $this->__vrr("Not Passed, cannot get produk detail");
          }
        }
        $this->__vdf($raw->body);
      }else if($raw->header->http_code == 404){
        $this->__vrr("Not found");
      }else if($raw->header->http_code == 500){
        $this->__vrr("Error 500");
      }else{
        $this->__vrr("Error ".$raw->header->http_code);
      }
    }else{
      $this->__vc('Cannot continue to next test');
    }
    $this->__ve();
  }

  public function bread(){
    $this->__setApiSess('65KMZDR');
    $memory = 0;
    $c_produk_id = 0;
    $this->__vo("Produk / Bread");

    //start test
    $i=1;
    $url = $this->url.'produk/baru'.$this->url_aft;
    $this->__vu("Produk Baru",$url);
    $raw = $this->seme_curl->post($url,$this->__add());
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
        if(isset($result->data->produk)){
          if(isset($result->data->produk->id)){
            $c_produk_id = $result->data->produk->id;
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
    $url = $this->url.'produk/edit/'.$c_produk_id.$this->url_aft;
    $this->__vu("Produk Edit jadi QXpress Same Day",$url);
    $raw = $this->seme_curl->post($url,$this->__editQXpressSameDay($c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
        if(isset($result->data->produk)){
          if(isset($result->data->produk->id)){
            if($result->data->produk->services_duration == 'Same Day' && $result->data->produk->courier_services == 'QXpress'){
              $this->__vrp("Passed");
            }else{
              $this->__vrh("Passed with error: Service Duration tidak same day atau bukan qxpress");
            }
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
    $url = $this->url.'produk/edit/'.$c_produk_id.$this->url_aft;
    $this->__vu("Produk Edit jadi QXpress Next Day",$url);
    $raw = $this->seme_curl->post($url,$this->__editQXpressNextDay($c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
        if(isset($result->data->produk)){
          if(isset($result->data->produk->id)){
            $is_passed = 0;
            $errmsg = '';
            $c_produk_id = $result->data->produk->id;
            if($result->data->produk->services_duration == 'Next Day' && $result->data->produk->courier_services == 'QXpress'){
              $this->__vrp("Passed");
            }else{
              $this->__vrh("Passed with error: Service Duration bukan next day atau bukan qxpress");
            }
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
  public function list(){
    //set endpoint
    $this->__baseUrl("https://sellon.thecloudalert.com/api_mobile/");
    $this->__resetURL();
  
    $this->__vo("Produk/list");

    //test
    $i=1; //iteration
    $url = $this->url.'produk'.$this->url_page; //url call
    $this->__vu("Produk List",$url); //testing title

    //start test
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if(isset($result->data->produk_total)){
        $dcount = (int) $result->data->produk_total;
        if($dcount>0){
          $ddata = $result->data->produks;
          if(is_array($ddata) && count($ddata)){
            if(isset($ddata[0]->id)){
              $dpass = 1;
              $dlast = $ddata[0];
              $this->__vrp("Passed");
            }else{
              $this->__vrr("Error");
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
    $this->__vb();
    //end test

    $this->__ve();
  }
  public function qxpress_nextday(){
    $memory = 0;
    $c_produk_id = 0;
    $this->__vo("Konversi QXPress: Next Day");

    //start test
    $url = $this->url.'seller/produk/active'.$this->url_page;
    $this->__vu("Ambil Produk Active",$url);
    $raw = $this->seme_curl->post($url,$this->__add());
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
        if(isset($result->data->produk_total)){
          $max = (int) $result->data->produk_total;
          if($max>10) $max = 10;
          $rand = rand(0,($max-1));
          if(isset($result->data->produks[$rand]->id)){
            $c_produk_id = $result->data->produks[$rand]->id;
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
    $i++;
    $url = $this->url.'produk/edit/'.$c_produk_id.$this->url_aft;
    $this->__vu("Produk Edit jadi QXpress Next Day",$url);
    $raw = $this->seme_curl->post($url,$this->__editQXpressNextDay($c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
        if(isset($result->data->produk)){
          if(isset($result->data->produk->id)){
            if($result->data->produk->services_duration == 'Next Day' && $result->data->produk->courier_services == 'QXpress'){
              $this->__vrp("Passed");
            }else{
              $this->__vrh("Passed with error: Service Duration tidak next day atau bukan qxpress");
            }
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
  public function qxpress_sameday(){
    $memory = 0;
    $c_produk_id = 0;
    $this->__vo("Konversi QXPress: Same Day");

    //start test
    $i=1;
    $url = $this->url.'seller/produk/active'.$this->url_page;
    $this->__vu("Ambil Produk Active",$url);
    $raw = $this->seme_curl->post($url,$this->__add());
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
        if(isset($result->data->produk_total)){
          $max = (int) $result->data->produk_total;
          if($max>10) $max = 10;
          $rand = rand(0,($max-1));
          if(isset($result->data->produks[$rand]->id)){
            $c_produk_id = $result->data->produks[$rand]->id;
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
    $i++;
    $url = $this->url.'produk/edit/'.$c_produk_id.$this->url_aft;
    $this->__vu("Produk Edit jadi QXpress Same Day",$url);
    $raw = $this->seme_curl->post($url,$this->__editQXpressSameDay($c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
        if(isset($result->data->produk)){
          if(isset($result->data->produk->id)){
            if($result->data->produk->services_duration == 'Same Day' && $result->data->produk->courier_services == 'QXpress'){
              $this->__vrp("Passed");
            }else{
              $this->__vrh("Passed with error: Service Duration tidak same day atau bukan qxpress");
            }
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
  public function edit_test(){
    $this->__vo("Produk/Edit check Nextday &amp; Sameday");
    $c_produk_id = 0;

    //start test
    $url = $this->url.'seller/produk/active'.$this->url_page;
    $this->__vu("Get Active Product",$url);
    $raw = $this->seme_curl->post($url,$this->__add());
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        if(isset($result->memory)) $memory = $result->memory;
        if(isset($result->data->produk_total)){
          $max = (int) $result->data->produk_total;
          if($max>10) $max = 10;
          $rand = rand(0,($max-1));
          if(isset($result->data->produks[$rand]->id)){
            $c_produk_id = $result->data->produks[$rand]->id;
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

    if(empty($c_produk_id)){
      $this->__vu("Cannot continue, c_produk_id empty",'');
      $this->__ve();
    }

    //start test
    $url = $this->url.'produk/edit'.$this->url_page;
    $this->__vu("Edit: Check Next Day not Nextday",$url);
    $raw = $this->seme_curl->post($url,$this->__editQXpressNextDay($c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        $result = json_decode($raw->body);
        if($result->status == 200){
          if(isset($result->memory)) $memory = $result->memory;
          if(isset($result->data->produk->services_duration)){
            if($result->data->produk->services_duration == "Next Day"){
              $this->__vrp("Passed");
            }else{
              $this->__vrp("Not Passed, services_duration value not 'Next Day'");
            }
          }else{
            $this->__vrp("Not Passed, produk->service_duration object undefined");
          }
        }else{
          $this->__vrr("Error: ".$result->message);
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
    $url = $this->url.'produk/edit'.$this->url_page;
    $this->__vu("Edit: Check Same Day not Sameday",$url);
    $raw = $this->seme_curl->post($url,$this->__editQXpressSameDay($c_produk_id));
    if($raw->header->http_code == 200){
      $result = json_decode($raw->body);
      if($result->status == 200){
        $result = json_decode($raw->body);
        if($result->status == 200){
          if(isset($result->memory)) $memory = $result->memory;
          if(isset($result->data->produk->services_duration)){
            if($result->data->produk->services_duration == "Same Day"){
              $this->__vrp("Passed");
            }else{
              $this->__vrp("Not Passed, services_duration value not 'Same Day'");
            }
          }else{
            $this->__vrp("Not Passed, produk->service_duration object undefined");
          }
        }else{
          $this->__vrr("Error: ".$result->message);
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

}
