<?php
class Transaction extends JI_Controller
{
    public $is_email = 1;

    //by Donny Dennison - 29 april 2021 14:06
    //add-void-and-refund-2c2p-after-reject-by-seller
    public $email_send = 1;
    public $is_log = 1;

    public function __construct()
    {
        parent::__construct();
        $this->current_parent = 'ecommerce';
        $this->current_page = 'ecommerce_produk';
        $this->load("api_admin/a_negara_model", 'anm');
        $this->load("api_admin/b_user_model", 'bum');
        $this->load("api_admin/c_produk_model", 'cpm');
        $this->load("api_admin/d_order_model", 'dom');
        $this->load("api_admin/d_order_detail_model", 'dodm');
        $this->load("api_admin/d_order_detail_item_model", 'dodim');

        //by Donny Dennison - 29 april 2021 14:06
        //add-void-and-refund-2c2p-after-reject-by-seller
        $this->lib("seme_log");
        $this->lib('seme_email');
        $this->load("api_admin/d_order_proses_model", "dopm");
        $this->load("api_admin/b_user_setting_model", "busm");
        $this->load("api_admin/a_notification_model", "anot");
        $this->load("api_admin/d_pemberitahuan_model", "dpem");
        $this->load("api_admin/a_pengguna_model", "apm");

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

    private function __getOrderStatus2($order_status)
    {
        $os = '-';
        if ($order_status=='pending') {
            $os = 'pending';
        }
        if ($order_status=='waiting_for_payment') {
            $os = 'Waiting for Payment';
        }
        if ($order_status=='payment_verification') {
            $os = 'Payment Verification';
        }
        if ($order_status=='forward_to_seller') {
            $os = 'On Process';
        }
        if ($order_status=='completed') {
            $os = 'Suceed';
        }
        if ($order_status=='cancelled') {
            $os = 'Cancelled';
        }
        return $os;
    }

    private function __convertToEmoji($text){
		$value = $text;
		$readTextWithEmoji = preg_replace("/\\\\u([0-9A-F]{2,5})/i", "&#x$1;", $value);
		return $readTextWithEmoji;
	}

    public function index()
    {
        http_response_code("404");
        $this->__json_out("Thank you for using Seme Framework");
    }
    public function seller()
    {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;
        $negara = $this->anm->getByNationCode($nation_code);
        $currency = '';
        if (isset($negara->simbol_mata_uang)) {
            $currency = $negara->simbol_mata_uang;
        }
        $data['currency'] = $currency;

        //get table alias
        $tbl_as = $this->dodm->getTableAlias();
        $tbl2_as = $this->dodm->getTableAlias2(); //product
        $tbl3_as = $this->dodm->getTableAlias3(); //d_order
        $tbl4_as = $this->dodm->getTableAlias4(); //seller
        $tbl5_as = $this->dodm->getTableAlias5(); //buyer
        $tbl6_as = $this->dodm->getTableAlias6();
        $tbl7_as = $this->dodm->getTableAlias7();

        //collect standar input
        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $keyword = $this->input->post("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $pagesize = $this->input->post("iDisplayLength");
        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");

        //standar input validation
        $sortCol = "date";
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }
        $sortCol = $iSortCol_0;
        if (empty($draw)) {
            $draw = 0;
        }
        if (empty($pagesize)) {
            $pagesize=10;
        }
        if (empty($page)) {
            $page=0;
        }

        //custom input
        $seller_status = $this->input->post('seller_status');
        $shipment_status = $this->input->post('shipment_status');
        $courier_service = $this->input->post('courier_service');
        $sdate = $this->input->post('date_order_min');
        $edate = $this->input->post('date_order_max');
        $order_status = $this->input->post('order_status');

        if (empty($seller_status)) {
            $seller_status = '';
        }
        if (empty($shipment_status)) {
            $shipment_status = '';
        }
        if (empty($courier_service)) {
            $courier_service = '';
        }
        if (empty($sdate)) {
            $sdate = '';
        }
        if (empty($edate)) {
            $edate = '';
        }
        if (empty($order_status)) {
            $order_status = '';
        }

        //validating date interval
        if (strlen($sdate)==10) {
            $sdate = date("Y-m-d", strtotime($sdate));
        } else {
            $sdate = "";
        }
        if (strlen($edate)==10) {
            $edate = date("Y-m-d", strtotime($edate));
        } else {
            $edate = "";
        }
        switch ($courier_service) {

            //by Donny Dennison - 15 september 2020 17:45
            //change name, image, etc from gogovan to gogox
            // case "gogovan":
            case "gogox":

                //by Donny Dennison - 15 september 2020 17:45
                //change name, image, etc from gogovan to gogox
                // $shipment_service = 'gogovan';
                $shipment_service = 'gogox';

                $shipment_type = 'next day';
                break;

            //by Donny Dennison - 15 september 2020 17:45
            //change name, image, etc from gogovan to gogox
            // case "gogovan_sameday":
            case "gogox_sameday":

                //by Donny Dennison - 15 september 2020 17:45
                //change name, image, etc from gogovan to gogox
                // $shipment_service = 'gogovan';
                $shipment_service = 'gogox';
                
                $shipment_type = 'same day';
                break;
            case "qxpress":
                $shipment_service = 'qxpress';
                $shipment_type = 'next day';
                break;
            case "qxpress_sameday":
                $shipment_service = 'qxpress';
                $shipment_type = 'same day';
                break;

            //by Donny Dennison - 23 september 2020 15:42
            //add direct delivery feature
            case "direct_delivery":
                $shipment_service = 'direct delivery';
                $shipment_type = 'next day';
                break;
                
            default:
             $shipment_service = '';
             $shipment_type = '';
        }

        $this->status = 200;
        $this->message = 'Success';
        $dcount = $this->dodm->countAll($nation_code, $keyword, $sdate, $edate, $shipment_type, $shipment_service, $seller_status, $shipment_status, $order_status);
        //By Aditya Adi - 2 July 2020 18.30 
        // Add parameter order status
        //$ddata = $this->dodm->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $sdate, $edate, $shipment_type, $shipment_service, $seller_status, $shipment_status);

        $ddata = $this->dodm->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $sdate, $edate, $shipment_type, $shipment_service, $seller_status, $shipment_status, $order_status);
        foreach ($ddata as &$gd) {
            if (isset($gd->cdate)) {
                $gd->cdate = date("d/M/y", strtotime($gd->cdate));
            }
            if(isset($gd->nama)){
				$gd->nama = $this->__convertToEmoji($gd->nama);
			}
            if (isset($gd->harga_jual)) {
                $gd->harga_jual = 'Rp. '.number_format($gd->harga_jual, 2, ',', '.');
            }
            if (isset($gd->order_status_text)) {
                $gd->order_status_text = $this->__orderStatusText($gd->order_status);
            }
            if (isset($gd->shipment_service) && isset($gd->shipment_type)) {
                $gd->shipment_service = $gd->shipment_service.' - '.$gd->shipment_type;
            }
            if (isset($gd->shipment_status) && isset($gd->delivery_date) && isset($gd->received_date)) {

                //By Donny Dennison - 08-07-2020 16:16
                //Request by Mr Jackie, add new shipment status "courier fail"
                if ($gd->shipment_status == 'courier fail') {
                    $gd->shipment_status = ucfirst('courier Fail');
                    
                } elseif ($gd->shipment_status == 'delivered' && strlen($gd->delivery_date) > 9 && strlen($gd->received_date) > 9) {
                    $gd->shipment_status = ucfirst('delivered');
                } elseif ($gd->shipment_status == 'delivered' && strlen($gd->delivery_date) >9 && strlen($gd->received_date) <= 9) {
                    $gd->shipment_status = ucfirst('delivery in progress');
                } elseif (($gd->shipment_status == 'process' || $gd->shipment_status == 'delivered') && strlen($gd->delivery_date) <= 9) {
                    $gd->shipment_status = ucfirst('not yet sent');
                } elseif ($gd->shipment_status == 'succeed') {
                    $gd->shipment_status = ucfirst('received');
                } else {
                    $gd->shipment_status = ucfirst($gd->shipment_status);
                }
            }
            
            //Edit By Aditya Adi Prabowo 3/9/2020 1:14
            // Edit in Transation By Seller Menu
            // Start Edit
            if (isset($gd->buyer_confirmed))
            {
                $getBuyerStatus = $this->dodim->getDetailBuyerStatus($gd->id_temp, $gd->id_temp2);
                $reject_status = (int) $getBuyerStatus->reject;
                $getWaitStatus = $this->dodim->getWait($gd->id_temp, $gd->id_temp2);
                $wait_status = (int) $getWaitStatus->wait;
                $getCountData = $this->dodim->getCount($gd->id_temp, $gd->id_temp2);
                $countData = (int) $getCountData->total;
                $getAcceptData = $this->dodim->getAccept($gd->id_temp, $gd->id_temp2);
                $acceptData = (int) $getAcceptData->accept;

                if($countData==1)
                {
                    if($reject_status==0)
                    {
                        if($wait_status==0)
                        {
                            $gd->buyer_confirmed='Accepted';
                        }
                        else{
                            $gd->buyer_confirmed='Unconfirmed';
                        }
                    }
                    else
                    {
                        if($wait_status==0)
                        {
                            $gd->buyer_confirmed='Rejected';
                        }
                        else{
                            $gd->buyer_confirmed='Unconfirmed';
                        }
                    }
                }
                else
                {
                    if($reject_status==0)
                    {
                        if($wait_status==0)
                        {
                            if($acceptData==0)
                            {
                                $gd->buyer_confirmed='Unconfirmed';
                            }
                            else
                            {
                                $gd->buyer_confirmed='Accepted';
                            }
                        }
                        else
                        {
                            if($acceptData==0)
                            {
                                $gd->buyer_confirmed='Unconfirmed';
                            }
                            else
                            {
                                $gd->buyer_confirmed='Accepted/Unconfirmed';
                            }
                        }
                    }
                    else
                    {
                        if($wait_status==0)
                        {
                            if($acceptData==0)
                            {
                                $gd->buyer_confirmed='Rejected';
                            }
                            else
                            {
                                $gd->buyer_confirmed='Accepted/Rejected';
                            }
                        }
                        else
                        {
                            if($acceptData==0)
                            {
                                $gd->buyer_confirmed='Rejected/Unconfirmed';
                            }
                            else
                            {
                                $gd->buyer_confirmed='Rejected/Accepted/Unconfirmed';
                            }
                        }
                    }
                }
                /*if($reject_status==0)
                {
                    if($wait_status==0)
                    {
                        $gd->buyer_confirmed='Accepted';
                    }
                    else
                    {
                        if($countData==1)
                        {
                            $gd->buyer_confirmed='Unconfirmed';
                        }
                        else
                        {
                            if($acceptData==0)
                            {
                                $gd->buyer_confirmed='Unconfirmed';
                            }
                            else
                            {
                                $gd->buyer_confirmed='Accepted/Unconfirmed';
                            }
                        }
                    }
                }
                else
                {
                    if($wait_status==0)
                    {
                        $gd->buyer_confirmed='Rejected';
                    }
                    else
                    {
                        if($countData==1)
                        {
                            $gd->buyer_confirmed='Unconfirmed';
                        }
                        else
                        {
                            if($acceptData==0)
                            {
                                $gd->buyer_confirmed='Rejected';
                            }
                            else
                            {
                                $gd->buyer_confirmed='Accept/Reject';
                            }
                            
                        }
                    }  
                }*/
            }
            // End Of Edit
            if (isset($gd->action_text)) {
                $gd->action_text = '<button class="btn btn-warning btn-sm btn-action">Action</button>';
            }
        }
        $this->__jsonDataTable($ddata, $dcount);
    }
    public function buyer()
    {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;
        $negara = $this->anm->getByNationCode($nation_code);
        $currency = '';
        if (isset($negara->simbol_mata_uang)) {
            $currency = $negara->simbol_mata_uang;
        }
        $data['currency'] = $currency;

        //get table alias
        $tbl_as = $this->dom->getTableAlias();
        $tbl2_as = $this->dom->getTableAlias2();
        $tbl3_as = $this->dom->getTableAlias3();
        $tbl4_as = $this->dom->getTableAlias4();
        $tbl5_as = $this->dom->getTableAlias5();
        $tbl7_as = $this->dom->getTableAlias7();

        //standar input
        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $keyword = $this->input->post("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $pagesize = $this->input->post("iDisplayLength");
        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");

        //standard validation
        $sortCol = "$tbl_as.cdate";
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }
        $sortCol = $iSortCol_0;
        
        if (empty($draw)) {
            $draw = 0;
        }
        if (empty($pagesize)) {
            $pagesize=10;
        }
        if (empty($page)) {
            $page=0;
        }

        //custom input
        $payment_status = $this->input->post('payment_status');
        $payment_gateway = $this->input->post('payment_gateway');
        $cdate_start = $this->input->post('cdate_start');
        $cdate_end = $this->input->post('cdate_end');
        $order_status = $this->input->post('order_status');

        //validating date interval
        if (strlen($cdate_start)==10) {
            $cdate_start = date("Y-m-d", strtotime($cdate_start));
        } else {
            $cdate_start = "";
        }
        if (strlen($cdate_end)==10) {
            $cdate_end = date("Y-m-d", strtotime($cdate_end));
        } else {
            $cdate_end = "";
        }

        $this->status = 200;
        $this->message = 'Success';
        $dcount = $this->dom->countAllBuyer($nation_code, $keyword, $cdate_start, $cdate_end, $payment_status, $payment_gateway, $order_status);
        $ddata = $this->dom->getAllBuyer($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $cdate_start, $cdate_end, $payment_status, $payment_gateway, $order_status);
        foreach ($ddata as &$gd) {
            if (isset($gd->cdate)) {
                $gd->cdate = date("d/M/y", strtotime($gd->cdate));
            }
            if (isset($gd->sub_total)) {
                $gd->sub_total = 'Rp. '.number_format($gd->sub_total, 2, ',', '.');
            }
            // if (isset($gd->sub_total)) {
            //     $gd->sub_total = number_format($gd->sub_total, 2, '.', ',');
            // }
            if (isset($gd->ongkir_total)) {
                $gd->ongkir_total = 'Rp. '.number_format($gd->ongkir_total, 2, ',', '.');
            }
            if (isset($gd->grand_total)) {
                $gd->grand_total = 'Rp. '.number_format($gd->grand_total, 2, ',', '.');
            }
            if (isset($gd->pg_cost)) {
                $gd->pg_cost = 'Rp. '.number_format($gd->pg_cost, 2, ',', '.');
            }
            if (isset($gd->ldate)) {
                $gd->ldate = date("d/M/y", strtotime($gd->ldate));
            }
            if (isset($gd->payment_status)) {
                $gd->payment_status = $this->__paymentStatusText($gd->payment_status);
            }
            if (isset($gd->order_status)) {
                $gd->order_status = $this->__orderStatusText($gd->order_status);
            }
            if (isset($gd->action_text)) {
                $gd->action_text = '<button class="btn btn-warning btn-sm btn-action">Action</button>';
            }
            if (isset($gd->payment_gateway) && isset($gd->code_bank) && isset($gd->payment_card_origin)) {
                if (strtolower($gd->payment_gateway) != 'unpaid') {
                    $ctx = $this->__card2Text($gd->code_bank);
                    if ($ctx != '-') {
                        $gd->payment_gateway .= ' - '.$ctx;
                        if ($gd->payment_card_origin != 'SG' && $gd->payment_card_origin != 'SGD') {
                            $gd->payment_gateway .= ' (FC)';
                        }
                    }
                }
            }
        }
        $this->__jsonDataTable($ddata, $dcount);
    }

