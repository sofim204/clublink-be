<?php
class Mobile_registration_activity extends JI_Controller
{

    public function __construct()
    {

        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_mobile/b_user_model", 'bu');
        $this->load("api_mobile/g_mobile_registration_activity_model", 'gmram');

    }

    public function index()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = "";
        // $data['avg_star'] = 0;
        // $data['review_total'] = 0;
        // $data['offer_review_total'] = 0;
        // $data['offer_reviews'] = array();

        $this->seme_log->write("api_mobile", "API_Mobile/mobile_registration_activity/index:: --POST: ".json_encode($_POST));

        // //check nation_code
        $nation_code = 62;
        // $nation_code = $this->input->get('nation_code');
        // $nation_code = $this->nation_check($nation_code);
        // if (empty($nation_code)) {
        //     $this->status = 101;
        //     $this->message = 'Missing or invalid nation_code';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
        //     die();
        // }

        // //check apikey
        // $apikey = $this->input->get('apikey');
        // $c = $this->apikey_check($apikey);
        // if (!$c) {
        //     $this->status = 400;
        //     $this->message = 'Missing or invalid API key';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
        //     die();
        // }

        // //check apisess
        // $apisess = $this->input->get('apisess');
        // $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        // if (!isset($pelanggan->id)) {
        //   $pelanggan = new stdClass();
        //   if($nation_code == 62){ //indonesia
        //     $pelanggan->language_id = 2;
        //   }else if($nation_code == 82){ //korea
        //     $pelanggan->language_id = 3;
        //   }else if($nation_code == 66){ //thailand
        //     $pelanggan->language_id = 4;
        //   }else {
        //     $pelanggan->language_id = 1;
        //   }
        // }

        $referral = trim(strtolower($this->input->post("referral")));
        $type = trim($this->input->post("type"));

        if($type == "click_link"){

            if(strlen($referral) <= 3){
                $this->status = 7280;
                $this->message = 'Referral empty or wrong referral';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "mobile_registration_activity");
                die();
            }

            //get last id
            $lastId = $this->gmram->getLastId($nation_code);

            //insert into database
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['id'] = $lastId;
            $di['referral'] = $referral;
            $di['cdate'] = "NOW()";

