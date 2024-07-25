<?php
class Five_Min extends JI_Controller
{
    public $send_email = 0;
    
    //by Donny Dennison - 30 July 2020 13:21
    //change auto reject become auto confirm
    public $email_send = 1;

    public $is_log = 1;
    public $is_push = 1;

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->lib("seme_email");
        $this->load("api_cron/a_notification_model", "anot");
        $this->load("api_cron/b_user_model", "bu");
        $this->load("api_cron/b_user_setting_model", "busm");
        $this->load("api_cron/c_produk_model", "cpm");
        $this->load("api_cron/d_cart_model", "cart");
        $this->load("api_cron/d_order_model", "order");
        $this->load("api_cron/d_order_detail_model", "dodm");
        $this->load("api_cron/d_order_proses_model", "dopm");
        $this->load("api_cron/d_pemberitahuan_model", "dpem"); //notification list
        $this->load("api_cron/d_order_detail_item_model", "dodim");
        $this->load("api_cron/d_order_detail_pickup_model", "dodpm");

        $this->load("api_mobile/common_code_model", "ccm");
        $this->load("api_mobile/d_order_alamat_model", "doam");
        $this->load("api_mobile/d_order_detail_pickup_model", "dodpum");
        $this->load("api_mobile/e_rating_model", "erm");

        // $this->load("api_mobile/g_highlight_community_model", "ghcm");

        //START by Donny Dennison - 12 july 2022 14:56
        //new offer system
        $this->load("api_mobile/e_chat_room_model", 'ecrm');
        $this->load("api_mobile/e_chat_model", 'chat');
        $this->load("api_mobile/e_chat_participant_model", 'ecpm');
        $this->load("api_mobile/e_offer_review_model", 'eorm');

        //by Donny Dennison - 10 august 2022 10:10
        //new point rule for offer system
        $this->load("api_mobile/b_user_alamat_model", "bua");
        $this->load("api_mobile/g_leaderboard_point_history_model", 'glphm');
        // $this->load("api_mobile/g_leaderboard_ranking_model", 'glrm');

