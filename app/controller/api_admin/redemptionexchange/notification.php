<?php
class Notification extends JI_Controller{

    var $status_in_table = array('wallet balance deducted','top up problem','wallet balance refunded');

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
		$this->current_page = 'redemptionexchange_notification';
	}

	private function __checkDir($periode){
		if(!is_dir(SENEROOT.'media/')) mkdir(SENEROOT.'media/',0777);
		if(!is_dir(SENEROOT.'media/redemptionexchange/')) mkdir(SENEROOT.'media/redemptionexchange/',0777);
		$str = $periode.'/01';
		$periode_y = date("Y",strtotime($str));
		$periode_m = date("m",strtotime($str));
		if(!is_dir(SENEROOT.'media/redemptionexchange/'.$periode_y)) mkdir(SENEROOT.'media/redemptionexchange/'.$periode_y,0777);
		if(!is_dir(SENEROOT.'media/redemptionexchange/'.$periode_y.'/'.$periode_m)) mkdir(SENEROOT.'media/redemptionexchange/'.$periode_y.'/'.$periode_m,0777);
		return SENEROOT.'media/redemptionexchange/'.$periode_y.'/'.$periode_m;
	}
	private function __forceDownload($pathFile){
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($pathFile));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($pathFile));
		ob_clean();
		flush();
		readfile($pathFile);
		exit;
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
        $type_list = 'notification';
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
				if($gd->status == 'top up problem') {
                    $status = '<label class="label label-danger">Fail (Top-Up Problem)</label>';
                } else if($gd->status == 'wallet balance refunded') {
                    $status = '<label class="label label-warning">Fail (Balance Refunded)</label>';
                } else {
                    $status = '<label class="label label-success">Ongoing (Balance Deducted)</label>';
                }				 
                $gd->status = '<center><span>'.$status.'</span></center><div style="margin-bottom: 3px;"></div>';
            }

            // if (isset($gd->is_active)) {
            //     $status = "";
			// 	if(!empty($gd->is_active)) $status = '<label class="label label-success">Active</label>';
			// 	else $status = '<label class="label label-default">Inactive</label>';
            //     $gd->is_active = '<span>'.$status.'</span><br /><div style="margin-bottom: 5px;"></div>';

				// if(!empty($gd->is_report)) $status = '<label class="label label-warning">Yes</label>';
				// else $status = '<label class="label label-default">No</label>';
				// $gd->is_active .= '<span>Reported: '.$status.' </span><br /><div style="margin-bottom: 5px;"></div>';

				// if(!empty($gd->is_take_down)) $status = '<label class="label label-danger">Yes</label>';
				// else $status = '<label class="label label-default">No</label>';
				// $gd->is_active .= '<span>Takedown : '.$status.' </span><br />';
            // }
		}
		//sleep(3);
		$another = array();
		$this->__jsonDataTable($ddata,$dcount);
	}

	public function download_xls(){
        $data = $this->__init();

        if(!$this->admin_login){
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;
        $keyword = '';
        $fromDate = $this->input->get("cdate_start");
        $toDate = $this->input->get("cdate_end");
        $type_list = 'notification';
        $statusFilter = $this->input->get("status");

        $type_xls = $this->input->get("type");

        // $data_json = array();
        // if($fromDate == '' || $fromDate == null) {
		// 	$this->status = 400;
		// 	$this->message = 'From date must be fill';
		// 	$this->__json_out($data_json);
		// 	exit();
		// }
        // if($toDate == '' || $toDate == null) {
		// 	$this->status = 400;
		// 	$this->message = 'To date must be fill';
		// 	$this->__json_out($data_json);
		// 	exit();
		// }

		// var_dump($_GET);die;

        $ddata = $this->list_model->exportXls($nation_code, $keyword, $fromDate, $toDate, $type_list, $statusFilter, $this->status_in_table, $type_xls);

        //loading library xls
        $this->lib('phpexcel/PHPExcel','','inc');
        $this->lib('phpexcel/PHPExcel/Writer/Excel2007','','inc');

        //preset array kolom
        $phpexcel_money = '_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)';
        $judul_pertama_sty = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 12,
                'name'  => 'Arial'
            )
        );
        $style = array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $styleborder = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        //create object xls
        $objPHPExcel = new PHPExcel();

        //===sheet redemptionexchange total===//
        $objWorkSheet = $objPHPExcel->setActiveSheetIndex(0);

        $objWorkSheet->getColumnDimension('A')->setAutoSize(false);
        $objWorkSheet->getColumnDimension('B')->setAutoSize(false);
        $objWorkSheet->getColumnDimension('C')->setAutoSize(false);
        $objWorkSheet->getColumnDimension('D')->setAutoSize(false);
        $objWorkSheet->getColumnDimension('E')->setAutoSize(false);
        $objWorkSheet->getColumnDimension('F')->setAutoSize(false);
        $objWorkSheet->getColumnDimension('G')->setAutoSize(false);
        $objWorkSheet->getColumnDimension('A')->setWidth(5);
        $objWorkSheet->getColumnDimension('B')->setWidth(16);
        $objWorkSheet->getColumnDimension('C')->setWidth(10);
        $objWorkSheet->getColumnDimension('D')->setWidth(20);
        $objWorkSheet->getColumnDimension('E')->setWidth(20);
        $objWorkSheet->getColumnDimension('F')->setWidth(20);
        $objWorkSheet->getColumnDimension('G')->setWidth(20);

        //header
        $objWorkSheet->setCellValue('A1', '<Sending date : '.date('Y.m.d').">");

        $objWorkSheet
        ->setCellValue('A2', 'No.')
        ->setCellValue('B2', 'HP')
        ->setCellValue('C2', 'Price')
        ->setCellValue('D2', 'Request Date')
        ->setCellValue('E2', 'Agent')
        ->setCellValue('E3', 'Result (success/fail)')
        ->setCellValue('F3', 'Result Date')
        ->setCellValue('G3', 'Serial no.')
        ;

        //setting gaya untuk header
        $objWorkSheet->getStyle('A2')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objWorkSheet->getStyle('B2')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objWorkSheet->getStyle('C2')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objWorkSheet->getStyle('D2')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objWorkSheet->getStyle('E2')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objWorkSheet->getStyle('F2')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objWorkSheet->getStyle('G2')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objWorkSheet->getStyle('A3')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objWorkSheet->getStyle('B3')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objWorkSheet->getStyle('C3')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objWorkSheet->getStyle('D3')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objWorkSheet->getStyle('E3')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objWorkSheet->getStyle('F3')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objWorkSheet->getStyle('G3')->applyFromArray($styleborder)->getAlignment()->applyFromArray($style)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objWorkSheet->mergeCells('A2:A3');
        $objWorkSheet->mergeCells('B2:B3');
        $objWorkSheet->mergeCells('C2:C3');
        $objWorkSheet->mergeCells('D2:D3');
        $objWorkSheet->mergeCells('E2:G2');

        $objWorkSheet
        ->getStyle('A2:G3')
        ->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()->setARGB('D8D8D8')
        ;

        $i=4;
        $dot=".";
        $nomor = 1;
        if(count($ddata)>0){
            foreach($ddata as $pb){
                //mengisikan masing2 data
                $objWorkSheet->setCellValue('A'.$i, $nomor);
                $objWorkSheet->setCellValue('B'.$i, $pb->telp);
                $objWorkSheet->setCellValue('C'.$i, $pb->amount_get);
                $objWorkSheet->setCellValue('D'.$i, date_format(date_create($pb->cdate),"Y.m.d H:i:s"));
                $objWorkSheet->setCellValue('E'.$i, '');
                $objWorkSheet->setCellValue('F'.$i, '');
                $objWorkSheet->setCellValue('G'.$i, '');

                //set border ke masing2 kolom
                $objWorkSheet->getStyle('A'.$i)->applyFromArray($styleborder)->getAlignment()->applyFromArray($style)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objWorkSheet->getStyle('B'.$i)->applyFromArray($styleborder);
                $objWorkSheet->getStyle('C'.$i)->applyFromArray($styleborder);
                $objWorkSheet->getStyle('D'.$i)->applyFromArray($styleborder);
                $objWorkSheet->getStyle('E'.$i)->applyFromArray($styleborder);
                $objWorkSheet->getStyle('F'.$i)->applyFromArray($styleborder);
                $objWorkSheet->getStyle('G'.$i)->applyFromArray($styleborder);
                //$objWorkSheet->getStyle('W'.$i)->applyFromArray($styleborder);
                $nomor++;
                $i++;
            }
        }else{
            $objWorkSheet->setCellValue('A'.$i,'Empty result')->mergeCells('A'.$i.':G'.$i.'');
            $objWorkSheet->getStyle('A'.$i.':G'.$i.'')->getAlignment()->applyFromArray($style);
            $objWorkSheet->getStyle('A'.$i.':G'.$i.'')->applyFromArray($styleborder);
        }
        $objPHPExcel->setActiveSheetIndex(0);

        //save file
        $save_dir = $this->__checkDir(date("Y/m"));
        $save_file = 'pulsa_form-'.date('Ymd_His');
        $save_file = str_replace(' ','',str_replace('/','',$save_file));

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        if(file_exists($save_dir.'/'.$save_file.'.xlsx')) unlink($save_dir.'/'.$save_file.'.xlsx');
        $objWriter->save($save_dir.'/'.$save_file.'.xlsx');

        //$download_path = str_replace(SENEROOT,'/',$save_dir.'/'.$save_file.'.xlsx');
        $this->__forceDownload($save_dir.'/'.$save_file.'.xlsx');
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
            if($data->status == 'wallet balance deducted') {
                $data->status_no = 1;
            } else if($data->status == 'top up problem') {
                $data->status_no = 2;
            } else {
                $data->status_no = 3;
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
            if(isset($getPointNow->b_user_id)) {
                $data->user_wallet_balance = number_format($getPointNow->total_point, 0, ',', '.');
            }else{
                $data->user_wallet_balance = number_format(0, 0, ',', '.');
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

	public function success() {
		$d = $this->__init();
		$data = array();

		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$id = $this->input->get('id') ? $this->input->get('id') : '';
        $reason = $this->input->get('reason') ? $this->input->get('reason') : '';
        if ($reason == 1) {
            $reason_id = "Selamat, Redeem kamu berhasil.";
            $reason_en = "Congratulations! Redemption has been completed successfully.";
        } else if($reason == 2) {
            $reason_id = "Maaf, Kamu hanya bisa top up 10.000 rupiah sebagai pengguna Smartfren. Sellon akan mengembalikan 1.000 SPT ke kamu hari ini";
            $reason_en = "Sorry, you can only top up 10.000 rupiah as a Smartfren user. Sellon will return 1.000 SPT to you today.";
        } else {
            $reason_id = "Selamat, redeem kamu berhasil.";
            $reason_en = "Congratulations! Redemption has been completed successfully.";
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
		$b_user_id_requester = $redemptionexchange_data->b_user_id;
		$user = $this->bu_model->getById($nation_code, $b_user_id_requester);

        

		//start transaction and lock table
		$this->list_model->trans_start();

        // Proses Reject
        $this->__prosesRedemptionExchange($nation_code, $id, $admin_name, $user, $data, "success", $reason_id, $reason_en);

    }

	public function problem() {
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
        // cek status apakah sudah 'wallet balance deducted' 
        if ($redemptionexchange_data->status != 'wallet balance deducted') {
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
        $this->__prosesRedemptionExchange($nation_code, $id, $admin_name, $user, $data, "top up problem", "Maaf, kami tidak dapat mengisi ulang PULSA untuk nomor Anda. Kami akan segera mengembalikan ".$redemptionexchange_data->cost_spt." SPT Anda.", "Sorry, we couldn't recharge PULSA for your number. We'll promptly refund your ".$redemptionexchange_data->cost_spt." SPT.");

    }

    public function refund() {
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
        // cek status apakah sudah 'top up problem' 
        if ($redemptionexchange_data->status != 'top up problem') {
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
        $this->__prosesRedemptionExchange($nation_code, $id, $admin_name, $user, $data, "wallet balance refunded", "Kami telah mengembalikan ".$redemptionexchange_data->cost_spt." SPT ke Wallet Anda", "We've refunded your ".$redemptionexchange_data->cost_spt." SPT to your wallet.");

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

        $this->status = 200;
        $this->message = 'Success';

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

        if($statusRedemption == "wallet balance refunded"){
            // insert ke g_leaderboard
            $redemptionexchange_data = $this->list_model->getById($nation_code, $id);
            $requesterAddress = $this->bua->getByUserIdDefault($nation_code, $user->id);

            // $leaderBoardHistoryId = $this->glphm->getLastId($nation_code, $user->id, $requesterAddress->kelurahan, $requesterAddress->kecamatan, $requesterAddress->kabkota, $requesterAddress->provinsi);
            $dp = array();
            $dp['nation_code'] = $nation_code;
            // $dp['id'] = $leaderBoardHistoryId;
            $dp['b_user_alamat_location_kelurahan'] = $requesterAddress->kelurahan;
            $dp['b_user_alamat_location_kecamatan'] = $requesterAddress->kecamatan;
            $dp['b_user_alamat_location_kabkota'] = $requesterAddress->kabkota;
            $dp['b_user_alamat_location_provinsi'] = $requesterAddress->provinsi;
            $dp['b_user_id'] = $user->id;
            $dp['plusorminus'] = "+";
            $dp['point'] = $redemptionexchange_data->cost_spt; // cost_spt
            $dp['custom_id'] = $id; // h_point_redemption_exchange.id
            $dp['custom_type'] = $redemptionexchange_data->name_point_history;
            $dp['custom_type_sub'] = "refund";
            $dp['custom_text'] = $user->fnama.' got '.$dp['custom_type'].' '.$dp['custom_type_sub'].' and get '.$dp['point'].' point(s)';
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
        }

        $this->list_model->trans_commit();

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
}