<?php
//buyer
class Complain extends JI_Controller{
	public $is_log = 1;

	public function __construct(){
		parent::__construct();
		$this->lib("seme_log");
        $this->load("api_mobile/a_notification_model", "anot");
	    $this->load("api_mobile/b_user_model","bu");
	    $this->load("api_mobile/d_order_model","order");
	    $this->load("api_mobile/d_order_detail_model","dodm");
	    $this->load("api_mobile/e_chat_model","chat");
	    $this->load("api_mobile/e_chat_attachment_model",'ecam');
        $this->load("api_mobile/e_chat_room_model",'ecrm');
        $this->load("api_mobile/e_chat_participant_model",'ecpm');
	    $this->load("api_mobile/e_complain_model","ecpl");
	    // $this->load("api_mobile/e_rating_model","erm");
        $this->load("api_mobile/e_chat_read_model", 'ecreadm');
        $this->load("api_mobile/b_user_setting_model", "busm");
	}

    //credit: https://www.php.net/manual/en/function.com-create-guid.php#119168
    private function GUIDv4($trim = true)
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

	// public function index(){
		// http_response_code("404");
		// echo 'Not found';
	// }
	
	// //By Donny Dennison - 3 august 2020 - 15:22
 //  	//Add 2 chat party, admin to seller / admin to buyer
	// // private function __dataEcp($nation_code,$d_order_id,$d_order_detail_item_id,$b_user_id){
	// private function __dataEcp($nation_code,$d_order_id,$d_order_detail_item_id,$b_user_id, $chat_type){
	// 	$ecp = array();
	// 	$ecp['nation_code'] = $nation_code;
	// 	$ecp['d_order_id'] = $d_order_id;
	// 	$ecp['c_produk_id'] = $d_order_detail_item_id;
	// 	$ecp['b_user_id'] = $b_user_id;
	// 	$ecp['is_read'] = 0;

 //        //By Donny Dennison - 3 august 2020 - 15:22
 //        //Add 2 chat party, admin to seller / admin to buyer
 //        $ecp['chat_type'] = $chat_type;

	// 	$ecps[] = $ecp;
	// 	return $ecp;
	// }

 //  //By Donny Dennison - 3 august 2020 - 15:22
 //  //Add 2 chat party, admin to seller / admin to buyer
 //  // private function __getChat($nation_code,$d_order_id,$d_order_detail_id,$d_order_detail_item_id=""){
 //  private function __getChat($nation_code,$d_order_id,$d_order_detail_id, $chat_type, $d_order_detail_item_id=""){
 //    //get complains
	// 	$complains = array();
	// 	$cn = $this->ecpl->getByOrderProdukId($nation_code,$d_order_id,$d_order_detail_id);
	// 	if(count($cn)>0){
	// 		foreach($cn as $c){
	// 			if(empty($c->e_chat_id)) $c->e_chat_id = 1;
	// 			$c->b_user_image_seller = $this->cdn_url($c->b_user_image_seller);
	// 			$c->b_user_image_buyer = $this->cdn_url($c->b_user_image_buyer);
	// 			$c->c_produk_foto = $this->cdn_url($c->c_produk_foto);
	// 			$c->c_produk_thumb = $this->cdn_url($c->c_produk_thumb);
	// 			$complains[$c->e_chat_id] = $c;
	// 		}
	// 	}
	// 	unset($c,$cn); //free up some memory

 //    //get attachment first
 //    $att = array();
 //    $attachments = $this->ecam->getDetailByOrder($nation_code,$d_order_id,$d_order_detail_id);
 //    foreach($attachments as $at){
 //      $key = $at->nation_code.'-'.$at->d_order_id.'-'.$at->d_order_detail_id.'-'.$at->e_chat_id;
 //      $at->url = $this->cdn_url($at->url);
 //      //put to array key
 //      if(!isset($att[$key])) $att[$key] = array();
 //      $att[$key][] = $at;
 //    }
 //    unset($at); //free some memory
 //    unset($attachments); //free some memory

