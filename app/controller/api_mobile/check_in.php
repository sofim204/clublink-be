<?php
class Check_in extends JI_Controller{

	public function __construct(){
    parent::__construct();
    $this->load("api_mobile/b_user_model","bu");
    $this->load("api_mobile/b_user_alamat_model", "bua");
    $this->load("api_mobile/common_code_model","ccm");
    $this->load("api_mobile/g_check_in_setting_model","gcism");
    // $this->load("api_mobile/g_point_check_in_history_model","gpcih");

    // $this->load("api_mobile/g_leaderboard_point_area_model", 'glpam');
    $this->load("api_mobile/g_leaderboard_point_history_model", 'glphm');
    $this->load("api_mobile/g_leaderboard_ranking_model", 'glrm');
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

	public function index(){

		$dt = $this->__init();

		//default result
		$data = array();
		$data['detail_event'] = new stdClass();
		$data['event_status'] = "ended";
		$data['total_point_check_in'] = "0";
		$data['check_in_total'] = "0";
		$data['check_in_history'] = array();
		$data['already_check_in_today'] = "0";

    //check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
    if(empty($nation_code)){
      $this->status = 101;
  		$this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "check_in");
      die();
    }

		//check apikey
		$apikey = $this->input->get('apikey');
		$c = $this->apikey_check($apikey);
		if(!$c){
			$this->status = 400;
			$this->message = 'Missing or invalid API key';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "check_in");
			die();
		}

