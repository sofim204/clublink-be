<?php
class Transaction extends JI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setTheme('admin');
        $this->current_parent = 'ecommerce';
        $this->current_page = 'ecommerce_transactionseller';
        $this->load("admin/a_negara_model", "anm");
        $this->load("admin/b_user_model", "bum");
        $this->load("admin/b_user_alamat_model", "buam");
        $this->load("admin/b_user_bankacc_model", "bubm");
        $this->load("admin/common_code_model", "ccm");
        $this->load("admin/c_produk_model", "cpm");
        $this->load("admin/d_order_model", "dom");
        $this->load("admin/d_order_alamat_model", "doam");
        $this->load("admin/d_order_detail_model", "dodm");
        $this->load("admin/d_order_detail_pickup_model", "dodpum");
        $this->load("admin/d_order_detail_item_model", "dodim");
        $this->load("admin/d_order_proses_model", "dopm");
        $this->load("admin/e_complain_model", "ecm");
        $this->load("admin/e_rating_model", "erm");

        //by Donny Dennison - 29 january 2021 14:22
        //change chat to open chatting
        // $this->load("admin/e_chat_room_model","ecpm");

        // by Muhammad Sofi 7 January 2022 16:10 | restore button Open Chat
		$this->load("api_admin/chat/chat_participant","chat_participant_model");

    }
    protected function __calcFee($nation_code)
    {
        $fee = array();
        $presets = $this->ccm->getByClassified($nation_code, "product_fee");
        if (count($presets)>0) {
            foreach ($presets as $pre) {
                $fee[$pre->code] = $pre;
            }
            unset($pre); //free some memory
            unset($presets); //free some memory
            $admin_pg = 0.0;
            $admin_fee = 0.0;
        }
        return $fee;
    }
    protected function __toStars($rating)
    {
        $str = '';
        $rating = ceil($rating);
        for ($rti=1;$rti<=5;$rti++) {
            if ($rti<=$rating) {
                $str .= '<i class="fa fa-star"></i>';
            } else {
                $str .= '<i class="fa fa-star-o"></i>';
            }
        }
        return $str;
    }
    protected function __m($amount, $nation_code="")
    {
        $n = $this->anm->getByNationCode($nation_code);
        if (isset($n->simbol_mata_uang)) {
            return $n->simbol_mata_uang.'. '.number_format($amount, 2, ',', '.');
        } else {
            return number_format($amount, 2, ',', '.');
        }
    }
    protected function __n($amount, $unit="", $after="0")
    {
        if (!empty($after)) {
            return number_format($amount, 0, '.', ',').' '.$unit;
        } else {
            return $unit.' '.number_format($amount, 0, '.', ',');
        }
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
        return $status_text;
    }

    /**
     * Address structure fixer
     * @param  string $alamat        [description]
     * @param  string $alamat2       [description]
     * @param  string $address_notes [description]
     * @param  string $negara        [description]
     * @param  string $kodepos       [description]
     * @return string                [description]
     */

    // by Donny Dennison - 3 November 2021 10:00
	// remark code
    // protected function __addressStructureFixer(string $alamat,string $alamat2,string $address_notes,string $negara,string $kodepos){
    protected function __addressStructureFixer(string $alamat2,string $address_notes,string $negara,string $kodepos){

        //check if shipping address are same
        //By Donny Dennison - 27 juni 2020 3:23
        //request by Mr Jackie, remove alamat in pdf
        // if (strtolower($alamat) == strtolower($alamat2)) $alamat = '';

        // by Donny Dennison - 3 November 2021 10:00
        // remark code
        // $alamat = mb_substr(trim(mb_ereg_replace('  ', ' ', mb_ereg_replace($kodepos, '', mb_ereg_replace(', Singapore', '', $alamat)))),0,50,'UTF-8');

        //By Donny Dennison - 27 juni 2020 3:23
        //request by Mr Jackie, remove alamat in pdf
        // $alamat2 = mb_substr(trim(mb_ereg_replace('  ', ' ', mb_ereg_replace($kodepos, '', mb_ereg_replace(', Singapore', '', $alamat2)))),0,50,'UTF-8');
        $alamat2 = mb_substr(trim(mb_ereg_replace('  ', ' ', mb_ereg_replace($kodepos, '', $alamat2))),0,50,'UTF-8');

        //By Donny Dennison - 29 juni 2020 11:20
        //request by Mr Jackie, after remove alamat in pdf make another bug, if there is no address_notes the return array is not in array 0, the return array is dynamic
        // $address_notes = mb_substr(trim(mb_ereg_replace('  ', ' ', mb_ereg_replace($kodepos, '', $address_notes))),0,50,'UTF-8');
        $address_notes = mb_substr(trim(mb_ereg_replace('  ', ' ', $address_notes)),0,50,'UTF-8');

        $negara = mb_substr(trim(mb_ereg_replace('  ', ' ', mb_ereg_replace($kodepos, '', $negara))),0,50,'UTF-8');
      
        // by Donny Dennison - 3 November 2021 10:00
        // remark code
        // $ka = array($address_notes,$alamat,$alamat2,$negara.' '.$kodepos,'');
        $ka = array($address_notes,$alamat2,$negara.' '.$kodepos,'');

        $c = count($ka);
        for($i=0;$i<$c;$i++){
            if(mb_strlen($ka[$i])==0 && ($i+1)<$c){
              $ka[$i] = $ka[$i+1];
              $ka[$i+1] = '';
            }
        }
        return $ka;
    }

    private function __convertToEmoji($text){
		$value = $text;
		$readTextWithEmoji = preg_replace("/\\\\u([0-9A-F]{2,5})/i", "&#x$1;", $value);
		return $readTextWithEmoji;
	}

    public function index()
    {
        redir(base_url_admin());
    }
    public function buyer()
    {
        $this->current_parent = 'ecommerce';
        $this->current_page = 'ecommerce_transactionbuyer';
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }

        if (!$this->checkPermissionAdmin($this->current_page)) {
            redir(base_url_admin('forbidden'));
            die();
        }

        $nation_code = $data['sess']->admin->nation_code;

        $this->setTitle('Transaction By Buyer '.$this->site_suffix_admin);

        $this->putThemeContent("ecommerce/transaction/buyer/home_modal", $data);
        $this->putThemeContent("ecommerce/transaction/buyer/home", $data);
        $this->putJsContent("ecommerce/transaction/buyer/home_bottom", $data);
        $this->loadLayout('col-2-left', $data);
        $this->render();
    }
    public function seller($type="", $d_order_id="", $c_produk_id="")
    {
        if (strlen($type)>0 && strlen($d_order_id)>0 && strlen($c_produk_id)>0) {
            $this->seller_detail($d_order_id, $c_produk_id);
            die();
        }

        if (!$this->checkPermissionAdmin($this->current_page)) {
            redir(base_url_admin('forbidden'));
            die();
        }

        $this->current_parent = 'ecommerce';
        $this->current_page = 'ecommerce_transactionseller';

        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;

        $this->setTitle('Transaction By Seller '.$this->site_suffix_admin);
        $this->putThemeContent("ecommerce/transaction/seller/home_modal", $data);
        $this->putThemeContent("ecommerce/transaction/seller/home", $data);
        $this->putJsContent("ecommerce/transaction/seller/home_bottom", $data);
        $this->loadLayout('col-2-left', $data);
        $this->render();
    }
    public function buyer_detail($d_order_id="", $c_produk_id="")
    {
        $this->current_parent = 'ecommerce';
        $this->current_page = 'ecommerce_transactionbuyer';

        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }

        if (!$this->checkPermissionAdmin($this->current_page)) {
            redir(base_url_admin('forbidden'));
            die();
        }

        $nation_code = $data['sess']->admin->nation_code;

        //get order
        $order = $this->dom->getById($nation_code, $d_order_id);
        if (!isset($order->id)) {
            redir(base_url_admin('transaction/buyer/'));
            die();
        }
        $order->detail = $this->dodm->getByOrderId($nation_code, $d_order_id);

        //get buyer info
        $buyer = $this->bum->getById($nation_code, $order->b_user_id);

        $total_qty = 0;
        $item_total = 0;
        //get order detail
        $produks = array();
        $items = $this->dodim->getByOrderId($nation_code, $d_order_id);
        foreach ($items as $itm) {
            $key  = $itm->nation_code.'-';
            $key .= $itm->d_order_id.'-';
            $key .= $itm->d_order_detail_id;
            if (!isset($produks[$key])) {
                $produks[$key] = array();
            }
            $produks[$key][] = $itm;
            $total_qty += $itm->qty;
            $item_total++;

            if(isset($itm->nama)){
                $itm->nama = $this->__convertToEmoji($itm->nama);
            }
        }
        unset($itm);
        unset($items);

        //check if already commited by buyer

        //seller iteration
        $ongkir_total = 0;
        $sub_total = 0;
        $sellers = array();
        foreach ($order->detail as &$seller) {
            $key  = $seller->nation_code.'-';
            $key .= $seller->d_order_id.'-';
            $key .= $seller->id;
            $seller->items = array();
            if (isset($produks[$key])) {
                $seller->items = $produks[$key];
            }
            if (empty($seller->is_include_delivery_cost)) {
                $ongkir_total += ($seller->shipment_cost+$seller->shipment_cost_add);
            }
            $sub_total += $seller->sub_total;

            if ($seller->buyer_confirmed != "confirmed") {
                $seller_item_count = count($seller->items);
                $seller_item_count_confirmed = 0;
                foreach ($seller->items as $item) {
                    if ($item->buyer_status != "wait") {
                        $seller_item_count_confirmed++;
                    }
                }
                if ($seller_item_count_confirmed>=$seller_item_count) {
                    $du = array();
                    $du['buyer_confirmed'] = "confirmed";
                    $this->dodm->update($nation_code, $order->id, $seller->id, $du);
                }
            }
        }
        unset($produks);
        //$this->debug($order);
        //die();

        //fill to order object
        $order->item_total = $item_total;
        $order->sub_total = $sub_total;
        $order->ongkir_total = $ongkir_total;
        $order->grand_total = $sub_total+$ongkir_total;

        //update order info
        $du = array();
        $du['item_total'] = $order->item_total;
        $du['sub_total'] = $order->sub_total;
        $du['ongkir_total'] = $order->ongkir_total;
        $du['grand_total'] = $order->grand_total;
        $this->dom->update($order->nation_code, $order->id, $du);

        //get bank data
        $bank_buyer = $this->bubm->getByUserId($nation_code, $order->b_user_id);

        //put to view
        $data['negara'] = $this->anm->getByNationCode($nation_code);
        $data['nation_code'] = $nation_code;
        $data['buyer'] = $buyer;
        $data['order'] = $order;
        $data['bank_buyer'] = $bank_buyer;
        $data['probj'] = json_decode($order->payment_response);

        $this->setTitle('Transaction By Buyer Detail '.$this->site_suffix_admin);
        $this->putThemeContent("ecommerce/transaction/buyer/detail_modal", $data);
        $this->putThemeContent("ecommerce/transaction/buyer/detail", $data);
        $this->putJsContent("ecommerce/transaction/buyer/detail_bottom", $data);
        $this->loadLayout('col-2-left', $data);
        $this->render();
    }

    public function seller_detail($d_order_id="", $c_produk_id="")
    {
        $this->current_parent = 'ecommerce';
        $this->current_page = 'ecommerce_transactionseller';

        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }

        if (!$this->checkPermissionAdmin($this->current_page)) {
            redir(base_url_admin('forbidden'));
            die();
        }

        $nation_code = $data['sess']->admin->nation_code;

        //validation
        $d_order_id = (int) $d_order_id;
        if ($d_order_id<=0) {
            $d_order_id=0;
        }
        $c_produk_id = (int) $c_produk_id;
        if ($c_produk_id<=0) {
            $c_produk_id=0;
        }

        //get order
        $order = $this->dom->getById($nation_code, $d_order_id);
        if (!isset($order->id)) {
            redir(base_url_admin('ecommerce/transaction/seller/'));
            die();
        }

        //get order detail
        $order->detail = $this->dodm->getDetailByOrderId($nation_code, $d_order_id, $c_produk_id);
        if (!isset($order->detail->id)) {
            redir(base_url_admin('ecommerce/transaction/seller/'));
            die();
        }
        $fee = $this->__calcFee($order->nation_code);
        $vat = 0.0;
        $vat_factor = 7;
        //$this->debug($order->detail->sub_total);
        //die();
        if ($order->payment_status == "paid" && $order->detail->pg_vat==0.00) {
            if (isset($fee['F6']->remark)) {
                $vat_factor = (int) $fee['F6']->remark;
            }
            $vat = $order->detail->pg_fee*($vat_factor/100);
            $du = array();
            $du['pg_vat'] = $vat;
            $du['profit_amount'] = $order->detail->profit_amount - $vat;
            $this->dodm->update($nation_code, $order->id, $order->detail->id, $du);
            $order->detail->pg_vat = $vat;
        }

        //get order detail item
        $items = $this->dodim->getByOrderDetailId($nation_code, $d_order_id, $c_produk_id);
        if (!isset($items[0]->id)) {
            redir(base_url_admin('ecommerce/transaction/seller/'));
            die();
        }

        //update is_rejected_all
        $icount = count($items);
        $dcount = 0;
        foreach ($items as $item) {
            if ($item->buyer_status != 'rejected') {
                $dcount++;
            }

            if(isset($item->nama)){
                $item->nama = $this->__convertToEmoji($item->nama);
            }
        }
        if ($icount>0 && empty($dcount) && empty($order->detail->is_rejected_all)) {
            $du = array();
            $du['is_rejected_all'] = 1;
            $this->dodm->update($nation_code, $order->id, $order->detail->id, $du);
            $order->detail->is_rejected_all = "1";
        }

        //get seller rating
        $seller_rating = 0;
        $rating_object = $this->erm->getSellerStats($nation_code, $order->detail->b_user_id_seller);
        if (isset($rating_object->rating_rate)) {
            $seller_rating = $rating_object->rating_rate;
        }
        unset($rating_object);

        //get buyer rating
        $buyer_rating = 0;
        $rating_object = $this->erm->getBuyerStats($nation_code, $order->b_user_id);
        if (isset($rating_object->rating_rate)) {
            $buyer_rating = $rating_object->rating_rate;
        }
        unset($rating_object);

        //put on order
        $order->pickup = $this->dodpum->getById($nation_code, $order->id, $order->detail->id);
        $order->billing = $this->doam->getBillingByOrderId($nation_code, $order->id);
        $order->shipping = $this->doam->getShippingByOrderId($nation_code, $order->id);
        $order->proses = $this->dopm->getDetailByID($nation_code, $order->id, $order->detail->id);

        //validate address
        if (!isset($order->pickup->penerima_nama)) {
            //get from b_user_alamat
            $order->pickup = $this->buam->getById($nation_code, $order->detail->b_user_id, $order->detail->b_user_alamat_id);
            if (!isset($order->pickup->penerima_nama)) {
                die('cannot get pickup address');
            }
            $order->pickup->address_notes = $order->pickup->catatan;
        }

        //get buyer data
        $buyer = $this->bum->getById($nation_code, $order->b_user_id);
        $buyer->rating = $buyer_rating;

        //get seller data
        $seller = $this->bum->getById($nation_code, $order->detail->b_user_id_seller);
        $seller->address = $order->pickup;
        $seller->rating = $seller_rating;

        //get bank data
        $bank_seller = $this->bubm->getByUserId($nation_code, $order->detail->b_user_id_seller);
        $bank_buyer = $this->bubm->getByUserId($nation_code, $order->b_user_id);

        // by Muhammad Sofi 7 January 2022 16:10 | restore button Open Chat
        // get chat room id
        $get_chat_data = $this->chat_participant_model->getRoomChatIDByParticipantId($nation_code, $seller->id, $buyer->id, 'buyandsell');
        $get_chat_admin_seller = $this->chat_participant_model->getRoomChatAdminID($nation_code, $seller->id, 'admin');
        $get_chat_admin_buyer = $this->chat_participant_model->getRoomChatAdminID($nation_code, $buyer->id, 'admin');

        //put to view
        $data['smu'] = ''; //currency symbol / simbol mata uang
        $data['negara'] = $this->anm->getByNationCode($nation_code);
        if (isset($data['negara']->simbol_mata_uang)) {
            $data['smu'] = $data['negara']->simbol_mata_uang;
        }
        $data['nation_code'] = $nation_code;
        $data['order'] = $order;
        $data['items'] = $items;
        $data['buyer'] = $buyer;
        $data['seller'] = $seller;
        $data['bank_buyer'] = $bank_buyer;
        $data['bank_seller'] = $bank_seller;
        $data['rating'] = $this->erm->getByOrderDetailId($nation_code, $order->id, $c_produk_id);
        $data['order_status'] = $this->__getOrderStatus($order->order_status, $order->payment_status, $order->detail->seller_status, $order->detail->shipment_status, $order->detail->buyer_confirmed);
        $data['complain'] = $this->ecm->getDetailByID($nation_code, $order->id, $order->b_user_id, $order->detail->b_user_id);
        $data['st'] = $this->__statusText($order, $order->detail);

        // by Muhammad Sofi 7 January 2022 16:10 | restore button Open Chat
        $data['room_chat_data'] = $get_chat_data;
        $data['room_chat_admin_seller'] = $get_chat_admin_seller;
        $data['room_chat_admin_buyer'] = $get_chat_admin_buyer;

        //$this->debug($data['rating']);
        //die();

        //by Donny Dennison - 29 january 2021 14:22
        //change chat to open chatting
        //START by Donny Dennison - 29 january 2021 14:22

        // START by Muhammad Sofi 3 February 2022 13:20 | table e_chat_v2 is not used
        
        // $chatRoomAllDetail = $this->ecpm->checkRoomChat($nation_code, $seller->id, $buyer->id, 'ALL');

        // if(!isset($chatRoomAllDetail->id)){
        //     $data['chatRoomAllID'] = 0;
        // }else{
        //     $data['chatRoomAllID'] = $chatRoomAllDetail->id;
        // }

        // //get chat room customer 1 detail
        // $chatRoomCustomer1Detail = $this->ecpm->checkRoomChat($nation_code, 0, $seller->id, 'ADMIN');

        // if(!isset($chatRoomCustomer1Detail->id)){
        //     $data['chatRoomSellerID'] = 0;
        // }else{
        //     $data['chatRoomSellerID'] = $chatRoomCustomer1Detail->id;
        // }

        // //get chat room customer 2 detail
        // $chatRoomCustomer2Detail = $this->ecpm->checkRoomChat($nation_code, 0, $buyer->id, 'ADMIN');

        // if(!isset($chatRoomCustomer2Detail->id)){
        //     $data['chatRoomBuyerID'] = 0;
        // }else{
        //     $data['chatRoomBuyerID'] = $chatRoomCustomer2Detail->id;
        // }

        // END by Muhammad Sofi 3 February 2022 13:20 | table e_chat_v2 is not used
        
        //END by Donny Dennison - 29 january 2021 14:22

        $this->setTitle('Transaction By Seller Detail '.$this->site_suffix_admin);
        $this->putThemeContent("ecommerce/transaction/seller/detail_modal", $data);
        $this->putThemeContent("ecommerce/transaction/seller/detail", $data);
        $this->putJsContent("ecommerce/transaction/seller/detail_bottom", $data);
        $this->loadLayout('col-2-left', $data);
        $this->render();
    }
}
