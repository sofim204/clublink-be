<?php
// class Shipment extends JI_Controller
// {
//     public $gogovan_api = 'https://stag-sg.gogovan.tech/';
//     public $gogovan_env = 'staging';
//     public $qxpress_api = 'http://apitest.qxpress.asia/';
//     public $qxpress_env = 'staging';
//     public $qx_api_key = 'u2P3Mq7cNeG4y8bCT8Qw';
//     public $qx_account_id = 'QxTestAPI_SG';
//     public $is_log = 1;
//     public $email_send = 1;

//     public function __construct()
//     {
//         parent::__construct();
//         $this->lib("seme_log");
//         $this->lib("seme_email");
//         $this->load("api_cron/b_user_model", "bu");
//         $this->load("api_cron/b_user_setting_model", "busm");
//         $this->load("api_cron/d_order_model", "order");
//         $this->load("api_cron/d_order_detail_model", "dodm");
//         $this->load("api_cron/d_order_proses_model", "dopm");
//         $this->load("api_cron/d_pemberitahuan_model", "dpem");

//         if ($this->gogovan_env == 'production') {
//             $this->gogovan_api = 'https://stag-sg.gogovan.tech/';
//         }
//         if ($this->qxpress_env == 'production') {
//             $this->qxpress_env = 'http://api.qxpress.asia/';
//         }
//     }

//     //qxpress create order
//     private function __trackQXpress($shipment_tranid)
//     {
//         $ch = curl_init();
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
//         curl_setopt($ch, CURLOPT_URL, $this->qxpress_api.'shipment/Tracking.php');
//         $headers = array();
//         $headers[] = 'Content-Type: Text/xml';
//         $headers[] = 'Accept: Text/xml';
//         $postdata = array(
//       'apiKey' => $this->qx_api_key,
//       'accountId' => $this->qx_account_id,
//       'trackingNo' => $shipment_tranid
//     );
//         curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));

//         $result = curl_exec($ch);
//         if (curl_errno($ch)) {
//             return 0;
//             //echo 'Error:' . curl_error($ch);
//         }
//         curl_close($ch);
//         return $result;
//     }

//     //gogovan check status / tracking order
//     private function __trackGogovan($shipment_tranid)
//     {
//         $ch = curl_init();
//         curl_setopt($ch, CURLOPT_URL, $this->gogovan_api.'api/v0/orders/'.$shipment_tranid.'.json');
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
//         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//         $headers = array();
//         $headers[] = 'Gogovan-Api-Key: 3af4ba76-3767-4963-9680-327bb6d391d1';
//         $headers[] = 'Gogovan-User-Language: en-US';
//         $headers[] = 'Accept: */*';
//         $headers[] = 'Cache-Control: no-cache';
//         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//         curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
//         $result = curl_exec($ch);
//         if (curl_errno($ch)) {
//             return 0;
//             //echo 'Error:' . curl_error($ch);
//         }
//         curl_close($ch);
//         return $result;
//     }
//     private function ___output($ordered_product)
//     {
//         $dt = new stdClass();
//         $dt->seller_status = $ordered_product->seller_status;
//         $dt->shipment_service = $ordered_product->shipment_service;
//         $dt->shipment_type = $ordered_product->shipment_type;
//         $dt->shipment_cost = $ordered_product->shipment_cost;
//         $dt->shipment_cost_add = $ordered_product->shipment_cost_add;
//         $dt->shipment_tranid = $ordered_product->shipment_tranid;
//         $dt->shipment_vehicle = $ordered_product->shipment_vehicle;
//         $dt->shipment_status = $ordered_product->shipment_status;
//         $dt->delivery_date = $ordered_product->delivery_date;
//         $dt->pickup_date = $ordered_product->pickup_date;
//         $dt->buyer_confirmed = $ordered_product->buyer_confirmed;
//         unset($ordered_product);
//         $this->debug($dt);
//     }

