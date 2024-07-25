<?php
// class Point_redemption_exchange extends JI_Controller
// {

//     public function __construct()
//     {
//         parent::__construct();
//         $this->lib("seme_log");
//         $this->load("api_cron/b_user_model", 'bu');
//         $this->load("api_cron/b_user_setting_model", "busm");
//         $this->load("api_cron/d_pemberitahuan_model", "dpem");
//         $this->load("api_for_wallet/g_leaderboard_point_history_model", 'glphm');
//         $this->load("api_for_wallet/h_point_redemption_exchange_model", 'hprem');
//         $this->load("api_for_wallet/h_point_redemption_exchange_history_model", 'hprehm');
//     }

//     //credit: https://www.php.net/manual/en/function.com-create-guid.php#119168
//     private function GUIDv4($trim = true)
//     {
//         // Windows
//         if (function_exists('com_create_guid') === true) {
//           if ($trim === true)
//             return trim(com_create_guid(), '{}');
//           else
//             return com_create_guid();
//         }

//         // OSX/Linux
//         if (function_exists('openssl_random_pseudo_bytes') === true) {
//           $data = openssl_random_pseudo_bytes(16);
//           $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
//           $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
//           return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
//         }

//         // Fallback (PHP 4.2+)
//         mt_srand((double)microtime() * 10000);
//         $charid = strtolower(md5(uniqid(rand(), true)));
//         $hyphen = chr(45);                  // "-"
//         $lbrace = $trim ? "" : chr(123);    // "{"
//         $rbrace = $trim ? "" : chr(125);    // "}"
//         $guidv4 = $lbrace.
//                   substr($charid,  0,  8).$hyphen.
//                   substr($charid,  8,  4).$hyphen.
//                   substr($charid, 12,  4).$hyphen.
//                   substr($charid, 16,  4).$hyphen.
//                   substr($charid, 20, 12).
//                   $rbrace;
//         return $guidv4;
//     }

//     // private function __callBlockChainSPTBalance($userWalletCode){

//     //     $ch = curl_init();
//     //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//     //     curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
//     //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//     //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
//     //     curl_setopt($ch, CURLOPT_URL, $this->blockchain_api_host."Wallet/GetSPTBalanceWithEncryption");

//     //     $headers = array();
//     //     $headers[] = 'Content-Type:  application/json';
//     //     $headers[] = 'Accept:  application/json';
//     //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

//     //     $postdata = array(
//     //       'userWalletCode' => $this->__encryptdecrypt($userWalletCode,"encrypt"),
//     //       'countryIsoCode' => $this->blockchain_api_country

//     //     );
//     //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));

//     //     $result = curl_exec($ch);
//     //     if (curl_errno($ch)) {
//     //       return 0;
//     //       //echo 'Error:' . curl_error($ch);
//     //     }
//     //     curl_close($ch);

//     //     $this->seme_log->write("api_mobile", "url untuk block chain server ".$this->blockchain_api_host."Wallet/GetSPTBalanceWithEncryption. data send to blockchain api ". json_encode($postdata).". isi response block chain server ". $result);

//     //     return $result;

//     // }

//     // private function __encryptdecrypt($text, $type="encrypt"){

//     //     if($type == "encrypt"){

//     //         // Encrypt using the public key
//     //         openssl_public_encrypt($text, $encrypted, $this->blockchain_api_public_key);

//     //         return base64_encode($encrypted);

//     //     }else if($type == "decrypt"){

//     //         // Decrypt the data using the private key
//     //         openssl_private_decrypt(base64_decode($text), $decrypted, openssl_pkey_get_private($this->blockchain_api_private_key, $this->blockchain_api_private_key_password));

//     //         return $decrypted;

//     //     }

//     // }

//     // public function index()
//     // {
//     //     //default result
//     //     $data = array();
//     //     $data["list"] = array();

//     //     //response message
//     //     $this->status = 200;
//     //     $this->message = 'Success';

//     //     //check nation_code
//     //     $nation_code = $this->input->get('nation_code');
//     //     $nation_code = $this->nation_check($nation_code);
//     //     if (empty($nation_code)) {
//     //         $this->status = 101;
//     //         $this->message = 'Missing or invalid nation_code';
//     //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
//     //         die();
//     //     }

//     //     $postData = $this->apikeyDecrypt($nation_code, $data, $this->input->post('samsung'), $this->input->post('nvidia'), $this->input->post('fullhd'));
//     //     if ($postData === false) {
//     //         $this->status = 1750;
//     //         $this->message = 'Please check your data again';
//     //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
//     //         die();
//     //     }
//     //     $postData = json_decode($postData);

