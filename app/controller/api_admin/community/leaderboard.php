<?php
class Leaderboard extends JI_Controller {

	public function __construct() {
    	parent::__construct();
		$this->lib("seme_purifier");
		$this->load("api_admin/g_leaderboard_model", 'glm');
		$this->load("api_admin/g_leaderboard_point_history_model", 'glphm');
		$this->current_parent = 'community';
		$this->current_page = 'community_leaderboard';
	}

	public function index() {
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
		$tbl_as = $this->glm->getTableAlias();

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
		$location = $this->input->post("id_kelurahan");

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

		$dcount = $this->glm->countAll($nation_code, $keyword, $location);
		$ddata = $this->glm->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $location);
		foreach($ddata as &$gd){
			if (isset($gd->user_image)) {
                if (strlen($gd->user_image)<=10) {
                    $gd->user_image = 'media/produk/default.png';
                }
                $gd->user_image = '<img src="'.$this->cdn_url($gd->user_image).'" class="img-responsive" style="max-width: 90px;" onerror="this.onerror=null;this.src=\''.$this->cdn_url('media/produk/default.png').'\';"/>';
            }

			if (isset($gd->user_name)) {
				$user = $gd->user_name;
				$gd->user_name = '<span style="font-size: 1.2em; font-weight: bolder;">'.$user.'</span><br />';

				if (isset($gd->address2)) $gd->user_name .= '<span>Address: '.$gd->address2.'</span><br />';
			}
		}
		//render
        $this->status = 200;
        $this->message = 'Success';
        $this->__jsonDataTable($ddata, $dcount);
	}

	public function getGeneralLocation() {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }
        $pengguna = $data['sess']->admin;
        $nation_code = $pengguna->nation_code;

        //standar input
        $search = $this->input->post("search");

        $ddata = $this->glm->getAllGeneralLocation($nation_code, -1, -1, $search, 1);
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
		
		$datapd = $this->glm->countPostalDistrict($nation_code, $kelurahan);
		echo json_encode($datapd, JSON_PRETTY_PRINT);
	}

    public function getleaderboardpointhistory() {
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
		$tbl_as = $this->glphm->getTblAs();

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
		$location = $this->input->post("id_kelurahan_history");

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

		$dcount = $this->glphm->countAll($nation_code, $keyword, $startDate, $location);
		$ddata = $this->glphm->getAll($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $startDate, $location);

		foreach($ddata as &$gd){
			if (isset($gd->user_name)) {
                $user = $gd->user_name;
                $gd->user_name = '<span style="font-size: 1.2em; font-weight: bolder;">'.$user.'</span><br />';
                if (isset($gd->address2)) $gd->user_name .= '<span>Address: '.$gd->address2.'</span><br />';
            }

            
            if (isset($gd->cdate)) {
                $gd->cdate = date("j F Y H:i:s", strtotime($gd->cdate));
            }
		}
		//render
        $this->status = 200;
        $this->message = 'Success';
        $this->__jsonDataTable($ddata, $dcount);
	}
}
