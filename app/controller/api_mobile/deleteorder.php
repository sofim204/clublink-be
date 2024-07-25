<?php

// require_once (SENEROOT.'kero/lib/PHP-FFMpeg-1.0.1/src/FFMpeg/FFMpeg.php');

class Deleteorder extends JI_Controller {

	//By Donny Dennison - 22 Juni 2020 - 18:38
	//Requested by Mr Jackie to make function that can delete order from DB

	public function __construct(){
    	parent::__construct();
    	$this->lib("seme_log");
		$this->load("api_mobile/delete_order_model",'dom');
        $this->load("api_mobile/d_order_model", 'order');
        $this->load("api_mobile/d_order_detail_model", 'dodm');

        $this->load("api_admin/b_user_model", 'bum');
        $this->load("api_mobile/b_user_alamat_model", 'buam');

        $this->load("api_mobile/c_produk_model", 'cpm');
    	$this->load("api_mobile/c_produk_foto_model", "cpfm");

    	// $this->load("api_mobile/g_leaderboard_point_total_model", 'glptm');
    	$this->load("api_mobile/g_leaderboard_ranking_model", 'glrm');

    	$this->load("api_mobile/g_leaderboard_point_limit_model", 'glplm');
    	$this->load("api_mobile/common_code_model", "ccm");

    	$this->load("api_mobile/c_community_attachment_model", "ccam");

        $this->load("api_mobile/g_leaderboard_point_history_model", 'glphm');
	}

    //by Donny Dennison - 6 september 2022 17:50
    //integrate api blockchain
    //credit: https://www.php.net/manual/en/function.com-create-guid.php#119168
	private	function GUIDv4($trim = true)
	{
	    // Windows
	    if (function_exists('com_create_guid') === true) {
	        if ($trim === true)
	            return trim(com_create_guid(), '{}');
	        else
	            return com_create_guid();
	    }

	    // OSX/Linux
	    if (function_exists('openssl_random_pseudo_bytes') === true) {
	        $data = openssl_random_pseudo_bytes(16);
	        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
	        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
	        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	    }

	    // Fallback (PHP 4.2+)
	    mt_srand((double)microtime() * 10000);
	    $charid = strtolower(md5(uniqid(rand(), true)));
	    $hyphen = chr(45);                  // "-"
	    $lbrace = $trim ? "" : chr(123);    // "{"
	    $rbrace = $trim ? "" : chr(125);    // "}"
	    $guidv4 = $lbrace.
	              substr($charid,  0,  8).$hyphen.
	              substr($charid,  8,  4).$hyphen.
	              substr($charid, 12,  4).$hyphen.
	              substr($charid, 16,  4).$hyphen.
	              substr($charid, 20, 12).
	              $rbrace;
	    return $guidv4;
	}

	public function index(){
		
		$data= array();

	    //check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
	    if(empty($nation_code)){
	      	$this->status = 101;
	  		$this->message = 'Missing or invalid nation_code';
	      	$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");
	      	die();
	    }

	    //check activation code
		$activation_code = $this->input->get('activation_code');
        if ($activation_code != '21F2E429ABD0404B46953A9BEE5E5FCEB279FDEC40788263991744E5931AA6D7P@$$w0rd!') {
            $this->status = 3000;
            $this->message = 'Wrong Activation Code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");
            die();
        }

		//get order id
        $d_order_id = (int) $this->input->get('d_order_id');
        if ($d_order_id<=0) {
            $this->status = 3001;
            $this->message = 'Order not found or deleted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");
            die();
        }

		//get order
        $order = $this->order->getById($nation_code, $d_order_id);
        if (!isset($order->id)) {
            $this->status = 3001;
            $this->message = 'Order not found or deleted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");
            die();
        }

        $this->dom->trans_start();

		//delete order data
		$res = $this->dom->deleteOrder($d_order_id,$nation_code);

		//delete order alamat data
		$res = $this->dom->deleteOrderAlamat($d_order_id,$nation_code);

		//delete order detail data
		$res = $this->dom->deleteOrderDetail($d_order_id,$nation_code);

		//delete order detail item data
		$res = $this->dom->deleteOrderDetailItem($d_order_id,$nation_code);

		//delete order detail pickup data
		$res = $this->dom->deleteOrderDetailPickup($d_order_id,$nation_code);

		//delete order proses data
		$res = $this->dom->deleteOrderProses($d_order_id,$nation_code);

		//delete chat data
		// $res = $this->dom->deleteChat($d_order_id,$nation_code);

		//delete chat attachment data
		$res = $this->dom->deleteChatAttachment($d_order_id,$nation_code);

		//delete chat participant data
		// $res = $this->dom->deleteChatParticipant($d_order_id,$nation_code);

		//delete complain data
		$res = $this->dom->deleteComplain($d_order_id,$nation_code);

		//delete rating data
		$res = $this->dom->deleteRating($d_order_id,$nation_code);

		//delete pemberitahuan data
		$res = $this->dom->deletePemberitahuan($d_order_id,$nation_code);


		if($res){
            $this->dom->trans_commit();
			$this->status = 200;
			$this->message = 'Success';
		}else{
            $this->dom->trans_rollback();
			$this->status = 802;
			$this->message = 'Failed to delete data';
		}
	    
        $this->dom->trans_end();
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");
	
	}