//     //     $listOfPostData = array(
//     //         "apikey",
//     //         "apisess"
//     //     );

//     //     foreach($listOfPostData as $value) {
//     //         if(!isset($postData->$value)){
//     //             $postData->$value = "";
//     //         }
//     //     }

//     //     //check apikey
//     //     $apikey = $postData->apikey;
//     //     $c = $this->apikey_check($apikey);
//     //     if (!$c) {
//     //         $this->status = 400;
//     //         $this->message = 'Missing or invalid API key';
//     //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
//     //         die();
//     //     }

//     //     //check apisess
//     //     $apisess = $postData->apisess;
//     //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
//     //     if (!isset($pelanggan->id)) {
//     //       $this->status = 401;
//     //       $this->message = 'Missing or invalid API session';
//     //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
//     //       die();
//     //     }

//     //     $data["list"] = $this->hprem->getAll($nation_code, $pelanggan->id);
//     //     // foreach($data["list"] as &$value){
//     //         // $value->icon = str_replace("//", "/", $value->icon);
//     //         // $value->icon = $this->cdn_url($value->icon);
//     //     // }

//     //     //render as json
//     //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "point_redemption_exchange");
//     // }

//     public function walletresult()
//     {
//         //default result
//         $data = array();

//         //response message
//         // $this->status = 1001;
//         // $this->message = 'Failed';
//         $this->status = 200;
//         $this->message = 'Success';

//         $nation_code = "62";

//         $post_data_json = file_get_contents('php://input');
//         $this->seme_log->write("api_for_wallet", 'point_redemption_exchange/walletresult, POST DATA = '. $post_data_json);
//         $post_data = json_decode($post_data_json);
//         if (!isset($post_data->transactionList)) {
//             $this->status = 1001;
//             $this->message = 'Failed';
//             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "point_redemption_exchange");
//             die();
//         }
//         if (!is_array($post_data->transactionList)) {
//             $this->status = 1001;
//             $this->message = 'Failed';
//             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "point_redemption_exchange");
//             die();
//         }
//         if (count($post_data->transactionList)<=0) {
//             $this->status = 1001;
//             $this->message = 'Failed';
//             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "point_redemption_exchange");
//             die();
//         }

//         foreach ($post_data->transactionList as $transList) {
//             $pointHistoryData = $this->glphm->getByMaintransactionidDetailtransactionid($nation_code, $transList->mainTransactionId, $transList->detailTransactionId);
//             if(!isset($pointHistoryData->custom_id)){
//                 continue;
//             }

//             $exchangeDetail = $this->hprem->getById($nation_code, $pointHistoryData->custom_id);
//             if($exchangeDetail->status == "approved by admin"){
//                 $user = $this->bu->getById($nation_code, $exchangeDetail->b_user_id);
//                 // if($user->language_id == 2){
//                 //     if($exchangeDetail->status == "request exchange"){
//                 //         $exchangeDetail->status = "pertukaran permintaan";
//                 //     }

//                 //     if($exchangeDetail->status == "rejected by admin" || $exchangeDetail->status == "rejected by system"){
//                 //         $exchangeDetail->status = "ditolak admin";
//                 //     }

//                 //     if($exchangeDetail->status == "approved by admin"){
//                 //         $exchangeDetail->status = "disetujui admin";
//                 //     }

//                 //     if($exchangeDetail->status == "insufficient wallet balance"){
//                 //         $exchangeDetail->status = "saldo wallet tidak cukup";
//                 //     }

//                 //     if($exchangeDetail->status == "wallet balance deducted"){
//                 //         $exchangeDetail->status = "saldo wallet telah dikurangi";
//                 //     }

//                 //     if($exchangeDetail->status == "top up problem"){
//                 //         $exchangeDetail->status = "isi ulang bermasalah";
//                 //     }

//                 //     if($exchangeDetail->status == "wallet balance refunded"){
//                 //         $exchangeDetail->status = "saldo wallet telah dikembalikan";
//                 //     }

//                 //     if($exchangeDetail->status == "success"){
//                 //         $exchangeDetail->status = "berhasil";
//                 //     }
//                 // }

//                 if($transList->isSuccess == true){
//                     $di = array();
//                     $di['status'] = "wallet balance deducted";
//                     $this->hprem->update($nation_code, $exchangeDetail->id,$di);

