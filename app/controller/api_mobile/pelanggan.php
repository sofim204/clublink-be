<?php

require_once (SENEROOT.'app/controller/api_mobile/setting.php');

// require_once (SENEROOT.'/vendor/autoload.php');
// use GuzzleHttp\Client;
// use GuzzleHttp\Promise;
// use Psr\Http\Message\ResponseInterface;
// use GuzzleHttp\Exception\RequestException;

/**
 * API for user
 */
class Pelanggan extends JI_Controller
{
    public $email_send = 1;
    public $a_company_kode = '00';
    public $kode_pattern = '%010d';
    public $is_log = 1;

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->lib('seme_email');
        $this->load("api_mobile/a_negara_model", 'anm');
        $this->load("api_mobile/a_apikey_model", 'aakm');
        $this->load("api_mobile/b_lokasi_model", 'bl');
        $this->load("api_mobile/b_kodepos_model", 'bkp');
        $this->load("api_mobile/b_user_model", 'bu');
        $this->load("api_mobile/b_user_alamat_model", 'bua');
        $this->load("api_mobile/b_user_bankacc_model", 'bubam');
        $this->load("api_mobile/common_code_model", 'ccm');
        $this->load("api_mobile/c_produk_model", 'cpm');
        $this->load("api_mobile/d_order_model", 'order');
        $this->load("api_mobile/d_order_detail_model", 'dodm');
        $this->load("api_mobile/d_order_alamat_model", 'doam');
        $this->load("api_mobile/d_pemberitahuan_model", 'dpem');
        $this->load("api_mobile/f_version_mobile_model", 'fvmm');
        $this->load("api_mobile/g_daily_track_record_model", 'gdtrm');

        if(!isset($this->expired_token)){
          $this->expired_token = 12;
        }

        //by Donny Dennison - 26 november 2021 15:25
        //api get general location list
        $this->load("api_mobile/b_user_alamat_location_model", 'bual');
        // $this->load("api_mobile/b_user_alamat_location_original_model", 'bualo');

        // $this->load("api_mobile/g_general_location_highlight_status_model", 'gglhsm');
        $this->load("api_mobile/g_leaderboard_point_total_model", 'glptm');
        $this->load("api_mobile/g_leaderboard_point_limit_model", 'glplm');

        $this->load("api_mobile/g_map_coverage_model","gmcm");

        //by Donny Dennison - 19 july 2022 15:42
        //delete temporary or permanent user feature
        $this->load("api_mobile/e_chat_room_model", 'ecrm');

        //by Donny Dennison - 12 september 2022 14:59
        //kode referral
        $this->load("api_mobile/g_leaderboard_point_history_model", 'glphm');
        // $this->load("api_mobile/g_leaderboard_ranking_model", 'glrm');

        //by Donny Dennison - 20 september 2022 15:04
        //mobile registration activity feature
        $this->load("api_mobile/g_mobile_registration_activity_model", 'gmram');

        //by Donny Dennison - 13 december 2022 14:31
        //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device
        $this->load("api_mobile/g_device_log_model", 'gdlm');

        $this->load("api_mobile/g_blacklist_model", 'gblm');

        $this->load("api_mobile/f_verification_phone_number_model", "fvpnm");
        $this->load("api_mobile/g_ip_whitelist_model", "giwlm");
        $this->load("api_mobile/c_community_event_re_targeting_model", "ccertm");
        $this->load("api_mobile/b_user_setting_model", "busm");
    }

    // private function __callBlockChainBlacklist($postdata){
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    //     curl_setopt($ch, CURLOPT_URL, $this->blockchain_api_host."Wallet/BlackListUserWallet");

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

    //     $this->seme_log->write("api_mobile", " API_Mobile/CallBlockChainBlackList::index -- url untuk block chain server ". $this->blockchain_api_host."Wallet/BlackListUserWallet. data send to blockchain api ". json_encode($postdata).". isi response block chain server ". $result);
    //     return $result;
    // }

    private function __passGen($password)
    {
        //$password = preg_replace('/[^a-zA-Z0-9]/', '', $password);
        return hash("sha256", $password);
        //return password_hash($password, PASSWORD_BCRYPT);
    }

    private function __passClear($password)
    {
        return $password;
        //return preg_replace('/[^a-zA-Z0-9]/', '', $password);
    }

    private function __orderStatusString($order_status)
    {
        $os = '';
        switch ($order_status) {
            case "order_konfirmasi_sudah":
                $os = '<label class="label label-warning">Menunggu Verifikasi<label>';
                break;
            case "order_cekstok":
                $os = '<label class="label label-info">Diproses (Cekstok)<label>';
                break;
            case "order_pembelian":
                $os = '<label class="label label-info">Diproses (Pembelian)<label>';
                break;
            case "order_store":
                $os = '<label class="label label-info">Diproses (Gudang)<label>';
                break;
            case "order_qc":
                $os = '<label class="label label-info">Diproses (QC)<label>';
                break;
            case "order_packing":
                $os = '<label class="label label-info">Diproses (Packing)<label>';
                break;
            case "order_kirim":
                $os = '<label class="label label-info">Diproses (Ekspedisi)<label>';
                break;
            case "order_selesai":
                $os = '<label class="label label-success">Dikirim<label>';
                break;
            case "order_batal":
                $os = '<label class="label label-secondary">Batalkan<label>';
                break;
            case "order_pending":
                $os = '<label class="label label-primary">Pending<label>';
                break;
            default:
                $os = '<label class="label label-danger">Menunggu Pembayaran<label>';
        }
        return $os;
    }

    private function __passwordGenerateLink($nation_code, $user_id)
    {
        $this->lib("conumtext");
        $token = $this->conumtext->genRand($type="str", $min=18, $max=24);
        $this->bu->setToken($nation_code, $user_id, $token, $kind="api_web");
        return base_url('account/password/reset/'.$token);
    }

    private function __uploadUserImage($b_user_id)
    {
        /*******************
         * Only these origins will be allowed to upload images *
         ******************/
        $folder = SENEROOT.DIRECTORY_SEPARATOR.$this->media_user.DIRECTORY_SEPARATOR;
        $folder = str_replace('\\', '/', $folder);
        $folder = str_replace('//', '/', $folder);
        $ifol = realpath($folder);
        //die($folder);
        if (!$ifol) {
            mkdir($folder);
        }
        $ifol = realpath($folder);
        //die($ifol);

        reset($_FILES);
        $temp = current($_FILES);
        if (is_array($temp) && is_uploaded_file($temp['tmp_name'])) {
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                // same-origin requests won't set an origin. If the origin is set, it must be valid.
                header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
            }
            header('Access-Control-Allow-Credentials: true');
            header('P3P: CP="There is no P3P policy."');

            // Sanitize input
            if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
                header("HTTP/1.0 500 Invalid file name.");
                return 0;
            }
            if (mime_content_type($temp['tmp_name']) == 'webp') {
                header("HTTP/1.0 500 WebP currently unsupported.");
                return 0;
            }
            // Verify extension
            $ext = pathinfo($temp['name'], PATHINFO_EXTENSION);
            if (!in_array(strtolower($ext), array("jpeg", "jpg", "png"))) {
                header("HTTP/1.0 500 Invalid extension.");
                return 0;
            }

            // Create magento style media directory
            $year = date("Y");
            $month = date("m");
            if (PHP_OS == "WINNT") {
                if (!is_dir($ifol)) {
                    mkdir($ifol);
                }
                $ifol = $ifol.DIRECTORY_SEPARATOR.$year.DIRECTORY_SEPARATOR;
                if (!is_dir($ifol)) {
                    mkdir($ifol);
                }
                $ifol = $ifol.DIRECTORY_SEPARATOR.$month.DIRECTORY_SEPARATOR;
                if (!is_dir($ifol)) {
                    mkdir($ifol);
                }
            } else {
                if (!is_dir($ifol)) {
                    mkdir($ifol, 0775, true);
                }
                $ifol = $ifol.DIRECTORY_SEPARATOR.$year.DIRECTORY_SEPARATOR;
                if (!is_dir($ifol)) {
                    mkdir($ifol, 0775, true);
                }
                $ifol = $ifol.DIRECTORY_SEPARATOR.$month.DIRECTORY_SEPARATOR;
                if (!is_dir($ifol)) {
                    mkdir($ifol, 0775, true);
                }
            }

            // Accept upload if there was no origin, or if it is an accepted origin
            $rand = rand(0, 9999);
            $name = $b_user_id.'-'.$rand;
            $filetowrite = $ifol.$name.'.'.$ext;
            $filetowrite = str_replace('//', '/', $filetowrite);
            if (file_exists($filetowrite)) {
                $rand = rand(0, 999);
                $name = $b_user_id.'-'.$rand;
                $filetowrite = $ifol.$name.'.'.$ext;
                $filetowrite = str_replace('//', '/', $filetowrite);
                if (file_exists($filetowrite)) {
                    $rand = rand(1000, 99999);
                    $name = $b_user_id.'-'.$rand;
                    $filetowrite = $ifol.$name.'.'.$ext;
                    $filetowrite = str_replace('//', '/', $filetowrite);
                }
            }
            move_uploaded_file($temp['tmp_name'], $filetowrite);
            if (file_exists($filetowrite)) {

                //START by Donny Dennison - 16 november 2022 11:13
                //fix rotated image after resize(thumb)
                // if (in_array(strtolower($ext), array("jpg","jpeg"))) {
                //   $this->correctImageOrientation($filetowrite);
                // }
                //END by Donny Dennison - 16 november 2022 11:13
                //fix rotated image after resize(thumb)

                $this->lib("wideimage/WideImage", "inc");
                WideImage::load($filetowrite)->reSize(300)->saveToFile($filetowrite);
                WideImage::load($filetowrite)->crop('center', 'center', 300, 300)->saveToFile($filetowrite);
                return $this->media_user."/".$year."/".$month."/".$name.'.'.$ext;
            } else {
                return 0;
            }
        } else {
            // Notify editor that the upload failed
            //header("HTTP/1.0 500 Server Error");
            return 0;
        }
    }

    //by Donny Dennison - 11 august 2022 10:46
    //fix rotated image after resize(thumb)
    //credit: https://stackoverflow.com/a/18919355/7578520
    // private function correctImageOrientation($filename) {
    //     //credit: https://github.com/FriendsOfCake/cakephp-upload/issues/221#issuecomment-50128062
    //     $exif = false;
    //     $size = getimagesize($filename, $info);
    //     if (!isset($info["APP13"])) {
    //         if (function_exists('exif_read_data')) {
    //             $exif = exif_read_data($filename);
    //             if($exif && isset($exif['Orientation'])) {
    //                 $orientation = $exif['Orientation'];
    //                 if($orientation != 1){
    //                     $img = imagecreatefromjpeg($filename);
    //                     $deg = 0;
    //                     switch ($orientation) {
    //                         case 3:
    //                         $deg = 180;
    //                         break;
    //                         case 6:
    //                         $deg = 270;
    //                         break;
    //                         case 8:
    //                         $deg = 90;
    //                         break;
    //                     }
    //                     if ($deg) {
    //                         $img = imagerotate($img, $deg, 0);        
    //                     }
    //                     // then rewrite the rotated image back to the disk as $filename
    //                     imagejpeg($img, $filename, 95);
    //                 } // if there is some rotation necessary
    //             } // if have the exif orientation info
    //         } // if function exists
    //     }
    // }

    private function __activateGenerateLink($nation_code, $user_id, $token="")
    {
        $this->lib("conumtext");
        $min = 25;
        if (strlen($token)<25) {
            $token = $this->conumtext->genRand($type="str", $min, $max=30);
            $this->bu->setToken($nation_code, $user_id, $token, $kind="api_reg");
        }
        return base_url("account/activate/index/$token");
    }

    private function __activateMobileToken($nation_code, $user_id)
    {
        // $user_id = (int) $user_id;
        $this->lib("conumtext");
        $token = $nation_code.$this->conumtext->genRand($type="str", $min=18, $max=28);
        $token_plain = hash('sha256',$token);
        $token = hash('sha256',$token_plain);
        $this->bu->setToken($nation_code, $user_id, $token, $kind="api_mobile");

        return $token_plain;
    }

    private function __getNegara($nation_code)
    {
        $negara = $this->anm->getByNationCode($nation_code);
        if (!isset($negara->nation_code)) {
            $negara = new stdClass();
            $negara->nation_code = '-';
            $negara->iso2 = '-';
            $negara->iso3 = '-';
            $negara->nama = '-';
            $negara->mata_uang = '-';
            $negara->kode_mata_uang = '-';
            $negara->simbol_mata_uang = '-';
            $negara->satuan_berat = '-';
            $negara->latitude = -7.0187339;
            $negara->longitude = 107.435032;
            $negara->is_provinsi = 0;
            $negara->is_kabkota = 0;
            $negara->is_kecamatan = 0;
            $negara->is_kelurahan = 0;
            $negara->is_kodepos = 0;
            $negara->is_active = 0;
        }
        return $negara;
    }

    // private function __orderAddresses($nation_code, $pelanggan, $order)
    // {
    //     //addresses init
    //     $addresses = new stdClass();
    //     $addresses->billing = new stdClass();
    //     $addresses->shipping = new stdClass();

    //     //get billing address
    //     $jenis_alamat = 'Billing Address';
    //     $address_status = $this->ccm->getByClassifiedByCodeName($nation_code, "address", $jenis_alamat);
    //     if(!isset($address_status->code)){
    //       $address_status = new stdClass();
    //       $address_status->code = 'A1';
    //     }
    //     $addresses->billing = $this->doam->getByOrderIdBuyerIdStatusAddress($nation_code, $order->id, $pelanggan->id, $address_status->code);

    //     //get shipping address
    //     //by Donny Dennison - 17 juni 2020 20:18
    //     // request by Mr Jackie change Shipping Address into Receiving Address
    //     // $jenis_alamat = 'Shipping Address';
    //     $jenis_alamat = 'Receiving Address';
    //     $address_status = $this->ccm->getByClassifiedByCodeName($nation_code, "address", $jenis_alamat);
    //     if(!isset($address_status->code)){
    //       $address_status = new stdClass();
    //       $address_status->code = 'A2';
    //     }
    //     $addresses->shipping = $this->doam->getByOrderIdBuyerIdStatusAddress($nation_code, $order->id, $pelanggan->id, $address_status->code);
    //     return $addresses;
    // }
    // private function __orderSellers($nation_code, $pelanggan, $order)
    // {
    //     $sps = $this->dodpm->getProdukAlamatByOrderId($nation_code, $order->id);
    //     $sellers = array();
    //     foreach ($sps as $product) {
    //         $pid = (int) $product->id;
    //         //url manipulator
    //         $product->foto = $this->cdn_url($product->foto);
    //         $product->thumb = $this->cdn_url($product->thumb);
            
    //         // by Muhammad Sofi - 26 October 2021 11:16
    //         // if user img & banner not exist or empty, change to default image
    //         // $product->b_user_image_seller = $this->cdn_url($product->b_user_image_seller);
    //         if(file_exists(SENEROOT.$product->b_user_image_seller) && $product->b_user_image_seller != 'media/user/default.png'){
    //             $product->b_user_image_seller = $this->cdn_url($product->b_user_image_seller);
    //         } else {
    //             $product->b_user_image_seller = $this->cdn_url('media/user/default-profile-picture.png');
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

    // private function __callUserSettings($nation_code, $apisess)
    // {
    //     $apikey = '';
    //     $aakm = $this->aakm->get();

    //     //by Donny Dennison - 25 august 2020 20:15
    //     //fix user setting not save to db
    //     // if (isset($aakm[0]->code)) {
    //     if (isset($aakm[0]->str)) {


    //         //by Donny Dennison - 25 august 2020 20:15
    //         //fix user setting not save to db
    //         // $apikey = $aakm[0]->code;
    //         $apikey = hash('sha256',$aakm[0]->str);

    //     }

    //     //by Donny Dennison - 25 august 2020 20:15
    //     //fix user setting not save to db
    //     $apisess = hash('sha256',$apisess);
        
    //     $this->lib("seme_curl");
    //     $url = base_url("api_mobile/setting/notification/?apikey=$apikey&nation_code=$nation_code&apisess=$apisess");
    //     $res = $this->seme_curl->get($url);
    //     $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::__callUserSettings');
    //     // $this->debug($res);
    //     // die();
    // }

    //by Donny Dennison - 6 september 2022 17:50
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

    //START by Donny Dennison - 26 september 2022 15:55
    //integrate api blockchain
    // private function __callBlockChainCreateWallet($userWalletCode, $referralUserWalletCode=""){

    //     $dateBefore = date("Y-m-d H:i:s");

    //     // $ch = curl_init();
    //     // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     // curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    //     // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    //     // curl_setopt($ch, CURLOPT_URL, $this->blockchain_api_host."Wallet/CreateWalletWithEncryption");

    //     // $headers = array();
    //     // $headers[] = 'Content-Type:  application/json';
    //     // $headers[] = 'Accept:  application/json';
    //     // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //     $postdata = json_encode(array(
    //       'userWalletCode' => $this->__encryptdecrypt($userWalletCode,"encrypt"),
    //       'countryIsoCode' => $this->blockchain_api_country,
    //       'isReferralSignUp' => ($referralUserWalletCode == "") ? false : true,
    //       'referralUserWalletCode' => ($referralUserWalletCode == "") ? "" : $this->__encryptdecrypt($referralUserWalletCode,"encrypt")
    //     ));
    //     // curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);

    //     // $result = curl_exec($ch);
    //     // if (curl_errno($ch)) {
    //     //   return 0;
    //     //   //echo 'Error:' . curl_error($ch);
    //     // }
    //     // curl_close($ch);

    //     $client = new Client([
    //       'base_uri' => $this->blockchain_api_host,
    //       'headers' => array(
    //             "Content-Type" => "application/json",
    //             "Accept" => "application/json"
    //         ),
    //       // default timeout 5 detik
    //       'timeout'  => 5,
    //     ]);

    //     //https://stackoverflow.com/a/54624802/7578520
    //     $promise = $client->postAsync("Wallet/CreateWalletWithEncryption", ['body'=>$postdata])->then(
    //         function (ResponseInterface $res) {
    //             $response = $res->getBody()->getContents();

    //             return $response;
    //         },
    //         function (RequestException $e) {
    //             $response = [];
    //             $response->data = $e->getMessage();

    //             return $response;
    //         }
    //     );
    //     $result = $promise->wait();
    //     // echo $result;

    //     $this->seme_log->write("api_mobile", "sebelum panggil api ".$dateBefore.", url untuk block chain server ". $this->blockchain_api_host."Wallet/CreateWalletWithEncryption. data send to blockchain api ". $postdata.". isi response block chain server ". $result);

    //     return $result;

    // }

    // private function __callBlockChainLateReferralRewardTransaction($mainTransactionId, $userWalletCode, $referralUserWalletCode){

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

    //     $postdata = array(
    //       'mainTransactionId' => $mainTransactionId,
    //       'userWalletCode' => $this->__encryptdecrypt($userWalletCode,"encrypt"),
    //       'countryIsoCode' => $this->blockchain_api_country,
    //       'referralUserWalletCode' => $this->__encryptdecrypt($referralUserWalletCode,"encrypt"),
    //       'referralCountryIsoCode' => $this->blockchain_api_country

    //     );
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));

    //     $result = curl_exec($ch);
    //     if (curl_errno($ch)) {
    //       return 0;
    //       //echo 'Error:' . curl_error($ch);
    //     }
    //     curl_close($ch);

    //     $this->seme_log->write("api_mobile", "url untuk block chain server ". $this->blockchain_api_host."Wallet/LateReferralRewardTransaction. data send to blockchain api ". json_encode($postdata).". isi response block chain server ". $result);

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
    //END by Donny Dennison - 26 september 2022 15:55
    //integrate api blockchain

    /**
     * get User profile
     */
    public function index()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['pelanggan'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //building response
        $data['pelanggan'] = $pelanggan;
        // by Muhammad Sofi - 26 October 2021 11:16
        // if user img & banner not exist or empty, change to default image
        // $data['pelanggan']->image = str_replace("//", "/", $this->cdn_url($data['pelanggan']->image));
        if(file_exists(SENEROOT.$data['pelanggan']->image) && $data['pelanggan']->image != 'media/user/default.png'){
            $data['pelanggan']->image = str_replace("//", "/", $this->cdn_url($data['pelanggan']->image));
        } else {
            $data['pelanggan']->image = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
        }

        //by Donny Dennison - 08-09-2021 11:35
        //revamp-profile
        // by Muhammad Sofi - 26 October 2021 11:16
        // if user img & banner not exist or empty, change to default image
        // $data['pelanggan']->image_banner = str_replace("//", "/", $this->cdn_url($data['pelanggan']->image_banner));
        if(file_exists(SENEROOT.$data['pelanggan']->image_banner)){
            $data['pelanggan']->image_banner = str_replace("//", "/", $this->cdn_url($data['pelanggan']->image_banner));
        } else {
            $data['pelanggan']->image_banner = str_replace("//", "/", $this->cdn_url('media/user/default.png'));
        }

        if(file_exists(SENEROOT.$data['pelanggan']->band_image) && $data['pelanggan']->band_image != 'media/user/default.png'){
            $data['pelanggan']->band_image = str_replace("//", "/", $this->cdn_url($data['pelanggan']->band_image));
        }else{
            $data['pelanggan']->band_image = str_replace("//", "/", $this->cdn_url('media/user/default-profile-picture.png'));
        }

        unset($data['pelanggan']->password);
        unset($data['pelanggan']->api_web_token);
        unset($data['pelanggan']->api_mobile_token);
        unset($data['pelanggan']->api_reg_token);

        //free some memory
        unset($pelanggan);

        //response message
        $this->status = 200;
        $this->message = 'Success';

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    /**
     * User Registration
     * POST   google_id
     * POST   fb_id
     * POST   apple_id
     * POST   email_id
     * POST   password_id
     * POST   telp
     * POST   fnama
     * POST   image
     * POST   fcm_token
     * POST   device
     */
    public function daftarv2()
    {
        //initial
        $token = '';
        $user_id = 0;
        $register_success = 0;
        $user = new stdClass();
        $dt = $this->__init();

        // $this->seme_log->write("api_mobile", "API_Mobile/pelanggan/daftar:: --POST: ".json_encode($_POST));

        //default response
        $data = array();
        $data['apisess'] = '';
        $data['apisess_expired'] = '';
        $data['pelanggan'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (empty($c)) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //flags
        $reg_from = 'online';
        $is_telp_valid = 0;
        $is_email_valid = 0;
        $is_password_valid = 0;
        $is_telp = 0;

        //populate input
        $email = strtolower(trim($this->input->post("email")));
        $telp = $this->input->post("telp");
        $fb_id = $this->input->post("fb_id");
        $google_id = $this->input->post("google_id");
        $apple_id = $this->input->post("apple_id");
        $fnama = trim($this->input->post("fnama"));
        $password = $this->input->post("password");
        $password_confirm = $this->input->post("password_confirm");
        $fcm_token = $this->input->post("fcm_token");
        $device = strtolower(trim($this->input->post("device")));
        
        //by Donny Dennison - 17 february 2022 17:51
        //change message language in response/return
        $language_id = trim($this->input->post("language_id"));

        //START by Donny Dennison - 08 june 2022 15:15
        //change address flow in register
        $coverage_id = trim($this->input->post("coverage_id"));
        $is_changed_address = trim($this->input->post("is_changed_address"));
        if($is_changed_address != 1){
            $is_changed_address = 0;
        }
        //END by Donny Dennison - 08 june 2022 15:15

        $verifPhone = trim($this->input->post("verifPhone"));

        if (strlen($email)>4) {
            $is_email_valid = 1;
        } else {
            $email = '';
        }
        if ($is_email_valid) {
            $cem = $this->bu->checkEmailIgnoreActive($nation_code, $email);
            if (isset($cem->id)) {
                $this->status = 1702;
                // $this->message = 'Email already registered, please try another email or login with current email';
                $this->message = 'Email is already registered. Please try again with another email';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }
        }

        if (strlen($telp)>4) {
            $is_telp_valid = 1;
        } else {
            $telp = '';
        }

        if($is_email_valid != 1 && $is_telp_valid != 1){
            $this->status = 1732;
            $this->message = 'Please input the correct email';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        $confirmeds=0;
        if (strlen($fb_id)>1) {
            $reg_from = 'facebook';
            $confirmeds=1;
        } elseif (strlen($google_id)>1) {
            $reg_from = 'google';
            $confirmeds=1;
        } elseif (strlen($apple_id)>1) {
            $reg_from = 'apple';
            $confirmeds=1;
        } elseif (empty($is_email_valid) && !empty($is_telp_valid)) {
            $reg_from = 'phone';
            $confirmeds=1;
        } else {
            $reg_from = 'online';
            $confirmeds=0;
        }

        //check fcm_token valid in firebase or not
        //https://stackoverflow.com/a/45697880
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");

        $headers = array();
        $headers[] = 'Content-Type:  application/json';
        $headers[] = 'Authorization:  key='.$this->fcm_server_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $postdata = array(
          'registration_ids' => array($fcm_token)
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
          return 0;
          //echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        $result = json_decode($result);
        if(isset($result->success)){
            if($result->success != 1){
                $this->status = 1750;
                $this->message = 'Please check your data again';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }
        }else{
            $this->status = 1750;
            $this->message = 'Please check your data again';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        if($this->input->post('call_from') == "1ns!d3r"){
            $ip_address = $this->input->post('ip_address');
        }else{
            $ip_address = $_SERVER['HTTP_X_REAL_IP'];
        }

        //lock table
        $this->bu->trans_start();

        // $countUserByIpAddress = $this->bu->checkByIpAddressDate($nation_code, $ip_address, $reg_from);
        // $countUserByFcmToken = $this->bu->checkByFcmTokenDate($nation_code, $fcm_token, $reg_from);
        // if($countUserByIpAddress > 0 || $countUserByFcmToken > 0){
        //     $this->bu->trans_rollback();
        //     $this->bu->trans_end();
        //     $this->status = 1750;
        //     $this->message = 'Please check your data again';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //     die();
        // }
        // // unset($ip_address);
        // unset($countUserByIpAddress, $countUserByFcmToken);

        // $totalRegisterBefore = $this->bu->getForRegisterBefore($nation_code, $reg_from);
        // if ($totalRegisterBefore > 0) {
        //   $this->cpm->trans_rollback();
        //   $this->cpm->trans_end();
        //   $this->status = 1751;
        //   $this->message = 'Please try again';
        //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
        //   die();
        // }

        $blackList = $this->gblm->check($nation_code, "fcm_token", $fcm_token);
        if(isset($blackList->id)){
            $this->bu->trans_rollback();
            $this->bu->trans_end();
            // $this->status = 1707;
            // $this->message = 'Invalid email or password';
            $this->status = 1728;
            $this->message = "Sorry, you're banned to log in, please contact WA(+65 8856 2024)";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        // $kode_referral = strtolower(trim($this->input->post("kode_referral")));
        // if (strlen($kode_referral) == 8) {
        //     $checkKodeReferral = $this->bu->checkKodeReferral($nation_code, $kode_referral);
        //     if(isset($checkKodeReferral->id)){
        //         $countRecomendeeByFcmToken = $this->bu->checkByFcmTokenRecommender($nation_code, $fcm_token, $checkKodeReferral->id);
        //         // $countRecomendeeByIpAddress = $this->bu->checkByIpAddressRecommender($nation_code, $ip_address, $checkKodeReferral->id);
        //         // if(($countRecomendeeByFcmToken == 4 || $countRecomendeeByIpAddress == 4) && $checkKodeReferral->total_recruited >= 4 && $checkKodeReferral->total_recruited <= 14){
        //         if($countRecomendeeByFcmToken == 6 && $checkKodeReferral->total_recruited >= 6 && $checkKodeReferral->total_recruited <= 14){
        //             $du = array();
        //             $du['is_permanent_inactive'] = 0;
        //             $du['permanent_inactive_by'] = 'admin';
        //             $du['permanent_inactive_date'] = date('Y-m-d H:i:s');
        //             $du['api_mobile_token'] = "";
        //             $du['fcm_token'] = "";
        //             $du['is_active'] = 0;
        //             $du['is_confirmed'] = 0;
        //             $du['is_online'] = 0;
        //             $du['telp_is_verif'] = 0;
        //             $du['inactive_text'] = "spammer account(automatic)";
        //             $this->bu->update($nation_code, $checkKodeReferral->id, $du);

        //             // call BlackListUserWallet from blockchain API, stop rewarding and withdraw suspended user
        //             // $postdata = array();
        //             // $postdata[] = array(
        //             // 'userWalletCode' => $this->__encryptdecrypt($checkKodeReferral->user_wallet_code, "encrypt"),
        //             // 'countryIsoCode' => $this->blockchain_api_country,
        //             // );

        //             // $postdata = array(
        //             //     "userWalletList" => $postdata
        //             // );

        //             // $responseWalletApi = 0;
        //             // $response = json_decode($this->__callBlockChainBlacklist($postdata));
        //             // if(isset($response->responseCode)){
        //             //     if($response->responseCode == 0){
        //             //         $responseWalletApi = 1;
        //             //     }
        //             // }
        //             // unset($response);

        //             $this->bu->trans_commit();
        //             $this->bu->trans_end();
        //             $this->status = 401;
        //             $this->message = 'Missing or invalid API session';
        //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //             die();
        //         }
        //     }
        // }
        // unset($kode_referral, $checkKodeReferral, $countRecomendee);
        // unset($ip_address);

        //by Donny Dennison - 16 june 2022 09:52
        //add new parameter "country_origin" in post at api "pelanggan/daftar"
        $country_origin = strtolower(trim($this->input->post("country_origin")));
        // if(empty($country_origin)){
        //     $country_origin = "indonesia";
        // }

        // force to indonesia
        $country_origin = "indonesia";
        if($country_origin != "indonesia"){
            $is_changed_address = 1;
        }

        //START by Donny Dennison - 12 september 2022 14:59
        //kode referral
        $kode_referral = strtolower(trim($this->input->post("kode_referral")));

        $referral_type = strtolower(trim($this->input->post("referral_type")));
        if($referral_type == "communitydetail"){
            $referral_type = "Community Detail";
        }else if($referral_type == "productdetail"){
            $referral_type = "Product Detail";
        }else if($referral_type == "shop"){
            $referral_type = "Shop";
        }else{
            $referral_type = "My Share";
        }
        //END by Donny Dennison - 12 september 2022 14:59
        //kode referral

        // by Muhammmad Sofi 10 January 2023 11:41 | get is_emulator
        $emulator = $this->input->post("is_emulator");
        if($emulator != 1){
            $emulator = 0;
        }

        //by Donny Dennison - 20 september 2022 15:04
        //mobile registration activity feature
        $device_id = trim($this->input->post("device_id"));

        //START by Donny Dennison - 13 december 2022 14:31
        //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device
        $totalUsedDeviceId = $this->gdlm->countAll($nation_code, "", $device_id);

        //get max used in 1 device
        $maxUsed = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C6");
        if (!isset($maxUsed->remark)) {
            $maxUsed = new stdClass();
            $maxUsed->remark = 5;
        }

        if($totalUsedDeviceId >= $maxUsed->remark){
            $this->bu->trans_rollback();
            $this->bu->trans_end();
            $this->status = 1726;
            $this->message = "You're not allowed to use many accounts";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        //END by Donny Dennison - 13 december 2022 14:31
        //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device

        //validation
        if ($this->__mbLen($fnama)==0) {
            $fnama = "";
        }
        //$fnama = mb_ereg_replace('[a-zA-Z0-9\s,.!?]', '', $fnama);

        if (strlen($device)<3) {
            $this->status = 1755;
            $this->message = 'Unknown device type';
            // if ($this->is_log) {
            //     $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::daftar -- WARN '.$this->status.': '.$this->message);
            // }
        }
        // $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::daftar -- INFO device: '.$device);

        //by Donny Dennison - 08 june 2022 - 14:56
        //phone number not mandatory
        // //by Donny Dennison - 27 august 2020 - 14:52
        // //check telephone
        // $checkPhoneNumber = $this->bu->checkTelp($nation_code, $telp);
        // if (isset($checkPhoneNumber->id)) {
        //     $this->status = 1703;
        //     $this->message = 'Phone number already registered, please try another phone number';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //     die();
        // }

        if ($this->__mbLen($fnama)>=64) {
            $this->bu->trans_rollback();
            $this->bu->trans_end();
            $this->status = 1736;
            $this->message = 'Name too long';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        // if (strlen($fcm_token)<=50) {
        //     $fcm_token = "";
        //     $this->status = 1756;
        //     $this->message = 'Invalid FCM Token';
        //     // $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::daftar -- WARN '.$this->status.': '.$this->message);
        // }

        //by Donny Dennison - 08 june 2022 15:15
        //change address flow in register
        $address_penerima_nama = $this->input->post('address_penerima_nama');
        $address_penerima_telp = $this->input->post('address_penerima_telp');
        $address_alamat2 = $this->input->post('address_alamat2');
        $address_provinsi = $this->input->post('address_provinsi');
        $address_kabkota = $this->input->post('address_kabkota');
        $address_kecamatan = $this->input->post('address_kecamatan');
        $address_kelurahan = $this->input->post('address_kelurahan');
        $address_kodepos = $this->input->post('address_kodepos');
        $address_latitude = $this->input->post('address_latitude');
        $address_longitude = $this->input->post('address_longitude');
        $address_catatan = $this->input->post('address_catatan');
        // $latlng = $address_latitude.','.$address_longitude;

        // $details_url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=". $latlng. "&key=AIzaSyCzx5dDTHwJLrgqgM0LXH6D1VGAHC0m4Rw&language=id";

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $details_url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $geoloc = json_decode(curl_exec($ch), true);
     
        // $step1 = $geoloc['results'];
        // $get1 = $step1[0]['address_components'];
        // $get11 = $get1[1];
        // $negara = $get1[9];
        // print_r($negara['short_name']);
        // // $this->debug($step1[0]['address_components']);
        // // die();
        // $step2 = $step1['geometry'];
        // $coords = $step2['location'];
     
        // print $coords['lat'];
        // print $coords['lng'];
        // // $test = $details_url->types[0]->country;
        // // $this->debug($details_url);
        // // die();

        // //by Donny Dennison - 28 december 2021 20:33
        // //add checking address
        // if(empty($address_alamat2) || empty($address_provinsi) || empty($address_kabkota) || empty($address_kecamatan) || empty($address_kelurahan) || empty($address_latitude) || empty($address_longitude)){
        //     //START by Donny Dennison - 08 june 2022 15:15
        //     //change address flow in register
        //     // $this->status = 104;
        //     // $this->message = 'There is address that empty';
        //     // $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //     // die();

        //     if($coverage_id > 0){
        //         $coverageDetail = $this->gmcm->getById($nation_code, $coverage_id);
        //         if(isset($coverageDetail->id)){
        //             if($coverageDetail->provinsi == 'DKI Jakarta'){
        //                 $address_alamat2 = "Gg. H. Zakaria, RW.5";
        //                 $address_provinsi = "DKI Jakarta";
        //                 $address_kabkota = "Jakarta Pusat";
        //                 $address_kecamatan = "Tanah Abang";
        //                 $address_kelurahan = "Kebon Melati";
        //                 $address_kodepos = "10230";
        //                 $address_latitude = "-6.200055499719067";
        //                 $address_longitude = "106.8162468531788";
        //             }else{
        //                 $address_alamat2 = "Gg. H. Zakaria, RW.5";
        //                 $address_provinsi = "DKI Jakarta";
        //                 $address_kabkota = "Jakarta Pusat";
        //                 $address_kecamatan = "Tanah Abang";
        //                 $address_kelurahan = "Kebon Melati";
        //                 $address_kodepos = "10230";
        //                 $address_latitude = "-6.200055499719067";
        //                 $address_longitude = "106.8162468531788";
        //             }
        //         }
        //     }else{
        //         $address_alamat2 = "Gg. H. Zakaria, RW.5";
        //         $address_provinsi = "DKI Jakarta";
        //         $address_kabkota = "Jakarta Pusat";
        //         $address_kecamatan = "Tanah Abang";
        //         $address_kelurahan = "Kebon Melati";
        //         $address_kodepos = "10230";
        //         $address_latitude = "-6.200055499719067";
        //         $address_longitude = "106.8162468531788";
        //     }
        //     //END by Donny Dennison - 08 june 2022 15:15
        // }

        // //check kelurahan, kecamatan, kabkota, provinsi, and kodepos in db or not
        // $checkInDBOrNot = $this->bual->checkInDBOrNot($nation_code, $this->input->post('address_kelurahan'), $this->input->post('address_kecamatan'), $this->input->post('address_kabkota'), $this->input->post('address_provinsi'), $this->input->post('address_kodepos'));
        // if (!isset($checkInDBOrNot->id)){
        //     $this->status = 104;
        //     $this->message = 'This address is invalid, please find other address';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //     die();
        // }

        // $this->seme_log->write("api_mobile", 'API_Mobile/Pelanggan::daftar -- activate :'.$confirmeds);
        if (mb_strlen($password)>3) {
            $is_password_valid = 1;
        }

        //debug post
        //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan:: --POST: ".json_encode($_POST));

        //populate insert
        $di = array();
        $di['email'] = 'null';
        $di['fnama'] = $fnama;
        $di['lnama'] = "";
        $di['telp'] = 'null';
        $di['fb_id'] = 'null';
        $di['apple_id'] = 'null';
        $di['google_id'] = 'null';
        $di['fcm_token'] = $fcm_token;
        $di['cdate'] = 'NOW()';
        $di['image'] = 'media/user/default.png';
        $di['band_image'] = 'media/user/default.png';
        // $di['latitude'] = 1.290270;
        // $di['longitude'] = 103.851959;
        $di['intro_teks'] = "";
        $di['api_reg_token'] = "";
        $di['api_web_token'] = "";
        $di['api_mobile_token'] = "";
        $di['api_social_id'] = "";
        $di['is_confirmed']= $confirmeds;
        $di['password'] = $this->__passGen($password);
        $di['device'] = $device;
        $di['register_from'] = $reg_from;
        $di['is_emulator'] = $emulator;

        //by Donny Dennison - 17 february 2022 17:51
        //change message language in response/return
        if($language_id){
            $di["language_id"] = $language_id;
        }else{
            if($nation_code == 62){
                $di["language_id"] = 2;
            }else if($nation_code == 82){
                $di["language_id"] = 3;
            }else if($nation_code == 66){
                $di["language_id"] = 4;
            }else {
                $di["language_id"] = 1;
            }
        }

        //by Donny Dennison - 08 june 2022 15:15
        //change address flow in register
        $di['is_changed_address'] = $is_changed_address;

        //by Donny Dennison - 16 june 2022 09:52
        //add new parameter "country_origin" in post at api "pelanggan/daftar"
        $di['country_origin'] = $country_origin;

        //by Donny Dennison - 20 september 2022 15:04
        //mobile registration activity feature
        if(strlen($device_id) > 3){
            $di['device_id'] = $device_id;
        }

        if($this->input->post('call_from') == "1ns!d3r"){
            $di['ip_address'] = $this->input->post('ip_address');
        }else{
            $di['ip_address'] = $_SERVER['HTTP_X_REAL_IP'];
        }

        //registration flow
        if ($reg_from == 'google') {
            //only put correct value
            if (strlen($google_id)>1) {
                $di['google_id'] = $google_id;
            }
            if (strlen($email)>4) {
                $di['email'] = $email;
            }
            if (strlen($telp)>4) {
                $di['telp'] = $telp;
            }

            //check if already registered
            $user = $this->bu->auth_sosmed($nation_code, $fb_id, $google_id, $apple_id, $email, $telp);
            if (isset($user->id)) {
                $this->bu->trans_rollback();
                $this->bu->trans_end();
                $this->status = 1002;
                $this->message = 'User already registered using Google ID, please login';
                // if ($this->is_log) {
                //     $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::daftar -- ForcedClose '.$this->status.' '.$this->message);
                // }
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }

            $checkWhiteList = $this->giwlm->check($nation_code, $di['ip_address']);
            if(!isset($checkWhiteList->id)){
                $totalUserSameIP = $this->bu->countbyIpAddress($nation_code, $di['ip_address']);

                $limit = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C8");
                if (!isset($limit->remark)) {
                  $limit = new stdClass();
                  $limit->remark = 5;
                }

                if($totalUserSameIP >= $limit->remark){
                    $this->bu->trans_rollback();
                    $this->bu->trans_end();
                    $this->status = 1749;
                    $this->message = "You're not allowed to make a new account";
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                    die();
                }
            }

            //by Donny Dennison - 22 September 2021
            //auto-generate-password-social-media-signup
            $di['password'] = $this->__passGen('5ell0n2o2i');

            // $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- using Google ID");
        } elseif ($reg_from == 'apple') {
            //only put correct value
            if (strlen($apple_id)>1) {
                $di['apple_id'] = $apple_id;
            }
            if (strlen($email)>4) {
                $di['email'] = $email;
            }
            if (strlen($telp)>4) {
                $di['telp'] = $telp;
            }

            //START by Donny Dennison - 10 december 2020 15:01
            //new registration system for apple id
            $di['is_reset_password'] = 0;
            $di['password'] = $this->__passGen('5ell0n2o2i');

            // do {
            //     $di['telp'] = rand(10000000,19999999);
            //     //check already in db or havent
            //     $checkPhoneNumber = $this->bu->checkTelp($nation_code, $di['telp']);
            // } while (isset($checkPhoneNumber->id));
            //END by Donny Dennison - 10 december 2020 15:01

            //check if already registered
            $user = $this->bu->auth_sosmed($nation_code, $fb_id, $google_id, $apple_id, $email, $telp);
            if (isset($user->id)) {
                $this->bu->trans_rollback();
                $this->bu->trans_end();
                $this->status = 1003;
                $this->message = 'User already registered using Apple ID, please login';
                // if ($this->is_log) {
                //     $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::daftar -- ForcedClose '.$this->status.' '.$this->message);
                // }
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }

            $checkWhiteList = $this->giwlm->check($nation_code, $di['ip_address']);
            if(!isset($checkWhiteList->id)){
                $totalUserSameIP = $this->bu->countbyIpAddress($nation_code, $di['ip_address']);

                $limit = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C8");
                if (!isset($limit->remark)) {
                  $limit = new stdClass();
                  $limit->remark = 5;
                }

                if($totalUserSameIP >= $limit->remark){
                    $this->bu->trans_rollback();
                    $this->bu->trans_end();
                    $this->status = 1749;
                    $this->message = "You're not allowed to make a new account";
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                    die();
                }
            }

            // $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- using apple ID");
        } elseif ($reg_from=='facebook') {

            //only put correct value
            if (strlen($fb_id)>1) {
                $di['fb_id'] = $fb_id;
            }
            if (strlen($email)>4) {
                $di['email'] = $email;
            }
            if (strlen($telp)>4) {
                $di['telp'] = $telp;
            }

            //check if already registered
            $user = $this->bu->auth_sosmed($nation_code, $fb_id, $google_id, $apple_id, $email, $telp);
            if (isset($user->id)) {
                $this->bu->trans_rollback();
                $this->bu->trans_end();
                $this->status = 1004;
                $this->message = 'User already registered, please login';
                // if ($this->is_log) {
                //     $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::daftar -- ForcedClose '.$this->status.' '.$this->message);
                // }
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }

            $checkWhiteList = $this->giwlm->check($nation_code, $di['ip_address']);
            if(!isset($checkWhiteList->id)){
                $totalUserSameIP = $this->bu->countbyIpAddress($nation_code, $di['ip_address']);

                $limit = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C8");
                if (!isset($limit->remark)) {
                  $limit = new stdClass();
                  $limit->remark = 5;
                }

                if($totalUserSameIP >= $limit->remark){
                    $this->bu->trans_rollback();
                    $this->bu->trans_end();
                    $this->status = 1749;
                    $this->message = "You're not allowed to make a new account";
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                    die();
                }
            }

            //by Donny Dennison - 22 September 2021
            //auto-generate-password-social-media-signup
            $di['password'] = $this->__passGen('5ell0n2o2i');

            // $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- using FB ID");
        } elseif ($reg_from=='phone') {
            $di['email'] = $telp."@sellon.net";

            if (strlen($telp)>4) {
                $di['telp'] = $telp;
            }

            $res = $this->bu->checkTelpIgnoreActive($nation_code, $telp);
            if (isset($res->id)) {
                $this->bu->trans_rollback();
                $this->bu->trans_end();
                $this->status = 1703;
                $this->message = 'Phone number already registered, please try another phone number';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }

            $is_phone_verif_avail = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C25")->remark;
            if($is_phone_verif_avail == "on"){
                $verificationPhoneNumber = $this->fvpnm->checkVerificationNumberConfirmed($nation_code, $verifPhone, $telp);
                if (!isset($verificationPhoneNumber->id)) {
                    $this->bu->trans_rollback();
                    $this->bu->trans_end();
                    $this->status = 1707;
                    $this->message = 'Invalid email or password';
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                    die();
                }
            }

            //by Donny Dennison - 22 September 2021
            //auto-generate-password-social-media-signup
            $di['password'] = $this->__passGen('5ell0n2o2i');

            $di['telp_is_verif'] = 1;

            // $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- using Phone");
        } elseif ($reg_from=='online') {
            //by Donny Dennison - 08 june 2022 - 14:56
            //phone number not mandatory
            // if (strlen($email)<=4 && strlen($telp)<=4) {
            if (strlen($email)<=4) {
                $this->bu->trans_rollback();
                $this->bu->trans_end();
                $this->status = 105;
                // $this->message = 'Email or Phone number are required';
                $this->message = 'Email are required';
                // if ($this->is_log) {
                //     $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::daftar -- ForcedClose '.$this->status.' '.$this->message);
                // }
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }
            $use_email=0;
            if (strlen($email)>4) {
                $di['email'] = $email;
                $use_email = 1;
            }
            $use_phone=0;
            if (strlen($telp)>4) {
                $di['telp'] = $telp;
                $use_phone = 1;
            }
            if (!empty($use_email) && !empty($use_phone)) {
                $res = $this->bu->checkEmailTelpIgnoreActive($nation_code, $email, $telp);
                if (isset($res->id)) {
                    $this->bu->trans_rollback();
                    $this->bu->trans_end();
                    $this->status = 1701;
                    $this->message = 'Email and phone number already used';
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                    die();
                }
            } elseif (!empty($use_email) && empty($use_phone)) {
                $res = $this->bu->checkEmailIgnoreActive($nation_code, $email);
                if (isset($res->id)) {
                    $this->bu->trans_rollback();
                    $this->bu->trans_end();
                    $this->status = 1702;
                    $this->message = 'Email is already registered. Please try again with another email';
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                    die();
                }
            } elseif (empty($use_email) && !empty($use_phone)) {
                $res = $this->bu->checkTelpIgnoreActive($nation_code, $telp);
                if (isset($res->id)) {
                    $this->bu->trans_rollback();
                    $this->bu->trans_end();
                    $this->status = 1703;
                    $this->message = 'Phone number already registered, please try another phone number';
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                    die();
                }
            }

            //password
            if (!$is_password_valid) {
                $this->bu->trans_rollback();
                $this->bu->trans_end();
                $this->status = 1704;
                $this->message = 'Password not match or password length too short';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }

            $is_email_verif_avail = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C24")->remark;
            if($is_email_verif_avail == "on"){
                $verificationPhoneNumber = $this->fvpnm->checkVerificationNumberConfirmed($nation_code, $verifPhone, $telp);
                if (!isset($verificationPhoneNumber->id)) {
                    $this->bu->trans_rollback();
                    $this->bu->trans_end();
                    $this->status = 1707;
                    $this->message = 'Invalid email or password';
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                    die();
                }
            }

            $di['telp_is_verif'] = 1;

            // if ($this->is_log) {
            //     $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- using normal flow");
            // }
        } else {
            $this->bu->trans_rollback();
            $this->bu->trans_end();
            $this->status = 1705;
            $this->message = 'Registration method undefined. Please specify Appled ID or Google ID or Facebook ID or Email Password combination.';
            // if ($this->is_log) {
            //     $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- status: ".$this->status." - ".$this->message);
            // }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //insert to db
        $di['nation_code'] = $nation_code;

        //START by Donny Dennison - 6 september 2022 17:50
        //integrate api blockchain
        $endDoWhile = 0;
        do{
            $di['user_wallet_code'] = $this->GUIDv4();
            $checkWalletCode = $this->bu->checkWalletCode($nation_code, $di['user_wallet_code']);
            if($checkWalletCode == 0){
                $endDoWhile = 1;
            }
        }while($endDoWhile == 0);
        $di['blockchain_createuserwallet_api_called'] = 0;
        //END by Donny Dennison - 6 september 2022 17:50
        //integrate api blockchain

        $endDoWhile = 0;
        do{
            $di['user_wallet_code_new'] = $this->GUIDv4();
            $checkWalletCode = $this->bu->checkWalletCodeNew($nation_code, $di['user_wallet_code_new']);
            if($checkWalletCode == 0){
                $endDoWhile = 1;
            }
        }while($endDoWhile == 0);
        $di['user_wallet_code_new_api_called'] = 0;

        //START by Donny Dennison - 12 september 2022 14:59
        //kode referral
        $endDoWhile = 0;
        do{
            $length = 8;
            $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
            $charactersLength = strlen($characters);
            $generatedKodeReferral = '';
            for ($i = 0; $i < $length; $i++) {
                $generatedKodeReferral .= $characters[rand(0, $charactersLength - 1)];
            }
            $checkKodeReferral = $this->bu->checkKodeReferral($nation_code, $generatedKodeReferral);
            if(!isset($checkKodeReferral->id)){
                $endDoWhile = 1;
            }
        }while($endDoWhile == 0);
        $di['kode_referral']= $generatedKodeReferral;

        $b_user_id_recruiter = '0';
        if (strlen($kode_referral) == 8) {
            $checkKodeReferral = $this->bu->checkKodeReferral($nation_code, $kode_referral);
            if(isset($checkKodeReferral->id)){
                $b_user_id_recruiter = $checkKodeReferral->id;
                $di['b_user_id_recruiter'] = $checkKodeReferral->id;
                $di['referral_type'] = $referral_type;

                //START by Donny Dennison - 20 september 2022 15:04
                //mobile registration activity feature
                $activityData = $this->gmram->getByReferralType($nation_code, $kode_referral, "registered");
                if(isset($activityData->id)){
                    $did = array();
                    $did['is_registered'] = 1;
                    $did['cdate_registered'] = "NOW()";
                    $this->gmram->update($nation_code, $activityData->id, $did);
                    // $this->bu->trans_commit();
                    $di['g_mobile_registration_activity_id'] = $activityData->id;
                }
                //END by Donny Dennison - 20 september 2022 15:04
                //mobile registration activity feature
            }
        }

        // start comment code
        // $url = "https://maps.google.com/maps/api/geocode/json?latlng=$latlng&key=AIzaSyCzx5dDTHwJLrgqgM0LXH6D1VGAHC0m4Rw&language=id";

		// $ch = curl_init();
		// curl_setopt($ch, CURLOPT_URL, $url);
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// // curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		// // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		// // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		// $response = curl_exec($ch);
		// curl_close($ch);
		// $response_a = json_decode($response);
		// // $location = $response_a->results[0]->address_components->types->administrative_area_level_3;
		// // $location = $response_a->results[0]->address_components[5]->long_name;
		// $response_geocode = $response_a->results[0]->address_components;
		// foreach ($response_geocode as $geo) { 
		// 	$type_geo = $geo->types[0];

		// 	// if($type_geo == "route") {
		// 	// 	$address_alamat2 = $geo->long_name;
		// 	// } 
		// 	if($type_geo == "administrative_area_level_4") {
		// 		$address_kelurahan = $geo->long_name;
		// 	}
		// 	if($type_geo == "administrative_area_level_3") {
        //         $address_kecamatan_long = $geo->long_name;

        //         if (strpos($address_kecamatan_long, 'Kecamatan') !== false) {
        //             $address_kecamatan = str_replace("Kecamatan", "", $address_kecamatan_long);
        //         } else {
        //             $address_kecamatan = $geo->long_name;
        //         }
		// 	}
        //     if($type_geo == "administrative_area_level_2") {
        //         $address_kabkota_long = $geo->long_name;

        //         if (strpos($address_kabkota_long, 'Kabupaten') !== false) {
        //             $address_kabkota = str_replace("Kabupaten", "", $address_kabkota_long);
        //         }else if (strpos($address_kabkota_long, 'Kota') !== false) {
        //             $address_kabkota = str_replace("Kota", "", $address_kabkota_long);
        //         } else {
        //             $address_kabkota = $geo->long_name;
        //         }
		// 	} 
		// 	if($type_geo == "administrative_area_level_1") {
		// 		$address_provinsi_long = $geo->long_name;

        //         if (strpos($address_provinsi_long, 'Daerah Khusus Ibukota') !== false) {
        //             $address_provinsi = str_replace("Daerah Khusus Ibukota", "DKI", $address_provinsi_long);
        //         } else {
        //             $address_provinsi = $geo->long_name;
        //         }
		// 	} 
		// 	if($type_geo == "country") {
		// 		$country_origin = $geo->long_name;
		// 		$country_origin = strtolower($country_origin);
		// 		$country_short = $geo->short_name;
		// 	} 
		// 	if($type_geo == "postal_code") {
		// 		$address_kodepos = $geo->long_name;
		// 	} 
		// }

        // $alamat2 = $response_a->results[0]->formatted_address;
        // $new_alamat2 = explode(",", $alamat2);
        // // $address_alamat2 = $new_alamat2[1];
        // $alamat2 = "";
        // foreach($new_alamat2 as $na) {
        //     if(stripos($na, "Jl.") !== false) {
        //         // echo "true array 0 \n";
        //         $alamat2 = $na;
        //         break;
        //     } else if(stripos($na, "Jl.") !== false) {
        //         // echo "true array 1 \n";
        //         $alamat2 = $na;
        //         break;
        //     }   
        // }
        // $address_alamat2 = $alamat2;
        // end comment code

        // // start by muhammad sofi 5 January 2023 10:36 | send event to google analytics
        // // $url = "https://www.google-analytics.com/mp/collect?firebase_app_id=$this->firebase_app_id&api_secret=$this->firebase_api_secret";

        // // $ch = curl_init();
        // // curl_setopt($ch, CURLOPT_URL, $url);
        // // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

        // // $headers = array();
        // // $headers[] = 'Content-Type:  application/json';
        // // $headers[] = 'Accept:  application/json';
        // // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // // $postdata = array(
        // //     'events' => 'GoogleMapSignUp'
        // // );
        // // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));

        // // $aa = curl_exec($ch);
        // // // echo $url;
        // // echo $aa;
        // // die();
        // // if (curl_errno($ch)) {
        // //     echo 'Error: ' . curl_error($ch);
        // //     return 0;
        // // }
        // // curl_close($ch);

        // //https://stackoverflow.com/a/72290077/7578520
        // // $ip = str_replace('.', '', $_SERVER['REMOTE_ADDR']);
        // $data = array(
        //     // 'client_id' => $ip,
        //     // 'user_id' => '123',
        //     'events' => array(
        //         'name' => 'GoogleMapSignUp'
        //     )
        // );
        // $datastring = json_encode($data);
        // $post_url = "https://www.google-analytics.com/mp/collect?api_secret=$this->firebase_api_secret&measurement_id=G-Z9BL0W0DJC";
        // $ch = curl_init($post_url);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $datastring);
        // curl_setopt($ch, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_URL, $post_url);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        // curl_setopt($ch, CURLOPT_POST, TRUE);
        // $result = curl_exec($ch);
        // curl_close($ch);
        // // end by muhammad sofi 5 January 2023 10:36 | send event to google analytics

        // //by Donny Dennison - 28 december 2021 20:33
        // //add checking address
        // if(empty($address_alamat2) || empty($address_provinsi) || empty($address_kabkota) || empty($address_kecamatan) || empty($address_kelurahan) || empty($address_latitude) || empty($address_longitude)){
        //     //START by Donny Dennison - 08 june 2022 15:15
        //     //change address flow in register
        //     // $this->status = 104;
        //     // $this->message = 'There is address that empty';
        //     // $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //     // die();

        //     if($coverage_id > 0){
        //         $coverageDetail = $this->gmcm->getById($nation_code, $coverage_id);
        //         if(isset($coverageDetail->id)){
        //             if($coverageDetail->provinsi == 'DKI Jakarta'){
        //                 $address_alamat2 = "Gg. H. Zakaria, RW.5";
        //                 $address_provinsi = "DKI Jakarta";
        //                 $address_kabkota = "Jakarta Pusat";
        //                 $address_kecamatan = "Tanah Abang";
        //                 $address_kelurahan = "Kebon Melati";
        //                 $address_kodepos = "10230";
        //                 $address_latitude = "-6.200055499719067";
        //                 $address_longitude = "106.8162468531788";
        //             }else{
        //                 $address_alamat2 = "Gg. H. Zakaria, RW.5";
        //                 $address_provinsi = "DKI Jakarta";
        //                 $address_kabkota = "Jakarta Pusat";
        //                 $address_kecamatan = "Tanah Abang";
        //                 $address_kelurahan = "Kebon Melati";
        //                 $address_kodepos = "10230";
        //                 $address_latitude = "-6.200055499719067";
        //                 $address_longitude = "106.8162468531788";
        //             }
        //         }
        //     }else{
        //         $address_alamat2 = "Gg. H. Zakaria, RW.5";
        //         $address_provinsi = "DKI Jakarta";
        //         $address_kabkota = "Jakarta Pusat";
        //         $address_kecamatan = "Tanah Abang";
        //         $address_kelurahan = "Kebon Melati";
        //         $address_kodepos = "10230";
        //         $address_latitude = "-6.200055499719067";
        //         $address_longitude = "106.8162468531788";
        //     }
        //     //END by Donny Dennison - 08 june 2022 15:15
        // }

        // by muhammad sofi 15 March 2023 | add checking to set default address
        if($country_origin == "indonesia") {
            if(empty($address_provinsi) || empty($address_kabkota) || empty($address_kecamatan) || empty($address_kelurahan) || empty($address_latitude) || empty($address_longitude)){ 
                $address_alamat2 = "Pulu Sakit";
                $address_provinsi = "DKI Jakarta";
                $address_kabkota = "Kepulauan Seribu";
                $address_kecamatan = "Kepulauan Seribu Selatan";
                $address_kelurahan = "Pulau Untung Jawa";
                $address_kodepos = "14510";
                $address_latitude = "-6.036292404954418";
                $address_longitude = "106.74658602378337";
            }
        } else {
            $address_alamat2 = "Pulu Sakit";
            $address_provinsi = "DKI Jakarta";
            $address_kabkota = "Kepulauan Seribu";
            $address_kecamatan = "Kepulauan Seribu Selatan";
            $address_kelurahan = "Pulau Untung Jawa";
            $address_kodepos = "14510";
            $address_latitude = "-6.036292404954418";
            $address_longitude = "106.74658602378337";
        }

        $di['register_place_alamat2'] = $address_alamat2;
        $di['register_place_kelurahan'] = $address_kelurahan;
        $di['register_place_kecamatan'] = $address_kecamatan;
        $di['register_place_kabkota'] = $address_kabkota;
        $di['register_place_provinsi'] = $address_provinsi;
        $di['register_place_kodepos'] = $address_kodepos;
        $di['latitude'] = $address_latitude;
        $di['longitude'] = $address_longitude;
        //END by Donny Dennison - 12 september 2022 14:59
        //kode referral

        $this->lib("conumtext");
        $token = $nation_code.$this->conumtext->genRand($type="str", $min=18, $max=28);
        $token_plain = hash('sha256',$token);
        $token = hash('sha256',$token_plain);
        $di['api_mobile_token'] = $token;
        $api_mobile_edate = date('Y-m-d H:i:00',strtotime("+$this->expired_token day"));
        $di['api_mobile_edate'] = $api_mobile_edate;

        if($reg_from == "online"){
            $min = 25;
            $token_reg = $this->conumtext->genRand($type="str", $min, $max=30);
            $di['api_reg_token'] = $token_reg;
        }

        $free_ticket_rock_paper_scissors = $this->ccm->getByClassifiedAndCode($nation_code, "game", "I1");
        if (!isset($free_ticket_rock_paper_scissors->remark)) {
          $free_ticket_rock_paper_scissors = new stdClass();
          $free_ticket_rock_paper_scissors->remark = 3;
        }

        $free_ticket_shooting_fire = $this->ccm->getByClassifiedAndCode($nation_code, "game", "I3");
        if (!isset($free_ticket_shooting_fire->remark)) {
          $free_ticket_shooting_fire = new stdClass();
          $free_ticket_shooting_fire->remark = 1;
        }

        $di['free_ticket_rock_paper_scissors'] = $free_ticket_rock_paper_scissors->remark;
        $di['free_ticket_shooting_fire'] = $free_ticket_shooting_fire->remark;

        $endDoWhile = 0;
        do{
          $user_id = $this->GUIDv4();
          $checkId = $this->bu->checkId($nation_code, $user_id);
          if($checkId == 0){
              $endDoWhile = 1;
          }
        }while($endDoWhile == 0);
        $di['id'] = $user_id;

        $image = $this->__uploadUserImage($user_id);
        if (strlen($image)>4) {
            $di['image'] = str_replace("//", "/", $image);
            $di['band_image'] = str_replace("//", "/", $image);
        }
        $di['band_fnama'] = $fnama;
        $res = $this->bu->register($di); //return user id;
        if ($res) {
            // insert to signup android / ios
            if($device == "android") {
                $this->gdtrm->updateTotalData(DATE("Y-m-d"), "signup_android", "+", "1");
            } else if($device == "ios") {
                $this->gdtrm->updateTotalData(DATE("Y-m-d"), "signup_ios", "+", "1");
            } else {}

            $this->gdtrm->updateTotalData(DATE("Y-m-d"), "signup", "+", "1");
            //commit table
            // $this->bu->trans_commit();
            $register_success = 1;

            //get current country configuration
            $negara = $this->__getNegara($nation_code);

            $penerima_nama = trim($address_penerima_nama);
            if ($this->__mbLen($penerima_nama)<=0) {
                $this->bu->trans_rollback();
                $this->bu->trans_end();
                $this->status = 1737;
                $this->message = 'Name cannot be empty';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }
            if ($this->__mbLen($penerima_nama)>=64) {
                $this->bu->trans_rollback();
                $this->bu->trans_end();
                $this->status = 1736;
                $this->message = 'Name too long';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }

            //sanitize null
            $penerima_nama = trim(mb_ereg_replace('null', '', $penerima_nama));

            //check $penerima_nama
            $penerima_telp = trim($address_penerima_telp);
            if (empty($penerima_telp)) {
                $penerima_telp = '';
            }

            //by Donny Dennison - 08 june 2022 - 14:56
            //phone number not mandatory
            // if (strlen($penerima_telp)<=0) {
                // $this->bu->trans_rollback();
                // $this->bu->trans_end();
            //     $this->status = 1763;
            //     $this->message = 'Phone number cannot be empty';
            //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            //     die();
            // }

            if (strlen($penerima_telp)>=32) {
                $this->bu->trans_rollback();
                $this->bu->trans_end();
                $this->status = 1723;
                $this->message = 'Phone number too long';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }

            //check $catatan
            $catatan = $address_catatan;
            if (empty($catatan)) {
                $catatan = '';
            }

            // if ($this->__mbLen($catatan)>=128) {
                // $this->bu->trans_rollback();
                // $this->bu->trans_end();
            //     $this->status = 1724;
            //     $this->message = 'Address notes too long';
            //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            //     die();
            // }
            // by Muhammad Sofi - 3 November 2021 10:00
            // remark code

            $alamat2 = trim($address_alamat2);
            if (empty($alamat2)) {
                $alamat2 = '';
            }
            // if ($this->__mbLen($alamat2)>=128) {
                // $this->bu->trans_rollback();
                // $this->bu->trans_end();
            //     $this->status = 1765;
            //     $this->message = 'Secondary address too long';
            //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            //     die();
            // }

            $latitude = $address_latitude;
            $longitude = $address_longitude;
            if (strlen($latitude)<=3 || strlen($longitude)<=3) {
                //by Donny Dennison - 24 juli 2020 18:23
                //change default latitude and longitude
                // $latitude = $negara->latitude;
                // $longitude = $negara->longitude;
                $latitude = 0;
                $longitude = 0;
            }

            //populating input for location
            $provinsi = $address_provinsi;
            $kabkota = $address_kabkota;
            $kecamatan = $address_kecamatan;
            $kelurahan = $address_kelurahan;
            $kodepos = $address_kodepos;

            //validating
            if (empty($provinsi)) {
                $provinsi = '';
            }
            if (empty($kabkota)) {
                $kabkota = '';
            }
            if (empty($kecamatan)) {
                $kecamatan = '';
            }
            if (empty($kelurahan)) {
                $kelurahan = '';
            }
            if (empty($kodepos)) {
                $kodepos = '99999';
            }

            //check location properties provinsi
            if (!empty($negara->is_provinsi)) {
                if (strlen($provinsi)<=0) {
                    $this->bu->trans_rollback();
                    $this->bu->trans_end();
                    $this->status = 1766;
                    $this->message = 'Province / State are required for this country';
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                    die();
                }
            }

            //check location properties kabkota
            if (!empty($negara->is_kabkota)) {
                if (strlen($kabkota)<=0) {
                    $this->bu->trans_rollback();
                    $this->bu->trans_end();
                    $this->status = 1767;
                    $this->message = 'City are required for this country';
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                    die();
                }
            }

            //check location properties kecamatan
            if (!empty($negara->is_kecamatan)) {
                if (strlen($kecamatan)<=0) {
                    $this->bu->trans_rollback();
                    $this->bu->trans_end();
                    $this->status = 1724;
                    $this->message = 'District are required for this country';
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                    die();
                }
            }

            //check location properties kelurahan
            if (!empty($negara->is_kelurahan)) {
                if (strlen($kelurahan)<=0) {
                    $this->bu->trans_rollback();
                    $this->bu->trans_end();
                    $this->status = 1769;
                    $this->message = 'Sub District are required for this country';
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                    die();
                }
            }

            //check location properties kelurahan
            if (!empty($negara->is_kodepos)) {
                if (strlen($kodepos)<=0) {
                    $this->bu->trans_rollback();
                    $this->bu->trans_end();
                    $this->status = 1770;
                    $this->message = 'Zipcode / Postal Code are required for this country';
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                    die();
                }
            }

            // //check kelurahan, kecamatan, kabkota, provinsi, and kodepos in db or not
            // $checkInDBOrNot = $this->bual->checkInDBOrNot($nation_code, $kelurahan, $kecamatan, $kabkota, $provinsi, $kodepos);
            // if (!isset($checkInDBOrNot->id)){
                    // $this->bu->trans_rollback();
            // $this->bu->trans_end();
            //     $this->status = 1774;
            //     $this->message = 'This address is invalid, please find other address';
            //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            //     die();
            // }

            //get last id
            $last_id = $this->bua->getLastId($nation_code, $user_id);

            //collect input
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['id'] = $last_id;
            $di['b_user_id'] = $user_id;

            //by Donny Dennison - 22 september 2021
            //auto-generate-address-title
            // $di['judul'] = $judul;
            $di['judul'] = 'Your Place '.$last_id;

            $di['penerima_nama'] = $penerima_nama;
            $di['penerima_telp'] = $penerima_telp;
            $di['alamat2'] = $alamat2;
            $di['kelurahan'] = $kelurahan;
            $di['kecamatan'] = $kecamatan;
            $di['kabkota'] = $kabkota;
            $di['provinsi'] = $provinsi;
            $di['negara'] = $negara->iso2;
            $di['kodepos'] = $kodepos;
            $di['longitude'] = $longitude;
            $di['latitude'] = $latitude;

            //by Donny Dennison - 13 july 2021 15:49
            //set-address-type-to-default
            // $di['address_status'] = $address_status;
            $di['address_status'] = 'A2';

            $di['catatan'] = $catatan;
            $di['is_default'] = 1;
            $di['is_active'] = 1;

            //insert into database
            $res = $this->bua->set($di);
            if ($res) {
                // $this->bu->trans_commit();
            } else {
                $this->bu->trans_rollback();
                $this->bu->trans_end();
                $this->status = 1771;
                $this->message = 'Failed insert user address';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }

            //START by Donny Dennison - 5 january 2021 - 11:49
            //change address default
            // //release default
            // $du = array("is_default"=>0);
            // $this->bua->updateByUserId($nation_code, $pelanggan->id, $du);
            // $this->bu->trans_commit();
            // //update default
            // $du = array("is_default"=>1);
            // $this->bua->update($nation_code, $pelanggan->id, $last_id, $du);
            // $this->bu->trans_commit();

            // $user_alamat_default = $this->bua->getByUserIdDefault($nation_code, $user_id);
            // if($last_id == 1 || !isset($user_alamat_default->alamat2)){
            //     $du = array("is_default"=>1);
            //     $this->bua->update($nation_code, $user_id, $last_id, $du);
            //     // $this->bu->trans_commit();
            // }
            //END by Donny Dennison - 5 january 2021 - 11:49

            //check kelurahan, kecamatan, kabkota, provinsi, and kodepos in db or not
            $checkInDBOrNot = $this->bual->checkInDBOrNot($nation_code, $kelurahan, $kecamatan, $kabkota, $provinsi, $kodepos);
            if (!isset($checkInDBOrNot->id)){  
                //get last id
                $last_id = $this->bual->getLastId($nation_code);

                //collect input
                $di = array();
                $di['nation_code'] = $nation_code;
                $di['id'] = $last_id;
                $di['kelurahan'] = $kelurahan;
                $di['kecamatan'] = $kecamatan;
                $di['kabkota'] = $kabkota;
                $di['provinsi'] = $provinsi;
                $di['kodepos'] = $kodepos;
                $this->bual->set($di);
                // $this->bu->trans_commit();
            }

            // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", "All", "All", $provinsi);
            // if(!isset($getStatusHighlight->status)){
            //     //get last id
            //     $highlight_status_id = $this->gglhsm->getLastId($nation_code);
            //     $di = array();
            //     $di['nation_code'] = $nation_code;
            //     $di['id'] = $highlight_status_id;
            //     $di['b_user_alamat_location_kelurahan'] = 'All';
            //     $di['b_user_alamat_location_kecamatan'] = 'All';
            //     $di['b_user_alamat_location_kabkota'] = 'All';
            //     $di['b_user_alamat_location_provinsi'] = $provinsi;
            //     $this->gglhsm->set($di);
            //     // $this->bu->trans_commit();
            // }

            // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", "All", $kabkota, $provinsi);
            // if(!isset($getStatusHighlight->status)){
            //     //get last id
            //     $highlight_status_id = $this->gglhsm->getLastId($nation_code);
            //     $di = array();
            //     $di['nation_code'] = $nation_code;
            //     $di['id'] = $highlight_status_id;
            //     $di['b_user_alamat_location_kelurahan'] = 'All';
            //     $di['b_user_alamat_location_kecamatan'] = 'All';
            //     $di['b_user_alamat_location_kabkota'] = $kabkota;
            //     $di['b_user_alamat_location_provinsi'] = $provinsi;
            //     $this->gglhsm->set($di);
            //     // $this->bu->trans_commit();
            // }

            // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", $kecamatan, $kabkota, $provinsi);
            // if(!isset($getStatusHighlight->status)){
            //     //get last id
            //     $highlight_status_id = $this->gglhsm->getLastId($nation_code);
            //     $di = array();
            //     $di['nation_code'] = $nation_code;
            //     $di['id'] = $highlight_status_id;
            //     $di['b_user_alamat_location_kelurahan'] = 'All';
            //     $di['b_user_alamat_location_kecamatan'] = $kecamatan;
            //     $di['b_user_alamat_location_kabkota'] = $kabkota;
            //     $di['b_user_alamat_location_provinsi'] = $provinsi;
            //     $this->gglhsm->set($di);
            //     // $this->bu->trans_commit();
            // }

            // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, $kelurahan, $kecamatan, $kabkota, $provinsi);
            // if(!isset($getStatusHighlight->status)){
            //     //get last id
            //     $highlight_status_id = $this->gglhsm->getLastId($nation_code);
            //     $di = array();
            //     $di['nation_code'] = $nation_code;
            //     $di['id'] = $highlight_status_id;
            //     $di['b_user_alamat_location_kelurahan'] = $kelurahan;
            //     $di['b_user_alamat_location_kecamatan'] = $kecamatan;
            //     $di['b_user_alamat_location_kabkota'] = $kabkota;
            //     $di['b_user_alamat_location_provinsi'] = $provinsi;
            //     $this->gglhsm->set($di);
            //     // $this->bu->trans_commit();
            // }
        } else {
            $this->bu->trans_rollback();
            $this->bu->trans_end();
            $this->status = 1706;
            $this->message = 'Failed save user to database, please try again';
            // if ($this->is_log) {
            //     $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- FAILED");
            // }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        usleep(500000);
        //START by Donny Dennison - 10 december 2020 15:01
        //new registration system for apple id
        // $token = $this->__activateMobileToken($nation_code, $user_id);
        $user = $this->bu->getById($nation_code, $user_id);
        if ($reg_from == 'apple' && strpos($email, '@privaterelay.appleid.com') !== false) {
            $this->status = 200;
        }else{
        //END by Donny Dennison - 10 december 2020 15:01
        //new registration system for apple id

            //after success
            if ($register_success && !empty($user_id)) {
                $this->status = 200;
                // $this->message = 'registration successful, please check your inbox or spam before login';
                $this->message = 'Success';
                if ($this->email_send && strlen($email)>4) {
                    if ($confirmeds==0) {
                        // $link = $this->__activateGenerateLink($nation_code, $user_id, $user->api_reg_token);
                        $link = base_url("account/activate/index/$token_reg");

                        $nama = $user->fnama;
                        $replacer = array();
                        $replacer['site_name'] = $this->app_name;
                        $replacer['fnama'] = $nama;
                        $replacer['activation_link'] = $link;
                        $this->seme_email->flush();
                        $this->seme_email->replyto($this->site_name, $this->site_replyto);
                        $this->seme_email->from($this->site_email, $this->site_name);
                        $this->seme_email->subject('Registration Successful');
                        $this->seme_email->to($email, $nama);
                        $this->seme_email->template('account_register');
                        $this->seme_email->replacer($replacer);
                        $this->seme_email->send();
                    }
                }
            } else {
                $this->status = 1706;
                $this->message = 'Failed save user to database, please try again';
                // if ($this->is_log) {
                //     $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- status: ".$this->status." - ".$this->message);
                // }
            }
        //START by Donny Dennison - 10 december 2020 15:01
        //new registration system for apple id
        }
        //END by Donny Dennison - 10 december 2020 15:01

        //only manipulating
        if ($this->status == 200 && isset($user->id)) {

            $is_email_verif_avail = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C24")->remark;
            if($reg_from == 'online' && $is_email_verif_avail == "on"){
                $di = array();
                $di['b_user_id'] = $user->id;
                $this->fvpnm->update($verificationPhoneNumber->id, $di);
            }

            $is_phone_verif_avail = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C25")->remark;
            if($reg_from == 'phone' && $is_phone_verif_avail == "on"){
                $di = array();
                $di['b_user_id'] = $user->id;
                $this->fvpnm->update($verificationPhoneNumber->id, $di);
            }

            // $image = $this->__uploadUserImage($user->id);

            // $dux = array();
            // $dux['api_mobile_edate'] = date('Y-m-d H:i:00',strtotime("+$this->expired_token day"));
            // if (strlen($image)>4) {
            //     $dux['image'] = str_replace("//", "/", $image);
            // }
            // if(is_array($dux) && count($dux)) $this->bu->update($nation_code, $user->id, $dux);

            //add base url to image
            if (isset($user->image)) {
                $user->image = $this->cdn_url($image);
            }

            //by Donny Dennison - 08-09-2021 11:35
            //revamp-profile
            if (isset($user->image_banner)) {
                // by Muhammad Sofi - 28 October 2021 11:00
                // if user img & banner not exist or empty, change to default image
                // $user->image_banner = $this->cdn_url($user->image_banner);
                if(file_exists(SENEROOT.$user->image_banner)){
                    $user->image_banner = $this->cdn_url($user->image_banner);
                } else {
                    $user->image_banner = $this->cdn_url('media/user/default.png');
                }
            }

            //remove unecessary properties
            unset($user->api_mobile_token);
            unset($user->api_web_token);
            unset($user->api_reg_token);
            unset($user->password);
            $user->apisess = $token_plain;
            // $user->apisess_expired = $dux['api_mobile_edate'];
            $user->apisess_expired = $api_mobile_edate;
            $user->api_mobile_edate = $user->apisess_expired;

            //put to response
            $data['apisess'] = $token_plain;
            $data['apisess_expired'] = $user->apisess_expired;
            $data['pelanggan'] = $user;
            // if ($this->is_log) {
            //     $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- Image Uploaded");
            // }
            //update user setting
            // $this->__callUserSettings($nation_code, $token);
            $settingController = new setting();
            $settingController->notificationcustom($nation_code, $apikey, $token);

            $getPointPlacement = $this->glptm->getByUserId($nation_code, $user->id);
            if(!isset($getPointPlacement->b_user_id)){
                //create point
                $di = array();
                $di['nation_code'] = $nation_code;
                $di['b_user_id'] = $user->id;
                $di['total_post'] = 0;
                $di['total_point'] = 0;
                $this->glptm->set($di);
            }
            unset($getPointPlacement);

            //START by Donny Dennison - 12 september 2022 14:59
            //kode referral
            //START by Donny Dennison - 06 december 2023 17:00
            //improve-spt-system-to-standalone
            if($b_user_id_recruiter != '0'){
                //recommendee
                $recruiterData = $this->bu->getById($nation_code, $b_user_id_recruiter);
                //get point
                $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EY");
                if (!isset($pointGet->remark)) {
                  $pointGet = new stdClass();
                  $pointGet->remark = 50;
                }

                $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $user->id);

                $di = array();
                $di['nation_code'] = $nation_code;
                $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
                $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
                $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
                $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
                $di['b_user_id'] = $user->id;
                $di['point'] = $pointGet->remark;
                $di['custom_id'] = $b_user_id_recruiter;
                $di['custom_type'] = 'sign up';
                $di['custom_type_sub'] = 'with referral(recommendee)';
                $di['custom_text'] = $user->fnama.' '.$di['custom_type'].' '.$di['custom_type_sub'].' '.$recruiterData->fnama.' and get '.$di['point'].' point(s)';
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
                // $this->glrm->updateTotal($nation_code, $user->id, 'total_point', '+', $di['point']);

                //recommender
                if($recruiterData->is_active == 1){
                    //get point
                    $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EZ");
                    if (!isset($pointGet->remark)) {
                      $pointGet = new stdClass();
                      $pointGet->remark = 50;
                    }

                    $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $b_user_id_recruiter);

                    $di = array();
                    $di['nation_code'] = $nation_code;
                    $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
                    $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
                    $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
                    $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
                    $di['b_user_id'] = $b_user_id_recruiter;
                    $di['point'] = $pointGet->remark;
                    $di['custom_id'] = $user->id;
                    $di['custom_type'] = 'sign up';
                    $di['custom_type_sub'] = 'with referral(recommender)';
                    $di['custom_text'] = $user->fnama.' '.$di['custom_type'].' '.$di['custom_type_sub'].' '.$recruiterData->fnama.' and get '.$di['point'].' point(s)';
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
                    // $this->glrm->updateTotal($nation_code, $b_user_id_recruiter, 'total_point', '+', $di['point']);
                }

                // $this->bu->updateTotal($nation_code, $b_user_id_recruiter, "total_recruited", "+", "1");
                // $this->bu->updateDate($nation_code, $b_user_id_recruiter, "bdate", date("Y-m-d H:i:s"));
                $this->bu->updateTotalAndBDate($nation_code, $b_user_id_recruiter, "total_recruited", "+", "1", "bdate", date("Y-m-d H:i:s"));
            }else{
                //get point
                $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E4");
                if (!isset($pointGet->remark)) {
                  $pointGet = new stdClass();
                  $pointGet->remark = 50;
                }

                $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $user->id);

                $di = array();
                $di['nation_code'] = $nation_code;
                $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
                $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
                $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
                $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
                $di['b_user_id'] = $user->id;
                $di['point'] = $pointGet->remark;
                $di['custom_id'] = "0";
                $di['custom_type'] = 'sign up';
                $di['custom_type_sub'] = 'without referral(recommendee)';
                $di['custom_text'] = $user->fnama.' '.$di['custom_type'].' '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
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
                // $this->glrm->updateTotal($nation_code, $user->id, 'total_point', '+', $di['point']);
            }
            //END by Donny Dennison - 12 september 2022 14:59
            //kode referral
            //END by Donny Dennison - 06 december 2023 17:00
            //improve-spt-system-to-standalone

            //START by Donny Dennison - 07 october 2022 15:49
            //integrate api blockchain
            // if($b_user_id_recruiter != 0){
            //     $recruiterData = $this->bu->getById($nation_code, $b_user_id_recruiter);
            //     if($recruiterData->is_get_point == 1){
            //         $response = json_decode($this->__callBlockChainCreateWallet($user->user_wallet_code, $recruiterData->user_wallet_code));
            //     }else{
            //         $response = json_decode($this->__callBlockChainCreateWallet($user->user_wallet_code));   
            //     }
            // }else{
            //     $response = json_decode($this->__callBlockChainCreateWallet($user->user_wallet_code));
            // }

            // if(isset($response->responseCode)){
            //     if($b_user_id_recruiter == 0){
            //         if($response->responseCode == 0){
            //             $du = array("blockchain_createuserwallet_api_called"=>1);
            //         }else{
            //             $du = array("blockchain_createuserwallet_api_called"=>0);
            //         }
            //     }else{
            //         if($response->responseCode == 0 && $recruiterData->is_get_point == 1){
            //             $du = array("blockchain_createuserwallet_api_called"=>1);
            //         }else if($response->responseCode == 0 && $recruiterData->is_get_point == 0){
            //             $du = array("blockchain_createuserwallet_api_called"=>3);
            //         }else{
            //             $du = array("blockchain_createuserwallet_api_called"=>0);
            //         }
            //     }
            // }else{
            //     $du = array("blockchain_createuserwallet_api_called"=>0);
            // }

            // $this->bu->update($nation_code, $user->id, $du);
            // unset($recruiterData, $response);
            //END by Donny Dennison - 07 october 2022 15:49
            //integrate api blockchain

            //START by Donny Dennison - 13 december 2022 14:31
            //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device
            $di = array();
            $di["nation_code"] = $nation_code;
            $endDoWhile = 0;
            do{
              $di["id"] = $this->GUIDv4();
              $checkId = $this->gdlm->checkId($nation_code, $di["id"]);
              if($checkId == 0){
                  $endDoWhile = 1;
              }
            }while($endDoWhile == 0);
            $di["b_user_id"] = $user->id;
            $di["device_id"] = $device_id;
            $di["type"] = "signup";
            $di["cdate"] = "NOW()";
            $this->gdlm->set($di);
            //END by Donny Dennison - 13 december 2022 14:31
            //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device

            if($b_user_id_recruiter != '0' && date("Y-m-d") >= "2023-10-16" && date("Y-m-d") <= "2023-10-31"){
                $recruiterData = $this->bu->getById($nation_code, $b_user_id_recruiter);
                $eventProgress = $this->ccertm->getByUserid($nation_code, $b_user_id_recruiter);
                if(isset($eventProgress->id)){
                    if($eventProgress->cdate_day_4 && !$eventProgress->cdate_day_5){
                        if(date("Y-m-d", strtotime($eventProgress->cdate_day_4. " +1 day")) == date("Y-m-d")){
                            $du = array();
                            $du['task_day_5'] = $user->id;
                            $du['cdate_day_5'] = 'NOW()';
                            $this->ccertm->update($nation_code, $eventProgress->id, $du);

                            $dpe = array();
                            $dpe['nation_code'] = $nation_code;
                            $dpe['b_user_id'] = $recruiterData->id;
                            $dpe['id'] = $this->dpem->getLastId($nation_code, $recruiterData->id);
                            $dpe['type'] = "event_hashtag_retargeting";
                            if($recruiterData->language_id == 2) {
                              $dpe['judul'] = "Retargeting Event";
                              $dpe['teks'] =  "Anda telah menyelesaikan Misi Harian di Event user lama. Kami akan menverifikasi proses anda. Mohon menunggu instruksi berikutnya.";
                            } else {
                              $dpe['judul'] = "Retargeting Event";
                              $dpe['teks'] =  "You have successfully completed the Daily Mission in our Event for old user. We are currently verifying your data. Please await further instructions.";
                            }

                            $dpe['gambar'] = 'media/pemberitahuan/promotion.png';
                            $dpe['cdate'] = "NOW()";
                            $extras = new stdClass();
                            $extras->id = $recruiterData->id;
                            if($recruiterData->language_id == 2) { 
                              $extras->judul = "Retargeting Event";
                              $extras->teks =  "Anda telah menyelesaikan Misi Harian di Event user lama. Kami akan menverifikasi proses anda. Mohon menunggu instruksi berikutnya.";
                            } else {
                              $extras->judul = "Retargeting Event";
                              $extras->teks =  "You have successfully completed the Daily Mission in our Event for old user. We are currently verifying your data. Please await further instructions.";
                            }

                            $dpe['extras'] = json_encode($extras);
                            $this->dpem->set($dpe);

                            $classified = 'setting_notification_user';
                            $code = 'U3';
                            $receiverSettingNotif = $this->busm->getValue($nation_code, $recruiterData->id, $classified, $code);
                            if (!isset($receiverSettingNotif->setting_value)){
                                $receiverSettingNotif->setting_value = 0;
                            }

                            if ($receiverSettingNotif->setting_value == 1 && $recruiterData->is_active == 1) {
                                if($recruiterData->device == "ios"){
                                    $device = "ios";
                                }else{
                                    $device = "android";
                                }

                                $tokens = $recruiterData->fcm_token;
                                if(!is_array($tokens)) $tokens = array($tokens);
                                if($recruiterData->language_id == 2){
                                    $title = "Retargeting Event";
                                    $message = "Anda telah menyelesaikan Misi Harian di Event user lama. Kami akan menverifikasi proses anda. Mohon menunggu instruksi berikutnya.";
                                } else {
                                    $title = "Retargeting Event";
                                    $message = "You have successfully completed the Daily Mission in our Event for old user. We are currently verifying your data. Please await further instructions.";
                                }

                                $image = 'media/pemberitahuan/promotion.png';
                                $type = 'event_hashtag_retargeting';
                                $payload = new stdClass();
                                $payload->id = $recruiterData->id;
                                if($recruiterData->language_id == 2) {
                                    $payload->judul = "Retargeting Event";
                                    $payload->teks = "Anda telah menyelesaikan Misi Harian di Event user lama. Kami akan menverifikasi proses anda. Mohon menunggu instruksi berikutnya.";
                                } else {
                                 $payload->judul = "Retargeting Event";
                                    $payload->teks = "You have successfully completed the Daily Mission in our Event for old user. We are currently verifying your data. Please await further instructions.";
                                }
                                $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
                            }
                        }
                    }
                }
            }
        }

        // if ($this->is_log) {
        //     $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- status: ".$this->status." - ".$this->message);
        // }

        //by Donny Dennison - 25 august 2020 20:15
        //fix user setting not save to db
        if ($register_success && !empty($user_id)) {
            $this->status = 200;
            // $this->message = 'registration successful, please check your inbox or spam before login';
            $this->message = 'Success';
        } else {
            $this->status = 1706;
            $this->message = 'Failed save user to database, please try again';
        }

        $this->bu->trans_commit();
        //release table
        $this->bu->trans_end();

        //output as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    /**
     * Authentification for buyer or seller through mobile apps
     */
    public function login()
    {
        //init
        $dt = $this->__init();

        //default result format
        $data = array();
        $data['apisess'] = '';
        $data['apisess_expired'] = '';
        $data['pelanggan'] = new stdClass();
        $data['can_input_referral'] = '0';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //default message
        $this->status = 400;
        $this->message = 'Missing or invalid API key';

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        $username = strtolower(trim($this->input->request('username')));
        $password = $this->__passClear($this->input->request('password'));
        $fcm_token = $this->input->request('fcm_token');
        $device = strtolower(trim($this->input->request('device')));
        if (strlen($device)<=2) {
            $device = '';
        }
        // if ($this->is_log) {
        //     $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan::login -> ".$username." - ".$device);
        // }

        $blackList = $this->gblm->check($nation_code, "fcm_token", $fcm_token);
        if(isset($blackList->id)){
            // $this->status = 1707;
            // $this->message = 'Invalid email or password';
            $this->status = 1728;
            $this->message = "Sorry, you're banned to log in, please contact WA(+65 8856 2024)";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        // $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::login -- custom log var_dump fcm_token from mobile, user '.$username.' with fcm_token '.$fcm_token);

        $res = 0;
        if (strlen($username) && strlen($password)) {
            $res = $this->bu->auth($nation_code, $username);
            if (isset($res->id)) {
                if (isset($res->fnama)) {
                    $res->fnama = $this->__dconv($res->fnama);
                }
                if (isset($res->lnama)) {
                    $res->lnama = $this->__dconv($res->lnama);
                }            

                // //flush old fcm_token
                // if (strlen($res->fcm_token)>6) {
                //     $fcm_token_old = explode(':', $res->fcm_token);
                //     if (isset($fcm_token_old[0])) {
                //         $fcm_token_old = $fcm_token_old[0];
                //     }
                //     if (is_string($fcm_token_old)) {
                //         $this->bu->flushFcmToken($fcm_token_old);
                //         if ($this->is_log) {
                //             $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan::login --flushFCM fcm_token_old: $fcm_token_old");
                //         }
                //     }
                // }

                //check password
                if (!hash_equals($res->password, hash("sha256", $password))) {
                    $this->status = 1707;
                    $this->message = 'Invalid email or password';
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                    die();
                }

                //by Donny Dennison - 19 july 2022 15:42
                //delete temporary or permanent user feature
                // if (empty($res->is_active)) {
                if ($res->is_permanent_inactive == 0) {
                    $this->status = 1728;
                    $this->message = "Sorry, you're banned to log in, please contact WA(+65 8856 2024)";
                    // if ($this->is_log) {
                    //     $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::login -- ForcedClose '.$this->status.' '.$this->message.' ID: '.$res->id);
                    // }
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                    die();
                }

                //START by Donny Dennison - 13 december 2022 14:31
                //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device
                $device_id = trim($this->input->post("device_id"));

                $checkUserHaveDevice = $this->gdlm->countAll($nation_code, $res->id, $device_id);
                if($checkUserHaveDevice == 0){
                    $totalUsedDeviceId = $this->gdlm->countAll($nation_code, "", $device_id);

                    //get max used in 1 device
                    $maxUsed = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C6");
                    if (!isset($maxUsed->remark)) {
                        $maxUsed = new stdClass();
                        $maxUsed->remark = 5;
                    }

                    if($totalUsedDeviceId >= $maxUsed->remark){
                        $this->status = 1726;
                        $this->message = "You're not allowed to use many accounts";
                        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                        die();
                    }
                }
                //END by Donny Dennison - 13 december 2022 14:31
                //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device

                if ($this->email_send && strlen($res->email)>4 && empty($res->is_confirmed)) {
                    if (strlen($res->fb_id)<=0 && strlen($res->google_id)<=0) {
                        $link = $this->__activateGenerateLink($nation_code, $res->id, $res->api_reg_token);
                        $email = $res->email;
                        $nama = $res->fnama;
                        $replacer = array();
                        $replacer['site_name'] = $this->app_name;
                        $replacer['fnama'] = $nama;
                        $replacer['activation_link'] = $link;
                        $this->seme_email->flush();
                        $this->seme_email->replyto($this->site_name, $this->site_replyto);
                        $this->seme_email->from($this->site_email, $this->site_name);
                        $this->seme_email->subject('Please Confirm your email');
                        $this->seme_email->to($email, $nama);
                        $this->seme_email->template('account_register');
                        $this->seme_email->replacer($replacer);
                        $this->seme_email->send();

                        //$this->status = 1722;
                        //$this->message = 'You have unconfirmed email, please check your email inbox or spam';
                        // if ($this->is_log) {
                        //     $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan::login --userID: $res->id --unconfirmedEmail");
                        // }
                        //$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                        //die();
                    }
                }
                $dux = array();
                $dux['api_mobile_edate'] = date('Y-m-d H:i:00',strtotime("+$this->expired_token day"));
                if (strlen($fcm_token)>6) {
                    $this->bu->flushFcm($fcm_token);
                    $dux['fcm_token'] = $fcm_token;
                    $dux['device'] = $device;
                    $res->fcm_token = $fcm_token;
                    // $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::login -- INFO FCM_TOKEN and Device has been updated for UID: '.$res->id.'');
                    // $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::login -- custom log var_dump new fcm_token inserted to db '.$dux['fcm_token']);
                }
                if(is_array($dux) && count($dux)) $this->bu->update($nation_code, $res->id, $dux);

                $token_plain = $this->__activateMobileToken($nation_code, $res->id);
                $data['apisess'] = $token_plain;
                $data['apisess_expired'] = $dux['api_mobile_edate'];

                //by Donny Dennison - 25 august 2020 20:15
                //fix user setting not save to db
                // $this->status = 200;
                // $this->message = 'Success';

                // by Muhammad Sofi - 26 October 2021 11:16
                // if user img & banner not exist or empty, change to default image
                // $res->image = base_url($res->image);
                if(file_exists(SENEROOT.$res->image) && $res->image != 'media/user/default.png'){
                    $res->image = base_url($res->image);
                } else {
                    $res->image = $this->cdn_url('media/user/default-profile-picture.png');
                }

                //by Donny Dennison - 13 july 2021 10:46
                //show-default-address-after-login
                $res->default_address = $this->bua->getByUserIdDefault($nation_code, $res->id);

                //by Donny Dennison - 08-09-2021 11:35
                //revamp-profile

                // by Muhammad Sofi - 26 October 2021 11:16
                // if user img & banner not exist or empty, change to default image
                // $res->image_banner = base_url($res->image_banner);
                if(file_exists(SENEROOT.$res->image_banner)){
                    $res->image_banner = base_url($res->image_banner);
                } else {
                    $res->image_banner = $this->cdn_url('media/user/default.png');
                }
                
                $res->apisess = $token_plain;
                $res->apisess_expired = $dux['api_mobile_edate'];
                $res->api_mobile_edate = $dux['api_mobile_edate'];
                unset($res->api_mobile_token);
                unset($res->api_web_token);
                unset($res->api_reg_token);
                unset($res->password);
                //unset($res->fb_id);
                //unset($res->google_id);

                // $this->__callUserSettings($nation_code, $token_plain);
                $settingController = new setting();
                $token = hash('sha256',$token_plain);
                $settingController->notificationcustom($nation_code, $apikey, $token);
            } else {
                $this->status = 1722;
                $this->message = 'User currently unregistered with any Social ID or Email';
            }
        } else {
            $this->status = 1710;
            $this->message = 'Email or password incorrect, please check again';
        }

        $data['pelanggan'] = $res;
        // if ($this->is_log) {
        //     $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan::login -> ".$this->message);
        // }
        // if ($this->is_log) {
        //     $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan::login --end");
        // }
        
        //by Donny Dennison - 25 august 2020 20:15
        //fix user setting not save to db
        if (strlen($username) && strlen($password)) {
            if (isset($res->id)) {
                $this->status = 200;
                $this->message = 'Success';

                $du = array();
                //request uncomment from mr jackie(7 nov 2023 14:59 by verbal)
                $du['is_online'] = 1;
                $du['last_online'] = date('Y-m-d H:i:s');

                //by Donny Dennison - 19 july 2022 15:42
                //delete temporary or permanent user feature
                $du['is_active'] = 1;

                $du['ip_address'] = $_SERVER['HTTP_X_REAL_IP'];
                $this->bu->update($nation_code, $res->id, $du);

                $data['pelanggan']->is_online = "1";

                $data['pelanggan']->total_product = $this->cpm->countAll($nation_code, "", "",$res->id, "", "", array(), array(), array(), "All", $data['pelanggan']->default_address, "ProtectionAndMeetUpAndAutomotive", 0, '', array(), array(), array(), 1);

                //START by Donny Dennison - 10 november 2022 14:34
                //new feature, join/input referral
                $limit = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E7");
                if (!isset($limit->remark)) {
                  $limit = new stdClass();
                  $limit->remark = 5;
                }

                if($data['pelanggan']->b_user_id_recruiter == '0' && date("Y-m-d", strtotime($data['pelanggan']->cdate." +".$limit->remark." days")) > date("Y-m-d")){
                    $data['can_input_referral'] = '1';
                }
                //END by Donny Dennison - 10 november 2022 14:34
                //new feature, join/input referral

                //START by Donny Dennison - 13 december 2022 14:31
                //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device
                $di = array();
                $di["nation_code"] = $nation_code;
                $endDoWhile = 0;
                do{
                  $di["id"] = $this->GUIDv4();
                  $checkId = $this->gdlm->checkId($nation_code, $di["id"]);
                  if($checkId == 0){
                      $endDoWhile = 1;
                  }
                }while($endDoWhile == 0);
                $di["b_user_id"] = $res->id;
                $di["device_id"] = $device_id;
                $di["type"] = "login";
                $di["cdate"] = "NOW()";
                $this->gdlm->set($di);
                //END by Donny Dennison - 13 december 2022 14:31
                //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device

                $data['pelanggan']->bKodeRecuiter = "";
                $data['pelanggan']->bNamaRecuiter = "";
                if($data['pelanggan']->b_user_id_recruiter != '0'){
                    $recommenderData = $this->bu->getById($nation_code, $data['pelanggan']->b_user_id_recruiter);
                    if(isset($recommenderData->kode_referral)){
                        $data['pelanggan']->bKodeRecuiter = $recommenderData->kode_referral;
                        $data['pelanggan']->bNamaRecuiter = $recommenderData->fnama;
                    }
                }
            } else {
                $this->status = 1722;
                $this->message = 'User currently unregistered with any Social ID or Email';
            }
        } else {
            $this->status = 1710;
            $this->message = 'Email or password incorrect, please check again';
        }

        $data['pelanggan']->wallet_access = $this->ccm->getByClassifiedAndCode('62', "app_config", "C26")->remark;

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    public function login_sosmedv2()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['apisess'] = '';
        $data['apisess_expired'] = '';
        $data['pelanggan'] = new stdClass();
        $data['can_input_referral'] = '0';
        $data['is_register'] = '0';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //default response

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (empty($c)) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        // $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::login_sosmed -- START');

        //populating input
        $email = strtolower(trim($this->input->post("email")));
        $fb_id = $this->input->post("fb_id");
        $apple_id = $this->input->post("apple_id");
        $google_id = $this->input->post("google_id");
        $telp = $this->input->post("telp");
        $fcm_token = $this->input->post("fcm_token");
        $device = strtolower(trim($this->input->post("device")));
        $is_register = $this->input->post("is_register");
        if($is_register != 1){
            $is_register = 0;
        }

        $blackList = $this->gblm->check($nation_code, "fcm_token", $fcm_token);
        if(isset($blackList->id)){
            // $this->status = 1707;
            // $this->message = 'Invalid email or password';
            $this->status = 1728;
            $this->message = "Sorry, you're banned to log in, please contact WA(+65 8856 2024)";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        // $address_latitude = $this->input->post('address_latitude');
        // $address_longitude = $this->input->post('address_longitude');
        // $latlng = $address_latitude.','.$address_longitude;

        // $url = "https://maps.google.com/maps/api/geocode/json?latlng=$latlng&key=AIzaSyCzx5dDTHwJLrgqgM0LXH6D1VGAHC0m4Rw&language=id";

		// $ch = curl_init();
		// curl_setopt($ch, CURLOPT_URL, $url);
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// // curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		// // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		// // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		// $response = curl_exec($ch);
		// curl_close($ch);
		// $response_a = json_decode($response);
		// $response_geocode = $response_a->results[0]->address_components;
		// foreach ($response_geocode as $geo) { 
		// 	$type_geo = $geo->types[0];

		// 	// if($type_geo == "route") {
		// 	// 	$address_alamat2 = $geo->long_name;
		// 	// } 
		// 	if($type_geo == "administrative_area_level_4") {
		// 		$address_kelurahan = $geo->long_name;
		// 	}
		// 	if($type_geo == "administrative_area_level_3") {
        //         $address_kecamatan_long = $geo->long_name;

        //         if (strpos($address_kecamatan_long, 'Kecamatan') !== false) {
        //             $address_kecamatan = str_replace("Kecamatan", "", $address_kecamatan_long);
        //         } else {
        //             $address_kecamatan = $geo->long_name;
        //         }
		// 	}
        //     if($type_geo == "administrative_area_level_2") {
        //         $address_kabkota_long = $geo->long_name;

        //         if (strpos($address_kabkota_long, 'Kabupaten') !== false) {
        //             $address_kabkota = str_replace("Kabupaten", "", $address_kabkota_long);
        //         }else if (strpos($address_kabkota_long, 'Kota') !== false) {
        //             $address_kabkota = str_replace("Kota", "", $address_kabkota_long);
        //         } else {
        //             $address_kabkota = $geo->long_name;
        //         }
		// 	} 
		// 	if($type_geo == "administrative_area_level_1") {
		// 		$address_provinsi_long = $geo->long_name;

        //         if (strpos($address_provinsi_long, 'Daerah Khusus Ibukota') !== false) {
        //             $address_provinsi = str_replace("Daerah Khusus Ibukota", "DKI", $address_provinsi_long);
        //         } else {
        //             $address_provinsi = $geo->long_name;
        //         }
		// 	} 
		// 	if($type_geo == "country") {
		// 		$country_origin = $geo->long_name;
		// 		$country_origin = strtolower($country_origin);
		// 		$country_short = $geo->short_name;
		// 	} 
		// 	if($type_geo == "postal_code") {
		// 		$address_kodepos = $geo->long_name;
		// 	} 
		// }

        // $alamat2 = $response_a->results[0]->formatted_address;
        // $new_alamat2 = explode(",", $alamat2);
        // $address_alamat2 = $new_alamat2[1];

        // // check if empty
        // if(empty($address_alamat2) || empty($address_provinsi) || empty($address_kabkota) || empty($address_kecamatan) || empty($address_kelurahan) || empty($address_latitude) || empty($address_longitude)){
        //     //START by Donny Dennison - 08 june 2022 15:15
        //     //change address flow in register
        //     // $this->status = 104;
        //     // $this->message = 'There is address that empty';
        //     // $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //     // die();

        //     $address_alamat2 = "Gg. H. Zakaria, RW.5";
        //     $address_provinsi = "DKI Jakarta";
        //     $address_kabkota = "Jakarta Pusat";
        //     $address_kecamatan = "Tanah Abang";
        //     $address_kelurahan = "Kebon Melati";
        //     $address_kodepos = "10230";
        //     $address_latitude = "-6.200055499719067";
        //     $address_longitude = "106.8162468531788";
        //     //END by Donny Dennison - 08 june 2022 15:15
        // }

        // if (strlen($fcm_token)<=100) {
        //     $fcm_token='';
        // }
        if (strlen($device)==3) {
            $device='ios';
        } else {
            $device='android';
        }

        //sanitize input
        if (empty($google_id)) {
            $google_id = "";
        }
        if (empty($fb_id)) {
            $fb_id = "";
        }
        if (empty($apple_id)) {
            $apple_id = "";
        }
        if (strlen($email)<=4) {
            $email = "";
        }

        // if(empty($fb_id) || empty($apple_id)) {
        //     $address_latitude = $this->input->post('address_latitude');
        //     $address_longitude = $this->input->post('address_longitude');
        //     $latlng = $address_latitude.','.$address_longitude;
        
        //     $url = "https://maps.google.com/maps/api/geocode/json?latlng=$latlng&key=AIzaSyCzx5dDTHwJLrgqgM0LXH6D1VGAHC0m4Rw&language=id";

        //     $ch = curl_init();
        //     curl_setopt($ch, CURLOPT_URL, $url);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //     // curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        //     // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        //     // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //     $response = curl_exec($ch);
        //     curl_close($ch);
        //     $response_a = json_decode($response);
        //     $response_geocode = $response_a->results[0]->address_components;
        //     foreach ($response_geocode as $geo) { 
        //         $type_geo = $geo->types[0];
    
        //         // if($type_geo == "route") {
        //         // 	$address_alamat2 = $geo->long_name;
        //         // } 
        //         if($type_geo == "administrative_area_level_4") {
        //             $address_kelurahan = $geo->long_name;
        //         }
        //         if($type_geo == "administrative_area_level_3") {
        //             $address_kecamatan_long = $geo->long_name;

        //             if (strpos($address_kecamatan_long, 'Kecamatan') !== false) {
        //                 $address_kecamatan = str_replace("Kecamatan", "", $address_kecamatan_long);
        //             } else {
        //                 $address_kecamatan = $geo->long_name;
        //             }
        //         }
        //         if($type_geo == "administrative_area_level_2") {
        //             $address_kabkota_long = $geo->long_name;

        //             if (strpos($address_kabkota_long, 'Kabupaten') !== false) {
        //                 $address_kabkota = str_replace("Kabupaten", "", $address_kabkota_long);
        //             }else if (strpos($address_kabkota_long, 'Kota') !== false) {
        //                 $address_kabkota = str_replace("Kota", "", $address_kabkota_long);
        //             } else {
        //                 $address_kabkota = $geo->long_name;
        //             }
        //         } 
        //         if($type_geo == "administrative_area_level_1") {
        //             $address_provinsi_long = $geo->long_name;

        //             if (strpos($address_provinsi_long, 'Daerah Khusus Ibukota') !== false) {
        //                 $address_provinsi = str_replace("Daerah Khusus Ibukota", "DKI", $address_provinsi_long);
        //             } else {
        //                 $address_provinsi = $geo->long_name;
        //             }
        //         } 
        //         if($type_geo == "country") {
        //             $country_origin = $geo->long_name;
        //             $country_origin = strtolower($country_origin);
        //             $country_short = $geo->short_name;
        //         } 
        //         if($type_geo == "postal_code") {
        //             $address_kodepos = $geo->long_name;
        //         } 
        //     }

        //     $alamat2 = $response_a->results[0]->formatted_address;
        //     $new_alamat2 = explode(",", $alamat2);
        //     $address_alamat2 = $new_alamat2[1];

        //     // check if empty
        //     if(empty($address_alamat2) || empty($address_provinsi) || empty($address_kabkota) || empty($address_kecamatan) || empty($address_kelurahan) || empty($address_latitude) || empty($address_longitude)){
        //         //START by Donny Dennison - 08 june 2022 15:15
        //         //change address flow in register
        //         // $this->status = 104;
        //         // $this->message = 'There is address that empty';
        //         // $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //         // die();

        //         $address_alamat2 = "Gg. H. Zakaria, RW.5";
        //         $address_provinsi = "DKI Jakarta";
        //         $address_kabkota = "Jakarta Pusat";
        //         $address_kecamatan = "Tanah Abang";
        //         $address_kelurahan = "Kebon Melati";
        //         $address_kodepos = "10230";
        //         $address_latitude = "-6.200055499719067";
        //         $address_longitude = "106.8162468531788";
        //         //END by Donny Dennison - 08 june 2022 15:15
        //     }
        // }

        //initial variable
        $user = new stdClass();
        if (strlen($google_id)>1 && strlen($fb_id)>1 && strlen($apple_id)>1) {
            $user = $this->bu->checkGoogleID($nation_code, $google_id);
            if (!isset($user->id)) {
                $user = $this->bu->checkAppleID($nation_code, $apple_id);
            }
            if (!isset($user->id)) {
                $user = $this->bu->checkFBID($nation_code, $fb_id);
            }
            if (!isset($user->id)) {
                $user = $this->bu->checkEmail($nation_code, $email);
            }
            if (isset($user->id)) {
                if (($user->email != $email) && strlen($email)>4) {
                    $this->status = 1711;
                    $this->message = 'Google ID or FBID or AppleID not related to current email';
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                    die();
                }
            }
        } elseif (strlen($google_id)>1 && strlen($fb_id)<=0 && strlen($apple_id)<=0) {
            $user = $this->bu->checkGoogleID($nation_code, $google_id);
            if (!isset($user->id)) {
                $user = $this->bu->checkEmail($nation_code, $email);
            }
            if (isset($user->id)) {
                if (($user->email != $email) && strlen($email)>4) {
                    $this->status = 1712;
                    $this->message = 'Google ID not related to current email';
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                    die();
                }
            }
        } elseif (strlen($google_id)<=0 && strlen($fb_id)>1 && strlen($apple_id)<=0) {
            $user = $this->bu->checkFBID($nation_code, $fb_id);
            if (!isset($user->id) && strlen($email)>4) {
                $user = $this->bu->checkEmail($nation_code, $email);
                if (isset($user->id)) {
                    if ($user->email != $email) {
                        $this->status = 1713;
                        $this->message = 'FB ID not related to current email';
                        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                        die();
                    }
                }
            }
            if (isset($user->id)) {
                $email = $user->email;
            }

        } elseif (strlen($google_id)<=0 && strlen($fb_id)<=0 && strlen($apple_id)>1) {
            $user = $this->bu->checkAppleID($nation_code, $apple_id);
            if (isset($user->id)) {
                // because apple_id sometimes hide their email
                // So we will bypassed email checker for apple_id only
                $email = $user->email;
            }
        }

        if(!isset($user->id) && $is_register == 1 && (strlen($google_id)<=0 && strlen($fb_id)>1 && strlen($apple_id)<=0)){

            $fnama = $this->input->post("fnama");
            if(empty($fnama)){
                $fnama = "no name";   
            }

            $coverage_id = trim($this->input->post("coverage_id"));
            $is_changed_address = trim($this->input->post("is_changed_address"));

            if($is_changed_address != 1){
                $is_changed_address = 0;
            }

            $country_origin = strtolower(trim($this->input->post("country_origin")));
            if(empty($country_origin)){
                $country_origin = "indonesia";   
            }

            if($country_origin != "indonesia"){
                $is_changed_address = 1;
            }

            if (strlen($email)<=4) {
                do {
                    $permitted_chars = "0123456789";
                    $permitted_chars_length = strlen($permitted_chars);
                    $length = 10;
                    $email = 'fb';
                    for($i = 0; $i < $length; $i++) {
                        $random_character = $permitted_chars[mt_rand(0, $permitted_chars_length - 1)];
                        $email .= $random_character;
                    }
                    $email .= "@sellon.net";
                    //check already in db or havent
                    $checkEmail = $this->bu->checkEmail($nation_code, $email);
                } while (isset($checkEmail->id));
            }

            // start comment code
            // $address_latitude = $this->input->post('address_latitude');
            // $address_longitude = $this->input->post('address_longitude');
            // $latlng = $address_latitude.','.$address_longitude;
            // $url = "https://maps.google.com/maps/api/geocode/json?latlng=$latlng&key=AIzaSyCzx5dDTHwJLrgqgM0LXH6D1VGAHC0m4Rw&language=id";

            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL, $url);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // // curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            // // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            // // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            // $response = curl_exec($ch);
            // curl_close($ch);
            // $response_a = json_decode($response);
            // $response_geocode = $response_a->results[0]->address_components;
            // foreach ($response_geocode as $geo) { 
            //     $type_geo = $geo->types[0];
    
            //     // if($type_geo == "route") {
            //     //   $address_alamat2 = $geo->long_name;
            //     // } 
            //     if($type_geo == "administrative_area_level_4") {
            //         $address_kelurahan = $geo->long_name;
            //     }
            //     if($type_geo == "administrative_area_level_3") {
            //         $address_kecamatan_long = $geo->long_name;

            //         if (strpos($address_kecamatan_long, 'Kecamatan') !== false) {
            //             $address_kecamatan = str_replace("Kecamatan", "", $address_kecamatan_long);
            //         } else {
            //             $address_kecamatan = $geo->long_name;
            //         }
            //     }
            //     if($type_geo == "administrative_area_level_2") {
            //         $address_kabkota_long = $geo->long_name;

            //         if (strpos($address_kabkota_long, 'Kabupaten') !== false) {
            //             $address_kabkota = str_replace("Kabupaten", "", $address_kabkota_long);
            //         }else if (strpos($address_kabkota_long, 'Kota') !== false) {
            //             $address_kabkota = str_replace("Kota", "", $address_kabkota_long);
            //         } else {
            //             $address_kabkota = $geo->long_name;
            //         }
            //     } 
            //     if($type_geo == "administrative_area_level_1") {
            //         $address_provinsi_long = $geo->long_name;

            //         if (strpos($address_provinsi_long, 'Daerah Khusus Ibukota') !== false) {
            //             $address_provinsi = str_replace("Daerah Khusus Ibukota", "DKI", $address_provinsi_long);
            //         } else {
            //             $address_provinsi = $geo->long_name;
            //         }
            //     } 
            //     if($type_geo == "country") {
            //         $country_origin = $geo->long_name;
            //         $country_origin = strtolower($country_origin);
            //         $country_short = $geo->short_name;
            //     } 
            //     if($type_geo == "postal_code") {
            //         $address_kodepos = $geo->long_name;
            //     } 
            // }
    
            // $alamat2 = $response_a->results[0]->formatted_address;
            // $new_alamat2 = explode(",", $alamat2);
            // // $address_alamat2 = $new_alamat2[1];

            // $alamat2 = "";
            // foreach($new_alamat2 as $na) {
            //     if(stripos($na, "Jl.") !== false) {
            //         // echo "true array 0 \n";
            //         $alamat2 = $na;
            //         break;
            //     } else if(stripos($na, "Jl.") !== false) {
            //         // echo "true array 1 \n";
            //         $alamat2 = $na;
            //         break;
            //     }   
            // }
            // $address_alamat2 = $alamat2;

    
            // // check if empty
            // if(empty($address_alamat2) || empty($address_provinsi) || empty($address_kabkota) || empty($address_kecamatan) || empty($address_kelurahan) || empty($address_latitude) || empty($address_longitude)){
            //     //START by Donny Dennison - 08 june 2022 15:15
            //     //change address flow in register
            //     // $this->status = 104;
            //     // $this->message = 'There is address that empty';
            //     // $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            //     // die();

            //     $address_alamat2 = "Gg. H. Zakaria, RW.5";
            //     $address_provinsi = "DKI Jakarta";
            //     $address_kabkota = "Jakarta Pusat";
            //     $address_kecamatan = "Tanah Abang";
            //     $address_kelurahan = "Kebon Melati";
            //     $address_kodepos = "10230";
            //     $address_latitude = "-6.200055499719067";
            //     $address_longitude = "106.8162468531788";
            //     //END by Donny Dennison - 08 june 2022 15:15
            // }
            // end comment code

            $postData= array(
                'fb_id' => $fb_id,
                'email' => $email,
                'fnama' => $fnama,
                'fcm_token' => $fcm_token,
                'device' => $device,
                'address_penerima_nama' => $fnama,
                'address_alamat2' => $this->input->post("address_alamat2"),
                'address_kelurahan' => $this->input->post("address_kelurahan"),
                'address_kecamatan' => $this->input->post("address_kecamatan"),
                'address_kabkota' => $this->input->post("address_kabkota"),
                'address_provinsi' => $this->input->post("address_provinsi"),
                'address_kodepos' => $this->input->post("address_kodepos"),
                'address_latitude' => $this->input->post("address_latitude"),
                'address_longitude' => $this->input->post("address_longitude"),
                'coverage_id' => $coverage_id,
                'is_changed_address' => $is_changed_address,
                'country_origin' => $country_origin,
                'kode_referral' => $this->input->post("kode_referral"),
                'referral_type' => $this->input->post("referral_type"),
                'device_id' => $this->input->post("device_id"),
                'call_from' => "1ns!d3r",
                'ip_address' => $_SERVER['HTTP_X_REAL_IP']
            );

            $this->lib("seme_curl");
            $url = base_url("api_mobile/pelanggan/daftarv2/?apikey=$apikey&nation_code=$nation_code");
            $curlResponse = $this->seme_curl->post($url, $postData);

            $body = json_decode($curlResponse->body);
            if ($body->status != 200) {
                $this->status = $body->status;
                $this->message = $body->message;
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }

            if($body->status == 200) {
                $data['is_register'] = '1';
            }

            usleep(500000);
            $user = $this->bu->checkFBID($nation_code, $fb_id);
            $email = $user->email;
        }

        if(!isset($user->id) && $is_register == 1 && (strlen($google_id)<=0 && strlen($fb_id)<=0 && strlen($apple_id)>1)){
            $fnama = $this->input->post("fnama");
            if(empty($fnama)){
                $fnama = "no name";   
            }

            $coverage_id = trim($this->input->post("coverage_id"));
            $is_changed_address = trim($this->input->post("is_changed_address"));
            if($is_changed_address != 1){
                $is_changed_address = 0;
            }

            $country_origin = strtolower(trim($this->input->post("country_origin")));
            if(empty($country_origin)){
                $country_origin = "indonesia";   
            }

            if($country_origin != "indonesia"){
                $is_changed_address = 1;
            }

            if (strlen($email)<=4) {
                do {
                    $permitted_chars = "0123456789abcdefghijklmnopqrstuvwxyz";
                    $permitted_chars_length = strlen($permitted_chars);
                    $length = 10;
                    $email = '';
                    for($i = 0; $i < $length; $i++) {
                        $random_character = $permitted_chars[mt_rand(0, $permitted_chars_length - 1)];
                        $email .= $random_character;
                    }
                    $email .= "@privaterelay.appleid.com";
                    //check already in db or havent
                    $checkEmail = $this->bu->checkEmail($nation_code, $email);
                } while (isset($checkEmail->id));
            }

            // start comment code
            // $address_latitude = $this->input->post('address_latitude');
            // $address_longitude = $this->input->post('address_longitude');
            // $latlng = $address_latitude.','.$address_longitude;

            // $url = "https://maps.google.com/maps/api/geocode/json?latlng=$latlng&key=AIzaSyCzx5dDTHwJLrgqgM0LXH6D1VGAHC0m4Rw&language=id";

            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL, $url);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // // curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            // // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            // // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            // $response = curl_exec($ch);
            // curl_close($ch);
            // $response_a = json_decode($response);
            // $response_geocode = $response_a->results[0]->address_components;
            // foreach ($response_geocode as $geo) { 
            //     $type_geo = $geo->types[0];

            //     // if($type_geo == "route") {
            //     //   $address_alamat2 = $geo->long_name;
            //     // } 
            //     if($type_geo == "administrative_area_level_4") {
            //         $address_kelurahan = $geo->long_name;
            //     }
            //     if($type_geo == "administrative_area_level_3") {
            //         $address_kecamatan_long = $geo->long_name;

            //         if (strpos($address_kecamatan_long, 'Kecamatan') !== false) {
            //             $address_kecamatan = str_replace("Kecamatan", "", $address_kecamatan_long);
            //         } else {
            //             $address_kecamatan = $geo->long_name;
            //         }
            //     }
            //     if($type_geo == "administrative_area_level_2") {
            //         $address_kabkota_long = $geo->long_name;

            //         if (strpos($address_kabkota_long, 'Kabupaten') !== false) {
            //             $address_kabkota = str_replace("Kabupaten", "", $address_kabkota_long);
            //         }else if (strpos($address_kabkota_long, 'Kota') !== false) {
            //             $address_kabkota = str_replace("Kota", "", $address_kabkota_long);
            //         } else {
            //             $address_kabkota = $geo->long_name;
            //         }
            //     } 
            //     if($type_geo == "administrative_area_level_1") {
            //         $address_provinsi_long = $geo->long_name;

            //         if (strpos($address_provinsi_long, 'Daerah Khusus Ibukota') !== false) {
            //             $address_provinsi = str_replace("Daerah Khusus Ibukota", "DKI", $address_provinsi_long);
            //         } else {
            //             $address_provinsi = $geo->long_name;
            //         }
            //     } 
            //     if($type_geo == "country") {
            //         $country_origin = $geo->long_name;
            //         $country_origin = strtolower($country_origin);
            //         $country_short = $geo->short_name;
            //     } 
            //     if($type_geo == "postal_code") {
            //         $address_kodepos = $geo->long_name;
            //     } 
            // }

            // $alamat2 = $response_a->results[0]->formatted_address;
            // $new_alamat2 = explode(",", $alamat2);
            // // $address_alamat2 = $new_alamat2[1];

            // $alamat2 = "";
            // foreach($new_alamat2 as $na) {
            //     if(stripos($na, "Jl.") !== false) {
            //         // echo "true array 0 \n";
            //         $alamat2 = $na;
            //         break;
            //     } else if(stripos($na, "Jl.") !== false) {
            //         // echo "true array 1 \n";
            //         $alamat2 = $na;
            //         break;
            //     }   
            // }
            // $address_alamat2 = $alamat2;

            // // check if empty
            // if(empty($address_alamat2) || empty($address_provinsi) || empty($address_kabkota) || empty($address_kecamatan) || empty($address_kelurahan) || empty($address_latitude) || empty($address_longitude)){
            //     //START by Donny Dennison - 08 june 2022 15:15
            //     //change address flow in register
            //     // $this->status = 104;
            //     // $this->message = 'There is address that empty';
            //     // $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            //     // die();

            //     $address_alamat2 = "Gg. H. Zakaria, RW.5";
            //     $address_provinsi = "DKI Jakarta";
            //     $address_kabkota = "Jakarta Pusat";
            //     $address_kecamatan = "Tanah Abang";
            //     $address_kelurahan = "Kebon Melati";
            //     $address_kodepos = "10230";
            //     $address_latitude = "-6.200055499719067";
            //     $address_longitude = "106.8162468531788";
            //     //END by Donny Dennison - 08 june 2022 15:15
            // }
            // end comment code

            $postData= array(
                'apple_id' => $apple_id,
                'email' => $email,
                'fnama' => $fnama,
                'fcm_token' => $fcm_token,
                'device' => $device,
                'address_penerima_nama' => $fnama,
                'address_alamat2' => $this->input->post("address_alamat2"),
                'address_kelurahan' => $this->input->post("address_kelurahan"),
                'address_kecamatan' => $this->input->post("address_kecamatan"),
                'address_kabkota' => $this->input->post("address_kabkota"),
                'address_provinsi' => $this->input->post("address_provinsi"),
                'address_kodepos' => $this->input->post("address_kodepos"),
                'address_latitude' => $this->input->post("address_latitude"),
                'address_longitude' => $this->input->post("address_longitude"),
                'coverage_id' => $coverage_id,
                'is_changed_address' => $is_changed_address,
                'country_origin' => $country_origin,
                'kode_referral' => $this->input->post("kode_referral"),
                'referral_type' => $this->input->post("referral_type"),
                'device_id' => $this->input->post("device_id"),
                'call_from' => "1ns!d3r",
                'ip_address' => $_SERVER['HTTP_X_REAL_IP']
            );

            $this->lib("seme_curl");
            $url = base_url("api_mobile/pelanggan/daftarv2/?apikey=$apikey&nation_code=$nation_code");
            $curlResponse = $this->seme_curl->post($url, $postData);

            $body = json_decode($curlResponse->body);
            if ($body->status != 200) {
                $this->status = $body->status;
                $this->message = $body->message;
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }

            if($body->status == 200) {
                $data['is_register'] = '1';
            }

            usleep(500000);
            $user = $this->bu->checkAppleID($nation_code, $apple_id);
            $email = $user->email;
        }

        //check email
        if (isset($user->id)) {
            if ($user->email != $email) {
                $this->status = 1720;
                $this->message = 'Email does not match with any social media ID';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }

            //START by Donny Dennison - 13 december 2022 14:31
            //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device
            $device_id = trim($this->input->post("device_id"));

            $checkUserHaveDevice = $this->gdlm->countAll($nation_code, $user->id, $device_id);
            if($checkUserHaveDevice == 0){
                $totalUsedDeviceId = $this->gdlm->countAll($nation_code, "", $device_id);

                //get max used in 1 device
                $maxUsed = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C6");
                if (!isset($maxUsed->remark)) {
                    $maxUsed = new stdClass();
                    $maxUsed->remark = 5;
                }

                if($totalUsedDeviceId >= $maxUsed->remark){
                    $this->status = 1726;
                    $this->message = "You're not allowed to use many accounts";
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                    die();
                }
            }
            //END by Donny Dennison - 13 december 2022 14:31
            //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device

            //by Donny Dennison - 19 july 2022 15:42
            //delete temporary or permanent user feature
            // if (empty($user->is_active)) {
            if ($user->is_permanent_inactive == 0) {
                $this->status = 1728;
                $this->message = "Sorry, you're banned to log in, please contact WA(+65 8856 2024)";
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }

            //check again
            $dux = array();
            $dux['api_mobile_edate'] = date('Y-m-d H:i:00',strtotime("+$this->expired_token day"));
            $dux['is_confirmed'] = 1;
            $dux['fcm_token'] = $fcm_token;
            $dux['device'] = $device;

            if (strlen($google_id)>1) {
                $dux['google_id'] = $google_id;
            }
            if (strlen($fb_id)>1) {
                $dux['fb_id'] = $fb_id;
            }
            if (strlen($apple_id)>1) {
                $dux['apple_id'] = $apple_id;
            }
            if (is_array($dux) && count($dux)) {
                $this->bu->update($nation_code, $user->id, $dux);
            }

            $user = $this->bu->getById($nation_code, $user->id);
            $token_plain = $this->__activateMobileToken($nation_code, $user->id);
            $data['apisess'] = $token_plain;
            $data['apisess_expired'] = $dux['api_mobile_edate'];
            // by Muhammad Sofi - 26 October 2021 11:16
            // if user img & banner not exist or empty, change to default image
            // $user->image = $this->cdn_url($user->image);
            if(file_exists(SENEROOT.$user->image) && $user->image != 'media/user/default.png'){
                $user->image = $this->cdn_url($user->image);
            } else {
                $user->image = $this->cdn_url('media/user/default-profile-picture.png');
            }

            //by Donny Dennison - 08-09-2021 11:35
            //revamp-profile

            // by Muhammad Sofi - 26 October 2021 11:16
            // if user img & banner not exist or empty, change to default image
            // $user->image_banner = $this->cdn_url($user->image_banner);
            if(file_exists(SENEROOT.$user->image_banner)){
                $user->image_banner = $this->cdn_url($user->image_banner);
            } else {
                $user->image_banner = $this->cdn_url('media/user/default.png');
            }

            //add user token
            $user->apisess = $token_plain;

            //remove sensitive content
            unset($user->api_mobile_token);
            unset($user->api_web_token);
            unset($user->api_reg_token);
            unset($user->password);
            //unset($user->fb_id);
            //unset($user->google_id);

            //by Donny Dennison - 25 august 2020 20:15
            //fix user setting not save to db
            // $this->status = 200;
            // $this->message = 'Success';

            //by Donny Dennison - 13 july 2021 10:46
            //show-default-address-after-login
            $user->default_address = $this->bua->getByUserIdDefault($nation_code, $user->id);

            $data['pelanggan'] = $user;

            //update user setting
            // $this->__callUserSettings($nation_code, $token_plain);
            $settingController = new setting();
            $token = hash('sha256',$token_plain);
            $settingController->notificationcustom($nation_code, $apikey, $token);
        } else {
            $this->status = 1722;
            $this->message = 'User currently unregistered with any Social ID or Email';
        }

        //by Donny Dennison - 25 august 2020 20:15
        //fix user setting not save to db
        if (isset($user->id)) {
            $this->status = 200;
            $this->message = 'Success';

            $du = array();
            //request uncomment from mr jackie(7 nov 2023 14:59 by verbal)
            $du['is_online'] = 1;
            $du['last_online'] = date('Y-m-d H:i:s');

            //by Donny Dennison - 19 july 2022 15:42
            //delete temporary or permanent user feature
            $du['is_active'] = 1;

            $du['ip_address'] = $_SERVER['HTTP_X_REAL_IP'];
            $this->bu->update($nation_code, $user->id, $du);

            $data['pelanggan']->is_online = "1";

            $data['pelanggan']->total_product = $this->cpm->countAll($nation_code, "", "",$user->id, "", "", array(), array(), array(), "All", $data['pelanggan']->default_address, "ProtectionAndMeetUpAndAutomotive", 0, '', array(), array(), array(), 1);

            //START by Donny Dennison - 10 november 2022 14:34
            //new feature, join/input referral
            $limit = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E7");
            if (!isset($limit->remark)) {
              $limit = new stdClass();
              $limit->remark = 5;
            }

            if($data['pelanggan']->b_user_id_recruiter == '0' && date("Y-m-d", strtotime($data['pelanggan']->cdate." +".$limit->remark." days")) > date("Y-m-d")){
                $data['can_input_referral'] = '1';
            }
            //END by Donny Dennison - 10 november 2022 14:34
            //new feature, join/input referral

            //START by Donny Dennison - 13 december 2022 14:31
            //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device
            $di = array();
            $di["nation_code"] = $nation_code;
            $endDoWhile = 0;
            do{
              $di["id"] = $this->GUIDv4();
              $checkId = $this->gdlm->checkId($nation_code, $di["id"]);
              if($checkId == 0){
                  $endDoWhile = 1;
              }
            }while($endDoWhile == 0);
            $di["b_user_id"] = $user->id;
            $di["device_id"] = $device_id;
            $di["type"] = "login";
            $di["cdate"] = "NOW()";
            $this->gdlm->set($di);
            //END by Donny Dennison - 13 december 2022 14:31
            //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device

            $data['pelanggan']->bKodeRecuiter = "";
            $data['pelanggan']->bNamaRecuiter = "";
            if($data['pelanggan']->b_user_id_recruiter != '0'){
                $recommenderData = $this->bu->getById($nation_code, $data['pelanggan']->b_user_id_recruiter);
                if(isset($recommenderData->kode_referral)){
                    $data['pelanggan']->bKodeRecuiter = $recommenderData->kode_referral;
                    $data['pelanggan']->bNamaRecuiter = $recommenderData->fnama;
                }
            }
        }else{
            $this->status = 1722;
            $this->message = 'User currently unregistered with any Social ID or Email';
        }

        // if ($this->is_log) {
        //     $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::login_sosmed -- END '.$this->status.' '.$this->message);
        // }

        $data['pelanggan']->wallet_access = $this->ccm->getByClassifiedAndCode('62', "app_config", "C26")->remark;

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    public function login_phone_number()
    {
        //init
        $dt = $this->__init();

        //default result format
        $data = array();
        $data['apisess'] = '';
        $data['apisess_expired'] = '';
        $data['pelanggan'] = new stdClass();
        $data['can_input_referral'] = '0';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //default message
        $this->status = 400;
        $this->message = 'Missing or invalid API key';

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        $telp = strtolower(trim($this->input->request('telp')));
        $fcm_token = $this->input->request('fcm_token');
        $device = strtolower(trim($this->input->request('device')));
        if (strlen($device)<=2) {
            $device = '';
        }
        // if ($this->is_log) {
        //     $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan::login_phone_number -> ".$telp." - ".$device);
        // }

        $blackList = $this->gblm->check($nation_code, "fcm_token", $fcm_token);
        if(isset($blackList->id)){
            // $this->status = 1707;
            // $this->message = 'Invalid email or password';
            $this->status = 1728;
            $this->message = "Sorry, you're banned to log in, please contact WA(+65 8856 2024)";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        // $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::login_phone_number -- custom log var_dump fcm_token from mobile, user '.$telp.' with fcm_token '.$fcm_token);

        //change message language in response/return
        $getlanguage = $this->input->get("language_id");

        $res = 0;
        if (strlen($telp)) {
            $res = $this->bu->checkTelpIgnoreActive($nation_code, $telp);
            if (isset($res->id)) {
                if (isset($res->fnama)) {
                    $res->fnama = $this->__dconv($res->fnama);
                }
                if (isset($res->lnama)) {
                    $res->lnama = $this->__dconv($res->lnama);
                }            

                // //flush old fcm_token
                // if (strlen($res->fcm_token)>6) {
                //     $fcm_token_old = explode(':', $res->fcm_token);
                //     if (isset($fcm_token_old[0])) {
                //         $fcm_token_old = $fcm_token_old[0];
                //     }
                //     if (is_string($fcm_token_old)) {
                //         $this->bu->flushFcmToken($fcm_token_old);
                //         if ($this->is_log) {
                //             $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan::login_phone_number --flushFCM fcm_token_old: $fcm_token_old");
                //         }
                //     }
                // }

                //by Donny Dennison - 19 july 2022 15:42
                //delete temporary or permanent user feature
                // if (empty($res->is_active)) {
                if ($res->is_permanent_inactive == 0) {
                    $this->status = 1728;
                    $this->message = "Sorry, you're banned to log in, please contact WA(+65 8856 2024)";
                    // if ($this->is_log) {
                    //     $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::login_phone_number -- ForcedClose '.$this->status.' '.$this->message.' ID: '.$res->id);
                    // }
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                    die();
                }

                //START by Donny Dennison - 13 december 2022 14:31
                //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device
                $device_id = trim($this->input->post("device_id"));

                $checkUserHaveDevice = $this->gdlm->countAll($nation_code, $res->id, $device_id);
                if($checkUserHaveDevice == 0){
                    $totalUsedDeviceId = $this->gdlm->countAll($nation_code, "", $device_id);

                    //get max used in 1 device
                    $maxUsed = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C6");
                    if (!isset($maxUsed->remark)) {
                        $maxUsed = new stdClass();
                        $maxUsed->remark = 5;
                    }

                    if($totalUsedDeviceId >= $maxUsed->remark){
                        $this->status = 1726;
                        $this->message = "You're not allowed to use many accounts";
                        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                        die();
                    }
                }
                //END by Donny Dennison - 13 december 2022 14:31
                //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device

                $dux = array();
                $dux['api_mobile_edate'] = date('Y-m-d H:i:00',strtotime("+$this->expired_token day"));
                if (strlen($fcm_token)>6) {
                    $this->bu->flushFcm($fcm_token);
                    $dux['fcm_token'] = $fcm_token;
                    $dux['device'] = $device;
                    $res->fcm_token = $fcm_token;
                    // $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::login_phone_number -- INFO FCM_TOKEN and Device has been updated for UID: '.$res->id.'');
                    // $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::login_phone_number -- custom log var_dump new fcm_token inserted to db '.$dux['fcm_token']);
                }
                if(is_array($dux) && count($dux)) $this->bu->update($nation_code, $res->id, $dux);

                $token_plain = $this->__activateMobileToken($nation_code, $res->id);
                $data['apisess'] = $token_plain;
                $data['apisess_expired'] = $dux['api_mobile_edate'];

                //by Donny Dennison - 25 august 2020 20:15
                //fix user setting not save to db
                // $this->status = 200;
                // $this->message = 'Success';

                // by Muhammad Sofi - 26 October 2021 11:16
                // if user img & banner not exist or empty, change to default image
                // $res->image = base_url($res->image);
                if(file_exists(SENEROOT.$res->image) && $res->image != 'media/user/default.png'){
                    $res->image = base_url($res->image);
                } else {
                    $res->image = $this->cdn_url('media/user/default-profile-picture.png');
                }

                //by Donny Dennison - 13 july 2021 10:46
                //show-default-address-after-login
                $res->default_address = $this->bua->getByUserIdDefault($nation_code, $res->id);

                //by Donny Dennison - 08-09-2021 11:35
                //revamp-profile

                // by Muhammad Sofi - 26 October 2021 11:16
                // if user img & banner not exist or empty, change to default image
                // $res->image_banner = base_url($res->image_banner);
                if(file_exists(SENEROOT.$res->image_banner)){
                    $res->image_banner = base_url($res->image_banner);
                } else {
                    $res->image_banner = $this->cdn_url('media/user/default.png');
                }

                $res->apisess = $token_plain;
                $res->apisess_expired = $dux['api_mobile_edate'];
                $res->api_mobile_edate = $dux['api_mobile_edate'];
                unset($res->api_mobile_token);
                unset($res->api_web_token);
                unset($res->api_reg_token);
                unset($res->password);
                //unset($res->fb_id);
                //unset($res->google_id);

                // $this->__callUserSettings($nation_code, $token_plain);
                $settingController = new setting();
                $token = hash('sha256',$token_plain);
                $settingController->notificationcustom($nation_code, $apikey, $token);
            } else {
                $this->status = 1727;
                $this->message = 'This number is not registered yet. Do you want to sign up with this number?';
            }
        } else {
            $this->status = 1716;
            $this->message = 'Telp incorrect, please check again';
        }

        $data['pelanggan'] = $res;
        // if ($this->is_log) {
        //     $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan::login_phone_number -> ".$this->message);
        // }
        // if ($this->is_log) {
        //     $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan::login_phone_number --end");
        // }

        //by Donny Dennison - 25 august 2020 20:15
        //fix user setting not save to db
        if (strlen($telp)) {
            if (isset($res->id)) {
                $this->status = 200;
                $this->message = 'Success';

                $du = array();
                //request uncomment from mr jackie(7 nov 2023 14:59 by verbal)
                $du['is_online'] = 1;
                $du['last_online'] = date('Y-m-d H:i:s');

                //by Donny Dennison - 19 july 2022 15:42
                //delete temporary or permanent user feature
                $du['is_active'] = 1;

                $du['ip_address'] = $_SERVER['HTTP_X_REAL_IP'];
                $this->bu->update($nation_code, $res->id, $du);

                $data['pelanggan']->is_online = "1";

                $data['pelanggan']->total_product = $this->cpm->countAll($nation_code, "", "",$res->id, "", "", array(), array(), array(), "All", $data['pelanggan']->default_address, "ProtectionAndMeetUpAndAutomotive", 0, '', array(), array(), array(), 1);

                //START by Donny Dennison - 10 november 2022 14:34
                //new feature, join/input referral
                $limit = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E7");
                if (!isset($limit->remark)) {
                  $limit = new stdClass();
                  $limit->remark = 5;
                }

                if($data['pelanggan']->b_user_id_recruiter == '0' && date("Y-m-d", strtotime($data['pelanggan']->cdate." +".$limit->remark." days")) > date("Y-m-d")){
                    $data['can_input_referral'] = '1';
                }
                //END by Donny Dennison - 10 november 2022 14:34
                //new feature, join/input referral

                //START by Donny Dennison - 13 december 2022 14:31
                //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device
                $di = array();
                $di["nation_code"] = $nation_code;
                $endDoWhile = 0;
                do{
                  $di["id"] = $this->GUIDv4();
                  $checkId = $this->gdlm->checkId($nation_code, $di["id"]);
                  if($checkId == 0){
                      $endDoWhile = 1;
                  }
                }while($endDoWhile == 0);
                $di["b_user_id"] = $res->id;
                $di["device_id"] = $device_id;
                $di["type"] = "login";
                $di["cdate"] = "NOW()";
                $this->gdlm->set($di);
                //END by Donny Dennison - 13 december 2022 14:31
                //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device

                $data['pelanggan']->bKodeRecuiter = "";
                $data['pelanggan']->bNamaRecuiter = "";
                if($data['pelanggan']->b_user_id_recruiter != '0'){
                    $recommenderData = $this->bu->getById($nation_code, $data['pelanggan']->b_user_id_recruiter);
                    if(isset($recommenderData->kode_referral)){
                        $data['pelanggan']->bKodeRecuiter = $recommenderData->kode_referral;
                        $data['pelanggan']->bNamaRecuiter = $recommenderData->fnama;
                    }
                }
            } else {
                $this->status = 1727;
                $this->message = 'This number is not registered yet. Do you want to sign up with this number?';
            }
        } else {
            $this->status = 1716;
            $this->message = 'Telp incorrect, please check again';
        }

        $data['pelanggan']->wallet_access = $this->ccm->getByClassifiedAndCode('62', "app_config", "C26")->remark;

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($getlanguage)) ? $getlanguage : "", "pelanggan");
    }

    /**
     * Forgot Password
     * POST     email
     */
    public function lupa()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['apisess'] = '';
        $data['apisess_expired'] = '';
        $data['pelanggan'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (empty($c)) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check email
        $email = strtolower(trim($this->input->post("email")));
        if (strlen($email)>6) {
            $user = $this->bu->getByEmail($nation_code, $email);
            if (isset($user->id)) {
                if ($this->email_send) {
                    $link = $this->__passwordGenerateLink($nation_code, $user->id);
                    //$this->lib('sene_email_sender');

                    $replacer = array();
                    $replacer['fnama'] = $user->fnama;
                    $replacer['site_name'] = $this->app_name;
                    $replacer['site_name1'] = $this->app_name;
                    $replacer['reset_link'] = $link;

                    $this->seme_email->flush();
                    $this->seme_email->replyto($this->site_name, $this->site_replyto);
                    $this->seme_email->from($this->site_email, $this->site_name);
                    $this->seme_email->subject('Forgot Password');
                    $this->seme_email->to($user->email, $user->fnama);
                    $this->seme_email->template('account_forgot');
                    $this->seme_email->replacer($replacer);
                    $this->seme_email->send();
                }
                $this->status = 200;
                // $this->message = 'Success, please check your email if you are registered';
                $this->message = 'Success';
            } else {
                $this->status = 1718;
                $this->message = 'It looks like the email you entered is incorrect, please check again';
            }
            //$this->status = 200;
            //$this->message = 'Success';
        } else {
            $this->status = 1718;
            $this->message = 'It looks like the email you entered is incorrect, please check again';
        }

        // if ($this->is_log) {
        //     $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::lupa -- END '.$this->status.' '.$this->message);
        // }
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    /**
     * User edit profile
     */
    public function edit()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['pelanggan'] = new stdClass();
        $data['can_input_referral'] = '0';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //START by Donny Dennison - 10 november 2022 14:34
        //new feature, join/input referral
        $limit = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E7");
        if (!isset($limit->remark)) {
          $limit = new stdClass();
          $limit->remark = 5;
        }

        if($pelanggan->b_user_id_recruiter == '0' && date("Y-m-d", strtotime($pelanggan->cdate." +".$limit->remark." days")) > date("Y-m-d")){
            $data['can_input_referral'] = '1';
        }
        //END by Donny Dennison - 10 november 2022 14:34
        //new feature, join/input referral

        //populating input
        $du = array();
        if (isset($_POST['fnama'])) {
            $fnama = trim($_POST['fnama']);
            //$fnama = mb_ereg_replace('[a-zA-Z0-9\s,.!?]', '', $fnama);
            $l = $this->__mbLen($fnama);
            if ($l>=64) {
                $this->status = 1748;
                $this->message = 'Name too long';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            } elseif ($l > 0 && $l<64) {
                $du['fnama'] = $fnama;
            }
        }
        if (isset($_POST['intro_teks'])) {
            $du['intro_teks'] = $this->input->post('intro_teks');
        }
        if (isset($_POST['telp'])) {
            $du['telp'] = $this->input->post('telp');
            if ($this->__mbLen($du['telp'])>=32) {
                $this->status = 1739;
                $this->message = 'Phone number too long';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }
        }
        if (isset($_POST['kelamin'])) {
            $du['kelamin'] = (int) $this->input->post('kelamin');
            if (!empty($du['kelamin'])) {
                $du['kelamin'] = 1;
            } else {
                $du['kelamin'] = 0;
            }
        }
        if (isset($_POST['bdate'])) {
            $du['bdate'] = $this->input->post('bdate');
        }

        //by Donny Dennison - 10 december 2020 15:01
        //new registration system for apple id
        //START by Donny Dennison - 10 december 2020 15:01
        $sendEmailVerification = 0;
        // if (strpos($pelanggan->email, '@privaterelay.appleid.com') !== false) {
        //     $du['email'] = strtolower(trim($this->input->post("email")));
        //     //check email already exist in db or not
        //     $emailExistOrNot = $this->bu->getByEmail($nation_code, $du['email']);
        //     if (isset($emailExistOrNot->id)) {
        //         $this->status = 1748;
        //         $this->message = 'Email already registered. Please add other email';
        //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //         die();
        //     }
        //     $du['is_confirmed'] = 0;
        //     $sendEmailVerification = 1;
        // }
        //END by Donny Dennison - 10 december 2020 15:01

        //by Donny Dennison - 08-09-2021 11:35
        //revamp-profile
        if($this->input->post('bio') && $this->input->post('website')){
            $du['bio'] = $this->input->post('bio');
            $du['website'] = $this->input->post('website');
        }

        //by Donny Dennison - 08 june 2022 15:15
        //change address flow in register
        if($this->input->post('is_changed_address')){
            $du['is_changed_address'] = $this->input->post('is_changed_address');
        }

        $res = $this->bu->update($nation_code, $pelanggan->id, $du);
        if ($res) {
            $pelanggan = $this->bu->getById($nation_code, $pelanggan->id);
            $this->status = 200;
            $this->message = 'Success';

            //by Donny Dennison - 27 october 2021 11:00
            //if edit profile then also change address penerima_nama and penerima_telp
            //START by Donny Dennison - 27 october 2021 11:00

            if (isset($du['fnama'])) {
                $di = array();
                $di['penerima_nama'] = $du['fnama'];
                $this->bua->updateByUserId($nation_code, $pelanggan->id, $di);
            }
            
            if (isset($du['telp'])) {
                $di = array();
                $di['penerima_telp'] = $du['telp'];
                $this->bua->updateByUserId($nation_code, $pelanggan->id, $di);
            }
            //END by Donny Dennison - 27 october 2021 11:00
        } else {
            $this->status = 1730;
            $this->message = 'Failed updating profile';
        }
        $data['pelanggan'] = $pelanggan;
        // by Muhammad Sofi - 26 October 2021 11:16
        // if user img & banner not exist or empty, change to default image
        // if (isset($data['pelanggan']->image)) {
        //    $data['pelanggan']->image = $this->cdn_url($data['pelanggan']->image);
        // }  
        if (isset($data['pelanggan']->image)) {
            if(file_exists(SENEROOT.$data['pelanggan']->image) && $data['pelanggan']->image != 'media/user/default.png'){
                $data['pelanggan']->image = $this->cdn_url($data['pelanggan']->image);
            } else {
                $data['pelanggan']->image = $this->cdn_url('media/user/default-profile-picture.png');
            }
        } 

        //by Donny Dennison - 08-09-2021 11:35
        //revamp-profile

        // by Muhammad Sofi - 26 October 2021 11:16
        // if user img & banner not exist or empty, change to default image
        // if (isset($data['pelanggan']->image_banner)) {
        //     $data['pelanggan']->image_banner = $this->cdn_url($data['pelanggan']->image_banner);
        // }
        if (isset($data['pelanggan']->image_banner)) {
            if(file_exists(SENEROOT.$data['pelanggan']->image_banner)){
                $data['pelanggan']->image_banner = $this->cdn_url($data['pelanggan']->image_banner);
            } else {
                $data['pelanggan']->image_banner = $this->cdn_url('media/user/default.png');
            }
        }  

        unset($data['pelanggan']->password);
        unset($data['pelanggan']->api_web_token);
        unset($data['pelanggan']->api_reg_token);
        unset($data['pelanggan']->api_mobile_token);

        //by Donny Dennison - 10 december 2020 15:01
        //new registration system for apple id
        //START by Donny Dennison - 10 december 2020 15:01
        if ($sendEmailVerification == 1) {
            $link = $this->__activateGenerateLink($nation_code, $pelanggan->id, '');

            $nama = $pelanggan->fnama;
            $replacer = array();
            $replacer['site_name'] = $this->app_name;
            $replacer['fnama'] = $nama;
            $replacer['activation_link'] = $link;
            $this->seme_email->flush();
            $this->seme_email->replyto($this->site_name, $this->site_replyto);
            $this->seme_email->from($this->site_email, $this->site_name);
            $this->seme_email->subject('Registration Successful');
            $this->seme_email->to($pelanggan->email, $nama);
            $this->seme_email->template('account_register');
            $this->seme_email->replacer($replacer);
            $this->seme_email->send();
        }
        //END by Donny Dennison - 10 december 2020 15:01

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    /**
     * User change display picture
     */
    public function edit_dp()
    {
        //error_reporting(E_ALL);
        //print_r($_FILES);
        //die();

        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['pelanggan'] = new stdClass();
        $data['can_input_referral'] = '0';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //START by Donny Dennison - 10 november 2022 14:34
        //new feature, join/input referral
        $limit = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E7");
        if (!isset($limit->remark)) {
          $limit = new stdClass();
          $limit->remark = 5;
        }

        if($pelanggan->b_user_id_recruiter == '0' && date("Y-m-d", strtotime($pelanggan->cdate." +".$limit->remark." days")) > date("Y-m-d")){
            $data['can_input_referral'] = '1';
        }
        //END by Donny Dennison - 10 november 2022 14:34
        //new feature, join/input referral

        //upload user image
        $image = $this->__uploadUserImage($pelanggan->id);
        $image = str_replace('//', '/', $image);
        if (!empty($image)) {
            $image_old = $pelanggan->image;
            if (file_exists(SENEROOT.DIRECTORY_SEPARATOR.$image_old) && $image_old != 'media/user/default-profile-picture.png') {
                unlink(SENEROOT.DIRECTORY_SEPARATOR.$image_old);
            }
            $du = array();
            $du['image'] = $image;
            $res = $this->bu->update($nation_code, $pelanggan->id, $du);
            if ($res) {
                $this->status = 200;
                $this->message = 'Success';
                $pelanggan = $this->bu->getById($nation_code, $pelanggan->id);
            } else {
                $this->status = 1731;
                $this->message = 'Failed changing display picture';
            }
        } else {
            $this->status = 1300;
            $this->message = 'Upload failed';
        }

        $data['pelanggan'] = $pelanggan;
        // by Muhammad Sofi - 26 October 2021 11:16
        // if user img & banner not exist or empty, change to default image
        // if (isset($data['pelanggan']->image)) {
        //     $data['pelanggan']->image = $this->cdn_url($data['pelanggan']->image);
        // }
        if (isset($data['pelanggan']->image)) {
            if(file_exists(SENEROOT.$data['pelanggan']->image) && $data['pelanggan']->image != 'media/user/default.png'){
                $data['pelanggan']->image = $this->cdn_url($data['pelanggan']->image);
            } else {
                $data['pelanggan']->image = $this->cdn_url('media/user/default-profile-picture.png');
            }
        }

        //by Donny Dennison - 08-09-2021 11:35
        //revamp-profile

        // by Muhammad Sofi - 26 October 2021 11:16
        // if user img & banner not exist or empty, change to default image
        // if (isset($data['pelanggan']->image_banner)) {
        //     $data['pelanggan']->image_banner = $this->cdn_url($data['pelanggan']->image_banner);
        // }
        if (isset($data['pelanggan']->image_banner)) {
            if(file_exists(SENEROOT.$data['pelanggan']->image_banner)){
                $data['pelanggan']->image_banner = $this->cdn_url($data['pelanggan']->image_banner);
            } else {
                $data['pelanggan']->image_banner = $this->cdn_url('media/user/default.png');
            }
        }

        unset($data['pelanggan']->password);
        unset($data['pelanggan']->api_web_token);
        unset($data['pelanggan']->api_reg_token);
        unset($data['pelanggan']->api_mobile_token);

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    //by Donny Dennison - 08-09-2021 11:35
    //revamp-profile
    public function edit_ib()
    {
        //error_reporting(E_ALL);
        //print_r($_FILES);
        //die();

        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['pelanggan'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //upload user image
        $image = $this->__uploadUserImage($pelanggan->id);
        $image = str_replace('//', '/', $image);
        if (!empty($image)) {
            // by Muhammad Sofi - 28 October 2021 11:00
            // if user img & banner not exist or empty, change to default image
            // $image_old = $pelanggan->image_banner;
            if(file_exists(SENEROOT.$pelanggan->image_banner)){
                $image_old = $pelanggan->image_banner;
            } else {
                $image_old = $this->cdn_url('media/user/default.png');
            }
            if (file_exists(SENEROOT.DIRECTORY_SEPARATOR.$image_old) && $image_old != 'media/user/default-profile-picture.png') {
                unlink(SENEROOT.DIRECTORY_SEPARATOR.$image_old);
            }
            $du = array();
            $du['image_banner'] = $image;
            $res = $this->bu->update($nation_code, $pelanggan->id, $du);
            if ($res) {
                $this->status = 200;
                $this->message = 'Success';
                $pelanggan = $this->bu->getById($nation_code, $pelanggan->id);
            } else {
                $this->status = 1731;
                $this->message = 'Failed changing display picture';
            }
        } else {
            $this->status = 1300;
            $this->message = 'Upload failed';
        }

        $data['pelanggan'] = $pelanggan;
        // START by Muhammad Sofi - 26 October 2021 11:16
        // if user img & banner not exist or empty, change to default image
        // if (isset($data['pelanggan']->image)) {
        //     $data['pelanggan']->image = $this->cdn_url($data['pelanggan']->image);
        // }
        if (isset($data['pelanggan']->image)) {
            if(file_exists(SENEROOT.$data['pelanggan']->image) && $data['pelanggan']->image != 'media/user/default.png'){
                $data['pelanggan']->image = $this->cdn_url($data['pelanggan']->image);
            } else {
                $data['pelanggan']->image = $this->cdn_url('media/user/default-profile-picture.png');
            }
        }
        
        // if (isset($data['pelanggan']->image_banner)) {
        //     $data['pelanggan']->image_banner = $this->cdn_url($data['pelanggan']->image_banner);
        // }

        if (isset($data['pelanggan']->image_banner)) {
            if(file_exists(SENEROOT.$data['pelanggan']->image_banner)){
                $data['pelanggan']->image_banner = $this->cdn_url($data['pelanggan']->image_banner);
            } else {
                $data['pelanggan']->image_banner = $this->cdn_url('media/user/default.png');
            }
        }

        // END by Muhammad Sofi - 26 October 2021 11:16

        unset($data['pelanggan']->password);
        unset($data['pelanggan']->api_web_token);
        unset($data['pelanggan']->api_reg_token);
        unset($data['pelanggan']->api_mobile_token);

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    /**
     * User change password
     * @return [type] [description]
     */
    public function edit_password()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['pelanggan'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        if ($pelanggan->register_from != "phone") {
            $this->status = 1746;
            $this->message = 'Cannot change password if register using phone';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //by Donny Dennison - 10 december 2020 15:01
        //new registration system for apple id
        //START by Donny Dennison - 10 december 2020 15:01
        $changeIsResetPasswordValue = 0;
        if (strpos($pelanggan->email, '@privaterelay.appleid.com') !== false) {
            $changeIsResetPasswordValue = 1;
        }else{
        //END by Donny Dennison - 10 december 2020 15:01

            $oldpassword = $this->__passClear($this->input->post('oldpassword'));
            if (strlen($oldpassword)<=5) {
                $this->status = 1740;
                $this->message = 'Missing or invalid old password';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }

            //check password
            $matched = 0;
            if (hash_equals($pelanggan->password, hash("sha256", $oldpassword))) {
                $matched++;
            }

            //$this->status = 3732;
            //$this->message = 'Matched: '.$matched.', Old Password: '.$oldpassword.', md5 oldpassword: '.md5($oldpassword).',hash oldpassword: '.password_hash($oldpassword,PASSWORD_BCRYPT).', in DB oldpassword: '.$pelanggan->password;
            //$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            //die();

            if (empty($matched)) {
                $this->status = 1741;
                $this->message = 'Your old password does not match, please try again';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }

        //by Donny Dennison - 10 december 2020 15:01
        //new registration system for apple id
        //START by Donny Dennison - 10 december 2020 15:01
        }
        //END by Donny Dennison - 10 december 2020 15:01

        //building array update
        $du = array();

        //collect input
        $du['password'] = $this->__passClear($this->input->post('newpassword'));
        if (strlen($du['password'])<=5) {
            $this->status = 1742;
            $this->message = 'Missing or invalid new password';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        $repassword = $this->__passClear($this->input->post('confirm_newpassword'));
        if (strlen($repassword)<=5) {
            $this->status = 1743;
            $this->message = 'Missing or invalid password confirmation';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        if ($du['password'] != $repassword) {
            $this->status = 1744;
            $this->message = 'Your new password with new password confirmation does not match';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        $du['password'] = $this->__passGen($du['password']);

        //by Donny Dennison - 10 december 2020 15:01
        //new registration system for apple id
        //START by Donny Dennison - 10 december 2020 15:01

        if ($changeIsResetPasswordValue == 1) {
            $du['is_reset_password'] = 1;
        }
        //END by Donny Dennison - 10 december 2020 15:01

        $res = $this->bu->update($nation_code, $pelanggan->id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
            $pelanggan = $this->bu->getById($nation_code, $pelanggan->id);
        } else {
            $this->status = 1745;
            $this->message = 'Change password failed';
        }

        $data['pelanggan'] = $pelanggan;
        // by Muhammad Sofi - 26 October 2021 11:16
        // if user img & banner not exist or empty, change to default image
        // if (isset($data['pelanggan']->image)) {
        //     $data['pelanggan']->image = $this->cdn_url($data['pelanggan']->image);
        // }
        if (isset($data['pelanggan']->image)) {
            if(file_exists(SENEROOT.$data['pelanggan']->image) && $data['pelanggan']->image != 'media/user/default.png'){
                $data['pelanggan']->image = $this->cdn_url($data['pelanggan']->image);
            } else {
                $data['pelanggan']->image = $this->cdn_url('media/user/default-profile-picture.png');
            }
        }

        //by Donny Dennison - 08-09-2021 11:35
        //revamp-profile

        // by Muhammad Sofi - 26 October 2021 11:16
        // if user img & banner not exist or empty, change to default image
        // if (isset($data['pelanggan']->image_banner)) {
        //     $data['pelanggan']->image_banner = $this->cdn_url($data['pelanggan']->image_banner);
        // }
        if (isset($data['pelanggan']->image_banner)) {
            if(file_exists(SENEROOT.$data['pelanggan']->image_banner)){
                $data['pelanggan']->image_banner = $this->cdn_url($data['pelanggan']->image_banner);
            } else {
                $data['pelanggan']->image_banner = $this->cdn_url('media/user/default.png');
            }    
        }
       
        unset($data['pelanggan']->password);
        unset($data['pelanggan']->api_web_token);
        unset($data['pelanggan']->api_reg_token);
        unset($data['pelanggan']->api_mobile_token);
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }



    public function email_check()
    {
        $s = $this->__init();
        $this->status = 400;
        $this->message = 'Missing or invalid API key';
        $data = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        
        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        
        $this->status = 200;
        $email = strtolower(trim($this->input->request("email")));
        if (strlen($email)<=4) {
            $this->status = 1732;
            $this->message = 'Please input the correct email';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        $user = $this->bu->getByEmail($nation_code, $email);
        if (isset($user->id)) {
            if (strlen($user->social_id)<=4) {
                // $this->message = "Email already registered";
                $this->message = "Success";
            } else {
                // $this->message = "Email already registered from 3rd party";
                $this->message = "Success";
            }
        } else {
            // $this->message = "Email has not been registered";
            $this->message = "Success";
        }
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    /**
     * Resend registration email
     */
    public function resend_email_register()
    {
        $data = new stdClass();
        $nation_code = $this->input->request("nation_code");
        if (strlen($nation_code)<=1) {
            $nation_code = 62;
        }

        $email = strtolower(trim($this->input->request("email")));
        if (strlen($email)<=4) {
            $email = 'daeng@somein.co.id';
        }
        $user = $this->bu->getByEmail($nation_code, $email);
        if (!isset($user->id)) {
            $this->status = 409;
            $this->message = "unregistered email";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        $nama = $user->fnama;

        //generate activation link
        $link = $this->__activateGenerateLink($nation_code, $user->id, $user->api_reg_token);

        //load email libary
        $replacer = array();
        $replacer['site_name'] = $this->app_name;
        $replacer['fnama'] = $nama;
        $replacer['activation_link'] = $link;

        //building email properties
        $this->seme_email->flush();
        $this->seme_email->replyto($this->site_name, $this->site_replyto);
        $this->seme_email->from($this->site_email, $this->site_name);
        $this->seme_email->subject('Please confirm your '.$this->site_name.' registration');
        $this->seme_email->to($email, $nama);
        $this->seme_email->template('account_register');
        $this->seme_email->replacer($replacer);
        $this->seme_email->send();

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }


    public function test_email_register()
    {
        $nation_code = $this->input->request("nation_code");
        if (strlen($nation_code)<=1) {
            $nation_code = 62;
        }

        $email = strtolower(trim($this->input->request("email")));
        if (strlen($email)<=4) {
            $email = 'daeng@cenah.co.id';
        }
        $user = $this->bu->getByEmail($nation_code, $email);
        //if(!isset($user->id)){
        //die("unregistered email");
        //}
        $nama = 'Daeng Cenah';
        if (isset($user->fnama)) {
            $nama = $user->fnama;
        }

        //generate activation link
        //$link = $this->__activateGenerateLink($nation_code,$user->id,$user->api_reg_token);
        $link = 'http://example.com/';

        //load email libary
        $replacer = array();
        $replacer['site_name'] = $this->app_name;
        $replacer['fnama'] = $nama;
        $replacer['activation_link'] = $link;

        //building email properties
        $this->seme_email->flush();
        $this->seme_email->replyto($this->site_name, $this->site_replyto);
        $this->seme_email->from($this->site_email, $this->site_name);
        $this->seme_email->subject('Please confirm your '.$this->site_name.' registration');
        $this->seme_email->to($email, $nama);
        $this->seme_email->template('account_register');
        $this->seme_email->replacer($replacer);
        $this->seme_email->send();
        $this->debug($this->seme_email->getLog());
    }

    /**
     * Get User Addresses
     */
    public function alamat()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['alamat'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        $general_location = $this->input->get('general_location');
        if(!$general_location){
            $general_location = 0;
        }

        //default response
        $this->status = 200;
        $this->message = 'Success';
        $data['alamat'] = $this->bua->getByUserId($nation_code, $pelanggan->id);
        foreach($data['alamat'] AS &$alt){
            if($pelanggan->language_id == 2){//indonesia
                $alt->judul = str_replace("Your Place", "Alamat", $alt->judul);
            // }else if($pelanggan->language_id == 3){//korea
            //     $data['alamat']->judul = str_replace("Your Place", "", $data['alamat']->judul);
            // }else if($pelanggan->language_id == 4){//thailand
            //     $data['alamat']->judul = str_replace("Your Place", "", $data['alamat']->judul);
            }else{//english
                // $data['alamat']->judul = str_replace("Your Place", "", $data['alamat']->judul);
            }
        }

        if($general_location != 0){
            $tempAddress = array();

            foreach($data['alamat'] AS $alamat){
                $tempAlamat2 = $alamat->alamat2;

                $alamat->alamat2 = strtolower(trim($alamat->alamat2));

                if (substr($alamat->alamat2, 0, strlen("jalan raya")) == "jalan raya") {
                    $alamat->alamat2 = trim(substr($alamat->alamat2, strlen("jalan raya")));
                }

                if (substr($alamat->alamat2, 0, strlen("jalan")) == "jalan") {
                    $alamat->alamat2 = trim(substr($alamat->alamat2, strlen("jalan")));
                }

                if (substr($alamat->alamat2, 0, strlen("jln.")) == "jln.") {
                    $alamat->alamat2 = trim(substr($alamat->alamat2, strlen("jln.")));
                }

                if (substr($alamat->alamat2, 0, strlen("jln")) == "jln") {
                    $alamat->alamat2 = trim(substr($alamat->alamat2, strlen("jln")));
                }

                if (substr($alamat->alamat2, 0, strlen("jl.")) == "jl.") {
                    $alamat->alamat2 = trim(substr($alamat->alamat2, strlen("jl.")));
                }
                
                if (substr($alamat->alamat2, 0, strlen("jl")) == "jl") {
                    $alamat->alamat2 = trim(substr($alamat->alamat2, strlen("jl")));
                }

                if (substr($alamat->alamat2, 0, strlen("gang")) == "gang") {
                    $alamat->alamat2 = trim(substr($alamat->alamat2, strlen("gang")));
                }

                if (substr($alamat->alamat2, 0, strlen("gg.")) == "gg.") {
                    $alamat->alamat2 = trim(substr($alamat->alamat2, strlen("gg.")));
                }

                if (substr($alamat->alamat2, 0, strlen("gg")) == "gg") {
                    $alamat->alamat2 = trim(substr($alamat->alamat2, strlen("gg")));
                }
                
                if (strpos($alamat->alamat2, ' ') !== false) {
                    $totalSpace = strpos($alamat->alamat2," ");
                    $tempAlamat2 = trim(substr($alamat->alamat2, $totalSpace));
                    if (strpos($tempAlamat2, ' ') !== false) {
                        $totalSpace += strpos($tempAlamat2, ' ') + 1;
                        $alamat->alamat2 = trim(substr($alamat->alamat2, 0, $totalSpace));
                    }
                    unset($totalSpace, $tempAlamat2);
                }

                //credit : https://stackoverflow.com/a/53736941/7578520
                if (array_search($alamat->alamat2, array_column($tempAddress, 'alamat2')) !== FALSE) {
                    // echo 'FOUND!';
                } else {
                    // echo 'NOT FOUND!';
                    array_push($tempAddress, $alamat);
                }
            }
            $data['alamat'] = $tempAddress;
            unset($tempAddress, $alamat);
        }

        //render
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    //by Donny Dennison - 14 july 2021 14:14
    //add-general-location-in-address
    public function customer_location_list()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['general_location'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //default response
        $this->status = 200;
        $this->message = 'Success';
        $data['general_location'] = $this->bua->getGeneralLocationCustomer($nation_code, $pelanggan->id);

        //render
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    /**
     * Get user address types
     */
    public function alamat_jenis()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['alamat_jenis'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //default response
        $this->status = 200;
        $this->message = 'Success';
        $data['alamat_jenis'] = $this->bua->getAddressType($nation_code);

        //render
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    /**
     * Create new user address
     */
    public function alamat_baru()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['alamat'] = array();
        $data['can_input_referral'] = '0';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        //get current country configuration
        $negara = $this->__getNegara($nation_code);

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //START by Donny Dennison - 10 november 2022 14:34
        //new feature, join/input referral
        $limit = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E7");
        if (!isset($limit->remark)) {
          $limit = new stdClass();
          $limit->remark = 5;
        }

        if($pelanggan->b_user_id_recruiter == '0' && date("Y-m-d", strtotime($pelanggan->cdate." +".$limit->remark." days")) > date("Y-m-d")){
            $data['can_input_referral'] = '1';
        }
        //END by Donny Dennison - 10 november 2022 14:34
        //new feature, join/input referral

        // if ($this->is_log) {
        //     $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan::alamat_baru --begin");
        // }

        //by Donny Dennison - 13 july 2021 15:49
        //set-address-type-to-default
        //check address code
        // $address_status = $this->input->post("address_status");
        
        // $check = $this->ccm->check($nation_code, "address", $address_status);
        // if (empty($check)) {
        //     $this->status = 1732;
        //     $this->message = 'Invalid address_status, please refer to pelanggan/alamat_jenis API.';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //     die();
        // }

        //by Donny Dennison - 22 september 2021
        //auto-generate-address-title
        // //check $judul
        // $judul = $this->input->post("judul");
        // if (empty($judul)) {
        //     $judul = '';
        // }
        // if (strlen($judul)<=0) {
        //     $this->status = 1761;
        //     $this->message = 'Title cannot be empty.';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //     die();
        // }

        //check $penerima_nama
        $penerima_nama = trim($this->input->post("penerima_nama"));
        if ($this->__mbLen($penerima_nama)<=0) {
            $this->status = 1737;
            $this->message = 'Name cannot be empty';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        if ($this->__mbLen($penerima_nama)>=64) {
            $this->status = 1736;
            $this->message = 'Name too long';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //sanitize null
        $penerima_nama = trim(mb_ereg_replace('null', '', $penerima_nama));

        //check $penerima_nama
        $penerima_telp = trim($this->input->post("penerima_telp"));
        if (empty($penerima_telp)) {
            $penerima_telp = '';
        }

        //by Donny Dennison - 08 june 2022 - 14:56
        //phone number not mandatory
        // if (strlen($penerima_telp)<=0) {
        //     $this->status = 1763;
        //     $this->message = 'Phone number cannot be empty';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //     die();
        // }
        if (strlen($penerima_telp)>=32) {
            $this->status = 1723;
            $this->message = 'Phone number too long';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check $catatan
        $catatan = $this->input->post("catatan");
        if (empty($catatan)) {
            $catatan = '';
        }
        // if ($this->__mbLen($catatan)>=128) {
        //     $this->status = 1724;
        //     $this->message = 'Address notes too long';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //     die();
        // }
        // by Muhammad Sofi - 3 November 2021 10:00
        // remark code

        $alamat2 = trim($this->input->post("alamat2"));
        if (empty($alamat2)) {
            $alamat2 = '';
        }
        // if ($this->__mbLen($alamat2)>=128) {
        //     $this->status = 1765;
        //     $this->message = 'Secondary address too long';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //     die();
        // }

        $latitude = $this->input->post("latitude");
        $longitude = $this->input->post("longitude");
        if (strlen($latitude)<=3 || strlen($longitude)<=3) {
            //by Donny Dennison - 24 juli 2020 18:23
            //change default latitude and longitude
            // $latitude = $negara->latitude;
            // $longitude = $negara->longitude;
            $latitude = 0;
            $longitude = 0;
        }

        //populating input for location
        $provinsi = $this->input->post("provinsi");
        $kabkota = $this->input->post("kabkota");
        $kecamatan = $this->input->post("kecamatan");
        $kelurahan = $this->input->post("kelurahan");
        $kodepos = $this->input->post("kodepos");

        //validating
        if (empty($provinsi)) {
            $provinsi = '';
        }
        if (empty($kabkota)) {
            $kabkota = '';
        }
        if (empty($kecamatan)) {
            $kecamatan = '';
        }
        if (empty($kelurahan)) {
            $kelurahan = '';
        }
        if (empty($kodepos)) {
            $kodepos = '99999';
        }

        //check location properties provinsi
        if (!empty($negara->is_provinsi)) {
            if (strlen($provinsi)<=0) {
                $this->status = 1766;
                $this->message = 'Province / State are required for this country';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }
        }

        //check location properties kabkota
        if (!empty($negara->is_kabkota)) {
            if (strlen($kabkota)<=0) {
                $this->status = 1767;
                $this->message = 'City are required for this country';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }
        }

        //check location properties kecamatan
        if (!empty($negara->is_kecamatan)) {
            if (strlen($kecamatan)<=0) {
                $this->status = 1724;
                $this->message = 'District are required for this country';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }
        }

        //check location properties kelurahan
        if (!empty($negara->is_kelurahan)) {
            if (strlen($kelurahan)<=0) {
                $this->status = 1769;
                $this->message = 'Sub District are required for this country';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }
        }

        //check location properties kelurahan
        if (!empty($negara->is_kodepos)) {
            if (strlen($kodepos)<=0) {
                $this->status = 1770;
                $this->message = 'Zipcode / Postal Code are required for this country';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }
        }

        // //check kelurahan, kecamatan, kabkota, provinsi, and kodepos in db or not
        // $checkInDBOrNot = $this->bual->checkInDBOrNot($nation_code, $kelurahan, $kecamatan, $kabkota, $provinsi, $kodepos);
        // if (!isset($checkInDBOrNot->id)){
        //     $this->status = 1774;
        //     $this->message = 'This address is invalid, please find other address';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //     die();
        // }

        //start transaction
        $this->bua->trans_start();

        //check is_default
        $is_default = (int) $this->input->post("is_default");

        //get last id
        $last_id = $this->bua->getLastId($nation_code, $pelanggan->id);

        //collect input
        $di = array();
        $di['nation_code'] = $nation_code;
        $di['id'] = $last_id;
        $di['b_user_id'] = $pelanggan->id;

        //by Donny Dennison - 22 september 2021
        //auto-generate-address-title
        // $di['judul'] = $judul;
        $di['judul'] = 'Your Place '.$last_id;

        $di['penerima_nama'] = $penerima_nama;
        $di['penerima_telp'] = $penerima_telp;
        $di['alamat2'] = $alamat2;
        $di['kelurahan'] = $kelurahan;
        $di['kecamatan'] = $kecamatan;
        $di['kabkota'] = $kabkota;
        $di['provinsi'] = $provinsi;
        $di['negara'] = $negara->iso2;
        $di['kodepos'] = $kodepos;
        $di['longitude'] = $longitude;
        $di['latitude'] = $latitude;

        //by Donny Dennison - 13 july 2021 15:49
        //set-address-type-to-default
        // $di['address_status'] = $address_status;
        $di['address_status'] = 'A2';
        
        $di['catatan'] = $catatan;
        $di['is_active'] = 1;

        //insert into database
        $res = $this->bua->set($di);
        if ($res) {
            $this->bua->trans_commit();
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->bua->trans_rollback();
            $this->status = 1771;
            $this->message = 'Failed insert user address';
        }

        //START by Donny Dennison - 5 january 2021 - 11:49
        //change address default

        // //release default
        // $du = array("is_default"=>0);
        // $this->bua->updateByUserId($nation_code, $pelanggan->id, $du);
        // $this->bua->trans_commit();
        // //update default
        // $du = array("is_default"=>1);
        // $this->bua->update($nation_code, $pelanggan->id, $last_id, $du);
        // $this->bua->trans_commit();
        // $this->bua->trans_end();

        $user_alamat_default = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
        if($last_id == 1 || !isset($user_alamat_default->alamat2)){
            $du = array("is_default"=>1);
            $this->bua->update($nation_code, $pelanggan->id, $last_id, $du);
            $this->bua->trans_commit();
        }
        //END by Donny Dennison - 5 january 2021 - 11:49

        if($this->status == 200){
            //check kelurahan, kecamatan, kabkota, provinsi, and kodepos in db or not
            $checkInDBOrNot = $this->bual->checkInDBOrNot($nation_code, $kelurahan, $kecamatan, $kabkota, $provinsi, $kodepos);
            if (!isset($checkInDBOrNot->id)){     
                //get last id
                $last_id = $this->bual->getLastId($nation_code);

                //collect input
                $di = array();
                $di['nation_code'] = $nation_code;
                $di['id'] = $last_id;
                $di['kelurahan'] = $kelurahan;
                $di['kecamatan'] = $kecamatan;
                $di['kabkota'] = $kabkota;
                $di['provinsi'] = $provinsi;
                $di['kodepos'] = $kodepos;

                //insert into database
                $this->bual->set($di);
                $this->bua->trans_commit();

            }

            // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", "All", "All", $provinsi);
            // if(!isset($getStatusHighlight->status)){
            //     //get last id
            //     $highlight_status_id = $this->gglhsm->getLastId($nation_code);
            //     $di = array();
            //     $di['nation_code'] = $nation_code;
            //     $di['id'] = $highlight_status_id;
            //     $di['b_user_alamat_location_kelurahan'] = 'All';
            //     $di['b_user_alamat_location_kecamatan'] = 'All';
            //     $di['b_user_alamat_location_kabkota'] = 'All';
            //     $di['b_user_alamat_location_provinsi'] = $provinsi;
            //     $this->gglhsm->set($di);
            //     $this->bua->trans_commit();
            // }

            // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", "All", $kabkota, $provinsi);
            // if(!isset($getStatusHighlight->status)){
            //     //get last id
            //     $highlight_status_id = $this->gglhsm->getLastId($nation_code);
            //     $di = array();
            //     $di['nation_code'] = $nation_code;
            //     $di['id'] = $highlight_status_id;
            //     $di['b_user_alamat_location_kelurahan'] = 'All';
            //     $di['b_user_alamat_location_kecamatan'] = 'All';
            //     $di['b_user_alamat_location_kabkota'] = $kabkota;
            //     $di['b_user_alamat_location_provinsi'] = $provinsi;
            //     $this->gglhsm->set($di);
            //     $this->bua->trans_commit();
            // }

            // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", $kecamatan, $kabkota, $provinsi);
            // if(!isset($getStatusHighlight->status)){
            //     //get last id
            //     $highlight_status_id = $this->gglhsm->getLastId($nation_code);
            //     $di = array();
            //     $di['nation_code'] = $nation_code;
            //     $di['id'] = $highlight_status_id;
            //     $di['b_user_alamat_location_kelurahan'] = 'All';
            //     $di['b_user_alamat_location_kecamatan'] = $kecamatan;
            //     $di['b_user_alamat_location_kabkota'] = $kabkota;
            //     $di['b_user_alamat_location_provinsi'] = $provinsi;
            //     $this->gglhsm->set($di);
            //     $this->bua->trans_commit();
            // }

            // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, $kelurahan, $kecamatan, $kabkota, $provinsi);
            // if(!isset($getStatusHighlight->status)){
            //     //get last id
            //     $highlight_status_id = $this->gglhsm->getLastId($nation_code);
            //     $di = array();
            //     $di['nation_code'] = $nation_code;
            //     $di['id'] = $highlight_status_id;
            //     $di['b_user_alamat_location_kelurahan'] = $kelurahan;
            //     $di['b_user_alamat_location_kecamatan'] = $kecamatan;
            //     $di['b_user_alamat_location_kabkota'] = $kabkota;
            //     $di['b_user_alamat_location_provinsi'] = $provinsi;
            //     $this->gglhsm->set($di);
            //     $this->bua->trans_commit();
            // }
        }

        $this->bua->trans_end();

        //logged
        // if ($this->is_log) {
        //     $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan::alamat_baru -> ".$this->message);
        // }
        // if ($this->is_log) {
        //     $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan::alamat_baru --end");
        // }

        //get latest data
        $data['alamat'] = $this->bua->getByUserId($nation_code, $pelanggan->id);

        foreach($data['alamat'] AS &$alamat){
            if($pelanggan->language_id == 2){//indonesia
                $alamat->judul = str_replace("Your Place", "Alamat", $alamat->judul);
            // }else if($pelanggan->language_id == 3){//korea
            //     $data['alamat']->judul = str_replace("Your Place", "", $data['alamat']->judul);
            // }else if($pelanggan->language_id == 4){//thailand
            //     $data['alamat']->judul = str_replace("Your Place", "", $data['alamat']->judul);
            }else{//english
                // $data['alamat']->judul = str_replace("Your Place", "", $data['alamat']->judul);
            }
        }

        //render
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    /**
     * edit user address
     * @param  [type] $b_user_alamat_id [description]
     * @return [type]                   [description]
     */
    public function alamat_edit($b_user_alamat_id)
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['alamat'] = array();
        $data['can_input_referral'] = '0';

        //check passing param
        $b_user_alamat_id = (int) $b_user_alamat_id;
        if ($b_user_alamat_id<=0) {
            $this->status = 1772;
            $this->message = 'Invalid User Alamat ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        //get current country configuration
        $negara = $this->__getNegara($nation_code);

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //START by Donny Dennison - 10 november 2022 14:34
        //new feature, join/input referral
        $limit = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E7");
        if (!isset($limit->remark)) {
          $limit = new stdClass();
          $limit->remark = 5;
        }

        if($pelanggan->b_user_id_recruiter == '0' && date("Y-m-d", strtotime($pelanggan->cdate." +".$limit->remark." days")) > date("Y-m-d")){
            $data['can_input_referral'] = '1';
        }
        //END by Donny Dennison - 10 november 2022 14:34
        //new feature, join/input referral

        $user_alamat = $this->bua->getByIdUserId($nation_code, $pelanggan->id, $b_user_alamat_id);
        if (!isset($user_alamat->id)) {
            $this->status = 1773;
            $this->message = 'User address with supplied ID not found or invalid or already deleted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //by Donny Dennison - 13 july 2021 15:49
        //set-address-type-to-default
        //check address code
        // $address_status = $this->input->post("address_status");

        // $check = $this->ccm->check($nation_code, "address", $address_status);
        // if (empty($check)) {
        //     $this->status = 1774;
        //     $this->message = 'Invalid address_status, please refer to settings/address/Address Type API.';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //     die();
        // }


        //by Donny Dennison - 22 september 2021
        //auto-generate-address-title
        // //check $judul
        // $judul = $this->input->post("judul");
        // if (empty($judul)) {
        //     $judul = '';
        // }
        // if (strlen($judul)<=0) {
        //     $this->status = 1775;
        //     $this->message = 'Title cannot empty.';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //     die();
        // }

        //check $penerima_nama
        $penerima_nama = $this->input->post("penerima_nama");
        if (empty($penerima_nama)) {
            $penerima_nama = '';
        }
        if ($this->__mbLen($penerima_nama)<=0) {
            $this->status = 1737;
            $this->message = 'Name cannot be empty';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        if ($this->__mbLen($penerima_nama)>=64) {
            $this->status = 1736;
            $this->message = 'Name too long';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //sanitize null
        $penerima_nama = trim(mb_ereg_replace('null', '', $penerima_nama));

        //check $penerima_nama
        $penerima_telp = $this->input->post("penerima_telp");
        if (empty($penerima_telp)) {
            $penerima_telp = '';
        }

        //by Donny Dennison - 08 june 2022 - 14:56
        //phone number not mandatory
        // if (strlen($penerima_telp)<=0) {
        //     $this->status = 1763;
        //     $this->message = 'Phone number cannot be empty.';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //     die();
        // }
        if (strlen($penerima_telp)>=32) {
            $this->status = 1739;
            $this->message = 'Phone number too long';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check $catatan
        $catatan = $this->input->post("catatan");
        if (empty($catatan)) {
            $catatan = '';
        }

        $alamat2 = $this->input->post("alamat2");
        if (empty($alamat2)) {
            $alamat2 = '';
        }
        // if ($this->__mbLen($alamat2)>=128) {
        //     $this->status = 1779;
        //     $this->message = 'Secondary address too long';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //     die();
        // }

        $latitude = $this->input->post("latitude");
        $longitude = $this->input->post("longitude");
        if (strlen($latitude)<=3 || strlen($longitude)<=3) {
            //by Donny Dennison - 24 juli 2020 18:23
            //change default latitude and longitude
            // $latitude = $negara->latitude;
            // $longitude = $negara->longitude;
            $latitude = 0;
            $longitude = 0;

        }
        $is_default = (int) $this->input->post("is_default");

        //populating input for location
        $provinsi = $this->input->post("provinsi");
        $kabkota = $this->input->post("kabkota");
        $kecamatan = $this->input->post("kecamatan");
        $kelurahan = $this->input->post("kelurahan");
        $kodepos = $this->input->post("kodepos");

        //validating
        if (empty($provinsi)) {
            $provinsi = '';
        }
        if (empty($kabkota)) {
            $kabkota = '';
        }
        if (empty($kecamatan)) {
            $kecamatan = '';
        }
        if (empty($kelurahan)) {
            $kelurahan = '';
        }
        if (empty($kodepos)) {
            $kodepos = '99999';
        }

        //check location properties provinsi
        if (!empty($negara->is_provinsi)) {
            if (strlen($provinsi)<=0) {
                $this->status = 1766;
                $this->message = 'Province / State are required for this country';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }
        }

        //check location properties kabkota
        if (!empty($negara->is_kabkota)) {
            if (strlen($kabkota)<=0) {
                $this->status = 1767;
                $this->message = 'City are required for this country';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }
        }

        //check location properties kecamatan
        if (!empty($negara->is_kecamatan)) {
            if (strlen($kecamatan)<=0) {
                $this->status = 1724;
                $this->message = 'District are required for this country';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }
        }

        //check location properties kelurahan
        if (!empty($negara->is_kelurahan)) {
            if (strlen($kelurahan)<=0) {
                $this->status = 1769;
                $this->message = 'Sub District are required for this country';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }
        }

        //check location properties kelurahan
        if (!empty($negara->is_kodepos)) {
            if (strlen($kodepos)<=0) {
                $this->status = 1770;
                $this->message = 'Zipcode / Postal Code are required for this country';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }
        }

        // //check kelurahan, kecamatan, kabkota, provinsi, and kodepos in db or not
        // $checkInDBOrNot = $this->bual->checkInDBOrNot($nation_code, $kelurahan, $kecamatan, $kabkota, $provinsi, $kodepos);
        // if (!isset($checkInDBOrNot->id)){
        //     $this->status = 1774;
        //     $this->message = 'This address is invalid, please find other address';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //     die();
        // }

        //update default
        if (!empty($is_default)) {
            $this->bua->updateByUserId($nation_code, $pelanggan->id, array("is_default"=>0));
        }

        $du = array();

        //by Donny Dennison - 22 september 2021
        //auto-generate-address-title
        // $du['judul'] = $judul;

        $du['penerima_nama'] = $penerima_nama;
        $du['penerima_telp'] = $penerima_telp;
        // $du['alamat'] = $alamat;
        $du['alamat2'] = $alamat2;
        $du['kelurahan'] = $kelurahan;
        $du['kecamatan'] = $kecamatan;
        $du['kabkota'] = $kabkota;
        $du['provinsi'] = $provinsi;
        $du['negara'] = $negara->iso2;
        $du['kodepos'] = $kodepos;
        $du['longitude'] = $longitude;
        $du['latitude'] = $latitude;
        $du['catatan'] = $catatan;

        //by Donny Dennison - 13 july 2021 15:49
        //set-address-type-to-default
        // $du['address_status'] = $address_status;
        $du['address_status'] = 'A2';

        $du['is_default'] = $is_default;
        $res = $this->bua->update($nation_code, $pelanggan->id, $b_user_alamat_id, $du);
        if ($res) {
            //doing update
            $this->status = 200;
            $this->message = 'Success';

            $producthaveEditedAddress = $this->cpm->getByUserIdAlamatId($nation_code, $pelanggan->id, $b_user_alamat_id);
            foreach($producthaveEditedAddress as $product){
                $du = array();
                $du['alamat2'] = $alamat2;
                $du['kelurahan'] = $kelurahan;
                $du['kecamatan'] = $kecamatan;
                $du['kabkota'] = $kabkota;
                $du['provinsi'] = $provinsi;
                $du['kodepos'] = $kodepos;
                $du['latitude'] = $latitude;
                $du['longitude'] = $longitude;
                $this->cpm->updateByUserIdAlamatId($nation_code, $pelanggan->id, $b_user_alamat_id, $du);
            }

            //check kelurahan, kecamatan, kabkota, provinsi, and kodepos in db or not
            $checkInDBOrNot = $this->bual->checkInDBOrNot($nation_code, $kelurahan, $kecamatan, $kabkota, $provinsi, $kodepos);
            if (!isset($checkInDBOrNot->id)){   
                //get last id
                $last_id = $this->bual->getLastId($nation_code);

                //collect input
                $di = array();
                $di['nation_code'] = $nation_code;
                $di['id'] = $last_id;
                $di['kelurahan'] = $kelurahan;
                $di['kecamatan'] = $kecamatan;
                $di['kabkota'] = $kabkota;
                $di['provinsi'] = $provinsi;
                $di['kodepos'] = $kodepos;

                //insert into database
                $this->bual->set($di);
            }

            // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", "All", "All", $provinsi);
            // if(!isset($getStatusHighlight->status)){
            //     //get last id
            //     $highlight_status_id = $this->gglhsm->getLastId($nation_code);
            //     $di = array();
            //     $di['nation_code'] = $nation_code;
            //     $di['id'] = $highlight_status_id;
            //     $di['b_user_alamat_location_kelurahan'] = 'All';
            //     $di['b_user_alamat_location_kecamatan'] = 'All';
            //     $di['b_user_alamat_location_kabkota'] = 'All';
            //     $di['b_user_alamat_location_provinsi'] = $provinsi;
            //     $this->gglhsm->set($di);
            // }

            // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", "All", $kabkota, $provinsi);
            // if(!isset($getStatusHighlight->status)){
            //     //get last id
            //     $highlight_status_id = $this->gglhsm->getLastId($nation_code);
            //     $di = array();
            //     $di['nation_code'] = $nation_code;
            //     $di['id'] = $highlight_status_id;
            //     $di['b_user_alamat_location_kelurahan'] = 'All';
            //     $di['b_user_alamat_location_kecamatan'] = 'All';
            //     $di['b_user_alamat_location_kabkota'] = $kabkota;
            //     $di['b_user_alamat_location_provinsi'] = $provinsi;
            //     $this->gglhsm->set($di);
            // }

            // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", $kecamatan, $kabkota, $provinsi);
            // if(!isset($getStatusHighlight->status)){
            //     //get last id
            //     $highlight_status_id = $this->gglhsm->getLastId($nation_code);
            //     $di = array();
            //     $di['nation_code'] = $nation_code;
            //     $di['id'] = $highlight_status_id;
            //     $di['b_user_alamat_location_kelurahan'] = 'All';
            //     $di['b_user_alamat_location_kecamatan'] = $kecamatan;
            //     $di['b_user_alamat_location_kabkota'] = $kabkota;
            //     $di['b_user_alamat_location_provinsi'] = $provinsi;
            //     $this->gglhsm->set($di);
            // }

            // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, $kelurahan, $kecamatan, $kabkota, $provinsi);
            // if(!isset($getStatusHighlight->status)){
            //     //get last id
            //     $highlight_status_id = $this->gglhsm->getLastId($nation_code);
            //     $di = array();
            //     $di['nation_code'] = $nation_code;
            //     $di['id'] = $highlight_status_id;
            //     $di['b_user_alamat_location_kelurahan'] = $kelurahan;
            //     $di['b_user_alamat_location_kecamatan'] = $kecamatan;
            //     $di['b_user_alamat_location_kabkota'] = $kabkota;
            //     $di['b_user_alamat_location_provinsi'] = $provinsi;
            //     $this->gglhsm->set($di);
            // }

        } else {
            $this->status = 1785;
            $this->message = 'Failed updating user address';
        }
        //get latest data
        // $alamat_list = $this->bua->getByUserId($nation_code, $pelanggan->id);
        // $data['alamat'] = $alamat_list;
        $data['alamat'] = $this->bua->getByIdUserId($nation_code, $pelanggan->id, $b_user_alamat_id);

        if($pelanggan->language_id == 2){//indonesia
            $data['alamat']->judul = str_replace("Your Place", "Alamat", $data['alamat']->judul);
        // }else if($pelanggan->language_id == 3){//korea
        //     $data['alamat']->judul = str_replace("Your Place", "", $data['alamat']->judul);
        // }else if($pelanggan->language_id == 4){//thailand
        //     $data['alamat']->judul = str_replace("Your Place", "", $data['alamat']->judul);
        }else{//english
            // $data['alamat']->judul = str_replace("Your Place", "", $data['alamat']->judul);
        }

        //render
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    /**
     * Delete user address
     * @param  int $b_user_alamat_id [description]
     */
    public function alamat_hapus($b_user_alamat_id)
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['alamat'] = array();
        $data['can_input_referral'] = '0';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        $default_deleted = 0;

        //START by Donny Dennison - 10 november 2022 14:34
        //new feature, join/input referral
        $limit = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E7");
        if (!isset($limit->remark)) {
          $limit = new stdClass();
          $limit->remark = 5;
        }

        if($pelanggan->b_user_id_recruiter == '0' && date("Y-m-d", strtotime($pelanggan->cdate." +".$limit->remark." days")) > date("Y-m-d")){
            $data['can_input_referral'] = '1';
        }
        //END by Donny Dennison - 10 november 2022 14:34
        //new feature, join/input referral

        //check passing param
        $b_user_alamat_id = (int) $b_user_alamat_id;
        if ($b_user_alamat_id<=0) {
            $this->status = 1772;
            $this->message = 'Invalid User Alamat ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        $user_alamat = $this->bua->getByIdUserId($nation_code, $pelanggan->id, $b_user_alamat_id);
        if (!isset($user_alamat->id)) {
            $this->status = 1791;
            $this->message = 'User address with supplied ID not found or invalid or already deleted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        if (empty($user_alamat->is_active)) {
            $this->status = 1792;
            $this->message = 'User address already deleted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        $totalAlamat = $this->bua->countByUserId($nation_code, $pelanggan->id);
        if ($totalAlamat <= 1) {
            $this->status = 1794;
            $this->message = 'Cannot delete last address';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //by Donny Dennison - 28 july 2020 11:39
        // check the address if there is product using this address then cannot delete
        $checkProductUsingThisAddress = $this->cpm->getActiveByUserIdAlamatId($nation_code, $pelanggan->id, $b_user_alamat_id);
        if (!empty($checkProductUsingThisAddress)) {
            $this->status = 1795;
            $this->message = "Not allowed to delete this address ! Because it's now being used for your product";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check if default one deleted
        if (!empty($user_alamat->is_default)) {
            $default_deleted = 1;
        }

        $du = array();
        $du['is_active'] = 0;
        $du['is_default'] = 0;
        $res = $this->bua->update($nation_code, $pelanggan->id, $b_user_alamat_id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->status = 1793;
            $this->message = 'Failed deleting user address';
        }

        //get latest data
        $alamat_list = $this->bua->getByUserId($nation_code, $pelanggan->id);
        if ($default_deleted) {
            // if ($this->is_log) {
            //     $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan::alamat_hapus -> Current Default Address has been deleted");
            // }
            foreach ($alamat_list as &$da) {
                $alamat_default = $da;
                $this->bua->update($nation_code, $pelanggan->id, $da->id, array("is_default"=>1));
                $da->is_default = 1;
                break;
            }

            //by Donny Dennison - 28 july 2020 11:39
            // check the address if there is product using this address then cannot delete
            // if (isset($alamat_default->id)) {
            //     $this->cpm->updateByUserId($nation_code, $pelanggan->id, array("b_user_alamat_id"=>$alamat_default->id));
            //     if ($this->is_log) {
            //         $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan::alamat_hapus -> All product address has changed to new default address");
            //     }
            // }
            
        // } else {
        //     $alamat_default = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
        //     if (isset($alamat_default->id)) {
        //         $this->cpm->updateByUserIdAlamatId($nation_code, $pelanggan->id, $b_user_alamat_id, array("b_user_alamat_id"=>$alamat_default->id));
        //         if ($this->is_log) {
        //             $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan::alamat_hapus -> Product with address ID: $b_user_alamat_id has been changed to default");
        //         }
        //     }

        }
        $data['alamat'] = $alamat_list;
        foreach($data['alamat'] AS &$alamat){
            if($pelanggan->language_id == 2){//indonesia
                $alamat->judul = str_replace("Your Place", "Alamat", $alamat->judul);
            // }else if($pelanggan->language_id == 3){//korea
            //     $data['alamat']->judul = str_replace("Your Place", "", $data['alamat']->judul);
            // }else if($pelanggan->language_id == 4){//thailand
            //     $data['alamat']->judul = str_replace("Your Place", "", $data['alamat']->judul);
            }else{//english
                // $data['alamat']->judul = str_replace("Your Place", "", $data['alamat']->judul);
            }
        }

        //render
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    /**
     * Set user default address
     * @param  int $b_user_alamat_id [description]
     */
    public function alamat_default($b_user_alamat_id)
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['alamat'] = array();

        //check passing param
        $b_user_alamat_id = (int) $b_user_alamat_id;
        if ($b_user_alamat_id<=0) {
            $this->status = 1772;
            $this->message = 'Invalid User Alamat ID';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        $user_alamat = $this->bua->getByIdUserId($nation_code, $pelanggan->id, $b_user_alamat_id);
        if (!isset($user_alamat->id)) {
            $this->status = 1791;
            $this->message = 'User address with supplied ID not found or invalid or already deleted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //if(empty($user_alamat->is_active)){
        //$this->status = 1772;
        //$this->message = 'User address already deleted';
        //$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //die();
        //}
        //remove default from any address
        $du = array();
        $du['is_default'] = 0;
        $this->bua->updateByUserId($nation_code, $pelanggan->id, $du);

        //set one address to default
        $du = array();
        $du['is_default'] = 1;
        $res = $this->bua->update($nation_code, $pelanggan->id, $b_user_alamat_id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';

            //by Donny Dennison - 19 january 2022 10:35
            //merge table free product to table product
            $allFreeProduct = $this->cpm->getMyProduk($nation_code, $pelanggan->id, 0, 0, "cp.id", "ASC", "", "", "Free", "", $pelanggan);
            foreach($allFreeProduct as $product){
                $du = array();
                $du['alamat2'] = $user_alamat->alamat2;
                $du['kelurahan'] = $user_alamat->kelurahan;
                $du['kecamatan'] = $user_alamat->kecamatan;
                $du['kabkota'] = $user_alamat->kabkota;
                $du['provinsi'] = $user_alamat->provinsi;
                $du['kodepos'] = $user_alamat->kodepos;
                $du['latitude'] = $user_alamat->latitude;
                $du['longitude'] = $user_alamat->longitude;
                $this->cpm->update($nation_code, $pelanggan->id, $product->id, $du);
            }
        } else {
            $this->status = 1793;
            $this->message = 'Failed deleting user address';
        }

        //get latest data
        $data['alamat'] = $this->bua->getByUserId($nation_code, $pelanggan->id);
        foreach($data['alamat'] AS &$alamat){
            if($pelanggan->language_id == 2){//indonesia
                $alamat->judul = str_replace("Your Place", "Alamat", $alamat->judul);
            // }else if($pelanggan->language_id == 3){//korea
            //     $data['alamat']->judul = str_replace("Your Place", "", $data['alamat']->judul);
            // }else if($pelanggan->language_id == 4){//thailand
            //     $data['alamat']->judul = str_replace("Your Place", "", $data['alamat']->judul);
            }else{//english
                // $data['alamat']->judul = str_replace("Your Place", "", $data['alamat']->judul);
            }
        }

        //render
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    /**
     * Get user default address
     */
    public function alamat_default_get()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['alamat_default'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        $user_alamat = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
        if (!isset($user_alamat->id)) {
            $this->status = 1791;
            $this->message = 'User address with supplied ID not found or invalid or already deleted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //render
        $this->status = 200;
        $this->message = 'Success';
        $data['alamat_default'] = $user_alamat;
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    /**
     * Update FCM Token
     * POST     fcm_token
     * POST     device
     */
    public function update_fcm()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        // if ($this->is_log) {
        //     $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan::update_fcm apisess: $apisess");
        // }

        //flush old fcm_token
        if (strlen($pelanggan->fcm_token)>6) {
            $fcm_token_old = explode(':', $pelanggan->fcm_token);
            if (isset($fcm_token_old[0])) {
                $fcm_token_old = $fcm_token_old[0];
            }
            if (is_string($fcm_token_old)) {
                $this->bu->flushFcmToken($fcm_token_old);
                // if ($this->is_log) {
                //     $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan::update_fcm --flushFCM fcm_token_old: $fcm_token_old");
                // }
            }
        }

        //collect input
        $device = strtolower(trim($this->input->post("device")));
        $fcm_token = $this->input->post("fcm_token");

        //validating
        if ($device == 'ios') {
            $device = 'ios';
        } else {
            $device = 'android';
        }
        // if (strlen($fcm_token)<=50) {
        //     $fcm_token = '';
        // }

        //update to table b_user
        $du = array("fcm_token"=>$fcm_token,"device"=>$device);
        $res = $this->bu->update($nation_code, $pelanggan->id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->status = 1798;
            $this->message = 'Failed';
        }
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    /**
     * Get notification list
     */
    public function pemberitahuan()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['pemberitahuan'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        // if ($this->is_log) {
        //     $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan::pemberitahuan");
        // }

        //manipulator
        $dpem = $this->dpem->getAll($nation_code, $pelanggan->id, $page=0, $pageSize=100, "cdate", "desc");
        foreach ($dpem as &$dp) {
            if (strlen($dp->extras)<=2) {
                $dp->extras = '{}';
            }
            $obj = json_decode($dp->extras);
            if (is_object($obj)) {
                $dp->extras = $obj;
            }
            if (strlen($dp->gambar)>4) {
                $dp->gambar = $this->cdn_url($dp->gambar);
            }
        }

        if (false) {
            //$this->dpem->updateUnRead($nation_code,$pelanggan->id);
            // if ($this->is_log) {
            //     $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan::pemberitahuan --forceRead");
            // }
        }

        //success
        $this->status = 200;
        $this->message = 'Success';
        $data['pemberitahuan'] = $dpem;
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    /**
     * Get notification list count
     */
    public function pemberitahuan_count()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['pemberitahuan_count'] = 0;

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //manipulator
        $pemberitahuan_count = (int) $this->dpem->countUnRead($nation_code, $pelanggan->id);
        $data['pemberitahuan_count'] = $pemberitahuan_count;

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    /**
     * Set notification list as read
     */
    public function pemberitahuan_read()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['pemberitahuan'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //$this->dpem->setReadByUserId($nation_code,$pelanggan->id,$pemberitahuan->id);

        //manipulator
        $dpem = $this->dpem->getAll($nation_code, $pelanggan->id, $page=0, $pageSize=100, "cdate", "desc");
        foreach ($dpem as &$dp) {
            if (strlen($dp->extras)<=2) {
                $dp->extras = '{}';
            }
            $obj = json_decode($dp->extras);
            if (is_object($obj)) {
                $dp->extras = $obj;
            }
            if (strlen($dp->gambar)>4) {
                $dp->gambar = $this->cdn_url($dp->gambar);
            }
        }
        //$this->dpem->updateUnRead($nation_code,$pelanggan->id);

        //success
        $this->status = 200;
        $this->message = 'Success';
        $data['pemberitahuan'] = $dpem;
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    /**
     * Registration email verification
     */
    public function verifikasi_email()
    {
        //initial
        $dt = $this->__init();

        $this->status = 200;
        $this->message = 'Success';

        //by Donny Dennison - 3 september 2020 13:22
        //error undefined variable data
        $data = "0";
        
        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        if (empty($pelanggan->is_confirmed) && (strlen($pelanggan->fb_id)<=0 || strlen($pelanggan->google_id)<=0)) {
            $data = $pelanggan->is_confirmed;
            $link = $this->__activateGenerateLink($nation_code, $pelanggan->id, $pelanggan->api_reg_token);
            $email = $pelanggan->email;
            $nama = $pelanggan->fnama;
            $replacer = array();
            $replacer['site_name'] = $this->app_name;
            $replacer['fnama'] = $nama;
            $replacer['activation_link'] = $link;
            $this->seme_email->flush();
            $this->seme_email->replyto($this->site_name, $this->site_replyto);
            $this->seme_email->from($this->site_email, $this->site_name);
            $this->seme_email->subject('Please Confirm your email');
            $this->seme_email->to($email, $nama);
            $this->seme_email->template('account_register');
            $this->seme_email->replacer($replacer);
            $this->seme_email->send();

            //$this->status = 1709;
            //by Donny Dennison
            //Request dari Mr Jackie untuk mengganti message
            // $this->message = 'Please activate your new account by clicking your email';
            $this->message = 'Please activate your new account by clicking the link at your email';

            // if ($this->is_log) {
            //     $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan::verifikasi_email --userID: $pelanggan->id --unconfirmedEmail");
            // }
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        } else {
            $data = "1";
            $this->message = 'Success';
        }

        $checkAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);
        if(!isset($checkAddress->id)){
            $this->status = 1001;
            $this->message = 'Please add address';
        }

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    /**
     * Bank account number verification
     * @return [type] [description]
     */
    public function verifikasi_rekening()
    {
        //initial
        $dt = $this->__init();

        $this->status = 200;
        $this->message = 'Success';

        //by Donny Dennison - 3 september 2020 13:22
        //error undefined variable data
        $data = "0";

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        $bankacc = $this->bubam->getByUserId($nation_code, $pelanggan->id);
        if (!isset($bankacc->a_bank_id)) {
            $data = "0";
            $this->message = 'Please activate your new account by inserting bank account';
        } else {
            $data = "1";
            $this->message = 'Success';
        }

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    //by Donny Dennison
    //to compare version mobile app with version in database
    public function check_version_mobile_app()
    {
        
        //initial
        $dt = $this->__init();

        $this->status = 200;
        $this->message = 'Success';
        $data = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        // $apikey = $this->input->get('apikey');
        // $c = $this->apikey_check($apikey);
        // if (!$c) {
        //     $this->status = 400;
        //     $this->message = 'Missing or invalid API key';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //     die();
        // }

        //check apisess
        // $apisess = $this->input->get('apisess');
        // $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        // if (!isset($pelanggan->id)) {
        //     $this->status = 401;
        //     $this->message = 'Missing or invalid API session';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //     die();
        // }

        //get mobile version from post
        $mobile_version = $this->input->post('mobile_version');

        //get device type from post, "android" or "ios"
        $device = $this->input->post('device');

        //get version / update type from post, "" or "minor" or "major"
        $type = $this->input->post('type');

        //get new mobile version from database
        $mobileVersionFromDatabase = $this->fvmm->compareMobileVersion($nation_code, $device, $mobile_version);

        //set return status
        $data = 0;

        if ($mobileVersionFromDatabase != null) {
            if ($type == null || $type == "") {
                //check if there is force update (status = 2)
                foreach ($mobileVersionFromDatabase as $value) {
                    if ($value['status'] == 2 && $value['version'] != $mobile_version) {
                        $data = 2;
                        break;
                    }
                }

                if ($data == 0) {
                    if ($mobile_version != $mobileVersionFromDatabase[0]["version"]) {

                        //set return status
                        $data=array(
                            'status_update' =>$mobileVersionFromDatabase[0]["status"],
                            'message_update' =>'Minor Update',
                            'version_update' =>$mobileVersionFromDatabase[0]["version"]
                        );
                    } else {

                        //set return status
                        $data=array(
                            'status_update' =>0,
                            'message_update' =>'No Update',
                            'version_update' =>$mobileVersionFromDatabase[0]["version"]
                        );
                    }
                } else {

                    //set return status
                    $data=array(
                        'status_update' =>2,
                        'message_update' =>'Force Update',
                        'version_update' =>$mobileVersionFromDatabase[0]["version"]
                    );
                }

            } else {
                if ($type == 'minor') {
                    foreach ($mobileVersionFromDatabase as $value) {
                        if ($value['status'] == 1 && $value['version'] != $mobile_version) {
                            //set return status
                            $data=array(
                                'update' =>'yes',
                                'status_update' =>$value["status"],
                                'message_update' =>'Minor Update',
                                'version_update' =>$value["version"]
                            );
                            break;
                        }
                    }
                } else {
                    foreach ($mobileVersionFromDatabase as $value) {
                        if ($value['status'] == 2 && $value['version'] != $mobile_version) {
                            //set return status
                            $data=array(
                                'update' =>'yes',
                                'status_update' =>$value["status"],
                                'message_update' =>'Force Update',
                                'version_update' =>$value["version"]
                            );
                            break;
                        }
                    }
                }

                if ($data == 0) {
                    //set return status
                    $data=array(
                        'update' =>'no',
                        'status_update' =>0,
                        'message_update' =>'No Update',
                        'version_update' =>$mobileVersionFromDatabase[0]["version"]
                    );
                }
            }

        } else {
            $mobileVersionFromDatabase = $this->fvmm->getNewMobileVersion($nation_code, $device);
            $data=array(
                'update' =>'yes',
                'status_update' =>2,
                'message_update' =>'Force Update',
                'version_update' =>$mobileVersionFromDatabase->version
            );
        }

        $data["map_coverage"] = $this->gmcm->getAll($nation_code);

        $data["default_setting_home_gnb"] = "all";
        $default_setting_home_gnb = $this->ccm->getByClassifiedAndCode($nation_code,"app_config","C5");
        if(isset($default_setting_home_gnb->remark)){
          $data["default_setting_home_gnb"] = $default_setting_home_gnb->remark;
        }

        $data["share_sellon_image"] = $this->ccm->getByClassifiedAndCode($nation_code,"app_config","C9");
        if(isset($data["share_sellon_image"]->remark)){
            $data["share_sellon_image"] = $this->cdn_url($data["share_sellon_image"]->remark);
        }else{
            $data["share_sellon_image"] = "";
        }

        $data["facebook_login"] = $this->ccm->getByClassifiedAndCode($nation_code,"app_config","C14");
        if(isset($data["facebook_login"]->remark)){
            $data["facebook_login"] = $data["facebook_login"]->remark;
        }else{
            $data["facebook_login"] = "off";
        }

        $data["minimum_video_length_to_get_spt"] = $this->ccm->getByClassifiedAndCode($nation_code,"leaderboard_point","E26");
        if(isset($data["minimum_video_length_to_get_spt"]->remark)){
            $data["minimum_video_length_to_get_spt"] = $data["minimum_video_length_to_get_spt"]->remark;
        }else{
            $data["minimum_video_length_to_get_spt"] = "10";
        }

        $data["max_club_created_each_day"] = $this->ccm->getByClassifiedAndCode($nation_code,"leaderboard_point","E30");
        if(isset($data["max_club_created_each_day"]->remark)){
            $data["max_club_created_each_day"] = $data["max_club_created_each_day"]->remark;
        }else{
            $data["max_club_created_each_day"] = "10";
        }

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");

    }

    //by Donny Dennison - 26 november 2021 15:25
    //api get general location list
    public function general_location_list()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['general_location'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');

        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        // check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $pelanggan = new stdClass();
            if($nation_code == 62){ //indonesia
                $pelanggan->language_id = 2;
            }else if($nation_code == 82){ //korea
                $pelanggan->language_id = 3;
            }else if($nation_code == 66){ //thailand
                $pelanggan->language_id = 4;
            }else {
                $pelanggan->language_id = 1;
            }
        }

        $keyword = trim($this->input->get("keyword"));

        //keyword
        if (mb_strlen($keyword)>1) {
          //$keyword = utf8_encode(trim($keyword));
          $enc = mb_detect_encoding($keyword, 'UTF-8');
          if ($enc == 'UTF-8') {
          } else {
              $keyword = iconv($enc, 'ISO-8859-1//TRANSLIT', $keyword);
          }
        } else {
          $keyword="";
        }
        $keyword = filter_var(strip_tags($keyword), FILTER_SANITIZE_SPECIAL_CHARS);
        $keyword = substr($keyword, 0, 32);

        $provinsi = trim($this->input->get("provinsi"));

        if(strlen($provinsi) <= 1){
            $provinsi = "DKI Jakarta";
        }

        //default response
        $this->status = 200;
        $this->message = 'Success';
        $data['general_location'] = $this->bual->getAll($nation_code, $keyword, $provinsi);

        // foreach($data['general_location'] AS &$location){
        //     $location->postal_sector = $this->bual->getAllPostalSector($nation_code, $location->postal_district);
        // }

        //render
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    //by Donny Dennison - 3 december 2021 14:45
    //add api logout 
    public function logout()
    {
        //init
        $dt = $this->__init();

        //default result format
        $data = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        if($apisess){
            $this->bu->flushApisessAndFcmToken($nation_code, $apisess);
        }
        
        $this->status = 200;
        $this->message = 'Success';
        
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    // public function checkkodepos()
    // {
    //     //initial
    //     $dt = $this->__init();

    //     //default result
    //     $data = array();
    //     // $data['alamat'] = array();

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if (!$c) {
    //         $this->status = 400;
    //         $this->message = 'Missing or invalid API key';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     //check apisess
    //     $apisess = $this->input->get('apisess');
    //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     if (!isset($pelanggan->id)) {
    //         $this->status = 401;
    //         $this->message = 'Missing or invalid API session';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     //populating input for location
    //     $kodepos = $this->input->post("kodepos");
    //     $b_user_alamat_id = $this->input->post("b_user_alamat_id");

    //     //validating
    //     if (empty($kodepos)) {
    //         $kodepos = '';
    //     }
    //     if (empty($b_user_alamat_id)) {
    //         $b_user_alamat_id = 0;
    //     }

    //     if (strlen($kodepos)<=0) {
    //         $this->status = 1770;
    //         $this->message = 'Zipcode / Postal Code are required';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     $checkKodeposAlreadyInDB = $this->bua->countByUserIdKodepos($nation_code, $pelanggan->id, $kodepos, $b_user_alamat_id);
    //     if ($checkKodeposAlreadyInDB > 0) {
    //         $this->status = 1774;
    //         $this->message = 'This address is already registered';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     $this->status = 200;
    //     $this->message = 'Success';

    //     //render
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    // }

    //by Donny Dennison - 06 January 2022 9:10
    //new api check apisess still exist in db or not
    public function check_login()
    {
        //initial
        $dt = $this->__init();
        $data = array();

        $this->status = 200;
        $this->message = 'Success';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        if(strlen($apisess) <= 3){
            $this->status = 200;
            // $this->message = 'API session empty';
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            // $this->status = 401;
            $this->status = 200;
            // $this->message = 'Missing or invalid API session';
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }

    //START by Donny Dennison - 19 july 2022 15:42
    //delete temporary or permanent user feature
    public function delete()
    {
        //initial
        $dt = $this->__init();
        $data = array();

        $this->status = 200;
        $this->message = 'Success';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        $delete_type = strtolower(trim($this->input->post("delete_type")));
        if($delete_type != "delete"){
            $delete_type = "deactivate";
        }

        $login_type = strtolower(trim($this->input->post("login_type")));
        if($login_type != "sosmed"){
            $login_type = "email";
        }

        $password = $this->__passClear(trim($this->input->post("password")));

        if($login_type == "email"){

            if (!hash_equals($pelanggan->password, hash("sha256", $password))){
                $this->status = 1715;
                $this->message = 'Invalid password';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }

        }

        $text = trim($this->input->post("text"));
        $text = str_replace('’',"'",$text);
        $text = nl2br($text);
        $text = str_replace(array("\r\n", "\n\r", "\r", "\n"), "", $text);
        $text = str_replace("\\n", "<br />", $text);

        // $totalUnfinishedOrderSeller = $this->dodm->countUnfinishedOrderSeller($nation_code, $pelanggan->id);
        // if($totalUnfinishedOrderSeller > 0){
        //     $this->status = 1714;
        //     $this->message = 'Cannot delete because there is unfinised business';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //     die(); 
        // }

        // $totalUnfinishedOrderBuyer = $this->dodim->countUnfinishedOrderBuyer($nation_code, $pelanggan->id);
        // if($totalUnfinishedOrderBuyer > 0){
        //     $this->status = 1714;
        //     $this->message = 'Cannot delete because there is unfinised business';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
        //     die(); 
        // }

        $totalUnfinishedOfferSeller = $this->ecrm->countAllUnfinisedOffer($nation_code, 'offer', "seller", $pelanggan->id, array(0 => "offering", 1=>"accepted", 2 => "waiting review from seller", 3 => "waiting review from buyer"));
        if($totalUnfinishedOfferSeller > 0){
            $this->status = 1714;
            $this->message = 'Cannot delete because there is unfinised business';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die(); 
        }

        $totalUnfinishedOfferBuyer = $this->ecrm->countAllUnfinisedOffer($nation_code, 'offer', "buyer", $pelanggan->id, array(0 => "offering", 1=>"accepted", 2 => "waiting review from seller", 3 => "waiting review from buyer"));
        if($totalUnfinishedOfferBuyer > 0){
            $this->status = 1714;
            $this->message = 'Cannot delete because there is unfinised business';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die(); 
        }

        $du = array();
        $du['api_mobile_token'] = "";
        $du['fcm_token'] = "";
        $du['is_active'] = 0;

        if($delete_type == "delete"){
            $du['is_permanent_inactive'] = 0;
            $du['permanent_inactive_date'] = date('Y-m-d H:i:s');

            //by Donny Dennison - 14 december 2022 11:55
            //delete permanent user cannot register anymore
            $du['permanent_inactive_by'] = "customer";
        }

        $du['inactive_text'] = $text;
        $this->bu->update($nation_code, $pelanggan->id, $du);

        // if($delete_type == "delete"){
        //     // call BlackListUserWallet from blockchain API, stop rewarding and withdraw suspended user
        //     $userList = $this->bu->getByIdData($nation_code, $pelanggan->id);
        //     if(count($userList) > 0){
        //         $postdata = array();
        //         foreach ($userList as $user) {
        //             $postdata[] = array(
        //                 'userWalletCode' => $this->__encryptdecrypt($user->user_wallet_code, "encrypt"),
        //                 'countryIsoCode' => $this->blockchain_api_country,
        //             );
        //         }
        //         unset($user);

        //         $postdata = array(
        //             "userWalletList" => $postdata
        //         );

        //         $responseWalletApi = 0;
        //         $response = json_decode($this->__callBlockChainBlacklist($postdata));
        //         if(isset($response->responseCode)){
        //             if($response->responseCode == 0){
        //                 $responseWalletApi = 1;
        //             }
        //         }
        //         unset($response);
        //     }
        //     unset($userList);
        // }

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }
    //END by Donny Dennison - 19 july 2022 15:42
    //delete temporary or permanent user feature

    //START by Donny Dennison - 10 november 2022 14:34
    //new feature, join/input referral
    public function inputreferral()
    {
        //initial
        $dt = $this->__init();
        $data = array();

        $this->status = 200;
        $this->message = 'Success';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        if($pelanggan->b_user_id_recruiter != '0'){
            $this->status = 1719;
            $this->message = 'You already have Referral';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        if(date("Y-m-d", strtotime($pelanggan->cdate." +5 days")) < date("Y-m-d")){
            $this->status = 1717;
            $this->message = 'Input Referral already past time limit';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        $kode_referral = strtolower(trim($this->input->post("kode_referral")));
        $checkKodeReferral = $this->bu->checkKodeReferral($nation_code, $kode_referral);
        if(!isset($checkKodeReferral->id)){
            $this->status = 1721;
            $this->message = 'Referral code is invalid';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        if($pelanggan->kode_referral == $kode_referral){
            $this->status = 1721;
            $this->message = 'Referral code is invalid';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        if($pelanggan->id == $checkKodeReferral->b_user_id_recruiter){
            $this->status = 1725;
            $this->message = 'You cannot use Recommender referral code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        $du = array();
        $du['b_user_id_recruiter'] = $checkKodeReferral->id;
        $this->bu->update($nation_code, $pelanggan->id, $du);

        //recommendee
        $recruiterData = $this->bu->getById($nation_code, $checkKodeReferral->id);
        //get point
        $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E5");
        if (!isset($pointGet->remark)) {
          $pointGet = new stdClass();
          $pointGet->remark = 50;
        }

        $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

        $di = array();
        $di['nation_code'] = $nation_code;
        $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
        $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
        $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
        $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
        $di['b_user_id'] = $pelanggan->id;
        $di['point'] = $pointGet->remark;
        $di['custom_id'] = $checkKodeReferral->id;
        $di['custom_type'] = 'input referral manual';
        $di['custom_type_sub'] = 'recommendee';
        $di['custom_text'] = $pelanggan->fnama.' '.$di['custom_type'].' '.$di['custom_type_sub'].' '.$recruiterData->fnama.' and get '.$di['point'].' point(s)';
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
        // $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '+', $di['point']);

        //recommender
        if($recruiterData->is_active == 1){
            //get point
            $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E6");
            if (!isset($pointGet->remark)) {
              $pointGet = new stdClass();
              $pointGet->remark = 50;
            }

            $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $checkKodeReferral->id);

            $di = array();
            $di['nation_code'] = $nation_code;
            $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
            $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
            $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
            $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
            $di['b_user_id'] = $checkKodeReferral->id;
            $di['point'] = $pointGet->remark;
            $di['custom_id'] = $pelanggan->id;
            $di['custom_type'] = 'input referral manual';
            $di['custom_type_sub'] = 'recommender';
            $di['custom_text'] = $pelanggan->fnama.' '.$di['custom_type'].' '.$di['custom_type_sub'].' '.$recruiterData->fnama.' and get '.$di['point'].' point(s)';
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
            // $this->glrm->updateTotal($nation_code, $checkKodeReferral->id, 'total_point', '+', $di['point']);
        }

        // $this->bu->updateTotal($nation_code, $checkKodeReferral->id, "total_recruited", "+", "1");
        // $this->bu->updateDate($nation_code, $checkKodeReferral->id, "bdate", date("Y-m-d H:i:s"));
        $this->bu->updateTotalAndBDate($nation_code, $checkKodeReferral->id, "total_recruited", "+", "1", "bdate", date("Y-m-d H:i:s"));

        if(date("Y-m-d") >= "2023-10-16" && date("Y-m-d") <= "2023-10-31"){
            $eventProgress = $this->ccertm->getByUserid($nation_code, $checkKodeReferral->id);
            if(isset($eventProgress->id)){
                if($eventProgress->cdate_day_4 && !$eventProgress->cdate_day_5){
                    if(date("Y-m-d", strtotime($eventProgress->cdate_day_4. " +1 day")) == date("Y-m-d")){
                        $du = array();
                        $du['task_day_5'] = $checkKodeReferral->id;
                        $du['cdate_day_5'] = 'NOW()';
                        $this->ccertm->update($nation_code, $eventProgress->id, $du);

                        $dpe = array();
                        $dpe['nation_code'] = $nation_code;
                        $dpe['b_user_id'] = $checkKodeReferral->id;
                        $dpe['id'] = $this->dpem->getLastId($nation_code, $checkKodeReferral->id);
                        $dpe['type'] = "event_hashtag_retargeting";
                        if($checkKodeReferral->language_id == 2) {
                          $dpe['judul'] = "Retargeting Event";
                          $dpe['teks'] =  "Anda telah menyelesaikan Misi Harian di Event user lama. Kami akan menverifikasi proses anda. Mohon menunggu instruksi berikutnya.";
                        } else {
                          $dpe['judul'] = "Retargeting Event";
                          $dpe['teks'] =  "You have successfully completed the Daily Mission in our Event for old user. We are currently verifying your data. Please await further instructions.";
                        }

                        $dpe['gambar'] = 'media/pemberitahuan/promotion.png';
                        $dpe['cdate'] = "NOW()";
                        $extras = new stdClass();
                        $extras->id = $checkKodeReferral->id;
                        if($checkKodeReferral->language_id == 2) { 
                          $extras->judul = "Retargeting Event";
                          $extras->teks =  "Anda telah menyelesaikan Misi Harian di Event user lama. Kami akan menverifikasi proses anda. Mohon menunggu instruksi berikutnya.";
                        } else {
                          $extras->judul = "Retargeting Event";
                          $extras->teks =  "You have successfully completed the Daily Mission in our Event for old user. We are currently verifying your data. Please await further instructions.";
                        }

                        $dpe['extras'] = json_encode($extras);
                        $this->dpem->set($dpe);

                        $classified = 'setting_notification_user';
                        $code = 'U3';
                        $receiverSettingNotif = $this->busm->getValue($nation_code, $checkKodeReferral->id, $classified, $code);
                        if (!isset($receiverSettingNotif->setting_value)){
                            $receiverSettingNotif->setting_value = 0;
                        }

                        if ($receiverSettingNotif->setting_value == 1 && $checkKodeReferral->is_active == 1) {
                            if($checkKodeReferral->device == "ios"){
                                $device = "ios";
                            }else{
                                $device = "android";
                            }

                            $tokens = $checkKodeReferral->fcm_token;
                            if(!is_array($tokens)) $tokens = array($tokens);
                            if($checkKodeReferral->language_id == 2){
                                $title = "Retargeting Event";
                                $message = "Anda telah menyelesaikan Misi Harian di Event user lama. Kami akan menverifikasi proses anda. Mohon menunggu instruksi berikutnya.";
                            } else {
                                $title = "Retargeting Event";
                                $message = "You have successfully completed the Daily Mission in our Event for old user. We are currently verifying your data. Please await further instructions.";
                            }

                            $image = 'media/pemberitahuan/promotion.png';
                            $type = 'event_hashtag_retargeting';
                            $payload = new stdClass();
                            $payload->id = $checkKodeReferral->id;
                            if($checkKodeReferral->language_id == 2) {
                                $payload->judul = "Retargeting Event";
                                $payload->teks = "Anda telah menyelesaikan Misi Harian di Event user lama. Kami akan menverifikasi proses anda. Mohon menunggu instruksi berikutnya.";
                            } else {
                             $payload->judul = "Retargeting Event";
                                $payload->teks = "You have successfully completed the Daily Mission in our Event for old user. We are currently verifying your data. Please await further instructions.";
                            }
                            $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);
                        }
                    }
                }
            }
        }

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    }
    //END by Donny Dennison - 10 november 2022 14:34
    //new feature, join/input referral

    // public function daftarv3()
    // {
    //     //initial
    //     $token = '';
    //     $user_id = 0;
    //     $register_success = 0;
    //     $user = new stdClass();
    //     $dt = $this->__init();

    //     // $this->seme_log->write("api_mobile", "API_Mobile/pelanggan/daftar:: --POST: ".json_encode($_POST));

    //     //default response
    //     $data = array();
    //     $data['apisess'] = '';
    //     $data['apisess_expired'] = '';
    //     $data['pelanggan'] = new stdClass();

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if (empty($c)) {
    //         $this->status = 400;
    //         $this->message = 'Missing or invalid API key';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     //https://php.watch/articles/modern-php-encryption-decryption-sodium
    //     // $keypair = sodium_crypto_box_keypair();
    //     // $keypair_public = sodium_crypto_box_publickey($keypair);
    //     // $keypair_secret = sodium_crypto_box_secretkey($keypair);
    //     // echo base64_encode($keypair_public);
    //     // echo "</br>";
    //     // echo base64_encode($keypair_secret);
    //     // echo "</br>";
    //     // $nonce = \random_bytes(\SODIUM_CRYPTO_BOX_NONCEBYTES);
    //     // $sender_keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey($keypair_secret, base64_decode("UXdxmySljXUyZ7CQzeoT5pqSb3eBVJ1NutpJdz4/S1E="));
    //     // $message = "Hi Bob, I'm Alice";
    //     // $encrypted_signed_text = sodium_crypto_box($message, $nonce, $sender_keypair);
    //     // echo base64_encode($nonce);
    //     // echo "</br>";
    //     // echo base64_encode($encrypted_signed_text);
    //     // die();
    //     $server_publickey = "UXdxmySljXUyZ7CQzeoT5pqSb3eBVJ1NutpJdz4/S1E=";
    //     $server_privatekey = "fAHE8sXgwi4Tm58T62aJ02XicKeC7k9DW9uRUB8+JKc=";
    //     $recipient_keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey(base64_decode($server_privatekey), base64_decode($this->input->post("bla2")));
    //     $postData = sodium_crypto_box_open(base64_decode($this->input->post("bla1")), base64_decode($this->input->post("bla3")), $recipient_keypair);
    //     if ($postData === false) {
    //         $this->status = 1750;
    //         $this->message = 'Please check your data again';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }
    //     $postData = json_decode($postData);

    //     $listOfPostData = array(
    //         "google_id",
    //         "fb_id",
    //         "apple_id",
    //         "email",
    //         "password",
    //         "password_confirm",
    //         "telp",
    //         "fnama",
    //         "fcm_token",
    //         "device",
    //         "language_id",
    //         "coverage_id",
    //         "is_changed_address",
    //         "verifPhone",
    //         "call_from",
    //         "ip_address",
    //         "kode_referral",
    //         "country_origin",
    //         "referral_type",
    //         "is_emulator",
    //         "device_id",
    //         "address_penerima_nama",
    //         "address_penerima_telp",
    //         "address_alamat2",
    //         "address_provinsi",
    //         "address_kabkota",
    //         "address_kecamatan",
    //         "address_kelurahan",
    //         "address_kodepos",
    //         "address_latitude",
    //         "address_longitude",
    //         "address_catatan"
    //     );

    //     foreach($listOfPostData as $value) {
    //         if(!isset($postData->$value)){
    //             $postData->$value = "";
    //         }
    //     }

    //     //flags
    //     $reg_from = 'online';
    //     $is_telp_valid = 0;
    //     $is_email_valid = 0;
    //     $is_password_valid = 0;
    //     $is_telp = 0;

    //     //populate input
    //     $email = strtolower(trim($postData->email));
    //     $telp = $postData->telp;
    //     $fb_id = $postData->fb_id;
    //     $google_id = $postData->google_id;
    //     $apple_id = $postData->apple_id;
    //     $fnama = trim($postData->fnama);
    //     $password = $postData->password;
    //     $password_confirm = $postData->password_confirm;
    //     $fcm_token = $postData->fcm_token;
    //     $device = strtolower(trim($postData->device));
        
    //     //by Donny Dennison - 17 february 2022 17:51
    //     //change message language in response/return
    //     $language_id = trim($postData->language_id);

    //     //START by Donny Dennison - 08 june 2022 15:15
    //     //change address flow in register
    //     $coverage_id = trim($postData->coverage_id);
    //     $is_changed_address = trim($postData->is_changed_address);
    //     if($is_changed_address != 1){
    //         $is_changed_address = 0;
    //     }
    //     //END by Donny Dennison - 08 june 2022 15:15

    //     $verifPhone = trim($postData->verifPhone);

    //     if (strlen($email)>4) {
    //         $is_email_valid = 1;
    //     } else {
    //         $email = '';
    //     }
    //     if ($is_email_valid) {
    //         $cem = $this->bu->checkEmailIgnoreActive($nation_code, $email);
    //         if (isset($cem->id)) {
    //             $this->status = 1702;
    //             // $this->message = 'Email already registered, please try another email or login with current email';
    //             $this->message = 'Email is already registered. Please try again with another email';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }
    //     }

    //     if (strlen($telp)>4) {
    //         $is_telp_valid = 1;
    //     } else {
    //         $telp = '';
    //     }

    //     if($is_email_valid != 1 && $is_telp_valid != 1){
    //         $this->status = 1732;
    //         $this->message = 'Please input the correct email';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     $confirmeds=0;
    //     if (strlen($fb_id)>1) {
    //         $reg_from = 'facebook';
    //         $confirmeds=1;
    //     } elseif (strlen($google_id)>1) {
    //         $reg_from = 'google';
    //         $confirmeds=1;
    //     } elseif (strlen($apple_id)>1) {
    //         $reg_from = 'apple';
    //         $confirmeds=1;
    //     } elseif (empty($is_email_valid) && !empty($is_telp_valid)) {
    //         $reg_from = 'phone';
    //         $confirmeds=1;
    //     } else {
    //         $reg_from = 'online';
    //         $confirmeds=0;
    //     }

    //     //check fcm_token valid in firebase or not
    //     //https://stackoverflow.com/a/45697880
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    //     curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");

    //     $headers = array();
    //     $headers[] = 'Content-Type:  application/json';
    //     $headers[] = 'Authorization:  key='.$this->fcm_server_token;
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //     $postdata = array(
    //       'registration_ids' => array($fcm_token)
    //     );
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));

    //     $result = curl_exec($ch);
    //     if (curl_errno($ch)) {
    //       return 0;
    //       //echo 'Error:' . curl_error($ch);
    //     }
    //     curl_close($ch);

    //     $result = json_decode($result);
    //     if(isset($result->success)){
    //         if($result->success != 1){
    //             $this->status = 1750;
    //             $this->message = 'Please check your data again';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }
    //     }else{
    //         $this->status = 1750;
    //         $this->message = 'Please check your data again';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     // if($postData->call_from == "1ns!d3r"){
    //     //     $ip_address = $postData->ip_address;
    //     // }else{
    //     //     $ip_address = $_SERVER['HTTP_X_REAL_IP'];
    //     // }

    //     //lock table
    //     $this->bu->trans_start();

    //     // $countUserByIpAddress = $this->bu->checkByIpAddressDate($nation_code, $ip_address, $reg_from);
    //     // $countUserByFcmToken = $this->bu->checkByFcmTokenDate($nation_code, $fcm_token, $reg_from);
    //     // if($countUserByIpAddress > 0 || $countUserByFcmToken > 0){
    //     //     $this->bu->trans_rollback();
    //     //     $this->bu->trans_end();
    //     //     $this->status = 1750;
    //     //     $this->message = 'Please check your data again';
    //     //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //     //     die();
    //     // }
    //     // // unset($countUser, $ip_address);
    //     // unset($countUser);

    //     // $totalRegisterBefore = $this->bu->getForRegisterBefore($nation_code, $reg_from);
    //     // if ($totalRegisterBefore > 0) {
    //     //   $this->cpm->trans_rollback();
    //     //   $this->cpm->trans_end();
    //     //   $this->status = 1751;
    //     //   $this->message = 'Please try again';
    //     //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
    //     //   die();
    //     // }

    //     $blackList = $this->gblm->check($nation_code, "fcm_token", $fcm_token);
    //     if(isset($blackList->id)){
    //         $this->bu->trans_rollback();
    //         $this->bu->trans_end();
    //         // $this->status = 1707;
    //         // $this->message = 'Invalid email or password';
    //         $this->status = 1728;
    //         $this->message = "Sorry, you're banned to log in, please contact WA(+65 8856 2024)";
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     // $kode_referral = strtolower(trim($postData->kode_referral));
    //     // if (strlen($kode_referral) == 8) {
    //     //     $checkKodeReferral = $this->bu->checkKodeReferral($nation_code, $kode_referral);
    //     //     if(isset($checkKodeReferral->id)){
    //     //         $countRecomendeeByFcmToken = $this->bu->checkByFcmTokenRecommender($nation_code, $fcm_token, $checkKodeReferral->id);
    //     //         $countRecomendeeByIpAddress = $this->bu->checkByIpAddressRecommender($nation_code, $ip_address, $checkKodeReferral->id);
    //     //         if(($countRecomendeeByFcmToken == 4 || $countRecomendeeByIpAddress == 4) && $checkKodeReferral->total_recruited >= 4 && $checkKodeReferral->total_recruited <= 14){
    //     //             $du = array();
    //     //             $du['is_permanent_inactive'] = 0;
    //     //             $du['permanent_inactive_by'] = 'admin';
    //     //             $du['permanent_inactive_date'] = date('Y-m-d H:i:s');
    //     //             $du['api_mobile_token'] = "";
    //     //             $du['fcm_token'] = "";
    //     //             $du['is_active'] = 0;
    //     //             $du['is_confirmed'] = 0;
    //     //             $du['is_online'] = 0;
    //     //             $du['telp_is_verif'] = 0;
    //     //             $du['inactive_text'] = "spammer account(automatic)";
    //     //             $this->bu->update($nation_code, $checkKodeReferral->id, $du);

    //     //             // call BlackListUserWallet from blockchain API, stop rewarding and withdraw suspended user
    //     //             $postdata = array();
    //     //             $postdata[] = array(
    //     //             'userWalletCode' => $this->__encryptdecrypt($checkKodeReferral->user_wallet_code, "encrypt"),
    //     //             'countryIsoCode' => $this->blockchain_api_country,
    //     //             );

    //     //             $postdata = array(
    //     //                 "userWalletList" => $postdata
    //     //             );

    //     //             // $responseWalletApi = 0;
    //     //             $response = json_decode($this->__callBlockChainBlacklist($postdata));
    //     //             // if(isset($response->responseCode)){
    //     //             //     if($response->responseCode == 0){
    //     //             //         $responseWalletApi = 1;
    //     //             //     }
    //     //             // }
    //     //             // unset($response);

    //     //             $this->bu->trans_commit();
    //     //             $this->bu->trans_end();
    //     //             $this->status = 401;
    //     //             $this->message = 'Missing or invalid API session';
    //     //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //     //             die();
    //     //         }
    //     //     }
    //     // }
    //     // unset($kode_referral, $checkKodeReferral, $countRecomendee);
    //     // unset($ip_address);

    //     //by Donny Dennison - 16 june 2022 09:52
    //     //add new parameter "country_origin" in post at api "pelanggan/daftar"
    //     $country_origin = strtolower(trim($postData->country_origin));
    //     // if(empty($country_origin)){
    //     //     $country_origin = "indonesia";
    //     // }

    //     // force to indonesia
    //     $country_origin = "indonesia";
    //     if($country_origin != "indonesia"){
    //         $is_changed_address = 1;
    //     }

    //     //START by Donny Dennison - 12 september 2022 14:59
    //     //kode referral
    //     $kode_referral = strtolower(trim($postData->kode_referral));

    //     $referral_type = strtolower(trim($postData->referral_type));
    //     if($referral_type == "communitydetail"){
    //         $referral_type = "Community Detail";
    //     }else if($referral_type == "productdetail"){
    //         $referral_type = "Product Detail";
    //     }else if($referral_type == "shop"){
    //         $referral_type = "Shop";
    //     }else{
    //         $referral_type = "My Share";
    //     }
    //     //END by Donny Dennison - 12 september 2022 14:59
    //     //kode referral

    //     // by Muhammmad Sofi 10 January 2023 11:41 | get is_emulator
    //     $emulator = $postData->is_emulator;
    //     if($emulator != 1){
    //         $emulator = 0;
    //     }

    //     //by Donny Dennison - 20 september 2022 15:04
    //     //mobile registration activity feature
    //     $device_id = trim($postData->device_id);

    //     //START by Donny Dennison - 13 december 2022 14:31
    //     //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device
    //     $totalUsedDeviceId = $this->gdlm->countAll($nation_code, "", $device_id);

    //     //get max used in 1 device
    //     $maxUsed = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C6");
    //     if (!isset($maxUsed->remark)) {
    //         $maxUsed = new stdClass();
    //         $maxUsed->remark = 5;
    //     }

    //     if($totalUsedDeviceId >= $maxUsed->remark){
    //         $this->bu->trans_rollback();
    //         $this->bu->trans_end();
    //         $this->status = 1726;
    //         $this->message = "You're not allowed to use many accounts";
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();

    //     }
    //     //END by Donny Dennison - 13 december 2022 14:31
    //     //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device

    //     //validation
    //     if ($this->__mbLen($fnama)==0) {
    //         $fnama = "";
    //     }
    //     //$fnama = mb_ereg_replace('[a-zA-Z0-9\s,.!?]', '', $fnama);

    //     if (strlen($device)<3) {
    //         $this->status = 1755;
    //         $this->message = 'Unknown device type';
    //         // if ($this->is_log) {
    //         //     $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::daftar -- WARN '.$this->status.': '.$this->message);
    //         // }
    //     }
    //     // $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::daftar -- INFO device: '.$device);

    //     //by Donny Dennison - 08 june 2022 - 14:56
    //     //phone number not mandatory
    //     // //by Donny Dennison - 27 august 2020 - 14:52
    //     // //check telephone
    //     // $checkPhoneNumber = $this->bu->checkTelp($nation_code, $telp);
    //     // if (isset($checkPhoneNumber->id)) {
    //     //     $this->status = 1703;
    //     //     $this->message = 'Phone number already registered, please try another phone number';
    //     //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //     //     die();
    //     // }

    //     if ($this->__mbLen($fnama)>=64) {
    //         $this->bu->trans_rollback();
    //         $this->bu->trans_end();
    //         $this->status = 1736;
    //         $this->message = 'Name too long';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     // if (strlen($fcm_token)<=50) {
    //     //     $fcm_token = "";
    //     //     $this->status = 1756;
    //     //     $this->message = 'Invalid FCM Token';
    //     //     // $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::daftar -- WARN '.$this->status.': '.$this->message);
    //     // }

    //     //by Donny Dennison - 08 june 2022 15:15
    //     //change address flow in register
    //     $address_penerima_nama = $postData->address_penerima_nama;
    //     $address_penerima_telp = $postData->address_penerima_telp;
    //     $address_alamat2 = $postData->address_alamat2;
    //     $address_provinsi = $postData->address_provinsi;
    //     $address_kabkota = $postData->address_kabkota;
    //     $address_kecamatan = $postData->address_kecamatan;
    //     $address_kelurahan = $postData->address_kelurahan;
    //     $address_kodepos = $postData->address_kodepos;
    //     $address_latitude = $postData->address_latitude;
    //     $address_longitude = $postData->address_longitude;
    //     $address_catatan = $postData->address_catatan;
    //     // $latlng = $address_latitude.','.$address_longitude;

    //     // $details_url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=". $latlng. "&key=AIzaSyCzx5dDTHwJLrgqgM0LXH6D1VGAHC0m4Rw&language=id";

    //     // $ch = curl_init();
    //     // curl_setopt($ch, CURLOPT_URL, $details_url);
    //     // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     // $geoloc = json_decode(curl_exec($ch), true);
     
    //     // $step1 = $geoloc['results'];
    //     // $get1 = $step1[0]['address_components'];
    //     // $get11 = $get1[1];
    //     // $negara = $get1[9];
    //     // print_r($negara['short_name']);
    //     // // $this->debug($step1[0]['address_components']);
    //     // // die();
    //     // $step2 = $step1['geometry'];
    //     // $coords = $step2['location'];
     
    //     // print $coords['lat'];
    //     // print $coords['lng'];
    //     // // $test = $details_url->types[0]->country;
    //     // // $this->debug($details_url);
    //     // // die();



    //     // //by Donny Dennison - 28 december 2021 20:33
    //     // //add checking address
    //     // if(empty($address_alamat2) || empty($address_provinsi) || empty($address_kabkota) || empty($address_kecamatan) || empty($address_kelurahan) || empty($address_latitude) || empty($address_longitude)){
    //     //     //START by Donny Dennison - 08 june 2022 15:15
    //     //     //change address flow in register
    //     //     // $this->status = 104;
    //     //     // $this->message = 'There is address that empty';
    //     //     // $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //     //     // die();

    //     //     if($coverage_id > 0){
    //     //         $coverageDetail = $this->gmcm->getById($nation_code, $coverage_id);
    //     //         if(isset($coverageDetail->id)){
    //     //             if($coverageDetail->provinsi == 'DKI Jakarta'){
    //     //                 $address_alamat2 = "Gg. H. Zakaria, RW.5";
    //     //                 $address_provinsi = "DKI Jakarta";
    //     //                 $address_kabkota = "Jakarta Pusat";
    //     //                 $address_kecamatan = "Tanah Abang";
    //     //                 $address_kelurahan = "Kebon Melati";
    //     //                 $address_kodepos = "10230";
    //     //                 $address_latitude = "-6.200055499719067";
    //     //                 $address_longitude = "106.8162468531788";
    //     //             }else{
    //     //                 $address_alamat2 = "Gg. H. Zakaria, RW.5";
    //     //                 $address_provinsi = "DKI Jakarta";
    //     //                 $address_kabkota = "Jakarta Pusat";
    //     //                 $address_kecamatan = "Tanah Abang";
    //     //                 $address_kelurahan = "Kebon Melati";
    //     //                 $address_kodepos = "10230";
    //     //                 $address_latitude = "-6.200055499719067";
    //     //                 $address_longitude = "106.8162468531788";
    //     //             }
    //     //         }
    //     //     }else{
    //     //         $address_alamat2 = "Gg. H. Zakaria, RW.5";
    //     //         $address_provinsi = "DKI Jakarta";
    //     //         $address_kabkota = "Jakarta Pusat";
    //     //         $address_kecamatan = "Tanah Abang";
    //     //         $address_kelurahan = "Kebon Melati";
    //     //         $address_kodepos = "10230";
    //     //         $address_latitude = "-6.200055499719067";
    //     //         $address_longitude = "106.8162468531788";
    //     //     }
    //     //     //END by Donny Dennison - 08 june 2022 15:15
    //     // }

    //     // //check kelurahan, kecamatan, kabkota, provinsi, and kodepos in db or not
    //     // $checkInDBOrNot = $this->bual->checkInDBOrNot($nation_code, $postData->address_kelurahan, $postData->address_kecamatan, $postData->address_kabkota, $postData->address_provinsi, $postData->address_kodepos);
    //     // if (!isset($checkInDBOrNot->id)){
    //     //     $this->status = 104;
    //     //     $this->message = 'This address is invalid, please find other address';
    //     //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //     //     die();
    //     // }

    //     // $this->seme_log->write("api_mobile", 'API_Mobile/Pelanggan::daftar -- activate :'.$confirmeds);
    //     if (mb_strlen($password)>3) {
    //         $is_password_valid = 1;
    //     }

    //     //debug post
    //     //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan:: --POST: ".json_encode($_POST));

    //     //populate insert
    //     $di = array();
    //     $di['email'] = 'null';
    //     $di['fnama'] = $fnama;
    //     $di['lnama'] = "";
    //     $di['telp'] = 'null';
    //     $di['fb_id'] = 'null';
    //     $di['apple_id'] = 'null';
    //     $di['google_id'] = 'null';
    //     $di['fcm_token'] = $fcm_token;
    //     $di['cdate'] = 'NOW()';
    //     $di['image'] = 'media/user/default.png';
    //     // $di['latitude'] = 1.290270;
    //     // $di['longitude'] = 103.851959;
    //     $di['intro_teks'] = "";
    //     $di['api_reg_token'] = "";
    //     $di['api_web_token'] = "";
    //     $di['api_mobile_token'] = "";
    //     $di['api_social_id'] = "";
    //     $di['is_confirmed']= $confirmeds;
    //     $di['password'] = $this->__passGen($password);
    //     $di['device'] = $device;
    //     $di['register_from'] = $reg_from;
    //     $di['is_emulator'] = $emulator;

    //     //by Donny Dennison - 17 february 2022 17:51
    //     //change message language in response/return
    //     if($language_id){
    //         $di["language_id"] = $language_id;
    //     }else{
    //         if($nation_code == 62){
    //             $di["language_id"] = 2;
    //         }else if($nation_code == 82){
    //             $di["language_id"] = 3;
    //         }else if($nation_code == 66){
    //             $di["language_id"] = 4;
    //         }else {
    //             $di["language_id"] = 1;
    //         }
    //     }

    //     //by Donny Dennison - 08 june 2022 15:15
    //     //change address flow in register
    //     $di['is_changed_address'] = $is_changed_address;

    //     //by Donny Dennison - 16 june 2022 09:52
    //     //add new parameter "country_origin" in post at api "pelanggan/daftar"
    //     $di['country_origin'] = $country_origin;

    //     //by Donny Dennison - 20 september 2022 15:04
    //     //mobile registration activity feature
    //     if(strlen($device_id) > 3){
    //         $di['device_id'] = $device_id;
    //     }

    //     if($postData->call_from == "1ns!d3r"){
    //         $di['ip_address'] = $postData->ip_address;

    //     }else{
    //         $di['ip_address'] = $_SERVER['HTTP_X_REAL_IP'];
    //     }

    //     //registration flow
    //     if ($reg_from == 'google') {

    //         //only put correct value
    //         if (strlen($google_id)>1) {
    //             $di['google_id'] = $google_id;
    //         }
    //         if (strlen($email)>4) {
    //             $di['email'] = $email;
    //         }
    //         if (strlen($telp)>4) {
    //             $di['telp'] = $telp;
    //         }

    //         //check if already registered
    //         $user = $this->bu->auth_sosmed($nation_code, $fb_id, $google_id, $apple_id, $email, $telp);
    //         if (isset($user->id)) {
    //             $this->bu->trans_rollback();
    //             $this->bu->trans_end();
    //             $this->status = 1002;
    //             $this->message = 'User already registered using Google ID, please login';
    //             // if ($this->is_log) {
    //             //     $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::daftar -- ForcedClose '.$this->status.' '.$this->message);
    //             // }
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         $checkWhiteList = $this->giwlm->check($nation_code, $di['ip_address']);
    //         if(!isset($checkWhiteList->id)){

    //             $totalUserSameIP = $this->bu->countbyIpAddress($nation_code, $di['ip_address']);

    //             $limit = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C8");
    //             if (!isset($limit->remark)) {
    //               $limit = new stdClass();
    //               $limit->remark = 5;
    //             }

    //             if($totalUserSameIP >= $limit->remark){
    //                 $this->bu->trans_rollback();
    //                 $this->bu->trans_end();
    //                 $this->status = 1749;
    //                 $this->message = "You're not allowed to make a new account";
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }

    //         }

    //         //by Donny Dennison - 22 September 2021
    //         //auto-generate-password-social-media-signup
    //         $di['password'] = $this->__passGen('5ell0n2o2i');

    //         // $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- using Google ID");

    //     } elseif ($reg_from == 'apple') {

    //         //only put correct value
    //         if (strlen($apple_id)>1) {
    //             $di['apple_id'] = $apple_id;
    //         }
    //         if (strlen($email)>4) {
    //             $di['email'] = $email;
    //         }
    //         if (strlen($telp)>4) {
    //             $di['telp'] = $telp;
    //         }

    //         //START by Donny Dennison - 10 december 2020 15:01
    //         //new registration system for apple id
    //         $di['is_reset_password'] = 0;
    //         $di['password'] = $this->__passGen('5ell0n2o2i');

    //         // do {

    //         //     $di['telp'] = rand(10000000,19999999);

    //         //     //check already in db or havent
    //         //     $checkPhoneNumber = $this->bu->checkTelp($nation_code, $di['telp']);

    //         // } while (isset($checkPhoneNumber->id));

    //         //END by Donny Dennison - 10 december 2020 15:01

    //         //check if already registered
    //         $user = $this->bu->auth_sosmed($nation_code, $fb_id, $google_id, $apple_id, $email, $telp);
    //         if (isset($user->id)) {
    //             $this->bu->trans_rollback();
    //             $this->bu->trans_end();
    //             $this->status = 1003;
    //             $this->message = 'User already registered using Apple ID, please login';
    //             // if ($this->is_log) {
    //             //     $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::daftar -- ForcedClose '.$this->status.' '.$this->message);
    //             // }
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         $checkWhiteList = $this->giwlm->check($nation_code, $di['ip_address']);
    //         if(!isset($checkWhiteList->id)){

    //             $totalUserSameIP = $this->bu->countbyIpAddress($nation_code, $di['ip_address']);

    //             $limit = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C8");
    //             if (!isset($limit->remark)) {
    //               $limit = new stdClass();
    //               $limit->remark = 5;
    //             }

    //             if($totalUserSameIP >= $limit->remark){
    //                 $this->bu->trans_rollback();
    //                 $this->bu->trans_end();
    //                 $this->status = 1749;
    //                 $this->message = "You're not allowed to make a new account";
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }

    //         }

    //         // $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- using apple ID");

    //     } elseif ($reg_from=='facebook') {

    //         //only put correct value
    //         if (strlen($fb_id)>1) {
    //             $di['fb_id'] = $fb_id;
    //         }
    //         if (strlen($email)>4) {
    //             $di['email'] = $email;
    //         }
    //         if (strlen($telp)>4) {
    //             $di['telp'] = $telp;
    //         }

    //         //check if already registered
    //         $user = $this->bu->auth_sosmed($nation_code, $fb_id, $google_id, $apple_id, $email, $telp);
    //         if (isset($user->id)) {
    //             $this->bu->trans_rollback();
    //             $this->bu->trans_end();
    //             $this->status = 1004;
    //             $this->message = 'User already registered, please login';
    //             // if ($this->is_log) {
    //             //     $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::daftar -- ForcedClose '.$this->status.' '.$this->message);
    //             // }
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         $checkWhiteList = $this->giwlm->check($nation_code, $di['ip_address']);
    //         if(!isset($checkWhiteList->id)){

    //             $totalUserSameIP = $this->bu->countbyIpAddress($nation_code, $di['ip_address']);

    //             $limit = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C8");
    //             if (!isset($limit->remark)) {
    //               $limit = new stdClass();
    //               $limit->remark = 5;
    //             }

    //             if($totalUserSameIP >= $limit->remark){
    //                 $this->bu->trans_rollback();
    //                 $this->bu->trans_end();
    //                 $this->status = 1749;
    //                 $this->message = "You're not allowed to make a new account";
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }

    //         }

    //         //by Donny Dennison - 22 September 2021
    //         //auto-generate-password-social-media-signup
    //         $di['password'] = $this->__passGen('5ell0n2o2i');

    //         // $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- using FB ID");

    //     } elseif ($reg_from=='phone') {

    //         $di['email'] = $telp."@sellon.net";

    //         if (strlen($telp)>4) {
    //             $di['telp'] = $telp;
    //         }

    //         $res = $this->bu->checkTelpIgnoreActive($nation_code, $telp);
    //         if (isset($res->id)) {
    //             $this->bu->trans_rollback();
    //             $this->bu->trans_end();
    //             $this->status = 1703;
    //             $this->message = 'Phone number already registered, please try another phone number';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         $verificationPhoneNumber = $this->fvpnm->checkVerificationNumberConfirmed($nation_code, $verifPhone, $telp);
    //         if (!isset($verificationPhoneNumber->id)) {
    //             $this->bu->trans_rollback();
    //             $this->bu->trans_end();
    //             $this->status = 1707;
    //             $this->message = 'Invalid email or password';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         //by Donny Dennison - 22 September 2021
    //         //auto-generate-password-social-media-signup
    //         $di['password'] = $this->__passGen('5ell0n2o2i');

    //         $di['telp_is_verif'] = 1;

    //         // $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- using Phone");

    //     } elseif ($reg_from=='online') {

    //         //by Donny Dennison - 08 june 2022 - 14:56
    //         //phone number not mandatory
    //         // if (strlen($email)<=4 && strlen($telp)<=4) {
    //         if (strlen($email)<=4) {
    //             $this->bu->trans_rollback();
    //             $this->bu->trans_end();
    //             $this->status = 105;
    //             // $this->message = 'Email or Phone number are required';
    //             $this->message = 'Email are required';
    //             // if ($this->is_log) {
    //             //     $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::daftar -- ForcedClose '.$this->status.' '.$this->message);
    //             // }
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }
    //         $use_email=0;
    //         if (strlen($email)>4) {
    //             $di['email'] = $email;
    //             $use_email = 1;
    //         }
    //         $use_phone=0;
    //         if (strlen($telp)>4) {
    //             $di['telp'] = $telp;
    //             $use_phone = 1;
    //         }
    //         if (!empty($use_email) && !empty($use_phone)) {
    //             $res = $this->bu->checkEmailTelpIgnoreActive($nation_code, $email, $telp);
    //             if (isset($res->id)) {
    //                 $this->bu->trans_rollback();
    //                 $this->bu->trans_end();
    //                 $this->status = 1701;
    //                 $this->message = 'Email and phone number already used';
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }
    //         } elseif (!empty($use_email) && empty($use_phone)) {
    //             $res = $this->bu->checkEmailIgnoreActive($nation_code, $email);
    //             if (isset($res->id)) {
    //                 $this->bu->trans_rollback();
    //                 $this->bu->trans_end();
    //                 $this->status = 1702;
    //                 $this->message = 'Email is already registered. Please try again with another email';
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }
    //         } elseif (empty($use_email) && !empty($use_phone)) {
    //             $res = $this->bu->checkTelpIgnoreActive($nation_code, $telp);
    //             if (isset($res->id)) {
    //                 $this->bu->trans_rollback();
    //                 $this->bu->trans_end();
    //                 $this->status = 1703;
    //                 $this->message = 'Phone number already registered, please try another phone number';
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }
    //         }

    //         //password
    //         if (!$is_password_valid) {
    //             $this->bu->trans_rollback();
    //             $this->bu->trans_end();
    //             $this->status = 1704;
    //             $this->message = 'Password not match or password length too short';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         $verificationPhoneNumber = $this->fvpnm->checkVerificationNumberConfirmed($nation_code, $verifPhone, $telp);
    //         if (!isset($verificationPhoneNumber->id)) {
    //             $this->bu->trans_rollback();
    //             $this->bu->trans_end();
    //             $this->status = 1707;
    //             $this->message = 'Invalid email or password';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         $di['telp_is_verif'] = 1;

    //         // if ($this->is_log) {
    //         //     $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- using normal flow");
    //         // }

    //     } else {
    //         $this->bu->trans_rollback();
    //         $this->bu->trans_end();
    //         $this->status = 1705;
    //         $this->message = 'Registration method undefined. Please specify Appled ID or Google ID or Facebook ID or Email Password combination.';
    //         // if ($this->is_log) {
    //         //     $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- status: ".$this->status." - ".$this->message);
    //         // }
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     // $user_id = $this->bu->getLastId($nation_code);

    //     //insert to db
    //     $di['nation_code'] = $nation_code;
    //     // $di['id'] = $user_id;

    //     //START by Donny Dennison - 6 september 2022 17:50
    //     //integrate api blockchain
    //     $endDoWhile = 0;
    //     do{
    //         $di['user_wallet_code'] = $this->GUIDv4();
    //         $checkWalletCode = $this->bu->checkWalletCode($nation_code, $di['user_wallet_code']);
    //         if($checkWalletCode == 0){
    //             $endDoWhile = 1;
    //         }
    //     }while($endDoWhile == 0);

    //     $di['blockchain_createuserwallet_api_called'] = 0;
    //     //END by Donny Dennison - 6 september 2022 17:50
    //     //integrate api blockchain

    //     //START by Donny Dennison - 12 september 2022 14:59
    //     //kode referral
    //     $endDoWhile = 0;
    //     do{
    //         $length = 8;
    //         $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    //         $charactersLength = strlen($characters);
    //         $generatedKodeReferral = '';
    //         for ($i = 0; $i < $length; $i++) {
    //             $generatedKodeReferral .= $characters[rand(0, $charactersLength - 1)];
    //         }
    //         $checkKodeReferral = $this->bu->checkKodeReferral($nation_code, $generatedKodeReferral);
    //         if(!isset($checkKodeReferral->id)){
    //             $endDoWhile = 1;
    //         }
    //     }while($endDoWhile == 0);
    //     $di['kode_referral']= $generatedKodeReferral;

    //     $b_user_id_recruiter = '0';
    //     if (strlen($kode_referral) == 8) {
    //         $checkKodeReferral = $this->bu->checkKodeReferral($nation_code, $kode_referral);
    //         if(isset($checkKodeReferral->id)){
    //             $b_user_id_recruiter = $checkKodeReferral->id;
    //             $di['b_user_id_recruiter'] = $checkKodeReferral->id;
    //             $di['referral_type'] = $referral_type;

    //             //START by Donny Dennison - 20 september 2022 15:04
    //             //mobile registration activity feature
    //             $activityData = $this->gmram->getByReferralType($nation_code, $kode_referral, "registered");
    //             if(isset($activityData->id)){
    //                 $did = array();
    //                 $did['is_registered'] = 1;
    //                 $did['cdate_registered'] = "NOW()";

    //                 $this->gmram->update($nation_code, $activityData->id, $did);
    //                 // $this->bu->trans_commit();

    //                 $di['g_mobile_registration_activity_id'] = $activityData->id;
    //             }
    //             //END by Donny Dennison - 20 september 2022 15:04
    //             //mobile registration activity feature
    //         }
    //     }

    //     // start comment code
    //     // $url = "https://maps.google.com/maps/api/geocode/json?latlng=$latlng&key=AIzaSyCzx5dDTHwJLrgqgM0LXH6D1VGAHC0m4Rw&language=id";

    //     // $ch = curl_init();
    //     // curl_setopt($ch, CURLOPT_URL, $url);
    //     // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     // // curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    //     // // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //     // // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //     // $response = curl_exec($ch);
    //     // curl_close($ch);
    //     // $response_a = json_decode($response);
    //     // // $location = $response_a->results[0]->address_components->types->administrative_area_level_3;
    //     // // $location = $response_a->results[0]->address_components[5]->long_name;
    //     // $response_geocode = $response_a->results[0]->address_components;
    //     // foreach ($response_geocode as $geo) { 
    //     //  $type_geo = $geo->types[0];

    //     //  // if($type_geo == "route") {
    //     //  //  $address_alamat2 = $geo->long_name;
    //     //  // } 
    //     //  if($type_geo == "administrative_area_level_4") {
    //     //      $address_kelurahan = $geo->long_name;
    //     //  }
    //     //  if($type_geo == "administrative_area_level_3") {
    //     //         $address_kecamatan_long = $geo->long_name;

    //     //         if (strpos($address_kecamatan_long, 'Kecamatan') !== false) {
    //     //             $address_kecamatan = str_replace("Kecamatan", "", $address_kecamatan_long);
    //     //         } else {
    //     //             $address_kecamatan = $geo->long_name;
    //     //         }
    //     //  }
    //     //     if($type_geo == "administrative_area_level_2") {
    //     //         $address_kabkota_long = $geo->long_name;

    //     //         if (strpos($address_kabkota_long, 'Kabupaten') !== false) {
    //     //             $address_kabkota = str_replace("Kabupaten", "", $address_kabkota_long);
    //     //         }else if (strpos($address_kabkota_long, 'Kota') !== false) {
    //     //             $address_kabkota = str_replace("Kota", "", $address_kabkota_long);
    //     //         } else {
    //     //             $address_kabkota = $geo->long_name;
    //     //         }
    //     //  } 
    //     //  if($type_geo == "administrative_area_level_1") {
    //     //      $address_provinsi_long = $geo->long_name;

    //     //         if (strpos($address_provinsi_long, 'Daerah Khusus Ibukota') !== false) {
    //     //             $address_provinsi = str_replace("Daerah Khusus Ibukota", "DKI", $address_provinsi_long);
    //     //         } else {
    //     //             $address_provinsi = $geo->long_name;
    //     //         }
    //     //  } 
    //     //  if($type_geo == "country") {
    //     //      $country_origin = $geo->long_name;
    //     //      $country_origin = strtolower($country_origin);
    //     //      $country_short = $geo->short_name;
    //     //  } 
    //     //  if($type_geo == "postal_code") {
    //     //      $address_kodepos = $geo->long_name;
    //     //  } 
    //     // }

    //     // $alamat2 = $response_a->results[0]->formatted_address;
    //     // $new_alamat2 = explode(",", $alamat2);
    //     // // $address_alamat2 = $new_alamat2[1];
    //     // $alamat2 = "";
    //     // foreach($new_alamat2 as $na) {
    //     //     if(stripos($na, "Jl.") !== false) {
    //     //         // echo "true array 0 \n";
    //     //         $alamat2 = $na;
    //     //         break;
    //     //     } else if(stripos($na, "Jl.") !== false) {
    //     //         // echo "true array 1 \n";
    //     //         $alamat2 = $na;
    //     //         break;
    //     //     }   
    //     // }
    //     // $address_alamat2 = $alamat2;
    //     // end comment code

    //     // // start by muhammad sofi 5 January 2023 10:36 | send event to google analytics
    //     // // $url = "https://www.google-analytics.com/mp/collect?firebase_app_id=$this->firebase_app_id&api_secret=$this->firebase_api_secret";

    //     // // $ch = curl_init();
    //     // // curl_setopt($ch, CURLOPT_URL, $url);
    //     // // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     // // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

    //     // // $headers = array();
    //     // // $headers[] = 'Content-Type:  application/json';
    //     // // $headers[] = 'Accept:  application/json';
    //     // // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //     // // $postdata = array(
    //     // //     'events' => 'GoogleMapSignUp'
    //     // // );
    //     // // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));

    //     // // $aa = curl_exec($ch);
    //     // // // echo $url;
    //     // // echo $aa;
    //     // // die();
    //     // // if (curl_errno($ch)) {
    //     // //     echo 'Error: ' . curl_error($ch);
    //     // //     return 0;
    //     // // }
    //     // // curl_close($ch);

    //     // //https://stackoverflow.com/a/72290077/7578520
    //     // // $ip = str_replace('.', '', $_SERVER['REMOTE_ADDR']);
    //     // $data = array(
    //     //     // 'client_id' => $ip,
    //     //     // 'user_id' => '123',
    //     //     'events' => array(
    //     //         'name' => 'GoogleMapSignUp'
    //     //     )
    //     // );
    //     // $datastring = json_encode($data);
    //     // $post_url = "https://www.google-analytics.com/mp/collect?api_secret=$this->firebase_api_secret&measurement_id=G-Z9BL0W0DJC";
    //     // $ch = curl_init($post_url);
    //     // curl_setopt($ch, CURLOPT_POSTFIELDS, $datastring);
    //     // curl_setopt($ch, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
    //     // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     // curl_setopt($ch, CURLOPT_URL, $post_url);
    //     // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    //     // curl_setopt($ch, CURLOPT_POST, TRUE);
    //     // $result = curl_exec($ch);
    //     // curl_close($ch);
    //     // // end by muhammad sofi 5 January 2023 10:36 | send event to google analytics

    //     // //by Donny Dennison - 28 december 2021 20:33
    //     // //add checking address
    //     // if(empty($address_alamat2) || empty($address_provinsi) || empty($address_kabkota) || empty($address_kecamatan) || empty($address_kelurahan) || empty($address_latitude) || empty($address_longitude)){

    //     //     //START by Donny Dennison - 08 june 2022 15:15
    //     //     //change address flow in register
    //     //     // $this->status = 104;
    //     //     // $this->message = 'There is address that empty';
    //     //     // $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //     //     // die();

    //     //     if($coverage_id > 0){
    //     //         $coverageDetail = $this->gmcm->getById($nation_code, $coverage_id);
    //     //         if(isset($coverageDetail->id)){
    //     //             if($coverageDetail->provinsi == 'DKI Jakarta'){
    //     //                 $address_alamat2 = "Gg. H. Zakaria, RW.5";
    //     //                 $address_provinsi = "DKI Jakarta";
    //     //                 $address_kabkota = "Jakarta Pusat";
    //     //                 $address_kecamatan = "Tanah Abang";
    //     //                 $address_kelurahan = "Kebon Melati";
    //     //                 $address_kodepos = "10230";
    //     //                 $address_latitude = "-6.200055499719067";
    //     //                 $address_longitude = "106.8162468531788";
    //     //             }else{
    //     //                 $address_alamat2 = "Gg. H. Zakaria, RW.5";
    //     //                 $address_provinsi = "DKI Jakarta";
    //     //                 $address_kabkota = "Jakarta Pusat";
    //     //                 $address_kecamatan = "Tanah Abang";
    //     //                 $address_kelurahan = "Kebon Melati";
    //     //                 $address_kodepos = "10230";
    //     //                 $address_latitude = "-6.200055499719067";
    //     //                 $address_longitude = "106.8162468531788";
    //     //             }
    //     //         }
    //     //     }else{
    //     //         $address_alamat2 = "Gg. H. Zakaria, RW.5";
    //     //         $address_provinsi = "DKI Jakarta";
    //     //         $address_kabkota = "Jakarta Pusat";
    //     //         $address_kecamatan = "Tanah Abang";
    //     //         $address_kelurahan = "Kebon Melati";
    //     //         $address_kodepos = "10230";
    //     //         $address_latitude = "-6.200055499719067";
    //     //         $address_longitude = "106.8162468531788";
    //     //     }
    //     //     //END by Donny Dennison - 08 june 2022 15:15
    //     // }

    //     // by muhammad sofi 15 March 2023 | add checking to set default address
    //     if($country_origin == "indonesia") {
    //         if(empty($address_provinsi) || empty($address_kabkota) || empty($address_kecamatan) || empty($address_kelurahan) || empty($address_latitude) || empty($address_longitude)){ 
    //             $address_alamat2 = "Pulu Sakit";
    //             $address_provinsi = "DKI Jakarta";
    //             $address_kabkota = "Kepulauan Seribu";
    //             $address_kecamatan = "Kepulauan Seribu Selatan";
    //             $address_kelurahan = "Pulau Untung Jawa";
    //             $address_kodepos = "14510";
    //             $address_latitude = "-6.036292404954418";
    //             $address_longitude = "106.74658602378337";
    //         }
    //     } else {
    //         $address_alamat2 = "Pulu Sakit";
    //         $address_provinsi = "DKI Jakarta";
    //         $address_kabkota = "Kepulauan Seribu";
    //         $address_kecamatan = "Kepulauan Seribu Selatan";
    //         $address_kelurahan = "Pulau Untung Jawa";
    //         $address_kodepos = "14510";
    //         $address_latitude = "-6.036292404954418";
    //         $address_longitude = "106.74658602378337";
    //     }

    //     $di['register_place_alamat2'] = $address_alamat2;
    //     $di['register_place_kelurahan'] = $address_kelurahan;
    //     $di['register_place_kecamatan'] = $address_kecamatan;
    //     $di['register_place_kabkota'] = $address_kabkota;
    //     $di['register_place_provinsi'] = $address_provinsi;
    //     $di['register_place_kodepos'] = $address_kodepos;
    //     $di['latitude'] = $address_latitude;
    //     $di['longitude'] = $address_longitude;
    //     //END by Donny Dennison - 12 september 2022 14:59
    //     //kode referral

    //     $this->lib("conumtext");
    //     $token = $nation_code.$this->conumtext->genRand($type="str", $min=18, $max=28);
    //     $token_plain = hash('sha256',$token);
    //     $token = hash('sha256',$token_plain);
    //     $di['api_mobile_token'] = $token;
    //     $api_mobile_edate = date('Y-m-d H:i:00',strtotime("+$this->expired_token day"));
    //     $di['api_mobile_edate'] = $api_mobile_edate;

    //     if($reg_from == "online"){
    //         $min = 25;
    //         $token_reg = $this->conumtext->genRand($type="str", $min, $max=30);
    //         $di['api_reg_token'] = $token_reg;
    //     }

    //     $endDoWhile = 0;
    //     do{
    //       $user_id = $this->GUIDv4();
    //       $checkId = $this->bu->checkId($nation_code, $user_id);
    //       if($checkId == 0){
    //           $endDoWhile = 1;
    //       }
    //     }while($endDoWhile == 0);
    //     $di['id'] = $user_id;

    //     $image = $this->__uploadUserImage($user_id);
    //     if (strlen($image)>4) {
    //         $di['image'] = str_replace("//", "/", $image);
    //     }

    //     $res = $this->bu->register($di); //return user id;
    //     if ($res) {
    //         // insert to signup android / ios
    //         if($device == "android") {
    //             $this->gdtrm->updateTotalData(DATE("Y-m-d"), "signup_android", "+", "1");
    //         } else if($device == "ios") {
    //             $this->gdtrm->updateTotalData(DATE("Y-m-d"), "signup_ios", "+", "1");
    //         } else {}

    //         $this->gdtrm->updateTotalData(DATE("Y-m-d"), "signup", "+", "1");
    //         //commit table
    //         // $this->bu->trans_commit();
    //         $register_success = 1;

    //         //get current country configuration
    //         $negara = $this->__getNegara($nation_code);

    //         $penerima_nama = trim($address_penerima_nama);

    //         if ($this->__mbLen($penerima_nama)<=0) {
    //             //rollback table
    //             $this->bu->trans_rollback();

    //             //release table
    //             $this->bu->trans_end();

    //             $this->status = 1737;
    //             $this->message = 'Name cannot be empty';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }
    //         if ($this->__mbLen($penerima_nama)>=64) {
    //             //rollback table
    //             $this->bu->trans_rollback();

    //             //release table
    //             $this->bu->trans_end();

    //             $this->status = 1736;
    //             $this->message = 'Name too long';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         //sanitize null
    //         $penerima_nama = trim(mb_ereg_replace('null', '', $penerima_nama));

    //         //check $penerima_nama
    //         $penerima_telp = trim($address_penerima_telp);
    //         if (empty($penerima_telp)) {
    //             $penerima_telp = '';
    //         }

    //         //by Donny Dennison - 08 june 2022 - 14:56
    //         //phone number not mandatory
    //         // if (strlen($penerima_telp)<=0) {
    //             // //rollback table
    //             // $this->bu->trans_rollback();

    //             // //release table
    //             // $this->bu->trans_end();

    //         //     $this->status = 1763;
    //         //     $this->message = 'Phone number cannot be empty';
    //         //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         //     die();
    //         // }

    //         if (strlen($penerima_telp)>=32) {
    //             //rollback table
    //             $this->bu->trans_rollback();

    //             //release table
    //             $this->bu->trans_end();

    //             $this->status = 1723;
    //             $this->message = 'Phone number too long';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         //check $catatan
    //         $catatan = $address_catatan;
    //         if (empty($catatan)) {
    //             $catatan = '';
    //         }

    //         // if ($this->__mbLen($catatan)>=128) {
    //             // //rollback table
    //             // $this->bu->trans_rollback();

    //             // //release table
    //             // $this->bu->trans_end();

    //         //     $this->status = 1724;
    //         //     $this->message = 'Address notes too long';
    //         //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         //     die();
    //         // }
    //         // by Muhammad Sofi - 3 November 2021 10:00
    //         // remark code

    //         $alamat2 = trim($address_alamat2);
    //         if (empty($alamat2)) {
    //             $alamat2 = '';
    //         }
    //         // if ($this->__mbLen($alamat2)>=128) {
    //             // //rollback table
    //             // $this->bu->trans_rollback();

    //             // //release table
    //             // $this->bu->trans_end();

    //         //     $this->status = 1765;
    //         //     $this->message = 'Secondary address too long';
    //         //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         //     die();
    //         // }

    //         $latitude = $address_latitude;
    //         $longitude = $address_longitude;
    //         if (strlen($latitude)<=3 || strlen($longitude)<=3) {
                
    //             //by Donny Dennison - 24 juli 2020 18:23
    //             //change default latitude and longitude
    //             // $latitude = $negara->latitude;
    //             // $longitude = $negara->longitude;
    //             $latitude = 0;
    //             $longitude = 0;
    //         }

    //         //populating input for location
    //         $provinsi = $address_provinsi;
    //         $kabkota = $address_kabkota;
    //         $kecamatan = $address_kecamatan;
    //         $kelurahan = $address_kelurahan;
    //         $kodepos = $address_kodepos;

    //         //validating
    //         if (empty($provinsi)) {
    //             $provinsi = '';
    //         }
    //         if (empty($kabkota)) {
    //             $kabkota = '';
    //         }
    //         if (empty($kecamatan)) {
    //             $kecamatan = '';
    //         }
    //         if (empty($kelurahan)) {
    //             $kelurahan = '';
    //         }
    //         if (empty($kodepos)) {
    //             $kodepos = '99999';
    //         }

    //         //check location properties provinsi
    //         if (!empty($negara->is_provinsi)) {
    //             if (strlen($provinsi)<=0) {
    //                 //rollback table
    //                 $this->bu->trans_rollback();

    //                 //release table
    //                 $this->bu->trans_end();

    //                 $this->status = 1766;
    //                 $this->message = 'Province / State are required for this country';
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }
    //         }

    //         //check location properties kabkota
    //         if (!empty($negara->is_kabkota)) {
    //             if (strlen($kabkota)<=0) {
    //                 //rollback table
    //                 $this->bu->trans_rollback();

    //                 //release table
    //                 $this->bu->trans_end();

    //                 $this->status = 1767;
    //                 $this->message = 'City are required for this country';
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }
    //         }

    //         //check location properties kecamatan
    //         if (!empty($negara->is_kecamatan)) {
    //             if (strlen($kecamatan)<=0) {
    //                 //rollback table
    //                 $this->bu->trans_rollback();

    //                 //release table
    //                 $this->bu->trans_end();

    //                 $this->status = 1724;
    //                 $this->message = 'District are required for this country';
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }
    //         }

    //         //check location properties kelurahan
    //         if (!empty($negara->is_kelurahan)) {
    //             if (strlen($kelurahan)<=0) {
    //                 //rollback table
    //                 $this->bu->trans_rollback();

    //                 //release table
    //                 $this->bu->trans_end();

    //                 $this->status = 1769;
    //                 $this->message = 'Sub District are required for this country';
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }
    //         }

    //         //check location properties kelurahan
    //         if (!empty($negara->is_kodepos)) {
    //             if (strlen($kodepos)<=0) {
    //                 //rollback table
    //                 $this->bu->trans_rollback();

    //                 //release table
    //                 $this->bu->trans_end();

    //                 $this->status = 1770;
    //                 $this->message = 'Zipcode / Postal Code are required for this country';
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }
    //         }

    //         // //check kelurahan, kecamatan, kabkota, provinsi, and kodepos in db or not
    //         // $checkInDBOrNot = $this->bual->checkInDBOrNot($nation_code, $kelurahan, $kecamatan, $kabkota, $provinsi, $kodepos);
    //         // if (!isset($checkInDBOrNot->id)){
    //                 // //rollback table
    //                 // $this->bu->trans_rollback();

    //         // //release table
    //         // $this->bu->trans_end();

    //         //     $this->status = 1774;
    //         //     $this->message = 'This address is invalid, please find other address';
    //         //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         //     die();
    //         // }

    //         //get last id
    //         $last_id = $this->bua->getLastId($nation_code, $user_id);

    //         //collect input
    //         $di = array();
    //         $di['nation_code'] = $nation_code;
    //         $di['id'] = $last_id;
    //         $di['b_user_id'] = $user_id;

    //         //by Donny Dennison - 22 september 2021
    //         //auto-generate-address-title
    //         // $di['judul'] = $judul;
    //         $di['judul'] = 'Your Place '.$last_id;

    //         $di['penerima_nama'] = $penerima_nama;
    //         $di['penerima_telp'] = $penerima_telp;
    //         $di['alamat2'] = $alamat2;
    //         $di['kelurahan'] = $kelurahan;
    //         $di['kecamatan'] = $kecamatan;
    //         $di['kabkota'] = $kabkota;
    //         $di['provinsi'] = $provinsi;
    //         $di['negara'] = $negara->iso2;
    //         $di['kodepos'] = $kodepos;
    //         $di['longitude'] = $longitude;
    //         $di['latitude'] = $latitude;

    //         //by Donny Dennison - 13 july 2021 15:49
    //         //set-address-type-to-default
    //         // $di['address_status'] = $address_status;
    //         $di['address_status'] = 'A2';

    //         $di['catatan'] = $catatan;
    //         $di['is_default'] = 1;
    //         $di['is_active'] = 1;

    //         //insert into database
    //         $res = $this->bua->set($di);
    //         if ($res) {
    //             // $this->bu->trans_commit();
    //         } else {
    //             $this->bu->trans_rollback();

    //             //release table
    //             $this->bu->trans_end();

    //             $this->status = 1771;
    //             $this->message = 'Failed insert user address';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         //START by Donny Dennison - 5 january 2021 - 11:49
    //         //change address default
    //         // //release default
    //         // $du = array("is_default"=>0);
    //         // $this->bua->updateByUserId($nation_code, $pelanggan->id, $du);
    //         // $this->bu->trans_commit();
    //         // //update default
    //         // $du = array("is_default"=>1);
    //         // $this->bua->update($nation_code, $pelanggan->id, $last_id, $du);
    //         // $this->bu->trans_commit();

    //         // $user_alamat_default = $this->bua->getByUserIdDefault($nation_code, $user_id);

    //         // if($last_id == 1 || !isset($user_alamat_default->alamat2)){

    //         //     $du = array("is_default"=>1);
    //         //     $this->bua->update($nation_code, $user_id, $last_id, $du);
    //         //     // $this->bu->trans_commit();

    //         // }
    //         //END by Donny Dennison - 5 january 2021 - 11:49

    //         //check kelurahan, kecamatan, kabkota, provinsi, and kodepos in db or not
    //         $checkInDBOrNot = $this->bual->checkInDBOrNot($nation_code, $kelurahan, $kecamatan, $kabkota, $provinsi, $kodepos);
    //         if (!isset($checkInDBOrNot->id)){
    //             //get last id
    //             $last_id = $this->bual->getLastId($nation_code);

    //             //collect input
    //             $di = array();
    //             $di['nation_code'] = $nation_code;
    //             $di['id'] = $last_id;
    //             $di['kelurahan'] = $kelurahan;
    //             $di['kecamatan'] = $kecamatan;
    //             $di['kabkota'] = $kabkota;
    //             $di['provinsi'] = $provinsi;
    //             $di['kodepos'] = $kodepos;

    //             //insert into database
    //             $this->bual->set($di);
    //             // $this->bu->trans_commit();
    //         }

    //         // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", "All", "All", $provinsi);
    //         // if(!isset($getStatusHighlight->status)){
    //         //     //get last id
    //         //     $highlight_status_id = $this->gglhsm->getLastId($nation_code);
    //         //     $di = array();
    //         //     $di['nation_code'] = $nation_code;
    //         //     $di['id'] = $highlight_status_id;
    //         //     $di['b_user_alamat_location_kelurahan'] = 'All';
    //         //     $di['b_user_alamat_location_kecamatan'] = 'All';
    //         //     $di['b_user_alamat_location_kabkota'] = 'All';
    //         //     $di['b_user_alamat_location_provinsi'] = $provinsi;
    //         //     $this->gglhsm->set($di);
    //         //     // $this->bu->trans_commit();
    //         // }

    //         // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", "All", $kabkota, $provinsi);
    //         // if(!isset($getStatusHighlight->status)){
    //         //     //get last id
    //         //     $highlight_status_id = $this->gglhsm->getLastId($nation_code);
    //         //     $di = array();
    //         //     $di['nation_code'] = $nation_code;
    //         //     $di['id'] = $highlight_status_id;
    //         //     $di['b_user_alamat_location_kelurahan'] = 'All';
    //         //     $di['b_user_alamat_location_kecamatan'] = 'All';
    //         //     $di['b_user_alamat_location_kabkota'] = $kabkota;
    //         //     $di['b_user_alamat_location_provinsi'] = $provinsi;
    //         //     $this->gglhsm->set($di);
    //         //     // $this->bu->trans_commit();
    //         // }

    //         // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", $kecamatan, $kabkota, $provinsi);
    //         // if(!isset($getStatusHighlight->status)){
    //         //     //get last id
    //         //     $highlight_status_id = $this->gglhsm->getLastId($nation_code);
    //         //     $di = array();
    //         //     $di['nation_code'] = $nation_code;
    //         //     $di['id'] = $highlight_status_id;
    //         //     $di['b_user_alamat_location_kelurahan'] = 'All';
    //         //     $di['b_user_alamat_location_kecamatan'] = $kecamatan;
    //         //     $di['b_user_alamat_location_kabkota'] = $kabkota;
    //         //     $di['b_user_alamat_location_provinsi'] = $provinsi;
    //         //     $this->gglhsm->set($di);
    //         //     // $this->bu->trans_commit();
    //         // }

    //         // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, $kelurahan, $kecamatan, $kabkota, $provinsi);
    //         // if(!isset($getStatusHighlight->status)){
    //         //     //get last id
    //         //     $highlight_status_id = $this->gglhsm->getLastId($nation_code);
    //         //     $di = array();
    //         //     $di['nation_code'] = $nation_code;
    //         //     $di['id'] = $highlight_status_id;
    //         //     $di['b_user_alamat_location_kelurahan'] = $kelurahan;
    //         //     $di['b_user_alamat_location_kecamatan'] = $kecamatan;
    //         //     $di['b_user_alamat_location_kabkota'] = $kabkota;
    //         //     $di['b_user_alamat_location_provinsi'] = $provinsi;
    //         //     $this->gglhsm->set($di);
    //         //     // $this->bu->trans_commit();
    //         // }

    //     } else {
    //         //rollback table
    //         $this->bu->trans_rollback();

    //         //release table
    //         $this->bu->trans_end();

    //         $this->status = 1706;
    //         $this->message = 'Failed save user to database, please try again';
    //         // if ($this->is_log) {
    //         //     $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- FAILED");
    //         // }
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     usleep(500000);
    //     //START by Donny Dennison - 10 december 2020 15:01
    //     //new registration system for apple id
    //     // $token = $this->__activateMobileToken($nation_code, $user_id);
    //     $user = $this->bu->getById($nation_code, $user_id);
    //     if ($reg_from == 'apple' && strpos($email, '@privaterelay.appleid.com') !== false) {
    //         $this->status = 200;
    //     }else{
    //     //END by Donny Dennison - 10 december 2020 15:01
    //     //new registration system for apple id

    //         //after success
    //         if ($register_success && !empty($user_id)) {
    //             $this->status = 200;
    //             // $this->message = 'registration successful, please check your inbox or spam before login';
    //             $this->message = 'Success';
    //             if ($this->email_send && strlen($email)>4) {
    //                 if ($confirmeds==0) {
    //                     // $link = $this->__activateGenerateLink($nation_code, $user_id, $user->api_reg_token);
    //                     $link = base_url("account/activate/index/$token_reg");

    //                     $nama = $user->fnama;
    //                     $replacer = array();
    //                     $replacer['site_name'] = $this->app_name;
    //                     $replacer['fnama'] = $nama;
    //                     $replacer['activation_link'] = $link;
    //                     $this->seme_email->flush();
    //                     $this->seme_email->replyto($this->site_name, $this->site_replyto);
    //                     $this->seme_email->from($this->site_email, $this->site_name);
    //                     $this->seme_email->subject('Registration Successful');
    //                     $this->seme_email->to($email, $nama);
    //                     $this->seme_email->template('account_register');
    //                     $this->seme_email->replacer($replacer);
    //                     $this->seme_email->send();
    //                 }
    //             }
    //         } else {
    //             $this->status = 1706;
    //             $this->message = 'Failed save user to database, please try again';
    //             // if ($this->is_log) {
    //             //     $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- status: ".$this->status." - ".$this->message);
    //             // }
    //         }

    //     //START by Donny Dennison - 10 december 2020 15:01
    //     //new registration system for apple id
    //     }
    //     //END by Donny Dennison - 10 december 2020 15:01

    //     //only manipulating
    //     if ($this->status == 200 && isset($user->id)) {
    //         if($reg_from == 'phone' || $reg_from == 'online'){
    //             $di = array();
    //             $di['b_user_id'] = $user->id;
    //             $this->fvpnm->update($verificationPhoneNumber->id, $di);
    //         }

    //         // $image = $this->__uploadUserImage($user->id);

    //         // $dux = array();
    //         // $dux['api_mobile_edate'] = date('Y-m-d H:i:00',strtotime("+$this->expired_token day"));
    //         // if (strlen($image)>4) {
    //         //     $dux['image'] = str_replace("//", "/", $image);
    //         // }
    //         // if(is_array($dux) && count($dux)) $this->bu->update($nation_code, $user->id, $dux);

    //         //add base url to image
    //         if (isset($user->image)) {
    //             $user->image = $this->cdn_url($image);
    //         }

    //         //by Donny Dennison - 08-09-2021 11:35
    //         //revamp-profile
    //         if (isset($user->image_banner)) {
    //             // by Muhammad Sofi - 28 October 2021 11:00
    //             // if user img & banner not exist or empty, change to default image
    //             // $user->image_banner = $this->cdn_url($user->image_banner);
    //             if(file_exists(SENEROOT.$user->image_banner)){
    //                 $user->image_banner = $this->cdn_url($user->image_banner);
    //             } else {
    //                 $user->image_banner = $this->cdn_url('media/user/default.png');
    //             }
    //         }

    //         //remove unecessary properties
    //         unset($user->api_mobile_token);
    //         unset($user->api_web_token);
    //         unset($user->api_reg_token);
    //         unset($user->password);
    //         $user->apisess = $token_plain;
    //         // $user->apisess_expired = $dux['api_mobile_edate'];
    //         $user->apisess_expired = $api_mobile_edate;
    //         $user->api_mobile_edate = $user->apisess_expired;

    //         //put to response
    //         $data['apisess'] = $token;
    //         $data['apisess_expired'] = $user->apisess_expired;
    //         $data['pelanggan'] = $user;
    //         // if ($this->is_log) {
    //         //     $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- Image Uploaded");
    //         // }
    //         //update user setting
    //         // $this->__callUserSettings($nation_code, $token);
    //         $settingController = new setting();
    //         $settingController->notificationcustom($nation_code, $apikey, $token);

    //         $getPointPlacement = $this->glptm->getByUserId($nation_code, $user->id);
    //         if(!isset($getPointPlacement->b_user_id)){
    //             //create point
    //             $di = array();
    //             $di['nation_code'] = $nation_code;
    //             $di['id'] = 1;
    //             $di['b_user_id'] = $user->id;
    //             $di['total_post'] = 0;
    //             $di['total_point'] = 0;
    //             $this->glptm->set($di);
    //         }
            // unset($getPointPlacement);

    //         //START by Donny Dennison - 12 september 2022 14:59
    //         //kode referral
    //         if($b_user_id_recruiter != '0'){
    //         //     $recruiterData = $this->bu->getById($nation_code, $b_user_id_recruiter);

    //         //     //RECRUITED
    //         //     //get point
    //         //     $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EY");
    //         //     if (!isset($pointGet->remark)) {
    //         //       $pointGet = new stdClass();
    //         //       $pointGet->remark = 10;
    //         //     }

    //         //     $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $user->id);

    //         //     $di = array();
    //         //     $di['nation_code'] = $nation_code;
    //         //     $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
    //         //     $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
    //         //     $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
    //         //     $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
    //         //     $di['b_user_id'] = $user->id;
    //         //     $di['point'] = $pointGet->remark;
    //         //     $di['custom_id'] = $b_user_id_recruiter;
    //         //     $di['custom_type'] = 'referral';
    //         //     $di['custom_type_sub'] = 'link';
    //         //     $di['custom_text'] = $user->fnama.' use '.$di['custom_type_sub'].' '.$di['custom_type'].' '.$recruiterData->fnama.' and get '.$di['point'].' point(s)';
          // $endDoWhile = 0;
          // do{
          //   $leaderBoardHistoryId = $this->GUIDv4();
          //   $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
          //   if($checkId == 0){
          //     $endDoWhile = 1;
          //   }
          // }while($endDoWhile == 0);
          // $di['id'] = $leaderBoardHistoryId;
    //         //     $this->glphm->set($di);
    //         //     // $this->glrm->updateTotal($nation_code, $user->id, 'total_point', '+', $di['point']);

    //         //     //RECRUITER
    //         //     if($recruiterData->is_active == 1){
    //         //         //get point
    //         //         $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EZ");
    //         //         if (!isset($pointGet->remark)) {
    //         //           $pointGet = new stdClass();
    //         //           $pointGet->remark = 10;
    //         //         }

    //         //         $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $b_user_id_recruiter);

    //         //         $di = array();
    //         //         $di['nation_code'] = $nation_code;
    //         //         $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
    //         //         $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
    //         //         $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
    //         //         $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
    //         //         $di['b_user_id'] = $b_user_id_recruiter;
    //         //         $di['point'] = $pointGet->remark;
    //         //         $di['custom_id'] = $user->id;
    //         //         $di['custom_type'] = 'referral';
    //         //         $di['custom_type_sub'] = 'link';
    //         //         $di['custom_text'] = $user->fnama.' use '.$di['custom_type_sub'].' '.$di['custom_type'].' '.$recruiterData->fnama.' and get '.$di['point'].' point(s)';
          // $endDoWhile = 0;
          // do{
          //   $leaderBoardHistoryId = $this->GUIDv4();
          //   $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
          //   if($checkId == 0){
          //     $endDoWhile = 1;
          //   }
          // }while($endDoWhile == 0);
          // $di['id'] = $leaderBoardHistoryId;
    //         //         $this->glphm->set($di);
    //         //         // $this->glrm->updateTotal($nation_code, $b_user_id_recruiter, 'total_point', '+', $di['point']);
    //         //     }

    //             // $this->bu->updateTotal($nation_code, $b_user_id_recruiter, "total_recruited", "+", "1");
    //             // $this->bu->updateDate($nation_code, $b_user_id_recruiter, "bdate", date("Y-m-d H:i:s"));
    //             $this->bu->updateTotalAndBDate($nation_code, $b_user_id_recruiter, "total_recruited", "+", "1", "bdate", date("Y-m-d H:i:s"));
    //         }
    //         //END by Donny Dennison - 12 september 2022 14:59
    //         //kode referral

    //         //START by Donny Dennison - 07 october 2022 15:49
    //         //integrate api blockchain
    //         // if($b_user_id_recruiter != 0){
    //         //     $recruiterData = $this->bu->getById($nation_code, $b_user_id_recruiter);
    //         //     if($recruiterData->is_get_point == 1){
    //         //         $response = json_decode($this->__callBlockChainCreateWallet($user->user_wallet_code, $recruiterData->user_wallet_code));
    //         //     }else{
    //         //         $response = json_decode($this->__callBlockChainCreateWallet($user->user_wallet_code));   
    //         //     }
    //         // }else{
    //         //     $response = json_decode($this->__callBlockChainCreateWallet($user->user_wallet_code));
    //         // }

    //         // if(isset($response->responseCode)){
    //         //     if($b_user_id_recruiter == 0){
    //         //         if($response->responseCode == 0){
    //         //             $du = array("blockchain_createuserwallet_api_called"=>1);
    //         //         }else{
    //         //             $du = array("blockchain_createuserwallet_api_called"=>0);
    //         //         }
    //         //     }else{
    //         //         if($response->responseCode == 0 && $recruiterData->is_get_point == 1){
    //         //             $du = array("blockchain_createuserwallet_api_called"=>1);
    //         //         }else if($response->responseCode == 0 && $recruiterData->is_get_point == 0){
    //         //             $du = array("blockchain_createuserwallet_api_called"=>3);
    //         //         }else{
    //         //             $du = array("blockchain_createuserwallet_api_called"=>0);
    //         //         }
    //         //     }
    //         // }else{
    //         //     $du = array("blockchain_createuserwallet_api_called"=>0);
    //         // }

    //         // $this->bu->update($nation_code, $user->id, $du);
    //         // unset($recruiterData, $response);
    //         //END by Donny Dennison - 07 october 2022 15:49
    //         //integrate api blockchain

    //         //START by Donny Dennison - 13 december 2022 14:31
    //         //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device
    //         $di = array();
    //         $di["nation_code"] = $nation_code;
    //         $endDoWhile = 0;
    //         do{
    //           $di["id"] = $this->GUIDv4();
    //           $checkId = $this->gdlm->checkId($nation_code, $di["id"]);
    //           if($checkId == 0){
    //               $endDoWhile = 1;
    //           }
    //         }while($endDoWhile == 0);
    //         $di["b_user_id"] = $user->id;
    //         $di["device_id"] = $device_id;
    //         $di["type"] = "signup";
    //         $di["cdate"] = "NOW()";
    //         $this->gdlm->set($di);
    //         //END by Donny Dennison - 13 december 2022 14:31
    //         //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device
    //     }

    //     // if ($this->is_log) {
    //     //     $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- status: ".$this->status." - ".$this->message);
    //     // }

    //     //by Donny Dennison - 25 august 2020 20:15
    //     //fix user setting not save to db
    //     if ($register_success && !empty($user_id)) {
    //         $this->status = 200;
    //         // $this->message = 'registration successful, please check your inbox or spam before login';
    //         $this->message = 'Success';
    //     } else {
    //         $this->status = 1706;
    //         $this->message = 'Failed save user to database, please try again';
    //     }

    //     $this->bu->trans_commit();
    //     //release table
    //     $this->bu->trans_end();

    //     //output as json
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    // }

    // public function daftarfrommobilev3()
    // {
    //     //initial
    //     $token = '';
    //     $user_id = 0;
    //     $register_success = 0;
    //     $user = new stdClass();
    //     $dt = $this->__init();

    //     // $this->seme_log->write("api_mobile", "API_Mobile/pelanggan/daftar:: --POST: ".json_encode($_POST));

    //     //default response
    //     $data = array();
    //     $data['apisess'] = '';
    //     $data['apisess_expired'] = '';
    //     $data['pelanggan'] = new stdClass();

    //     $cf296563 = $this->input->get('cf296563');
    //     if($cf296563 != "zF2CSXpnQgu5NtNF7T3f"){
    //         $this->status = 1750;
    //         $this->message = 'Please check your data again';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();

    //     }

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if (empty($c)) {
    //         $this->status = 400;
    //         $this->message = 'Missing or invalid API key';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     //flags
    //     $reg_from = 'online';
    //     $is_telp_valid = 0;
    //     $is_email_valid = 0;
    //     $is_password_valid = 0;
    //     $is_telp = 0;

    //     //populate input
    //     $email = strtolower(trim($this->input->post("email")));
    //     $telp = $this->input->post("telp");
    //     $fb_id = $this->input->post("fb_id");
    //     $google_id = $this->input->post("google_id");
    //     $apple_id = $this->input->post("apple_id");
    //     $fnama = trim($this->input->post("fnama"));
    //     $password = $this->input->post("password");
    //     $password_confirm = $this->input->post("password_confirm");
    //     $fcm_token = $this->input->post("fcm_token");
    //     $device = strtolower(trim($this->input->post("device")));
        
    //     //by Donny Dennison - 17 february 2022 17:51
    //     //change message language in response/return
    //     $language_id = trim($this->input->post("language_id"));

    //     //START by Donny Dennison - 08 june 2022 15:15
    //     //change address flow in register
    //     $coverage_id = trim($this->input->post("coverage_id"));
    //     $is_changed_address = trim($this->input->post("is_changed_address"));
    //     if($is_changed_address != 1){
    //         $is_changed_address = 0;
    //     }
    //     //END by Donny Dennison - 08 june 2022 15:15

    //     $verifPhone = trim($this->input->post("verifPhone"));

    //     if (strlen($email)>4) {
    //         $is_email_valid = 1;
    //     } else {
    //         $email = '';
    //     }
    //     if ($is_email_valid) {
    //         $cem = $this->bu->checkEmailIgnoreActive($nation_code, $email);
    //         if (isset($cem->id)) {
    //             $this->status = 1702;
    //             // $this->message = 'Email already registered, please try another email or login with current email';
    //             $this->message = 'Email is already registered. Please try again with another email';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }
    //     }

    //     if (strlen($telp)>4) {
    //         $is_telp_valid = 1;
    //     } else {
    //         $telp = '';
    //     }

    //     if($is_email_valid != 1 && $is_telp_valid != 1){
    //         $this->status = 1732;
    //         $this->message = 'Please input the correct email';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     $confirmeds=0;
    //     if (strlen($fb_id)>1) {
    //         $reg_from = 'facebook';
    //         $confirmeds=1;
    //     } elseif (strlen($google_id)>1) {
    //         $reg_from = 'google';
    //         $confirmeds=1;
    //     } elseif (strlen($apple_id)>1) {
    //         $reg_from = 'apple';
    //         $confirmeds=1;
    //     } elseif (empty($is_email_valid) && !empty($is_telp_valid)) {
    //         $reg_from = 'phone';
    //         $confirmeds=1;
    //     } else {
    //         $reg_from = 'online';
    //         $confirmeds=0;
    //     }

    //     //check fcm_token valid in firebase or not
    //     //https://stackoverflow.com/a/45697880
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    //     curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");

    //     $headers = array();
    //     $headers[] = 'Content-Type:  application/json';
    //     $headers[] = 'Authorization:  key='.$this->fcm_server_token;
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //     $postdata = array(
    //       'registration_ids' => array($fcm_token)
    //     );
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));

    //     $result = curl_exec($ch);
    //     if (curl_errno($ch)) {
    //       return 0;
    //       //echo 'Error:' . curl_error($ch);
    //     }
    //     curl_close($ch);

    //     $result = json_decode($result);
    //     if(isset($result->success)){
    //         if($result->success != 1){
    //             $this->status = 1750;
    //             $this->message = 'Please check your data again';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();

    //         }

    //     }else{
    //         $this->status = 1750;
    //         $this->message = 'Please check your data again';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     // if($this->input->post('call_from') == "1ns!d3r"){
    //     //     $ip_address = $this->input->post('ip_address');
    //     // }else{
    //     //     $ip_address = $_SERVER['HTTP_X_REAL_IP'];
    //     // }

    //     //lock table
    //     $this->bu->trans_start();

    //     // $countUserByIpAddress = $this->bu->checkByIpAddressDate($nation_code, $ip_address, $reg_from);
    //     // $countUserByFcmToken = $this->bu->checkByFcmTokenDate($nation_code, $fcm_token, $reg_from);
    //     // if($countUserByIpAddress > 0 || $countUserByFcmToken > 0){
    //     //     $this->bu->trans_rollback();
    //     //     $this->bu->trans_end();
    //     //     $this->status = 1750;
    //     //     $this->message = 'Please check your data again';
    //     //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //     //     die();
    //     // }
    //     // // unset($countUser, $ip_address);
    //     // unset($countUser);

    //     // $totalRegisterBefore = $this->bu->getForRegisterBefore($nation_code, $reg_from);
    //     // if ($totalRegisterBefore > 0) {
    //     //   $this->cpm->trans_rollback();
    //     //   $this->cpm->trans_end();
    //     //   $this->status = 1751;
    //     //   $this->message = 'Please try again';
    //     //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "produk");
    //     //   die();
    //     // }

    //     $blackList = $this->gblm->check($nation_code, "fcm_token", $fcm_token);
    //     if(isset($blackList->id)){
    //         $this->bu->trans_rollback();
    //         $this->bu->trans_end();
    //         // $this->status = 1707;
    //         // $this->message = 'Invalid email or password';
    //         $this->status = 1728;
    //         $this->message = "Sorry, you're banned to log in, please contact WA(+65 8856 2024)";
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     // $kode_referral = strtolower(trim($this->input->post("kode_referral")));
    //     // if (strlen($kode_referral) == 8) {
    //     //     $checkKodeReferral = $this->bu->checkKodeReferral($nation_code, $kode_referral);
    //     //     if(isset($checkKodeReferral->id)){
    //     //         $countRecomendeeByFcmToken = $this->bu->checkByFcmTokenRecommender($nation_code, $fcm_token, $checkKodeReferral->id);
    //     //         $countRecomendeeByIpAddress = $this->bu->checkByIpAddressRecommender($nation_code, $ip_address, $checkKodeReferral->id);
    //     //         if(($countRecomendeeByFcmToken == 4 || $countRecomendeeByIpAddress == 4) && $checkKodeReferral->total_recruited >= 4 && $checkKodeReferral->total_recruited <= 14){

    //     //             $du = array();
    //     //             $du['is_permanent_inactive'] = 0;
    //     //             $du['permanent_inactive_by'] = 'admin';
    //     //             $du['permanent_inactive_date'] = date('Y-m-d H:i:s');
    //     //             $du['api_mobile_token'] = "";
    //     //             $du['fcm_token'] = "";
    //     //             $du['is_active'] = 0;
    //     //             $du['is_confirmed'] = 0;
    //     //             $du['is_online'] = 0;
    //     //             $du['telp_is_verif'] = 0;
    //     //             $du['inactive_text'] = "spammer account(automatic)";
    //     //             $this->bu->update($nation_code, $checkKodeReferral->id, $du);

    //     //             // call BlackListUserWallet from blockchain API, stop rewarding and withdraw suspended user
    //     //             $postdata = array();
    //     //             $postdata[] = array(
    //     //             'userWalletCode' => $this->__encryptdecrypt($checkKodeReferral->user_wallet_code, "encrypt"),
    //     //             'countryIsoCode' => $this->blockchain_api_country,
    //     //             );

    //     //             $postdata = array(
    //     //                 "userWalletList" => $postdata
    //     //             );

    //     //             // $responseWalletApi = 0;
    //     //             $response = json_decode($this->__callBlockChainBlacklist($postdata));
    //     //             // if(isset($response->responseCode)){
    //     //             //     if($response->responseCode == 0){
    //     //             //         $responseWalletApi = 1;
    //     //             //     }
    //     //             // }
    //     //             // unset($response);

    //     //             $this->bu->trans_commit();
    //     //             $this->bu->trans_end();
    //     //             $this->status = 401;
    //     //             $this->message = 'Missing or invalid API session';
    //     //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //     //             die();
    //     //         }
    //     //     }
    //     // }
    //     // unset($kode_referral, $checkKodeReferral, $countRecomendee);
    //     // unset($ip_address);

    //     //by Donny Dennison - 16 june 2022 09:52
    //     //add new parameter "country_origin" in post at api "pelanggan/daftar"
    //     $country_origin = strtolower(trim($this->input->post("country_origin")));
    //     // if(empty($country_origin)){
    //     //     $country_origin = "indonesia";
    //     // }

    //     // force to indonesia
    //     $country_origin = "indonesia";
    //     if($country_origin != "indonesia"){
    //         $is_changed_address = 1;
    //     }

    //     //START by Donny Dennison - 12 september 2022 14:59
    //     //kode referral
    //     $kode_referral = strtolower(trim($this->input->post("kode_referral")));

    //     $referral_type = strtolower(trim($this->input->post("referral_type")));
    //     if($referral_type == "communitydetail"){
    //         $referral_type = "Community Detail";
    //     }else if($referral_type == "productdetail"){
    //         $referral_type = "Product Detail";
    //     }else if($referral_type == "shop"){
    //         $referral_type = "Shop";
    //     }else{
    //         $referral_type = "My Share";
    //     }
    //     //END by Donny Dennison - 12 september 2022 14:59
    //     //kode referral

    //     // by Muhammmad Sofi 10 January 2023 11:41 | get is_emulator
    //     $emulator = $this->input->post("is_emulator");
    //     if($emulator != 1){
    //         $emulator = 0;
    //     }

    //     //by Donny Dennison - 20 september 2022 15:04
    //     //mobile registration activity feature
    //     $device_id = trim($this->input->post("device_id"));

    //     //START by Donny Dennison - 13 december 2022 14:31
    //     //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device
    //     $totalUsedDeviceId = $this->gdlm->countAll($nation_code, "", $device_id);

    //     //get max used in 1 device
    //     $maxUsed = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C6");
    //     if (!isset($maxUsed->remark)) {
    //         $maxUsed = new stdClass();
    //         $maxUsed->remark = 5;
    //     }

    //     if($totalUsedDeviceId >= $maxUsed->remark){
    //         $this->bu->trans_rollback();
    //         $this->bu->trans_end();
    //         $this->status = 1726;
    //         $this->message = "You're not allowed to use many accounts";
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();

    //     }
    //     //END by Donny Dennison - 13 december 2022 14:31
    //     //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device

    //     //validation
    //     if ($this->__mbLen($fnama)==0) {
    //         $fnama = "";
    //     }
    //     //$fnama = mb_ereg_replace('[a-zA-Z0-9\s,.!?]', '', $fnama);

    //     if (strlen($device)<3) {
    //         $this->status = 1755;
    //         $this->message = 'Unknown device type';
    //         // if ($this->is_log) {
    //         //     $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::daftar -- WARN '.$this->status.': '.$this->message);
    //         // }
    //     }
    //     // $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::daftar -- INFO device: '.$device);

    //     //by Donny Dennison - 08 june 2022 - 14:56
    //     //phone number not mandatory
    //     // //by Donny Dennison - 27 august 2020 - 14:52
    //     // //check telephone
    //     // $checkPhoneNumber = $this->bu->checkTelp($nation_code, $telp);
    //     // if (isset($checkPhoneNumber->id)) {
    //     //     $this->status = 1703;
    //     //     $this->message = 'Phone number already registered, please try another phone number';
    //     //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //     //     die();
    //     // }

    //     if ($this->__mbLen($fnama)>=64) {
    //         $this->bu->trans_rollback();
    //         $this->bu->trans_end();
    //         $this->status = 1736;
    //         $this->message = 'Name too long';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     // if (strlen($fcm_token)<=50) {
    //     //     $fcm_token = "";
    //     //     $this->status = 1756;
    //     //     $this->message = 'Invalid FCM Token';
    //     //     // $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::daftar -- WARN '.$this->status.': '.$this->message);
    //     // }

    //     //by Donny Dennison - 08 june 2022 15:15
    //     //change address flow in register
    //     $address_penerima_nama = $this->input->post('address_penerima_nama');
    //     $address_penerima_telp = $this->input->post('address_penerima_telp');
    //     $address_alamat2 = $this->input->post('address_alamat2');
    //     $address_provinsi = $this->input->post('address_provinsi');
    //     $address_kabkota = $this->input->post('address_kabkota');
    //     $address_kecamatan = $this->input->post('address_kecamatan');
    //     $address_kelurahan = $this->input->post('address_kelurahan');
    //     $address_kodepos = $this->input->post('address_kodepos');
    //     $address_latitude = $this->input->post('address_latitude');
    //     $address_longitude = $this->input->post('address_longitude');
    //     $address_catatan = $this->input->post('address_catatan');
    //     // $latlng = $address_latitude.','.$address_longitude;

    //     // $details_url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=". $latlng. "&key=AIzaSyCzx5dDTHwJLrgqgM0LXH6D1VGAHC0m4Rw&language=id";

    //     // $ch = curl_init();
    //     // curl_setopt($ch, CURLOPT_URL, $details_url);
    //     // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     // $geoloc = json_decode(curl_exec($ch), true);
     
    //     // $step1 = $geoloc['results'];
    //     // $get1 = $step1[0]['address_components'];
    //     // $get11 = $get1[1];
    //     // $negara = $get1[9];
    //     // print_r($negara['short_name']);
    //     // // $this->debug($step1[0]['address_components']);
    //     // // die();
    //     // $step2 = $step1['geometry'];
    //     // $coords = $step2['location'];
     
    //     // print $coords['lat'];
    //     // print $coords['lng'];
    //     // // $test = $details_url->types[0]->country;
    //     // // $this->debug($details_url);
    //     // // die();



    //     // //by Donny Dennison - 28 december 2021 20:33
    //     // //add checking address
    //     // if(empty($address_alamat2) || empty($address_provinsi) || empty($address_kabkota) || empty($address_kecamatan) || empty($address_kelurahan) || empty($address_latitude) || empty($address_longitude)){
    //     //     //START by Donny Dennison - 08 june 2022 15:15
    //     //     //change address flow in register
    //     //     // $this->status = 104;
    //     //     // $this->message = 'There is address that empty';
    //     //     // $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //     //     // die();

    //     //     if($coverage_id > 0){
    //     //         $coverageDetail = $this->gmcm->getById($nation_code, $coverage_id);
    //     //         if(isset($coverageDetail->id)){
    //     //             if($coverageDetail->provinsi == 'DKI Jakarta'){
    //     //                 $address_alamat2 = "Gg. H. Zakaria, RW.5";
    //     //                 $address_provinsi = "DKI Jakarta";
    //     //                 $address_kabkota = "Jakarta Pusat";
    //     //                 $address_kecamatan = "Tanah Abang";
    //     //                 $address_kelurahan = "Kebon Melati";
    //     //                 $address_kodepos = "10230";
    //     //                 $address_latitude = "-6.200055499719067";
    //     //                 $address_longitude = "106.8162468531788";
    //     //             }else{
    //     //                 $address_alamat2 = "Gg. H. Zakaria, RW.5";
    //     //                 $address_provinsi = "DKI Jakarta";
    //     //                 $address_kabkota = "Jakarta Pusat";
    //     //                 $address_kecamatan = "Tanah Abang";
    //     //                 $address_kelurahan = "Kebon Melati";
    //     //                 $address_kodepos = "10230";
    //     //                 $address_latitude = "-6.200055499719067";
    //     //                 $address_longitude = "106.8162468531788";
    //     //             }
    //     //         }
    //     //     }else{
    //     //         $address_alamat2 = "Gg. H. Zakaria, RW.5";
    //     //         $address_provinsi = "DKI Jakarta";
    //     //         $address_kabkota = "Jakarta Pusat";
    //     //         $address_kecamatan = "Tanah Abang";
    //     //         $address_kelurahan = "Kebon Melati";
    //     //         $address_kodepos = "10230";
    //     //         $address_latitude = "-6.200055499719067";
    //     //         $address_longitude = "106.8162468531788";
    //     //     }
    //     //     //END by Donny Dennison - 08 june 2022 15:15
    //     // }

    //     // //check kelurahan, kecamatan, kabkota, provinsi, and kodepos in db or not
    //     // $checkInDBOrNot = $this->bual->checkInDBOrNot($nation_code, $this->input->post('address_kelurahan'), $this->input->post('address_kecamatan'), $this->input->post('address_kabkota'), $this->input->post('address_provinsi'), $this->input->post('address_kodepos'));
    //     // if (!isset($checkInDBOrNot->id)){
    //     //     $this->status = 104;
    //     //     $this->message = 'This address is invalid, please find other address';
    //     //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //     //     die();
    //     // }

    //     // $this->seme_log->write("api_mobile", 'API_Mobile/Pelanggan::daftar -- activate :'.$confirmeds);
    //     if (mb_strlen($password)>3) {
    //         $is_password_valid = 1;
    //     }

    //     //debug post
    //     //if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Pelanggan:: --POST: ".json_encode($_POST));

    //     //populate insert
    //     $di = array();
    //     $di['email'] = 'null';
    //     $di['fnama'] = $fnama;
    //     $di['lnama'] = "";
    //     $di['telp'] = 'null';
    //     $di['fb_id'] = 'null';
    //     $di['apple_id'] = 'null';
    //     $di['google_id'] = 'null';
    //     $di['fcm_token'] = $fcm_token;
    //     $di['cdate'] = 'NOW()';
    //     $di['image'] = 'media/user/default.png';
    //     // $di['latitude'] = 1.290270;
    //     // $di['longitude'] = 103.851959;
    //     $di['intro_teks'] = "";
    //     $di['api_reg_token'] = "";
    //     $di['api_web_token'] = "";
    //     $di['api_mobile_token'] = "";
    //     $di['api_social_id'] = "";
    //     $di['is_confirmed']= $confirmeds;
    //     $di['password'] = $this->__passGen($password);
    //     $di['device'] = $device;
    //     $di['register_from'] = $reg_from;
    //     $di['is_emulator'] = $emulator;

    //     //by Donny Dennison - 17 february 2022 17:51
    //     //change message language in response/return
    //     if($language_id){
    //         $di["language_id"] = $language_id;
    //     }else{
    //         if($nation_code == 62){
    //             $di["language_id"] = 2;
    //         }else if($nation_code == 82){
    //             $di["language_id"] = 3;
    //         }else if($nation_code == 66){
    //             $di["language_id"] = 4;
    //         }else {
    //             $di["language_id"] = 1;
    //         }
    //     }

    //     //by Donny Dennison - 08 june 2022 15:15
    //     //change address flow in register
    //     $di['is_changed_address'] = $is_changed_address;

    //     //by Donny Dennison - 16 june 2022 09:52
    //     //add new parameter "country_origin" in post at api "pelanggan/daftar"
    //     $di['country_origin'] = $country_origin;

    //     //by Donny Dennison - 20 september 2022 15:04
    //     //mobile registration activity feature
    //     if(strlen($device_id) > 3){
    //         $di['device_id'] = $device_id;
    //     }

    //     if($this->input->post('call_from') == "1ns!d3r"){
    //         $di['ip_address'] = $this->input->post('ip_address');
    //     }else{
    //         $di['ip_address'] = $_SERVER['HTTP_X_REAL_IP'];
    //     }

    //     //registration flow
    //     if ($reg_from == 'google') {
    //         //only put correct value
    //         if (strlen($google_id)>1) {
    //             $di['google_id'] = $google_id;
    //         }
    //         if (strlen($email)>4) {
    //             $di['email'] = $email;
    //         }
    //         if (strlen($telp)>4) {
    //             $di['telp'] = $telp;
    //         }

    //         //check if already registered
    //         $user = $this->bu->auth_sosmed($nation_code, $fb_id, $google_id, $apple_id, $email, $telp);
    //         if (isset($user->id)) {
    //             $this->bu->trans_rollback();
    //             $this->bu->trans_end();
    //             $this->status = 1002;
    //             $this->message = 'User already registered using Google ID, please login';
    //             // if ($this->is_log) {
    //             //     $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::daftar -- ForcedClose '.$this->status.' '.$this->message);
    //             // }
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         $checkWhiteList = $this->giwlm->check($nation_code, $di['ip_address']);
    //         if(!isset($checkWhiteList->id)){
    //             $totalUserSameIP = $this->bu->countbyIpAddress($nation_code, $di['ip_address']);
    //             $limit = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C8");
    //             if (!isset($limit->remark)) {
    //               $limit = new stdClass();
    //               $limit->remark = 5;
    //             }

    //             if($totalUserSameIP >= $limit->remark){
    //                 $this->bu->trans_rollback();
    //                 $this->bu->trans_end();
    //                 $this->status = 1749;
    //                 $this->message = "You're not allowed to make a new account";
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }
    //         }

    //         //by Donny Dennison - 22 September 2021
    //         //auto-generate-password-social-media-signup
    //         $di['password'] = $this->__passGen('5ell0n2o2i');

    //         // $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- using Google ID");

    //     } elseif ($reg_from == 'apple') {
    //         //only put correct value
    //         if (strlen($apple_id)>1) {
    //             $di['apple_id'] = $apple_id;
    //         }
    //         if (strlen($email)>4) {
    //             $di['email'] = $email;
    //         }
    //         if (strlen($telp)>4) {
    //             $di['telp'] = $telp;
    //         }

    //         //START by Donny Dennison - 10 december 2020 15:01
    //         //new registration system for apple id
    //         $di['is_reset_password'] = 0;
    //         $di['password'] = $this->__passGen('5ell0n2o2i');

    //         // do {
    //         //     $di['telp'] = rand(10000000,19999999);
    //         //     //check already in db or havent
    //         //     $checkPhoneNumber = $this->bu->checkTelp($nation_code, $di['telp']);
    //         // } while (isset($checkPhoneNumber->id));
    //         //END by Donny Dennison - 10 december 2020 15:01

    //         //check if already registered
    //         $user = $this->bu->auth_sosmed($nation_code, $fb_id, $google_id, $apple_id, $email, $telp);
    //         if (isset($user->id)) {
    //             $this->bu->trans_rollback();
    //             $this->bu->trans_end();
    //             $this->status = 1003;
    //             $this->message = 'User already registered using Apple ID, please login';
    //             // if ($this->is_log) {
    //             //     $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::daftar -- ForcedClose '.$this->status.' '.$this->message);
    //             // }
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         $checkWhiteList = $this->giwlm->check($nation_code, $di['ip_address']);
    //         if(!isset($checkWhiteList->id)){

    //             $totalUserSameIP = $this->bu->countbyIpAddress($nation_code, $di['ip_address']);

    //             $limit = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C8");
    //             if (!isset($limit->remark)) {
    //               $limit = new stdClass();
    //               $limit->remark = 5;
    //             }

    //             if($totalUserSameIP >= $limit->remark){
    //                 $this->bu->trans_rollback();
    //                 $this->bu->trans_end();
    //                 $this->status = 1749;
    //                 $this->message = "You're not allowed to make a new account";
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }

    //         }

    //         // $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- using apple ID");

    //     } elseif ($reg_from=='facebook') {

    //         //only put correct value
    //         if (strlen($fb_id)>1) {
    //             $di['fb_id'] = $fb_id;
    //         }
    //         if (strlen($email)>4) {
    //             $di['email'] = $email;
    //         }
    //         if (strlen($telp)>4) {
    //             $di['telp'] = $telp;
    //         }

    //         //check if already registered
    //         $user = $this->bu->auth_sosmed($nation_code, $fb_id, $google_id, $apple_id, $email, $telp);
    //         if (isset($user->id)) {
    //             $this->bu->trans_rollback();
    //             $this->bu->trans_end();
    //             $this->status = 1004;
    //             $this->message = 'User already registered, please login';
    //             // if ($this->is_log) {
    //             //     $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::daftar -- ForcedClose '.$this->status.' '.$this->message);
    //             // }
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         $checkWhiteList = $this->giwlm->check($nation_code, $di['ip_address']);
    //         if(!isset($checkWhiteList->id)){

    //             $totalUserSameIP = $this->bu->countbyIpAddress($nation_code, $di['ip_address']);

    //             $limit = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C8");
    //             if (!isset($limit->remark)) {
    //               $limit = new stdClass();
    //               $limit->remark = 5;
    //             }

    //             if($totalUserSameIP >= $limit->remark){
    //                 $this->bu->trans_rollback();
    //                 $this->bu->trans_end();
    //                 $this->status = 1749;
    //                 $this->message = "You're not allowed to make a new account";
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }

    //         }

    //         //by Donny Dennison - 22 September 2021
    //         //auto-generate-password-social-media-signup
    //         $di['password'] = $this->__passGen('5ell0n2o2i');

    //         // $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- using FB ID");

    //     } elseif ($reg_from=='phone') {

    //         $di['email'] = $telp."@sellon.net";

    //         if (strlen($telp)>4) {
    //             $di['telp'] = $telp;
    //         }

    //         $res = $this->bu->checkTelpIgnoreActive($nation_code, $telp);
    //         if (isset($res->id)) {
    //             $this->bu->trans_rollback();
    //             $this->bu->trans_end();
    //             $this->status = 1703;
    //             $this->message = 'Phone number already registered, please try another phone number';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         $verificationPhoneNumber = $this->fvpnm->checkVerificationNumberConfirmed($nation_code, $verifPhone, $telp);
    //         if (!isset($verificationPhoneNumber->id)) {
    //             $this->bu->trans_rollback();
    //             $this->bu->trans_end();
    //             $this->status = 1707;
    //             $this->message = 'Invalid email or password';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         //by Donny Dennison - 22 September 2021
    //         //auto-generate-password-social-media-signup
    //         $di['password'] = $this->__passGen('5ell0n2o2i');

    //         $di['telp_is_verif'] = 1;

    //         // $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- using Phone");

    //     } elseif ($reg_from=='online') {

    //         //by Donny Dennison - 08 june 2022 - 14:56
    //         //phone number not mandatory
    //         // if (strlen($email)<=4 && strlen($telp)<=4) {
    //         if (strlen($email)<=4) {
    //             $this->bu->trans_rollback();
    //             $this->bu->trans_end();
    //             $this->status = 105;
    //             // $this->message = 'Email or Phone number are required';
    //             $this->message = 'Email are required';
    //             // if ($this->is_log) {
    //             //     $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::daftar -- ForcedClose '.$this->status.' '.$this->message);
    //             // }
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }
    //         $use_email=0;
    //         if (strlen($email)>4) {
    //             $di['email'] = $email;
    //             $use_email = 1;
    //         }
    //         $use_phone=0;
    //         if (strlen($telp)>4) {
    //             $di['telp'] = $telp;
    //             $use_phone = 1;
    //         }
    //         if (!empty($use_email) && !empty($use_phone)) {
    //             $res = $this->bu->checkEmailTelpIgnoreActive($nation_code, $email, $telp);
    //             if (isset($res->id)) {
    //                 $this->bu->trans_rollback();
    //                 $this->bu->trans_end();
    //                 $this->status = 1701;
    //                 $this->message = 'Email and phone number already used';
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }
    //         } elseif (!empty($use_email) && empty($use_phone)) {
    //             $res = $this->bu->checkEmailIgnoreActive($nation_code, $email);
    //             if (isset($res->id)) {
    //                 $this->bu->trans_rollback();
    //                 $this->bu->trans_end();
    //                 $this->status = 1702;
    //                 $this->message = 'Email is already registered. Please try again with another email';
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }
    //         } elseif (empty($use_email) && !empty($use_phone)) {
    //             $res = $this->bu->checkTelpIgnoreActive($nation_code, $telp);
    //             if (isset($res->id)) {
    //                 $this->bu->trans_rollback();
    //                 $this->bu->trans_end();
    //                 $this->status = 1703;
    //                 $this->message = 'Phone number already registered, please try another phone number';
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }
    //         }

    //         //password
    //         if (!$is_password_valid) {
    //             $this->bu->trans_rollback();
    //             $this->bu->trans_end();
    //             $this->status = 1704;
    //             $this->message = 'Password not match or password length too short';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         $verificationPhoneNumber = $this->fvpnm->checkVerificationNumberConfirmed($nation_code, $verifPhone, $telp);
    //         if (!isset($verificationPhoneNumber->id)) {
    //             $this->bu->trans_rollback();
    //             $this->bu->trans_end();
    //             $this->status = 1707;
    //             $this->message = 'Invalid email or password';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         $di['telp_is_verif'] = 1;

    //         // if ($this->is_log) {
    //         //     $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- using normal flow");
    //         // }

    //     } else {
    //         $this->bu->trans_rollback();
    //         $this->bu->trans_end();
    //         $this->status = 1705;
    //         $this->message = 'Registration method undefined. Please specify Appled ID or Google ID or Facebook ID or Email Password combination.';
    //         // if ($this->is_log) {
    //         //     $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- status: ".$this->status." - ".$this->message);
    //         // }
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     // $user_id = $this->bu->getLastId($nation_code);

    //     //insert to db
    //     $di['nation_code'] = $nation_code;
    //     // $di['id'] = $user_id;

    //     //START by Donny Dennison - 6 september 2022 17:50
    //     //integrate api blockchain
    //     $endDoWhile = 0;
    //     do{
    //         $di['user_wallet_code'] = $this->GUIDv4();
    //         $checkWalletCode = $this->bu->checkWalletCode($nation_code, $di['user_wallet_code']);
    //         if($checkWalletCode == 0){
    //             $endDoWhile = 1;
    //         }
    //     }while($endDoWhile == 0);

    //     $di['blockchain_createuserwallet_api_called'] = 0;
    //     //END by Donny Dennison - 6 september 2022 17:50
    //     //integrate api blockchain

    //     //START by Donny Dennison - 12 september 2022 14:59
    //     //kode referral
    //     $endDoWhile = 0;
    //     do{
    //         $length = 8;
    //         $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    //         $charactersLength = strlen($characters);
    //         $generatedKodeReferral = '';
    //         for ($i = 0; $i < $length; $i++) {
    //             $generatedKodeReferral .= $characters[rand(0, $charactersLength - 1)];
    //         }
    //         $checkKodeReferral = $this->bu->checkKodeReferral($nation_code, $generatedKodeReferral);
    //         if(!isset($checkKodeReferral->id)){
    //             $endDoWhile = 1;
    //         }
    //     }while($endDoWhile == 0);

    //     $di['kode_referral']= $generatedKodeReferral;

    //     $b_user_id_recruiter = '0';
    //     if (strlen($kode_referral) == 8) {
    //         $checkKodeReferral = $this->bu->checkKodeReferral($nation_code, $kode_referral);
    //         if(isset($checkKodeReferral->id)){
    //             $b_user_id_recruiter = $checkKodeReferral->id;
    //             $di['b_user_id_recruiter'] = $checkKodeReferral->id;
    //             $di['referral_type'] = $referral_type;

    //             //START by Donny Dennison - 20 september 2022 15:04
    //             //mobile registration activity feature
    //             $activityData = $this->gmram->getByReferralType($nation_code, $kode_referral, "registered");
    //             if(isset($activityData->id)){
    //                 $did = array();
    //                 $did['is_registered'] = 1;
    //                 $did['cdate_registered'] = "NOW()";

    //                 $this->gmram->update($nation_code, $activityData->id, $did);
    //                 // $this->bu->trans_commit();

    //                 $di['g_mobile_registration_activity_id'] = $activityData->id;
    //             }
    //             //END by Donny Dennison - 20 september 2022 15:04
    //             //mobile registration activity feature
    //         }
    //     }

    //     // start comment code
    //     // $url = "https://maps.google.com/maps/api/geocode/json?latlng=$latlng&key=AIzaSyCzx5dDTHwJLrgqgM0LXH6D1VGAHC0m4Rw&language=id";

    //     // $ch = curl_init();
    //     // curl_setopt($ch, CURLOPT_URL, $url);
    //     // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     // // curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    //     // // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //     // // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //     // $response = curl_exec($ch);
    //     // curl_close($ch);
    //     // $response_a = json_decode($response);
    //     // // $location = $response_a->results[0]->address_components->types->administrative_area_level_3;
    //     // // $location = $response_a->results[0]->address_components[5]->long_name;
    //     // $response_geocode = $response_a->results[0]->address_components;
    //     // foreach ($response_geocode as $geo) { 
    //     //  $type_geo = $geo->types[0];

    //     //  // if($type_geo == "route") {
    //     //  //  $address_alamat2 = $geo->long_name;
    //     //  // } 
    //     //  if($type_geo == "administrative_area_level_4") {
    //     //      $address_kelurahan = $geo->long_name;
    //     //  }
    //     //  if($type_geo == "administrative_area_level_3") {
    //     //         $address_kecamatan_long = $geo->long_name;

    //     //         if (strpos($address_kecamatan_long, 'Kecamatan') !== false) {
    //     //             $address_kecamatan = str_replace("Kecamatan", "", $address_kecamatan_long);
    //     //         } else {
    //     //             $address_kecamatan = $geo->long_name;
    //     //         }
    //     //  }
    //     //     if($type_geo == "administrative_area_level_2") {
    //     //         $address_kabkota_long = $geo->long_name;

    //     //         if (strpos($address_kabkota_long, 'Kabupaten') !== false) {
    //     //             $address_kabkota = str_replace("Kabupaten", "", $address_kabkota_long);
    //     //         }else if (strpos($address_kabkota_long, 'Kota') !== false) {
    //     //             $address_kabkota = str_replace("Kota", "", $address_kabkota_long);
    //     //         } else {
    //     //             $address_kabkota = $geo->long_name;
    //     //         }
    //     //  } 
    //     //  if($type_geo == "administrative_area_level_1") {
    //     //      $address_provinsi_long = $geo->long_name;

    //     //         if (strpos($address_provinsi_long, 'Daerah Khusus Ibukota') !== false) {
    //     //             $address_provinsi = str_replace("Daerah Khusus Ibukota", "DKI", $address_provinsi_long);
    //     //         } else {
    //     //             $address_provinsi = $geo->long_name;
    //     //         }
    //     //  } 
    //     //  if($type_geo == "country") {
    //     //      $country_origin = $geo->long_name;
    //     //      $country_origin = strtolower($country_origin);
    //     //      $country_short = $geo->short_name;
    //     //  } 
    //     //  if($type_geo == "postal_code") {
    //     //      $address_kodepos = $geo->long_name;
    //     //  }
    //     // }

    //     // $alamat2 = $response_a->results[0]->formatted_address;
    //     // $new_alamat2 = explode(",", $alamat2);
    //     // // $address_alamat2 = $new_alamat2[1];
    //     // $alamat2 = "";
    //     // foreach($new_alamat2 as $na) {
    //     //     if(stripos($na, "Jl.") !== false) {
    //     //         // echo "true array 0 \n";
    //     //         $alamat2 = $na;
    //     //         break;
    //     //     } else if(stripos($na, "Jl.") !== false) {
    //     //         // echo "true array 1 \n";
    //     //         $alamat2 = $na;
    //     //         break;
    //     //     }   
    //     // }
    //     // $address_alamat2 = $alamat2;
    //     // end comment code

    //     // // start by muhammad sofi 5 January 2023 10:36 | send event to google analytics
    //     // // $url = "https://www.google-analytics.com/mp/collect?firebase_app_id=$this->firebase_app_id&api_secret=$this->firebase_api_secret";

    //     // // $ch = curl_init();
    //     // // curl_setopt($ch, CURLOPT_URL, $url);
    //     // // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     // // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

    //     // // $headers = array();
    //     // // $headers[] = 'Content-Type:  application/json';
    //     // // $headers[] = 'Accept:  application/json';
    //     // // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //     // // $postdata = array(
    //     // //     'events' => 'GoogleMapSignUp'
    //     // // );
    //     // // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));

    //     // // $aa = curl_exec($ch);
    //     // // // echo $url;
    //     // // echo $aa;
    //     // // die();
    //     // // if (curl_errno($ch)) {
    //     // //     echo 'Error: ' . curl_error($ch);
    //     // //     return 0;
    //     // // }
    //     // // curl_close($ch);

    //     // //https://stackoverflow.com/a/72290077/7578520
    //     // // $ip = str_replace('.', '', $_SERVER['REMOTE_ADDR']);
    //     // $data = array(
    //     //     // 'client_id' => $ip,
    //     //     // 'user_id' => '123',
    //     //     'events' => array(
    //     //         'name' => 'GoogleMapSignUp'
    //     //     )
    //     // );
    //     // $datastring = json_encode($data);
    //     // $post_url = "https://www.google-analytics.com/mp/collect?api_secret=$this->firebase_api_secret&measurement_id=G-Z9BL0W0DJC";
    //     // $ch = curl_init($post_url);
    //     // curl_setopt($ch, CURLOPT_POSTFIELDS, $datastring);
    //     // curl_setopt($ch, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
    //     // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     // curl_setopt($ch, CURLOPT_URL, $post_url);
    //     // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    //     // curl_setopt($ch, CURLOPT_POST, TRUE);
    //     // $result = curl_exec($ch);
    //     // curl_close($ch);
    //     // // end by muhammad sofi 5 January 2023 10:36 | send event to google analytics

    //     // //by Donny Dennison - 28 december 2021 20:33
    //     // //add checking address
    //     // if(empty($address_alamat2) || empty($address_provinsi) || empty($address_kabkota) || empty($address_kecamatan) || empty($address_kelurahan) || empty($address_latitude) || empty($address_longitude)){
    //     //     //START by Donny Dennison - 08 june 2022 15:15
    //     //     //change address flow in register
    //     //     // $this->status = 104;
    //     //     // $this->message = 'There is address that empty';
    //     //     // $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //     //     // die();

    //     //     if($coverage_id > 0){
    //     //         $coverageDetail = $this->gmcm->getById($nation_code, $coverage_id);
    //     //         if(isset($coverageDetail->id)){
    //     //             if($coverageDetail->provinsi == 'DKI Jakarta'){
    //     //                 $address_alamat2 = "Gg. H. Zakaria, RW.5";
    //     //                 $address_provinsi = "DKI Jakarta";
    //     //                 $address_kabkota = "Jakarta Pusat";
    //     //                 $address_kecamatan = "Tanah Abang";
    //     //                 $address_kelurahan = "Kebon Melati";
    //     //                 $address_kodepos = "10230";
    //     //                 $address_latitude = "-6.200055499719067";
    //     //                 $address_longitude = "106.8162468531788";
    //     //             }else{
    //     //                 $address_alamat2 = "Gg. H. Zakaria, RW.5";
    //     //                 $address_provinsi = "DKI Jakarta";
    //     //                 $address_kabkota = "Jakarta Pusat";
    //     //                 $address_kecamatan = "Tanah Abang";
    //     //                 $address_kelurahan = "Kebon Melati";
    //     //                 $address_kodepos = "10230";
    //     //                 $address_latitude = "-6.200055499719067";
    //     //                 $address_longitude = "106.8162468531788";
    //     //             }
    //     //         }
    //     //     }else{
    //     //         $address_alamat2 = "Gg. H. Zakaria, RW.5";
    //     //         $address_provinsi = "DKI Jakarta";
    //     //         $address_kabkota = "Jakarta Pusat";
    //     //         $address_kecamatan = "Tanah Abang";
    //     //         $address_kelurahan = "Kebon Melati";
    //     //         $address_kodepos = "10230";
    //     //         $address_latitude = "-6.200055499719067";
    //     //         $address_longitude = "106.8162468531788";
    //     //     }
    //     //     //END by Donny Dennison - 08 june 2022 15:15
    //     // }

    //     // by muhammad sofi 15 March 2023 | add checking to set default address
    //     if($country_origin == "indonesia") {
    //         if(empty($address_provinsi) || empty($address_kabkota) || empty($address_kecamatan) || empty($address_kelurahan) || empty($address_latitude) || empty($address_longitude)){ 
    //             $address_alamat2 = "Pulu Sakit";
    //             $address_provinsi = "DKI Jakarta";
    //             $address_kabkota = "Kepulauan Seribu";
    //             $address_kecamatan = "Kepulauan Seribu Selatan";
    //             $address_kelurahan = "Pulau Untung Jawa";
    //             $address_kodepos = "14510";
    //             $address_latitude = "-6.036292404954418";
    //             $address_longitude = "106.74658602378337";
    //         }
    //     } else {
    //         $address_alamat2 = "Pulu Sakit";
    //         $address_provinsi = "DKI Jakarta";
    //         $address_kabkota = "Kepulauan Seribu";
    //         $address_kecamatan = "Kepulauan Seribu Selatan";
    //         $address_kelurahan = "Pulau Untung Jawa";
    //         $address_kodepos = "14510";
    //         $address_latitude = "-6.036292404954418";
    //         $address_longitude = "106.74658602378337";
    //     }

    //     $di['register_place_alamat2'] = $address_alamat2;
    //     $di['register_place_kelurahan'] = $address_kelurahan;
    //     $di['register_place_kecamatan'] = $address_kecamatan;
    //     $di['register_place_kabkota'] = $address_kabkota;
    //     $di['register_place_provinsi'] = $address_provinsi;
    //     $di['register_place_kodepos'] = $address_kodepos;
    //     $di['latitude'] = $address_latitude;
    //     $di['longitude'] = $address_longitude;
    //     //END by Donny Dennison - 12 september 2022 14:59
    //     //kode referral

    //     $this->lib("conumtext");
    //     $token = $nation_code.$this->conumtext->genRand($type="str", $min=18, $max=28);
    //     $token_plain = hash('sha256',$token);
    //     $token = hash('sha256',$token_plain);
    //     $di['api_mobile_token'] = $token;
    //     $api_mobile_edate = date('Y-m-d H:i:00',strtotime("+$this->expired_token day"));
    //     $di['api_mobile_edate'] = $api_mobile_edate;

    //     if($reg_from == "online"){
    //         $min = 25;
    //         $token_reg = $this->conumtext->genRand($type="str", $min, $max=30);
    //         $di['api_reg_token'] = $token_reg;
    //     }

    //     $endDoWhile = 0;
    //     do{
    //       $user_id = $this->GUIDv4();
    //       $checkId = $this->bu->checkId($nation_code, $user_id);
    //       if($checkId == 0){
    //           $endDoWhile = 1;
    //       }
    //     }while($endDoWhile == 0);
    //     $di['id'] = $user_id;

    //     $image = $this->__uploadUserImage($user_id);
    //     if (strlen($image)>4) {
    //         $di['image'] = str_replace("//", "/", $image);
    //     }

    //     $res = $this->bu->register($di); //return user id;
    //     if ($res) {
    //         // insert to signup android / ios
    //         if($device == "android") {
    //             $this->gdtrm->updateTotalData(DATE("Y-m-d"), "signup_android", "+", "1");
    //         } else if($device == "ios") {
    //             $this->gdtrm->updateTotalData(DATE("Y-m-d"), "signup_ios", "+", "1");
    //         } else {}

    //         $this->gdtrm->updateTotalData(DATE("Y-m-d"), "signup", "+", "1");
    //         //commit table
    //         // $this->bu->trans_commit();
    //         $register_success = 1;

    //         //get current country configuration
    //         $negara = $this->__getNegara($nation_code);

    //         $penerima_nama = trim($address_penerima_nama);

    //         if ($this->__mbLen($penerima_nama)<=0) {
    //             //rollback table
    //             $this->bu->trans_rollback();

    //             //release table
    //             $this->bu->trans_end();

    //             $this->status = 1737;
    //             $this->message = 'Name cannot be empty';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }
    //         if ($this->__mbLen($penerima_nama)>=64) {
    //             //rollback table
    //             $this->bu->trans_rollback();

    //             //release table
    //             $this->bu->trans_end();

    //             $this->status = 1736;
    //             $this->message = 'Name too long';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         //sanitize null
    //         $penerima_nama = trim(mb_ereg_replace('null', '', $penerima_nama));

    //         //check $penerima_nama
    //         $penerima_telp = trim($address_penerima_telp);
    //         if (empty($penerima_telp)) {
    //             $penerima_telp = '';
    //         }

    //         //by Donny Dennison - 08 june 2022 - 14:56
    //         //phone number not mandatory
    //         // if (strlen($penerima_telp)<=0) {
    //             // //rollback table
    //             // $this->bu->trans_rollback();

    //             // //release table
    //             // $this->bu->trans_end();

    //         //     $this->status = 1763;
    //         //     $this->message = 'Phone number cannot be empty';
    //         //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         //     die();
    //         // }

    //         if (strlen($penerima_telp)>=32) {
    //             //rollback table
    //             $this->bu->trans_rollback();

    //             //release table
    //             $this->bu->trans_end();

    //             $this->status = 1723;
    //             $this->message = 'Phone number too long';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         //check $catatan
    //         $catatan = $address_catatan;
    //         if (empty($catatan)) {
    //             $catatan = '';
    //         }

    //         // if ($this->__mbLen($catatan)>=128) {
    //             // //rollback table
    //             // $this->bu->trans_rollback();

    //             // //release table
    //             // $this->bu->trans_end();

    //         //     $this->status = 1724;
    //         //     $this->message = 'Address notes too long';
    //         //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         //     die();
    //         // }
    //         // by Muhammad Sofi - 3 November 2021 10:00
    //         // remark code

    //         $alamat2 = trim($address_alamat2);
    //         if (empty($alamat2)) {
    //             $alamat2 = '';
    //         }
    //         // if ($this->__mbLen($alamat2)>=128) {
    //             // //rollback table
    //             // $this->bu->trans_rollback();

    //             // //release table
    //             // $this->bu->trans_end();

    //         //     $this->status = 1765;
    //         //     $this->message = 'Secondary address too long';
    //         //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         //     die();
    //         // }

    //         $latitude = $address_latitude;
    //         $longitude = $address_longitude;
    //         if (strlen($latitude)<=3 || strlen($longitude)<=3) {
    //             //by Donny Dennison - 24 juli 2020 18:23
    //             //change default latitude and longitude
    //             // $latitude = $negara->latitude;
    //             // $longitude = $negara->longitude;
    //             $latitude = 0;
    //             $longitude = 0;
    //         }

    //         //populating input for location
    //         $provinsi = $address_provinsi;
    //         $kabkota = $address_kabkota;
    //         $kecamatan = $address_kecamatan;
    //         $kelurahan = $address_kelurahan;
    //         $kodepos = $address_kodepos;

    //         //validating
    //         if (empty($provinsi)) {
    //             $provinsi = '';
    //         }
    //         if (empty($kabkota)) {
    //             $kabkota = '';
    //         }
    //         if (empty($kecamatan)) {
    //             $kecamatan = '';
    //         }
    //         if (empty($kelurahan)) {
    //             $kelurahan = '';
    //         }
    //         if (empty($kodepos)) {
    //             $kodepos = '99999';
    //         }

    //         //check location properties provinsi
    //         if (!empty($negara->is_provinsi)) {
    //             if (strlen($provinsi)<=0) {
    //                 $this->bu->trans_rollback();
    //                 $this->bu->trans_end();

    //                 $this->status = 1766;
    //                 $this->message = 'Province / State are required for this country';
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }
    //         }

    //         //check location properties kabkota
    //         if (!empty($negara->is_kabkota)) {
    //             if (strlen($kabkota)<=0) {
    //                 $this->bu->trans_rollback();
    //                 $this->bu->trans_end();

    //                 $this->status = 1767;
    //                 $this->message = 'City are required for this country';
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }
    //         }

    //         //check location properties kecamatan
    //         if (!empty($negara->is_kecamatan)) {
    //             if (strlen($kecamatan)<=0) {
    //                 $this->bu->trans_rollback();
    //                 $this->bu->trans_end();

    //                 $this->status = 1724;
    //                 $this->message = 'District are required for this country';
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }
    //         }

    //         //check location properties kelurahan
    //         if (!empty($negara->is_kelurahan)) {
    //             if (strlen($kelurahan)<=0) {
    //                 $this->bu->trans_rollback();
    //                 $this->bu->trans_end();

    //                 $this->status = 1769;
    //                 $this->message = 'Sub District are required for this country';
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }
    //         }

    //         //check location properties kelurahan
    //         if (!empty($negara->is_kodepos)) {
    //             if (strlen($kodepos)<=0) {
    //                 $this->bu->trans_rollback();
    //                 $this->bu->trans_end();

    //                 $this->status = 1770;
    //                 $this->message = 'Zipcode / Postal Code are required for this country';
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }
    //         }

    //         // //check kelurahan, kecamatan, kabkota, provinsi, and kodepos in db or not
    //         // $checkInDBOrNot = $this->bual->checkInDBOrNot($nation_code, $kelurahan, $kecamatan, $kabkota, $provinsi, $kodepos);
    //         // if (!isset($checkInDBOrNot->id)){
    //                 // $this->bu->trans_rollback();
    //         // $this->bu->trans_end();

    //         //     $this->status = 1774;
    //         //     $this->message = 'This address is invalid, please find other address';
    //         //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         //     die();
    //         // }

    //         //get last id
    //         $last_id = $this->bua->getLastId($nation_code, $user_id);

    //         //collect input
    //         $di = array();
    //         $di['nation_code'] = $nation_code;
    //         $di['id'] = $last_id;
    //         $di['b_user_id'] = $user_id;

    //         //by Donny Dennison - 22 september 2021
    //         //auto-generate-address-title
    //         // $di['judul'] = $judul;
    //         $di['judul'] = 'Your Place '.$last_id;

    //         $di['penerima_nama'] = $penerima_nama;
    //         $di['penerima_telp'] = $penerima_telp;
    //         $di['alamat2'] = $alamat2;
    //         $di['kelurahan'] = $kelurahan;
    //         $di['kecamatan'] = $kecamatan;
    //         $di['kabkota'] = $kabkota;
    //         $di['provinsi'] = $provinsi;
    //         $di['negara'] = $negara->iso2;
    //         $di['kodepos'] = $kodepos;
    //         $di['longitude'] = $longitude;
    //         $di['latitude'] = $latitude;

    //         //by Donny Dennison - 13 july 2021 15:49
    //         //set-address-type-to-default
    //         // $di['address_status'] = $address_status;
    //         $di['address_status'] = 'A2';

    //         $di['catatan'] = $catatan;
    //         $di['is_default'] = 1;
    //         $di['is_active'] = 1;

    //         //insert into database
    //         $res = $this->bua->set($di);
    //         if ($res) {
    //             // $this->bu->trans_commit();
    //         } else {
    //             $this->bu->trans_rollback();
    //             $this->bu->trans_end();

    //             $this->status = 1771;
    //             $this->message = 'Failed insert user address';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         //START by Donny Dennison - 5 january 2021 - 11:49
    //         //change address default
    //         // //release default
    //         // $du = array("is_default"=>0);
    //         // $this->bua->updateByUserId($nation_code, $pelanggan->id, $du);
    //         // $this->bu->trans_commit();
    //         // //update default
    //         // $du = array("is_default"=>1);
    //         // $this->bua->update($nation_code, $pelanggan->id, $last_id, $du);
    //         // $this->bu->trans_commit();

    //         // $user_alamat_default = $this->bua->getByUserIdDefault($nation_code, $user_id);
    //         // if($last_id == 1 || !isset($user_alamat_default->alamat2)){
    //         //     $du = array("is_default"=>1);
    //         //     $this->bua->update($nation_code, $user_id, $last_id, $du);
    //         //     // $this->bu->trans_commit();
    //         // }
    //         //END by Donny Dennison - 5 january 2021 - 11:49

    //         //check kelurahan, kecamatan, kabkota, provinsi, and kodepos in db or not
    //         $checkInDBOrNot = $this->bual->checkInDBOrNot($nation_code, $kelurahan, $kecamatan, $kabkota, $provinsi, $kodepos);
    //         if (!isset($checkInDBOrNot->id)){ 
    //             //get last id
    //             $last_id = $this->bual->getLastId($nation_code);

    //             //collect input
    //             $di = array();
    //             $di['nation_code'] = $nation_code;
    //             $di['id'] = $last_id;
    //             $di['kelurahan'] = $kelurahan;
    //             $di['kecamatan'] = $kecamatan;
    //             $di['kabkota'] = $kabkota;
    //             $di['provinsi'] = $provinsi;
    //             $di['kodepos'] = $kodepos;
    //             $this->bual->set($di);
    //             // $this->bu->trans_commit();
    //         }

    //         // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", "All", "All", $provinsi);
    //         // if(!isset($getStatusHighlight->status)){
    //         //     //get last id
    //         //     $highlight_status_id = $this->gglhsm->getLastId($nation_code);
    //         //     $di = array();
    //         //     $di['nation_code'] = $nation_code;
    //         //     $di['id'] = $highlight_status_id;
    //         //     $di['b_user_alamat_location_kelurahan'] = 'All';
    //         //     $di['b_user_alamat_location_kecamatan'] = 'All';
    //         //     $di['b_user_alamat_location_kabkota'] = 'All';
    //         //     $di['b_user_alamat_location_provinsi'] = $provinsi;
    //         //     $this->gglhsm->set($di);
    //         //     // $this->bu->trans_commit();
    //         // }

    //         // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", "All", $kabkota, $provinsi);
    //         // if(!isset($getStatusHighlight->status)){
    //         //     //get last id
    //         //     $highlight_status_id = $this->gglhsm->getLastId($nation_code);
    //         //     $di = array();
    //         //     $di['nation_code'] = $nation_code;
    //         //     $di['id'] = $highlight_status_id;
    //         //     $di['b_user_alamat_location_kelurahan'] = 'All';
    //         //     $di['b_user_alamat_location_kecamatan'] = 'All';
    //         //     $di['b_user_alamat_location_kabkota'] = $kabkota;
    //         //     $di['b_user_alamat_location_provinsi'] = $provinsi;
    //         //     $this->gglhsm->set($di);
    //         //     // $this->bu->trans_commit();
    //         // }

    //         // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, "All", $kecamatan, $kabkota, $provinsi);
    //         // if(!isset($getStatusHighlight->status)){
    //         //     //get last id
    //         //     $highlight_status_id = $this->gglhsm->getLastId($nation_code);
    //         //     $di = array();
    //         //     $di['nation_code'] = $nation_code;
    //         //     $di['id'] = $highlight_status_id;
    //         //     $di['b_user_alamat_location_kelurahan'] = 'All';
    //         //     $di['b_user_alamat_location_kecamatan'] = $kecamatan;
    //         //     $di['b_user_alamat_location_kabkota'] = $kabkota;
    //         //     $di['b_user_alamat_location_provinsi'] = $provinsi;
    //         //     $this->gglhsm->set($di);
    //         //     // $this->bu->trans_commit();

    //         // }

    //         // $getStatusHighlight = $this->gglhsm->getByLocation($nation_code, $kelurahan, $kecamatan, $kabkota, $provinsi);
    //         // if(!isset($getStatusHighlight->status)){
    //         //     //get last id
    //         //     $highlight_status_id = $this->gglhsm->getLastId($nation_code);
    //         //     $di = array();
    //         //     $di['nation_code'] = $nation_code;
    //         //     $di['id'] = $highlight_status_id;
    //         //     $di['b_user_alamat_location_kelurahan'] = $kelurahan;
    //         //     $di['b_user_alamat_location_kecamatan'] = $kecamatan;
    //         //     $di['b_user_alamat_location_kabkota'] = $kabkota;
    //         //     $di['b_user_alamat_location_provinsi'] = $provinsi;
    //         //     $this->gglhsm->set($di);
    //         //     // $this->bu->trans_commit();
    //         // }
    //     } else {
    //         //rollback table
    //         $this->bu->trans_rollback();

    //         //release table
    //         $this->bu->trans_end();

    //         $this->status = 1706;
    //         $this->message = 'Failed save user to database, please try again';
    //         // if ($this->is_log) {
    //         //     $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- FAILED");
    //         // }
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     usleep(500000);
    //     //START by Donny Dennison - 10 december 2020 15:01
    //     //new registration system for apple id
    //     // $token = $this->__activateMobileToken($nation_code, $user_id);
    //     $user = $this->bu->getById($nation_code, $user_id);
    //     if ($reg_from == 'apple' && strpos($email, '@privaterelay.appleid.com') !== false) {
    //         $this->status = 200;
    //     }else{
    //     //END by Donny Dennison - 10 december 2020 15:01
    //     //new registration system for apple id

    //         //after success
    //         if ($register_success && !empty($user_id)) {
    //             $this->status = 200;
    //             // $this->message = 'registration successful, please check your inbox or spam before login';
    //             $this->message = 'Success';
    //             if ($this->email_send && strlen($email)>4) {
    //                 if ($confirmeds==0) {
    //                     // $link = $this->__activateGenerateLink($nation_code, $user_id, $user->api_reg_token);
    //                     $link = base_url("account/activate/index/$token_reg");

    //                     $nama = $user->fnama;
    //                     $replacer = array();
    //                     $replacer['site_name'] = $this->app_name;
    //                     $replacer['fnama'] = $nama;
    //                     $replacer['activation_link'] = $link;
    //                     $this->seme_email->flush();
    //                     $this->seme_email->replyto($this->site_name, $this->site_replyto);
    //                     $this->seme_email->from($this->site_email, $this->site_name);
    //                     $this->seme_email->subject('Registration Successful');
    //                     $this->seme_email->to($email, $nama);
    //                     $this->seme_email->template('account_register');
    //                     $this->seme_email->replacer($replacer);
    //                     $this->seme_email->send();
    //                 }
    //             }
    //         } else {
    //             $this->status = 1706;
    //             $this->message = 'Failed save user to database, please try again';
    //             // if ($this->is_log) {
    //             //     $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- status: ".$this->status." - ".$this->message);
    //             // }
    //         }

    //     //START by Donny Dennison - 10 december 2020 15:01
    //     //new registration system for apple id
    //     }
    //     //END by Donny Dennison - 10 december 2020 15:01

    //     //only manipulating
    //     if ($this->status == 200 && isset($user->id)) {
    //         if($reg_from == 'phone' || $reg_from == 'online'){
    //             $di = array();
    //             $di['b_user_id'] = $user->id;
    //             $this->fvpnm->update($verificationPhoneNumber->id, $di);
    //         }

    //         // $image = $this->__uploadUserImage($user->id);

    //         // $dux = array();
    //         // $dux['api_mobile_edate'] = date('Y-m-d H:i:00',strtotime("+$this->expired_token day"));
    //         // if (strlen($image)>4) {
    //         //     $dux['image'] = str_replace("//", "/", $image);
    //         // }
    //         // if(is_array($dux) && count($dux)) $this->bu->update($nation_code, $user->id, $dux);

    //         //add base url to image
    //         if (isset($user->image)) {
    //             $user->image = $this->cdn_url($image);
    //         }

    //         //by Donny Dennison - 08-09-2021 11:35
    //         //revamp-profile
    //         if (isset($user->image_banner)) {

    //             // by Muhammad Sofi - 28 October 2021 11:00
    //             // if user img & banner not exist or empty, change to default image
    //             // $user->image_banner = $this->cdn_url($user->image_banner);
    //             if(file_exists(SENEROOT.$user->image_banner)){
    //                 $user->image_banner = $this->cdn_url($user->image_banner);
    //             } else {
    //                 $user->image_banner = $this->cdn_url('media/user/default.png');
    //             }
    //         }

    //         //remove unecessary properties
    //         unset($user->api_mobile_token);
    //         unset($user->api_web_token);
    //         unset($user->api_reg_token);
    //         unset($user->password);
    //         $user->apisess = $token_plain;
    //         // $user->apisess_expired = $dux['api_mobile_edate'];
    //         $user->apisess_expired = $api_mobile_edate;
    //         $user->api_mobile_edate = $user->apisess_expired;

    //         //put to response
    //         $data['apisess'] = $token;
    //         $data['apisess_expired'] = $user->apisess_expired;
    //         $data['pelanggan'] = $user;
    //         // if ($this->is_log) {
    //         //     $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- Image Uploaded");
    //         // }
    //         //update user setting
    //         // $this->__callUserSettings($nation_code, $token);
    //         $settingController = new setting();
    //         $settingController->notificationcustom($nation_code, $apikey, $token);

    //         $getPointPlacement = $this->glptm->getByUserId($nation_code, $user->id);
    //         if(!isset($getPointPlacement->b_user_id)){
    //             //create point
    //             $di = array();
    //             $di['nation_code'] = $nation_code;
    //             $di['id'] = 1;
    //             $di['b_user_id'] = $user->id;
    //             $di['total_post'] = 0;
    //             $di['total_point'] = 0;
    //             $this->glptm->set($di);
    //         }
            // unset($getPointPlacement);

    //         //START by Donny Dennison - 12 september 2022 14:59
    //         //kode referral
    //         if($b_user_id_recruiter != '0'){
    //         //     $recruiterData = $this->bu->getById($nation_code, $b_user_id_recruiter);

    //         //     //RECRUITED
    //         //     //get point
    //         //     $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EY");
    //         //     if (!isset($pointGet->remark)) {
    //         //       $pointGet = new stdClass();
    //         //       $pointGet->remark = 10;
    //         //     }

    //         //     $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $user->id);

    //         //     $di = array();
    //         //     $di['nation_code'] = $nation_code;
    //         //     $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
    //         //     $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
    //         //     $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
    //         //     $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
    //         //     $di['b_user_id'] = $user->id;
    //         //     $di['point'] = $pointGet->remark;
    //         //     $di['custom_id'] = $b_user_id_recruiter;
    //         //     $di['custom_type'] = 'referral';
    //         //     $di['custom_type_sub'] = 'link';
    //         //     $di['custom_text'] = $user->fnama.' use '.$di['custom_type_sub'].' '.$di['custom_type'].' '.$recruiterData->fnama.' and get '.$di['point'].' point(s)';
          // $endDoWhile = 0;
          // do{
          //   $leaderBoardHistoryId = $this->GUIDv4();
          //   $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
          //   if($checkId == 0){
          //     $endDoWhile = 1;
          //   }
          // }while($endDoWhile == 0);
          // $di['id'] = $leaderBoardHistoryId;
    //         //     $this->glphm->set($di);
    //         //     // $this->glrm->updateTotal($nation_code, $user->id, 'total_point', '+', $di['point']);

    //         //     //RECRUITER
    //         //     if($recruiterData->is_active == 1){
    //         //         //get point
    //         //         $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EZ");
    //         //         if (!isset($pointGet->remark)) {
    //         //           $pointGet = new stdClass();
    //         //           $pointGet->remark = 10;
    //         //         }

    //         //         $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $b_user_id_recruiter);

    //         //         $di = array();
    //         //         $di['nation_code'] = $nation_code;
    //         //         $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
    //         //         $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
    //         //         $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
    //         //         $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
    //         //         $di['b_user_id'] = $b_user_id_recruiter;
    //         //         $di['point'] = $pointGet->remark;
    //         //         $di['custom_id'] = $user->id;
    //         //         $di['custom_type'] = 'referral';
    //         //         $di['custom_type_sub'] = 'link';
    //         //         $di['custom_text'] = $user->fnama.' use '.$di['custom_type_sub'].' '.$di['custom_type'].' '.$recruiterData->fnama.' and get '.$di['point'].' point(s)';
          // $endDoWhile = 0;
          // do{
          //   $leaderBoardHistoryId = $this->GUIDv4();
          //   $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
          //   if($checkId == 0){
          //     $endDoWhile = 1;
          //   }
          // }while($endDoWhile == 0);
          // $di['id'] = $leaderBoardHistoryId;
    //         //         $this->glphm->set($di);
    //         //         // $this->glrm->updateTotal($nation_code, $b_user_id_recruiter, 'total_point', '+', $di['point']);
    //         //     }

    //             // $this->bu->updateTotal($nation_code, $b_user_id_recruiter, "total_recruited", "+", "1");
    //             // $this->bu->updateDate($nation_code, $b_user_id_recruiter, "bdate", date("Y-m-d H:i:s"));
    //             $this->bu->updateTotalAndBDate($nation_code, $b_user_id_recruiter, "total_recruited", "+", "1", "bdate", date("Y-m-d H:i:s"));
    //         }
    //         //END by Donny Dennison - 12 september 2022 14:59
    //         //kode referral

    //         //START by Donny Dennison - 07 october 2022 15:49
    //         //integrate api blockchain
    //         // if($b_user_id_recruiter != 0){
    //         //     $recruiterData = $this->bu->getById($nation_code, $b_user_id_recruiter);

    //         //     if($recruiterData->is_get_point == 1){
    //         //         $response = json_decode($this->__callBlockChainCreateWallet($user->user_wallet_code, $recruiterData->user_wallet_code));
    //         //     }else{
    //         //         $response = json_decode($this->__callBlockChainCreateWallet($user->user_wallet_code));   
    //         //     }
    //         // }else{
    //         //     $response = json_decode($this->__callBlockChainCreateWallet($user->user_wallet_code));
    //         // }

    //         // if(isset($response->responseCode)){
    //         //     if($b_user_id_recruiter == 0){
    //         //         if($response->responseCode == 0){
    //         //             $du = array("blockchain_createuserwallet_api_called"=>1);
    //         //         }else{
    //         //             $du = array("blockchain_createuserwallet_api_called"=>0);
    //         //         }
    //         //     }else{
    //         //         if($response->responseCode == 0 && $recruiterData->is_get_point == 1){
    //         //             $du = array("blockchain_createuserwallet_api_called"=>1);
    //         //         }else if($response->responseCode == 0 && $recruiterData->is_get_point == 0){
    //         //             $du = array("blockchain_createuserwallet_api_called"=>3);
    //         //         }else{
    //         //             $du = array("blockchain_createuserwallet_api_called"=>0);
    //         //         }
    //         //     }
    //         // }else{
    //         //     $du = array("blockchain_createuserwallet_api_called"=>0);
    //         // }

    //         // $this->bu->update($nation_code, $user->id, $du);
    //         // unset($recruiterData, $response);
    //         //END by Donny Dennison - 07 october 2022 15:49
    //         //integrate api blockchain

    //         //START by Donny Dennison - 13 december 2022 14:31
    //         //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device
    //         $di = array();
    //         $di["nation_code"] = $nation_code;
    //         $endDoWhile = 0;
    //         do{
    //           $di["id"] = $this->GUIDv4();
    //           $checkId = $this->gdlm->checkId($nation_code, $di["id"]);
    //           if($checkId == 0){
    //               $endDoWhile = 1;
    //           }
    //         }while($endDoWhile == 0);
    //         $di["b_user_id"] = $user->id;
    //         $di["device_id"] = $device_id;
    //         $di["type"] = "signup";
    //         $di["cdate"] = "NOW()";
    //         $this->gdlm->set($di);
    //         //END by Donny Dennison - 13 december 2022 14:31
    //         //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device
    //     }

    //     // if ($this->is_log) {
    //     //     $this->seme_log->write("api_mobile", "API_Mobile/pelanggan::daftar -- status: ".$this->status." - ".$this->message);
    //     // }

    //     //by Donny Dennison - 25 august 2020 20:15
    //     //fix user setting not save to db
    //     if ($register_success && !empty($user_id)) {
    //         $this->status = 200;
    //         // $this->message = 'registration successful, please check your inbox or spam before login';
    //         $this->message = 'Success';
    //     } else {
    //         $this->status = 1706;
    //         $this->message = 'Failed save user to database, please try again';
    //     }

    //     $this->bu->trans_commit();
    //     //release table
    //     $this->bu->trans_end();

    //     //output as json
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    // }

    // public function login_sosmedv3()
    // {
    //     // $this->seme_log->write("api_mobile", "API_Mobile/pelanggan/login_sosmed:: --POST: ".json_encode($_POST));

    //     //initial
    //     $dt = $this->__init();

    //     //default result
    //     $data = array();
    //     $data['apisess'] = '';
    //     $data['apisess_expired'] = '';
    //     $data['pelanggan'] = new stdClass();
    //     $data['can_input_referral'] = '0';
    //     $data['is_register'] = '0';

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     //check apikey
    //     $apikey = $this->input->get('apikey');
    //     $c = $this->apikey_check($apikey);
    //     if (empty($c)) {
    //         $this->status = 400;
    //         $this->message = 'Missing or invalid API key';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }
    //     // $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::login_sosmed -- START');

    //     //https://php.watch/articles/modern-php-encryption-decryption-sodium
    //     // $keypair = sodium_crypto_box_keypair();
    //     // $keypair_public = sodium_crypto_box_publickey($keypair);
    //     // $keypair_secret = sodium_crypto_box_secretkey($keypair);
    //     // echo base64_encode($keypair_public);
    //     // echo "</br>";
    //     // echo base64_encode($keypair_secret);
    //     // echo "</br>";
    //     // $nonce = \random_bytes(\SODIUM_CRYPTO_BOX_NONCEBYTES);
    //     // $sender_keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey($keypair_secret, base64_decode("UXdxmySljXUyZ7CQzeoT5pqSb3eBVJ1NutpJdz4/S1E="));
    //     // $message = "Hi Bob, I'm Alice";
    //     // $encrypted_signed_text = sodium_crypto_box($message, $nonce, $sender_keypair);
    //     // echo base64_encode($nonce);
    //     // echo "</br>";
    //     // echo base64_encode($encrypted_signed_text);
    //     // die();
    //     $server_publickey = "FChvnIDp5EFw9Nf/Y2AIwrYKnturxomEefEQidHCNxg=";
    //     $server_privatekey = "Epl7V5hVoQIKLd14Tk/0Dryfh6HOl6vKOODy5U4kIJI=";
    //     $recipient_keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey(base64_decode($server_privatekey), base64_decode($this->input->post("bla2")));
    //     $postData = sodium_crypto_box_open(base64_decode($this->input->post("bla1")), base64_decode($this->input->post("bla3")), $recipient_keypair);
    //     if ($postData === false) {
    //         $this->status = 1750;
    //         $this->message = 'Please check your data again';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }
    //     $postData = json_decode($postData);

    //     $listOfPostData = array(
    //         "email",
    //         "fb_id",
    //         "apple_id",
    //         "google_id",
    //         "telp",
    //         "fcm_token",
    //         "device",
    //         "is_register",
    //         "fnama",
    //         "coverage_id",
    //         "is_changed_address",
    //         "coverage_id",
    //         "is_changed_address",
    //         "country_origin",
    //         "address_alamat2",
    //         "address_kelurahan",
    //         "address_kecamatan",
    //         "address_kabkota",
    //         "address_provinsi",
    //         "address_kodepos",
    //         "address_latitude",
    //         "address_longitude",
    //         "kode_referral",
    //         "referral_type",
    //         "device_id"
    //     );

    //     foreach($listOfPostData as $value) {
    //         if(!isset($postData->$value)){
    //             $postData->$value = "";
    //         }
    //     }

    //     //populating input
    //     $email = strtolower(trim($postData->email));
    //     $fb_id = $postData->fb_id;
    //     $apple_id = $postData->apple_id;
    //     $google_id = $postData->google_id;
    //     $telp = $postData->telp;
    //     $fcm_token = $postData->fcm_token;
    //     $device = strtolower(trim($postData->device));
    //     $is_register = $postData->is_register;
    //     if($is_register != 1){
    //         $is_register = 0;
    //     }

    //     $blackList = $this->gblm->check($nation_code, "fcm_token", $fcm_token);
    //     if(isset($blackList->id)){
    //         // $this->status = 1707;
    //         // $this->message = 'Invalid email or password';
    //         $this->status = 1728;
    //         $this->message = "Sorry, you're banned to log in, please contact WA(+65 8856 2024)";
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }

    //     // $address_latitude = $this->input->post('address_latitude');
    //     // $address_longitude = $this->input->post('address_longitude');
    //     // $latlng = $address_latitude.','.$address_longitude;

    //     // $url = "https://maps.google.com/maps/api/geocode/json?latlng=$latlng&key=AIzaSyCzx5dDTHwJLrgqgM0LXH6D1VGAHC0m4Rw&language=id";

    //     // $ch = curl_init();
    //     // curl_setopt($ch, CURLOPT_URL, $url);
    //     // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     // // curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    //     // // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //     // // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //     // $response = curl_exec($ch);
    //     // curl_close($ch);
    //     // $response_a = json_decode($response);
    //     // $response_geocode = $response_a->results[0]->address_components;
    //     // foreach ($response_geocode as $geo) { 
    //     //  $type_geo = $geo->types[0];

    //     //  // if($type_geo == "route") {
    //     //  //  $address_alamat2 = $geo->long_name;
    //     //  // } 
    //     //  if($type_geo == "administrative_area_level_4") {
    //     //      $address_kelurahan = $geo->long_name;
    //     //  }
    //     //  if($type_geo == "administrative_area_level_3") {
    //     //         $address_kecamatan_long = $geo->long_name;

    //     //         if (strpos($address_kecamatan_long, 'Kecamatan') !== false) {
    //     //             $address_kecamatan = str_replace("Kecamatan", "", $address_kecamatan_long);
    //     //         } else {
    //     //             $address_kecamatan = $geo->long_name;
    //     //         }
    //     //  }
    //     //     if($type_geo == "administrative_area_level_2") {
    //     //         $address_kabkota_long = $geo->long_name;

    //     //         if (strpos($address_kabkota_long, 'Kabupaten') !== false) {
    //     //             $address_kabkota = str_replace("Kabupaten", "", $address_kabkota_long);
    //     //         }else if (strpos($address_kabkota_long, 'Kota') !== false) {
    //     //             $address_kabkota = str_replace("Kota", "", $address_kabkota_long);
    //     //         } else {
    //     //             $address_kabkota = $geo->long_name;
    //     //         }
    //     //  } 
    //     //  if($type_geo == "administrative_area_level_1") {
    //     //      $address_provinsi_long = $geo->long_name;

    //     //         if (strpos($address_provinsi_long, 'Daerah Khusus Ibukota') !== false) {
    //     //             $address_provinsi = str_replace("Daerah Khusus Ibukota", "DKI", $address_provinsi_long);
    //     //         } else {
    //     //             $address_provinsi = $geo->long_name;
    //     //         }
    //     //  } 
    //     //  if($type_geo == "country") {
    //     //      $country_origin = $geo->long_name;
    //     //      $country_origin = strtolower($country_origin);
    //     //      $country_short = $geo->short_name;
    //     //  } 
    //     //  if($type_geo == "postal_code") {
    //     //      $address_kodepos = $geo->long_name;
    //     //  }
    //     // }

    //     // $alamat2 = $response_a->results[0]->formatted_address;
    //     // $new_alamat2 = explode(",", $alamat2);
    //     // $address_alamat2 = $new_alamat2[1];

    //     // // check if empty
    //     // if(empty($address_alamat2) || empty($address_provinsi) || empty($address_kabkota) || empty($address_kecamatan) || empty($address_kelurahan) || empty($address_latitude) || empty($address_longitude)){
    //     //     //START by Donny Dennison - 08 june 2022 15:15
    //     //     //change address flow in register
    //     //     // $this->status = 104;
    //     //     // $this->message = 'There is address that empty';
    //     //     // $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //     //     // die();

    //     //     $address_alamat2 = "Gg. H. Zakaria, RW.5";
    //     //     $address_provinsi = "DKI Jakarta";
    //     //     $address_kabkota = "Jakarta Pusat";
    //     //     $address_kecamatan = "Tanah Abang";
    //     //     $address_kelurahan = "Kebon Melati";
    //     //     $address_kodepos = "10230";
    //     //     $address_latitude = "-6.200055499719067";
    //     //     $address_longitude = "106.8162468531788";
    //     //     //END by Donny Dennison - 08 june 2022 15:15
    //     // }

    //     // if (strlen($fcm_token)<=100) {
    //     //     $fcm_token='';
    //     // }
    //     if (strlen($device)==3) {
    //         $device='ios';
    //     } else {
    //         $device='android';
    //     }

    //     //sanitize input
    //     if (empty($google_id)) {
    //         $google_id = "";
    //     }
    //     if (empty($fb_id)) {
    //         $fb_id = "";
    //     }
    //     if (empty($apple_id)) {
    //         $apple_id = "";
    //     }
    //     if (strlen($email)<=4) {
    //         $email = "";
    //     }

    //     // if(empty($fb_id) || empty($apple_id)) {
    //     //     $address_latitude = $this->input->post('address_latitude');
    //     //     $address_longitude = $this->input->post('address_longitude');
    //     //     $latlng = $address_latitude.','.$address_longitude;

    //     //     $url = "https://maps.google.com/maps/api/geocode/json?latlng=$latlng&key=AIzaSyCzx5dDTHwJLrgqgM0LXH6D1VGAHC0m4Rw&language=id";

    //     //     $ch = curl_init();
    //     //     curl_setopt($ch, CURLOPT_URL, $url);
    //     //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     //     // curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    //     //     // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //     //     // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //     //     $response = curl_exec($ch);
    //     //     curl_close($ch);
    //     //     $response_a = json_decode($response);
    //     //     $response_geocode = $response_a->results[0]->address_components;
    //     //     foreach ($response_geocode as $geo) { 
    //     //         $type_geo = $geo->types[0];

    //     //         // if($type_geo == "route") {
    //     //         //   $address_alamat2 = $geo->long_name;
    //     //         // } 
    //     //         if($type_geo == "administrative_area_level_4") {
    //     //             $address_kelurahan = $geo->long_name;
    //     //         }
    //     //         if($type_geo == "administrative_area_level_3") {
    //     //             $address_kecamatan_long = $geo->long_name;

    //     //             if (strpos($address_kecamatan_long, 'Kecamatan') !== false) {
    //     //                 $address_kecamatan = str_replace("Kecamatan", "", $address_kecamatan_long);
    //     //             } else {
    //     //                 $address_kecamatan = $geo->long_name;
    //     //             }
    //     //         }
    //     //         if($type_geo == "administrative_area_level_2") {
    //     //             $address_kabkota_long = $geo->long_name;

    //     //             if (strpos($address_kabkota_long, 'Kabupaten') !== false) {
    //     //                 $address_kabkota = str_replace("Kabupaten", "", $address_kabkota_long);
    //     //             }else if (strpos($address_kabkota_long, 'Kota') !== false) {
    //     //                 $address_kabkota = str_replace("Kota", "", $address_kabkota_long);
    //     //             } else {
    //     //                 $address_kabkota = $geo->long_name;
    //     //             }
    //     //         } 
    //     //         if($type_geo == "administrative_area_level_1") {
    //     //             $address_provinsi_long = $geo->long_name;

    //     //             if (strpos($address_provinsi_long, 'Daerah Khusus Ibukota') !== false) {
    //     //                 $address_provinsi = str_replace("Daerah Khusus Ibukota", "DKI", $address_provinsi_long);
    //     //             } else {
    //     //                 $address_provinsi = $geo->long_name;
    //     //             }
    //     //         } 
    //     //         if($type_geo == "country") {
    //     //             $country_origin = $geo->long_name;
    //     //             $country_origin = strtolower($country_origin);
    //     //             $country_short = $geo->short_name;
    //     //         } 
    //     //         if($type_geo == "postal_code") {
    //     //             $address_kodepos = $geo->long_name;
    //     //         }
    //     //     }

    //     //     $alamat2 = $response_a->results[0]->formatted_address;
    //     //     $new_alamat2 = explode(",", $alamat2);
    //     //     $address_alamat2 = $new_alamat2[1];

    //     //     // check if empty
    //     //     if(empty($address_alamat2) || empty($address_provinsi) || empty($address_kabkota) || empty($address_kecamatan) || empty($address_kelurahan) || empty($address_latitude) || empty($address_longitude)){
    //     //         //START by Donny Dennison - 08 june 2022 15:15
    //     //         //change address flow in register
    //     //         // $this->status = 104;
    //     //         // $this->message = 'There is address that empty';
    //     //         // $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //     //         // die();

    //     //         $address_alamat2 = "Gg. H. Zakaria, RW.5";
    //     //         $address_provinsi = "DKI Jakarta";
    //     //         $address_kabkota = "Jakarta Pusat";
    //     //         $address_kecamatan = "Tanah Abang";
    //     //         $address_kelurahan = "Kebon Melati";
    //     //         $address_kodepos = "10230";
    //     //         $address_latitude = "-6.200055499719067";
    //     //         $address_longitude = "106.8162468531788";
    //     //         //END by Donny Dennison - 08 june 2022 15:15
    //     //     }
    //     // }

    //     //initial variable
    //     $user = new stdClass();
    //     if (strlen($google_id)>1 && strlen($fb_id)>1 && strlen($apple_id)>1) {
    //         $user = $this->bu->checkGoogleID($nation_code, $google_id);
    //         if (!isset($user->id)) {
    //             $user = $this->bu->checkAppleID($nation_code, $apple_id);
    //         }
    //         if (!isset($user->id)) {
    //             $user = $this->bu->checkFBID($nation_code, $fb_id);
    //         }
    //         if (!isset($user->id)) {
    //             $user = $this->bu->checkEmail($nation_code, $email);
    //         }
    //         if (isset($user->id)) {
    //             if (($user->email != $email) && strlen($email)>4) {
    //                 $this->status = 1711;
    //                 $this->message = 'Google ID or FBID or AppleID not related to current email';
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }
    //         }
    //     } elseif (strlen($google_id)>1 && strlen($fb_id)<=0 && strlen($apple_id)<=0) {
    //         $user = $this->bu->checkGoogleID($nation_code, $google_id);
    //         if (!isset($user->id)) {
    //             $user = $this->bu->checkEmail($nation_code, $email);
    //         }
    //         if (isset($user->id)) {
    //             if (($user->email != $email) && strlen($email)>4) {
    //                 $this->status = 1712;
    //                 $this->message = 'Google ID not related to current email';
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }
    //         }
    //     } elseif (strlen($google_id)<=0 && strlen($fb_id)>1 && strlen($apple_id)<=0) {
    //         $user = $this->bu->checkFBID($nation_code, $fb_id);
    //         if (!isset($user->id) && strlen($email)>4) {
    //             $user = $this->bu->checkEmail($nation_code, $email);
    //             if (isset($user->id)) {
    //                 if ($user->email != $email) {
    //                     $this->status = 1713;
    //                     $this->message = 'FB ID not related to current email';
    //                     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                     die();
    //                 }
    //             }
    //         }
    //         if (isset($user->id)) {
    //             $email = $user->email;
    //         }
    //     } elseif (strlen($google_id)<=0 && strlen($fb_id)<=0 && strlen($apple_id)>1) {
    //         $user = $this->bu->checkAppleID($nation_code, $apple_id);
    //         if (isset($user->id)) {
    //             // because apple_id sometimes hide their email
    //             // So we will bypassed email checker for apple_id only
    //             $email = $user->email;
    //         }
    //     }

    //     if(!isset($user->id) && $is_register == 1 && (strlen($google_id)<=0 && strlen($fb_id)>1 && strlen($apple_id)<=0)){
    //         $fnama = $postData->fnama;
    //         if(empty($fnama)){
    //             $fnama = "no name";   
    //         }

    //         $coverage_id = trim($postData->coverage_id);
    //         $is_changed_address = trim($postData->is_changed_address);
    //         if($is_changed_address != 1){
    //             $is_changed_address = 0;
    //         }

    //         $country_origin = strtolower(trim($postData->country_origin));
    //         if(empty($country_origin)){
    //             $country_origin = "indonesia";   
    //         }

    //         if($country_origin != "indonesia"){
    //             $is_changed_address = 1;
    //         }

    //         if (strlen($email)<=4) {
    //             do {
    //                 $permitted_chars = "0123456789";
    //                 $permitted_chars_length = strlen($permitted_chars);
    //                 $length = 10;
    //                 $email = 'fb';
    //                 for($i = 0; $i < $length; $i++) {
    //                     $random_character = $permitted_chars[mt_rand(0, $permitted_chars_length - 1)];
    //                     $email .= $random_character;
    //                 }

    //                 $email .= "@sellon.net";

    //                 //check already in db or havent
    //                 $checkEmail = $this->bu->checkEmail($nation_code, $email);
    //             } while (isset($checkEmail->id));
    //         }

    //         // start comment code
    //         // $address_latitude = $this->input->post('address_latitude');
    //         // $address_longitude = $this->input->post('address_longitude');
    //         // $latlng = $address_latitude.','.$address_longitude;
    //         // $url = "https://maps.google.com/maps/api/geocode/json?latlng=$latlng&key=AIzaSyCzx5dDTHwJLrgqgM0LXH6D1VGAHC0m4Rw&language=id";

    //         // $ch = curl_init();
    //         // curl_setopt($ch, CURLOPT_URL, $url);
    //         // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //         // // curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    //         // // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //         // // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //         // $response = curl_exec($ch);
    //         // curl_close($ch);
    //         // $response_a = json_decode($response);
    //         // $response_geocode = $response_a->results[0]->address_components;
    //         // foreach ($response_geocode as $geo) { 
    //         //     $type_geo = $geo->types[0];

    //         //     // if($type_geo == "route") {
    //         //     //   $address_alamat2 = $geo->long_name;
    //         //     // } 
    //         //     if($type_geo == "administrative_area_level_4") {
    //         //         $address_kelurahan = $geo->long_name;
    //         //     }
    //         //     if($type_geo == "administrative_area_level_3") {
    //         //         $address_kecamatan_long = $geo->long_name;

    //         //         if (strpos($address_kecamatan_long, 'Kecamatan') !== false) {
    //         //             $address_kecamatan = str_replace("Kecamatan", "", $address_kecamatan_long);
    //         //         } else {
    //         //             $address_kecamatan = $geo->long_name;
    //         //         }
    //         //     }
    //         //     if($type_geo == "administrative_area_level_2") {
    //         //         $address_kabkota_long = $geo->long_name;

    //         //         if (strpos($address_kabkota_long, 'Kabupaten') !== false) {
    //         //             $address_kabkota = str_replace("Kabupaten", "", $address_kabkota_long);
    //         //         }else if (strpos($address_kabkota_long, 'Kota') !== false) {
    //         //             $address_kabkota = str_replace("Kota", "", $address_kabkota_long);
    //         //         } else {
    //         //             $address_kabkota = $geo->long_name;
    //         //         }
    //         //     } 
    //         //     if($type_geo == "administrative_area_level_1") {
    //         //         $address_provinsi_long = $geo->long_name;

    //         //         if (strpos($address_provinsi_long, 'Daerah Khusus Ibukota') !== false) {
    //         //             $address_provinsi = str_replace("Daerah Khusus Ibukota", "DKI", $address_provinsi_long);
    //         //         } else {
    //         //             $address_provinsi = $geo->long_name;
    //         //         }
    //         //     } 
    //         //     if($type_geo == "country") {
    //         //         $country_origin = $geo->long_name;
    //         //         $country_origin = strtolower($country_origin);
    //         //         $country_short = $geo->short_name;
    //         //     } 
    //         //     if($type_geo == "postal_code") {
    //         //         $address_kodepos = $geo->long_name;
    //         //     } 
    //         // }

    //         // $alamat2 = $response_a->results[0]->formatted_address;
    //         // $new_alamat2 = explode(",", $alamat2);
    //         // // $address_alamat2 = $new_alamat2[1];

    //         // $alamat2 = "";
    //         // foreach($new_alamat2 as $na) {
    //         //     if(stripos($na, "Jl.") !== false) {
    //         //         // echo "true array 0 \n";
    //         //         $alamat2 = $na;
    //         //         break;
    //         //     } else if(stripos($na, "Jl.") !== false) {
    //         //         // echo "true array 1 \n";
    //         //         $alamat2 = $na;
    //         //         break;
    //         //     }   
    //         // }
    //         // $address_alamat2 = $alamat2;

    //         // // check if empty
    //         // if(empty($address_alamat2) || empty($address_provinsi) || empty($address_kabkota) || empty($address_kecamatan) || empty($address_kelurahan) || empty($address_latitude) || empty($address_longitude)){
    //         //     //START by Donny Dennison - 08 june 2022 15:15
    //         //     //change address flow in register
    //         //     // $this->status = 104;
    //         //     // $this->message = 'There is address that empty';
    //         //     // $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         //     // die();

    //         //     $address_alamat2 = "Gg. H. Zakaria, RW.5";
    //         //     $address_provinsi = "DKI Jakarta";
    //         //     $address_kabkota = "Jakarta Pusat";
    //         //     $address_kecamatan = "Tanah Abang";
    //         //     $address_kelurahan = "Kebon Melati";
    //         //     $address_kodepos = "10230";
    //         //     $address_latitude = "-6.200055499719067";
    //         //     $address_longitude = "106.8162468531788";
    //         //     //END by Donny Dennison - 08 june 2022 15:15
    //         // }
    //         // end comment code

    //         $postDataCurl= array(
    //             'fb_id' => $fb_id,
    //             'email' => $email,
    //             'fnama' => $fnama,
    //             'fcm_token' => $fcm_token,
    //             'device' => $device,
    //             'address_penerima_nama' => $fnama,
    //             'address_alamat2' => $postData->address_alamat2,
    //             'address_kelurahan' => $postData->address_kelurahan,
    //             'address_kecamatan' => $postData->address_kecamatan,
    //             'address_kabkota' => $postData->address_kabkota,
    //             'address_provinsi' => $postData->address_provinsi,
    //             'address_kodepos' => $postData->address_kodepos,
    //             'address_latitude' => $postData->address_latitude,
    //             'address_longitude' => $postData->address_longitude,
    //             'coverage_id' => $coverage_id,
    //             'is_changed_address' => $is_changed_address,
    //             'country_origin' => $country_origin,
    //             'kode_referral' => $postData->kode_referral,
    //             'referral_type' => $postData->referral_type,
    //             'device_id' => $postData->device_id,
    //             'call_from' => "1ns!d3r",
    //             'ip_address' => $_SERVER['HTTP_X_REAL_IP']
    //         );

    //         $this->lib("seme_curl");
    //         $url = base_url("api_mobile/pelanggan/daftarfrommobilev3/?apikey=$apikey&nation_code=$nation_code&cf296563=zF2CSXpnQgu5NtNF7T3f");
    //         $curlResponse = $this->seme_curl->post($url, $postDataCurl);

    //         $body = json_decode($curlResponse->body);
    //         if ($body->status != 200) {
    //             $this->status = $body->status;
    //             $this->message = $body->message;
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         if($body->status == 200) {
    //             $data['is_register'] = '1';
    //         }

    //         usleep(500000);
    //         $user = $this->bu->checkFBID($nation_code, $fb_id);
    //         $email = $user->email;
    //     }

    //     if(!isset($user->id) && $is_register == 1 && (strlen($google_id)<=0 && strlen($fb_id)<=0 && strlen($apple_id)>1)){
    //         $fnama = $postData->fnama;
    //         if(empty($fnama)){
    //             $fnama = "no name";   
    //         }

    //         $coverage_id = trim($postData->coverage_id);
    //         $is_changed_address = trim($postData->is_changed_address);

    //         if($is_changed_address != 1){
    //             $is_changed_address = 0;
    //         }

    //         $country_origin = strtolower(trim($postData->country_origin));
    //         if(empty($country_origin)){
    //             $country_origin = "indonesia";   
    //         }

    //         if($country_origin != "indonesia"){
    //             $is_changed_address = 1;
    //         }

    //         if (strlen($email)<=4) {
    //             do {
    //                 $permitted_chars = "0123456789abcdefghijklmnopqrstuvwxyz";
    //                 $permitted_chars_length = strlen($permitted_chars);
    //                 $length = 10;
    //                 $email = '';
    //                 for($i = 0; $i < $length; $i++) {
    //                     $random_character = $permitted_chars[mt_rand(0, $permitted_chars_length - 1)];
    //                     $email .= $random_character;
    //                 }

    //                 $email .= "@privaterelay.appleid.com";

    //                 //check already in db or havent
    //                 $checkEmail = $this->bu->checkEmail($nation_code, $email);
    //             } while (isset($checkEmail->id));
    //         }

    //         // start comment code
    //         // $address_latitude = $this->input->post('address_latitude');
    //         // $address_longitude = $this->input->post('address_longitude');
    //         // $latlng = $address_latitude.','.$address_longitude;

    //         // $url = "https://maps.google.com/maps/api/geocode/json?latlng=$latlng&key=AIzaSyCzx5dDTHwJLrgqgM0LXH6D1VGAHC0m4Rw&language=id";

    //         // $ch = curl_init();
    //         // curl_setopt($ch, CURLOPT_URL, $url);
    //         // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //         // // curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    //         // // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //         // // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //         // $response = curl_exec($ch);
    //         // curl_close($ch);
    //         // $response_a = json_decode($response);
    //         // $response_geocode = $response_a->results[0]->address_components;
    //         // foreach ($response_geocode as $geo) { 
    //         //     $type_geo = $geo->types[0];

    //         //     // if($type_geo == "route") {
    //         //     //   $address_alamat2 = $geo->long_name;
    //         //     // } 
    //         //     if($type_geo == "administrative_area_level_4") {
    //         //         $address_kelurahan = $geo->long_name;
    //         //     }
    //         //     if($type_geo == "administrative_area_level_3") {
    //         //         $address_kecamatan_long = $geo->long_name;

    //         //         if (strpos($address_kecamatan_long, 'Kecamatan') !== false) {
    //         //             $address_kecamatan = str_replace("Kecamatan", "", $address_kecamatan_long);
    //         //         } else {
    //         //             $address_kecamatan = $geo->long_name;
    //         //         }
    //         //     }
    //         //     if($type_geo == "administrative_area_level_2") {
    //         //         $address_kabkota_long = $geo->long_name;

    //         //         if (strpos($address_kabkota_long, 'Kabupaten') !== false) {
    //         //             $address_kabkota = str_replace("Kabupaten", "", $address_kabkota_long);
    //         //         }else if (strpos($address_kabkota_long, 'Kota') !== false) {
    //         //             $address_kabkota = str_replace("Kota", "", $address_kabkota_long);
    //         //         } else {
    //         //             $address_kabkota = $geo->long_name;
    //         //         }
    //         //     } 
    //         //     if($type_geo == "administrative_area_level_1") {
    //         //         $address_provinsi_long = $geo->long_name;

    //         //         if (strpos($address_provinsi_long, 'Daerah Khusus Ibukota') !== false) {
    //         //             $address_provinsi = str_replace("Daerah Khusus Ibukota", "DKI", $address_provinsi_long);
    //         //         } else {
    //         //             $address_provinsi = $geo->long_name;
    //         //         }
    //         //     } 
    //         //     if($type_geo == "country") {
    //         //         $country_origin = $geo->long_name;
    //         //         $country_origin = strtolower($country_origin);
    //         //         $country_short = $geo->short_name;
    //         //     } 
    //         //     if($type_geo == "postal_code") {
    //         //         $address_kodepos = $geo->long_name;
    //         //     }
    //         // }

    //         // $alamat2 = $response_a->results[0]->formatted_address;
    //         // $new_alamat2 = explode(",", $alamat2);
    //         // // $address_alamat2 = $new_alamat2[1];

    //         // $alamat2 = "";
    //         // foreach($new_alamat2 as $na) {
    //         //     if(stripos($na, "Jl.") !== false) {
    //         //         // echo "true array 0 \n";
    //         //         $alamat2 = $na;
    //         //         break;
    //         //     } else if(stripos($na, "Jl.") !== false) {
    //         //         // echo "true array 1 \n";
    //         //         $alamat2 = $na;
    //         //         break;
    //         //     }   
    //         // }
    //         // $address_alamat2 = $alamat2;

    //         // // check if empty
    //         // if(empty($address_alamat2) || empty($address_provinsi) || empty($address_kabkota) || empty($address_kecamatan) || empty($address_kelurahan) || empty($address_latitude) || empty($address_longitude)){

    //         //     //START by Donny Dennison - 08 june 2022 15:15
    //         //     //change address flow in register
    //         //     // $this->status = 104;
    //         //     // $this->message = 'There is address that empty';
    //         //     // $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         //     // die();

    //         //     $address_alamat2 = "Gg. H. Zakaria, RW.5";
    //         //     $address_provinsi = "DKI Jakarta";
    //         //     $address_kabkota = "Jakarta Pusat";
    //         //     $address_kecamatan = "Tanah Abang";
    //         //     $address_kelurahan = "Kebon Melati";
    //         //     $address_kodepos = "10230";
    //         //     $address_latitude = "-6.200055499719067";
    //         //     $address_longitude = "106.8162468531788";
    //         //     //END by Donny Dennison - 08 june 2022 15:15
    //         // }
    //         // end comment code

    //         $postDataCurl= array(
    //             'apple_id' => $apple_id,
    //             'email' => $email,
    //             'fnama' => $fnama,
    //             'fcm_token' => $fcm_token,
    //             'device' => $device,
    //             'address_penerima_nama' => $fnama,
    //             'address_alamat2' => $postData->address_alamat2,
    //             'address_kelurahan' => $postData->address_kelurahan,
    //             'address_kecamatan' => $postData->address_kecamatan,
    //             'address_kabkota' => $postData->address_kabkota,
    //             'address_provinsi' => $postData->address_provinsi,
    //             'address_kodepos' => $postData->address_kodepos,
    //             'address_latitude' => $postData->address_latitude,
    //             'address_longitude' => $postData->address_longitude,
    //             'coverage_id' => $coverage_id,
    //             'is_changed_address' => $is_changed_address,
    //             'country_origin' => $country_origin,
    //             'kode_referral' => $postData->kode_referral,
    //             'referral_type' => $postData->referral_type,
    //             'device_id' => $postData->device_id,
    //             'call_from' => "1ns!d3r",
    //             'ip_address' => $_SERVER['HTTP_X_REAL_IP']
    //         );

    //         $this->lib("seme_curl");
    //         $url = base_url("api_mobile/pelanggan/daftarfrommobilev3/?apikey=$apikey&nation_code=$nation_code&cf296563=zF2CSXpnQgu5NtNF7T3f");
    //         $curlResponse = $this->seme_curl->post($url, $postDataCurl);

    //         $body = json_decode($curlResponse->body);
    //         if ($body->status != 200) {
    //             $this->status = $body->status;
    //             $this->message = $body->message;
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         if($body->status == 200) {
    //             $data['is_register'] = '1';
    //         }

    //         usleep(500000);
    //         $user = $this->bu->checkAppleID($nation_code, $apple_id);
    //         $email = $user->email;

    //     }

    //     //check email
    //     if (isset($user->id)) {
    //         if ($user->email != $email) {
    //             $this->status = 1720;
    //             $this->message = 'Email does not match with any social media ID';
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         //START by Donny Dennison - 13 december 2022 14:31
    //         //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device
    //         $device_id = trim($postData->device_id);

    //         $checkUserHaveDevice = $this->gdlm->countAll($nation_code, $user->id, $device_id);
    //         if($checkUserHaveDevice == 0){
    //             $totalUsedDeviceId = $this->gdlm->countAll($nation_code, "", $device_id);

    //             //get max used in 1 device
    //             $maxUsed = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C6");
    //             if (!isset($maxUsed->remark)) {
    //                 $maxUsed = new stdClass();
    //                 $maxUsed->remark = 5;
    //             }

    //             if($totalUsedDeviceId >= $maxUsed->remark){
    //                 $this->status = 1726;
    //                 $this->message = "You're not allowed to use many accounts";
    //                 $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //                 die();
    //             }

    //         }
    //         //END by Donny Dennison - 13 december 2022 14:31
    //         //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device

    //         //by Donny Dennison - 19 july 2022 15:42
    //         //delete temporary or permanent user feature
    //         // if (empty($user->is_active)) {
    //         if ($user->is_permanent_inactive == 0) {
    //             $this->status = 1728;
    //             $this->message = "Sorry, you're banned to log in, please contact WA(+65 8856 2024)";
    //             $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //             die();
    //         }

    //         //check again
    //         $dux = array();
    //         $dux['api_mobile_edate'] = date('Y-m-d H:i:00',strtotime("+$this->expired_token day"));
    //         $dux['is_confirmed'] = 1;
    //         $dux['fcm_token'] = $fcm_token;
    //         $dux['device'] = $device;

    //         if (strlen($google_id)>1) {
    //             $dux['google_id'] = $google_id;
    //         }
    //         if (strlen($fb_id)>1) {
    //             $dux['fb_id'] = $fb_id;
    //         }
    //         if (strlen($apple_id)>1) {
    //             $dux['apple_id'] = $apple_id;
    //         }
    //         if (is_array($dux) && count($dux)) {
    //             $this->bu->update($nation_code, $user->id, $dux);
    //         }

    //         $user = $this->bu->getById($nation_code, $user->id);
    //         $token = $this->__activateMobileToken($nation_code, $user->id);
    //         $data['apisess'] = $token;
    //         $data['apisess_expired'] = $dux['api_mobile_edate'];
    //         // by Muhammad Sofi - 26 October 2021 11:16
    //         // if user img & banner not exist or empty, change to default image
    //         // $user->image = $this->cdn_url($user->image);
    //         if(file_exists(SENEROOT.$user->image) && $user->image != 'media/user/default.png'){
    //             $user->image = $this->cdn_url($user->image);
    //         } else {
    //             $user->image = $this->cdn_url('media/user/default-profile-picture.png');
    //         }

    //         //by Donny Dennison - 08-09-2021 11:35
    //         //revamp-profile

    //         // by Muhammad Sofi - 26 October 2021 11:16
    //         // if user img & banner not exist or empty, change to default image
    //         // $user->image_banner = $this->cdn_url($user->image_banner);
    //         if(file_exists(SENEROOT.$user->image_banner)){
    //             $user->image_banner = $this->cdn_url($user->image_banner);
    //         } else {
    //             $user->image_banner = $this->cdn_url('media/user/default.png');
    //         }

    //         //add user token
    //         $user->apisess = $token_plain;

    //         //remove sensitive content
    //         unset($user->api_mobile_token);
    //         unset($user->api_web_token);
    //         unset($user->api_reg_token);
    //         unset($user->password);
    //         //unset($user->fb_id);
    //         //unset($user->google_id);

    //         //by Donny Dennison - 25 august 2020 20:15
    //         //fix user setting not save to db
    //         // $this->status = 200;
    //         // $this->message = 'Success';

    //         //by Donny Dennison - 13 july 2021 10:46
    //         //show-default-address-after-login
    //         $user->default_address = $this->bua->getByUserIdDefault($nation_code, $user->id);

    //         $data['pelanggan'] = $user;

    //         //update user setting
    //         // $this->__callUserSettings($nation_code, $token);
    //         $settingController = new setting();
    //         $settingController->notificationcustom($nation_code, $apikey, $token);

    //     } else {
    //         $this->status = 1722;
    //         $this->message = 'User currently unregistered with any Social ID or Email';
    //     }

    //     //by Donny Dennison - 25 august 2020 20:15
    //     //fix user setting not save to db
    //     if (isset($user->id)) {
    //         $this->status = 200;
    //         $this->message = 'Success';

    //         $du = array();
    //         $du['is_online'] = 1;
    //         $du['last_online'] = date('Y-m-d H:i:s');

    //         //by Donny Dennison - 19 july 2022 15:42
    //         //delete temporary or permanent user feature
    //         $du['is_active'] = 1;

    //         $du['ip_address'] = $_SERVER['HTTP_X_REAL_IP'];
    //         $this->bu->update($nation_code, $user->id, $du);

    //         $data['pelanggan']->is_online = "1";

    //         $data['pelanggan']->total_product = $this->cpm->countAll($nation_code, "", "",$user->id, "", "", array(), array(), array(), "All", $data['pelanggan']->default_address, "ProtectionAndMeetUpAndAutomotive", 0, '', array(), array(), array(), 1);

    //         //START by Donny Dennison - 10 november 2022 14:34
    //         //new feature, join/input referral
    //         $limit = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E7");
    //         if (!isset($limit->remark)) {
    //           $limit = new stdClass();
    //           $limit->remark = 5;
    //         }

    //         if($data['pelanggan']->b_user_id_recruiter == '0' && date("Y-m-d", strtotime($data['pelanggan']->cdate." +".$limit->remark." days")) > date("Y-m-d")){
    //             $data['can_input_referral'] = '1';
    //         }
    //         //END by Donny Dennison - 10 november 2022 14:34
    //         //new feature, join/input referral

    //         //START by Donny Dennison - 13 december 2022 14:31
    //         //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device
    //         $di = array();
    //         $di["nation_code"] = $nation_code;
    //         $endDoWhile = 0;
    //         do{
    //           $di["id"] = $this->GUIDv4();
    //           $checkId = $this->gdlm->checkId($nation_code, $di["id"]);
    //           if($checkId == 0){
    //               $endDoWhile = 1;
    //           }
    //         }while($endDoWhile == 0);
    //         $di["b_user_id"] = $user->id;
    //         $di["device_id"] = $device_id;
    //         $di["type"] = "login";
    //         $di["cdate"] = "NOW()";
    //         $this->gdlm->set($di);
    //         //END by Donny Dennison - 13 december 2022 14:31
    //         //Allow only 5 (dynamic) accounts available for signIn&SignUp in the same device

    //         $data['pelanggan']->bKodeRecuiter = "";
    //         $data['pelanggan']->bNamaRecuiter = "";
    //         if($data['pelanggan']->b_user_id_recruiter != '0'){
    //             $recommenderData = $this->bu->getById($nation_code, $data['pelanggan']->b_user_id_recruiter);
    //             if(isset($recommenderData->kode_referral)){
    //                 $data['pelanggan']->bKodeRecuiter = $recommenderData->kode_referral;
    //                 $data['pelanggan']->bNamaRecuiter = $recommenderData->fnama;
    //             }
    //         }
    //     }else{
    //         $this->status = 1722;
    //         $this->message = 'User currently unregistered with any Social ID or Email';
    //     }

    //     // if ($this->is_log) {
    //     //     $this->seme_log->write("api_mobile", 'API_Mobile/pelanggan::login_sosmed -- END '.$this->status.' '.$this->message);
    //     // }

    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    // }
}