	public function emptyCustomerPhoneNumber(){

		$data= array();

	    //check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
	    if(empty($nation_code)){
	      	$this->status = 101;
	  		$this->message = 'Missing or invalid nation_code';
	      	$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");
	      	die();
	    }

	    //check activation code
		$activation_code = $this->input->get('activation_code');
        if ($activation_code != '21F2E429ABD0404B46953A9BEE5E5FCEB279FDEC40788263991744E5931AA6D7P@$$w0rd!') {
            $this->status = 3000;
            $this->message = 'Wrong Activation Code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");
            die();
        }

        $this->dom->trans_start();

		//delete order data
		$res = $this->dom->emptyCustomerPhoneNumber($nation_code);

		if($res){
            $this->dom->trans_commit();
			$this->status = 200;
			$this->message = 'Success';
		}else{
            $this->dom->trans_rollback();
			$this->status = 802;
			$this->message = 'Failed to delete data';
		}

        $this->dom->trans_end();
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");

	}

	public function checkcustomerdonthavedefaultaddress(){

		$data= array();

	    //check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
	    if(empty($nation_code)){
	      	$this->status = 101;
	  		$this->message = 'Missing or invalid nation_code';
	      	$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");
	      	die();
	    }

	    //check activation code
		$activation_code = $this->input->get('activation_code');
        if ($activation_code != '21F2E429ABD0404B46953A9BEE5E5FCEB279FDEC40788263991744E5931AA6D7P@$$w0rd!') {
            $this->status = 3000;
            $this->message = 'Wrong Activation Code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");
            die();
        }

        $this->dom->trans_start();

		//get customer list
        $listCustomer = $this->bum->getAll($nation_code, -1, -1, "", "", '', "", 1);
		
		$return = array();

		foreach ($listCustomer as $customer) {
			
			//get total address
			$address = $this->buam->getByUserIdDefault($nation_code, $customer->id);

			if(count($address) == 1){
				
				$du= array();
				$du['is_default']= 1;
    			$this->buam->update($nation_code, $customer->id, $address[0]->id, $du);

			}else if(count($address) > 1){

				$is_default = 0;

				foreach ($address as $add) {


					if($add->is_default == 1){
						$is_default = 1;
					}

	           	}

	           	if($is_default == 0){
	           		$return[] = $customer;
	           	}

			}


		}

		$data = $return;

		if($return){
            $this->dom->trans_commit();
			$this->status = 200;
			$this->message = 'Success';
		}else{
            $this->dom->trans_rollback();
			$this->status = 802;
			$this->message = 'Failed to delete data';
		}

        $this->dom->trans_end();
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");

	}

	// public function duplicateaddresstotableproduk(){
		
	// 	$data= array();

	//     //check nation_code
	// 	$nation_code = $this->input->get('nation_code');
	// 	$nation_code = $this->nation_check($nation_code);
	//     if(empty($nation_code)){
	//       	$this->status = 101;
	//   		$this->message = 'Missing or invalid nation_code';
	//       	$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");
	//       	die();
	//     }