//     public function cancelled($nation_code, $d_order_id, $c_produk_id)
//     {
//         $nation_code = (int) $nation_code;
//         if ($nation_code<=0) {
//             http_response_code("500");
//             die('ERROR 1st param: $nation_code');
//         }
//         $d_order_id = (int) $d_order_id;
//         if ($d_order_id<=0) {
//             http_response_code("500");
//             die('ERROR 2nd param: $d_order_id');
//         }
//         $c_produk_id = (int) $c_produk_id;
//         if ($c_produk_id<=0) {
//             http_response_code("500");
//             die('ERROR 3rd param: $c_produk_id');
//         }
//         $ordered_product = $this->dodm->getByIdFull($nation_code, $d_order_id, $c_produk_id);
//         if (!isset($ordered_product->d_order_id)) {
//             http_response_code("500");
//             echo('ERROR: Ordered Product not found');
//             die();
//         }
//         $du = array();
//         $du['pickup_date'] = "NOW()";
//         $du['shipment_status'] = "delivered";
//         $du['received_date'] = 'NULL';
//         $res = $this->dodm->update($nation_code, $d_order_id, $c_produk_id, $du);
//         if ($res) {
//             $this->order->trans_start();
//             $di = array();
//             $di['nation_code'] = $nation_code;
//             $di['d_order_id'] = $d_order_id;
//             $di['c_produk_id'] = $c_produk_id;
//             $di['id'] = (int) $this->dopm->getLastId($nation_code, $d_order_id, $c_produk_id);
//             $di['nama'] = 'Shipment Cancelled';
//             $di['deskripsi'] = 'Shipment process was cancelled by Shipping Serivce, system will try again to create shipment request';
//             $di['cdate'] = "NOW()";
//             $di['initiator'] = "System";
//             $di['is_done'] = 1;
//             $this->dopm->set($di);
//             $this->order->trans_commit();
//             $this->order->trans_end();
//             //output
//             $ordered_product = $this->dodm->getByIdFull($nation_code, $d_order_id, $c_produk_id);
//             $this->___output($ordered_product);
//             unset($ordered_product);
//             die("Success: Shipment process was noted!");
//         } else {
//             die("Failed: Cant change shipment status!");
//         }
//     }
//     public function pickup($nation_code, $d_order_id, $c_produk_id)
//     {
//         $nation_code = (int) $nation_code;
//         if ($nation_code<=0) {
//             http_response_code("500");
//             die('ERROR 1st param: $nation_code');
//         }
//         $d_order_id = (int) $d_order_id;
//         if ($d_order_id<=0) {
//             http_response_code("500");
//             die('ERROR 2nd param: $d_order_id');
//         }
//         $c_produk_id = (int) $c_produk_id;
//         if ($c_produk_id<=0) {
//             http_response_code("500");
//             die('ERROR 3rd param: $c_produk_id');
//         }
//         $ordered_product = $this->dodm->getByIdFull($nation_code, $d_order_id, $c_produk_id);
//         if (!isset($ordered_product->d_order_id)) {
//             http_response_code("500");
//             echo('ERROR: Ordered Product not found');
//             die();
//         }
//         $du = array();
//         $du['pickup_date'] = "NOW()";
//         $du['shipment_status'] = "delivered";
//         $du['received_date'] = 'NULL';
//         $res = $this->dodm->update($nation_code, $d_order_id, $c_produk_id, $du);
//         if ($res) {
//             $this->order->trans_start();
//             $di = array();
//             $di['nation_code'] = $nation_code;
//             $di['d_order_id'] = $d_order_id;
//             $di['c_produk_id'] = $c_produk_id;
//             $di['id'] = (int) $this->dopm->getLastId($nation_code, $d_order_id, $c_produk_id);
//             $di['nama'] = 'Shipment Pickup';
//             $di['deskripsi'] = 'Ordered product has been picked up by Shipping Service';
//             $di['cdate'] = "NOW()";
//             $di['initiator'] = "System";
//             $di['is_done'] = 1;
//             $this->dopm->set($di);
//             $this->order->trans_commit();
//             $this->order->trans_end();

