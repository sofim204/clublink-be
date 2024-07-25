<?php
  class Card extends JI_Controller{
    public function __construct(){
      parent::__construct();
      $this->lib("seme_log");
      $this->load("api_mobile/b_user_model",'bu');
      $this->load("api_mobile/b_user_card_model",'buc');

      //by Donny Dennison - 28 october 2021 10:48
      //payment call 2c2p in api for flutter version
      $this->load("api_mobile/b_user_card_type_model",'buct');

    }

    //by Donny Dennison - 16 april 2021 13:33
    //deprecated
    // //by Donny Dennison - 16 april 2021 13:33
    // //add-void-2c2p-after-add-credit-card
    // private function __call2c2pApi($invoiceno, $processType)
    // {
    //   /* 
    //   Process Type:
    //       I = transaction inquiry
    //       V = transaction void
    //       R = transaction Refund
    //       S = transaction Settlement 
    //   */
    //   $version = "3.4";
      
    //   //Construct signature string
    //   $stringToHash = $version . $this->merchantID_2c2p . $processType . $invoiceno . '0.01' ; 
    //   $hash = strtoupper(hash_hmac('sha1', $stringToHash ,$this->secretKey_2c2p, false)); //Compute hash value

    //   //Construct request message
    //   $xml = "<PaymentProcessRequest>
    //           <version>$version</version>
    //           <merchantID>$this->merchantID_2c2p</merchantID>
    //           <processType>$processType</processType>
    //           <invoiceNo>$invoiceno</invoiceNo>
    //           <actionAmount>0.01</actionAmount>
    //           <hashValue>$hash</hashValue>
    //           </PaymentProcessRequest>";
      
    //   if($this->env_2c2p == 'staging'){
    //     //staging
    //     $payload = $this->encrypt_2c2p($xml,SENEROOT."key/demo2.crt"); //Encrypt payload   
    //   }else{
    //     //production
    //     $payload = $this->encrypt_2c2p($xml,SENEROOT."key/prod_2c2p_public.cer"); //Encrypt payload        
    //   }
      
    //   //Send request to 2C2P PGW and get back response
    //   //open connection
    //   $ch = curl_init();
    //   curl_setopt($ch,CURLOPT_URL, $this->payment_action_api_host_2c2p);
    //   curl_setopt($ch,CURLOPT_POSTFIELDS, "paymentRequest=".$payload);
    //   curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    //   //execute post
    //   $response = curl_exec($ch); //close connection
    //   curl_close($ch);

    //   //Decrypt response message and display  
    //   $response = $this->decrypt_2c2p($response,SENEROOT."key/cert.crt",SENEROOT."key/private.pem","pwFVhwEf73p6");   
   
    //   //Validate response Hash
    //   $resXml=simplexml_load_string($response); 
    //   $res_version = $resXml->version;
    //   $res_respCode = $resXml->respCode;
    //   $res_processType = $resXml->processType;
    //   $res_invoiceNo = $resXml->invoiceNo;
    //   $res_amount = $resXml->amount;
    //   $res_status = $resXml->status;
    //   $res_approvalCode = $resXml->approvalCode;
    //   $res_referenceNo = $resXml->referenceNo;
    //   $res_transactionDateTime = $resXml->transactionDateTime;
    //   $res_paidAgent = $resXml->paidAgent;
    //   $res_paidChannel = $resXml->paidChannel;
    //   $res_maskedPan = $resXml->maskedPan;
    //   $res_eci = $resXml->eci;
    //   $res_paymentScheme = $resXml->paymentScheme;
    //   $res_processBy = $resXml->processBy;
    //   $res_refundReferenceNo = $resXml->refundReferenceNo;
    //   $res_userDefined1 = $resXml->userDefined1;
    //   $res_userDefined2 = $resXml->userDefined2;
    //   $res_userDefined3 = $resXml->userDefined3;
    //   $res_userDefined4 = $resXml->userDefined4;
    //   $res_userDefined5 = $resXml->userDefined5;
      
    //   //Compute response hash
    //   $res_stringToHash = $res_version.$res_respCode.$res_processType.$res_invoiceNo.$res_amount.$res_status.$res_approvalCode.$res_referenceNo.$res_transactionDateTime.$res_paidAgent.$res_paidChannel.$res_maskedPan.$res_eci.$res_paymentScheme.$res_processBy.$res_refundReferenceNo.$res_userDefined1.$res_userDefined2.$res_userDefined3.$res_userDefined4.$res_userDefined5 ; 
    //   $res_responseHash = strtoupper(hash_hmac('sha1',$res_stringToHash,$this->secretKey_2c2p, false)); //Calculate response Hash Value 

    //   if(strtolower($resXml->hashValue) == strtolower($res_responseHash)){ 
    //     return $resXml; 
    //   } else{
    //     $return = new stdClass();
    //     $return->respCode = 99;
    //     return $return; 
    //   }

    // }

    public function index(){
      //initial
      $dt = $this->__init();

      //default result
      $data = array();
      $data['cards'] = array();

      //check nation_code
      $nation_code = $this->input->get('nation_code');
      $nation_code = $this->nation_check($nation_code);
      if(empty($nation_code)){
        $this->status = 101;
        $this->message = 'Missing or invalid nation_code';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
        die();
      }

      //check apikey
      $apikey = $this->input->get('apikey');
      $c = $this->apikey_check($apikey);
      if(!$c){
        $this->status = 400;
        $this->message = 'Missing or invalid API key';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
        die();
      }

      //check apisess
      $apisess = $this->input->get('apisess');
      $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
      if(!isset($pelanggan->id)){
        $this->status = 401;
        $this->message = 'Missing or invalid API session';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
        die();
      }

      $cards = $this->buc->getAll($nation_code, $pelanggan->id);

      //by Donny Dennison - 28 october 2021 10:48
      //payment call 2c2p in api for flutter version
      foreach($cards AS &$card){
        $card->url = $this->cdn_url($card->url);
      }

      //building response
      $data['cards'] = $cards;

      //response message
      $this->status = 200;
      $this->message = 'Success';

      //render as json
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
    }

    //by Donny Dennison - 11 november 2021 11:11
    //deprecated
    // public function baru(){
    //   //initial
    //   $dt = $this->__init();

    //   //default result
    //   $data = array();

    //   //check nation_code
    //   $nation_code = $this->input->get('nation_code');
    //   $nation_code = $this->nation_check($nation_code);
    //   if(empty($nation_code)){
    //     $this->status = 101;
    //     $this->message = 'Missing or invalid nation_code';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
    //     die();
    //   }

    //   //check apikey
    //   $apikey = $this->input->get('apikey');
    //   $c = $this->apikey_check($apikey);
    //   if(!$c){
    //     $this->status = 400;
    //     $this->message = 'Missing or invalid API key';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
    //     die();
    //   }

    //   //check apisess
    //   $apisess = $this->input->get('apisess');
    //   $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //   if(!isset($pelanggan->id)){
    //     $this->status = 401;
    //     $this->message = 'Missing or invalid API session';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
    //     die();
    //   }

    //   $this->status = 8000;
    //   $this->message = 'One or more parameters are missing.';

    //   //check $jenis
    //   $jenis = $this->input->post('jenis');
    //   if(strlen($jenis)<=0 || empty($jenis)){
    //     $this->status = 8001;
    //     $this->message = 'Card type is not allowed to be empty.';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
    //     die();
    //   }

    //   //check $bank
    //   $bank = $this->input->post('bank');
    //   if(strlen($bank)<=0 || empty($bank)){
    //     $this->status = 8002;
    //     $this->message = 'Bank Name is not allowed to be empty.';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
    //     die();
    //   }

    //   //check $no_telp
    //   $nomor = $this->input->post('nomor');
    //   if(strlen($nomor)<=0 || empty($nomor)){
    //     $this->status = 8003;
    //     $this->message = 'Card Number is not allwed to be empty.';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
    //     die();
    //   }

    //   //check $token_result
    //   $token_result = $this->input->post('token_result');
    //   if(strlen($token_result)<=0 || empty($token_result)){
    //     $this->status = 8004;
    //     $this->message = 'Invalid Token Result';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
    //     die();
    //   }

    //   //by Donny Dennison - 22 april 2021 09:14
    //   //add-void-2c2p-after-add-credit-card
    //   $invoiceno = $this->input->post('invoiceno');

    //   //start transaction
    //   $this->buc->trans_start();

    //   //get last id
    //   $card_id = $this->buc->getLastId($nation_code,$pelanggan->id);

    //   //collect input
    //   $di = array();
    //   $di['nation_code'] = $nation_code;
    //   $di['b_user_id'] = $pelanggan->id;
    //   $di['id'] = $card_id;
    //   $di['jenis'] = $jenis;
    //   $di['bank'] = $bank;
    //   $di['nomor'] = $nomor;
    //   $di['token_result'] = $token_result;
    //   $di['invoiceno'] = $invoiceno;

    //   //insert into database
    //   $res = $this->buc->set($di);

    //   //by Donny Dennison - 22 april 2021 09:14
    //   //add-void-2c2p-after-add-credit-card
    //   if($invoiceno != NULL || $invoiceno != ''){
        
    //     $response = $this->__call2c2pApi($invoiceno, 'V');
        
    //     $this->seme_log->write("api_mobile", 'API_Mobile/Card::Baru -- Void InvoiceNo '. $invoiceno .',Response from 2c2p : '. json_encode($response));

    //     if($response->respCode != 00){

    //       $this->buc->trans_rollback();
    //       $this->status = 8005;
    //       $this->message = 'Failed to insert card data.';

    //       $this->buc->trans_end();

    //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");

    //       die();

    //     }

    //   }

    //   if($res){
    //     $this->status = 200;
    //     $this->message = 'Success';
    //     $this->buc->trans_commit();
    //     $data['card'] = $this->buc->getById($nation_code,$pelanggan->id,$card_id);
    //   }else{
    //     $this->buc->trans_rollback();
    //     $this->status = 8005;
    //     $this->message = 'Failed to insert card data.';
    //   }
    //   $this->buc->trans_end();

    //   //render
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
    // }

    //by Donny Dennison - 11 november 2021 11:11
    //deprecated
    // public function edit(){
    //   //initial
    //   $dt = $this->__init();

    //   //default result
    //   $data = array();

    //   //check nation_code
    //   $nation_code = $this->input->get('nation_code');
    //   $nation_code = $this->nation_check($nation_code);
    //   if(empty($nation_code)){
    //     $this->status = 101;
    //     $this->message = 'Missing or invalid nation_code';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
    //     die();
    //   }

    //   //check apikey
    //   $apikey = $this->input->get('apikey');
    //   $c = $this->apikey_check($apikey);
    //   if(!$c){
    //     $this->status = 400;
    //     $this->message = 'Missing or invalid API key';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
    //     die();
    //   }

    //   //check apisess
    //   $apisess = $this->input->get('apisess');
    //   $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //   if(!isset($pelanggan->id)){
    //     $this->status = 401;
    //     $this->message = 'Missing or invalid API session';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
    //     die();
    //   }

    //   $card_id = (int) $this->input->post('id');
    //   if($card_id<=0){
    //     $this->status = 8006;
    //     $this->message = 'Invalid card ID';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
    //     die();
    //   }

    //   $card = $this->buc->getById($nation_code, $pelanggan->id,$card_id);
    //   if(!isset($card->id)){
    //     $this->status = 8007;
    //     $this->message = 'Card not found or deleted.';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
    //     die();
    //   }

    //   //check $id

    //   //check $jenis
    //   $jenis = $this->input->post('jenis');
    //   if(strlen($jenis)<=0 || empty($jenis)){
    //     $this->status = 8008;
    //     $this->message = 'Card type is not allowed to be empty.';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
    //     die();
    //   }

    //   //check $bank
    //   $bank = $this->input->post('bank');
    //   if(strlen($bank)<=0 || empty($bank)){
    //     $this->status = 8009;
    //     $this->message = 'Bank Name is not allowed to be empty.';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
    //     die();
    //   }

    //   //check $no_telp
    //   $nomor = $this->input->post('nomor');
    //   if(strlen($nomor)<=0 || empty($nomor)){
    //     $this->status = 8010;
    //     $this->message = 'Card Number is not allwed to be empty.';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
    //     die();
    //   }

    //   //check $token_result
    //   $token_result = $this->input->post('token_result');
    //   if(strlen($token_result)<=0){
    //     $this->status = 8011;
    //     $this->message = 'Invalid Token Result';
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
    //     die();
    //   }


    //   //start transaction
    //   $this->buc->trans_start();

    //   //collect input
    //   $du = array();
    //   $du['jenis'] = $jenis;
    //   $du['bank'] = $bank;
    //   $du['nomor'] = $nomor;
    //   $du['token_result'] = $token_result;

    //   //insert into database
    //   $res = $this->buc->update($nation_code,$pelanggan->id,$card_id,$du);
    //   if($res){
    //     $this->status = 200;
    //     $this->message = 'Success';
    //     $this->buc->trans_commit();
    //     $data['card'] = $this->buc->getById($nation_code,$pelanggan->id,$card_id);
    //   }else{
    //     $this->buc->trans_rollback();
    //     $this->status = 8011;
    //     $this->message = 'Failed updating card data';
    //   }
    //   $this->buc->trans_end();
    //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
    // }

    public function hapus(){
      //initial
      $dt = $this->__init();

      //default result
      $data = array();

      //check nation_code
      $nation_code = $this->input->get('nation_code');
      $nation_code = $this->nation_check($nation_code);
      if(empty($nation_code)){
        $this->status = 101;
        $this->message = 'Missing or invalid nation_code';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
        die();
      }

      //check apikey
      $apikey = $this->input->get('apikey');
      $c = $this->apikey_check($apikey);
      if(!$c){
        $this->status = 400;
        $this->message = 'Missing or invalid API key';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
        die();
      }

      //check apisess
      $apisess = $this->input->get('apisess');
      $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
      if(!isset($pelanggan->id)){
        $this->status = 401;
        $this->message = 'Missing or invalid API session';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
        die();
      }

      $card_id = (int) $this->input->post('id');
      if($card_id<=0){
        $this->status = 8012;
        $this->message = 'Invalid Card ID';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
        die();
      }

      //bypassing
      $card = $this->buc->getById($nation_code, $pelanggan->id, $card_id);
      if(!isset($card->id)){
        $this->status = 8013;
        $this->message = 'Card ID not found or deleted';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
        die();
      }

      $res = $this->buc->delete($nation_code,$pelanggan->id,$card_id);
      if($res){
        
        //By Donny Dennison - 14-07-2020 14:04
        //request by mas Rian, return success showing list of the card
        // $this->status = 200;
        // $this->message = 'Success';
        $cards = $this->buc->getAll($nation_code, $pelanggan->id);

        //building response
        $data['cards'] = $cards;

        //response message
        $this->status = 200;
        $this->message = 'Success';
      
      }else{
        $this->status = 8014;
        $this->message = 'Failed deleting card data';
      }

      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
    }

    //by Donny Dennison - 28 october 2021 10:48
    //payment call 2c2p in api for flutter version
    public function type(){
      //initial
      $dt = $this->__init();

      //default result
      $data = array();
      $data['types'] = array();

      //check nation_code
      $nation_code = $this->input->get('nation_code');
      $nation_code = $this->nation_check($nation_code);
      if(empty($nation_code)){
        $this->status = 101;
        $this->message = 'Missing or invalid nation_code';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
        die();
      }

      //check apikey
      $apikey = $this->input->get('apikey');
      $c = $this->apikey_check($apikey);
      if(!$c){
        $this->status = 400;
        $this->message = 'Missing or invalid API key';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
        die();
      }

      //check apisess
      // $apisess = $this->input->get('apisess');
      // $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
      // if(!isset($pelanggan->id)){
      //   $this->status = 401;
      //   $this->message = 'Missing or invalid API session';
      //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
      //   die();
      // }

      $types = $this->buct->getAll($nation_code);

      foreach($types AS &$type){
        $type->url = $this->cdn_url($type->url);
      }

      //building response
      $data['types'] = $types;

      //response message
      $this->status = 200;
      $this->message = 'Success';

      //render as json
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "card");
    }

  }
