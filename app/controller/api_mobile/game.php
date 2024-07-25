<?php
class Game extends JI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_mobile/b_user_model", 'bu');
        $this->load("api_mobile/b_user_alamat_model", "bua");
        $this->load("api_mobile/common_code_model", 'ccm');
        $this->load("api_mobile/g_leaderboard_point_history_model", 'glphm');
        $this->load("api_mobile/h_ticket_shop_model", 'htsm');
        $this->load("api_mobile/h_games_model", 'hgm');
        $this->load("api_mobile/h_ticket_history_model", 'hthm');
        $this->load("api_mobile/g_url", "gu");
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

    private function __yourfreeticket($game_data, $pelanggan)
    {
        if($game_data->name_point_history == "rock paper scissors"){
            $free_ticket = $pelanggan->free_ticket_rock_paper_scissors;
        }else if($game_data->name_point_history == "shooting fire"){
            $free_ticket = $pelanggan->free_ticket_shooting_fire;
        }else {
            $free_ticket = $pelanggan->free_ticket_rock_paper_scissors;
        }

        return $free_ticket;
    }

    private function __freeticketcolumnname($game_data)
    {
        if($game_data->name_point_history == "rock paper scissors"){
            $columnName = "free_ticket_rock_paper_scissors";
        }else if($game_data->name_point_history == "shooting fire"){
            $columnName = "free_ticket_shooting_fire";
        }else {
            $columnName = "free_ticket_rock_paper_scissors";
        }

        return $columnName;
    }

    public function index()
    {
        //default result
        $data = array();
        $data["list_game"] = array();

        //response message
        $this->status = 200;
        $this->message = 'Success';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
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
            "ios"
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
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
            die();
        }

        //check apisess
        $apisess = $postData->apisess;
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
          $this->status = 401;
          $this->message = 'Missing or invalid API session';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
          die();
        }

        $is_ios = $postData->ios;
        if($is_ios != "true"){
            $is_ios = "false";
        }

        $activeDomain = $this->gu->getListUrlActive($nation_code);

        $list_game = $this->hgm->getAll($nation_code, $is_ios);
        foreach($list_game as $game){
            $game->icon = str_replace("//", "/", $game->icon);
            $game->icon = $this->cdn_url($game->icon);
            $game->url_for_mobile = str_replace("sellon.net", $activeDomain->url, $game->url_for_mobile);

            // if($game->type_for_mobile != "web" || $is_ios == "false"){
                $data["list_game"][] = $game;
            // }

        }

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    }

    public function detail()
    {
        //default result
        $data = array();
        $data["game"] = array();

        //response message
        $this->status = 200;
        $this->message = 'Success';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
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
            "game_id"
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
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
            die();
        }

        //check apisess
        $apisess = $postData->apisess;
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
          $this->status = 401;
          $this->message = 'Missing or invalid API session';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
          die();
        }

        $game_id = $postData->game_id;
        $data["game"] = $this->hgm->getById($nation_code, $game_id);
        if($game_id == "0" || !isset($data["game"]->id)){
            $this->status = 1002;
            $this->message = 'Ticket not enough';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
            die();
        }

        $data["game"]->icon = str_replace("//", "/", $data["game"]->icon);
        $data["game"]->icon = $this->cdn_url($data["game"]->icon);

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    }

    // public function yourticket()
    // {
    //     //default result
    //     $data = array();
    //     $data["free_ticket"] = "0";
    //     $data["earned_ticket"] = "0";

    //     //response message
    //     $this->status = 200;
    //     $this->message = 'Success';

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    //         die();
    //     }

    //     $postData = $this->apikeyDecrypt($nation_code, $data, $this->input->post('samsung'), $this->input->post('nvidia'), $this->input->post('fullhd'));
    //     if ($postData === false) {
    //         $this->status = 1750;
    //         $this->message = 'Please check your data again';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }
    //     $postData = json_decode($postData);

    //     $listOfPostData = array(
    //         "apikey",
    //         "apisess"
    //     );

    //     foreach($listOfPostData as $value) {
    //         if(!isset($postData->$value)){
    //             $postData->$value = "";
    //         }
    //     }

    //     //check apikey
    //     $apikey = $postData->apikey;
    //     $c = $this->apikey_check($apikey);
    //     if (!$c) {
    //         $this->status = 400;
    //         $this->message = 'Missing or invalid API key';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    //         die();
    //     }

    //     //check apisess
    //     $apisess = $postData->apisess;
    //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     if (!isset($pelanggan->id)) {
    //       $this->status = 401;
    //       $this->message = 'Missing or invalid API session';
    //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    //       die();
    //     }

    //     $data["free_ticket"] = $pelanggan->free_ticket_rock_paper_scissors;
    //     $data["earned_ticket"] = $pelanggan->earned_ticket;

    //     //render as json
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    // }

    public function yourticketv2()
    {
        //default result
        $data = array();
        $data["free_ticket"] = "0";
        $data["earned_ticket"] = "0";

        //response message
        $this->status = 200;
        $this->message = 'Success';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
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
            "game_id"
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
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
            die();
        }

        //check apisess
        $apisess = $postData->apisess;
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
          $this->status = 401;
          $this->message = 'Missing or invalid API session';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
          die();
        }

        $game_id = $postData->game_id;
        $game_data = $this->hgm->getById($nation_code, $game_id);
        if($game_id == "0" || !isset($game_data->id)){
            $this->status = 1002;
            $this->message = 'Ticket not enough';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
            die();
        }

        $data["free_ticket"] = $this->__yourfreeticket($game_data, $pelanggan);
        $data["earned_ticket"] = $pelanggan->earned_ticket;

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    }

    public function ticketshop()
    {
        //default result
        $data = array();
        $data["balance"] = '0';
        $data["earned_ticket"] = '0';
        $data["list_ticket_shop"] = array();

        //response message
        $this->status = 200;
        $this->message = 'Success';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
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
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
            die();
        }

        //check apisess
        $apisess = $postData->apisess;
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
          $this->status = 401;
          $this->message = 'Missing or invalid API session';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
          die();
        }

        $data["earned_ticket"] = $pelanggan->earned_ticket;

        $getPointNow = $this->glptm->getByUserId($nation_code, $pelanggan->id);
        if(isset($getPointNow->b_user_id)){
            $data["balance"] = number_format($getPointNow->total_point, 0, ',', '.');
        }

        $data["list_ticket_shop"] = $this->htsm->getAll($nation_code);    

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    }

    public function buyticket()
    {
        //default result
        $data = array();

        //response message
        $this->status = 1001;
        $this->message = 'Balance not enough';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
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
            "ticket_shop_id"
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
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
            die();
        }

        //check apisess
        $apisess = $postData->apisess;
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
          $this->status = 401;
          $this->message = 'Missing or invalid API session';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
          die();
        }

        $ticket_shop_id = $postData->ticket_shop_id;
        $ticket_shop_data = $this->htsm->getById($nation_code, $ticket_shop_id);
        if($ticket_shop_id == "0" || !isset($ticket_shop_data->id)){
            $this->status = 1001;
            $this->message = 'Balance not enough';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
            die();
        }

        $getPointNow = $this->glptm->getByUserId($nation_code, $pelanggan->id);
        if(isset($getPointNow->b_user_id)){
            $pendingExchange = $this->glphm->sumPending($nation_code, $pelanggan->id, 'game', 'BuyTicket');
            $pendingExchange += $ticket_shop_data->price;

            if($getPointNow->total_point >= $pendingExchange){
                $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

                $di = array();
                $di['nation_code'] = $nation_code;
                $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
                $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
                $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
                $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
                $di['b_user_id'] = $pelanggan->id;
                $di['plusorminus'] = "-";
                $di['point'] = $ticket_shop_data->price;
                $di['custom_id'] = $ticket_shop_data->id;
                $di['custom_type'] = 'game';
                $di['custom_type_sub'] = 'BuyTicket';
                $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and deduct '.$di['point'].' point(s)';
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

                $this->bu->updateTotal($nation_code, $pelanggan->id, 'earned_ticket', '+', $ticket_shop_data->earned_ticket);

                $di = array();
                $di['nation_code'] = $nation_code;
                $di['b_user_id'] = $pelanggan->id;
                $di['type'] = "buy ticket";
                $di['total_ticket'] = $ticket_shop_data->earned_ticket;
                $di['custom_text'] = $pelanggan->fnama.' has buy '.$di['total_ticket'].' tickets';
                $endDoWhile = 0;
                do{
                  $di['id'] = $this->GUIDv4();
                  $checkId = $this->hthm->checkId($nation_code, $di['id']);
                  if($checkId == 0){
                      $endDoWhile = 1;
                  }
                }while($endDoWhile == 0);
                $this->hthm->set($di);

                //response message
                $this->status = 200;
                $this->message = 'Success';
            }
        }

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    }

    // public function checkcanplay()
    // {
    //     //default result
    //     $data = array();
    //     $data["version_for_mobile"] = "";

    //     //response message
    //     $this->status = 1002;
    //     $this->message = 'Ticket not enough';

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    //         die();
    //     }

    //     $postData = $this->apikeyDecrypt($nation_code, $data, $this->input->post('samsung'), $this->input->post('nvidia'), $this->input->post('fullhd'));
    //     if ($postData === false) {
    //         $this->status = 1750;
    //         $this->message = 'Please check your data again';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }
    //     $postData = json_decode($postData);

    //     $listOfPostData = array(
    //         "apikey",
    //         "apisess",
    //         "game_id"
    //     );

    //     foreach($listOfPostData as $value) {
    //         if(!isset($postData->$value)){
    //             $postData->$value = "";
    //         }
    //     }

    //     //check apikey
    //     $apikey = $postData->apikey;
    //     $c = $this->apikey_check($apikey);
    //     if (!$c) {
    //         $this->status = 400;
    //         $this->message = 'Missing or invalid API key';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    //         die();
    //     }

    //     //check apisess
    //     $apisess = $postData->apisess;
    //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     if (!isset($pelanggan->id)) {
    //       $this->status = 401;
    //       $this->message = 'Missing or invalid API session';
    //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    //       die();
    //     }

    //     $game_id = $postData->game_id;
    //     $game_data = $this->hgm->getById($nation_code, $game_id);

    //     if(isset($game_data->version_for_mobile)){
    //         $data["version_for_mobile"] = $game_data->version_for_mobile;
    //     }

    //     if($game_id == "0" || !isset($game_data->id)){
    //         $this->status = 1002;
    //         $this->message = 'Ticket not enough';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    //         die();
    //     }

    //     $ticket_cost = 0;
    //     if($game_data->win_cost_ticket > $game_data->lose_cost_ticket && $game_data->win_cost_ticket > $game_data->draw_cost_ticket){
    //         $ticket_cost = $game_data->win_cost_ticket;
    //     }else if($game_data->lose_cost_ticket > $game_data->win_cost_ticket && $game_data->lose_cost_ticket > $game_data->draw_cost_ticket){
    //         $ticket_cost = $game_data->lose_cost_ticket;
    //     }else{
    //         $ticket_cost = $game_data->draw_cost_ticket;
    //     }

    //     $free_ticket = $this->__yourfreeticket($game_data, $pelanggan);

    //     $total_ticket_owned = $free_ticket + $pelanggan->earned_ticket;
    //     if($total_ticket_owned >= $ticket_cost){
    //         $this->status = 200;
    //         $this->message = 'Success';
    //     }

    //     //render as json
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    // }

    public function checkcanplayv2()
    {
        //default result
        $data = array();
        $data["version_for_mobile"] = "";

        //response message
        $this->status = 1002;
        $this->message = 'Ticket not enough';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
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
            "game_id"
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
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
            die();
        }

        //check apisess
        $apisess = $postData->apisess;
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
          $this->status = 401;
          $this->message = 'Missing or invalid API session';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
          die();
        }

        $game_id = $postData->game_id;
        $game_data = $this->hgm->getById($nation_code, $game_id);

        if(isset($game_data->version_for_mobile)){
            $data["version_for_mobile"] = $game_data->version_for_mobile;
        }

        if($game_id == "0" || !isset($game_data->id)){
            $this->status = 1002;
            $this->message = 'Ticket not enough';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
            die();
        }

        $ticket_cost = 0;
        if($game_data->win_cost_ticket > $game_data->lose_cost_ticket && $game_data->win_cost_ticket > $game_data->draw_cost_ticket){
            $ticket_cost = $game_data->win_cost_ticket;
        }else if($game_data->lose_cost_ticket > $game_data->win_cost_ticket && $game_data->lose_cost_ticket > $game_data->draw_cost_ticket){
            $ticket_cost = $game_data->lose_cost_ticket;
        }else{
            $ticket_cost = $game_data->draw_cost_ticket;
        }

        $free_ticket = $this->__yourfreeticket($game_data, $pelanggan);

        $total_ticket_owned = $free_ticket + $pelanggan->earned_ticket;
        if($total_ticket_owned >= $ticket_cost){
            $this->status = 200;
            $this->message = 'Success';
        }

        if($game_data->name_point_history == "rock paper scissors"){
            $maxTotalWin = $this->ccm->getByClassifiedAndCode($nation_code, "game", "I5")->remark;
            $totalWin = $this->glphm->countAccomplishment($nation_code, "", "", "", "", $pelanggan->id, "", "", $game_data->name_point_history, 'win', date("Y-m-d"));
            $totalWinLeft = $maxTotalWin - $totalWin;
            $data["totalWinLeft"] = ($totalWinLeft > "0")? $totalWinLeft : "0";
            if($maxTotalWin <= $totalWin){
              $this->status = 1003;
              $this->message = 'Please play this game tomorrow again(Daily Limit).';
              $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
              die();
            }
        }

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    }

    // public function played()
    // {
    //     //default result
    //     $data = array();

    //     //response message
    //     $this->status = 200;
    //     $this->message = 'Success';

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    //         die();
    //     }

    //     $postData = $this->apikeyDecrypt($nation_code, $data, $this->input->post('samsung'), $this->input->post('nvidia'), $this->input->post('fullhd'));
    //     if ($postData === false) {
    //         $this->status = 1750;
    //         $this->message = 'Please check your data again';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
    //         die();
    //     }
    //     $postData = json_decode($postData);

    //     $listOfPostData = array(
    //         "apikey",
    //         "apisess",
    //         "game_id",
    //         "result"
    //     );

    //     foreach($listOfPostData as $value) {
    //         if(!isset($postData->$value)){
    //             $postData->$value = "";
    //         }
    //     }

    //     //check apikey
    //     $apikey = $postData->apikey;
    //     $c = $this->apikey_check($apikey);
    //     if (!$c) {
    //         $this->status = 400;
    //         $this->message = 'Missing or invalid API key';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    //         die();
    //     }

    //     //check apisess
    //     $apisess = $postData->apisess;
    //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     if (!isset($pelanggan->id)) {
    //       $this->status = 401;
    //       $this->message = 'Missing or invalid API session';
    //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    //       die();
    //     }

    //     $game_data = $this->hgm->getById($nation_code, $postData->game_id);
    //     if($postData->game_id == "0" || !isset($game_data->id)){
    //         $this->status = 1002;
    //         $this->message = 'Ticket not enough';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    //         die();
    //     }

    //     $result = $postData->result;

    //     $ticket_cost = 0;
    //     $sptGet = 0;
    //     if($result == "win"){
    //         $ticket_cost = $game_data->win_cost_ticket;
    //         $sptGet = $game_data->win_spt;
    //     }else if($result == "lose"){
    //         $ticket_cost = $game_data->lose_cost_ticket;
    //         $sptGet = $game_data->lose_spt;
    //     }else{
    //         $ticket_cost = $game_data->draw_cost_ticket;
    //         $sptGet = $game_data->draw_spt;
    //     }

    //     $free_ticket = $this->__yourfreeticket($game_data, $pelanggan);

    //     $total_ticket_owned = $free_ticket + $pelanggan->earned_ticket;
    //     if($total_ticket_owned < $ticket_cost){
    //         $this->status = 1002;
    //         $this->message = 'Ticket not enough';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    //         die();
    //     }

    //     if($ticket_cost != 0){

    //         $free_ticket_column_name = $this->__freeticketcolumnname($game_data);

    //         if($free_ticket >= $ticket_cost){

    //             $this->bu->updateTotal($nation_code, $pelanggan->id, $free_ticket_column_name, '-', $ticket_cost);

    //             $di = array();
    //             $di['nation_code'] = $nation_code;
    //             $di['b_user_id'] = $pelanggan->id;
    //             $di['type'] = "play game(free ticket)";
    //             $di['game_name'] = $game_data->name;
    //             $di['plusorminus'] = "-";
    //             $di['total_ticket'] = $ticket_cost;
    //             $di['result'] = $result;
    //             $di['custom_text'] = $pelanggan->fnama.' has '.$di['type'].' and lose '.$di['total_ticket'].' tickets';

    //             $endDoWhile = 0;
    //             do{
    //               $di['id'] = $this->GUIDv4();
    //               $checkId = $this->hthm->checkId($nation_code, $di['id']);
    //               if($checkId == 0){
    //                   $endDoWhile = 1;
    //               }
    //             }while($endDoWhile == 0);
    //             $this->hthm->set($di);

    //         }else{
    //             if($free_ticket > 0){
    //                 $this->bu->updateTotal($nation_code, $pelanggan->id, $free_ticket_column_name, '-', $free_ticket);

    //                 $di = array();
    //                 $di['nation_code'] = $nation_code;
    //                 $di['b_user_id'] = $pelanggan->id;
    //                 $di['type'] = "play game(free ticket)";
    //                 $di['game_name'] = $game_data->name;
    //                 $di['plusorminus'] = "-";
    //                 $di['total_ticket'] = $free_ticket;
    //                 $di['result'] = $result;
    //                 $di['custom_text'] = $pelanggan->fnama.' has '.$di['type'].' and lose '.$di['total_ticket'].' tickets';
    //                 $endDoWhile = 0;
    //                 do{
    //                   $di['id'] = $this->GUIDv4();
    //                   $checkId = $this->hthm->checkId($nation_code, $di['id']);
    //                   if($checkId == 0){
    //                       $endDoWhile = 1;
    //                   }
    //                 }while($endDoWhile == 0);
    //                 $this->hthm->set($di);

    //                 $ticket_cost -= $free_ticket;
    //             }
    //             $this->bu->updateTotal($nation_code, $pelanggan->id, "earned_ticket", '-', $ticket_cost);

    //             $di = array();
    //             $di['nation_code'] = $nation_code;
    //             $di['b_user_id'] = $pelanggan->id;
    //             $di['type'] = "play game(earned ticket)";
    //             $di['game_name'] = $game_data->name;
    //             $di['plusorminus'] = "-";
    //             $di['total_ticket'] = $ticket_cost;
    //             $di['result'] = $result;
    //             $di['custom_text'] = $pelanggan->fnama.' has '.$di['type'].' and lose '.$di['total_ticket'].' tickets';
    //             $endDoWhile = 0;
    //             do{
    //               $di['id'] = $this->GUIDv4();
    //               $checkId = $this->hthm->checkId($nation_code, $di['id']);
    //               if($checkId == 0){
    //                   $endDoWhile = 1;
    //               }
    //             }while($endDoWhile == 0);
    //             $this->hthm->set($di);
    //         }
    //     }

    //     if($sptGet != 0){
    //         $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

    //         $di = array();
    //         $di['nation_code'] = $nation_code;
    //         $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
    //         $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
    //         $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
    //         $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
    //         $di['b_user_id'] = $pelanggan->id;
    //         $di['point'] = $sptGet;
    //         $di['custom_id'] = $game_data->id;
    //         $di['custom_type'] = $game_data->name_point_history;
    //         $di['custom_type_sub'] = 'win';
    //         $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
    //         $endDoWhile = 0;
    //         do{
    //             $leaderBoardHistoryId = $this->GUIDv4();
    //             $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
    //             if($checkId == 0){
    //                 $endDoWhile = 1;
    //             }
    //         }while($endDoWhile == 0);
    //         $di['id'] = $leaderBoardHistoryId;
    //         $this->glphm->set($di);
    //     }

    //     //render as json
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    // }

    public function playedv2()
    {
        //default result
        $data = array();
        $data["totalWinLeft"] = "";
        $data["message"] = "";

        //response message
        $this->status = 200;
        $this->message = 'Success';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
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
            "game_id",
            "result"
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
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
            die();
        }

        //check apisess
        $apisess = $postData->apisess;
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
          $this->status = 401;
          $this->message = 'Missing or invalid API session';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
          die();
        }

        $game_data = $this->hgm->getById($nation_code, $postData->game_id);
        if($postData->game_id == "0" || !isset($game_data->id)){
            $this->status = 1002;
            $this->message = 'Ticket not enough';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
            die();
        }

        if($game_data->name_point_history == "rock paper scissors"){
            $maxTotalWin = $this->ccm->getByClassifiedAndCode($nation_code, "game", "I5")->remark;
            $totalWin = $this->glphm->countAccomplishment($nation_code, "", "", "", "", $pelanggan->id, "", "", $game_data->name_point_history, 'win', date("Y-m-d"));
            $totalWinLeft = $maxTotalWin - $totalWin;
            $data["totalWinLeft"] = ($totalWinLeft > "0")? $totalWinLeft : "0";
            if($maxTotalWin <= $totalWin){
              $this->status = 1003;
              $this->message = 'Please play this game tomorrow again(Daily Limit).';
              $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
              die();
            }
        }

        $result = $postData->result;

        $ticket_cost = 0;
        $sptGet = 0;
        if($result == "win"){
            $ticket_cost = $game_data->win_cost_ticket;
            $sptGet = $game_data->win_spt;
        }else if($result == "lose"){
            $ticket_cost = $game_data->lose_cost_ticket;
            $sptGet = $game_data->lose_spt;
        }else{
            $ticket_cost = $game_data->draw_cost_ticket;
            $sptGet = $game_data->draw_spt;
        }

        $free_ticket = $this->__yourfreeticket($game_data, $pelanggan);

        $total_ticket_owned = $free_ticket + $pelanggan->earned_ticket;
        if($total_ticket_owned < $ticket_cost){
            $this->status = 1002;
            $this->message = 'Ticket not enough';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
            die();
        }

        if($ticket_cost != 0){
            $free_ticket_column_name = $this->__freeticketcolumnname($game_data);

            if($free_ticket >= $ticket_cost){
                $this->bu->updateTotal($nation_code, $pelanggan->id, $free_ticket_column_name, '-', $ticket_cost);

                $di = array();
                $di['nation_code'] = $nation_code;
                $di['b_user_id'] = $pelanggan->id;
                $di['type'] = "play game(free ticket)";
                $di['game_name'] = $game_data->name;
                $di['plusorminus'] = "-";
                $di['total_ticket'] = $ticket_cost;
                $di['result'] = $result;
                $di['custom_text'] = $pelanggan->fnama.' has '.$di['type'].' and lose '.$di['total_ticket'].' tickets';

                $endDoWhile = 0;
                do{
                  $di['id'] = $this->GUIDv4();
                  $checkId = $this->hthm->checkId($nation_code, $di['id']);
                  if($checkId == 0){
                      $endDoWhile = 1;
                  }
                }while($endDoWhile == 0);
                $this->hthm->set($di);
            }else{
                if($free_ticket > 0){
                    $this->bu->updateTotal($nation_code, $pelanggan->id, $free_ticket_column_name, '-', $free_ticket);

                    $di = array();
                    $di['nation_code'] = $nation_code;
                    $di['b_user_id'] = $pelanggan->id;
                    $di['type'] = "play game(free ticket)";
                    $di['game_name'] = $game_data->name;
                    $di['plusorminus'] = "-";
                    $di['total_ticket'] = $free_ticket;
                    $di['result'] = $result;
                    $di['custom_text'] = $pelanggan->fnama.' has '.$di['type'].' and lose '.$di['total_ticket'].' tickets';
                    $endDoWhile = 0;
                    do{
                      $di['id'] = $this->GUIDv4();
                      $checkId = $this->hthm->checkId($nation_code, $di['id']);
                      if($checkId == 0){
                          $endDoWhile = 1;
                      }
                    }while($endDoWhile == 0);
                    $this->hthm->set($di);

                    $ticket_cost -= $free_ticket;
                }
                $this->bu->updateTotal($nation_code, $pelanggan->id, "earned_ticket", '-', $ticket_cost);

                $di = array();
                $di['nation_code'] = $nation_code;
                $di['b_user_id'] = $pelanggan->id;
                $di['type'] = "play game(earned ticket)";
                $di['game_name'] = $game_data->name;
                $di['plusorminus'] = "-";
                $di['total_ticket'] = $ticket_cost;
                $di['result'] = $result;
                $di['custom_text'] = $pelanggan->fnama.' has '.$di['type'].' and lose '.$di['total_ticket'].' tickets';
                $endDoWhile = 0;
                do{
                  $di['id'] = $this->GUIDv4();
                  $checkId = $this->hthm->checkId($nation_code, $di['id']);
                  if($checkId == 0){
                      $endDoWhile = 1;
                  }
                }while($endDoWhile == 0);
                $this->hthm->set($di);
            }
        }

        if($sptGet != 0){
            $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

            $di = array();
            $di['nation_code'] = $nation_code;
            $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
            $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
            $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
            $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
            $di['b_user_id'] = $pelanggan->id;
            $di['point'] = $sptGet;
            $di['custom_id'] = $game_data->id;
            $di['custom_type'] = $game_data->name_point_history;
            $di['custom_type_sub'] = 'win';
            $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
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

        if($game_data->name_point_history == "rock paper scissors"){
            $maxTotalWin = $this->ccm->getByClassifiedAndCode($nation_code, "game", "I5")->remark;
            $totalWin = $this->glphm->countAccomplishment($nation_code, "", "", "", "", $pelanggan->id, "", "", $game_data->name_point_history, 'win', date("Y-m-d"));
            $totalWinLeft = $maxTotalWin - $totalWin;
            $data["totalWinLeft"] = ($totalWinLeft > "0")? $totalWinLeft : "0";
            if($data["totalWinLeft"] == "0"){
                if($pelanggan->language_id == 2){
                    $data["message"] = "Silakan mainkan game ini lagi besok (Batas Harian).";
                }else{
                    $data["message"] = "Please play this game tomorrow again(Daily Limit).";
                }
            }
        }

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    }

    public function playedshootingfire()
    {
        //default result
        $data = array();

        //response message
        $this->status = 200;
        $this->message = 'Success';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
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
            "game_id",
            "spt_get",
            "deducted"
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
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
            die();
        }

        //check apisess
        $apisess = $postData->apisess;
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
          $this->status = 401;
          $this->message = 'Missing or invalid API session';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
          die();
        }

        $game_data = $this->hgm->getById($nation_code, $postData->game_id);
        if($postData->game_id == "0" || !isset($game_data->id)){
            $this->status = 1002;
            $this->message = 'Ticket not enough';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
            die();
        }

        $alreadyPlayedBefore = $this->hthm->getPlayedBefore($nation_code, $pelanggan->id, $game_data->name);
        if(isset($alreadyPlayedBefore->id)){
            $this->status = 1002;
            $this->message = 'Ticket not enough';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
            die();
        }
        unset($alreadyPlayedBefore);

        $sptGet = $postData->spt_get;

        $deducted = $postData->deducted;
        if($deducted <= 0){
            $deducted = 1;
        }

        $ticket_cost = $game_data->win_cost_ticket * $deducted;

        $free_ticket = $this->__yourfreeticket($game_data, $pelanggan);

        $total_ticket_owned = $free_ticket + $pelanggan->earned_ticket;
        if($total_ticket_owned < $ticket_cost){
            $this->status = 1002;
            $this->message = 'Ticket not enough';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
            die();
        }

        if($ticket_cost != 0){

            $free_ticket_column_name = $this->__freeticketcolumnname($game_data);

            if($free_ticket >= $ticket_cost){

                $this->bu->updateTotal($nation_code, $pelanggan->id, $free_ticket_column_name, '-', $ticket_cost);

                $di = array();
                $di['nation_code'] = $nation_code;
                $di['b_user_id'] = $pelanggan->id;
                $di['type'] = "play game(free ticket)";
                $di['game_name'] = $game_data->name;
                $di['plusorminus'] = "-";
                $di['total_ticket'] = $ticket_cost;
                $di['result'] = 'win';
                $di['custom_text'] = $pelanggan->fnama.' has '.$di['type'].' and lose '.$di['total_ticket'].' tickets';

                $endDoWhile = 0;
                do{
                  $di['id'] = $this->GUIDv4();
                  $checkId = $this->hthm->checkId($nation_code, $di['id']);
                  if($checkId == 0){
                      $endDoWhile = 1;
                  }
                }while($endDoWhile == 0);
                $this->hthm->set($di);

            }else{

                if($free_ticket > 0){

                    $this->bu->updateTotal($nation_code, $pelanggan->id, $free_ticket_column_name, '-', $free_ticket);

                    $di = array();
                    $di['nation_code'] = $nation_code;
                    $di['b_user_id'] = $pelanggan->id;
                    $di['type'] = "play game(free ticket)";
                    $di['game_name'] = $game_data->name;
                    $di['plusorminus'] = "-";
                    $di['total_ticket'] = $free_ticket;
                    $di['result'] = 'win';
                    $di['custom_text'] = $pelanggan->fnama.' has '.$di['type'].' and lose '.$di['total_ticket'].' tickets';

                    $endDoWhile = 0;
                    do{
                      $di['id'] = $this->GUIDv4();
                      $checkId = $this->hthm->checkId($nation_code, $di['id']);
                      if($checkId == 0){
                          $endDoWhile = 1;
                      }
                    }while($endDoWhile == 0);
                    $this->hthm->set($di);

                    $ticket_cost -= $free_ticket;

                }

                $this->bu->updateTotal($nation_code, $pelanggan->id, "earned_ticket", '-', $ticket_cost);

                $di = array();
                $di['nation_code'] = $nation_code;
                $di['b_user_id'] = $pelanggan->id;
                $di['type'] = "play game(earned ticket)";
                $di['game_name'] = $game_data->name;
                $di['plusorminus'] = "-";
                $di['total_ticket'] = $ticket_cost;
                $di['result'] = 'win';
                $di['custom_text'] = $pelanggan->fnama.' has '.$di['type'].' and lose '.$di['total_ticket'].' tickets';
                $endDoWhile = 0;
                do{
                  $di['id'] = $this->GUIDv4();
                  $checkId = $this->hthm->checkId($nation_code, $di['id']);
                  if($checkId == 0){
                      $endDoWhile = 1;
                  }
                }while($endDoWhile == 0);
                $this->hthm->set($di);

            }
        }

        if($sptGet != 0){
            $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

            $di = array();
            $di['nation_code'] = $nation_code;
            $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
            $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
            $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
            $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
            $di['b_user_id'] = $pelanggan->id;
            $di['point'] = $sptGet;
            $di['custom_id'] = $game_data->id;
            $di['custom_type'] = $game_data->name_point_history;
            $di['custom_type_sub'] = 'win';
            $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
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

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "game");
    }

}