    //by Donny Dennison - 4 january 2021 14:36
    //change chat to open chatting
    public function getinvoiceajax()
    {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;
        $negara = $this->anm->getByNationCode($nation_code);
        // $currency = '';
        // if (isset($negara->simbol_mata_uang)) {
        //     $currency = $negara->simbol_mata_uang;
        // }
        // $data['currency'] = $currency;

        //standar input
        $search = $this->input->post("search");
        $user_id_buyer = $this->input->post("user_id_buyer");
        $user_id_seller = $this->input->post("user_id_seller");

        $ddata = $this->dodim->getinvoiceajax($nation_code, $search, $user_id_buyer, $user_id_seller);

        $data = array();

        foreach ($ddata as $gd) {
            // if (isset($gd->cdate)) {
            //     $gd->cdate = date("d/M/y", strtotime($gd->cdate));
            // }
            // if (isset($gd->sub_total)) {
            //     $gd->sub_total = number_format($gd->sub_total, 2, '.', ',');
            // }
            // if (isset($gd->ongkir_total)) {
            //     $gd->ongkir_total = number_format($gd->ongkir_total, 2, '.', ',');
            // }
            // if (isset($gd->grand_total)) {
            //     $gd->grand_total = number_format($gd->grand_total, 2, '.', ',');
            // }
            // if (isset($gd->pg_cost)) {
            //     $gd->pg_cost = number_format($gd->pg_cost, 2, '.', ',');
            // }
            // if (isset($gd->ldate)) {
            //     $gd->ldate = date("d/M/y", strtotime($gd->ldate));
            // }
            // if (isset($gd->payment_status)) {
            //     $gd->payment_status = $this->__paymentStatusText($gd->payment_status);
            // }
            // if (isset($gd->order_status)) {
            //     $gd->order_status = $this->__orderStatusText($gd->order_status);
            // }
            // if (isset($gd->payment_gateway) && isset($gd->code_bank) && isset($gd->payment_card_origin)) {
            //     if (strtolower($gd->payment_gateway) != 'unpaid') {
            //         $ctx = $this->__card2Text($gd->code_bank);
            //         if ($ctx != '-') {
            //             $gd->payment_gateway .= ' - '.$ctx;
            //             if ($gd->payment_card_origin != 'SG' && $gd->payment_card_origin != 'SGD') {
            //                 $gd->payment_gateway .= ' (FC)';
            //             }
            //         }
            //     }
            // }

            $data[] = array("id"=>$gd->order_id.'/'.$gd->order_detail_id.'/'.$gd->order_detail_item_id, "text"=>$gd->invoice_code.' - '.$gd->nama);

        }
        
        echo json_encode($data);

    }

