<?php
class Wishlist extends JI_Controller {
  var $nation_code = '65';
  var $apikey = 'kmz373ac';
  var $apisess = '65KMZDS';
  var $url = '';
  var $url_aft = '';
  var $url_page = '';
  var $page = 1;
  var $page_size = 10;
  public function __construct(){
    parent::__construct();
    $this->lib("seme_curl");
    $this->url = base_url('api_mobile/');
    $this->url_aft .= '/';
    $this->url_aft .= '?nation_code='.$this->__encURICom($this->nation_code);
    $this->url_aft .= '&apikey='.$this->__encURICom($this->apikey);
    $this->url_aft .= '&apisess='.$this->__encURICom($this->apisess);

    $this->url_page  = $this->url_aft;
    $this->url_page .= '&page_size='.$this->page_size;
    $this->url_page .= '&page='.$this->page;
    $this->url_page .= '&sort_col=id';
    $this->url_page .= '&sort_dir=desc';
  }

  private function __encURICom($str) {
    $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
    return strtr(rawurlencode($str), $revert);
  }
  private function __resetPage(){
    $this->page = 1;
    $this->url_page  = $this->url_aft;
    $this->url_page .= '&page_size='.$this->page_size;
    $this->url_page .= '&page='.$this->page;
    $this->url_page .= '&sort_col=id';
    $this->url_page .= '&sort_dir=desc';
  }
  private function __pageNext(){
    $this->page = $this->page+1;
    $this->url_page  = $this->url_aft;
    $this->url_page .= '&page_size='.$this->page_size;
    $this->url_page .= '&page='.$this->page;
    $this->url_page .= '&sort_col=id';
    $this->url_page .= '&sort_dir=desc';
  }
  private function __pageBefore(){
    $this->page = $this->page-1;
    $this->url_page  = $this->url_aft;
    $this->url_page .= '&page_size='.$this->page_size;
    $this->url_page .= '&page='.$this->page;
    $this->url_page .= '&sort_col=id';
    $this->url_page .= '&sort_dir=desc';
  }

