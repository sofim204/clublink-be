<?php
class community_like_category extends JI_Controller{

	public function __construct(){
    parent::__construct();
		//$this->setTheme('frontx');
        $this->load("api_mobile/b_user_model", 'bu');
		$this->load("api_mobile/c_community_like_category_model","cccm");
	}

	public function index(){

    //init
		$dt = $this->__init();

    //default result format
		$data = array();
		$data['kategori'] = array();

    //check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
    if(empty($nation_code)){
      $this->status = 101;
  		$this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like_category");
      die();
    }

    //check apikey
		$apikey = $this->input->get('apikey');
		$ca = $this->apikey_check($apikey);
    if(empty($ca)){
      $this->status = 400;
  		$this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like_category");
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

		$this->status = 200;
		$this->message = "Success";
		$data = array();

		$data['kategori'] = $this->cccm->getKategori($nation_code);

		foreach($data['kategori'] as &$kat){
			// if(isset($kat->image_icon)) if(strlen($kat->image_icon)<=4) $kat->image_icon = "media/kategori/default-icon.png";
			// if(isset($kat->image_cover)) if(strlen($kat->image_cover)<=4) $kat->image_cover = "media/kategori/default-cover.png";
			// if(isset($kat->image)) if(strlen($kat->image)<=4) $kat->image = "media/kategori/default.png";
			if(isset($kat->image_icon)) $kat->image_icon = $this->cdn_url($kat->image_icon);
			// if(isset($kat->image_cover)) $kat->image_cover = $this->cdn_url($kat->image_cover);
			// if(isset($kat->image)) $kat->image = $this->cdn_url($kat->image);
		}
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like_category");
	}

	// public function detail($id=""){
 //    //init
	// 	$dt = $this->__init();

 //    //default result format
	// 	$data = array();
	// 	$data['kategori'] = new stdClass();

 //    //check id
 //    $id = (int) $id;
 //    if($id<=0){
 //      $this->status = 180;
 //  		$this->message = 'Invalid Kategori ID';
 //      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like_category");
 //      die();
 //    }

 //    //check nation_code
	// 	$nation_code = $this->input->get('nation_code');
	// 	$nation_code = $this->nation_check($nation_code);
 //    if(empty($nation_code)){
 //      $this->status = 101;
 //  		$this->message = 'Missing or invalid nation_code';
 //      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like_category");
 //      die();
 //    }

 //    //check apikey
	// 	$apikey = $this->input->get('apikey');
	// 	$ca = $this->apikey_check($apikey);
 //    if(empty($ca)){
 //      $this->status = 400;
 //  		$this->message = 'Missing or invalid API key';
 //      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like_category");
 //      die();
 //    }
	// 	$data['kategori'] = $this->cccm->getById($nation_code,$id);
 //    if(!isset($data['kategori']->id)){
 //      $this->status = 180;
 //  		$this->message = 'Invalid kategori ID or kategori has been deleted';
 //      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like_category");
 //      die();
 //    }
	// 	if(strlen($data['kategori']->image_icon)<=4) $data['kategori']->image_icon = "media/kategori/default-icon.png";
	// 	if(strlen($data['kategori']->image_cover)<=4) $data['kategori']->image_cover = "media/kategori/default-cover.png";
	// 	if(strlen($data['kategori']->image)<=4) $data['kategori']->image = "media/kategori/default.png";
	// 	$data['kategori']->image_icon = base_url($data['kategori']->image_icon);
	// 	$data['kategori']->image_cover = base_url($data['kategori']->image_cover);
	// 	$data['kategori']->image = base_url($data['kategori']->image);



 //    //default message
	// 	$this->status = 200;
	// 	$this->message = 'Success';
 //    //render
	// 	$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_like_category");
	// }
	
}