    //by Donny Dennison - 29 april 2021 14:06
    //add-void-and-refund-2c2p-after-reject-by-seller
    public function voidorrefund($d_order_id,$d_order_detail_id){
        $d = $this->__init();
        $data = array();
        if(!$this->admin_login){
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $nation_code = $d['sess']->admin->nation_code;
        
        //validation

        $d_order_id = (int) $d_order_id;
        if($d_order_id<=0){
            $this->status = 6001;
            $this->message = 'Invalid d_order_id';
            $this->__json_out($data);
            die();
        }
        $d_order_detail_id = (int) $d_order_detail_id;
        if($d_order_detail_id<=0){
            $this->status = 6002;
            $this->message = 'Invalid d_order_detail_id';
            $this->__json_out($data);
            die();
        }

        //default error message
        $this->status = 440;
        $this->message = 'One or more parameter are required';

        //get order detail
        $op = $this->dodm->getDetailByID($nation_code,$d_order_id,$d_order_detail_id);
        if(!isset($op->nation_code)){
            $this->status = 477;
            $this->message = 'Data with supplied ID not found';
            $this->__json_out($data);
            die();
        }
        if($op->settlement_status == "completed" ){

            $this->status = 940;
            $this->message = 'This order already Refunded';
            $this->__json_out($data);
            die();
        }
        if( $op->settlement_status == "processing"){

            $this->status = 940;
            $this->message = 'This order is in refund process';
            $this->__json_out($data);
            die();
        }
        if($op->payment_status != 'paid'){
            $this->status = 943;
            $this->message = 'This order currently unpaid, process aborted';
            $this->__json_out($data);
            die();
        }
        if($op->payment_status == 'unconfirmed'){
            $this->status = 942;
            $this->message = 'This order currently still waiting confirmation from seller';
            $this->__json_out($data);
            die();
        }
        $items = $this->dodim->getByOrderIdDetailId($nation_code,$d_order_id,$d_order_detail_id);
        $item_count = count($items);
        if($item_count<=0){
            $this->status = 941;
            $this->message = 'This order has no item left on table, please contact your system administrator';
            $this->__json_out($data);
            die();
        }

        //start transaction
        $this->dom->trans_start();

        //by Donny Dennison - 16 February 2020 15:50
        //fix reject by seller didnt deduct the total
        //START by Donny Dennison - 16 February 2020 15:50
        $totalPG = $op->pg_fee + $op->pg_fee_vat;
        $newSubTotal = $op->sub_total_order - $op->sub_total;
        $newOngkirTotal = $op->ongkir_total_order - $op->shipment_cost - $op->shipment_cost_add;
        $newGrandTotal = $op->grand_total_order - $op->sub_total- $op->shipment_cost - $op->shipment_cost_add;
        $newRefundAmount = $op->refund_amount_order + $op->sub_total + $op->shipment_cost + $op->shipment_cost_add;
        $newSellingFee = ($op->sub_total_order - $op->sub_total) * ($op->selling_fee_percent_order/100);
        $newProfitAmount = $newSellingFee - $totalPG;

        //update to d_order table
        $du = array();
        $du['sub_total'] = floatval($newSubTotal);
        $du['ongkir_total'] = floatval($newOngkirTotal);
        $du['grand_total'] = floatval($newGrandTotal);
        $du['refund_amount'] = floatval($newRefundAmount);
        $du['selling_fee'] = floatval($newSellingFee);
        $du['profit_amount'] = floatval($newProfitAmount);
        $this->dom->update($nation_code, $d_order_id,$du);

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
        $du['refund_amount'] = $op->sub_total+$op->shipment_cost+$op->shipment_cost_add;
        $du['settlement_status'] = "processing";

        //by Donny Dennison - 16 February 2020 15:50
        //fix reject by seller didnt deduct the total
        //START by Donny Dennison - 16 February 2020 15:50
        $du['shipment_cost'] = 0;
        $du['shipment_cost_add'] = 0;
        $du['sub_total'] = 0;
        $du['grand_total'] = 0;
        $du['selling_fee'] = floatval($du['sub_total'] * ($op->selling_fee_percent/100));
        $du['earning_total'] = floatval($du['sub_total'] - $du['selling_fee']);
        $du['is_rejected_by_admin'] = 1;
        //END by Donny Dennison - 16 February 2020 15:50

        $res = $this->dodm->update($nation_code, $d_order_id, $d_order_detail_id, $du);
        if ($res) {
            $this->dom->trans_commit(); //commit transaction
            
            $this->seme_log->write("api_admin", 'API_Admin/ecommerce/Transcation::voidorrefund -> processing, order_id '.$d_order_id.' and order_detail_id '.$d_order_detail_id);

            //build history process
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['d_order_id'] = $d_order_id;
            $di['c_produk_id'] = $d_order_detail_id;
            $di['id'] = $this->dopm->getLastId($nation_code, $d_order_id, $d_order_detail_id);
            $di['initiator'] = "Seller";
            $di['nama'] = "Seller Rejected";
            $di['deskripsi'] = "The ordered product: ".$op->c_produk_nama." has been rejected by Seller.";
            $di['cdate'] = "NOW()";
            $di['is_done'] = "0";
            $res2 = $this->dopm->set($di);
            $this->dom->trans_commit(); //commit transaction
            if ($res2) {
                $this->status = 200;
                $this->message = 'Success, ordered product has been rejected.';
                $this->dom->trans_commit();

                //get buyer data
                $buyer = $this->bum->getById($nation_code, $op->b_user_id_buyer);
                $seller = $this->bum->getById($nation_code, $op->b_user_id_seller);

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
                $replacer['invoice_code'] = $op->invoice_code;

                //push notif for buyer
                if (strlen($buyer->fcm_token) > 50 && !empty($setting_value)) {
                    $device = $buyer->device;
                    $tokens = array($buyer->fcm_token);
                    $title = 'Ditolak';
                    $message = "Maaf, penjual tidak dapat memproses pesanan Anda dengan faktur: $op->invoice_code";
                    $type = 'transaction';
                    $image = 'media/pemberitahuan/transaction.png';
                    $payload = new stdClass();
                    $payload->id_produk = "".$d_order_detail_id;
                    $payload->id_order = "".$d_order_id;
                    $payload->id_order_detail = "".$d_order_detail_id;
                    $payload->b_user_id_buyer = $buyer->id;
                    $payload->b_user_fnama_buyer = $buyer->fnama;
                    $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
                    $payload->b_user_id_seller = $seller->id;
                    $payload->b_user_fnama_seller = $seller->fnama;
                    $payload->b_user_image_seller = $this->cdn_url($seller->image);
                    $nw = $this->anot->get($nation_code, "push", $type, $anotid);
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
                        $this->seme_log->write("api_admin", 'API_Admin/ecommerce/Transcation::voidorrefund -> Buyer Push notif title: '.$title.'');
                    }
                    if ($this->is_log) {
                        $this->seme_log->write("api_admin", 'API_Admin/ecommerce/Transcation::voidorrefund __pushNotif: '.json_encode($res));
                    }
                }

                //collect array notification list for buyer
                $dpe = array();
                $dpe['nation_code'] = $nation_code;
                $dpe['b_user_id'] = $buyer->id;
                $dpe['id'] = $this->dpem->getLastId($nation_code, $buyer->id);
                $dpe['type'] = "transaction";
                $dpe['judul'] = "Rejected!";
                $dpe['teks'] = "Sorry, the seller cannot process your order for invoice: $op->invoice_code. Your money will be automatically refunded within 2 days.";
                $dpe['cdate'] = "NOW()";
                $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
                $extras = new stdClass();
                $extras->id_order = "".$d_order_id;
                $extras->id_produk = "".$d_order_detail_id;
                $extras->id_order_detail = "".$d_order_detail_id;
                $extras->b_user_id_buyer = $buyer->id;
                $extras->b_user_fnama_buyer = $buyer->fnama;
                $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
                $extras->b_user_id_seller = $seller->id;
                $extras->b_user_fnama_seller = $seller->fnama;
                $extras->b_user_image_seller = $this->cdn_url($seller->image);
                $nw = $this->anot->get($nation_code, "list", $type, $anotid);
                if (isset($nw->title)) {
                    $dpe['judul'] = $nw->title;
                }
                if (isset($nw->message)) {
                    $dpe['teks'] = $this->__nRep($nw->message, $replacer);
                }
                if (isset($nw->image)) {
                    $dpe['gambar'] = $nw->image;
                }
                $dpe['gambar'] = $this->cdn_url($dpe['gambar']);
                $dpe['extras'] = json_encode($extras);
                $this->dpem->set($dpe);
                $this->dom->trans_commit();

                //email for buyer
                if (!empty($this->email_send) && strlen($buyer->email)>4) {
                    $replacer = array();
                    $replacer['site_name'] = $this->app_name;
                    $replacer['fnama'] = $buyer->fnama;
                    $replacer['invoice_code'] = $op->invoice_code;
                    $replacer['produk_nama'] = $op->c_produk_nama;
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
                    $this->seme_log->write("api_admin", "API_Admin/ecommerce/Transcation::voidorrefund setting_notification_seller: $notif_code, value: $setting_value");
                }

                //type and replacer idem with above
                $anotid = 6;
                //push notif for seller
                if (strlen($seller->fcm_token) > 50 && !empty($setting_value)) {
                    $device = $seller->device;
                    $tokens = array($seller->fcm_token);
                    $title = 'Pembatalan';
                    $message = "Anda telah menolak pesanan dengan nomor faktur: $op->invoice_code";
                    $type = 'transaction';
                    $image = 'media/pemberitahuan/transaction.png';
                    $payload = new stdClass();
                    $payload->id_produk = "".$d_order_detail_id;
                    $payload->id_order = "".$d_order_id;
                    $payload->id_order_detail = "".$d_order_detail_id;
                    $payload->b_user_id_buyer = $buyer->id;
                    $payload->b_user_fnama_buyer = $buyer->fnama;
                    $payload->b_user_image_buyer = $this->cdn_url($buyer->image);
                    $payload->b_user_id_seller = $seller->id;
                    $payload->b_user_fnama_seller = $seller->fnama;
                    $payload->b_user_image_seller = $this->cdn_url($seller->image);
                    $nw = $this->anot->get($nation_code, "push", $type, $anotid);
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
                        $this->seme_log->write("api_admin", 'API_Admin/ecommerce/Transcation::voidorrefund -> Seller Push notif title: '.$title.'');
                    }
                    if ($this->is_log) {
                        $this->seme_log->write("api_admin", 'API_Admin/ecommerce/Transcation::voidorrefund __pushNotif: '.json_encode($res));
                    }
                }

                $replacer['order_name'] = $op->c_produk_nama;
                //collect array notification list for seller
                $dpe = array();
                $dpe['nation_code'] = $nation_code;
                $dpe['b_user_id'] = $seller->id;
                $dpe['id'] = $this->dpem->getLastId($nation_code, $seller->id);
                $dpe['type'] = "transaction";
                $dpe['judul'] = "Cancellation";
                $dpe['teks'] = "You have rejected an order with invoice number: $op->c_produk_nama ($op->invoice_code)";
                $dpe['cdate'] = "NOW()";
                $dpe['gambar'] = 'media/pemberitahuan/transaction.png';
                $extras = new stdClass();
                $extras->id_order = "".$d_order_id;
                $extras->id_produk = "".$d_order_detail_id;
                $extras->id_order_detail = "".$d_order_detail_id;
                $extras->b_user_id_buyer = $buyer->id;
                $extras->b_user_fnama_buyer = $buyer->fnama;
                $extras->b_user_image_buyer = $this->cdn_url($buyer->image);
                $extras->b_user_id_seller = $seller->id;
                $extras->b_user_fnama_seller = $seller->fnama;
                $extras->b_user_image_seller = $this->cdn_url($seller->image);
                $nw = $this->anot->get($nation_code, "list", $type, $anotid);
                if (isset($nw->title)) {
                    $dpe['judul'] = $nw->title;
                }
                if (isset($nw->message)) {
                    $dpe['teks'] = $this->__nRep($nw->message, $replacer);
                }
                if (isset($nw->image)) {
                    $dpe['gambar'] = $nw->image;
                }
                $dpe['gambar'] = $this->cdn_url($dpe['gambar']);
                $dpe['extras'] = json_encode($extras);
                $this->dpem->set($dpe);
                $this->dom->trans_commit();

                //email for seller
                if (!empty($this->email_send) && strlen($seller->email)>4) {
                    $replacer = array();
                    $replacer['site_name'] = $this->app_name;
                    $replacer['fnama'] = $seller->fnama;
                    $replacer['invoice_code'] = $op->invoice_code;
                    $replacer['produk_nama'] = $op->c_produk_nama;
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
                    $replacer['fnama'] = $seller->fnama;
                    $replacer['invoice_code'] = $op->invoice_code;
                    $replacer['seller_id'] = $seller->id;
                    $replacer['seller_fnama'] = $seller->fnama;
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

                if(date('Y-m-d',strtotime($op->payment_date)) == date('Y-m-d') && $op->payment_date <= date('Y-m-d 22:53:00') && $op->payment_method != 'Grab Pay'){
                
                    $checkTotalSeller = $this->dodm->countTotalSeller($nation_code, $d_order_id);
                    if($checkTotalSeller == 1){
                        $response = $this->__call2c2pApi($op->payment_tranid, 'V');
                        if($response->respCode == 00){

                            //update to d_order table
                            $du = array();
                            $du['pg_fee'] = 0;
                            $du['pg_fee_vat'] = 0;
                            $du['profit_amount'] = 0;
                            $this->dom->update($nation_code, $d_order_id,$du);

                            //update to d_order_detail
                            $du = array();
                            $du['pg_fee'] = 0;
                            $du['pg_vat'] = 0;
                            $du['profit_amount'] = 0;
                            $du['is_calculated'] = '1';
                            $du['settlement_status'] = 'completed';
                            $this->dodm->update($nation_code, $d_order_id, $d_order_detail_id, $du);

                            $ops = array();
                            $ops['nation_code'] = $nation_code;
                            $ops['d_order_id'] = $d_order_id;
                            $ops['c_produk_id'] = $d_order_detail_id;
                            $ops['id'] = $this->dopm->getLastId($nation_code,$d_order_id,$d_order_detail_id);
                            $ops['initiator'] = "Admin";
                            $ops['nama'] = "Refund";
                            $ops['deskripsi'] = "Your order with invoice number: $op->invoice_code ($op->c_produk_nama) has been refunded successfully";
                            $ops['cdate'] = "NOW()";
                            $this->dopm->set($ops);

                        }

                    }else{
                        $checkTotalSellerRejected = $this->dodm->countTotalSeller($nation_code, $d_order_id, 'rejected');
                        if($checkTotalSeller == $checkTotalSellerRejected){
                            $response = $this->__call2c2pApi($op->payment_tranid, 'V');
                            if($response->respCode == 00){

                                //update to d_order table
                                $du = array();
                                $du['pg_fee'] = 0;
                                $du['pg_fee_vat'] = 0;
                                $du['profit_amount'] = 0;
                                $this->dom->update($nation_code, $d_order_id,$du);

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
                                $ops['c_produk_id'] = $d_order_detail_id;
                                $ops['id'] = $this->dopm->getLastId($nation_code,$d_order_id,$d_order_detail_id);
                                $ops['initiator'] = "Admin";
                                $ops['nama'] = "Refund";
                                $ops['deskripsi'] = "Your order with invoice number: $op->invoice_code ($op->c_produk_nama) has been refunded successfully";
                                $ops['cdate'] = "NOW()";
                                $this->dopm->set($ops);

                            }
                        }
                    }

                }

                //END by Donny Dennison - 29 april 2021 14:06

            } else {
                $this->status = 999;
                $this->message = 'Cannot create order history';
                $this->dom->trans_rollback();
            }
        } else {
            $this->status = 999;
            $this->message = 'Cannot void or refund';
            $this->dom->trans_rollback();
        }
        $this->dom->trans_end();

        //render output
        $this->__json_out($data);

    }

}
