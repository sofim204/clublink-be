<?php
class Check_wanted extends JI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->lib("seme_email");
        $this->load("api_cron/a_notification_model", "anot");
        $this->load("api_cron/b_user_model", "bu");
        $this->load("api_cron/b_user_setting_model", "busm");
        $this->load("api_cron/c_produk_model", "cpm");
        $this->load("api_cron/d_pemberitahuan_model", "dpem");

    	$this->load("api_cron/b_user_productwanted_model", "bupw");
    	$this->load("api_cron/b_user_wish_product_model", "buwp");
    }

    public function index()
    {
        //open transaction
        // $this->cpm->trans_start();

        //change log filename
        // $this->seme_log->changeFilename("cron.log");

        //put on log
        $this->seme_log->write("api_cron", "API_Cron/Check_Wanted::index Start");

        $productList = $this->cpm->getuncheckWanted();
        $this->seme_log->write("api_cron", "API_Cron/Check_Wanted::total product: ".count($productList));
        if($productList){
			
			foreach ($productList as $product) {

	          	$du = array();
	          	$du['check_wanted'] = "1";
        		$this->cpm->update($product->nation_code, $product->id, $du);
        		// $this->cpm->trans_commit();

	        }
	        unset($product);

          	foreach ($productList as $product) {

	          	//get wanted user
	          	$wanteds = $this->bupw->getWanteds($product->nation_code, $product->nama, $product->b_user_id);
	        	$this->seme_log->write("api_cron", "API_Cron/Check_Wanted::--pushNotifCount: ".count($wanteds));

      			$seller = $this->bu->getById($product->nation_code, $product->b_user_id);

	          	foreach ($wanteds as $w) {

            		$checkAlreadyInList = $this->buwp->getByProductIDUserID($product->nation_code, $product->id, $w->b_user_id_buyer);

            		if(!isset($checkAlreadyInList->c_produk_id)){

		            	$buwpId = $this->buwp->getLastId($product->nation_code,$w->b_user_id_buyer);
		            	$di = array();
		            	$di['nation_code'] = $product->nation_code;
		            	$di['id'] = $buwpId;
		            	$di['b_user_id'] = $w->b_user_id_buyer;
		            	$di['c_produk_id'] = $product->id;
		            	$this->buwp->set($di);
		            	unset($di, $buwpId);

		        	}

		            //get notification config for buyer
		            $type = 'product_recommend';
		            $anotid = 1;
		            $replacer = array();

		            $replacer['product_recommendation'] = $this->convertEmoji($product->nama);

		            $setting_value2 = 0;
		            // $classified = 'setting_notification_buyer';
		            // $notif_code = 'B5';
		            $classified = 'setting_notification_user';
		            $notif_code = 'U7';
		            $notif_cfg = $this->busm->getValue($product->nation_code, $w->b_user_id_buyer, $classified, $notif_code);
		            if (isset($notif_cfg->setting_value)) {
		              $setting_value2 = (int) $notif_cfg->setting_value;
		            }
	              	$this->seme_log->write("api_cron", "API_Cron/Check_Wanted::-- b_user_device_buyer: $w->b_user_device_buyer, b_user_id_buyer: $w->b_user_id_buyer, setting_value2: $setting_value2");

	            	//push notif to buyer
	           	 	if (strlen($w->b_user_fcm_token_buyer) > 50 && !empty($setting_value2) && $w->b_user_is_active == 1) {

	              		$device = $w->b_user_device_buyer;
	              		$tokens = array($w->b_user_fcm_token_buyer);
	              		$type = 'product_recommend';
	              		$image = 'media/pemberitahuan/promotion.png';
	              		$payload = new stdClass();
	              		$payload->keyword = $w->keyword_text;
	              		$payload->id_produk = $product->id;
	              		$payload->product_type = $product->product_type;

	              		$payload->id_order = null;
	              		$payload->id_order_detail = null;
	              		$payload->b_user_id_buyer = $w->b_user_id_buyer;
	              		$payload->b_user_fnama_buyer = $w->b_user_fnama_buyer;
	              		if(file_exists(SENEROOT.$w->b_user_image_buyer) && $w->b_user_image_buyer != 'media/user/default.png'){
	                		$payload->b_user_image_buyer = $this->cdn_url($w->b_user_image_buyer);
	              		} else {
	                		$payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
	              		}
	              		$payload->b_user_id_seller = $seller->id;
	              		$payload->b_user_fnama_seller = $seller->fnama;

	              		if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
	                		$payload->b_user_image_seller = $this->cdn_url($seller->image);
	              		} else {
	                		$payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
	              		}
	              		$nw = $this->anot->get($product->nation_code, "push", $type, $anotid,$w->b_user_language_id);
	              		if (isset($nw->title)) {
	                		$title = $nw->title;
	              		}
	              		if (isset($nw->message)) {
	                		$message = $this->__nRep($nw->message, $replacer);
	              		}
	              		if (isset($nw->image)) {
	                		$image = $nw->image;
	              		}
	              		$image = $this->cdn_url($image);
	              		$res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
	        			unset($payload);

	            	}

			        //change log filename
			        // $this->seme_log->changeFilename("cron.log");

	            	//collect array notification list for buyer
	            	$extras = new stdClass();
	            	$extras->keyword = $w->keyword_text;
	            	$extras->id_order = null;
	            	$extras->id_produk = $product->id;
	            	$extras->product_type = $product->product_type;

	            	$extras->id_order_detail = null;
	            	$extras->b_user_id_buyer = $w->b_user_id_buyer;
	            	$extras->b_user_fnama_buyer = $w->b_user_fnama_buyer;
	            
	           	 	if(file_exists(SENEROOT.$w->b_user_image_buyer) && $w->b_user_image_buyer != 'media/user/default.png'){
	              		$extras->b_user_image_buyer = $this->cdn_url($w->b_user_image_buyer);
	            	} else {
	              		$extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
	            	}
	            	$extras->b_user_id_seller = $seller->id;
	            	$extras->b_user_fnama_seller = $seller->fnama;

		            if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
		              $extras->b_user_image_seller = $this->cdn_url($seller->image);
		            } else {
		              $extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
		            }
		            $dpe = array();
		            $dpe['nation_code'] = $product->nation_code;
		            $dpe['b_user_id'] = $w->b_user_id_buyer;
		            $dpe['id'] = $this->dpem->getLastId($product->nation_code, $w->b_user_id_buyer);
		            $dpe['type'] = "product_recommend";
		            if($w->b_user_language_id == 2) {
						$dpe['judul'] = "Pemberitahuan Kata Kunci";
						$dpe['teks'] = "Produk yang anda inginkan telah terdaftar (".html_entity_decode($product->nama,ENT_QUOTES).")";
					} else {
						$dpe['judul'] = "Keyword Notice";
						$dpe['teks'] = "The product you find has been registered (".html_entity_decode($product->nama,ENT_QUOTES).")";
					}
					
		            $dpe['cdate'] = "NOW()";
		            $dpe['gambar'] = 'media/pemberitahuan/promotion.png';
		            $dpe['extras'] = json_encode($extras);
		            $this->dpem->set($dpe);
	        		// $this->cpm->trans_commit();
	        		unset($dpe, $extras);
            
	          	}
	          	unset($wanteds, $w, $seller);

	        }

        }

        //end transacation
        // $this->cpm->trans_end();

        $this->seme_log->write("api_cron", "API_Cron/Check_Wanted::index Stop");

        die();
    }

}
