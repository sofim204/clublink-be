<?php
class Offer_review extends JI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        // $this->lib("seme_curl");
        // $this->load("api_mobile/a_notification_model", 'anot');
        $this->load("api_mobile/b_user_model", 'bu');
        $this->load("api_mobile/b_user_alamat_model", "bua");
        // $this->load("api_mobile/d_order_detail_model", 'dodm');
        // $this->load("api_mobile/d_order_detail_item_model", 'dodim');
        $this->load("api_mobile/d_pemberitahuan_model", 'dpem');
        $this->load("api_mobile/e_chat_model", 'chat');
        $this->load("api_mobile/e_chat_room_model", 'ecrm');
        $this->load("api_mobile/e_chat_participant_model", 'ecpm');
        // $this->load("api_mobile/e_chat_read_model", 'ecreadm');
        // $this->load("api_mobile/e_complain_model", 'complain');
        // $this->load("api_mobile/e_chat_attachment_model", 'ecam');
        $this->load("api_mobile/b_user_setting_model", "busm");
        $this->load("api_mobile/c_produk_model", 'cpm');
        $this->load("api_mobile/e_offer_review_model", 'eorm');

        //by Donny Dennison - 10 august 2022 10:10
        //new point rule for offer system
        $this->load("api_mobile/common_code_model", "ccm");
        $this->load("api_mobile/g_leaderboard_point_history_model", 'glphm');
        // $this->load("api_mobile/g_leaderboard_ranking_model", 'glrm');

        //by Donny Dennison - 02 september 2022 14:20
        //record user sales and total transaction as seller and buyer
        $this->load("api_mobile/b_user_offer_sales_model", 'buosm');

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

    public function index()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['avg_star'] = 0;
        $data['review_total'] = 0;
        $data['offer_review_total'] = 0;
        $data['offer_reviews'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
            die();
        }

        //check apisess
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

        $sort_col = $this->input->get("sort_col");
        $sort_dir = $this->input->get("sort_dir");
        $page = $this->input->get("page");
        $page_size = $this->input->get("page_size");
        $type = $this->input->get("type");
        $b_user_id = $this->input->get("b_user_id");
        $timezone = $this->input->get("timezone");

        if(empty($type)){
            $type = 'seller';
        }

        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        $tbl_as = $this->eorm->getTblAs();
        $sort_col = $this->__sortCol($sort_col, $tbl_as);
        $sort_dir = $this->__sortDir($sort_dir);
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);

        if($b_user_id <= '0'){
            //default output
            $this->status = 200;
            $this->message = 'Success';

            //render as json
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
        }

        $user = $this->bu->getById($nation_code, $b_user_id);

        if($type == "seller"){

            $data['avg_star'] = $user->offer_rating_seller_avg;

            $data['review_total'] = $user->offer_rating_seller_total;

        }else{

            $data['avg_star'] = $user->offer_rating_buyer_avg;

            $data['review_total'] = $user->offer_rating_buyer_total;

        }
        unset($user);

        $data['offer_review_total'] = $this->eorm->countAll($nation_code, $b_user_id, $type);

        $data['offer_reviews'] = $this->eorm->getAll($nation_code, $b_user_id, $page, $page_size, $sort_col, $sort_dir, $type);

        foreach ($data['offer_reviews'] as &$cr) {

            $cr->b_user_fnama = html_entity_decode($cr->b_user_fnama,ENT_QUOTES);

            // $cr->cdate_text = $this->humanTiming($cr->cdate);
            $cr->cdate_text = $this->humanTiming($cr->cdate, null, $pelanggan->language_id);

            $cr->cdate = $this->customTimezone($cr->cdate, $timezone);

            if(file_exists(SENEROOT.$cr->b_user_image) && $cr->b_user_image != 'media/user/default.png'){
                $cr->b_user_image = $this->cdn_url($cr->b_user_image);
            } else {
                $cr->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
            }

        }

        //default output
        $this->status = 200;
        $this->message = 'Success';

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
    }

    // public function detail($chat_room_id="0", $chat_type = 'private')
    // {
    //     //initial
    //     $dt = $this->__init();

    //     //default result
    //     $data = array();
    //     $data['chat_room_id'] = '';
    //     $data['chat_room'] = new stdClass();
    //     $data['participant_list'] = array();
    //     $data['participant_total'] = 0;
    //     $data['chat_total'] = 0;
    //     $data['chat'] = array();
    //     // $data['items'] = array();

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if (!$c) {
    //         $this->status = 400;
    //         $this->message = 'Missing or invalid API key';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //check apisess
    //     $apisess = $this->input->get('apisess');
    //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     if (!isset($pelanggan->id)) {
    //         $this->status = 401;
    //         $this->message = 'Missing or invalid API session';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //cek chat_room_id
    //     $chat_room_id = (int) $chat_room_id;
    //     $data['chat_room_id'] = (string) $chat_room_id;
    //     $data['chat_room'] = $this->ecrm->getChatRoomByID($nation_code, $chat_room_id, $chat_type);
    //     if ($chat_room_id<=0 || !isset($data['chat_room']->id)) {
    //         $this->status = 7280;
    //         $this->message = 'Invalid Chat Room ID';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }
        
    //     $data['chat_room']->custom_name_1 = html_entity_decode($data['chat_room']->custom_name_1,ENT_QUOTES);

    //     if (isset($data['chat_room']->b_user_image_starter)) {

    //         if(file_exists(SENEROOT.$data['chat_room']->b_user_image_starter) && $data['chat_room']->b_user_image_starter != 'media/user/default.png'){
    //             $data['chat_room']->b_user_image_starter = $this->cdn_url($data['chat_room']->b_user_image_starter);
    //         } else {
    //             $data['chat_room']->b_user_image_starter = $this->cdn_url('media/user/default-profile-picture.png');
    //         }
    //     }

    //     $sort_col = $this->input->get("sort_col");
    //     $sort_dir = $this->input->get("sort_dir");
    //     $page = $this->input->get("page");
    //     $page_size = $this->input->get("page_size");
    //     $timezone = $this->input->get("timezone");

    //     if($this->isValidTimezoneId($timezone) === false){
    //       $timezone = $this->default_timezone;
    //     }

    //     $tbl_as = $this->chat->getTblAs();
    //     // $tbl2_as = $this->chat->getTbl2As();
    //     $sort_col = $this->__sortCol($sort_col, $tbl_as);
    //     $sort_dir = $this->__sortDir($sort_dir);
    //     $page = $this->__page($page);
    //     $page_size = $this->__pageSize($page_size);

    //     // if (isset($data['chat_room']->message)) {
    //     //   $data['chat_room']->message = $this->__dconv($data['chat_room']->message);
    //     // }
        
    //     $data['participant_list'] = $this->ecpm->getParticipantByRoomChatId($nation_code,$chat_room_id);

    //     //check already in group chat or not
    //     $checkStillParticipant = 0;
    //     foreach($data['participant_list'] as $participant){

    //         if($participant->b_user_id == $pelanggan->id){
    //             $checkStillParticipant = 1;
    //             break;
    //         }

    //     }
    //     unset($participant);

    //     if ($checkStillParticipant == 0) {
    //         $this->status = 8104;
    //         $this->message = 'You can no longer chat as you exited this chat group';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     $data['participant_total'] = count($data['participant_list']);

    //     $last_delete_chat = '';
    //     foreach($data['participant_list'] as &$participantList){

    //         if($participantList->b_user_id == $pelanggan->id){
    //             $last_delete_chat = $participantList->last_delete_chat;
    //         }

    //         if(isset($participantList->b_user_image) && file_exists(SENEROOT.$participantList->b_user_image) && $participantList->b_user_image != 'media/user/default.png'){
    //             $participantList->b_user_image = $this->cdn_url($participantList->b_user_image);
    //         } else {
    //             $participantList->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
    //         }

    //         if($data['chat_room']->chat_type == 'admin'){
    //             $data['chat_room']->custom_name_2 = 'SellOn Support';

    //             $data['chat_room']->custom_image = $participantList->b_user_image;
    //         }else if($participantList->b_user_id != $pelanggan->id && $data['chat_room']->chat_type != 'community'){
    //             $data['chat_room']->custom_name_2 = $participantList->b_user_fnama;
                
    //             $data['chat_room']->custom_image = $participantList->b_user_image;
    //         }else if($data['chat_room']->chat_type == 'community'){
    //             $data['chat_room']->custom_name_2 = $data['chat_room']->b_user_nama_starter;

    //             $data['chat_room']->custom_image = $data['chat_room']->b_user_image_starter;
    //         }

    //     }
    //     unset($participantList);

    //     $data['chat_total'] = $this->chat->countAllByChatRoomId($nation_code, $chat_room_id, $last_delete_chat);

    //     $data['chat'] = $this->chat->getAllByChatRoomId($nation_code, $chat_room_id, $last_delete_chat, $page, $page_size, $sort_col, $sort_dir, $pelanggan->language_id);

    //     $e_chat_ids = array();
    //     foreach($data['chat'] AS $chat){
    //         $e_chat_ids[] = $chat->chat_id;
    //     }
    //     unset($chat);

    //     //get complains
    //     $complains = array();
    //     if($e_chat_ids){
    //         $cn = $this->complain->getDetailByChatRoomID($nation_code, $chat_room_id, $e_chat_ids);
    //         if (count($cn)) {
    //             foreach ($cn as $c) {
    //                 // $c->c_produk_nama = $this->__dconv($c->c_produk_nama);
    //                 $c->c_produk_nama = html_entity_decode($c->c_produk_nama,ENT_QUOTES);

    //                 // START by Muhammad Sofi - 27 October 2021 10:12
    //                 // if user img & banner not exist or empty, change to default image
    //                 // $c->b_user_image_seller = $this->cdn_url($c->b_user_image_seller);
    //                 if(file_exists(SENEROOT.$c->b_user_image_seller) && $c->b_user_image_seller != 'media/user/default.png'){
    //                     $c->b_user_image_seller = $this->cdn_url($c->b_user_image_seller);
    //                 } else {
    //                     $c->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
    //                 }
    //                 // $c->b_user_image_buyer = $this->cdn_url($c->b_user_image_buyer);
    //                 if(file_exists(SENEROOT.$c->b_user_image_buyer) && $c->b_user_image_buyer != 'media/user/default.png'){
    //                     $c->b_user_image_buyer = $this->cdn_url($c->b_user_image_buyer);
    //                 } else {
    //                     $c->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
    //                 }
    //                 // END by Muhammad Sofi - 27 October 2021 10:12

    //                 $c->c_produk_foto = $this->cdn_url($c->c_produk_foto);
    //                 $c->c_produk_thumb = $this->cdn_url($c->c_produk_thumb);

    //                 $c->status_text = $this->__statusText($c, $c);

    //                 $key = $nation_code.'-'.$chat_room_id.'-'.$c->e_chat_id;
    //                 $complains[$key] = $c;
    //             }
    //         }
    //         unset($c,$cn); //free up some memory
    //     }

    //     //get attachment 
    //     $att = array();
    //     if($e_chat_ids){
    //         $attachments = $this->ecam->getDetailByChatRoomID($nation_code, $chat_room_id, $e_chat_ids);
    //         foreach ($attachments as $at) {
    //             $key = $nation_code.'-'.$chat_room_id.'-'.$at->e_chat_id;
                
    //             $at->order_invoice_code = '';
    //             $at->order_thumb = '';
    //             $at->order_user_id_seller = '';
    //             $at->status_text = '';

    //             if($at->jenis == 'product' || $at->jenis == 'barter_request' || $at->jenis == 'barter_exchange'){    

    //                 $at->produk_thumb = $this->cdn_url($at->produk_thumb);
    //                 // $at->produk_nama = $this->__dconv($at->produk_nama);
    //                 $at->produk_nama = html_entity_decode($at->produk_nama,ENT_QUOTES);

    //             }else if($at->jenis == 'order'){

    //                 $produk = $this->dodm->getByIdForChat($nation_code, $at->url, $at->order_detail_id);
    //                 $item = $this->dodim->getById($nation_code, $at->url, $at->order_detail_id, $at->order_detail_item_id);

    //                 if (isset($produk->c_produk_id)) {

    //                     $at->order_invoice_code = $produk->invoice_code;
    //                     $at->order_thumb = $this->cdn_url($item->thumb);
    //                     $at->order_user_id_seller = $produk->b_user_id_seller;
    //                     $at->status_text = $this->__statusText($produk, $produk);

    //                 }

    //             }else{

    //                 $at->url = $this->cdn_url($at->url);

    //             }

    //             //put to array key
    //             if (!isset($att[$key])) {
    //                 $att[$key] = array();
    //             }
    //             $att[$key][] = $at;
    //         }
    //         unset($at); //free some memory
    //         unset($attachments); //free some memory
    //     }

    //     //chat iteration
    //     foreach ($data['chat'] as &$ch) {

    //         $ch->message = $this->__changeOfferMessage($ch->type, $ch->message, $pelanggan->language_id);

    //         $ch->message = html_entity_decode($ch->message,ENT_QUOTES);

    //         if (isset($ch->b_user_image)) {
    //             // by Muhammad Sofi - 27 October 2021 10:12
    //             // if user img & banner not exist or empty, change to default image
    //             // $ch->b_user_image = $this->cdn_url($ch->b_user_image);
    //             if(file_exists(SENEROOT.$ch->b_user_image) && $ch->b_user_image != 'media/user/default.png'){
    //                 $ch->b_user_image = $this->cdn_url($ch->b_user_image);
    //             } else {
    //                 $ch->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
    //             }
    //         }
    //         if (isset($ch->a_pengguna_foto)) {
    //             $ch->a_pengguna_foto = $this->cdn_url($ch->a_pengguna_foto);
    //         }

    //         $ch->cdate_text = $this->humanTiming($ch->cdate);

    //         $ch->cdate = $this->customTimezone($ch->cdate, $timezone);

    //         //fill attachments
    //         // $ch->attachments = new stdClass();
    //         $ch->attachments = array();
    //         $key = $nation_code.'-'.$chat_room_id.'-'.$ch->chat_id;
    //         if (isset($att[$key])) {
    //             // $ch->attachments = $att[$key][0];
    //             $ch->attachments = $att[$key];
    //         }

    //         //fill complain
    //         $ch->complain = new stdClass();
    //         $key = $nation_code.'-'.$chat_room_id.'-'.$ch->chat_id;
    //         if (isset($complains[$key])) {
    //             $ch->complain = $complains[$key];
    //         }

    //         if($data['chat_room']->chat_type == 'admin'){
                
    //             $ch->is_read_lawan_bicara = $data['chat_room']->is_read_admin;

    //         }else{
                
    //             $ch->is_read_lawan_bicara = $this->ecreadm->checkReadByLawanBicara($nation_code, $chat_room_id, $ch->chat_id, $pelanggan->id);;
    //         }

    //     }

    //     $this->ecpm->setAsRead($nation_code, $chat_room_id, $pelanggan->id);

    //     $this->ecreadm->setAsRead($nation_code, $chat_room_id, $pelanggan->id);

    //     //render
    //     $this->status = 200;
    //     $this->message = 'Success';
    //     //render
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    // }

    // public function checkparticipantstatus()
    // {
    //     //default result
    //     $data = array();
    //     $data['chat_room_id'] = $this->input->get('chat_room_id');
    //     $data['chat_room'] = new stdClass();
    //     $data['participant_list'] = array();
    //     $data['is_leave'] = '0';

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if (!$c) {
    //         $this->status = 400;
    //         $this->message = 'Missing or invalid API key';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //check apisess
    //     $apisess = $this->input->get('apisess');
    //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     if (!isset($pelanggan->id)) {
    //         $this->status = 401;
    //         $this->message = 'Missing or invalid API session';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //cek chat_room_id
    //     $chat_room_id = $this->input->get('chat_room_id');
    //     $data['chat_room_id'] = $chat_room_id;
    //     $data['chat_room'] = $this->ecrm->getChatRoomByID($nation_code, $chat_room_id);
    //     if ($chat_room_id<=0 || !isset($data['chat_room']->id)) {
    //         $this->status = 7280;
    //         $this->message = 'Invalid Chat Room ID';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }
        
    //     $data['participant_list'] = $this->ecpm->getParticipantByRoomChatId($nation_code,$chat_room_id);

    //     //check already in group chat or not
    //     if($data['chat_room']->c_community_id > 0 && $data['chat_room']->chat_type == 'community'){

    //         $alreadyInGroupChat = 0;
    //         foreach($data['participant_list'] as $participantList){
                
    //             if($participantList->b_user_id == $pelanggan->id){
    //                 $alreadyInGroupChat = 1;
    //                 break;
    //             }

    //         }
    //         unset($participantList);

    //         if($alreadyInGroupChat == 0){
    //             $data['is_leave'] = '1';
    //         }

    //     }
        
    //     unset($data['chat_room_id'], $data['chat_room'], $data['participant_list']);

    //     //render
    //     $this->status = 200;
    //     // $this->message = 'You can no longer chat as you exited this chat group';
    //     $this->message = 'Success';
    //     //render
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    // }

    public function baru()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        // $data['chat'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
            die();
        }

        //collect chat_room_id
        $chat_room_id = $this->input->post('chat_room_id');
        if ($chat_room_id <= '0') {
            $this->status = 7280;
            $this->message = 'Invalid Chat Room ID or Chat Room ID not registered';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
            die();
        }

        $roomChat = $this->ecrm->getChatRoomByID($nation_code, $chat_room_id, "offer");
        if (!isset($roomChat->nation_code)) {
            $this->status = 7280;
            $this->message = 'Invalid Chat Room ID or Chat Room ID not registered';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
            die();
        }

        if($roomChat->b_user_id_seller != $pelanggan->id){

            $type = "seller";

        }else{

            $type = "buyer";

        }

        $chatAcceptedType = $this->chat->getLastAcceptedByChatRoomId($nation_code, $chat_room_id);

        $reviewExist = $this->eorm->getByChatRoomId($nation_code, $chat_room_id, $chatAcceptedType->id, $type);
        if (isset($reviewExist->chat_room_id)) {
            $this->status = 7281;
            $this->message = 'this user have already review';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
            die();
        }

        $star = $this->input->post('star');
        if ($star <= 0 && $star >= 6) {
            $this->status = 7282;
            $this->message = 'rate from 1 to 5';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
            die();
        }

        //collect message
        $message = trim(((empty($this->input->post('message')))? "" : $this->input->post('message')));
        // if (!strlen($message)>0) {
        //     $this->status = 8105;
        //     $this->message = 'Message is empty';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
        //     die();
        // }

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
        $this->eorm->trans_start();

        //get last id
        $review_id = $this->eorm->getLastId($nation_code);

        //insert into database
        $di = array();
        $di['id'] = $review_id;
        $di['nation_code'] = $nation_code;
        $di['e_chat_room_id'] = $chat_room_id;
        $di['e_chat_id'] = $chatAcceptedType->id;
        $di['b_user_id'] = $pelanggan->id;

        if($type == "seller"){
            $di['b_user_id_to'] = $roomChat->b_user_id_seller;
        }else{
            $di['b_user_id_to'] = $roomChat->b_user_id_starter;
        }

        $di['type'] = $type;
        $di['star'] = $star;
        $di['review'] = $message;
        $di['cdate'] = "NOW()";

        $res = $this->eorm->set($di);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
            // $this->eorm->trans_commit();

            $reviewExistBuyer = $this->eorm->getByChatRoomId($nation_code, $chat_room_id, $chatAcceptedType->id, "seller");

            $reviewExistSeller = $this->eorm->getByChatRoomId($nation_code, $chat_room_id, $chatAcceptedType->id, "buyer");

            if(isset($reviewExistBuyer->chat_room_id) && isset($reviewExistSeller->chat_room_id)){
                $offer_status = "reviewed";
            }else if(isset($reviewExistBuyer->chat_room_id)){
                $offer_status = "waiting review from seller";
            }else{
                $offer_status = "waiting review from buyer";
            }

            $du = array();
            $du['offer_status'] = $offer_status;
            $du['offer_status_update_date'] = "NOW()";
            $this->ecrm->update($nation_code, $chat_room_id, $du);
            // $this->eorm->trans_commit();

            if($type == "seller"){

                $this->bu->updateTotal($nation_code, $roomChat->b_user_id_seller, "offer_rating_seller_total", "+", 1);
                // $this->eorm->trans_commit();

                $avg_star = $this->eorm->getAvg($nation_code, $roomChat->b_user_id_seller, "seller")->avg_star;

                $du = array();
                $du['offer_rating_seller_avg'] = $avg_star;
                $this->bu->update($nation_code, $roomChat->b_user_id_seller, $du);
                // $this->eorm->trans_commit();

            }else{

                $this->bu->updateTotal($nation_code, $roomChat->b_user_id_starter, "offer_rating_buyer_total", "+", 1);
                // $this->eorm->trans_commit();

                $avg_star = $this->eorm->getAvg($nation_code, $roomChat->b_user_id_starter, "buyer")->avg_star;

                $du = array();
                $du['offer_rating_buyer_avg'] = $avg_star;
                $this->bu->update($nation_code, $roomChat->b_user_id_starter, $du);
                // $this->eorm->trans_commit();

            }

            //START by Donny Dennison - 10 august 2022 10:10
            //new point rule for offer system
            $getProductType = $this->cpm->getProductType($nation_code, $roomChat->c_produk_id);
            $getProductType = $getProductType->product_type;

            $produk = $this->cpm->getById($nation_code, $roomChat->c_produk_id, $pelanggan, $getProductType);

            //buyer
            if($offer_status == "reviewed"){

                // $checkAlreadyGetPointBuyer = $this->glphm->checkAlreadyInDB($nation_code, "", "", "", "", "", $roomChat->b_user_id_starter, $chat_room_id, "offer", "review", "");

                // $checkAlreadyGetPointSeller = $this->glphm->checkAlreadyInDB($nation_code, "", "", "", "", "", $roomChat->b_user_id_seller, $chat_room_id, "offer", "review", "");

                // if($produk->product_type != "Free"){

                //     if(!isset($checkAlreadyGetPointBuyer->id) && !isset($checkAlreadyGetPointSeller->id)){

                //         //get point
                //         $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "ER");
                //         if (!isset($pointGet->remark)) {
                //           $pointGet = new stdClass();
                //           $pointGet->remark = 30;
                //         }

                //         $user = $this->bu->getById($nation_code, $roomChat->b_user_id_starter);

                //         $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $roomChat->b_user_id_starter);

                //         $leaderBoardHistoryId = $this->glphm->getLastId($nation_code, $roomChat->b_user_id_starter, $pelangganAddress->kelurahan, $pelangganAddress->kecamatan, $pelangganAddress->kabkota, $pelangganAddress->provinsi);

                //         $checkAlreadyInDB = $this->glphm->checkAlreadyInDB($nation_code, $leaderBoardHistoryId, $pelangganAddress->kelurahan, $pelangganAddress->kecamatan, $pelangganAddress->kabkota, $pelangganAddress->provinsi, $roomChat->b_user_id_starter, $chat_room_id, "offer", "review", date("Y-m-d"));
                //         if(!isset($checkAlreadyInDB->id)){
                //             $di = array();
                //             $di['nation_code'] = $nation_code;
                //             $di['id'] = $leaderBoardHistoryId;
                //             $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
                //             $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
                //             $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
                //             $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
                //             $di['b_user_id'] = $roomChat->b_user_id_starter;
                //             $di['point'] = $pointGet->remark;
                //             $di['custom_id'] = $chat_room_id;
                //             $di['custom_type'] = 'offer';
                //             $di['custom_type_sub'] = 'review';
                //             $di['custom_text'] = $user->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
                //             $this->glphm->set($di);
                //             // $this->eorm->trans_commit();
                //             // $this->glrm->updateTotal($nation_code, $roomChat->b_user_id_starter, 'total_point', '+', $di['point']);
                //             // $this->eorm->trans_commit();
                //             // $this->glrm->updateTotal($nation_code, $roomChat->b_user_id_starter, 'total_post', '+', 1);
                //             // $this->eorm->trans_commit();

                //         }

                //     }

                // }

                //START by Donny Dennison - 02 september 2022 14:20
                //record user sales and total transaction as seller and buyer
                if($produk->product_type != "Free"){

                    $ExistingData = $this->buosm->getByUserId($nation_code, $roomChat->b_user_id_starter, date("Y"), date("m"));
                    if(!isset($ExistingData->b_user_id)){

                        // $lastId = $this->buosm->getLastId($nation_code);
                        $di = array();
                        $di['nation_code'] = $nation_code;
                        // $di['id'] = $lastId;
                        $di['b_user_id'] = $roomChat->b_user_id_starter;
                        $di['year'] = date("Y");
                        $di['month'] = date("m");
                        $di['total_sales_buyer'] = $chatAcceptedType->message;
                        $di['total_transaction_buyer'] = 1;
                        $this->buosm->set($di);
                        // $this->eorm->trans_commit();

                    }else{

                        $this->buosm->updateTotal($nation_code, $roomChat->b_user_id_starter, date("Y"), date("m"), "total_sales_buyer", "+", $chatAcceptedType->message);
                        // $this->eorm->trans_commit();

                        $this->buosm->updateTotal($nation_code, $roomChat->b_user_id_starter, date("Y"), date("m"), "total_transaction_buyer", "+", 1);
                        // $this->eorm->trans_commit();

                    }

                }else{

                    $ExistingData = $this->buosm->getByUserId($nation_code, $roomChat->b_user_id_starter, date("Y"), date("m"));
                    if(!isset($ExistingData->b_user_id)){

                        // $lastId = $this->buosm->getLastId($nation_code);
                        $di = array();
                        $di['nation_code'] = $nation_code;
                        // $di['id'] = $lastId;
                        $di['b_user_id'] = $roomChat->b_user_id_starter;
                        $di['year'] = date("Y");
                        $di['month'] = date("m");
                        $di['total_sales_buyer'] = 0;
                        $di['total_transaction_buyer'] = 1;
                        $this->buosm->set($di);
                        // $this->eorm->trans_commit();

                    }else{

                        $this->buosm->updateTotal($nation_code, $roomChat->b_user_id_starter, date("Y"), date("m"), "total_transaction_buyer", "+", 1);
                        // $this->eorm->trans_commit();

                    }

                }
                //END by Donny Dennison - 02 september 2022 14:20
                //record user sales and total transaction as seller and buyer

            }

            //seller
            if($offer_status == "reviewed"){

                // if(!isset($checkAlreadyGetPointBuyer->id) && !isset($checkAlreadyGetPointSeller->id)){

                //     if($produk->product_type == "Free"){

                //         //get point
                //         $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "ES");
                //         if (!isset($pointGet->remark)) {
                //           $pointGet = new stdClass();
                //           $pointGet->remark = 50;
                //         }

                //     }else{

                //         //get point
                //         $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EQ");
                //         if (!isset($pointGet->remark)) {
                //           $pointGet = new stdClass();
                //           $pointGet->remark = 30;
                //         }

                //     }

                //     $user = $this->bu->getById($nation_code, $roomChat->b_user_id_seller);

                //     $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $roomChat->b_user_id_seller);

                //     $leaderBoardHistoryId = $this->glphm->getLastId($nation_code, $roomChat->b_user_id_seller, $pelangganAddress->kelurahan, $pelangganAddress->kecamatan, $pelangganAddress->kabkota, $pelangganAddress->provinsi);

                //     $checkAlreadyInDB = $this->glphm->checkAlreadyInDB($nation_code, $leaderBoardHistoryId, $pelangganAddress->kelurahan, $pelangganAddress->kecamatan, $pelangganAddress->kabkota, $pelangganAddress->provinsi, $roomChat->b_user_id_seller, $chat_room_id, "offer", "review", date("Y-m-d"));
                //     if(!isset($checkAlreadyInDB->id)){
                //         $di = array();
                //         $di['nation_code'] = $nation_code;
                //         $di['id'] = $leaderBoardHistoryId;
                //         $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
                //         $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
                //         $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
                //         $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
                //         $di['b_user_id'] = $roomChat->b_user_id_seller;
                //         $di['point'] = $pointGet->remark;
                //         $di['custom_id'] = $chat_room_id;
                //         $di['custom_type'] = 'offer';

                //         if($produk->product_type == "Free"){
                //             $di['custom_type_sub'] = 'review free product';
                //         }else{
                //             $di['custom_type_sub'] = 'review';
                //         }

                //         $di['custom_text'] = $user->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
                //         $this->glphm->set($di);
                //         // $this->eorm->trans_commit();
                //         // $this->glrm->updateTotal($nation_code, $roomChat->b_user_id_seller, 'total_point', '+', $di['point']);
                //         // $this->eorm->trans_commit();
                //         // $this->glrm->updateTotal($nation_code, $roomChat->b_user_id_seller, 'total_post', '+', 1);
                //         // $this->eorm->trans_commit();
                //     }
                // }

                //START by Donny Dennison - 02 september 2022 14:20
                //record user sales and total transaction as seller and buyer
                if($produk->product_type != "Free"){
                    $ExistingData = $this->buosm->getByUserId($nation_code, $roomChat->b_user_id_seller, date("Y"), date("m"));
                    if(!isset($ExistingData->b_user_id)){
                        // $lastId = $this->buosm->getLastId($nation_code);
                        $di = array();
                        $di['nation_code'] = $nation_code;
                        // $di['id'] = $lastId;
                        $di['b_user_id'] = $roomChat->b_user_id_seller;
                        $di['year'] = date("Y");
                        $di['month'] = date("m");
                        $di['total_sales_seller'] = $chatAcceptedType->message;
                        $di['total_transaction_seller'] = 1;
                        $this->buosm->set($di);
                        // $this->eorm->trans_commit();

                    }else{

                        $this->buosm->updateTotal($nation_code, $roomChat->b_user_id_seller, date("Y"), date("m"), "total_sales_seller", "+", $chatAcceptedType->message);
                        // $this->eorm->trans_commit();

                        $this->buosm->updateTotal($nation_code, $roomChat->b_user_id_seller, date("Y"), date("m"), "total_transaction_seller", "+", 1);
                        // $this->eorm->trans_commit();

                    }

                }else{

                    $ExistingData = $this->buosm->getByUserId($nation_code, $roomChat->b_user_id_seller, date("Y"), date("m"));
                    if(!isset($ExistingData->b_user_id)){

                        // $lastId = $this->buosm->getLastId($nation_code);
                        $di = array();
                        $di['nation_code'] = $nation_code;
                        // $di['id'] = $lastId;
                        $di['b_user_id'] = $roomChat->b_user_id_seller;
                        $di['year'] = date("Y");
                        $di['month'] = date("m");
                        $di['total_sales_seller'] = 0;
                        $di['total_transaction_seller'] = 1;
                        $this->buosm->set($di);
                        // $this->eorm->trans_commit();

                    }else{

                        $this->buosm->updateTotal($nation_code, $roomChat->b_user_id_seller, date("Y"), date("m"), "total_transaction_seller", "+", 1);
                        // $this->eorm->trans_commit();

                    }

                }
                //END by Donny Dennison - 02 september 2022 14:20
                //record user sales and total transaction as seller and buyer

            }
            //END by Donny Dennison - 10 august 2022 10:10
            //new point rule for offer system

            $roomChat->c_produk_nama = html_entity_decode($roomChat->c_produk_nama,ENT_QUOTES);

            //get missing data
            if($type == "seller"){
                $sender = $this->bu->getById($nation_code, $roomChat->b_user_id_starter);
                $receiver = $this->bu->getById($nation_code, $roomChat->b_user_id_seller);
            }else{
                $sender = $this->bu->getById($nation_code, $roomChat->b_user_id_seller);
                $receiver = $this->bu->getById($nation_code, $roomChat->b_user_id_starter);
            }

            $dpe = array();
            $dpe['nation_code'] = $nation_code;
            $dpe['b_user_id'] = $receiver->id;
            $dpe['id'] = $this->dpem->getLastId($nation_code, $receiver->id);
            $dpe['type'] = "offer_review";

            if($type == "seller"){
                if($receiver->language_id == 2) {
                    $dpe['judul'] = "Ulasan Penawaran";
                    $dpe['teks'] =  "Pembeli telah meninggalkan ulasan di ".$roomChat->c_produk_nama;
                } else {
                    $dpe['judul'] = "Offer Review";
                    $dpe['teks'] =  "Buyer's review for ".$roomChat->c_produk_nama. " is done now";
                }
            }else{
                if($receiver->language_id == 2) {
                    $dpe['judul'] = "Ulasan Penawaran";
                    $dpe['teks'] =  "Penjual telah meninggalkan ulasan di ".$roomChat->c_produk_nama;
                } else {
                    $dpe['judul'] = "Offer Review";
                    $dpe['teks'] =  "Seller's review for ".$roomChat->c_produk_nama. " is done now";
                }
            }

            $dpe['gambar'] = 'media/pemberitahuan/outbounding.png';
            $dpe['cdate'] = "NOW()";
            $extras = new stdClass();
            // $extras->chat_room_id = $roomChat->id;
            $extras->c_produk_id = $roomChat->c_produk_id;
            // $extras->c_produk_nama = $roomChat->c_produk_nama;
            // $extras->b_user_id_starter = $roomChat->b_user_id_starter;
            // $extras->b_user_id_seller = $roomChat->b_user_id_seller;

            if($type == "seller"){
                if($receiver->language_id == 2) {
                    $extras->judul = "Ulasan Penawaran";
                    $extras->teks =  "Pembeli telah meninggalkan ulasan di ".$roomChat->c_produk_nama;
                } else {
                    $extras->judul = "Offer Review";
                    $extras->teks =  "Buyer's review for ".$roomChat->c_produk_nama. " is done now";
                }
            }else{
                if($receiver->language_id == 2) {
                    $extras->judul = "Ulasan Penawaran";
                    $extras->teks =  "Penjual telah meninggalkan ulasan di ".$roomChat->c_produk_nama;
                } else {
                    $extras->judul = "Offer Review";
                    $extras->teks =  "Seller's review for ".$roomChat->c_produk_nama. " is done now";
                }
            }

            $dpe['extras'] = json_encode($extras);
            $this->dpem->set($dpe);
            // $this->eorm->trans_commit();

            $classified = 'setting_notification_user';
            $code = 'U5';

            $receiverSettingNotif = $this->busm->getValue($nation_code, $receiver->id, $classified, $code);

            if (!isset($receiverSettingNotif->setting_value)) {
                $receiverSettingNotif->setting_value = 0;
            }

            //push notif
            if ($receiverSettingNotif->setting_value == 1 && $receiver->is_active == 1) {

                if (strlen($receiver->fcm_token)>50) {
                    $device = $receiver->device; //jenis device
                    $tokens = array($receiver->fcm_token); //device token

                    if($type == "seller"){
                        if($receiver->language_id == 2) {
                            $title = "Ulasan Penawaran";
                            $message =  "Pembeli telah meninggalkan ulasan di ".$roomChat->c_produk_nama;
                        } else {
                            $title = "Offer Review";
                            $message =  "Buyer's review for ".$roomChat->c_produk_nama. " is done now";
                        }
                    }else{
                        if($receiver->language_id == 2) {
                            $title = "Ulasan Penawaran";
                            $message =  "Penjual telah meninggalkan ulasan di ".$roomChat->c_produk_nama;
                        } else {
                            $title = "Offer Review";
                            $message =  "Seller's review for ".$roomChat->c_produk_nama. " is done now";
                        }
                    }

                    $type = 'offer_review';
                    $image = 'media/pemberitahuan/outbounding.png';
                    $payload = new stdClass();
                    // $payload->chat_room_id = $roomChat->id;
                    $payload->c_produk_id = $roomChat->c_produk_id;
                    // $payload->c_produk_nama = $roomChat->c_produk_nama;
                    // $payload->b_user_id_starter = $roomChat->b_user_id_starter;
                    // $payload->b_user_id_seller = $roomChat->b_user_id_seller;

                    $image = $this->cdn_url($image);
                    $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
                }

            }

        } else {
            $this->eorm->trans_rollback();
            $this->status = 8011;
            $this->message = 'Failed updating data';
        }
        $this->eorm->trans_commit();
        $this->eorm->trans_end();
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
    }

    // public function getchatroomid()
    // {
    //     //initial
    //     $dt = $this->__init();

    //     //default result
    //     $data = array();
    //     $data['chat_room_id'] = 0;

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if (!$c) {
    //         $this->status = 400;
    //         $this->message = 'Missing or invalid API key';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //check apisess
    //     $apisess = $this->input->get('apisess');
    //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     if (!isset($pelanggan->id)) {
    //         $this->status = 401;
    //         $this->message = 'Missing or invalid API session';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //collect b_user_id_to
    //     $b_user_id_to = (int) $this->input->post('b_user_id_to');
    //     if ($b_user_id_to<=0) {
    //         $this->status = 8101;
    //         $this->message = 'Invalid B User ID To';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     $chat_type = $this->input->post('chat_type');

    //     if ($chat_type != 'buyandsell' && $chat_type != 'private' && $chat_type != 'barter') {
    //         $this->status = 8102;
    //         $this->message = 'Chat type must be buyandsell or private or barter';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //check already have room or not
    //     $checkRoomChat = $this->ecpm->getRoomChatIDByParticipantId($nation_code, $pelanggan->id, $b_user_id_to, $chat_type);
    //     if (isset($checkRoomChat->nation_code)) {
            
    //         $data['chat_room_id'] = $checkRoomChat->e_chat_room_id;

    //     }   

    //     $this->status = 200;
    //     $this->message = 'Success';
            
    //     $this->chat->trans_end();
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    // }

    // public function read($chat_room_id="", $chat_type= "private")
    // {
    //     //initial
    //     $dt = $this->__init();

    //     //default result
    //     $data = array();

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if (!$c) {
    //         $this->status = 400;
    //         $this->message = 'Missing or invalid API key';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //check apisess
    //     $apisess = $this->input->get('apisess');
    //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     if (!isset($pelanggan->id)) {
    //         $this->status = 401;
    //         $this->message = 'Missing or invalid API session';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //cek d_order_id
    //     $chat_room_id = (int) $chat_room_id;
    //     if ($chat_room_id<=0) {
    //         $this->status = 7280;
    //         $this->message = 'Invalid Chat Room ID or Chat Room ID not registered';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     $this->ecpm->setAsRead($nation_code, $chat_room_id, $pelanggan->id);

    //     $this->ecreadm->setAsRead($nation_code, $chat_room_id, $pelanggan->id);

    //     //render to json
    //     $this->status = 200;
    //     $this->message = 'Success';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    // }

    // public function readall()
    // {
    //     //initial
    //     $dt = $this->__init();

    //     //default result
    //     $data = array();

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if (!$c) {
    //         $this->status = 400;
    //         $this->message = 'Missing or invalid API key';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //check apisess
    //     $apisess = $this->input->get('apisess');
    //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     if (!isset($pelanggan->id)) {
    //         $this->status = 401;
    //         $this->message = 'Missing or invalid API session';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     $this->ecpm->setAsReadAll($nation_code, $pelanggan->id);

    //     $this->ecreadm->setAsReadAll($nation_code, $pelanggan->id);

    //     //render to json
    //     $this->status = 200;
    //     $this->message = 'Success';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    // }

    // public function hapus(){
    //     //initial
    //     $dt = $this->__init();

    //     //default result
    //     $data = array();
    //     $data['chat_unread'] = "0";
    //     $data['chat_room'] = array();

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if(empty($nation_code)){
    //       $this->status = 101;
    //       $this->message = 'Missing or invalid nation_code';
    //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //       die();
    //     }

    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if(!$c){
    //       $this->status = 400;
    //       $this->message = 'Missing or invalid API key';
    //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //       die();
    //     }

    //     //check apisess
    //     $apisess = $this->input->get('apisess');
    //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     if(!isset($pelanggan->id)){
    //       $this->status = 401;
    //       $this->message = 'Missing or invalid API session';
    //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //       die();
    //     }

    //     $post_data_json = $this->input->post("post_data");
    //     $post_data = json_decode($post_data_json);
    //     if (!is_array($post_data)) {
    //         $this->status = 829;
    //         $this->message = 'post_data must be an array';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }
    //     if (count($post_data)<=0) {
    //         $this->status = 830;
    //         $this->message = 'Please add at least one chat room on post_data';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //delete chat
    //     foreach ($post_data as $pdp) {

    //         //get chat room
    //         $roomChat = $this->ecrm->getChatRoomByID($nation_code, $pdp->chat_room_id);

    //         $du = array();
    //         $du['last_delete_chat'] = date('Y-m-d H:i:s');

    //         //update table e_chat_participant
    //         $this->ecpm->update($nation_code, $pdp->chat_room_id, $pelanggan->id, $du);

    //         $this->ecpm->setAsRead($nation_code, $pdp->chat_room_id, $pelanggan->id);
            
    //         $this->ecreadm->setAsRead($nation_code, $pdp->chat_room_id, $pelanggan->id);

    //     }

    //     $url = base_url("api_mobile/chat/?apikey=$apikey&nation_code=$nation_code&apisess=$apisess");
    //     $res = $this->seme_curl->get($url);
        
    //     $body = json_decode($res->body);
    //     $chat_room = $body->data;

    //     //default output
    //     $this->status = 200;
    //     $this->message = 'Success';
    //     $data['chat_room'] = $chat_room;
    //     unset($chat_room);
    //     //get unread count
    //     $data['chat_unread'] = "".$this->ecpm->countUnread($nation_code,$pelanggan->id);

    //     //render as json
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    // }

    // public function keluar(){
    //     //initial
    //     $dt = $this->__init();

    //     //default result
    //     $data = array();
    //     $data['chat_unread'] = "0";
    //     $data['chat_room'] = array();

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if(empty($nation_code)){
    //       $this->status = 101;
    //       $this->message = 'Missing or invalid nation_code';
    //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //       die();
    //     }

    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if(!$c){
    //       $this->status = 400;
    //       $this->message = 'Missing or invalid API key';
    //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //       die();
    //     }

    //     //check apisess
    //     $apisess = $this->input->get('apisess');
    //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     if(!isset($pelanggan->id)){
    //       $this->status = 401;
    //       $this->message = 'Missing or invalid API session';
    //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //       die();
    //     }

    //     $chat_room_id = (int) $this->input->post("chat_room_id");
    //     if ($chat_room_id<=0) {
    //         $this->status = 831;
    //         $this->message = 'missing chat_room_id or chat room not community';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //get chat room
    //     $roomChat = $this->ecrm->getChatRoomByID($nation_code, $chat_room_id);

    //     if($roomChat->chat_type != 'community'){
            
    //         $this->status = 831;
    //         $this->message = 'missing chat_room_id or chat room not community';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();

    //     }else{

    //         $type = 'chat';
    //         $replacer = array();

    //         $replacer['user_nama'] = $pelanggan->fnama;
    //         $message = '';
    //         $message_indonesia = '';

    //         $nw = $this->anot->get($nation_code, "push", $type, 3, 1);
    //         if (isset($nw->message)) {
    //           $message = $this->__nRep($nw->message, $replacer);
    //         }

    //         $nw = $this->anot->get($nation_code, "push", $type, 3, 2);
    //         if (isset($nw->message)) {
    //           $message_indonesia = $this->__nRep($nw->message, $replacer);
    //         }

    //         //get last chat id
    //         $chat_id = $this->chat->getLastId($nation_code, $roomChat->id);

    //         $di = array();
    //         $di['id'] = $chat_id;
    //         $di['nation_code'] = $nation_code;
    //         $di['e_chat_room_id'] = $roomChat->id;
    //         $di['b_user_id'] = 0;
    //         $di['type'] = 'announcement';
    //         $di['message'] = $message;
    //         $di['message_indonesia'] = $message_indonesia;
    //         $di['cdate'] = "NOW()";
    //         $this->chat->set($di);

    //         $participant_list = $this->ecpm->getParticipantByRoomChatId($nation_code,$chat_room_id);
            
    //         //set unread in table e_chat_read
    //         foreach($participant_list AS $participant){
                
    //             $du = array();
    //             $du['nation_code'] = $nation_code;
    //             $du['b_user_id'] = $participant->b_user_id;
    //             $du['e_chat_room_id'] = $roomChat->id;
    //             $du['e_chat_id'] = $chat_id;
    //             if($participant->b_user_id == $pelanggan->id){
    //                 $du['is_read'] = 1;
    //             }
    //             $du['cdate'] = "NOW()";

    //             $this->ecreadm->set($du);

    //         }
    //         unset($participant_list, $participant);

    //     }
        
    //     $du = array();
    //     $du['last_delete_chat'] = date('Y-m-d H:i:s');

    //     if($roomChat->chat_type == 'community'){
    //         $du['is_active'] = 0;
    //     }

    //     //update table e_chat_participant
    //     $this->ecpm->update($nation_code, $chat_room_id, $pelanggan->id, $du);

    //     $this->ecpm->setAsRead($nation_code, $chat_room_id, $pelanggan->id);
        
    //     $this->ecreadm->setAsRead($nation_code, $chat_room_id, $pelanggan->id);

    //     // by Muhammad Sofi - 12 November 2021 16:32 
    //     // remove subquery get total_people_group_chat, update data on join and leave user
    //     $this->ccm->updateTotalPeople($nation_code, $chat_room_id, '-', 1);

    //     //send push notif
    //     $ownerCommunity = $this->bu->getById($nation_code, $roomChat->b_user_id_starter);
    //     $sender = $this->bu->getById($nation_code, $pelanggan->id);
    //     $participant_list = $this->ecpm->getParticipantByRoomChatId($nation_code,$chat_room_id);
    //     $ios = array();
    //     $android = array();

    //     foreach($participant_list as $participant){

    //         if($participant->b_user_id != $pelanggan->id){
    //             $receiver = $this->bu->getById($nation_code, $participant->b_user_id);

    //             $classified = 'setting_notification_user';
    //             $code = 'U4';

    //             $receiverSettingNotif = $this->busm->getValue($nation_code, $participant->b_user_id, $classified, $code);

    //             if (!isset($receiverSettingNotif->setting_value)) {
    //                 $receiverSettingNotif->setting_value = 0;
    //             }

    //             //push notif
    //             if ($receiverSettingNotif->setting_value == 1 && $receiver->is_active == 1) {
                    
    //                 if (strtolower($receiver->device) == 'ios') {
    //                   $ios[] = $receiver->fcm_token;
    //                 } else {
    //                   $android[] = $receiver->fcm_token;
    //                 }

    //             }
    //         }

    //     }

    //     $type = 'chat';
    //     $anotid = 3;
    //     $replacer = array();
    //     $replacer['user_nama'] = $sender->fnama;
    //     if($sender->language_id == 2) {
    //         $title = 'Obrolan Baru';
    //         $message = "$sender->fnama telah meninggalkan obrolan grup";
    //     } else {
    //         $title = 'New Chat';
    //         $message = "$sender->fnama has left the group chat";
    //     }
        
    //     $image = 'media/pemberitahuan/chat.png';

    //     if (array_unique($ios)) {
    //         $device = "ios"; //jenis device
    //         $tokens = $ios; //device token
    //         $payload = new stdClass();
    //         $payload->chat_room_id = (string) $chat_room_id;
    //         $payload->user_id = $sender->id;
    //         $payload->user_fnama = $sender->fnama;

    //         // by Muhammad Sofi - 27 October 2021 10:12
    //         // if user img & banner not exist or empty, change to default image
    //         // $payload->user_image = $this->cdn_url($ownerCommunity->image);
    //         if(file_exists(SENEROOT.$ownerCommunity->image) && $ownerCommunity->image != 'media/user/default.png'){
    //             $payload->user_image = $this->cdn_url($ownerCommunity->image);
    //         } else {
    //             $payload->user_image = $this->cdn_url('media/user/default-profile-picture.png');
    //         }
    //         $payload->chat_type = $roomChat->chat_type;
    //         $payload->custom_name_1 = $roomChat->custom_name_1;
    //         $payload->custom_name_2 = $roomChat->custom_name_2;
    //         // $payload->custom_image = $roomChat->custom_image;


    //         $nw = $this->anot->get($nation_code, "push", $type, $anotid, $sender->language_id);
    //         if (isset($nw->message)) {
    //             $message = $this->__nRep($nw->message, $replacer);
    //         }
    //         if (isset($nw->image)) {
    //             $image = $nw->image;
    //         }
    //         $image = $this->cdn_url($image);
    //         $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
    //     }

    //     if (array_unique($android)) {
    //         $device = "android"; //jenis device
    //         $tokens = $android; //device token
    //         $payload = new stdClass();
    //         $payload->chat_room_id = (string) $chat_room_id;
    //         $payload->user_id = $sender->id;
    //         $payload->user_fnama = $sender->fnama;
            
    //         // by Muhammad Sofi - 27 October 2021 10:12
    //         // if user img & banner not exist or empty, change to default image
    //         // $payload->user_image = $this->cdn_url($ownerCommunity->image);
    //         if(file_exists(SENEROOT.$ownerCommunity->image) && $ownerCommunity->image != 'media/user/default.png'){
    //             $payload->user_image = $this->cdn_url($ownerCommunity->image);
    //         } else {
    //             $payload->user_image = $this->cdn_url('media/user/default-profile-picture.png');
    //         }
    //         $payload->chat_type = $roomChat->chat_type;
    //         $payload->custom_name_1 = $roomChat->custom_name_1;
    //         $payload->custom_name_2 = $roomChat->custom_name_2;
    //         // $payload->custom_image = $roomChat->custom_image;

    //         $nw = $this->anot->get($nation_code, "push", $type, $anotid, $sender->language_id);
    //         if (isset($nw->message)) {
    //             $message = $this->__nRep($nw->message, $replacer);
    //         }
    //         if (isset($nw->image)) {
    //             $image = $nw->image;
    //         }
    //         $image = $this->cdn_url($image);
    //         $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
    //     }

    //     $url = base_url("api_mobile/chat/?apikey=$apikey&nation_code=$nation_code&apisess=$apisess");
    //     $res = $this->seme_curl->get($url);
        
    //     $body = json_decode($res->body);
    //     $chat_room = $body->data;

    //     //default output
    //     $this->status = 200;
    //     $this->message = 'Success';
    //     $data['chat_room'] = $chat_room;
    //     unset($chat_room);
    //     //get unread count
    //     $data['chat_unread'] = "".$this->ecpm->countUnread($nation_code,$pelanggan->id);

    //     //render as json
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    // }

    // public function count(){
    //     //initial
    //     $dt = $this->__init();

    //     //default result
    //     $data = 0;

    //     //default output
    //     $this->status = 200;
    //     $this->message = 'Success';

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if(empty($nation_code)){
    //       $this->status = 101;
    //       $this->message = 'Missing or invalid nation_code';
    //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //       die();
    //     }

    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if(!$c){
    //       $this->status = 400;
    //       $this->message = 'Missing or invalid API key';
    //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //       die();
    //     }

    //     //check apisess
    //     $apisess = $this->input->get('apisess');
    //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     if(!isset($pelanggan->id)){
    //       $this->status = 401;
    //       $this->message = 'Missing or invalid API session';
    //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //       die();
    //     }

    //     $this->status = 200;
    //     $this->message = "Success";

    //     $data = "".$this->ecpm->countUnread($nation_code,$pelanggan->id);

    //     //render as json
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    // }

    // public function new_chat()
    // {

    //     //initial
    //     //default result
    //     $data = array();
    //     $data['chat_total'] = 0;
    //     $data['chat'] = array();
    //     $data['chat_id_havent_read_lawan_bicara'] = array();

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if (!$c) {
    //         $this->status = 400;
    //         $this->message = 'Missing or invalid API key';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //check apisess
    //     $apisess = $this->input->get('apisess');
    //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     if (!isset($pelanggan->id)) {
    //         $this->status = 401;
    //         $this->message = 'Missing or invalid API session';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     //get chat_room_id
    //     $chat_room_id = $this->input->get('chat_room_id');
    //     $chat_room = $this->ecrm->getChatRoomByID($nation_code, $chat_room_id);
    //     if ($chat_room_id<=0 || !isset($chat_room->id)) {
    //         $this->status = 200;
    //         $this->message = 'Success';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    //         die();
    //     }

    //     $timezone = $this->input->get("timezone");

    //     if($this->isValidTimezoneId($timezone) === false){
    //       $timezone = $this->default_timezone;
    //     }
        
    //     $data['chat_total'] = $this->ecreadm->countAllByChatRoomIdUserId($nation_code, $chat_room_id, $pelanggan->id);

    //     $data['chat'] = $this->chat->getAllUnreadByChatRoomIdUserId($nation_code, $chat_room_id, $pelanggan->id, $pelanggan->language_id);

    //     $e_chat_ids = array();
    //     foreach($data['chat'] AS $chat){
    //         $e_chat_ids[] = $chat->chat_id;
    //     }
    //     unset($chat);

    //     //get complains
    //     $complains = array();
    //     if($e_chat_ids){
    //         $cn = $this->complain->getDetailByChatRoomID($nation_code, $chat_room_id, $e_chat_ids);
    //         if (count($cn)) {
    //             foreach ($cn as $c) {
    //                 // $c->c_produk_nama = $this->__dconv($c->c_produk_nama);
    //                 $c->c_produk_nama = html_entity_decode($c->c_produk_nama,ENT_QUOTES);

    //                 // START by Muhammad Sofi - 27 October 2021 10:12
    //                 // if user img & banner not exist or empty, change to default image
    //                 // $c->b_user_image_seller = $this->cdn_url($c->b_user_image_seller);
    //                 if(file_exists(SENEROOT.$c->b_user_image_seller) && $c->b_user_image_seller != 'media/user/default.png'){
    //                     $c->b_user_image_seller = $this->cdn_url($c->b_user_image_seller);
    //                 } else {
    //                     $c->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
    //                 }
    //                 // $c->b_user_image_buyer = $this->cdn_url($c->b_user_image_buyer);
    //                 if(file_exists(SENEROOT.$c->b_user_image_buyer) && $c->b_user_image_buyer != 'media/user/default.png'){
    //                     $c->b_user_image_buyer = $this->cdn_url($c->b_user_image_buyer);
    //                 } else {
    //                     $c->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
    //                 }
    //                 // END by Muhammad Sofi - 27 October 2021 10:12

    //                 $c->c_produk_foto = $this->cdn_url($c->c_produk_foto);
    //                 $c->c_produk_thumb = $this->cdn_url($c->c_produk_thumb);

    //                 $c->status_text = $this->__statusText($c, $c);

    //                 $key = $nation_code.'-'.$chat_room_id.'-'.$c->e_chat_id;
    //                 $complains[$key] = $c;
    //             }
    //         }
    //         unset($c,$cn); //free up some memory
    //     }

    //     //get attachment 
    //     $att = array();
    //     if($e_chat_ids){
    //         $attachments = $this->ecam->getDetailByChatRoomID($nation_code, $chat_room_id, $e_chat_ids);
    //         foreach ($attachments as $at) {
    //             $key = $nation_code.'-'.$chat_room_id.'-'.$at->e_chat_id;
                
    //             $at->order_invoice_code = '';
    //             $at->order_thumb = '';
    //             $at->order_user_id_seller = '';
    //             $at->status_text = '';

    //             if($at->jenis == 'product' || $at->jenis == 'barter_request' || $at->jenis == 'barter_exchange'){    

    //                 $at->produk_thumb = $this->cdn_url($at->produk_thumb);
    //                 // $at->produk_nama = $this->__dconv($at->produk_nama);
    //                 $at->produk_nama = html_entity_decode($at->produk_nama,ENT_QUOTES);

    //             }else if($at->jenis == 'order'){

    //                 $produk = $this->dodm->getByIdForChat($nation_code, $at->url, $at->order_detail_id);
    //                 $item = $this->dodim->getById($nation_code, $at->url, $at->order_detail_id, $at->order_detail_item_id);

    //                 if (isset($produk->c_produk_id)) {

    //                     $at->order_invoice_code = $produk->invoice_code;
    //                     $at->order_thumb = $this->cdn_url($item->thumb);
    //                     $at->order_user_id_seller = $produk->b_user_id_seller;
    //                     $at->status_text = $this->__statusText($produk, $produk);

    //                 }

    //             }else{

    //                 $at->url = $this->cdn_url($at->url);

    //             }

    //             //put to array key
    //             if (!isset($att[$key])) {
    //                 $att[$key] = array();
    //             }
    //             $att[$key][] = $at;
    //         }
    //         unset($at); //free some memory
    //         unset($attachments); //free some memory
    //     }

    //     //chat iteration
    //     foreach ($data['chat'] as &$ch) {
    //         if (isset($ch->b_user_image)) {
    //             // by Muhammad Sofi - 27 October 2021 10:12
    //             // if user img & banner not exist or empty, change to default image
    //             // $ch->b_user_image = $this->cdn_url($ch->b_user_image);
    //             if(file_exists(SENEROOT.$ch->b_user_image) && $ch->b_user_image != 'media/user/default.png'){
    //                 $ch->b_user_image = $this->cdn_url($ch->b_user_image);
    //             } else {
    //                 $ch->b_user_image = $this->cdn_url('media/user/default-profile-picture.png');
    //             }
    //         }
    //         if (isset($ch->a_pengguna_foto)) {
    //             $ch->a_pengguna_foto = $this->cdn_url($ch->a_pengguna_foto);
    //         }

    //         $ch->message = $this->__changeOfferMessage($ch->type, $ch->message, $pelanggan->language_id);

    //         $ch->message = html_entity_decode($ch->message,ENT_QUOTES);

    //         $ch->cdate_text = $this->humanTiming($ch->cdate);

    //         $ch->cdate = $this->customTimezone($ch->cdate, $timezone);

    //         //fill attachments
    //         // $ch->attachments = new stdClass();
    //         $ch->attachments = array();
    //         $key = $nation_code.'-'.$chat_room_id.'-'.$ch->chat_id;
    //         if (isset($att[$key])) {
    //             // $ch->attachments = $att[$key][0];
    //             $ch->attachments = $att[$key];
    //         }

    //         //fill complain
    //         $ch->complain = new stdClass();
    //         $key = $nation_code.'-'.$chat_room_id.'-'.$ch->chat_id;
    //         if (isset($complains[$key])) {
    //             $ch->complain = $complains[$key];
    //         }

    //         if($chat_room->chat_type == 'admin'){
                
    //             $ch->is_read_lawan_bicara = $chat_room->is_read_admin;

    //         }else{
                
    //             $ch->is_read_lawan_bicara = $this->ecreadm->checkReadByLawanBicara($nation_code, $chat_room_id, $ch->chat_id, $pelanggan->id);
    //         }

    //     }
        
    //     $this->ecpm->setAsRead($nation_code, $chat_room_id, $pelanggan->id);

    //     $this->ecreadm->setAsRead($nation_code, $chat_room_id, $pelanggan->id, $e_chat_ids);
        
    //     $data['chat_id_havent_read_lawan_bicara'] = $this->ecreadm->GetUnReadByLawanBicara($nation_code, $chat_room_id, $pelanggan->id);

    //     //render
    //     $this->status = 200;
    //     $this->message = 'Success';
    //     //render
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "chat");
    // }

}