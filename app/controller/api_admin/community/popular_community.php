<?php
class Popular_Community extends JI_Controller {
	public function __construct(){
		parent::__construct();
		$this->lib("seme_purifier");
		$this->load("api_admin/c_homepage_main_popular_model", "chmpm");
		$this->load("api_admin/a_pengguna_model", "apm");
		$this->load("api_admin/c_community_list_model", "cclm");
		$this->current_parent = 'band';
		$this->current_page = 'band_popular';
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

		switch($iSortCol_0) {
			case 0:
				$sortCol = "no";
				break;
			case 1:
				$sortCol = "id";
				break;
            case 2:
                $sortCol = "title";
                break;
			case 3:
				$sortCol = "status_active";
				break;
			case 4:
				$sortCol = "priority";
				break;
			case 5:
				$sortCol = "start_date";
				break;
			case 6:
				$sortCol = "end_date";
				break;
			case 7:
				$sortCol = "a_pengguna_id";
				break;
			case 8:
				$sortCol = "cdate";
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
		$dcount = $this->chmpm->countAllPopularCommunityPost($pengguna->nation_code, $keyword);
		$ddata = $this->chmpm->getAllPopularCommunityPost($pengguna->nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword);

		foreach($ddata as &$gd){
			if(isset($gd->a_pengguna_id)) {
				if(is_null($gd->a_pengguna_id)) {
					$admin_name = "";
				} else {
					$get_admin_name = $this->apm->getById($pengguna->nation_code, $gd->a_pengguna_id);
					$admin_name = $get_admin_name->nama;
				}

				$gd->a_pengguna_id = $admin_name;
			} else {
				$gd->a_pengguna_id = "";
			}

			if (isset($gd->status_active)) {
                $status_active = "";
				if($gd->status_active == '1') {
                    $status_active = '<label class="label label-success">Active</label>';
                } else {
                    $status_active = '<label class="label label-danger">Inactive</label>';
                }				 
                $gd->status_active = '<center><span>'.$status_active.'</span></center><div style="margin-bottom: 3px;"></div>';
            }
		}

		// var_dump($ddata);die();
		//sleep(3);
		$another = array();
		$this->__jsonDataTable($ddata,$dcount);
	}

	public function get_community() {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;

        //standar input
        $search = $this->input->post("search");

        $ddata = $this->chmpm->getCommunityList($nation_code, $search, 1);

        $data = array();

        foreach ($ddata as $gd) {
            $data[] = array("id"=>$gd->id, "text"=>$gd->title);
        }
        echo json_encode($data);
    }

	public function change_community_post($id) {
		$d = $this->__init();
        $pengguna = $d['sess']->admin;

		$data = array();

		$gd = $this->chmpm->getById($pengguna->nation_code, $id);
		if(!isset($gd->id)){
			$this->status = 111;
			$this->message = 'Wrong ID';
			$this->__json_out($data);
			die();
		}

		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}

		$this->chmpm->trans_start();

        $du = [
            'custom_id' => $_POST['custom_id'],
            'priority' => $_POST['priority'],
			'is_active' => $_POST['is_active'],
            'a_pengguna_id' => $pengguna->id,
			'start_date' => $_POST['start_date'],
			'end_date' => $_POST['end_date'],
        ];
		
		$res = $this->chmpm->update($pengguna->nation_code, $id, $du);
		if($res) {
			$this->status = 200;
			$this->message = 'Success';
			$this->chmpm->trans_commit();
		} else {
			$this->status = 901;
			$this->message = 'Failed update to database';
			$this->chmpm->trans_rollback();
		}
		$this->chmpm->trans_end();
		$this->__json_out($data);
	}

	public function add_popular_community_to_homepage() {
		$d = $this->__init();
        $pengguna = $d['sess']->admin;

		$data = array();

		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}

		$this->chmpm->trans_start();

		// check if club already registered
		$community_id = $_POST['custom_id'];
		// $checkdata = $this->chmpm->checkPopularClubAlreadyRegistered($community_id);
		// if(isset($checkdata->custom_id)) {
		// 	$this->status = 920;
		// 	$this->message = 'Club already registered';
		// 	$this->__json_out($data);
		// 	die();
		// 	// $du = array();
		// 	// $du['custom_id'] = $community_id;
		// 	// $this->chmpm->updateBy($pengguna->nation_code, $_POST['priority'], $du);
		// }

		// check exist community
		$checkdata = $this->cclm->getById($pengguna->nation_code, $community_id);
		if(!isset($checkdata->id)) {
			$this->status = 920;
			$this->message = 'Community id not found';
			$this->__json_out($data);
			die();
		}

		// check if there is same priority with start date end date
		$checkdata = $this->chmpm->checkDuplicatePriorityAndStartEndDate($pengguna->nation_code, $_POST['priority'], $_POST['start_date'], $_POST['end_date'], "community");
		if($checkdata > 0) {
			$this->status = 920;
			$this->message = 'There is priority '. $_POST['priority'] . ' in date between ' . $_POST['start_date'] . ' and ' . $_POST['end_date'] .'. Please review first';
			$this->__json_out($data);
			die();
		}

        $di = [
            'nation_code' => $pengguna->nation_code,
            'id' => $this->GUIDv4(),
			'a_pengguna_id' => $pengguna->id,
			'type' => "community",
            'custom_id' => $community_id,
            'priority' => $_POST['priority'],
            'cdate' => 'NOW()',
            'start_date' => $_POST['start_date'] ? $_POST['start_date'] : DATE("Y-m-d"),
            'end_date' => $_POST['end_date'] ? $_POST['end_date'] : DATE("Y-m-d"),
            'cdate' => 'NOW()',
            'is_active' => '1',
        ];
		
		$res = $this->chmpm->set($di);
		if($res) {
			$this->status = 200;
			$this->message = 'Success';
			$this->chmpm->trans_commit();
		} else {
			$this->status = 901;
			$this->message = 'Failed update to database';
			$this->chmpm->trans_rollback();
		}
		$this->chmpm->trans_end();
		$this->__json_out($data);
	}

	// public function change_and_reorder_popular_community_post() {
	// 	$d = $this->__init();
    //     $pengguna = $d['sess']->admin;

	// 	$data = array();

	// 	$gd = $this->chmpm->getById($pengguna->nation_code, $id);
	// 	if(!isset($gd->id)){
	// 		$this->status = 111;
	// 		$this->message = 'Wrong ID';
	// 		$this->__json_out($data);
	// 		die();
	// 	}

	// 	if(!$this->admin_login){
	// 		$this->status = 400;
	// 		$this->message = 'Unauthorized access';
	// 		header("HTTP/1.0 400 Unauthorized");
	// 		$this->__json_out($data);
	// 		die();
	// 	}

	// 	$this->chmpm->trans_start();

    //     $du = [
    //         'i_group_id' => $_POST['i_group_id'],
    //         'ldate' => 'NOW()',
    //         'admin_name' => $pengguna->user_alias,
    //     ];
		
	// 	$res = $this->chmpm->update($pengguna->nation_code, $id, $du);
	// 	if($res) {
	// 		$this->status = 200;
	// 		$this->message = 'Success';
	// 		$this->chmpm->trans_commit();
	// 	} else {
	// 		$this->status = 901;
	// 		$this->message = 'Failed update to database';
	// 		$this->chmpm->trans_rollback();
	// 	}
	// 	$this->chmpm->trans_end();
	// 	$this->__json_out($data);
	// }
}