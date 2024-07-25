<?php
class Chat extends JI_Controller
{
    public $is_log = 1;

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->lib("seme_curl");
        $this->load("api_mobile/a_notification_model", 'anot');
        $this->load("api_mobile/b_user_model", 'bu');
        $this->load("api_mobile/b_user_alamat_model", "bua");
        $this->load("api_mobile/d_order_detail_model", 'dodm');
        $this->load("api_mobile/d_order_detail_item_model", 'dodim');
        // $this->load("api_mobile/d_pemberitahuan_model", 'dpem');
        $this->load("api_mobile/e_chat_model", 'chat');
        $this->load("api_mobile/e_chat_room_model", 'ecrm');
        $this->load("api_mobile/e_chat_participant_model", 'ecpm');
        $this->load("api_mobile/e_chat_read_model", 'ecreadm');
        $this->load("api_mobile/e_complain_model", 'complain');
        $this->load("api_mobile/e_chat_attachment_model", 'ecam');

        //by Donny Dennison - 19 october 2020 14:51
        //add user setting chat notif
        $this->load("api_mobile/b_user_setting_model", "busm");

        $this->load("api_mobile/c_produk_model", 'cpm');

        $this->load("api_mobile/c_community_model", 'ccomm');

        //by Donny Dennison - 25 july 2022 11:40
        //change point get rule for group chat community and upload video product
        $this->load("api_mobile/common_code_model", "ccm");
        $this->load("api_mobile/g_leaderboard_point_history_model", 'glphm');

        //by Donny Dennison - 7 november 2022 14:17
        //new feature, block community post or account
        $this->load("api_mobile/c_block_model", "cbm");
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

    private function __sortCol($sort_col, $tbl_as)
    {
        switch ($sort_col) {
          case 'cdate':
          $sort_col = "$tbl_as.cdate";
          break;
          
          default:
          $sort_col = "$tbl_as.cdate";
        }
        return $sort_col;
    }

    private function __sortDir($sort_dir)
    {
        $sort_dir = strtolower($sort_dir);
        if ($sort_dir == "asc") {
          $sort_dir = "ASC";
        } else {
          $sort_dir = "DESC";
        }
        return $sort_dir;
    }

    private function __page($page)
    {
        if (!is_int($page)) {
          $page = (int) $page;
        }
        if ($page<=0) {
          $page = 1;
        }
        return $page;
    }

    private function __pageSize($page_size)
    {
        $page_size = (int) $page_size;
        if ($page_size<=0) {
          $page_size = 10;
        }
        return $page_size;
    }

    private function __statusText($order, $detail)
    {
        $status_text = new stdClass();
        $status_text->seller = '';
        $status_text->buyer = '';
        $order->order_status = strtolower($order->order_status);
        if ($order->order_status == 'waiting_for_payment') {
            $status_text->seller = '-';
            $status_text->buyer = 'Waiting for Payment';
        } elseif ($order->order_status == 'forward_to_seller') {
            $detail->seller_status = strtolower($detail->seller_status);
            $detail->shipment_status = strtolower($detail->shipment_status);
            $detail->buyer_confirmed = strtolower($detail->buyer_confirmed);
            $detail->settlement_status = strtolower($detail->settlement_status);
            if ($detail->seller_status == 'unconfirmed') {
                $status_text->seller = 'Waiting for Confirmation';
                $status_text->buyer = 'Waiting for Confirmation';
            } elseif ($detail->seller_status == 'confirmed') {
                if ($detail->shipment_status == "process") {
                    $status_text->seller = 'In Process';
                    $status_text->buyer = 'In Process';

                //By Donny Dennison - 08-07-2020 16:16
                //Request by Mr Jackie, add new shipment status "courier fail"
                } elseif ($detail->shipment_status == "courier fail") {
                    $status_text->seller = 'Courier Fail';
                    $status_text->buyer = 'Courier Fail';

                } elseif ($detail->shipment_status == "delivered") {
                    $status_text->seller = 'Delivery in progress';
                    $status_text->buyer = 'Delivery in progress';
                } else {
                    $status_text->seller = 'Delivered';
                    $status_text->buyer = 'Delivered';
                    if ($detail->buyer_confirmed == 'confirmed') {
                        $status_text->seller = 'Finished';
                        $status_text->buyer = 'Finished';
                    }
                }
            } else {
                $status_text->buyer = 'Rejected';
                $status_text->seller = 'Rejected';
                if ($detail->settlement_status == 'completed') {
                    //$status_text->seller = 'Refund (Paid)';
                    //$status_text->buyer = 'Refund (Paid)';
                } elseif ($detail->settlement_status == 'processing') {
                    //$status_text->seller = 'Refund (On Process)';
                    //$status_text->buyer = 'Refund (On Process)';
                }
            }
        } elseif ($order->order_status == 'cancelled') {
            $status_text->seller = 'Order Cancelled';
            $status_text->buyer = 'Order Cancelled';
        } elseif ($order->order_status == 'expired') {
            $status_text->buyer = 'Order Expired';
            $status_text->seller = 'Order Expired';
        } else {
            $status_text->seller = 'Unknown';
            $status_text->buyer = 'Unknown';
        }
        return $status_text->buyer;
    }

    //START by Donny Dennison - 12 july 2022 14:56
    //new offer system
    private function __changeOfferMessage($type, $message, $language_id=1)
    {

        if($language_id == 2){
            // by Muhammad Sofi 2 Agustus 2022 | change response message to indo language
            if($type == "offering"){
                $message = "Tawaran Dibuat<br>".$message;
            }else if($type == "cancelled"){
                $message = "Tawaran Dibatalkan<br>".$message;
            }else if($type == "rejected"){
                $message = "Tawaran Ditolak<br>".$message;
            }else if($type == "accepted"){
                $message = "Tawaran Diterima<br>".$message;
            }

        }else{

            if($type == "offering"){
                $message = "Made an Offer<br>".$message;
            }else if($type == "cancelled"){
                $message = "Cancelled Offer<br>".$message;
            }else if($type == "rejected"){
                $message = "Rejected Offer<br>".$message;
            }else if($type == "accepted"){
                $message = "Accepted Offer<br>".$message;
            }

        }

        return $message;

    }
    //END by Donny Dennison - 12 july 2022 14:56

    public function index()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['chat_room_total'] = 0;
        $data['chat_room'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        $sort_col = $this->input->get("sort_col");
        $sort_dir = $this->input->get("sort_dir");
        $page = $this->input->get("page");
        $page_size = $this->input->get("page_size");
        $chat_type = $this->input->get("chat_type");
        $timezone = $this->input->get("timezone");

        if(empty($chat_type)){
            $chat_type = '';
        }

        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        $tbl_as = $this->ecrm->getTblAs();
        $sort_col = $this->__sortCol($sort_col, $tbl_as);
        $sort_dir = $this->__sortDir($sort_dir);
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);

        //get room total
        $data['chat_room_total'] = $this->ecrm->countRoomChatByUserID($nation_code, $pelanggan->id, $chat_type);

        //get room
        $data['chat_room'] = $this->ecrm->getRoomChatByUserID($nation_code, $pelanggan->id, $page, $page_size, $sort_col, $sort_dir, $chat_type);