            $this->gmram->set($di);

        }else if($type == "downloaded"){

            if(strlen($referral) <= 3){
                $this->status = 7280;
                $this->message = 'referral empty or wrong referral';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "mobile_registration_activity");
                die();
            }

            $activityData = $this->gmram->getByReferralType($nation_code, $referral, $type);

            if(isset($activityData->id)){

                $di = array();
                $di['is_downloaded'] = 1;
                $di['cdate_downloaded'] = "NOW()";

                $this->gmram->update($nation_code, $activityData->id, $di);

            }

        }else{
            $this->status = 7280;
            $this->message = 'type not found';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "mobile_registration_activity");
            die();
        }

        //default output
        $this->status = 200;
        $this->message = 'Success';

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "mobile_registration_activity");
    }

    // public function baru()
    // {
    //     //initial
    //     $dt = $this->__init();

    //     //default result
    //     $data = array();
    //     // $data['chat'] = new stdClass();

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
    //         die();
    //     }

    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if (!$c) {
    //         $this->status = 400;
    //         $this->message = 'Missing or invalid API key';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
    //         die();
    //     }

    //     //check apisess
    //     $apisess = $this->input->get('apisess');
    //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     if (!isset($pelanggan->id)) {
    //         $this->status = 401;
    //         $this->message = 'Missing or invalid API session';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
    //         die();
    //     }

    //     //collect chat_room_id
    //     $chat_room_id = $this->input->post('chat_room_id');
    //     if ($chat_room_id <= 0) {
    //         $this->status = 7280;
    //         $this->message = 'Invalid Chat Room ID or Chat Room ID not registered';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
    //         die();
    //     }

    //     $roomChat = $this->ecrm->getChatRoomByID($nation_code, $chat_room_id, "offer");
    //     if (!isset($roomChat->nation_code)) {
    //         $this->status = 7280;
    //         $this->message = 'Invalid Chat Room ID or Chat Room ID not registered';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
    //         die();
    //     }

    //     if($roomChat->b_user_id_seller != $pelanggan->id){

    //         $type = "seller";

    //     }else{

    //         $type = "buyer";

    //     }

    //     $chatAcceptedType = $this->chat->getLastAcceptedByChatRoomId($nation_code, $chat_room_id);

    //     $reviewExist = $this->eorm->getByChatRoomId($nation_code, $chat_room_id, $chatAcceptedType->id, $type);
    //     if (isset($reviewExist->chat_room_id)) {
    //         $this->status = 7281;
    //         $this->message = 'this user have already review';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
    //         die();
    //     }

    //     $star = $this->input->post('star');
    //     if ($star <= 0 && $star >= 6) {
    //         $this->status = 7282;
    //         $this->message = 'rate from 1 to 5';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
    //         die();
    //     }

    //     //collect message
    //     $message = trim(((empty($this->input->post('message')))? "" : $this->input->post('message')));
    //     // if (!strlen($message)>0) {
    //     //     $this->status = 8105;
    //     //     $this->message = 'Message is empty';
    //     //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
    //     //     die();
    //     // }

    //     //by Donny Dennison 24 february 2021 18:45
    //     //change ’ to ' in add & edit product name and description
    //     $message = str_replace('’',"'",$message);
    //     // $message = filter_var($message, FILTER_SANITIZE_STRING);
    //     $message = nl2br($message);

    //     //by Donny Dennison - 15 augustus 2020 15:09
    //     //bug fix \n (enter) didnt get remove
    //     $message = str_replace(array("\r\n", "\n\r", "\r", "\n"), "", $message);

    //     //by Donny Dennison - 17 september 2021 9:16
    //     //community-feature
    //     //fix response in community description showing more "\"
    //     $message = str_replace("\\n", "<br />", $message);

    //     //start transaction
    //     $this->eorm->trans_start();

    //     //get last id
    //     $review_id = $this->eorm->getLastId($nation_code);

    //     //insert into database
    //     $di = array();
    //     $di['id'] = $review_id;
    //     $di['nation_code'] = $nation_code;
    //     $di['e_chat_room_id'] = $chat_room_id;
    //     $di['e_chat_id'] = $chatAcceptedType->id;
    //     $di['b_user_id'] = $pelanggan->id;

    //     if($type == "seller"){
    //         $di['b_user_id_to'] = $roomChat->b_user_id_seller;
    //     }else{
    //         $di['b_user_id_to'] = $roomChat->b_user_id_starter;
    //     }

    //     $di['type'] = $type;
    //     $di['star'] = $star;
    //     $di['review'] = $message;
    //     $di['cdate'] = "NOW()";

    //     $res = $this->eorm->set($di);
    //     if ($res) {
    //         $this->status = 200;
    //         $this->message = 'Success';
    //         $this->eorm->trans_commit();

    //         $reviewExistBuyer = $this->eorm->getByChatRoomId($nation_code, $chat_room_id, $chatAcceptedType->id, "seller");

    //         $reviewExistSeller = $this->eorm->getByChatRoomId($nation_code, $chat_room_id, $chatAcceptedType->id, "buyer");

    //         if(isset($reviewExistBuyer->chat_room_id) && isset($reviewExistSeller->chat_room_id)){
    //             $offer_status = "reviewed";
    //         }else if(isset($reviewExistBuyer->chat_room_id)){
    //             $offer_status = "waiting review from seller";
    //         }else{
    //             $offer_status = "waiting review from buyer";
    //         }

    //         $du = array();
    //         $du['offer_status'] = $offer_status;
    //         $du['offer_status_update_date'] = "NOW()";
    //         $this->ecrm->update($nation_code, $chat_room_id, $du);
    //         $this->eorm->trans_commit();

    //         if($type == "seller"){

    //             $this->bu->updateTotal($nation_code, $roomChat->b_user_id_seller, "offer_rating_seller_total", "+", 1);
    //             $this->eorm->trans_commit();

    //             $avg_star = $this->eorm->getAvg($nation_code, $roomChat->b_user_id_seller, "seller")->avg_star;

    //             $du = array();
    //             $du['offer_rating_seller_avg'] = $avg_star;
    //             $this->bu->update($nation_code, $roomChat->b_user_id_seller, $du);
    //             $this->eorm->trans_commit();

    //         }else{

    //             $this->bu->updateTotal($nation_code, $roomChat->b_user_id_starter, "offer_rating_buyer_total", "+", 1);
    //             $this->eorm->trans_commit();

    //             $avg_star = $this->eorm->getAvg($nation_code, $roomChat->b_user_id_starter, "buyer")->avg_star;

    //             $du = array();
    //             $du['offer_rating_buyer_avg'] = $avg_star;
    //             $this->bu->update($nation_code, $roomChat->b_user_id_starter, $du);
    //             $this->eorm->trans_commit();

    //         }

    //         //START by Donny Dennison - 10 august 2022 10:10
    //         //new point rule for offer system
    //         $produk = $this->cpm->getById($nation_code, $roomChat->c_produk_id, $pelanggan);

    //         //buyer
    //         if($offer_status == "reviewed"){

    //             if($produk->product_type != "Free"){

    //                 //get point
    //                 $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "ER");
    //                 if (!isset($pointGet->remark)) {
    //                   $pointGet = new stdClass();
    //                   $pointGet->remark = 30;
    //                 }

    //                 $user = $this->bu->getById($nation_code, $roomChat->b_user_id_starter);

    //                 $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $roomChat->b_user_id_starter);

    //                 $di = array();
    //                 $di['nation_code'] = $nation_code;
    //                 $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
    //                 $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
    //                 $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
    //                 $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
    //                 $di['b_user_id'] = $roomChat->b_user_id_starter;
    //                 $di['point'] = $pointGet->remark;
    //                 $di['custom_id'] = $chatAcceptedType->id;
    //                 $di['custom_type'] = 'offer';
    //                 $di['custom_type_sub'] = 'review';
    //                 $di['custom_text'] = $user->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
          // $endDoWhile = 0;
          // do{
          //   $leaderBoardHistoryId = $this->GUIDv4();
          //   $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
          //   if($checkId == 0){
          //     $endDoWhile = 1;
          //   }
          // }while($endDoWhile == 0);
          // $di['id'] = $leaderBoardHistoryId;
    //                 $this->glphm->set($di);
    //                 $this->eorm->trans_commit();
    //                 // $this->glrm->updateTotal($nation_code, $roomChat->b_user_id_starter, 'total_point', '+', $di['point']);
    //                 // $this->eorm->trans_commit();
    //                 // $this->glrm->updateTotal($nation_code, $roomChat->b_user_id_starter, 'total_post', '+', 1);
    //                 // $this->eorm->trans_commit();
    //             }

    //             //START by Donny Dennison - 02 september 2022 14:20
    //             //record user sales and total transaction as seller and buyer
    //             if($produk->product_type != "Free"){
    //                 $ExistingData = $this->buosm->getByUserId($nation_code, $roomChat->b_user_id_starter, date("Y"), date("m"));
    //                 if(!isset($ExistingData->b_user_id)){
    //                     // $lastId = $this->buosm->getLastId($nation_code);
    //                     $di = array();
    //                     $di['nation_code'] = $nation_code;
    //                     // $di['id'] = $lastId;
    //                     $di['b_user_id'] = $roomChat->b_user_id_starter;
    //                     $di['year'] = date("Y");
    //                     $di['month'] = date("m");
    //                     $di['total_sales_buyer'] = $chatAcceptedType->message;
    //                     $di['total_transaction_buyer'] = 1;
    //                     $this->buosm->set($di);
    //                     $this->eorm->trans_commit();

    //                 }else{

    //                     $this->buosm->updateTotal($nation_code, $roomChat->b_user_id_starter, date("Y"), date("m"), "total_sales_buyer", "+", $chatAcceptedType->message);
    //                     $this->eorm->trans_commit();

    //                     $this->buosm->updateTotal($nation_code, $roomChat->b_user_id_starter, date("Y"), date("m"), "total_transaction_buyer", "+", 1);
    //                     $this->eorm->trans_commit();

    //                 }

    //             }else{

    //                 $ExistingData = $this->buosm->getByUserId($nation_code, $roomChat->b_user_id_starter, date("Y"), date("m"));
    //                 if(!isset($ExistingData->b_user_id)){

    //                     // $lastId = $this->buosm->getLastId($nation_code);
    //                     $di = array();
    //                     $di['nation_code'] = $nation_code;
    //                     // $di['id'] = $lastId;
    //                     $di['b_user_id'] = $roomChat->b_user_id_starter;
    //                     $di['year'] = date("Y");
    //                     $di['month'] = date("m");
    //                     $di['total_sales_buyer'] = 0;
    //                     $di['total_transaction_buyer'] = 1;
    //                     $this->buosm->set($di);
    //                     $this->eorm->trans_commit();

    //                 }else{

    //                     $this->buosm->updateTotal($nation_code, $roomChat->b_user_id_starter, date("Y"), date("m"), "total_transaction_buyer", "+", 1);
    //                     $this->eorm->trans_commit();

    //                 }

    //             }
    //             //END by Donny Dennison - 02 september 2022 14:20
    //             //record user sales and total transaction as seller and buyer

    //         }

    //         //seller
    //         if($offer_status == "reviewed"){

    //             if($produk->product_type == "Free"){

    //                 //get point
    //                 $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "ES");
    //                 if (!isset($pointGet->remark)) {
    //                   $pointGet = new stdClass();
    //                   $pointGet->remark = 50;
    //                 }

    //             }else{

    //                 //get point
    //                 $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EQ");
    //                 if (!isset($pointGet->remark)) {
    //                   $pointGet = new stdClass();
    //                   $pointGet->remark = 30;
    //                 }

    //             }

    //             $user = $this->bu->getById($nation_code, $roomChat->b_user_id_seller);

    //             $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $roomChat->b_user_id_seller);

    //             $di = array();
    //             $di['nation_code'] = $nation_code;
    //             $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
    //             $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
    //             $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
    //             $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
    //             $di['b_user_id'] = $roomChat->b_user_id_seller;
    //             $di['point'] = $pointGet->remark;
    //             $di['custom_id'] = $chatAcceptedType->id;
    //             $di['custom_type'] = 'offer';
    //             $di['custom_type_sub'] = 'review';
    //             $di['custom_text'] = $user->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
          // $endDoWhile = 0;
          // do{
          //   $leaderBoardHistoryId = $this->GUIDv4();
          //   $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
          //   if($checkId == 0){
          //     $endDoWhile = 1;
          //   }
          // }while($endDoWhile == 0);
          // $di['id'] = $leaderBoardHistoryId;
    //             $this->glphm->set($di);
    //             $this->eorm->trans_commit();
    //             // $this->glrm->updateTotal($nation_code, $roomChat->b_user_id_seller, 'total_point', '+', $di['point']);
    //             // $this->eorm->trans_commit();
    //             // $this->glrm->updateTotal($nation_code, $roomChat->b_user_id_seller, 'total_post', '+', 1);
    //             // $this->eorm->trans_commit();

    //             //START by Donny Dennison - 02 september 2022 14:20
    //             //record user sales and total transaction as seller and buyer
    //             if($produk->product_type != "Free"){
    //                 $ExistingData = $this->buosm->getByUserId($nation_code, $roomChat->b_user_id_seller, date("Y"), date("m"));
    //                 if(!isset($ExistingData->b_user_id)){
    //                     // $lastId = $this->buosm->getLastId($nation_code);
    //                     $di = array();
    //                     $di['nation_code'] = $nation_code;
    //                     // $di['id'] = $lastId;
    //                     $di['b_user_id'] = $roomChat->b_user_id_seller;
    //                     $di['year'] = date("Y");
    //                     $di['month'] = date("m");
    //                     $di['total_sales_seller'] = $chatAcceptedType->message;
    //                     $di['total_transaction_seller'] = 1;
    //                     $this->buosm->set($di);
    //                     $this->eorm->trans_commit();
    //                 }else{
    //                     $this->buosm->updateTotal($nation_code, $roomChat->b_user_id_seller, date("Y"), date("m"), "total_sales_seller", "+", $chatAcceptedType->message);
    //                     $this->eorm->trans_commit();

    //                     $this->buosm->updateTotal($nation_code, $roomChat->b_user_id_seller, date("Y"), date("m"), "total_transaction_seller", "+", 1);
    //                     $this->eorm->trans_commit();
    //                 }
    //             }else{
    //                 $ExistingData = $this->buosm->getByUserId($nation_code, $roomChat->b_user_id_seller, date("Y"), date("m"));
    //                 if(!isset($ExistingData->b_user_id)){
    //                     // $lastId = $this->buosm->getLastId($nation_code);
    //                     $di = array();
    //                     $di['nation_code'] = $nation_code;
    //                     // $di['id'] = $lastId;
    //                     $di['b_user_id'] = $roomChat->b_user_id_seller;
    //                     $di['year'] = date("Y");
    //                     $di['month'] = date("m");
    //                     $di['total_sales_seller'] = 0;
    //                     $di['total_transaction_seller'] = 1;
    //                     $this->buosm->set($di);
    //                     $this->eorm->trans_commit();
    //                 }else{
    //                     $this->buosm->updateTotal($nation_code, $roomChat->b_user_id_seller, date("Y"), date("m"), "total_transaction_seller", "+", 1);
    //                     $this->eorm->trans_commit();
    //                 }
    //             }
    //             //END by Donny Dennison - 02 september 2022 14:20
    //             //record user sales and total transaction as seller and buyer
    //         }
    //         //END by Donny Dennison - 10 august 2022 10:10
    //         //new point rule for offer system

    //         $roomChat->c_produk_nama = html_entity_decode($roomChat->c_produk_nama,ENT_QUOTES);

    //         //get missing data
    //         if($type == "seller"){
    //             $sender = $this->bu->getById($nation_code, $roomChat->b_user_id_starter);
    //             $receiver = $this->bu->getById($nation_code, $roomChat->b_user_id_seller);
    //         }else{
    //             $sender = $this->bu->getById($nation_code, $roomChat->b_user_id_seller);
    //             $receiver = $this->bu->getById($nation_code, $roomChat->b_user_id_starter);
    //         }

    //         $dpe = array();
    //         $dpe['nation_code'] = $nation_code;
    //         $dpe['b_user_id'] = $receiver->id;
    //         $dpe['id'] = $this->dpem->getLastId($nation_code, $receiver->id);
    //         $dpe['type'] = "offer_review";

    //         if($type == "seller"){
    //             if($receiver->language_id == 2) {
    //                 $dpe['judul'] = "Ulasan Penawaran";
    //                 $dpe['teks'] =  "Pembeli telah meninggalkan ulasan di ".$roomChat->c_produk_nama;
    //             } else {
    //                 $dpe['judul'] = "Offer Review";
    //                 $dpe['teks'] =  "Buyer's review for ".$roomChat->c_produk_nama. " is done now";
    //             }
    //         }else{
    //             if($receiver->language_id == 2) {
    //                 $dpe['judul'] = "Ulasan Penawaran";
    //                 $dpe['teks'] =  "Penjual telah meninggalkan ulasan di ".$roomChat->c_produk_nama;
    //             } else {
    //                 $dpe['judul'] = "Offer Review";
    //                 $dpe['teks'] =  "Seller's review for ".$roomChat->c_produk_nama. " is done now";
    //             }
    //         }

    //         $dpe['gambar'] = 'media/pemberitahuan/outbounding.png';
    //         $dpe['cdate'] = "NOW()";
    //         $extras = new stdClass();
    //         // $extras->chat_room_id = $roomChat->id;
    //         $extras->c_produk_id = $roomChat->c_produk_id;
    //         // $extras->c_produk_nama = $roomChat->c_produk_nama;
    //         // $extras->b_user_id_starter = $roomChat->b_user_id_starter;
    //         // $extras->b_user_id_seller = $roomChat->b_user_id_seller;

    //         if($type == "seller"){
    //             if($receiver->language_id == 2) {
    //                 $extras->judul = "Ulasan Penawaran";
    //                 $extras->teks =  "Pembeli telah meninggalkan ulasan di ".$roomChat->c_produk_nama;
    //             } else {
    //                 $extras->judul = "Offer Review";
    //                 $extras->teks =  "Buyer's review for ".$roomChat->c_produk_nama. " is done now";
    //             }
    //         }else{
    //             if($receiver->language_id == 2) {
    //                 $extras->judul = "Ulasan Penawaran";
    //                 $extras->teks =  "Penjual telah meninggalkan ulasan di ".$roomChat->c_produk_nama;
    //             } else {
    //                 $extras->judul = "Offer Review";
    //                 $extras->teks =  "Seller's review for ".$roomChat->c_produk_nama. " is done now";
    //             }
    //         }

    //         $dpe['extras'] = json_encode($extras);
    //         $this->dpem->set($dpe);
    //         $this->eorm->trans_commit();

    //         $classified = 'setting_notification_user';
    //         $code = 'U5';

    //         $receiverSettingNotif = $this->busm->getValue($nation_code, $receiver->id, $classified, $code);

    //         if (!isset($receiverSettingNotif->setting_value)) {
    //             $receiverSettingNotif->setting_value = 0;
    //         }

    //         //push notif
    //         if ($receiverSettingNotif->setting_value == 1 && $receiver->is_active == 1) {

    //             if (strlen($receiver->fcm_token)>50) {
    //                 $device = $receiver->device; //jenis device
    //                 $tokens = array($receiver->fcm_token); //device token

    //                 if($type == "seller"){
    //                     if($receiver->language_id == 2) {
    //                         $title = "Ulasan Penawaran";
    //                         $message =  "Pembeli telah meninggalkan ulasan di ".$roomChat->c_produk_nama;
    //                     } else {
    //                         $title = "Offer Review";
    //                         $message =  "Buyer's review for ".$roomChat->c_produk_nama. " is done now";
    //                     }
    //                 }else{
    //                     if($receiver->language_id == 2) {
    //                         $title = "Ulasan Penawaran";
    //                         $message =  "Penjual telah meninggalkan ulasan di ".$roomChat->c_produk_nama;
    //                     } else {
    //                         $title = "Offer Review";
    //                         $message =  "Seller's review for ".$roomChat->c_produk_nama. " is done now";
    //                     }
    //                 }

    //                 $type = 'offer_review';
    //                 $image = 'media/pemberitahuan/outbounding.png';
    //                 $payload = new stdClass();
    //                 // $payload->chat_room_id = $roomChat->id;
    //                 $payload->c_produk_id = $roomChat->c_produk_id;
    //                 // $payload->c_produk_nama = $roomChat->c_produk_nama;
    //                 // $payload->b_user_id_starter = $roomChat->b_user_id_starter;
    //                 // $payload->b_user_id_seller = $roomChat->b_user_id_seller;

    //                 $image = $this->cdn_url($image);
    //                 $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
    //             }

    //         }

    //     } else {
    //         $this->eorm->trans_rollback();
    //         $this->status = 8011;
    //         $this->message = 'Failed updating data';
    //     }
    //     $this->eorm->trans_end();
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "offer_review");
    // }

}