<?php
class Daily extends JI_Controller
{
    public $email_send=1;
    public $is_log = 1;
    public $is_push = 1;

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->lib("seme_email");
        $this->load("api_cron/a_notification_model", "anot");
        $this->load("api_cron/a_pengguna_model", "apm");
        $this->load("api_cron/b_user_model", "bu");
        $this->load("api_cron/b_user_setting_model", "busm");
        $this->load("api_cron/common_code_model", "ccm");

        $this->load("api_cron/c_produk_model", "cpm");

        $this->load("api_cron/d_order_model", "order");
        $this->load("api_cron/d_order_detail_model", "dodm");
        $this->load("api_cron/d_order_detail_item_model", "dodim");
        $this->load("api_cron/d_order_proses_model", "dopm");
        $this->load("api_cron/d_pemberitahuan_model", "dpem");
    }

    // private function __codeGen($nation_code)
    // {
    //         $this->lib("conumtext");
    //         $token = $this->conumtext->genRand($type="str", $min=6, $max=14);
    //         return $nation_code.''.$token;
    // }

    public function index()
    {
        if ($this->is_log) {
            $this->seme_log->write("api_cron", "API_Cron/Daily::index START");
        }

        //start transaction
        $this->order->trans_start();

        //change log filename
        // $this->seme_log->changeFilename("cron.log");

        /** @var int define delivery timeout in hour(s)*/
        // $delivery_timeout = 24;
        
        // get config from app/core/ji_controller
        // if (isset($this->delivery_timeout)) {
        //     $delivery_timeout = $this->delivery_timeout;
        // }

        /** @var int define buyer confirmation timeout in day(s) */
        // $buyer_confirm_timeout = 6;

        // get config from app/core/ji_controller
        // if (isset($this->buyer_timeout)) {
        //     $buyer_confirm_timeout = $this->buyer_timeout;
        // }

        //set expired free product
        $fpncs = $this->cpm->getNationCodes();
        foreach ($fpncs as $nc) {
            $nation_code = $nc->nation_code;
            // if ($this->is_log) {
            //     $this->seme_log->write("api_cron", "API_Cron/Daily::index --freeProductExpired for nation_code: $nation_code");
            // }
            $ddcount = $this->cpm->countAll($nation_code, '');
            $quota = $this->produk_gratis_limit_jumlah - $ddcount;
            if ($this->is_log) {
                $this->seme_log->write("api_cron", "API_Cron/Daily::index --countPublished: $ddcount, --limit: ".$this->produk_gratis_limit_jumlah.", --quota: $quota");
            }
            if ($quota<=0) {
                $ddcount = $this->cpm->countAll($nation_code, '');
                $quota = $this->produk_gratis_limit_jumlah - $ddcount;
                $itr = 0;
                while ($quota<0 && $itr<3) {
                    //doing dequeue
                    // if ($this->is_log) {
                    //     $this->seme_log->write("api_cron", "API_Cron/Daily::index --emptyQuota --deQueue --getOldestData");
                    // }
                    $old = $this->cpm->getFirstOldest($nation_code);
                    if (isset($old->id)) {
                        $dx = array();
                        $dx['is_published'] = 0;
                        $dx['is_active'] = 0;
                        $this->cpm->update($nation_code, $old->id, $dx);
                        $this->order->trans_commit();
                        $ddcount = $this->cpm->countAll($nation_code, '');
                        $quota = $this->produk_gratis_limit_jumlah - $ddcount;
                        if ($this->is_log) {
                            $this->seme_log->write("api_cron", "API_Cron/Daily::index --countPublished: $ddcount, --limit: ".$this->produk_gratis_limit_jumlah.", --quota: $quota");
                        }
                    } else {
                        //for breaking loop
                        $quota = 0;
                        if ($this->is_log) {
                            $this->seme_log->write("api_cron", "API_Cron/Daily::index --countPublished: $ddcount, --limit: ".$this->produk_gratis_limit_jumlah.", --quota: $quota");
                        }
                        if ($this->is_log) {
                            $this->seme_log->write("api_cron", "API_Cron/Daily::index --breakLoop");
                        }
                    }
                    $itr++; //second opt for fail safe endless loop
                }
            }
        } //end country
        unset($fpncs, $nc);
        //END by Donny Dennison - 19 january 2022 10:35

        //by Donny Dennison - 08-07-2020 13:59
        //request by Mr Jackie, change shipment status from delivered to succeed after 48 hours for Qxpress
        //update shipment status from delivered to succeed
        // $delivereds = $this->dodm->getDelivereds($delivery_timeout);
        // $delivereds = $this->dodm->getDeliveredsGogovan($delivery_timeout);
        // if (count($delivereds)>0) {
        //     foreach ($delivereds as $delivered) {
        //         $du = array();
        //         $du['shipment_status'] = 'succeed';
        //         $this->dodm->update($delivered->nation_code, $delivered->d_order_id, $delivered->c_produk_id, $du);
        //         $this->order->trans_commit();

        //         //get buyer data
        //         $buyer = $this->bu->getById($delivered->nation_code, $delivered->b_user_id_buyer);
        //         $seller = $this->bu->getById($delivered->nation_code, $delivered->b_user_id_seller);

        //         //get notification config for buyer
        //         $setting_value = 0;
        //         $classified = 'setting_notification_buyer';
        //         $notif_code = 'B3';
        //         $notif_cfg = $this->busm->getValue($delivered->nation_code, $buyer->id, $classified, $notif_code);
        //         if (isset($notif_cfg->setting_value)) {
        //             $setting_value = (int) $notif_cfg->setting_value;
        //         }

        //         $type = 'transaction';
        //         $anotid = 11;
        //         $replacer = array();
        //         $replacer['delivered_name'] = html_entity_decode($delivered->nama,ENT_QUOTES);
        //         $replacer['invoice_code'] = $delivered->invoice_code;

        //         //push notif for buyer
        //         if (strlen($buyer->fcm_token) > 50 && !empty($setting_value)) {
        //             $device = $buyer->device;
        //             $tokens = array($buyer->fcm_token);
        //             if($buyer->language_id == 2) {
        //                 $title = 'Pesanan Terkirim';
        //                 $message = "Pesanan Anda ".html_entity_decode($this->convertEmoji($delivered->nama),ENT_QUOTES)." ($delivered->invoice_code) telah tiba. Mohon konfirmasi!";
        //             } else {
        //                 $title = 'Order Delivered';
        //                 $message = "Your order ".html_entity_decode($this->convertEmoji($delivered->nama),ENT_QUOTES)." ($delivered->invoice_code) have arrived. Please confirm!";
        //             }
                    
        //             $type = 'transaction';
        //             $image = 'media/pemberitahuan/transaction.png';
        //             $payload = new stdClass();
        //             $payload->id_produk = "".$delivered->c_produk_id;
        //             $payload->id_order = "".$delivered->d_order_id;
        //             $payload->id_order_detail = "".$delivered->c_produk_id;
        //             $payload->b_user_id_buyer = $buyer->id;
        //             $payload->b_user_fnama_buyer = $buyer->fnama;
                    
        //             // by Muhammad Sofi - 27 October 2021 10:12
        //             // if user img & banner not exist or empty, change to default image
        //             // $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
        //             if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
        //                 $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
        //             } else {
        //                 $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
        //             }
        //             $payload->b_user_id_seller = $seller->id;
        //             $payload->b_user_fnama_seller = $seller->fnama;
                    
        //             // by Muhammad Sofi - 27 October 2021 10:12
        //             // if user img & banner not exist or empty, change to default image
        //             // $payload->b_user_image_seller = $this->cdn_url($seller->image);
        //             if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
        //                 $payload->b_user_image_seller = $this->cdn_url($seller->image);
        //             } else {
        //                 $payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
        //             }
        //             $nw = $this->anot->get($nation_code, "push", $type, $anotid, $buyer->language_id);
        //             if (isset($nw->title)) {
        //                 $title = $nw->title;
        //             }
        //             if (isset($nw->message)) {
        //                 $message = $this->__nRep($nw->message, $replacer);
        //             }
        //             if (isset($nw->image)) {
        //                 $image = $nw->image;
        //             }
        //             $image = $this->cdn_url($image);
        //             $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        //             $this->seme_log->write("api_cron", 'API_Cron/Daily::index __pushNotif: '.json_encode($res));
        //         }

        //         //collect array notification list for buyer
        //         $dpe = array();
        //         $dpe['nation_code'] = $delivered->nation_code;
        //         $dpe['b_user_id'] = $buyer->id;
        //         $dpe['id'] = $this->dpem->getLastId($delivered->nation_code, $buyer->id);
        //         $dpe['type'] = "transaction";
        //         if($buyer->language_id == 2) {
        //             $dpe['judul'] = "Pesanan Terkirim";
        //             $dpe['teks'] = "Pesanan Anda ".html_entity_decode($delivered->nama,ENT_QUOTES)." ($delivered->invoice_code) telah tiba. Silakan periksa kondisi produk dan konfirmasi dalam waktu 6 hari.";
        //         } else {
        //             $dpe['judul'] = "Order Delivered";
        //             $dpe['teks'] = "Your order ".html_entity_decode($delivered->nama,ENT_QUOTES)." ($delivered->invoice_code) has arrived. Please check the product's condition and confirm within 6 days.";
        //         }
                
        //         $dpe['cdate'] = "NOW()";
        //         $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
        //         $extras = new stdClass();
        //         $extras->id_produk = "".$delivered->c_produk_id;
        //         $extras->id_order = "".$delivered->d_order_id;
        //         $extras->id_order_detail = "".$delivered->c_produk_id;
        //         $extras->b_user_id_buyer = $buyer->id;
        //         $extras->b_user_fnama_buyer = $buyer->fnama;
                
        //         // by Muhammad Sofi - 27 October 2021 10:12
        //         // if user img & banner not exist or empty, change to default image
        //         // $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
        //         if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
        //             $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
        //         } else {
        //             $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
        //         }
        //         $extras->b_user_id_seller = $seller->id;
        //         $extras->b_user_fnama_seller = $seller->fnama;
                
        //         // by Muhammad Sofi - 27 October 2021 10:12
        //         // if user img & banner not exist or empty, change to default image
        //         // $extras->b_user_image_seller = $this->cdn_url($seller->image);
        //         if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
        //             $extras->b_user_image_seller = $this->cdn_url($seller->image);
        //         } else {
        //             $extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
        //         }
        //         $nw = $this->anot->get($nation_code, "list", $type, $anotid, $buyer->language_id);
        //         if (isset($nw->title)) {
        //             $dpe['judul'] = $nw->title;
        //         }
        //         if (isset($nw->message)) {
        //             $dpe['teks'] = $this->__nRep($nw->message, $replacer);
        //         }
        //         if (isset($nw->image)) {
        //             $dpe['gambar'] = $nw->image;
        //         }
        //         // $dpe['gambar'] = $this->cdn_url($dpe['gambar']);
        //         $dpe['extras'] = json_encode($extras);
        //         $dpe['is_read'] = 0;
        //         $this->dpem->set($dpe);
        //         $this->order->trans_commit();

        //         //get notification config for seller
        //         $setting_value = 0;
        //         $classified = 'setting_notification_seller';
        //         $notif_code = 'S1';
        //         $notif_cfg = $this->busm->getValue($delivered->nation_code, $seller->id, $classified, $notif_code);
        //         if (isset($notif_cfg->setting_value)) {
        //             $setting_value = (int) $notif_cfg->setting_value;
        //         }

        //         $type = 'transaction';
        //         $anotid = 12;
        //         $replacer = array();
        //         $replacer['invoice_code'] = $delivered->invoice_code;
        //         $replacer['delivered_name'] = html_entity_decode($delivered->nama,ENT_QUOTES);
        //         //push notif for seller
        //         if (strlen($seller->fcm_token) > 50 && !empty($setting_value)) {
        //             $device = $seller->device;
        //             $tokens = array($seller->fcm_token);
        //             if($seller->language_id == 2) {
        //                 $title = 'Terkirim';
        //                 $message = "Produk Anda dengan nomor faktur $delivered->invoice_code sudah sampai tujuan.";
        //             } else {
        //                 $title = 'Delivered';
        //                 $message = "Your product with invoice number $delivered->invoice_code has arrived.";
        //             }
                    
        //             $type = 'transaction';
        //             $image = 'media/pemberitahuan/transaction.png';
        //             $payload = new stdClass();
        //             $payload->id_produk = "".$delivered->c_produk_id;
        //             $payload->id_order = "".$delivered->d_order_id;
        //             $payload->id_order_detail = "".$delivered->c_produk_id;
        //             $payload->b_user_id_buyer = $buyer->id;
        //             $payload->b_user_fnama_buyer = $buyer->fnama;
                    
        //             // by Muhammad Sofi - 27 October 2021 10:12
        //             // if user img & banner not exist or empty, change to default image
        //             // $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
        //             if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
        //                 $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
        //             } else {
        //                 $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
        //             }
        //             $payload->b_user_id_seller = $seller->id;
        //             $payload->b_user_fnama_seller = $seller->fnama;
                    
        //             // by Muhammad Sofi - 27 October 2021 10:12
        //             // if user img & banner not exist or empty, change to default image
        //             // $payload->b_user_image_seller = $this->cdn_url($seller->image);
        //             if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
        //                 $payload->b_user_image_seller = $this->cdn_url($seller->image);
        //             } else {
        //                 $payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
        //             }
        //             $nw = $this->anot->get($nation_code, "push", $type, $anotid, $seller->language_id);
        //             if (isset($nw->title)) {
        //                 $title = $nw->title;
        //             }
        //             if (isset($nw->message)) {
        //                 $message = $this->__nRep($nw->message, $replacer);
        //             }
        //             if (isset($nw->image)) {
        //                 $image = $nw->image;
        //             }
        //             $image = $this->cdn_url($image);
        //             $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        //             if ($this->is_log) {
        //                 $this->seme_log->write("api_cron", 'API_Mobile/seller/Order::delivery_progress __pushNotif: '.json_encode($res));
        //             }
        //         }

        //         //collect array notification list for seller
        //         $dpe = array();
        //         $dpe['nation_code'] = $delivered->nation_code;
        //         $dpe['b_user_id'] = $seller->id;
        //         $dpe['id'] = $this->dpem->getLastId($delivered->nation_code, $seller->id);
        //         $dpe['type'] = "transaction";
        //         if($seller->language_id == 2) {
        //             $dpe['judul'] = "Terkirim";
        //             $dpe['teks'] = "Produk Anda ".html_entity_decode($delivered->nama,ENT_QUOTES)." ($delivered->invoice_code) telah sampai di tujuan. Mohon menunggu pembeli untuk mengkonfirmasi penerimaan barang.";
        //         } else {
        //             $dpe['judul'] = "Delivered";
        //             $dpe['teks'] = "Your product ".html_entity_decode($delivered->nama,ENT_QUOTES)." ($delivered->invoice_code) has arrived at its destination. Please wait for the buyer to confirm receipt of the goods.";
        //         }
                
        //         $dpe['cdate'] = "NOW()";
        //         $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
        //         $extras = new stdClass();
        //         $extras->id_produk = "".$delivered->c_produk_id;
        //         $extras->id_order = "".$delivered->d_order_id;
        //         $extras->id_order_detail = "".$delivered->c_produk_id;
        //         $extras->b_user_id_buyer = $buyer->id;
        //         $extras->b_user_fnama_buyer = $buyer->fnama;
                
        //         // by Muhammad Sofi - 27 October 2021 10:12
        //         // if user img & banner not exist or empty, change to default image
        //         // $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
        //         if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
        //             $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
        //         } else {
        //             $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
        //         }
        //         $extras->b_user_id_seller = $seller->id;
        //         $extras->b_user_fnama_seller = $seller->fnama;
                
        //         // by Muhammad Sofi - 27 October 2021 10:12
        //         // if user img & banner not exist or empty, change to default image
        //         // $extras->b_user_image_seller = $this->cdn_url($seller->image);
        //         if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
        //             $extras->b_user_image_seller = $this->cdn_url($seller->image);
        //         } else {
        //             $extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
        //         }
        //         $nw = $this->anot->get($nation_code, "list", $type, $anotid, $seller->language_id);
        //         if (isset($nw->title)) {
        //             $dpe['judul'] = $nw->title;
        //         }
        //         if (isset($nw->message)) {
        //             $dpe['teks'] = $this->__nRep($nw->message, $replacer);
        //         }
        //         if (isset($nw->image)) {
        //             $dpe['gambar'] = $nw->image;
        //         }
        //         $dpe['extras'] = json_encode($extras);
        //         $dpe['is_read'] = 0;
        //         $this->dpem->set($dpe);
        //         $this->order->trans_commit();
        //     }
        //     unset($delivereds, $delivered);
        // }

        //by Donny Dennison - 08-07-2020 13:59
        //add direct delivery feature
        //48 hours
        // $delivereds = $this->dodm->getDeliveredsDirectDelivery(48);
        // if (count($delivereds)>0) {
        //     foreach ($delivereds as $delivered) {
        //         $du = array();
        //         $du['shipment_status'] = 'succeed';
        //         $this->dodm->update($delivered->nation_code, $delivered->d_order_id, $delivered->c_produk_id, $du);
        //         $this->order->trans_commit();

        //     }
        //     unset($delivereds, $delivered);
        // }

        //by Donny Dennison - 08-07-2020 13:59
        //request by Mr Jackie, change shipment status from delivered to succeed after 48 hours for Qxpress
        // from 48 hours change to 72 hours (3 days)
        // from 72 hours change to 96 hours (4 days)
        //update shipment status from delivered to succeed
        // $delivereds = $this->dodm->getDeliveredsQxpress(4);
        // if (count($delivereds)>0) {
        //     foreach ($delivereds as $delivered) {
        //         $du = array();
        //         $du['shipment_status'] = 'succeed';
        //         $this->dodm->update($delivered->nation_code, $delivered->d_order_id, $delivered->c_produk_id, $du);
        //         $this->order->trans_commit();

        //         //get buyer data
        //         $buyer = $this->bu->getById($delivered->nation_code, $delivered->b_user_id_buyer);
        //         $seller = $this->bu->getById($delivered->nation_code, $delivered->b_user_id_seller);

        //         //get notification config for buyer
        //         $setting_value = 0;
        //         $classified = 'setting_notification_buyer';
        //         $notif_code = 'B3';
        //         $notif_cfg = $this->busm->getValue($delivered->nation_code, $buyer->id, $classified, $notif_code);
        //         if (isset($notif_cfg->setting_value)) {
        //             $setting_value = (int) $notif_cfg->setting_value;
        //         }

        //         $type = 'transaction';
        //         $anotid = 11;
        //         $replacer = array();
        //         $replacer['delivered_name'] = html_entity_decode($delivered->nama,ENT_QUOTES);
        //         $replacer['invoice_code'] = $delivered->invoice_code;

        //         //push notif for buyer
        //         if (strlen($buyer->fcm_token) > 50 && !empty($setting_value)) {
        //             $device = $buyer->device;
        //             $tokens = array($buyer->fcm_token);
        //             if($buyer->language_id == 2) {
        //                 $title = 'Pesanan Terkirim';
        //                 $message = "Pesanan Anda ".html_entity_decode($this->convertEmoji($delivered->nama),ENT_QUOTES)." ($delivered->invoice_code) telah tiba. Mohon konfirmasi!";
        //             } else {
        //                 $title = 'Order Delivered';
        //                 $message = "Your Order ".html_entity_decode($this->convertEmoji($delivered->nama),ENT_QUOTES)." ($delivered->invoice_code) have arrived. Please confirm!";
        //             }
                    
        //             $type = 'transaction';
        //             $image = 'media/pemberitahuan/transaction.png';
        //             $payload = new stdClass();
        //             $payload->id_produk = "".$delivered->c_produk_id;
        //             $payload->id_order = "".$delivered->d_order_id;
        //             $payload->id_order_detail = "".$delivered->c_produk_id;
        //             $payload->b_user_id_buyer = $buyer->id;
        //             $payload->b_user_fnama_buyer = $buyer->fnama;
                    
        //             // by Muhammad Sofi - 27 October 2021 10:12
        //             // if user img & banner not exist or empty, change to default image
        //             // $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
        //             if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
        //                 $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
        //             } else {
        //                 $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
        //             }
        //             $payload->b_user_id_seller = $seller->id;
        //             $payload->b_user_fnama_seller = $seller->fnama;
                    
        //             // by Muhammad Sofi - 27 October 2021 10:12
        //             // if user img & banner not exist or empty, change to default image
        //             // $payload->b_user_image_seller = $this->cdn_url($seller->image);
        //             if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
        //                 $payload->b_user_image_seller = $this->cdn_url($seller->image);
        //             } else {
        //                 $payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
        //             }
        //             $nw = $this->anot->get($nation_code, "push", $type, $anotid, $buyer->language_id);
        //             if (isset($nw->title)) {
        //                 $title = $nw->title;
        //             }
        //             if (isset($nw->message)) {
        //                 $message = $this->__nRep($nw->message, $replacer);
        //             }
        //             if (isset($nw->image)) {
        //                 $image = $nw->image;
        //             }
        //             $image = $this->cdn_url($image);
        //             $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        //             $this->seme_log->write("api_cron", 'API_Cron/Daily::index __pushNotif: '.json_encode($res));
        //         }

        //         //collect array notification list for buyer
        //         $dpe = array();
        //         $dpe['nation_code'] = $delivered->nation_code;
        //         $dpe['b_user_id'] = $buyer->id;
        //         $dpe['id'] = $this->dpem->getLastId($delivered->nation_code, $buyer->id);
        //         $dpe['type'] = "transaction";
        //         if($buyer->language_id == 2) {
        //             $dpe['judul'] = "Pesanan Terkirim";
        //             $dpe['teks'] = "Pesanan Anda ".html_entity_decode($delivered->nama,ENT_QUOTES)." ($delivered->invoice_code) telah tiba. Silakan periksa kondisi produk dan konfirmasi dalam waktu 6 hari.";
        //         } else {
        //             $dpe['judul'] = "Order Delivered";
        //             $dpe['teks'] = "Your order ".html_entity_decode($delivered->nama,ENT_QUOTES)." ($delivered->invoice_code) has arrived. Please check the product's condition and confirm within 6 days.";
        //         }
                
        //         $dpe['cdate'] = "NOW()";
        //         $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
        //         $extras = new stdClass();
        //         $extras->id_produk = "".$delivered->c_produk_id;
        //         $extras->id_order = "".$delivered->d_order_id;
        //         $extras->id_order_detail = "".$delivered->c_produk_id;
        //         $extras->b_user_id_buyer = $buyer->id;
        //         $extras->b_user_fnama_buyer = $buyer->fnama;
                
        //         // by Muhammad Sofi - 27 October 2021 10:12
        //         // if user img & banner not exist or empty, change to default image
        //         // $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
        //         if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
        //             $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
        //         } else {
        //             $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
        //         }
        //         $extras->b_user_id_seller = $seller->id;
        //         $extras->b_user_fnama_seller = $seller->fnama;
                
        //         // by Muhammad Sofi - 27 October 2021 10:12
        //         // if user img & banner not exist or empty, change to default image
        //         // $extras->b_user_image_seller = $this->cdn_url($seller->image);
        //         if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
        //             $extras->b_user_image_seller = $this->cdn_url($seller->image);
        //         } else {
        //             $extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
        //         }
        //         $nw = $this->anot->get($nation_code, "list", $type, $anotid, $buyer->language_id);
        //         if (isset($nw->title)) {
        //             $dpe['judul'] = $nw->title;
        //         }
        //         if (isset($nw->message)) {
        //             $dpe['teks'] = $this->__nRep($nw->message, $replacer);
        //         }
        //         if (isset($nw->image)) {
        //             $dpe['gambar'] = $nw->image;
        //         }
        //         // $dpe['gambar'] = $this->cdn_url($dpe['gambar']);
        //         $dpe['extras'] = json_encode($extras);
        //         $dpe['is_read'] = 0;
        //         $this->dpem->set($dpe);
        //         $this->order->trans_commit();

        //         //get notification config for seller
        //         $setting_value = 0;
        //         $classified = 'setting_notification_seller';
        //         $notif_code = 'S1';
        //         $notif_cfg = $this->busm->getValue($delivered->nation_code, $seller->id, $classified, $notif_code);
        //         if (isset($notif_cfg->setting_value)) {
        //             $setting_value = (int) $notif_cfg->setting_value;
        //         }

        //         $type = 'transaction';
        //         $anotid = 12;
        //         $replacer = array();
        //         $replacer['invoice_code'] = $delivered->invoice_code;
        //         $replacer['delivered_name'] = html_entity_decode($delivered->nama,ENT_QUOTES);
        //         //push notif for seller
        //         if (strlen($seller->fcm_token) > 50 && !empty($setting_value)) {
        //             $device = $seller->device;
        //             $tokens = array($seller->fcm_token);
        //             if($seller->language_id == 2) {
        //                 $title = 'Terkirim';
        //                 $message = "Produk Anda dengan nomor faktur $delivered->invoice_code sudah sampai tujuan.";
        //             } else {
        //                 $title = 'Delivered';
        //                 $message = "Your product with invoice number $delivered->invoice_code has arrived at destination.";
        //             }
                    
        //             $type = 'transaction';
        //             $image = 'media/pemberitahuan/transaction.png';
        //             $payload = new stdClass();
        //             $payload->id_produk = "".$delivered->c_produk_id;
        //             $payload->id_order = "".$delivered->d_order_id;
        //             $payload->id_order_detail = "".$delivered->c_produk_id;
        //             $payload->b_user_id_buyer = $buyer->id;
        //             $payload->b_user_fnama_buyer = $buyer->fnama;
                    
        //             // by Muhammad Sofi - 27 October 2021 10:12
        //             // if user img & banner not exist or empty, change to default image
        //             // $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
        //             if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
        //                 $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
        //             } else {
        //                 $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
        //             }
        //             $payload->b_user_id_seller = $seller->id;
        //             $payload->b_user_fnama_seller = $seller->fnama;
                    
        //             // by Muhammad Sofi - 27 October 2021 10:12
        //             // if user img & banner not exist or empty, change to default image
        //             // $payload->b_user_image_seller = $this->cdn_url($seller->image);
        //             if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
        //                 $payload->b_user_image_seller = $this->cdn_url($seller->image);
        //             } else {
        //                 $payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
        //             }
        //             $nw = $this->anot->get($nation_code, "push", $type, $anotid, $seller->language_id);
        //             if (isset($nw->title)) {
        //                 $title = $nw->title;
        //             }
        //             if (isset($nw->message)) {
        //                 $message = $this->__nRep($nw->message, $replacer);
        //             }
        //             if (isset($nw->image)) {
        //                 $image = $nw->image;
        //             }
        //             $image = $this->cdn_url($image);
        //             $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        //             if ($this->is_log) {
        //                 $this->seme_log->write("api_cron", 'API_Mobile/seller/Order::delivery_progress __pushNotif: '.json_encode($res));
        //             }
        //         }

        //         //collect array notification list for seller
        //         $dpe = array();
        //         $dpe['nation_code'] = $delivered->nation_code;
        //         $dpe['b_user_id'] = $seller->id;
        //         $dpe['id'] = $this->dpem->getLastId($delivered->nation_code, $seller->id);
        //         $dpe['type'] = "transaction";
        //         if($seller->language_id == 2) {
        //             $dpe['judul'] = "Terkirim";
        //             $dpe['teks'] = "Produk Anda ".html_entity_decode($delivered->nama,ENT_QUOTES)." ($delivered->invoice_code) telah sampai di tujuan. Mohon menunggu pembeli untuk mengkonfirmasi penerimaan barang.";
        //         } else {
        //             $dpe['judul'] = "Delivered";
        //             $dpe['teks'] = "Your product ".html_entity_decode($delivered->nama,ENT_QUOTES)." ($delivered->invoice_code) has arrived at its destination. Please wait for the buyer to confirm receipt of the goods.";
        //         }
                
        //         $dpe['cdate'] = "NOW()";
        //         $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
        //         $extras = new stdClass();
        //         $extras->id_produk = "".$delivered->c_produk_id;
        //         $extras->id_order = "".$delivered->d_order_id;
        //         $extras->id_order_detail = "".$delivered->c_produk_id;
        //         $extras->b_user_id_buyer = $buyer->id;
        //         $extras->b_user_fnama_buyer = $buyer->fnama;
                
        //         // by Muhammad Sofi - 27 October 2021 10:12
        //         // if user img & banner not exist or empty, change to default image
        //         // $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
        //         if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
        //             $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
        //         } else {
        //             $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
        //         }
        //         $extras->b_user_id_seller = $seller->id;
        //         $extras->b_user_fnama_seller = $seller->fnama;
                
        //         // by Muhammad Sofi - 27 October 2021 10:12
        //         // if user img & banner not exist or empty, change to default image
        //         // $extras->b_user_image_seller = $this->cdn_url($seller->image);
        //         if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
        //             $extras->b_user_image_seller = $this->cdn_url($seller->image);
        //         } else {
        //             $extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
        //         }
        //         $nw = $this->anot->get($nation_code, "list", $type, $anotid, $seller->language_id);
        //         if (isset($nw->title)) {
        //             $dpe['judul'] = $nw->title;
        //         }
        //         if (isset($nw->message)) {
        //             $dpe['teks'] = $this->__nRep($nw->message, $replacer);
        //         }
        //         if (isset($nw->image)) {
        //             $dpe['gambar'] = $nw->image;
        //         }
        //         $dpe['extras'] = json_encode($extras);
        //         $dpe['is_read'] = 0;
        //         $this->dpem->set($dpe);
        //         $this->order->trans_commit();
        //     }
        //     unset($delivereds, $delivered);
        // }

        //force update buyer comfirmation after sent.
        // $sents = $this->dodim->getSent($buyer_confirm_timeout);
        // if (count($sents)>0) {
        //     $ods = array(); //order detail array();
        //     foreach ($sents as $sent) {
        //         //add to order detail array
        //         $key = $sent->nation_code.'-'.$sent->d_order_id.'-'.$sent->d_order_detail_id;
        //         if (!isset($ods[$key])) {
        //             $obj = new stdClass();
        //             $obj->nation_code = $sent->nation_code;
        //             $obj->d_order_id = $sent->d_order_id;
        //             $obj->d_order_detail_id = $sent->d_order_detail_id;
        //             $obj->invoice_code = $sent->invoice_code;
        //             $obj->nama = $sent->nama;
        //             $obj->c_produk_nama = $sent->nama;
        //             $obj->b_user_id_seller = $sent->b_user_id_seller;
        //             $obj->b_user_id_buyer = $sent->b_user_id_buyer;
        //             $ods[$key] = $obj;
        //         }

        //         //update to order detail item(s)
        //         $du = array();
        //         $du['buyer_status'] = 'accepted';
        //         $du['settlement_status'] = 'solved_to_seller';
        //         $this->dodim->update($sent->nation_code, $sent->d_order_id, $sent->d_order_detail_id, $sent->d_order_detail_item_id, $du);
        //         $this->order->trans_commit();
        //     }
        //     unset($sent,$sents);

        //     if (count($ods)>0) {
        //         foreach ($ods as $od) {
        //             $nation_code = $od->nation_code;
        //             //get buyer and seller
        //             $buyer = $this->bu->getById($od->nation_code, $od->b_user_id_buyer);
        //             $seller = $this->bu->getById($od->nation_code, $od->b_user_id_seller);

        //             //build order history process
        //             $di = array();
        //             $di['nation_code'] = $od->nation_code;
        //             $di['d_order_id'] = $od->d_order_id;
        //             $di['c_produk_id'] = $od->d_order_detail_id;
        //             $di['id'] = $this->dopm->getLastId($od->nation_code, $od->d_order_id, $od->d_order_detail_id);
        //             $di['initiator'] = "System";
        //             $di['nama'] = "Confirm Order (by system)";
        //             $di['deskripsi'] = "Our system has confirmed your order: ".html_entity_decode($od->c_produk_nama,ENT_QUOTES)." ($od->invoice_code). Please rate the seller!";
        //             $di['cdate'] = "NOW()";
        //             $di['is_done'] = "1";
        //             $res2 = $this->dopm->set($di);
        //             $this->order->trans_commit();//get buyer configuration

        //             //get buyer setting
        //             $setting_value = 0;
        //             $classified = 'setting_notification_buyer';
        //             $notif_code = 'B4';
        //             $notif_cfg = $this->busm->getValue($od->nation_code, $buyer->id, $classified, $notif_code);
        //             if (isset($notif_cfg->setting_value)) {
        //                 $setting_value = (int) $notif_cfg->setting_value;
        //             }

        //             $type = 'transaction';
        //             $anotid = 13;
        //             $replacer = array();
        //             $replacer['sent_order'] = html_entity_decode($od->c_produk_nama,ENT_QUOTES);
        //             $replacer['invoice_code'] = $od->invoice_code;
        //             //push notif for buyer
        //             if (strlen($buyer->fcm_token) > 50 && !empty($setting_value)) {
        //                 $device = $buyer->device;
        //                 $tokens = array($buyer->fcm_token);
        //                 if($buyer->language_id == 2) {
        //                     $title = 'Nilai Penjual!';
        //                     $message = "Terima kasih! Anda telah mengkonfirmasi pesanan Anda: ".html_entity_decode($this->convertEmoji($od->c_produk_nama),ENT_QUOTES)." ($od->invoice_code).";
        //                 } else {
        //                     $title = 'Rate the Seller!';
        //                     $message = "Thank You! You have confirmed your order: ".html_entity_decode($this->convertEmoji($od->c_produk_nama),ENT_QUOTES)." ($od->invoice_code).";
        //                 }
                        
        //                 $type = 'transaction';
        //                 $image = 'media/pemberitahuan/transaction.png';
        //                 $payload = new stdClass();
        //                 $payload->id_order = "".$od->d_order_id;
        //                 $payload->id_order_detail = "".$od->d_order_detail_id;
        //                 $payload->id_produk = "".$od->d_order_detail_id;
        //                 $payload->b_user_id_buyer = $buyer->id;
        //                 $payload->b_user_fnama_buyer = $buyer->fnama;
                        
        //                 // by Muhammad Sofi - 27 October 2021 10:12
        //                 // if user img & banner not exist or empty, change to default image
        //                 // $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
        //                 if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
        //                     $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
        //                 } else {
        //                     $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
        //                 }
        //                 $payload->b_user_id_seller = $seller->id;
        //                 $payload->b_user_fnama_seller = $seller->fnama;
                        
        //                 // by Muhammad Sofi - 27 October 2021 10:12
        //                 // if user img & banner not exist or empty, change to default image
        //                 // $payload->b_user_image_seller = $this->cdn_url($seller->image);
        //                 if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
        //                     $payload->b_user_image_seller = $this->cdn_url($seller->image);
        //                 } else {
        //                     $payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
        //                 }
        //                 $nw = $this->anot->get($nation_code, "push", $type, $anotid, $buyer->language_id);
        //                 if (isset($nw->title)) {
        //                     $title = $nw->title;
        //                 }
        //                 if (isset($nw->message)) {
        //                     $message = $this->__nRep($nw->message, $replacer);
        //                 }
        //                 if (isset($nw->image)) {
        //                     $image = $nw->image;
        //                 }
        //                 $image = $this->cdn_url($image);
        //                 $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        //                 if ($this->is_log) {
        //                     $this->seme_log->write("api_cron", 'API_Cron/daily::index --autoBuyerAccept -> __pushNotif'.json_encode($res));
        //                 }
        //             }

        //             //notification list for buyer
        //             $dpe = array();
        //             $dpe['nation_code'] = $od->nation_code;
        //             $dpe['b_user_id'] = $buyer->id;
        //             $dpe['id'] = $this->dpem->getLastId($od->nation_code, $buyer->id);
        //             $dpe['type'] = $type;
        //             if($buyer->language_id == 2) {
        //                 $dpe['judul'] = "Nilai Penjual!";
        //                 $dpe['teks'] = "Terima kasih! Anda telah mengkonfirmasi pesanan Anda: ".html_entity_decode($od->c_produk_nama,ENT_QUOTES)." ($od->invoice_code).";
        //             } else {
        //                 $dpe['judul'] = "Rate the Seller!";
        //                 $dpe['teks'] = "Thanks! You have confirmed your order: ".html_entity_decode($od->c_produk_nama,ENT_QUOTES)." ($od->invoice_code).";
        //             }
                    
        //             $dpe['cdate'] = "NOW()";
        //             $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
        //             $extras = new stdClass();
        //             $extras->id_order = "".$od->d_order_id;
        //             $extras->id_order_detail = "".$od->d_order_detail_id;
        //             $extras->id_produk = "".$od->d_order_detail_id;
        //             $extras->b_user_id_buyer = $buyer->id;
        //             $extras->b_user_fnama_buyer = $buyer->fnama;
                    
        //             // by Muhammad Sofi - 27 October 2021 10:12
        //             // if user img & banner not exist or empty, change to default image
        //             // $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
        //             if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
        //                 $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
        //             } else {
        //                 $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
        //             }
        //             $extras->b_user_id_seller = $seller->id;
        //             $extras->b_user_fnama_seller = $seller->fnama;
                    
        //             // by Muhammad Sofi - 27 October 2021 10:12
        //             // if user img & banner not exist or empty, change to default image
        //             // $extras->b_user_image_seller = $this->cdn_url($seller->image);
        //             if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
        //                 $extras->b_user_image_seller = $this->cdn_url($seller->image);
        //             } else {
        //                 $extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
        //             }
        //             $nw = $this->anot->get($nation_code, "list", $type, $anotid, $buyer->language_id);
        //             if (isset($nw->title)) {
        //                 $dpe['judul'] = $nw->title;
        //             }
        //             if (isset($nw->message)) {
        //                 $dpe['teks'] = $this->__nRep($nw->message, $replacer);
        //             }
        //             if (isset($nw->image)) {
        //                 $dpe['gambar'] = $nw->image;
        //             }
        //             $dpe['extras'] = json_encode($extras);
        //             $dpe['is_read'] = 0;
        //             $this->dpem->set($dpe);
        //             $this->order->trans_commit();

        //             //send email to buyer
        //             if (strlen($buyer->email)>5) {
        //                 $replacer = array();
        //                 $replacer['site_name'] = $this->app_name;
        //                 $replacer['fnama'] = $buyer->fnama;
        //                 $replacer['produk_nama'] = html_entity_decode($od->c_produk_nama,ENT_QUOTES);
        //                 $replacer['invoice_code'] = $od->invoice_code;
        //                 $this->seme_email->flush();
        //                 $this->seme_email->replyto($this->site_name, $this->site_replyto);
        //                 $this->seme_email->from($this->site_email, $this->site_name);
        //                 $this->seme_email->subject('Rate the Seller');
        //                 $this->seme_email->to($buyer->email, $buyer->fnama);
        //                 $this->seme_email->template('rate_the_seller');
        //                 $this->seme_email->replacer($replacer);
        //                 $this->seme_email->send();
        //                 if ($this->is_log) {
        //                     $this->seme_log->write("api_cron", $this->seme_email->getLog());
        //                 }
        //             }

        //             //declare notification
        //             $type = 'transaction';
        //             $anotid = 14;
        //             $replacer = array();
        //             $replacer['invoice_code'] = $od->invoice_code;
        //             $replacer['sent_order'] = html_entity_decode($od->c_produk_nama,ENT_QUOTES);

        //             //get seller configuration
        //             $setting_value = 0;
        //             $classified = 'setting_notification_seller';
        //             $notif_code = 'S3';
        //             $notif_cfg = $this->busm->getValue($od->nation_code, $seller->id, $classified, $notif_code);
        //             if (isset($notif_cfg->setting_value)) {
        //                 $setting_value = (int) $notif_cfg->setting_value;
        //             }

        //             //push notif for seller
        //             if (strlen($seller->fcm_token) > 50 && !empty($setting_value)) {
        //                 $device = $seller->device;
        //                 $tokens = array($seller->fcm_token);
        //                 if($seller->language_id == 2) {
        //                     $title = 'Konfirmasi Pesanan (Oleh Pembeli)';
        //                     $message = "Pelanggan telah mengkonfirmasi produk Anda! (Nomor faktur: $od->invoice_code).";
        //                 } else {
        //                     $title = 'Order Confirmation (By Buyer)';
        //                     $message = "The customer has confirmed your product! (Invoice number: $od->invoice_code).";
        //                 }
                        
        //                 $image = 'media/pemberitahuan/transaction.png';
        //                 $payload = new stdClass();
        //                 $payload->id_produk = "".$od->d_order_detail_id;
        //                 $payload->id_order = "".$od->d_order_id;
        //                 $payload->id_order_detail = "".$od->d_order_detail_id;
        //                 $payload->b_user_id_buyer = $buyer->id;
        //                 $payload->b_user_fnama_buyer = $buyer->fnama;
                        
        //                 // by Muhammad Sofi - 27 October 2021 10:12
        //                 // if user img & banner not exist or empty, change to default image
        //                 // $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
        //                 if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
        //                     $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
        //                 } else {
        //                     $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
        //                 }
        //                 $payload->b_user_id_seller = $seller->id;
        //                 $payload->b_user_fnama_seller = $seller->fnama;
                        
        //                 // by Muhammad Sofi - 27 October 2021 10:12
        //                 // if user img & banner not exist or empty, change to default image
        //                 // $payload->b_user_image_seller = $this->cdn_url($seller->image);
        //                 if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
        //                     $payload->b_user_image_seller = $this->cdn_url($seller->image);
        //                 } else {
        //                     $payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
        //                 }
        //                 $nw = $this->anot->get($nation_code, "push", $type, $anotid, $seller->language_id);
        //                 if (isset($nw->title)) {
        //                     $title = $nw->title;
        //                 }
        //                 if (isset($nw->message)) {
        //                     $message = $this->__nRep($nw->message, $replacer);
        //                 }
        //                 if (isset($nw->image)) {
        //                     $image = $nw->image;
        //                 }
        //                 $image = $this->cdn_url($image);
        //                 $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        //                 $this->seme_log->write("api_cron", 'API_Cron/daily::index --autoBuyerAccept -> __pushNotif '.json_encode($res));
        //             }

        //             //notification list for seller
        //             $dpe = array();
        //             $dpe['nation_code'] = $od->nation_code;
        //             $dpe['b_user_id'] = $seller->id;
        //             $dpe['id'] = $this->dpem->getLastId($od->nation_code, $seller->id);
        //             $dpe['type'] = "transaction";
        //             if($seller->language_id == 2) {
        //                 $dpe['judul'] = "Konfirmasi Pesanan (Oleh Pembeli)";
        //                 $dpe['teks'] = "Pelanggan telah mengkonfirmasi produk Anda: ".html_entity_decode($od->c_produk_nama,ENT_QUOTES)." ($od->invoice_code). Sistem kami akan mengirimkan pembayaran maksimal 7 hari. Jika Anda tidak menerimanya dalam waktu 7 hari, silakan hubungi layanan pelanggan kami segera.";
        //             } else {
        //                 $dpe['judul'] = "Confirm Order (By Buyer)";
        //                 $dpe['teks'] = "The customer has confirmed your product: ".html_entity_decode($od->c_produk_nama,ENT_QUOTES)." ($od->invoice_code). Our system will send the payment for a maximum of 7 days. If you don't receive it within 7 days, please contact our customer service immediately.";
        //             }
                    
        //             $dpe['cdate'] = "NOW()";
        //             $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
        //             $extras = new stdClass();
        //             $extras->id_order = "".$od->d_order_id;
        //             $extras->id_produk = "".$od->d_order_detail_id;
        //             $extras->id_order_detail = "".$od->d_order_detail_id;
        //             $extras->b_user_id_buyer = $buyer->id;
        //             $extras->b_user_fnama_buyer = $buyer->fnama;
                    
        //             // by Muhammad Sofi - 27 October 2021 10:12
        //             // if user img & banner not exist or empty, change to default image
        //             // $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
        //             if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
        //                 $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
        //             } else {
        //                 $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
        //             }
        //             $extras->b_user_id_seller = $seller->id;
        //             $extras->b_user_fnama_seller = $seller->fnama;
        //             // by Muhammad Sofi - 27 October 2021 10:12
        //             // if user img & banner not exist or empty, change to default image
        //             // $extras->b_user_image_seller = $this->cdn_url($seller->image);
        //             if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
        //                 $extras->b_user_image_seller = $this->cdn_url($seller->image);
        //             } else {
        //                 $extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
        //             }
        //             $nw = $this->anot->get($nation_code, "list", $type, $anotid, $seller->language_id);
        //             if (isset($nw->title)) {
        //                 $dpe['judul'] = $nw->title;
        //             }
        //             if (isset($nw->message)) {
        //                 $dpe['teks'] = $this->__nRep($nw->message, $replacer);
        //             }
        //             if (isset($nw->image)) {
        //                 $dpe['gambar'] = $nw->image;
        //             }
        //             $dpe['extras'] = json_encode($extras);
        //             $this->dpem->set($dpe);
        //             $this->order->trans_commit();

        //             //send email to admin
        //             if ($this->email_send) {
        //                 $admin = $this->apm->getEmailActive();
        //                 $replacer = array();
        //                 $replacer['site_name'] = $this->app_name;
        //                 $replacer['invoice_code'] = $od->invoice_code;
        //                 $replacer['seller_id'] = $seller->id;
        //                 $replacer['seller_fnama'] = $seller->fnama;
        //                 $replacer['admincms_link'] = base_url_admin("ecommerce/transactionhistory/");
        //                 $this->seme_email->flush();
        //                 $this->seme_email->replyto($this->site_name, $this->site_replyto);
        //                 $this->seme_email->from($this->site_email, $this->site_name);
        //                 $this->seme_email->subject('Confirmed by the Buyer');
        //                 foreach ($admin as $adm) {
        //                     if (strlen($adm->email)>4) {
        //                         $this->seme_email->to($adm->email, $adm->nama);
        //                     }
        //                 }
        //                 $this->seme_email->template('confirm_order_to_admin');
        //                 $this->seme_email->replacer($replacer);
        //                 $this->seme_email->send();
        //                 if ($this->is_log) {
        //                     $this->seme_log->write("api_cron", $this->seme_email->getLog());
        //                 }
        //             }

        //             $du = array();
        //             $du['buyer_confirmed'] = 'confirmed';
        //             $du['settlement_status'] = 'unconfirmed';
        //             $du['received_date'] = 'NOW()';
        //             $du['is_calculated'] = '0';
        //             $this->dodm->update($od->nation_code, $od->d_order_id, $od->d_order_detail_id, $du);
        //             $this->order->trans_commit();

        //         }
        //         unset($ods, $od);
        //     }
        // }

        //by Donny Dennison - 29 april 2021 14:06
        //add-void-and-refund-2c2p-after-reject-by-seller
        //START by Donny Dennison - 29 april 2021 14:06

        // //recalculating profit, seller settlement amount and refund from seller rejected
        // $sellerrejecteds = $this->dodim->getSellerRejected();
        // if (count($sellerrejecteds)>0) {
        //     foreach ($sellerrejecteds as $sr) {
        //         //refund calculation
        //         $refund_amount = $sr->grand_total;

        //         //update to database
        //         $du = array();
        //         $du['refund_amount'] = $refund_amount;
        //         $du['settlement_status'] = 'unconfirmed';
        //         $du['is_calculated'] = '1';
        //         $this->dodm->update($sr->nation_code, $sr->d_order_id, $sr->d_order_detail_id, $du);
        //         $this->order->trans_commit();//get buyer configuration
        //     }
        // }

        //END by Donny Dennison - 29 april 2021 14:06

        //close transaction
        $this->order->trans_end();

        //update apikey
        // $this->load('api_cron/a_apikey_model','aakm');
        // $apikeys = $this->aakm->getActive();
        // if(is_array($apikeys) && count($apikeys)>0){
        //   foreach($apikeys as $apikey){
        //     $du = array();
        //     $du['str'] = $this->__codeGen($apikey->nation_code);
        //     $du['code'] = hash('sha256',$du['str']);
        //     $this->aakm->update($apikey->nation_code,$apikey->id,$du);
        //     $this->seme_log->write("api_cron", 'API_Cron/daily::index -- INFO apikey re-generated: OK');
        //   }
        //   unset($apikeys, $apikey);
        // }

        $this->seme_log->write("api_cron", "API_Cron/Daily::index END");

        die();
    }

}
