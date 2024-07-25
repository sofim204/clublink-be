<?php

require_once (SENEROOT.'app/controller/api_mobile/seller/waybill.php');

class Order extends JI_Controller
{
    public $email_send = 1;
    public $is_log = 1;

    public function __construct()
    {
        parent::__construct();
        //$this->setTheme('frontx');
        $this->lib("seme_log");
        $this->lib('seme_email');
        $this->load("api_mobile/a_pengguna_model", "apm");
        $this->load("api_mobile/a_notification_model", "anot");
        $this->load("api_mobile/b_kategori_model3", "bkm3");
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
        $this->load("api_mobile/d_order_proses_model", "dopm");
        $this->load("api_mobile/d_pemberitahuan_model", "dpem");
        $this->load("api_mobile/d_order_detail_item_model", "dodim");
        $this->load("api_mobile/d_order_detail_pickup_model", "dodpum");
        $this->load("api_mobile/e_rating_model", "erm");
    }


    //by Donny Dennison - 29 april 2021 14:06
    //add-void-and-refund-2c2p-after-reject-by-seller
    private function __call2c2pApi($invoiceno, $processType)
    {
      /* 
      Process Type:
          I = transaction inquiry
          V = transaction void
          R = transaction Refund
          S = transaction Settlement 
      */
      $version = "3.4";
      
      //Construct signature string
      $stringToHash = $version . $this->merchantID_2c2p . $processType . $invoiceno . '0.01' ; 
      $hash = strtoupper(hash_hmac('sha1', $stringToHash ,$this->secretKey_2c2p, false)); //Compute hash value

      //Construct request message
      $xml = "<PaymentProcessRequest>
              <version>$version</version>
              <merchantID>$this->merchantID_2c2p</merchantID>
              <processType>$processType</processType>
              <invoiceNo>$invoiceno</invoiceNo>
              <actionAmount>0.01</actionAmount>
              <hashValue>$hash</hashValue>
              </PaymentProcessRequest>";
      
      if($this->env_2c2p == 'staging'){
        //staging
        $payload = $this->encrypt_2c2p($xml,SENEROOT."key/demo2.crt"); //Encrypt payload   
      }else{
        //production
        $payload = $this->encrypt_2c2p($xml,SENEROOT."key/prod_2c2p_public.cer"); //Encrypt payload        
      }
      
      //Send request to 2C2P PGW and get back response
      //open connection
      $ch = curl_init();
      curl_setopt($ch,CURLOPT_URL, $this->payment_action_api_host_2c2p);
      curl_setopt($ch,CURLOPT_POSTFIELDS, "paymentRequest=".$payload);
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
      //execute post
      $response = curl_exec($ch); //close connection
      curl_close($ch);

      //Decrypt response message and display  
      $response = $this->decrypt_2c2p($response,SENEROOT."key/cert.crt",SENEROOT."key/private.pem","pwFVhwEf73p6");   
   
      //Validate response Hash
      $resXml=simplexml_load_string($response); 
      $res_version = $resXml->version;
      $res_respCode = $resXml->respCode;
      $res_processType = $resXml->processType;
      $res_invoiceNo = $resXml->invoiceNo;
      $res_amount = $resXml->amount;
      $res_status = $resXml->status;
      $res_approvalCode = $resXml->approvalCode;
      $res_referenceNo = $resXml->referenceNo;
      $res_transactionDateTime = $resXml->transactionDateTime;
      $res_paidAgent = $resXml->paidAgent;
      $res_paidChannel = $resXml->paidChannel;
      $res_maskedPan = $resXml->maskedPan;
      $res_eci = $resXml->eci;
      $res_paymentScheme = $resXml->paymentScheme;
      $res_processBy = $resXml->processBy;
      $res_refundReferenceNo = $resXml->refundReferenceNo;
      $res_userDefined1 = $resXml->userDefined1;
      $res_userDefined2 = $resXml->userDefined2;
      $res_userDefined3 = $resXml->userDefined3;
      $res_userDefined4 = $resXml->userDefined4;
      $res_userDefined5 = $resXml->userDefined5;
      
      //Compute response hash
      $res_stringToHash = $res_version.$res_respCode.$res_processType.$res_invoiceNo.$res_amount.$res_status.$res_approvalCode.$res_referenceNo.$res_transactionDateTime.$res_paidAgent.$res_paidChannel.$res_maskedPan.$res_eci.$res_paymentScheme.$res_processBy.$res_refundReferenceNo.$res_userDefined1.$res_userDefined2.$res_userDefined3.$res_userDefined4.$res_userDefined5 ; 
      $res_responseHash = strtoupper(hash_hmac('sha1',$res_stringToHash,$this->secretKey_2c2p, false)); //Calculate response Hash Value 

      if(strtolower($resXml->hashValue) == strtolower($res_responseHash)){ 
        return $resXml; 
      } else{
        $return = new stdClass();
        $return->respCode = 99;
        return $return; 
      }

    }

    private function __feeCalculation($nation_code)
    {
        //declare initial variable
        $pg_fee = 0.0; //payment gateway deduction value
        $pg_fee_jenis = 'percentage';
        $pg_fee_vat = 0;
        $profit = 0.0; //profit
        $profit_jenis = 'percentage';
        $asuransi = 0.0; //insurance
        $asuransi_jenis = 'percentage';
        $selling_fee_percent = 0; //selling fee

        //get preset from DB
        $fee = array();
        $presets = $this->ccm->getByClassified($nation_code, "product_fee");
        if (count($presets)>0) {
            foreach ($presets as $pre) {
                $fee[$pre->code] = $pre;
            }
            unset($pre); //free some memory
            unset($presets); //free some memory
        }

        //passing into current var
        if (isset($fee['F7']->remark)) {
            $selling_fee_percent = $fee['F7']->remark;
        }
        if (isset($fee['F0']->remark)) {
            $pg_fee = ($fee['F0']->remark);
        }
        if (isset($fee['F1']->remark)) {
            $pg_fee_jenis = $fee['F1']->remark;
        }
        if (isset($fee['F6']->remark)) {
            $pg_fee_vat = $fee['F6']->remark;
        }
        if (isset($fee['F2']->remark)) {
            $profit = ($fee['F2']->remark);
        }
        if (isset($fee['F3']->remark)) {
            $profit_jenis = $fee['F3']->remark;
        }
        if (isset($fee['F4']->remark)) {
            $asuransi = ($fee['F4']->remark);
        }
        if (isset($fee['F5']->remark)) {
            $asuransi_jenis = $fee['F5']->remark;
        }

        $fee = new stdClass();
        $fee->pg_fee = $pg_fee;
        $fee->pg_fee_jenis = $pg_fee_jenis;
        $fee->profit = $profit;
        $fee->profit_jenis = $profit_jenis;
        $fee->asuransi = $asuransi;
        $fee->asuransi_jenis = $asuransi_jenis;
        $fee->pg_fee_vat = $pg_fee_vat;
        $fee->selling_fee_percent = $selling_fee_percent;
        return $fee;
    }

    private function __getRatings($nation_code, $d_order_id, $c_produk_id, $b_user_id)
    {
        $rs = $this->__ratingObj(0, 0);
        $rate = $this->erm->getByOrderIdSellerId($nation_code, $d_order_id, $c_produk_id, $b_user_id);
        if (isset($rate->nation_code)) {
            $rs = $this->__ratingObj($rate->seller_rating, $rate->buyer_rating);
        }
        unset($rate);
        return $rs;
    }
    private function __ratingObj($seller_rating=0, $buyer_rating=0)
    {
        $r = new stdClass();
        $r->seller = new stdClass();
        $r->seller->rating_value = (int) $seller_rating;
        $r->buyer = new stdClass();
        $r->buyer->rating_value = (int) $buyer_rating;
        return $r;
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

    // private function __orderSellers($nation_code, $pelanggan, $order)
    // {
    //     //get rating
    //     $ratings = array();
    //     $r = $this->erm->getByOrderId($nation_code, $order->id);
    //     if (isset($r->b_user_id_seller)) {
    //         $ratings[$r->b_user_id_seller] = $r;
    //     }
    //     unset($r);

    //     //get sellers
    //     $sps = $this->dodm->getProdukAlamatByOrderId($nation_code, $order->id);
    //     $sellers = array();
    //     foreach ($sps as $product) {
    //         $pid = (int) $product->id;
    //         //url manipulator
    //         $product->foto = $this->cdn_url($product->foto);
    //         $product->thumb = $this->cdn_url($product->thumb);
            
    //         // by Muhammad Sofi - 28 October 2021 11:00
    //         // if user img & banner not exist or empty, change to default image
    //         // $product->b_user_image_seller = $this->cdn_url($product->b_user_image_seller);
    //         if(file_exists(SENEROOT.$product->b_user_image_seller) && $product->b_user_image_seller != 'media/user/default.png'){
    //             $product->b_user_image_seller = $this->cdn_url($product->b_user_image_seller);
    //         } else {
    //             $product->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
    //         }
    //         $product->shipment_icon = $this->cdn_url("assets/images/unavailable.png");

    //         //by Donny Dennison - 15 september 2020 17:45
    //         //change name, image, etc from gogovan to gogox
    //         // if (strtolower($product->shipment_service) == 'gogovan') {
    //         //     $product->shipment_icon = $this->cdn_url("assets/images/gogovan.png");
    //         if (strtolower($product->shipment_service) == 'gogox') {
    //             $product->shipment_icon = $this->cdn_url("assets/images/gogox.png");

    //         } elseif (strtolower($product->shipment_service) == 'qxpress') {
    //             $product->shipment_icon = $this->cdn_url("assets/images/qxpress.png");
            
    //         //by Donny Dennison - 23 september 2020 15:42
    //         //add direct delivery feature
    //         // }
    //         } elseif (strtolower($product->shipment_service) == 'direct delivery') {
    //             $product->shipment_icon = $this->cdn_url("assets/images/direct_delivery.png");
    //         }

    //         //building seller data
    //         $seller = new stdClass();
    //         $seller->nation_code = $order->nation_code;
    //         $seller->d_order_id = $order->id;
    //         $seller->b_user_id = (int) $product->b_user_id_seller;
            
    //         // by Muhammad Sofi - 28 October 2021 11:00
    //         // if user img & banner not exist or empty, change to default image
    //         // $seller->image = $product->b_user_image_seller;
    //         if(file_exists(SENEROOT.$product->b_user_image_seller) && $product->b_user_image_seller != 'media/user/default.png'){
    //             $seller->image = $product->b_user_image_seller;
    //         } else {
    //             $seller->image = $this->cdn_url('media/user/default-profile-picture.png');
    //         }
    //         $seller->nama = $product->b_user_nama_seller;

    //         //add rating to seller
    //         $seller->rating = new stdClass();
    //         $seller->rating->seller = new stdClass();
    //         $seller->rating->seller->rating_value = 0;
    //         $seller->rating->buyer = new stdClass();
    //         $seller->rating->buyer->rating_value = 0;
    //         if (isset($ratings[$seller->b_user_id]->seller_rating)) {
    //             $seller->rating->seller->rating_value = (int) $ratings[$seller->b_user_id]->seller_rating;
    //         }
    //         if (isset($ratings[$seller->b_user_id]->buyer_rating)) {
    //             $seller->rating->buyer->rating_value = (int) $ratings[$seller->b_user_id]->buyer_rating;
    //         }

    //         //add products to seller
    //         $seller->products = array();
    //         $seller->products[] = $product;
    //         if (!isset($sellers[$seller->b_user_id])) {
    //             $sellers[$seller->b_user_id] = $seller;
    //         } else {
    //             $sellers[$seller->b_user_id]->products[] = $product;
    //         }
    //     }
    //     $sellers = array_values($sellers);
    //     return $sellers;
    // }

    private function __sortCol($sort_col, $tbl_as, $tbl2_as)
    {
        switch ($sort_col) {
            case 'id':
            $sort_col = "$tbl_as.id";
            break;
            case 'kondisi':
            $sort_col = "$tbl_as.b_kondisi_id";
            break;
            case 'harga':
            $sort_col = "$tbl_as.harga_jual";
            break;
            case 'harga_jual':
            $sort_col = "$tbl_as.harga_jual";
            break;
            case 'nama':
            $sort_col = "$tbl_as.nama";
            break;
            default:
            $sort_col = "$tbl_as.nama";
        }
        return $sort_col;
    }
    private function __sortDir($sort_dir)
    {
        $sort_dir = strtolower($sort_dir);
        if ($sort_dir == "desc") {
            $sort_dir = "DESC";
        } else {
            $sort_dir = "ASC";
        }
        return $sort_dir;
    }
    private function __page($page)
    {
        if (!is_int($page)) {
            $page = (int) $page;
        }
        if (empty($page)) {
            $page = 1;
        }
        return $page;
    }
    private function __pageSize($page_size)
    {
        $page_size = (int) $page_size;
        if ($page_size<=0) {
            $page_size = 100;
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
        return $status_text;
    }

    private function __earningCalc($order, $items, $d_order_detail_id)
    {
        //settlement status check
        $product_items = array(); //for view purpose
        $nation_code = $order->nation_code;
        $d_order_id = $order->id;
        $i=0;
        $item_total = 0;
        $item_count = count($items);
        $accept_count = 0;
        $reject_count = 0;
        foreach ($items as $im) {
            if ($im->shipment_status == "delivered" || $im->shipment_status == "succeed") {
                if (empty($order->is_rejected_all)) {
                    if ($im->buyer_status == "accepted") {
                        $product_items[] = $im;
                        $item_total++;
                        $accept_count++;
                    } elseif ($im->buyer_status == "wait") {
                        $product_items[] = $im;
                        $item_total++;
                    } else {
                        $reject_count++;
                    }
                } else {
                    $product_items[] = $im;
                    $item_total++;
                }
            } else {
                $product_items[] = $im;
                $item_total++;
            }
        }
        $order->buyer_status = "wait";
        if ($reject_count == $item_count) {
            $order->buyer_status = "rejected";
            $order->buyer_confirmed = "confirmed";
        } elseif ($reject_count == $item_count || $item_total==$item_count) {
            $order->buyer_status = "accepted";
            $order->buyer_confirmed = "confirmed";
        }
        $order->total_item = strval($item_total);
        if ($order->settlement_status != "completed") {
        }//end settlement status check
        $this->product_items = $product_items;
        return $order;
    }

    public function index()
    {
        
        //by Donny Dennison - 10 july 2020 10:31
        //move send api delivery to controller/api_mobile/order/delivery_process
        // $this->status = '404';
        // header("HTTP/1.0 404 Not Found");
        // $data = array();
        // $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
        
    }

    /**
     * View detailed information about order by seller
     * @param  integer $d_order_id        ID from d_order table
     * @param  integer $d_order_detail_id ID from d_order_detail table
     * @return [type]                    [description]
     */
    public function detail($d_order_id="", $d_order_detail_id="")
    {
        //initial
        $dt = $this->__init();
        $data = array();
        $data['order'] = new stdClass();
        $data['order']->sellers = array();
        $data['order']->history = array();
        $data['order']->addresses = new stdClass();
        $data['order']->addresses->billing = new stdClass();
        $data['order']->addresses->shipping = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }
        $d_order_id = (int) $d_order_id;
        $d_order_detail_id = (int) $d_order_detail_id;
        if ($d_order_id<=0 && $d_order_detail_id<=0) {
            $d_order_id = (int) $this->input->post("d_order_id");
            if ($d_order_id<=0) {
                $this->status = 4010;
                $this->message = 'Invalid Order ID';
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", 'API_Mobile/seller/Order::detail -- INFO DOID: '.$d_order_id.' DODID: '.$d_order_detail_id);
                    $this->seme_log->write("api_mobile", 'API_Mobile/seller/Order::detail -- forceClose '.$this->status.' '.$this->message);
                }
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
                die();
            }

            $d_order_detail_id = (int) $this->input->post("c_produk_id");
            if ($d_order_detail_id<=0) {
                $this->status = 4012;
                $this->message = 'Invalid Produk ID';
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", 'API_Mobile/seller/Order::detail -- INFO DOID: '.$d_order_id.' DODID: '.$d_order_detail_id);
                    $this->seme_log->write("api_mobile", 'API_Mobile/seller/Order::detail -- forceClose '.$this->status.' '.$this->message);
                }
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
                die();
            }
        }

