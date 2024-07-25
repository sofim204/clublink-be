<?php
class Every_min extends JI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_cron/b_user_model", "bu");
        $this->load("api_mobile/b_user_alamat_model", "bua");
        $this->load("api_cron/g_convert_history_model", 'gchm');
        $this->load("api_cron/g_leaderboard_point_history_model", 'glphm');
        $this->load("api_cron/d_pemberitahuan_model", "dpem");
        $this->load("api_mobile/h_point_redemption_exchange_user_influencer_model", "hpreuim");
    }

    //credit: https://www.php.net/manual/en/function.com-create-guid.php#119168
    private function GUIDv4($trim = true)
    {
        // Windows
        if (function_exists('com_create_guid') === true) {
            if ($trim === true)
                return trim(com_create_guid(), '{}');
            else
                return com_create_guid();
        }

        // OSX/Linux
        if (function_exists('openssl_random_pseudo_bytes') === true) {
            $data = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }

        // Fallback (PHP 4.2+)
        mt_srand((double)microtime() * 10000);
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);                  // "-"
        $lbrace = $trim ? "" : chr(123);    // "{"
        $rbrace = $trim ? "" : chr(125);    // "}"
        $guidv4 = $lbrace.
                substr($charid,  0,  8).$hyphen.
                substr($charid,  8,  4).$hyphen.
                substr($charid, 12,  4).$hyphen.
                substr($charid, 16,  4).$hyphen.
                substr($charid, 20, 12).
                $rbrace;
        return $guidv4;
    }

    // private function __callBlockChainCreateWallet($dateLog, $postdata){
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    //     curl_setopt($ch, CURLOPT_URL, $this->blockchain_api_host."Wallet/CreateWalletsWithEncryption");

    //     $headers = array();
    //     $headers[] = 'Content-Type:  application/json';
    //     $headers[] = 'Accept:  application/json';
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //     // $postdata = array(
    //     //   'userWalletCode' => $this->__encryptdecrypt($userWalletCode,"encrypt"),
    //     //   'countryIsoCode' => $this->blockchain_api_country,
    //     //   'isReferralSignUp' => ($referralUserWalletCode == "") ? false : true,
    //     //   'referralUserWalletCode' => ($referralUserWalletCode == "") ? "" : $this->__encryptdecrypt($referralUserWalletCode,"encrypt")
    //     // );
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));

    //     $result = curl_exec($ch);
    //     if (curl_errno($ch)) {
    //       return 0;
    //       //echo 'Error:' . curl_error($ch);
    //     }
    //     curl_close($ch);

    //     $this->seme_log->write("api_cron", $dateLog." API_Cron/Every_min::index -- url untuk block chain server ". $this->blockchain_api_host."Wallet/CreateWalletsWithEncryption. data send to blockchain api ". json_encode($postdata).". isi response block chain server ". $result);
    //     return $result;
    // }

    private function __callBlockChainCreateWalletNew($dateLog, $postdata){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_URL, $this->blockchain_new_api_host."api/CreateWallets");

        $headers = array();
        $headers[] = 'Content-Type:  application/json';
        $headers[] = 'Accept:  application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
          return 0;
          //echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        $this->seme_log->write("api_cron", $dateLog." API_Cron/Every_min::index -- url untuk block chain server ". $this->blockchain_new_api_host."api/CreateWallets. data send to blockchain api ". json_encode($postdata).". isi response block chain server ". $result);
        return $result;
    }

    // private function __callBlockChainLateReferralRewardTransaction($dateLog, $postdata){
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    //     curl_setopt($ch, CURLOPT_URL, $this->blockchain_api_host."Wallet/LateReferralRewardTransaction");

    //     $headers = array();
    //     $headers[] = 'Content-Type:  application/json';
    //     $headers[] = 'Accept:  application/json';
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //     // $postdata = array(
    //     //   'mainTransactionId' => $mainTransactionId,
    //     //   'userWalletCode' => $this->__encryptdecrypt($userWalletCode,"encrypt"),
    //     //   'countryIsoCode' => $this->blockchain_api_country,
    //     //   'referralUserWalletCode' => $this->__encryptdecrypt($referralUserWalletCode,"encrypt"),
    //     //   'referralCountryIsoCode' => $this->blockchain_api_country
    //     // );
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));

    //     $result = curl_exec($ch);
    //     if (curl_errno($ch)) {
    //       return 0;
    //       //echo 'Error:' . curl_error($ch);
    //     }
    //     curl_close($ch);

    //     $this->seme_log->write("api_cron", $dateLog." API_Cron/Every_min::index -- url untuk block chain server ". $this->blockchain_api_host."Wallet/LateReferralRewardTransaction. data send to blockchain api ". json_encode($postdata).". isi response block chain server ". $result);
    //     return $result;
    // }

    //credit :
    //https://stackoverflow.com/a/35289156/7578520
    //https://stackoverflow.com/a/29560553/7578520
    // private function __encryptdecrypt($text, $type="encrypt"){
    //     if($type == "encrypt"){
    //         // Encrypt using the public key
    //         openssl_public_encrypt($text, $encrypted, $this->blockchain_api_public_key);
    //         return base64_encode($encrypted);
    //     }else if($type == "decrypt"){
    //         // Decrypt the data using the private key
    //         openssl_private_decrypt(base64_decode($text), $decrypted, openssl_pkey_get_private($this->blockchain_api_private_key, $this->blockchain_api_private_key_password));
    //         return $decrypted;
    //     }
    // }

    private function __callBlockChainMiningTransactionHistory($dateLog, $postdata){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_URL, $this->blockchain_new_api_host."api/MiningTransactionHistory");

        $headers = array();
        $headers[] = 'Content-Type:  application/json';
        $headers[] = 'Accept:  application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
          return 0;
          //echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        $this->seme_log->write("api_cron", $dateLog." API_Cron/Every_min::index -- url untuk block chain server ". $this->blockchain_new_api_host."api/MiningTransactionHistory. data send to blockchain api ". json_encode($postdata).". isi response block chain server ". $result);
        return $result;
    }

    // public function index()
    // {
    //     $dateLog = date("Y-m-d H:i:s");

    //     $this->seme_log->write("api_cron", $dateLog." API_Cron/Every_min::index start");

    //     //start transaction
    //     // $this->order->trans_start();

    //     $nation_code = 62;

    //     $userList = $this->bu->getByBlockChainCreateUserWalletApiHaventCalled($nation_code);
    //     if(count($userList) > 0){
    //         foreach ($userList as $user) {
    //             $du = array("blockchain_createuserwallet_api_called" => 2);
    //             $this->bu->update($user->id, $du);
    //         }
    //         unset($user);

    //         $postdata = array();
    //         $b_user_id_list = array();
    //         foreach ($userList as $user) {
    //             if($user->b_user_id_recruiter != '0' && $user->main_transaction_id == ""){
    //                 $recruiterData = $this->bu->getById($nation_code, $user->b_user_id_recruiter);
    //             }else{
    //                 $recruiterData = new stdClass();
    //                 $recruiterData->user_wallet_code = "";
    //             }

    //             $given = new DateTime($user->cdate);
    //             $given->setTimezone(new DateTimeZone("UTC"));
    //             $user->cdate = str_replace(" ","T",$given->format("Y-m-d H:i:s"))."Z";

    //             $postdata[] = array(
    //               'userWalletCode' => $this->__encryptdecrypt($user->user_wallet_code,"encrypt"),
    //               'countryIsoCode' => $this->blockchain_api_country,
    //               'isReferralSignUp' => ($recruiterData->user_wallet_code == "") ? false : true,
    //               'referralUserWalletCode' => ($recruiterData->user_wallet_code == "") ? "" : $this->__encryptdecrypt($recruiterData->user_wallet_code,"encrypt"),
    //               'signupUtcDate' => $user->cdate
    //             );
    //             $b_user_id_list[] = $user->id;
    //         }
    //         unset($user);

    //         $postdata = array(
    //             "userWalletList" => $postdata
    //         );

    //         $responseWalletApi = 0;
    //         $response = json_decode($this->__callBlockChainCreateWallet($dateLog, $postdata));
    //         if(isset($response->responseCode)){
    //             if($response->responseCode == 0){
    //                 $responseWalletApi = 1;
    //             }
    //         }
    //         unset($response);

    //         if($responseWalletApi == 0){
    //             $du = array("blockchain_createuserwallet_api_called" => 0);
    //             $this->bu->updateMass($b_user_id_list, $du);
    //         }

    //         if($responseWalletApi == 1){
    //             $du = array("blockchain_createuserwallet_api_called" => 1);
    //             $this->bu->updateMass($b_user_id_list, $du);
    //         }
    //     }
    //     unset($userList, $b_user_id_list);

    //     $userList = $this->bu->getByBlockChainLateReferralRewardTransactionApiHaventCalled($nation_code);
    //     if(count($userList) > 0){
    //         foreach ($userList as $user) {
    //             $du = array("blockchain_latereferralrewardtransaction_api_called" => 2);
    //             $this->bu->update($user->id, $du);
    //         }
    //         unset($user);

    //         $postdata = array();
    //         $b_user_id_list = array();
    //         foreach ($userList as $user) {
    //             $recruiterData = $this->bu->getById($nation_code, $user->b_user_id_recruiter);

    //             $given = new DateTime($user->cdate);
    //             $given->setTimezone(new DateTimeZone("UTC"));
    //             $user->cdate = str_replace(" ","T",$given->format("Y-m-d H:i:s"))."Z";

    //             $postdata[] = array(
    //               'mainTransactionId' => $user->main_transaction_id,
    //               'userWalletCode' => $this->__encryptdecrypt($user->user_wallet_code,"encrypt"),
    //               'countryIsoCode' => $this->blockchain_api_country,
    //               'referralUserWalletCode' => $this->__encryptdecrypt($recruiterData->user_wallet_code,"encrypt"),
    //               'referralCountryIsoCode' => $this->blockchain_api_country,
    //               'signupUtcDate' => $user->cdate

    //             );
    //             $b_user_id_list[] = $user->id;
    //         }
    //         unset($userList, $recruiterData, $user);

    //         $postdata = array(
    //             "rewardTransactionList" => $postdata
    //         );

    //         $responseWalletApi = 0;
    //         $response = json_decode($this->__callBlockChainLateReferralRewardTransaction($dateLog, $postdata));
    //         if(isset($response->responseCode)){
    //             if($response->responseCode == 0){
    //                 $responseWalletApi = 1;
    //             }
    //         }

    //         if($responseWalletApi == 1){
    //             $du = array("blockchain_latereferralrewardtransaction_api_called" => 1, "blockchain_latereferralrewardtransaction_api_called_cdate" => 'NOW()');
    //         }else{
    //             $du = array("blockchain_latereferralrewardtransaction_api_called" => 0);
    //         }
    //         $this->bu->updateMass($b_user_id_list, $du);
    //     }
    //     unset($userList, $b_user_id_list);

    //     //close transaction
    //     // $this->order->trans_end();

    //     $this->seme_log->write("api_cron", $dateLog." API_Cron/Every_min::index stop");
    //     die();
    // }

    public function index()
    {
        $dateLog = date("Y-m-d H:i:s");

        $this->seme_log->write("api_cron", $dateLog." API_Cron/Every_min::index start");

        //start transaction
        // $this->order->trans_start();

        $nation_code = 62;

        $userList = $this->bu->getByBlockChainCreateUserWalletNewApiHaventCalled($nation_code);
        if(count($userList) > 0){
            foreach ($userList as $user) {
                $du = array("user_wallet_code_new_api_called" => 2);
                $this->bu->update($user->id, $du);
            }
            unset($user);

            $postdata = array();
            $b_user_id_list = array();
            foreach ($userList as $user) {
                if($user->language_id == 2){
                    $language = 'id';
                }else{
                    $language = 'en';
                }

                // check is influencer or not | Muhammad Sofi - 13 February 2024
                $checkInfluencer = $this->hpreuim->getInfluencerById($user->id);
                if(isset($checkInfluencer->b_user_id)){
                    $is_influencer = "1";
                } else {
                    $is_influencer = "0";
                }

                $postdata[] = array(
                    'userWalletCode' => $user->user_wallet_code_new,
                    'countryIsoCode' => strtolower($this->blockchain_api_country),
                    'LanguageIsoCode' => $language,
                    'signupUtcDate' => $user->cdate,
                    'sellonEmail' => $user->email,
                    'sellonPhoneNumber' => $user->telp,
                    'sellonUsername' => $user->fnama,
                    'isInfluencer' => $is_influencer,
                    'registerFrom' => $user->register_from,
                );
                $b_user_id_list[] = $user->id;
            }
            unset($user);

            $postdata = array(
                "userWalletList" => $postdata
            );

            // $responseWalletApi = 0;
            $response = json_decode($this->__callBlockChainCreateWalletNew($dateLog, $postdata));
            // if(isset($response->responseCode)){
            //     if($response->responseCode == 0){
            //         $responseWalletApi = 1;
            //     }
            // }
            // unset($response);

            // if($responseWalletApi == 0){
            //     $du = array("user_wallet_code_new_api_called" => 0);
            //     $this->bu->updateMass($b_user_id_list, $du);
            // }

            // if($responseWalletApi == 1){
                $du = array("user_wallet_code_new_api_called" => 1);
                $this->bu->updateMass($b_user_id_list, $du);
            // }
        }
        unset($userList, $b_user_id_list);

        //close transaction
        // $this->order->trans_end();

        $this->seme_log->write("api_cron", $dateLog." API_Cron/Every_min::index stop");
        die();
    }

    public function oldUser()
    {
        $dateLog = date("Y-m-d H:i:s");

        $this->seme_log->write("api_cron", $dateLog." API_Cron/Every_min/oldUser::index start");

        //start transaction
        // $this->order->trans_start();

        $nation_code = 62;
        $page = $this->input->get('page');
        $page_size = $this->input->get('page_size');

        $userList = $this->bu->getByBlockChainCreateUserWalletNewApiOldUser($nation_code, $page, $page_size);
        if(count($userList) > 0){
            // foreach ($userList as $user) {
            //     $du = array("user_wallet_code_new_api_called" => 2);
            //     $this->bu->update($user->id, $du);
            // }
            // unset($user);

            // if($pelanggan->language_id == 2){
                $language = 'id';
            // }else{
            //     $language = 'en';
            // }

            $postdata = array();
            $b_user_id_list = array();
            foreach ($userList as $user) {
                $postdata[] = array(
                  'userWalletCode' => $user->user_wallet_code_new,
                  'countryIsoCode' => strtolower($this->blockchain_api_country),
                  'LanguageIsoCode' => $language,
                  'signupUtcDate' => $user->cdate
                );
                $b_user_id_list[] = $user->id;
            }
            unset($user);

            $postdata = array(
                "userWalletList" => $postdata
            );

            // $responseWalletApi = 0;
            $response = json_decode($this->__callBlockChainCreateWalletNew($dateLog, $postdata));
            // if(isset($response->responseCode)){
            //     if($response->responseCode == 0){
            //         $responseWalletApi = 1;
            //     }
            // }
            // unset($response);

            // if($responseWalletApi == 0){
            //     $du = array("user_wallet_code_new_api_called" => 0);
            //     $this->bu->updateMass($b_user_id_list, $du);
            // }

            // if($responseWalletApi == 1){
                // $du = array("user_wallet_code_new_api_called" => 1);
                // $this->bu->updateMass($b_user_id_list, $du);
            // }
        }
        unset($userList, $b_user_id_list);

        //close transaction
        // $this->order->trans_end();

        $this->seme_log->write("api_cron", $dateLog." API_Cron/Every_min/oldUser::index stop");
        die();
    }

    public function convertresult()
    {
        $dateLog = date("Y-m-d H:i:s");

        $this->seme_log->write("api_cron", $dateLog." API_Cron/Every_min/convertresult::index start");

        $nation_code = "62";

        $transactionProcessList = $this->gchm->getAllStatusProcessing($nation_code);
        if(count($transactionProcessList) == 0){
            $this->seme_log->write("api_cron", $dateLog." API_Cron/Every_min/convertresult::index stop");
            die();
        }

        $postdata = array();
        foreach ($transactionProcessList as $transList) {
            $postdata[] = array(
              'miningTransactionId' => $transList->id
            );
        }
        unset($transList);

        $postdata = array(
            "miningTransactionIdList" => $postdata
        );

        $response = json_decode($this->__callBlockChainMiningTransactionHistory($dateLog, $postdata));
        if (!isset($response->transactionList)) {
            $this->seme_log->write("api_cron", $dateLog." API_Cron/Every_min/convertresult::index stop");
            die();
        }
        if (!is_array($response->transactionList)) {
            $this->seme_log->write("api_cron", $dateLog." API_Cron/Every_min/convertresult::index stop");
            die();
        }
        if (count($response->transactionList) == 0) {
            $this->seme_log->write("api_cron", $dateLog." API_Cron/Every_min/convertresult::index stop");
            die();
        }

        foreach ($response->transactionList as $transList) {
            $historyData = $this->gchm->getById($nation_code, strtolower($transList->historyid));
            if(!isset($historyData->id)){
                continue;
            }

            if($historyData->status == "processing"){
                if($transList->isSuccess == true){
                    $di = array();
                    $di['transactionid'] = $transList->transaction_id;
                    $di['status'] = "completed";
                    $di['completed_date'] = $transList->completed_date;
                    $this->gchm->update($nation_code, $historyData->id,$di);

                    $pelanggan = $this->bu->getById($nation_code, $historyData->b_user_id);
                    $dpe = array();
                    $dpe['nation_code'] = $nation_code;
                    $dpe['b_user_id'] = $historyData->b_user_id;
                    $dpe['id'] = $this->dpem->getLastId($nation_code, $historyData->b_user_id);
                    $dpe['type'] = "convert_spt";
                    $dpe['judul'] = "Exchange Token";
                    if($pelanggan->language_id == 2) {
                        $dpe['teks'] = "Anda berhasil menukarkan ".$historyData->amount_bbt." BBT.";
                    } else {
                        $dpe['teks'] = "You successfully exchanged ".$historyData->amount_bbt." BBT.";
                    }

                    $dpe['gambar'] = 'media/pemberitahuan/community.png';
                    $dpe['cdate'] = "NOW()";
                    $extras = new stdClass();
                    // $extras->id = $c_community_id;
                    $dpe['extras'] = json_encode($extras);
                    $this->dpem->set($dpe);

                    if ($pelanggan->is_active == 1) {
                        if($pelanggan->device == "ios"){
                            $device = "ios";
                        }else{
                            $device = "android";
                        }

                        $tokens = $pelanggan->fcm_token; //device token
                        if(!is_array($tokens)) $tokens = array($tokens);
                        $title = "Exchange Token";
                        if($pelanggan->language_id == 2){
                            $message = "Anda berhasil menukarkan ".$historyData->amount_bbt." BBT.";
                        } else {
                            $message = "You successfully exchanged ".$historyData->amount_bbt." BBT.";
                        }

                        $image = 'media/pemberitahuan/promotion.png';
                        $type = 'convert_spt';
                        $payload = new stdClass();
                        // $payload->id = $c_community_id;
                        $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
                    }
                }

                if($transList->isSuccess == false){
                    $di = array();
                    $di['status'] = "failed";
                    $this->gchm->update($nation_code, $historyData->id,$di);

                    $pelanggan = $this->bu->getById($nation_code, $historyData->b_user_id);

                    $di = array();
                    $di['nation_code'] = $nation_code;
                    $di['b_user_alamat_location_kelurahan'] = "All";
                    $di['b_user_alamat_location_kecamatan'] = "All";
                    $di['b_user_alamat_location_kabkota'] = "All";
                    $di['b_user_alamat_location_provinsi'] = "All";
                    $di['b_user_id'] = $pelanggan->id;
                    $di['plusorminus'] = "+";
                    $di['point'] = $historyData->amount_spt;
                    $di['custom_id'] = $historyData->id;
                    $di['custom_type'] = 'convert';
                    $di['custom_type_sub'] = "spt to bbt";
                    $di['custom_text'] = $di['custom_type_sub'].' '.$di['custom_type'].' failed and refund '.$di['point'].' point(s)';
                    $endDoWhile = 0;
                    do{
                      $leaderBoardHistoryId = $this->GUIDv4();
                      $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
                      if($checkId == 0){
                        $endDoWhile = 1;
                      }
                    }while($endDoWhile == 0);
                    $di['id'] = $leaderBoardHistoryId;
                    $this->glphm->set($di);
                }
            }
        }

        $this->seme_log->write("api_cron", $dateLog." API_Cron/Every_min/convertresult::index stop");
        die();
    }
}
