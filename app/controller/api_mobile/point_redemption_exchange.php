<?php
class Point_redemption_exchange extends JI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_mobile/b_user_model", 'bu');
        // $this->load("api_mobile/b_user_alamat_model", "bua");
        $this->load("api_mobile/common_code_model", 'ccm');
        // $this->load("api_mobile/g_leaderboard_point_history_model", 'glphm');
        $this->load("api_mobile/h_point_redemption_exchange_list_model", 'hprelm');
        $this->load("api_mobile/h_point_redemption_exchange_model", 'hprem');
        $this->load("api_mobile/h_point_redemption_exchange_history_model", 'hprehm');
        $this->load("api_mobile/g_leaderboard_point_total_model", "glptm");
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

    private function __statusTextCustom($status, $pelanggan){
        if($status == "request exchange"){
            $status = "ongoing";
        }

        if($status == "rejected by system"){
            $status = "rejected(insufficient balance-1st)";
        }

        if($status == "approved by admin"){
            $status = "accepted by admin";
        }

        if($status == "insufficient wallet balance"){
            $status = "accepted(insufficient balance-2nd)";
        }

        if($status == "wallet balance deducted"){
            $status = "ongoing(balance deducted)";
        }

        if($status == "top up problem"){
            $status = "fail(top-up problem)";
        }

        if($status == "wallet balance refunded"){
            $status = "fail(balance refunded)";
        }

        if($status == "success"){
            $status = "success(final)";
        }

        if($pelanggan->language_id == 2){
            if($status == "ongoing"){
                $status = "sedang diproses";
            }

            if($status == "rejected by admin"){
                $status = "ditolak admin";
            }

            if($status == "rejected(insufficient balance-1st)"){
                $status = "ditolak(saldo tidak cukup-1st)";
            }

            if($status == "accepted by admin"){
                $status = "disetujui admin";
            }

            if($status == "accepted(insufficient balance-2nd)"){
                $status = "disetujui(saldo tidak cukup-2nd)";
            }

            if($status == "ongoing(balance deducted)"){
                $status = "sedang diproses(saldo telah dikurangi)";
            }

            if($status == "fail(top-up problem)"){
                $status = "gagal(isi ulang bermasalah)";
            }

            if($status == "fail(balance refunded)"){
                $status = "gagal(saldo telah dikembalikan)";
            }

            if($status == "success(final)"){
                $status = "berhasil(final)";
            }
        }

        return ucfirst($status);
    }

    private function __sortCol($sort_col, $tbl_as)
    {
        switch ($sort_col) {
          case 'cdate':
          $sort_col = "$tbl_as.cdate";
          break;
          default:
          $sort_col = "$tbl_as.cdate";
        }
        return $sort_col;
    }

    private function __sortDir($sort_dir)
    {
        $sort_dir = strtolower($sort_dir);
        if ($sort_dir == "asc") {
          $sort_dir = "ASC";
        } else {
          $sort_dir = "DESC";
        }
        return $sort_dir;
    }

    private function __page($page)
    {
        if (!is_int($page)) {
          $page = (int) $page;
        }
        if ($page<=0) {
          $page = 1;
        }
        return $page;
    }

    private function __pageSize($page_size)
    {
        $page_size = (int) $page_size;
        if ($page_size<=0) {
          $page_size = 10;
        }
        return $page_size;
    }

    public function index()
    {
        //default result
        $data = array();
        $data["list"] = array();

        //response message
        $this->status = 200;
        $this->message = 'Success';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $postData = $this->apikeyDecrypt($nation_code, $data, $this->input->post('samsung'), $this->input->post('nvidia'), $this->input->post('fullhd'));
        if ($postData === false) {
            $this->status = 1750;
            $this->message = 'Please check your data again';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        $postData = json_decode($postData);

        $listOfPostData = array(
            "apikey",
            "apisess",
            "sort_col",
            "sort_dir",
            "page",
            "page_size"
        );

        foreach($listOfPostData as $value) {
            if(!isset($postData->$value)){
                $postData->$value = "";
            }
        }

        //check apikey
        $apikey = $postData->apikey;
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $postData->apisess;
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
          $this->status = 401;
          $this->message = 'Missing or invalid API session';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
          die();
        }

        $sort_col = $postData->sort_col;
        $sort_dir = $postData->sort_dir;
        $page = $postData->page;
        $page_size = $postData->page_size;

        $tbl_as = $this->hprem->getTblAs();
        $sort_col = $this->__sortCol($sort_col, $tbl_as);
        $sort_dir = $this->__sortDir($sort_dir);
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);

        $data["list"] = $this->hprem->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $pelanggan->id);
        foreach($data["list"] as &$value){
            // if($value->type == "credit phone"){
            //     if($pelanggan->language_id == 2){
            //         $value->title = "Tukar SPT ke Pulsa Prabayar";
            //     }else{
            //         $value->title = "Exchange SPT to Prepaid Phone Credit";
            //     }
            // }

            // if($value->type == "electricity token"){
            //     if($pelanggan->language_id == 2){
            //         $value->title = "Tukar SPT ke Token Listrik";
            //     }else{
            //         $value->title = "Exchange SPT to Electricity Token";
            //     }
            // }

            $value->cost_spt .= " SPT";

            if($value->status == "wallet balance deducted" || $value->status == "top up problem" || $value->status == "success"){
                $value->cost_spt = "-".$value->cost_spt;
            }else if($value->status == "wallet balance refunded"){
                $value->cost_spt = "+".$value->cost_spt;
            }else{
                $value->cost_spt = "";
            }

            $value->status = $this->__statusTextCustom($value->status, $pelanggan);
            $value->status_for_mobile = $value->status;
            $value->status .= "(".$value->telp.")";

            if($pelanggan->language_id == 2) {
                $cdate = $value->cdate;
                $cdate = $this->__dateIndonesia($value->cdate, 'tanggal_jam');
                $value->cdate = str_replace(" WIB", date(":s", strtotime($value->cdate)), $cdate);
            }else{
                $cdate = $value->cdate;
                $value->cdate = $this->__dateEnglish($value->cdate, 'tanggal_jam');
                $value->cdate = str_replace(" WIB", date(":s", strtotime($value->cdate)), $cdate);
            }

            // $value->icon = str_replace("//", "/", $value->icon);
            // $value->icon = $this->cdn_url($value->icon);
        }

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "point_redemption_exchange");
    }

    public function detail()
    {
        //default result
        $data = array();
        $data["detail"] = array();

        //response message
        $this->status = 200;
        $this->message = 'Success';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $postData = $this->apikeyDecrypt($nation_code, $data, $this->input->post('samsung'), $this->input->post('nvidia'), $this->input->post('fullhd'));
        if ($postData === false) {
            $this->status = 1750;
            $this->message = 'Please check your data again';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        $postData = json_decode($postData);

        $listOfPostData = array(
            "apikey",
            "apisess",
            "detail_id"
        );

        foreach($listOfPostData as $value) {
            if(!isset($postData->$value)){
                $postData->$value = "";
            }
        }

        //check apikey
        $apikey = $postData->apikey;
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $postData->apisess;
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
          $this->status = 401;
          $this->message = 'Missing or invalid API session';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
          die();
        }

        $detail_id = $postData->detail_id;
        $data["detail"] = $this->hprem->getById($nation_code, $detail_id, $pelanggan->id);
        if($detail_id == "0" || !isset($data["detail"]->id)){
            $this->status = 1750;
            $this->message = 'Please check your data again';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        // if($data["detail"]->type == "credit phone"){
        //     if($pelanggan->language_id == 2){
        //         $data["detail"]->title = "Tukar SPT ke Pulsa Prabayar";
        //     }else{
        //         $data["detail"]->title = "Exchange SPT to Prepaid Phone Credit";
        //     }
        // }

        // if($data["detail"]->type == "electricity token"){
        //     if($pelanggan->language_id == 2){
        //         $data["detail"]->title = "Tukar SPT ke Token Listrik";
        //     }else{
        //         $data["detail"]->title = "Exchange SPT to Electricity Token";
        //     }
        // }

        $data["detail"]->cost_spt .= " SPT";

        if($data["detail"]->status == "wallet balance deducted" || $data["detail"]->status == "top up problem" || $data["detail"]->status == "success"){
            $data["detail"]->cost_spt = "-".$data["detail"]->cost_spt;
        }else if($data["detail"]->status == "wallet balance refunded"){
            $data["detail"]->cost_spt = "+".$data["detail"]->cost_spt;
        }else{
            $data["detail"]->cost_spt = "";
        }

        $data["detail"]->status = $this->__statusTextCustom($data["detail"]->status, $pelanggan);
        $data["detail"]->status .= "(".$data["detail"]->telp.")";

        if($pelanggan->language_id == 2) {
            $cdate = $value->cdate;
            $data["detail"]->cdate = $this->__dateIndonesia($data["detail"]->cdate, 'tanggal_jam');
            $value->cdate = str_replace(" WIB", date(":s", strtotime($value->cdate)), $cdate);
        }else{
            $cdate = $value->cdate;
            $data["detail"]->cdate = $this->__dateEnglish($data["detail"]->cdate, 'tanggal_jam');
            $value->cdate = str_replace(" WIB", date(":s", strtotime($value->cdate)), $cdate);
        }

        // $data["detail"]->icon = str_replace("//", "/", $data["detail"]->icon);
        // $data["detail"]->icon = $this->cdn_url($data["detail"]->icon);

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "point_redemption_exchange");
    }

    public function statusdetail()
    {
        //default result
        $data = array();
        $data["detail"] = array();

        //response message
        $this->status = 200;
        $this->message = 'Success';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $postData = $this->apikeyDecrypt($nation_code, $data, $this->input->post('samsung'), $this->input->post('nvidia'), $this->input->post('fullhd'));
        if ($postData === false) {
            $this->status = 1750;
            $this->message = 'Please check your data again';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        $postData = json_decode($postData);

        $listOfPostData = array(
            "apikey",
            "apisess",
            "detail_id"
        );

        foreach($listOfPostData as $value) {
            if(!isset($postData->$value)){
                $postData->$value = "";
            }
        }

        //check apikey
        $apikey = $postData->apikey;
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $postData->apisess;
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
          $this->status = 401;
          $this->message = 'Missing or invalid API session';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
          die();
        }

        $detail_id = $postData->detail_id;
        $data["detail"] = $this->hprehm->getAll($nation_code, $detail_id);
        if($detail_id == "0" || count($data["detail"]) == 0){
            $this->status = 1750;
            $this->message = 'Please check your data again';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        foreach($data["detail"] as &$value){
            $value->status = $this->__statusTextCustom($value->status, $pelanggan);

            if($pelanggan->language_id == 2) {
                $cdate = $value->cdate;
                $cdate = $this->__dateIndonesia($value->cdate, 'tanggal_jam');
                $value->cdate = str_replace(" WIB", date(":s", strtotime($value->cdate)), $cdate);
            }else{
                $cdate = $value->cdate;
                $value->cdate = $this->__dateEnglish($value->cdate, 'tanggal_jam');
                $value->cdate = str_replace(" WIB", date(":s", strtotime($value->cdate)), $cdate);
            }
        }

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "point_redemption_exchange");
    }

    public function shop()
    {
        //default result
        $data = array();
        $data["balance"] = '0';
        $data["eligible_to_buy"] = 'no';
        $data["eligible_to_buy_message"] = '';
        $data["list_shop"] = array();

        //response message
        $this->status = 200;
        $this->message = 'Success';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $postData = $this->apikeyDecrypt($nation_code, $data, $this->input->post('samsung'), $this->input->post('nvidia'), $this->input->post('fullhd'));
        if ($postData === false) {
            $this->status = 1750;
            $this->message = 'Please check your data again';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        $postData = json_decode($postData);

        $listOfPostData = array(
            "apikey",
            "apisess"
        );

        foreach($listOfPostData as $value) {
            if(!isset($postData->$value)){
                $postData->$value = "";
            }
        }

        //check apikey
        $apikey = $postData->apikey;
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $postData->apisess;
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
          $this->status = 401;
          $this->message = 'Missing or invalid API session';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
          die();
        }

        $getPointNow = $this->glptm->getByUserId($nation_code, $pelanggan->id);
        if(isset($getPointNow->b_user_id)){
            $data["balance"] = number_format($getPointNow->total_point, 0, ',', '.');
        }

        $totalUnfinishedTransaction = $this->hprem->countUnfinishedTransaction($nation_code, $pelanggan->id);
        if($totalUnfinishedTransaction == 0){
            $lastInsertedData = $this->hprem->getLastInserted($nation_code, $pelanggan->id);
            if(isset($lastInsertedData->cdate)){
                if(strtotime($lastInsertedData->cdate) > strtotime('-7 day')){
                    if($pelanggan->language_id == 2){
                        $data["eligible_to_buy_message"] = "Anda baru boleh redeem PULSA pada ".date("Y.m.d", strtotime($lastInsertedData->cdate." + 7 days")).".(Sekali dalam seminggu)";
                    }else{
                        $data["eligible_to_buy_message"] = "You're not allowed to redeem PULSA by ".date("Y.m.d", strtotime($lastInsertedData->cdate." + 7 days")).".(Only once a week)";
                    }
                }else{
                    $data["eligible_to_buy"] = 'yes';
                }
            }else{
                $data["eligible_to_buy"] = 'yes';
            }
        }else{
            if($pelanggan->language_id == 2){
                $data["eligible_to_buy_message"] = "anda tidak diperbolehkan untuk request karena sebelumnya sedang berjalan";
            }else{
                $data["eligible_to_buy_message"] = "Sorry, you can't request PULSA redemption at the moment, as your previous request is currently being processed.";
            }
        }

        $data["list_shop"] = $this->hprelm->getAll($nation_code);
        foreach($data["list_shop"] as &$value){
            $value->icon = str_replace("//", "/", $value->icon);
            $value->icon = $this->cdn_url($value->icon);
        }

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "point_redemption_exchange");
    }

    public function buy()
    {
        //default result
        $data = array();
        $data["response_message"] = "";

        //response message
        $this->status = 1001;
        $this->message = 'Balance is not enough';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $postData = $this->apikeyDecrypt($nation_code, $data, $this->input->post('samsung'), $this->input->post('nvidia'), $this->input->post('fullhd'));
        if ($postData === false) {
            $this->status = 1750;
            $this->message = 'Please check your data again';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        $postData = json_decode($postData);

        $listOfPostData = array(
            "apikey",
            "apisess",
            "shop_id",
            "telp"
        );

        foreach($listOfPostData as $value) {
            if(!isset($postData->$value)){
                $postData->$value = "";
            }
        }

        //check apikey
        $apikey = $postData->apikey;
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $postData->apisess;
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
          $this->status = 401;
          $this->message = 'Missing or invalid API session';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
          die();
        }

        $exchange_pulsa_config = $this->ccm->getByClassifiedAndCode($nation_code,"app_config","C22")->remark;
        if($exchange_pulsa_config == "off"){
            $this->status = 1007;
            $this->message = 'PULSA Reward closed';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "point_redemption_exchange");
            die();
        }

        $shop_id = $postData->shop_id;
        $shop_data = $this->hprelm->getById($nation_code, $shop_id);
        if($shop_id == "0" || !isset($shop_data->id)){
            $this->status = 1750;
            $this->message = 'Please check your data again';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        $telp = str_replace(" ", "", $postData->telp);
        if (!is_numeric($telp)) {
            $this->status = 1004;
            $this->message = "Please, enter only numerical digits for the cell phone number.";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "point_redemption_exchange");
            die();
        }

        if (substr($telp, 0, 2) != '08') {
            $this->status = 1005;
            $this->message = "Your cell phone number should begin with 08";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "point_redemption_exchange");
            die();
        }

        if (strlen($telp) < 7 || strlen($telp) > 13) {
            $this->status = 1006;
            $this->message = "Your cell phone number should be between 7 and 13 digits";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "point_redemption_exchange");
            die();
        }

        $totalUnfinishedTransaction = $this->hprem->countUnfinishedTransaction($nation_code, $pelanggan->id);
        if($totalUnfinishedTransaction != 0){
            $this->status = 1002;
            $this->message = "Sorry, you can't request PULSA redemption at the moment, as your previous request is currently being processed.";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "point_redemption_exchange");
            die();
        }

        $lastInsertedData = $this->hprem->getLastInserted($nation_code, $pelanggan->id);
        if(isset($lastInsertedData->cdate)){
            if(strtotime($lastInsertedData->cdate) > strtotime('-7 day')){
                $this->status = 1003;
                $this->message = "You're not allowed to redeem PULSA by ".date("Y.m.d", strtotime($lastInsertedData->cdate." + 7 days")).".(Only once a week)";
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "point_redemption_exchange");
                die();
            }
        }

        $getPointNow = $this->glptm->getByUserId($nation_code, $pelanggan->id);
        if(isset($getPointNow->b_user_id)){
            $pendingExchange = $this->hprem->sumPending($nation_code, $pelanggan->id);
            $pendingExchange += $shop_data->cost_spt;

            if($getPointNow->total_point >= $pendingExchange){
                $di = array();
                $di['nation_code'] = $nation_code;
                $di['h_point_redemption_exchange_list_id'] = $shop_data->id;
                $di['type'] = $shop_data->type;
                $di['cost_spt'] = $shop_data->cost_spt;
                $di['amount_get'] = $shop_data->amount_get;
                $di['name_point_history'] = $shop_data->name_point_history;
                $di['b_user_id'] = $pelanggan->id;
                $di['telp'] = $telp;
                $di['status'] = "request exchange";
                $endDoWhile = 0;
                do{
                  $di['id'] = $this->GUIDv4();
                  $checkId = $this->hprem->checkId($nation_code, $di['id']);
                  if($checkId == 0){
                      $endDoWhile = 1;
                  }
                }while($endDoWhile == 0);
                $this->hprem->set($di);

                $du = array();
                $du['nation_code'] = $nation_code;
                $du['h_point_redemption_exchange_id'] = $di['id'];
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

                //response message
                $this->status = 200;
                $this->message = 'Success';

                if($pelanggan->language_id == 2){
                    $data["response_message"] = "Permintaan anda telah diterima.\nTunggu 1-2 hari untuk mendapatkan PULSA.";
                }else{
                    $data["response_message"] = "Your request is accepted.\nPlease, wait 1-2 days to get PULSA on your phone.";
                }
            }
        }

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "point_redemption_exchange");
    }

    public function cancel()
    {
        //default result
        $data = array();
        // $data["detail"] = array();

        //response message
        $this->status = 200;
        $this->message = 'Success';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $postData = $this->apikeyDecrypt($nation_code, $data, $this->input->post('samsung'), $this->input->post('nvidia'), $this->input->post('fullhd'));
        if ($postData === false) {
            $this->status = 1750;
            $this->message = 'Please check your data again';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }
        $postData = json_decode($postData);

        $listOfPostData = array(
            "apikey",
            "apisess",
            "detail_id"
        );

        foreach($listOfPostData as $value) {
            if(!isset($postData->$value)){
                $postData->$value = "";
            }
        }

        //check apikey
        $apikey = $postData->apikey;
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $postData->apisess;
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
          $this->status = 401;
          $this->message = 'Missing or invalid API session';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
          die();
        }

        $detail_id = $postData->detail_id;
        $data["detail"] = $this->hprem->getById($nation_code, $detail_id, $pelanggan->id);
        if($detail_id == "0" || !isset($data["detail"]->id)){
            $this->status = 1750;
            $this->message = 'Please check your data again';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        if($data["detail"]->status != "request exchange"){
            $this->status = 1750;
            $this->message = 'Please check your data again';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        $this->hprem->del($nation_code, $detail_id, $pelanggan->id);
        $this->hprehm->del($nation_code, $detail_id);

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "point_redemption_exchange");
    }

    public function config()
    {
        //initial
        $dt = $this->__init();

        $this->status = 200;
        $this->message = 'Success';
        $data = array();
        $data["exchange_pulsa"] = "off";

        //check nation_code
        $nation_code = 62;
        // $nation_code = $this->input->get('nation_code');
        // $nation_code = $this->nation_check($nation_code);
        // if (empty($nation_code)) {
        //     $this->status = 101;
        //     $this->message = 'Missing or invalid nation_code';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        //     die();
        // }

        //check apikey
        // $apikey = $this->input->get('apikey');
        // $c = $this->apikey_check($apikey);
        // if (!$c) {
        //     $this->status = 400;
        //     $this->message = 'Missing or invalid API key';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        //     die();
        // }

        //check apisess
        // $apisess = $this->input->get('apisess');
        // $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        // if (!isset($pelanggan->id)) {
        //     $this->status = 401;
        //     $this->message = 'Missing or invalid API session';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
        //     die();
        // }

        $data["exchange_pulsa"] = $this->ccm->getByClassifiedAndCode($nation_code,"app_config","C22");
        if(isset($data["exchange_pulsa"]->remark)){
            $data["exchange_pulsa"] = $data["exchange_pulsa"]->remark;
        }else{
            $data["exchange_pulsa"] = "off";
        }

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "point_redemption_exchange");
    }
}
