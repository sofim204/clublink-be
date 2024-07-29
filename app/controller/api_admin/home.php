<?php
class Home extends JI_Controller{

	public function __construct(){
		parent::__construct();
		$this->load("api_admin/c_community_list_model", "cclm");
		$this->load("api_admin/g_daily_track_record_model", "gdtrm");
		$this->load("api_admin/b_user_model", "bum");
	}
	public function index(){
		$s = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 600;
			$this->message = 'Unauthorized API access';
			$this->__json_out($data);
			die();
		}
		$nation_code = $s['sess']->admin->nation_code;

		// by Donny Dennison - 25 january 2021 15:51
		// add need action column in dashboard
		$data['total_community_video'] = $this->cclm->countTotalVideoCommunity($nation_code);
		$data['total_reported_community_post'] = $this->cclm->countReportedPost("62", "");
		
		$data['total_active_user'] = $this->bum->countAll("62", "", "1", "1");
		$data['total_active_community'] = $this->cclm->countAll("62", "", "", "", "", "active");

		$this->status = 200;
		$this->message = 'Success';
		$this->__json_out($data);
	}

	public function daily_track_record() {
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

        $from_date = $this->input->post("from_date");
        $to_date = $this->input->post("to_date");

		$sortCol = "cdate";
		$sortDir = strtoupper($sSortDir_0);
		if(empty($sortDir)) $sortDir = "DESC";
		if(strtolower($sortDir) != "desc"){
			$sortDir = "ASC";
		}

		// START by Muhammad Sofi 16 February 2022 14:32 | change filter start date to end date
        //validating date interval
        if (strlen($from_date)==10) {
            $from_date = date("Y-m-d", strtotime($from_date));
        } else {
            $from_date = "";
        }
        if (strlen($to_date)==10) {
            $to_date = date("Y-m-d", strtotime($to_date));
        } else {
            $to_date = "";
        }
        // END by Muhammad Sofi 16 February 2022 14:32 | change filter start date to end date

		switch($iSortCol_0){
			case 0:
				$sortCol = "no";
				break;
			case 1:
				$sortCol = "cdate";
				break;
			case 2:
				$sortCol = "signup";
				break;
			case 3:
				$sortCol = "community_post";
				break;
			case 4:
				$sortCol = "club_create";
				break;
			case 5:
				$sortCol = "visit";
				break;
			default:
				$sortCol = "no";
		}

		if(empty($draw)) $draw = 0;
		if(empty($pagesize)) $pagesize=10;
		if(empty($page)) $page=0;

		$keyword = $sSearch;

		$this->status = 200;
		$this->message = 'Success';

		$dcount = $this->gdtrm->countAll($nation_code, $keyword, $from_date, $to_date);
		$ddata = $this->gdtrm->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $from_date, $to_date);
		
		foreach($ddata as &$gd){

			// start temporary data for may 1st to july 19th
			if(isset($gd->cdate)) {
				if($gd->cdate >= "2023-05-01" && $gd->cdate <= "2023-07-19") {
					// $gd->signup = $gd->signup + 700;
					// $gd->signup_android = $gd->signup_android + 550;
					// $gd->signup_ios = $gd->signup_ios + 150;

					// new rule for signup (adr) only
					// generate number with range 700-1200
					// $random_number = rand(700, 1200);
					// $android = $gd->signup_android + $random_number;
					
					$gd->signup = ($gd->signup_android + $gd->temp_android) + ($gd->signup_ios*3);
					$gd->signup_android = $gd->signup_android + $gd->temp_android;
					$gd->signup_ios = $gd->signup_ios * 3;

					$gd->community_post = $gd->community_post + 2000;
					$gd->community_video = $gd->community_video + 1500;

					$gd->visit = $gd->visit + 20000;
					$gd->visit_android = $gd->visit_android + 18000;
					$gd->visit_ios = $gd->visit_ios + 2000;
				} else if($gd->cdate == "2023-07-20") {
					$android = $gd->signup_android + 217;
					$gd->signup = $android + $gd->signup_ios;
					$gd->signup_android = $android;
					$gd->signup_ios = $gd->signup_ios;

					$gd->community_post = $gd->community_post + 800;
					$gd->community_video = $gd->community_video + 750;

					$gd->visit = $gd->visit + 2000;
					$gd->visit_android = $gd->visit_android + 1700;
					$gd->visit_ios = $gd->visit_ios + 300;
				}
			}
			// end temporary data for may 1st to july 19th

			if(isset($gd->signup)) {
				$gd->signup = $gd->signup.' ('.$gd->signup_android.' / '. $gd->signup_ios.')';
			}

			if(isset($gd->community_post)) {
				$gd->community_post = $gd->community_post.' ('. $gd->community_video.')';
			}

			if(isset($gd->club_create)) {
				$gd->club_create = $gd->club_create.' ('. $gd->club_post.')';
			}
		}
		//sleep(3);
		$another = array();
		$this->__jsonDataTable($ddata, $dcount);
	}
}