  public function index(){
    echo 'runner wishlist index';
  }
  public function paging(){
    $dcount = 0;
    $ddata = array();
    $dlast = new stdClass();
    $dpass = 0;
    echo '---<br />';
    echo '--Runner for Wishlist/paging--<br />';
    echo '---------------------------------------<br /><br />';
    $i=1;
    $url = $this->url.'wishlist'.$this->url_page;
    echo $i.'. Calling: '.$url.'<br />';
    echo 'Result: ';
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      echo 'Success<br />';
      $data = json_decode($raw->body);
      if(isset($data->data->wishlist_count)){
        $dcount = $data->data->wishlist_count;
        $ddata = $data->data->wishlist;
        if(is_array($ddata) && count($ddata)){
          if(isset($ddata[0]->id)) $dlast = $ddata[0];
          $dpass = 1;
        }
      }
      echo 'Found: '.$dcount.' data<br />';
    }else if($raw->header->http_code == 404){
      echo 'Not Found<br />';
    }else if($raw->header->http_code == 500){
      echo 'Error 500<br />';
      echo '<pre>'.$raw->body.'</pre><br />';
    }else{
      echo 'Error occured with code: '.$raw->header->http_code.'<br />';
      echo 'When call api -> '.$url.'<br />';
    }
    if($dpass){
      $i++;
      $this->__pageNext();
      $url = $this->url.'wishlist'.$this->url_page;
      echo $i.'. Calling: '.$url.'<br />';
      echo 'Result: ';
      $raw = $this->seme_curl->get($url);
      if($raw->header->http_code == 200){
        echo 'Success<br />';
        $data = json_decode($raw->body);
        if(isset($data->data->wishlist_count)){
          $ddata = $data->data->wishlist;
          if(is_array($ddata) && count($ddata)){
            if(isset($ddata[0]->id)){
              if($dlast->id != $ddata[0]->id){
                $dpass=1;
              }
            }
          }
        }
        if($dpass){
          echo 'Paging passed<br />';
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
    }else{
      echo '--Cannot continue, test before does not passed--';
    }
  }
  public function tambahBanyak(){
    $produks = array();
    echo '---<br />';
    echo '--Runner for Wishlist/tambahBanyak--<br />';
    echo '---------------------------------------<br /><br />';
    $i=1;
    $url = $this->url.'produk'.$this->url_page;
    echo $i.'. Calling: '.$url.'<br />';
    echo 'Result: ';
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      echo 'Success<br />';
      $data = json_decode($raw->body);
      if(isset($data->data->produks)){
        $produks = $data->data->produks;
      }
      echo '<pre>'.print_r($produks).'</pre><br />';
    }else if($raw->header->http_code == 404){
      echo 'Not Found<br />';
    }else if($raw->header->http_code == 500){
      echo 'Error 500<br />';
      echo '<pre>'.$raw->body.'</pre><br />';
    }else{
      echo 'Error occured with code: '.$raw->header->http_code.'<br />';
      echo 'When call api -> '.$url.'<br />';
    }

    foreach($produks as $produk){
      $i++;
      $postdata = array();
      $postdata['c_produk_id'] = $produk->id;
      $url = $this->url.'wishlist/tambah'.$this->url_aft;
      echo $i.'. Calling: '.$url.'<br />';
      echo 'post_data: <br />';
      echo '<pre>'.print_r($postdata).'</pre>';
      echo '<br />';
      echo 'Result: ';
      $raw = $this->seme_curl->post($url,$postdata);
      if($raw->header->http_code == 200){
        echo 'Success<br />';
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
  public function test(){
    $produk1 = new stdClass();
    $produk1->id = 15;
    echo '---<br />';
    echo '--Runner for Wishlist/Test--<br />';
    echo '---------------------------------------<br /><br />';
    $i=1;
    $url = $this->url.'wishlist'.$this->url_aft;
    echo $i.'. Calling: '.$url.'<br />';
    echo 'Result: ';
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      echo 'Success<br />';
      $data = json_decode($raw->body);
      if(isset($data->data->wishlist)){
        $data = $data->data->wishlist;
        if(is_array($data) && count($data)>0){
          if(isset($data[0]->id)) $produk1 = $data[0];
        }
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
    $postdata = array();
    $postdata['c_produk_id'] = $produk1->id;
    $url = $this->url.'wishlist/tambah'.$this->url_aft;
    echo $i.'. Calling: '.$url.'<br />';
    echo 'post_data: <br />';
    echo '<pre>'.print_r($postdata).'</pre>';
    echo '<br />';
    echo 'Result: ';
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      echo 'Success<br />';
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
    $postdata = array();
    $postdata['c_produk_id'] = $produk1->id;
    $url = $this->url.'wishlist/hapus'.$this->url_aft;
    echo $i.'. Calling: '.$url.'<br />';
    echo 'post_data: <br />';
    echo '<pre>'.print_r($postdata).'</pre>';
    echo '<br />';
    echo 'Result: ';
    $raw = $this->seme_curl->post($url,$postdata);
    if($raw->header->http_code == 200){
      echo 'Success<br />';
    }else if($raw->header->http_code == 404){
      echo 'Not Found<br />';
    }else if($raw->header->http_code == 500){
      echo 'Error 500<br />';
      echo '<pre>'.$raw->body.'</pre><br />';
    }else{
      echo 'Error occured with code: '.$raw->header->http_code.'<br />';
      echo 'When call api -> '.$url.'<br />';
    }
    $url = $this->url.'wishlist'.$this->url_aft;
    echo $i.'. Calling: '.$url.'<br />';
    echo 'Result: ';
    $raw = $this->seme_curl->get($url);
    if($raw->header->http_code == 200){
      echo 'Success<br />';
      echo '<pre>'.$raw->body.'</pre><br />';
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
