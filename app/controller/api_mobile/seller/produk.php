<?php
class Produk extends JI_Controller{

	public function __construct(){
		parent::__construct();
		//$this->setTheme('frontx');
		$this->load("api_mobile/b_kategori_model3","bkm3");
		$this->load("api_mobile/b_user_alamat_model","bua");
		$this->load("api_mobile/c_produk_model","cpm");
		$this->load("api_mobile/c_produk_foto_model","cpfm");
		$this->load("api_mobile/b_user_model","bu");
		$this->load("api_mobile/d_wishlist_model","dwlm");
	}

	private function __sortCol($sort_col,$tbl_as,$tbl2_as){
		switch($sort_col){
			case 'id':
			$sort_col = "$tbl_as.id";
			break;
			case 'kondisi':
			$sort_col = "$tbl_as.b_kondisi_id";
			break;
			case 'harga':
			$sort_col = "$tbl_as.harga_jual";
			break;
			case 'harga_jual':
			$sort_col = "$tbl_as.harga_jual";
			break;
			case 'nama':
			$sort_col = "$tbl_as.nama";
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

  // 	public function index(){
		// $this->status = '404';
		// header("HTTP/1.0 404 Not Found");
		// $data = array();
		// $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_produk");
  // 	}

  	public function index(){
		//initial
		$dt = $this->__init();

		//default result
		$data = array();
		$data['produk_total'] = 0;
		$data['produks'] = array();

    	//check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
	    if(empty($nation_code)){
	      $this->status = 101;
	  		$this->message = 'Missing or invalid nation_code';
	      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_produk");
	      die();
	    }

		//check apikey
		$apikey = $this->input->get('apikey');
		$c = $this->apikey_check($apikey);
		if(!$c){
			$this->status = 400;
			$this->message = 'Missing or invalid API key';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_produk");
			die();
		}

		//check apisess
		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if(!isset($pelanggan->id)){
			$this->status = 401;
			$this->message = 'Missing or invalid API session';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_produk");
			die();
		}
		$b_user_id = $pelanggan->id;

		//get alias from model
		$tbl_as = $this->cpm->getTblAs();
		$tbl2_as = $this->cpm->getTbl2As();

		//populate input get
		$sortcol = $this->input->get("sortcol");
		$sortdir = $this->input->get("sortdir");
		$page = $this->input->get("page");
		$page_size = $this->input->get("page_size");
		$grid = $this->input->get("grid");
		$keyword = $this->input->get("keyword");

		$product_type = $this->input->get("product_type");

	    if(!$product_type){
	      $product_type = 'All';
	    }

	    //by Donny Dennison - 22 february 2022 17:42
	    //change product_type language
	    if($product_type == "Proteksi"){
	      $product_type = "Protection";
	    } else if($product_type == "Otomotif"){
	      $product_type = "Automotive";
	    } else if($product_type == "Gratis"){
	      $product_type = "Free";
	    }

	    $show_soldout = (string) $this->input->get("show_soldout");

		//sanitize input
		if(empty($keyword)) $keyword="";
		$sortcol = $this->__sortCol($sortcol,$tbl_as,$tbl2_as);
		$sortdir = (strtolower($sortdir)=="asc") ? 'asc':'desc';
		$page = $this->__page($page);
		$page_size = $this->__pageSize($page_size);
		$keyword = filter_var(strip_tags($keyword),FILTER_SANITIZE_SPECIAL_CHARS);

		//get produk data
		$dcount = $this->cpm->countMyProduct($nation_code,$b_user_id,$keyword,1, $product_type, $show_soldout);
	  	$ddata = $this->cpm->getMyProduk($nation_code,$b_user_id,$page,$page_size,$sortcol,$sortdir,$keyword,1, $product_type, $show_soldout, $pelanggan, $pelanggan->language_id);
		foreach($ddata as &$pd){

      		$pd->nama = html_entity_decode($pd->nama,ENT_QUOTES);
      		$pd->deskripsi = html_entity_decode($pd->deskripsi,ENT_QUOTES);

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
			if(isset($pd->b_kondisi_icon)){
				if(empty($pd->b_kondisi_icon)) $pd->b_kondisi_icon = 'media/produk/default.png';
				$pd->b_kondisi_icon = $this->cdn_url($pd->b_kondisi_icon);
			}
			if(isset($pd->b_berat_icon)){
				if(empty($pd->b_berat_icon)) $pd->b_berat_icon = 'media/produk/default.png';
				$pd->b_berat_icon = $this->cdn_url($pd->b_berat_icon);
			}
			if(isset($pd->thumb)){
				if(empty($pd->thumb)) $pd->thumb = 'media/produk/default.png';
				$pd->thumb = $this->cdn_url($pd->thumb);
			}
			if(isset($pd->foto)){
				if(empty($pd->foto)) $pd->foto = 'media/produk/default.png';
				$pd->foto = $this->cdn_url($pd->foto);
			}

			if($pd->product_type == 'Automotive' && ($pd->b_kategori_id == 32 || $pd->b_kategori_id == 33)){
				$pd->automotive_type = $pd->kategori;
			}else{
				$pd->automotive_type = "";
			}

			//by Donny Dennison - 22 february 2022 17:42
			//change product_type language
			if($pelanggan->language_id == 2){
				if($pd->product_type == "Protection"){
				 	$pd->product_type = "Proteksi";
				} else if($pd->product_type == "Automotive"){
					$pd->product_type = "Otomotif";
				} else if($pd->product_type == "Free"){
					$pd->product_type = "Gratis";
				}
			}

			//by Donny Dennison - 22 july 2022 10:45
			//add response parameter have_video in api product, product/detail, homepage, seller/product, product_automotive, wishlist
			$pd->have_video = ($this->cpfm->countByProdukIdJenisConvertStatusNotEqual($nation_code, $pd->id, "video", "uploading") > 0) ? "1" : "0";

		}

		$data['produk_total'] = $dcount;
		$data['produks'] = $ddata;

		$this->status = 200;
		$this->message = 'Success';
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_produk");
  	}

  	public function draft(){
		//initial
		$dt = $this->__init();

		//default result
		$data = array();
		$data['produk_total'] = 0;
		$data['produks'] = array();

    	//check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
	    if(empty($nation_code)){
	      $this->status = 101;
	  		$this->message = 'Missing or invalid nation_code';
	      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_produk");
	      die();
	    }

		//check apikey
		$apikey = $this->input->get('apikey');
		$c = $this->apikey_check($apikey);
		if(!$c){
			$this->status = 400;
			$this->message = 'Missing or invalid API key';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_produk");
			die();
		}

		//check apisess
		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if(!isset($pelanggan->id)){
			$this->status = 401;
			$this->message = 'Missing or invalid API session';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_produk");
			die();
		}
		$b_user_id = $pelanggan->id;

		//get alias from model
		$tbl_as = $this->cpm->getTblAs();
		$tbl2_as = $this->cpm->getTbl2As();

		//populate input get
		$sortcol = $this->input->get("sortcol");
		$sortdir = $this->input->get("sortdir");
		$page = $this->input->get("page");
		$page_size = $this->input->get("page_size");
		$grid = $this->input->get("grid");
		$keyword = $this->input->get("keyword");

		//sanitize input
		if(empty($keyword)) $keyword="";
		$sortcol = $this->__sortCol($sortcol,$tbl_as,$tbl2_as);
		$sortdir = (strtolower($sortdir)=="asc") ? 'asc':'desc';
		$page = $this->__page($page);
		$page_size = $this->__pageSize($page_size);
		$keyword = filter_var(strip_tags($keyword),FILTER_SANITIZE_SPECIAL_CHARS);

		//print_r($page_size);
		//die();

		//get produk data
		$dcount = $this->cpm->countMyProduct($nation_code, $b_user_id, $keyword,0);
	  	$ddata = $this->cpm->getMyProduk($nation_code, $b_user_id, $page,$page_size,$sortcol,$sortdir,$keyword,0, "All", "", $pelanggan, $pelanggan->language_id);
		foreach($ddata as &$pd){

      		$pd->nama = html_entity_decode($pd->nama,ENT_QUOTES);
      		$pd->deskripsi = html_entity_decode($pd->deskripsi,ENT_QUOTES);

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
			if(isset($pd->b_kondisi_icon)){
				if(empty($pd->b_kondisi_icon)) $pd->b_kondisi_icon = 'media/produk/default.png';
				$pd->b_kondisi_icon = $this->cdn_url($pd->b_kondisi_icon);
			}
			if(isset($pd->b_berat_icon)){
				if(empty($pd->b_berat_icon)) $pd->b_berat_icon = 'media/produk/default.png';
				$pd->b_berat_icon = $this->cdn_url($pd->b_berat_icon);
			}
			if(isset($pd->thumb)){
				if(empty($pd->thumb)) $pd->thumb = 'media/produk/default.png';
				$pd->thumb = $this->cdn_url($pd->thumb);
			}
			if(isset($pd->foto)){
				if(empty($pd->foto)) $pd->foto = 'media/produk/default.png';
				$pd->foto = $this->cdn_url($pd->foto);
			}

			if($pd->product_type == 'Automotive' && ($pd->b_kategori_id == 32 || $pd->b_kategori_id == 33)){
				$pd->automotive_type = $pd->kategori;
			}else{
				$pd->automotive_type = "";
			}

			//by Donny Dennison - 22 february 2022 17:42
			//change product_type language
			if($pelanggan->language_id == 2){
				if($pd->product_type == "Protection"){
				 	$pd->product_type = "Proteksi";
				} else if($pd->product_type == "Automotive"){
					$pd->product_type = "Otomotif";
				} else if($pd->product_type == "Free"){
					$pd->product_type = "Gratis";
				}
			}

		}

		$data['produk_total'] = $dcount;
		$data['produks'] = $ddata;

		$this->status = 200;
		$this->message = 'Success';
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_produk");
	}

}
