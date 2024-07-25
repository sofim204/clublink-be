<?php
class Leaderboard_ranking extends JI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_cron/common_code_model", "ccm");
        // $this->load("api_cron/g_general_location_highlight_status_model", 'gglhsm');
        // $this->load("api_cron/g_leaderboard_point_area_model", 'glpam');
        $this->load("api_cron/g_leaderboard_point_total_model", 'glptm');
        // $this->load("api_cron/g_leaderboard_ranking_model", 'glrm');
        $this->load("api_cron/g_leaderboard_point_history_model", 'glphm');

        //by Donny Dennison - 30 september 2022 10:49
        //integrate api blockchain
        $this->load("api_cron/b_user_model", 'bu');
        $this->load("api_for_wallet/h_point_redemption_exchange_model", 'hprem');
        $this->load("api_for_wallet/h_point_redemption_exchange_history_model", 'hprehm');
        $this->load("api_cron/d_pemberitahuan_model", "dpem");
        $this->load("api_cron/b_user_setting_model", "busm");
    }

    //START by Donny Dennison - 30 september 2022 10:49
    //integrate api blockchain
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

    // private function __callBlockChainActiveRewardTransaction($postdata){

    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    //     curl_setopt($ch, CURLOPT_URL, $this->blockchain_api_host."Wallet/ActiveRewardTransaction");

    //     $headers = array();
    //     $headers[] = 'Content-Type:  application/json';
    //     $headers[] = 'Accept:  application/json';
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //     // $postData = array(
    //     //   'userWalletCode' => $this->__encryptdecrypt($userWalletCode,"encrypt"),
    //     //   'countryIsoCode' => $this->blockchain_api_country

    //     // );
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));

    //     $result = curl_exec($ch);
    //     if (curl_errno($ch)) {
    //       return 0;
    //       //echo 'Error:' . curl_error($ch);
    //     }
    //     curl_close($ch);
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
    //END by Donny Dennison - 30 september 2022 10:49
    //integrate api blockchain

    public function index()
    {
        //open transaction
        // $this->glrm->trans_start();

        //change log filename
        // $this->seme_log->changeFilename("cron.log");

        $dateLog = date("Y-m-d H:i:s");

        //put on log
        $this->seme_log->write("api_cron", $dateLog." API_Cron/leaderboard_ranking::index Start");

        $nation_code = 62;

        //check leaderboard ranking cron is still running or not
        $cronStatus = $this->ccm->getByClassifiedAndCode($nation_code, "cron_config", "HA");
        if (!isset($cronStatus->remark)) {
          $cronStatus->remark = "running";
        }

        if($cronStatus->remark == "running"){
            $split = explode(",",$cronStatus->codename);
            $timeNow = date("Y-m-d H:i:s");
            $timeInCodeName = date("Y-m-d H:i:s",strtotime($split[1]." +30 minutes"));
            if(strtotime($timeNow) > strtotime($timeInCodeName)){
                $cronStatus->remark = "stop";
            }
        }

        if($cronStatus->remark == "stop"){
            $du = array();
            $du["remark"] = "running";
            $du["codename"] = "Leaderboard Ranking,".date("Y-m-d H:i:s");
            $this->ccm->update($nation_code, 200, $du);

            $stuckList = $this->glphm->getAllStuck($nation_code);
            if(count($stuckList) > 0){
                foreach ($stuckList as $history) {
                    $totalPoint = 0;
                    $totalPost = 0;

                    $dataNow = $this->glptm->getByUserId($nation_code, $history->b_user_id);
                    if(!isset($dataNow->b_user_id)){
                        //create point
                        $di = array();
                        $di['nation_code'] = $nation_code;
                        $di['b_user_id'] = $history->b_user_id;
                        $di['total_post'] = 0;
                        $di['total_point'] = 0;
                        $this->glptm->set($di);
                    }
                    unset($dataNow);

                    if($history->plusorminus == "-"){
                        $totalPoint += $history->point;
                    }else{
                        $totalPoint -= $history->point;
                    }

                    if(($history->custom_type == "community" && $history->custom_type_sub == "post") || ($history->custom_type == "product" && $history->custom_type_sub == "post")){
                        if($history->plusorminus == "-"){
                            $totalPost += 1;
                        }else{
                            $totalPost -= 1;
                        }
                    }

                    // if($totalPoint < 0){
                    //     $totalPoint = 0;
                    // }

                    // if($totalPost < 0){
                    //     $totalPost = 0;
                    // }

                    // $du = array();
                    // $du["total_point"] = $totalPoint;
                    // $du["total_post"] = $totalPost;
                    // $this->glptm->update($nation_code, $history->b_user_id, $du);
                    $this->glptm->updateTotal($nation_code, $history->b_user_id, "total_point", "+", $totalPoint);
                    $this->glptm->updateTotal($nation_code, $history->b_user_id, "total_post", "+", $totalPost);

                    $du = array();
                    $du["is_calculated"] = "0";
                    // $du["main_transaction_id"] = "NULL";
                    // $du["detail_transaction_id"] = "NULL";
                    $this->glphm->update($nation_code, $history->id, $du);
                }
            }
            unset($stuckList);

            //calculate total point and total post user
            $limitDay = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EL");
            if (!isset($limitDay->remark)) {
              $limitDay->remark = 3;
            }

            $historyPointAll = $this->glphm->getAll($nation_code, "", "", "", "", "", "", "", "", "", date("Y-m-d",strtotime("-".$limitDay->remark." days")));

            $b_user_id = '0';
            $totalPoint = 0;
            $totalPost = 0;

            if(count($historyPointAll) == 0){
                // $this->sendpendingtransaction();

                $du = array();
                $du["remark"] = "stop";
                $this->ccm->update($nation_code, 200, $du);

                $this->seme_log->write("api_cron", $dateLog." API_Cron/leaderboard_ranking:: no new point, no reranking and changed status to stop");
                die();
            }

            foreach($historyPointAll AS $key=>$hp){
                if($hp->b_user_id != $b_user_id){
                    if($b_user_id != '0'){
                        $dataNow = $this->glptm->getByUserId($nation_code, $b_user_id);
                        if(!isset($dataNow->b_user_id)){
                            //create point
                            $di = array();
                            $di['nation_code'] = $nation_code;
                            $di['b_user_id'] = $b_user_id;
                            $di['total_post'] = 0;
                            $di['total_point'] = 0;
                            $this->glptm->set($di);
                        }
                        unset($dataNow);

                        // $totalPoint += $dataNow->total_point;
                        // if($totalPoint < 0){
                        //     $totalPoint = 0;
                        // }

                        // $totalPost += $dataNow->total_post;
                        // if($totalPost < 0){
                        //     $totalPost = 0;
                        // }

                        // $du = array();
                        // $du["total_point"] = $totalPoint;
                        // $du["total_post"] = $totalPost;
                        // $this->glptm->update($nation_code, $b_user_id, $du);
                        $this->glptm->updateTotal($nation_code, $b_user_id, "total_point", "+", $totalPoint);
                        $this->glptm->updateTotal($nation_code, $b_user_id, "total_post", "+", $totalPost);
                    }
                    $b_user_id = $hp->b_user_id;
                    $totalPoint = 0;
                    $totalPost = 0;
                }

                if($hp->custom_type == "point redemption" && $hp->custom_type_sub == "credit phone"){
                    $dataNow = $this->glptm->getByUserId($nation_code, $b_user_id);
                    if(!isset($dataNow->b_user_id)){
                        //create point
                        $di = array();
                        $di['nation_code'] = $nation_code;
                        $di['b_user_id'] = $b_user_id;
                        $di['total_post'] = 0;
                        $di['total_point'] = 0;
                        $this->glptm->set($di);
                        $dataNow = $this->glptm->getByUserId($nation_code, $b_user_id);
                    }

                    $totalPointTemp = $dataNow->total_point + $totalPoint;
                    if($totalPointTemp >= $hp->point){
                        $di = array();
                        $di['status'] = "wallet balance deducted";
                        $this->hprem->update($nation_code, $hp->custom_id,$di);

                        $du = array();
                        $du['nation_code'] = $nation_code;
                        $du['h_point_redemption_exchange_id'] = $hp->custom_id;
                        $du['status'] = $di['status'];
                        $endDoWhile = 0;
                        do{
                            $du['id'] = $this->GUIDv4();
                            $checkId = $this->hprehm->checkId($nation_code, $du['id']);
                            if($checkId == 0){
                                $endDoWhile = 1;
                            }
                        }while($endDoWhile == 0);
                        $this->hprehm->set($du);
                    }else{
                        $di = array();
                        $di['status'] = "insufficient wallet balance";
                        $this->hprem->update($nation_code, $hp->custom_id,$di);

                        $du = array();
                        $du['nation_code'] = $nation_code;
                        $du['h_point_redemption_exchange_id'] = $hp->custom_id;
                        $du['status'] = $di['status'];
                        $endDoWhile = 0;
                        do{
                            $du['id'] = $this->GUIDv4();
                            $checkId = $this->hprehm->checkId($nation_code, $du['id']);
                            if($checkId == 0){
                                $endDoWhile = 1;
                            }
                        }while($endDoWhile == 0);
                        $this->hprehm->set($du);

                        $user = $this->bu->getById($nation_code, $b_user_id);

                        $dpe = array();
                        $dpe['nation_code'] = $nation_code;
                        $dpe['b_user_id'] = $b_user_id;
                        $dpe['id'] = $this->dpem->getLastId($nation_code, $b_user_id);
                        $dpe['type'] = "point_redemption_exchange";
                        if($user->language_id == 2) {
                            $dpe['judul'] = "Point Redemption Exchange";
                            $dpe['teks'] =  "Maaf, saldo wallet anda tidak cukup.";
                        } else {
                            $dpe['judul'] = "Point Redemption Exchange";
                            $dpe['teks'] =  "Sorry, your wallet balance is insufficient.";
                        }

                        $dpe['gambar'] = 'media/pemberitahuan/promotion.png';
                        $dpe['cdate'] = "NOW()";
                        $extras = new stdClass();
                        $extras->id = $hp->custom_id;
                        // $extras->title = $community->title;
                        if($user->language_id == 2) { 
                            $extras->judul = "Point Redemption Exchange";
                            $extras->teks =  "Maaf, saldo wallet anda tidak cukup.";
                        } else {
                            $extras->judul = "Point Redemption Exchange";
                            $extras->teks =  "Sorry, your wallet balance is insufficient.";
                        }

                        $dpe['extras'] = json_encode($extras);
                        $this->dpem->set($dpe);

                        $classified = 'setting_notification_user';
                        $code = 'U6';

                        $receiverSettingNotif = $this->busm->getValue($nation_code, $b_user_id, $classified, $code);
                        if (!isset($receiverSettingNotif->setting_value)) {
                            $receiverSettingNotif->setting_value = 0;
                        }

                        if ($receiverSettingNotif->setting_value == 1 && $user->is_active == 1) {
                          if($user->device == "ios"){
                            $device = "ios";
                          }else{
                            $device = "android";
                          }

                          $tokens = $user->fcm_token; //device token
                          if(!is_array($tokens)) $tokens = array($tokens);
                          if($user->language_id == 2){
                            $title = "Point Redemption Exchange";
                            $message = "Maaf, saldo wallet anda tidak cukup.";
                          } else {
                            $title = "Point Redemption Exchange";
                            $message = "Sorry, your wallet balance is insufficient.";
                          }

                          $image = 'media/pemberitahuan/promotion.png';
                          $type = 'point_redemption_exchange';
                          $payload = new stdClass();
                          $payload->id = $hp->custom_id;
                          // $payload->title = html_entity_decode($community->title,ENT_QUOTES);
                          if($user->language_id == 2) {
                            $payload->judul = "Point Redemption Exchange";
                            $payload->teks = "Maaf, saldo wallet anda tidak cukup.";
                          } else {
                            $payload->judul = "Point Redemption Exchange";
                            $payload->teks = "Sorry, your wallet balance is insufficient.";
                          }

                          $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
                        }

                        $du = array();
                        $du["is_calculated"] = "2";
                        $du["blockchain_api_called"] = "1";
                        $this->glphm->update($nation_code, $hp->id, $du);
                        // unset($historyPointAll[$key]);
                        continue;
                    }
                }
                unset($dataNow, $totalPointTemp);

                if($hp->plusorminus == "-"){
                    $totalPoint -= $hp->point;
                }else{
                    $totalPoint += $hp->point;
                }

                if(($hp->custom_type == "community" && $hp->custom_type_sub == "post") || ($hp->custom_type == "product" && $hp->custom_type_sub == "post")){
                    if($hp->plusorminus == "-"){
                        $totalPost -= 1;
                    }else{
                        $totalPost += 1;
                    }
                }

                $du = array();
                $du["is_calculated"] = "1";
                $du["blockchain_api_called"] = "1";
                $this->glphm->update($nation_code, $hp->id, $du);
            }
            unset($hp);

            if($b_user_id != '0'){
                $dataNow = $this->glptm->getByUserId($nation_code, $b_user_id);
                if(!isset($dataNow->b_user_id)){
                    //create point
                    $di = array();
                    $di['nation_code'] = $nation_code;
                    $di['b_user_id'] = $b_user_id;
                    $di['total_post'] = 0;
                    $di['total_point'] = 0;
                    $this->glptm->set($di);
                }
                unset($dataNow);

                // $totalPoint += $dataNow->total_point;
                // if($totalPoint < 0){
                //     $totalPoint = 0;
                // }

                // $totalPost += $dataNow->total_post;
                // if($totalPost < 0){
                //     $totalPost = 0;
                // }

                // $du = array();
                // $du["total_point"] = $totalPoint;
                // $du["total_post"] = $totalPost;
                // $this->glptm->update($nation_code, $b_user_id, $du);
                $this->glptm->updateTotal($nation_code, $b_user_id, "total_point", "+", $totalPoint);
                $this->glptm->updateTotal($nation_code, $b_user_id, "total_post", "+", $totalPost);
            }
            $this->seme_log->write("api_cron", $dateLog." API_Cron/leaderboard_ranking::calculate total point and total post user done");

            // // $areas = $this->gglhsm->getAll($nation_code);
            // // if($areas){
    		// 	// foreach ($areas as $area) {
            //         // if($area->provinsi == "All"){
            //             $type = "All";
            //         // }else if($area->kabkota == "All"){
            //             // $type = "province";
            //         // }else if($area->kecamatan == "All"){
            //             // $type = "city";
            //         // }else if($area->kelurahan == "All"){
            //             // $type = "district";
            //         // }else{
            //             // $type = "neighborhood";
            //         // }

            //         // $rankingList = $this->glptm->getAll($nation_code, 0, 0, $area->kelurahan, $area->kecamatan, $area->kabkota, $area->provinsi, $type);
            //         $rankingList = $this->glptm->getAll($nation_code, 0, 0);

            //         $insertArray = array();
            //         foreach($rankingList AS $list){
            //             $di = array();
            //             $di['nation_code'] = $nation_code;
            //             $di['id'] = $list->ranking;
            //             // $di['b_user_alamat_location_kelurahan'] = $area->kelurahan;
            //             // $di['b_user_alamat_location_kecamatan'] = $area->kecamatan;
            //             // $di['b_user_alamat_location_kabkota'] = $area->kabkota;
            //             // $di['b_user_alamat_location_provinsi'] = $area->provinsi;
            //             $di['b_user_alamat_location_kelurahan'] = "All";
            //             $di['b_user_alamat_location_kecamatan'] = "All";
            //             $di['b_user_alamat_location_kabkota'] = "All";
            //             $di['b_user_alamat_location_provinsi'] = "All";
            //             $di['b_user_id'] = $list->b_user_id;
            //             $di['type'] = "old";
            //             $di['total_post'] = $list->total_post;
            //             $di['total_point'] = $list->total_point;
            //             $insertArray[] = $di;
            //         }
            //         unset($rankingList, $list);

            //         if(count($insertArray) > 0){
            //             //delete ranking
            //             // $this->glrm->delAll($nation_code, $area->kelurahan, $area->kecamatan, $area->kabkota, $area->provinsi);
            //             $this->glrm->delAll($nation_code, "All", "All", "All", "All");

            //             $chunkInsertArray = array_chunk($insertArray,50);
            //             foreach($chunkInsertArray AS $chunk){
            //                 //insert multi
            //                 $this->glrm->setMass($chunk);
            //             }
            //             unset($chunkInsertArray, $chunk);
            //         }
            //         unset($insertArray);

            //         // //update new to old
            //         // $di = array();
            //         // $di['type'] = "old";
            //         // // $this->glrm->update($nation_code, "new", $area->kelurahan, $area->kecamatan, $area->kabkota, $area->provinsi, $di);
            //         // $this->glrm->update($nation_code, "new", "All", "All", "All", "All", $di);
    	    //     // }
    	    //     // unset($area);
            // // }
            // // unset($areas);
            // $this->seme_log->write("api_cron", $dateLog." API_Cron/leaderboard_ranking::reranking user done");

            //START by Donny Dennison - 30 september 2022 10:49
            //integrate api blockchain
            // if(count($historyPointAll) > 0){
            //     $endDoWhile = 0;
            //     do{
            //         $main_transaction_id = $this->GUIDv4();
            //         $existInDB = $this->glphm->checkAlreadyInDB($nation_code, "", "", "", "", "", "", "", "", "", "",$main_transaction_id);
            //         if(!isset($existInDB->id)){
            //             $endDoWhile = 1;
            //         }
            //     }while($endDoWhile == 0);

            //     $previous_b_user_id = '0';
            //     $b_user_id = '0';
            //     $previousRewardTypeNo = 0;
            //     $previousRewardSubTypeNo = 0;
            //     $rewardTypeNo = 0;
            //     $rewardSubTypeNo = 0;

            //     $endDoWhile = 0;
            //     do{
            //         $detail_transaction_id = $this->GUIDv4();
            //         $existInDB = $this->glphm->checkAlreadyInDB($nation_code, "", "", "", "", "", "", "", "", "", "", "", $detail_transaction_id);
            //         if(!isset($existInDB->id)){
            //             $endDoWhile = 1;
            //         }
            //     }while($endDoWhile == 0);

            //     foreach($historyPointAll AS $hp){
            //         if($hp->custom_type == "product"){
            //             $rewardTypeNo = 1;
            //             if($hp->custom_type_sub == "post" || $hp->custom_type_sub == "takedown post"){
            //                 $rewardSubTypeNo = 1;
            //             }else if($hp->custom_type_sub == "reply"){
            //                 $rewardSubTypeNo = 2;
            //             }else if($hp->custom_type_sub == "share"){
            //                 $rewardSubTypeNo = 3;
            //             }else if($hp->custom_type_sub == "upload video" || $hp->custom_type_sub == "takedown video"){
            //                 $rewardSubTypeNo = 5;
            //             }
            //         }else if($hp->custom_type == "community"){
            //             $rewardTypeNo = 2;
            //             if($hp->custom_type_sub == "post" && $hp->point == 100){
            //                 $rewardSubTypeNo = 13;
            //             }else if($hp->custom_type_sub == "post" || $hp->custom_type_sub == "takedown post"){
            //                 $rewardSubTypeNo = 1;
            //             }else if($hp->custom_type_sub == "reply"){
            //                 $rewardSubTypeNo = 2;
            //             }else if($hp->custom_type_sub == "share"){
            //                 $rewardSubTypeNo = 3;
            //             }else if($hp->custom_type_sub == "more than"){
            //                 $rewardSubTypeNo = 4;
            //             }else if($hp->custom_type_sub == "upload video" || $hp->custom_type_sub == "takedown video"){
            //                 $rewardSubTypeNo = 5;
            //             }else if($hp->custom_type_sub == "like"){
            //                 $rewardSubTypeNo = 6;
            //             }else if($hp->custom_type_sub == "takedown post(first time)"){
            //                 $rewardSubTypeNo = 13;
            //             }else if($hp->custom_type_sub == "upload image" || $hp->custom_type_sub == "takedown image"){
            //                 $rewardSubTypeNo = 16;
            //             }else if($hp->custom_type_sub == "post(double point)" || $hp->custom_type_sub == "takedown post(double point)"){
            //                 $rewardSubTypeNo = 17;
            //             }else if($hp->custom_type_sub == "upload video(double point)" || $hp->custom_type_sub == "takedown video(double point)"){
            //                 $rewardSubTypeNo = 18;
            //             }else if($hp->custom_type_sub == "upload image(double point)" || $hp->custom_type_sub == "takedown image(double point)"){
            //                 $rewardSubTypeNo = 19;
            //             }
            //         }else if($hp->custom_type == "offer"){
            //             if($hp->custom_type_sub == "review"){
            //                 $rewardTypeNo = 3;
            //             }else if($hp->custom_type_sub == "review free product"){
            //                 $rewardTypeNo = 7;
            //             }
            //             $rewardSubTypeNo = 7;
            //         }else if($hp->custom_type == "order"){
            //             $rewardTypeNo = 4;
            //             if($hp->custom_type_sub == "review"){
            //                 $rewardSubTypeNo = 7;
            //             }
            //         }else if($hp->custom_type == "check in"){
            //             $rewardTypeNo = 5;
            //             if($hp->custom_type_sub == "daily"){
            //                 $rewardSubTypeNo = 8;
            //             }else if($hp->custom_type_sub == "weekly"){
            //                 $rewardSubTypeNo = 9;
            //             }else if($hp->custom_type_sub == "monthly"){
            //                 $rewardSubTypeNo = 10;
            //             }
            //         }else if($hp->custom_type == "game" || $hp->custom_type == "rock paper scissors" || $hp->custom_type == "shooting fire"){
            //             $rewardTypeNo = 8;
            //             if($hp->custom_type_sub == "BuyTicket"){
            //                 $rewardSubTypeNo = 1;
            //             }else if($hp->custom_type == "shooting fire" && $hp->custom_type_sub == "win"){
            //                 $rewardSubTypeNo = 3;
            //             }else if($hp->custom_type_sub == "win"){
            //                 $rewardSubTypeNo = 2;
            //             }
            //         }else if($hp->custom_type == "point redemption"){
            //             $rewardTypeNo = 9;
            //             if($hp->custom_type_sub == "credit phone"){
            //                 $rewardSubTypeNo = 1;
            //             }else if($hp->custom_type_sub == "electricity token"){
            //                 $rewardSubTypeNo = 2;
            //             }
            //         }

            //         if($previousRewardTypeNo == 0 && $previousRewardSubTypeNo == 0){
            //             $previousRewardTypeNo = $rewardTypeNo;
            //             $previousRewardSubTypeNo = $rewardSubTypeNo;
            //         }

            //         if($previous_b_user_id != '0' && ($previousRewardTypeNo != $rewardTypeNo || $previousRewardSubTypeNo != $rewardSubTypeNo)){
            //             $endDoWhile = 0;
            //             do{
            //                 $detail_transaction_id = $this->GUIDv4();
            //                 $existInDB = $this->glphm->checkAlreadyInDB($nation_code, "", "", "", "", "", "", "", "", "", "", "", $detail_transaction_id);
            //                 if(!isset($existInDB->id)){
            //                     $endDoWhile = 1;
            //                 }
            //             }while($endDoWhile == 0);
            //             $previousRewardTypeNo = $rewardTypeNo;
            //             $previousRewardSubTypeNo = $rewardSubTypeNo;
            //         }

            //         if($hp->b_user_id != $b_user_id){
            //             if($previous_b_user_id != '0'){
            //                 $endDoWhile = 0;
            //                 do{
            //                     $detail_transaction_id = $this->GUIDv4();
            //                     $existInDB = $this->glphm->checkAlreadyInDB($nation_code, "", "", "", "", "", "", "", "", "", "", "", $detail_transaction_id);
            //                     if(!isset($existInDB->id)){
            //                         $endDoWhile = 1;
            //                     }
            //                 }while($endDoWhile == 0);
            //                 $previousRewardTypeNo = $rewardTypeNo;
            //                 $previousRewardSubTypeNo = $rewardSubTypeNo;
            //             }
            //             $previous_b_user_id = $hp->b_user_id;
            //             $b_user_id = $hp->b_user_id;
            //         }

            //         $du = array();
            //         $du["main_transaction_id"] = $main_transaction_id;
            //         $du["detail_transaction_id"] = $detail_transaction_id;
            //         $this->glphm->update($nation_code, $hp->id, $du);
            //     }
            //     unset($historyPointAll, $hp);

            //     $this->seme_log->write("api_cron", $dateLog." API_Cron/leaderboard_ranking::new mainTransactionId finish");
            // }else{
            //     $this->seme_log->write("api_cron", $dateLog." API_Cron/leaderboard_ranking::no new mainTransactionId");
            // }

            // $this->sendpendingtransaction();

            $du = array();
            $du["remark"] = "stop";
            $this->ccm->update($nation_code, 200, $du);

            $this->seme_log->write("api_cron", $dateLog." API_Cron/leaderboard_ranking::changed status to stop");
        }else{
            $this->seme_log->write("api_cron", $dateLog." API_Cron/leaderboard_ranking::previous cron is still running");
        }

        //end transacation
        // $this->glrm->trans_end();

        $this->seme_log->write("api_cron", $dateLog." API_Cron/leaderboard_ranking::index Stop");
        die();
    }

    // public function sendpendingtransaction(){
    //     $dateLog = date("Y-m-d H:i:s");

    //     //put on log
    //     $this->seme_log->write("api_cron", $dateLog." API_Cron/leaderboard_ranking/sendpendingtransaction::index Start");

    //     $nation_code = 62;

    //     //check leaderboard ranking cron is still running or not
    //     // $cronStatus = $this->ccm->getByClassifiedAndCode($nation_code, "cron_config", "HB");
    //     // if (!isset($cronStatus->remark)) {
    //     //   $cronStatus->remark = "stop";
    //     // }

    //     // if($cronStatus->remark == "stop"){
    //     //     $du = array();
    //     //     $du["remark"] = "running";
    //     //     $this->ccm->update($nation_code, 201, $du);

    //         // $maxDate = $this->ccm->getByClassifiedAndCode($nation_code, "cron_config", "HC");
    //         // if (!isset($maxDate->remark)) {
    //         //   $maxDate->remark = "2022-12-28";
    //         // }

    //         $mainTransactionIds = $this->glphm->getMainTransactionIdForApiBlockChain($nation_code, "", "", "", "", "", "", "", "", "");
    //         $tempMainTransactionIds =array();
    //         foreach($mainTransactionIds as $main){
    //             $tempMainTransactionIds[] = $main->main_transaction_id;
    //         }
    //         $mainTransactionIds = array_values($tempMainTransactionIds);
    //         if(count($mainTransactionIds) > 0){
    //             $postData = array();
    //             $postData["mainTransactionIdList"] = array();
    //             $rewardUserList = array();
    //             $rewardDetailsList = array();
    //             $b_user_id = '0';
    //             $mainTransactionId = "";
    //             $detailTransactionId = "";
    //             $rewardTypeNo = 0;
    //             $rewardSubTypeNo = 0;
    //             $activityCount = 0;
    //             $totalRewardAmount = 0;
    //             $mainTransactionIdTrue = array();
    //             $mainTransactionIdZeroReward = array();

    //             $getDataForApiBlockChain = $this->glphm->getDataForApiBlockChainByMainTransactionId($nation_code, "", "", "", "", "", "", "", "", "", $mainTransactionIds);
    //             foreach($getDataForApiBlockChain AS $dataForApi){
    //                 if($mainTransactionId == ""){
    //                     $mainTransactionId = $dataForApi->main_transaction_id;
    //                     $detailTransactionId = $dataForApi->detail_transaction_id;
    //                     $b_user_id = $dataForApi->b_user_id;
    //                 }

    //                 if($detailTransactionId != $dataForApi->detail_transaction_id){
    //                     if($totalRewardAmount != 0){
    //                         $rewardDetailsList[] = array(
    //                             "detailTransactionId" => $detailTransactionId,
    //                             "rewardTypeNo" => $rewardTypeNo,
    //                             "rewardSubTypeNo" => $rewardSubTypeNo,
    //                             "activityCount" => $activityCount,
    //                             "totalRewardAmount" => $totalRewardAmount
    //                         );
    //                     }
    //                     $detailTransactionId = $dataForApi->detail_transaction_id;
    //                     $activityCount = 0;
    //                     $totalRewardAmount = 0;
    //                 }

    //                 if($mainTransactionId != $dataForApi->main_transaction_id || $b_user_id != $dataForApi->b_user_id){
    //                     if(count($rewardDetailsList) > 0){
    //                         $userData = $this->bu->getById($nation_code, $b_user_id);
    //                         $rewardUserList[] = array(
    //                             "userWalletCode" => $this->__encryptdecrypt($userData->user_wallet_code,"encrypt"),
    //                             "countryIsoCode" => $this->blockchain_api_country,
    //                             "rewardDetailsList" => $rewardDetailsList
    //                         );
    //                     }
    //                     $rewardDetailsList = array();
    //                     $b_user_id = $dataForApi->b_user_id;
    //                 }

    //                 if($mainTransactionId != $dataForApi->main_transaction_id){
    //                     if(count($rewardUserList) > 0){
    //                         $postData["mainTransactionIdList"][] = array(
    //                             "mainTransactionId" => $mainTransactionId,
    //                             "rewardUserList" => $rewardUserList
    //                         );
    //                         $mainTransactionIdTrue[] = $mainTransactionId;
    //                     }else{
    //                         $mainTransactionIdZeroReward[] = $mainTransactionId;
    //                     }
    //                     $rewardUserList = array();
    //                     $mainTransactionId = $dataForApi->main_transaction_id;
    //                 }

    //                 if($dataForApi->custom_type == "product"){
    //                     $rewardTypeNo = 1;
    //                     if($dataForApi->custom_type_sub == "post" || $dataForApi->custom_type_sub == "takedown post"){
    //                         $rewardSubTypeNo = 1;
    //                     }else if($dataForApi->custom_type_sub == "reply"){
    //                         $rewardSubTypeNo = 2;
    //                     }else if($dataForApi->custom_type_sub == "share"){
    //                         $rewardSubTypeNo = 3;
    //                     }else if($dataForApi->custom_type_sub == "upload video" || $dataForApi->custom_type_sub == "takedown video"){
    //                         $rewardSubTypeNo = 5;
    //                     }
    //                 }else if($dataForApi->custom_type == "community"){
    //                     $rewardTypeNo = 2;
    //                     if($dataForApi->custom_type_sub == "post" && $dataForApi->point == 100){
    //                         $rewardSubTypeNo = 13;
    //                     }else if($dataForApi->custom_type_sub == "post" || $dataForApi->custom_type_sub == "takedown post"){
    //                         $rewardSubTypeNo = 1;
    //                     }else if($dataForApi->custom_type_sub == "reply"){
    //                         $rewardSubTypeNo = 2;
    //                     }else if($dataForApi->custom_type_sub == "share"){
    //                         $rewardSubTypeNo = 3;
    //                     }else if($dataForApi->custom_type_sub == "more than"){
    //                         $rewardSubTypeNo = 4;
    //                     }else if($dataForApi->custom_type_sub == "upload video" || $dataForApi->custom_type_sub == "takedown video"){
    //                         $rewardSubTypeNo = 5;
    //                     }else if($dataForApi->custom_type_sub == "like"){
    //                         $rewardSubTypeNo = 6;
    //                     }else if($dataForApi->custom_type_sub == "takedown post(first time)"){
    //                         $rewardSubTypeNo = 13;
    //                     }else if($dataForApi->custom_type_sub == "upload image" || $dataForApi->custom_type_sub == "takedown image"){
    //                         $rewardSubTypeNo = 16;
    //                     }else if($dataForApi->custom_type_sub == "post(double point)" || $dataForApi->custom_type_sub == "takedown post(double point)"){
    //                         $rewardSubTypeNo = 17;
    //                     }else if($dataForApi->custom_type_sub == "upload video(double point)" || $dataForApi->custom_type_sub == "takedown video(double point)"){
    //                         $rewardSubTypeNo = 18;
    //                     }else if($dataForApi->custom_type_sub == "upload image(double point)" || $dataForApi->custom_type_sub == "takedown image(double point)"){
    //                         $rewardSubTypeNo = 19;
    //                     }
    //                 }else if($dataForApi->custom_type == "offer"){
    //                     if($dataForApi->custom_type_sub == "review"){
    //                         $rewardTypeNo = 3;
    //                     }else if($dataForApi->custom_type_sub == "review free product"){
    //                         $rewardTypeNo = 7;
    //                     }
    //                     $rewardSubTypeNo = 7;
    //                 }else if($dataForApi->custom_type == "order"){
    //                     $rewardTypeNo = 4;
    //                     if($dataForApi->custom_type_sub == "review"){
    //                         $rewardSubTypeNo = 7;
    //                     }
    //                 }else if($dataForApi->custom_type == "check in"){
    //                     $rewardTypeNo = 5;
    //                     if($dataForApi->custom_type_sub == "daily"){
    //                         $rewardSubTypeNo = 8;
    //                     }else if($dataForApi->custom_type_sub == "weekly"){
    //                         $rewardSubTypeNo = 9;
    //                     }else if($dataForApi->custom_type_sub == "monthly"){
    //                         $rewardSubTypeNo = 10;
    //                     }
    //                 }else if($dataForApi->custom_type == "game" || $dataForApi->custom_type == "rock paper scissors" || $dataForApi->custom_type == "shooting fire"){
    //                     $rewardTypeNo = 8;
    //                     if($dataForApi->custom_type_sub == "BuyTicket"){
    //                         $rewardSubTypeNo = 1;
    //                     }else if($dataForApi->custom_type == "shooting fire" && $dataForApi->custom_type_sub == "win"){
    //                         $rewardSubTypeNo = 3;
    //                     }else if($dataForApi->custom_type_sub == "win"){
    //                         $rewardSubTypeNo = 2;
    //                     }
    //                 }else if($dataForApi->custom_type == "point redemption"){
    //                     $rewardTypeNo = 9;
    //                     if($dataForApi->custom_type_sub == "credit phone"){
    //                         $rewardSubTypeNo = 1;
    //                     }else if($dataForApi->custom_type_sub == "electricity token"){
    //                         $rewardSubTypeNo = 2;
    //                     }
    //                 }

    //                 if($dataForApi->plusorminus == "-"){
    //                     $activityCount -= 1;
    //                 }else{
    //                     $activityCount += 1;
    //                 }

    //                 if($dataForApi->plusorminus == "-"){
    //                     $totalRewardAmount -= $dataForApi->point;
    //                 }else{
    //                     $totalRewardAmount += $dataForApi->point;
    //                 }
    //             }

    //             if($totalRewardAmount != 0){
    //                 $rewardDetailsList[] = array(
    //                     "detailTransactionId" => $detailTransactionId,
    //                     "rewardTypeNo" => $rewardTypeNo,
    //                     "rewardSubTypeNo" => $rewardSubTypeNo,
    //                     "activityCount" => $activityCount,
    //                     "totalRewardAmount" => $totalRewardAmount
    //                 );
    //             }

    //             if(count($rewardDetailsList) > 0){
    //                 $userData = $this->bu->getById($nation_code, $b_user_id);
    //                 $rewardUserList[] = array(
    //                     "userWalletCode" => $this->__encryptdecrypt($userData->user_wallet_code,"encrypt"),
    //                     "countryIsoCode" => $this->blockchain_api_country,
    //                     "rewardDetailsList" => $rewardDetailsList
    //                 );
    //             }

    //             if(count($rewardUserList) > 0){
    //                 $postData["mainTransactionIdList"][] = array(
    //                     "mainTransactionId" => $mainTransactionId,
    //                     "rewardUserList" => $rewardUserList
    //                 );
    //                 $mainTransactionIdTrue[] = $mainTransactionId;
    //             }else if($mainTransactionId != ""){
    //                 $mainTransactionIdZeroReward[] = $mainTransactionId;
    //             }
    //             unset($rewardDetailsList, $rewardUserList);

    //             if(count($postData["mainTransactionIdList"]) > 0){
    //                 $this->seme_log->write("api_cron", $dateLog." API_Cron/leaderboard_ranking/sendpendingtransaction::data send to blockchain api: ".json_encode($postData));

    //                 $response = json_decode($this->__callBlockChainActiveRewardTransaction($postData));

    //                 $this->seme_log->write("api_cron", $dateLog." API_Cron/leaderboard_ranking/sendpendingtransaction::response blockchain api: ".json_encode($response));

    //                 if(isset($response->responseCode)){
    //                     if($response->responseCode == 0){
    //                         $du = array();
    //                         $du["blockchain_api_called"] = 1;
    //                         $this->glphm->updateByMainTransactionId($nation_code, $mainTransactionIdTrue,$du);
    //                     }
    //                 }
    //                 $this->seme_log->write("api_cron", $dateLog." API_Cron/leaderboard_ranking/sendpendingtransaction::send to blockchain api done");
    //             }else{
    //                 $this->seme_log->write("api_cron", $dateLog." API_Cron/leaderboard_ranking/sendpendingtransaction::no data so dont send to blockchain api");
    //             }

    //             if(count($mainTransactionIdZeroReward) > 0){
    //                 $du = array();
    //                 $du["blockchain_api_called"] = 2;
    //                 $this->glphm->updateByMainTransactionId($nation_code, $mainTransactionIdZeroReward,$du);
    //             }
    //         }

    //         // $du = array();
    //         // $du["remark"] = "stop";
    //         // $this->ccm->update($nation_code, 201, $du);

    //         // $this->seme_log->write("api_cron", $dateLog." API_Cron/leaderboard_ranking/sendpendingtransaction::reranking done and changed status to stop");
    //     // }else{
    //     //     $this->seme_log->write("api_cron", $dateLog." API_Cron/leaderboard_ranking/sendpendingtransaction::previous cron is still running");
    //     // }

    //     $this->seme_log->write("api_cron", $dateLog." API_Cron/leaderboard_ranking/sendpendingtransaction::index Stop");
    //     return 0;
    // }
}
