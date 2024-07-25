<?php
class Home extends JI_Controller{

	public function __construct(){
		parent::__construct();
		//$this->setTheme('frontx');
		$this->load("api_admin/d_order_model",'dom');
		$this->load("api_admin/d_order_detail_model",'dodm');
		$this->load("api_admin/d_order_detail_item_model",'dodim');

		// by Donny Dennison - 25 january 2021 15:51
		// add need action column in dashboard
		$this->load("api_admin/c_produk_laporan_model", 'cplm');
		$this->load("api_admin/c_discuss_model","cdm");

		$this->load("api_admin/c_produk_model","cpm");
		$this->load("api_admin/c_community_list_model","cclm");

		$this->load("api_admin/b_user_offer_sales_admin_model", "buosam");
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
		$overview = array();
		for($i=0;$i<12;$i++){
			$overview[$i] = new stdClass();
			$overview[$i]->month = ($i+1);
			$overview[$i]->earnings = 0;
			$overview[$i]->sales = 0;
		}
		$year = date("Y");
		$today = date("Y-m-d");
		$cdate_start = $this->input->post('start_date');
		$cdate_end = $this->input->post('end_date');
		/*var_dump($cdate_start); die();*/
		if(empty($cdate_start) && empty($cdate_end))
		{	
			$mulai = date("Y-m-")."01";
			$akhir = date("Y-m-t");

			$orders = $this->dodm->getPerMonth($nation_code,$year);

			foreach($orders as $ord){
				$ord->month = (int) $ord->month;
				$month = $ord->month-1;
				if(isset($overview[$month])){
					$overview[$month] = new stdClass();
					$overview[$month]->month = $ord->month;
					$overview[$month]->earnings = (float) $ord->earnings;
					$overview[$month]->sales = (float) $ord->sales;
				}
			}
			$data['overview'] = $overview;
			$data['sales_total'] = (float) $this->dodm->getSales($nation_code,$mulai,$akhir,$cdate_start,$cdate_end,0);
			$data['earning_total'] = (float) $this->dodm->getEarnings($nation_code,$mulai,$akhir,$cdate_start,$cdate_end,0);
			$data['unpaid_total'] = (float) $this->dodm->getUnPaid($nation_code,$mulai,$akhir,$cdate_start,$cdate_end,0);
			$data['order_total'] = (float) $this->dom->countOrders($nation_code,$today,0);
			$data['pending_count'] = $this->dom->countOrdersPending($nation_code,$today,0);
			$data['bestseller'] = $this->dodim->getBestSeller($nation_code);
			$data['order_latest'] = $this->dom->getLatestDashboard($nation_code,0,10,'id','desc');
		}
		else
		{
			$mulai = $cdate_start;
			$akhir = $cdate_end;

			$orders = $this->dodm->getPerMonth($nation_code,$year);

			foreach($orders as $ord){
				$ord->month = (int) $ord->month;
				$month = $ord->month-1;
				if(isset($overview[$month])){
					$overview[$month] = new stdClass();
					$overview[$month]->month = $ord->month;
					$overview[$month]->earnings = (float) $ord->earnings;
					$overview[$month]->sales = (float) $ord->sales;
				}
			}
			$data['overview'] = $overview;
			$data['sales_total'] = (float) $this->dodm->getSales($nation_code,$mulai,$akhir,$cdate_start,$cdate_end,0);
			$data['earning_total'] = (float) $this->dodm->getEarnings($nation_code,$mulai,$akhir,$cdate_start,$cdate_end,0);
			$data['unpaid_total'] = (float) $this->dodm->getUnPaid($nation_code,$mulai,$akhir,$cdate_start,$cdate_end,0);
			$data['order_total'] = (float) $this->dom->countOrders($nation_code,$today,0);
			$data['pending_count'] = $this->dom->countOrdersPending($nation_code,$today,0);
			$data['bestseller'] = $this->dodim->getBestSeller($nation_code);
			$data['order_latest'] = $this->dom->getLatestDashboard($nation_code,0,10,'id','desc');
		}

		// by Donny Dennison - 25 january 2021 15:51
		// add need action column in dashboard
		$data['reported_product_total'] = $this->cplm->countAll($nation_code, "", "", "", "", "", "", "", "", "", "", "ignore takedown and ignore");
		$data['reported_discussion_total'] = $this->cdm->countAllReport($nation_code);
		$data['rejected_by_seller_total'] = $this->dodm->countAllForRejectSeller($nation_code, "", "", "", "unconfirmed");
		$data['rejected_item_by_buyer_total'] = $this->dodim->countAllForRejectBuyer($nation_code, "", "", "", "wait");
		$data['total_product_video'] = $this->cpm->countTotalVideoProduct($nation_code);
		$data['total_community_video'] = $this->cclm->countTotalVideoCommunity($nation_code);
		$data['total_reported_community_post'] = $this->cclm->countReportedPost("62", "");
		
		$data['total_active_user'] = $this->bum->countAll("62", "", "1", "1");
		$data['total_active_community'] = $this->cclm->countAll("62", "", "", "", "", "active");
		$data['total_active_product'] =  $this->cpm->countAll("62", "", "", "", "", "", "", "1", "1", "", "", "", "");

		// $data['total_sales_seller_month'] = $this->buosam->countTotalSalesSellerMonth("62", date("Y"), date("m"));
		// $data['total_transaction_seller_month'] = $this->buosam->countTotalTransactionSellerMonth("62", date("Y"), date("m"));
		$data['total_sales_all'] = $this->buosam->countTotalSalesAll("62");

		$this->status = 200;
		$this->message = 'Success';
		$this->__json_out($data);
	}