        //by Donny Dennison - 02 september 2022 14:20
        //record user sales and total transaction as seller and buyer
        $this->load("api_mobile/b_user_offer_sales_model", 'buosm');

    }

    public function index()
    {
        //open transaction
        $this->order->trans_start();

        //change log filename
        // $this->seme_log->changeFilename("cron.log");

        /** @var int define payment timeout */
        // $payment_timeout = 10;

        // if (isset($this->payment_timeout)) {
        //     $payment_timeout = $this->payment_timeout;
        // }

        /** @var int get half time of payment timeout*/
        // $payment_timeout_half = ceil($payment_timeout/2);

        /** @var int define seller confirmation timeout */
        // $seller_confirm_timeout = 12;
        // if (isset($this->seller_timeout)) {
        //     $seller_confirm_timeout = $this->seller_timeout;
        // }

        //by Donny Dennison - 30 July 2020 13:21
        //change auto reject become auto confirm
        //START Change by Donny Dennison - 3- July 2020 13:21

        //By Donny Dennison - 26 Juni 2020 21:05
        //Request by Mr Jackie, auto reject if seller not confirm every day 22:30
        // $seller_confirm_timeout = date('22:30:00');
        // $seller_confirm_timeout = date('22:53:00');

        //END Change by Donny Dennison - 3- July 2020 13:21

        //put on log
        // $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --configuration --payment_timeout: '.$payment_timeout.'m --2nd_payment_timeout: '.$payment_timeout_half.'m --seller_confirmation_timeout: '.$seller_confirm_timeout.'h');

        /** @var array list of pending order */
        // $pendings = $this->dodm->getBuyerPending($payment_timeout+1); //get pending order
        // $c = count($pendings);
        // $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --pendingsCount: '.$c);
        // if (count($pendings)>0) {
        //   foreach ($pendings as $pending) {
        //       // move pending products back to cart
        //       foreach($this->dodim->getByOrderDetailId($pending->nation_code,$pending->d_order_id,$pending->d_order_detail_id) as $produk){
        //         $crt = $this->cart->check($pending->nation_code,$pending->b_user_id_buyer,$produk->c_produk_id);
        //         if(empty($crt)){
        //           $di = array();
        //           $di['nation_code'] = $pending->nation_code;
        //           $di['b_user_id'] = $pending->b_user_id_buyer;
        //           $di['c_produk_id'] = $produk->c_produk_id;
        //           $di['id'] = $this->cart->getLastId($pending->nation_code,$produk->c_produk_id,$pending->b_user_id_buyer);
        //           $di['qty'] = $produk->qty;
        //           $di['cdate'] = 'NOW()';
        //           $cres = $this->cart->set($di);
        //           if($cres){
        //             $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --pendings --rollback2cart: SUCCESS --pID: '.$produk->c_produk_id.' --pQty: '.$produk->qty);
        //           }else{
        //             $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --pendings --rollback2cart: FAILED --pID: '.$produk->c_produk_id.' --pQty: '.$produk->qty);
        //           }
        //         }else{
        //           $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --pendings --rollback2cart: UPDATED --pID: '.$produk->c_produk_id.' --pQty: '.$produk->qty);
        //           $this->cart->updateQty($pending->nation_code,$pending->b_user_id_buyer,$produk->c_produk_id,$produk->qty);
        //         }

        //         //START by Donny Dennison - 17 january 2022 14:01
        //         //make image in order product standalone
        //         $file_path = SENEROOT.$produk->foto;
        //         if(file_exists($file_path)) {
        //           unlink($file_path);
        //         }
        //         $file_path_thumb = SENEROOT.$produk->thumb;
        //         if(file_exists($file_path_thumb)) {
        //           unlink($file_path_thumb);
        //         }
        //         //END by Donny Dennison - 17 january 2022 14:01
        //       }

        //       //delete pending order after 30 minutes (default)
        //       $cres = $this->dodim->delbyOrderId($pending->nation_code,$pending->d_order_id);
        //       if($cres){
        //         $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --pendings --delOrderDetailItem: SUCCESS --oID: '.$pending->d_order_id);
        //       }else{
        //         $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --pendings --delOrderDetailItem: FAILED --oID: '.$pending->d_order_id);
        //       }

        //       $cres = $this->dodpm->delbyOrderId($pending->nation_code,$pending->d_order_id);
        //       if($cres){
        //         $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --pendings --delOrderDetailPickup: SUCCESS --oID: '.$pending->d_order_id);
        //       }else{
        //         $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --pendings --delOrderDetailPickup: FAILED --oID: '.$pending->d_order_id);
        //       }

        //       $cres = $this->dodm->delbyOrderId($pending->nation_code,$pending->d_order_id);
        //       if($cres){
        //         $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --pendings --delOrderDetail: SUCCESS --oID: '.$pending->d_order_id);
        //       }else{
        //         $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --pendings --delOrderDetail: FAILED --oID: '.$pending->d_order_id);
        //       }

        //       $cres = $this->order->del($pending->nation_code,$pending->d_order_id);
        //       if($cres){
        //         $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --pendings --delOrder: SUCCESS --oID: '.$pending->d_order_id);
        //       }else{
        //         $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --pendings --delOrder: FAILED --oID: '.$pending->d_order_id);
        //       }
        //   }//end foreach
        // }//end data count

        //get freeze order list
        // $freezeOrders = $this->dodm->getBuyerFreezeOrder($payment_timeout);
        // $c = count($freezeOrders);
        // $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --freezeOrdersCount: '.$c);
        // if (count($freezeOrders)>0) {
        //   foreach ($freezeOrders as $freeze) {
        //     //update status order to cancelled
        //     $res = $this->order->update($freeze->nation_code, $freeze->d_order_id, array("order_status"=>"cancelled"));
        //     $this->order->trans_commit();
        //     if ($res) {
        //         $this->seme_log->write("api_cron", "API_Cron/Five_Min::index --freezeOrders order_status: cancelled -- DONE");
        //     }

        //     //rollback stock
        //     foreach($this->dodim->getByOrderDetailId($freeze->nation_code,$freeze->d_order_id,$freeze->d_order_detail_id) as $produk){
        //       $res = $this->cpm->addStok($produk->nation_code,$produk->c_produk_id,$produk->qty);
        //       $this->order->trans_commit();
        //       if($res){
        //         $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --freezeOrders Product stock addition (rollback): SUCCEED --pID: '.$produk->c_produk_id.' --pQty: '.$produk->qty.' ');
        //       }else{
        //         $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --freezeOrders Product stock addition (rollback): FAILED --pID: '.$produk->c_produk_id.' --pQty: '.$produk->qty.' ');
        //       }
        //     }

        //     //get buyer and seller
        //     $buyer = $this->bu->getById($freeze->nation_code, $freeze->b_user_id_buyer);
        //     $seller = $this->bu->getById($freeze->nation_code, $freeze->b_user_id_seller);

        //     //add to d_order_prosess table for historical order process
        //     $dop = array();
        //     $dop['nation_code'] = $freeze->nation_code;
        //     $dop['d_order_id'] = $freeze->d_order_id;
        //     $dop['c_produk_id'] = $freeze->c_produk_id;
        //     $dop['id'] = $this->dopm->getLastId($freeze->nation_code, $freeze->d_order_id, $freeze->c_produk_id);
        //     $dop['nama'] = "Expired Payment";
        //     $dop['deskripsi'] = "This order was automatically canceled by the system because it had reached the specified time limit.";
        //     $dop['initiator'] = "system";
        //     $dop['cdate'] = "NOW()";
        //     $res = $this->dopm->set($dop);
        //     if ($res) {
        //         $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --freezeOrders --orderProgress: done '.$dop['initiator'].'-'.$dop['nama']);
        //     }

        //   }
        //   //end foreach
        // }
        // //end data freeze order


        //Sent notification to buyer before timeout

        //get unpaid list
        // $unpaids = $this->dodm->getBuyerUnPaid($payment_timeout);
        // $c = count($unpaids);
        // $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --unpaidsCount: '.$c);
        // if (count($unpaids)>0) {
        //     foreach ($unpaids as $unpaid) {
        //         //update status order to cancelled
        //         $res = $this->order->update($unpaid->nation_code, $unpaid->d_order_id, array("order_status"=>"cancelled"));
        //         $this->order->trans_commit();
        //         if ($res) {
        //             $this->seme_log->write("api_cron", "API_Cron/Five_Min::index --unpaids order_status: cancelled -- DONE");
        //         }

        //         //rollback stock
        //         foreach($this->dodim->getByOrderDetailId($unpaid->nation_code,$unpaid->d_order_id,$unpaid->d_order_detail_id) as $produk){
        //           $res = $this->cpm->addStok($produk->nation_code,$produk->c_produk_id,$produk->qty);
        //           $this->order->trans_commit();
        //           if($res){
        //             $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --unpaids Product stock addition (rollback): SUCCEED --pID: '.$produk->c_produk_id.' --pQty: '.$produk->qty.' ');
        //           }else{
        //             $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --unpaids Product stock addition (rollback): FAILED --pID: '.$produk->c_produk_id.' --pQty: '.$produk->qty.' ');
        //           }
        //         }

        //         //get buyer and seller
        //         $buyer = $this->bu->getById($unpaid->nation_code, $unpaid->b_user_id_buyer);
        //         $seller = $this->bu->getById($unpaid->nation_code, $unpaid->b_user_id_seller);

        //         //add to d_order_prosess table for historical order process
        //         $dop = array();
        //         $dop['nation_code'] = $unpaid->nation_code;
        //         $dop['d_order_id'] = $unpaid->d_order_id;
        //         $dop['c_produk_id'] = $unpaid->c_produk_id;
        //         $dop['id'] = $this->dopm->getLastId($unpaid->nation_code, $unpaid->d_order_id, $unpaid->c_produk_id);
        //         $dop['nama'] = "Expired Payment";
        //         $dop['deskripsi'] = "This order was automatically canceled by the system because it had reached the specified time limit.";
        //         $dop['initiator'] = "system";
        //         $dop['cdate'] = "NOW()";
        //         $res = $this->dopm->set($dop);
        //         if ($res) {
        //             $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --unpaids --orderProgress: done '.$dop['initiator'].'-'.$dop['nama']);
        //         }

        //         //By Donny Dennison - 6 june 2020 15:45
        //         //request by Mr Jackie, remove notification for 10 minute, 5 minute, expired payment buyer
        //         //START change by Donny Dennison
        //         //add to d_order_prosess table for historical order process
        //         // $dpe = array();
        //         // $dpe['nation_code'] = $unpaid->nation_code;
        //         // $dpe['b_user_id'] = $buyer->id;
        //         // $dpe['id'] = $this->dpem->getLastId($unpaid->nation_code, $buyer->id);
        //         // $dpe['type'] = "transaction";
        //         // $dpe['judul'] = "Expired Payment";
        //         // $dpe['teks'] = "Your order payment at ".date("l, j F Y H:i", strtotime($unpaid->d_order_cdate))." has expired, please repeat your order.";
        //         // $dpe['gambar'] = "media/pemberitahuan/transaction.png";
        //         // $dpe['cdate'] = "NOW()";
        //         // $extras = new stdClass();
        //         // $extras->id_order = "".$unpaid->d_order_id;
        //         // $extras->id_produk = null;
        //         // $extras->id_order_detail = null;
        //         // $extras->b_user_id_buyer = $buyer->id;
        //         // $extras->b_user_fnama_buyer = $buyer->fnama;
        //         // $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
        //         // $extras->b_user_id_seller = $seller->id;
        //         // $extras->b_user_fnama_seller = $seller->fnama;
        //         // $extras->b_user_image_seller = $this->cdn_url($seller->image);
        //         // $extras->status = "waiting_payment";
        //         // $dpe['extras'] = json_encode($extras);
        //         // $dpe['is_read'] = 0;
        //         // $res = $this->dpem->set($dpe);
        //         // if ($res) {
        //         //     $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --unpaids --pemberitahuan: done '.$dpe['type'].'-'.$dpe['judul'] );
        //         // }

        //         //get notification config for buyer
        //         // $setting_value = 0;
        //         // $classified = 'setting_notification_buyer';
        //         // $notif_code = 'B1';
        //         // $notif_cfg = $this->busm->getValue($unpaid->nation_code, $buyer->id, $classified, $notif_code);
        //         // if (isset($notif_cfg->setting_value)) {
        //         //     $setting_value = (int) $notif_cfg->setting_value;
        //         // }
        //         // if ($this->is_log) {
        //         //     $this->seme_log->write("api_cron", "API_Cron/Five_Min::index --unpaids -> buyer: $buyer->id, Notif enable: $setting_value");
        //         // }

        //         // $type = 'transaction';
        //         // $anotid = 15;
        //         // $replacer['date'] = date("l, j F Y H:i", strtotime($unpaid->d_order_cdate));
        //         // //push notif for seller
        //         // if (strlen($buyer->fcm_token)>50 && !empty($setting_value)) {
        //         //     //push notif to seller
        //         //     $device = $buyer->device;
        //         //     $tokens = array($buyer->fcm_token);
        //         //     $title = 'Expired Payment';
        //         //     $message = "Your order payment at ".date("l, j F Y H:i", strtotime($unpaid->d_order_cdate))." has expired, please repeat your order.";
        //         //     $type = 'transaction';
        //         //     $image = 'media/pemberitahuan/transaction.png';
        //         //     $payload = new stdClass();
        //         //     $payload->id_produk = null;
        //         //     $payload->id_order = "".$unpaid->d_order_id;
        //         //     $payload->id_order_detail = null;
        //         //     $payload->b_user_id_buyer = $buyer->id;
        //         //     $payload->b_user_fnama_buyer = $buyer->fnama;
        //         //     $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
        //         //     $payload->b_user_id_seller = $seller->id;
        //         //     $payload->b_user_fnama_seller = $seller->fnama;
        //         //     $payload->b_user_image_seller = $this->cdn_url($seller->image);
        //         //     $payload->status = "waiting_payment";
        //         //     $nw = $this->anot->get($unpaid->nation_code, "push", $type, $anotid);
        //         //     if (isset($nw->title)) {
        //         //         $title = $nw->title;
        //         //     }
        //         //     if (isset($nw->message)) {
        //         //         $message = $this->__nRep($nw->message, $replacer);
        //         //     }
        //         //     if (isset($nw->image)) {
        //         //         $image = $nw->image;
        //         //     }
        //         //     $image = $this->cdn_url($image);
        //         //     if ($this->is_push) {
        //         //         $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        //         //     }
        //         //     if ($this->is_log) {
        //         //         if ($this->is_push) {
        //         //             $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --unpaids -> __pushNotif(): '.json_encode($res));
        //         //         }
        //         //     }
        //         // }
        //         //END change by Donny Dennison
        //     }
        //     //end foreach
        // }
        //end data count unpaids

        
        //get unpaid order before last half timeout

        //by Donny Dennison - 20 october 2020 15:22
        //moved to one min cron
        //START by Donny Dennison - 20 october 2020 15:22

        // //by Donny Dennison - 20 october 2020 10:28
        // //change from 1 minute to 2 minute

        // //by Donny Dennison - 10 october 2020
        // //send notif 1 minute before payment expired and disable first notif payment
        // // $unpaid10 = $this->dodm->getBuyerUnPaidLast10($payment_timeout_half);

        // // $unpaid10 = $this->dodm->getBuyerUnPaidLast10(1);
        // $unpaid10 = $this->dodm->getBuyerUnPaidLast10(2);

        // $c = count($unpaid10);
        // $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --unpaids10Count: '.$c);
        // if (count($unpaid10)>0) {
        //     foreach ($unpaid10 as $unpaid) {
        //         //get product name
        //         $c_produk_nama = $unpaid->nama;
        //         if ($unpaid->total_item>1) {
        //             $c_produk_nama .= ' +'.($unpaid->total_item-1).' item(s)';
        //         }
        //         //update status order to cancelled
        //         $res = $this->order->update($unpaid->nation_code, $unpaid->d_order_id, array("payment_notif_count"=>"1"));
        //         $this->order->trans_commit();
        //         if ($res) {
        //             $this->seme_log->write("api_cron", "API_Cron/Five_Min::index --unpaids10 --paymentNotifCount: added");
        //         }

        //         //get buyer and seller
        //         $buyer = $this->bu->getById($unpaid->nation_code, $unpaid->b_user_id_buyer);
        //         $seller = $this->bu->getById($unpaid->nation_code, $unpaid->b_user_id_seller);

        //         //define notification message from DB
        //         $type = 'transaction';

        //         //by Donny Dennison - 10 october 2020
        //         //send notif 1 minute before payment expired and disable first notif payment
        //         // $anotid = 15;
        //         // $replacer = array();
        //         // $replacer['c_produk_nama'] = $c_produk_nama;
        //         // $replacer['invoice_code'] = $unpaid->invoice_code;
        //         // $replacer['date'] = date("l, j F Y H:i", strtotime($unpaid->d_order_cdate));

        //         //add to d_order_prosess table for historical order process
        //         $dpe = array();
        //         $dpe['nation_code'] = $unpaid->nation_code;
        //         $dpe['b_user_id'] = $buyer->id;
        //         $dpe['id'] = $this->dpem->getLastId($unpaid->nation_code, $buyer->id);
        //         $dpe['type'] = $type;
        //         $dpe['judul'] = "Waiting for payment";

        //         //by Donny Dennison - 10 october 2020
        //         //send notif 1 minute before payment expired and disable first notif payment
        //         // $dpe['teks'] = "Please finish your payment for this product ".$c_produk_nama." (".$unpaid->invoice_code.") immediately. If you don't complete your payment within the next 10 minutes, you must repeat your order.";
        //         $dpe['teks'] = "Please finish your payment for this product ".$c_produk_nama." (".$unpaid->invoice_code.") immediately. If you don't complete your payment within the next 1 minutes, you must repeat your order.";
                
        //         $dpe['gambar'] = "media/pemberitahuan/transaction.png";
        //         $dpe['cdate'] = "NOW()";
        //         $extras = new stdClass();
        //         $extras->id_order = "".$unpaid->d_order_id;
        //         $extras->id_produk = null;
        //         $extras->id_order_detail = null;
        //         $extras->b_user_id_buyer = $buyer->id;
        //         $extras->b_user_fnama_buyer = $buyer->fnama;
        //         $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
        //         $extras->b_user_id_seller = $seller->id;
        //         $extras->b_user_fnama_seller = $seller->fnama;
        //         $extras->b_user_image_seller = $this->cdn_url($seller->image);
        //         $extras->status = "waiting_payment";

        //         //by Donny Dennison - 10 october 2020
        //         //send notif 1 minute before payment expired and disable first notif payment
        //         // $nw = $this->anot->get($unpaid->nation_code, "list", $type, $anotid);
        //         // if (isset($nw->title)) {
        //         //     $dpe['judul'] = $nw->title;
        //         // }
        //         // if (isset($nw->message)) {
        //         //     $dpe['teks'] = $this->__nRep($nw->message, $replacer);
        //         // }
        //         // if (isset($nw->image)) {
        //         //     $dpe['gambar'] = $nw->image;
        //         // }
        //         $dpe['extras'] = json_encode($extras);
        //         $dpe['is_read'] = 0;
        //         $res = $this->dpem->set($dpe);
        //         if ($res) {
        //             $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --unpaids10 --pemberitahuan: done '.$dpe['type'].'-'.$dpe['judul'] );
        //         }

        //         //get notification config for buyer
        //         $setting_value = 0;
        //         $classified = 'setting_notification_buyer';
        //         $notif_code = 'B1';
        //         $notif_cfg = $this->busm->getValue($unpaid->nation_code, $buyer->id, $classified, $notif_code);
        //         if (isset($notif_cfg->setting_value)) {
        //             $setting_value = (int) $notif_cfg->setting_value;
        //         }
        //         if ($this->is_log) {
        //             $this->seme_log->write("api_cron", "API_Cron/Five_Min::index --unpaids10 -> buyer: $buyer->id, Notif enable: $setting_value");
        //         }

        //         //push notif for seller
        //         if (strlen($buyer->fcm_token)>50 && !empty($setting_value)) {
        //             //push notif to seller
        //             $device = $buyer->device;
        //             $tokens = array($buyer->fcm_token);
        //             $title = 'Waiting for Payment';
        //             $message = "Finish your payment for this product immediately: ".$c_produk_nama." (".$unpaid->invoice_code.").";
        //             $type = 'transaction';
        //             $image = 'media/pemberitahuan/transaction.png';
        //             $payload = new stdClass();
        //             $payload->id_order = "".$unpaid->d_order_id;
        //             $payload->id_produk = null;
        //             $payload->id_order_detail = null;
        //             $payload->b_user_id_buyer = $buyer->id;
        //             $payload->b_user_fnama_buyer = $buyer->fnama;
        //             $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
        //             $payload->b_user_id_seller = $seller->id;
        //             $payload->b_user_fnama_seller = $seller->fnama;
        //             $payload->b_user_image_seller = $this->cdn_url($seller->image);
        //             $payload->status = "waiting_payment";

        //             //by Donny Dennison - 10 october 2020
        //             //send notif 1 minute before payment expired and disable first notif payment
        //             // $nw = $this->anot->get($unpaid->nation_code, "push", $type, $anotid);
        //             // if (isset($nw->title)) {
        //             //     $title = $nw->title;
        //             // }
        //             // if (isset($nw->message)) {
        //             //     $message = $this->__nRep($nw->message, $replacer);
        //             // }
        //             // if (isset($nw->image)) {
        //             //     $image = $nw->image;
        //             // }
        //             $image = $this->cdn_url($image);
        //             if ($this->is_push) {
        //                 $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        //             }
        //             if ($this->is_log) {
        //                 if ($this->is_push) {
        //                     $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --unpaids10 -> __pushNotif(): '.json_encode($res));
        //                 }
        //             }
        //         }
                
        //     }//end foreach
        // }
        // //end data count unpaids10

        //END by Donny Dennison - 20 october 2020 15:22

        //by Donny Dennison - 30 July 2020 13:21
        //change auto reject become auto confirm
        //START change by Donny Dennison - 30 july 2020 13:21

        // //auto reject order from seller, get unconfirmed

        // //By Donny Dennison - 26 Juni 2020 21:05
        // //Request by Mr Jackie, jalankan auto reject sewaktu di 22:30 setiap hari
        // if(date('H:i:s') >= '22:30:00' && date('H:i:s') <= '23:00:00'){

        //     $unconfirmeds = $this->dodm->getSellerUnconfirmed($seller_confirm_timeout);
        //     $c = count($unconfirmeds);
        //     $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --unconfirmedSellerCount: '.$c);
        //     if ($c>0) {
        //         foreach ($unconfirmeds as $unconfirmed) {
        //             //update status order to cancelled
        //             $dux = array();
        //             $dux['settlement_status'] = "unconfirmed";
        //             $dux['seller_status'] = "rejected";
        //             $dux['refund_amount'] = $unconfirmed->sub_total+$unconfirmed->shipment_cost+$unconfirmed->shipment_cost_add;
        //             $dux['is_expired'] = 1;
        //             $res = $this->dodm->update($unconfirmed->nation_code, $unconfirmed->d_order_id, $unconfirmed->c_produk_id, $dux);
        //             $this->order->trans_commit();
        //             if ($res) {
        //                 $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --unconfirmedSeller --autoRejectedBySeller: SUCCEED --refund: '.$dux['refund_amount'].' ');
        //             }else{
        //                 $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --unconfirmedSeller --autoRejectedBySeller: FAILED');
        //             }

        //             //rollback stock
        //             foreach($this->dodim->getByOrderDetailId($unconfirmed->nation_code,$unconfirmed->d_order_id,$unconfirmed->d_order_detail_id) as $produk){
        //               $res = $this->cpm->addStok($produk->nation_code,$produk->c_produk_id,$produk->qty);
        //               $this->order->trans_commit();
        //               if($res){
        //                 $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --unconfirmedSeller Product stock addition (rollback): succeed --pID: '.$produk->c_produk_id.' --pQty: '.$produk->qty);
        //               }else{
        //                 $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --unconfirmedSeller Product stock addition (rollback): failed --pID: '.$produk->c_produk_id.' --pQty: '.$produk->qty);
        //               }
        //             }

        //             //get buyer and seller
        //             $buyer = $this->bu->getById($unconfirmed->nation_code, $unconfirmed->b_user_id_buyer);
        //             $seller = $this->bu->getById($unconfirmed->nation_code, $unconfirmed->b_user_id_seller);

        //             //add to d_order_prosess table for historical order process
        //             $dop = array();
        //             $dop['nation_code'] = $unconfirmed->nation_code;
        //             $dop['d_order_id'] = $unconfirmed->d_order_id;
        //             $dop['c_produk_id'] = $unconfirmed->c_produk_id;
        //             $dop['id'] = $this->dopm->getLastId($unconfirmed->nation_code, $unconfirmed->d_order_id, $unconfirmed->c_produk_id);
        //             $dop['nama'] = "Expired Order!";
        //             $dop['deskripsi'] = "Sorry, the seller cannot process your order for invoice: $unconfirmed->invoice_code";
        //             $dop['initiator'] = "system";
        //             $dop['cdate'] = "NOW()";
        //             $res = $this->dopm->set($dop);
        //             if ($res) {
        //                 $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --unconfirmedSeller --orderProgress: done '.$dop['initiator'].'-'.$dop['nama']);
        //             }

        //             //declare transaction notif
        //             $type = 'transaction';
        //             $anotid = 5;
        //             $replacer = array();
        //             $replacer['invoice_code'] = $unconfirmed->invoice_code;

        //             //add to notification list for buyer
        //             $dpe = array();
        //             $dpe['nation_code'] = $unconfirmed->nation_code;
        //             $dpe['b_user_id'] = $buyer->id;
        //             $dpe['id'] = $this->dpem->getLastId($unconfirmed->nation_code, $buyer->id);
        //             $dpe['type'] = "transaction";
        //             $dpe['judul'] = "Expired Order!";

        //             //by Donny Dennison - 26 Juni 2020 21:05
        //             //request by Mr Jackie, change the message from 12 hours to 10:30 pm tonight
        //             // $dpe['teks'] = "Sorry, the seller cannot process your order for invoice: $unconfirmed->invoice_code. Your money will be automatically refunded within 2 days.";
        //             $dpe['teks'] = "Sorry, the seller cannot process your order for invoice: $unconfirmed->invoice_code. Please try again a few minutes later.";

        //             $dpe['gambar'] = "media/pemberitahuan/transaction.png";
        //             $dpe['cdate'] = "NOW()";
        //             $extras = new stdClass();
        //             $extras->id_order = "".$unconfirmed->d_order_id;
        //             $extras->id_produk = null;
        //             $extras->id_order_detail = null;
        //             $extras->b_user_id_buyer = $buyer->id;
        //             $extras->b_user_fnama_buyer = $buyer->fnama;
        //             $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
        //             $extras->b_user_id_seller = $seller->id;
        //             $extras->b_user_fnama_seller = $seller->fnama;
        //             $extras->b_user_image_seller = $this->cdn_url($seller->image);
        //             $extras->status = "waiting_confirmation";
        //             $nw = $this->anot->get($unconfirmed->nation_code, "list", $type, $anotid);
        //             if (isset($nw->title)) {
        //                 $dpe['judul'] = $nw->title;
        //             }
        //             if (isset($nw->message)) {
        //                 $dpe['teks'] = $this->__nRep($nw->message, $replacer);
        //             }
        //             if (isset($nw->image)) {
        //                 $dpe['gambar'] = $nw->image;
        //             }
        //             //$dpe['gambar'] = $this->cdn_url($dpe['gambar']);
        //             $dpe['extras'] = json_encode($extras);
        //             $dpe['is_read'] = 0;
        //             $res = $this->dpem->set($dpe);
        //             if ($res) {
        //                 $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --unconfirmedSeller --pemberitahuan: done '.$dpe['type'].'-'.$dpe['judul'] );
        //             }

        //             //notification ID
        //             $anotid = 5;
        //             $replacer['date'] = date("l, j F Y H:i", strtotime($unconfirmed->d_order_cdate));
        //             //push notif for seller
        //             if (strlen($buyer->fcm_token)>50) {
        //                 //push notif to seller
        //                 $device = $buyer->device;
        //                 $tokens = array($buyer->fcm_token);
        //                 $title = 'Expired Order!';

        //                 //by Donny Dennison - 26 Juni 2020 21:05
        //                 //request by Mr Jackie, change the message from 12 hours to 10:30 pm tonight
        //                 // $message = "Your order payment at ".date("l, j F Y H:i", strtotime($unconfirmed->d_order_cdate))." has expired, please repeat your order.Sorry, the seller cannot process your order for invoice: $unconfirmed->invoice_code. Your money will be automatically refunded within 2 days.";
        //                 $message = "Your order payment at ".date("l, j F Y H:i", strtotime($unconfirmed->d_order_cdate))." has expired, please repeat your order.Sorry, the seller cannot process your order for invoice: $unconfirmed->invoice_code. Please try again a few minutes later.";

        //                 $type = 'transaction';
        //                 $image = 'media/pemberitahuan/transaction.png';
        //                 $payload = new stdClass();
        //                 $payload->id_order = "".$unconfirmed->d_order_id;
        //                 $payload->id_produk = null;
        //                 $payload->id_order_detail = null;
        //                 $payload->b_user_id_buyer = $buyer->id;
        //                 $payload->b_user_fnama_buyer = $buyer->fnama;
        //                 $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
        //                 $payload->b_user_id_seller = $seller->id;
        //                 $payload->b_user_fnama_seller = $seller->fnama;
        //                 $payload->b_user_image_seller = $this->cdn_url($seller->image);
        //                 $payload->status = "waiting_confirmation";
        //                 $nw = $this->anot->get($unconfirmed->nation_code, "push", $type, $anotid);
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
        //                 if ($this->is_push) {
        //                     $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        //                 }
        //                 if ($this->is_log) {
        //                     if ($this->is_push) {
        //                         $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --unconfirmedSeller -> __pushNotif(): '.json_encode($res));
        //                     }
        //                 }
        //             }
        //             if (!empty($this->send_email) && strlen($buyer->email)>4) {
        //                 $replacer = array();
        //                 $replacer['site_name'] = $this->app_name;
        //                 $replacer['fnama'] = $buyer->fnama;
        //                 $replacer['invoice_code'] = $unconfirmed->invoice_code;
        //                 $this->seme_email->flush();
        //                 $this->seme_email->replyto($this->site_name, $this->site_replyto);
        //                 $this->seme_email->from($this->site_email, $this->site_name);
        //                 $this->seme_email->subject('Expired Order!');
        //                 $this->seme_email->to($buyer->email, $buyer->fnama);
        //                 $this->seme_email->template('unconfirmed_by_seller');
        //                 $this->seme_email->replacer($replacer);
        //                 $this->seme_email->send();
        //                 if ($this->is_log) {
        //                     $this->seme_log->write("api_cron", $this->seme_email->getLog());
        //                 }
        //             }

        //             $anotid = 16;
        //             $replacer = array();
        //             $replacer['order'] = $unconfirmed->nama;
        //             $replacer['invoice_code'] = $unconfirmed->invoice_code;
        //             //add to notification list for seller
        //             $dpe = array();
        //             $dpe['nation_code'] = $unconfirmed->nation_code;
        //             $dpe['b_user_id'] = $seller->id;
        //             $dpe['id'] = $this->dpem->getLastId($unconfirmed->nation_code, $seller->id);
        //             $dpe['type'] = "transaction";
        //             $dpe['judul'] = "Auto Cancellation";

        //             //by Donny Dennison - 26 Juni 2020 21:05
        //             //request by Mr Jackie, change the message from 12 hours to 10:30 pm tonight
        //             // $dpe['teks'] = "Sorry, our system has canceled the order: $unconfirmed->nama ($unconfirmed->invoice_code) because you didn't confirm the order for the past 12 hours.";
        //             $dpe['teks'] = "Sorry, our system has canceled the order: $unconfirmed->nama ($unconfirmed->invoice_code) because you didn't confirm the order by 10:30 pm of the day.";

        //             $dpe['gambar'] = "media/pemberitahuan/transaction.png";
        //             $dpe['cdate'] = "NOW()";
        //             $extras = new stdClass();
        //             $extras->id_order = "".$unconfirmed->d_order_id;
        //             $extras->id_produk = "".$unconfirmed->d_order_detail_id;
        //             $extras->id_order_detail = "".$unconfirmed->d_order_detail_id;
        //             $extras->b_user_id_buyer = $buyer->id;
        //             $extras->b_user_fnama_buyer = $buyer->fnama;
        //             $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
        //             $extras->b_user_id_seller = $seller->id;
        //             $extras->b_user_fnama_seller = $seller->fnama;
        //             $extras->b_user_image_seller = $this->cdn_url($seller->image);
        //             $extras->status = "waiting_confirmation";
        //             $nw = $this->anot->get($unconfirmed->nation_code, "list", $type, $anotid);
        //             if (isset($nw->title)) {
        //                 $di2['judul'] = $nw->title;
        //             }
        //             if (isset($nw->message)) {
        //                 $di2['teks'] = $this->__nRep($nw->message, $replacer);
        //             }
        //             if (isset($nw->image)) {
        //                 $di2['gambar'] = $nw->image;
        //             }
        //             //$di2['gambar'] = $this->cdn_url($di2['gambar']);
        //             $dpe['extras'] = json_encode($extras);
        //             $dpe['is_read'] = 0;
        //             $res = $this->dpem->set($dpe);
        //             if ($res) {
        //                 $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --unconfirmedSeller --pemberitahuan: done '.$dpe['type'].'-'.$dpe['judul'] );
        //             }

        //             //push notif for seller
        //             if (strlen($seller->fcm_token)>50) {
        //                 $device = $seller->device;
        //                 $tokens = array($seller->fcm_token);
        //                 $title = 'Auto Cancellation';
        //                 $message = "Sorry, our system has canceled the order with invoice number: $unconfirmed->invoice_code.";
        //                 $type = 'transaction';
        //                 $image = 'media/pemberitahuan/transaction.png';
        //                 $payload = new stdClass();
        //                 $payload->id_order = "".$unconfirmed->d_order_id;
        //                 $payload->id_produk = "".$unconfirmed->d_order_detail_id;
        //                 $payload->id_order_detail = "".$unconfirmed->d_order_detail_id;
        //                 $payload->b_user_id_buyer = $buyer->id;
        //                 $payload->b_user_fnama_buyer = $buyer->fnama;
        //                 $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
        //                 $payload->b_user_id_seller = $seller->id;
        //                 $payload->b_user_fnama_seller = $seller->fnama;
        //                 $payload->b_user_image_seller = $this->cdn_url($seller->image);
        //                 $payload->status = "waiting_confirmation";
        //                 $nw = $this->anot->get($unconfirmed->nation_code, "push", $type, $anotid);
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
        //                 if ($this->is_push) {
        //                     $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        //                 }
        //                 if ($this->is_log) {
        //                     if ($this->is_push) {
        //                         $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --unconfirmedSeller -> __pushNotif(): '.json_encode($res));
        //                     }
        //                 }
        //             }

        //             //email for seller
        //             if (!empty($this->send_email) && strlen($seller->email)>4) {
        //                 $replacer = array();
        //                 $replacer['site_name'] = $this->app_name;
        //                 $replacer['fnama'] = $seller->fnama;
        //                 $replacer['produk_nama'] = $unconfirmed->nama;
        //                 $replacer['invoice_code'] = $unconfirmed->invoice_code;
        //                 $this->seme_email->flush();
        //                 $this->seme_email->replyto($this->site_name, $this->site_replyto);
        //                 $this->seme_email->from($this->site_email, $this->site_name);
        //                 $this->seme_email->subject('Auto Cancellation');
        //                 $this->seme_email->to($seller->email, $seller->fnama);
        //                 $this->seme_email->template('auto_cancellation');
        //                 $this->seme_email->replacer($replacer);
        //                 $this->seme_email->send();
        //                 if ($this->is_log) {
        //                     $this->seme_log->write("api_cron", $this->seme_email->getLog());
        //                 }
        //             }
        //         }
        //     }
        // }

        // if(date('H:i:s') >= '22:53:00' && date('H:i:s') <= '22:59:00'){

        //   $unconfirmeds = $this->dodm->getSellerUnconfirmed($seller_confirm_timeout);
        //   $c = count($unconfirmeds);
        //   $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --unconfirmedSellerCount: '.$c);
        //   if ($c>0) {
        //       foreach ($unconfirmeds as $unconfirmed) {

        //           //START copy from controller/api_mobile/seller/order/confirmed

        //           //get pickup address from product data
        //           $pickup = $this->dodpum->getById($unconfirmed->nation_code, $unconfirmed->d_order_id, $unconfirmed->d_order_detail_id);
        //           if (!isset($pickup->latitude) || !isset($pickup->longitude)) {
        //               $pickup = new stdClass();
        //               $pickup->latitude = 0;
        //               $pickup->longitude = 0;
        //           }

        //           //get destination address from order alamat table
        //           //by Donny Dennison - 17 juni 2020 20:18
        //           // request by Mr Jackie change Shipping Address into Receiving Address
        //           // $alamat_kode = 'A1';
        //           // $alamat_jenis = 'Shipping Address';
        //           $alamat_kode = 'A2';
        //           $alamat_jenis = 'Receiving Address';
        //           $address_status = $this->ccm->getByClassifiedByCodeName($unconfirmed->nation_code, "address", $alamat_jenis);
        //           if (isset($address_status->code)) {
        //               $alamat_kode = $address_status->code;
        //           }
        //           $alamat = $this->doam->getByOrderIdBuyerIdStatusAddress($unconfirmed->nation_code, $unconfirmed->d_order_id, $unconfirmed->b_user_id_buyer, $alamat_kode);
        //           if (!isset($alamat->latitude) || !isset($alamat->longitude)) {
        //               $alamat = new stdClass();
        //               $alamat->latitude = 0;
        //               $alamat->longitude = 0;
        //           }

        //           //start transaction
        //           // $this->order->trans_start();

        //           //populating update data
        //           $du = array();
        //           $du['seller_status'] = 'confirmed';
        //           $du['shipment_status'] = 'process';
        //           $du['shipment_tranid'] = '';
        //           $du['shipment_response'] = '';
        //           $du['shipment_confirmed'] = 0;
        //           $du['pickup_date'] = 'NULL';
        //           $du['date_begin'] = 'NULL';
        //           $du['date_expire'] = 'NULL';

        //           //add fallback if empty shipment service or type
        //           if (strlen($unconfirmed->shipment_service)<=1) {
        //               $unconfirmed->shipment_service = 'qxpress';
        //               $du['shipment_service'] = $unconfirmed->shipment_service;
        //           }
        //           if (strlen($unconfirmed->shipment_type)==0) {
        //               $unconfirmed->shipment_type = 'next day';
        //               $du['shipment_type'] = $unconfirmed->shipment_type;
        //           }
        //           $res = $this->dodm->update($unconfirmed->nation_code, $unconfirmed->d_order_id, $unconfirmed->d_order_detail_id, $du);
        //           if ($res) {
        //               $this->order->trans_commit();
        //               //build order history process
        //               $di = array();
        //               $di['nation_code'] = $unconfirmed->nation_code;
        //               $di['d_order_id'] = $unconfirmed->d_order_id;
        //               $di['c_produk_id'] = $unconfirmed->d_order_detail_id;
        //               $di['id'] = $this->dopm->getLastId($unconfirmed->nation_code, $unconfirmed->d_order_id, $unconfirmed->d_order_detail_id);
        //               $di['initiator'] = "Seller";
        //               $di['nama'] = "Seller Confirmed";
        //               $di['deskripsi'] = "The ordered product: ".html_entity_decode($unconfirmed->c_produk_nama,ENT_QUOTES)." has been processed by Seller.";
        //               $di['cdate'] = "NOW()";
        //               $di['is_done'] = "1";

        //               $res2 = $this->dopm->set($di);
        //               if ($res2) {
        //                   $this->order->trans_commit();
        //                   $this->status = 200;
        //                   $this->message = 'Success, ordered product now in process';
        //                   $this->erm->create($unconfirmed->nation_code, $unconfirmed->d_order_id, $unconfirmed->b_user_id_seller, $unconfirmed->b_user_id_buyer, 0, 0);
        //                   $this->order->trans_commit();

        //                   //get buyer data
        //                   $buyer = $this->bu->getById($unconfirmed->nation_code, $unconfirmed->b_user_id_buyer);
        //                   $seller = $this->bu->getById($unconfirmed->nation_code, $unconfirmed->b_user_id_seller);

        //                   //declare notification
        //                   $type = 'transaction';
        //                   $anotid = 2;
        //                   $replacer = array();

        //                   //collect array notification list for buyer
        //                   $dpe = array();
        //                   $dpe['nation_code'] = $unconfirmed->nation_code;
        //                   $dpe['b_user_id'] = $buyer->id;
        //                   $dpe['id'] = $this->dpem->getLastId($unconfirmed->nation_code, $buyer->id);
        //                   $dpe['type'] = $type;
        //                   if($buyer->language_id == 2) {
        //                     $dpe['judul'] = "Pesanan sedang diproses";
        //                     $dpe['teks'] = "Pesanan dikonfirmasi! Kami sedang memproses pesanan Anda.";
        //                   } else {
        //                     $dpe['judul'] = "Order is being processed";
        //                     $dpe['teks'] = "Order confirmed! We are processing your order.";
        //                   }
                          
        //                   $dpe['cdate'] = "NOW()";
        //                   $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
        //                   $extras = new stdClass();
        //                   $extras->id_order = "".$unconfirmed->d_order_id;
        //                   $extras->id_produk = "".$unconfirmed->c_produk_id;
        //                   $extras->id_order_detail = "".$unconfirmed->d_order_detail_id;
        //                   $extras->b_user_id_buyer = $buyer->id;
        //                   $extras->b_user_fnama_buyer = $buyer->fnama;
                          
        //                   // by Muhammad Sofi - 27 October 2021 10:12
        //                   // if user img & banner not exist or empty, change to default image
        //                   // $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
        //                   if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
        //                       $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
        //                   } else {
        //                       $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
        //                   }
        //                   $extras->b_user_id_seller = $seller->id;
        //                   $extras->b_user_fnama_seller = $seller->fnama;
                          
        //                   // by Muhammad Sofi - 27 October 2021 10:12
        //                   // if user img & banner not exist or empty, change to default image
        //                   // $extras->b_user_image_seller = $this->cdn_url($seller->image);
        //                   if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
        //                       $extras->b_user_image_seller = $this->cdn_url($seller->image);
        //                   } else {
        //                       $extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
        //                   }
        //                   $dpe['extras'] = json_encode($extras);
        //                   $nw = $this->anot->get($unconfirmed->nation_code, "list", $type, $anotid, $buyer->language_id);
        //                   if (isset($nw->title)) {
        //                       $dpe['judul'] = $nw->title;
        //                   }
        //                   if (isset($nw->message)) {
        //                       $dpe['teks'] = $this->__nRep($nw->message, $replacer);
        //                   }
        //                   if (isset($nw->image)) {
        //                       $dpe['gambar'] = $nw->image;
        //                   }
        //                   $this->dpem->set($dpe);
        //                   $this->order->trans_commit();

        //                   //get notification config for buyer
        //                   $setting_value = 0;
        //                   $classified = 'setting_notification_buyer';
        //                   $notif_code = 'B2';
        //                   $notif_cfg = $this->busm->getValue($unconfirmed->nation_code, $buyer->id, $classified, $notif_code);
        //                   if (isset($notif_cfg->setting_value)) {
        //                       $setting_value = (int) $notif_cfg->setting_value;
        //                   }

        //                   //push notif to buyer
        //                   if (strlen($buyer->fcm_token) > 50 && !empty($setting_value)) {
        //                       $device = $buyer->device;
        //                       $tokens = array($buyer->fcm_token);
        //                       if($buyer->language_id == 2) {
        //                         $title = 'Pesanan sedang diproses';
        //                         $message = "Pesanan dikonfirmasi! Kami sedang memproses pesanan Anda.";
        //                       } else {
        //                         $title = 'Order is being processed';
        //                         $message = "Order confirmed! We are processing your order.";
        //                       }
        //                       $image = 'media/pemberitahuan/transaction.png';
        //                       $payload = new stdClass();
        //                       $payload->id_produk = "".$unconfirmed->c_produk_id;
        //                       $payload->id_order = "".$unconfirmed->d_order_id;
        //                       $payload->id_order_detail = "".$unconfirmed->d_order_detail_id;
        //                       $payload->b_user_id_buyer = $buyer->id;
        //                       $payload->b_user_fnama_buyer = $buyer->fnama;
                              
        //                       // by Muhammad Sofi - 27 October 2021 10:12
        //                       // if user img & banner not exist or empty, change to default image
        //                       // $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
        //                       if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
        //                           $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
        //                       } else {
        //                           $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
        //                       }
        //                       $payload->b_user_id_seller = $seller->id;
        //                       $payload->b_user_fnama_seller = $seller->fnama;
                              
        //                       // by Muhammad Sofi - 27 October 2021 10:12
        //                       // if user img & banner not exist or empty, change to default image
        //                       // $payload->b_user_image_seller = $this->cdn_url($seller->image);
        //                       if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
        //                           $payload->b_user_image_seller = $this->cdn_url($seller->image);
        //                       } else {
        //                           $payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
        //                       }
        //                       $nw = $this->anot->get($unconfirmed->nation_code, "push", $type, $anotid, $buyer->language_id);
        //                       if (isset($nw->title)) {
        //                           $title = $nw->title;
        //                       }
        //                       if (isset($nw->message)) {
        //                           $message = $this->__nRep($nw->message, $replacer);
        //                       }
        //                       if (isset($nw->image)) {
        //                           $image = $nw->image;
        //                       }
        //                       $image = $this->cdn_url($image);
        //                       $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        //                       if ($this->is_log) {
        //                           $this->seme_log->write("api_cron", 'API_Cron/five_min::auto confirmed __pushNotif: '.json_encode($res));
        //                       }
        //                   }

        //                   //declare notif for seller
        //                   $type = 'transaction';
        //                   $anotid = 1;
        //                   $replacer = array();

        //                   //collect array notification list for seller
        //                   $dpe = array();
        //                   $dpe['nation_code'] = $unconfirmed->nation_code;
        //                   $dpe['b_user_id'] = $seller->id;
        //                   $dpe['id'] = $this->dpem->getLastId($unconfirmed->nation_code, $seller->id);
        //                   $dpe['type'] = $type;
        //                   if($seller->language_id == 2) {
        //                     $dpe['judul'] = "Pesanan Dikonfirmasi (Sedang Diproses)";
                          
        //                     //Donny Dennison - 17-07-2020 17:48
        //                     //delete print waybill
        //                     // $dpe['teks'] = "Please prepare the order immediately. We have sent the waybill to your e-mail. Use the \"Print Waybill\" feature in the app and don't forget to post the waybill on the packaging!";
        //                     $dpe['teks'] = "SellOn akan mengirimkan email kepada Anda \"Waybill\", mohon tunggu beberapa saat.";
        //                   } else {
        //                     $dpe['judul'] = "Order Confirmed (In Process)";
        //                     $dpe['teks'] = "SellOn will email you a \"Waybill\", please wait a moment.";
        //                   }
                          

        //                   $dpe['cdate'] = "NOW()";
        //                   $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
        //                   $extras = new stdClass();
        //                   $extras->id_order = "".$unconfirmed->d_order_id;
        //                   $extras->id_produk = "".$unconfirmed->c_produk_id;
        //                   $extras->id_order_detail = "".$unconfirmed->d_order_detail_id;
        //                   $extras->b_user_id_buyer = $buyer->id;
        //                   $extras->b_user_fnama_buyer = $buyer->fnama;
                          
        //                   // by Muhammad Sofi - 27 October 2021 10:12
        //                   // if user img & banner not exist or empty, change to default image
        //                   // $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
        //                   if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
        //                       $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
        //                   } else {
        //                       $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
        //                   }
        //                   $extras->b_user_id_seller = $seller->id;
        //                   $extras->b_user_fnama_seller = $seller->fnama;
                          
        //                   // by Muhammad Sofi - 27 October 2021 10:12
        //                   // if user img & banner not exist or empty, change to default image
        //                   // $extras->b_user_image_seller = $this->cdn_url($seller->image);
        //                   if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
        //                       $extras->b_user_image_seller = $this->cdn_url($seller->image);
        //                   } else {
        //                       $extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
        //                   }
        //                   $nw = $this->anot->get($unconfirmed->nation_code, "list", $type, $anotid, $seller->language_id);
        //                   if (isset($nw->title)) {
        //                       $dpe['judul'] = $nw->title;
        //                   }
        //                   if (isset($nw->message)) {
        //                       $dpe['teks'] = $this->__nRep($nw->message, $replacer);
        //                   }
        //                   if (isset($nw->image)) {
        //                       $dpe['gambar'] = $nw->image;
        //                   }
        //                   $dpe['extras'] = json_encode($extras);
        //                   $this->dpem->set($dpe);
        //                   $this->order->trans_commit();

        //                   //get notification config for seller
        //                   $setting_value = 0;
        //                   $classified = 'setting_notification_seller';
        //                   $notif_code = 'S0';
        //                   $notif_cfg = $this->busm->getValue($unconfirmed->nation_code, $seller->id, $classified, $notif_code);
        //                   if (isset($notif_cfg->setting_value)) {
        //                       $setting_value = (int) $notif_cfg->setting_value;
        //                   }

        //                   //push notif to seller
        //                   if (strlen($seller->fcm_token) > 50 && !empty($setting_value)) {
        //                       $device = $seller->device;
        //                       $tokens = array($seller->fcm_token);
        //                       if($seller->language_id == 2) {
        //                         $title = 'Pesanan Dikonfirmasi (Sedang Diproses)';
                              
        //                         //Donny Dennison - 17-07-2020 17:48
        //                         //delete print waybill
        //                         // $message = "Please prepare the order immediately. Don't forget to post the waybill on the packaging!";
        //                         $message = "SellOn akan mengirimkan email kepada Anda \"Waybill\", mohon tunggu beberapa saat.";
        //                       } else {
        //                         $title = 'Order Confirmed (In Process)';
        //                         $message = "SellOn will email you a \"Waybill\", please wait a moment.";
        //                       }

        //                       $image = 'media/pemberitahuan/transaction.png';
        //                       $payload = new stdClass();
        //                       $payload = new stdClass();
        //                       $payload->id_produk = "".$unconfirmed->c_produk_id;
        //                       $payload->id_order = "".$unconfirmed->d_order_id;
        //                       $payload->id_order_detail = "".$unconfirmed->d_order_detail_id;
        //                       $payload->b_user_id_buyer = $buyer->id;
        //                       $payload->b_user_fnama_buyer = $buyer->fnama;
                              
        //                       // by Muhammad Sofi - 27 October 2021 10:12
        //                       // if user img & banner not exist or empty, change to default image
        //                       // $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
        //                       if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
        //                           $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
        //                       } else {
        //                           $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
        //                       }
        //                       $payload->b_user_id_seller = $seller->id;
        //                       $payload->b_user_fnama_seller = $seller->fnama;
                              
        //                       // by Muhammad Sofi - 27 October 2021 10:12
        //                       // if user img & banner not exist or empty, change to default image
        //                       // $payload->b_user_image_seller = $this->cdn_url($seller->image);
        //                       if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
        //                           $payload->b_user_image_seller = $this->cdn_url($seller->image);
        //                       } else {
        //                           $payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
        //                       }
        //                       $nw = $this->anot->get($unconfirmed->nation_code, 'push', $type, $anotid, $seller->language_id);
        //                       if (isset($nw->title)) {
        //                           $title = $nw->title;
        //                       }
        //                       if (isset($nw->message)) {
        //                           $message = $this->__nRep($nw->message, $replacer);
        //                       }
        //                       if (isset($nw->image)) {
        //                           $image = $nw->image;
        //                       }
        //                       $image = $this->cdn_url($image);
        //                       $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        //                       if ($this->is_log) {
        //                           $this->seme_log->write("api_cron", 'API_Cron/five_min::auto confirmed __pushNotif: '.json_encode($res));
        //                       }
        //                   }

        //                   //send email for seller
        //                   if ($this->email_send && strlen($seller->email)>4) {
        //                       $replacer = array();
        //                       $replacer['site_name'] = $this->app_name;
        //                       $replacer['fnama'] = $seller->fnama;
        //                       $this->seme_email->flush();
        //                       $this->seme_email->replyto($this->site_name, $this->site_replyto);
        //                       $this->seme_email->from($this->site_email, $this->site_name);
        //                       $this->seme_email->subject('Order Confirmed (Processing)');
        //                       $this->seme_email->to($seller->email, $seller->fnama);
        //                       $this->seme_email->template('order_confirmed_processing');
        //                       $this->seme_email->replacer($replacer);
        //                       $this->seme_email->send();
        //                       if ($this->is_log) {
        //                           $this->seme_log->write("api_cron", "API_Cron/five_min::auto confirmed --emailSentSeller: $seller->email");
        //                       }
        //                   }
                  
        //               }

        //           }

        //           //END copy from controller/api_mobile/seller/order/confirmed

        //       }

        //   }

        // }
        //END change by Donny Dennison - 30 july 2020 13:21

        // comment code for new flow
        // //get online user list
        // $userOnline = $this->bu->getUserOnline();
        // if (count($userOnline)>0) {
        //   foreach ($userOnline as $user) {

        //     $di = array();
        //     $di['is_online'] = 0;
        //     $this->bu->update($user->id, $di);
        //     $this->order->trans_commit();

        //   }
        //   unset($userOnline, $user, $di);
        // }

        //get all postal district
        $nation_code = 62;
        // $allLocation = $this->ghcm->getAllByLocationGroupBy($nation_code);
        // foreach($allLocation AS $location){
        
        //   //get total highlight community
        //   $highlightPost = $this->ghcm->countAllByLocation($nation_code, $location->kelurahan, $location->kecamatan, $location->kabkota, $location->provinsi);
        //   //check total more than 50
        //   if ($highlightPost > 30) {

        //     $highlightPost = $highlightPost - 30;

        //     $this->ghcm->updateByPriorityDesc($nation_code, $location->kelurahan, $location->kecamatan, $location->kabkota, $location->provinsi, $highlightPost);
        //     $this->order->trans_commit();

        //     $this->ghcm->delete($nation_code);
        //     $this->order->trans_commit();

        //   }
        //   unset($highlightPost);

        // }
        // unset($allLocation, $location);

        //START by Donny Dennison - 12 july 2022 14:56
        //new offer system
        //get waiting for review list
        $waitingReviews = $this->ecrm->getAll($nation_code, 'offer', 0, array(0 => "accepted", 1 => "waiting review from seller", 2 => "waiting review from buyer"), 5);  // by Muhammad Sofi 2 December 2022 | change auto review from interval 1 day to 2 day
        $c = count($waitingReviews);
        $this->seme_log->write("api_cron", 'API_Cron/Five_Min::index --waitingReviewsCount: '.$c);
        if (count($waitingReviews)>0) {
          foreach ($waitingReviews as $review) {

            $chatAcceptedType = $this->chat->getLastAcceptedByChatRoomId($nation_code, $review->id);

            if($review->offer_status == "waiting review from seller" || $review->offer_status == "accepted"){

                $reviewExistSeller = $this->eorm->getByChatRoomId($nation_code, $review->id, $chatAcceptedType->id, "buyer");
                if(!isset($reviewExistSeller->chat_room_id)){

                    //get last id
                    $review_id = $this->eorm->getLastId($nation_code);

                    //insert into database
                    $di = array();
                    $di['id'] = $review_id;
                    $di['nation_code'] = $nation_code;
                    $di['e_chat_room_id'] = $review->id;
                    $di['e_chat_id'] = $chatAcceptedType->id;
                    $di['b_user_id'] = $review->b_user_id_seller;
                    $di['b_user_id_to'] = $review->b_user_id_starter;
                    $di['type'] = "buyer";
                    $di['star'] = 5;
                    // by Muhammad Sofi 2 December 2022 | change message from Auto Review By System
                    $userLanguage = $this->bu->getById($nation_code, $review->b_user_id_seller);
                    if($userLanguage->language_id == 2) {
                        $di['review'] = "Pembeli sangat baik";
                    } else {
                        // $di['review'] = "Auto Review By System";
                        $di['review'] = "That's very nice";
                    }
                    $di['cdate'] = "NOW()";

                    $this->eorm->set($di);
                    $this->order->trans_commit();

                    $this->bu->updateTotal($nation_code, $review->b_user_id_starter, "offer_rating_buyer_total", "+", 1);
                    $this->order->trans_commit();

                    $avg_star = $this->eorm->getAvg($nation_code, $review->b_user_id_starter, "buyer")->avg_star;

                    $du = array();
                    $du['offer_rating_buyer_avg'] = $avg_star;
                    $this->bu->update($review->b_user_id_starter, $du);
                    $this->order->trans_commit();

                }

            }

            if($review->offer_status == "waiting review from buyer" || $review->offer_status == "accepted"){

                $reviewExistBuyer = $this->eorm->getByChatRoomId($nation_code, $review->id, $chatAcceptedType->id, "seller");
                if(!isset($reviewExistBuyer->chat_room_id)){

                    //get last id
                    $review_id = $this->eorm->getLastId($nation_code);

                    //insert into database
                    $di = array();
                    $di['id'] = $review_id;
                    $di['nation_code'] = $nation_code;
                    $di['e_chat_room_id'] = $review->id;
                    $di['e_chat_id'] = $chatAcceptedType->id;
                    $di['b_user_id'] = $review->b_user_id_starter;
                    $di['b_user_id_to'] = $review->b_user_id_seller;
                    $di['type'] = "seller";
                    $di['star'] = 5;
                    // by Muhammad Sofi 2 December 2022 | change message from Auto Review By System
                    $userLanguage = $this->bu->getById($nation_code, $review->b_user_id_starter);
                    if($userLanguage->language_id == 2) {
                        $di['review'] = "Barangnya bagus";
                    } else {
                        // $di['review'] = "Auto Review By System";
                        $di['review'] = "That's very nice";
                    }
                    $di['cdate'] = "NOW()";

                    $this->eorm->set($di);
                    $this->order->trans_commit();

                    $this->bu->updateTotal($nation_code, $review->b_user_id_seller, "offer_rating_seller_total", "+", 1);
                    $this->order->trans_commit();

                    $avg_star = $this->eorm->getAvg($nation_code, $review->b_user_id_seller, "seller")->avg_star;

                    $du = array();
                    $du['offer_rating_seller_avg'] = $avg_star;
                    $this->bu->update($review->b_user_id_seller, $du);
                    $this->order->trans_commit();

                }

            }

            $du = array();
            $du['offer_status'] = "reviewed";
            $du['offer_status_update_date'] = "NOW()";
            $this->ecrm->update($nation_code, $review->id, $du);
            $this->order->trans_commit();

            //START by Donny Dennison - 10 august 2022 10:10
            //new point rule for offer system
            $produk = $this->cpm->getByIdIgnoreActive($nation_code, $review->c_produk_id);

            // $checkAlreadyGetPointBuyer = $this->glphm->checkAlreadyInDB($nation_code, "", "", "", "", "", $review->b_user_id_starter, $review->id, "offer", "review", "");

            // $checkAlreadyGetPointSeller = $this->glphm->checkAlreadyInDB($nation_code, "", "", "", "", "", $review->b_user_id_seller, $review->id, "offer", "review", "");

            //buyer
            // if($produk->product_type != "Free"){

            //   if(!isset($checkAlreadyGetPointBuyer->id) && !isset($checkAlreadyGetPointSeller->id)){

            //     //get point
            //     $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "ER");
            //     if (!isset($pointGet->remark)) {
            //       $pointGet = new stdClass();
            //       $pointGet->remark = 30;
            //     }

            //     $user = $this->bu->getById($nation_code, $review->b_user_id_starter);

            //     $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $review->b_user_id_starter);

            //     $di = array();
            //     $di['nation_code'] = $nation_code;
            //     $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
            //     $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
            //     $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
            //     $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
            //     $di['b_user_id'] = $review->b_user_id_starter;
            //     $di['point'] = $pointGet->remark;
            //     $di['custom_id'] = $review->id;
            //     $di['custom_type'] = 'offer';
            //     $di['custom_type_sub'] = 'review';
            //     $di['custom_text'] = $user->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
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
            //     $this->order->trans_commit();
            //     // $this->glrm->updateTotal($nation_code, $review->b_user_id_starter, 'total_point', '+', $di['point']);
            //     // $this->order->trans_commit();
            //     // $this->glrm->updateTotal($nation_code, $review->b_user_id_starter, 'total_post', '+', 1);
            //     // $this->order->trans_commit();
            //   }
            // }

            //START by Donny Dennison - 02 september 2022 14:20
            //record user sales and total transaction as seller and buyer
            if($produk->product_type != "Free"){
              $ExistingData = $this->buosm->getByUserId($nation_code, $review->b_user_id_starter, date("Y"), date("m"));
              if(!isset($ExistingData->b_user_id)){
                // $lastId = $this->buosm->getLastId($nation_code);
                $di = array();
                $di['nation_code'] = $nation_code;
                // $di['id'] = $lastId;
                $di['b_user_id'] = $review->b_user_id_starter;
                $di['year'] = date("Y");
                $di['month'] = date("m");
                $di['total_sales_buyer'] = $chatAcceptedType->message;
                $di['total_transaction_buyer'] = 1;
                $this->buosm->set($di);
                $this->order->trans_commit();

              }else{

                $this->buosm->updateTotal($nation_code, $review->b_user_id_starter, date("Y"), date("m"), "total_sales_buyer", "+", $chatAcceptedType->message);
                $this->order->trans_commit();

                $this->buosm->updateTotal($nation_code, $review->b_user_id_starter, date("Y"), date("m"), "total_transaction_buyer", "+", 1);
                $this->order->trans_commit();

              }

            }else{

              $ExistingData = $this->buosm->getByUserId($nation_code, $review->b_user_id_starter, date("Y"), date("m"));
              if(!isset($ExistingData->b_user_id)){

                // $lastId = $this->buosm->getLastId($nation_code);
                $di = array();
                $di['nation_code'] = $nation_code;
                // $di['id'] = $lastId;
                $di['b_user_id'] = $review->b_user_id_starter;
                $di['year'] = date("Y");
                $di['month'] = date("m");
                $di['total_sales_buyer'] = 0;
                $di['total_transaction_buyer'] = 1;
                $this->buosm->set($di);
                $this->order->trans_commit();

              }else{

                $this->buosm->updateTotal($nation_code, $review->b_user_id_starter, date("Y"), date("m"), "total_transaction_buyer", "+", 1);
                $this->order->trans_commit();

              }

            }
            //END by Donny Dennison - 02 september 2022 14:20
            //record user sales and total transaction as seller and buyer

            //seller
            // if(!isset($checkAlreadyGetPointBuyer->id) && !isset($checkAlreadyGetPointSeller->id)){

            //   if($produk->product_type == "Free"){

            //     //get point
            //     $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "ES");
            //     if (!isset($pointGet->remark)) {
            //       $pointGet = new stdClass();
            //       $pointGet->remark = 50;
            //     }

            //   }else{

            //     //get point
            //     $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EQ");
            //     if (!isset($pointGet->remark)) {
            //       $pointGet = new stdClass();
            //       $pointGet->remark = 30;
            //     }

            //   }

            //   $user = $this->bu->getById($nation_code, $review->b_user_id_seller);

            //   $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $review->b_user_id_seller);

            //   $di = array();
            //   $di['nation_code'] = $nation_code;
            //   $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
            //   $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
            //   $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
            //   $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
            //   $di['b_user_id'] = $review->b_user_id_seller;
            //   $di['point'] = $pointGet->remark;
            //   $di['custom_id'] = $review->id;
            //   $di['custom_type'] = 'offer';

            //   if($produk->product_type == "Free"){
            //       $di['custom_type_sub'] = 'review free product';
            //   }else{
            //       $di['custom_type_sub'] = 'review';
            //   }

            //   $di['custom_text'] = $user->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
          // $endDoWhile = 0;
          // do{
          //   $leaderBoardHistoryId = $this->GUIDv4();
          //   $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
          //   if($checkId == 0){
          //     $endDoWhile = 1;
          //   }
          // }while($endDoWhile == 0);
          // $di['id'] = $leaderBoardHistoryId;
            //   $this->glphm->set($di);
            //   $this->order->trans_commit();
            //   // $this->glrm->updateTotal($nation_code, $review->b_user_id_seller, 'total_point', '+', $di['point']);
            //   // $this->order->trans_commit();
            //   // $this->glrm->updateTotal($nation_code, $review->b_user_id_seller, 'total_post', '+', 1);
            //   // $this->order->trans_commit();
            //   //END by Donny Dennison - 10 august 2022 10:10
            //   //new point rule for offer system
            // }

            //START by Donny Dennison - 02 september 2022 14:20
            //record user sales and total transaction as seller and buyer
            if($produk->product_type != "Free"){
              $ExistingData = $this->buosm->getByUserId($nation_code, $review->b_user_id_seller, date("Y"), date("m"));
              if(!isset($ExistingData->b_user_id)){
                // $lastId = $this->buosm->getLastId($nation_code);
                $di = array();
                $di['nation_code'] = $nation_code;
                // $di['id'] = $lastId;
                $di['b_user_id'] = $review->b_user_id_seller;
                $di['year'] = date("Y");
                $di['month'] = date("m");
                $di['total_sales_seller'] = $chatAcceptedType->message;
                $di['total_transaction_seller'] = 1;
                $this->buosm->set($di);
                $this->order->trans_commit();

              }else{

                $this->buosm->updateTotal($nation_code, $review->b_user_id_seller, date("Y"), date("m"), "total_sales_seller", "+", $chatAcceptedType->message);
                $this->order->trans_commit();

                $this->buosm->updateTotal($nation_code, $review->b_user_id_seller, date("Y"), date("m"), "total_transaction_seller", "+", 1);
                $this->order->trans_commit();

              }

            }else{

              $ExistingData = $this->buosm->getByUserId($nation_code, $review->b_user_id_seller, date("Y"), date("m"));
              if(!isset($ExistingData->b_user_id)){

                // $lastId = $this->buosm->getLastId($nation_code);
                $di = array();
                $di['nation_code'] = $nation_code;
                // $di['id'] = $lastId;
                $di['b_user_id'] = $review->b_user_id_seller;
                $di['year'] = date("Y");
                $di['month'] = date("m");
                $di['total_sales_seller'] = 0;
                $di['total_transaction_seller'] = 1;
                $this->buosm->set($di);
                $this->order->trans_commit();

              }else{

                $this->buosm->updateTotal($nation_code, $review->b_user_id_seller, date("Y"), date("m"), "total_transaction_seller", "+", 1);
                $this->order->trans_commit();

              }

            }
            //END by Donny Dennison - 02 september 2022 14:20
            //record user sales and total transaction as seller and buyer

            $review->c_produk_nama = html_entity_decode($review->c_produk_nama,ENT_QUOTES);

            if($review->offer_status == "waiting review from seller" || $review->offer_status == "accepted"){

              $sender = $this->bu->getById($nation_code, $review->b_user_id_seller);
              $receiver = $this->bu->getById($nation_code, $review->b_user_id_starter);

              $dpe = array();
              $dpe['nation_code'] = $nation_code;
              $dpe['b_user_id'] = $receiver->id;
              $dpe['id'] = $this->dpem->getLastId($nation_code, $receiver->id);
              $dpe['type'] = "offer_review";

              if($receiver->language_id == 2) {
                $dpe['judul'] = "Ulasan Penawaran";
                $dpe['teks'] =  "Penjual telah meninggalkan ulasan di ".$review->c_produk_nama."(auto)";
              } else {
                $dpe['judul'] = "Offer Review";
                $dpe['teks'] =  "Seller's review for botol ".$review->c_produk_nama. " is done now(auto)";
              }

              $dpe['gambar'] = 'media/pemberitahuan/outbounding.png';
              $dpe['cdate'] = "NOW()";
              $extras = new stdClass();
              // $extras->chat_room_id = $review->id;
              $extras->c_produk_id = $review->c_produk_id;
              // $extras->c_produk_nama = $review->c_produk_nama;
              // $extras->b_user_id_starter = $review->b_user_id_starter;
              // $extras->b_user_id_seller = $review->b_user_id_seller;

              if($receiver->language_id == 2) {
                $extras->judul = "Ulasan Penawaran";
                $extras->teks =  "Penjual telah meninggalkan ulasan di ".$review->c_produk_nama."(auto)";
              } else {
                $extras->judul = "Offer Review";
                $extras->teks =  "Seller's review for botol ".$review->c_produk_nama. " is done now(auto)";
              }

              $dpe['extras'] = json_encode($extras);
              $this->dpem->set($dpe);
              $this->order->trans_commit();

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

                  if($receiver->language_id == 2) {
                    $title = "Ulasan Penawaran";
                    $message =  "Penjual telah meninggalkan ulasan di ".$review->c_produk_nama."(auto)";
                  } else {
                    $title = "Offer Review";
                    $message =  "Seller's review for botol ".$review->c_produk_nama. " is done now(auto)";
                  }

                  $type = 'offer_review';
                  $image = 'media/pemberitahuan/outbounding.png';
                  $payload = new stdClass();
                  // $payload->chat_room_id = $review->id;
                  $payload->c_produk_id = $review->c_produk_id;
                  // $payload->c_produk_nama = $review->c_produk_nama;
                  // $payload->b_user_id_starter = $review->b_user_id_starter;
                  // $payload->b_user_id_seller = $review->b_user_id_seller;

                  $image = $this->cdn_url($image);
                  $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
                }

              }

            }

            if($review->offer_status == "waiting review from buyer" || $review->offer_status == "accepted"){

              $sender = $this->bu->getById($nation_code, $review->b_user_id_starter);
              $receiver = $this->bu->getById($nation_code, $review->b_user_id_seller);

              $dpe = array();
              $dpe['nation_code'] = $nation_code;
              $dpe['b_user_id'] = $receiver->id;
              $dpe['id'] = $this->dpem->getLastId($nation_code, $receiver->id);
              $dpe['type'] = "offer_review";

              if($receiver->language_id == 2) {
                $dpe['judul'] = "Ulasan Penawaran";
                $dpe['teks'] =  "Pembeli telah meninggalkan ulasan di ".$review->c_produk_nama."(auto)";
              } else {
                $dpe['judul'] = "Offer Review";
                $dpe['teks'] =  "Buyer's review for botol ".$review->c_produk_nama. " is done now(auto)";
              }

              $dpe['gambar'] = 'media/pemberitahuan/outbounding.png';
              $dpe['cdate'] = "NOW()";
              $extras = new stdClass();
              // $extras->chat_room_id = $review->id;
              $extras->c_produk_id = $review->c_produk_id;
              // $extras->c_produk_nama = $review->c_produk_nama;
              // $extras->b_user_id_starter = $review->b_user_id_starter;
              // $extras->b_user_id_seller = $review->b_user_id_seller;

              if($receiver->language_id == 2) {
                $extras->judul = "Ulasan Penawaran";
                $extras->teks =  "Pembeli telah meninggalkan ulasan di ".$review->c_produk_nama."(auto)";
              } else {
                $extras->judul = "Offer Review";
                $extras->teks =  "Buyer's review for botol ".$review->c_produk_nama. " is done now(auto)";
              }

              $dpe['extras'] = json_encode($extras);
              $this->dpem->set($dpe);
              $this->order->trans_commit();

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

                  if($receiver->language_id == 2) {
                    $title = "Ulasan Penawaran";
                    $message =  "Pembeli telah meninggalkan ulasan di ".$review->c_produk_nama."(auto)";
                  } else {
                    $title = "Offer Review";
                    $message =  "Buyer's review for botol ".$review->c_produk_nama. " is done now(auto)";
                  }

                  $type = 'offer_review';
                  $image = 'media/pemberitahuan/outbounding.png';
                  $payload = new stdClass();
                  // $payload->chat_room_id = $review->id;
                  $payload->c_produk_id = $review->c_produk_id;
                  // $payload->c_produk_nama = $review->c_produk_nama;
                  // $payload->b_user_id_starter = $review->b_user_id_starter;
                  // $payload->b_user_id_seller = $review->b_user_id_seller;

                  $image = $this->cdn_url($image);
                  $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
                }

              }

            }

          }
          //end foreach
        }
        //end data count waiting for review
        //END by Donny Dennison - 12 july 2022 14:56

        //end transacation
        $this->order->trans_end();

        die();
    }

}