//             //output
//             $ordered_product = $this->dodm->getByIdFull($nation_code, $d_order_id, $c_produk_id);
//             $this->___output($ordered_product);
//             unset($ordered_product);
//             die("Success: Shipment process was noted!");
//         } else {
//             die("Failed: Cant change shipment status!");
//         }
//     }
//     public function delivered($nation_code, $d_order_id, $c_produk_id)
//     {
//         $nation_code = (int) $nation_code;
//         if ($nation_code<=0) {
//             http_response_code("500");
//             die('ERROR 1st param: $nation_code');
//         }
//         $d_order_id = (int) $d_order_id;
//         if ($d_order_id<=0) {
//             http_response_code("500");
//             die('ERROR 2nd param: $d_order_id');
//         }
//         $c_produk_id = (int) $c_produk_id;
//         if ($c_produk_id<=0) {
//             http_response_code("500");
//             die('ERROR 3rd param: $c_produk_id');
//         }
//         $ordered_product = $this->dodm->getByIdFull($nation_code, $d_order_id, $c_produk_id);
//         if (!isset($ordered_product->d_order_id)) {
//             http_response_code("500");
//             echo('ERROR: Ordered Product not found');
//             die();
//         }

//         //log
//         if ($this->is_log) {
//             $this->seme_log->write("api_cron", "API_CRON/Shipment::delivered MAGIC_LINK --tiggered nation_code: $nation_code, d_order_id: $d_order_id, c_produk_id: $c_produk_id");
//         }

//         $du = array();
//         $du['pickup_date'] = "NOW()";
//         $du['shipment_status'] = "delivered";
//         $du['received_date'] = 'NULL';
//         $res = $this->dodm->update($nation_code, $d_order_id, $c_produk_id, $du);
//         if ($res) {
//             //open transaction
//             $this->order->trans_start();

//             //collect data
//             $di = array();
//             $di['nation_code'] = $nation_code;
//             $di['d_order_id'] = $d_order_id;
//             $di['c_produk_id'] = $c_produk_id;
//             $di['id'] = (int) $this->dopm->getLastId($nation_code, $d_order_id, $c_produk_id);
//             $di['nama'] = 'Shipment Delivered';
//             $di['deskripsi'] = 'Ordered product has been shipping by Seller';
//             $di['cdate'] = "NOW()";
//             $di['initiator'] = "System";
//             $di['is_done'] = 1;
//             $this->dopm->set($di);
//             $this->order->trans_commit();

//             //end transaction
//             $this->order->trans_end();

//             //output
//             $ordered_product = $this->dodm->getByIdFull($nation_code, $d_order_id, $c_produk_id);
//             $this->___output($ordered_product);
//             unset($ordered_product);
//             die("Success: Shipment process was noted!");
//         } else {
//             die("Failed: Cant change shipment status!");
//         }
//     }
        
//     /**
//      * Force shipment status to received
//      * Set d_order_detail.shipment_status = succeed
//      * @param  int $nation_code     [description]
//      * @param  int $d_order_id      ID from d_order
//      * @param  int $c_produk_id     ID from d_order_detail
//      */
//     public function succeed($nation_code, $d_order_id, $c_produk_id)
//     {
//         $nation_code = (int) $nation_code;
//         if ($nation_code<=0) {
//             http_response_code("500");
//             die('ERROR 1st param: $nation_code');
//         }
//         $d_order_id = (int) $d_order_id;
//         if ($d_order_id<=0) {
//             http_response_code("500");
//             die('ERROR 2nd param: $d_order_id');
//         }
//         $c_produk_id = (int) $c_produk_id;
//         if ($c_produk_id<=0) {
//             http_response_code("500");
//             die('ERROR 3rd param: $c_produk_id');
//         }
//         $ordered_product = $this->dodm->getByIdFull($nation_code, $d_order_id, $c_produk_id);
//         if (!isset($ordered_product->d_order_id)) {
//             http_response_code("500");
//             echo('ERROR: Ordered Product not found');
//             die();
//         }
//         //log
//         if ($this->is_log) {
//             $this->seme_log->write("api_cron", "API_CRON/Shipment::delivered MAGIC_LINK --tiggered nation_code: $nation_code, d_order_id: $d_order_id, c_produk_id: $c_produk_id");
//         }

