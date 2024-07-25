<?php
class Daily_New_User extends JI_Controller{

    var $status_in_table = array('pending','accepted','rejected','finished');

	public function __construct(){
    parent::__construct();
		$this->lib("seme_purifier");
        $this->lib("seme_log");
		$this->load("api_admin/c_community_event_new_user_model",'list_model');
        $this->load("api_admin/c_community_event_status_history_model",'list_history_model');
        $this->load("api_mobile/b_user_alamat_model", "bua");
		$this->load("api_admin/b_user_model",'bu_model');

		$this->load("api_mobile/d_pemberitahuan_model", "dpem");

		$this->current_parent = 'event';
		$this->current_page = 'event_daily_new_user';
	}

	private function __checkDir($periode){
		if(!is_dir(SENEROOT.'media/')) mkdir(SENEROOT.'media/',0777);
		if(!is_dir(SENEROOT.'media/event/')) mkdir(SENEROOT.'media/event/',0777);
		if(!is_dir(SENEROOT.'media/event/daily_new_user/')) mkdir(SENEROOT.'media/event/daily_new_user/',0777);
		$str = $periode.'/01';
		$periode_y = date("Y",strtotime($str));
		$periode_m = date("m",strtotime($str));
		if(!is_dir(SENEROOT.'media/event/daily_new_user/'.$periode_y)) mkdir(SENEROOT.'media/event/daily_new_user/'.$periode_y,0777);
		if(!is_dir(SENEROOT.'media/event/daily_new_user/'.$periode_y.'/'.$periode_m)) mkdir(SENEROOT.'media/event/daily_new_user/'.$periode_y.'/'.$periode_m,0777);
		return SENEROOT.'media/event/daily_new_user/'.$periode_y.'/'.$periode_m;
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
        $statusFilter = $this->input->post("status");

		$sortCol = "cdate";
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
				$sortCol = "user";
				break;
            case 4:
                $sortCol = "telp";
                break;
            case 5:
                $sortCol = "verif_telp_manual";
                break;
			case 6:
				$sortCol = "day1";
				break;
			case 7:
				$sortCol = "day2";
				break;
			case 8:
				$sortCol = "day3";
				break;
			case 9:
				$sortCol = "status_redeem_pulsa";
				break;
			case 10:
				$sortCol = "cdate_redeem_pulsa";
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
        
		$dcount = $this->list_model->countAll($nation_code, $keyword, $fromDate, $toDate, $statusFilter, $this->status_in_table);
		$ddata = $this->list_model->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $fromDate, $toDate, $statusFilter, $this->status_in_table);
		
