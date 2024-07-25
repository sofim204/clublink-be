<?php

// require_once (SENEROOT.'/vendor/autoload.php');
// use GuzzleHttp\Client;
// use GuzzleHttp\Exception\ClientException;
// use GuzzleHttp\Promise;
// use Psr\Http\Message\ResponseInterface;
// use GuzzleHttp\Exception\RequestException;

class Blockchain extends JI_Controller{

    public function __construct(){
        parent:: __construct();
        $this->lib("seme_log");
        $this->load("api_mobile/b_user_model","bu");
        $this->load("api_mobile/common_code_model", 'ccm');
        $this->load("api_mobile/g_wallet_access_schedule_model", 'gwasm');
        $this->load("api_mobile/d_pemberitahuan_model", "dpem");
        $this->load("api_mobile/h_point_redemption_exchange_user_influencer_model", "hpreuim");
    }

    // private function __callBlockChainAccessToken($userWalletCode, $id_banner, $language_id){
    //     $dateBefore = date("Y-m-d H:i:s");

    //     // $ch = curl_init();
    //     // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     // curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    //     // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    //     // curl_setopt($ch, CURLOPT_URL, $this->blockchain_api_host."User/GetAccessTokenWithEncryption");

    //     // $headers = array();
    //     // $headers[] = 'Content-Type:  application/json';
    //     // $headers[] = 'Accept:  application/json';
    //     // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //     $postdata = json_encode(array(
    //         'userWalletCode' => $this->__encryptdecrypt($userWalletCode,"encrypt"),
    //         'countryIsoCode' => $this->blockchain_api_country,
    //         'languageIsoCode' => ($language_id == 2) ? "id" : "en",
    //         'externalBannerId' => $id_banner
    //     ));
    //     // curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);

    //     // $result = curl_exec($ch);
    //     // if (curl_errno($ch)) {
    //     //   return 0;
    //     //   //echo 'Error:' . curl_error($ch);
    //     // }
    //     // curl_close($ch);

    //     try {
    //         $client = new Client([
    //             'base_uri' => $this->blockchain_api_host,
    //             'headers' => array(
    //                 "Content-Type" => "application/json",
    //                 "Accept" => "application/json"
    //             ),
    //             // default timeout 5 detik
    //             'timeout'  => 5,
    //         ]);

    //         //https://stackoverflow.com/a/54624802/7578520
    //         $promise = $client->postAsync("User/GetAccessTokenWithEncryption", ['body'=>$postdata])->then(
    //             function (ResponseInterface $res) {
    //                 $response = $res->getBody()->getContents();

    //                 return $response;
    //             }
    //         );
    //         $result = $promise->wait();
    //         // echo $result;

    //         $this->seme_log->write("api_mobile", "sebelum panggil api ".$dateBefore.", url untuk block chain server ". $this->blockchain_api_host."User/GetAccessTokenWithEncryption. data send to blockchain api ". $postdata.". isi response block chain server ". $result);

    //         return $result;
    //     } catch (ClientException $e) {
    //         $this->seme_log->write("api_mobile", "sebelum panggil api ".$dateBefore.", url untuk block chain server ". $this->blockchain_api_host."User/GetAccessTokenWithEncryption. data send to blockchain api ". $postdata.". isi response block chain server ". $e->getMessage());

    //         return $e->getMessage();
    //     }
    // }

    private function __callBlockChainAccessTokenNew($postdata){
        $dateBefore = date("Y-m-d H:i:s");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_URL, $this->blockchain_new_api_host."api/GetAccessToken");

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

