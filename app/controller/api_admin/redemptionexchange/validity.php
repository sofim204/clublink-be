<?php
class Validity extends JI_Controller{

    var $status_in_table = array('request exchange','approved by admin','insufficient wallet balance','rejected by admin','rejected by system');

	public function __construct(){
    parent::__construct();
		$this->lib("seme_purifier");
        $this->lib("seme_log");
		$this->load("api_admin/h_redemptionexchange_validity_model",'list_model');
		$this->load("api_admin/h_redemptionexchange_history_model",'list_history_model');
        $this->load("api_mobile/b_user_alamat_model", "bua");
		// $this->load("api_admin/c_community_report_model",'list_report_model');
		// $this->load("api_admin/g_leaderboard_model",'glm_model');
		$this->load("api_admin/b_user_model",'bu_model');
		// $this->load("api_admin/common_code_model", "ccm");
		$this->load("api_admin/g_leaderboard_point_history_model", "glphm");
		// $this->load("api_admin/a_pengguna_model", "apm");

		//by Donny Dennison - 28 december 2022 17:46
		//bug take down community reply/discussion dont reduce the total reply/discussion
		// $this->load("api_admin/c_community_discussion_model",'discussion_model');

		$this->load("api_mobile/d_pemberitahuan_model", "dpem");
		// $this->load("api_mobile/b_user_setting_model", "busm");

        $this->load("api_admin/h_redemptionexchange_user_influencer_model", "influencer_model");

        $this->load("api_mobile/g_leaderboard_point_total_model", "glptm");

		$this->current_parent = 'redemptionexchange';
		$this->current_page = 'redemptionexchange_validity';

        
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

    private function __callBlockChainSPTBalance($userWalletCode){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_URL, $this->blockchain_api_host."Wallet/GetSPTBalanceWithEncryption");

        $headers = array();
        $headers[] = 'Content-Type:  application/json';
        $headers[] = 'Accept:  application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $postdata = array(
          'userWalletCode' => $this->__encryptdecrypt($userWalletCode,"encrypt"),
          'countryIsoCode' => $this->blockchain_api_country

        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
          return 0;
          //echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        $this->seme_log->write("api_mobile", "url untuk block chain server ".$this->blockchain_api_host."Wallet/GetSPTBalanceWithEncryption. data send to blockchain api ". json_encode($postdata).". isi response block chain server ". $result);

        return $result;

    }

    private function __encryptdecrypt($text, $type="encrypt"){

        if($type == "encrypt"){

            // Encrypt using the public key
            openssl_public_encrypt($text, $encrypted, $this->blockchain_api_public_key);

            return base64_encode($encrypted);

        }else if($type == "decrypt"){

            // Decrypt the data using the private key
            openssl_private_decrypt(base64_decode($text), $decrypted, openssl_pkey_get_private($this->blockchain_api_private_key, $this->blockchain_api_private_key_password));

            return $decrypted;

        }

    }

	private function __imageValidation($imgkey){
		$data = array();
		//image validation
		if(!isset($_FILES[$imgkey])){
			$this->status = 102;
			$this->message = 'Image icon file are required';
			$this->__json_out($data);
			die();
		}
		if(empty($_FILES[$imgkey]['tmp_name'])){
			$this->status = 103;
			$this->message = 'Failed upload image icon';
			$this->__json_out($data);
			die();
		}
		if($_FILES[$imgkey]['size']<=0){
			$this->status = 104;
			$this->message = 'Failed upload image icon';
			$this->__json_out($data);
			die();
		}
		if($_FILES[$imgkey]['size']>100000){
			$this->status = 105;
			$this->message = 'Image icon file size too big, please try another image';
			$this->__json_out($data);
			die();
		}
		if(mime_content_type($_FILES[$imgkey]['tmp_name']) == "image/webp"){
			$this->status = 106;
			$this->message = 'WebP file format currently unsupported by this system, please try another image';
			$this->__json_out($data);
			die();
		}
		if(mime_content_type($_FILES[$imgkey]['tmp_name']) == "image/webp"){
			$this->status = 106;
			$this->message = 'WebP file format currently unsupported by this system, please try another image';
			$this->__json_out($data);
			die();
		}
		$ext = strtolower(pathinfo($_FILES[$imgkey]['name'], PATHINFO_EXTENSION));
		if (!in_array($ext, array("jpg", "png","jpeg"))) {
			$this->status = 107;
			$this->message = 'Invalid file extension, only supported PNG or JPG extension';
			$this->__json_out($data);
			die();
		}
	}

