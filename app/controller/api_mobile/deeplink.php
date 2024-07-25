<?php

class Deeplink extends JI_Controller {

	public function __construct(){
    	parent::__construct();
    	$this->lib("seme_log");

        $this->load("api_mobile/b_user_model", 'bu');
		$this->load("api_mobile/c_produk_model", "cpm");
    	$this->load("api_mobile/c_community_model", "ccomm");
    	$this->load("api_mobile/c_community_attachment_model", "ccam");
    	$this->load("api_mobile/c_community_category_model", "cccm");

	}

	public function index(){
		
		$data= array();

		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "deeplink");
	
	}

	public function productdetail(){
		
		// $data= array();
		$data= new stdClass();
    	// $data['produk'] = new stdClass();

	    //check nation_code
		$nation_code = 62;
		// $nation_code = $this->input->get('nation_code');
		// $nation_code = $this->nation_check($nation_code);
	 //    if(empty($nation_code)){
	 //      	$this->status = 101;
	 //  		$this->message = 'Missing or invalid nation_code';
	 //      	$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");
	 //      	die();
	 //    }

		$c_produk_id = $this->input->get('c_produk_id');

	    $getProductType = $this->cpm->getProductType($nation_code, $c_produk_id);
	    $getProductType = $getProductType->product_type;

	    $produk = $this->cpm->getById($nation_code, $c_produk_id, array(), $getProductType, 1);
	    if (!isset($produk->id)) {
	      $this->status = 595;
	      $this->message = 'Invalid product ID or Product not found';
	      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
	      die();
	    }

    	$data->nama = html_entity_decode($produk->nama,ENT_QUOTES);

    	$data->deskripsi = html_entity_decode($produk->deskripsi,ENT_QUOTES);

    	$data->thumb = $this->cdn_url($produk->thumb);

		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "deeplink");
	
	}

	public function communitydetail(){
		
		// $data= array();
		$data= new stdClass();
    	// $data['produk'] = new stdClass();

	    //check nation_code
		$nation_code = 62;
		// $nation_code = $this->input->get('nation_code');
		// $nation_code = $this->nation_check($nation_code);
	 //    if(empty($nation_code)){
	 //      	$this->status = 101;
	 //  		$this->message = 'Missing or invalid nation_code';
	 //      	$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");
	 //      	die();
	 //    }

		$community_id = $this->input->get('community_id');
	    $community = $this->ccomm->getById($nation_code, $community_id, array(), 1);
	    if (!isset($community->id)) {
	      $this->status = 1160;
	      $this->message = 'This post is deleted by an author';
	      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community");
	      die();
	    }

	    if (strlen($community->b_user_image_starter)<=4) {
	      $community->b_user_image_starter = 'media/user/default.png';
	    }

	    if(file_exists(SENEROOT.$community->b_user_image_starter) && $community->b_user_image_starter != 'media/user/default.png'){
	      $community->b_user_image_starter = $this->cdn_url($community->b_user_image_starter);
	    } else {
	      $community->b_user_image_starter = $this->cdn_url('media/user/default-profile-picture.png');
	    }

	    // filter utf-8
	    if (isset($community->b_user_nama_starter)) {
	      $community->b_user_nama_starter = $this->__dconv($community->b_user_nama_starter);
	    }

    	$data->b_user_nama_starter = html_entity_decode($community->b_user_nama_starter,ENT_QUOTES);

    	$data->title = html_entity_decode($community->title,ENT_QUOTES);

    	$data->deskripsi = html_entity_decode($community->deskripsi,ENT_QUOTES);

        $attachment = $this->ccam->getByCommunityId($nation_code, $community_id,"first", "image");
        if(isset($attachment->id)){

          // if (empty($attachment->url)) {
          //   $attachment->url = 'media/community_default.png';
          // }
          // if (empty($attachment->url_thumb)) {
          //   $attachment->url_thumb = 'media/community_default.png';
          // }
            if (empty($attachment->url_thumb)) {
                $categoryData = $this->cccm->getById($nation_code, $community->c_community_category_id);
                $attachment->url = $categoryData->image_cover;
                $attachment->url_thumb = $categoryData->image_cover;
            }

            $attachment->url = $this->cdn_url($attachment->url);
            $attachment->url_thumb = $this->cdn_url($attachment->url_thumb);

        }else{

            $attachment = $this->cccm->getById($nation_code, $community->c_community_category_id);

            $attachment->url = $this->cdn_url($attachment->image_cover);
            $attachment->url_thumb = $this->cdn_url($attachment->image_cover);
            
        }

        $data->image = $attachment->url_thumb;

		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "deeplink");
	
	}

	public function profile(){
		
		// $data= array();
		$data= new stdClass();
    	// $data['produk'] = new stdClass();

	    //check nation_code
		$nation_code = 62;
		// $nation_code = $this->input->get('nation_code');
		// $nation_code = $this->nation_check($nation_code);
	 //    if(empty($nation_code)){
	 //      	$this->status = 101;
	 //  		$this->message = 'Missing or invalid nation_code';
	 //      	$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");
	 //      	die();
	 //    }

        $b_user_id = $this->input->get('b_user_id');
        $userData = $this->bu->getById($nation_code, $b_user_id);
        if (!isset($userData->id)) {
            $this->status = 1001;
            $this->message = 'Missing or invalid b_user_id';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "profile");
            die();
        }

        if(file_exists(SENEROOT.$userData->image) && $userData->image != 'media/user/default.png'){
            $userData->image = str_replace("//", "/", $this->cdn_url($userData->image));
        }else{
            $userData->image = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
        }

    	$data->fnama = html_entity_decode($userData->fnama,ENT_QUOTES);

    	$data->bio = html_entity_decode($userData->bio,ENT_QUOTES);

    	$data->image = $userData->image;

		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "deeplink");
	
	}

	public function encodedecode()
    {
        //initial
        $dt = $this->__init();
        $data = '';

        $type = $this->input->post("type");
        if ($type != 'encrypt' && $type != 'decrypt' ) {
            $data = array();
            $data['type'] = $type;
            $this->status = 828;
            $this->message = 'Wrong Type';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "deeplink");
            die();
        }

        if($type == 'encrypt'){

            $post_data = json_decode($this->input->post("post_data"));
            if (!isset($post_data)) {
                $this->status = 828;
                $this->message = 'Invalid post_data format';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "deeplink");
                die();
            }

        }else{

            $post_data = $this->input->post("data");
            if (strlen($post_data) < 1) {
                $this->status = 828;
                $this->message = 'Invalid post_data format';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "deeplink");
                die();
            }

        }

        //https://gist.github.com/vielhuber/3e4a74b44e62d057e6b9
		$key =  "09c8be0f08fb7695bdc28471fcfccee8bdf4f5673d2483a43dd2535c61f986ca";

        if($type == 'encrypt'){

            $data = base64_encode(openssl_encrypt(json_encode($post_data), 'AES-256-CBC', $key, 0, str_pad(substr($key, 0, 16), 16, '0', STR_PAD_LEFT)));

        }else{

            $data = openssl_decrypt(base64_decode($post_data), 'AES-256-CBC', $key, 0, str_pad(substr($key, 0, 16), 16, '0', STR_PAD_LEFT));;

        }

        //response
        $this->status = 200;
        $this->message = 'Success';

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "deeplink");
    }

}