		foreach($ddata as &$gd){

            if (isset($gd->cdate)) {
				$gd->cdate = date("d M Y H:i:s", strtotime($gd->cdate));
            }

            if (isset($gd->cdate_redeem_pulsa)) {
				$gd->cdate_redeem_pulsa = date("d M Y H:i:s", strtotime($gd->cdate_redeem_pulsa));
            }

            if (isset($gd->status_redeem_pulsa)) {
                $gd->status_redeem_pulsa_only = $gd->status_redeem_pulsa;
            }else{
                $gd->status_redeem_pulsa_only = '';
            }

            // if (isset($gd->status_redeem_pulsa)) {
            //     $status_redeem_pulsa = "";
			// 	if($gd->status_redeem_pulsa == 'pending') {
            //         // $status_redeem_pulsa = '<label class="label label-warning">Pending</label>';
            //         // cek jika cdate day 3 null
                    // if (isset($gd->cdate_day_3)) {
                    //     $status_redeem_pulsa = '<a class="btn btn-warning bpending" data-idc='.$gd->id.'><i class="fa fa-hand-pointer-o"></i> Pending</a>';
                    // }else{
                    //     $status_redeem_pulsa = '';
                    // }                    
            //     } else if($gd->status_redeem_pulsa == 'accepted') {
            //         $status_redeem_pulsa = '<label class="label label-info">Accepted</label>';
            //     } else if($gd->status_redeem_pulsa == 'rejected') {
            //         $status_redeem_pulsa = '<label class="label label-danger">Rejected</label>';
            //     } else {
            //         $status_redeem_pulsa = '<label class="label label-success">Finished</label>';
            //     }				 
            //     $gd->status_redeem_pulsa = '<center><span>'.$status_redeem_pulsa.'</span></center><div style="margin-bottom: 3px;"></div>';
            // }

            if (isset($gd->status_redeem_pulsa)) {
                $status_redeem_pulsa = "";
				if($gd->status_redeem_pulsa == 'pending') {
                    if (isset($gd->cdate_day_3)) {
                        $status_redeem_pulsa = '<label class="label label-warning">Pending</label>';
                    }else{
                        $status_redeem_pulsa = '';
                    }                                         
                } else if($gd->status_redeem_pulsa == 'accepted') {
                    $status_redeem_pulsa = '<label class="label label-info">Accepted</label>';
                } else if($gd->status_redeem_pulsa == 'rejected') {
                    $status_redeem_pulsa = '<label class="label label-danger">Rejected</label>';
                } else {
                    $status_redeem_pulsa = '<label class="label label-success">Finished</label>';
                }				 
                $gd->status_redeem_pulsa = '<center><span>'.$status_redeem_pulsa.'</span></center><div style="margin-bottom: 3px;"></div>';
            }
            
            

            // if (isset($gd->day1)) {
			// 	$gd->day1 = "<a href=".base_url('a/community/listing/detail/'.$gd->day1)." target='_blank'>$gd->day1</a>";
            // }
            // if (isset($gd->day2)) {
			// 	$gd->day2 = "<a href=".base_url('a/community/listing/detail/'.$gd->day2)." target='_blank'>$gd->day2</a>";
            // }
            // if (isset($gd->day3)) {
			// 	$gd->day3 = "<a href=".base_url('a/community/listing/detail/'.$gd->day3)." target='_blank'>$gd->day3</a>";
            // }

            if (isset($gd->day1)) {
				// $gd->day1 = "<center><span><label class='label label-info'>Yes</label></span></center><div style='margin-bottom: 3px;'></div>";
				$gd->day1 = "<center><span><i class='fa fa-check' aria-hidden='true'></i></span><br>".date("d M Y H:i:s", strtotime($gd->cdate_day_1))."</center><div style='margin-bottom: 3px;'></div>";
            }
            if (isset($gd->day2)) {
				$gd->day2 = "<center><span><i class='fa fa-check' aria-hidden='true'></i></span><br>".date("d M Y H:i:s", strtotime($gd->cdate_day_2))."</center><div style='margin-bottom: 3px;'></div>";
            }
            if (isset($gd->day3)) {
				$gd->day3 = "<center><span><i class='fa fa-check' aria-hidden='true'></i></span><br>".date("d M Y H:i:s", strtotime($gd->cdate_day_3))."</center><div style='margin-bottom: 3px;'></div>";
            }

            if (isset($gd->telp)) {
				if ($gd->telp != '' && $gd->telp != null) {
                    if ($gd->verif_telp_manual == 1) { // cek jika no sudah diverified pakai no telp event
                        if ($gd->telp_event[0] == '8') {
                            $gd->telp_event = '0'.$gd->telp_event;
                        }
                        $gd->telp = $gd->telp_event.'<img src="'.base_url('media/icon/verified.png').'" class="img-responsive" style="max-width: 20px;display:inline-block;" />';                        
                    }else{
                        if ($gd->telp[0] == '8') {
                            $gd->telp = '0'.$gd->telp;
                        }
                    }
                }
            }
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
        if ($type_xls == 'agent') {
            $objWorkSheet->setCellValue('A1', '<Sending date : '.date('Y.m.d')."> Event New User for PULSA AGENT");
        }else{
            $objWorkSheet->setCellValue('A1', '<Sending date : '.date('Y.m.d')."> Event New User");
        }

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
                if ($pb->verif_telp_manual == 1) { // cek jika no sudah diverified pakai no telp event
                    if ($pb->telp_event[0] == '8') {
                        $pb->telp_event = '0'.$pb->telp_event;
                    }
                    $pb->telp = $pb->telp_event;                        
                }else{
                    if ($pb->telp != '' && $pb->telp != null) {
                        if ($pb->telp[0] == '8') {
                            $pb->telp = '0'.$pb->telp;
                        }
                    } 
                }
                //mengisikan masing2 data
                $objWorkSheet->setCellValue('A'.$i, $nomor);
                $objWorkSheet->setCellValue('B'.$i, $pb->telp);
                $objWorkSheet->setCellValue('C'.$i, '10000');
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

        // if (isset($data->status)) {
        //     if($data->status == 'wallet balance deducted') {
        //         $data->status_no = 1;
        //     } else if($data->status == 'top up problem') {
        //         $data->status_no = 2;
        //     } else {
        //         $data->status_no = 3;
        //     }				 
        // }

        $data->cdate = $data->cdate ? date("d M Y H:i:s", strtotime($data->cdate)) : "";
        
        $data->day1 = "<a href=".base_url('a/community/listing/detail/'.$data->day1)." target='_blank'>$data->day1_title</a>";
        $data->day2 = "<a href=".base_url('a/community/listing/detail/'.$data->day2)." target='_blank'>$data->day2_title</a>";
        $data->day3 = "<a href=".base_url('a/community/listing/detail/'.$data->day3)." target='_blank'>$data->day3_title</a>";

        if (isset($data->telp)) {
            if ($data->telp != '' && $data->telp != null) {
                if ($data->verif_telp_manual == 1) { // cek jika no sudah diverified pakai no telp event
                    if ($data->telp_event[0] == '8') {
                        $data->telp_event = '0'.$data->telp_event;
                    }
                    $data->telp = $data->telp_event.'<img src="'.base_url('media/icon/verified.png').'" class="img-responsive" style="max-width: 20px;display:inline-block;" />';                        
                }else{
                    if ($data->telp[0] == '8') {
                        $data->telp = '0'.$data->telp;
                    }
                    $data->telp = $data->telp." <b>(Not Verified)</b>";
                }
            }
        }

        $data->note_rejected = '';
        $type = 'new-user';
        $status = 'rejected';
        $data_note_rejected = $this->list_history_model->getNoteHistory($nation_code, $id, $type, $status);
        
        if (isset($data_note_rejected->note)){
            if ($data_note_rejected->note != ''){
                $data->note_rejected = '<br>Reason Rejected => <b>'.$data_note_rejected->note.'</b>';
            }
        }        
        
        $this->__json_out($data);
    }