	private function __slugify($text){
	  // replace non letter or digits by -
	  $text = preg_replace('~[^\pL\d]+~u', '-', $text);
	  // transliterate
	  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
	  // remove unwanted characters
	  $text = preg_replace('~[^-\w]+~', '', $text);
	  // trim
	  $text = trim($text, '-');
	  // remove duplicate -
	  $text = preg_replace('~-+~', '-', $text);
	  // lowercase
	  $text = strtolower($text);
	  if (empty($text)) {
	    return 'n-a';
	  }
	  return $text;
	}

	// by Muhammad Sofi 28 December 2021 18:14 | testing remove json_decode
    // private function __convertToEmoji($text){
    //     $value = ($text);
    //     if ($value) {
    //         return ($text);
    //     } else {
    //         return ('"'.$text.'"');
    //     }
    // }

	// by Muhammad Sofi 28 December 2021 20:00 | read text with emoji
	private function __convertToEmoji($text){
		$value = $text;
		$readTextWithEmoji = preg_replace("/\\\\u([0-9A-F]{2,5})/i", "&#x$1;", $value);
		return $readTextWithEmoji;
	}

	public function index(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$draw = $this->input->post("draw");
		$sval = $this->input->post("search");
		$sSearch = $this->input->post("sSearch");
		$sEcho = $this->input->post("sEcho");
		$page = $this->input->post("iDisplayStart");
		$pagesize = $this->input->post("iDisplayLength");

		$iSortCol_0 = $this->input->post("iSortCol_0");
		$sSortDir_0 = $this->input->post("sSortDir_0");

        $fromDate = $this->input->post("from_date");
        $toDate = $this->input->post("to_date");
        // $userId = $this->input->post("user_id");
        $type_list = 'validity';
        $statusFilter = $this->input->post("status");

		$sortCol = "date";
		$sortDir = strtoupper($sSortDir_0);
		if(empty($sortDir)) $sortDir = "DESC";
		if(strtolower($sortDir) != "desc"){
			$sortDir = "ASC";
		}

		// START by Muhammad Sofi 16 February 2022 14:32 | change filter start date to end date
        //validating date interval
        if (strlen($fromDate)==10) {
            $fromDate = date("Y-m-d", strtotime($fromDate));
        } else {
            $fromDate = "";
        }
        if (strlen($toDate)==10) {
            $toDate = date("Y-m-d", strtotime($toDate));
        } else {
            $toDate = "";
        }
        // END by Muhammad Sofi 16 February 2022 14:32 | change filter start date to end date

		switch($iSortCol_0){
			case 0:
				$sortCol = "no";
				break;
			case 1:
				$sortCol = "id";
				break;
			case 2:
				$sortCol = "cdate";
				break;
			case 3:
				$sortCol = "approved_by_admin_date";
				break;
			case 4:
				$sortCol = "redemption_exchange_name";
				break;
			default:
				$sortCol = "cdate";
		}

		if(empty($draw)) $draw = 0;
		if(empty($pagesize)) $pagesize=10;
		if(empty($page)) $page=0;

		$keyword = $sSearch;

		$this->status = 200;
		$this->message = 'Success';

		$dcount = $this->list_model->countAll($nation_code, $keyword, $fromDate, $toDate, $type_list, $statusFilter, $this->status_in_table);
		$ddata = $this->list_model->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $fromDate, $toDate, $type_list, $statusFilter, $this->status_in_table);
		
