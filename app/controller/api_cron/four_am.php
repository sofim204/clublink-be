<?php
// class Four_am extends JI_Controller
// {
//     public $email_send = 1;

//     public $is_log = 1;
//     public $is_push = 1;

//     public function __construct()
//     {
//         parent::__construct();
//         // $this->lib("seme_log");
//         $this->lib("seme_email");
//         $this->load("api_cron/d_order_detail_model", "dodm");
//         $this->load("api_cron/d_order_proses_model", "dopm");
//     }

//     private function __call2c2pApi($invoiceno, $processType, $refund_amount)
//     {
//       /* 
//       Process Type:
//           I = transaction inquiry
//           V = transaction void
//           R = transaction Refund
//           S = transaction Settlement 
//       */
//       $version = "3.4";
      
//       //Construct signature string
//       $stringToHash = $version . $this->merchantID_2c2p . $processType . $invoiceno . $refund_amount ; 
//       $hash = strtoupper(hash_hmac('sha1', $stringToHash ,$this->secretKey_2c2p, false)); //Compute hash value

//       //Construct request message
//       $xml = "<PaymentProcessRequest>
//               <version>$version</version>
//               <merchantID>$this->merchantID_2c2p</merchantID>
//               <processType>$processType</processType>
//               <invoiceNo>$invoiceno</invoiceNo>
//               <actionAmount>$refund_amount</actionAmount>
//               <hashValue>$hash</hashValue>
//               </PaymentProcessRequest>";
      
//       if($this->env_2c2p == 'staging'){
//         //staging
//         $payload = $this->encrypt_2c2p($xml,SENEROOT."key/demo2.crt"); //Encrypt payload   
//       }else{
//         //production
//         $payload = $this->encrypt_2c2p($xml,SENEROOT."key/prod_2c2p_public.cer"); //Encrypt payload        
//       }
      
//       //Send request to 2C2P PGW and get back response
//       //open connection
//       $ch = curl_init();
//       curl_setopt($ch,CURLOPT_URL, $this->payment_action_api_host_2c2p);
//       curl_setopt($ch,CURLOPT_POSTFIELDS, "paymentRequest=".$payload);
//       curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
//       //execute post
//       $response = curl_exec($ch); //close connection
//       curl_close($ch);

//       //Decrypt response message and display  
//       $response = $this->decrypt_2c2p($response,SENEROOT."key/cert.crt",SENEROOT."key/private.pem","pwFVhwEf73p6");   
      
//       $this->seme_log->write("api_cron", 'API_Cron/Four_Am::__call2c2pApi -- Refund InvoiceNo '. $invoiceno .' and Refund Amount '. $refund_amount .',Response from 2c2p : '. json_encode($response));

//       //Validate response Hash
//       $resXml=simplexml_load_string($response); 
//       $res_version = $resXml->version;
//       $res_respCode = $resXml->respCode;
//       $res_processType = $resXml->processType;
//       $res_invoiceNo = $resXml->invoiceNo;
//       $res_amount = $resXml->amount;
//       $res_status = $resXml->status;
//       $res_approvalCode = $resXml->approvalCode;
//       $res_referenceNo = $resXml->referenceNo;
//       $res_transactionDateTime = $resXml->transactionDateTime;
//       $res_paidAgent = $resXml->paidAgent;
//       $res_paidChannel = $resXml->paidChannel;
//       $res_maskedPan = $resXml->maskedPan;
//       $res_eci = $resXml->eci;
//       $res_paymentScheme = $resXml->paymentScheme;
//       $res_processBy = $resXml->processBy;
//       $res_refundReferenceNo = $resXml->refundReferenceNo;
//       $res_userDefined1 = $resXml->userDefined1;
//       $res_userDefined2 = $resXml->userDefined2;
//       $res_userDefined3 = $resXml->userDefined3;
//       $res_userDefined4 = $resXml->userDefined4;
//       $res_userDefined5 = $resXml->userDefined5;
      
//       //Compute response hash
//       $res_stringToHash = $res_version.$res_respCode.$res_processType.$res_invoiceNo.$res_amount.$res_status.$res_approvalCode.$res_referenceNo.$res_transactionDateTime.$res_paidAgent.$res_paidChannel.$res_maskedPan.$res_eci.$res_paymentScheme.$res_processBy.$res_refundReferenceNo.$res_userDefined1.$res_userDefined2.$res_userDefined3.$res_userDefined4.$res_userDefined5 ; 
//       $res_responseHash = strtoupper(hash_hmac('sha1',$res_stringToHash,$this->secretKey_2c2p, false)); //Calculate response Hash Value 

//       if(strtolower($resXml->hashValue) == strtolower($res_responseHash)){ 
//         return $resXml; 
//       } else{
//         $return = new stdClass();
//         $return->respCode = 99;
//         return $return; 
//       }

//     }

//     public function index()
//     {

//         //change log filename
//         // $this->seme_log->changeFilename("cron.log");


//         //put on log
//         $this->seme_log->write("api_cron", 'API_Cron/Four_Am::index --configuration --countdown_timeout: 4 am');
        
//         //open transaction
//         $this->dodm->trans_start();

//         /** @var array list of rejected order that haven't refund*/
//         $rejected = $this->dodm->getSellerRejected();
//         $c = count($rejected);
//         $this->seme_log->write("api_cron", 'API_Cron/Four_Am::index --rejectedsCount: '.$c);
//         if (count($rejected)>0) {
//           foreach ($rejected as $reject) {

//             $response = $this->__call2c2pApi($reject->payment_tranid, 'R', $reject->refund_amount);

//             if($response->respCode == 00){

//                 //update to d_order_detail
//                 $du = array();
//                 $du['is_calculated'] = '1';
//                 $du['settlement_status'] = 'completed';
//                 $this->dodm->update($reject->nation_code, $reject->d_order_id, $reject->d_order_detail_id, $du);

//                 $ops = array();
//                 $ops['nation_code'] = $reject->nation_code;
//                 $ops['d_order_id'] = $reject->d_order_id;
//                 $ops['c_produk_id'] = $reject->d_order_detail_id;
//                 $ops['id'] = $this->dopm->getLastId($reject->nation_code,$reject->d_order_id,$reject->d_order_detail_id);
//                 $ops['initiator'] = "Admin";
//                 $ops['nama'] = "Refund";
//                 $ops['deskripsi'] = "Your order with invoice number: $reject->invoice_code ($reject->nama) has been refunded successfully";
//                 $ops['cdate'] = "NOW()";
//                 $this->dopm->set($ops);

//             }

//           }//end foreach
//         }//end data count

//         $this->dodm->trans_commit();
        
//         //end transacation
//         $this->dodm->trans_end();

//     }
// }
