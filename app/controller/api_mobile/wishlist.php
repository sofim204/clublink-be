<?php
class Wishlist extends JI_Controller {

	public function __construct(){
    parent::__construct();
		$this->load("api_mobile/b_user_model",'bu');
		$this->load("api_mobile/c_produk_model",'cpm');
		$this->load("api_mobile/d_wishlist_model",'dwlm');

    //by Donny Dennison - 22 july 2022 10:45
    //add response parameter have_video in api product, product/detail, homepage, seller/product, product_automotive, wishlist
    $this->load("api_mobile/c_produk_foto_model", "cpfm");

	}
	public function index(){
		//initial
		$dt = $this->__init();

		//default result
		$data = array();
		$data['wishlist_count'] = 0;
		$data['wishlist'] = array();

    //check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
    if(empty($nation_code)){
      $this->status = 101;
  		$this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wishlist");
      die();
    }

		//check apikey
		$apikey = $this->input->get('apikey');
		$c = $this->apikey_check($apikey);
		if(!$c){
			$this->status = 400;
			$this->message = 'Missing or invalid API key';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wishlist");
			die();
		}

		//check apisess
		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if(!isset($pelanggan->id)){
			$this->status = 401;
			$this->message = 'Missing or invalid API session';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wishlist");
			die();
		}

		//populate input
		$page = (int) $this->input->get("page");
		$pageSize = (int) $this->input->get("page_size"); //has different
		if(empty($pageSize)) $pageSize = (int) $this->input->get("pageSize"); //has different
		$sort_col = $this->input->get("sort_col");
		$sort_dir = $this->input->get("sort_dir");
		$keyword = $this->input->get("keyword");
		if($page<=0) $page = 0;
		if($pageSize<=0) $pageSize = 10;
		if(strlen($keyword)<=1) $keyword = "";
		if($sort_dir != 'desc') $sort_dir = 'asc';

		$tbl_as = $this->dwlm->getTableAlias();
		$tbl2_as = $this->dwlm->getTableAlias2();
		switch($sort_col){
			case "id":
				$sort_col = $tbl2_as.".id";
				break;
			default:
				$sort_col = $tbl2_as.".id";
		}

		$this->status = 200;
		$this->message = 'Success';
    $data['wishlist'] = $this->dwlm->getAll($nation_code,$pelanggan->id,$page,$pageSize,$sort_col,$sort_dir,$keyword, $pelanggan, $pelanggan->language_id);
    $data['wishlist_count'] = $this->dwlm->countAll($nation_code,$pelanggan->id,$keyword);

		foreach($data['wishlist'] as &$dw){
			
			$dw->nama = html_entity_decode($dw->nama,ENT_QUOTES);

			if(isset($dw->b_user_image_seller)){
				if(empty($dw->b_user_image_seller)) $dw->b_user_image_seller = 'media/produk/default.png';
				
				// by Muhammad Sofi - 28 October 2021 11:00
        // if user img & banner not exist or empty, change to default image
        // $dw->b_user_image_seller = $this->cdn_url($dw->b_user_image_seller);
        if(file_exists(SENEROOT.$dw->b_user_image_seller) && $dw->b_user_image_seller != 'media/user/default.png'){
            $dw->b_user_image_seller = $this->cdn_url($dw->b_user_image_seller);
        } else {
            $dw->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
        }
			}
      if(isset($dw->foto)){
        if(empty($dw->foto)) $dw->foto = 'media/produk/default.png';
        $dw->foto = $this->cdn_url($dw->foto);
      }
      if(isset($dw->thumb)){
        if(empty($dw->thumb)) $dw->thumb = 'media/produk/default.png';
        $dw->thumb = $this->cdn_url($dw->thumb);
      }
			if(isset($dw->b_kondisi_icon)){
				if(empty($dw->b_kondisi_icon)) $dw->b_kondisi_icon = 'media/icon/default.png';
				$dw->b_kondisi_icon = $this->cdn_url($dw->b_kondisi_icon);
			}
			if(isset($dw->b_berat_icon)){
				if(empty($dw->b_berat_icon)) $dw->b_berat_icon = 'media/icon/default.png';
				$dw->b_berat_icon = $this->cdn_url($dw->b_berat_icon);
			}

      if($dw->product_type == 'Automotive' && ($dw->b_kategori_id == 32 || $dw->b_kategori_id == 33)){
        $dw->automotive_type = $dw->kategori;
      }else{
        $dw->automotive_type = "";
      }

      //by Donny Dennison - 22 february 2022 17:42
      //change product_type language
      if($pelanggan->language_id == 2){
        if($dw->product_type == "Protection"){
          $dw->product_type = "Proteksi";
        } else if($dw->product_type == "Automotive"){
          $dw->product_type = "Otomotif";
        } else if($dw->product_type == "Free"){
          $dw->product_type = "Gratis";
        }
      }

      //by Donny Dennison - 22 july 2022 10:45
      //add response parameter have_video in api product, product/detail, homepage, seller/product, product_automotive, wishlist
      $dw->have_video = ($this->cpfm->countByProdukIdJenisConvertStatusNotEqual($nation_code, $dw->id, "video", "uploading") > 0) ? "1" : "0";

		}
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wishlist");
	}
	public function tambah(){
		//initial
		$dt = $this->__init();

		//default result
		$data = array();
		$data['wishlist_count'] = 0;
		$data['wishlist'] = array();

    //check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
    if(empty($nation_code)){
      $this->status = 101;
  		$this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wishlist");
      die();
    }

		//check apikey
		$apikey = $this->input->get('apikey');
		$c = $this->apikey_check($apikey);
		if(!$c){
			$this->status = 400;
			$this->message = 'Missing or invalid API key';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wishlist");
			die();
		}

		//check apisess
		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if(!isset($pelanggan->id)){
			$this->status = 401;
			$this->message = 'Missing or invalid API session';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wishlist");
			die();
		}

    $c_produk_id = $this->input->post('c_produk_id');
		if($c_produk_id<='0'){
			$this->status = 311;
			$this->message = 'Invalid product ID';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wishlist");
			die();
		}

		$getProductType = $this->cpm->getProductType($nation_code, $c_produk_id);
		if(!isset($getProductType->product_type)){
			$this->status = 310;
			$this->message = 'Data not found or deleted';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wishlist");
			die();
		}

		$getProductType = $getProductType->product_type;

		$produk = $this->cpm->getById($nation_code, $c_produk_id, $pelanggan, $getProductType);
		if(!isset($produk->id)){
			$this->status = 310;
			$this->message = 'Data not found or deleted';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wishlist");
			die();
		}

    $check = $this->dwlm->check($nation_code, $pelanggan->id, $c_produk_id);
    if(empty($check)){
      //insert data
      $di = array();
			$di['nation_code'] = $nation_code;
      $di['c_produk_id'] = $c_produk_id;
      $di['b_user_id'] = $pelanggan->id;
			$di['id'] = 1;
      $di['cdate'] = 'NOW()';
      $res = $this->dwlm->set($di);
      if($res){
        $this->status = 200;
        $this->message = 'Success';
      }else{
        $this->status = 803;
        $this->message = 'Failed add to wishlist';
      }
    }else{
  		$this->status = 801;
  		$this->message = 'Product has been wishlisted';
    }
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wishlist");
	}
	public function hapus(){
		//initial
		$dt = $this->__init();

		//default result
		$data = array();
		$data['wishlist_count'] = 0;
		$data['wishlist'] = array();

    //check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
    if(empty($nation_code)){
      $this->status = 101;
  		$this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wishlist");
      die();
    }

		//check apikey
		$apikey = $this->input->get('apikey');
		$c = $this->apikey_check($apikey);
		if(!$c){
			$this->status = 400;
			$this->message = 'Missing or invalid API key';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wishlist");
			die();
		}

		//check apisess
		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if(!isset($pelanggan->id)){
			$this->status = 401;
			$this->message = 'Missing or invalid API session';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wishlist");
			die();
		}

    $c_produk_id = $this->input->post('c_produk_id');
		if($c_produk_id<='0'){
			$this->status = 311;
			$this->message = 'Invalid product ID';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wishlist");
			die();
		}

		$getProductType = $this->cpm->getProductType($nation_code, $c_produk_id);
		if(!isset($getProductType->product_type)){
			$this->status = 310;
			$this->message = 'Data not found or deleted';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wishlist");
			die();
		}

		$getProductType = $getProductType->product_type;

		$produk = $this->cpm->getById($nation_code, $c_produk_id, $pelanggan, $getProductType);
		if(!isset($produk->id)){
			$this->status = 310;
			$this->message = 'Data not found or deleted';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wishlist");
			die();
		}

    $check = $this->dwlm->check($nation_code, $pelanggan->id, $c_produk_id);
    if(!empty($check)){
      //insert data
      $di = array();
			$di['nation_code'] = $nation_code;
      $di['c_produk_id'] = $c_produk_id;
      $di['b_user_id'] = $pelanggan->id;
			$di['id'] = 1;
      $di['cdate'] = 'NOW()';
      $res = $this->dwlm->del($nation_code,$pelanggan->id, $c_produk_id);
      if($res){
        $this->status = 200;
        $this->message = 'Success';
      }else{
        $this->status = 804;
        $this->message = 'Failed to delete data from Wishlist';
      }
    }else{
  		$this->status = 802;
  		$this->message = 'Product is not in wishlist';
    }
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "wishlist");
	}
}