//         $du = array();
//         $du['received_date'] = 'NOW()';
//         $du['shipment_status'] = "succeed";
//         $du['buyer_confirmed'] = "unconfirmed";
//         $du['settlement_status'] = "unconfirmed";
//         $res = $this->dodm->update($nation_code, $d_order_id, $c_produk_id, $du);
//         if ($res) {
//             //open transaction
//             $this->order->trans_start();

//             //collect data
//             $di = array();
//             $di['nation_code'] = $nation_code;
//             $di['d_order_id'] = $d_order_id;
//             $di['c_produk_id'] = $c_produk_id;
//             $di['id'] = (int) $this->dopm->getLastId($nation_code, $d_order_id, $c_produk_id);
//             $di['nama'] = 'Shipment Succeed';
//             $di['deskripsi'] = 'Ordered product has been successfully received by Seller from Shipping Service';
//             $di['cdate'] = "NOW()";
//             $di['initiator'] = "System";
//             $di['is_done'] = 1;
//             $this->dopm->set($di);
//             $this->order->trans_commit();

//             //get buyer data
//             $buyer = $this->bu->getById($nation_code, $ordered_product->b_user_id_buyer);
//             $seller = $this->bu->getById($nation_code, $ordered_product->b_user_id_seller);

//             //get notification config for buyer
//             $setting_value = 0;
//             $classified = 'setting_notification_buyer';
//             $notif_code = 'B3';
//             $notif_cfg = $this->busm->getValue($nation_code, $buyer->id, $classified, $notif_code);
//             if (isset($notif_cfg->setting_value)) {
//                 $setting_value = (int) $notif_cfg->setting_value;
//             }
//             if ($this->is_log) {
//                 $this->seme_log->write("api_cron", "API_CRON/Shipment::succeed MAGIC_LINK --pushNotifBuyer ID: $buyer->id, Device: $buyer->device, Classified: $classified, Code: $notif_code, value: $setting_value");
//             }

//             //push notif for buyer
//             if (strlen($buyer->fcm_token) > 50 && !empty($setting_value)) {
//                 $device = $buyer->device;
//                 $tokens = array($buyer->fcm_token);
//                 if($buyer->language_id == 2) {
//                     $title = 'Pesanan Terkirim';
//                     $message = "Pesanan Anda $ordered_product->nama ($ordered_product->invoice_code) telah tiba. Mohon konfirmasi!";
//                 } else {
//                     $title = 'Order Delivered';
//                     $message = "Your Order $ordered_product->nama ($ordered_product->invoice_code) have arrived. Please confirm!";
//                 }
                
//                 $type = 'transaction';
//                 $image = 'media/pemberitahuan/transaction.png';
//                 $payload = new stdClass();
//                 $payload->id_order = $ordered_product->d_order_id;
//                 $payload->id_produk = $ordered_product->c_produk_id;
//                 $payload->id_order_detail = $ordered_product->c_produk_id;
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
//                 $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
//                 if ($this->is_log) {
//                     $this->seme_log->write("api_cron", 'API_Cron/Shipment::succeed __pushNotifBuyer: '.json_encode($res));
//                 }
//             }

