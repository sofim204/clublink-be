<?php
class Convert_spt extends JI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->lib("seme_log");
    $this->load("api_mobile/common_code_model", "ccm");
    $this->load("api_mobile/b_user_model", "bu");
    $this->load("api_mobile/b_user_alamat_model", "bua");
    // $this->load("api_mobile/b_user_setting_model", "busm");
    $this->load("api_mobile/g_convert_history_model", 'gchm');
    $this->load("api_mobile/g_leaderboard_point_history_model", 'glphm');
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

  private function __callBlockChainConvertSptToBbt($postdata){
    $dateBefore = date("Y-m-d H:i:s");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_URL, $this->blockchain_new_api_host."api/MiningBbt");

    $headers = array();
    $headers[] = 'Content-Type:  application/json';
    $headers[] = 'Accept:  application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $server_privatekey = $this->ccm->getByClassifiedAndCode("62", "app_config", "C11")->remark;
    $nonce = \random_bytes(\SODIUM_CRYPTO_BOX_NONCEBYTES);
    $sender_keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey(base64_decode($server_privatekey), base64_decode("qx3Bbl83GN8yKhvP47FpLBR+8HyRii/myOKpxZ39CFI="));
    $message = json_encode($postdata);
    $encrypted_signed_text = sodium_crypto_box($message, $nonce, $sender_keypair);
    $postDataSend = array(
      "nonce" => base64_encode($nonce),
      "encryped_data" => base64_encode($encrypted_signed_text)
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postDataSend));

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
      return 0;
      //echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

    $this->seme_log->write("api_mobile", "sebelum panggil api ".$dateBefore.", url untuk block chain server ". $this->blockchain_new_api_host."api/MiningBbt. data send to blockchain api ". json_encode($postdata).", after encryption ".json_encode($postDataSend).". isi response block chain server ". $result);
    return $result;
  }

  private function __sortCol($sort_col, $tbl_as)
  {
    switch ($sort_col) {
      case 'cdate':
      $sort_col = "$tbl_as.cdate";
      break;
      case 'id':
      $sort_col = "$tbl_as.id";
      break;
      case 'completed_date':
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
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['minimum_trf'] = 0;
    $data['x_spt_equal_one_bbt'] = 0;
    $data['commission_in_percent'] = 0;
    $data['spt_balance'] = 0;
    $data['can_convert_spt_to_bbt'] = "0";

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $data['minimum_trf'] = (float) $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C19")->remark;
    $data['x_spt_equal_one_bbt'] = (float) $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C20")->remark;
    $data['commission_in_percent'] = (float) $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C21")->remark;

    $getPointNow = $this->glptm->getByUserId($nation_code, $pelanggan->id);
    if(isset($getPointNow->b_user_id)){
        $data['spt_balance'] = (float) $getPointNow->total_point;
    }

    $checkInsertedStillprocessing = $this->gchm->getByUserIdStatusProcessing($nation_code, $pelanggan->id);
    if(!isset($checkInsertedStillprocessing->id)){
      $data['can_convert_spt_to_bbt'] = "1";
    }

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "convert_spt");
  }

  public function history()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    $data['datas'] = array();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $timezone = $this->input->get("timezone");
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
    }

    $sort_col = $this->input->post("sort_col");
    $sort_dir = $this->input->post("sort_dir");
    $page = $this->input->post("page");
    // $page_size = $this->input->post("page_size");
    $page_size = 10;

    $tbl_as = $this->gchm->getTblAs();
    $sort_col = $this->__sortCol($sort_col, $tbl_as);
    $sort_dir = $this->__sortDir($sort_dir);
    $page = $this->__page($page);
    $page_size = $this->__pageSize($page_size);

    $fromdate = $this->input->post("fromdate");
    $todate = $this->input->post("todate");
    if(!strtotime($fromdate) !== false || !strtotime($todate) !== false){
      $fromdate = '';
      $todate = '';
    }

    $data['datas'] = $this->gchm->getAll($nation_code, $page, $page_size, $sort_col, $sort_dir, $pelanggan->id, $fromdate, $todate);
    foreach ($data['datas'] as &$pd) {
      $pd->cdate = $this->customTimezone($pd->cdate, $timezone);
    }

    //response
    $this->status = 200;
    $this->message = 'Success';
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "convert_spt");
  }

  // public function detail()
  // {
  //   //initial
  //   $dt = $this->__init();

  //   //default result
  //   $data = array();
  //   $data['detail'] = new stdClass();

  //   //check nation_code
  //   $nation_code = $this->input->get('nation_code');
  //   $nation_code = $this->nation_check($nation_code);
  //   if (empty($nation_code)) {
  //     $this->status = 101;
  //     $this->message = 'Missing or invalid nation_code';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
  //     die();
  //   }

  //   //check apikey
  //   $apikey = $this->input->get('apikey');
  //   $c = $this->apikey_check($apikey);
  //   if (!$c) {
  //     $this->status = 400;
  //     $this->message = 'Missing or invalid API key';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
  //     die();
  //   }

  //   //check apisess
  //   $apisess = $this->input->get('apisess');
  //   $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
  //   if (!isset($pelanggan->id)) {
  //     $this->status = 401;
  //     $this->message = 'Missing or invalid API session';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
  //     die();
  //   }

  //   $timezone = $this->input->get('timezone');
  //   if($this->isValidTimezoneId($timezone) === false){
  //     $timezone = $this->default_timezone;
  //   }

  //   $id = $this->input->get('id');
  //   $data['detail'] = $this->gchm->getById($nation_code, $id);
  //   if (!isset($data['detail']->id)) {
  //     $this->status = 1160;
  //     $this->message = 'Not found';
  //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "convert_spt");
  //     die();
  //   }

  //   $data['detail']->cdate = $this->customTimezone($data['detail']->cdate, $timezone);

  //   $this->status = 200;
  //   $this->message = 'Success';
  //   $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "convert_spt");
  // }

  public function baru()
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
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $timezone = $this->input->get('timezone');
    if($this->isValidTimezoneId($timezone) === false){
      $timezone = $this->default_timezone;
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
      "amount_spt"
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

    $checkInsertedStillprocessing = $this->gchm->getByUserIdStatusProcessing($nation_code, $pelanggan->id);
    if(isset($checkInsertedStillprocessing->id)){
      $this->status = 1003;
      $this->message = 'Please wait your previous request finish first';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "convert_spt");
      die();
    }

    $amount_spt = $postData->amount_spt;
    if($amount_spt <= 0){
      $this->status = 300;
      $this->message = 'Missing one or more parameters';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
      die();
    }

    $minimum_trf = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C19")->remark;
    if($amount_spt < $minimum_trf){
      $this->status = 1002;
      $this->message = 'Minumum transfer '.$minimum_trf.'SPT';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "convert_spt");
      die();
    }

    $getPointNow = $this->glptm->getByUserId($nation_code, $pelanggan->id);
    if(!isset($getPointNow->b_user_id)){
      //create point
      $di = array();
      $di['nation_code'] = $nation_code;
      $di['b_user_id'] = $pelanggan->id;
      $di['total_post'] = 0;
      $di['total_point'] = 0;
      $this->glptm->set($di);
      $getPointNow = $this->glptm->getByUserId($nation_code, $pelanggan->id);
    }

    if($amount_spt > $getPointNow->total_point){
      $this->status = 1001;
      $this->message = 'Balance not enough';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "convert_spt");
      die();
    }

    $x_spt_equal_one_bbt = $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C20")->remark;
    $commission_in_percent= $this->ccm->getByClassifiedAndCode($nation_code, "app_config", "C21")->remark;

    $amount_bbt = $amount_spt / $x_spt_equal_one_bbt;
    $commission = $amount_bbt * $commission_in_percent/100;
    $amount_bbt -= $commission;

    $this->gchm->trans_start();

    $di = array();
    $di['nation_code'] = $nation_code;
    $di['b_user_id'] = $pelanggan->id;
    $di['type'] = "spt to bbt";
    $di['title'] = "Request to exchange tokens";
    $di['plusorminus'] = "-";
    $di['amount_spt'] = $amount_spt;
    $di['amount_bbt'] = $amount_bbt;
    $di['commission'] = $commission;
    $di['commission_persen'] = $commission_in_percent;
    $di['status'] = "processing";
    $endDoWhile = 0;
    do{
      $id = $this->GUIDv4();
      $checkId = $this->gchm->checkId($nation_code, $id);
      if($checkId == 0){
        $endDoWhile = 1;
      }
    }while($endDoWhile == 0);
    $di['id'] = $id;
    $res = $this->gchm->set($di);
    if (!$res) {
      $this->gchm->trans_rollback();
      $this->gchm->trans_end();
      $this->status = 1107;
      $this->message = "Error, please try again later";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "convert_spt");
      die();
    }

    if($pelanggan->language_id == 2){
      $language = 'id';
    }else{
      $language = 'en';
    }

    $postdata = array(
      'userWalletCode' => $pelanggan->user_wallet_code_new,
      'countryIsoCode' => strtolower($this->blockchain_api_country),
      'LanguageIsoCode' => $language,
      'signupUtcDate' => $pelanggan->cdate,
      'userSptPoint' => $getPointNow->total_point,
      'userSptToConvert' => $amount_spt,
      'bbtAmount' => $amount_bbt,
      'commissionFee' => $commission,
      'transactionDate' => date("Y-m-d H:i:s"),
      'miningTransactionId' => $id
    );
    $response = json_decode($this->__callBlockChainConvertSptToBbt($postdata));
    if(isset($response->status)){
      if($response->status != 200){
        $this->gchm->trans_rollback();
        $this->gchm->trans_end();
        $this->status = 1107;
        $this->message = "Error, please try again later";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "convert_spt");
        die();
      }
    } else {
      $this->gchm->trans_rollback();
      $this->gchm->trans_end();
      $this->status = 1107;
      $this->message = "Error, please try again later";
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "convert_spt");
      die();
    }
    unset($response);

    $address = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

    $di = array();
    $di['nation_code'] = $nation_code;
    $di['b_user_alamat_location_kelurahan'] = $address->kelurahan;
    $di['b_user_alamat_location_kecamatan'] = $address->kecamatan;
    $di['b_user_alamat_location_kabkota'] = $address->kabkota;
    $di['b_user_alamat_location_provinsi'] = $address->provinsi;
    $di['b_user_id'] = $pelanggan->id;
    $di['plusorminus'] = "-";
    $di['point'] = $amount_spt;
    $di['custom_id'] = $id;
    $di['custom_type'] = 'convert';
    $di['custom_type_sub'] = "spt to bbt";
    $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type'].' '.$di['custom_type_sub'].' and lose '.$di['point'].' point(s)';
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

    $this->gchm->trans_commit();
    $this->gchm->trans_end();

    $this->status = 200;
    $this->message = "Success";
    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "convert_spt");
  }
}
