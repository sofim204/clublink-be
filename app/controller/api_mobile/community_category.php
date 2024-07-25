<?php
class Community_category extends JI_Controller{

	public function __construct(){
		parent::__construct();
		//$this->setTheme('frontx');
    	$this->load("api_mobile/b_user_model", "bu");
		$this->load("api_mobile/c_community_category_model","cccm");
	}

	//by Donny Dennison - 15 october 2020 16:49
  	//add automovite product api
	// public function index(){
	public function index($kategori_id = 0){

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
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_category");
			die();
		}

    	//check apikey
		$apikey = $this->input->get('apikey');
		$ca = $this->apikey_check($apikey);
		if(empty($ca)){
			$this->status = 400;
			$this->message = 'Missing or invalid API key';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_category");
			die();
		}

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

	    $timezone = $this->input->get("timezone");
	    if($this->isValidTimezoneId($timezone) === false){
	      $timezone = $this->default_timezone;
	    }

	    if (!isset($pelanggan->id)) {
	      if($timezone == "Asia/Jakarta") {
	        $pelanggan->language_id = 2;
	      }else{
	        $pelanggan->language_id = 1;
	      }
	    }

		$this->status = 200;
		$this->message = "Success";

	    //by Donny Dennison - 15 february 2022 9:50
	    //category product and category community have more than 1 language
		// $data['kategori'] = $this->cccm->getHomepage($nation_code, $kategori_id);
		$data['kategori'] = $this->cccm->getHomepage($nation_code, $kategori_id, $pelanggan->language_id);
		foreach($data['kategori'] as &$kat){
			if(isset($kat->image_icon)) if(strlen($kat->image_icon)<=4) $kat->image_icon = "media/kategori/default-icon.png";
			if(isset($kat->image_cover)) if(strlen($kat->image_cover)<=4) $kat->image_cover = "media/kategori/default-cover.png";
			if(isset($kat->image)) if(strlen($kat->image)<=4) $kat->image = "media/kategori/default.png";
			if(isset($kat->image_icon)) $kat->image_icon = $this->cdn_url($kat->image_icon);
			if(isset($kat->image_cover)) $kat->image_cover = $this->cdn_url($kat->image_cover);
			if(isset($kat->image)) $kat->image = $this->cdn_url($kat->image);
		}

		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_category");
	}

	public function detail($id=""){
    	//init
		$dt = $this->__init();

    	//default result format
		$data = array();
		$data['kategori'] = new stdClass();

    	//check id
		$id = (int) $id;
		if($id<=0){
			$this->status = 180;
			// $this->message = 'Invalid Kategori ID';
			$this->message = 'Invalid kategori ID or kategori has been deleted';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_category");
			die();
		}

    	//check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
		if(empty($nation_code)){
			$this->status = 101;
			$this->message = 'Missing or invalid nation_code';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_category");
			die();
		}

    	//check apikey
		$apikey = $this->input->get('apikey');
		$ca = $this->apikey_check($apikey);
		if(empty($ca)){
			$this->status = 400;
			$this->message = 'Missing or invalid API key';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_category");
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

		// $data['kategori'] = $this->cccm->getById($nation_code,$id);
		$data['kategori'] = $this->cccm->getById($nation_code,$id, $pelanggan->language_id);

		if(!isset($data['kategori']->id)){
			$this->status = 180;
			$this->message = 'Invalid kategori ID or kategori has been deleted';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_category");
			die();
		}
		if(strlen($data['kategori']->image_icon)<=4) $data['kategori']->image_icon = "media/kategori/default-icon.png";
		if(strlen($data['kategori']->image_cover)<=4) $data['kategori']->image_cover = "media/kategori/default-cover.png";
		if(strlen($data['kategori']->image)<=4) $data['kategori']->image = "media/kategori/default.png";
		$data['kategori']->image_icon = base_url($data['kategori']->image_icon);
		$data['kategori']->image_cover = base_url($data['kategori']->image_cover);
		$data['kategori']->image = base_url($data['kategori']->image);



    //default message
		$this->status = 200;
		$this->message = 'Success';
    //render
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_category");
	}

	// public function produk(){
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
 //      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_category");
 //      die();
 //    }

 //    //check nation_code
	// 	$nation_code = $this->input->get('nation_code');
	// 	$nation_code = $this->nation_check($nation_code);
 //    if(empty($nation_code)){
 //      $this->status = 101;
 //  		$this->message = 'Missing or invalid nation_code';
 //      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_category");
 //      die();
 //    }
	// 	$this->status = 200;
	// 	$this->message = 'Success';
	// 	$cats = array();
	// 	$cat = array();
	// 	$kategories = $this->cccm->getKategori($nation_code);
	// 	foreach($kategories as $kategori){
	// 		if($kategori->b_kategori_id == "-" || $kategori->utype=="kategori"){

	// 			//manipulator
	// 			if(strlen($kategori->image_icon)<=4) $kategori->image_icon = "media/kategori/default-icon.png";
	// 			if(strlen($kategori->image_cover)<=4) $kategori->image_cover = "media/kategori/default-cover.png";
	// 			if(strlen($kategori->image)<=4) $kategori->image = "media/kategori/default.png";
	// 			$kategori->image_icon = base_url($kategori->image_icon);
	// 			$kategori->image_cover = base_url($kategori->image_cover);
	// 			$kategori->image = base_url($kategori->image);

	// 			$cats[$kategori->id] = $kategori;
	// 			$cats[$kategori->id]->childs = array();
	// 		}else if($kategori->utype=="kategori_sub"){
	// 			if(!isset($cats[$kategori->b_kategori_id])){
	// 				$cats[$kategori->b_kategori_id] = new stdClass();
	// 				$cats[$kategori->b_kategori_id]->childs = array();
	// 			}
	// 			if(!isset($cats[$kategori->b_kategori_id]->childs[$kategori->id]))
	// 				$cats[$kategori->b_kategori_id]->childs[$kategori->id] = new stdClass();

	// 				//manipulator
	// 				if(strlen($kategori->image_icon)<=4) $kategori->image_icon = "media/kategori/default-icon.png";
	// 				if(strlen($kategori->image_cover)<=4) $kategori->image_cover = "media/kategori/default-cover.png";
	// 				if(strlen($kategori->image)<=4) $kategori->image = "media/kategori/default.png";
	// 				$kategori->image_icon = base_url($kategori->image_icon);
	// 				$kategori->image_cover = base_url($kategori->image_cover);
	// 				$kategori->image = base_url($kategori->image);

	// 			$cats[$kategori->b_kategori_id]->childs[$kategori->id] = $kategori;
	// 		}else{
	// 			//manipulator
	// 			if(strlen($kategori->image_icon)<=4) $kategori->image_icon = "media/kategori/default-icon.png";
	// 			if(strlen($kategori->image_cover)<=4) $kategori->image_cover = "media/kategori/default-cover.png";
	// 			if(strlen($kategori->image)<=4) $kategori->image = "media/kategori/default.png";
	// 			$kategori->image_icon = base_url($kategori->image_icon);
	// 			$kategori->image_cover = base_url($kategori->image_cover);
	// 			$kategori->image = base_url($kategori->image);

	// 			$cat[$kategori->id] = $kategori;
	// 		}
	// 	}
	// 	$cats = array_values($cats);
	// 	foreach($cats as &$cat){
	// 		if(count($cat->childs)){
	// 			$chld1=array();
	// 			foreach($cat->childs as $ca){
	// 				$chld2 = $ca;
	// 				if(isset($ca->childs)){
	// 					$chld3 = array();
	// 					foreach($ca->childs as $c){
	// 						$chld3[] = $c;
	// 					}
	// 					$chld2->childs = $chld3;
	// 				}
	// 				$chld1[] = $chld2;
	// 			}
	// 		}
	// 		$cat->childs = $chld1;
	// 	}
	// 	$data['kategori'] = $cats;

 //    //render
	// 	$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "community_category");
	// }
	
}
