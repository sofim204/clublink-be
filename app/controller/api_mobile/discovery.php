<?php
class Discovery extends JI_Controller{
  public function __construct(){
    parent:: __construct();
    $this->load("api_mobile/b_user_model", 'bu');
    $this->load("api_mobile/c_produk_model",'cp');
  }

	private function __sortCol($sort_col,$tbl_as,$tbl2_as){
		switch($sort_col){
			case 'kondisi':
			$sort_col = "$tbl_as.kondisi";
			break;
			case 'harga':
			$sort_col = "$tbl_as.harga_jual";
			break;
			case 'harga_jual':
			$sort_col = "$tbl_as.harga_jual";
			break;
			case "$tbl_as.harga_retail":
			$sort_col = "$tbl_as.harga_retail";
			break;
			case 'diskon_harga':
			$sort_col = "$tbl_as.diskon_expired DESC, $tbl_as.diskon_harga";
			break;
			case 'sales_count':
			$sort_col = "$tbl_as.sales_count";
			break;
			case 'sales_rate':
			$sort_col = "$tbl_as.sales_rate";
			break;
			case 'dimensi':
			$sort_col = "$tbl_as.dimensi";
			break;
			case 'berat':
			$sort_col = "$tbl_as.berat";
			break;
			default:
			$sort_col = "$tbl_as.nama";
		}
		return $sort_col;
	}
	private function __sortDir($sort_dir){
		$sort_dir = strtolower($sort_dir);
		if($sort_dir == "desc"){
			$sort_dir = "DESC";
		}else{
			$sort_dir = "ASC";
		}
		return $sort_dir;
	}
	private function __page($page){
		if(!is_int($page)) $page = (int) $page;
		if(empty($page)) $page = 1;
		return $page;
	}
	private function __pageSize($page_size){
		$page_size = (int) $page_size;
		if($page_size<=0){
			$page_size = 1;
		}
		return $page_size;
	}
  public function index(){
		//initial
		$dt = $this->__init();

		//default result
		$data = array();
		$data['produk'] = array();
		$data['produk_count'] = 0;

    //check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
    if(empty($nation_code)){
      $this->status = 101;
  		$this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "discovery");
      die();
    }

		//check apikey
		$apikey = $this->input->get('apikey');
		$c = $this->apikey_check($apikey);
		if(!$c){
			$this->status = 400;
			$this->message = 'Missing or invalid API key';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "discovery");
			die();
		}
		
		//by Donny Dennison - 15 february 2022 9:50
    //category product and category community have more than 1 language
    // check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
        $pelanggan = new stdClass();
        if($nation_code == 62){ //indonesia
            $pelanggan->language_id = 2;
        }else if($nation_code == 82){ //korea
            $pelanggan->language_id = 3;
        }else if($nation_code == 66){ //thailand
            $pelanggan->language_id = 4;
        }else {
            $pelanggan->language_id = 1;
        }
    }

		$page = (int) $this->input->get("page");
		$page_size = (int) $this->input->get("page_size");
		$sort_col = strtolower($this->input->get("sort_col"));
		$sort_dir = strtolower($this->input->get("sort_dir"));
		$keyword = strtolower($this->input->get("keyword"));
    if(empty($keyword)) $keyword = '';

    $tbl_as = $this->cp->getTblAs();
    $tbl2_as = $this->cp->getTbl2As();
    $sort_col = $this->__sortCol($sort_col,$tbl_as,$tbl2_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);


    //advanced filter
    $harga_jual_min = '';
    if(isset($_GET['harga_jual_min'])){
      $harga_jual_min = (int) $_GET['harga_jual_min'];
      if($harga_jual_min<=-1) $harga_jual_min = '';
    }

    $harga_jual_max = (int) $this->input->get("harga_jual_max");
    if($harga_jual_max<=0) $harga_jual_max = "";

    $b_kondisi_ids = "";
    if(isset($_GET['b_kondisi_ids'])) $b_kondisi_ids = $_GET['b_kondisi_ids'];
    if(strlen($b_kondisi_ids)>0){
      $b_kondisi_ids = explode("-",$b_kondisi_ids);
      if(count($b_kondisi_ids)){
        $kons = array();
        foreach($b_kondisi_ids as &$bks){
          $bks = (int) $bks;
          if($bks>0) $kons[] = $bks;
        }
        $b_kondisi_ids = $kons;
      }else{
        $b_kondisi_ids = array();
      }
    }else{
      $b_kondisi_ids = array();
    }

    $b_kategori_ids = "";
    if(isset($_GET['b_kategori_ids'])) $b_kategori_ids = $_GET['b_kategori_ids'];
    if(strlen($b_kategori_ids)>0){
      $b_kategori_ids = explode("-",$b_kategori_ids);
      if(count($b_kategori_ids)){
        $kods = array();
        foreach($b_kategori_ids as &$bki){
          $bki = (int) $bki;
          if($bki>0) $kods[] = $bki;
        }
        $b_kategori_ids = $kods;
      }else{
        $b_kategori_ids = array();
      }
    }else{
      $b_kategori_ids = array();
    }

    $kecamatan = $this->input->get("kecamatan");
    if(strlen($kecamatan)) $kecamatan = "";

    $data['produk_count'] = $this->cp->countHomePage($nation_code,$keyword,$harga_jual_min,$harga_jual_max,$b_kondisi_ids,$b_kategori_ids,$kecamatan);
    $data['produk'] = $this->cp->getHomePage($nation_code,$page,$page_size,$sort_col,$sort_dir,$keyword,$harga_jual_min,$harga_jual_max,$b_kondisi_ids,$b_kategori_ids,$kecamatan);

		//manipulator
		foreach($data['produk'] as &$pd){
      if(isset($pd->b_user_image_seller)){
				if(empty($pd->b_user_image_seller)) $pd->b_user_image_seller = 'media/produk/default.png';
				
				// by Muhammad Sofi - 28 October 2021 11:00
                // if user img & banner not exist or empty, change to default image
                // $pd->b_user_image_seller = $this->cdn_url($pd->b_user_image_seller);
                if(file_exists(SENEROOT.$pd->b_user_image_seller) && $pd->b_user_image_seller != 'media/user/default.png'){
                    $pd->b_user_image_seller = $this->cdn_url($pd->b_user_image_seller);
                } else {
                    $pd->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                }
			}
			if(isset($pd->thumb)){
				if(empty($pd->thumb)) $pd->thumb = 'media/produk/default.png';
				$pd->thumb = $this->cdn_url($pd->thumb);
			}
			if(isset($pd->foto)){
				if(empty($pd->foto)) $pd->foto = 'media/produk/default.png';
				$pd->foto = $this->cdn_url($pd->foto);
			}
			if(isset($pd->b_kondisi_icon)){
				if(empty($pd->b_kondisi_icon)) $pd->b_kondisi_icon = 'media/icon/default.png';
				$pd->b_kondisi_icon = $this->cdn_url($pd->b_kondisi_icon);
			}
			if(isset($pd->b_berat_icon)){
				if(empty($pd->b_berat_icon)) $pd->b_berat_icon = 'media/icon/default.png';
				$pd->b_berat_icon = $this->cdn_url($pd->b_berat_icon);
			}
		}

    $this->status = 200;
    $this->message = "Success";
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "discovery");
  }
}
