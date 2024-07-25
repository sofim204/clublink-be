<?php
class Report extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->lib("seme_purifier");
        $this->load("api_admin/h_ticket_history_model", 'hthm');
		$this->current_parent = 'game';
		$this->current_page = 'game_report';
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


		$sortCol = "";
		$sortDir = strtoupper($sSortDir_0);
		if(empty($sortDir)) $sortDir = "DESC";
		if(strtolower($sortDir) != "desc"){
			$sortDir = "ASC";
		}

		switch($iSortCol_0){
			case 0:
				$sortCol = "cdate";
				break;
			case 1:
				$sortCol = "game_name";
				break;
			case 2:
				$sortCol = "count(b_user_id)";
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
		$dcount = $this->hthm->countAll($nation_code,$keyword);
		$ddata = $this->hthm->getAll($nation_code,$page,$pagesize,$sortCol,$sortDir,$keyword);

		// foreach($ddata as &$gd){

		// 	if(isset($gd->icon)){
		// 		// if(strlen($gd->icon)<=4) $gd->icon = 'media/icon/default-icon.png';
		// 		// if($gd->icon == 'default.png' || $gd->icon== 'default.jpg') $gd->icon = 'media/icon/default-icon.png';
		// 		// $gd->icon = base_url($gd->icon);
		// 		if(strlen($gd->icon) > 4) {
		// 			$gd->icon = '<img src="'.base_url($gd->icon).'" class="img-responsive" style="width: 64px;" />';
		// 		}
		// 	}

		// 	if(isset($gd->is_active)){
		// 		if(!empty($gd->is_active)){
		// 			$gd->is_active = 'Yes';
		// 		}else{
		// 			$gd->is_active = 'No';
		// 		}
		// 	}
		// }

		$this->__jsonDataTable($ddata,$dcount);
	}

}