	public function accepted() {
		$d = $this->__init();
		$data = array();

		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

        $event_type = 'new-user';

		$id = $this->input->get('id') ? $this->input->get('id') : '';
		$admin_name = $pengguna->username ? $pengguna->username : '';

		if(empty($id)) {
			$this->status = 1108;
			$this->message = 'Id Not Found';
			$this->__json_out($data);
			die();
		}

		$redemptionexchange_data = $this->list_model->getById($nation_code, $id);
        if (!$redemptionexchange_data) {
            $this->status = 1108;
			$this->message = 'Id Not Found';
			$this->__json_out($data);
			die();
        }
		$b_user_id_requester = $redemptionexchange_data->b_user_id;
		$user = $this->bu_model->getById($nation_code, $b_user_id_requester);

        $message_id = "Selamat! Anda akan menerima pulsa sebesar Rp 10.000 ke nomor telepon yang terdaftar di akun SellOn Anda. Mohon menunggu 2x24 jam hingga transaksi dapat diproses.";
        $message_inggris = "Congratulations! You have received a credit of Rp 10.000 to the registered phone number on your SellOn account. Please wait 2x24 hours for the transaction to be processed.";

        

		//start transaction and lock table
		$this->list_model->trans_start();

        // Proses Accepted
        $this->__prosesRedemptionExchange($nation_code, $id, $admin_name, $user, $data, "accepted", $message_id, $message_inggris, $event_type, null);

    }

	public function rejected() {
		$d = $this->__init();
		$data = array();

		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

        $event_type = 'new-user';

		$id = $this->input->get('id') ? $this->input->get('id') : '';
		$note = $this->input->get('note') ? $this->input->get('note') : '';
        
		$admin_name = $pengguna->username ? $pengguna->username : '';

		if(empty($id)) {
			$this->status = 1108;
			$this->message = 'Id Not Found';
			$this->__json_out($data);
			die();
		}

		$redemptionexchange_data = $this->list_model->getById($nation_code, $id);
        if (!$redemptionexchange_data) {
            $this->status = 1108;
			$this->message = 'Id Not Found';
			$this->__json_out($data);
			die();
        }
		$b_user_id_requester = $redemptionexchange_data->b_user_id;
		$user = $this->bu_model->getById($nation_code, $b_user_id_requester);

        $message_id = "Dengan menyesal kami informasikan kepada Anda bahwa Anda tidak memenuhi persyaratan untuk Acara Misi Harian. Untuk bantuan lebih lanjut, silakan menghubungi kami melalui WhatsApp di Nomor Dukungan Pelanggan: +65 8856 2024 atau melalui email di support@sellon.net.";
        $message_inggris = "We regret to inform you that you did not meet the requirements for the Daily Missions Event. For further assistance, please contact us via WhatsApp at Customer Support Number: +65 8856 2024 or via email at support@sellon.net.";        

		//start transaction and lock table
		$this->list_model->trans_start();

        // Proses Accepted
        $this->__prosesRedemptionExchange($nation_code, $id, $admin_name, $user, $data, "rejected", $message_id, $message_inggris, $event_type, $note);

    }

