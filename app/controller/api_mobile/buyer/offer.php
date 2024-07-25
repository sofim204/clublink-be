<?php
class Offer extends JI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        // $this->lib("seme_curl");
        $this->load("api_mobile/a_notification_model", 'anot');
        $this->load("api_mobile/b_user_model", 'bu');
        $this->load("api_mobile/e_chat_model", 'chat');
        $this->load("api_mobile/e_chat_room_model", 'ecrm');
        $this->load("api_mobile/e_chat_participant_model", 'ecpm');
        $this->load("api_mobile/e_chat_read_model", 'ecreadm');
        $this->load("api_mobile/e_chat_attachment_model", 'ecam');
        $this->load("api_mobile/b_user_setting_model", "busm");
        $this->load("api_mobile/c_produk_model", 'cpm');
        $this->load("api_mobile/c_produk_foto_model", "cpfm");
        $this->load("api_mobile/common_code_model", "ccm");

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

    public function index()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['offer_total'] = 0;
        $data['offer_list'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer");
            die();
        }

        $sort_col = $this->input->get("sort_col");
        $sort_dir = $this->input->get("sort_dir");
        $page = $this->input->get("page");
        $page_size = $this->input->get("page_size");
        $product_type = $this->input->get("product_type");
        $type = $this->input->get("type");
        $timezone = $this->input->get("timezone");

        if(!$product_type){
          $product_type = 'All';
        }

        //by Donny Dennison - 22 february 2022 17:42
        //change product_type language
        if($product_type == "Proteksi"){
          $product_type = "Protection";
        } else if($product_type == "Otomotif"){
          $product_type = "Automotive";
        } else if($product_type == "Gratis"){
          $product_type = "Free";
        }

        if(empty($type)){
            $type = 'ongoing';
        }

        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        $tbl2_as = $this->ecrm->getTblAs2();
        $sort_col = $this->__sortCol($sort_col, $tbl2_as);
        $sort_dir = $this->__sortDir($sort_dir);
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);

        $data['offer_total'] = $this->ecrm->countAllForOfferList($nation_code, "offer", "buyer", $pelanggan->id, $product_type, $type);

        $data['offer_list'] = $this->ecrm->getAllForOfferList($nation_code, "offer", "buyer", $page, $page_size, $sort_col, $sort_dir, $pelanggan->id, $product_type, $type);

        foreach ($data['offer_list'] as &$cr) {

            $cr->b_user_nama_buyer = html_entity_decode($cr->b_user_nama_buyer,ENT_QUOTES);

            if (isset($cr->b_user_image_buyer)) {

                if(file_exists(SENEROOT.$cr->b_user_image_buyer) && $cr->b_user_image_buyer != 'media/user/default.png'){
                    $cr->b_user_image_buyer = $this->cdn_url($cr->b_user_image_buyer);
                } else {
                    $cr->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }

            $cr->b_user_nama_seller = html_entity_decode($cr->b_user_nama_seller,ENT_QUOTES);

            if (isset($cr->b_user_image_seller)) {

                if(file_exists(SENEROOT.$cr->b_user_image_seller) && $cr->b_user_image_seller != 'media/user/default.png'){
                    $cr->b_user_image_seller = $this->cdn_url($cr->b_user_image_seller);
                } else {
                    $cr->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                }
            }

            $cr->c_produk_nama = html_entity_decode($cr->c_produk_nama,ENT_QUOTES);

            if(file_exists(SENEROOT.$cr->c_produk_thumb) && $cr->c_produk_thumb != 'media/produk/default.png'){
                $cr->c_produk_thumb = $this->cdn_url($cr->c_produk_thumb);
            } else {
                $cr->c_produk_thumb = $this->cdn_url('media/produk/default.png');
            }

            // $cr->cdate_text = $this->humanTiming($cr->cdate);
            $cr->cdate_text = $this->humanTiming($cr->cdate, null, $pelanggan->language_id);

            $cr->cdate = $this->customTimezone($cr->cdate, $timezone);

            if($cr->product_type == 'Automotive' && ($cr->b_kategori_id == 32 || $cr->b_kategori_id == 33)){
                $cr->automotive_type = $cr->kategori;
            }else{
                $cr->automotive_type = "";
            }

            //by Donny Dennison - 22 february 2022 17:42
            //change product_type language
            if($pelanggan->language_id == 2){
                if($cr->product_type == "Protection"){
                  $cr->product_type = "Proteksi";
                } else if($cr->product_type == "Automotive"){
                  $cr->product_type = "Otomotif";
                } else if($cr->product_type == "Free"){
                  $cr->product_type = "Gratis";
                }
            }

            //by Donny Dennison - 22 july 2022 10:45
            //add response parameter have_video in api product, product/detail, homepage, seller/product, product_automotive, wishlist
            $cr->have_video = ($this->cpfm->countByProdukIdJenisConvertStatusNotEqual($nation_code, $cr->c_produk_id, "video", "uploading") > 0) ? "1" : "0";

        }

        //default output
        $this->status = 200;
        $this->message = 'Success';

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer");
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

    //     //by Donny Dennison - 12 july 2022 14:56
    //     //new offer system
    //     $data['chat_room']->buyer_or_seller = "";
    //     $data['chat_room']->c_produk_stok = "0";

    //     if($data['chat_room']->chat_type == "offer"){

    //         $produk = $this->cpm->getById($nation_code, $data['chat_room']->c_produk_id, $pelanggan);

    //         if($pelanggan->id == $produk->b_user_id_seller){
    //             $data['chat_room']->buyer_or_seller = "seller";
    //         }else{
    //             $data['chat_room']->buyer_or_seller = "buyer";
    //         }

    //         $data['chat_room']->c_produk_stok = $produk->stok;

    //         $data['chat_room']->c_produk_nama = html_entity_decode($data['chat_room']->c_produk_nama,ENT_QUOTES);

    //         if(file_exists(SENEROOT.$data['chat_room']->c_produk_thumb) && $data['chat_room']->c_produk_thumb != 'media/user/default.png'){
    //             $data['chat_room']->c_produk_thumb = $this->cdn_url($data['chat_room']->c_produk_thumb);
    //         } else {
    //             $data['chat_room']->c_produk_thumb = $this->cdn_url('media/produk/default.png');
    //         }

    //     }
    //     //END by Donny Dennison - 12 july 2022 14:56

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

    //         //by Donny Dennison - 12 july 2022 14:56
    //         //new offer system
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

    //         //START by Donny Dennison - 12 july 2022 14:56
    //         //new offer system
    //         if($roomChat->chat_type == "offer" && ($roomChat->offer_status == "cancelled" || $roomChat->offer_status == "rejected" || $roomChat->offer_status == "reviewed")){
    //         //END by Donny Dennison - 12 july 2022 14:56

    //             $du = array();
    //             $du['last_delete_chat'] = date('Y-m-d H:i:s');

    //             //update table e_chat_participant
    //             $this->ecpm->update($nation_code, $pdp->chat_room_id, $pelanggan->id, $du);

    //             $this->ecpm->setAsRead($nation_code, $pdp->chat_room_id, $pelanggan->id);

    //             $this->ecreadm->setAsRead($nation_code, $pdp->chat_room_id, $pelanggan->id);

    //         //START by Donny Dennison - 12 july 2022 14:56
    //         //new offer system
    //         }
    //         //END by Donny Dennison - 12 july 2022 14:56

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

}