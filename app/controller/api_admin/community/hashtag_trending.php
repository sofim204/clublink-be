<?php
class Hashtag_Trending extends JI_Controller{

    var $status_in_table = array('success');

	public function __construct(){
    parent::__construct();
		$this->lib("seme_purifier");
        $this->lib("seme_log");
		$this->load("api_admin/c_community_hashtag_history_model",'cchhm');
		$this->load("api_admin/b_user_model",'bu_model');

		$this->current_parent = 'redemptionexchange';
		$this->current_page = 'redemptionexchange_success';
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

        $fromDate_post = $this->input->post("from_date");
        $toDate_post = $this->input->post("to_date");

		$fromDate = $fromDate_post;
		$toDate = $toDate_post;

		if ($fromDate_post == '') {
			$fromDate = date('Y-m-d');
		}
		if ($toDate_post == '') {
			$toDate = date('Y-m-d');
		}
		
        // $userId = $this->input->post("user_id");
        $type_list = 'success';
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
				$sortCol = "hashtag";
				break;
			case 2:
				$sortCol = "jumlah";
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
        
		$dcount = $this->cchhm->countAll($nation_code, $keyword, $fromDate, $toDate, $type_list, $statusFilter, $this->status_in_table);
		$ddata = $this->cchhm->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $fromDate, $toDate, $type_list, $statusFilter, $this->status_in_table);
		
		foreach($ddata as &$gd){

            if (isset($gd->cdate)) {
				$gd->cdate = date("d M Y H:i:s", strtotime($gd->cdate));
            }

            if (isset($gd->custom_status_date)) {
				$gd->custom_status_date = date("d M Y H:i:s", strtotime($gd->custom_status_date));
            }

            if (isset($gd->status)) {
                $status = "";
				if($gd->status == 'success') {
                    $status = '<label class="label label-success">Success (Final)</label>';
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

		// var_dump($fromDate);die;
		// if($fromDate_post != '' && $toDate_post != '') {
		// 	$gd->date_info = "Trending from ".date("d M Y", strtotime($fromDate_post))." to ".date("d M Y", strtotime($toDate_post));
		// }elseif($fromDate_post != '') {
		// 	$gd->date_info = "Trending from ".date("d M Y", strtotime($fromDate_post));
		// }elseif($toDate_post != '') {
		// 	$gd->date_info = "Trending until ".date("d M Y", strtotime($toDate_post));
		// }else{
		// 	$gd->date_info = "Trending Today";
		// }
		//sleep(3);
		$another = array();
		$this->__jsonDataTable($ddata,$dcount);
	}


}