    public function finished() {
		$d = $this->__init();
		$data = array();

		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

        $event_type = 'new-user';

		$id = $this->input->get('id') ? $this->input->get('id') : '';
		$admin_name = $pengguna->username ? $pengguna->username : '';

		if(empty($id)) {
			$this->status = 1108;
			$this->message = 'Id Not Found';
			$this->__json_out($data);
			die();
		}

		$redemptionexchange_data = $this->list_model->getById($nation_code, $id);
        if (!$redemptionexchange_data) {
            $this->status = 1108;
			$this->message = 'Id Not Found';
			$this->__json_out($data);
			die();
        }
		$b_user_id_requester = $redemptionexchange_data->b_user_id;
		$user = $this->bu_model->getById($nation_code, $b_user_id_requester);

        $message_id = "Selamat! Anda telah mendapatkan reward Misi Harian untuk pengguna baru berupa pulsa dan berhasil ditransfer ke nomor yang terdaftar di aplikasi SellOn.";
        $message_inggris = "Congratulations! You have earned the Daily Missions reward for new users, which is a credit of pulsa, and it has been successfully transferred to the registered number in the SellOn application.";        

		//start transaction and lock table
		$this->list_model->trans_start();

        // Proses Accepted
        $this->__prosesRedemptionExchange($nation_code, $id, $admin_name, $user, $data, "finished", $message_id, $message_inggris, $event_type, null);

    }


    private function __prosesRedemptionExchange($nation_code="", $id="", $admin_name="", $user=[], $data=[], $statusRedemption="", $message_id="", $message_inggris="", $event_type="", $note=null) {
        //update table
		$du = array();
		$du['status_redeem_pulsa'] = $statusRedemption;
        if ($statusRedemption == 'finished') {
            $du['cdate_redeem_pulsa'] = "NOW()";
        }
		$res = $this->list_model->update($nation_code, $id, $du);

		if (!$res) {
			$this->list_model->trans_rollback();
			$this->list_model->trans_end();
			$this->status = 1108;
			$this->message = "Error while update data, please try again later";
			$this->__json_out($data);
			die();
		} 

        // insert to table history
		$di = array();
		$di['nation_code'] = $nation_code;
		$di['id'] = $this->GUIDv4();
		$di['custom_id'] = $id;
		$di['type'] = $event_type;
		$di['status_redeem_pulsa'] = $statusRedemption;
		$di['note'] = $note;
		$di['admin_name'] = $admin_name;
        $di['cdate'] = "NOW()";
		$resh = $this->list_history_model->set($di);

        if (!$resh) {
			$this->list_model->trans_rollback();
			$this->list_model->trans_end();
			$this->status = 1108;
			$this->message = "Error while insert community event status history, please try again later";
			$this->__json_out($data);
			die();
		}

        $this->list_model->trans_commit();
        $this->status = 200;
        $this->message = 'Success';

        // ============= save to dpem ====================
        $dpe = array();
        $dpe['nation_code'] = $nation_code;
        $dpe['b_user_id'] = $user->id;
        $dpe['id'] = $this->dpem->getLastId($nation_code, $user->id);
        $dpe['type'] = "event_hashtag_new_user";
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
        $extras->title = html_entity_decode("New User Event",ENT_QUOTES);
        if($user->language_id == 2) { 
            $extras->judul = "Event Pengguna Baru";
            $extras->teks =  $message_id;
        } else {
            $extras->judul = "New User Event";
            $extras->teks =  $message_inggris;
        }

        $dpe['extras'] = json_encode($extras);
        $this->dpem->set($dpe);
        // ============== End save to dpem ========================

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
                $title = "Event Pengguna Baru";
                $message = $message_id;
            } else {
                $title = "New User Event";
                $message = $message_inggris;
            }
            
            $image = 'media/pemberitahuan/promotion.png';
            $type = 'event_hashtag_new_user';
            $payload = new stdClass();
            $payload->id = $id;
            $payload->title = html_entity_decode("New User Event",ENT_QUOTES);
            // $payload->harga_jual = $community->harga_jual;
            // $payload->foto = base_url().$community->thumb;
            if($user->language_id == 2) {
                $payload->judul = "Event Pengguna Baru";
                //by Donny Dennison
                //dicomment untuk handle message too big, response dari fcm
                // $payload->teks = strip_tags(html_entity_decode($di['teks']));
                // $payload->teks = "You get a reply from your neighbors (".$tempTitle->{'title'}.")";
                $payload->teks = $message_id;
            } else {
                $payload->judul = "New User Event";
                $payload->teks = $message_inggris;
            }