		foreach($ddata as &$gd){

            if (isset($gd->cdate)) {
				$gd->cdate = date("d M Y H:i:s", strtotime($gd->cdate));
            }

            if (isset($gd->custom_status_date)) {
				$gd->custom_status_date = date("d M Y H:i:s", strtotime($gd->custom_status_date));
            }

            if (isset($gd->status)) {
                $status = "";
				if($gd->status == 'approved by admin') {
                    $status = '<label class="label label-success">Accepted by Admin</label>';
                } else if($gd->status == 'insufficient wallet balance') {
                    $status = '<label class="label label-success">Accepted (Insufficient Balance-2nd)</label>';
                } else if($gd->status == 'rejected by admin') {
                    $status = '<label class="label label-danger">Rejected by Admin</label>';
                } else if($gd->status == 'rejected by system') {
                    $status = '<label class="label label-danger">Rejected (Insufficient Balance-1st)</label>';
                } else {
                    $status = '<label class="label label-warning">Ongoing</label>';
                }				 
                $gd->status = '<center><span>'.$status.'</span></center><div style="margin-bottom: 3px;"></div>';
            }

            // if (isset($gd->is_active)) {
            //     $status = "";
			// 	if(!empty($gd->is_active)) $status = '<label class="label label-success">Active</label>';
			// 	else $status = '<label class="label label-default">Inactive</label>';
            //     $gd->is_active = '<span>'.$status.'</span><br /><div style="margin-bottom: 5px;"></div>';

			// 	// if(!empty($gd->is_report)) $status = '<label class="label label-warning">Yes</label>';
			// 	// else $status = '<label class="label label-default">No</label>';
			// 	// $gd->is_active .= '<span>Reported: '.$status.' </span><br /><div style="margin-bottom: 5px;"></div>';

			// 	// if(!empty($gd->is_take_down)) $status = '<label class="label label-danger">Yes</label>';
			// 	// else $status = '<label class="label label-default">No</label>';
			// 	// $gd->is_active .= '<span>Takedown : '.$status.' </span><br />';
            // }
		}
		//sleep(3);
		$another = array();
		$this->__jsonDataTable($ddata,$dcount);
	}

    public function detail($id)
    {
        // $id = (int) $id;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($id)) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $this->status = 200;
        $this->message = 'Success';

        
        $data = $this->list_model->getById($nation_code, $id, $this->status_in_table);
        if (!isset($data->id)) {
            $this->status = 451;
            $this->message = 'Invalid ID or ID has been deleted';
            $this->__json_out($data);
            die();
        }

        if (isset($data->status)) {
            if($data->status == 'request exchange') {
                $data->status_no = 1;
            } else {
                $data->status_no = 2;
            }				 
        }

        if (isset($data->status)) {
            if($data->status == 'rejected by admin') {
                $data_note_rejected = $this->list_history_model->getNoteHistory($nation_code, $id, $data->status);
        
                if (isset($data_note_rejected->note)){
                    if ($data_note_rejected->note != ''){
                        $data->note_rejected = '<br>Reason Rejected => <b>'.$data_note_rejected->note.'</b>';
                    }
                }
            } 				 
        }

        $data->cdate = $data->cdate ? date("d M Y H:i:s", strtotime($data->cdate)) : "";	

        $user = $this->bu_model->getByIdRedeem($nation_code, $data->b_user_id);
        $data->user_name = '';
        $data->user_email = '';
        $data->user_reg_date = '';
        $data->user_telp = '';
        $data->user_total_recruited = '';
        $data->user_is_influencer = '';
        $data->user_wallet_balance = '';
        $data->user_ip_address = '';
        $data->user_permanent_inactive = '';
        $data->user_recommender = '';
        $data->user_device_id = '';
        $data->user_address = '';
        $data->user_signup_method = '';
        if ($user) {
            $data->user_name = $user->nama;
            $data->user_email = $user->email;
            $data->user_reg_date = $user->cdate ? date("d M Y H:i:s", strtotime($user->cdate)) : "";
            $data->user_telp = $user->telp;
            $data->user_total_recruited = $user->total_recruited;
            $influencer = $this->influencer_model->getById($data->b_user_id);
            $data->user_is_influencer = ($influencer > 0) ? "Yes" : "No";
            // check wallet balance
            // $response = json_decode($this->__callBlockChainSPTBalance($user->user_wallet_code));
            // if(isset($response->responseCode)){
            //     if($response->responseCode == 0){
            //         $data->user_wallet_balance = number_format($response->amount, 0, ',', '.');

            //         // by Muhammad Sofi - 9 November 2023 | update total point of user
            //         // check if data exist or not
            //         $checkData = $this->glptm->getByUserId($nation_code, $data->b_user_id);
            //         if(!isset($checkData->b_user_id)) {
            //             // create data
            //             $di = array();
            //             $di['nation_code'] = $nation_code;
            //             $di['b_user_id']   = $data->b_user_id;
            //             $di['total_post'] = 0;
            //             $di['total_point'] = $data->user_wallet_balance;
            //             $this->glptm->set($di);
            //         } else {
            //             // update data
            //             $du = array();
            //             $du['total_point'] = $data->user_wallet_balance;
            //             $this->glptm->update($nation_code, $data->b_user_id, $du);
            //         }
            //     }
            // }
            // end check wallet balance
            
            // start get SPT Balance
            $getPointNow = $this->glptm->getByUserId($nation_code, $data->b_user_id);
            $pointRockPaperScissors = $this->glphm->countPointCustomType($nation_code, $data->b_user_id, date('Y-m-d', strtotime('-1 days')), date('Y-m-d'), "rock paper scissors", "win");
            if(isset($getPointNow->b_user_id)) {
                $data->user_wallet_balance = number_format($getPointNow->total_point, 0, ',', '.')." (rps: ".$pointRockPaperScissors.")";
            }else{
                $data->user_wallet_balance = number_format(0, 0, ',', '.')." (rps: ".$pointRockPaperScissors.")";
            }
            // end get SPT Balance

            $data->user_ip_address = $user->ip_address;
            $data->user_permanent_inactive = $user->is_permanent_inactive == 1 ? 'Active' : 'Inactive';
            $data->user_recommender = $user->fnama_recommender;
            $data->user_device_id = $user->device_id;
            $data->user_address = $user->alamat2;
            $data->user_signup_method = $user->register_from;
        }
           
        $this->__json_out($data);
    }

	public function approve() {
		$d = $this->__init();
		$data = array();

		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$id = $this->input->get('id') ? $this->input->get('id') : '';
		$admin_name = $pengguna->username ? $pengguna->username : '';

		if(empty($id)) {
			$this->status = 400;
			$this->message = 'No Redemption Exchange Id';
			$this->__json_out($data);
			die();
		}

		$redemptionexchange_data = $this->list_model->getById($nation_code, $id);
        if (!$redemptionexchange_data) {
            $this->status = 400;
			$this->message = 'No Redemption Exchange Id Not Found';
			$this->__json_out($data);
			die();
        }
        // cek status apakah sudah 'request exchange' 
        if ($redemptionexchange_data->status != 'request exchange') {
            $this->status = 400;
			$this->message = 'The status not allowed';
			$this->__json_out($data);
			die();
        }
		$b_user_id_requester = $redemptionexchange_data->b_user_id;
		$user = $this->bu_model->getById($nation_code, $b_user_id_requester);

		//start transaction and lock table
		$this->list_model->trans_start();

        // Pengecekan wallet ada / tidak
        // $response = json_decode($this->__callBlockChainSPTBalance($user->user_wallet_code));

        // if(isset($response->responseCode)){
        //   if($response->responseCode == 0){
        //     if ($response->amount >= (int) $redemptionexchange_data->cost_spt ) { // jika cukup
        //         // insert ke g_leaderboard
        //         $requesterAddress = $this->bua->getByUserIdDefault($nation_code, $b_user_id_requester);

        //         $leaderBoardHistoryId = $this->glphm->getLastId($nation_code, $user->id, $requesterAddress->kelurahan, $requesterAddress->kecamatan, $requesterAddress->kabkota, $requesterAddress->provinsi);
        //         $dp = array();
        //         $dp['nation_code'] = $nation_code;
        //         $dp['id'] = $leaderBoardHistoryId;
        //         $dp['b_user_alamat_location_kelurahan'] = $requesterAddress->kelurahan;
        //         $dp['b_user_alamat_location_kecamatan'] = $requesterAddress->kecamatan;
        //         $dp['b_user_alamat_location_kabkota'] = $requesterAddress->kabkota;
        //         $dp['b_user_alamat_location_provinsi'] = $requesterAddress->provinsi;
        //         $dp['b_user_id'] = $b_user_id_requester;
        //         $dp['plusorminus'] = "-";
        //         $dp['point'] = $redemptionexchange_data->cost_spt; // cost_spt
        //         $dp['custom_id'] = $id; // h_point_redemption_exchange.id
        //         $dp['custom_type'] = $redemptionexchange_data->name_point_history;
        //         $dp['custom_type_sub'] = $redemptionexchange_data->type;
        //         $dp['custom_text'] = $user->fnama.' has redeem '.$dp['custom_type'].' '.$dp['custom_type_sub'].' and deduct '.$dp['point'].' point(s)';
        //         $res = $this->glphm->set($dp);
        //         if (!$res) {
        //             $this->list_model->trans_rollback();
        //             $this->list_model->trans_end();
        //             $this->status = 1108;
        //             $this->message = "Error while update leaderboard history, please try again later";
        //             $this->__json_out($data);
        //             die();
        //         }

        //         // Proses Approve
        //         $this->__prosesRedemptionExchange($nation_code, $id, $admin_name, $user, $data, "approved by admin", "Selamat, permintaan redeem kamu sedang diproses.", "Congratulations! Redemption process is ongoing.");

        //     }

        //     // by Muhammad Sofi - 9 November 2023 | update total point of user
        //     // check if data exist or not
        //     $checkData = $this->glptm->getByUserId($nation_code, $user->id);
        //     if(!isset($checkData->b_user_id)) {
        //         // create data
        //         $di = array();
        //         $di['nation_code'] = $nation_code;
        //         $di['b_user_id']   = $user->id;
        //         $di['total_post'] = 0;
        //         $di['total_point'] = $response->amount;
        //         $this->glptm->set($di);
        //     } else {
        //         // update data
        //         $du = array();
        //         $du['total_point'] = $response->amount;
        //         $this->glptm->update($nation_code, $user->id, $du);
        //     }
        //   }
        // }

        // start get SPT Balance
        $getPointNow = $this->glptm->getByUserId($nation_code, $b_user_id_requester);
        if(isset($getPointNow->b_user_id)) {
            $spt = $getPointNow->total_point;
        }else{
            $spt = 0;
        }
        // end get SPT Balance

        if ($spt >= (int) $redemptionexchange_data->cost_spt ) { // jika cukup
            // insert ke g_leaderboard
            $requesterAddress = $this->bua->getByUserIdDefault($nation_code, $b_user_id_requester);

            // $leaderBoardHistoryId = $this->glphm->getLastId($nation_code, $user->id, $requesterAddress->kelurahan, $requesterAddress->kecamatan, $requesterAddress->kabkota, $requesterAddress->provinsi);
            $dp = array();
            $dp['nation_code'] = $nation_code;
            // $dp['id'] = $leaderBoardHistoryId;
            $dp['b_user_alamat_location_kelurahan'] = $requesterAddress->kelurahan;
            $dp['b_user_alamat_location_kecamatan'] = $requesterAddress->kecamatan;
            $dp['b_user_alamat_location_kabkota'] = $requesterAddress->kabkota;
            $dp['b_user_alamat_location_provinsi'] = $requesterAddress->provinsi;
            $dp['b_user_id'] = $b_user_id_requester;
            $dp['plusorminus'] = "-";
            $dp['point'] = $redemptionexchange_data->cost_spt; // cost_spt
            $dp['custom_id'] = $id; // h_point_redemption_exchange.id
            $dp['custom_type'] = $redemptionexchange_data->name_point_history;
            $dp['custom_type_sub'] = $redemptionexchange_data->type;
            $dp['custom_text'] = $user->fnama.' has redeem '.$dp['custom_type'].' '.$dp['custom_type_sub'].' and deduct '.$dp['point'].' point(s)';
            $endDoWhile = 0;
            do{
                $leaderBoardHistoryId = $this->GUIDv4();
                $checkId = $this->glphm->checkId($nation_code, $leaderBoardHistoryId);
                if($checkId == 0){
                $endDoWhile = 1;
                }
            }while($endDoWhile == 0);
            $dp['id'] = $leaderBoardHistoryId;
            $res = $this->glphm->set($dp);
            if (!$res) {
                $this->list_model->trans_rollback();
                $this->list_model->trans_end();
                $this->status = 1108;
                $this->message = "Error while update leaderboard history, please try again later";
                $this->__json_out($data);
                die();
            }

            // Proses Approve
            $this->__prosesRedemptionExchange($nation_code, $id, $admin_name, $user, $data, "approved by admin", "Selamat, permintaan redeem kamu sedang diproses.", "Congratulations! Redemption process is ongoing.");

        }

		// Jika wallet gagal / tidak ada / tidak cukup
        // Proses Reject
        $this->__prosesRedemptionExchange($nation_code, $id, $admin_name, $user, $data, "rejected by system", "Maaf, permintaan redeem PULSA kamu ditolak.", "Sorry, your request for PULSA redemption has been declined.");
		
	}

    public function reject() {
		$d = $this->__init();
		$data = array();

		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$id = $this->input->get('id') ? $this->input->get('id') : '';
		$reason = $this->input->get('reason') ? $this->input->get('reason') : '';
        if ($reason == 1) {
            $reason_id = "Maaf, Kamu tidak dapat menukarkan PULSA karena tidak memiliki cukup aktivitas di Sellon.";
            $reason_en = "Sorry, you can't redeem PULSA as you don't have enough activity on Sellon.";
        } else if($reason == 2) {
            $reason_id = "Maaf, Kamu tidak berhak meminta PULSA.";
            $reason_en = "Sorry, you're not eligible to request PULSA.";
        } else if($reason == 3) {
            $reason_id = "Maaf, Sellon memiliki kebijakan yang mencegah penyalahgunaan untuk meminta penukaran PULSA.";
            $reason_en = "Sorry, Sellon has a policy that prevents abusers from requesting PULSA redemption.";
        } else {
            $reason_id = "Maaf, permintaan redeem PULSA kamu ditolak.";
            $reason_en = "Sorry, your request for PULSA redemption has been declined.";
        }
        
		$admin_name = $pengguna->username ? $pengguna->username : '';

		if(empty($id)) {
			$this->status = 400;
			$this->message = 'No Redemption Exchange Id';
			$this->__json_out($data);
			die();
		}

		$redemptionexchange_data = $this->list_model->getById($nation_code, $id);
        if (!$redemptionexchange_data) {
            $this->status = 400;
			$this->message = 'No Redemption Exchange Id Not Found';
			$this->__json_out($data);
			die();
        }
        // cek status apakah sudah 'request exchange' 
        if ($redemptionexchange_data->status != 'request exchange') {
            $this->status = 400;
			$this->message = 'The status not allowed';
			$this->__json_out($data);
			die();
        }
		$b_user_id_requester = $redemptionexchange_data->b_user_id;
		$user = $this->bu_model->getById($nation_code, $b_user_id_requester);

		//start transaction and lock table
		$this->list_model->trans_start();

        // Proses Reject
        $this->__prosesRedemptionExchange($nation_code, $id, $admin_name, $user, $data, "rejected by admin", $reason_id, $reason_en);

    }


    private function __prosesRedemptionExchange($nation_code="", $id="", $admin_name="", $user=[], $data=[], $statusRedemption="", $message_id="", $message_inggris="") {
        //update table
		$du = array();
		$du['status'] = $statusRedemption;
        $du['custom_status_date'] = "NOW()";
		$du['ldate'] = "NOW()";
		$res = $this->list_model->update($nation_code, $id, $du);
		if (!$res) {
			$this->list_model->trans_rollback();
			$this->list_model->trans_end();
			$this->status = 1108;
			$this->message = "Error while update redemption exchange, please try again later";
			$this->__json_out($data);
			die();
		} 

        // insert to table history
		$di = array();
		$di['nation_code'] = $nation_code;
		$di['id'] = $this->GUIDv4();
		$di['h_point_redemption_exchange_id'] = $id;
		$di['status'] = $statusRedemption;
        $di['note'] = null;
        if ($statusRedemption == "rejected by admin") {
            $di['note'] = $message_id."<p><i> (".$message_inggris.")</i></p>";
        }		
		$di['admin_name'] = $admin_name;
		$this->list_history_model->set($di);
        if (!$res) {
			$this->list_model->trans_rollback();
			$this->list_model->trans_end();
			$this->status = 1108;
			$this->message = "Error while insert redemption exchange history, please try again later";
			$this->__json_out($data);
			die();
		}

        // ============= save to dpem ====================
        $dpe = array();
        $dpe['nation_code'] = $nation_code;
        $dpe['b_user_id'] = $user->id;
        $dpe['id'] = $this->dpem->getLastId($nation_code, $user->id);
        $dpe['type'] = "point_redemption_exchange";
        if($user->language_id == 2) {
            $dpe['judul'] = "Perhatian";
            $dpe['teks'] =  $message_id;
        } else {
            $dpe['judul'] = "Attention";
            $dpe['teks'] =  $message_inggris;
        }

        $dpe['gambar'] = 'media/pemberitahuan/promotion.png';
        $dpe['cdate'] = "NOW()";
        $extras = new stdClass();
        $extras->id = $id;
        $extras->title = html_entity_decode("Point Redemption Exchange",ENT_QUOTES);
        if($user->language_id == 2) { 
            $extras->judul = "Perhatian";
            $extras->teks =  $message_id;
        } else {
            $extras->judul = "Attention";
            $extras->teks =  $message_inggris;
        }

        $dpe['extras'] = json_encode($extras);
        $this->dpem->set($dpe);
        // ============== End save to dpem ========================

        $this->list_model->trans_commit();

        $this->status = 200;
        if ($statusRedemption == 'rejected by system'){
            $this->status = 201;
        }        
        $this->message = 'Success';

        // cek fcm token
        $tokens = $user->fcm_token; //device token
        if ($tokens){ // jika ada fcm token => push notifitication
            if($user->device == "ios"){
				//push notif to ios
				$device = "ios"; //jenis device
			}else{
				//push notif to android
				$device = "android"; //jenis device
			}
            if(!is_array($tokens)) 
            {
                $tokens = array($tokens);
            }
            if($user->language_id == 2){
                $title = "Perhatian";
                $message = $message_id;
            } else {
                $title = "Attention";
                $message = $message_inggris;
            }
            
            $image = 'media/pemberitahuan/promotion.png';
            $type = 'point_redemption_exchange';
            $payload = new stdClass();
            $payload->id = $id;
            $payload->title = html_entity_decode("Point Redemption Exchange",ENT_QUOTES);
            // $payload->harga_jual = $community->harga_jual;
            // $payload->foto = base_url().$community->thumb;
            if($user->language_id == 2) {
                $payload->judul = "Perhatian";
                //by Donny Dennison
                //dicomment untuk handle message too big, response dari fcm
                // $payload->teks = strip_tags(html_entity_decode($di['teks']));
                // $payload->teks = "You get a reply from your neighbors (".$tempTitle->{'title'}.")";
                $payload->teks = $message_id;
            } else {
                $payload->judul = "Attention";
                $payload->teks = $message_inggris;
            }

            $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);            
        }
        // END cek fcm token

        //end transaction
		$this->list_model->trans_end();

        $this->__json_out($data);
    }

    public function exchangeDetailStatusIndonesia($status="") {
        if($status == "request exchange"){
            return "pertukaran permintaan";
        }

        if($status == "rejected by admin"){
            return "ditolak admin";
        }

        if($status == "approved by admin"){
            return "disetujui admin";
        }

        if($status == "insufficient wallet balance"){
            return "saldo wallet tidak cukup";
        }

        if($status == "wallet balance deducted"){
            return "saldo wallet telah dikurangi";
        }

        if($status == "top up problem"){
            return "isi ulang bermasalah";
        }

        if($status == "wallet balance refunded"){
            return "saldo wallet telah dikembalikan";
        }

        if($status == "success"){
            return "berhasil";
        }
    }
}