<?php
class Highlight extends JI_Controller{

	public function __construct(){
    	parent::__construct();
		$this->lib("seme_purifier");
		$this->load("api_admin/g_highlight_community_model",'ghcm');
		$this->load("api_admin/c_community_list_model",'list_model');
		// by Muhammad Sofi - 13 December 2021 15:42 | add checking to set priority before insert data
		$this->load("api_admin/g_general_location_highlight_status_model", 'gglhsm');
		$this->current_parent = 'community';
		$this->current_page = 'community_highlight';
	}

    // private function __convertToEmoji($text){
    //     $value = json_decode($text);
    //     if ($value) {
    //         return json_decode($text);
    //     } else {
    //         return json_decode('"'.$text.'"');
    //     }
    // }

	// by Muhammad Sofi 10 January 2022 13:58 | read text with emoji
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

		//collect table alias
		$tbl_as = $this->ghcm->getTableAlias();
		$tbl2_as = $this->ghcm->getTableAlias2();

		//collect standard input
        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $sSearch = $this->input->post("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $pagesize = $this->input->post("iDisplayLength");
        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");

		//collect custom input
		$startDate = $this->input->post("start_date");
		$location = $this->input->post("id_kelurahan");
		$active_status = $this->input->post("active_status");

		//validate custom input
		$is_active = "";
		switch ($active_status) {
			case 'publish_active':
				$is_active=1;
				break;
			case 'inactive':
				$is_active=0;
				break;
			default:
				$is_active = "";
				break;
		}

		//input validation
        $sortCol = $iSortCol_0;
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }

        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }
        
        if (empty($draw)) {
            $draw = 0;
        }

        if (empty($pagesize)) {
            $pagesize=10;
        }

        if (empty($page)) {
            $page=0;
        }
		
        $keyword = $sSearch;

		$dcount = $this->ghcm->countAll($nation_code, $keyword, $startDate, $location, $is_active);
		$ddata = $this->ghcm->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $startDate, $location, $is_active);

		foreach($ddata as &$gd){
			if(isset($gd->title)){
				if (strlen($gd->title)>255) {
					$gd->title = substr($this->__convertToEmoji($gd->title), 0, 255)." <strong>(More...)<strong>";
				} else {
					$gd->title = $this->__convertToEmoji($gd->title);
				}
			}

			if(isset($gd->description)){
				if (strlen($gd->description)>255) {
					$gd->description = substr($this->__convertToEmoji($gd->description), 0, 600)." <strong>(More...)<strong>"; // by Muhammad Sofi 10 January 2022 15:46 | change position of substr
				} else {
					$gd->description = $this->__convertToEmoji($gd->description);
				}
			}

			if (isset($gd->start_date)) {
                $gd->start_date = date("j F Y", strtotime($gd->start_date)); // by Muhammad Sofi - 7 December 2021 17:35 | change start_date from datetime to date
            }

			if (isset($gd->user)) {
				$user = $gd->user;
				$gd->user = '<span style="font-size: 1.2em; font-weight: bolder;">'.$user.'</span><br />';

				// if (isset($gd->address2)) $gd->user .= '<span>Address: '.$gd->address2.'</span><br />';
				if (isset($gd->address2)) {
					$fulladdress = $gd->address2.', '.$gd->kelurahan.', '.$gd->kecamatan.', '.$gd->kabkota.', '.$gd->provinsi;
					$limitaddress = substr($fulladdress, 0, 30)."<strong>......<strong>";
					$gd->user .= '<span>Address: '.$limitaddress.'</span>';
				} 
			}

			if (isset($gd->is_active)) {
                $nama = '';
                if (empty($gd->is_active)) {
                    $prop = '<span class="tbl-content-nok">Inactive <i class="fa fa-times"></i></span>';
                    $nama .= $prop.'';
                } else {
					$prop = '<span class="tbl-content-ok">Active <i class="fa fa-check"></i></span>';
					$nama .= $prop.'';
                }
                $gd->is_active = $nama;
            }
		}
		//render
        $this->status = 200;
        $this->message = 'Success';
        $this->__jsonDataTable($ddata, $dcount);
	}

	// to save to table highlight community
	public function addData($id){
		$id = (int) $id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($id)){
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
		// $data = $this->ghcm->getPostalDistrict($nation_code, $id);
		$data = $this->ghcm->getLocation($nation_code, $id);

		// by Muhammad Sofi - 13 December 2021 15:42 | add checking to set priority before insert data
		$getStatusHighlight = $this->gglhsm->getByLocation($nation_code, $data->kelurahan);
		if($getStatusHighlight->status == 'automatic') {
			
			$totalHighlight = $this->ghcm->countAllByLocation($nation_code, $data->kelurahan);
			if($totalHighlight >= 10) {
				$overHighlight = $totalHighlight - 9;
				$this->ghcm->updateByPriorityDesc($nation_code, $data->kelurahan , $overHighlight);
			}
			$this->ghcm->updatePriority($nation_code, $data->kelurahan, '+' , 1);

			//get last id
			$highlight_id = $this->ghcm->getLastId($nation_code);
			$du = array();
			// $end_date = Date('y:m:d H:i:s', strtotime('+23 hours')); // fix issue adding to 12 hour and excess 1 hour
			$du['nation_code'] = $nation_code;
			$du['id'] = $highlight_id;
			$du['c_community_id'] = $data->c_community_id;
			$du['b_user_alamat_location_kelurahan'] = $data->kelurahan;
			$du['b_user_alamat_location_kecamatan'] = $data->kecamatan;
			$du['b_user_alamat_location_kabkota'] = $data->kabkota;
			$du['b_user_alamat_location_provinsi'] = $data->provinsi;

			// by Muhammad Sofi 11 January 2022 10:14 | change insert start date and end date
			$du['start_date'] = date('Y-m-d');
        	$du['end_date'] = '9999-12-31';
			$du['is_active'] = 1;
			$du['priority'] = 1; // by Muhammad Sofi 14 December 2021 09:13 | set initial value for priority
			$this->ghcm->set($du);

			// by Muhammad Sofi 11 January 2022 10:14 | change location flag to manual
			// $di = array();
			// $di['status'] = 'automatic';
			// $this->ghcm->updateManualSystem($nation_code, $data->kelurahan, $di);

			if(!isset($data->c_community_id)){
				$data = new stdClass();
				$this->status = 441;
				$this->message = 'No Data';
				$this->__json_out($data);
				die();
			}
		} 
		
		// START by Muhammad Sofi 2 February 2022 11:22 | there is no manual flag status

		// else if($getStatusHighlight->status == 'manual') {
		// 	$totalHighlight = $this->ghcm->countAllByPostalDistrict($nation_code, $data->postal_district);
		// 	if($totalHighlight >= 10) {
		// 		$overHighlight = $totalHighlight - 9;
		// 		$this->ghcm->updateByPriorityDesc($nation_code, $data->postal_district , $overHighlight);
		// 	}
		// 	$this->ghcm->updatePriority($nation_code, $data->postal_district, '+' , 1);

		// 	//get last id
		// 	$highlight_id = $this->ghcm->getLastId($nation_code);
		// 	$du = array();
		// 	// $end_date = Date('y:m:d H:i:s', strtotime('+23 hours')); // fix issue adding to 12 hour and excess 1 hour
		// 	$du['nation_code'] = $nation_code;
		// 	$du['id'] = $highlight_id;
		// 	$du['c_community_id'] = $data->c_community_id;
		// 	// $du['b_user_alamat_location_postal_district'] = $data->postal_district;
		// 	$du['b_user_alamat_location_postal_district'] = '00'; // by Muhammad Sofi 2 February 2022 11:22 | when add data from select community, set general location to 00
		// 	// $du['start_date'] = 'NOW()';
		// 	// $du['end_date'] = $end_date;

		// 	// by Muhammad Sofi 11 January 2022 10:14 | change insert start date and end date
		// 	$du['start_date'] = date('Y-m-d');
		// 	$du['end_date'] = '9999-12-31';
		// 	$du['is_active'] = 1;
		// 	$du['priority'] = 1; // by Muhammad Sofi 14 December 2021 09:13 | set initial value for priority
		// 	$this->ghcm->set($du);

		// 	// by Muhammad Sofi 11 January 2022 10:14 | change location flag to manual
		// 	$di = array();
		// 	$di['status'] = 'manual';
		// 	$this->ghcm->updateManualSystem($nation_code, $data->postal_district, $di);

		// 	if(!isset($data->c_community_id)){
		// 		$data = new stdClass();
		// 		$this->status = 441;
		// 		$this->message = 'No Data';
		// 		$this->__json_out($data);
		// 		die();
		// 	}
		// } else {}

		// END by Muhammad Sofi 2 February 2022 11:22 | there is no manual flag status

		$this->__json_out($data);
	}

	// START by Muhammad Sofi 2 February 2022 11:22 | comment unused code

	// to set inactive from table highlight community
	// public function setInactive($id){
	// 	$id = (int) $id;
	// 	$d = $this->__init();
	// 	$data = array();
	// 	if(!$this->admin_login && empty($id)){
	// 		$this->status = 400;
	// 		$this->message = 'Unauthorized access';
	// 		header("HTTP/1.0 400 Unauthorized");
	// 		$this->__json_out($data);
	// 		die();
	// 	}
	// 	$pengguna = $d['sess']->admin;
	// 	$nation_code = $pengguna->nation_code;
	// 	$status = $this->input->post("status"); // get status inactive

	// 	$du = array();
	// 	$du['is_active'] = 0;
	// 	$this->ghcm->updatesetInactive($nation_code, $id, $du);

	// 	// change automatic to manual if there are some changes on selected highlight post in that general location
	// 	$pd = $this->ghcm->getById($nation_code, $id);
	// 	$di = array();
	// 	$di['status'] = 'manual';
	// 	$this->ghcm->updateManualSystem($nation_code, $pd->postal_district, $di);

	// 	$this->status = 200;
	// 	$this->message = 'success';	
	// 	if(!isset($id)){
	// 		$data = new stdClass();
	// 		$this->status = 441;
	// 		$this->message = 'No Data';
	// 		$this->__json_out($data);
	// 		die();
	// 	}
	// 	$this->__json_out($data);
	// }
	
	// END by Muhammad Sofi 2 February 2022 11:22 | comment unused code
	
	// to set delete from table highlight community
	public function setDelete($id){
		$id = (int) $id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($id)){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;
		$status = $this->input->post("status"); // get status inactive

		// change automatic to manual if there are some changes on selected post in that general location
		// $pd = $this->ghcm->getById($nation_code, $id);
		// $di = array();
		// $di['status'] = 'automatic';
		// $this->ghcm->updateManualSystem($nation_code, $pd->postal_district, $di);

		$this->ghcm->del($nation_code, $id);

		$this->status = 200;
		$this->message = 'success';	
		if(!isset($id)){
			$data = new stdClass();
			$this->status = 441;
			$this->message = 'No Data';
			$this->__json_out($data);
			die();
		}
		$this->__json_out($data);
	}

	public function getcommunitydata() {
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

		$location = $this->input->post("id_kelurahan_modal");
        $active_status = $this->input->post("status");

		//validate custom input
		$is_active = "";
		switch ($active_status) {
			case 'publish_active':
				$is_active=1;
				break;
			case 'inactive':
				$is_active=0;
				break;
			default:
				$is_active = "";
				break;
		}

		//input validation
        $sortCol = $iSortCol_0;
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }

        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }
        
        if (empty($draw)) {
            $draw = 0;
        }

        if (empty($pagesize)) {
            $pagesize=10;
        }

        if (empty($page)) {
            $page=0;
        }

		$keyword = $sSearch;

		$dcount = $this->ghcm->countAllCommunity($nation_code, $keyword, $location, $is_active);
		$ddata = $this->ghcm->getAllCommunity($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $location, $is_active);
		
		foreach($ddata as &$gd){
			
			// START by Muhammad Sofi 10 January 2022 13:58 | read text with emoji
			if(isset($gd->title)){
				if (strlen($gd->title)>255) {
					$gd->title = substr($this->__convertToEmoji($gd->title), 0, 255)." <strong>(More...)<strong>";
				} else {
					$gd->title = $this->__convertToEmoji($gd->title);
				}
			}

			if(isset($gd->description)){
				if (strlen($gd->description)>255) {
					$gd->description = substr($this->__convertToEmoji($gd->description), 0, 255)." <strong>(More...)<strong>"; // by Muhammad Sofi 10 January 2022 15:46 | change position of substr
				} else {
					$gd->description = $this->__convertToEmoji($gd->description);
				}
			}
			// END by Muhammad Sofi 10 January 2022 13:58 | read text with emoji

            if (isset($gd->cdate)) {
				$gd->cdate = date("d/M/y", strtotime($gd->cdate));
            }

            if (isset($gd->user)) {
                $user = $gd->user;
                $gd->user = '<span style="font-size: 1.2em; font-weight: bolder;">'.$user.'</span><br />';
                // if (isset($gd->address2)) $gd->user .= '<span>Address: '.$gd->address2.'</span><br />';
				if (isset($gd->address2)) {
					$fulladdress = $gd->address2.','.$gd->kelurahan.','.$gd->kecamatan.','.$gd->kabkota.','.$gd->provinsi;
					$limitaddress = substr($fulladdress, 0, 30)."<strong>......<strong>";
					$gd->user .= '<span>Address: '.$limitaddress.'</span>';
				} 
            }

            if (isset($gd->is_active)) {
				$nama = '';
                if (empty($gd->is_active)) {
                    $prop = '<span class="tbl-content-nok">Inactive <i class="fa fa-times"></i></span>';
                    $nama .= $prop.'';
                } else {
					$prop = '<span class="tbl-content-ok">Active <i class="fa fa-check"></i></span>';
					$nama .= $prop.'';
                }
                $gd->is_active = $nama;
            }
		}
		$this->status = 200;
		$this->message = 'Success';
		$this->__jsonDataTable($ddata,$dcount);
	}

	public function getGeneralLocation() {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;

        //standar input
        $search = $this->input->post("search");

        $ddata = $this->ghcm->getAllGeneralLocation($nation_code, -1, -1, $search, 1);
        $data = array();

        foreach ($ddata as $gd) {
            $data[] = array("id"=>$gd->id, "text"=>$gd->general_location);
        }
        echo json_encode($data);
    }
	
	// count to limit inserting to 10 highlight post based on kelurahan
	public function getCountPostalDistrict($kelurahan) {
		$data = $this->__init();
		if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;
		
		$datapd = $this->ghcm->countPostalDistrict($nation_code, $kelurahan);
		echo json_encode($datapd, JSON_PRETTY_PRINT);
	}

	// by Muhammad Sofi 2 February 2022 11:22 | comment unused code

	// by Muhammad Sofi 14 December 2021 17:32 | add function to change from manual to automatic 
	// public function setSystemAutomatic($postalDistrict) {
	// 	$data = $this->__init();
	// 	if (!$this->admin_login) {
    //         redir(base_url_admin('login'));
    //         die();
    //     }
	// 	$pengguna = $data['sess']->admin;
	// 	$nation_code = $pengguna->nation_code;

	// 	// change manual to automatic
	// 	$this->ghcm->updateAutomaticSystem($nation_code, $postalDistrict, 'automatic');

	// 	$this->status = 200;
	// 	$this->message = 'success';	

	// 	$this->__json_out($data);
	// }
}