//             //collect array notification list for buyer
//             $dpe = array();
//             $dpe['nation_code'] = $nation_code;
//             $dpe['b_user_id'] = $buyer->id;
//             $dpe['id'] = $this->dpem->getLastId($ordered_product->nation_code, $buyer->id);
//             $dpe['type'] = "transaction";
//             if($buyer->language_id == 2) {
//                 $dpe['judul'] = "Pesanan Terkirim";
//                 $dpe['teks'] = "Pesanan Anda $ordered_product->nama ($ordered_product->invoice_code) telah tiba. Silakan periksa kondisi produk dan konfirmasi dalam waktu 3 hari.";
//             } else {
//                 $dpe['judul'] = "Order Delivered";
//                 $dpe['teks'] = "Your Order $ordered_product->nama ($ordered_product->invoice_code) have arrived. Please check the product condition and confirm within 3 days.";
//             }
            
//             $dpe['cdate'] = "NOW()";
//             $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
//             $extras = new stdClass();
//             $extras->id_order = $ordered_product->d_order_id;
//             $extras->id_produk = $ordered_product->c_produk_id;
//             $extras->id_order_detail = $ordered_product->c_produk_id;
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
//             $dpe['extras'] = json_encode($extras);
//             $this->dpem->set($dpe);
//             $this->order->trans_commit();

//             //get notification config for seller
//             $setting_value = 0;
//             $classified = 'setting_notification_seller';
//             $notif_code = 'S1';
//             $notif_cfg = $this->busm->getValue($nation_code, $seller->id, $classified, $notif_code);
//             if (isset($notif_cfg->setting_value)) {
//                 $setting_value = (int) $notif_cfg->setting_value;
//             }
//             if ($this->is_log) {
//                 $this->seme_log->write("api_cron", "API_CRON/Shipment::succeed MAGIC_LINK --pushNotifSeller ID: $buyer->id, Device: $seller->device, Classified: $classified, Code: $notif_code, value: $setting_value");
//             }

//             //push notif for seller
//             if (strlen($seller->fcm_token) > 50 && !empty($setting_value)) {
//                 $device = $seller->device;
//                 $tokens = array($seller->fcm_token);
//                 if($seller->language_id == 2) {
//                     $title = 'Terkirim';
//                     $message = "Produk Anda dengan nomor faktur $ordered_product->invoice_code sudah sampai tujuan.";
//                 } else {
//                     $title = 'Delivered';
//                     $message = "Your product with invoice number $ordered_product->invoice_code has arrived at destination.";
//                 }
                
//                 $type = 'transaction';
//                 $image = 'media/pemberitahuan/transaction.png';
//                 $payload = new stdClass();
//                 $payload->id_order = $ordered_product->d_order_id;
//                 $payload->id_produk = $ordered_product->c_produk_id;
//                 $payload->id_order_detail = $ordered_product->c_produk_id;
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
//                 $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
//                 if ($this->is_log) {
//                     $this->seme_log->write("api_cron", 'API_Cron/Shipment::succeed __pushNotifSeller: '.json_encode($res));
//                 }
//             }

//             //collect array notification list for seller
//             $dpe = array();
//             $dpe['nation_code'] = $nation_code;
//             $dpe['b_user_id'] = $seller->id;
//             $dpe['id'] = $this->dpem->getLastId($nation_code, $seller->id);
//             $dpe['type'] = "transaction";
//             if($seller->language_id == 2) {
//                 $dpe['judul'] = "Terkirim";
//                 $dpe['teks'] = "Produk Anda $ordered_product->nama ($ordered_product->invoice_code) telah sampai di tujuan. Mohon menunggu pembeli untuk mengkonfirmasi penerimaan barang.";
//             } else {
//                 $dpe['judul'] = "Delivered";
//                 $dpe['teks'] = "Your product $ordered_product->nama ($ordered_product->invoice_code) has arrived at the destination. Please wait for the buyer to confirm receipt of the goods.";
//             }
            
