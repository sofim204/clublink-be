<?php
class Activity_dashboard extends JI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_mobile/b_user_model", 'bu');
        $this->load("api_mobile/common_code_model", 'ccm');
        $this->load("api_mobile/g_leaderboard_point_history_model", 'glphm');
        $this->load("api_mobile/c_community_model", "ccomm");
        $this->load("api_mobile/g_leaderboard_point_total_model", "glptm");
    }

    //START by Donny Dennison - 10 october 2022 10:45
    //integrate api blockchain
    // private function __callBlockChainSPTBalance($userWalletCode){
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    //     curl_setopt($ch, CURLOPT_URL, $this->blockchain_api_host."Wallet/GetSPTBalanceWithEncryption");

    //     $headers = array();
    //     $headers[] = 'Content-Type:  application/json';
    //     $headers[] = 'Accept:  application/json';
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //     $postdata = array(
    //       'userWalletCode' => $this->__encryptdecrypt($userWalletCode,"encrypt"),
    //       'countryIsoCode' => $this->blockchain_api_country

    //     );
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));

    //     $result = curl_exec($ch);
    //     if (curl_errno($ch)) {
    //       return 0;
    //       //echo 'Error:' . curl_error($ch);
    //     }
    //     curl_close($ch);

    //     $this->seme_log->write("api_mobile", "url untuk block chain server ".$this->blockchain_api_host."Wallet/GetSPTBalanceWithEncryption. data send to blockchain api ". json_encode($postdata).". isi response block chain server ". $result);

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
    //END by Donny Dennison - 10 october 2022 10:45
    //integrate api blockchain

    public function index()
    {
        //default result
        $data = array();
        $data['point_get'] = "0";
        $data['point_max'] = "0";
        // $data['spt_balance'] = "menghitung";
        $data['spt_balance'] = "0";
        $data['accomplishment'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "activity_dashboard");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "activity_dashboard");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "activity_dashboard");
            die();
        }

        $getPointNow = $this->glptm->getByUserId($nation_code, $pelanggan->id);
        if(isset($getPointNow->b_user_id)){
            $data['spt_balance'] = (string) number_format($getPointNow->total_point, 0, ',', '.');
        }

        $dateCompare = date("Y-m-d");

        if($pelanggan->language_id == 2){
            $data['accomplishment'][] = array(
                "title" => "General",
                "data" => array()
            );
            $data['accomplishment'][] = array(
                "title" => "Komunitas",
                "data" => array()
            );
            $data['accomplishment'][] = array(
                "title" => "Beli & Jual",
                "data" => array()
            );
            $data['accomplishment'][] = array(
                "title" => "Klub",
                "data" => array()
            );

            $isiArray = array(
                0 => array(
                    0 => array(
                        "title" => "Daftar",
                        "clicked" => "",
                        "query_type" => 4,
                        "custom_type" => "sign up",
                        "custom_type_sub" => "",
                        "point_code" => "",
                        "point_get_code" => "",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    1 => array(
                        "title" => "Undangan",
                        "clicked" => "invite friend",
                        "query_type" => 3,
                        "custom_type" => "referral",
                        "custom_type_sub" => "",
                        "point_code" => "EZ",
                        "point_get_code" => "",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    2 => array(
                        "title" => "Harian",
                        "clicked" => "check in",
                        "query_type" => 1,
                        "custom_type" => "check in",
                        "custom_type_sub" => "daily",
                        "point_code" => "",
                        "point_get_code" => "",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    3 => array(
                        "title" => "Mingguan",
                        "clicked" => "check in",
                        "query_type" => 1,
                        "custom_type" => "check in",
                        "custom_type_sub" => "weekly",
                        "point_code" => "",
                        "point_get_code" => "",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    4 => array(
                        "title" => "Bulanan",
                        "clicked" => "check in",
                        "query_type" => 1,
                        "custom_type" => "check in",
                        "custom_type_sub" => "monthly",
                        "point_code" => "",
                        "point_get_code" => "",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    )
                ),
                1 => array(
                    // 0 => array(
                    //     "title" => "Postingan Pertama",
                    //     "clicked" => "community",
                    //     "query_type" => 5,
                    //     "custom_type" => "community",
                    //     "custom_type_sub" => "post",
                    //     "point_code" => "",
                    //     "point_get_code" => "EF",
                    //     "point_text_type" => 1
                    // ),
                    0 => array(
                        "title" => "Kata",
                        "clicked" => "new post",
                        "query_type" => 6,
                        "custom_type" => "community",
                        "custom_type_sub" => "post",
                        "point_code" => "EE",
                        "point_get_code" => "EG",
                        "point_text_type" => 1,
                        "show_count" => "yes"
                    ),
                    1 => array(
                        "title" => "Foto",
                        "clicked" => "new post",
                        "query_type" => 6,
                        "custom_type" => "community",
                        "custom_type_sub" => "upload image",
                        "point_code" => "EE",
                        "point_get_code" => "E13",
                        "point_text_type" => 1,
                        "show_count" => "yes"
                    ),
                    2 => array(
                        "title" => "Video",
                        "clicked" => "new post",
                        "query_type" => 6,
                        "custom_type" => "community",
                        "custom_type_sub" => "upload video",
                        "point_code" => "EE",
                        "point_get_code" => "EP",
                        "point_text_type" => 1,
                        "show_count" => "yes"
                    ),
                    3 => array(
                        "title" => "Balasan",
                        "clicked" => "community",
                        "query_type" => 1,
                        "custom_type" => "community",
                        "custom_type_sub" => "reply",
                        "point_code" => "EH",
                        "point_get_code" => "EI",
                        "point_text_type" => 1,
                        "show_count" => "yes"
                    ),
                    // 2 => array(
                    //     "title" => "Suka",
                    //     "clicked" => "community",
                    //     "query_type" => 1,
                    //     "custom_type" => "community",
                    //     "custom_type_sub" => "like",
                    //     "point_code" => "EJ",
                    //     "point_get_code" => "EK",
                    //     "point_text_type" => 1,
                        // "show_count" => "yes"
                    // ),
                    // 2 => array(
                    //     "title" => "Video",
                    //     "clicked" => "new post",
                    //     "query_type" => 1,
                    //     "custom_type" => "community",
                    //     "custom_type_sub" => "upload video",
                    //     "point_code" => "E9",
                    //     "point_get_code" => "",
                    //     "point_text_type" => 1,
                        // "show_count" => "yes"
                    // )
                ),
                2 => array(
                    0 => array(
                        "title" => "Kata",
                        "clicked" => "add product",
                        "query_type" => 6,
                        "custom_type" => "product",
                        "custom_type_sub" => "post",
                        "point_code" => "E10",
                        "point_get_code" => "EA",
                        "point_text_type" => 1,
                        "show_count" => "yes"
                    ),
                    // 1 => array(
                    //     "title" => "Buat Tawaran",
                    //     "clicked" => "buy & sell",
                    //     "query_type" => 2,
                    //     "custom_type" => "offer",
                    //     "custom_type_sub" => "review",
                    //     "point_code" => "EQ",
                    //     "point_get_code" => "",
                    //     "point_text_type" => 1,
                        // "show_count" => "yes"
                    // ),
                    // 2 => array(
                    //     "title" => "Pesanan Proteksi",
                    //     "clicked" => "buy & sell",
                    //     "query_type" => 1,
                    //     "custom_type" => "order",
                    //     "custom_type_sub" => "review",
                    //     "point_code" => "",
                        // "point_get_code" => "",
                    //     "point_text_type" => 1,
                        // "show_count" => "yes"
                    // ),
                    1 => array(
                        "title" => "Video",
                        "clicked" => "add product",
                        "query_type" => 6,
                        "custom_type" => "product",
                        "custom_type_sub" => "upload video",
                        "point_code" => "E8",
                        "point_get_code" => "EO",
                        "point_text_type" => 1,
                        "show_count" => "yes"
                    )
                ),
                3 => array(
                    0 => array(
                        "title" => "Buat club",
                        "clicked" => "new club",
                        "query_type" => 7,
                        "custom_type" => "club",
                        "custom_type_sub" => "create club",
                        "point_code" => "E33",
                        "point_get_code" => "E17",
                        "point_text_type" => 1,
                        "show_count" => "yes"
                    ),
                    1 => array(
                        "title" => "Kata",
                        "clicked" => "new club post",
                        "query_type" => 1,
                        "custom_type" => "club",
                        "custom_type_sub" => "post",
                        "point_code" => "",
                        "point_get_code" => "E19",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    2 => array(
                        "title" => "Foto",
                        "clicked" => "new club post",
                        "query_type" => 1,
                        "custom_type" => "club",
                        "custom_type_sub" => "upload image",
                        "point_code" => "",
                        "point_get_code" => "E20",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    3 => array(
                        "title" => "Video",
                        "clicked" => "new club post",
                        "query_type" => 1,
                        "custom_type" => "club",
                        "custom_type_sub" => "upload video",
                        "point_code" => "",
                        "point_get_code" => "E21",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    4 => array(
                        "title" => "Lembar kehadiran",
                        "clicked" => "new club post",
                        "query_type" => 1,
                        "custom_type" => "club",
                        "custom_type_sub" => "attendance sheet",
                        "point_code" => "",
                        "point_get_code" => "E22",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    5 => array(
                        "title" => "Lokasi",
                        "clicked" => "new club post",
                        "query_type" => 1,
                        "custom_type" => "club",
                        "custom_type_sub" => "location",
                        "point_code" => "",
                        "point_get_code" => "E23",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    6 => array(
                        "title" => "Suka",
                        "clicked" => "club",
                        "query_type" => 1,
                        "custom_type" => "club",
                        "custom_type_sub" => "like",
                        "point_code" => "",
                        "point_get_code" => "E24",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    7 => array(
                        "title" => "Balasan",
                        "clicked" => "club",
                        "query_type" => 1,
                        "custom_type" => "club",
                        "custom_type_sub" => "reply",
                        "point_code" => "",
                        "point_get_code" => "E25",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    8 => array(
                        "title" => "Video ditonton",
                        "clicked" => "club",
                        "query_type" => 1,
                        "custom_type" => "club",
                        "custom_type_sub" => "watch video",
                        "point_code" => "",
                        "point_get_code" => "E27",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    9 => array(
                        "title" => "Undang Anggota",
                        "clicked" => "club",
                        "query_type" => 1,
                        "custom_type" => "club",
                        "custom_type_sub" => "invite member join club",
                        "point_code" => "",
                        "point_get_code" => "E28",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    10 => array(
                        "title" => "Orang masuk",
                        "clicked" => "club",
                        "query_type" => 1,
                        "custom_type" => "club",
                        "custom_type_sub" => "member join club",
                        "point_code" => "",
                        "point_get_code" => "E29",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    11 => array(
                        "title" => "Aktivitas member",
                        "clicked" => "club",
                        "query_type" => 1,
                        "custom_type" => "club",
                        "custom_type_sub" => "commission",
                        "point_code" => "",
                        "point_get_code" => "E32",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    )
                )
            );
        }else{
            $data['accomplishment'][] = array(
                "title" => "General",
                "data" => array()
            );
            $data['accomplishment'][] = array(
                "title" => "Community",
                "data" => array()
            );
            $data['accomplishment'][] = array(
                "title" => "Buy & Sell",
                "data" => array()
            );
            $data['accomplishment'][] = array(
                "title" => "Club",
                "data" => array()
            );

            $isiArray = array(
                0 => array(
                    0 => array(
                        "title" => "Sign Up",
                        "clicked" => "",
                        "query_type" => 4,
                        "custom_type" => "sign up",
                        "custom_type_sub" => "",
                        "point_code" => "",
                        "point_get_code" => "",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    1 => array(
                        "title" => "Invite",
                        "clicked" => "invite friend",
                        "query_type" => 3,
                        "custom_type" => "referral",
                        "custom_type_sub" => "",
                        "point_code" => "EZ",
                        "point_get_code" => "",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    2 => array(
                        "title" => "Daily",
                        "clicked" => "check in",
                        "query_type" => 1,
                        "custom_type" => "check in",
                        "custom_type_sub" => "daily",
                        "point_code" => "",
                        "point_get_code" => "",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    3 => array(
                        "title" => "Weekly",
                        "clicked" => "check in",
                        "query_type" => 1,
                        "custom_type" => "check in",
                        "custom_type_sub" => "weekly",
                        "point_code" => "",
                        "point_get_code" => "",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    4 => array(
                        "title" => "Monthly",
                        "clicked" => "check in",
                        "query_type" => 1,
                        "custom_type" => "check in",
                        "custom_type_sub" => "monthly",
                        "point_code" => "",
                        "point_get_code" => "",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    )
                ),
                1 => array(
                    // 0 => array(
                    //     "title" => "First Post",
                    //     "clicked" => "community",
                    //     "query_type" => 5,
                    //     "custom_type" => "community",
                    //     "custom_type_sub" => "post",
                    //     "point_code" => "",
                    //     "point_get_code" => "EF",
                    //     "point_text_type" => 1,
                        // "show_count" => "no"
                    // ),
                    0 => array(
                        "title" => "Text",
                        "clicked" => "new post",
                        "query_type" => 6,
                        "custom_type" => "community",
                        "custom_type_sub" => "post",
                        "point_code" => "EE",
                        "point_get_code" => "EG",
                        "point_text_type" => 1,
                        "show_count" => "yes"
                    ),
                    1 => array(
                        "title" => "Photo",
                        "clicked" => "new post",
                        "query_type" => 6,
                        "custom_type" => "community",
                        "custom_type_sub" => "upload image",
                        "point_code" => "EE",
                        "point_get_code" => "E13",
                        "point_text_type" => 1,
                        "show_count" => "yes"
                    ),
                    2 => array(
                        "title" => "Video",
                        "clicked" => "new post",
                        "query_type" => 6,
                        "custom_type" => "community",
                        "custom_type_sub" => "upload video",
                        "point_code" => "EE",
                        "point_get_code" => "EP",
                        "point_text_type" => 1,
                        "show_count" => "yes"
                    ),
                    3 => array(
                        "title" => "Reply",
                        "clicked" => "community",
                        "query_type" => 1,
                        "custom_type" => "community",
                        "custom_type_sub" => "reply",
                        "point_code" => "EH",
                        "point_get_code" => "EI",
                        "point_text_type" => 1,
                        "show_count" => "yes"
                    ),
                    // 2 => array(
                    //     "title" => "Likes",
                    //     "clicked" => "community",
                    //     "query_type" => 1,
                    //     "custom_type" => "community",
                    //     "custom_type_sub" => "like",
                    //     "point_code" => "EJ",
                    //     "point_get_code" => "EK",
                    //     "point_text_type" => 1,
                        // "show_count" => "yes"
                    // ),
                    // 2 => array(
                    //     "title" => "Video",
                    //     "clicked" => "new post",
                    //     "query_type" => 1,
                    //     "custom_type" => "community",
                    //     "custom_type_sub" => "upload video",
                    //     "point_code" => "E9",
                    //     "point_get_code" => "",
                    //     "point_text_type" => 1,
                        // "show_count" => "yes"
                    // )
                ),
                2 => array(
                    0 => array(
                        "title" => "Text",
                        "clicked" => "add product",
                        "query_type" => 6,
                        "custom_type" => "product",
                        "custom_type_sub" => "post",
                        "point_code" => "E10",
                        "point_get_code" => "EA",
                        "point_text_type" => 1,
                        "show_count" => "yes"
                    ),
                    // 1 => array(
                    //     "title" => "Make an Offer",
                    //     "clicked" => "buy & sell",
                    //     "query_type" => 2,
                    //     "custom_type" => "offer",
                    //     "custom_type_sub" => "",
                    //     "point_code" => "",
                    //     "point_get_code" => "",
                    //     "point_text_type" => 1,
                        // "show_count" => "yes"
                    // ),
                    // 2 => array(
                    //     "title" => "Protection Order",
                    //     "clicked" => "buy & sell",
                    //     "query_type" => 1,
                    //     "custom_type" => "order",
                    //     "custom_type_sub" => "review",
                    //     "point_code" => "",
                        // "point_get_code" => "",
                    //     "point_text_type" => 1,
                        // "show_count" => "yes"
                    // ),
                    1 => array(
                        "title" => "Video",
                        "clicked" => "add product",
                        "query_type" => 6,
                        "custom_type" => "product",
                        "custom_type_sub" => "upload video",
                        "point_code" => "E8",
                        "point_get_code" => "EO",
                        "point_text_type" => 1,
                        "show_count" => "yes"
                    )
                ),
                3 => array(
                    0 => array(
                        "title" => "Create club",
                        "clicked" => "new club",
                        "query_type" => 7,
                        "custom_type" => "club",
                        "custom_type_sub" => "create club",
                        "point_code" => "E33",
                        "point_get_code" => "E17",
                        "point_text_type" => 1,
                        "show_count" => "yes"
                    ),
                    1 => array(
                        "title" => "Text",
                        "clicked" => "new club post",
                        "query_type" => 1,
                        "custom_type" => "club",
                        "custom_type_sub" => "post",
                        "point_code" => "",
                        "point_get_code" => "E19",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    2 => array(
                        "title" => "Photo",
                        "clicked" => "new club post",
                        "query_type" => 1,
                        "custom_type" => "club",
                        "custom_type_sub" => "upload image",
                        "point_code" => "",
                        "point_get_code" => "E20",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    3 => array(
                        "title" => "Video",
                        "clicked" => "new club post",
                        "query_type" => 1,
                        "custom_type" => "club",
                        "custom_type_sub" => "upload video",
                        "point_code" => "",
                        "point_get_code" => "E21",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    4 => array(
                        "title" => "Attendance Sheet",
                        "clicked" => "new club post",
                        "query_type" => 1,
                        "custom_type" => "club",
                        "custom_type_sub" => "attendance sheet",
                        "point_code" => "",
                        "point_get_code" => "E22",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    5 => array(
                        "title" => "Location",
                        "clicked" => "new club post",
                        "query_type" => 1,
                        "custom_type" => "club",
                        "custom_type_sub" => "location",
                        "point_code" => "",
                        "point_get_code" => "E23",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    6 => array(
                        "title" => "Like",
                        "clicked" => "club",
                        "query_type" => 1,
                        "custom_type" => "club",
                        "custom_type_sub" => "like",
                        "point_code" => "",
                        "point_get_code" => "E24",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    7 => array(
                        "title" => "Reply",
                        "clicked" => "club",
                        "query_type" => 1,
                        "custom_type" => "club",
                        "custom_type_sub" => "reply",
                        "point_code" => "",
                        "point_get_code" => "E25",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    8 => array(
                        "title" => "Watched video",
                        "clicked" => "club",
                        "query_type" => 1,
                        "custom_type" => "club",
                        "custom_type_sub" => "watch video",
                        "point_code" => "",
                        "point_get_code" => "E27",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    9 => array(
                        "title" => "Invite member",
                        "clicked" => "club",
                        "query_type" => 1,
                        "custom_type" => "club",
                        "custom_type_sub" => "invite member join club",
                        "point_code" => "",
                        "point_get_code" => "E28",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    10 => array(
                        "title" => "Member join",
                        "clicked" => "club",
                        "query_type" => 1,
                        "custom_type" => "club",
                        "custom_type_sub" => "member join club",
                        "point_code" => "",
                        "point_get_code" => "E29",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    ),
                    11 => array(
                        "title" => "Member activity",
                        "clicked" => "club",
                        "query_type" => 1,
                        "custom_type" => "club",
                        "custom_type_sub" => "commission",
                        "point_code" => "",
                        "point_get_code" => "E32",
                        "point_text_type" => 1,
                        "show_count" => "no"
                    )
                )
            );
        }

        // //community default variable
        // $countTotal = 0;
        // $pointTotalPost = 0;
        // $pointTotalPostAndImage = 0;
        // $pointTotalPostAndVideo = 0;
        foreach($isiArray AS $key => $isis){
            foreach($isis AS $isi){
                $sumAccomplishment = 0;
                $countAccomplishment = 0;
                $limit = 0;

                //general
                if($isi["query_type"] == 1){
                    $sumAccomplishment = $this->glphm->sumAccomplishment($nation_code, "", "", "", "", $pelanggan->id, "", "", $isi["custom_type"], $isi["custom_type_sub"], $dateCompare);

                    $countAccomplishment = $this->glphm->countAccomplishment($nation_code, "", "", "", "", $pelanggan->id, "", "", $isi["custom_type"], $isi["custom_type_sub"], $dateCompare);

                    if($isi["point_code"] != ""){
                        $limit = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $isi["point_code"])->remark;

                        $data["point_max"] += $limit * $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $isi["point_get_code"])->remark;
                    }

                    if($isi["custom_type"] == "check in"){
                        $limit = 1;
                    }
                //make an offer
                }else if($isi["query_type"] == 2){
                    //normal product
                    $limit = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EQ")->remark;

                    $sumAccomplishment = $this->glphm->sumAccomplishmentOfferReview($nation_code, "", "", "", "", $pelanggan->id, "+", "", $isi["custom_type"], "review", $dateCompare, $limit);

                    $countAccomplishment = $this->glphm->countAccomplishmentOfferReview($nation_code, "", "", "", "", $pelanggan->id, "+", "", $isi["custom_type"], "review", $dateCompare, $limit);

                    //free product
                    $limit = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "ES")->remark;

                    $sumAccomplishment += $this->glphm->sumAccomplishmentOfferReview($nation_code, "", "", "", "", $pelanggan->id, "+", "", $isi["custom_type"], "review free product", $dateCompare, $limit);

                    $countAccomplishment += $this->glphm->countAccomplishmentOfferReview($nation_code, "", "", "", "", $pelanggan->id, "+", "", $isi["custom_type"], "review free product", $dateCompare, $limit);

                    $limit = 0;
                //invite
                }else if($isi["query_type"] == 3){
                    $countAccomplishment = $this->bu->countRecruited($nation_code, $pelanggan->id, $dateCompare);

                    $sumAccomplishment = $countAccomplishment * $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $isi["point_code"])->remark;
                //sign up
                }else if($isi["query_type"] == 4){
                    $checkReferralManual = $this->glphm->countAll($nation_code, "", "", "", "", $pelanggan->id, "+", "", "input referral manual", "recommendee", $dateCompare, "");
                    if($checkReferralManual != "0"){
                        $sumAccomplishment = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E5")->remark;
                    }else if(date("Y-m-d", strtotime($pelanggan->cdate)) != $dateCompare){
                        continue;
                    }

                    if($pelanggan->b_user_id_recruiter == '0'){
                        $sumAccomplishment = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E4")->remark;
                    }else if($pelanggan->b_user_id_recruiter != '0' && $checkReferralManual == "0"){
                        $sumAccomplishment = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EY")->remark;
                    }

                    $data["point_max"] += $sumAccomplishment;

                    $countAccomplishment = 1;

                    $limit = 1;
                //first post community
                }else if($isi["query_type"] == 5){
                    //get total community post
                    $totalPostNow = $this->ccomm->countAllByUserId($nation_code, $pelanggan->id);
                    if($totalPostNow > 1){
                        continue;
                    }

                    $postData = $this->ccomm->getAll($nation_code, 1, 1, "cc.cdate", "ASC", '', array(), "", array(), $pelanggan->id, array(), array(), array(), $pelanggan->language_id);
                    if($postData){
                        if(date("Y-m-d", strtotime($postData[0]->cdate)) == $dateCompare){
                            $sumAccomplishment = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EF")->remark;

                            $countAccomplishment = 1;

                            $limit = 1;
                        }else{
                            continue;
                        }
                    }else{
                        $sumAccomplishment = 0;

                        $countAccomplishment = 0;

                        $limit = 1;
                    }

                    $data["point_max"] -= $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EG")->remark;

                    $data["point_max"] += $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $isi["point_get_code"])->remark;
                //product & community
                }else if($isi["query_type"] == 6){
                    $limit = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $isi["point_code"])->remark;

                    // if($isi["custom_type_sub"] == "post"){
                    //     $post = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EG")->remark;
                    //     $image = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E13")->remark;
                    //     $video = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EP")->remark;
                    //     $tempId = "";
                    //     $pointTotal = 0;
                    //     $dataCommunity = $this->glphm->dataCommunity($nation_code, $pelanggan->id, $dateCompare);
                    //     foreach($dataCommunity AS $value){
                    //         if($tempId != ""){
                    //             if($tempId != $value->custom_id){
                    //                 if($pointTotal == $post){
                    //                     $pointTotalPost += $pointTotal;
                    //                 }else if($pointTotal == $image){
                    //                     $pointTotalPostAndImage += $pointTotal;
                    //                 }else if($pointTotal == $video){
                    //                     $pointTotalPostAndVideo += $pointTotal;
                    //                 }

                    //                 $countTotal += 1;
                    //                 $pointTotal = 0;
                    //                 $tempId = $value->custom_id;
                    //             }
                    //         }

                    //         if($tempId == ""){
                    //             $tempId = $value->custom_id;
                    //         }

                    //         $pointTotal += $value->point;
                    //     }

                    //     if($tempId != ""){
                    //         if($pointTotal == $post){
                    //             $pointTotalPost += $pointTotal;
                    //         }else if($pointTotal == $image){
                    //             $pointTotalPostAndImage += $pointTotal;
                    //         }else if($pointTotal == $video){
                    //             $pointTotalPostAndVideo += $pointTotal;
                    //         }
                    //         $countTotal += 1;
                    //     }
                    //     $sumAccomplishment = $pointTotalPost;
                    //     $countAccomplishment = $countTotal;
                    // }else if($isi["custom_type_sub"] == "upload image"){
                    //     $sumAccomplishment = $pointTotalPostAndImage;
                    //     $countAccomplishment = $countTotal;
                    // }else if($isi["custom_type_sub"] == "upload video"){
                    //     $sumAccomplishment = $pointTotalPostAndVideo;
                    //     $countAccomplishment = $countTotal;
                    // }

                    $sumAccomplishment = $this->glphm->sumAccomplishment($nation_code, "", "", "", "", $pelanggan->id, "", "", $isi["custom_type"], $isi["custom_type_sub"], $dateCompare);

                    $countAccomplishment = $this->glphm->countAccomplishment($nation_code, "", "", "", "", $pelanggan->id, "", "", $isi["custom_type"], $isi["custom_type_sub"], $dateCompare);
                //create club
                }else if($isi["query_type"] == 7){
                    $countAccomplishment = $this->glphm->countCreateClub($nation_code, $pelanggan->id, "club", "create club");

                    $sumAccomplishment = $countAccomplishment * $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $isi["point_get_code"])->remark;

                    $limit = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $isi["point_code"])->remark;

                    $data["point_max"] += $limit * $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $isi["point_get_code"])->remark;
                }

                $data["point_get"] += $sumAccomplishment;

                if($isi["point_text_type"] == 1){
                    if($countAccomplishment > $limit){
                        $countAccomplishment = $limit;
                    }

                    $data['accomplishment'][$key]["data"][] = array(
                        "title" => $isi["title"],
                        "clicked" => $isi["clicked"],
                        "point" => (string) $sumAccomplishment,
                        "gain" => (string) $countAccomplishment,
                        "limit" => (string) $limit,
                        "show_count" => $isi["show_count"]
                    );
                }
            }
        }

        //check in
        $posisiArray = array();
        foreach($data['accomplishment'][0]["data"] AS $key => $value){
            if($value["clicked"] == "check in"){
                $posisiArray[] = $key;
            }
        }
        unset($key, $value);

        $tempArray = array();
        $firstKey = 0;
        $gain = 0;
        $limit = 0;
        foreach($posisiArray AS $value){
            $tempArray[] = $data['accomplishment'][0]["data"][$value];

            $gain += $data['accomplishment'][0]["data"][$value]["gain"];

            $limit += $data['accomplishment'][0]["data"][$value]["limit"];

            if($firstKey == 0){
                $firstKey = $value;
            }else{
                unset($data['accomplishment'][0]["data"][$value]);
            }
        }
        unset($value);

        $data['accomplishment'][0]["data"][$firstKey]["title"] = "Check-In";
        $data['accomplishment'][0]["data"][$firstKey]["gain"] = (string) $gain;
        $data['accomplishment'][0]["data"][$firstKey]["limit"] = (string) $limit;
        $data['accomplishment'][0]["data"][$firstKey]["detail"] = $tempArray;


        //community
        $posisiArray = array();
        foreach($data['accomplishment'][1]["data"] AS $key => $value){
            if($value["clicked"] == "new post"){
                $posisiArray[] = $key;
            }
        }
        unset($key, $value);

        $tempArray = array();
        $firstKey = 0;
        $gain = 0;
        $limit = 0;
        foreach($posisiArray AS $value){
            $tempArray[] = $data['accomplishment'][1]["data"][$value];

            if($value == 0){
                // $firstKey = $value;
                $gain = $data['accomplishment'][1]["data"][$value]["gain"];

                $limit = $data['accomplishment'][1]["data"][$value]["limit"];
            }else{
                unset($data['accomplishment'][1]["data"][$value]);
            }
        }
        unset($value);

        if($pelanggan->language_id == 2){
            $data['accomplishment'][1]["data"][$firstKey]["title"] = "Postingan";
        }else{
            $data['accomplishment'][1]["data"][$firstKey]["title"] = "Post";
        }
        $data['accomplishment'][1]["data"][$firstKey]["gain"] = (string) $gain;
        $data['accomplishment'][1]["data"][$firstKey]["limit"] = (string) $limit;
        $data['accomplishment'][1]["data"][$firstKey]["detail"] = $tempArray;

        $data['accomplishment'][1]["data"] = array_values($data['accomplishment'][1]["data"]);


        //product
        $posisiArray = array();
        foreach($data['accomplishment'][2]["data"] AS $key => $value){
            if($value["clicked"] == "add product"){
                $posisiArray[] = $key;
            }
        }
        unset($key, $value);

        $tempArray = array();
        $firstKey = 0;
        $gain = 0;
        $limit = 0;
        foreach($posisiArray AS $value){
            $tempArray[] = $data['accomplishment'][2]["data"][$value];

            if($value == 0){
                // $firstKey = $value;
                $gain = $data['accomplishment'][2]["data"][$value]["gain"];

                $limit = $data['accomplishment'][2]["data"][$value]["limit"];
            }else{
                unset($data['accomplishment'][2]["data"][$value]);
            }
        }
        unset($value);

        if($pelanggan->language_id == 2){
            $data['accomplishment'][2]["data"][$firstKey]["title"] = "Produk";
        }else{
            $data['accomplishment'][2]["data"][$firstKey]["title"] = "Product";
        }
        $data['accomplishment'][2]["data"][$firstKey]["gain"] = (string) $gain;
        $data['accomplishment'][2]["data"][$firstKey]["limit"] = (string) $limit;
        $data['accomplishment'][2]["data"][$firstKey]["detail"] = $tempArray;

        $data['accomplishment'][2]["data"] = array_values($data['accomplishment'][2]["data"]);


        //club
        $posisiArray = array();
        foreach($data['accomplishment'][3]["data"] AS $key => $value){
            if($value["clicked"] == "new club post"){
                $posisiArray[] = $key;
            }
        }
        unset($key, $value);

        $tempArray = array();
        $firstKey = 1;
        $gain = 0;
        $limit = 0;
        foreach($posisiArray AS $value){
            $tempArray[] = $data['accomplishment'][3]["data"][$value];

            if($value == 1){
                // $firstKey = $value;
                $gain = $data['accomplishment'][3]["data"][$value]["gain"];

                $limit = $data['accomplishment'][3]["data"][$value]["limit"];
            }else{
                unset($data['accomplishment'][3]["data"][$value]);
            }
        }
        unset($value);

        if($pelanggan->language_id == 2){
            $data['accomplishment'][3]["data"][$firstKey]["title"] = "Postingan";
        }else{
            $data['accomplishment'][3]["data"][$firstKey]["title"] = "Post";
        }
        $data['accomplishment'][3]["data"][$firstKey]["gain"] = (string) $gain;
        $data['accomplishment'][3]["data"][$firstKey]["limit"] = (string) $limit;
        $data['accomplishment'][3]["data"][$firstKey]["detail"] = $tempArray;

        $data['accomplishment'][3]["data"] = array_values($data['accomplishment'][3]["data"]);


        $listCodeForPointMax = array(
            "EZ",
            "E1",
            "E19",
            "E20",
            "E21",
            "E22",
            "E23",
            "E24",
            "E25",
            "E27",
            "E28",
            "E29"
        );

        foreach($listCodeForPointMax AS $code){
            $data["point_max"] += $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $code)->remark;
        }

        $data["point_get"] = (string) $data["point_get"];

        $data["point_max"] = (string) $data["point_max"];

        //response message
        $this->status = 200;
        $this->message = 'Success';

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "activity_dashboard");
    }

    public function reward_chart()
    {
        //default result
        $data = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "activity_dashboard");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "activity_dashboard");
            die();
        }

        //check apisess
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

        if($pelanggan->language_id == 2){
            $data[] = array(
                "title" => "Daftar",
                "data" => array()
            );
            $data[] = array(
                "title" => "Check-In",
                "data" => array()
            );
            $data[] = array(
                "title" => "Komunitas",
                "data" => array()
            );
            $data[] = array(
                "title" => "Beli & Jual",
                "data" => array()
            );
            $data[] = array(
                "title" => "Klub",
                "data" => array()
            );

            $isiArray = array(
                0 => array(
                    0 => array(
                        "title" => "Daftar",
                        "point" => "E4",
                        "text" => "Terbatas untuk pertama kali"
                    ),
                    1 => array(
                        "title" => "Undangan",
                        "point" => "EZ",
                        "text" => "Tidak Terbatas"
                    )
                ),
                1 => array(
                    0 => array(
                        "title" => "Check-In(Harian)",
                        "point" => "E1",
                        "text" => "Sekali sehari"
                    ),
                    1 => array(
                        "title" => "Check-In(Mingguan)",
                        "point" => "E2",
                        "text" => "Sekali seminggu"
                    ),
                    2 => array(
                        "title" => "Check-In(Bulanan)",
                        "point" => "E3",
                        "text" => "Sekali sebulan"
                    )
                ),
                2 => array(
                    // 0 => array(
                    //     "title" => "Postingan Pertama",
                    //     "point" => "EF",
                    //     "text" => "Terbatas untuk pertama kali"
                    // ),
                    0 => array(
                        "title" => "Postingan(kata)",
                        "point" => "EG",
                        "text" => "40 kali sehari*"
                    ),
                    1 => array(
                        "title" => "Postingan(foto)",
                        "point" => "E13",
                        "text" => "40 kali sehari*"
                    ),
                    2 => array(
                        "title" => "Postingan(video)",
                        "point" => "EP",
                        "text" => "40 kali sehari*"
                    ),
                    3 => array(
                        "title" => "Balasan",
                        "point" => "EI",
                        "text" => "10 kali sehari"
                    ),
                    // 2 => array(
                    //     "title" => "Suka",
                    //     "point" => "EK",
                    //     "text" => "20 kali sehari"
                    // ),
                    // 2 => array(
                    //     "title" => "Video",
                    //     "point" => "EP",
                    //     "text" => "40 kali sehari"
                    // )
                ),
                3 => array(
                    0 => array(
                        "title" => "Produk",
                        "point" => "EA",
                        "text" => "40 kali sehari"
                    ),
                    // 1 => array(
                    //     "title" => "Buat Tawaran",
                    //     "point" => "EQ",
                    //     "text" => "Tidak Terbatas"
                    // ),
                    // 2 => array(
                    //     "title" => "Pesanan Proteksi",
                    //     "point" => "EU",
                    //     "text" => "Tidak Terbatas"
                    // ),
                    1 => array(
                        "title" => "Video",
                        "point" => "EO",
                        "text" => "40 kali sehari"
                    )
                ),
                4 => array(
                    0 => array(
                        "title" => "Buat club",
                        "point" => "E17",
                        "text" => "10 kali sebulan"
                    ),
                    1 => array(
                        "title" => "Postingan(kata)",
                        "point" => "E19",
                        "text" => "Tidak Terbatas"
                    ),
                    2 => array(
                        "title" => "Postingan(foto)",
                        "point" => "E20",
                        "text" => "Tidak Terbatas"
                    ),
                    3 => array(
                        "title" => "Postingan(video)",
                        "point" => "E21",
                        "text" => "Tidak Terbatas"
                    ),
                    4 => array(
                        "title" => "Postingan(lembar kehadiran)",
                        "point" => "E22",
                        "text" => "Tidak Terbatas"
                    ),
                    5 => array(
                        "title" => "Postingan(lokasi)",
                        "point" => "E23",
                        "text" => "Tidak Terbatas"
                    ),
                    6 => array(
                        "title" => "Suka",
                        "point" => "E24",
                        "text" => "Tidak Terbatas"
                    ),
                    7 => array(
                        "title" => "Balasan",
                        "point" => "E25",
                        "text" => "Tidak Terbatas"
                    ),
                    8 => array(
                        "title" => "Video ditonton",
                        "point" => "E27",
                        "text" => "Tidak Terbatas"
                    ),
                    9 => array(
                        "title" => "Undang Anggota",
                        "point" => "E28",
                        "text" => "Tidak Terbatas"
                    ),
                    10 => array(
                        "title" => "Orang masuk",
                        "point" => "E29",
                        "text" => "Tidak Terbatas"
                    ),
                    11 => array(
                        "title" => "Aktivitas member",
                        "point" => "E32",
                        "text" => "Tidak Terbatas"
                    )
                )
            );
        }else{
            $data[] = array(
                "title" => "Sign Up",
                "data" => array()
            );
            $data[] = array(
                "title" => "Check-In",
                "data" => array()
            );
            $data[] = array(
                "title" => "Community",
                "data" => array()
            );
            $data[] = array(
                "title" => "Buy & Sell",
                "data" => array()
            );
            $data[] = array(
                "title" => "Club",
                "data" => array()
            );

            $isiArray = array(
                0 => array(
                    0 => array(
                        "title" => "Sign Up",
                        "point" => "E4",
                        "text" => "Limited to the first time"
                    ),
                    1 => array(
                        "title" => "Invite",
                        "point" => "EZ",
                        "text" => "No Limit"
                    )
                ),
                1 => array(
                    0 => array(
                        "title" => "Check-In(Daily)",
                        "point" => "E1",
                        "text" => "Once a day"
                    ),
                    1 => array(
                        "title" => "Check-In(Weekly)",
                        "point" => "E2",
                        "text" => "Once a week"
                    ),
                    2 => array(
                        "title" => "Check-In(Monthly)",
                        "point" => "E3",
                        "text" => "Once a month"
                    )
                ),
                2 => array(
                    // 0 => array(
                    //     "title" => "First Post",
                    //     "point" => "EF",
                    //     "text" => "Limited to the first time"
                    // ),
                    0 => array(
                        "title" => "Posts(text)",
                        "point" => "EG",
                        "text" => "40 times a day*"
                    ),
                    1 => array(
                        "title" => "Posts(image)",
                        "point" => "E13",
                        "text" => "40 times a day*"
                    ),
                    2 => array(
                        "title" => "Posts(video)",
                        "point" => "EP",
                        "text" => "40 times a day*"
                    ),
                    3 => array(
                        "title" => "Reply",
                        "point" => "EI",
                        "text" => "10 times a day"
                    ),
                    // 2 => array(
                    //     "title" => "Like",
                    //     "point" => "EK",
                    //     "text" => "20 times a day"
                    // ),
                    // 2 => array(
                    //     "title" => "Video",
                    //     "point" => "EP",
                    //     "text" => "40 times a day"
                    // )
                ),
                3 => array(
                    0 => array(
                        "title" => "Product",
                        "point" => "EA",
                        "text" => "40 times a day"
                    ),
                    // 1 => array(
                    //     "title" => "Make an Offer",
                    //     "point" => "EQ",
                    //     "text" => "No Limit"
                    // ),
                    // 2 => array(
                    //     "title" => "Protection order",
                    //     "point" => "EU",
                    //     "text" => "No Limit"
                    // ),
                    1 => array(
                        "title" => "Video",
                        "point" => "EO",
                        "text" => "40 times a day"
                    )
                ),
                4 => array(
                    0 => array(
                        "title" => "Create club",
                        "point" => "E17",
                        "text" => "10 times a month"
                    ),
                    1 => array(
                        "title" => "Posts(text)",
                        "point" => "E19",
                        "text" => "No Limit"
                    ),
                    2 => array(
                        "title" => "Posts(image)",
                        "point" => "E20",
                        "text" => "No Limit"
                    ),
                    3 => array(
                        "title" => "Posts(video)",
                        "point" => "E21",
                        "text" => "No Limit"
                    ),
                    4 => array(
                        "title" => "Posts(attendance sheet)",
                        "point" => "E22",
                        "text" => "No Limit"
                    ),
                    5 => array(
                        "title" => "Posts(location)",
                        "point" => "E23",
                        "text" => "No Limit"
                    ),
                    6 => array(
                        "title" => "Like",
                        "point" => "E24",
                        "text" => "No Limit"
                    ),
                    7 => array(
                        "title" => "Reply",
                        "point" => "E25",
                        "text" => "No Limit"
                    ),
                    8 => array(
                        "title" => "Watched video",
                        "point" => "E27",
                        "text" => "No Limit"
                    ),
                    9 => array(
                        "title" => "Invite member",
                        "point" => "E28",
                        "text" => "No Limit"
                    ),
                    10 => array(
                        "title" => "Member join",
                        "point" => "E29",
                        "text" => "No Limit"
                    ),
                    11 => array(
                        "title" => "Member activity",
                        "point" => "E32",
                        "text" => "No Limit"
                    )
                )
            );

        }

        foreach($isiArray AS $key => $d){
            foreach($d AS $key2 => $value){
                $data[$key]["data"][$key2] = $value;
                if($data[$key]["data"][$key2]["point"] == "E13" || $data[$key]["data"][$key2]["point"] == "EP"){
                    $data[$key]["data"][$key2]["point"] = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "EG")->remark+$this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $data[$key]["data"][$key2]["point"])->remark." SPT";
                }else if($data[$key]["data"][$key2]["point"] == "E32"){
                    $data[$key]["data"][$key2]["point"] = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $data[$key]["data"][$key2]["point"])->remark."%";
                }else{
                    $data[$key]["data"][$key2]["point"] = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $data[$key]["data"][$key2]["point"])->remark." SPT";
                }
            }
        }

        //response message
        $this->status = 200;
        $this->message = 'Success';

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "activity_dashboard");
    }

}