	//     //check activation code
	// 	$activation_code = $this->input->get('activation_code');
 //        if ($activation_code != '21F2E429ABD0404B46953A9BEE5E5FCEB279FDEC40788263991744E5931AA6D7P@$$w0rd!') {
 //            $this->status = 3000;
 //            $this->message = 'Wrong Activation Code';
 //            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");
 //            die();
 //        }

 //        $this->cpm->trans_start();

	// 	$productData = $this->cpm->getAllForMigrationAddress($nation_code);

	// 	foreach($productData as $product){
	// 		$du = array();
	// 		$du['alamat2'] = $product->alamat2;
	// 		$du['kelurahan'] = $product->kelurahan;
	// 		$du['kecamatan'] = $product->kecamatan;
	// 		$du['kabkota'] = $product->kabkota;
	// 		$du['provinsi'] = $product->provinsi;
	// 		$du['kodepos'] = $product->kodepos;
	// 		$du['latitude'] = $product->latitude;
	// 		$du['longitude'] = $product->longitude;
	// 		$this->cpm->update($nation_code, $product->b_user_id_seller, $product->id, $du);
 //        	$this->cpm->trans_commit();
	// 	}


	// 	$this->status = 200;
	// 	$this->message = 'Success';
    
 //        $this->cpm->trans_end();
	// 	$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");
	
	// }

	// public function inserttablepointtotal(){
		
	// 	$data= array();

	//     //check nation_code
	// 	$nation_code = $this->input->get('nation_code');
	// 	$nation_code = $this->nation_check($nation_code);
	//     if(empty($nation_code)){
	//       	$this->status = 101;
	//   		$this->message = 'Missing or invalid nation_code';
	//       	$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");
	//       	die();
	//     }

	//     //check activation code
	// 	$activation_code = $this->input->get('activation_code');
 //        if ($activation_code != '21F2E429ABD0404B46953A9BEE5E5FCEB279FDEC40788263991744E5931AA6D7P@$$w0rd!') {
 //            $this->status = 3000;
 //            $this->message = 'Wrong Activation Code';
 //            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");
 //            die();
 //        }

 //        $this->cpm->trans_start();

 //        $listCustomer = $this->bum->getAll($nation_code, -1, -1, "", "", '', "", 1);
        
	// 	foreach($listCustomer as $customer){
	// 		$pointAndPost = $this->glrm->getByUserId($nation_code, $customer->id);

	// 		$du = array();
	// 		$du['nation_code'] = $customer->nation_code;
	// 		$du['b_user_id'] = $customer->id;
	// 		if(isset($pointAndPost->total_post)){
	// 			$du['total_post'] = $pointAndPost->total_post;
	// 			$du['total_point'] = $pointAndPost->total_point;
	// 		}
	// 		$this->glptm->set($du);
 //        	$this->cpm->trans_commit();
	// 	}


	// 	$this->status = 200;
	// 	$this->message = 'Success';
    
 //        $this->cpm->trans_end();
	// 	$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");
	
	// }

	// public function convertvideocommunity(){
		
	// 	$data= array();

	//     //check nation_code
	// 	$nation_code = $this->input->get('nation_code');
	// 	$nation_code = $this->nation_check($nation_code);
	//     if(empty($nation_code)){
	//       	$this->status = 101;
	//   		$this->message = 'Missing or invalid nation_code';
	//       	$this->__json_out($data);
	//       	die();
	//     }

	//     //check activation code
	// 	$activation_code = $this->input->get('activation_code');
 //        if ($activation_code != '21F2E429ABD0404B46953A9BEE5E5FCEB279FDEC40788263991744E5931AA6D7P@$$w0rd!') {
 //            $this->status = 3000;
 //            $this->message = 'Wrong Activation Code';
 //            $this->__json_out($data);
 //            die();
 //        }

	// 	$c_community_id = $this->input->get('c_community_id');
	//     if($c_community_id <= 0){
	//       	$this->status = 3001;
	//   		$this->message = 'Missing or invalid c_community_id';
	//       	$this->__json_out($data);
	//       	die();
	//     }

	// 	$video_id = $this->input->get('video_id');
	//     if($video_id <= 0){
	//       	$this->status = 3002;
	//   		$this->message = 'Missing or invalid video_id';
	//       	$this->__json_out($data);
	//       	die();
	//     }