        foreach ($data['chat_room'] as &$cr) {

            $cr->custom_name_1 = html_entity_decode($cr->custom_name_1,ENT_QUOTES);

            // $cr->cdate_text = $this->humanTiming($cr->cdate_for_order_by);
            $cr->cdate_text = $this->humanTiming($cr->cdate_for_order_by, null, $pelanggan->language_id);

            $cr->cdate_for_order_by = $this->customTimezone($cr->cdate_for_order_by, $timezone);

            $cr->participant_list = $this->ecpm->getParticipantByRoomChatId($nation_code,$cr->id);

            $cr->participant_total = count($cr->participant_list);

            foreach($cr->participant_list as &$participantList){

                if($cr->chat_type == 'admin'){
                    $cr->custom_name_2 = 'SellOn Support';
                    $cr->is_admin = 1;

                    if(isset($participantList->b_user_image) && file_exists(SENEROOT.$participantList->b_user_image) && $participantList->b_user_image != 'media/user/default.png'){
                        $cr->custom_image = $this->cdn_url($participantList->b_user_image);
                    } else {
                        $cr->custom_image = $this->cdn_url('media/user/default-profile-picture.png');
                    }

                }else if($participantList->b_user_id != $pelanggan->id && $cr->chat_type != 'community'){
                    $cr->custom_name_2 = $participantList->b_user_fnama;
                    $cr->is_admin = $participantList->is_admin;

                    if(isset($participantList->b_user_image) && file_exists(SENEROOT.$participantList->b_user_image) && $participantList->b_user_image != 'media/user/default.png'){
                        $cr->custom_image = $this->cdn_url($participantList->b_user_image);
                    } else {
                        $cr->custom_image = $this->cdn_url('media/user/default-profile-picture.png');
                    }

                }else if($cr->chat_type == 'community'){
                    $cr->custom_name_2 = $cr->b_user_nama_starter;

                    if(isset($cr->b_user_image_starter) && file_exists(SENEROOT.$cr->b_user_image_starter) && $cr->b_user_image_starter != 'media/user/default.png'){
                        $cr->custom_image = $this->cdn_url($cr->b_user_image_starter);
                    } else {
                        $cr->custom_image = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                }

            }
            unset($participantList);

            if (isset($cr->b_user_image_starter)) {

                if(file_exists(SENEROOT.$cr->b_user_image_starter) && $cr->b_user_image_starter != 'media/user/default.png'){
                    $cr->b_user_image_starter = $this->cdn_url($cr->b_user_image_starter);
                } else {
                    $cr->b_user_image_starter = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }

            $lastChat = $this->chat->getLastChatByChatRoomId($nation_code,$cr->id, $cr->last_delete_chat, $pelanggan->language_id);

            $cr->last_chat = new stdClass();

            if(isset($lastChat->id)){

                //by Donny Dennison - 12 july 2022 14:56
                //new offer system
                $lastChat->message = $this->__changeOfferMessage($lastChat->type, $lastChat->message, $pelanggan->language_id);

                $lastChat->message = html_entity_decode($lastChat->message,ENT_QUOTES);

                if (isset($lastChat->a_pengguna_foto)) {
                    $lastChat->a_pengguna_foto = $this->cdn_url($lastChat->a_pengguna_foto);
                }
                if (isset($lastChat->b_user_image)) {

                    // by Muhammad Sofi - 27 October 2021 10:12
                    // if user img & banner not exist or empty, change to default image
                    // $lastChat->b_user_image = $this->cdn_url($lastChat->b_user_image);
                    if(file_exists(SENEROOT.$lastChat->b_user_image) && $lastChat->b_user_image != 'media/user/default.png'){
                        $lastChat->b_user_image = $this->cdn_url($lastChat->b_user_image);
                    } else {
                        $lastChat->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                }

            }
            
            $cr->last_chat = $lastChat;

        }

        //default output
        $this->status = 200;
        $this->message = 'Success';

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    }

    public function new_chat_list()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['chat_room_total'] = 0;
        $data['chat_room'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        $sort_col = $this->input->get("sort_col");
        $sort_dir = $this->input->get("sort_dir");
        // $page = $this->input->get("page");
        // $page_size = $this->input->get("page_size");
        $chat_type = $this->input->get("chat_type");
        $timezone = $this->input->get("timezone");
        $datetime_last_call = $this->input->get("datetime_last_call");

        if(empty($chat_type)){
            $chat_type = '';
        }

        if (empty($datetime_last_call)) {
            $this->status = 402;
            $this->message = 'datetime_last_call is required';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }
        
        $datetime_last_call_server = $this->customTimezoneFrom($datetime_last_call, $timezone);

        $tbl_as = $this->ecrm->getTblAs();
        $sort_col = $this->__sortCol($sort_col, $tbl_as);
        $sort_dir = $this->__sortDir($sort_dir);

        //get room total
        $data['chat_room_total'] = $this->ecrm->countRoomChatByUserID($nation_code, $pelanggan->id, $chat_type, $datetime_last_call_server);

        //get room
        $data['chat_room'] = $this->ecrm->getRoomChatByUserID($nation_code, $pelanggan->id, 0, 0, $sort_col, $sort_dir, $chat_type, $datetime_last_call_server);

        foreach ($data['chat_room'] as &$cr) {

            $cr->custom_name_1 = html_entity_decode($cr->custom_name_1,ENT_QUOTES);

            // $cr->cdate_text = $this->humanTiming($cr->cdate_for_order_by);
            $cr->cdate_text = $this->humanTiming($cr->cdate_for_order_by, null, $pelanggan->language_id);

            $cr->cdate_for_order_by = $this->customTimezone($cr->cdate_for_order_by, $timezone);

            $cr->participant_list = $this->ecpm->getParticipantByRoomChatId($nation_code,$cr->id);
            
            $cr->participant_total = count($cr->participant_list);

            foreach($cr->participant_list as &$participantList){

                if($cr->chat_type == 'admin'){
                    $cr->custom_name_2 = 'SellOn Support';
                    $cr->is_admin = 1;
                    
                    if(isset($participantList->b_user_image) && file_exists(SENEROOT.$participantList->b_user_image) && $participantList->b_user_image != 'media/user/default.png'){
                        $cr->custom_image = $this->cdn_url($participantList->b_user_image);
                    } else {
                        $cr->custom_image = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                    
                }else if($participantList->b_user_id != $pelanggan->id && $cr->chat_type != 'community'){
                    $cr->custom_name_2 = $participantList->b_user_fnama;
                    $cr->is_admin = $participantList->is_admin;

                    if(isset($participantList->b_user_image) && file_exists(SENEROOT.$participantList->b_user_image) && $participantList->b_user_image != 'media/user/default.png'){
                        $cr->custom_image = $this->cdn_url($participantList->b_user_image);
                    } else {
                        $cr->custom_image = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                    
                }else if($cr->chat_type == 'community'){
                    $cr->custom_name_2 = $cr->b_user_nama_starter;

                    if(isset($cr->b_user_image_starter) && file_exists(SENEROOT.$cr->b_user_image_starter) && $cr->b_user_image_starter != 'media/user/default.png'){
                        $cr->custom_image = $this->cdn_url($cr->b_user_image_starter);
                    } else {
                        $cr->custom_image = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                }

            }
            unset($participantList);

            if (isset($cr->b_user_image_starter)) {

                if(file_exists(SENEROOT.$cr->b_user_image_starter) && $cr->b_user_image_starter != 'media/user/default.png'){
                    $cr->b_user_image_starter = $this->cdn_url($cr->b_user_image_starter);
                } else {
                    $cr->b_user_image_starter = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }

            $lastChat = $this->chat->getLastChatByChatRoomId($nation_code,$cr->id, $cr->last_delete_chat, $pelanggan->language_id);

            $cr->last_chat = new stdClass();

            if(isset($lastChat->id)){

                //by Donny Dennison - 12 july 2022 14:56
                //new offer system
                $lastChat->message = $this->__changeOfferMessage($lastChat->type, $lastChat->message, $pelanggan->language_id);

                $lastChat->message = html_entity_decode($lastChat->message,ENT_QUOTES);

                if (isset($lastChat->a_pengguna_foto)) {
                    $lastChat->a_pengguna_foto = $this->cdn_url($lastChat->a_pengguna_foto);
                }
                if (isset($lastChat->b_user_image)) {

                    // by Muhammad Sofi - 27 October 2021 10:12
                    // if user img & banner not exist or empty, change to default image
                    // $lastChat->b_user_image = $this->cdn_url($lastChat->b_user_image);
                    if(file_exists(SENEROOT.$lastChat->b_user_image) && $lastChat->b_user_image != 'media/user/default.png'){
                        $lastChat->b_user_image = $this->cdn_url($lastChat->b_user_image);
                    } else {
                        $lastChat->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                }

            }
            
            $cr->last_chat = $lastChat;

        }

        //default output
        $this->status = 200;
        $this->message = 'Success';

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    }

    public function detail($chat_room_id="0", $chat_type = 'private')
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['chat_room_id'] = '';
        $data['chat_room'] = new stdClass();
        $data['participant_list'] = array();
        $data['participant_total'] = 0;
        $data['chat_total'] = 0;
        $data['chat'] = array();
        $data['is_blocked'] = '0';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //cek chat_room_id
        $data['chat_room_id'] = $chat_room_id;
        $data['chat_room'] = $this->ecrm->getChatRoomByID($nation_code, $chat_room_id, $chat_type);
        if ($chat_room_id<='0' || !isset($data['chat_room']->id)) {
            $this->status = 7280;
            $this->message = 'Invalid Chat Room ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        $data['chat_room']->custom_name_1 = html_entity_decode($data['chat_room']->custom_name_1,ENT_QUOTES);

        if (isset($data['chat_room']->b_user_image_starter)) {

            if(file_exists(SENEROOT.$data['chat_room']->b_user_image_starter) && $data['chat_room']->b_user_image_starter != 'media/user/default.png'){
                $data['chat_room']->b_user_image_starter = $this->cdn_url($data['chat_room']->b_user_image_starter);
            } else {
                $data['chat_room']->b_user_image_starter = $this->cdn_url('media/user/default-profile-picture.png');
            }

        }

        //by Donny Dennison - 12 july 2022 14:56
        //new offer system
        $data['chat_room']->buyer_or_seller = "";
        $data['chat_room']->c_produk_stok = "0";
        $data['chat_room']->last_offer_price = "0";

        if($data['chat_room']->chat_type == "offer"){

            $getProductType = $this->cpm->getProductType($nation_code, $data['chat_room']->c_produk_id);
            $getProductType = $getProductType->product_type;

            $produk = $this->cpm->getByIdIgnoreActive($nation_code, $data['chat_room']->c_produk_id, $pelanggan, $getProductType);

            if($pelanggan->id == $produk->b_user_id_seller){
                $data['chat_room']->buyer_or_seller = "seller";
            }else{
                $data['chat_room']->buyer_or_seller = "buyer";
            }

            $data['chat_room']->c_produk_stok = $produk->stok;

            $data['chat_room']->c_produk_nama = html_entity_decode($data['chat_room']->c_produk_nama,ENT_QUOTES);

            if(file_exists(SENEROOT.$data['chat_room']->c_produk_thumb) && $data['chat_room']->c_produk_thumb != 'media/user/default.png'){
                $data['chat_room']->c_produk_thumb = $this->cdn_url($data['chat_room']->c_produk_thumb);
            } else {
                $data['chat_room']->c_produk_thumb = $this->cdn_url('media/produk/default.png');
            }

            $data['chat_room']->last_offer_price = $this->chat->getLastOfferByChatRoomId($nation_code, $chat_room_id, "offering");
            if(isset($data['chat_room']->last_offer_price->message)){
                $data['chat_room']->last_offer_price = $data['chat_room']->last_offer_price->message;
            }else{
                $data['chat_room']->last_offer_price = "0";
            }

        }
        //END by Donny Dennison - 12 july 2022 14:56

        $sort_col = $this->input->get("sort_col");
        $sort_dir = $this->input->get("sort_dir");
        $page = $this->input->get("page");
        $page_size = $this->input->get("page_size");
        $timezone = $this->input->get("timezone");

        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        $tbl_as = $this->chat->getTblAs();
        // $tbl2_as = $this->chat->getTbl2As();
        $sort_col = $this->__sortCol($sort_col, $tbl_as);
        $sort_dir = $this->__sortDir($sort_dir);
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);

        // if (isset($data['chat_room']->message)) {
        //   $data['chat_room']->message = $this->__dconv($data['chat_room']->message);
        // }

        $data['participant_list'] = $this->ecpm->getParticipantByRoomChatId($nation_code,$chat_room_id);

        //check already in group chat or not
        $checkStillParticipant = 0;
        foreach($data['participant_list'] as $participant){

            if($participant->b_user_id == $pelanggan->id){
                $checkStillParticipant = 1;
                break;
            }

        }
        unset($participant);

        if ($checkStillParticipant == 0) {
            $this->status = 8104;
            $this->message = 'You can no longer chat as you exited this chat group';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        $data['participant_total'] = count($data['participant_list']);

        $last_delete_chat = '';
        $id_lawan_bicara = 0;
        foreach($data['participant_list'] as &$participantList){

            if($participantList->b_user_id == $pelanggan->id){
                $last_delete_chat = $participantList->last_delete_chat;
            }else{
                $id_lawan_bicara = $participantList->b_user_id;
            }

            if(isset($participantList->b_user_image) && file_exists(SENEROOT.$participantList->b_user_image) && $participantList->b_user_image != 'media/user/default.png'){
                $participantList->b_user_image = $this->cdn_url($participantList->b_user_image);
            } else {
                $participantList->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
            }

            if($data['chat_room']->chat_type == 'admin'){
                $data['chat_room']->custom_name_2 = 'SellOn Support';
                $data['chat_room']->is_admin = 1;

                $data['chat_room']->custom_image = $participantList->b_user_image;
            }else if($participantList->b_user_id != $pelanggan->id && $data['chat_room']->chat_type != 'community'){
                $data['chat_room']->custom_name_2 = $participantList->b_user_fnama;
                $data['chat_room']->is_admin = $participantList->is_admin;

                $data['chat_room']->custom_image = $participantList->b_user_image;
            }else if($data['chat_room']->chat_type == 'community'){
                $data['chat_room']->custom_name_2 = $data['chat_room']->b_user_nama_starter;

                $data['chat_room']->custom_image = $data['chat_room']->b_user_image_starter;
            }

        }
        unset($participantList);

        //START by Donny Dennison - 7 november 2022 14:17
        //new feature, block community post or account
        if($pelanggan->id != $data['chat_room']->b_user_id_starter){

            if($data['chat_room']->chat_type == 'community'){

                $blockDataAccount = $this->cbm->getById($nation_code, 0, $data['chat_room']->b_user_id_starter, "account", $pelanggan->id);

                if(isset($blockDataAccount->block_id)){

                    $data['is_blocked'] = '1';

                }

            }

        }

        if($data['chat_room']->chat_type != 'community' && $data['chat_room']->chat_type != 'admin'){

            $blockDataAccount = $this->cbm->getById($nation_code, 0, $id_lawan_bicara, "account", $pelanggan->id);
            $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $id_lawan_bicara);

            if(isset($blockDataAccount->block_id) || isset($blockDataAccountReverse->block_id)){

                $data['is_blocked'] = '1';

            }

        }

        if($data['chat_room']->chat_type == 'community'){

            if(isset($blockDataAccount->block_id)){

                if($last_delete_chat < $blockDataAccount->cdate){

                    $last_delete_chat = $blockDataAccount->cdate;

                }

            }

        }
        //END by Donny Dennison - 7 november 2022 14:17
        //new feature, block community post or account

        $data['chat_total'] = $this->chat->countAllByChatRoomId($nation_code, $chat_room_id, $last_delete_chat);

        $data['chat'] = $this->chat->getAllByChatRoomId($nation_code, $chat_room_id, $last_delete_chat, $page, $page_size, $sort_col, $sort_dir, $pelanggan->language_id);

        $e_chat_ids = array();
        foreach($data['chat'] AS $chat){
            $e_chat_ids[] = $chat->chat_id;
        }
        unset($chat);

        //get complains
        $complains = array();
        if($e_chat_ids){
            $cn = $this->complain->getDetailByChatRoomID($nation_code, $chat_room_id, $e_chat_ids);
            if (count($cn)) {
                foreach ($cn as $c) {
                    // $c->c_produk_nama = $this->__dconv($c->c_produk_nama);
                    $c->c_produk_nama = html_entity_decode($c->c_produk_nama,ENT_QUOTES);

                    // START by Muhammad Sofi - 27 October 2021 10:12
                    // if user img & banner not exist or empty, change to default image
                    // $c->b_user_image_seller = $this->cdn_url($c->b_user_image_seller);
                    if(file_exists(SENEROOT.$c->b_user_image_seller) && $c->b_user_image_seller != 'media/user/default.png'){
                        $c->b_user_image_seller = $this->cdn_url($c->b_user_image_seller);
                    } else {
                        $c->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                    // $c->b_user_image_buyer = $this->cdn_url($c->b_user_image_buyer);
                    if(file_exists(SENEROOT.$c->b_user_image_buyer) && $c->b_user_image_buyer != 'media/user/default.png'){
                        $c->b_user_image_buyer = $this->cdn_url($c->b_user_image_buyer);
                    } else {
                        $c->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                    // END by Muhammad Sofi - 27 October 2021 10:12

                    $c->c_produk_foto = $this->cdn_url($c->c_produk_foto);
                    $c->c_produk_thumb = $this->cdn_url($c->c_produk_thumb);

                    $c->status_text = $this->__statusText($c, $c);

                    $key = $nation_code.'-'.$chat_room_id.'-'.$c->e_chat_id;
                    $complains[$key] = $c;
                }
            }
            unset($c,$cn); //free up some memory
        }

        //get attachment 
        $att = array();
        if($e_chat_ids){
            $attachments = $this->ecam->getDetailByChatRoomID($nation_code, $chat_room_id, $e_chat_ids);
            foreach ($attachments as $at) {
                $key = $nation_code.'-'.$chat_room_id.'-'.$at->e_chat_id;
                
                $at->order_invoice_code = '';
                $at->order_thumb = '';
                $at->order_user_id_seller = '';
                $at->status_text = '';

                if($at->jenis == 'product' || $at->jenis == 'barter_request' || $at->jenis == 'barter_exchange'){    

                    $at->produk_thumb = $this->cdn_url($at->produk_thumb);
                    // $at->produk_nama = $this->__dconv($at->produk_nama);
                    $at->produk_nama = html_entity_decode($at->produk_nama,ENT_QUOTES);

                }else if($at->jenis == 'order'){

                    $produk = $this->dodm->getByIdForChat($nation_code, $at->url, $at->order_detail_id);
                    $item = $this->dodim->getById($nation_code, $at->url, $at->order_detail_id, $at->order_detail_item_id);

                    if (isset($produk->c_produk_id)) {

                        $at->order_invoice_code = $produk->invoice_code;
                        $at->order_thumb = $this->cdn_url($item->thumb);
                        $at->order_user_id_seller = $produk->b_user_id_seller;
                        $at->status_text = $this->__statusText($produk, $produk);

                    }

                }else{

                    $at->url = $this->cdn_url($at->url);

                }

                //put to array key
                if (!isset($att[$key])) {
                    $att[$key] = array();
                }
                $att[$key][] = $at;
            }
            unset($at); //free some memory
            unset($attachments); //free some memory
        }

        //chat iteration
        foreach ($data['chat'] as &$ch) {

            //by Donny Dennison - 12 july 2022 14:56
            //new offer system
            $ch->message = $this->__changeOfferMessage($ch->type, $ch->message, $pelanggan->language_id);

            $ch->message = html_entity_decode($ch->message,ENT_QUOTES);

            if (isset($ch->b_user_image)) {
                // by Muhammad Sofi - 27 October 2021 10:12
                // if user img & banner not exist or empty, change to default image
                // $ch->b_user_image = $this->cdn_url($ch->b_user_image);
                if(file_exists(SENEROOT.$ch->b_user_image) && $ch->b_user_image != 'media/user/default.png'){
                    $ch->b_user_image = $this->cdn_url($ch->b_user_image);
                } else {
                    $ch->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }
            if (isset($ch->a_pengguna_foto)) {
                $ch->a_pengguna_foto = $this->cdn_url($ch->a_pengguna_foto);
            }

            // $ch->cdate_text = $this->humanTiming($ch->cdate);
            $ch->cdate_text = $this->humanTiming($ch->cdate, null, $pelanggan->language_id);

            $ch->cdate = $this->customTimezone($ch->cdate, $timezone);

            //fill attachments
            // $ch->attachments = new stdClass();
            $ch->attachments = array();
            $key = $nation_code.'-'.$chat_room_id.'-'.$ch->chat_id;
            if (isset($att[$key])) {
                // $ch->attachments = $att[$key][0];
                $ch->attachments = $att[$key];
            }

            //fill complain
            $ch->complain = new stdClass();
            $key = $nation_code.'-'.$chat_room_id.'-'.$ch->chat_id;
            if (isset($complains[$key])) {
                $ch->complain = $complains[$key];
            }

            if($data['chat_room']->chat_type == 'admin'){
                
                $ch->is_read_lawan_bicara = $data['chat_room']->is_read_admin;

            }else{
                
                $ch->is_read_lawan_bicara = $this->ecreadm->checkReadByLawanBicara($nation_code, $chat_room_id, $ch->chat_id, $pelanggan->id);
            }

        }

        $this->ecpm->setAsRead($nation_code, $chat_room_id, $pelanggan->id);

        $this->ecreadm->setAsRead($nation_code, $chat_room_id, $pelanggan->id);

        //render
        $this->status = 200;
        $this->message = 'Success';
        //render
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    }

    public function join()
    {

        //default result
        $data = array();
        $data['chat_room_id'] = $this->input->get('chat_room_id');
        $data['chat_room'] = new stdClass();
        $data['participant_list'] = array();
        $data['participant_total'] = 0;
        $data['is_join'] = '0';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //cek chat_room_id
        $chat_room_id = $this->input->get('chat_room_id');
        $data['chat_room_id'] = $chat_room_id;
        $data['chat_room'] = $this->ecrm->getChatRoomByID($nation_code, $chat_room_id);
        if ($chat_room_id<='0' || !isset($data['chat_room']->id)) {
            $this->status = 7280;
            $this->message = 'Invalid Chat Room ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        $data['chat_room']->custom_name_1 = html_entity_decode($data['chat_room']->custom_name_1,ENT_QUOTES);

        if (isset($data['chat_room']->b_user_image_starter)) {
            // by Muhammad Sofi - 27 October 2021 10:12
            // if user img & banner not exist or empty, change to default image
            // $data['chat_room']->b_user_image_starter = $this->cdn_url($data['chat_room']->b_user_image_starter);
            if(file_exists(SENEROOT.$data['chat_room']->b_user_image_starter) && $data['chat_room']->b_user_image_starter != 'media/user/default.png'){
                $data['chat_room']->b_user_image_starter = $this->cdn_url($data['chat_room']->b_user_image_starter);
            } else {
                $data['chat_room']->b_user_image_starter = $this->cdn_url('media/user/default-profile-picture.png');
            }
        }

        $data['participant_list'] = $this->ecpm->getParticipantByRoomChatId($nation_code,$chat_room_id);

        if($data['chat_room']->c_community_id == '0' && $data['chat_room']->chat_type != 'community'){
            $this->status = 7280;
            $this->message = 'Invalid Chat Room ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //check already in group chat or not
        $alreadyInGroupChat = 0;
        foreach($data['participant_list'] as $participantList){

            if($participantList->b_user_id == $pelanggan->id){
                $alreadyInGroupChat = 1;
                break;
            }

        }
        unset($participantList);

        //START by Donny Dennison - 7 november 2022 14:17
        //new feature, block community post or account
        if($alreadyInGroupChat == 0 && $pelanggan->id != $data['chat_room']->b_user_id_starter){

            $blockDataCommunity = $this->cbm->getById($nation_code, 0, $pelanggan->id, "community", $data['chat_room']->c_community_id);
            $blockDataAccount = $this->cbm->getById($nation_code, 0, $data['chat_room']->b_user_id_starter, "account", $pelanggan->id);
            $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $data['chat_room']->b_user_id_starter);

            if(isset($blockDataCommunity->block_id) || isset($blockDataAccount->block_id) || isset($blockDataAccountReverse->block_id)){

                $this->status = 1005;
                $this->message = "You can no longer chat as you're blocked";
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat_blocked_general");
                die();

            }

        }
        //END by Donny Dennison - 7 november 2022 14:17
        //new feature, block community post or account

        $community = $this->ccomm->getById($nation_code, $data['chat_room']->c_community_id, $pelanggan);

        if ($alreadyInGroupChat == 0 && count($data['participant_list']) >= $community->max_people_group_chat) {
            if($community->max_people_group_chat == 50){
                $this->status = 8103;
            }else{
                $this->status = 8108;
            }

            $this->message = 'Cannot join the group chat anymore because already past the limit ('.$community->max_people_group_chat.')';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        // $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

        // if(isset($pelangganAddress->alamat2)){

        //   if($community->postal_district == $pelangganAddress->postal_district){
            
        //   }else{
        //     $this->status = 1099;
        //     $this->message = 'You\'re not allowed to join Group Chat outside your neighborhood';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
        //     die();
        //   }

        // }else{
        //   $this->status = 1099;
        //   $this->message = 'You\'re not allowed to join Group Chat outside your neighborhood';
        //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
        //   die();
        // }

        if($alreadyInGroupChat == 0){

            //insert chat participant
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['e_chat_room_id'] = $chat_room_id;
            $di['b_user_id'] = $pelanggan->id;
            $di['cdate'] = 'NOW()';
            $di['last_delete_chat'] = 'NOW()';
            $di['is_read'] = 1;
            $this->ecpm->set($di);

            // by Muhammad Sofi - 12 November 2021 16:32 
            // remove subquery get total_people_group_chat, update data on join and leave user
            $this->ccomm->updateTotal($nation_code, $data['chat_room']->c_community_id, "total_people_group_chat", '+', 1);

            //create announcement
            $type = 'chat';
            $replacer = array();

            $replacer['user_nama'] = html_entity_decode($pelanggan->fnama,ENT_QUOTES);
            $message = '';
            $message_indonesia = '';

            $nw = $this->anot->get($nation_code, "push", $type, 2, 1);
            if (isset($nw->message)) {
              $message = $this->__nRep($nw->message, $replacer);
            }

            $nw = $this->anot->get($nation_code, "push", $type, 2, 2);
            if (isset($nw->message)) {
              $message_indonesia = $this->__nRep($nw->message, $replacer);
            }

            //get last chat id
            $chat_id = $this->chat->getLastId($nation_code, $chat_room_id);

            $di = array();
            $di['id'] = $chat_id;
            $di['nation_code'] = $nation_code;
            $di['e_chat_room_id'] = $chat_room_id;
            $di['b_user_id'] = 0;
            $di['type'] = 'announcement';
            $di['message'] = $message;
            $di['message_indonesia'] = $message_indonesia;
            $di['cdate'] = date('Y-m-d H:i:s', strtotime('+ 1 second'));
            $this->chat->set($di);

            $data['participant_list'] = $this->ecpm->getParticipantByRoomChatId($nation_code,$chat_room_id);

            //set unread in table e_chat_read
            $insertArray = array();
            foreach($data['participant_list'] AS $participant){

                $du = array();
                $du['nation_code'] = $nation_code;
                $du['b_user_id'] = $participant->b_user_id;
                $du['e_chat_room_id'] = $chat_room_id;
                $du['e_chat_id'] = $chat_id;
                if($participant->b_user_id == $pelanggan->id){
                    $du['is_read'] = 1;
                }else{
                    $du['is_read'] = 0;
                }
                $du['cdate'] = "NOW()";
                $insertArray[] = $du;

            }
            unset($participant);

            $chunkInsertArray = array_chunk($insertArray,50);

            foreach($chunkInsertArray AS $chunk){

                //insert multi
                $this->ecreadm->setMass($chunk);

            }
            unset($insertArray, $chunkInsertArray, $chunk);

            foreach($data['participant_list'] as $participantList){

                if($data['chat_room']->chat_type == 'admin'){
                    $data['chat_room']->custom_name_2 = 'SellOn Support';
                    $data['chat_room']->is_admin = 1;

                    if(isset($participantList->b_user_image) && file_exists(SENEROOT.$participantList->b_user_image) && $participantList->b_user_image != 'media/user/default.png'){
                        $data['chat_room']->custom_image = $this->cdn_url($participantList->b_user_image);
                    } else {
                        $data['chat_room']->custom_image = $this->cdn_url('media/user/default-profile-picture.png');
                    }

                }else if($participantList->b_user_id != $pelanggan->id && $data['chat_room']->chat_type != 'community'){
                    $data['chat_room']->custom_name_2 = $participantList->b_user_fnama;
                    $data['chat_room']->is_admin = $participantList->is_admin;

                    if(isset($participantList->b_user_image) && file_exists(SENEROOT.$participantList->b_user_image) && $participantList->b_user_image != 'media/user/default.png'){
                        $data['chat_room']->custom_image = $this->cdn_url($participantList->b_user_image);
                    } else {
                        $data['chat_room']->custom_image = $this->cdn_url('media/user/default-profile-picture.png');
                    }

                }else if($data['chat_room']->chat_type == 'community'){
                    $data['chat_room']->custom_name_2 = $data['chat_room']->b_user_nama_starter;

                    if(isset($data['chat_room']->b_user_image_starter) && file_exists(SENEROOT.$data['chat_room']->b_user_image_starter) && $data['chat_room']->b_user_image_starter != 'media/user/default.png'){
                        $data['chat_room']->custom_image = $this->cdn_url($data['chat_room']->b_user_image_starter);
                    } else {
                        $data['chat_room']->custom_image = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                }

            }
            unset($participantList);

            //send push notif
            $ownerCommunity = $this->bu->getById($nation_code, $data['chat_room']->b_user_id_starter);
            $sender = $this->bu->getById($nation_code, $pelanggan->id);
            $ios = array();
            $android = array();

            foreach($data['participant_list'] as $participant){

                if($participant->b_user_id != $pelanggan->id){
                    $receiver = $this->bu->getById($nation_code, $participant->b_user_id);

                    $classified = 'setting_notification_user';
                    $code = 'U4';

                    $receiverSettingNotif = $this->busm->getValue($nation_code, $participant->b_user_id, $classified, $code);

                    if (!isset($receiverSettingNotif->setting_value)) {
                        $receiverSettingNotif->setting_value = 0;
                    }

                    //push notif
                    if ($receiverSettingNotif->setting_value == 1 && $receiver->is_active == 1) {

                        if (strtolower($receiver->device) == 'ios') {
                          $ios[] = $receiver->fcm_token;
                        }else{
                          $android[] = $receiver->fcm_token;
                        }

                    }
                }

            }
            unset($participant);

            $type = 'chat';
            $anotid = 2;
            $replacer = array();
            $replacer['user_nama'] = html_entity_decode($sender->fnama,ENT_QUOTES);

            $title = 'Obrolan Baru';
            $message = "$sender->fnama telah bergabung dengan obrolan grup";
            $image = 'media/pemberitahuan/chat.png';

            if (array_unique($ios)) {
                $device = "ios"; //jenis device
                $tokens = $ios; //device token
                $payload = new stdClass();
                $payload->chat_room_id = (string) $chat_room_id;
                $payload->user_id = $sender->id;
                $payload->user_fnama = $sender->fnama;

                // by Muhammad Sofi - 27 October 2021 10:12
                // if user img & banner not exist or empty, change to default image
                // $payload->user_image = $this->cdn_url($ownerCommunity->image);
                if(file_exists(SENEROOT.$ownerCommunity->image) && $ownerCommunity->image != 'media/user/default.png'){
                    $payload->user_image = $this->cdn_url($ownerCommunity->image);
                } else {
                    $payload->user_image = $this->cdn_url('media/user/default-profile-picture.png');
                }
                $payload->chat_type = $data['chat_room']->chat_type;
                $payload->custom_name_1 = $data['chat_room']->custom_name_1;
                $payload->custom_name_2 = $data['chat_room']->custom_name_2;
                // $payload->custom_image = $data['chat_room']->custom_image;

                $nw = $this->anot->get($nation_code, "push", $type, $anotid);
                if (isset($nw->message)) {
                    $message = $this->__nRep($nw->message, $replacer);
                }
                if (isset($nw->image)) {
                    $image = $nw->image;
                }
                $image = $this->cdn_url($image);
                $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
            }

            if (array_unique($android)) {
                $device = "android"; //jenis device
                $tokens = $android; //device token
                $payload = new stdClass();
                $payload->chat_room_id = (string) $chat_room_id;
                $payload->user_id = $sender->id;
                $payload->user_fnama = $sender->fnama;

                // by Muhammad Sofi - 27 October 2021 10:12
                // if user img & banner not exist or empty, change to default image
                // $payload->user_image = $this->cdn_url($ownerCommunity->image);
                if(file_exists(SENEROOT.$ownerCommunity->image) && $ownerCommunity->image != 'media/user/default.png'){
                    $payload->user_image = $this->cdn_url($ownerCommunity->image);
                } else {
                    $payload->user_image = $this->cdn_url('media/user/default-profile-picture.png');
                }
                $payload->chat_type = $data['chat_room']->chat_type;
                $payload->custom_name_1 = $data['chat_room']->custom_name_1;
                $payload->custom_name_2 = $data['chat_room']->custom_name_2;
                // $payload->custom_image = $data['chat_room']->custom_image;

                $nw = $this->anot->get($nation_code, "push", $type, $anotid);
                if (isset($nw->message)) {
                    $message = $this->__nRep($nw->message, $replacer);
                }
                if (isset($nw->image)) {
                    $image = $nw->image;
                }
                $image = $this->cdn_url($image);
                $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
            }

            //START by Donny Dennison - 13 october 2022 14:10
            //change point policy
            // $limitTotalParticipant = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EM");
            // if (!isset($limitTotalParticipant->remark)) {
            //   $limitTotalParticipant = new stdClass();
            //   $limitTotalParticipant->remark = 5;
            // }

            // //check already get point or not previously
            // $alreadyGetPointGroupChat = $this->glphm->countAll($nation_code, "", "", "", "", $ownerCommunity->id, "+", $data['chat_room']->c_community_id, "community", "more than", "", "");

            // if(count($data['participant_list']) >= $limitTotalParticipant->remark && $alreadyGetPointGroupChat == 0 && $ownerCommunity->is_active == 1){

            //     $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $ownerCommunity->id);

            //     //get point
            //     $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EN");
            //     if (!isset($pointGet->remark)) {
            //         $pointGet = new stdClass();
            //         $pointGet->remark = 50;
            //     }

            //     $di = array();
            //     $di['nation_code'] = $nation_code;
            //     $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
            //     $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
            //     $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
            //     $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
            //     $di['b_user_id'] = $ownerCommunity->id;
            //     $di['point'] = $pointGet->remark;
            //     $di['custom_id'] = $data['chat_room']->c_community_id;
            //     $di['custom_type'] = 'community';
            //     $di['custom_type_sub'] = 'more than';
            //     $di['custom_text'] = $ownerCommunity->fnama.' has '.$di['custom_type_sub'].' '.$limitTotalParticipant->remark.' '.$di['custom_type'].' group chat and get '.$di['point'].' point(s)';
          // $endDoWhile = 0;
          // do{
          //   $leaderBoardHistoryId = $this->GUIDv4();
          //   $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
          //   if($checkId == 0){
          //     $endDoWhile = 1;
          //   }
          // }while($endDoWhile == 0);
          // $di['id'] = $leaderBoardHistoryId;
            //     $this->glphm->set($di);
            //     // $this->glrm->updateTotal($nation_code, $ownerCommunity->id, 'total_point', '+', $di['point']);
            // }
            //END by Donny Dennison - 13 october 2022 14:10
            //change point policy
        }

        // $data['participant_total'] = count($data['participant_list']);

        unset($data['chat_room_id'], $data['chat_room'], $data['participant_list'], $data['participant_total']);

        $data['is_join'] = '1';

        //render
        $this->status = 200;
        $this->message = 'Success';
        //render
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    }

    public function checkparticipantstatus()
    {
        //default result
        $data = array();
        $data['chat_room_id'] = $this->input->get('chat_room_id');
        $data['chat_room'] = new stdClass();
        $data['participant_list'] = array();
        $data['is_leave'] = '0';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //cek chat_room_id
        $chat_room_id = $this->input->get('chat_room_id');
        $data['chat_room_id'] = $chat_room_id;
        $data['chat_room'] = $this->ecrm->getChatRoomByID($nation_code, $chat_room_id);
        if ($chat_room_id<='0' || !isset($data['chat_room']->id)) {
            $this->status = 7280;
            $this->message = 'Invalid Chat Room ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }
        
        $data['participant_list'] = $this->ecpm->getParticipantByRoomChatId($nation_code,$chat_room_id);

        //check already in group chat or not
        if($data['chat_room']->c_community_id > '0' && $data['chat_room']->chat_type == 'community'){

            $alreadyInGroupChat = 0;
            foreach($data['participant_list'] as $participantList){
                
                if($participantList->b_user_id == $pelanggan->id){
                    $alreadyInGroupChat = 1;
                    break;
                }

            }
            unset($participantList);

            if($alreadyInGroupChat == 0){
                $data['is_leave'] = '1';
            }

        }
        
        unset($data['chat_room_id'], $data['chat_room'], $data['participant_list']);

        //render
        $this->status = 200;
        // $this->message = 'You can no longer chat as you exited this chat group';
        $this->message = 'Success';
        //render
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    }

    /**
     * Send chat
     * @return mixed result API
     */
    public function send()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['chat'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //collect chat_room_id
        $chat_room_id = $this->input->post('chat_room_id');
        if (!$chat_room_id) {
            $chat_room_id = 0;
        }

        $chat_type = $this->input->post('chat_type');
        if (empty($chat_type)) {

            $chat_type = "private";
            
        }

        //collect b_user_id_to
        $b_user_id_to = $this->input->post('b_user_id_to');
        if ($b_user_id_to<='0' && ($chat_type == 'buyandsell' || $chat_type == 'private' || $chat_type == 'barter' || $chat_type == 'offer')) {
            $this->status = 8101;
            $this->message = 'Invalid B User ID To';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        if($chat_type == 'buyandsell' || $chat_type == 'private' || $chat_type == 'barter' || $chat_type == 'offer'){
            $checkUserActiveOrNot = $this->bu->getById($nation_code, $b_user_id_to);
            if($checkUserActiveOrNot->is_active == 0){
                $this->status = 8106;
                $this->message = 'Chat is unavailable now';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
                die();
            }
        }

        //START by Donny Dennison - 12 july 2022 14:56
        //new offer system
        $offer_product_id = $this->input->post('offer_product_id');
        if ($offer_product_id <= '0' && $chat_type == 'offer' && $chat_room_id == "0") {
            $this->status = 8107;
            $this->message = 'Invalid offer_product_id';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        if ($offer_product_id > '0' && $chat_type == 'offer') {

            $getProductType = $this->cpm->getProductType($nation_code, $offer_product_id);
            $getProductType = $getProductType->product_type;

            //get product detail
            $productOffered = $this->cpm->getByIdIgnoreActive($nation_code, $offer_product_id, $pelanggan, $getProductType);
            if(!isset($productOffered->id)){
              $this->status = 595;
              $this->message = 'Invalid product ID or Product not found';
              $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
              die();
            }

        }

        $offer_status = $this->input->post('offer_status');
        if(empty($offer_status)){
            $offer_status = "chat";
        }
        //END by Donny Dennison - 12 july 2022 14:56

        $timezone = $this->input->post("timezone");

        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        if($chat_room_id){

            $roomChat = $this->ecrm->getChatRoomByID($nation_code, $chat_room_id, $chat_type);
            if (!isset($roomChat->nation_code)) {
                $this->status = 7280;
                $this->message = 'Invalid Chat Room ID or Chat Room ID not registered';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
                die();
            }

            $roomChatparticipant = $this->ecpm->getParticipantByRoomChatId($nation_code, $chat_room_id);

        }else{

            if($chat_type == 'buyandsell' || $chat_type == 'private' || $chat_type == 'barter' || $chat_type == 'offer'){

                //START by Donny Dennison - 7 november 2022 14:17
                //new feature, block community post or account
                $blockDataAccount = $this->cbm->getById($nation_code, 0, $b_user_id_to, "account", $pelanggan->id);
                $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $b_user_id_to);

                if(isset($blockDataAccount->block_id) || isset($blockDataAccountReverse->block_id)){

                    if($chat_type == 'offer'){
                        $this->status = 1005;
                        $this->message = "An offer is not allowed as you're blocked";
                        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat_blocked_offer");
                        die();
                    }

                    $this->status = 1005;
                    $this->message = "You can no longer chat as you're blocked";
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat_blocked_general");
                    die();

                }
                //END by Donny Dennison - 7 november 2022 14:17
                //new feature, block community post or account

                //check already have room or not
                $checkRoomChat = $this->ecpm->getRoomChatIDByParticipantId($nation_code, $pelanggan->id, $b_user_id_to, $chat_type, $offer_product_id);
                if (isset($checkRoomChat->nation_code)) {

                    $chat_room_id = $checkRoomChat->e_chat_room_id;

                }else{

                    // $chat_room_id = $this->ecrm->getLastId($nation_code);
                    
                    //START by Donny Dennison - 12 july 2022 14:56
                    //new offer system
                    if ($chat_type == 'offer') {

                        //get product detail
                        $getProductType = $this->cpm->getProductType($nation_code, $offer_product_id);
                        $getProductType = $getProductType->product_type;

                        $productOffered = $this->cpm->getById($nation_code, $offer_product_id, $pelanggan, $getProductType);

                        //directory structure
                        // $thn = date("Y");
                        // $bln = date("m");
                        $ds = DIRECTORY_SEPARATOR;
                        $target = $this->media_chat;
                        if (!realpath($target)) {
                            mkdir($target, 0775);
                        }
                        // $target = $this->media_chat.$ds.$thn;
                        // if (!realpath($target)) {
                        //     mkdir($target, 0775);
                        // }
                        // $target = $this->media_chat.$ds.$thn.$ds.$bln;
                        // if (!realpath($target)) {
                        //     mkdir($target, 0775);
                        // }

                        // $jenis = mime_content_type(SENEROOT.$productOffered->thumb);
                        $ext = pathinfo(SENEROOT.$productOffered->thumb, PATHINFO_EXTENSION);
                        // $filename = $chat_room_id.'-'.$offer_product_id.'.'.$ext;
                        $filename = $offer_product_id.'.'.$ext;

                        if (file_exists(SENEROOT.$productOffered->thumb) && is_file(SENEROOT.$productOffered->thumb)) {
                            copy(SENEROOT.$productOffered->thumb, SENEROOT.$ds.$target.$ds.$filename);
                        }

                        // $url = $this->media_chat.'/'.$thn.'/'.$bln.'/'.$filename;
                        $url = $this->media_chat.'/'.$filename;
                        $url = str_replace("//", "/", $url);

                    }
                    //END by Donny Dennison - 12 july 2022 14:56
                    //new offer system

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
                    $di['b_user_id_starter'] = $pelanggan->id;
                    $di['custom_name_1'] = '';
                    $di['custom_name_2'] = '';
                    $di['cdate'] = 'NOW()';
                    $di['chat_type'] = $chat_type;

                    //START by Donny Dennison - 12 july 2022 14:56
                    //new offer system
                    if($chat_type == 'offer'){

                        $di['custom_name_1'] = $productOffered->nama;
                        $di['c_produk_id'] = $offer_product_id;
                        $di['b_user_id_seller'] = $productOffered->b_user_id_seller;
                        $di['c_produk_nama'] = $productOffered->nama;
                        $di['c_produk_harga_jual'] = $productOffered->harga_jual;
                        $di['c_produk_thumb'] = $url;

                    }
                    //END by Donny Dennison - 12 july 2022 14:56

                    $this->ecrm->set($di);

                    //insert chat participant 1
                    $di = array();
                    $di['nation_code'] = $nation_code;
                    $di['e_chat_room_id'] = $chat_room_id;
                    $di['b_user_id'] = $pelanggan->id;
                    $di['cdate'] = 'NOW()';
                    $di['is_read'] = 1;
                    $this->ecpm->set($di);

                    //insert chat participant 2
                    $di = array();
                    $di['nation_code'] = $nation_code;
                    $di['e_chat_room_id'] = $chat_room_id;
                    $di['b_user_id'] = $b_user_id_to;
                    $di['cdate'] = 'NOW()';
                    $di['is_read'] = 1;
                    $this->ecpm->set($di);

                }

                $roomChat = $this->ecrm->getChatRoomByID($nation_code, $chat_room_id, $chat_type);

                $roomChatparticipant = $this->ecpm->getParticipantByRoomChatId($nation_code, $chat_room_id);

            }else{
                $this->status = 7280;
                $this->message = 'Invalid Chat Room ID or Chat Room ID not registered';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
                die();
            }

        }

        $checkStillParticipant = 0;
        foreach($roomChatparticipant as $participant){

            if($participant->b_user_id == $pelanggan->id){
                $checkStillParticipant = 1;
                break;
            }

        }
        unset($participant);

        if ($checkStillParticipant == 0) {
            $this->status = 8104;
            $this->message = 'You can no longer chat as you exited this chat group';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //START by Donny Dennison - 7 november 2022 14:17
        //new feature, block community post or account
        if($pelanggan->id != $roomChat->b_user_id_starter){

            if($roomChat->chat_type == 'community'){

                $blockDataCommunity = $this->cbm->getById($nation_code, 0, $pelanggan->id, "community", $roomChat->c_community_id);
                $blockDataAccount = $this->cbm->getById($nation_code, 0, $roomChat->b_user_id_starter, "account", $pelanggan->id);
                $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $roomChat->b_user_id_starter);

                if(isset($blockDataCommunity->block_id) || isset($blockDataAccount->block_id) || isset($blockDataAccountReverse->block_id)){

                    $this->status = 1005;
                    $this->message = "You can no longer chat as you're blocked";
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat_blocked_general");
                    die();

                }

            }

        }

        if($roomChat->chat_type != 'community' && $roomChat->chat_type != 'admin'){

            $id_lawan_bicara = 0;
            foreach($roomChatparticipant as $participant){

                if($participant->b_user_id != $roomChat->b_user_id_starter){
                    $id_lawan_bicara = $participant->b_user_id;
                    break;
                }

            }
            unset($participant);

            $blockDataAccount = $this->cbm->getById($nation_code, 0, $roomChat->b_user_id_starter, "account", $id_lawan_bicara);
            $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $id_lawan_bicara, "account", $roomChat->b_user_id_starter);

            if(isset($blockDataAccount->block_id) || isset($blockDataAccountReverse->block_id)){

                if($roomChat->chat_type == 'offer'){
                    $this->status = 1005;
                    $this->message = "An offer is not allowed as you're blocked";
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat_blocked_offer");
                    die();
                }

                $this->status = 1005;
                $this->message = "You can no longer chat as you're blocked";
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat_blocked_general");
                die();

            }

            if($pelanggan->id == $roomChat->b_user_id_starter){
                $b_user_id_to = $id_lawan_bicara;
            }else{
                $b_user_id_to = $roomChat->b_user_id_starter;
            }

        }
        //END by Donny Dennison - 7 november 2022 14:17
        //new feature, block community post or account

        //collect message
        $message = trim(((empty($this->input->post('message')))? "" : $this->input->post('message')));
        if (!strlen($message)>0) {

            if ($chat_type == 'barter') {

                if ($this->input->post('product_id_barter_request') <= '0') {

                    $this->status = 8105;
                    $this->message = 'Message is empty';
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
                    die();

                }

            //START by Donny Dennison - 12 july 2022 14:56
            //new offer system
            }else if($chat_type == 'offer'){

                if($offer_status != "offering" && $offer_status != "chat"){

                    $message = $this->chat->getLastOfferByChatRoomId($nation_code, $roomChat->id, "offering")->message;

                    if (strpos($message, ' ') !== false) {
                        $message = trim(substr($message, 0, strpos($message," ")));
                    }

                }

            //END by Donny Dennison - 12 july 2022 14:56

            }else{

                $this->status = 8105;
                $this->message = 'Message is empty';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
                die();

            }

        }

        //by Donny Dennison 24 february 2021 18:45
        //change ’ to ' in add & edit product name and description
        $message = str_replace('’',"'",$message);
        // $message = filter_var($message, FILTER_SANITIZE_STRING);
        $message = nl2br($message);

        //by Donny Dennison - 15 augustus 2020 15:09
        //bug fix \n (enter) didnt get remove
        $message = str_replace(array("\r\n", "\n\r", "\r", "\n"), "", $message);

        //by Donny Dennison - 17 september 2021 9:16
        //community-feature
        //fix response in community description showing more "\"
        $message = str_replace("\\n", "<br />", $message);

        //start transaction
        $this->chat->trans_start();

        //get last chat id
        $chat_id = $this->chat->getLastId($nation_code, $roomChat->id);

        //insert chat into database
        $di = array();
        $di['id'] = $chat_id;
        $di['nation_code'] = $nation_code;
        $di['e_chat_room_id'] = $roomChat->id;
        $di['b_user_id'] = $pelanggan->id;

        //by Donny Dennison - 12 july 2022 14:56
        //new offer system
        $di['type'] = $offer_status;

        $di['message'] = $message;
        $di['message_indonesia'] = $message;
        $di['cdate'] = "NOW()";

        $res = $this->chat->set($di);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
            $this->chat->trans_commit();

            //set unread for admin
            $du = array();
            $du['is_read_admin'] = 0;

            //START by Donny Dennison - 12 july 2022 14:56
            //new offer system
            if($chat_type == 'offer' && $offer_status != "chat"){

                $du['offer_status'] = $offer_status;
                $du['offer_status_update_date'] = "NOW()";

            }
            //END by Donny Dennison - 12 july 2022 14:56
            //new offer system

            $this->ecrm->update($nation_code, $roomChat->id, $du);
            $this->chat->trans_commit();

            //set unread for other chat participant
            $du = array();
            $du['is_read'] = 0;
            $this->ecpm->updateUnread($nation_code, $roomChat->id, $pelanggan->id, $du);
            $this->chat->trans_commit();

            //set unread in table e_chat_read
            $insertArray = array();
            foreach($roomChatparticipant AS $participant){
                
                $du = array();
                $du['nation_code'] = $nation_code;
                $du['b_user_id'] = $participant->b_user_id;
                $du['e_chat_room_id'] = $roomChat->id;
                $du['e_chat_id'] = $chat_id;
                if($participant->b_user_id == $pelanggan->id){
                    $du['is_read'] = 1;
                }else{
                    $du['is_read'] = 0;
                }
                $du['cdate'] = "NOW()";
                $insertArray[] = $du;

            }
            unset($participant);

            $chunkInsertArray = array_chunk($insertArray,50);

            foreach($chunkInsertArray AS $chunk){

                //insert multi
                $this->ecreadm->setMass($chunk);
                $this->chat->trans_commit();

            }
            unset($insertArray, $chunkInsertArray, $chunk);

            //START by Donny Dennison - 12 july 2022 14:56
            //new offer system
            if($chat_type == 'offer' && $offer_status == "accepted"){

                $du = array();
                $du['stok'] = 0;

                $this->cpm->update($nation_code, $roomChat->b_user_id_seller, $roomChat->c_produk_id, $du);
                $this->chat->trans_commit();

                $listOffering = $this->ecrm->getAll($nation_code, $chat_type, $roomChat->c_produk_id, "offering");
                foreach($listOffering as $offering){

                    $roomChatOfferingparticipant = $this->ecpm->getParticipantByRoomChatId($nation_code, $offering->id);

                    $message = $this->chat->getLastOfferByChatRoomId($nation_code, $offering->id, "offering")->message;

                    $last_id = $this->chat->getLastId($nation_code, $offering->id);
                    $di = array();
                    $di['id'] = $last_id;
                    $di['nation_code'] = $nation_code;
                    $di['e_chat_room_id'] = $offering->id;
                    $di['b_user_id'] = $roomChat->b_user_id_seller;
                    $di['type'] = "rejected";
                    $di['message'] = $message;
                    $di['message_indonesia'] = $message;
                    $di['cdate'] = "NOW()";

                    $this->chat->set($di);
                    $this->chat->trans_commit();

                    //set unread for admin
                    $du = array();
                    $du['is_read_admin'] = 0;
                    $du['offer_status'] = "rejected";
                    $du['offer_status_update_date'] = "NOW()";

                    $this->ecrm->update($nation_code, $offering->id, $du);
                    $this->chat->trans_commit();

                    //set unread for other chat participant
                    $du = array();
                    $du['is_read'] = 0;
                    $this->ecpm->updateUnread($nation_code, $offering->id, $roomChat->b_user_id_seller, $du);
                    $this->chat->trans_commit();

                    //set unread in table e_chat_read
                    foreach($roomChatOfferingparticipant AS $participant){
                        
                        $du = array();
                        $du['nation_code'] = $nation_code;
                        $du['b_user_id'] = $participant->b_user_id;
                        $du['e_chat_room_id'] = $offering->id;
                        $du['e_chat_id'] = $last_id;
                        if($participant->b_user_id == $roomChat->b_user_id_seller){
                            $du['is_read'] = 1;
                        }
                        $du['cdate'] = "NOW()";
                        $this->ecreadm->set($du);
                        $this->chat->trans_commit();

                    }
                    unset($participant);

                }
                unset($listOffering, $offering);

            }
            //END by Donny Dennison - 12 july 2022 14:56

            //add attachment//receive the file
            $file = reset($_FILES);
            if (isset($file['name'])) {
                if ($file['size']>8000000) {
                    $this->message .= ', but attachment too big';
                } elseif (strlen($file['tmp_name'])) {

                    //get last id of attachment
                    $last_id = $this->ecam->getLastId($nation_code, $roomChat->id, $chat_id);

                    //directory structure
                    $thn = date("Y");
                    $bln = date("m");
                    $ds = DIRECTORY_SEPARATOR;
                    $target = $this->media_chat;
                    if (!realpath($target)) {
                        mkdir($target, 0775);
                    }
                    $target = $this->media_chat.$ds.$thn;
                    if (!realpath($target)) {
                        mkdir($target, 0775);
                    }
                    $target = $this->media_chat.$ds.$thn.$ds.$bln;
                    if (!realpath($target)) {
                        mkdir($target, 0775);
                    }

                    // $jenis = mime_content_type($file['tmp_name']);
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = $roomChat->id.'-'.$chat_id.'-'.$last_id.'.'.$ext;
                    if (file_exists(SENEROOT.$ds.$target.$ds.$filename)) {
                        unlink(SENEROOT.$ds.$target.$ds.$filename);
                    }
                    $res = move_uploaded_file($file['tmp_name'], SENEROOT.$ds.$target.$ds.$filename);
                    if ($res) {
                        $url = $this->media_chat.'/'.$thn.'/'.$bln.'/'.$filename;
                        $url = str_replace("//", "/", $url);

                        $dix = array();
                        $dix['nation_code'] = $nation_code;
                        $dix['e_chat_room_id'] = $roomChat->id;
                        $dix['e_chat_id'] = $chat_id;
                        $dix['id'] = $last_id;
                        $dix['jenis'] = $file['type'];
                        $dix['ukuran'] = $file['size'];
                        $dix['url'] = $url;
                        //insert into database
                        $res = $this->ecam->set($dix);
                        if ($res) {
                            $this->message = ', attachment uploaded';
                        } else {
                            $this->message = ', attachment failed';
                        }
                        $this->chat->trans_commit();
                    } else {
                        $this->message = ', but image upload failed';
                    }
                } //end check file Size
            }

            //add attachment product
            $product_id = $this->input->post('product_id');
            if ($product_id  > '0') {
                
                //get last id of attachment
                $last_id = $this->ecam->getLastId($nation_code, $roomChat->id, $chat_id);

                //get product detail
                $getProductType = $this->cpm->getProductType($nation_code, $product_id);
                $getProductType = $getProductType->product_type;

                $productAttached = $this->cpm->getById($nation_code, $product_id, $pelanggan, $getProductType);

                //directory structure
                $thn = date("Y");
                $bln = date("m");
                $ds = DIRECTORY_SEPARATOR;
                $target = $this->media_chat;
                if (!realpath($target)) {
                    mkdir($target, 0775);
                }
                $target = $this->media_chat.$ds.$thn;
                if (!realpath($target)) {
                    mkdir($target, 0775);
                }
                $target = $this->media_chat.$ds.$thn.$ds.$bln;
                if (!realpath($target)) {
                    mkdir($target, 0775);
                }

                // $jenis = mime_content_type(SENEROOT.$productAttached->thumb);
                $ext = pathinfo(SENEROOT.$productAttached->thumb, PATHINFO_EXTENSION);
                $filename = $roomChat->id.'-'.$chat_id.'-'.$last_id.'.'.$ext;
                
                if (file_exists(SENEROOT.$productAttached->thumb) && is_file(SENEROOT.$productAttached->thumb)) {
                    copy(SENEROOT.$productAttached->thumb, SENEROOT.$ds.$target.$ds.$filename);
                }
                
                $url = $this->media_chat.'/'.$thn.'/'.$bln.'/'.$filename;
                $url = str_replace("//", "/", $url);

                $dix = array();
                $dix['nation_code'] = $nation_code;
                $dix['e_chat_room_id'] = $roomChat->id;
                $dix['e_chat_id'] = $chat_id;
                $dix['id'] = $last_id;
                $dix['jenis'] = 'product';
                $dix['url'] = $product_id;
                $dix['produk_nama'] = $productAttached->nama;
                $dix['produk_type'] = $productAttached->product_type;
                $dix['produk_harga_jual'] = $productAttached->harga_jual;
                $dix['produk_thumb'] = $url;

                //insert into database
                $this->ecam->set($dix);
                $this->chat->trans_commit();

                if($chat_type != 'community'){

                    //set custom_name_1
                    $du = array();
                    $du['custom_name_1'] = $productAttached->nama;

                    $this->ecrm->update($nation_code, $roomChat->id, $du);
                    $this->chat->trans_commit();

                }
                
            }

            //add attachment order
            $order_id = $this->input->post('order_id');
            $order_detail_id = $this->input->post('order_detail_id');
            $order_detail_item_id = $this->input->post('order_detail_item_id');
            if ($order_id > 0 && $order_detail_id > 0 && $order_detail_item_id > 0) {
                
                //get last id of attachment
                $last_id = $this->ecam->getLastId($nation_code, $roomChat->id, $chat_id);

                $dix = array();
                $dix['nation_code'] = $nation_code;
                $dix['e_chat_room_id'] = $roomChat->id;
                $dix['e_chat_id'] = $chat_id;
                $dix['id'] = $last_id;
                $dix['jenis'] = 'order';
                $dix['url'] = $order_id;
                $dix['order_detail_id'] = $order_detail_id;
                $dix['order_detail_item_id'] = $order_detail_item_id;

                //insert into database
                $this->ecam->set($dix);
                $this->chat->trans_commit();
                
            }
            
            //add attachment product barter request
            $product_id_barter_request = $this->input->post('product_id_barter_request');
            if ($product_id_barter_request  > '0') {

                //get last id of attachment
                $last_id = $this->ecam->getLastId($nation_code, $roomChat->id, $chat_id);

                //get product detail
                $getProductType = $this->cpm->getProductType($nation_code, $product_id_barter_request);
                $getProductType = $getProductType->product_type;

                $productAttached = $this->cpm->getById($nation_code, $product_id_barter_request, $pelanggan, $getProductType);

                //directory structure
                $thn = date("Y");
                $bln = date("m");
                $ds = DIRECTORY_SEPARATOR;
                $target = $this->media_chat;
                if (!realpath($target)) {
                    mkdir($target, 0775);
                }
                $target = $this->media_chat.$ds.$thn;
                if (!realpath($target)) {
                    mkdir($target, 0775);
                }
                $target = $this->media_chat.$ds.$thn.$ds.$bln;
                if (!realpath($target)) {
                    mkdir($target, 0775);
                }

                // $jenis = mime_content_type(SENEROOT.$productAttached->thumb);
                $ext = pathinfo(SENEROOT.$productAttached->thumb, PATHINFO_EXTENSION);
                $filename = $roomChat->id.'-'.$chat_id.'-'.$last_id.'.'.$ext;

                if (file_exists(SENEROOT.$productAttached->thumb) && is_file(SENEROOT.$productAttached->thumb)) {
                    copy(SENEROOT.$productAttached->thumb, SENEROOT.$ds.$target.$ds.$filename);
                }

                $url = $this->media_chat.'/'.$thn.'/'.$bln.'/'.$filename;
                $url = str_replace("//", "/", $url);

                $dix = array();
                $dix['nation_code'] = $nation_code;
                $dix['e_chat_room_id'] = $roomChat->id;
                $dix['e_chat_id'] = $chat_id;
                $dix['id'] = $last_id;
                $dix['jenis'] = 'barter_request';
                $dix['url'] = $product_id_barter_request;
                $dix['produk_nama'] = $productAttached->nama;
                $dix['produk_type'] = $productAttached->product_type;
                $dix['produk_harga_jual'] = $productAttached->harga_jual;
                $dix['produk_thumb'] = $url;

                //insert into database
                $this->ecam->set($dix);
                $this->chat->trans_commit();

                if($chat_type != 'community'){

                    //set custom_name_1
                    $du = array();
                    $du['custom_name_1'] = $productAttached->nama;

                    $this->ecrm->update($nation_code, $roomChat->id, $du);
                    $this->chat->trans_commit();

                }
 
            }

            //add attachment product barter exchange
            $product_id_barter_exchange = $this->input->post('product_id_barter_exchange');
            if(is_array($product_id_barter_exchange)){

                if(count($product_id_barter_exchange) > 0){

                    foreach ($product_id_barter_exchange as $key => $product_id) {

                        //get last id of attachment
                        $last_id = $this->ecam->getLastId($nation_code, $roomChat->id, $chat_id);

                        //get product detail
                        $getProductType = $this->cpm->getProductType($nation_code, $product_id);
                        $getProductType = $getProductType->product_type;

                        $productAttached = $this->cpm->getById($nation_code, $product_id, $pelanggan, $getProductType);

                        //directory structure
                        $thn = date("Y");
                        $bln = date("m");
                        $ds = DIRECTORY_SEPARATOR;
                        $target = $this->media_chat;
                        if (!realpath($target)) {
                            mkdir($target, 0775);
                        }
                        $target = $this->media_chat.$ds.$thn;
                        if (!realpath($target)) {
                            mkdir($target, 0775);
                        }
                        $target = $this->media_chat.$ds.$thn.$ds.$bln;
                        if (!realpath($target)) {
                            mkdir($target, 0775);
                        }

                        // $jenis = mime_content_type(SENEROOT.$productAttached->thumb);
                        $ext = pathinfo(SENEROOT.$productAttached->thumb, PATHINFO_EXTENSION);
                        $filename = $roomChat->id.'-'.$chat_id.'-'.$last_id.'.'.$ext;

                        if (file_exists(SENEROOT.$productAttached->thumb) && is_file(SENEROOT.$productAttached->thumb)) {
                            copy(SENEROOT.$productAttached->thumb, SENEROOT.$ds.$target.$ds.$filename);
                        }

                        $url = $this->media_chat.'/'.$thn.'/'.$bln.'/'.$filename;
                        $url = str_replace("//", "/", $url);

                        $dix = array();
                        $dix['nation_code'] = $nation_code;
                        $dix['e_chat_room_id'] = $roomChat->id;
                        $dix['e_chat_id'] = $chat_id;
                        $dix['id'] = $last_id;
                        $dix['jenis'] = 'barter_exchange';
                        $dix['url'] = $product_id;
                        $dix['produk_nama'] = $productAttached->nama;
                        $dix['produk_type'] = $productAttached->product_type;
                        $dix['produk_harga_jual'] = $productAttached->harga_jual;
                        $dix['produk_thumb'] = $url;

                        //insert into database
                        $this->ecam->set($dix);
                        $this->chat->trans_commit();

                    }

                }

            }

            $roomChat = $this->ecrm->getChatRoomByID($nation_code, $chat_room_id, $chat_type);

            $roomChat->custom_name_1 = html_entity_decode($roomChat->custom_name_1,ENT_QUOTES);

            $participant_list = $this->ecpm->getParticipantByRoomChatId($nation_code,$roomChat->id);

            foreach($participant_list as $participantList){

                if($roomChat->chat_type == 'admin'){
                    $roomChat->custom_name_2 = 'SellOn Support';
                    $roomChat->is_admin = 1;

                    if(isset($participantList->b_user_image) && file_exists(SENEROOT.$participantList->b_user_image) && $participantList->b_user_image != 'media/user/default.png'){
                        $roomChat->custom_image = $this->cdn_url($participantList->b_user_image);
                    } else {
                        $roomChat->custom_image = $this->cdn_url('media/user/default-profile-picture.png');
                    }

                    break;

                }else if($participantList->b_user_id != $pelanggan->id && $roomChat->chat_type != 'community'){
                    $roomChat->custom_name_2 = $participantList->b_user_fnama;
                    $roomChat->is_admin = $participantList->is_admin;

                    if(isset($participantList->b_user_image) && file_exists(SENEROOT.$participantList->b_user_image) && $participantList->b_user_image != 'media/user/default.png'){
                        $roomChat->custom_image = $this->cdn_url($participantList->b_user_image);
                    } else {
                        $roomChat->custom_image = $this->cdn_url('media/user/default-profile-picture.png');
                    }

                    break;

                }else if($roomChat->chat_type == 'community'){
                    $roomChat->custom_name_2 = $roomChat->b_user_nama_starter;

                    if(isset($roomChat->b_user_image_starter) && file_exists(SENEROOT.$roomChat->b_user_image_starter) && $roomChat->b_user_image_starter != 'media/user/default.png'){
                        $roomChat->custom_image = $this->cdn_url($roomChat->b_user_image_starter);
                    } else {
                        $roomChat->custom_image = $this->cdn_url('media/user/default-profile-picture.png');
                    }

                    break;

                }

            }
            unset($participant_list, $participantList);

            if ($chat_type == "buyandsell" || $chat_type == "private" || $chat_type == 'barter' || $chat_type == 'offer') {

                //get missing data
                $sender = $this->bu->getById($nation_code, $pelanggan->id);
                $receiver = $this->bu->getById($nation_code, $b_user_id_to);

                $type = 'chat';
                $anotid = 1;
                $replacer = array();
                $replacer['pelanggan_fnama'] = $sender->fnama;
                $classified = 'setting_notification_user';
                $code = 'U4';

                $receiverSettingNotif = $this->busm->getValue($nation_code, $b_user_id_to, $classified, $code);

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
                        $payload->chat_type = $chat_type;
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
                        $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
                    }

                }

            }else if($chat_type == 'community'){

                //get missing data
                $ownerCommunity = $this->bu->getById($nation_code, $roomChat->b_user_id_starter);
                $sender = $this->bu->getById($nation_code, $pelanggan->id);

                foreach($roomChatparticipant as $participant){

                    if($participant->b_user_id != $pelanggan->id){
                        $receiver = $this->bu->getById($nation_code, $participant->b_user_id);

                        $classified = 'setting_notification_user';
                        $code = 'U4';

                        $receiverSettingNotif = $this->busm->getValue($nation_code, $participant->b_user_id, $classified, $code);

                        if (!isset($receiverSettingNotif->setting_value)) {
                            $receiverSettingNotif->setting_value = 0;
                        }

                        //push notif
                        if ($receiverSettingNotif->setting_value == 1 && $receiver->is_active == 1) {

                            $type = 'chat';
                            $anotid = 5;
                            $replacer = array();
                            $replacer['pelanggan_fnama'] = $sender->fnama;
                            if($receiver->language_id == 2) {
                                $title = 'Obrolan Baru';
                                $message = "Anda memiliki pesan obrolan dari $sender->fnama";
                            } else {
                                $title = 'New Chat';
                                $message = "You have chat messages from $sender->fnama";
                            }

                            $image = 'media/pemberitahuan/chat.png';

                            $device = "aa"; //jenis device
                            $tokens = array($receiver->fcm_token); //device token
                            $payload = new stdClass();
                            $payload->chat_room_id = (string) $roomChat->id;
                            $payload->user_id = $sender->id;
                            $payload->user_fnama = $sender->fnama;

                            // by Muhammad Sofi - 27 October 2021 10:12
                            // if user img & banner not exist or empty, change to default image
                            // $payload->user_image = $this->cdn_url($ownerCommunity->image);
                            if(file_exists(SENEROOT.$ownerCommunity->image) && $ownerCommunity->image != 'media/user/default.png'){
                                $payload->user_image = $this->cdn_url($ownerCommunity->image);
                            } else {
                                $payload->user_image = $this->cdn_url('media/user/default-profile-picture.png');
                            }
                            $payload->chat_type = $chat_type;
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
                            $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);

                        }
                    }

                }

            }

            $data['chat'] = $this->chat->getChatByChatIdChatRoomId($nation_code, $chat_id, $roomChat->id, $pelanggan->language_id);

            //get complains
            $data['chat']->complain = $this->complain->getDetailByChatRoomIDChatID($nation_code, $roomChat->id, $data['chat']->chat_id);
            if (isset($data['chat']->complain->c_produk_nama)) {
                // $data['chat']->complain->c_produk_nama = $this->__dconv($data['chat']->complain->c_produk_nama);
                $data['chat']->complain->c_produk_nama = html_entity_decode($data['chat']->complain->c_produk_nama,ENT_QUOTES);

                // START by Muhammad Sofi - 27 October 2021 10:12
                // if user img & banner not exist or empty, change to default image
                // $data['chat']->complain->b_user_image_seller = $this->cdn_url($data['chat']->complain->b_user_image_seller);
                if(file_exists(SENEROOT.$data['chat']->complain->b_user_image_seller) && $data['chat']->complain->b_user_image_seller != 'media/user/default.png'){
                    $data['chat']->complain->b_user_image_seller = $this->cdn_url($data['chat']->complain->b_user_image_seller);
                } else {
                    $data['chat']->complain->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                }
                // $data['chat']->complain->b_user_image_buyer = $this->cdn_url($data['chat']->complain->b_user_image_buyer);
                if(file_exists(SENEROOT.$data['chat']->complain->b_user_image_buyer) && $data['chat']->complain->b_user_image_buyer != 'media/user/default.png'){
                    $data['chat']->complain->b_user_image_buyer = $this->cdn_url($data['chat']->complain->b_user_image_buyer);
                } else {
                    $data['chat']->complain->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                }
                // END by Muhammad Sofi - 27 October 2021 10:12

                $data['chat']->complain->c_produk_foto = $this->cdn_url($data['chat']->complain->c_produk_foto);
                $data['chat']->complain->c_produk_thumb = $this->cdn_url($data['chat']->complain->c_produk_thumb);

                $data['chat']->complain->status_text = $this->__statusText($data['chat']->complain, $data['chat']->complain);
            }

            //get attachment 
            $data['chat']->attachments = $this->ecam->getDetailByChatRoomIDChatID($nation_code, $roomChat->id, $data['chat']->chat_id);
            if ($data['chat']->attachments) {

                foreach($data['chat']->attachments AS &$at){

                    $at->order_invoice_code = '';
                    $at->order_thumb = '';
                    $at->order_user_id_seller = '';
                    $at->status_text = '';

                    if($at->jenis == 'product' || $at->jenis == 'barter_request' || $at->jenis == 'barter_exchange'){    

                        $at->produk_thumb = $this->cdn_url($at->produk_thumb);
                        // $at->produk_nama = $this->__dconv($at->produk_nama);
                        $at->produk_nama = html_entity_decode($at->produk_nama,ENT_QUOTES);

                    }else if($at->jenis == 'order'){

                        $produk = $this->dodm->getByIdForChat($nation_code, $at->url, $at->order_detail_id);
                        $item = $this->dodim->getById($nation_code, $at->url, $at->order_detail_id, $at->order_detail_item_id);

                        if (isset($produk->c_produk_id)) {

                            $at->order_invoice_code = $produk->invoice_code;
                            $at->order_thumb = $this->cdn_url($item->thumb);
                            $at->order_user_id_seller = $produk->b_user_id_seller;
                            $at->status_text = $this->__statusText($produk, $produk);

                        }

                    }else{

                        $at->url = $this->cdn_url($at->url);

                    }

                }

            }

            //chat iteration
            // $data['chat']->cdate_text = $this->humanTiming($data['chat']->cdate);
            $data['chat']->cdate_text = $this->humanTiming($data['chat']->cdate, null, $pelanggan->language_id);

            $data['chat']->cdate = $this->customTimezone($data['chat']->cdate, $timezone);

            //by Donny Dennison - 12 july 2022 14:56
            //new offer system
            $data['chat']->message = $this->__changeOfferMessage($data['chat']->type, $data['chat']->message, $pelanggan->language_id);

            $data['chat']->message = html_entity_decode($data['chat']->message,ENT_QUOTES);

            if (isset($data['chat']->b_user_image)) {

                // by Muhammad Sofi - 27 October 2021 10:12
                // if user img & banner not exist or empty, change to default image
                // $data['chat']->b_user_image = $this->cdn_url($data['chat']->b_user_image);
                if(file_exists(SENEROOT.$data['chat']->b_user_image) && $data['chat']->b_user_image != 'media/user/default.png'){
                    $data['chat']->b_user_image = $this->cdn_url($data['chat']->b_user_image);
                } else {
                    $data['chat']->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
                }

            }

            if (isset($ch->a_pengguna_foto)) {
                $data['chat']->a_pengguna_foto = $this->cdn_url($data['chat']->a_pengguna_foto);
            }

        } else {
            $this->chat->trans_rollback();
            $this->status = 8011;
            $this->message = 'Failed updating data';
        }

        $this->chat->trans_end();

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    }

    /**
     * Send chat
     * @return mixed result API
     */
    public function getchatroomid()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['chat_room_id'] = 0;

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //collect b_user_id_to
        $b_user_id_to = $this->input->post('b_user_id_to');
        if ($b_user_id_to<='0') {
            $this->status = 8101;
            $this->message = 'Invalid B User ID To';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        $chat_type = $this->input->post('chat_type');

        if ($chat_type != 'buyandsell' && $chat_type != 'private' && $chat_type != 'barter' && $chat_type != 'offer') {
            $this->status = 8102;
            $this->message = 'Chat type must be buyandsell or private or barter or offer';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        $offer_product_id = $this->input->post('offer_product_id');
        if ($offer_product_id <= '0' && $chat_type == 'offer') {
            $this->status = 8107;
            $this->message = 'Invalid offer_product_id';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //check already have room or not
        $checkRoomChat = $this->ecpm->getRoomChatIDByParticipantId($nation_code, $pelanggan->id, $b_user_id_to, $chat_type, $offer_product_id);
        if (isset($checkRoomChat->nation_code)) {
            
            $data['chat_room_id'] = $checkRoomChat->e_chat_room_id;

        }   

        $this->status = 200;
        $this->message = 'Success';

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    }

    public function read($chat_room_id="", $chat_type= "private")
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        $chat_room_id = $chat_room_id;
        if ($chat_room_id<='0') {
            $this->status = 7280;
            $this->message = 'Invalid Chat Room ID or Chat Room ID not registered';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        $this->ecpm->setAsRead($nation_code, $chat_room_id, $pelanggan->id);

        $this->ecreadm->setAsRead($nation_code, $chat_room_id, $pelanggan->id);

        //render to json
        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    }

    public function readall()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        $this->ecpm->setAsReadAll($nation_code, $pelanggan->id);

        $this->ecreadm->setAsReadAll($nation_code, $pelanggan->id);

        //render to json
        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    }

    public function hapus(){
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['custom_message'] = "";
        $data['chat_unread'] = "0";
        $data['chat_room'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if(empty($nation_code)){
          $this->status = 101;
          $this->message = 'Missing or invalid nation_code';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
          die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if(!$c){
          $this->status = 400;
          $this->message = 'Missing or invalid API key';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
          die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if(!isset($pelanggan->id)){
          $this->status = 401;
          $this->message = 'Missing or invalid API session';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
          die();
        }

        $post_data_json = $this->input->post("post_data");
        $post_data = json_decode($post_data_json);
        if (!is_array($post_data)) {
            $this->status = 829;
            $this->message = 'post_data must be an array';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        if (count($post_data)<=0) {
            $this->status = 830;
            $this->message = 'Please add at least one chat room on post_data';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //by Donny Dennison - 12 july 2022 14:56
        //new offer system
        $total_cannot_delete = 0;

        //delete chat
        foreach ($post_data as $pdp) {

            //get chat room
            $roomChat = $this->ecrm->getChatRoomByID($nation_code, $pdp->chat_room_id);

            //START by Donny Dennison - 12 july 2022 14:56
            //new offer system
            if($roomChat->chat_type == "offer"){

                if($roomChat->offer_status == "cancelled" || $roomChat->offer_status == "rejected" || $roomChat->offer_status == "reviewed"){
            //END by Donny Dennison - 12 july 2022 14:56
            //new offer system

                $du = array();
                $du['last_delete_chat'] = date('Y-m-d H:i:s');

                //update table e_chat_participant
                $this->ecpm->update($nation_code, $pdp->chat_room_id, $pelanggan->id, $du);

                $this->ecpm->setAsRead($nation_code, $pdp->chat_room_id, $pelanggan->id);

                $this->ecreadm->setAsRead($nation_code, $pdp->chat_room_id, $pelanggan->id);

            //START by Donny Dennison - 12 july 2022 14:56
            //new offer system
                }else{
                    $total_cannot_delete++;
                }

            }else{

                $du = array();
                $du['last_delete_chat'] = date('Y-m-d H:i:s');

                //update table e_chat_participant
                $this->ecpm->update($nation_code, $pdp->chat_room_id, $pelanggan->id, $du);

                $this->ecpm->setAsRead($nation_code, $pdp->chat_room_id, $pelanggan->id);

                $this->ecreadm->setAsRead($nation_code, $pdp->chat_room_id, $pelanggan->id);

            }
            //END by Donny Dennison - 12 july 2022 14:56
            //new offer system

        }

        $url = base_url("api_mobile/chat/?apikey=$apikey&nation_code=$nation_code&apisess=$apisess");
        $res = $this->seme_curl->get($url);

        $body = json_decode($res->body);
        $chat_room = $body->data;

        //START by Donny Dennison - 12 july 2022 14:56
        //new offer system
        if($total_cannot_delete > 0){

            if($pelanggan->language_id == 2){//indonesia
                $data['custom_message'] = "Anda tidak bisa menghapus chat ini, Transaksi belum selesai";
            }else{
                $data['custom_message'] = "You can't delete this chat, Offer transaction is not done yet";
            }

        }
        //END by Donny Dennison - 12 july 2022 14:56
        //new offer system

        //default output
        $this->status = 200;
        $this->message = 'Success';
        $data['chat_room'] = $chat_room;
        unset($chat_room);
        //get unread count
        $data['chat_unread'] = "".$this->ecpm->countUnread($nation_code,$pelanggan->id);

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    }

    public function keluar(){
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['chat_unread'] = "0";
        $data['chat_room'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if(empty($nation_code)){
          $this->status = 101;
          $this->message = 'Missing or invalid nation_code';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
          die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if(!$c){
          $this->status = 400;
          $this->message = 'Missing or invalid API key';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
          die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if(!isset($pelanggan->id)){
          $this->status = 401;
          $this->message = 'Missing or invalid API session';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
          die();
        }

        $chat_room_id = $this->input->post("chat_room_id");
        if ($chat_room_id<='0') {
            $this->status = 831;
            $this->message = 'missing chat_room_id or chat room not community';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //get chat room
        $roomChat = $this->ecrm->getChatRoomByID($nation_code, $chat_room_id);
        if($roomChat->chat_type != 'community'){
            
            $this->status = 831;
            $this->message = 'missing chat_room_id or chat room not community';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();

        }else{

            $type = 'chat';
            $replacer = array();

            $replacer['user_nama'] = html_entity_decode($pelanggan->fnama,ENT_QUOTES);
            $message = '';
            $message_indonesia = '';

            $nw = $this->anot->get($nation_code, "push", $type, 3, 1);
            if (isset($nw->message)) {
              $message = $this->__nRep($nw->message, $replacer);
            }

            $nw = $this->anot->get($nation_code, "push", $type, 3, 2);
            if (isset($nw->message)) {
              $message_indonesia = $this->__nRep($nw->message, $replacer);
            }

            //get last chat id
            $chat_id = $this->chat->getLastId($nation_code, $roomChat->id);

            $di = array();
            $di['id'] = $chat_id;
            $di['nation_code'] = $nation_code;
            $di['e_chat_room_id'] = $roomChat->id;
            $di['b_user_id'] = 0;
            $di['type'] = 'announcement';
            $di['message'] = $message;
            $di['message_indonesia'] = $message_indonesia;
            $di['cdate'] = "NOW()";
            $this->chat->set($di);

            $participant_list = $this->ecpm->getParticipantByRoomChatId($nation_code,$roomChat->id);
            
            //set unread in table e_chat_read
            $insertArray = array();
            foreach($participant_list AS $participant){
                
                $du = array();
                $du['nation_code'] = $nation_code;
                $du['b_user_id'] = $participant->b_user_id;
                $du['e_chat_room_id'] = $roomChat->id;
                $du['e_chat_id'] = $chat_id;
                if($participant->b_user_id == $pelanggan->id){
                    $du['is_read'] = 1;
                }else{
                    $du['is_read'] = 0;
                }
                $du['cdate'] = "NOW()";
                $insertArray[] = $du;

            }
            unset($participant_list, $participant);

            $chunkInsertArray = array_chunk($insertArray,50);

            foreach($chunkInsertArray AS $chunk){

                //insert multi
                $this->ecreadm->setMass($chunk);

            }
            unset($insertArray, $chunkInsertArray, $chunk);

        }

        $du = array();
        $du['last_delete_chat'] = date('Y-m-d H:i:s');

        if($roomChat->chat_type == 'community'){
            $du['is_active'] = 0;
        }

        //update table e_chat_participant
        $this->ecpm->update($nation_code, $roomChat->id, $pelanggan->id, $du);

        $this->ecpm->setAsRead($nation_code, $roomChat->id, $pelanggan->id);

        $this->ecreadm->setAsRead($nation_code, $roomChat->id, $pelanggan->id);

        // by Muhammad Sofi - 12 November 2021 16:32 
        // remove subquery get total_people_group_chat, update data on join and leave user
        $this->ccomm->updateTotal($nation_code, $roomChat->c_community_id, "total_people_group_chat", '-', 1);

        //send push notif
        $ownerCommunity = $this->bu->getById($nation_code, $roomChat->b_user_id_starter);
        $sender = $this->bu->getById($nation_code, $pelanggan->id);
        $participant_list = $this->ecpm->getParticipantByRoomChatId($nation_code,$roomChat->id);
        $ios = array();
        $android = array();

        foreach($participant_list as $participant){

            if($participant->b_user_id != $pelanggan->id){
                $receiver = $this->bu->getById($nation_code, $participant->b_user_id);

                $classified = 'setting_notification_user';
                $code = 'U4';

                $receiverSettingNotif = $this->busm->getValue($nation_code, $participant->b_user_id, $classified, $code);

                if (!isset($receiverSettingNotif->setting_value)) {
                    $receiverSettingNotif->setting_value = 0;
                }

                //push notif
                if ($receiverSettingNotif->setting_value == 1 && $receiver->is_active == 1) {
                    
                    if (strtolower($receiver->device) == 'ios') {
                      $ios[] = $receiver->fcm_token;
                    } else {
                      $android[] = $receiver->fcm_token;
                    }

                }
            }

        }

        $type = 'chat';
        $anotid = 3;
        $replacer = array();
        $replacer['user_nama'] = html_entity_decode($sender->fnama,ENT_QUOTES);
        if($sender->language_id == 2) {
            $title = 'Obrolan Baru';
            $message = "$sender->fnama telah meninggalkan obrolan grup";
        } else {
            $title = 'New Chat';
            $message = "$sender->fnama has left the group chat";
        }
        
        $image = 'media/pemberitahuan/chat.png';

        if (array_unique($ios)) {
            $device = "ios"; //jenis device
            $tokens = $ios; //device token
            $payload = new stdClass();
            $payload->chat_room_id = (string) $chat_room_id;
            $payload->user_id = $sender->id;
            $payload->user_fnama = $sender->fnama;

            // by Muhammad Sofi - 27 October 2021 10:12
            // if user img & banner not exist or empty, change to default image
            // $payload->user_image = $this->cdn_url($ownerCommunity->image);
            if(file_exists(SENEROOT.$ownerCommunity->image) && $ownerCommunity->image != 'media/user/default.png'){
                $payload->user_image = $this->cdn_url($ownerCommunity->image);
            } else {
                $payload->user_image = $this->cdn_url('media/user/default-profile-picture.png');
            }
            $payload->chat_type = $roomChat->chat_type;
            $payload->custom_name_1 = $roomChat->custom_name_1;
            $payload->custom_name_2 = $roomChat->custom_name_2;
            // $payload->custom_image = $roomChat->custom_image;


            $nw = $this->anot->get($nation_code, "push", $type, $anotid, $sender->language_id);
            if (isset($nw->message)) {
                $message = $this->__nRep($nw->message, $replacer);
            }
            if (isset($nw->image)) {
                $image = $nw->image;
            }
            $image = $this->cdn_url($image);
            $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        }

        if (array_unique($android)) {
            $device = "android"; //jenis device
            $tokens = $android; //device token
            $payload = new stdClass();
            $payload->chat_room_id = (string) $chat_room_id;
            $payload->user_id = $sender->id;
            $payload->user_fnama = $sender->fnama;
            
            // by Muhammad Sofi - 27 October 2021 10:12
            // if user img & banner not exist or empty, change to default image
            // $payload->user_image = $this->cdn_url($ownerCommunity->image);
            if(file_exists(SENEROOT.$ownerCommunity->image) && $ownerCommunity->image != 'media/user/default.png'){
                $payload->user_image = $this->cdn_url($ownerCommunity->image);
            } else {
                $payload->user_image = $this->cdn_url('media/user/default-profile-picture.png');
            }
            $payload->chat_type = $roomChat->chat_type;
            $payload->custom_name_1 = $roomChat->custom_name_1;
            $payload->custom_name_2 = $roomChat->custom_name_2;
            // $payload->custom_image = $roomChat->custom_image;

            $nw = $this->anot->get($nation_code, "push", $type, $anotid, $sender->language_id);
            if (isset($nw->message)) {
                $message = $this->__nRep($nw->message, $replacer);
            }
            if (isset($nw->image)) {
                $image = $nw->image;
            }
            $image = $this->cdn_url($image);
            $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        }

        $url = base_url("api_mobile/chat/?apikey=$apikey&nation_code=$nation_code&apisess=$apisess");
        $res = $this->seme_curl->get($url);
        
        $body = json_decode($res->body);
        $chat_room = $body->data;

        //default output
        $this->status = 200;
        $this->message = 'Success';
        $data['chat_room'] = $chat_room;
        unset($chat_room);
        //get unread count
        $data['chat_unread'] = "".$this->ecpm->countUnread($nation_code,$pelanggan->id);

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    }

    public function count(){
        //initial
        $dt = $this->__init();

        //default result
        $data = 0;

        //default output
        $this->status = 200;
        $this->message = 'Success';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if(empty($nation_code)){
          $this->status = 101;
          $this->message = 'Missing or invalid nation_code';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
          die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if(!$c){
          $this->status = 400;
          $this->message = 'Missing or invalid API key';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
          die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if(!isset($pelanggan->id)){
          $this->status = 401;
          $this->message = 'Missing or invalid API session';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
          die();
        }

        $this->status = 200;
        $this->message = "Success";

        $data = "".$this->ecpm->countUnread($nation_code,$pelanggan->id);

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    }

    public function new_chat()
    {

        //initial
        //default result
        $data = array();
        $data['chat_room'] = new stdClass();
        $data['participant_list'] = array();
        $data['participant_total'] = 0;
        $data['chat_total'] = 0;
        $data['chat'] = array();
        $data['chat_id_havent_read_lawan_bicara'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        //get chat_room_id
        $chat_room_id = $this->input->get('chat_room_id');
        $data['chat_room'] = $this->ecrm->getChatRoomByID($nation_code, $chat_room_id);
        if ($chat_room_id<='0' || !isset($data['chat_room']->id)) {
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        $data['chat_room']->custom_name_1 = html_entity_decode($data['chat_room']->custom_name_1,ENT_QUOTES);

        if (isset($data['chat_room']->b_user_image_starter)) {

            if(file_exists(SENEROOT.$data['chat_room']->b_user_image_starter) && $data['chat_room']->b_user_image_starter != 'media/user/default.png'){
                $data['chat_room']->b_user_image_starter = $this->cdn_url($data['chat_room']->b_user_image_starter);
            } else {
                $data['chat_room']->b_user_image_starter = $this->cdn_url('media/user/default-profile-picture.png');
            }

        }

        //by Donny Dennison - 12 july 2022 14:56
        //new offer system
        $data['chat_room']->buyer_or_seller = "";
        $data['chat_room']->c_produk_stok = "0";
        $data['chat_room']->last_offer_price = "0";

        if($data['chat_room']->chat_type == "offer"){

            $getProductType = $this->cpm->getProductType($nation_code, $data['chat_room']->c_produk_id);
            $getProductType = $getProductType->product_type;

            $produk = $this->cpm->getByIdIgnoreActive($nation_code, $data['chat_room']->c_produk_id, $pelanggan, $getProductType);

            if($pelanggan->id == $produk->b_user_id_seller){
                $data['chat_room']->buyer_or_seller = "seller";
            }else{
                $data['chat_room']->buyer_or_seller = "buyer";
            }

            $data['chat_room']->c_produk_stok = $produk->stok;

            $data['chat_room']->c_produk_nama = html_entity_decode($data['chat_room']->c_produk_nama,ENT_QUOTES);

            if(file_exists(SENEROOT.$data['chat_room']->c_produk_thumb) && $data['chat_room']->c_produk_thumb != 'media/user/default.png'){
                $data['chat_room']->c_produk_thumb = $this->cdn_url($data['chat_room']->c_produk_thumb);
            } else {
                $data['chat_room']->c_produk_thumb = $this->cdn_url('media/produk/default.png');
            }

            $data['chat_room']->last_offer_price = $this->chat->getLastOfferByChatRoomId($nation_code, $chat_room_id, "offering");
            if(isset($data['chat_room']->last_offer_price->message)){
                $data['chat_room']->last_offer_price = $data['chat_room']->last_offer_price->message;
            }else{
                $data['chat_room']->last_offer_price = "0";
            }

        }
        //END by Donny Dennison - 12 july 2022 14:56

        $data['participant_list'] = $this->ecpm->getParticipantByRoomChatId($nation_code,$chat_room_id);

        //check already in group chat or not
        $checkStillParticipant = 0;
        foreach($data['participant_list'] as $participant){

            if($participant->b_user_id == $pelanggan->id){
                $checkStillParticipant = 1;
                break;
            }

        }
        unset($participant);

        if ($checkStillParticipant == 0) {
            $this->status = 8104;
            $this->message = 'You can no longer chat as you exited this chat group';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
            die();
        }

        $data['participant_total'] = count($data['participant_list']);

        $last_delete_chat = '';
        $id_lawan_bicara = 0;
        foreach($data['participant_list'] as &$participantList){

            if($participantList->b_user_id == $pelanggan->id){
                $last_delete_chat = $participantList->last_delete_chat;
            }else{
                $id_lawan_bicara = $participantList->b_user_id;
            }

            if(isset($participantList->b_user_image) && file_exists(SENEROOT.$participantList->b_user_image) && $participantList->b_user_image != 'media/user/default.png'){
                $participantList->b_user_image = $this->cdn_url($participantList->b_user_image);
            } else {
                $participantList->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
            }

            if($data['chat_room']->chat_type == 'admin'){
                $data['chat_room']->custom_name_2 = 'SellOn Support';
                $data['chat_room']->is_admin = 1;

                $data['chat_room']->custom_image = $participantList->b_user_image;
            }else if($participantList->b_user_id != $pelanggan->id && $data['chat_room']->chat_type != 'community'){
                $data['chat_room']->custom_name_2 = $participantList->b_user_fnama;
                $data['chat_room']->is_admin = $participantList->is_admin;
  
                $data['chat_room']->custom_image = $participantList->b_user_image;
            }else if($data['chat_room']->chat_type == 'community'){
                $data['chat_room']->custom_name_2 = $data['chat_room']->b_user_nama_starter;

                $data['chat_room']->custom_image = $data['chat_room']->b_user_image_starter;
            }

        }
        unset($participantList);

        //START by Donny Dennison - 7 november 2022 14:17
        //new feature, block community post or account
        if($pelanggan->id != $data['chat_room']->b_user_id_starter){

            if($data['chat_room']->chat_type == 'community'){

                $blockDataCommunity = $this->cbm->getById($nation_code, 0, $pelanggan->id, "community", $data['chat_room']->c_community_id);
                $blockDataAccount = $this->cbm->getById($nation_code, 0, $data['chat_room']->b_user_id_starter, "account", $pelanggan->id);
                $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $data['chat_room']->b_user_id_starter);

                if(isset($blockDataCommunity->block_id) || isset($blockDataAccount->block_id) || isset($blockDataAccountReverse->block_id)){

                    $this->status = 1005;
                    $this->message = "You can no longer chat as you're blocked";
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat_blocked_general");
                    die();

                }

            }

        }

        if($data['chat_room']->chat_type != 'community' && $data['chat_room']->chat_type != 'admin'){

            $blockDataAccount = $this->cbm->getById($nation_code, 0, $id_lawan_bicara, "account", $pelanggan->id);
            $blockDataAccountReverse = $this->cbm->getById($nation_code, 0, $pelanggan->id, "account", $id_lawan_bicara);

            if(isset($blockDataAccount->block_id) || isset($blockDataAccountReverse->block_id)){

                if($data['chat_room']->chat_type == 'offer'){
                    $this->status = 1005;
                    $this->message = "An offer is not allowed as you're blocked";
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat_blocked_offer");
                    die();
                }

                $this->status = 1005;
                $this->message = "You can no longer chat as you're blocked";
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat_blocked_general");
                die();

            }

        }
        //END by Donny Dennison - 7 november 2022 14:17
        //new feature, block community post or account

        $timezone = $this->input->get("timezone");

        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        $data['chat_total'] = $this->ecreadm->countAllByChatRoomIdUserId($nation_code, $chat_room_id, $pelanggan->id);

        $data['chat'] = $this->chat->getAllUnreadByChatRoomIdUserId($nation_code, $chat_room_id, $pelanggan->id, $pelanggan->language_id);

        $e_chat_ids = array();
        foreach($data['chat'] AS $chat){
            $e_chat_ids[] = $chat->chat_id;
        }
        unset($chat);

        //get complains
        $complains = array();
        if($e_chat_ids){
            $cn = $this->complain->getDetailByChatRoomID($nation_code, $chat_room_id, $e_chat_ids);
            if (count($cn)) {
                foreach ($cn as $c) {
                    // $c->c_produk_nama = $this->__dconv($c->c_produk_nama);
                    $c->c_produk_nama = html_entity_decode($c->c_produk_nama,ENT_QUOTES);

                    // START by Muhammad Sofi - 27 October 2021 10:12
                    // if user img & banner not exist or empty, change to default image
                    // $c->b_user_image_seller = $this->cdn_url($c->b_user_image_seller);
                    if(file_exists(SENEROOT.$c->b_user_image_seller) && $c->b_user_image_seller != 'media/user/default.png'){
                        $c->b_user_image_seller = $this->cdn_url($c->b_user_image_seller);
                    } else {
                        $c->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                    // $c->b_user_image_buyer = $this->cdn_url($c->b_user_image_buyer);
                    if(file_exists(SENEROOT.$c->b_user_image_buyer) && $c->b_user_image_buyer != 'media/user/default.png'){
                        $c->b_user_image_buyer = $this->cdn_url($c->b_user_image_buyer);
                    } else {
                        $c->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                    // END by Muhammad Sofi - 27 October 2021 10:12

                    $c->c_produk_foto = $this->cdn_url($c->c_produk_foto);
                    $c->c_produk_thumb = $this->cdn_url($c->c_produk_thumb);

                    $c->status_text = $this->__statusText($c, $c);

                    $key = $nation_code.'-'.$chat_room_id.'-'.$c->e_chat_id;
                    $complains[$key] = $c;
                }
            }
            unset($c,$cn); //free up some memory
        }

        //get attachment 
        $att = array();
        if($e_chat_ids){
            $attachments = $this->ecam->getDetailByChatRoomID($nation_code, $chat_room_id, $e_chat_ids);
            foreach ($attachments as $at) {
                $key = $nation_code.'-'.$chat_room_id.'-'.$at->e_chat_id;

                $at->order_invoice_code = '';
                $at->order_thumb = '';
                $at->order_user_id_seller = '';
                $at->status_text = '';

                if($at->jenis == 'product' || $at->jenis == 'barter_request' || $at->jenis == 'barter_exchange'){    

                    $at->produk_thumb = $this->cdn_url($at->produk_thumb);
                    // $at->produk_nama = $this->__dconv($at->produk_nama);
                    $at->produk_nama = html_entity_decode($at->produk_nama,ENT_QUOTES);

                }else if($at->jenis == 'order'){

                    $produk = $this->dodm->getByIdForChat($nation_code, $at->url, $at->order_detail_id);
                    $item = $this->dodim->getById($nation_code, $at->url, $at->order_detail_id, $at->order_detail_item_id);

                    if (isset($produk->c_produk_id)) {

                        $at->order_invoice_code = $produk->invoice_code;
                        $at->order_thumb = $this->cdn_url($item->thumb);
                        $at->order_user_id_seller = $produk->b_user_id_seller;
                        $at->status_text = $this->__statusText($produk, $produk);

                    }

                }else{

                    $at->url = $this->cdn_url($at->url);

                }

                //put to array key
                if (!isset($att[$key])) {
                    $att[$key] = array();
                }
                $att[$key][] = $at;
            }
            unset($at); //free some memory
            unset($attachments); //free some memory
        }

        //chat iteration
        foreach ($data['chat'] as &$ch) {
            if (isset($ch->b_user_image)) {
                // by Muhammad Sofi - 27 October 2021 10:12
                // if user img & banner not exist or empty, change to default image
                // $ch->b_user_image = $this->cdn_url($ch->b_user_image);
                if(file_exists(SENEROOT.$ch->b_user_image) && $ch->b_user_image != 'media/user/default.png'){
                    $ch->b_user_image = $this->cdn_url($ch->b_user_image);
                } else {
                    $ch->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }
            if (isset($ch->a_pengguna_foto)) {
                $ch->a_pengguna_foto = $this->cdn_url($ch->a_pengguna_foto);
            }

            //by Donny Dennison - 12 july 2022 14:56
            //new offer system
            $ch->message = $this->__changeOfferMessage($ch->type, $ch->message, $pelanggan->language_id);

            $ch->message = html_entity_decode($ch->message,ENT_QUOTES);

            // $ch->cdate_text = $this->humanTiming($ch->cdate);
            $ch->cdate_text = $this->humanTiming($ch->cdate, null, $pelanggan->language_id);

            $ch->cdate = $this->customTimezone($ch->cdate, $timezone);

            //fill attachments
            // $ch->attachments = new stdClass();
            $ch->attachments = array();
            $key = $nation_code.'-'.$chat_room_id.'-'.$ch->chat_id;
            if (isset($att[$key])) {
                // $ch->attachments = $att[$key][0];
                $ch->attachments = $att[$key];
            }

            //fill complain
            $ch->complain = new stdClass();
            $key = $nation_code.'-'.$chat_room_id.'-'.$ch->chat_id;
            if (isset($complains[$key])) {
                $ch->complain = $complains[$key];
            }

            if($data['chat_room']->chat_type == 'admin'){

                $ch->is_read_lawan_bicara = $data['chat_room']->is_read_admin;

            }else{

                $ch->is_read_lawan_bicara = $this->ecreadm->checkReadByLawanBicara($nation_code, $chat_room_id, $ch->chat_id, $pelanggan->id);
            }

        }

        $this->ecpm->setAsRead($nation_code, $chat_room_id, $pelanggan->id);

        $this->ecreadm->setAsRead($nation_code, $chat_room_id, $pelanggan->id, $e_chat_ids);

        $data['chat_id_havent_read_lawan_bicara'] = $this->ecreadm->GetUnReadByLawanBicara($nation_code, $chat_room_id, $pelanggan->id);

        //render
        $this->status = 200;
        $this->message = 'Success';
        //render
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    }

}