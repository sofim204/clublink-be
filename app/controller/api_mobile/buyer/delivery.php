<?php
class Delivery extends JI_Controller
{
    public $is_log = 1;
    public $email_send = 1;

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->lib("seme_email");
        $this->load("api_mobile/a_notification_model", "anot");
        $this->load("api_mobile/a_pengguna_model", "apm");
        $this->load("api_mobile/b_kategori_model3", "bkm3");
        $this->load("api_mobile/b_user_model", "bu");
        $this->load("api_mobile/b_user_alamat_model", "bua");
        $this->load("api_mobile/b_user_setting_model", "busm");
        $this->load("api_mobile/common_code_model", "ccm");
        $this->load("api_mobile/c_produk_model", "cpm");
        $this->load("api_mobile/c_produk_foto_model", "cpfm");
        $this->load("api_mobile/b_user_model", "bu");
        $this->load("api_mobile/d_wishlist_model", "dwlm");
        $this->load("api_mobile/d_order_model", "order");
        $this->load("api_mobile/d_order_alamat_model", "doam");
        $this->load("api_mobile/d_order_detail_model", "dodm");
        $this->load("api_mobile/d_order_detail_item_model", "dodim");
        $this->load("api_mobile/d_order_proses_model", "dopm");
        $this->load("api_mobile/d_pemberitahuan_model", "dpem");
    }
    private function __earningCalculation($nation_code, $harga_jual, $berat)
    {
        //default response
        $data = new stdClass();
        $data->harga_jual= 0.0;
        $data->biaya = new stdClass();
        $data->biaya->admin = 0.0;
        $data->biaya->asuransi = 0.0;
        $data->biaya->admin_teks = "0%";
        $data->pendapatan = 0.0;
        //$data->debug = new stdClass();

        //convert to string
        $data->harga_jual = strval($data->harga_jual);
        $data->biaya->admin = strval($data->biaya->admin);
        $data->biaya->asuransi = strval($data->biaya->asuransi);
        $data->pendapatan = strval($data->pendapatan);

        $harga_jual = (float) $harga_jual;
        $harga_jual = round($harga_jual, 2);
        $data->harga_jual = $harga_jual;

        $berat = (float) $berat;
        $berat = round($berat, 2);

        //declare and get variable
        $admin_pg = 0.35; //payment gateway deduction value
        $admin_pg_jenis = 'percentage';
        $admin_fee = 0.65;
        $admin_fee_jenis = 'percentage';
        $asuransi = 0.0;
        $asuransi_jenis = 'percentage';

        //get preset from DB
        $fee = array();
        $presets = $this->ccm->getByClassified($nation_code, "product_fee");
        if (count($presets)>0) {
            foreach ($presets as $pre) {
                $fee[$pre->code] = $pre;
            }
            unset($pre); //free some memory
            unset($presets); //free some memory
            $data->biaya->admin = 0.0;
            $admin_pg = 0.0;
            $admin_fee = 0.0;
        }

        //passing into current var
        if (isset($fee['F0']->remark)) {
            $admin_pg = round($fee['F0']->remark, 2);
        } //pg deduction value
        if (isset($fee['F1']->remark)) {
            $admin_pg_jenis = $fee['F1']->remark;
        } //pg deduction type
        if (isset($fee['F2']->remark)) {
            $admin_fee = round($fee['F2']->remark, 2);
        } //admin deduction value
        if (isset($fee['F3']->remark)) {
            $admin_fee_jenis = $fee['F3']->remark;
        } //admin deduction type
        if (isset($fee['F4']->remark)) {
            $asuransi = round($fee['F4']->remark, 2);
        } //insurance deduction value
        if (isset($fee['F5']->remark)) {
            $asuransi_jenis = $fee['F5']->remark;
        } //insurance deduction type

        //calculating admin fee
        $data->biaya->admin_teks = 0;
        if ($admin_pg_jenis == 'percentage') {
            $data->biaya->admin += round($harga_jual * ($admin_pg/100), 2);
            $data->biaya->admin_teks += $admin_pg;
        } else {
            $data->biaya->admin += round($admin_pg, 2);
        }
        if ($admin_fee_jenis == 'percentage') {
            $data->biaya->admin += round($harga_jual * ($admin_fee/100), 2);
            $data->biaya->admin_teks += $admin_fee;
        } else {
            $data->biaya->admin += round($admin_fee, 2);
        }
        //$data->biaya->admin_teks = round($data->biaya->admin_teks,0).'%';
        $data->biaya->admin_teks = round($data->biaya->admin_teks, 0);

        //calculating insurance
        if ($asuransi_jenis == 'percentage') {
            $data->biaya->asuransi = round($harga_jual * ($asuransi/100), 2);
        } else {
            $data->biaya->asuransi = round($asuransi, 2);
        }

        //end result
        $data->pendapatan = round($harga_jual - ($data->biaya->admin + $data->biaya->asuransi), 2);

        //render output
        $data->harga_jual = strval($data->harga_jual);
        $data->biaya->admin = strval($data->biaya->admin);
        $data->biaya->asuransi = strval($data->biaya->asuransi);
        $data->biaya->admin_teks = strval($data->biaya->admin_teks);
        $data->pendapatan = strval($data->pendapatan);

        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::__earningCalculation -> ".json_encode($data));
        }
        return $data;
    }

    private function __orderAddresses($nation_code, $pelanggan, $order)
    {
        //addresses init
        $addresses = new stdClass();
        $addresses->billing = new stdClass();
        $addresses->shipping = new stdClass();

        //get billing address
        $jenis_alamat = 'Billing Address';
        $address_status = $this->ccm->getByClassifiedByCodeName($nation_code, "address", $jenis_alamat);
        if(!isset($address_status->code)){
          $address_status = new stdClass();
          $address_status->code = 'A1';
        }
        $addresses->billing = $this->doam->getByOrderIdBuyerIdStatusAddress($nation_code, $order->id, $pelanggan->id, $address_status->code);

        //get shipping address
        //by Donny Dennison - 17 juni 2020 20:18
        // request by Mr Jackie change Shipping Address into Receiving Address
        // $jenis_alamat = 'Shipping Address';
        $jenis_alamat = 'Receiving Address';
        $address_status = $this->ccm->getByClassifiedByCodeName($nation_code, "address", $jenis_alamat);
        if(!isset($address_status->code)){
          $address_status = new stdClass();
          $address_status->code = 'A2';
        }
        $addresses->shipping = $this->doam->getByOrderIdBuyerIdStatusAddress($nation_code, $order->id, $pelanggan->id, $address_status->code);
        return $addresses;
    }
    
    /**
     * Recall user login in background, for solving issue PROD 30/07/2020 no 2
     * @param  [type] $apikey            [description]
     * @param  [type] $nation_code       [description]
     * @param  [type] $apisess           [description]
     * @param  [type] $d_order_id        [description]
     * @param  [type] $d_order_detail_id [description]
     */
    private function __callBuyerOrderdetail($apikey,$nation_code, $apisess, $d_order_id, $d_order_detail_id)
    {
        $this->lib("seme_curl");
        $url = base_url("api_mobile/buyer/order/detail/$d_order_id/$d_order_detail_id/?apikey=$apikey&nation_code=$nation_code&apisess=$apisess");
        $res = $this->seme_curl->get($url);
        $this->seme_log->write("api_mobile", 'API_Mobile/buyer/Delivery::__callBuyerOrderdetail');
    }

    public function index()
    {
        $data = array();
        //http_response_code("404");
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
    }
    /**
     * Buyer confirmed the product
     * @return mixed [description]
     */
    public function item_confirmed()
    {
        //initial
        $dt = $this->__init();
        $data = array();

        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::item_confirmed -> POST:".json_encode($_POST));
        }

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }

        $d_order_id = (int) $this->input->post("d_order_id");
        if ($d_order_id<=0) {
            $this->status = 7018;
            $this->message = 'Invalid Order ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }

        $d_order_detail_id = (int) $this->input->post("d_order_detail_id");
        if ($d_order_detail_id<=0) {
            $this->status = 7019;
            $this->message = 'Invalid Order Detail ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }

        $d_order_detail_item_id = (int) $this->input->post("d_order_detail_item_id");
        if ($d_order_detail_item_id<=0) {
            $this->status = 7020;
            $this->message = 'Invalid Order Detail Item ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }

        //get order item
        $oi = $this->dodim->getById($nation_code, $d_order_id, $d_order_detail_id, $d_order_detail_item_id);
        if (!isset($oi->d_order_id)) {
            $this->status = 7022;
            $this->message = 'Order with supplied ID(s) not found';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }
        if ($oi->b_user_id_buyer != $pelanggan->id) {
            $this->status = 7034;
            $this->message = "Sorry This order doesn't belong to you";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }
        if ($oi->seller_status == 'unconfirmed') {
            $this->status = 7035;
            $this->message = 'Waiting for seller confirmation';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }
        if ($oi->seller_status == 'rejected') {
            $this->status = 7036;
            $this->message = 'Ordered product already rejected by seller';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }
        if ($oi->shipment_status == 'process') {
            $this->status = 7037;
            $this->message = 'Ordered product still processed by seller';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }
        if ($oi->shipment_status == 'accepted' && $oi->buyer_confirmed == 'confirmed' && $oi->buyer_status == 'accepted' && $oi->settlement_status == 'solved_to_seller') {
            $this->status = 7038;
            $this->message = 'Product already accepted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }
        if ($oi->shipment_status == 'accepted' && $oi->buyer_confirmed == 'confirmed' && $oi->buyer_status == 'rejected' && $oi->settlement_status == 'solved_to_buyer') {
            $this->status = 7039;
            $this->message = 'Product already rejected';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }
        if ($oi->buyer_status == 'accepted' && $oi->settlement_status == 'solved_to_seller') {
            $this->status = 7040;
            $this->message = 'Product already accepted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }

        //open transaction
        $this->order->trans_start();

        //only process order that in delivery state or succeed state
        if ($oi->shipment_status == 'delivered' || $oi->shipment_status == 'succeed') {

            //updating to order_detail
            $dux = array();
            $dux['buyer_status'] = 'accepted';
            $dux['settlement_status'] = 'solved_to_seller';
            $res = $this->dodim->update($nation_code, $d_order_id, $d_order_detail_id, $d_order_detail_item_id, $dux);
            if ($res) {
                $this->status = 200;
                $this->message = 'Success';
                $this->order->trans_commit();

                //get earning data
                $berat = $oi->berat * $oi->qty;
                $harga_jual = $oi->harga_jual;
                $earning = $this->__earningCalculation($nation_code, $harga_jual, $berat);

                //get ordered detail items
                $is_confirmed = 0;
                $items = $this->dodim->getByOrderDetailId($nation_code, $d_order_id, $d_order_detail_id);
                $item_count = count($items);
                foreach ($items as $itm) {

                    //by Donny Dennison - 26 january 2021 11:46
                    //bug fix confirm and reject buyer
                    // if ($oi->shipment_status == 'accepted' && $oi->buyer_status != 'wait') {
                    if ($itm->buyer_status != 'wait') {

                        $is_confirmed++;
                    }
                }

                //get current order detail
                $dodm = $this->dodm->getById($nation_code, $d_order_id, $d_order_detail_id);
                $du = array();
                $du['buyer_confirmed'] = 'partially';
                $du['received_date'] = 'NOW()';
                $du['date_begin'] = 'NULL';
                $du['date_expire'] = 'NULL';
                $du['shipment_status'] = 'succeed';
                if ($is_confirmed == $item_count) {
                    $du['buyer_confirmed'] = 'confirmed';

                    //by Donny Dennison - 26 january 2021 11:46
                    //bug fix confirm and reject buyer
                    // $du['settlement_status'] = 'wait';
                    $du['settlement_status'] = 'unconfirmed';
                    
                }
                $res = $this->dodm->update($nation_code, $d_order_id, $d_order_detail_id, $du);
                $this->order->trans_commit();
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::item_confirmed -> Updated earning");
                }

                //treat $d_order_detail_id as $c_produk_id
                $c_produk_id = $d_order_detail_id;
                $order = $this->dodm->getOrderByBuyer($nation_code, $d_order_id, $c_produk_id, $pelanggan->id);

                //build order history process
                $di = array();
                $di['nation_code'] = $nation_code;
                $di['d_order_id'] = $d_order_id;
                $di['c_produk_id'] = $c_produk_id;
                $di['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $c_produk_id);
                $di['initiator'] = "Buyer";
                $di['nama'] = "Buyer Confirmed";
                $di['deskripsi'] = html_entity_decode($oi->nama)." has been received by buyer";
                $di['cdate'] = "NOW()";
                $di['is_done'] = "1";
                $res2 = $this->dopm->set($di);
                $this->order->trans_commit();

                //get product data, seller data, and buyer data
                $seller = $this->bu->getById($nation_code, $order->b_user_id_seller);
                $buyer = $pelanggan;

                //get buyer configuration
                $setting_value = 0;
                $classified = 'setting_notification_buyer';
                $notif_code = 'B4';
                $notif_cfg = $this->busm->getValue($nation_code, $buyer->id, $classified, $notif_code);
                if (isset($notif_cfg->setting_value)) {
                    $setting_value = (int) $notif_cfg->setting_value;
                }
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::confirmed --pushnotifconfig F: B, UID: $buyer->id, Classified: $classified, Code: $notif_code, value: $setting_value");
                }

                //declare notification
                $type = "transaction";
                $anotid = 7;
                $replacer = array();
                $replacer['order_name'] = html_entity_decode($oi->nama,ENT_QUOTES);
                $replacer['invoice_code'] = $order->invoice_code;

                //notification list for buyer
                $dpe = array();
                $dpe['nation_code'] = $nation_code;
                $dpe['b_user_id'] = $buyer->id;
                $dpe['id'] = $this->dpem->getLastId($nation_code, $buyer->id);
                $dpe['type'] = $type;
                if($buyer->language_id == 2) {
                    $dpe['judul'] = "Nilai Seller";
                    $dpe['teks'] = "Terima kasih! Anda telah mengkonfirmasi pesanan Anda: ".html_entity_decode($oi->nama,ENT_QUOTES)." ($order->invoice_code).";
                } else {
                    $dpe['judul'] = "Rate the Seller";
                    $dpe['teks'] = "Thank You! You have confirmed your order: ".html_entity_decode($oi->nama,ENT_QUOTES)." ($order->invoice_code).";
                }
                
                $dpe['cdate'] = "NOW()";
                $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
                $extras = new stdClass();
                $extras->id_order = "".$order->d_order_id;
                $extras->id_produk = "".$c_produk_id;
                $extras->id_order_detail = "".$c_produk_id;
                $extras->b_user_id_buyer = $buyer->id;
                $extras->b_user_fnama_buyer = $buyer->fnama;
                // by Muhammad Sofi - 26 October 2021 11:16
                // if user img & banner not exist or empty, change to default image
                // $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
                if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
                    $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
                } else {
                    $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                }
                $extras->b_user_id_seller = $seller->id;
                $extras->b_user_fnama_seller = $seller->fnama;
                // by Muhammad Sofi - 26 October 2021 11:16
                // if user img & banner not exist or empty, change to default image
                // $extras->b_user_image_seller = $this->cdn_url($seller->image);
                if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
                    $extras->b_user_image_seller = $this->cdn_url($seller->image);
                } else {
                    $extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                }

                $nw = $this->anot->get($nation_code, "list", $type, $anotid, $buyer->language_id);
                if (isset($nw->title)) {
                    $dpe['judul'] = $nw->title;
                }
                if (isset($nw->message)) {
                    $dpe['teks'] = $this->__nRep($nw->message, $replacer);
                }
                if (isset($nw->image)) {
                    $dpe['gambar'] = $nw->image;
                }
                $dpe['extras'] = json_encode($extras);
                $this->dpem->set($dpe);
                $this->order->trans_commit();

                //push notif for buyer
                if (strlen($buyer->fcm_token) > 50 && !empty($setting_value) && $buyer->is_active == 1) {
                    $device = $buyer->device;
                    $tokens = array($buyer->fcm_token);
                    if($buyer->language_id == 2) {
                        $title = 'Nilai Penjual!';
                        $message = "Terima kasih! Anda telah mengkonfirmasi pesanan Anda: ".html_entity_decode($this->convertEmoji($oi->nama),ENT_QUOTES)." ($order->invoice_code).";
                    } else {
                        $title = 'Rate the Seller!';
                        $message = "Thank You! You have confirmed your order: ".html_entity_decode($this->convertEmoji($oi->nama),ENT_QUOTES)." ($order->invoice_code).";
                    }
                    
                    $image = 'media/pemberitahuan/transaction.png';
                    $payload = new stdClass();
                    $payload->id_order = "".$order->d_order_id;
                    $payload->id_produk = "".$c_produk_id;
                    $payload->id_order_detail = "".$c_produk_id;
                    $payload->b_user_id_buyer = $buyer->id;
                    $payload->b_user_fnama_buyer = $buyer->fnama;
                    // by Muhammad Sofi - 26 October 2021 11:16
                    // if user img & banner not exist or empty, change to default image
                    // $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
                    if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
                        $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
                    } else {
                        $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                    $payload->b_user_id_seller = $seller->id;
                    $payload->b_user_fnama_seller = $seller->fnama;
                    // by Muhammad Sofi - 26 October 2021 11:16
                    // if user img & banner not exist or empty, change to default image
                    // $payload->b_user_image_seller = $this->cdn_url($seller->image);
                    if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
                        $payload->b_user_image_seller = $this->cdn_url($seller->image);
                    } else {
                        $payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                    }

                    $nw = $this->anot->get($nation_code, "push", $type, $anotid, $buyer->language_id);
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
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", 'API_Mobile/buyer/Delivery::confirmed __pushNotif: '.json_encode($res));
                    }
                }

                //send email to buyer
                if (strlen($buyer->email)>5) {
                    $replacer = array();
                    $replacer['site_name'] = $this->app_name;
                    $replacer['fnama'] = $buyer->fnama;
                    $replacer['produk_nama'] = html_entity_decode($oi->nama,ENT_QUOTES);
                    $replacer['invoice_code'] = $order->invoice_code;
                    $this->seme_email->flush();
                    $this->seme_email->replyto($this->site_name, $this->site_replyto);
                    $this->seme_email->from($this->site_email, $this->site_name);
                    $this->seme_email->subject('Rate the Seller');
                    $this->seme_email->to($buyer->email, $buyer->fnama);
                    $this->seme_email->template('rate_the_seller');
                    $this->seme_email->replacer($replacer);
                    $this->seme_email->send();
                }

                //declare notification for seller
                $type = 'transaction';
                $anotid = 8;
                $replacer = array();
                $replacer['invoice_code'] = $order->invoice_code;
                $replacer['order_name'] = html_entity_decode($oi->nama,ENT_QUOTES);

                //notification list for seller
                $dpe = array();
                $dpe['nation_code'] = $nation_code;
                $dpe['b_user_id'] = $seller->id;
                $dpe['id'] = $this->dpem->getLastId($nation_code, $seller->id);
                $dpe['type'] = $type;
                if($seller->language_id == 2) {
                    $dpe['judul'] = "Konfirmasi Pesanan (Oleh Pembeli)";
                    $dpe['teks'] = "Pembeli telah mengkonfirmasi produk Anda: ".html_entity_decode($oi->nama,ENT_QUOTES)." ($order->invoice_code). Sistem kami akan mengirimkan pembayaran maksimal 7 hari. Jika Anda tidak menerimanya dalam 7 hari, segera hubungi layanan pelanggan kami.";
                } else {
                    $dpe['judul'] = "Order Confirmation (By Buyer)";
                    $dpe['teks'] = "The buyer has confirmed your product: ".html_entity_decode($oi->nama,ENT_QUOTES)." ($order->invoice_code). Our system will send the payment a maximum of 7 days. If you do not receive it within 7 days, please contact our customer service immediately.";
                }
                
                $dpe['cdate'] = "NOW()";
                $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
                $extras = new stdClass();
                $extras->id_order = "".$order->d_order_id;
                $extras->id_produk = "".$c_produk_id;
                $extras->id_order_detail = "".$c_produk_id;
                $extras->b_user_id_buyer = $buyer->id;
                $extras->b_user_fnama_buyer = $buyer->fnama;
                
                // by Muhammad Sofi - 26 October 2021 11:16
                // if user img & banner not exist or empty, change to default image
                // $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
                if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
                    $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
                } else {
                    $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                }
                $extras->b_user_id_seller = $seller->id;
                $extras->b_user_fnama_seller = $seller->fnama;
                
                // by Muhammad Sofi - 26 October 2021 11:16
                // if user img & banner not exist or empty, change to default image
                // $extras->b_user_image_seller = $this->cdn_url($seller->image);
                if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
                    $extras->b_user_image_seller = $this->cdn_url($seller->image);
                } else {
                    $extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                }
                $nw = $this->anot->get($nation_code, "list", $type, $anotid, $seller->language_id);
                if (isset($nw->title)) {
                    $dpe['judul'] = $nw->title;
                }
                if (isset($nw->message)) {
                    $dpe['teks'] = $this->__nRep($nw->message, $replacer);
                }
                if (isset($nw->image)) {
                    $dpe['gambar'] = $nw->image;
                }
                $dpe['extras'] = json_encode($extras);
                $this->dpem->set($dpe);
                $this->order->trans_commit();

                //get seller configuration
                $setting_value = 0;
                $classified = 'setting_notification_seller';
                $notif_code = 'S2';
                $notif_cfg = $this->busm->getValue($nation_code, $seller->id, $classified, $notif_code);
                if (isset($notif_cfg->setting_value)) {
                    $setting_value = (int) $notif_cfg->setting_value;
                }
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::confirmed --pushNotifConfig, F: S, UID: $seller->id, Classified: $classified, Code: $notif_code, value: $setting_value");
                }

                //push notif for seller
                if (strlen($seller->fcm_token) > 50 && !empty($setting_value) && $seller->is_active == 1) {
                    $device = $seller->device;
                    $tokens = array($seller->fcm_token);
                    if($seller->language_id == 2) {
                        $title = 'Konfirmasi Pesanan (Oleh Pembeli)';
                        $message = "Pembeli telah mengkonfirmasi produk Anda! (Nomor faktur: $order->invoice_code).";
                    } else {
                        $title = 'Order Confirmation (By Buyer)';
                        $message = "The buyer has confirmed your product! (Invoice number: $order->invoice_code).";
                    }

                    $image = 'media/pemberitahuan/transaction.png';
                    $payload = new stdClass();
                    $payload->id_produk = "".$c_produk_id;
                    $payload->id_order = "".$order->d_order_id;
                    $payload->id_order_detail = "".$c_produk_id;
                    $payload->b_user_id_buyer = $buyer->id;
                    $payload->b_user_fnama_buyer = $buyer->fnama;
                    // by Muhammad Sofi - 26 October 2021 11:16
                    // if user img & banner not exist or empty, change to default image
                    // $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
                    if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
                        $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
                    } else {
                        $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                    $payload->b_user_id_seller = $seller->id;
                    $payload->b_user_fnama_seller = $seller->fnama;
                    // by Muhammad Sofi - 26 October 2021 11:16
                    // if user img & banner not exist or empty, change to default image
                    // $payload->b_user_image_seller = $this->cdn_url($seller->image);
                    if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
                        $payload->b_user_image_seller = $this->cdn_url($seller->image);
                    } else {
                        $payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                    $nw = $this->anot->get($nation_code, "push", $type, $anotid, $seller->language_id);
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
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", 'API_Mobile/buyer/Delivery()::confirmed __pushNotif: '.json_encode($res));
                    }
                }

                //send email to admin
                if ($this->email_send) {
                    $admin = $this->apm->getEmailActive();
                    $replacer = array();
                    $replacer['site_name'] = $this->app_name;
                    $replacer['invoice_code'] = $order->invoice_code;
                    $replacer['seller_id'] = $seller->id;
                    $replacer['seller_fnama'] = $seller->fnama;
                    $replacer['admincms_link'] = base_url_admin("ecommerce/transactionhistory/");
                    $this->seme_email->flush();
                    $this->seme_email->replyto($this->site_name, $this->site_replyto);
                    $this->seme_email->from($this->site_email, $this->site_name);
                    $this->seme_email->subject('Confirmed by the Buyer');
                    foreach ($admin as $adm) {

                        //by Donny Dennison - 11 Augustus 2020 - 15:06
                        //disable send email to support@sellon.net for buyer confirm
                        // if (strlen($adm->email)>4) {
                        if (strlen($adm->email)>4 && $adm->email != 'support@sellon.net') {

                            $this->seme_email->to($adm->email, $adm->nama);
                        }
                    }
                    $this->seme_email->template('confirm_order_to_admin');
                    $this->seme_email->replacer($replacer);
                    $this->seme_email->send();
                    //if($this->is_log) $this->seme_log->write("api_mobile", $this->seme_email->getLog());
                }
            } else {
                $this->order->trans_rollback();
                $this->status = 7042;
                $this->message = 'Failed';
            }
            $this->order->trans_end();
        } else {
            $this->status = 7041;
            $this->message = 'Ordered being shipped';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
        }
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
    }
    /**
     * Buyer rejected item product
     * @return mixed [description]
     */
    public function item_rejected()
    {
        //initial
        $dt = $this->__init();
        $data = array();
        $data['item'] = new stdClass();
        $data['item']->buyer_status_before = '-';
        $data['item']->buyer_status_after = '-';

        //logger
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::item_rejected -> POST: ".json_encode($_POST));
        }

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::item_rejected -> forceClose ".$this->status." - ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }

        $d_order_id = (int) $this->input->post("d_order_id");
        if ($d_order_id<=0) {
            $this->status = 7018;
            $this->message = 'Invalid Order ID';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::item_rejected -> forceClose ".$this->status." - ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }

        $d_order_detail_id = (int) $this->input->post("d_order_detail_id");
        if ($d_order_detail_id<=0) {
            $this->status = 7019;
            $this->message = 'Invalid Order Detail ID';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::item_rejected -> forceClose ".$this->status." - ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }

        $d_order_detail_item_id = (int) $this->input->post("d_order_detail_item_id");
        if ($d_order_detail_item_id<=0) {
            $this->status = 7020;
            $this->message = 'Invalid Order Detail Item ID';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::item_rejected -> forceClose ".$this->status." - ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }

        //get order
        $oi = $this->dodim->getById($nation_code, $d_order_id, $d_order_detail_id, $d_order_detail_item_id);
        if (!isset($oi->d_order_id)) {
            $this->status = 7022;
            $this->message = 'Order with supplied ID(s) not found';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::item_rejected -> forceClose ".$this->status." - ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }

        //put to buyer status
        $data['item']->buyer_status_before = $oi->buyer_status;

        if($oi->buyer_status == 'rejected'){
            $this->status = 7028;
            $this->message = 'This item already rejected';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::item_rejected -> forceClose ".$this->status." - ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }

        if ($oi->b_user_id_buyer != $pelanggan->id) {
            $this->status = 7022;
            $this->message = 'Order with supplied ID(s) not found';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::item_rejected -> forceClose ".$this->status." - ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }
        if ($oi->seller_status == 'unconfirmed') {
            $this->status = 7035;
            $this->message = 'Waiting for seller confirmation';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::item_rejected -> forceClose ".$this->status." - ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }
        if ($oi->seller_status == 'rejected') {
            $this->status = 7036;
            $this->message = 'Ordered product already rejected by seller';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::item_rejected -> forceClose ".$this->status." - ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }
        if ($oi->shipment_status == 'process') {
            $this->status = 7037;
            $this->message = 'Ordered product still processed by seller';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::item_rejected -> forceClose ".$this->status." - ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }
        if ($oi->shipment_status == 'accepted' && $oi->buyer_confirmed == 'confirmed' && $oi->buyer_status == 'accepted' && $oi->settlement_status == 'solved_to_seller') {
            $this->status = 7038;
            $this->message = 'Product already accepted';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::item_rejected -> forceClose ".$this->status." - ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }
        if ($oi->shipment_status == 'accepted' && $oi->buyer_confirmed == 'confirmed' && $oi->buyer_status == 'rejected' && $oi->settlement_status == 'solved_to_buyer') {
            $this->status = 7039;
            $this->message = 'Product already rejected';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::item_rejected -> forceClose ".$this->status." - ".$this->message);
            }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
            die();
        }

        //start transaction
        //$this->order->trans_start();

        //update order detail item
        $dux = array();
        $dux['buyer_status'] = 'rejected';
        $res = $this->dodim->update($nation_code, $d_order_id, $d_order_detail_id, $d_order_detail_item_id, $dux);

        // //by mas aditya
        // //START by mas aditya

        // $item_refund = (int)$oi->harga_jual;

        // $getdataorder = $this->order->getByIdForReject($nation_code, $d_order_id);
        //     //$total_value = (int)$getdataorder->{'sub_total'};
        // $pg_fee = (float)$getdataorder->{'pg_fee'};
        // $pg_fee_vat = (float)$getdataorder->{'pg_fee_vat'};
        // $total_pg_fee = $pg_fee + $pg_fee_vat;

        // $val_profit_amount = (int)$getdataorder->{'profit_amount'};

        // $val_selling_fee = (float)$getdataorder->{'selling_fee'};

        // $selling_fee_percent = 10;

        // $temp_refund = (int)$getdataorder->{'refund_amount'};

        // $total_refund = $temp_refund + $item_refund;
        
        // $value_refund_amount = array();
        // $value_refund_amount['refund_amount'] = floatval($total_refund);

        // $value_profit_amount['profit_amount'] = floatval($val_profit_amount);
        // $value_profit_amount['selling_fee'] = floatval($val_selling_fee);

        // $update_refund_amount = $this->order->updateRefund($nation_code, $d_order_id, $value_refund_amount);
        

        // $temp_total_value = $this->order->getByIdForReject($nation_code, $d_order_id);

        // $total_value = (int)$temp_total_value->{'sub_total'};
        
        // $calculaterefund = $total_value - $item_refund;
        // //var_dump($calculaterefund); die();
        // $total_earning = $calculaterefund * ($selling_fee_percent/100);
        // $orderreject = array();
        // $orderreject['selling_fee'] = floatval($total_earning);
        // $updatereject = $this->order->updateReject($nation_code, $d_order_id, $orderreject);


        // $getcombo = $this->order->getByIdForReject($nation_code, $d_order_id);

        // $temp_selling_fee = (int)$getcombo->{'selling_fee'};

        // $update_selling_fee = $temp_selling_fee - $total_pg_fee;
        // $value_selling_fee2 = array();
        // $value_selling_fee2['profit_amount'] = floatval($update_selling_fee);
        // $update_selling_fee2 = $this->order->updateSellingFee($nation_code, $d_order_id, $value_selling_fee2);


        // // Improve by Aditya Adi Prabowo, 28 July 2020
        // // Fix to update to database d_order_detail to fix value in field profit_amount is "0"
        // // Ask by Mr. Jackie
        // $getcombo4 = $this->order->getByIdForReject($nation_code, $d_order_id);
        // $detail_earning = (float)$getcombo4->{'sub_total'};


        // $detail_pg_fee = (float)$getcombo4->{'pg_fee'};
        // $detail_pg_vat = (float)$getcombo4->{'pg_fee_vat'};

        // $detail_profit_amount = (float)$getcombo4->{'profit_amount'};

        // $detail_selling_fee = (float)$getcombo4->{'selling_fee'};

        // $detail_refund_amount = (float)$getcombo4->{'refund_amount'};
        // $detail_earning_total = (float)$detail_earning - $detail_refund_amount;

        // $value_update_detail_order = array();
        // $value_update_detail_order['sub_total'] = floatval($detail_earning);
        // $value_update_detail_order['pg_fee'] = floatval($detail_pg_fee);
        // $value_update_detail_order['pg_vat'] = floatval($detail_pg_vat);

        // $value_update_detail_order['profit_amount'] = floatval($detail_profit_amount);

        // $value_update_detail_order['selling_fee'] = floatval($detail_selling_fee);
        // $value_update_detail_order['earning_total'] = floatval($detail_earning_total);
        // $value_update_detail_order['refund_amount'] = floatval($detail_refund_amount);

        // //END of Improve
        // $update_selling_fee2 = $this->dodm->updateAfterreject($nation_code, $d_order_id, $value_update_detail_order);

        // //END by mas aditya


        //by Donny Dennison - 2 august 2020 14:47
        //bug fixing earning total in d_order_detail table
        //profit_amount value is the same in d_order and table d_order_detail table
        //START by Donny Dennison - 2 august 2020 14:47

        $totalPG = $oi->pg_fee + $oi->pg_fee_vat;
        $newSubTotal = $oi->sub_total - $oi->harga_jual * $oi->qty;
        $newRefundAmount = $oi->refund_amount + $oi->harga_jual * $oi->qty;
        $newSellingFee = ($oi->sub_total - $oi->harga_jual * $oi->qty) * ($oi->selling_fee_percent/100);
        $newProfitAmount = $newSellingFee - $totalPG;

        //update to d_order table
        $du = array();
        $du['sub_total'] = floatval($newSubTotal);
        $du['refund_amount'] = floatval($newRefundAmount);
        $du['selling_fee'] = floatval($newSellingFee);
        $du['profit_amount'] = floatval($newProfitAmount);
        $this->order->update($nation_code, $d_order_id,$du);

        //update all order detail 
        $du = array();
        $du['profit_amount'] = floatval($newProfitAmount);
        $this->dodm->updateByOrderId($nation_code, $d_order_id, $du);

        //update to d_order_detail table
        $du = array();
        $du['sub_total'] = floatval($oi->sub_total_d_order_detail - $oi->harga_jual * $oi->qty);
        $du['grand_total'] = floatval($du['sub_total'] + $oi->shipment_cost + $oi->shipment_cost_add + $oi->shipment_cost_sub);
        $du['selling_fee'] = floatval($du['sub_total'] * ($oi->selling_fee_percent_d_order_detail/100));
        $du['earning_total'] = floatval($du['sub_total'] - $du['selling_fee']);
        $du['refund_amount'] = floatval($oi->refund_amount_d_order_detail + $oi->harga_jual * $oi->qty);
        $this->dodm->update($nation_code, $d_order_id, $d_order_detail_id, $du);

        //END by Donny Dennison - 2 august 2020 14:47

        if ($res) {
            //result
            $this->status = 200;
            $this->message = 'Success';
            if($this->is_log) $this->seme_log->write("api_mobile", 'API_Mobile/buyer/Delivery::item_rejected -- Call __callBuyerOrderdetail');
            $this->__callBuyerOrderdetail($apikey,$nation_code, $apisess, $d_order_id, $d_order_detail_id);

            //$this->order->trans_commit();
            //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::item_rejected QueryLast: ".$this->dodim->getLastQuery());

            //get order detail item
            $oi = $this->dodim->getById($nation_code, $d_order_id, $d_order_detail_id, $d_order_detail_item_id);
            $data['item']->buyer_status_after = $oi->buyer_status;

            //get ordered detail items
            $thumb = '';
            $foto = '';
            $is_confirmed = 0;
            $items = $this->dodim->getByOrderDetailId($nation_code, $d_order_id, $d_order_detail_id);
            $item_count = count($items);
            $item_rjctd = 0;
            foreach ($items as $itm) {

                //by Donny Dennison - 26 january 2021 11:46
                //bug fix confirm and reject buyer
                // if ($oi->shipment_status == 'accepted' && $oi->buyer_status != 'wait') {
                if ($itm->buyer_status != 'wait') {

                    if (empty($is_confirmed)) {

                        //by Donny Dennison - 26 january 2021 11:46
                        //bug fix confirm and reject buyer
                        // $thumb = $im->thumb;
                        // $foto = $im->foto;
                        $thumb = $itm->thumb;
                        $foto = $itm->foto;

                    }
                    $is_confirmed++;

                    //by Donny Dennison - 26 january 2021 11:46
                    //bug fix confirm and reject buyer
                    // if ($oi->buyer_status == 'rejected') {
                    if ($itm->buyer_status == 'rejected') {

                        $item_rjctd++;
                    }
                }
            }

            //get earning data
            $berat = $oi->berat * $oi->qty;
            $harga_jual = $oi->harga_jual;
            $earning = $this->__earningCalculation($nation_code, $harga_jual, $berat);

            //get current order detail
            $dodm = $this->dodm->getById($nation_code, $d_order_id, $d_order_detail_id);
            $du = array();
            $du['buyer_confirmed'] = 'partially';
            $du['is_calculated'] = 0;
            $du['received_date'] = 'NOW()';
            $du['date_begin'] = 'NULL';
            $du['date_expire'] = 'NULL';
            $du['shipment_status'] = 'succeed';
            if (strlen($thumb) && strlen($foto)) {
                $du['thumb'] = $thumb;
                $du['foto'] = $foto;
            }
            if ($is_confirmed == $item_count) {
                $du['buyer_confirmed'] = 'confirmed';

                //by Donny Dennison - 26 january 2021 11:46
                //bug fix confirm and reject buyer
                // $du['settlement_status'] = 'wait';
                $du['settlement_status'] = 'unconfirmed';

            }
            if ($item_rjctd==$item_count) {
                $du['is_rejected_all'] = 1;
            }
            $res = $this->dodm->update($nation_code, $d_order_id, $d_order_detail_id, $du);
            //$this->order->trans_commit();

            //treat $d_order_detail_id as $c_produk_id
            $c_produk_id = $d_order_detail_id;
            $order = $this->dodm->getOrderByBuyer($nation_code, $d_order_id, $c_produk_id, $pelanggan->id);

            //build order history process
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['d_order_id'] = $d_order_id;
            $di['c_produk_id'] = $c_produk_id;
            $di['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $c_produk_id);
            $di['initiator'] = "Buyer";
            $di['nama'] = "Buyer Rejected";
            $di['deskripsi'] = "Product ".html_entity_decode($oi->nama)." has been received but was rejected by the buyer.";
            $di['cdate'] = "NOW()";
            $di['is_done'] = "1";
            $res2 = $this->dopm->set($di);
            //$this->order->trans_commit();

            //get product data, seller data, and buyer data
            $seller = $this->bu->getById($nation_code, $order->b_user_id_seller);
            $buyer = $pelanggan;

            //get buyer configuration
            $setting_value = 0;
            $classified = 'setting_notification_buyer';
            $notif_code = 'B4';
            $notif_cfg = $this->busm->getValue($nation_code, $buyer->id, $classified, $notif_code);
            if (isset($notif_cfg->setting_value)) {
                $setting_value = (int) $notif_cfg->setting_value;
            }
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::item_rejected --pushNotifConfig, F: B, UID: $buyer->id, Classified: $classified, Code: $notif_code, value: $setting_value");
            }

            $type = 'transaction';
            $anotid = 9;
            $replacer = array();
            $replacer['order_name'] = html_entity_decode($oi->nama,ENT_QUOTES);
            $replacer['invoice_code'] = $order->invoice_code;

            //push notif for buyer
            if (strlen($buyer->fcm_token) > 50 && !empty($setting_value) && $buyer->is_active == 1) {
                $device = $buyer->device;
                $tokens = array($buyer->fcm_token);
                if($buyer->language_id == 2) {
                    $title = 'Pesanan Ditolak';
                    $message = "Kami telah menerima keluhan Anda tentang ".html_entity_decode($this->convertEmoji($oi->nama),ENT_QUOTES)." ($order->invoice_code). Harap tunggu sementara kami melakukan verifikasi.";
                } else {
                    $title = 'Order Rejected';
                    $message = "We have received your complaint about ".html_entity_decode($this->convertEmoji($oi->nama),ENT_QUOTES)." ($order->invoice_code). Please wait while we verify.";
                }
                
                $type = 'transaction';
                $image = 'media/pemberitahuan/transaction.png';
                $payload = new stdClass();
                $payload->id_order = "".$order->d_order_id;
                $payload->id_produk = "".$c_produk_id;
                $payload->id_order_detail = "".$c_produk_id;
                $payload->b_user_id_buyer = $buyer->id;
                $payload->b_user_fnama_buyer = $buyer->fnama;
                
                // by Muhammad Sofi - 26 October 2021 11:16
                // if user img & banner not exist or empty, change to default image
                // $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
                if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
                    $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
                } else {
                    $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                }
                $payload->b_user_id_seller = $seller->id;
                $payload->b_user_fnama_seller = $seller->fnama;
                
                // by Muhammad Sofi - 26 October 2021 11:16
                // if user img & banner not exist or empty, change to default image
                // $payload->b_user_image_seller = $this->cdn_url($seller->image);
                if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
                    $payload->b_user_image_seller = $this->cdn_url($seller->image);
                } else {
                    $payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                }
                $nw = $this->anot->get($nation_code, "push", $type, $anotid, $buyer->language_id);
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
                //if($this->is_log) $this->seme_log->write("api_mobile", 'api_mobile/buyer/delivery::item_rejected __pushNotif: '.json_encode($res));
            }

            //notification list for buyer
            $dpe = array();
            $dpe['nation_code'] = $nation_code;
            $dpe['b_user_id'] = $buyer->id;
            $dpe['id'] = $this->dpem->getLastId($nation_code, $buyer->id);
            $dpe['type'] = "transaction";
            if($buyer->language_id == 2) {
                $dpe['judul'] = "Pesanan Ditolak";
                $dpe['teks'] = "Kami telah menerima keluhan Anda tentang ".html_entity_decode($oi->nama,ENT_QUOTES)." ($order->invoice_code). Harap tunggu sementara kami melakukan verifikasi. Untuk komunikasi yang lebih cepat, silahkan gunakan fitur Chat untuk menghubungi admin, seller, atau customer service kami.";
            } else {
                $dpe['judul'] = "Order Rejected";
                $dpe['teks'] = "We have received your complaint about ".html_entity_decode($oi->nama,ENT_QUOTES)." ($order->invoice_code). Please wait while we verify. For direct communication, please use the Chat feature to contact our admin, seller, or customer service.";
            }
            
            $dpe['cdate'] = "NOW()";
            $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
            $extras = new stdClass();
            $extras->id_order = "".$order->d_order_id;
            $extras->id_produk = "".$c_produk_id;
            $extras->id_order_detail = "".$c_produk_id;
            $extras->b_user_id_buyer = $buyer->id;
            $extras->b_user_fnama_buyer = $buyer->fnama;
            // by Muhammad Sofi - 26 October 2021 11:16
            // if user img & banner not exist or empty, change to default image
            // $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
            if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
                $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
            } else {
                $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
            }
            $extras->b_user_id_seller = $seller->id;
            $extras->b_user_fnama_seller = $seller->fnama;
            
            // by Muhammad Sofi - 26 October 2021 11:16
            // if user img & banner not exist or empty, change to default image
            // $extras->b_user_image_seller = $this->cdn_url($seller->image);
            if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
                $extras->b_user_image_seller = $this->cdn_url($seller->image);
            } else {
                $extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
            }
            $nw = $this->anot->get($nation_code, "list", $type, $anotid, $buyer->language_id);
            if (isset($nw->title)) {
                $dpe['judul'] = $nw->title;
            }
            if (isset($nw->message)) {
                $dpe['teks'] = $this->__nRep($nw->message, $replacer);
            }
            if (isset($nw->image)) {
                $dpe['gambar'] = $nw->image;
            }
            $dpe['extras'] = json_encode($extras);
            $this->dpem->set($dpe);
            //$this->order->trans_commit();

            //send email to buyer
            if (strlen($buyer->email)>5) {
                $replacer = array();
                $replacer['site_name'] = $this->app_name;
                $replacer['fnama'] = $buyer->fnama;
                $replacer['produk_nama'] = html_entity_decode($oi->nama,ENT_QUOTES);
                $replacer['invoice_code'] = $order->invoice_code;
                $this->seme_email->flush();
                $this->seme_email->replyto($this->site_name, $this->site_replyto);
                $this->seme_email->from($this->site_email, $this->site_name);
                $this->seme_email->subject('Rejected');
                $this->seme_email->to($buyer->email, $buyer->fnama);
                $this->seme_email->template('rejected_for_buyer');
                $this->seme_email->replacer($replacer);
                $this->seme_email->send();
            }

            //get seller configuration
            $setting_value = 0;
            $classified = 'setting_notification_seller';
            $notif_code = 'S2';
            $notif_cfg = $this->busm->getValue($nation_code, $seller->id, $classified, $notif_code);
            if (isset($notif_cfg->setting_value)) {
                $setting_value = (int) $notif_cfg->setting_value;
            }
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::item_rejected --pushNotifConfig, F: S, UID: $seller->id, Classified: $classified, Code: $notif_code, value: $setting_value");
            }

            //type and replacer idem with above
            $anotid = 10;
            //push notif for seller
            if (strlen($seller->fcm_token) > 50 && !empty($setting_value) && $seller->is_active == 1) {
                $device = $seller->device;
                $tokens = array($seller->fcm_token);
                if($seller->language_id == 2) {
                    $title = 'Ditolak';
                    $message = "Pembeli telah mengajukan keluhan tentang pesanan $order->invoice_code.";
                } else {
                    $title = 'Rejected';
                    $message = "The buyer has filed a complaint about the order $order->invoice_code.";
                }

                $type = 'transaction';
                $image = 'media/pemberitahuan/transaction.png';
                $payload = new stdClass();
                $payload->id_order = "".$order->d_order_id;
                $payload->id_produk = "".$c_produk_id;
                $payload->id_order_detail = "".$c_produk_id;
                $payload->b_user_id_buyer = $buyer->id;
                $payload->b_user_fnama_buyer = $buyer->fnama;
                
                // by Muhammad Sofi - 26 October 2021 11:16
                // if user img & banner not exist or empty, change to default image
                // $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
                if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
                    $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
                } else {
                    $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                }
                $payload->b_user_id_seller = $seller->id;
                $payload->b_user_fnama_seller = $seller->fnama;
                
                // by Muhammad Sofi - 26 October 2021 11:16
                // if user img & banner not exist or empty, change to default image
                // $payload->b_user_image_seller = $this->cdn_url($seller->image);
                if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
                    $payload->b_user_image_seller = $this->cdn_url($seller->image);
                } else {
                    $payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                }
                $nw = $this->anot->get($nation_code, "push", $type, $anotid, $seller->language_id);
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
                //if($this->is_log) $this->seme_log->write("api_mobile", 'API_Mobile/buyer/Delivery::item_rejected __pushNotif: '.json_encode($res));
            }

            //notification list for seller
            $dpe = array();
            $dpe['nation_code'] = $nation_code;
            $dpe['b_user_id'] = $seller->id;
            $dpe['id'] = $this->dpem->getLastId($nation_code, $seller->id);
            $dpe['type'] = "transaction";
            if($seller->language_id == 2) { 
                $dpe['judul'] = "Ditolak";
                $dpe['teks'] = "Pembeli telah mengajukan keluhan tentang pesanan ".html_entity_decode($oi->nama,ENT_QUOTES)." ($order->invoice_code). Harap segera verifikasi klaim menggunakan fitur Chat atau cek email Anda untuk lebih jelasnya.";
            } else {
                $dpe['judul'] = "Rejected";
                $dpe['teks'] = "The buyer has filed a complaint about the order ".html_entity_decode($oi->nama,ENT_QUOTES)." ($order->invoice_code). Please immediately verify the claim using the Chat feature or check your email for more details.";
            }
            $dpe['cdate'] = "NOW()";
            $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
            $extras = new stdClass();
            $extras->id_order = "".$order->d_order_id;
            $extras->id_produk = "".$c_produk_id;
            $extras->id_order_detail = "".$c_produk_id;
            $extras->b_user_id_buyer = $buyer->id;
            $extras->b_user_fnama_buyer = $buyer->fnama;
            // by Muhammad Sofi - 26 October 2021 11:16
            // if user img & banner not exist or empty, change to default image
            // $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
            if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
                $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
            } else {
                $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
            }
            $extras->b_user_id_seller = $seller->id;
            $extras->b_user_fnama_seller = $seller->fnama;
            // by Muhammad Sofi - 26 October 2021 11:16
            // if user img & banner not exist or empty, change to default image
            // $extras->b_user_image_seller = $this->cdn_url($seller->image);
            if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
                $extras->b_user_image_seller = $this->cdn_url($seller->image);
            } else {
                $extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
            }
            $dpe['extras'] = json_encode($extras);
            $this->dpem->set($dpe);
            //$this->order->trans_commit();

            //send email to seller
            if (strlen($seller->email)>5) {
                $replacer = array();
                $replacer['site_name'] = $this->app_name;
                $replacer['fnama'] = $seller->fnama;
                $replacer['produk_nama'] = html_entity_decode($oi->nama,ENT_QUOTES);
                $replacer['invoice_code'] = $order->invoice_code;
                $this->seme_email->flush();
                $this->seme_email->replyto($this->site_name, $this->site_replyto);
                $this->seme_email->from($this->site_email, $this->site_name);
                $this->seme_email->subject('Rejected');
                $this->seme_email->to($seller->email, $seller->fnama);
                $this->seme_email->template('rejected_for_seller');
                $this->seme_email->replacer($replacer);
                $this->seme_email->send();
            }

            //send email to admin
            if ($this->email_send) {
                $admin = $this->apm->getEmailActive();
                $replacer = array();
                $replacer['site_name'] = $this->app_name;

                //by Donny Dennison - 11 Augustus 2020 - 15:06
                //change from invoice_code to order_id
                // $replacer['invoice_code'] = $order->invoice_code;
                $replacer['order_id'] = "".$order->d_order_id;

                $replacer['c_produk_nama'] = html_entity_decode($oi->nama,ENT_QUOTES);
                $replacer['buyer_fnama'] = $buyer->fnama;
                $replacer['admincms_link'] = base_url_admin("ecommerce/cancellation/");
                $this->seme_email->flush();
                $this->seme_email->replyto($this->site_name, $this->site_replyto);
                $this->seme_email->from($this->site_email, $this->site_name);
                $this->seme_email->subject('Rejected Item '.$order->invoice_code);
                $em = '';
                foreach ($admin as $adm) {
                    if (strlen($adm->email)>4) {
                        $this->seme_email->to($adm->email, $adm->nama);
                        $em .= $adm->email.", ";
                    }
                }

                $this->seme_email->template('rejected_item');
                $this->seme_email->replacer($replacer);
                $this->seme_email->send();
                $em = rtrim($adm->email, ", ");
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::item_rejected -> Email Admin Sent: ".$em);
                }
                //if($this->is_log) $this->seme_log->write("api_mobile", $this->seme_email->getLog());
            }
        } else {
            //$this->order->trans_rollback();
            $this->status = 7027;
            $this->message = 'Please wait for a while, you can reject order while shipment status succeed or cancelled.';
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::item_rejected -> FC: ROLLBACK");
            }
        }
        //$this->order->trans_end();
        //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::item_rejected -> BEFORE: ".$data['item']->buyer_status_before." - AFTER: ".$data['item']->buyer_status_after);
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/buyer/Delivery::item_rejected -> RESULT: ".$this->status." - ".$this->message);
        }

        //response
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "buyer_delivery");
    }
}
