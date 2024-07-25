<?php
class Get_spt extends JI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
    	$this->load("api_mobile/b_user_model", "bu");
        $this->load("api_for_wallet/g_leaderboard_point_history_model", 'glphm');
    	$this->load("api_mobile/common_code_model", "ccm");
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

    // public function index()
    // {
    //     //default result
    //     $data = array();
    //     $data["list"] = array();

    //     //response message
    //     $this->status = 200;
    //     $this->message = 'Success';

    //     //check nation_code
    //     $nation_code = $this->input->get('nation_code');
    //     $nation_code = $this->nation_check($nation_code);
    //     if (empty($nation_code)) {
    //         $this->status = 101;
    //         $this->message = 'Missing or invalid nation_code';
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
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
    //         $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
    //         die();
    //     }

    //     //check apisess
    //     $apisess = $postData->apisess;
    //     $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    //     if (!isset($pelanggan->id)) {
    //       $this->status = 401;
    //       $this->message = 'Missing or invalid API session';
    //       $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
    //       die();
    //     }

    //     $data["list"] = $this->hprem->getAll($nation_code, $pelanggan->id);
    //     // foreach($data["list"] as &$value){
    //         // $value->icon = str_replace("//", "/", $value->icon);
    //         // $value->icon = $this->cdn_url($value->icon);
    //     // }

    //     //render as json
    //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "point_redemption_exchange");
    // }

    public function inputemailandpassword()
    {
        //default result
        $data = array();
        $data['message'] = '';

        $this->status = 200;
        $this->message = 'Success';

        $nation_code = "62";

        $user_wallet_code = $this->input->post('user_wallet_code');
        if (!$user_wallet_code) {
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $user = $this->bu->getByUserWalletCodeNew($nation_code, $user_wallet_code);
        if (!isset($user->id)) {
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $checkAlreadyInDB = $this->glphm->checkAlreadyInDB($nation_code, "", "", "", "", "", $user->id, "0", "wallet", "input email and password", "");
        if (isset($checkAlreadyInDB->id)) {
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

		//get point
		$pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E31");
		if (!isset($pointGet->remark)) {
			$pointGet = new stdClass();
			$pointGet->remark = 1000;
		}

		$di = array();
		$di['nation_code'] = $nation_code;
		$di['b_user_alamat_location_kelurahan'] = "All";
		$di['b_user_alamat_location_kecamatan'] = "All";
		$di['b_user_alamat_location_kabkota'] = "All";
		$di['b_user_alamat_location_provinsi'] = "All";
		$di['b_user_id'] = $user->id;
		$di['point'] = $pointGet->remark;
		$di['custom_id'] = "0";
		$di['custom_type'] = "wallet";
		$di['custom_type_sub'] = "input email and password";
		$di['custom_text'] = $user->fnama.' has '.$di['custom_type_sub'].' '.$di['custom_type'].' and get '.$di['point'].' point(s)';
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

        if($user->language_id == 2){
            $data['message'] = 'Selamat!. Anda memperoleh '.$pointGet->remark.' SPT dan dapat menukarkannya menjadi BabyBoomToken.';
        }else{
            $data['message'] = 'Congratulations! You earned '.$pointGet->remark.' SPT and can exchange them to BabyBoomToken.';
        }

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "get_spt");
    }
}