//                     $du = array();
//                     $du['nation_code'] = $nation_code;
//                     $du['h_point_redemption_exchange_id'] = $exchangeDetail->id;
//                     $du['status'] = $di['status'];

//                     $endDoWhile = 0;
//                     do{
//                       $du['id'] = $this->GUIDv4();
//                       $checkId = $this->hprehm->checkId($nation_code, $du['id']);
//                       if($checkId == 0){
//                           $endDoWhile = 1;
//                       }
//                     }while($endDoWhile == 0);
//                     $this->hprehm->set($du);
//                 }

//                 if($transList->isSuccess == false){
//                     $di = array();
//                     $di['status'] = "insufficient wallet balance";
//                     $this->hprem->update($nation_code, $exchangeDetail->id,$di);

//                     $du = array();
//                     $du['nation_code'] = $nation_code;
//                     $du['h_point_redemption_exchange_id'] = $exchangeDetail->id;
//                     $du['status'] = $di['status'];

//                     $endDoWhile = 0;
//                     do{
//                       $du['id'] = $this->GUIDv4();
//                       $checkId = $this->hprehm->checkId($nation_code, $du['id']);
//                       if($checkId == 0){
//                           $endDoWhile = 1;
//                       }
//                     }while($endDoWhile == 0);
//                     $this->hprehm->set($du);

//                     $dpe = array();
//                     $dpe['nation_code'] = $nation_code;
//                     $dpe['b_user_id'] = $exchangeDetail->b_user_id;
//                     $dpe['id'] = $this->dpem->getLastId($nation_code, $exchangeDetail->b_user_id);
//                     $dpe['type'] = "point_redemption_exchange";
//                     if($user->language_id == 2) {
//                       $dpe['judul'] = "Point Redemption Exchange";
//                       $dpe['teks'] =  "Maaf, saldo wallet anda tidak cukup.";
//                     } else {
//                       $dpe['judul'] = "Point Redemption Exchange";
//                       $dpe['teks'] =  "Sorry, your wallet balance is insufficient.";
//                     }

//                     $dpe['gambar'] = 'media/pemberitahuan/promotion.png';
//                     $dpe['cdate'] = "NOW()";
//                     $extras = new stdClass();
//                     $extras->id = $exchangeDetail->id;
//                     // $extras->title = $community->title;
//                     if($user->language_id == 2) { 
//                       $extras->judul = "Point Redemption Exchange";
//                       $extras->teks =  "Maaf, saldo wallet anda tidak cukup.";
//                     } else {
//                       $extras->judul = "Point Redemption Exchange";
//                       $extras->teks =  "Sorry, your wallet balance is insufficient.";
//                     }

//                     $dpe['extras'] = json_encode($extras);
//                     $this->dpem->set($dpe);

//                     $classified = 'setting_notification_user';
//                     $code = 'U6';

//                     $receiverSettingNotif = $this->busm->getValue($nation_code, $exchangeDetail->b_user_id, $classified, $code);
//                     if (!isset($receiverSettingNotif->setting_value)) {
//                         $receiverSettingNotif->setting_value = 0;
//                     }

//                     if ($receiverSettingNotif->setting_value == 1 && $user->is_active == 1) {
//                       if($user->device == "ios"){
//                         $device = "ios";
//                       }else{
//                         $device = "android";
//                       }

//                       $tokens = $user->fcm_token; //device token
//                       if(!is_array($tokens)) $tokens = array($tokens);
//                       if($user->language_id == 2){
//                         $title = "Point Redemption Exchange";
//                         $message = "Maaf, saldo wallet anda tidak cukup.";
//                       } else {
//                         $title = "Point Redemption Exchange";
//                         $message = "Sorry, your wallet balance is insufficient.";
//                       }

//                       $image = 'media/pemberitahuan/promotion.png';
//                       $type = 'point_redemption_exchange';
//                       $payload = new stdClass();
//                       $payload->id = $exchangeDetail->id;
//                       // $payload->title = html_entity_decode($community->title,ENT_QUOTES);
//                       if($user->language_id == 2) {
//                         $payload->judul = "Point Redemption Exchange";
//                         $payload->teks = "Maaf, saldo wallet anda tidak cukup.";
//                       } else {
//                         $payload->judul = "Point Redemption Exchange";
//                         $payload->teks = "Sorry, your wallet balance is insufficient.";
//                       }

//                       $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
//                     }
//                 }
//             }
//         }

//         //render as json
//         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "point_redemption_exchange");
//     }
// }
