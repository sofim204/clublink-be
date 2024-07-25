<?php
class Visitor extends JI_Controller {

	public function __construct(){
    parent::__construct();
		$this->lib("seme_curl");
        $this->load("api_mobile/b_user_model", 'bu');
		$this->load("api_mobile/f_visitor_model",'fvm');
		$this->load("api_mobile/f_visitor_count_model",'fvcm');
		$this->load("api_mobile/g_daily_track_record_model", 'gdtrm');
	}

	// public function index(){
	// 	//initial
	// 	$dt = $this->__init();

	// 	//default result
	// 	$data = array();
	// 	$data['wishlist_count'] = 0;
	// 	$data['wishlist'] = array();

 //    //check nation_code
	// 	$nation_code = $this->input->get('nation_code');
	// 	$nation_code = $this->nation_check($nation_code);
 //    if(empty($nation_code)){
 //      $this->status = 101;
 //  		$this->message = 'Missing or invalid nation_code';
 //      $this->__json_out($data);
 //      die();
 //    }

	// 	//check apikey
	// 	$apikey = $this->input->get('apikey');
	// 	$c = $this->apikey_check($apikey);
	// 	if(!$c){
	// 		$this->status = 400;
	// 		$this->message = 'Missing or invalid API key';
	// 		$this->__json_out($data);
	// 		die();
	// 	}

	// 	//check apisess
	// 	$apisess = $this->input->get('apisess');
	// 	$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
	// 	if(!isset($pelanggan->id)){
	// 		$this->status = 401;
	// 		$this->message = 'Missing or invalid API session';
	// 		$this->__json_out($data);
	// 		die();
	// 	}

	// 	//populate input
	// 	$page = (int) $this->input->get("page");
	// 	$pageSize = (int) $this->input->get("page_size"); //has different
	// 	if(empty($pageSize)) $pageSize = (int) $this->input->get("pageSize"); //has different
	// 	$sort_col = $this->input->get("sort_col");
	// 	$sort_dir = $this->input->get("sort_dir");
	// 	$keyword = $this->input->get("keyword");
	// 	if($page<=0) $page = 0;
	// 	if($pageSize<=0) $pageSize = 10;
	// 	if(strlen($keyword)<=1) $keyword = "";
	// 	if($sort_dir != 'desc') $sort_dir = 'asc';

	// 	$tbl_as = $this->dwlm->getTableAlias();
	// 	$tbl2_as = $this->dwlm->getTableAlias2();
	// 	switch($sort_col){
	// 		case "id":
	// 			$sort_col = $tbl2_as.".id";
	// 			break;
	// 		default:
	// 			$sort_col = $tbl2_as.".id";
	// 	}

	// 	$this->status = 200;
	// 	$this->message = 'Success';
 //    $data['wishlist'] = $this->dwlm->getAll($nation_code,$pelanggan->id,$page,$pageSize,$sort_col,$sort_dir,$keyword);
 //    $data['wishlist_count'] = $this->dwlm->countAll($nation_code,$pelanggan->id,$keyword);

	// 	foreach($data['wishlist'] as &$dw){
	// 		if(isset($dw->b_user_image_seller)){
	// 			if(empty($dw->b_user_image_seller)) $dw->b_user_image_seller = 'media/produk/default.png';
	// 			$dw->b_user_image_seller = $this->cdn_url($dw->b_user_image_seller);
	// 		}
 //      if(isset($dw->foto)){
 //        if(empty($dw->foto)) $dw->foto = 'media/produk/default.png';
 //        $dw->foto = $this->cdn_url($dw->foto);
 //      }
 //      if(isset($dw->thumb)){
 //        if(empty($dw->thumb)) $dw->thumb = 'media/produk/default.png';
 //        $dw->thumb = $this->cdn_url($dw->thumb);
 //      }
	// 		if(isset($dw->b_kondisi_icon)){
	// 			if(empty($dw->b_kondisi_icon)) $dw->b_kondisi_icon = 'media/icon/default.png';
	// 			$dw->b_kondisi_icon = $this->cdn_url($dw->b_kondisi_icon);
	// 		}
	// 		if(isset($dw->b_berat_icon)){
	// 			if(empty($dw->b_berat_icon)) $dw->b_berat_icon = 'media/icon/default.png';
	// 			$dw->b_berat_icon = $this->cdn_url($dw->b_berat_icon);
	// 		}
	// 	}
	// 	$this->__json_out($data);
	// }
	// public function tambah(){
	// 	//initial
	// 	$dt = $this->__init();

	// 	//default result
	// 	$data = array();
	// 	$data['wishlist_count'] = 0;
	// 	$data['wishlist'] = array();