	// //By Donny Dennison - 3 august 2020 - 15:22
 //  	//Add 2 chat party, admin to seller / admin to buyer
 //    //get chat
 //    // $chat = $this->chat->getDetailByOrder($nation_code,$d_order_id,$d_order_detail_id);
 //    $chat = $this->chat->getDetailByOrder($nation_code,$d_order_id,$d_order_detail_id, $chat_type);

 //    //chat iteration
 //    foreach($chat as &$ch){
	// 		if(isset($ch->c_produk_thumb)) $ch->c_produk_thumb = $this->cdn_url($ch->c_produk_thumb);
 //      if(isset($ch->c_produk_foto)) $ch->c_produk_foto = $this->cdn_url($ch->c_produk_foto);
 //      if(isset($ch->b_user_image)) $ch->b_user_image = $this->cdn_url($ch->b_user_image);
 //      if(isset($ch->a_pengguna_foto)) $ch->a_pengguna_foto = $this->cdn_url($ch->a_pengguna_foto);
 //      if(isset($ch->b_user_image_buyer)) $ch->b_user_image_buyer = $this->cdn_url($ch->b_user_image_buyer);
 //      if(isset($ch->b_user_image_seller)) $ch->b_user_image_seller = $this->cdn_url($ch->b_user_image_seller);

	// 		//fill attachments
 //      $ch->attachments = array();
 //      $key = $ch->nation_code.'-'.$ch->d_order_id.'-'.$ch->d_order_detail_id.'-'.$ch->id;
 //      if(isset($att[$key])) $ch->attachments = $att[$key];

	// 		//fill complain
	// 		$ch->complain = new stdClass();
	// 		if(isset($complains[$ch->id])) $ch->complain = $complains[$ch->id];
 //    }
	// 	unset($complains);
 //    return $chat;
 //  }