	public function getTotalSalesTransactionYearMonth($from_date, $to_date) {
		$data['total_sales_seller_month_offer'] = $this->buosam->countTotalSalesSellerMonth("62", $from_date, $to_date);
		$data['total_transaction_seller_month_offer'] = $this->buosam->countTotalTransactionSellerMonth("62", $from_date, $to_date);

		$this->status = 200;
		$this->message = 'Success';
		$this->__json_out($data);
	}

	public function show_offer_summary(){
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

		$year_from_date = date('Y', strtotime($from_date));
		$month_from_date = date('m', strtotime($from_date));

		$year_to_date = date('Y', strtotime($to_date));
		$month_to_date = date('m', strtotime($to_date));

		// $year = date('Y', strtotime($year_full));
		// $month = date('m', strtotime($year_full));

		$sortCol = "cdate";
		$sortDir = strtoupper($sSortDir_0);
		if(empty($sortDir)) $sortDir = "DESC";
		if(strtolower($sortDir) != "desc"){
			$sortDir = "ASC";
		}

		// START by Muhammad Sofi 16 February 2022 14:32 | change filter start date to end date
        //validating date interval
        // if (strlen($fromDate)==10) {
        //     $fromDate = date("Y-m-d", strtotime($fromDate));
        // } else {
        //     $fromDate = "";
        // }
        // if (strlen($toDate)==10) {
        //     $toDate = date("Y-m-d", strtotime($toDate));
        // } else {
        //     $toDate = "";
        // }
        // END by Muhammad Sofi 16 February 2022 14:32 | change filter start date to end date

		switch($iSortCol_0){
			case 0:
				$sortCol = "no";
				break;
			case 1:
				$sortCol = "b_user_id";
				break;
			case 2:
				$sortCol = "user_name";
				break;
			case 3:
				$sortCol = "SUM(total_sales_seller)";
				break;
			case 4:
				$sortCol = "SUM(total_transaction_seller)";
				break;
			case 5:
				$sortCol = "SUM(total_sales_buyer)";
				break;
			case 6:
				$sortCol = "SUM(total_transaction_buyer)";
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
		// $dcount = $this->buosam->countAll($nation_code, $keyword, $year_from_date, $month_from_date, $year_to_date, $month_to_date);
		// $ddata = $this->buosam->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $year_from_date, $month_from_date, $year_to_date, $month_to_date);
		$dcount = $this->buosam->countAll($nation_code, $keyword, $from_date, $to_date);
		$ddata = $this->buosam->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $from_date, $to_date);
		
		foreach($ddata as &$gd){
			if(isset($gd->total_sales_seller)) {
				$gd->total_sales_seller = number_format($gd->total_sales_seller, 2, "," , ".");
			}

			if(isset($gd->total_sales_buyer)) {
				$gd->total_sales_buyer = number_format($gd->total_sales_buyer, 2, "," , ".");
			}
		}
		//sleep(3);
		$another = array();
		$this->__jsonDataTable($ddata, $dcount);
	}

	public function daily_track_record(){
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

		// $year = date('Y', strtotime($year_full));
		// $month = date('m', strtotime($year_full));

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
				$sortCol = "product_post";
				break;
			case 5:
				$sortCol = "club_create";
				break;
			case 6:
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

					$gd->product_post = $gd->product_post + 1000;
					$gd->product_video = $gd->product_video + 110;

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

					$gd->product_post = $gd->product_post + 171;
					$gd->product_video = $gd->product_video + 13;

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
			
			if(isset($gd->product_post)) {
				$gd->product_post = $gd->product_post.' ('. $gd->product_video.')';
			}

			if(isset($gd->club_create)) {
				$gd->club_create = $gd->club_create.' ('. $gd->club_post.')';
			}

			// if(isset($gd->visit)) {
			// 	$gd->visit = $gd->visit.' ('.$gd->visit_android.' / '. $gd->visit_ios.')';
			// }

		}
		//sleep(3);
		$another = array();
		$this->__jsonDataTable($ddata, $dcount);
	}
}