 //    //check nation_code
	// 	$nation_code = $this->input->get('nation_code');
	// 	$nation_code = $this->nation_check($nation_code);
 //    if(empty($nation_code)){
 //      $this->status = 101;
 //  		$this->message = 'Missing or invalid nation_code';
 //      $this->__json_out($data);
 //      die();
 //    }

	// 	//check apikey
	// 	$apikey = $this->input->get('apikey');
	// 	$c = $this->apikey_check($apikey);
	// 	if(!$c){
	// 		$this->status = 400;
	// 		$this->message = 'Missing or invalid API key';
	// 		$this->__json_out($data);
	// 		die();
	// 	}

	// 	//check apisess
	// 	$apisess = $this->input->get('apisess');
	// 	$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
	// 	if(!isset($pelanggan->id)){
	// 		$this->status = 401;
	// 		$this->message = 'Missing or invalid API session';
	// 		$this->__json_out($data);
	// 		die();
	// 	}

 //    $c_produk_id = (int) $this->input->post('c_produk_id');
	// 	if($c_produk_id<=0){
	// 		$this->status = 311;
	// 		$this->message = 'Invalid product ID';
	// 		$this->__json_out($data);
	// 		die();
	// 	}
	// 	$produk = $this->cpm->getById($nation_code, $c_produk_id);
	// 	if(!isset($produk->id)){
	// 		$this->status = 310;
	// 		$this->message = 'Data not found or deleted';
	// 		$this->__json_out($data);
	// 		die();
	// 	}

 //    $check = $this->dwlm->check($nation_code, $pelanggan->id, $c_produk_id);
 //    if(empty($check)){
 //      //insert data
 //      $di = array();
	// 		$di['nation_code'] = $nation_code;
 //      $di['c_produk_id'] = $c_produk_id;
 //      $di['b_user_id'] = $pelanggan->id;
	// 		$di['id'] = 1;
 //      $di['cdate'] = 'NOW()';
 //      $res = $this->dwlm->set($di);
 //      if($res){
 //        $this->status = 200;
 //        $this->message = 'Success';
 //      }else{
 //        $this->status = 802;
 //        $this->message = 'Failed add to wishlist';
 //      }
 //    }else{
 //  		$this->status = 801;
 //  		$this->message = 'Product has been wishlisted';
 //    }
	// 	$this->__json_out($data);
	// }
	// public function hapus(){
	// 	//initial
	// 	$dt = $this->__init();

	// 	//default result
	// 	$data = array();
	// 	$data['wishlist_count'] = 0;
	// 	$data['wishlist'] = array();

 //    //check nation_code
	// 	$nation_code = $this->input->get('nation_code');
	// 	$nation_code = $this->nation_check($nation_code);
 //    if(empty($nation_code)){
 //      $this->status = 101;
 //  		$this->message = 'Missing or invalid nation_code';
 //      $this->__json_out($data);
 //      die();
 //    }

	// 	//check apikey
	// 	$apikey = $this->input->get('apikey');
	// 	$c = $this->apikey_check($apikey);
	// 	if(!$c){
	// 		$this->status = 400;
	// 		$this->message = 'Missing or invalid API key';
	// 		$this->__json_out($data);
	// 		die();
	// 	}

	// 	//check apisess
	// 	$apisess = $this->input->get('apisess');
	// 	$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
	// 	if(!isset($pelanggan->id)){
	// 		$this->status = 401;
	// 		$this->message = 'Missing or invalid API session';
	// 		$this->__json_out($data);
	// 		die();
	// 	}

 //    $c_produk_id = (int) $this->input->post('c_produk_id');
	// 	if($c_produk_id<=0){
	// 		$this->status = 311;
	// 		$this->message = 'Invalid product ID';
	// 		$this->__json_out($data);
	// 		die();
	// 	}
	// 	$produk = $this->cpm->getById($nation_code, $c_produk_id);
	// 	if(!isset($produk->id)){
	// 		$this->status = 310;
	// 		$this->message = 'Data not found or deleted';
	// 		$this->__json_out($data);
	// 		die();
	// 	}

 //    $check = $this->dwlm->check($nation_code, $pelanggan->id, $c_produk_id);
 //    if(!empty($check)){
 //      //insert data
 //      $di = array();
	// 		$di['nation_code'] = $nation_code;
 //      $di['c_produk_id'] = $c_produk_id;
 //      $di['b_user_id'] = $pelanggan->id;
	// 		$di['id'] = 1;
 //      $di['cdate'] = 'NOW()';
 //      $res = $this->dwlm->del($nation_code,$pelanggan->id, $c_produk_id);
 //      if($res){
 //        $this->status = 200;
 //        $this->message = 'Success';
 //      }else{
 //        $this->status = 802;
 //        $this->message = 'Failed to delete data from Wishlist.';
 //      }
 //    }else{
 //  		$this->status = 801;
 //  		$this->message = 'Product is not in wishlist';
 //    }
	// 	$this->__json_out($data);
	// }