        $this->seme_log->write("api_mobile", "sebelum panggil api ".$dateBefore.", url untuk block chain server ". $this->blockchain_new_api_host."api/GetAccessToken. data send to blockchain api ". json_encode($postdata).". isi response block chain server ". $result);
        return $result;
    }

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

    public function index(){
        // //init
        // $dt = $this->__init();

        // //default result format
        // $data = array();
        // $data['kondisi'] = array();

        // //check nation_code
        // $nation_code = $this->input->get('nation_code');
        // $nation_code = $this->nation_check($nation_code);
        // if(empty($nation_code)){
        //   $this->status = 101;
        //   $this->message = 'Missing or invalid nation_code';
        //   $this->__json_out($data);
        //   die();
        // }

        // //check apikey
        // $apikey = $this->input->get('apikey');
        // $c = $this->apikey_check($apikey);
        // if(!$c){
        //   $this->status = 400;
        //   $this->message = 'Missing or invalid API key';
        //   $this->__json_out($data);
        //   die();
        // }

        // $this->status = 200;
        // $this->message = "Success";

        // $data = array();
        // $data['berat'] = $this->bbm->get($nation_code);
        // foreach($data['berat'] as &$berat){
        //   if(isset($berat->icon)){
        //     if(strlen($berat->icon)<=4){
        //       $berat->icon = 'media/icon/default-icon.png';
        //     }
        //     $berat->icon = base_url($berat->icon);
        //   }
        // }
        // $this->status = 200;
        // $this->message = "Success";
        // $this->__json_out($data);
    }

    public function accesstoken()
    {
        //init
        $dt = $this->__init();

        //default result format
        $data = array();
        $data['url'] = "";
        $data['isOpenWallet'] = "open";
        $data['isOpenMessage'] = "close";
        $data['message'] = "";

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "blockchain");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data);
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "blockchain");
            die();
        }

        $wallet_active = "off";
        $wallet_active = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C7");
        if (isset($wallet_active->remark)) {
            $wallet_active = $wallet_active->remark;
        }

        if ($wallet_active == "off") {
            $this->status = 1001;
            $this->message = "Sorry!!  We're now upgrading Wallet service to meet huge user connections. It'll take 1-2 days, please wait until then. Your points are being managed safely.";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "blockchain");
            die();
        }

        $walletAccessSchedule = $this->gwasm->getByDate($nation_code, date("Y-m-d"));
        if (isset($walletAccessSchedule->id)) {
            if ($walletAccessSchedule->isOpenWallet == "close") {
                $data["isOpenWallet"] = $walletAccessSchedule->isOpenWallet;
                $data["isOpenMessage"] = "open";
                if ($pelanggan->language_id == 1) {
                    $data["message"] = $walletAccessSchedule->message;
                } else {
                    $data["message"] = $walletAccessSchedule->message_indonesia;
                }

                $this->status = 200;
                $this->message = "Success";
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "blockchain");
                die();
            } else {
                $data["isOpenMessage"] = "open";
                if ($pelanggan->language_id == 1) {
                    $data["message"] = $walletAccessSchedule->message;
                } else {
                    $data["message"] = $walletAccessSchedule->message_indonesia;
                }
            }
        }

        $id_banner = (int)$this->input->get('id_banner');

        // try {
        //     $response = json_decode($this->__callBlockChainAccessToken($pelanggan->user_wallet_code, $id_banner, $pelanggan->language_id));

        //     if (!empty($response)) {
        //         $this->status = 200;
        //         $this->message = "Success";
        //         $data['url'] = $response->accessToken;
        //     } else {
        //         $this->status = 1001;
        //         $this->message = "Sorry!!  We're now upgrading Wallet service to meet huge user connections. It'll take 1-2 days, please wait until then. Your points are being managed safely.";
        //     }

        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "blockchain");

        // } catch (Exception $e) {
        //     $this->status = 1001;
        //     $this->message = "Sorry!!  We're now upgrading Wallet service to meet huge user connections. It'll take 1-2 days, please wait until then. Your points are being managed safely.";

        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "blockchain");
        // }

        if($pelanggan->language_id == 2){
            $language = 'id';
        }else{
            $language = 'en';
        }

        // check is influencer or not | Muhammad Sofi - 13 February 2024
        $checkInfluencer = $this->hpreuim->getInfluencerById($pelanggan->id);
        if(isset($checkInfluencer->b_user_id)) {
            $is_influencer = "1";
        } else {
            $is_influencer = "0";
        }

        $postdata = array(
          'userWalletCode' => $pelanggan->user_wallet_code_new,
          'countryIsoCode' => strtolower($this->blockchain_api_country),
          'LanguageIsoCode' => $language,
          'signupUtcDate' => $pelanggan->cdate,
          'sellonEmail' => $pelanggan->email,
          'sellonPhoneNumber' => $pelanggan->telp,
          'sellonUsername' => $pelanggan->fnama,
          'isInfluencer' => $is_influencer,
          'registerFrom' => $pelanggan->register_from,
        );
        $response = json_decode($this->__callBlockChainAccessTokenNew($postdata));
        if(isset($response->status)){
            if($response->status == 200){
                $this->status = 200;
                $this->message = "Success";
                $data['url'] = $response->data;
            } else {
                $response = json_decode($this->__callBlockChainAccessTokenNew($postdata));
                if(isset($response->status)){
                    if($response->status == 200){
                        $this->status = 200;
                        $this->message = "Success";
                        $data['url'] = $response->data;
                    } else {
                        $this->status = 1002;
                        $this->message = "Connection is not good temporarily due to lots of traffic. Please try again.";
                    }
                } else {
                    $this->status = 1002;
                    $this->message = "Connection is not good temporarily due to lots of traffic. Please try again.";
                }
            }
        } else {
            $response = json_decode($this->__callBlockChainAccessTokenNew($postdata));
            if(isset($response->status)){
                if($response->status == 200){
                    $this->status = 200;
                    $this->message = "Success";
                    $data['url'] = $response->data;
                } else {
                    $this->status = 1002;
                    $this->message = "Connection is not good temporarily due to lots of traffic. Please try again.";
                }
            } else {
                $this->status = 1002;
                $this->message = "Connection is not good temporarily due to lots of traffic. Please try again.";
            }
        }
        unset($response);

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "blockchain");
    }

    public function accesstokenv2()
    {
        //init
        $dt = $this->__init();

        //default result format
        $data = array();
        $data['url'] = "";
        $data['isOpenWallet'] = "open";
        $data['isOpenMessage'] = "close";
        $data['message'] = "";

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "blockchain");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data);
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "blockchain");
            die();
        }

        $wallet_active = "off";
        $wallet_active = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C7");
        if (isset($wallet_active->remark)) {
            $wallet_active = $wallet_active->remark;
        }

        if ($wallet_active == "off") {
            $this->status = 1001;
            $this->message = "Sorry!!  We're now upgrading Wallet service to meet huge user connections. It'll take 1-2 days, please wait until then. Your points are being managed safely.";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "blockchain");
            die();
        }

        $walletAccessSchedule = $this->gwasm->getByDate($nation_code, date("Y-m-d"));
        if (isset($walletAccessSchedule->id)) {
            if ($walletAccessSchedule->isOpenWallet == "close") {
                $data["isOpenWallet"] = $walletAccessSchedule->isOpenWallet;
                $data["isOpenMessage"] = "open";
                if ($pelanggan->language_id == 1) {
                    $data["message"] = $walletAccessSchedule->message;
                } else {
                    $data["message"] = $walletAccessSchedule->message_indonesia;
                }

                $this->status = 200;
                $this->message = "Success";
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "blockchain");
                die();
            } else {
                $data["isOpenMessage"] = "open";
                if ($pelanggan->language_id == 1) {
                    $data["message"] = $walletAccessSchedule->message;
                } else {
                    $data["message"] = $walletAccessSchedule->message_indonesia;
                }
            }
        }

        $id_banner = (int)$this->input->get('id_banner');

        // try {
        //     $response = json_decode($this->__callBlockChainAccessToken($pelanggan->user_wallet_code, $id_banner, $pelanggan->language_id));

        //     if (!empty($response)) {
        //         $this->status = 200;
        //         $this->message = "Success";
        //         $data['url'] = $response->accessToken;
        //     } else {
        //         $this->status = 1001;
        //         $this->message = "Sorry!!  We're now upgrading Wallet service to meet huge user connections. It'll take 1-2 days, please wait until then. Your points are being managed safely.";
        //     }

        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "blockchain");

        // } catch (Exception $e) {
        //     $this->status = 1001;
        //     $this->message = "Sorry!!  We're now upgrading Wallet service to meet huge user connections. It'll take 1-2 days, please wait until then. Your points are being managed safely.";

        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "blockchain");
        // }

        if($pelanggan->language_id == 2){
            $language = 'id';
        }else{
            $language = 'en';
        }

        // check is influencer or not | Muhammad Sofi - 13 February 2024
        $checkInfluencer = $this->hpreuim->getInfluencerById($pelanggan->id);
        if(isset($checkInfluencer->b_user_id)) {
            $is_influencer = "1";
        } else {
            $is_influencer = "0";
        }

        $postdata = array(
          'userWalletCode' => $pelanggan->user_wallet_code_new,
          'countryIsoCode' => strtolower($this->blockchain_api_country),
          'LanguageIsoCode' => $language,
          'signupUtcDate' => $pelanggan->cdate,
          'sellonEmail' => $pelanggan->email,
          'sellonPhoneNumber' => $pelanggan->telp,
          'sellonUsername' => $pelanggan->fnama,
          'isInfluencer' => $is_influencer,
          'registerFrom' => $pelanggan->register_from,
        );

        $response = json_decode($this->__callBlockChainAccessTokenNew($postdata));
        if(isset($response->status)){
            if($response->status == 200){
                $this->status = 200;
                $this->message = "Success";
                $data['url'] = $response->data;
            } else {
                $response = json_decode($this->__callBlockChainAccessTokenNew($postdata));
                if(isset($response->status)){
                    if($response->status == 200){
                        $this->status = 200;
                        $this->message = "Success";
                        $data['url'] = $response->data;
                    } else {
                        $this->status = 1002;
                        $this->message = "Connection is not good temporarily due to lots of traffic. Please try again.";
                    }
                } else {
                    $this->status = 1002;
                    $this->message = "Connection is not good temporarily due to lots of traffic. Please try again.";
                }
            }
        } else {
            $response = json_decode($this->__callBlockChainAccessTokenNew($postdata));
            if(isset($response->status)){
                if($response->status == 200){
                    $this->status = 200;
                    $this->message = "Success";
                    $data['url'] = $response->data;
                } else {
                    $this->status = 1002;
                    $this->message = "Connection is not good temporarily due to lots of traffic. Please try again.";
                }
            } else {
                $this->status = 1002;
                $this->message = "Connection is not good temporarily due to lots of traffic. Please try again.";
            }
        }
        unset($response);

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "blockchain");
    }

    public function sendnotif()
    {
        //init
        $dt = $this->__init();

        //default result format
        $data = array();

        $nation_code = 62;

        //check code/pass
        $pass = $this->input->post('pass');
        if ($pass != 'm3BmftHnSUYR6GJM9qZIT90TboFHGRaC') {
            $this->status = 200;
            $this->message = "Success";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "blockchain");
            die();
        }

        $user_wallet_code = $this->input->post('user_wallet_code');
        $pelanggan = $this->bu->getByUserWalletCodeNew($nation_code, $user_wallet_code);
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $title = $this->input->post('title');
        $message = $this->input->post('message');
        $type = $this->input->post('type');
        $page = $this->input->post('page');

        if($message == "Your redemption request is accepted successfully." && $pelanggan->language_id == 2) {
          $message = "Permintaan penukaran Anda berhasil diterima.";
        }
        if($message == "Sorry, your redemption request is rejected." && $pelanggan->language_id == 2) {
          $message = "Maaf, permintaan penukaran Anda ditolak.";
        }

        $dpe = array();
        $dpe['nation_code'] = $nation_code;
        $dpe['b_user_id'] = $pelanggan->id;
        $dpe['id'] = $this->dpem->getLastId($nation_code, $pelanggan->id);
        $dpe['type'] = $type;
        // if($pelanggan->language_id == 2) {
        //   $dpe['judul'] = "New User Event";
        //   $dpe['teks'] =  "Anda telah menyelesaikan Misi Harian di Event user baru. Kami akan menverifikasi proses anda. Mohon menunggu instruksi berikutnya.";
        // } else {
        //   $dpe['judul'] = "New User Event";
        //   $dpe['teks'] =  "You have successfully completed the Daily Mission in our Event for New User. We are currently verifying. Please await further instructions.";
        // }
        $dpe['judul'] = $title;
        $dpe['teks'] =  $message;

        $dpe['gambar'] = 'media/pemberitahuan/promotion.png';
        $dpe['cdate'] = "NOW()";
        $extras = new stdClass();
        $extras->page = $page;
        // if($pelanggan->language_id == 2) { 
        //   $extras->judul = "New User Event";
        //   $extras->teks =  "Anda telah menyelesaikan Misi Harian di Event user baru. Kami akan menverifikasi proses anda. Mohon menunggu instruksi berikutnya.";
        // } else {
        //   $extras->judul = "New User Event";
        //   $extras->teks =  "You have successfully completed the Daily Mission in our Event for New User. We are currently verifying. Please await further instructions.";
        // }

        $dpe['extras'] = json_encode($extras);
        $this->dpem->set($dpe);

        // $classified = 'setting_notification_user';
        // $code = 'U3';
        // $receiverSettingNotif = $this->busm->getValue($nation_code, $pelanggan->id, $classified, $code);
        // if (!isset($receiverSettingNotif->setting_value)){
        //     $receiverSettingNotif->setting_value = 0;
        // }

        // if ($receiverSettingNotif->setting_value == 1 && $pelanggan->is_active == 1) {
        if ($pelanggan->is_active == 1) {
            if($pelanggan->device == "ios"){
                $device = "ios";
            }else{
                $device = "android";
            }

            $tokens = $pelanggan->fcm_token;
            if(!is_array($tokens)) $tokens = array($tokens);
            // if($pelanggan->language_id == 2){
            //     $title = "New User Event";
            //     $message = "Anda telah menyelesaikan Misi Harian di Event user baru. Kami akan menverifikasi proses anda. Mohon menunggu instruksi berikutnya.";
            // } else {
            //     $title = "New User Event";
            //     $message = "You have successfully completed the Daily Mission in our Event for New User. We are currently verifying. Please await further instructions.";
            // }

            $image = 'media/pemberitahuan/promotion.png';
            // $type = 'event_hashtag_new_user';
            $payload = new stdClass();
            $payload->page = $page;
            // if($pelanggan->language_id == 2) {
            //     $payload->judul = "New User Event";
            //     $payload->teks = "Anda telah menyelesaikan Misi Harian di Event user baru. Kami akan menverifikasi proses anda. Mohon menunggu instruksi berikutnya.";
            // } else {
            //     $payload->judul = "New User Event";
            //     $payload->teks = "You have successfully completed the Daily Mission in our Event for New User. We are currently verifying. Please await further instructions.";
            // }
            $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
        }

        $this->status = 200;
        $this->message = "Success";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "blockchain");
    }
}