 //        $this->seme_log->write("api_mobile", 'start convert video community, community id '. $c_community_id .', video id '.$video_id);

 //        $this->cpm->trans_start();

 //        $videoNeedConvert = $this->ccam->getAll($nation_code, "video");
 //        if($videoNeedConvert){
          
 //            foreach ($videoNeedConvert as $video) {

	//             $fileext = pathinfo($video->url, PATHINFO_EXTENSION);

 //            	// if(strtolower($fileext) != "mp4"){

 //            		// if($video->c_community_id == $c_community_id && $video->id == $video_id){

	// 		    		$tempName = $video->nation_code.$video->c_community_id.$video->id.date('YmdHis').rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).".".$fileext;

	// 					exec("ffmpeg -y -i ".SENEROOT.$video->url." -preset slow -movflags faststart -crf 30 -ar 44100 ".SENEROOT.$this->media_temporary.DIRECTORY_SEPARATOR.$tempName." -hide_banner 2>&1", $responseFFmpeg , $statusFFmpeg);

	// 					if($statusFFmpeg == 0){

	// 			          	$fileloc = SENEROOT.$video->url;
	// 			            unlink($fileloc);

	// 						// $video->url = str_replace(".".strtolower($fileext), ".mp4", $video->url);

	// 		      			rename(SENEROOT.$this->media_temporary.DIRECTORY_SEPARATOR.$tempName, SENEROOT.$video->url);

	// 			            $di = array();
	// 			            $di['url'] = $video->url;
	// 			            $di['convert_status'] = "processed";
	// 			            $this->ccam->update($video->nation_code, $video->c_community_id, $video->id, $video->jenis, $di);
	//         				$this->cpm->trans_commit();

	//         				// break;
	// 			        }

	// 			    // }

	// 		    // }

 //    		}
 //    		unset($videoNeedConvert, $video);

 //        }

 //        $this->seme_log->write("api_mobile", 'end convert video community, community id '. $c_community_id .', video id '.$video_id);

	// 	$this->status = 200;
	// 	$this->message = 'Success';
    
 //        $this->cpm->trans_end();
	// 	$this->__json_out($data);
	
	// }

	// public function generateblockchainaccount(){

	// 	$data= array();

	//     //check nation_code
	// 	$nation_code = $this->input->get('nation_code');
	// 	$nation_code = $this->nation_check($nation_code);
	//     if(empty($nation_code)){
	//       	$this->status = 101;
	//   		$this->message = 'Missing or invalid nation_code';
	//       	$this->__json_out($data);
	//       	die();
	//     }

	//     //check activation code
	// 	$activation_code = $this->input->get('activation_code');
    //     if ($activation_code != '21F2E429ABD0404B46953A9BEE5E5FCEB279FDEC40788263991744E5931AA6D7P@$$w0rd!') {
    //         $this->status = 3000;
    //         $this->message = 'Wrong Activation Code';
    //         $this->__json_out($data);
    //         die();
    //     }

	// 	//get customer list
    //     $listCustomer = $this->bum->getAllDontHaveWallet($nation_code);
	// 	foreach ($listCustomer as $customer) {
	//         $endDoWhile = 0;
	//         do{
	// 			$generatedGUID = $this->GUIDv4();
	//             $checkWalletCode = $this->bum->checkWalletCode($nation_code, $generatedGUID);
	//         	if($checkWalletCode == 0){
	//                 $endDoWhile = 1;
	//             }
	//         }while($endDoWhile == 0);

	// 		$du= array();
	// 		$du['user_wallet_code']= strtoupper($generatedGUID);
	// 		$this->bum->update($nation_code, $customer->id, $du);
	// 	}

	// 	$this->__json_out($data);
	// }

	// public function generatekodereferral(){

	// 	$data= array();

	//     //check nation_code
	// 	$nation_code = $this->input->get('nation_code');
	// 	$nation_code = $this->nation_check($nation_code);
	//     if(empty($nation_code)){
	//       	$this->status = 101;
	//   		$this->message = 'Missing or invalid nation_code';
	//       	$this->__json_out($data);
	//       	die();
	//     }