		//check apisess
		$apisess = $this->input->get('apisess');
		$pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
		if(!isset($pelanggan->id)){
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

		$data['detail_event'] = $this->gcism->getOldest($nation_code);

		if(isset($data['detail_event']->id)){

			if($data['detail_event']->start_date <= date("Y-m-d") && $data['detail_event']->end_date >= date("Y-m-d")){
				$data['event_status'] = "running";
			}

			if($pelanggan->language_id == 2){
      	$data['detail_event']->end_date = $this->__dateIndonesia($data['detail_event']->end_date,'tanggal');
	    }else{
      	$data['detail_event']->end_date = date("F d Y", strtotime($data['detail_event']->end_date));
	    }

		}

		if(isset($pelanggan->id)){

      $dateCompare = date('Y-m-d');

      $data['total_point_check_in'] = (string) $this->glphm->sumCheckIn($nation_code, $pelanggan->id, "check in", $dateCompare);

			$data['check_in_total'] = $this->glphm->countAll($nation_code, "", "", "", "", $pelanggan->id, "+", 0, "check in", "daily", $dateCompare, "check in");

			$data['check_in_history'] = $this->glphm->getAll($nation_code, "", "", "", "", $pelanggan->id, "+", 0, "check in", "daily", $dateCompare, "check in");

			if($data['check_in_history']){

				end($data['check_in_history']);
				$key = key($data['check_in_history']);
				reset($data['check_in_history']);

				if(date("Y-m-d", strtotime($data['check_in_history'][$key]->cdate)) == $dateCompare){
					$data['already_check_in_today'] = "1";
				}

			}

		}

    $this->status = 200;
    $this->message = 'Success';
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "check_in");

	}

  public function baru()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    // $data['chat'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "check_in");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "check_in");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "check_in");
      die();
    }

    $dateNow = date("Y-m-d");

  	$HistoryCheckIn = $this->glphm->checkAlreadyInDB($nation_code, "", "", "", "", "", $pelanggan->id, 0, "check in", "daily", $dateNow);
    if (isset($HistoryCheckIn->id)) {
      $this->status = 7280;
      $this->message = 'you have already check in';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "check_in");
      die();
    }

    $pointGetFinal = "";
    $pointTextType = 0; //1 = daily, 2 = weekly, 3 = monthly

    //start transaction
    $this->glphm->trans_start();

    //get point
    $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E1");
    if (!isset($pointGet->remark)) {
      $pointGet = new stdClass();
      $pointGet->remark = 10;
    }

    $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

    //insert into database
    $di = array();
    $di['nation_code'] = $nation_code;
    $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
    $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
    $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
    $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
    $di['b_user_id'] = $pelanggan->id;
    $di['point'] = $pointGet->remark;
    $di['custom_id'] = 0;
    $di['custom_type'] = 'check in';
    $di['custom_type_sub'] = 'daily';
    $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type'].' '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
    $endDoWhile = 0;
    do{
      $leaderBoardHistoryId = $this->GUIDv4();
      $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
      if($checkId == 0){
        $endDoWhile = 1;
      }
    }while($endDoWhile == 0);
    $di['id'] = $leaderBoardHistoryId;
    $res = $this->glphm->set($di);
    if ($res) {
      $this->status = 200;
      $this->message = 'Success';
      $this->glphm->trans_commit();
			// $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '-', $di['point']);
	    // $this->glphm->trans_commit();

      $pointGetFinal .= $di['point'];
      $pointTextType++;

      //check in weekly
      //https://www.geeksforgeeks.org/how-to-get-last-day-of-a-month-from-date-in-php/
      if(date("l", strtotime($dateNow)) == "Sunday"){
      	$totalCheckIn = $this->glphm->countCheckIn($nation_code, $pelanggan->id, date("Y-m-d", strtotime($dateNow."-6 days")), $dateNow);
      	if($totalCheckIn == 7){
			    //get point
			    $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E2");
			    if (!isset($pointGet->remark)) {
			      $pointGet = new stdClass();
			      $pointGet->remark = 30;
			    }

			    //insert into database
			    $di = array();
			    $di['nation_code'] = $nation_code;
			    $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
			    $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
			    $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
			    $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
			    $di['b_user_id'] = $pelanggan->id;
			    $di['point'] = $pointGet->remark;
    			$di['custom_id'] = 0;
			    $di['custom_type'] = 'check in';
			    $di['custom_type_sub'] = 'weekly';
			    $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type'].' '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
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
			    $this->glphm->trans_commit();
					// $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '-', $di['point']);
			    // $this->glphm->trans_commit();

      		$pointGetFinal .= "+".$di['point'];
      		$pointTextType++;
		    }
      }

      //check in monthly
      //https://www.geeksforgeeks.org/how-to-get-last-day-of-a-month-from-date-in-php/
      if($dateNow == date("Y-m-t")){
      	$totalCheckIn = $this->glphm->countCheckIn($nation_code, $pelanggan->id, date("Y-m-01"), $dateNow);
      	if($totalCheckIn == date("t")){
			    //get point
			    $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E3");
			    if (!isset($pointGet->remark)) {
			      $pointGet = new stdClass();
			      $pointGet->remark = 500;
			    }

			    //insert into database
			    $di = array();
			    $di['nation_code'] = $nation_code;
			    $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
			    $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
			    $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
			    $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
			    $di['b_user_id'] = $pelanggan->id;
			    $di['point'] = $pointGet->remark;
    			$di['custom_id'] = 0;
			    $di['custom_type'] = 'check in';
			    $di['custom_type_sub'] = 'monthly';
			    $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type'].' '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
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
			    $this->glphm->trans_commit();
					// $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '-', $di['point']);
			    // $this->glphm->trans_commit();

      		$pointGetFinal .= "+".$di['point'];
      		$pointTextType++;
		    }
      }

      $this->status = 200;

	  	if($pelanggan->language_id == 2){
		    if($pointTextType == 1){
		    	$this->message = 'Anda mendapatkan +'.$pointGetFinal.' poin SELLON';
		    }else{
		    	$this->message = 'Anda mendapatkan '.$pointGetFinal.' bonus poin SELLON';
		    }
		  }else{
		    if($pointTextType == 1){
		    	$this->message = 'You got +'.$pointGetFinal.' SELLON point';
		    }else{
		    	$this->message = 'You got '.$pointGetFinal.' SELLON bonus point';
		    }
		  }

    } else {
      $this->glphm->trans_rollback();
      $this->status = 8011;
      $this->message = 'Failed updating data';
    }

    $this->glphm->trans_end();

    if($this->status == 200){
    	$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "check_in","custom 200");
    }else{
    	$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "check_in");
    }
  }

  public function barubisasettgl()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    // $data['chat'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "check_in");
      die();
    }

    //check apikey
    $apikey = $this->input->get('apikey');
    $c = $this->apikey_check($apikey);
    if (!$c) {
      $this->status = 400;
      $this->message = 'Missing or invalid API key';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "check_in");
      die();
    }

    //check apisess
    $apisess = $this->input->get('apisess');
    $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
    if (!isset($pelanggan->id)) {
      $this->status = 401;
      $this->message = 'Missing or invalid API session';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "check_in");
      die();
    }

    $dateNow = date("Y-m-d", strtotime($this->input->get('tgl')));
    // $dateNow = date("Y-m-d");

  	$HistoryCheckIn = $this->glphm->checkAlreadyInDB($nation_code, "", "", "", "", "", $pelanggan->id, 0, "check in", "daily", $dateNow);
    if (isset($HistoryCheckIn->id)) {
      $this->status = 7280;
      $this->message = 'you have already check in';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "check_in");
      die();
    }

    $pointGetFinal = "";
    $pointTextType = 0; //1 = daily, 2 = weekly, 3 = monthly

    //start transaction
    // $this->glphm->trans_start();

    //get point
    $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E1");
    if (!isset($pointGet->remark)) {
      $pointGet = new stdClass();
      $pointGet->remark = 10;
    }

    $pelangganAddress = $this->bua->getByUserIdDefault($nation_code, $pelanggan->id);

    //insert into database
    $di = array();
    $di['nation_code'] = $nation_code;
    $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
    $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
    $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
    $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
    $di['b_user_id'] = $pelanggan->id;
    $di['point'] = $pointGet->remark;
    $di['custom_id'] = 0;
    $di['custom_type'] = 'check in';
    $di['custom_type_sub'] = 'daily';
    $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type'].' '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
    $di['cdate'] = date("Y-m-d 00:00:01", strtotime($dateNow));
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
    // $this->glphm->trans_commit();
		// $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '-', $di['point']);
    // $this->glphm->trans_commit();

    $pointGetFinal .= $di['point'];
    $pointTextType++;

    //check in weekly
    //https://www.geeksforgeeks.org/how-to-get-last-day-of-a-month-from-date-in-php/
    if(date("l", strtotime($dateNow)) == "Sunday"){
    	$totalCheckIn = $this->glphm->countCheckIn($nation_code, $pelanggan->id, date("Y-m-d", strtotime($dateNow."-6 days")), $dateNow);
    	if($totalCheckIn == 7){
		    //get point
		    $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E2");
		    if (!isset($pointGet->remark)) {
		      $pointGet = new stdClass();
		      $pointGet->remark = 30;
		    }

		    //insert into database
		    $di = array();
		    $di['nation_code'] = $nation_code;
		    $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
		    $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
		    $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
		    $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
		    $di['b_user_id'] = $pelanggan->id;
		    $di['point'] = $pointGet->remark;
    		$di['custom_id'] = 0;
		    $di['custom_type'] = 'check in';
		    $di['custom_type_sub'] = 'weekly';
		    $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type'].' '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
    		$di['cdate'] = date("Y-m-d 00:00:01", strtotime($dateNow));
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
		    // $this->glphm->trans_commit();
				// $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '-', $di['point']);
		    // $this->glphm->trans_commit();

    		$pointGetFinal .= "+".$di['point'];
    		$pointTextType++;
	    }
    }

    //check in monthly
    //https://www.geeksforgeeks.org/how-to-get-last-day-of-a-month-from-date-in-php/
    if($dateNow == date("Y-m-t")){
    	$totalCheckIn = $this->glphm->countCheckIn($nation_code, $pelanggan->id, date("Y-m-01"), $dateNow);
    	if($totalCheckIn == date("t")){
		    //get point
		    $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", "E3");
		    if (!isset($pointGet->remark)) {
		      $pointGet = new stdClass();
		      $pointGet->remark = 500;
		    }

		    //insert into database
		    $di = array();
		    $di['nation_code'] = $nation_code;
		    $di['b_user_alamat_location_kelurahan'] = $pelangganAddress->kelurahan;
		    $di['b_user_alamat_location_kecamatan'] = $pelangganAddress->kecamatan;
		    $di['b_user_alamat_location_kabkota'] = $pelangganAddress->kabkota;
		    $di['b_user_alamat_location_provinsi'] = $pelangganAddress->provinsi;
		    $di['b_user_id'] = $pelanggan->id;
		    $di['point'] = $pointGet->remark;
    		$di['custom_id'] = 0;
		    $di['custom_type'] = 'check in';
		    $di['custom_type_sub'] = 'monthly';
		    $di['custom_text'] = $pelanggan->fnama.' has '.$di['custom_type'].' '.$di['custom_type_sub'].' and get '.$di['point'].' point(s)';
    		$di['cdate'] = date("Y-m-d 00:00:01", strtotime($dateNow));
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
		    // $this->glphm->trans_commit();
				// $this->glrm->updateTotal($nation_code, $pelanggan->id, 'total_point', '-', $di['point']);
		    // $this->glphm->trans_commit();

    		$pointGetFinal .= "+".$di['point'];
    		$pointTextType++;
	    }
    }

    $this->status = 200;

  	if($pelanggan->language_id == 2){
	    if($pointTextType == 1){
	    	$this->message = 'Anda mendapatkan +'.$pointGetFinal.' poin SELLON';
	    }else{
	    	$this->message = 'Anda mendapatkan '.$pointGetFinal.' bonus poin SELLON';
	    }
	  }else{
	    if($pointTextType == 1){
	    	$this->message = 'You got +'.$pointGetFinal.' SELLON point';
	    }else{
	    	$this->message = 'You got '.$pointGetFinal.' SELLON bonus point';
	    }
	  }

    // $this->glphm->trans_end();

    if($this->status == 200){
    	$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "check_in","custom 200");
    }else{
    	$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "check_in");
    }
  }

  public function hapus()
  {
    //initial
    $dt = $this->__init();

    //default result
    $data = array();
    // $data['chat'] = new stdClass();

    //check nation_code
    $nation_code = $this->input->get('nation_code');
    $nation_code = $this->nation_check($nation_code);
    if (empty($nation_code)) {
      $this->status = 101;
      $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data);
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
      $this->__json_out($data);
      die();
    }

    $dateNow = $this->input->get('tgl');
    // $dateNow = date("Y-m-d");

    $HistoryCheckIn = $this->glphm->checkAlreadyInDB($nation_code, "", "", "", "", "", $pelanggan->id, 0, "check in", "daily", $dateNow);
    if (isset($HistoryCheckIn->id)) {
      $this->status = 7280;
      $this->message = 'you have not check in';
      $this->__json_out($data);
      die();
    }

    if ($HistoryCheckIn->is_calculated == 0) {
      $this->status = 7280;
      $this->message = 'point have not transfer';
      $this->__json_out($data);
      die();
    }

    $HistoryCheckIn1 = $this->glphm->checkAlreadyInDB($nation_code, "", "", "", "", "", $pelanggan->id, 0, "check in", "weekly", $dateNow);
    if (isset($HistoryCheckIn1->id)) {
	    if ($HistoryCheckIn1->is_calculated != 0) {
	    }else{
	      $this->status = 7280;
	      $this->message = 'point have not transfer';
	      $this->__json_out($data);
	      die(); 	
	    }
    }

    $HistoryCheckIn2 = $this->glphm->checkAlreadyInDB($nation_code, "", "", "", "", "", $pelanggan->id, 0, "check in", "monthly", $dateNow);
    if (isset($HistoryCheckIn2->id)) {
	    if ($HistoryCheckIn2->is_calculated != 0) {
	    }else{
	      $this->status = 7280;
	      $this->message = 'point have not transfer';
	      $this->__json_out($data);
	      die(); 	
	    }
    }

    $this->glphm->del($nation_code, $HistoryCheckIn->id, $HistoryCheckIn->b_user_id, $HistoryCheckIn->kelurahan, $HistoryCheckIn->kecamatan, $HistoryCheckIn->kabkota, $HistoryCheckIn->provinsi);

    //check in weekly
    if(date("l", strtotime($dateNow)) == "Sunday"){

    	$HistoryCheckIn = $this->glphm->checkAlreadyInDB($nation_code, "", "", "", "", "", $pelanggan->id, 0, "check in", "weekly", $dateNow);
    	if (isset($HistoryCheckIn->id)) {
  			$this->glphm->del($nation_code, $HistoryCheckIn->id, $HistoryCheckIn->b_user_id, $HistoryCheckIn->kelurahan, $HistoryCheckIn->kecamatan, $HistoryCheckIn->kabkota, $HistoryCheckIn->provinsi);
	    }

		}

    //check in monthly
    if($dateNow == date("Y-m-t")){

    	$HistoryCheckIn = $this->glphm->checkAlreadyInDB($nation_code, "", "", "", "", "", $pelanggan->id, 0, "check in", "monthly", $dateNow);
    	if (isset($HistoryCheckIn->id)) {
  			$this->glphm->del($nation_code, $HistoryCheckIn->id, $HistoryCheckIn->b_user_id, $HistoryCheckIn->kelurahan, $HistoryCheckIn->kecamatan, $HistoryCheckIn->kabkota, $HistoryCheckIn->provinsi);
	    }

    }

    $this->status = 200;
    $this->message = 'Success';

    $this->__json_out($data);

  }

}
