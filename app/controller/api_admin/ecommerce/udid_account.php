<?php
class Udid_Account extends JI_Controller {
    public $is_log = 1;

    public function __construct() {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_admin/g_user_udid_account", 'guua');
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
                $sortCol = "id";
                break;
            case 1:
                $sortCol = "udid";
                break;
            case 2:
                $sortCol = "account";
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
        $dcount = $this->guua->countAll($keyword, $type);
        $ddata = $this->guua->getAll($page, $pagesize, $sortCol, $sortDir, $keyword, $type);

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

	public function detail($device_id){

		$d = $this->__init();
		$data = array();
		
		$data = $this->guua->getById($device_id);
		
		$this->status = 200;
		$this->message = 'Success';
		$this->__json_out($data);
	}

    public function detail_list_udid_account() {
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

		$udid = $this->input->post('detail_udid');

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
                $sortCol = "fnama";
                break;
            case 2:
                $sortCol = "email";
                break;
            case 2:
                $sortCol = "create_date";
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
		$dcount = $this->guua->countDetailAll($keyword, $udid);
		$data = $this->guua->getDetailAll($page, $pagesize, $sortCol, $sortDir, $keyword, $udid);

		foreach($data as &$gd){

		}

        $this->__jsonDataTable($data, $dcount);
    }
}