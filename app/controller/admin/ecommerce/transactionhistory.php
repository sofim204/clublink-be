<?php
class Transactionhistory extends JI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setTheme('admin');
        $this->current_parent = 'ecommerce';
        $this->current_page = 'ecommerce_transactionhistory';
        $this->load("admin/a_negara_model", "anm");
        $this->load("admin/b_user_model", "bum");
        $this->load("admin/b_user_alamat_model", "buam");
        $this->load("admin/b_user_bankacc_model", "bubm");
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
    protected function __m($amount, $nation_code="")
    {
        $n = $this->anm->getByNationCode($nation_code);
        if (isset($n->simbol_mata_uang)) {
            return $n->simbol_mata_uang.' '.number_format($amount, 2, '.', ',');
        } else {
            return number_format($amount, 2, '.', ',');
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

    private function __forceDownload($pathFile)
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($pathFile));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($pathFile));
        ob_clean();
        flush();
        readfile($pathFile);
        exit;
    }

    private function __checkDir($periode)
    {
        if (!is_dir(SENEROOT.'media/')) {
            mkdir(SENEROOT.'media/', 0777);
        }
        if (!is_dir(SENEROOT.'media/laporan/')) {
            mkdir(SENEROOT.'media/laporan/', 0777);
        }
        $str = $periode.'/01';
        $periode_y = date("Y", strtotime($str));
        $periode_m = date("m", strtotime($str));
        if (!is_dir(SENEROOT.'media/laporan/'.$periode_y)) {
            mkdir(SENEROOT.'media/laporan/'.$periode_y, 0777);
        }
        if (!is_dir(SENEROOT.'media/laporan/'.$periode_y.'/'.$periode_m)) {
            mkdir(SENEROOT.'media/laporan/'.$periode_y.'/'.$periode_m, 0777);
        }
        return SENEROOT.'media/laporan/'.$periode_y.'/'.$periode_m;
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

        //By Donny Dennison - 27 juni 2020 3:23
        //request by Mr Jackie, remove alamat in pdf
      // if (strtolower($alamat) == strtolower($alamat2)) $alamat = '';
      // $alamat = mb_substr(trim(mb_ereg_replace('  ', ' ', mb_ereg_replace($kodepos, '', $alamat))),0,50,'UTF-8');
     
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
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }

        if (!$this->checkPermissionAdmin($this->current_page)) {
            redir(base_url_admin('forbidden'));
            die();
        }


        $this->setTitle('Transaction History '.$this->site_suffix_admin);

        $this->putThemeContent("ecommerce/transactionhistory/home_modal", $data);
        $this->putThemeContent("ecommerce/transactionhistory/home", $data);
        $this->putJsContent("ecommerce/transactionhistory/home_bottom", $data);

        $this->loadLayout('col-2-left', $data);
        $this->render();
    }

    public function detail($d_order_id, $c_produk_id)
    {
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
        $detail = $this->dodm->getDetailByOrderId($nation_code, $d_order_id, $c_produk_id);
        if (!isset($order->id)) {
            redir(base_url_admin('ecommerce/transaction/seller/'));
            die();
        }

        $items = $this->dodim->getByOrderDetailId($nation_code, $order->id, $detail->id);

        //update is_rejected_all
        $item_confirmed = 0;
        $icount = count($items);
        $dcount = 0;
        foreach ($items as $item) {
            if ($item->buyer_status != 'rejected') {
                $dcount++;
            }
            if ($item->buyer_status != 'wait') {
                $item_confirmed++;
            }
            if(isset($item->nama)){
                $item->nama = $this->__convertToEmoji($item->nama);
            }
        }
        $du = array();
        // $du['buyer_confirmed'] = 'unconfirmed';
        // if($item_confirmed==$icount){
        //  $du['buyer_confirmed'] = 'confirmed';
        // $detail->buyer_confirmed = $du['buyer_confirmed'];
        // }
        if ($icount>0 && empty($dcount) && empty($detail->is_rejected_all)) {
            $du['is_rejected_all'] = 1;
            $detail->is_rejected_all = "1";
        }

        if (count($du)>0) {
            $this->dodm->update($nation_code, $order->id, $detail->id, $du);
        }
        // $this->debug($detail);
        // die();

        //get seller rating
        $seller_rating = 0;
        $rating_object = $this->erm->getSellerStats($nation_code, $detail->b_user_id_seller);
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
        $order->detail = $detail;
        $order->pickup = $this->dodpum->getById($nation_code, $order->id, $detail->id);
        $order->billing = $this->doam->getBillingByOrderId($nation_code, $order->id);
        $order->shipping = $this->doam->getShippingByOrderId($nation_code, $order->id);
        $order->proses = $this->dopm->getDetailByID($nation_code, $order->id, $detail->id);

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
        $seller = $this->bum->getById($nation_code, $detail->b_user_id);
        $seller->address = $order->pickup;
        $seller->rating = $seller_rating;

        //get bank data
        $bank_seller = $this->bubm->getByUserId($nation_code, $detail->b_user_id);
        $bank_buyer = $this->bubm->getByUserId($nation_code, $order->b_user_id);

        // by Muhammad Sofi 7 January 2022 16:10 | restore button Open Chat
        // get chat room id

        // if(isset($seller->id)) {
        //     $id_seller = $seller->id;
        // } else {
        //     $id_seller = 0;
        //     // redir(base_url_admin('ecommerce/transactionhistory'));
        //     // echo "<script>
        //     //     alert('Seller not found');
        //     //     window.location.href='#';
        //     //     history.go(-1);
        //     //     </script>";
        //     // die();
        // }

        // if(isset($buyer->id)) {
        //     $id_buyer = $buyer->id;
        // } else {
        //     $id_buyer = 0;
        //     // redir(base_url_admin('ecommerce/transactionhistory'));
        //     // echo "<script>
        //     //     alert('Buyer not found');
        //     //     window.location.href='#';
        //     //     history.go(-1);
        //     //     </script>";
        //     // die();
        // }

        // $get_chat_data = $this->chat_participant_model->getRoomChatIDByParticipantId($nation_code, $seller->id, $buyer->id, 'buyandsell');
        // $get_chat_admin_seller = $this->chat_participant_model->getRoomChatAdminID($nation_code, $seller->id, 'admin');
        // $get_chat_admin_buyer = $this->chat_participant_model->getRoomChatAdminID($nation_code, $buyer->id, 'admin');
        // $get_chat_data = $this->chat_participant_model->getRoomChatIDByParticipantId($nation_code, $id_seller, $id_buyer, 'buyandsell');
        // $get_chat_admin_seller = $this->chat_participant_model->getRoomChatAdminID($nation_code, $id_seller, 'admin');
        // $get_chat_admin_buyer = $this->chat_participant_model->getRoomChatAdminID($nation_code, $id_buyer, 'admin');

        $get_chat_data = $this->chat_participant_model->getRoomChatIDByParticipantId($nation_code, $seller->id, $buyer->id, 'buyandsell');
        $get_chat_admin_seller = $this->chat_participant_model->getRoomChatAdminID($nation_code, $seller->id, 'admin');
        $get_chat_admin_buyer = $this->chat_participant_model->getRoomChatAdminID($nation_code, $buyer->id, 'admin');

        //put to view
        $data['negara'] = $this->anm->getByNationCode($nation_code);
        $data['nation_code'] = $nation_code;
        $data['order'] = $order;
        $data['items'] = $items;
        $data['buyer'] = $buyer;
        $data['seller'] = $seller;
        $data['bank_buyer'] = $bank_buyer;
        $data['bank_seller'] = $bank_seller;
        $data['rating'] = $this->erm->getByOrderDetailId($nation_code, $order->id, $c_produk_id);
        $data['order_status'] = $this->__getOrderStatus($order->order_status, $order->payment_status, $order->detail->seller_status, $order->detail->shipment_status, $order->detail->buyer_confirmed);
        $data['complain'] = $this->ecm->getDetailByID($nation_code, $order->id, $order->b_user_id, $order->detail->b_user_id_seller);
        $data['st'] = $this->__statusText($order, $detail);

        // by Muhammad Sofi 7 January 2022 16:10 | restore button Open Chat
        $data['room_chat_data'] = $get_chat_data;
        $data['room_chat_admin_seller'] = $get_chat_admin_seller;
        $data['room_chat_admin_buyer'] = $get_chat_admin_buyer;

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

        $this->setTitle('Transaction History Detail '.$this->site_suffix_admin);
        $this->putThemeContent("ecommerce/transactionhistory/detail_modal", $data);
        $this->putThemeContent("ecommerce/transactionhistory/detail", $data);
        $this->putJsContent("ecommerce/transactionhistory/detail_bottom", $data);

        $this->loadLayout('col-2-left', $data);
        $this->render();
    }
}