	public function create(){
        //initial
        $dt = $this->__init();
        $data = array();
        $data['complain'] = new stdClass();
        $data['chat_room_id'] = '0';
        $data['chat'] = array();

        //log
        // if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/buyer/complain::create -> Executed");
        // if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/buyer/complain::create -> POST: ".json_encode($_POST));

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if(empty($nation_code)){
          $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
                // if($this->is_log) $this->seme_log->write("api_mobile", 'API_Mobile/buyer/complain::create -> forceClose: '.$this->status.' '.$this->message);
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
          die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if(!$c){
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            // if($this->is_log) $this->seme_log->write("api_mobile", 'API_Mobile/buyer/complain::create -> forceClose: '.$this->status.' '.$this->message);
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if(!isset($pelanggan->id)){
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            // if($this->is_log) $this->seme_log->write("api_mobile", 'API_Mobile/buyer/complain::create -> forceClose: '.$this->status.' '.$this->message);
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
            die();
        }

        $d_order_id = (int) $this->input->post("d_order_id");
        if($d_order_id<=0){
            $this->status = 8520;
            $this->message = 'Invalid Order ID';
            // if($this->is_log) $this->seme_log->write("api_mobile", 'API_Mobile/buyer/complain::create -> forceClose: '.$this->status.' '.$this->message);
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
            die();
        }

        $d_order_detail_id = (int) $this->input->post("c_produk_id");
        if($d_order_detail_id<=0){
            $d_order_detail_id = (int) $this->input->post("d_order_detail_id");
            if($d_order_detail_id<=0){
                $this->status = 8522;
                $this->message = 'Invalid C Produk ID';
                // if($this->is_log) $this->seme_log->write("api_mobile", 'API_Mobile/buyer/complain::create -> forceClose: '.$this->status.' '.$this->message);
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
                die();
            }
        }

        //get d_order_detail_id
        $d_order_detail_item_id = (int) $this->input->post("d_order_detail_item_id");
        if($d_order_detail_id<=0){
            $this->status = 8406;
            $this->message = 'Invalid d_order_detail_item_id';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
            die();
        }


        $detail = $this->dodm->getById($nation_code,$d_order_id,$d_order_detail_id);
        if(!isset($detail->b_user_id)){
                $this->status = 8525;
                $this->message = 'Order Detail with supplied ID not found';
                // if($this->is_log) $this->seme_log->write("api_mobile", 'API_Mobile/buyer/Complain::create -> forceClose '.$this->status.' - '.$this->message);
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
                die();
        }

        //assign b_user_id
        $b_user_id_buyer = $pelanggan->id;
        $b_user_id_seller = $detail->b_user_id;

        //check complain if already exists
        // $check = $this->ecpl->check($nation_code,$d_order_id,$d_order_detail_id,$b_user_id_seller,$b_user_id_buyer);

        //collect input
        $alasan = strip_tags($this->input->post("alasan"));

        if(strlen($alasan)<=0){
            $this->status = 8510;
            $this->message = 'Reason is required for creating complain';
            // if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/buyer/Complain::create -> forceClose ".$this->status." - ".$this->message);
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
            die();
        }

        //trans start
        $this->chat->trans_start();

        //By Donny Dennison - 3 august 2020 - 15:22
        //Add 2 chat party, admin to seller / admin to buyer
        $chat_type = "buyandsell";

        //message sanitize
        $message = str_replace('"','',trim($alasan));
        $message = str_replace('\r\n','<br>',$message);
        $message = str_replace('\n','<br>',$message);

        $checkRoomChat = $this->ecpm->getRoomChatIDByParticipantId($nation_code, $b_user_id_buyer, $b_user_id_seller, $chat_type);
        if (!isset($checkRoomChat->nation_code)) {
            
            // $chat_room_id = $this->ecrm->getLastId($nation_code);

            $endDoWhile = 0;
            do{

                $chat_room_id = $this->GUIDv4();

                $checkId = $this->ecrm->checkId($nation_code, $chat_room_id);

                if($checkId == 0){
                    $endDoWhile = 1;
                }

            }while($endDoWhile == 0);

            //insert room chat
            $di = array();
            $di['id'] = $chat_room_id;
            $di['nation_code'] = $nation_code;
            $di['b_user_id_starter'] = $b_user_id_buyer;
            $di['custom_name_1'] = '';
            $di['custom_name_2'] = '';
            $di['cdate'] = 'NOW()';
            $di['chat_type'] = $chat_type;
            $this->ecrm->set($di);
            $this->chat->trans_commit();

            //insert chat participant 1
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['e_chat_room_id'] = $chat_room_id;
            $di['b_user_id'] = $b_user_id_buyer;
            $di['cdate'] = 'NOW()';
            $di['is_read'] = 1;
            $this->ecpm->set($di);
            $this->chat->trans_commit();

            //insert chat participant 2
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['e_chat_room_id'] = $chat_room_id;
            $di['b_user_id'] = $b_user_id_seller;
            $di['cdate'] = 'NOW()';
            $di['is_read'] = 1;
            $this->ecpm->set($di);
            $this->chat->trans_commit();

            $roomChat = $this->ecrm->getChatRoomByID($nation_code, $chat_room_id, $chat_type);

        }else{
            $roomChat = $this->ecrm->getChatRoomByID($nation_code, $checkRoomChat->e_chat_room_id, $chat_type);
        }

        //put to chat
        $chat_id = $this->chat->getLastId($nation_code, $roomChat->id);
        $di = array();
        $di['id'] = $chat_id;
        $di['nation_code'] = $nation_code;
        $di['e_chat_room_id'] = $roomChat->id;
        $di['b_user_id'] = $b_user_id_buyer;
        $di['message'] = $message;
        $di['message_indonesia'] = $message;
        $di['cdate'] = "NOW()";

        $res = $this->chat->set($di);
        if($res){
            $this->chat->trans_commit();

            //set unread for admin
            $du = array();
            $du['is_read_admin'] = 0;

            $this->ecrm->update($nation_code, $roomChat->id, $du);
            $this->chat->trans_commit();

            //set unread for other chat participant
            $du = array();
            $du['is_read'] = 0;

            $this->ecpm->updateUnread($nation_code, $roomChat->id, $b_user_id_buyer, $du);
            $this->chat->trans_commit();

            //set unread in table e_chat_read
            $du = array();
            $du['nation_code'] = $nation_code;
            $du['b_user_id'] = $b_user_id_buyer;
            $du['e_chat_room_id'] = $roomChat->id;
            $du['e_chat_id'] = $chat_id;
            $du['is_read'] = 1;
            $du['cdate'] = "NOW()";

            $this->ecreadm->set($du);
            $this->chat->trans_commit();

            //set unread in table e_chat_read
            $du = array();
            $du['nation_code'] = $nation_code;
            $du['b_user_id'] = $b_user_id_seller;
            $du['e_chat_room_id'] = $roomChat->id;
            $du['e_chat_id'] = $chat_id;
            $du['cdate'] = "NOW()";

            $this->ecreadm->set($du);
            $this->chat->trans_commit();

            //insert into table complain
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['d_order_id'] = $d_order_id;
            $di['d_order_detail_id'] = $d_order_detail_id;
            $di['c_produk_id'] = $d_order_detail_item_id;
            $di['b_user_id_seller'] = $b_user_id_seller;
            $di['b_user_id_buyer'] = $b_user_id_buyer;
            $di['e_chat_room_id'] = $roomChat->id;
            $di['e_chat_id'] = $chat_id;
            $di['alasan'] = $alasan;
            $di['dari'] = 'buyer';
            $di['cdate'] = "NOW()";
            $res = $this->ecpl->set($di);
            if($res){
                $this->status = 200;
                $this->message = 'Success';
                $this->chat->trans_commit();

                //get missing data
                $sender = $this->bu->getById($nation_code, $b_user_id_buyer);
                $receiver = $this->bu->getById($nation_code, $b_user_id_seller);

                $type = 'chat';
                $anotid = 1;
                $replacer = array();
                $replacer['pelanggan_fnama'] = $sender->fnama;
                $classified = 'setting_notification_user';
                $code = 'U4';

                $receiverSettingNotif = $this->busm->getValue($nation_code, $b_user_id_seller, $classified, $code);

                if (!isset($receiverSettingNotif->setting_value)) {
                    $receiverSettingNotif->setting_value = 0;
                }

                //push notif
                if ($receiverSettingNotif->setting_value == 1 && $receiver->is_active == 1) {
                    
                    if (strlen($receiver->fcm_token)>50) {
                        $device = $receiver->device; //jenis device
                        $tokens = array($receiver->fcm_token); //device token
                        if($receiver->language_id == 2) {
                            $title = 'Obrolan Baru';
                            $message = "Anda memiliki pesan obrolan dari $sender->fnama";
                        } else {
                            $title = 'New Chat';
                            $message = "You have chat messages from $sender->fnama";
                        }
                        
                        $type = 'chat';
                        $image = 'media/pemberitahuan/chat.png';
                        $payload = new stdClass();
                        $payload->chat_room_id = (string) $roomChat->id;
                        $payload->user_id = $sender->id;
                        $payload->user_fnama = $sender->fnama;

                        // by Muhammad Sofi - 27 October 2021 10:12
                        // if user img & banner not exist or empty, change to default image
                        // $payload->user_image = $this->cdn_url($sender->image);
                        if(file_exists(SENEROOT.$sender->image) && $sender->image != 'media/user/default.png'){
                            $payload->user_image = $this->cdn_url($sender->image);
                        } else {
                            $payload->user_image = $this->cdn_url('media/user/default-profile-picture.png');
                        }
                        $payload->chat_type = $roomChat->chat_type;
                        $payload->custom_name_1 = $roomChat->custom_name_1;
                        $payload->custom_name_2 = $roomChat->custom_name_2;
                        // $payload->custom_image = $roomChat->custom_image;

                        $nw = $this->anot->get($nation_code, "push", $type, $anotid, $receiver->language_id);
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
                    }

                }

                $data['chat_room_id'] = $roomChat->id;
                // $data['chat'] = $this->__getChat($nation_code,$d_order_id,$d_order_detail_id, $chat_type);
            
            }else{
                $this->status = 7420;
                $this->message = 'Failed add complain to chat list';
                $this->chat->trans_rollback();
            }
        }else{
            $this->chat->trans_rollback();
            $this->status = 7419;
            $this->message = 'Failed to create order complain right now, please try again later';
        }
        
        $this->chat->trans_end();

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
    }

	// public function change(){
	// 	//initial
	// 	$dt = $this->__init();
	// 	$data = array();
	// 	$data['complain'] = new stdClass();
 //    $data['chat'] = array();

 //    //check nation_code
	// 	$nation_code = $this->input->get('nation_code');
	// 	$nation_code = $this->nation_check($nation_code);
 //    if(empty($nation_code)){
 //      $this->status = 101;
 //  		$this->message = 'Missing or invalid nation_code';
 //      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
 //      die();
 //    }

	// 	//check apikey
	// 	$apikey = $this->input->get('apikey');
	// 	$c = $this->apikey_check($apikey);
	// 	if(!$c){
	// 		$this->status = 400;
	// 		$this->message = 'Missing or invalid API key';
	// 		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
	// 		die();
	// 	}

	// 	//check apisess
	// 	$apisess = $this->input->get('apisess');
	// 	$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
	// 	if(!isset($pelanggan->id)){
	// 		$this->status = 401;
	// 		$this->message = 'Missing or invalid API session';
	// 		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
	// 		die();
	// 	}

	// 	$d_order_id = (int) $this->input->post("d_order_id");
	// 	if($d_order_id<=0){
	// 		$this->status = 8500;
	// 		$this->message = 'Invalid Order ID';
	// 		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
	// 		die();
	// 	}

	// 	$d_order_detail_id = (int) $this->input->post("d_order_detail_id");
	// 	if($d_order_detail_id<=0){
	// 		$this->status = 8500;
	// 		$this->message = 'Invalid d_order_detail_id';
	// 		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
	// 		die();
	// 	}

 //    $ordered_product = $this->dodm->getByIdFull($nation_code,$d_order_id,$d_order_detail_id);
 //    if(!isset($ordered_product->b_user_id_buyer)){
	// 		$this->status = 8505;
	// 		$this->message = 'Order with d_order_detail_id combination not found';
	// 		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
	// 		die();
 //    }
 //    if($ordered_product->b_user_id_buyer != $pelanggan->id){
	// 		$this->status = 8506;
	// 		$this->message = "Sorry This order doesn't belong to you.";
	// 		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
	// 		die();
 //    }

	// 	//assign b_user_id
	// 	$b_user_id_buyer = $pelanggan->id;
	// 	$b_user_id_seller = $ordered_product->b_user_id_seller;

 //    //check complain if already exists
	// 	$complain = $this->ecpl->getByOrderProdukId($nation_code,$d_order_id,$d_order_detail_id);
	// 	if(!isset($complain->dari)){
	// 		$this->status = 8510;
	// 		$this->message = 'This order not in complain yet';
	// 		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
	// 		die();
	// 	}
	// 	$data['complain'] = $complain;

 //    //collect input
	// 	$dari = "buyer";
 //    $alasan = strip_tags($this->input->post("alasan"));

	// 	//validate
	// 	if(strlen($alasan)<=0){
	// 		$this->status = 8511;
	// 		$this->message = 'Complain reason are required';
	// 		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
	// 		die();
	// 	}
	// 	if($complain->dari != $dari){
	// 		$this->status = 8512;
	// 		$this->message = 'You cannot change this complain';
	// 		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
	// 		die();
	// 	}

 //    //insert to table e_complain
 //    $du = array();
 //    $du['alasan'] = $alasan;
 //    $res = $this->ecpl->update($nation_code,$d_order_id,$d_order_detail_id,$du);
 //    if($res){
 //  		$this->status = 200;
 //  		$this->message = 'Success';
	// 		$data['complain'] = $this->ecpl->getByOrderProdukId($nation_code,$d_order_id,$d_order_detail_id);
 //    }else{
 //  		$this->status = 8519;
 //  		$this->message = 'Failed to change complain, please try again later';
 //    }
	// 	$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
	// }
	// public function detail($d_order_id,$d_order_detail_id,$d_order_detail_item_id){
	// 	//initial
	// 	$dt = $this->__init();
	// 	$data = array();
	// 	$data['complain'] = new stdClass();
 //    $data['chat'] = array();

 //    //check nation_code
	// 	$nation_code = $this->input->get('nation_code');
	// 	$nation_code = $this->nation_check($nation_code);
 //    if(empty($nation_code)){
 //      $this->status = 101;
 //  		$this->message = 'Missing or invalid nation_code';
 //      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
 //      die();
 //    }

	// 	//check apikey
	// 	$apikey = $this->input->get('apikey');
	// 	$c = $this->apikey_check($apikey);
	// 	if(!$c){
	// 		$this->status = 400;
	// 		$this->message = 'Missing or invalid API key';
	// 		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
	// 		die();
	// 	}

	// 	//check apisess
	// 	$apisess = $this->input->get('apisess');
	// 	$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
	// 	if(!isset($pelanggan->id)){
	// 		$this->status = 401;
	// 		$this->message = 'Missing or invalid API session';
	// 		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
	// 		die();
	// 	}

	// 	$d_order_id = (int) $d_order_id;
	// 	if($d_order_id<=0){
	// 		$this->status = 8400;
	// 		$this->message = 'Invalid Order ID';
	// 		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
	// 		die();
	// 	}

	// 	$order = $this->order->getById($nation_code,$d_order_id);
	// 	if(!isset($order->id)){
	// 		$this->status = 8401;
	// 		$this->message = 'Order with supplied ID not found';
	// 		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
	// 		die();
	// 	}
	// 	if($order->b_user_id != $pelanggan->id){
	// 		$this->status = 8402;
	// 		$this->message = 'Sorry this order ID not belong to you';
	// 		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
	// 		die();
	// 	}

 //    //get d_order_detail_id
	// 	$d_order_detail_id = (int) $d_order_detail_id;
	// 	if($d_order_detail_id<=0){
	// 		$this->status = 8404;
	// 		$this->message = 'Invalid Order ID';
	// 		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
	// 		die();
	// 	}
 //    $ordered_product = $this->dodm->getByIdFull($nation_code,$d_order_id,$d_order_detail_id);
 //    if(!isset($ordered_product->d_order_detail_id)){
	// 		$this->status = 8405;
	// 		$this->message = 'Order detail not found';
	// 		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
	// 		die();
 //    }

 //    //get d_order_detail_id
	// 	$d_order_detail_item_id = (int) $d_order_detail_item_id;
	// 	if($d_order_detail_id<=0){
	// 		$this->status = 8406;
	// 		$this->message = 'Invalid d_order_detail_item_id';
	// 		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
	// 		die();
	// 	}

 //    //get complain
 //    $complain = $this->ecpl->getByOrderProdukId($nation_code,$d_order_id,$d_order_detail_id);
 //    if(isset($complain->d_order_id)){
 //      $data['complain'] = $complain;
 //      $this->message = 'Success';
 //    }else{
 //      $this->message = 'This order does not have complain';
 //    }

	// 	$this->status = 200;
	// 	$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "complain");
	// }
	
}
