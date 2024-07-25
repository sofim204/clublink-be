<?php
class Install_Trace extends JI_Controller {
    public $is_log = 1;

    public function __construct() {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_admin/g_install_trace_model", 'gitm');
    }

    public function index() {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access, please login';
            header("HTTP/1.0 400 Unauthorized access, please login");
            $this->__json_out($data);
            die();
        }

        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $sSearch = $this->input->request("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $pagesize = $this->input->request("iDisplayLength");

        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");

		$type = $this->input->post("type_multilanguage");

        $sortCol = "date";
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }

		switch ($iSortCol_0) {
            case 0:
                $sortCol = "no";
                break;
            case 1:
                $sortCol = "gmra.id";
                break;
            case 2:
                $sortCol = "gmra.referral";
                break;
            case 3:
                $sortCol = "COUNT(gmra.referral)";
                break;
            case 4:
                $sortCol = "SUM(IF(gmra.is_downloaded = 1, 1, 0))";
                break;
            case 5:
                $sortCol = "SUM(IF(gmra.is_registered = 1, 1, 0))";
                break;
            default:
                $sortCol = "no";
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

        $this->status = 200;
        $this->message = 'Success';
        $dcount = $this->gitm->countAll($keyword, $type);
        $ddata = $this->gitm->getAll($page, $pagesize, $sortCol, $sortDir, $keyword, $type);

		foreach ($ddata as &$gd) {
            if (isset($gd->is_downloaded)) {
				$gd->is_downloaded = $gd->is_downloaded == "1" ? "yes" : "no";
            }

			if (isset($gd->is_registered)) {
				$gd->is_registered = $gd->is_registered == "1" ? "yes" : "no";
            }
		}

        $this->__jsonDataTable($ddata, $dcount);
    }

	public function detail($referral){

		$d = $this->__init();
		$data = array();
		
		$data = $this->gitm->getById($referral);
		
		$this->status = 200;
		$this->message = 'Success';
		$this->__json_out($data);
	}

    public function detail_list_install_trace() {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access, please login';
            header("HTTP/1.0 400 Unauthorized access, please login");
            $this->__json_out($data);
            die();
        }

        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $sSearch = $this->input->request("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $pagesize = $this->input->request("iDisplayLength");

        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");

		$referral = $this->input->post('detail_referral');

        $sortCol = "cdate";
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }

		switch ($iSortCol_0) {
            case 0:
                $sortCol = "no";
                break;
            case 1:
                $sortCol = "cdate";
                break;
            case 2:
                $sortCol = "is_downloaded";
                break;
            case 3:
                $sortCol = "cdate_downloaded";
                break;
            case 4:
                $sortCol = "is_registered";
                break;
            case 5:
                $sortCol = "cdate_registered";
                break;
            default:
                $sortCol = "no";
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

        $this->status = 200;
        $this->message = 'Success';
		$dcount = $this->gitm->countDetailAll($keyword, $referral);
		$data = $this->gitm->getDetailAll($page, $pagesize, $sortCol, $sortDir, $keyword, $referral);

		foreach($data as &$gd){
            if(isset($gd->register_place_alamat2)) {
                $location = $gd->register_place_alamat2. ", " .$gd->register_place_kabkota;
                $gd->register_place_alamat2 = $location;
            }
		}

        $this->__jsonDataTable($data, $dcount);
    }
}