	//     //check activation code
	// 	$activation_code = $this->input->get('activation_code');
 //        if ($activation_code != '21F2E429ABD0404B46953A9BEE5E5FCEB279FDEC40788263991744E5931AA6D7P@$$w0rd!') {
 //            $this->status = 3000;
 //            $this->message = 'Wrong Activation Code';
 //            $this->__json_out($data);
 //            die();
 //        }

	// 	//get customer list
 //        $listCustomer = $this->bum->getAll($nation_code, -1, -1, "", "", '', "", "");

	// 	foreach ($listCustomer as $customer) {

	//         $endDoWhile = 0;
	//         do{

	//         	$length = 8;
	//         	$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
	// 		    $charactersLength = strlen($characters);
	// 		    $generatedKodeReferral = '';
	// 		    for ($i = 0; $i < $length; $i++) {
	// 		        $generatedKodeReferral .= $characters[rand(0, $charactersLength - 1)];
	// 		    }

	//             $checkKodeReferral = $this->bum->checkKodeReferral($nation_code, $generatedKodeReferral);

	//         	if($checkKodeReferral == 0){
	//                 $endDoWhile = 1;
	//             }

	//         }while($endDoWhile == 0);

	// 		$du= array();
	// 		$du['kode_referral']= $generatedKodeReferral;
	// 		$this->bum->update($nation_code, $customer->id, $du);

	// 	}

	// 	$this->__json_out($data);
	// }

	// public function revertpoint(){

	// 	$data= array();

	//     //check nation_code
	// 	$nation_code = $this->input->get('nation_code');
	// 	$nation_code = $this->nation_check($nation_code);
	//     if(empty($nation_code)){
	//       	$this->status = 101;
	//   		$this->message = 'Missing or invalid nation_code';
	//       	$this->__json_out($data);
	//       	die();
	//     }

	//     //check activation code
	// 	$activation_code = $this->input->get('activation_code');
    //     if ($activation_code != '21F2E429ABD0404B46953A9BEE5E5FCEB279FDEC40788263991744E5931AA6D7P@$$w0rd!') {
    //         $this->status = 3000;
    //         $this->message = 'Wrong Activation Code';
    //         $this->__json_out($data);
    //         die();
    //     }

    //     $list = $this->glphm->getAllStuck($nation_code);
	// 	foreach ($list as $history) {

    //     	$totalPoint = 0;
    //     	$totalPost = 0;

    //         $dataNow = $this->glptm->getByUserId($nation_code, $history->b_user_id);
    //         if(!isset($dataNow->id)){

    //             //create point
    //             $di = array();
    //             $di['nation_code'] = $nation_code;
    //             $di['b_user_id'] = $history->b_user_id;
    //             $di['total_post'] = 0;
    //             $di['total_point'] = 0;
    //             $this->glptm->set($di);

    //             $dataNow = $this->glptm->getByUserId($nation_code, $history->b_user_id);

    //         }

    //         if($history->plusorminus == "-"){
    //         	$totalPoint = $dataNow->total_point + $history->point;
    //         }else{
    //         	$totalPoint = $dataNow->total_point - $history->point;
    //         }

    //         if(($history->custom_type == "community" && $history->custom_type_sub == "post") || ($history->custom_type == "product" && $history->custom_type_sub == "post")){

    //             if($history->plusorminus == "-"){
    //                 $totalPost = $dataNow->total_post + 1;
    //             }else{
    //                 $totalPost = $dataNow->total_post - 1;
    //             }

    //         }

    //         if($totalPoint < 0){
    //             $totalPoint = 0;
    //         }

    //         if($totalPost < 0){
    //             $totalPost = 0;
    //         }

    //         $du = array();
    //         $du["total_point"] = $totalPoint;
    //         $du["total_post"] = $totalPost;
    //         $this->glptm->update($nation_code, $history->b_user_id, $du);

    //         $du = array();
    //         $du["is_calculated"] = "0";
    //         $du["main_transaction_id"] = "NULL";
    //         $du["detail_transaction_id"] = "NULL";
    //         $this->glphm->update($nation_code, $history->b_user_id, $history->id, $history->kelurahan, $history->kecamatan, $history->kabkota, $history->provinsi, $du);

	// 	}

	// 	$this->__json_out($data);
	// }

}