//             $dpe['cdate'] = "NOW()";
//             $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
//             $extras = new stdClass();
//             $extras->id_order = $ordered_product->d_order_id;
//             $extras->id_produk = $ordered_product->c_produk_id;
//             $extras->id_order_detail = $ordered_product->c_produk_id;
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
//             $dpe['extras'] = json_encode($extras);
//             $this->dpem->set($dpe);
//             $this->order->trans_commit();

//             //end transaction
//             $this->order->trans_end();

//             //output
//             $ordered_product = $this->dodm->getByIdFull($nation_code, $d_order_id, $c_produk_id);
//             $this->___output($ordered_product);
//             unset($ordered_product);
//             die("Success: Shipment process was noted!");
//         } else {
//             die("Failed: Cant change shipment status!");
//         }
//     }

//     public function index()
//     {
//         http_response_code("404");
//         $this->__json_out(array());
//     }

//     public function check()
//     {
//         //start transaction
//         $this->order->trans_start();

//         //get delivered order
//         $terkirims = $this->dodm->getTerkirims();
//         if (count($terkirims)>0) {
//             foreach ($terkirims as $t) {
//                 if (strtolower($t->shipment_service) == 'gogovan') {
//                     $res = $this->__trackGogovan($t->shipment_tranid);
//                     $this->debug($res);
//                     die();
//                 } elseif (strtolower($t->shipment_service) == 'qxpress') {
//                     if (strtolower($t->shipment_type) == 'next day' || strtolower($t->shipment_type) == 'nextday') {
//                         $rq = $this->__trackQXpress($t->shipment_tranid);
//                         $sodt = simplexml_load_string($rq);
//                         if ($sodt === false) {
//                             //parsing error
//                             $cqxe = '';
//                             foreach (libxml_get_errors() as $error) {
//                                 $cqxe .= $error->message.', ';
//                             }
//                             $cqxe = rtrim($cqxe, ', ');
//                             if ($this->is_log) {
//                                 $this->seme_log->write("api_cron", "api_cron/Shipment::check() -> __trackQXpress PARSE_ERROR: ".$cqxe);
//                             }
//                         } else {
//                             //parse OK
//                             if (isset($sodt->ResultObject->tracking_history->History)) {
//                                 if (isset($sodt->ResultObject->tracking_history->History[0])) {
//                                     $h = $sodt->ResultObject->tracking_history->History[0];
//                                     $dopm = $this->dopm->checkByInitiatorAndNama($t->nation_code, $t->d_order_id, $t->c_produk_id, "System Tracker", $h->status);
//                                     if (isset($h->status)) {

//                     //insert into d_order_process for historical process
//                                         if (!isset($dopm->id)) {
//                                             $di = array();
//                                             $di['nation_code'] = $t->nation_code;
//                                             $di['d_order_id'] = $t->d_order_id;
//                                             $di['c_produk_id'] = $t->c_produk_id;
//                                             $di['id'] = $this->dopm->getLastId($t->nation_code, $t->d_order_id, $t->c_produk_id);
//                                             $di['initiator'] = "System Tracker";
//                                             $di['nama'] = $h->status;
//                                             $di['cdate'] = 'NOW()';
//                                             if (isset($h->date)) {
//                                                 $di['cdate'] = date("Y-m-d H:i:s", strtotime($h->date));
//                                             }
//                                             $di['deskripsi'] = $h->status;
//                                             if (isset($h->location)) {
//                                                 if (strlen($h->location)) {
//                                                     $di['deskripsi'] .= ', location '.$h->location;
//                                                 }
//                                             }
//                                             $this->dopm->set($di);
//                                             $this->order->trans_commit();
//                                         }
//                                     }
//                                 }
//                             }
//                         }

//                         //$this->debug($res);
//                         die();
//                     } else {
//                         //no tracking for qxpress same day
//                     }
//                 } else {
//                     //do nothing
//                 }
//             }
//         }

//         //end transaction
//         $this->order->trans_end();
//     }
// }