            $this->__pushNotif($device, $tokens, $title, $message, $type, $image, $payload);            
        }
        // END cek fcm token

        //end transaction
		$this->list_model->trans_end();

        $this->__json_out($data);
    }

    public function edit_telp() {
		$d = $this->__init();
		$data = array();

		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$id = $this->input->get('id') ? $this->input->get('id') : '';
		$telp = $this->input->get('telp') ? $this->input->get('telp') : '';
		$admin_name = $pengguna->username ? $pengguna->username : '';

		if(empty($id)) {
			$this->status = 1108;
			$this->message = 'Id Not Found';
			$this->__json_out($data);
			die();
		}

		$redemptionexchange_data = $this->list_model->getById($nation_code, $id);
        if (!$redemptionexchange_data) {
            $this->status = 1108;
			$this->message = 'Id Not Found';
			$this->__json_out($data);
			die();
        }
		$b_user_id_requester = $redemptionexchange_data->b_user_id;
		$user = $this->bu_model->getById($nation_code, $b_user_id_requester);
        if (!$user) {
            $this->status = 1108;
			$this->message = 'Id User Not Found';
			$this->__json_out($data);
			die();
        }
        // Check duplicate telp on b user
        $checkTelp = $this->list_model->checkTelpUser($nation_code, $telp);
        if (isset($checkTelp->telp)) {
            $this->status = 1108;
			$this->message = 'Phone Number Already Exist';
			$this->__json_out($data);
			die();
        }
        // End check duplicate telp on b user

        // Check duplicate telp on event
        $checkTelpEvent = $this->list_model->checkTelpEvent($nation_code, $telp);
        if (isset($checkTelpEvent->telp)) {
            $this->status = 1108;
			$this->message = 'Phone Number has already participated in this event';
			$this->__json_out($data);
			die();
        }
        // End check duplicate telp on event        

        //start transaction and lock table
		$this->list_model->trans_start();

        //update table community event new user
		$du = array();
		$du['telp'] = $telp;
		$du['verif_telp_manual'] = 1;
		$res = $this->list_model->update($nation_code, $id, $du);

		if (!$res) {
			$this->list_model->trans_rollback();
			$this->list_model->trans_end();
			$this->status = 1108;
			$this->message = "Error while update data, please try again later";
			$this->__json_out($data);
			die();
		} 

        //update table b_user
		$dx = array();
		$dx['telp'] = $telp;
		$res = $this->bu_model->update($nation_code, $b_user_id_requester, $dx);

		if (!$res) {
			$this->list_model->trans_rollback();
			$this->list_model->trans_end();
			$this->status = 1108;
			$this->message = "Error while update data, please try again later";
			$this->__json_out($data);
			die();
		} 

        $this->list_model->trans_commit();


        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data);
        die();


    }

    public function verif_telp() {
		$d = $this->__init();
		$data = array();

		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$id = $this->input->get('id') ? $this->input->get('id') : '';
		$admin_name = $pengguna->username ? $pengguna->username : '';

		if(empty($id)) {
			$this->status = 1108;
			$this->message = 'Id Not Found';
			$this->__json_out($data);
			die();
		}

		$redemptionexchange_data = $this->list_model->getById($nation_code, $id);
        if (!$redemptionexchange_data) {
            $this->status = 1108;
			$this->message = 'Id Not Found';
			$this->__json_out($data);
			die();
        }

        $redemptionexchange_data = $this->list_model->getById($nation_code, $id);
        if (!$redemptionexchange_data) {
            $this->status = 1108;
			$this->message = 'Id Not Found';
			$this->__json_out($data);
			die();
        }
		$b_user_id_requester = $redemptionexchange_data->b_user_id;
		$user = $this->bu_model->getById($nation_code, $b_user_id_requester);
        if (!$user) {
            $this->status = 1108;
			$this->message = 'Id User Not Found';
			$this->__json_out($data);
			die();
        }

        // Check duplicate telp on event
        $checkTelpEvent = $this->list_model->checkTelpEvent($nation_code, $user->telp);
        if (isset($checkTelpEvent->telp)) {
            $this->status = 1108;
			$this->message = 'Phone Number has already participated in this event';
			$this->__json_out($data);
			die();
        }
        // End check duplicate telp on event  

        //start transaction and lock table
		$this->list_model->trans_start();

        //update table community event new user
		$du = array();
        $du['telp'] = $user->telp;
		$du['verif_telp_manual'] = 1;
		$res = $this->list_model->update($nation_code, $id, $du);

		if (!$res) {
			$this->list_model->trans_rollback();
			$this->list_model->trans_end();
			$this->status = 1108;
			$this->message = "Error while update data, please try again later";
			$this->__json_out($data);
			die();
		}
        $this->list_model->trans_commit();


        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data);
        die();


    }
}