	public function visit($mobile_type){
	// 	//initial
		$dt = $this->__init();

	// 	//default result
		$data = array();
	// 	// $data['wishlist_count'] = 0;
	// 	// $data['wishlist'] = array();

    // 	//check nation_code
	// 	$nation_code = $this->input->get('nation_code');
	// 	$nation_code = $this->nation_check($nation_code);
	//     if(empty($nation_code)){
	//       $this->status = 101;
	//   		$this->message = 'Missing or invalid nation_code';
	//       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "visitor");
	//       die();
	//     }

    // 	//get user id
	// 	$b_user_id = $this->input->get('id');
	// 	if(!isset($b_user_id) || empty($b_user_id)) {
	// 		$b_user_id = "0";
	// 	}

	// 	//get udid
	// 	$udid = $this->input->get('udid');
	// 	if(!isset($udid) || empty($udid)) {
	// 		$udid = "NULL";
	// 	}

	// 	//get latitude, longitude
	// 	// $latitude = $this->input->get('latitude');
	// 	// $longitude = $this->input->get('longitude');
	   
	// 	//check apikey
	// 	$apikey = $this->input->get('apikey');
	// 	$c = $this->apikey_check($apikey);
	// 	if(!$c){
	// 		$this->status = 400;
	// 		$this->message = 'Missing or invalid API key';
	// 		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "visitor");
	// 		die();
	// 	}

	// 	//by Donny Dennison - 15 february 2022 9:50
    //     //category product and category community have more than 1 language
    //     // check apisess
    //     $apisess = $this->input->get('apisess');
    //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     if (!isset($pelanggan->id)) {
    //         $pelanggan = new stdClass();
    //         if($nation_code == 62){ //indonesia
    //             $pelanggan->language_id = 2;
    //         }else if($nation_code == 82){ //korea
    //             $pelanggan->language_id = 3;
    //         }else if($nation_code == 66){ //thailand
    //             $pelanggan->language_id = 4;
    //         }else {
    //             $pelanggan->language_id = 1;
    //         }
    //     }

	// 	// $url = base_url("api_mobile/pelanggan/?apikey=$apikey&nation_code=$nation_code&apisess=$apisess");
    //     // $res = $this->seme_curl->get($url);
        
    //     // $body = json_decode($res->body);
    //     // $latlongdata = $body->data;

	// 	// $this->debug($body);

	// 	// $di = array();
	// 	// $di['total_visit'] = +1;
	// 	$res = $this->fvm->update($nation_code, $mobile_type);
	// 	$datenow = date("Y-m-d");
	// 	// $datenow = "NOW()";
	// 	$last_id = $this->fvm->getLastId($datenow, $mobile_type);
	// 	// $di = array();
    //     // $di['id'] = $last_id;
    //     // $di['b_user_id'] = $b_user_id;
    //     // $di['cdate'] = "NOW()";
    //     // $di['mobile_type'] =  $mobile_type;
    //     // $di['latitude'] =  $latitude;
    //     // $di['longitude'] =  $longitude;

	// 	// $url = "https://maps.google.com/maps/api/geocode/json?latlng=$latitude,$longitude&key=AIzaSyCzx5dDTHwJLrgqgM0LXH6D1VGAHC0m4Rw&language=id";

	// 	// $ch = curl_init();
	// 	// curl_setopt($ch, CURLOPT_URL, $url);
	// 	// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	// 	// curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
	// 	// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	// 	// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	// 	// $response = curl_exec($ch);
	// 	// curl_close($ch);
	// 	// $response_a = json_decode($response);
	// 	// // $location = $response_a->results[0]->address_components->types->administrative_area_level_3;
	// 	// // $location = $response_a->results[0]->address_components[5]->long_name;
	// 	// $response_geocode = $response_a->results[0]->address_components;
	// 	// foreach ($response_geocode as $geo) { 
	// 	// 	$type_geo = $geo->types[0];
	// 	// 	// $test2 = 
	// 	// 	// $test2 = $loc->long_name;
	// 	// 	if($type_geo == "administrative_area_level_3") {
	// 	// 		// echo "true\n";
	// 	// 		// echo $loc->long_name;
	// 	// 		$getlocation = $geo->long_name;
	// 	// 	} 
	// 	// 	if($type_geo == "administrative_area_level_1") {
	// 	// 		// echo "true\n";
	// 	// 		// echo $loc->long_name;
	// 	// 		$getprovince = $geo->long_name;
	// 	// 	} 
	// 	// 	// else {
	// 	// 	// 	echo "false\n";
	// 	// 	// }
	// 	// 	// echo $test;

	// 	// 	// echo $locationx;
	// 	// 	// var_dump($test);
	// 	// 	// echo
	// 	// 	// $locationx = $location;
	// 	// }
	// 	// // echo $location->administrative_area_level_3;
	// 	// // echo "<br />";
	// 	// // echo $long = $response_a->results[0]->geometry->location->lng;
	// 	// // echo $getlocation;
	// 	// $location = $getlocation;
	// 	// $province = $getprovince;
	// 	// var_dump($response_a);


	// 	$postData= array(
	// 		'id' => $last_id,
	// 		'b_user_id' => $b_user_id,
	// 		'cdate' => "NOW()",
	// 		'mobile_type' => $mobile_type,
	// 		'udid' => $udid,
	// 		// 'latitude' => $latitude,
	// 		// 'longitude' => $longitude,
	// 		// 'location' => $location,
	// 		// 'province' => $province,
	// 		'latitude' => 0.00,
	// 		'longitude' => 0.00,
	// 		'location' => "",
	// 		'province' => "",
	// 	);

	// 	// $this->lib("seme_curl");
	// 	// $url = base_url("api_mobile/pelanggan/daftar/?apikey=$apikey&nation_code=$nation_code");
	// 	// $curlResponse = $this->seme_curl->post($url, $postData);

	// 	// $body = json_decode($curlResponse->body);
	// 	// if ($body->status != 200) {
	// 	// 	$this->status = $body->status;
	// 	// 	$this->message = $body->message;
	// 	// 	$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
	// 	// 	die();
	// 	// }

	// 	$res = $this->fvm->addLog($postData);
	// 	// $this->gdtrm->updateTotalData(DATE("Y-m-d"), "visit", "+", "1");
	// 	if($res){
			$this->status = 200;
			$this->message = 'Success';
	// 	}else{
	// 		$this->status = 802;
	// 		$this->message = 'Failed add visit';
	// 	}
	
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "visitor");
	
	}


	public function visitorcount(){
		//initial
		$dt = $this->__init();

		//default result
		$data = array();

  //   	//check nation_code
		// $nation_code = $this->input->get('nation_code');
		// $nation_code = $this->nation_check($nation_code);
	 //    if(empty($nation_code)){
	 //      $this->status = 101;
	 //  		$this->message = 'Missing or invalid nation_code';
	 //      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "visitor");
	 //      die();
	 //    }

		// //check apikey
		// $apikey = $this->input->get('apikey');
		// $c = $this->apikey_check($apikey);
		// if(!$c){
		// 	$this->status = 400;
		// 	$this->message = 'Missing or invalid API key';
		// 	$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "visitor");
		// 	die();
		// }

        // // check apisess
        // $apisess = $this->input->get('apisess');
        // $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        // if (!isset($pelanggan->id)) {
        //     $pelanggan = new stdClass();
        //     if($nation_code == 62){ //indonesia
        //         $pelanggan->language_id = 2;
        //     }else if($nation_code == 82){ //korea
        //         $pelanggan->language_id = 3;
        //     }else if($nation_code == 66){ //thailand
        //         $pelanggan->language_id = 4;
        //     }else {
        //         $pelanggan->language_id = 1;
        //     }
        // }

		// $udid = $this->input->get('udid');
		$mobile_type = $this->input->get('mobile_type');
		if(!$mobile_type){
			$mobile_type = "android";
		}

		$datenow = date("Y-m-d");
		// $last_id = $this->fvcm->getLastId($datenow, $mobile_type);

		// $di = array();
        // $di['id'] = $last_id;
        // $di['cdate'] = "NOW()";
        // $di['mobile_type'] =  $mobile_type;
        // $di['udid'] =  $udid;

		// $this->fvcm->set($di);
		if($mobile_type == "android") {
			$this->gdtrm->updateTotalData($datenow, "visit_android", "+", "1");
		} else if($mobile_type == "ios") {
			$this->gdtrm->updateTotalData($datenow, "visit_ios", "+", "1");
		} else {}

		$this->gdtrm->updateTotalData($datenow, "visit", "+", "1");

		$this->status = 200;
		$this->message = 'Success';
	
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "visitor");
	
	}

}