        //by Donny Dennison - 18 november 2021 16:37
        //set timezone in api buyer->order and seller->order
        $timezone = $this->input->get("timezone");
        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        $order = $this->dodm->getOrderBySeller($nation_code, $d_order_id, $d_order_detail_id, $pelanggan->id);
        //$this->debug($order);
        //die();
        if (!isset($order->d_order_id)) {
            $this->status = 4011;
            $this->message = 'Order with supplied ID(s) not found';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        $i=0;
        $item_total = 0;
        $total_amount = 0.0;
        $product_items = array();
        $sub_total = 0.0;
        $items = $this->dodim->getOrderByDetailId($nation_code, $d_order_id, $d_order_detail_id);
        $item_count = count($items);
        $accepted_count = 0;
        $rejected_count = 0;
        $confirmed_count = 0;
        //$this->debug($items);
        //die();
        foreach ($items as $im) {
            //sanitize utf-8
            // if (isset($im->nama)) {
            //     $im->nama = $this->__dconv($im->nama);
            // }
            $im->nama = html_entity_decode($im->nama,ENT_QUOTES);
            
            // if (isset($im->c_produk_nama)) {
            //     $im->c_produk_nama = $this->__dconv($im->c_produk_nama);
            // }
            if (isset($im->deskripsi)) {
                $im->deskripsi = $this->__dconv($im->deskripsi);
            }

            $st = ($im->qty * $im->harga_jual);
            $total_amount += $st;
            if ($im->shipment_status == "delivered" || $im->shipment_status == "succeed") {
                if (empty($order->is_rejected_all)) {
                    if ($im->buyer_status == "accepted") {
                        $sub_total += $st;
                        if ($this->is_log) {
                            $this->seme_log->write("api_mobile", "API_Mobile/seller/Order::detail -> Earning Total: $sub_total");
                        }
                        $item_total++;
                        $im->foto = ($im->foto);
                        $im->thumb = ($im->thumb);
                        $product_items[] = $im;
                        $accepted_count++;
                        $confirmed_count++;
                    } elseif ($im->buyer_status == "wait") {
                        $sub_total += $st;
                        $im->buyer_confirmed = "unconfirmed";
                        $product_items[] = $im;
                        $item_total++;
                        $im->foto = ($im->foto);
                        $im->thumb = ($im->thumb);
                    } else {
                        $rejected_count++;
                        $confirmed_count++;
                        if ($im->settlement_status == "paid_to_buyer") {
                        } else {
                            $sub_total += $st;
                            $im->buyer_confirmed = "unconfirmed";
                            $product_items[] = $im;
                            $item_total++;
                            $im->foto = ($im->foto);
                            $im->thumb = ($im->thumb);
                        }
                    }
                } else {
                    $sub_total += $st;
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", "API_Mobile/seller/Order::detail -> Earning Total: $sub_total");
                    }
                    $item_total++;
                    $im->foto = ($im->foto);
                    $im->thumb = ($im->thumb);
                    $product_items[] = $im;
                }
            } else {
                $sub_total += $st;
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/seller/Order::detail -> Earning Total: $sub_total");
                }
                $item_total++;
                $im->foto = ($im->foto);
                $im->thumb = ($im->thumb);
                $product_items[] = $im;
            }
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/seller/Order::detail -> Total Amount: $total_amount");
            }
            $i++;
        }
        $order->total_item = "".$item_total;

        //get calcuation fee
        $f = $this->__feeCalculation($nation_code);
        if (isset($f->admin_pg_jenis) && isset($f->admin_pg) && isset($f->admin_fee) && isset($f->admin_fee_jenis)) {
            $grand_total = $sub_total + $order->shipment_cost + $order->shipment_cost_add;
            $vat = 7;
            $pg_fee = 0.0;
            $pg_vat = 0.0;
            $pg_fee_percent = 0.0;
            if ($f->admin_pg_jenis == 'percentage') {
                $pg_fee_percent = $f->admin_pg;
                $pg_fee = ($grand_total * ($f->admin_pg/100));
            } else {
                $pg_fee = ($f->admin_pg);
            }
            if (isset($f->admin_vat)) {
                $vat = (int) $f->admin_vat;
            }
            $pg_vat = $pg_fee * ($vat/100);

            $profit_amount = 0.0;
            $profit_percent = 0.0;
            if ($f->admin_fee_jenis == 'percentage') {
                $profit_percent = $f->admin_fee;
                $profit_amount = ($grand_total * ($f->admin_fee/100));
            } else {
                $profit_amount = ($f->admin_fee);
            }
            $profit_amount = $profit_amount - $pg_vat;

            //declare var
            $cancel_fee = 0;
            $refund_amount = 0;
            $selling_fee_percent = $pg_fee_percent+$profit_percent;
            $earning_percent = 100 - $selling_fee_percent;
            $shipment_service = strtolower($order->shipment_service);

            //earning calcuation
            $selling_fee = $sub_total*($selling_fee_percent/100);
            $earning_total = $sub_total*($earning_percent/100);
            
            //by Donny Dennison - 15 september 2020 17:45
            //change name, image, etc from gogovan to gogox
            // if ($shipment_service == "gogovan") {
            if ($shipment_service == "gogox") {

                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/seller/Order::detail -> SETTAMOUNT: ".$earning_total);
                }
                $earning_total = $earning_total + (($order->shipment_cost + $order->shipment_cost_add) - $order->shipment_cost_sub);
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/seller/Order::detail -> SETTAMOUNT + GOGOVAN: ".$earning_total);
                }
            }
            if ($earning_total<0) {
                $cancel_fee += abs($earning_total);
                $earning_total = 0;
            }
            $ci = count($items);
            $order->buyer_status = "wait";
            if ($order->buyer_confirmed = "unconfirmed" && $ci == $confirmed_count) {
                $order->buyer_confirmed = "confirmed";
                $du = array();
                $du['buyer_confirmed'] = "confirmed";
                $this->dodm->update($nation_code, $d_order_id, $d_order_detail_id, $du);
            } elseif ($order->buyer_confirmed = "confirmed" && $ci != $confirmed_count) {
                $order->buyer_confirmed = "unconfirmed";
                $du = array();
                $du['buyer_confirmed'] = "unconfirmed";
                $this->dodm->update($nation_code, $d_order_id, $d_order_detail_id, $du);
            }
            if ($ci == $accepted_count) {
                $order->buyer_status = "accepted";
            } elseif ($ci == $rejected_count) {
                $order->buyer_status = "rejected";
                $order->is_rejected_all = "1";
                $du = array();
                $du['is_rejected_all'] = 1;
                $this->dodm->update($nation_code, $d_order_id, $d_order_detail_id, $du);
            }

            //if rejected by seller
            if ($order->seller_status == "rejected") {
                $cancel_fee = $pg_fee + $pg_vat;
                $profit_amount = 0;
                $earning_total = 0;
                $selling_fee = 0;
                $refund_amount = $grand_total;
            }

            //settlement status check
            if ($order->settlement_status != "completed") {
                //profit earning calcuation
                if (empty($order->is_calculated) && (($order->profit_amount != $profit_amount) || ($order->cancel_fee != $cancel_fee) || ($order->earning_total != $earning_total))) {
                    $du = array();
                    $du['buyer_confirmed'] = $order->buyer_confirmed;
                    if (!empty($order->is_rejected_all)) {
                        $du['is_rejected_all'] = 1;
                    }
                    $this->dodm->update($nation_code, $d_order_id, $d_order_detail_id, $du);

                    //$this->order->trans_commit();//get buyer configuration
                    $order->pg_fee = strval($pg_fee);
                    $order->pg_vat = strval($pg_vat);
                    $order->profit_amount = strval($profit_amount);
                    $order->cancel_fee = strval($cancel_fee);
                    $order->selling_fee = strval($selling_fee);
                    $order->selling_fee_percent = strval($selling_fee_percent);
                    $order->earning_total = strval($earning_total);
                    $order->earning_percent = strval($earning_percent);
                    $order->refund_amount = strval($refund_amount);
                } //end profit earning calcuation
            } //end settlement status check
        } //end get calcuation fee

        //set buyer_status if is_rejected_all=1
        if (!empty($order->is_rejected_all)) {
            $order->buyer_status = 'rejected';
        }
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/seller/Order::detail -> ORDERID: ".$d_order_id.", PRODUKID: ".$d_order_detail_id);
        }

        if (isset($order->foto)) {
            $order->foto = $this->cdn_url($order->foto);
        }
        if (isset($order->thumb)) {
            $order->thumb = $this->cdn_url($order->thumb);
        }
        $order->shipment_icon = $this->cdn_url("assets/images/unavailable.png");

        //by Donny Dennison - 15 september 2020 17:45
        //change name, image, etc from gogovan to gogox
        // if (strtolower($order->shipment_service) == 'gogovan') {
        //     $order->shipment_icon = $this->cdn_url("assets/images/gogovan.png");
        if (strtolower($order->shipment_service) == 'gogox') {
            $order->shipment_icon = $this->cdn_url("assets/images/gogox.png");

        } elseif (strtolower($order->shipment_service) == 'qxpress') {
            $order->shipment_icon = $this->cdn_url("assets/images/qxpress.png");
        
        //by Donny Dennison - 23 september 2020 15:42
        //add direct delivery feature
        // }
        } elseif (strtolower($order->shipment_service) == 'direct delivery') {
            $order->shipment_icon = $this->cdn_url("assets/images/direct_delivery.png");
        }

        //date current
        $order->date_current = date("Y-m-d H:i:s");

        //by Donny Dennison - 18 november 2021 16:37
        //set timezone in api buyer->order and seller->order
        $order->date_begin = $this->customTimezone($order->date_begin, $timezone);
        $order->date_expire = $this->customTimezone($order->date_expire, $timezone);

        //calculation
        $order->shipment_cost = $order->shipment_cost + $order->shipment_cost_add + $order->shipment_cost_sub;
        $order->grand_total = $order->sub_total + $order->shipment_cost;

        //get buyer detail
        $buyer = $this->bu->getById($nation_code, $order->b_user_id_buyer);

        //$data['order']->sub_total = 0;
        //$data['order']->grand_total = 0;
        //build result
        $data['order'] = $order;
        $data['order']->products = $product_items;
        //$data['order']->invoice_code = $order->invoice_code.'-'.$d_order_id.'-'.$d_order_detail_id;
        $data['order']->invoice_code = $order->invoice_code;
        $data['order']->b_user_id_buyer = $buyer->id;
        $data['order']->b_user_fnama_buyer = $buyer->fnama;
        // by Muhammad Sofi - 27 October 2021 10:12
        // if user img & banner not exist or empty, change to default image
        // $data['order']->b_user_image_buyer = $this->cdn_url($buyer->image);
        if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
            $data['order']->b_user_image_buyer = $this->cdn_url($buyer->image);
        } else {
            $data['order']->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
        }
        $data['order']->rating = $this->__getRatings($nation_code, $d_order_id, $d_order_detail_id, $pelanggan->id);
        $data['order']->addresses = $this->__orderAddresses($nation_code, $buyer, $order);
        $data['order']->history = $this->dopm->getByOrderIdProdukId($nation_code, $d_order_id, $d_order_detail_id);

        foreach ($data['order']->history as &$h) {
            if (isset($h->initiator)) {
                $h->initiator = ucwords($h->initiator);
            }
        }
        $data['order']->sub_total = 0.0;
        foreach ($data['order']->products as &$prod) {
            if (isset($prod->foto)) {
                $prod->foto = $this->cdn_url($prod->foto);
            }
            if (isset($prod->thumb)) {
                $prod->thumb = $this->cdn_url($prod->thumb);
            }
            $data['order']->sub_total += ($prod->qty * $prod->harga_jual);
        }
        $data['order']->harga_jual = $data['order']->sub_total;
        $data['order']->qty = $data['order']->total_item;
        $data['order']->status_text = $this->__statusText($data['order'], $data['order']);

        //re cast to str for ajie sake
        $data['order']->harga_jual = strval($data['order']->harga_jual);
        $data['order']->shipment_cost = strval($data['order']->shipment_cost);
        $data['order']->sub_total = strval($data['order']->sub_total);
        $data['order']->grand_total = strval($data['order']->grand_total);

        //free some memory;
        unset($order);
        unset($rating);
        unset($r);

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
    }

    /**
     * Order list
     *   for new ordered product(s)
     *   waiting for seller confirmation
     * @return [type] [description]
     */
    public function baru()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['order_total'] = 0;
        $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //pagination
        $page = (int) $this->input->get("page");
        $page_size = (int) $this->input->get("page_size");
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);

        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/seller/Order::new -- PID: ".$pelanggan->id." APISESS: $apisess");
        }
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/seller/Order::new -> page: $page, page_size: $page_size");
        }

        //by Donny Dennison - 18 november 2021 16:37
        //set timezone in api buyer->order and seller->order
        $timezone = $this->input->get("timezone");
        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        //get produk data
        $dcount = $this->dodm->countSellerNew($nation_code, $pelanggan->id);
        $ddata = $this->dodm->getSellerNew($nation_code, $pelanggan->id, $page, $page_size);
        foreach ($ddata as &$pd) {

            $pd->c_produk_nama = html_entity_decode($pd->c_produk_nama,ENT_QUOTES);

            if (isset($pd->harga_jual)) {
                $pd->harga_jual = strval($pd->harga_jual);
            }
            $pd->d_order_invoice_code = '';
            if (isset($pd->invoice_code)) {
                $pd->d_order_invoice_code = $pd->invoice_code;
            }
            if (isset($pd->c_produk_thumb)) {
                if (empty($pd->c_produk_thumb)) {
                    $pd->c_produk_thumb = 'media/produk/default.png';
                }
                $pd->c_produk_thumb = $this->cdn_url($pd->c_produk_thumb);
            }
            if (isset($pd->c_produk_foto)) {
                if (empty($pd->c_produk_foto)) {
                    $pd->c_produk_foto = 'media/produk/default.png';
                }
                $pd->c_produk_foto = $this->cdn_url($pd->c_produk_foto);
            }
            if (isset($pd->harga_jual)) {
                $pd->harga_jual = strval($pd->harga_jual);
            }
            if (isset($pd->sub_total)) {
                $pd->sub_total = strval($pd->sub_total);
            }
            if (isset($pd->ongkir_total)) {
                $pd->ongkir_total = strval($pd->ongkir_total);
            }
            if (isset($pd->grand_total)) {
                $pd->grand_total = strval($pd->grand_total);
            }
            if (isset($pd->grand_total)) {
                $pd->d_order_grand_total = strval($pd->grand_total);
            }

            //by Donny Dennison - 18 november 2021 16:37
            //set timezone in api buyer->order and seller->order
            $pd->date_begin = $this->customTimezone($pd->date_begin, $timezone);
            $pd->date_expire = $this->customTimezone($pd->date_expire, $timezone);

        }
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/seller/Order::new -> Result Count: $dcount");
        }


        $data['order_total'] = $dcount;
        $data['orders'] = $ddata;

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
    }

    /**
     * Order List
     *   Confirmed by seller
     * @return [type] [description]
     */
    public function process()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['order_total'] = 0;
        $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //pagination
        $page = (int) $this->input->get("page");
        $page_size = (int) $this->input->get("page_size");
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);

        //get produk data
        $dcount = $this->dodm->countSellerProcess($nation_code, $pelanggan->id);
        $ddata = $this->dodm->getSellerProcess($nation_code, $pelanggan->id, $page, $page_size);
        foreach ($ddata as &$pd) {

            $pd->c_produk_nama = html_entity_decode($pd->c_produk_nama,ENT_QUOTES);

            $pd->d_order_grand_total = "0";
            if (isset($pd->grand_total)) {
                $pd->d_order_grand_total = strval($pd->grand_total);
            }
            $pd->grand_total = $pd->d_order_grand_total;
            if (isset($pd->harga_jual)) {
                $pd->harga_jual = strval($pd->harga_jual);
            }
            $pd->d_order_invoice_code = '';
            if (isset($pd->invoice_code)) {
                $pd->d_order_invoice_code = $pd->invoice_code;
            }
            if (isset($pd->c_produk_thumb)) {
                if (empty($pd->c_produk_thumb)) {
                    $pd->c_produk_thumb = 'media/produk/default.png';
                }
                $pd->c_produk_thumb = $this->cdn_url($pd->c_produk_thumb);
            }
            if (isset($pd->c_produk_foto)) {
                if (empty($pd->c_produk_foto)) {
                    $pd->c_produk_foto = 'media/produk/default.png';
                }
                $pd->c_produk_foto = $this->cdn_url($pd->c_produk_foto);
            }
        }

        $data['order_total'] = $dcount;
        $data['orders'] = $ddata;

        $this->status = 200;
        $this->message = 'Success';


        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
    }

    /**
     * Order list
     *   Delivered by seller
     *   delivery in process
     * @return [type] [description]
     */
    public function delivered()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['order_total'] = 0;
        $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/seller/Order::delivered");
        }

        //by Donny Dennison - 18 november 2021 16:37
        //set timezone in api buyer->order and seller->order
        $timezone = $this->input->get("timezone");
        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        //get produk data
        $dcount = $this->dodm->countSellerDelivered($nation_code, $pelanggan->id);
        $ddata = $this->dodm->getSellerDelivered($nation_code, $pelanggan->id);
        foreach ($ddata as &$pd) {

            $pd->c_produk_nama = html_entity_decode($pd->c_produk_nama,ENT_QUOTES);

            $pd->d_order_grand_total = "0";
            if (isset($pd->grand_total)) {
                $pd->d_order_grand_total = strval($pd->grand_total);
            }
            $pd->grand_total = $pd->d_order_grand_total;
            if (isset($pd->harga_jual)) {
                $pd->harga_jual = strval($pd->harga_jual);
            }
            $pd->d_order_invoice_code = '';
            if (isset($pd->invoice_code)) {
                $pd->d_order_invoice_code = $pd->invoice_code;
            }
            if (isset($pd->c_produk_thumb)) {
                if (empty($pd->c_produk_thumb)) {
                    $pd->c_produk_thumb = 'media/produk/default.png';
                }
                $pd->c_produk_thumb = $this->cdn_url($pd->c_produk_thumb);
            }
            if (isset($pd->c_produk_foto)) {
                if (empty($pd->c_produk_foto)) {
                    $pd->c_produk_foto = 'media/produk/default.png';
                }
                $pd->c_produk_foto = $this->cdn_url($pd->c_produk_foto);
            }
            
            //by Donny Dennison - 18 november 2021 16:37
            //set timezone in api buyer->order and seller->order
            $pd->date_begin = $this->customTimezone($pd->date_begin, $timezone);
            $pd->date_expire = $this->customTimezone($pd->date_expire, $timezone);

        }
        $order = $ddata;
        $data['order_total'] = $dcount;
        $data['orders'] = $ddata;

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
    }

    /**
     * Order List
     *   Received by buyer
     *   Shipment status succeed
     * @return [type] [description]
     */
    public function received()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['order_total'] = 0;
        $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }
        //get produk data
        $dcount = $this->dodm->countSellerReceived($nation_code, $pelanggan->id);
        $ddata = $this->dodm->getSellerReceived($nation_code, $pelanggan->id);
        foreach ($ddata as &$pd) {

            $pd->c_produk_nama = html_entity_decode($pd->c_produk_nama,ENT_QUOTES);

            $pd->d_order_grand_total = "0";
            if (isset($pd->grand_total)) {
                $pd->d_order_grand_total = strval($pd->grand_total);
            }
            $pd->grand_total = $pd->d_order_grand_total;
            if (isset($pd->harga_jual)) {
                $pd->harga_jual = strval($pd->harga_jual);
            }
            $pd->d_order_invoice_code = '';
            if (isset($pd->invoice_code)) {
                $pd->d_order_invoice_code = $pd->invoice_code;
            }
            if (isset($pd->c_produk_thumb)) {
                if (empty($pd->c_produk_thumb)) {
                    $pd->c_produk_thumb = 'media/produk/default.png';
                }
                $pd->c_produk_thumb = $this->cdn_url($pd->c_produk_thumb);
            }
            if (isset($pd->c_produk_foto)) {
                if (empty($pd->c_produk_foto)) {
                    $pd->c_produk_foto = 'media/produk/default.png';
                }
                $pd->c_produk_foto = $this->cdn_url($pd->c_produk_foto);
            }
            if (isset($pd->date_begin)) {
                $pd->date_begin = null;
            }
            if (isset($pd->date_expire)) {
                $pd->date_expire = null;
            }
            if (isset($pd->date_current)) {
                $pd->date_current = null;
            }
        }
        $data['order_total'] = $dcount;
        $data['orders'] = $ddata;

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
    }

    /**
     * Order List
     *   Finished Order
     *   Confirmed by buyer
     * @return [type] [description]
     */
    public function succeed()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['order_total'] = 0;
        $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }
        //get produk data
        $dcount = $this->dodm->countSellerSucceed($nation_code, $pelanggan->id);
        $ddata = $this->dodm->getSellerSucceed($nation_code, $pelanggan->id);
        foreach ($ddata as &$pd) {

            $pd->c_produk_nama = html_entity_decode($pd->c_produk_nama,ENT_QUOTES);

            if (isset($pd->harga_jual)) {
                $pd->harga_jual = strval($pd->harga_jual);
            }
            $pd->d_order_invoice_code = '';
            if (isset($pd->invoice_code)) {
                $pd->d_order_invoice_code = $pd->invoice_code;
            }
            if (isset($pd->c_produk_thumb)) {
                if (empty($pd->c_produk_thumb)) {
                    $pd->c_produk_thumb = 'media/produk/default.png';
                }
                $pd->c_produk_thumb = $this->cdn_url($pd->c_produk_thumb);
            }
            if (isset($pd->c_produk_foto)) {
                if (empty($pd->c_produk_foto)) {
                    $pd->c_produk_foto = 'media/produk/default.png';
                }
                $pd->c_produk_foto = $this->cdn_url($pd->c_produk_foto);
            }
            if (isset($pd->harga_jual)) {
                $pd->harga_jual = strval($pd->harga_jual);
            }
            if (isset($pd->sub_total)) {
                $pd->sub_total = strval($pd->sub_total);
            }
            if (isset($pd->ongkir_total)) {
                $pd->ongkir_total = strval($pd->ongkir_total);
            }
            if (isset($pd->grand_total)) {
                $pd->grand_total = strval($pd->grand_total);
            }
            if (isset($pd->grand_total)) {
                $pd->d_order_grand_total = strval($pd->grand_total);
            }
        }
        $data['order_total'] = $dcount;
        $data['orders'] = $ddata;

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
    }

    /**
     * Seller confirmed an order
     * POST d_order_id (INT) ID from d_order
     * POST c_produk_id (INT) ID from d_order_detail
     */
    public function confirmed()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['order_total'] = 0;
        $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        $d_order_id = (int) $this->input->post("d_order_id");
        if ($d_order_id<=0) {
            $this->status = 4010;
            $this->message = 'Invalid Order ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        // actually this is ID from d_order_detail table
        $c_produk_id = (int) $this->input->post("c_produk_id");
        if ($c_produk_id<=0) {
            $this->status = 4012;
            $this->message = 'Invalid Produk ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        $order = $this->dodm->getOrderBySeller($nation_code, $d_order_id, $c_produk_id, $pelanggan->id);
        if (!isset($order->b_user_id_seller)) {
            $this->status = 4011;
            $this->message = 'Order with supplied ID(s) not found';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }
        if ($order->order_status != "forward_to_seller") {
            $this->status = 5004;
            $this->message = 'Order status not in forward to seller';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }
        if ($order->b_user_id_seller != $pelanggan->id) {
            $this->status = 5005;
            $this->message = 'This ordered product not belong to you';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }
        if ($order->seller_status != "unconfirmed") {
            $this->status = 5006;
            $this->message = 'Seller has confirmed / rejected this ordered product';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/seller/Order::confirmed");
        }

        //get pickup address from product data
        // $pickup = $this->dodpum->getById($nation_code, $d_order_id, $c_produk_id);
        // if (!isset($pickup->latitude) || !isset($pickup->longitude)) {
        //     $pickup = new stdClass();
        //     $pickup->latitude = 0;
        //     $pickup->longitude = 0;
        // }

        // //get destination address from order alamat table
        // //by Donny Dennison - 17 juni 2020 20:18
        // // request by Mr Jackie change Shipping Address into Receiving Address
        // // $alamat_kode = 'A1';
        // // $alamat_jenis = 'Shipping Address';
        // $alamat_kode = 'A2';
        // $alamat_jenis = 'Receiving Address';
        // $address_status = $this->ccm->getByClassifiedByCodeName($nation_code, "address", $alamat_jenis);
        // if (isset($address_status->code)) {
        //     $alamat_kode = $address_status->code;
        // }
        // $alamat = $this->doam->getByOrderIdBuyerIdStatusAddress($nation_code, $order->d_order_id, $order->b_user_id_buyer, $alamat_kode);
        // if (!isset($alamat->latitude) || !isset($alamat->longitude)) {
        //     $alamat = new stdClass();
        //     $alamat->latitude = 0;
        //     $alamat->longitude = 0;
        // }

        //start transaction
        $this->order->trans_start();

        //populating update data
        $du = array();
        $du['seller_status'] = 'confirmed';
        $du['shipment_status'] = 'process';
        $du['shipment_tranid'] = '';
        $du['shipment_response'] = '';
        $du['shipment_confirmed'] = 0;
        $du['pickup_date'] = 'NULL';
        $du['date_begin'] = 'NULL';
        $du['date_expire'] = 'NULL';

        //add fallback if empty shipment service or type
        if (strlen($order->shipment_service)<=1) {
            $order->shipment_service = 'qxpress';
            $du['shipment_service'] = $order->shipment_service;
        }
        if (strlen($order->shipment_type)==0) {
            $order->shipment_type = 'next day';
            $du['shipment_type'] = $order->shipment_type;
        }
        $res = $this->dodm->update($nation_code, $d_order_id, $c_produk_id, $du);
        if ($res) {
            $this->order->trans_commit();
            //build order history process
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['d_order_id'] = $d_order_id;
            $di['c_produk_id'] = $c_produk_id;
            $di['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $c_produk_id);
            $di['initiator'] = "Seller";
            $di['nama'] = "Seller Confirmed";
            $di['deskripsi'] = "The ordered product: ".html_entity_decode($order->c_produk_nama)." has been processed by Seller.";
            $di['cdate'] = "NOW()";
            $di['is_done'] = "1";

            $res2 = $this->dopm->set($di);
            if ($res2) {
                $this->order->trans_commit();
                $this->status = 200;
                // $this->message = 'Success, ordered product now in process';
                $this->message = 'Success';
                $this->erm->create($nation_code, $order->d_order_id, $pelanggan->id, $order->b_user_id_buyer, 0, 0);
                $this->order->trans_commit();

                //get buyer data
                $buyer = $this->bu->getById($nation_code, $order->b_user_id_buyer);
                $seller = $pelanggan;

                //declare notification
                $type = 'transaction';
                $anotid = 2;
                $replacer = array();

                //collect array notification list for buyer
                $dpe = array();
                $dpe['nation_code'] = $nation_code;
                $dpe['b_user_id'] = $buyer->id;
                $dpe['id'] = $this->dpem->getLastId($nation_code, $buyer->id);
                $dpe['type'] = $type;
                if($buyer->language_id == 2) {
                    $dpe['judul'] = "Pesanan sedang diproses";
                    $dpe['teks'] = "Pesanan dikonfirmasi! Kami sedang memproses pesanan Anda.";
                } else {
                    $dpe['judul'] = "Order is being processed";
                    $dpe['teks'] = "Order confirmed! We are processing your order.";
                }
                
                $dpe['cdate'] = "NOW()";
                $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
                $extras = new stdClass();
                $extras->id_order = "".$order->id;
                $extras->id_produk = "".$c_produk_id;
                $extras->id_order_detail = "".$c_produk_id;
                $extras->b_user_id_buyer = $buyer->id;
                $extras->b_user_fnama_buyer = $buyer->fnama;

                // by Muhammad Sofi - 27 October 2021 10:12
                // if user img & banner not exist or empty, change to default image
                // $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
                if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
                    $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
                } else {
                    $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                }
                $extras->b_user_id_seller = $seller->id;
                $extras->b_user_fnama_seller = $seller->fnama;

                // by Muhammad Sofi - 27 October 2021 10:12
                // if user img & banner not exist or empty, change to default image
                // $extras->b_user_image_seller = $this->cdn_url($seller->image);
                if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
                    $extras->b_user_image_seller = $this->cdn_url($seller->image);
                } else {
                    $extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                }
                $dpe['extras'] = json_encode($extras);
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
                $this->dpem->set($dpe);
                $this->order->trans_commit();

                //get notification config for buyer
                $setting_value = 0;
                $classified = 'setting_notification_buyer';
                $notif_code = 'B2';
                $notif_cfg = $this->busm->getValue($nation_code, $buyer->id, $classified, $notif_code);
                if (isset($notif_cfg->setting_value)) {
                    $setting_value = (int) $notif_cfg->setting_value;
                }

                //push notif to buyer
                if (strlen($buyer->fcm_token) > 50 && !empty($setting_value) && $buyer->is_active == 1) {
                    $device = $buyer->device;
                    $tokens = array($buyer->fcm_token);
                    if($buyer->language_id == 2) { 
                        $title = 'Pesanan sedang diproses';
                        $message = "Pesanan dikonfirmasi! Kami sedang memproses pesanan Anda.";
                    } else {
                        $title = 'Order is being processed';
                        $message = "Order confirmed! We are processing your order.";
                    }
                    
                    $image = 'media/pemberitahuan/transaction.png';
                    $payload = new stdClass();
                    $payload->id_produk = "".$c_produk_id;
                    $payload->id_order = "".$order->id;
                    $payload->id_order_detail = "".$c_produk_id;
                    $payload->b_user_id_buyer = $buyer->id;
                    $payload->b_user_fnama_buyer = $buyer->fnama;

                    // by Muhammad Sofi - 27 October 2021 10:12
                    // if user img & banner not exist or empty, change to default image
                    // $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
                    if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
                        $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
                    } else {
                        $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                    $payload->b_user_id_seller = $seller->id;
                    $payload->b_user_fnama_seller = $seller->fnama;

                    // by Muhammad Sofi - 27 October 2021 10:12
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
                        $this->seme_log->write("api_mobile", 'API_Mobile/seller/Order::confirmed __pushNotif: '.json_encode($res));
                    }
                }

                //declare notif for seller
                $type = 'transaction';
                $anotid = 1;
                $replacer = array();

                //collect array notification list for seller
                $dpe = array();
                $dpe['nation_code'] = $nation_code;
                $dpe['b_user_id'] = $seller->id;
                $dpe['id'] = $this->dpem->getLastId($nation_code, $seller->id);
                $dpe['type'] = $type;
                if($seller->language_id == 2) {
                    $dpe['judul'] = "Pesanan Dikonfirmasi (Sedang Diproses)";
                    
                    //Donny Dennison - 17-07-2020 17:48
                    //delete print waybill
                    // $dpe['teks'] = "Please prepare the order immediately. We have sent the waybill to your e-mail. Use the \"Print Waybill\" feature in the app and don't forget to post the waybill on the packaging!";
                    $dpe['teks'] = "SellOn akan mengirimkan email \"Waybill\" kepada Anda, harap tunggu beberapa menit.";
                } else {
                    $dpe['judul'] = "Order Confirmed (In Process)";
                    $dpe['teks'] = "SellOn will send you a \"Waybill\" email, please wait for a few minutes.";
                }
                $dpe['cdate'] = "NOW()";
                $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
                $extras = new stdClass();
                $extras->id_order = "".$order->id;
                $extras->id_produk = "".$c_produk_id;
                $extras->id_order_detail = "".$c_produk_id;
                $extras->b_user_id_buyer = $buyer->id;
                $extras->b_user_fnama_buyer = $buyer->fnama;

                // by Muhammad Sofi - 27 October 2021 10:12
                // if user img & banner not exist or empty, change to default image
                // $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
                if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
                    $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
                } else {
                    $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                }
                $extras->b_user_id_seller = $seller->id;
                $extras->b_user_fnama_seller = $seller->fnama;

                // by Muhammad Sofi - 27 October 2021 10:12
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

                //get notification config for seller
                $setting_value = 0;
                $classified = 'setting_notification_seller';
                $notif_code = 'S0';
                $notif_cfg = $this->busm->getValue($nation_code, $seller->id, $classified, $notif_code);
                if (isset($notif_cfg->setting_value)) {
                    $setting_value = (int) $notif_cfg->setting_value;
                }

                //push notif to seller
                if (strlen($seller->fcm_token) > 50 && !empty($setting_value) && $seller->is_active == 1) {
                    $device = $seller->device;
                    $tokens = array($seller->fcm_token);
                    if($seller->language_id == 2) {
                        $title = 'Pesanan Dikonfirmasi (Sedang Diproses)';
                        
                        //Donny Dennison - 17-07-2020 17:48
                        //delete print waybill
                        // $message = "Please prepare the order immediately. Don't forget to post the waybill on the packaging!";
                        $message = "SellOn akan mengirimkan email \"Waybill\" kepada Anda, harap tunggu beberapa menit.";
                    } else {
                        $title = 'Order Confirmed (In Process)';
                        $message = "SellOn will send you a \"Waybill\" email, please wait for a few minutes.";
                    }
                    $image = 'media/pemberitahuan/transaction.png';
                    $payload = new stdClass();
                    $payload = new stdClass();
                    $payload->id_produk = "".$c_produk_id;
                    $payload->id_order = "".$order->id;
                    $payload->id_order_detail = "".$c_produk_id;
                    $payload->b_user_id_buyer = $buyer->id;
                    $payload->b_user_fnama_buyer = $buyer->fnama;

                    // by Muhammad Sofi - 27 October 2021 10:12
                    // if user img & banner not exist or empty, change to default image
                    // $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
                    if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
                        $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
                    } else {
                        $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                    $payload->b_user_id_seller = $seller->id;
                    $payload->b_user_fnama_seller = $seller->fnama;

                    // by Muhammad Sofi - 27 October 2021 10:12
                    // if user img & banner not exist or empty, change to default image
                    // $payload->b_user_image_seller = $this->cdn_url($seller->image);
                    if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
                        $payload->b_user_image_seller = $this->cdn_url($seller->image);
                    } else {
                        $payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                    $nw = $this->anot->get($nation_code, 'push', $type, $anotid, $seller->language_id);
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
                        $this->seme_log->write("api_mobile", 'API_Mobile/seller/Order::confirmed __pushNotif: '.json_encode($res));
                    }
                }

                //send email for seller
                if ($this->email_send && strlen($seller->email)>4) {
                    $replacer = array();
                    $replacer['site_name'] = $this->app_name;
                    $replacer['fnama'] = $seller->fnama;
                    $this->seme_email->flush();
                    $this->seme_email->replyto($this->site_name, $this->site_replyto);
                    $this->seme_email->from($this->site_email, $this->site_name);
                    $this->seme_email->subject('Order Confirmed (Processing)');
                    $this->seme_email->to($seller->email, $seller->fnama);
                    $this->seme_email->template('order_confirmed_processing');
                    $this->seme_email->replacer($replacer);
                    $this->seme_email->send();
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", "API_Mobile/seller/Order::confirmed --emailSentSeller: $seller->email");
                    }
                }
            } else {
                $this->status = 4005;
                $this->message = 'Cannot create order history';
                $this->order->trans_rollback();
            }
        } else {
            $this->status = 4006;
            $this->message = 'Cannot update shipment status';
            $this->order->trans_rollback();
        }
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/seller/Order::confirmed DONE");
        }

        //close transaction
        $this->order->trans_end();

        //json response
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
    }

    /**
     * Seller proceed to delivery order
     * POST d_order_id (INT) ID from d_order
     * POST c_produk_id (INT) ID from d_order_detail
     */
    public function delivery_process()
    {
        //initial
        $dt = $this->__init();

        //By Donny Dennison
        //initial controller from different file
        $waybillController = new waybill();

        //default result
        $data = array();
        $data['order'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }
        $d_order_id = (int) $this->input->post("d_order_id");
        if ($d_order_id<=0) {
            $this->status = 4010;
            $this->message = 'Invalid Order ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        $c_produk_id = (int) $this->input->post("c_produk_id");
        if ($c_produk_id<=0) {
            $this->status = 4012;
            $this->message = 'Invalid Produk ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        $order = $this->dodm->getOrderBySeller($nation_code, $d_order_id, $c_produk_id, $pelanggan->id);
        if (!isset($order->b_user_id_seller)) {
            $c_produk_id = $this->dodim->getOrderDetailByOrderIdProdukId($nation_code, $d_order_id, $c_produk_id);
            if ($c_produk_id<=0) {
                $this->status = 4011;
                $this->message = 'Order with supplied ID(s) not found';
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/seller/order::delivery_process -> POST:".json_encode($_POST));
                }
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/seller/order::delivery_process -> BUID:".$d_order_id." BUID:".$c_produk_id." BUID:".$pelanggan->id);
                }
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/seller/order::delivery_process -> forceClose: ".$this->message);
                }
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
                die();
            }
            $order = $this->dodm->getOrderBySeller($nation_code, $d_order_id, $c_produk_id, $pelanggan->id);
        }
        if ($order->order_status != "forward_to_seller") {
            $this->status = 5004;
            $this->message = 'Order status not in forward to seller';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }
        if ($order->b_user_id_seller != $pelanggan->id) {
            $this->status = 5005;
            $this->message = 'This ordered product not belong to you';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }
        if ($order->seller_status == "unconfirmed") {
            $this->status = 5206;
            $this->message = 'Seller have to confirmed order before delivery process';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }
        if ($order->seller_status == "rejected") {
            $this->status = 5207;
            $this->message = 'Order has been rejected by seller';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }
        if ($order->shipment_status == "delivered") {
            $this->status = 5208;
            // $this->message = 'Ordered product(s) already delivered';
            $this->message = 'Your delivery is already confirmed';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //start transaction
        $this->order->trans_start();

        //build history process
        $di = array();
        $di['nation_code'] = $nation_code;
        $di['d_order_id'] = $d_order_id;
        $di['c_produk_id'] = $c_produk_id;
        $di['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $c_produk_id);
        $di['initiator'] = "Seller";
        $di['nama'] = "Order in Process";
        $di['deskripsi'] = "".html_entity_decode($order->c_produk_nama)." is confirmed and ready to be delivered by the seller.";
        $di['cdate'] = "NOW()";
        $di['is_done'] = "1";
        $res2 = $this->dopm->set($di);
        $this->order->trans_commit();

        //populating update data
        $du = array();
        $du['delivery_date'] = 'NOW()';
        $du['shipment_status'] = 'delivered';
        $du['date_begin'] = date("Y-m-d H:i:s");
        $du['date_expire'] = date("Y-m-d H:i:s", strtotime("+3 days"));
        $res = $this->dodm->update($nation_code, $d_order_id, $c_produk_id, $du);
        if ($res) {
            $this->order->trans_commit();

            //by Donny Dennison - 10 july 2020 10:31
            //move send api delivery to controller/api_mobile/order/delivery_process
            // START change by Donny Dennison - 10 july 2020 10:31
            //get order from d_order_detail and d_order
            $order2 = $this->dodm->getById($nation_code, $d_order_id, $c_produk_id);

            //running backward compatibilty
            if (!isset($order2->d_order_id)) {
                $c_produk_id = $this->dodim->getOrderDetailByOrderIdProdukId($nation_code, $d_order_id, $c_produk_id);
                if ($c_produk_id<=0) {
                    $this->status = 4011;
                    $this->message = 'Order with supplied ID(s) not found';
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
                    die();
                }
                $order2 = $this->dodm->getById($nation_code, $d_order_id, $c_produk_id);
            }
            //get address pickup
            $pickup = $this->dodpum->getById($nation_code, $order2->d_order_id, $order2->d_order_detail_id);
            if (!isset($pickup->penerima_nama)) {
                //if not exist, get from b_user_alamat
                $pa = $this->bua->getById($nation_code, $order2->b_user_id_seller, $order2->b_user_alamat_id);
                if (!isset($pa->penerima_nama)) {
                    $this->status = 6022;
                    $this->message = 'Pickup address not found';
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
                    die();
                }

                //insert into pickup order
                $padi = array();
                $padi['nation_code'] = $nation_code;
                $padi['d_order_id'] = $order2->d_order_id;
                $padi['d_order_detail_id'] = $order2->d_order_detail_id;
                $padi['b_user_id'] = $order2->b_user_id_seller;
                $padi['b_user_alamat_id'] = $order2->b_user_alamat_id;
                $padi['nama'] = $pa->penerima_nama;
                $padi['telp'] = $pa->penerima_telp;
                // by Muhammad Sofi - 3 November 2021 10:00
                // remark code
                // $padi['alamat'] = $pa->alamat;
                $padi['alamat2'] = $pa->alamat2;
                $padi['kelurahan'] = $pa->kelurahan;
                $padi['kecamatan'] = $pa->kecamatan;
                $padi['kabkota'] = $pa->kabkota;
                $padi['provinsi'] = $pa->penerima_nama;
                $padi['negara'] = $pa->negara;
                $padi['kodepos'] = $pa->kodepos;
                $padi['latitude'] = $pa->latitude;
                $padi['longitude'] = $pa->longitude;
                $padi['catatan'] = $pa->address_notes;
                $this->dodpum->set($padi);
                $pickup = $pa;
                $pickup->nama = $pa->penerima_nama;
                $pickup->telp = $pa->penerima_nama;
                // by Muhammad Sofi - 3 November 2021 10:00
                // remark code
                // $pickup->alamat1 = $pa->alamat;
                $pickup->catatan = $pa->address_notes;
            }

            // if (isset($order2->is_wb_download)) {
            //     $this->dodm->updateWB($nation_code, $d_order_id, $order2->d_order_detail_id);
            // }
            if (isset($order2->foto)) {
                $order2->foto = $this->cdn_url($order2->foto);
            }
            if (isset($order2->thumb)) {
                $order2->thumb = $this->cdn_url($order2->thumb);
            }

            //get buyer detail
            $buyer = $this->bu->getById($nation_code, $order2->b_user_id_buyer);
            $seller = $pelanggan;

            //put another
            $order2->addresses = $waybillController->__orderAddresses($nation_code, $buyer, $d_order_id);
            $order2->proses = $this->dopm->getByOrderId($nation_code, $d_order_id);

            //validation
            $is_rejected = 0;
            if (strtolower($order2->seller_status) == 'rejected') {
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process --expiredPDF");
                }
                $is_rejected = 1;
                $waybillController->__expiredPDF($pelanggan, $order2, $pickup);
                die();
                //$this->status = 6013;
                //$this->message = 'Order already rejected by seller';
                //$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
                //die();
            }
            //log order id
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process -> POST: d_order_id: $d_order_id, c_produk_id: $c_produk_id");
            }

            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process -- order2->shipment_service: $order2->shipment_service, order2->shipment_type: $order2->shipment_type");
            }

            //By Donny Dennison - 08-07-2020 16:16
            //Request by Mr Jackie, add new shipment status "courier fail"
            //create pickup order, for shipment == process
            $isFailedApiDelivery = FALSE;

            //by Donny Dennison - 23 september 2020 15:42
            //add direct delivery feature
            //START by Donny Dennison - 23 september 2020 15:42

            if ((strtolower($order2->shipment_service) == 'direct delivery' || $order2->is_direct_delivery_buyer == 1) && (strtolower($order2->shipment_status) == 'process' || strtolower($order2->shipment_status) == 'delivered' || strtolower($order2->shipment_status) == 'courier fail')) {

                $addr = $order2->addresses->shipping;

                //update order detail
                $dx = array();
                $refOrderNo = ''.$order->nation_code.''.date('ymdHis');
                $dx["shipment_tranid"] = $refOrderNo;
                $dx["shipment_confirmed"] = 1;
                $dx["pickup_date"] = date("Y-m-d H:i:s", strtotime("+1 day"));
                $dx["delivery_date"] = $dx["pickup_date"];


                $this->dodm->update($nation_code, $order2->d_order_id, $order2->c_produk_id, $dx);
                $this->order->trans_commit();


                // add to order proses with current status
                $ops = array();
                $ops['nation_code'] = $nation_code;
                $ops['d_order_id'] = $d_order_id;
                $ops['c_produk_id'] = $order2->c_produk_id;
                $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order2->c_produk_id);
                $ops['initiator'] = "Seller";
                $ops['nama'] = "Delivery in Progress";
                $ops['deskripsi'] = "Seller is going to deliver the product, ".html_entity_decode($order2->nama)."($order2->invoice_code), to you directly.";
                $ops['cdate'] = "NOW()";
                $this->dopm->set($ops);
                $this->order->trans_commit();

            //By Donny Dennison - 08-07-2020 16:16
            //Request by Mr Jackie, add new shipment status "courier fail"
            //create pickup order, for shipment == process
            // if (strtolower($order2->shipment_service) == 'qxpress' && (strtolower($order2->shipment_status) == 'process' || strtolower($order2->shipment_status) == 'delivered')) {

            // if (strtolower($order2->shipment_service) == 'qxpress' && (strtolower($order2->shipment_status) == 'process' || strtolower($order2->shipment_status) == 'delivered' || strtolower($order2->shipment_status) == 'courier fail')) {
            }else if (strtolower($order2->shipment_service) == 'qxpress' && (strtolower($order2->shipment_status) == 'process' || strtolower($order2->shipment_status) == 'delivered' || strtolower($order2->shipment_status) == 'courier fail')) {

            //END by Donny Dennison - 23 september 2020 15:42

                if (strtolower($order2->shipment_type) == 'next day' && strlen($order2->shipment_tranid)<=4) {
                    $addr = $order2->addresses->shipping;


                    //by Donny Dennison - 13-07-2020 13:54
                    //disable send api to qxpress
                    // //By Donny Dennison - 7 june 2020 - 14:29
                    // //change send data send to qxpress from buyer to seller
                    // // $rq = $this->__createQXpress($addr, $order2, $seller);
                    // $rq = $waybillController->__createQXpress($addr, $order2, $buyer);

                    // //put on log
                    // $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process -> __createQXpress: ".($rq));

                    // //parsing XML result
                    // $sodt = @simplexml_load_string($rq);
                    // if ($sodt === false) {
                        
                    //     //parsing error
                    //     $cqxe = '';
                    //     foreach (libxml_get_errors() as $error) {
                    //         $cqxe .= $error->message.', ';
                    //     }
                    //     $cqxe = rtrim($cqxe, ', ');
                    //     if ($this->is_log) {
                    //         $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process -> __createQXpress PARSE_ERROR: ".$cqxe);
                    //     }

                    //     //By Donny Dennison - 08-07-2020 16:16
                    //     //Request by Mr Jackie, add new shipment status "courier fail"
                    //     // //notif seller to sent it manually, collect array notification list
                    //     // $extras = new stdClass();
                    //     // $extras->id_produk = $order2->c_produk_id;
                    //     // $extras->id_order = $order2->d_order_id;
                    //     // $extras->id_order_detail = $order2->c_produk_id;
                    //     // $dpe = array();
                    //     // $dpe['nation_code'] = $nation_code;
                    //     // $dpe['b_user_id'] = $pelanggan->id;
                    //     // $dpe['id'] = $this->dpem->getLastId($nation_code, $pelanggan->id);
                    //     // $dpe['type'] = "transaction";
                    //     // $dpe['judul'] = "Sent to QXpress";
                    //     // $dpe['teks'] = "Please bring your ordered product to nearest QXPress courier services.";
                    //     // $dpe['cdate'] = "NOW()";
                    //     // $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
                    //     // $dpe['extras'] = json_encode($extras);
                    //     // $this->dpem->set($dpe);
                    //     // $this->order->trans_commit();
                    //     $isFailedApiDelivery = TRUE;

                    // } else {
                    //     //parse XML success
                    //     if (!is_object($sodt)) {
                    //         $sodt = new stdClass();
                    //     }
                    //     if (!isset($sodt->ResultCode)) {
                    //         $sodt->ResultCode = '-99999';
                    //     }
                    //     if ($sodt->ResultCode==0) {
                    //         if ($this->is_log) {
                    //             $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process -> sodt->ResultCode->TrackingNo: ".json_encode($sodt->ResultObject));
                    //         }
                    //         //success, check response result object reference number
                    //         if (isset($sodt->ResultObject->TrackingNo)) {
                    //             $order2->shipment_tranid = $sodt->ResultObject->TrackingNo;
                    //         }

                    //         //By Donny Dennison, 30 june 2020 15:43
                    //         //Request by Mr Jackie, bug fixing to get tracking number, the previous call api to Qxpress only give response reference number
                    //         //Start change by Donny Dennison
                    //         $tracking_number = NULL;
                    //         $rq2 = $waybillController->__getQXpressTracking($order2->shipment_tranid);

                    //         //put on log
                    //         $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process -> __getQXpressTracking: ".($rq2));

                    //         //parsing XML result
                    //         $sodt2 = @simplexml_load_string($rq2);
                            
                    //         //parse XML success
                    //         if (!is_object($sodt2)) {
                    //           $sodt2 = new stdClass();
                    //         }
                    //         if (!isset($sodt2->ResultCode)) {
                    //           $sodt2->ResultCode = '-99';
                    //         }
                    //         if ($sodt2->ResultCode==0) {

                    //           if ($this->is_log) {
                    //             $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process -> sodt2->ResultCode->TrackingNo: ".json_encode($sodt2->ResultObject));
                    //           }

                    //           //add to field tracking_number
                    //           $tracking_number = $sodt2->ResultObject->info->shipping_no;
                                  
                    //         } elseif ($sodt2->ResultCode=="-99" || $sodt2->ResultCode==-99) {
                    //           //if server error, recreate order QXpress
                    //           $rq2 = $waybillController->__getQXpressTracking($orde2r->shipment_tranid);
                    //           $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process QXpress server error, recreating");
                    //           //put on log
                    //           $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process -> __getQXpressTracking : ".($rq2));

                    //           //parsing XML result
                    //           $sodt2 = simplexml_load_string($rq2);
                              
                    //           //parse OK
                    //           if ($this->is_log) {
                    //               $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process -> sodt2->ResultCode->TrackingNo: ".json_encode($sodt2->ResultObject));
                    //           }

                    //           //decode result
                    //           $pudt = json_decode($rq2);
                    //           if (isset($pudt->ResultCode)) {
                    //             if ($pudt->ResultCode == 0) {
                                    
                    //               //add to field tracking_number
                    //               $tracking_number = $pudt->ResultObject->info->shipping_no;

                    //             } else {
                    //                 //maybe pickup order has created before, do nothing
                    //             }
                    //           } else {
                    //               //notif to seller for deliver their product manually, collect array notification list
                    //           }

                    //         }

                    //         //End change by Donny Dennison

                            //update order detail
                            $dx = array();

                            //by Donny Dennison - 13-07-2020 13:54
                            //disable send api to qxpress
                            // $dx["shipment_tranid"] = $order2->shipment_tranid;
                            $refOrderNo = ''.$order->nation_code.''.date('ymdHis');
                            $dx["shipment_tranid"] = $refOrderNo;

                            //by Donny Dennison - 13-07-2020 13:54
                            //disable send api to qxpress
                            // //By Donny Dennison, 30 june 2020 15:43
                            // //Request by Mr Jackie, bug fixing to get tracking number, the previous call api to Qxpress only give response reference number
                            // $dx["tracking_number"] = $tracking_number;
                            
                            $dx["shipment_confirmed"] = 1;
                            // $dx["pickup_date"] = "null";
                            $dx["pickup_date"] = date("Y-m-d H:i:s", strtotime("+1 day"));
                            $dx["delivery_date"] = $dx["pickup_date"];

                            //by Donny Dennison - 13-07-2020 13:54
                            //disable send api to qxpress
                            // $dx['shipment_response'] = $rq;

                            $this->dodm->update($nation_code, $order2->d_order_id, $order2->c_produk_id, $dx);
                            $this->order->trans_commit();

                            //comment by mas Ilham
                            // //create pickup
                            // $seller = $this->bu->getById($nation_code, $order2->b_user_id_seller);
                            // $rq2 = $this->__createQXpressPickup($pickup, $order2, $seller);

                            // //put on log
                            // $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process -> __createQXpressPickup: ".($rq2));

                            // //decode result
                            // $pudt = json_decode($rq2);
                            // if (isset($pudt->ResultCode)) {
                            //     if ($pudt->ResultCode == 0) {
                            //         //collect array notification list
                            //         $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process QXpress Pickup Order done");

                            //         //update pickup date, to tommorow
                            //         $dx = array();
                            //         $dx["pickup_date"] = date("Y-m-d H:i:s", strtotime("+1 day"));
                            //         $dx["delivery_date"] = $dx["pickup_date"];
                            //         $this->dodm->update($nation_code, $order2->d_order_id, $order2->c_produk_id, $dx);
                            //         $this->order->trans_commit();
                            //         $order2->delivery_date = $dx["delivery_date"];
                            //         $order2->pickup_date = $dx["pickup_date"];

                            //         // add to order proses with current status
                            //         $ops = array();
                            //         $ops['nation_code'] = $nation_code;
                            //         $ops['d_order_id'] = $d_order_id;
                            //         $ops['c_produk_id'] = $order2->c_produk_id;
                            //         $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order2->c_produk_id);
                            //         $ops['initiator'] = "Seller";
                            //         $ops['nama'] = "Pickup Requested";
                            //         $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been added to the QXpress: Next Day pickup queue list";
                            //         $ops['cdate'] = "NOW()";
                            //         $this->dopm->set($ops);
                            //         $this->order->trans_commit();
                            //     } else {
                            //         //maybe pickup order has created before, do nothing
                            //     }
                            // } else {
                            //     //notif to seller for deliver their product manually, collect array notification list
                            // }

                            // add to order proses with current status
                            $ops = array();
                            $ops['nation_code'] = $nation_code;
                            $ops['d_order_id'] = $d_order_id;
                            $ops['c_produk_id'] = $order2->c_produk_id;
                            $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order2->c_produk_id);
                            $ops['initiator'] = "Seller";
                            $ops['nama'] = "Delivery in Progress";

                            //by Donny Dennison - 10 october 2020 17:50
                            //remove receipt number

                            //By Donny Dennison, 30 june 2020 15:43
                            //Request by Mr Jackie, bug fixing to get tracking number, the previous call api to Qxpress only give response reference number
                            // $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been sent by the seller using a courier from QXpress: Next Day (receipt number: $order2->shipment_tranid)";

                            // $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been sent by the seller using a courier from QXpress: Next Day (receipt number: ".$refOrderNo.")";
                            $ops['deskripsi'] = "Your order ".html_entity_decode($order2->nama)." ($order2->invoice_code) has been sent by the seller using a courier from QXpress: Next Day";

                            $ops['cdate'] = "NOW()";
                            $this->dopm->set($ops);
                            $this->order->trans_commit();
                        // } elseif ($sodt->ResultCode=="-55" || $sodt->ResultCode==-55) {
                        //     //duplicate invoice code or tranid, recreate order QXpress

                        //     //By Donny Dennison - 7 june 2020 - 14:29
                        //     //change send data send to qxpress from buyer to seller
                        //     // $rq = $this->__createQXpress($addr, $order, $seller, 0);
                        //     $rq = $waybillController->__createQXpress($addr, $order2, $buyer, 0);

                        //     $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process QXpress Create Order same TranID, recreating");
                        //     //put on log
                        //     $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process -> __createQXpress phase2: ".($rq));

                        //     //parsing XML result
                        //     $sodt = simplexml_load_string($rq);
                        //     if ($sodt === false) {
                        //         //parsing error
                        //         $cqxe = '';
                        //         foreach (libxml_get_errors() as $error) {
                        //             $cqxe .= $error->message.', ';
                        //         }
                        //         $cqxe = rtrim($cqxe, ', ');
                        //         if ($this->is_log) {
                        //             $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process -> __createQXpress phase2 PARSE_ERROR: ".$cqxe);
                        //         }

                        //         //By Donny Dennison - 08-07-2020 16:16
                        //         //Request by Mr Jackie, add new shipment status "courier fail"
                        //         $isFailedApiDelivery = TRUE;

                        //     } else {
                        //         //parse OK
                        //         if ($this->is_log) {
                        //             $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process -> sodt->ResultCode->TrackingNo: ".json_encode($sodt->ResultObject));
                        //         }
                        //         //success, check response result object tracking number
                        //         if (isset($sodt->ResultObject->TrackingNo)) {
                        //             $order2->shipment_tranid = $sodt->ResultObject->TrackingNo;
                        //         }

                        //         //By Donny Dennison, 30 june 2020 15:43
                        //         //Request by Mr Jackie, bug fixing to get tracking number, the previous call api to Qxpress only give response reference number
                        //         //Start change by Donny Dennison
                        //         $tracking_number = NULL;
                        //         $rq2 = $waybillController->__getQXpressTracking($order2->shipment_tranid);

                        //         //put on log
                        //         $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process -> __getQXpressTracking: ".($rq2));

                        //         //parsing XML result
                        //         $sodt2 = @simplexml_load_string($rq2);
                                
                        //         //parse XML success
                        //         if (!is_object($sodt2)) {
                        //           $sodt2 = new stdClass();
                        //         }
                        //         if (!isset($sodt2->ResultCode)) {
                        //           $sodt2->ResultCode = '-99';
                        //         }
                        //         if ($sodt2->ResultCode==0) {

                        //           if ($this->is_log) {
                        //             $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process -> sodt2->ResultCode->TrackingNo: ".json_encode($sodt2->ResultObject));
                        //           }

                        //           //add to field tracking_number
                        //           $tracking_number = $sodt2->ResultObject->info->shipping_no;
                                      
                        //         } elseif ($sodt2->ResultCode=="-99" || $sodt2->ResultCode==-99) {
                        //           //if server error, recreate order QXpress
                        //           $rq2 = $waybillController->__getQXpressTracking($order2->shipment_tranid);
                        //           $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process QXpress server error, recreating");
                        //           //put on log
                        //           $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process -> __getQXpressTracking phase2: ".($rq2));

                        //           //parsing XML result
                        //           $sodt2 = simplexml_load_string($rq2);
                                  
                        //           //parse OK
                        //           if ($this->is_log) {
                        //               $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process -> sodt2->ResultCode->TrackingNo: ".json_encode($sodt2->ResultObject));
                        //           }

                        //           //decode result
                        //           $pudt = json_decode($rq2);
                        //           if (isset($pudt->ResultCode)) {
                        //             if ($pudt->ResultCode == 0) {
                                        
                        //               //add to field tracking_number
                        //               $tracking_number = $pudt->ResultObject->info->shipping_no;

                        //             } else {
                        //                 //maybe pickup order has created before, do nothing
                        //             }
                        //           } else {
                        //               //notif to seller for deliver their product manually, collect array notification list
                        //           }

                        //         }

                        //         //End change by Donny Dennison

                        //         //update order detail
                        //         $dx = array();
                        //         $dx["shipment_tranid"] = $order2->shipment_tranid;
                                
                        //         //By Donny Dennison, 30 june 2020 15:43
                        //         //Request by Mr Jackie, bug fixing to get tracking number, the previous call api to Qxpress only give response reference number
                        //         $dx["tracking_number"] = $tracking_number;

                        //         $dx["shipment_confirmed"] = 1;
                        //         $dx["pickup_date"] = "null";
                        //         $dx["delivery_date"] = "NOW()";
                        //         $dx['shipment_response'] = $rq;
                        //         $this->dodm->update($nation_code, $order2->d_order_id, $order2->c_produk_id, $dx);
                        //         $this->order->trans_commit();
                        //         $order->delivery_date = date("Y-m-d");
                        //         $order->pickup_date = date("Y-m-d");

                        //         //comment by mas Ilham
                        //         // //create pickup
                        //         // $seller = $this->bu->getById($nation_code, $order2->b_user_id_seller);
                        //         // $rq2 = $this->__createQXpressPickup($pickup, $order2, $seller);

                        //         // //put on log
                        //         // $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process -> __createQXpressPickup: ".($rq2));

                        //         // //decode result
                        //         // $pudt = json_decode($rq2);
                        //         // if (isset($pudt->ResultCode)) {
                        //         //     if ($pudt->ResultCode == 0) {
                        //         //         //update pickup date, to tommorow
                        //         //         $dx = array();
                        //         //         $dx["pickup_date"] = date("Y-m-d H:i:s", strtotime("+1 day"));
                        //         //         $dx["delivery_date"] = date("Y-m-d H:i:s", strtotime("+1 day"));
                        //         //         $this->dodm->update($nation_code, $order2->d_order_id, $order2->c_produk_id, $dx);
                        //         //         $this->order->trans_commit();
                        //         //         $order2->delivery_date = $dx["delivery_date"];
                        //         //         $order2->pickup_date = $dx["pickup_date"];

                        //         //         // add to order proses with current status
                        //         //         $ops = array();
                        //         //         $ops['nation_code'] = $nation_code;
                        //         //         $ops['d_order_id'] = $d_order_id;
                        //         //         $ops['c_produk_id'] = $order2->c_produk_id;
                        //         //         $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order2->c_produk_id);
                        //         //         $ops['initiator'] = "Seller";
                        //         //         $ops['nama'] = "Pickup Requested";
                        //         //         $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been added to the QXpress: Next Day pickup queue list";
                        //         //         $ops['cdate'] = "NOW()";
                        //         //         $this->dopm->set($ops);
                        //         //         $this->order->trans_commit();
                        //         //     } else {
                        //         //         //maybe pickup order has created before, do nothing
                        //         //     }
                        //         // } else {
                        //         //     //notif to seller for deliver their product manually, collect array notification list
                        //         // }

                        //         // add to order proses with current status
                        //         $ops = array();
                        //         $ops['nation_code'] = $nation_code;
                        //         $ops['d_order_id'] = $d_order_id;
                        //         $ops['c_produk_id'] = $order2->c_produk_id;
                        //         $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order2->c_produk_id);
                        //         $ops['initiator'] = "Seller";
                        //         $ops['nama'] = "Delivery in Progress";

                        //         //By Donny Dennison, 30 june 2020 15:43
                        //         //Request by Mr Jackie, bug fixing to get tracking number, the previous call api to Qxpress only give response reference number
                        //         // $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been sent by the seller using a courier from QXpress: Next Day (receipt number: $order2->shipment_tranid)";
                        //         $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been sent by the seller using a courier from QXpress: Next Day (receipt number: ".$tracking_number.")";

                        //         $ops['cdate'] = "NOW()";
                        //         $this->dopm->set($ops);
                        //         $this->order->trans_commit();
                        //     } //end parse validation
                        // } else {
                        //     // __createQXpress response code error, maybe order already created

                        //     //By Donny Dennison - 08-07-2020 16:16
                        //     //Request by Mr Jackie, add new shipment status "courier fail"
                        //     if ($this->is_log) {
                        //       $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process -> response dari QXpress bukan 0, isi sodt: ".json_encode($sodt));
                        //     }
                        //     $isFailedApiDelivery = TRUE;

                        // }
                    // }
                } elseif ((strtolower($order2->shipment_type) == 'same day' || (strtolower($order2->shipment_type) == 'sameday')) && strlen($order2->shipment_tranid)<=4) {
                    //for qxpress same day, manually with admin action
                    //update pickup date to next 2hours
                    $dod = array();
                    $dod["pickup_date"] = date("Y-m-d H:i:s", strtotime("+2 hour"));
                    $dod["delivery_date"] = date("Y-m-d H:i:s", strtotime("+4 hour"));
                    $this->dodm->update($nation_code, $d_order_id, $c_produk_id, $dod);
                    $this->order->trans_commit();
                    $order2->delivery_date = $dod["delivery_date"];
                    $order2->pickup_date = $dod["pickup_date"];

                    //send email to admin
                    if ($this->email_send) {
                        //get product data
                        $produk_nama = '-';
                        $items = $this->dodim->getByOrderIdDetailId($nation_code, $d_order_id, $c_produk_id);
                        if (count($items)) {
                            $produk_nama = '';
                            foreach ($items as $itm) {
                                $produk_nama .= $itm->nama.', ';
                            }
                        }

                        //get active admin
                        $admins = $this->apm->getEmailActive();

                        //begin send email to admin
                        $replacer = array();
                        $replacer['site_name'] = $this->app_name;
                        $replacer['produk_nama'] = html_entity_decode($produk_nama,ENT_QUOTES);
                        $replacer['invoice_code'] = $order2->invoice_code;
                        $this->seme_email->replyto($this->site_name, $this->site_replyto);
                        $this->seme_email->from($this->site_email, $this->site_name);
                        $eml = '';
                        foreach ($admins as $adm) {
                            if (strlen($adm->email)>4) {
                                $this->seme_email->to($adm->email, $adm->nama);
                                $eml .= $adm->email.', ';
                            }
                        }
                        $this->seme_email->subject('QXpress - Same day');
                        $this->seme_email->template('qxpress_sameday');
                        $this->seme_email->replacer($replacer);
                        $this->seme_email->send();

                        $eml = rtrim($eml, ', ');
                        if ($this->is_log) {
                            $this->seme_log->write("api_mobile", "API_Mobile/WayBill::delivery_process --sendEmailWBAdmin --to: $eml");
                        }
                        //end send email to admin

                        // add to order proses with current status
                        $ops = array();
                        $ops['nation_code'] = $nation_code;
                        $ops['d_order_id'] = $d_order_id;
                        $ops['c_produk_id'] = $order2->c_produk_id;
                        $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order2->c_produk_id);
                        $ops['initiator'] = "Seller";
                        $ops['nama'] = "Delivery in Progress";
                        $ops['deskripsi'] = "Your order ".html_entity_decode($order2->nama)." ($order2->invoice_code) has been added to QXpress: Same Day queue, please wait for delivery";
                        $ops['cdate'] = "NOW()";
                        $this->dopm->set($ops);
                        $this->order->trans_commit();
                    }
                } else {
                    //undefined shipping type, do nothing
                }

            //by Donny Dennison - 15 september 2020 17:45
            //change name, image, etc from gogovan to gogox

            //By Donny Dennison - 08-07-2020 16:16
            //Request by Mr Jackie, add new shipment status "courier fail"
            // } elseif (strtolower($order2->shipment_service) == 'gogovan' && (strtolower($order2->shipment_status) == 'process' || strlen($order2->shipment_tranid)<=4)) {
            // } elseif (strtolower($order2->shipment_service) == 'gogovan' && (strtolower($order2->shipment_status) == 'process' || strtolower($order2->shipment_status) == 'courier fail' || strlen($order2->shipment_tranid)<=4)) {
            } elseif (strtolower($order2->shipment_service) == 'gogox' && (strtolower($order2->shipment_status) == 'process' || strtolower($order2->shipment_status) == 'courier fail' || strlen($order2->shipment_tranid)<=4)) {

                $address_deliver = $order2->addresses->shipping;
               
                //by Donny Dennison 7 oktober 2020 - 14:10
                //add promotion face mask
                //START by Donny Dennison 7 oktober 2020 - 14:10

                //find the face mask product
                $products = $this->dodim->getByOrderDetailIdForShipment($nation_code, $d_order_id, $c_produk_id);

                $promotion1 = 0;
                foreach ($products as $key => $value) {
                    
                    if($value->c_produk_id == 1746 || $value->c_produk_id == 1752 || $value->c_produk_id == 1754){

                        $promotion1 = 1;
                        break;
                        
                    }

                }

                if($promotion1 == 1){

                    $refOrderNo = ''.$nation_code.''.date('ymdHis');
                    // error_reporting(E_ALL);
                    $dx = array();

                    $dx["shipment_tranid"] = $refOrderNo;
                    $dx["shipment_confirmed"] = 1;
                    $dx["pickup_date"] = date("Y-m-d H:i:00", strtotime("+2 hours"));
                    $dx["delivery_date"] = date("Y-m-d H:i:00", strtotime("+4 hours"));
                    
                    // $dx['shipment_response'] = $rq;

                    $this->dodm->update($nation_code, $order2->d_order_id, $order2->c_produk_id, $dx);
                    $this->order->trans_commit();

                    $order2->shipment_tranid = $refOrderNo;

                    $order2->delivery_date = $dx["delivery_date"];
                    $order2->pickup_date = $dx["pickup_date"];

                    //inform buyer with current status
                    $ops = array();
                    $ops['nation_code'] = $nation_code;
                    $ops['d_order_id'] = $d_order_id;
                    $ops['c_produk_id'] = $order2->c_produk_id;
                    $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order2->c_produk_id);
                    $ops['initiator'] = "Seller";
                    $ops['nama'] = "Delivery in Progress";

                    //by Donny Dennison - 10 october 2020 17:50
                    //remove receipt number

                    //by Donny Dennison - 15 september 2020 17:45
                    //change name, image, etc from gogovan to gogox
                    // $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been sent by the seller using a courier from Gogovan (receipt number: $order2->shipment_tranid)";

                    // $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been sent by the seller using a courier from Gogox (receipt number: $order2->shipment_tranid)";
                    $ops['deskripsi'] = "Your order ".html_entity_decode($order2->nama)." ($order2->invoice_code) has been sent by the seller using a courier from Gogox";

                    $ops['cdate'] = "NOW()";
                    $this->dopm->set($ops);
                    $this->order->trans_commit();

                //by Donny Dennison - 10 august 2020 14:57
                //if latitude or longitude is empty or 0 then set delivery fee to $39
                //START by Donny Dennison - 10 august 2020 14:57

                // }else{
                } else if($pickup->latitude == 0 || $pickup->longitude == 0 || $address_deliver->latitude == 0 || $address_deliver->longitude == 0){

                //END by Donny Dennison 7 oktober 2020 - 14:10

                    $refOrderNo = ''.$nation_code.''.date('ymdHis');
                    // error_reporting(E_ALL);
                    $dx = array();

                    $dx["shipment_tranid"] = $refOrderNo;
                    $dx["shipment_confirmed"] = 1;
                    $dx["pickup_date"] = date("Y-m-d H:i:00", strtotime("+2 hours"));
                    $dx["delivery_date"] = date("Y-m-d H:i:00", strtotime("+4 hours"));
                    
                    // $dx['shipment_response'] = $rq;

                    $this->dodm->update($nation_code, $order2->d_order_id, $order2->c_produk_id, $dx);
                    $this->order->trans_commit();

                    $order2->shipment_tranid = $refOrderNo;

                    $order2->delivery_date = $dx["delivery_date"];
                    $order2->pickup_date = $dx["pickup_date"];

                    //inform buyer with current status
                    $ops = array();
                    $ops['nation_code'] = $nation_code;
                    $ops['d_order_id'] = $d_order_id;
                    $ops['c_produk_id'] = $order2->c_produk_id;
                    $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order2->c_produk_id);
                    $ops['initiator'] = "Seller";
                    $ops['nama'] = "Delivery in Progress";

                    //by Donny Dennison - 10 october 2020 17:50
                    //remove receipt number

                    //by Donny Dennison - 15 september 2020 17:45
                    //change name, image, etc from gogovan to gogox
                    // $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been sent by the seller using a courier from Gogovan (receipt number: $order2->shipment_tranid)";

                    // $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been sent by the seller using a courier from Gogox (receipt number: $order2->shipment_tranid)";
                    $ops['deskripsi'] = "Your order ".html_entity_decode($order2->nama)." ($order2->invoice_code) has been sent by the seller using a courier from Gogox";

                    $ops['cdate'] = "NOW()";
                    $this->dopm->set($ops);
                    $this->order->trans_commit();

                    //send email to support@sellon.net and jackie@corea.co.id
                    if ($this->email_send) {
                        $replacer = array();
                        $replacer['site_name'] = $this->app_name;
                        $replacer['order_id'] = $order2->d_order_id;
                        $replacer['produk_nama'] = html_entity_decode($order2->nama,ENT_QUOTES);
                        $this->seme_email->flush();
                        $this->seme_email->replyto($this->site_name, $this->site_replyto);
                        $this->seme_email->from($this->site_email, $this->site_name);
                        $this->seme_email->subject('(Admin) GoGoX delivery is required manually.');
                        $this->seme_email->to('support@sellon.net', 'support@sellon.net');
                        $this->seme_email->to('jackie@corea.co.id', 'jackie@corea.co.id');
                        $this->seme_email->template('delivery_in_progress_gogovan_manual');
                        $this->seme_email->replacer($replacer);
                        $this->seme_email->send();
                    }

                }else{

                //END by Donny Dennison - 10 august 2020 14:57

                    $rq = $waybillController->__createGogovan($order2, $pickup, $address_deliver);
                    $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process -> __createGogovan: ".($rq));
                   
                    $rqd = json_decode($rq);

                    //by Donny Dennison - 8 September 2020 15:09
                    //change api gogovan to new version (gogovan change name to gogox)
                    // if (isset($rqd->id)) {
                    if (isset($rqd->uuid)) {

                        // error_reporting(E_ALL);
                        $dx = array();

                        //by Donny Dennison - 8 September 2020 15:09
                        //change api gogovan to new version (gogovan change name to gogox)
                        // $dx["shipment_tranid"] = $rqd->id;
                        $dx["shipment_tranid"] = $rqd->uuid;

                        $dx["shipment_confirmed"] = 1;
                        $dx["pickup_date"] = date("Y-m-d H:i:00", strtotime("+2 hours"));
                        $dx["delivery_date"] = date("Y-m-d H:i:00", strtotime("+4 hours"));
                        
                        $dx['shipment_response'] = $rq;

                        $this->dodm->update($nation_code, $order2->d_order_id, $order2->c_produk_id, $dx);
                        $this->order->trans_commit();

                        //by Donny Dennison - 8 September 2020 15:09
                        //change api gogovan to new version (gogovan change name to gogox)
                        // $order2->shipment_tranid = $rqd->id;
                        $order2->shipment_tranid = $rqd->uuid;

                        $order2->delivery_date = $dx["delivery_date"];
                        $order2->pickup_date = $dx["pickup_date"];

                        //inform buyer with current status
                        $ops = array();
                        $ops['nation_code'] = $nation_code;
                        $ops['d_order_id'] = $d_order_id;
                        $ops['c_produk_id'] = $order2->c_produk_id;
                        $ops['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $order2->c_produk_id);
                        $ops['initiator'] = "Seller";
                        $ops['nama'] = "Delivery in Progress";

                        //by Donny Dennison - 10 october 2020 17:50
                        //remove receipt number

                        //by Donny Dennison - 15 september 2020 17:45
                        //change name, image, etc from gogovan to gogox
                        // $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been sent by the seller using a courier from Gogovan (receipt number: $order2->shipment_tranid)";

                        // $ops['deskripsi'] = "Your order $order2->nama ($order2->invoice_code) has been sent by the seller using a courier from Gogox (receipt number: $order2->shipment_tranid)";
                        $ops['deskripsi'] = "Your order ".html_entity_decode($order2->nama)." ($order2->invoice_code) has been sent by the seller using a courier from Gogox";

                        $ops['cdate'] = "NOW()";
                        $this->dopm->set($ops);
                        $this->order->trans_commit();

                    }else{

                        //By Donny Dennison - 08-07-2020 16:16
                        //Request by Mr Jackie, add new shipment status "courier fail"
                        //response from Gogovan not an id
                        if ($this->is_log) {
                          $this->seme_log->write("api_mobile", "API_Mobile/seller/WayBill::delivery_process -> response dari Gogovan bukan uuid, isi rq: ".($rq));
                        }

                        $isFailedApiDelivery = TRUE;

                    }

                }

            } else {
                //undefined shipment method, do nothing...        
            }

            //By Donny Dennison - 08-07-2020 16:16
            //Request by Mr Jackie, add new shipment status "courier fail"
            if($isFailedApiDelivery == TRUE){

               //populating update data
              $du = array();
              $du['pickup_date'] = "null";
              $du['delivery_date'] = "null";
              $du['shipment_status'] = 'courier fail';
              $du['date_begin'] = "null";
              $du['date_expire'] = "null";
              $res = $this->dodm->update($nation_code, $d_order_id, $order2->c_produk_id, $du);
              $this->order->trans_commit();

              $code = '301';
              $message = '-';
              
                //by Donny Dennison - 15 september 2020 17:45
                //change name, image, etc from gogovan to gogox
                // if(strtolower($order2->shipment_service) == 'gogovan'){
                if(strtolower($order2->shipment_service) == 'gogox'){
                
                    if(!isset($rqd->uuid)){
                    
                        if(!isset($rqd->code)){

                            $code = '503';
                            $message = 'Service Temporarily Unavailable';

                        }else{

                            $code = '503';
                            $message = ''.$rqd->code;

                        }

                    }

                //by Donny Dennison - 13-07-2020 13:54
                //disable send api to qxpress
                // }else if(strtolower($order2->shipment_service) == 'qxpress'){

                //   if($sodt === false) {
                        
                //       $message = $cqxe;
                
                //   }else{

                //       $code = ''.$sodt->ResultCode;
                //       $message = ''.$sodt->ResultMsg;

                //   }


                }

                $data['messageFromAPI'] = array(
                    'code'=>$code,
                    'message'=>$message
                );

              $this->status = 301;
              $this->message = $message;
              $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
              die();
            
            }

            // END change by Donny Dennison - 10 july 2020 10:31

            if ($res2) {
                $this->status = 200;
                // $this->message = 'Success, ordered product now in process';
                $this->message = 'Success';
                $this->order->trans_commit();

                //get buyer data
                $buyer = $this->bu->getById($nation_code, $order->b_user_id_buyer);
                $seller = $pelanggan;

                //get notification config for buyer
                $setting_value = 0;
                $classified = 'setting_notification_buyer';
                $notif_code = 'B3';
                $notif_cfg = $this->busm->getValue($nation_code, $buyer->id, $classified, $notif_code);
                if (isset($notif_cfg->setting_value)) {
                    $setting_value = (int) $notif_cfg->setting_value;
                }

                $type = 'transaction';
                $anotid = '3';
                $replacer = array();
                $replacer['invoice_code'] = $order->invoice_code;
                $replacer['shipment_service'] = $order->shipment_service;
                
                //by Donny Dennison - 9 october 2020 21:23
                // receipt number not showing
                $replacer['order_name'] = html_entity_decode($order->c_produk_nama,ENT_QUOTES);
                $replacer['shipment_tranid'] = $order2->shipment_tranid;

                //push notif for buyer
                if (strlen($buyer->fcm_token) > 50 && !empty($setting_value) && $buyer->is_active == 1) {
                    $device = $buyer->device;
                    $tokens = array($buyer->fcm_token);
                    $title = 'Pengiriman sedang berlangsung!';

                    if(strtolower($order->shipment_service) == 'direct delivery'){
                        $message = "Pesanan Anda dengan nomor faktur $order->invoice_code sudah disiapkan oleh penjual. Silakan hubungi dia sekarang.";
                    }else{
                        $message = "Pesanan Anda dengan nomor faktur $order->invoice_code telah dikirim oleh penjual menggunakan kurir dari $order->shipment_service";
                    }

                    $type = 'transaction';
                    $image = 'media/pemberitahuan/transaction.png';
                    $payload = new stdClass();
                    $payload->id_produk = "".$c_produk_id;
                    $payload->id_order = "".$order->id;
                    $payload->id_order_detail = "".$c_produk_id;
                    $payload->b_user_id_buyer = $buyer->id;
                    $payload->b_user_fnama_buyer = $buyer->fnama;

                    // by Muhammad Sofi - 27 October 2021 10:12
                    // if user img & banner not exist or empty, change to default image
                    // $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
                    if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
                        $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
                    } else {
                        $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                    }

                    $payload->b_user_id_seller = $seller->id;
                    $payload->b_user_fnama_seller = $seller->fnama;

                    // by Muhammad Sofi - 27 October 2021 10:12
                    // if user img & banner not exist or empty, change to default image
                    // $payload->b_user_image_seller = $this->cdn_url($seller->image);
                    if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
                        $payload->b_user_image_seller = $this->cdn_url($seller->image);
                    } else {
                        $payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                    $nw = $this->anot->get($nation_code, 'push', $type, $anotid);
                    if (isset($nw->title)) {
                        $title = $nw->title;
                    }
                    if (isset($nw->message) && strtolower($order->shipment_service) != 'direct delivery') {
                        $message = $this->__nRep($nw->message, $replacer);
                    }
                    if (isset($nw->image)) {
                        $image = $nw->image;
                    }
                    $image = $this->cdn_url($image);
                    $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
                    $this->seme_log->write("api_mobile", 'Checkout::delivery_progress __pushNotif: '.json_encode($res));
                }

                //collect array notification list for buyer
                $replacer['order_name'] = html_entity_decode($order->c_produk_nama,ENT_QUOTES);
                
                //by Donny Dennison - 9 october 2020 21:23
                // receipt number not showing
                // $replacer['shipment_tranid'] = $order->shipment_tranid;
                $replacer['shipment_tranid'] = $order2->shipment_tranid;

                $dpe = array();
                $dpe['nation_code'] = $nation_code;
                $dpe['b_user_id'] = $buyer->id;
                $dpe['id'] = $this->dpem->getLastId($nation_code, $buyer->id);
                $dpe['type'] = "transaction";
                $dpe['judul'] = "Pengiriman sedang berlangsung";

                if(strtolower($order->shipment_service) == 'direct delivery'){
                    $dpe['teks'] = "Pesanan Anda ".html_entity_decode($order->c_produk_nama,ENT_QUOTES)." ($order->invoice_code) sudah siap oleh penjual. Silakan hubungi dia sekarang.";
                }else{
                    $dpe['teks'] = "Pesanan Anda ".html_entity_decode($order->c_produk_nama,ENT_QUOTES)." ($order->invoice_code) telah dikirim oleh penjual menggunakan kurir dari $order->shipment_service.";
                }

                $dpe['cdate'] = "NOW()";
                $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
                $extras = new stdClass();
                $extras->id_order = "".$order->id;
                $extras->id_produk = "".$c_produk_id;
                $extras->id_order_detail = "".$c_produk_id;
                $extras->b_user_id_buyer = $buyer->id;
                $extras->b_user_fnama_buyer = $buyer->fnama;

                // by Muhammad Sofi - 27 October 2021 10:12
                // if user img & banner not exist or empty, change to default image
                // $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
                if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
                    $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
                } else {
                    $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                }
                $extras->b_user_id_seller = $seller->id;
                $extras->b_user_fnama_seller = $seller->fnama;
                
                // by Muhammad Sofi - 27 October 2021 10:12
                // if user img & banner not exist or empty, change to default image
                // $extras->b_user_image_seller = $this->cdn_url($seller->image);
                if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
                    $extras->b_user_image_seller = $this->cdn_url($seller->image);
                } else {
                    $extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                }
                $nw = $this->anot->get($nation_code, "list", $type, $anotid);
                if (isset($nw->title)) {
                    $dpe['judul'] = $nw->title;
                }
                if (isset($nw->message) && strtolower($order->shipment_service) != 'direct delivery') {
                    $dpe['teks'] = $this->__nRep($nw->message, $replacer);
                }
                if (isset($nw->image)) {
                    $dpe['gambar'] = $nw->image;
                }
                // $dpe['gambar'] = $this->cdn_url($dpe['gambar']);
                $dpe['extras'] = json_encode($extras);
                $this->dpem->set($dpe);
                $this->order->trans_commit();

                //send email for buyer
                if ($this->email_send && strlen($buyer->email)>4) {
                    $replacer = array();
                    $replacer['site_name'] = $this->app_name;
                    $replacer['fnama'] = $buyer->fnama;
                    $replacer['produk_nama'] = html_entity_decode($order->c_produk_nama,ENT_QUOTES);
                    $replacer['invoice_code'] = $order->invoice_code;

                    //by Donny Dennison - 9 october 2020 21:23
                    // receipt number not showing
                    // $replacer['shipment_tranid'] = $order->shipment_tranid;
                    $replacer['shipment_tranid'] = $order2->shipment_tranid;

                    $replacer['shipment_service'] = $order->shipment_service;
                    $this->seme_email->flush();
                    $this->seme_email->replyto($this->site_name, $this->site_replyto);
                    $this->seme_email->from($this->site_email, $this->site_name);
                    $this->seme_email->subject('Delivery in Progress');
                    $this->seme_email->to($buyer->email, $buyer->fnama);
                    if(strtolower($order->shipment_service) == 'direct delivery'){
                        $this->seme_email->template('delivery_in_progress_direct_delivery');
                    }else{
                        $this->seme_email->template('delivery_in_progress');
                    }
                    $this->seme_email->replacer($replacer);
                    $this->seme_email->send();
                }

                //get notification config for seller
                $setting_value = 0;
                $classified = 'setting_notification_seller';
                $notif_code = 'S1';
                $notif_cfg = $this->busm->getValue($nation_code, $seller->id, $classified, $notif_code);
                if (isset($notif_cfg->setting_value)) {
                    $setting_value = (int) $notif_cfg->setting_value;
                }

                //push notif for seller
                $type = 'transaction';
                $anotid = '4';
                $replacer['invoice_code'] = $order->invoice_code;
                if (strlen($seller->fcm_token) > 50 && !empty($setting_value) && $seller->is_active == 1) {
                    $device = $seller->device;
                    $tokens = array($seller->fcm_token);
                    $title = 'Pengiriman sedang berlangsung!';

                    if(strtolower($order->shipment_service) == 'direct delivery'){
                        $message = "Produk Anda dengan nomor faktur $order->invoice_code perlu diberikan langsung. Silahkan hubungi pembeli.";
                    }else{
                        $message = "Produk Anda dengan nomor faktur $order->invoice_code sedang dikirim.";
                    }

                    $type = 'transaction';
                    $image = 'media/pemberitahuan/transaction.png';
                    $payload = new stdClass();
                    $payload->id_produk = "".$c_produk_id;
                    $payload->id_order = "".$order->id;
                    $payload->id_order_detail = "".$c_produk_id;
                    $payload->b_user_id_buyer = $buyer->id;
                    $payload->b_user_fnama_buyer = $buyer->fnama;

                    // by Muhammad Sofi - 27 October 2021 10:12
                    // if user img & banner not exist or empty, change to default image
                    // $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
                    if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
                        $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
                    } else {
                        $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                    $payload->b_user_id_seller = $seller->id;
                    $payload->b_user_fnama_seller = $seller->fnama;
                    
                    // by Muhammad Sofi - 27 October 2021 10:12
                    // if user img & banner not exist or empty, change to default image
                    // $payload->b_user_image_seller = $this->cdn_url($seller->image);
                    if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
                        $payload->b_user_image_seller = $this->cdn_url($seller->image);
                    } else {
                        $payload->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                    $nw = $this->anot->get($nation_code, 'push', $type, $anotid);
                    if (isset($nw->title)) {
                        $title = $nw->title;
                    }
                    if (isset($nw->message) && strtolower($order->shipment_service) != 'direct delivery') {
                        $message = $this->__nRep($nw->message, $replacer);
                    }
                    if (isset($nw->image)) {
                        $image = $nw->image;
                    }
                    $image = $this->cdn_url($image);

                    $res = $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", 'API_Mobile/seller/Order::delivery_progress __pushNotif: '.json_encode($res));
                    }
                }

                //collect array notification list for seller
                $dpe = array();
                $dpe['nation_code'] = $nation_code;
                $dpe['b_user_id'] = $seller->id;
                $dpe['id'] = $this->dpem->getLastId($nation_code, $seller->id);
                $dpe['type'] = "transaction";
                $dpe['judul'] = "Pengiriman sedang berlangsung!";

                if(strtolower($order->shipment_service) == 'direct delivery'){
                    $dpe['teks'] = "Produk Anda dengan nomor faktur $order->invoice_code perlu diberikan langsung. Silahkan hubungi pembeli.";
                }else{
                    $dpe['teks'] = "Produk Anda dengan nomor faktur $order->invoice_code sedang dikirim.";
                }

                $dpe['cdate'] = "NOW()";
                $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
                $extras = new stdClass();
                $extras->id_order = "".$order->id;
                $extras->id_produk = "".$c_produk_id;
                $extras->id_order_detail = "".$c_produk_id;
                $extras->b_user_id_buyer = $buyer->id;
                $extras->b_user_fnama_buyer = $buyer->fnama;

                // by Muhammad Sofi - 27 October 2021 10:12
                // if user img & banner not exist or empty, change to default image
                // $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
                if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
                    $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
                } else {
                    $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                }
                $extras->b_user_id_seller = $seller->id;
                $extras->b_user_fnama_seller = $seller->fnama;
                
                // by Muhammad Sofi - 27 October 2021 10:12
                // if user img & banner not exist or empty, change to default image
                // $extras->b_user_image_seller = $this->cdn_url($seller->image);
                if(file_exists(SENEROOT.$seller->image) && $seller->image != 'media/user/default.png'){
                    $extras->b_user_image_seller = $this->cdn_url($seller->image);
                } else {
                    $extras->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
                }
                $nw = $this->anot->get($nation_code, "list", $type, $anotid);
                if (isset($nw->title)) {
                    $dpe['judul'] = $nw->title;
                }
                if (isset($nw->message) && strtolower($order->shipment_service) != 'direct delivery') {
                    $dpe['teks'] = $this->__nRep($nw->message, $replacer);
                }
                if (isset($nw->image)) {
                    $dpe['gambar'] = $nw->image;
                }
                // $dpe['gambar'] = $this->cdn_url($dpe['gambar']);
                $dpe['extras'] = json_encode($extras);
                $this->dpem->set($dpe);
                $this->order->trans_commit();
            } else {
                $this->status = 4005;
                $this->message = 'Cannot create order history';
                $this->order->trans_rollback();
            }
        } else {
            $this->status = 4006;
            $this->message = 'Cannot update shipment status';
            $this->order->trans_rollback();
        }
        $this->order->trans_end();
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
    }

    /**
     * Order List
     *   Rejected Items
     * @return [type] [description]
     */
    public function listrejected()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['order_total'] = 0;
        $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }
        //get produk data
        $dcount = $this->dodm->countSellerRejected($nation_code, $pelanggan->id);
        $ddata = $this->dodm->getSellerRejected($nation_code, $pelanggan->id);
        foreach ($ddata as &$pd) {

            $pd->c_produk_nama = html_entity_decode($pd->c_produk_nama,ENT_QUOTES);

            $pd->d_order_grand_total = 0.0;
            if (isset($pd->grand_total)) {
                $pd->d_order_grand_total = $pd->grand_total;
            }
            $pd->d_order_invoice_code = '';
            if (isset($pd->invoice_code)) {
                $pd->d_order_invoice_code = $pd->invoice_code;
            }
            if (isset($pd->c_produk_thumb)) {
                if (empty($pd->c_produk_thumb)) {
                    $pd->c_produk_thumb = 'media/produk/default.png';
                }
                $pd->c_produk_thumb = $this->cdn_url($pd->c_produk_thumb);
            }
            if (isset($pd->c_produk_foto)) {
                if (empty($pd->c_produk_foto)) {
                    $pd->c_produk_foto = 'media/produk/default.png';
                }
                $pd->c_produk_foto = $this->cdn_url($pd->c_produk_foto);
            }
        }
        $data['order_total'] = $dcount;
        $data['orders'] = $ddata;

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
    }

    /**
     * Order List
     *   Expired Order
     * @return [type] [description]
     */
    public function expired()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['order_total'] = 0;
        $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }
        //get produk data
        $dcount = $this->dodm->countSellerExpired($nation_code, $pelanggan->id);
        $ddata = $this->dodm->getSellerExpired($nation_code, $pelanggan->id);
        foreach ($ddata as &$pd) {

            $pd->c_produk_nama = html_entity_decode($pd->c_produk_nama,ENT_QUOTES);

            $pd->d_order_grand_total = "0";
            if (isset($pd->grand_total)) {
                $pd->d_order_grand_total = strval($pd->grand_total);
            }
            $pd->grand_total = $pd->d_order_grand_total;
            if (isset($pd->harga_jual)) {
                $pd->harga_jual = strval($pd->harga_jual);
            }
            $pd->d_order_invoice_code = '';
            if (isset($pd->invoice_code)) {
                $pd->d_order_invoice_code = $pd->invoice_code;
            }

            if (isset($pd->c_produk_thumb)) {
                if (empty($pd->c_produk_thumb)) {
                    $pd->c_produk_thumb = 'media/produk/default.png';
                }
                $pd->c_produk_thumb = $this->cdn_url($pd->c_produk_thumb);
            }
            if (isset($pd->c_produk_foto)) {
                if (empty($pd->c_produk_foto)) {
                    $pd->c_produk_foto = 'media/produk/default.png';
                }
                $pd->c_produk_foto = $this->cdn_url($pd->c_produk_foto);
            }
        }
        $data['order_total'] = $dcount;
        $data['orders'] = $ddata;

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
    }

    /**
     * Seller rejected an order
     * POST d_order_id (INT) ID from d_order table
     * POST c_produk_id (INT) ID from d_order_detail table
     * @return [type] [description]
     */
    public function rejected()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['order_total'] = 0;
        $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }
        $d_order_id = (int) $this->input->post("d_order_id");
        if ($d_order_id<=0) {
            $this->status = 4010;
            $this->message = 'Invalid Order ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }
        $c_produk_id = (int) $this->input->post("c_produk_id");
        if ($c_produk_id<=0) {
            $this->status = 4012;
            $this->message = 'Invalid Produk ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }

        $order = $this->dodm->getOrderBySeller($nation_code, $d_order_id, $c_produk_id, $pelanggan->id);
        if (!isset($order->b_user_id_seller)) {
            $this->status = 4011;
            $this->message = 'Order with supplied ID(s) not found';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }
        if ($order->order_status != "forward_to_seller") {
            $this->status = 5004;
            $this->message = 'Order status not in forward to seller';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }
        if ($order->b_user_id_seller != $pelanggan->id) {
            $this->status = 5005;
            $this->message = 'This ordered product not belong to you';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }
        if ($order->seller_status != "unconfirmed") {
            $this->status = 5006;
            $this->message = 'Seller has confirmed / rejected this ordered product';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
            die();
        }
        //start transaction
        $this->order->trans_start();

        //by Donny Dennison - 16 February 2020 15:50
        //fix reject by seller didnt deduct the total
        //START by Donny Dennison - 16 February 2020 15:50
        $totalPG = $order->pg_fee + $order->pg_fee_vat;
        $newSubTotal = $order->sub_total_order - $order->sub_total;
        $newOngkirTotal = $order->ongkir_total_order - $order->shipment_cost - $order->shipment_cost_add;
        $newGrandTotal = $order->grand_total_order - $order->sub_total- $order->shipment_cost - $order->shipment_cost_add;
        $newRefundAmount = $order->refund_amount_order + $order->sub_total + $order->shipment_cost + $order->shipment_cost_add;
        $newSellingFee = ($order->sub_total_order - $order->sub_total) * ($order->selling_fee_percent_order/100);
        $newProfitAmount = $newSellingFee - $totalPG;

        //update to d_order table
        $du = array();
        $du['sub_total'] = floatval($newSubTotal);
        $du['ongkir_total'] = floatval($newOngkirTotal);
        $du['grand_total'] = floatval($newGrandTotal);
        $du['refund_amount'] = floatval($newRefundAmount);
        $du['selling_fee'] = floatval($newSellingFee);
        $du['profit_amount'] = floatval($newProfitAmount);
        $this->order->update($nation_code, $d_order_id,$du);

        //update all order detail 
        $du = array();
        $du['profit_amount'] = floatval($newProfitAmount);
        $this->dodm->updateByOrderId($nation_code, $d_order_id, $du);
        //END by Donny Dennison - 16 February 2020 15:50

        //update to table d_order_detail
        $du = array();
        $du['shipment_status'] = 'cancelled';
        $du['seller_status'] = 'rejected';
        $du['shipment_tranid'] = '';
        $du['shipment_response'] = '';
        $du['shipment_confirmed'] = 0;
        $du['pickup_date'] = 'NULL';
        $du['date_begin'] = 'NULL';
        $du['date_expire'] = 'NULL';
        $du['refund_amount'] = $order->sub_total+$order->shipment_cost+$order->shipment_cost_add;
        $du['settlement_status'] = "processing";

        //by Donny Dennison - 16 February 2020 15:50
        //fix reject by seller didnt deduct the total
        //START by Donny Dennison - 16 February 2020 15:50
        $du['shipment_cost'] = 0;
        $du['shipment_cost_add'] = 0;
        $du['sub_total'] = 0;
        $du['grand_total'] = 0;
        $du['selling_fee'] = floatval($du['sub_total'] * ($order->selling_fee_percent/100));
        $du['earning_total'] = floatval($du['sub_total'] - $du['selling_fee']);
        //END by Donny Dennison - 16 February 2020 15:50

        $res = $this->dodm->update($nation_code, $d_order_id, $c_produk_id, $du);
        if ($res) {
            $this->order->trans_commit(); //commit transaction

            //build history process
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['d_order_id'] = $d_order_id;
            $di['c_produk_id'] = $c_produk_id;
            $di['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $c_produk_id);
            $di['initiator'] = "Seller";
            $di['nama'] = "Seller Rejected";
            $di['deskripsi'] = "The ordered product: ".html_entity_decode($order->c_produk_nama)." has been rejected by Seller.";
            $di['cdate'] = "NOW()";
            $di['is_done'] = "0";
            $res2 = $this->dopm->set($di);
            $this->order->trans_commit(); //commit transaction
            if ($res2) {
                $this->status = 200;
                // $this->message = 'Success, ordered product has been rejected';
                $this->message = 'Success';
                $this->order->trans_commit();

                //get buyer data
                $buyer = $this->bu->getById($nation_code, $order->b_user_id_buyer);
                $seller = $pelanggan;

                //get notification config for buyer
                $setting_value = 0;
                $classified = 'setting_notification_buyer';
                $notif_code = 'B2';
                $notif_cfg = $this->busm->getValue($nation_code, $buyer->id, $classified, $notif_code);
                if (isset($notif_cfg->setting_value)) {
                    $setting_value = (int) $notif_cfg->setting_value;
                }

                $type = 'transaction';
                $anotid = 5;
                $replacer = array();
                $replacer['invoice_code'] = $order->invoice_code;

                //push notif for buyer
                if (strlen($buyer->fcm_token) > 50 && !empty($setting_value) && $buyer->is_active == 1) {
                    $device = $buyer->device;
                    $tokens = array($buyer->fcm_token);
                    if($buyer->language_id == 2) {
                        $title = 'Ditolak';
                        $message = "Maaf, penjual tidak dapat memproses pesanan Anda untuk faktur: $order->invoice_code";
                    } else {
                        $title = 'Rejected';
                        $message = "Sorry, the seller was unable to process your order for invoice: $order->invoice_code";
                    }
                    
                    $type = 'transaction';
                    $image = 'media/pemberitahuan/transaction.png';
                    $payload = new stdClass();
                    $payload->id_produk = "".$c_produk_id;
                    $payload->id_order = "".$order->id;
                    $payload->id_order_detail = "".$c_produk_id;
                    $payload->b_user_id_buyer = $buyer->id;
                    $payload->b_user_fnama_buyer = $buyer->fnama;

                    // by Muhammad Sofi - 27 October 2021 10:12
                    // if user img & banner not exist or empty, change to default image
                    // $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
                    if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
                        $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
                    } else {
                        $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                    $payload->b_user_id_seller = $seller->id;
                    $payload->b_user_fnama_seller = $seller->fnama;
                    
                    // by Muhammad Sofi - 27 October 2021 10:12
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
                        $this->seme_log->write("api_mobile", 'API_Mobile/seller/Order::rejected -> Buyer Push notif title: '.$title.'');
                    }
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", 'API_Mobile/seller/Order::rejected __pushNotif: '.json_encode($res));
                    }
                }

                //collect array notification list for buyer
                $dpe = array();
                $dpe['nation_code'] = $nation_code;
                $dpe['b_user_id'] = $buyer->id;
                $dpe['id'] = $this->dpem->getLastId($nation_code, $buyer->id);
                $dpe['type'] = "transaction";
                if($buyer->language_id == 2) { 
                    $dpe['judul'] = "Ditolak!";
                    $dpe['teks'] = "Maaf, penjual tidak dapat memproses pesanan Anda untuk faktur: $order->invoice_code. Uang Anda akan dikembalikan secara otomatis dalam waktu 2 hari.";    
                } else {
                    $dpe['judul'] = "Rejected!";
                    $dpe['teks'] = "Sorry, the seller was unable to process your order for invoice: $order->invoice_code. Uang Anda akan dikembalikan secara otomatis dalam waktu 2 hari.";
                }
                $dpe['cdate'] = "NOW()";
                $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
                $extras = new stdClass();
                $extras->id_order = "".$order->id;
                $extras->id_produk = "".$c_produk_id;
                $extras->id_order_detail = "".$c_produk_id;
                $extras->b_user_id_buyer = $buyer->id;
                $extras->b_user_fnama_buyer = $buyer->fnama;
                
                // by Muhammad Sofi - 27 October 2021 10:12
                // if user img & banner not exist or empty, change to default image
                // $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
                if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
                    $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
                } else {
                    $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                }
                $extras->b_user_id_seller = $seller->id;
                $extras->b_user_fnama_seller = $seller->fnama;
                
                // by Muhammad Sofi - 27 October 2021 10:12
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
                // $dpe['gambar'] = $this->cdn_url($dpe['gambar']);
                $dpe['extras'] = json_encode($extras);
                $this->dpem->set($dpe);
                $this->order->trans_commit();

                //email for buyer
                if (!empty($this->email_send) && strlen($buyer->email)>4) {
                    $replacer = array();
                    $replacer['site_name'] = $this->app_name;
                    $replacer['fnama'] = $buyer->fnama;
                    $replacer['invoice_code'] = $order->invoice_code;
                    $replacer['produk_nama'] = html_entity_decode($order->c_produk_nama,ENT_QUOTES);
                    $this->seme_email->flush();
                    $this->seme_email->replyto($this->site_name, $this->site_replyto);
                    $this->seme_email->from($this->site_email, $this->site_name);
                    $this->seme_email->subject('Expired Order!');
                    $this->seme_email->to($buyer->email, $buyer->fnama);
                    $this->seme_email->template('expired_order');
                    $this->seme_email->replacer($replacer);
                    $this->seme_email->send();
                }

                //get notification config for seller
                $setting_value = 0;
                $classified = 'setting_notification_seller';
                $notif_code = 'S0';
                $notif_cfg = $this->busm->getValue($nation_code, $seller->id, $classified, $notif_code);
                if (isset($notif_cfg->setting_value)) {
                    $setting_value = (int) $notif_cfg->setting_value;
                }
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/seller/Order::rejected setting_notification_seller: $notif_code, value: $setting_value");
                }

                //type and replacer idem with above
                $anotid = 6;
                //push notif for seller
                if (strlen($seller->fcm_token) > 50 && !empty($setting_value) && $seller->is_active == 1) {
                    $device = $seller->device;
                    $tokens = array($seller->fcm_token);
                    if($seller->language_id == 2) { 
                        $title = 'Pembatalan';
                        $message = "Anda telah menolak pesanan dengan nomor faktur: $order->invoice_code";
                    } else {
                        $title = 'Cancellation';
                        $message = "You have rejected an order with an invoice number: $order->invoice_code";
                    }
                    $type = 'transaction';
                    $image = 'media/pemberitahuan/transaction.png';
                    $payload = new stdClass();
                    $payload->id_produk = "".$c_produk_id;
                    $payload->id_order = "".$order->id;
                    $payload->id_order_detail = "".$c_produk_id;
                    $payload->b_user_id_buyer = $buyer->id;
                    $payload->b_user_fnama_buyer = $buyer->fnama;

                    // by Muhammad Sofi - 27 October 2021 10:12
                    // if user img & banner not exist or empty, change to default image
                    // $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
                    if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
                        $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
                    } else {
                        $payload->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                    }
                    $payload->b_user_id_seller = $seller->id;
                    $payload->b_user_fnama_seller = $seller->fnama;
                    
                    // by Muhammad Sofi - 27 October 2021 10:12
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
                        $this->seme_log->write("api_mobile", 'API_Mobile/seller/Order::rejected -> Seller Push notif title: '.$title.'');
                    }
                    if ($this->is_log) {
                        $this->seme_log->write("api_mobile", 'API_Mobile/Seller/order::rejected __pushNotif: '.json_encode($res));
                    }
                }

                $replacer['order_name'] = html_entity_decode($order->c_produk_nama,ENT_QUOTES);
                //collect array notification list for seller
                $dpe = array();
                $dpe['nation_code'] = $nation_code;
                $dpe['b_user_id'] = $seller->id;
                $dpe['id'] = $this->dpem->getLastId($nation_code, $seller->id);
                $dpe['type'] = "transaction";
                if($seller->language_id == 2) { 
                    $dpe['judul'] = "Pembatalan";
                    $dpe['teks'] = "Anda telah menolak pesanan dengan nomor faktur: ".html_entity_decode($order->c_produk_nama,ENT_QUOTES)." ($order->invoice_code)";
                } else {
                    $dpe['judul'] = "Cancellation";
                    $dpe['teks'] = "You have rejected an order with an invoice number: ".html_entity_decode($order->c_produk_nama,ENT_QUOTES)." ($order->invoice_code)"; 
                }
                $dpe['cdate'] = "NOW()";
                $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
                $extras = new stdClass();
                $extras->id_order = "".$order->id;
                $extras->id_produk = "".$c_produk_id;
                $extras->id_order_detail = "".$c_produk_id;
                $extras->b_user_id_buyer = $buyer->id;
                $extras->b_user_fnama_buyer = $buyer->fnama;
                
                // by Muhammad Sofi - 27 October 2021 10:12
                // if user img & banner not exist or empty, change to default image
                // $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
                if(file_exists(SENEROOT.$buyer->image) && $buyer->image != 'media/user/default.png'){
                    $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
                } else {
                    $extras->b_user_image_buyer = $this->cdn_url('media/user/default-profile-picture.png');
                }
                $extras->b_user_id_seller = $seller->id;
                $extras->b_user_fnama_seller = $seller->fnama;
                
                // by Muhammad Sofi - 27 October 2021 10:12
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
                // $dpe['gambar'] = $this->cdn_url($dpe['gambar']);
                $dpe['extras'] = json_encode($extras);
                $this->dpem->set($dpe);
                $this->order->trans_commit();

                //email for seller
                if (!empty($this->email_send) && strlen($seller->email)>4) {
                    $replacer = array();
                    $replacer['site_name'] = $this->app_name;
                    $replacer['fnama'] = $seller->fnama;
                    $replacer['invoice_code'] = $order->invoice_code;
                    $replacer['produk_nama'] = html_entity_decode($order->c_produk_nama,ENT_QUOTES);
                    $this->seme_email->flush();
                    $this->seme_email->replyto($this->site_name, $this->site_replyto);
                    $this->seme_email->from($this->site_email, $this->site_name);
                    $this->seme_email->subject('Cancellation');
                    $this->seme_email->to($seller->email, $seller->fnama);
                    $this->seme_email->template('cancellation');
                    $this->seme_email->replacer($replacer);
                    $this->seme_email->send();
                }

                //email to admin
                if ($this->email_send) {
                    $admin = $this->apm->getEmailActive();
                    $replacer = array();
                    $replacer['site_name'] = $this->app_name;
                    $replacer['fnama'] = $pelanggan->fnama;
                    $replacer['invoice_code'] = $order->invoice_code;
                    $replacer['seller_id'] = $pelanggan->id;
                    $replacer['seller_fnama'] = $pelanggan->fnama;
                    $replacer['admincms_link'] = base_url_admin("ecommerce/cancellation/");
                    $this->seme_email->flush();
                    $this->seme_email->replyto($this->site_name, $this->site_replyto);
                    $this->seme_email->from($this->site_email, $this->site_name);
                    $this->seme_email->subject('Rejected by Seller');
                    foreach ($admin as $adm) {
                        if (strlen($adm->email)>4) {
                            $this->seme_email->to($adm->email, $adm->nama);
                        }
                    }
                    $this->seme_email->template('rejected_by_seller');
                    $this->seme_email->replacer($replacer);
                    $this->seme_email->send();
                }

                //by Donny Dennison - 29 april 2021 14:06
                //add-void-and-refund-2c2p-after-reject-by-seller
                //START by Donny Dennison - 29 april 2021 14:06

                if(date('Y-m-d H:i:s') <= date('Y-m-d 22:53:00') && $order->payment_method != 'Grab Pay'){
                
                    $checkTotalSeller = $this->dodm->countTotalSeller($nation_code, $d_order_id);
                    if($checkTotalSeller == 1){
                        $response = $this->__call2c2pApi($order->payment_tranid, 'V');
                        if($response->respCode == 00){

                            //update to d_order table
                            $du = array();
                            $du['pg_fee'] = 0;
                            $du['pg_fee_vat'] = 0;
                            $du['profit_amount'] = 0;
                            $this->order->update($nation_code, $d_order_id,$du);

                            //update to d_order_detail
                            $du = array();
                            $du['pg_fee'] = 0;
                            $du['pg_vat'] = 0;
                            $du['profit_amount'] = 0;
                            $du['is_calculated'] = '1';
                            $du['settlement_status'] = 'completed';
                            $this->dodm->update($nation_code, $d_order_id, $c_produk_id, $du);

                            $ops = array();
                            $ops['nation_code'] = $nation_code;
                            $ops['d_order_id'] = $d_order_id;
                            $ops['c_produk_id'] = $c_produk_id;
                            $ops['id'] = $this->dopm->getLastId($nation_code,$d_order_id,$c_produk_id);
                            $ops['initiator'] = "Admin";
                            $ops['nama'] = "Refund";
                            $ops['deskripsi'] = "Your order with invoice number: $order->invoice_code (".html_entity_decode($order->c_produk_nama).") has been refunded successfully";
                            $ops['cdate'] = "NOW()";
                            $this->dopm->set($ops);

                        }

                    }else{
                        $checkTotalSellerRejected = $this->dodm->countTotalSeller($nation_code, $d_order_id, 'rejected');
                        if($checkTotalSeller == $checkTotalSellerRejected){
                            $response = $this->__call2c2pApi($order->payment_tranid, 'V');
                            if($response->respCode == 00){

                                //update to d_order table
                                $du = array();
                                $du['pg_fee'] = 0;
                                $du['pg_fee_vat'] = 0;
                                $du['profit_amount'] = 0;
                                $this->order->update($nation_code, $d_order_id,$du);

                                //update to d_order_detail
                                $du = array();
                                $du['pg_fee'] = 0;
                                $du['pg_vat'] = 0;
                                $du['profit_amount'] = 0;
                                $du['is_calculated'] = '1';
                                $du['settlement_status'] = 'completed';
                                $this->dodm->updateByOrderId($nation_code, $d_order_id, $du);

                                $ops = array();
                                $ops['nation_code'] = $nation_code;
                                $ops['d_order_id'] = $d_order_id;
                                $ops['c_produk_id'] = $c_produk_id;
                                $ops['id'] = $this->dopm->getLastId($nation_code,$d_order_id,$c_produk_id);
                                $ops['initiator'] = "Admin";
                                $ops['nama'] = "Refund";
                                $ops['deskripsi'] = "Your order with invoice number: $order->invoice_code (".html_entity_decode($order->c_produk_nama).") has been refunded successfully";
                                $ops['cdate'] = "NOW()";
                                $this->dopm->set($ops);

                            }
                        }
                    }

                }

                //END by Donny Dennison - 29 april 2021 14:06

            } else {
                $this->status = 4005;
                $this->message = 'Cannot create order history';
                $this->order->trans_rollback();
            }
        } else {
            $this->status = 4006;
            $this->message = 'Cannot update shipment status';
            $this->order->trans_rollback();
        }
        $this->order->trans_end();
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "seller_order");
    }
}
