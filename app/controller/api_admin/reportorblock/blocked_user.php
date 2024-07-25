<?php
class Blocked_User extends JI_Controller {

    public function __construct() {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_admin/b_blocked_user_model", 'bbum');
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

        $from_date = $this->input->post("cdate_start");
        $to_date = $this->input->post("cdate_end");
        $type = $this->input->post("type");

        $sortCol = "cdate";
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }

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

        switch ($iSortCol_0) {
            case 0:
                $sortCol = "no";
                break;
            case 1:
                $sortCol = "id";
                break;
            case 2:
                $sortCol = "user_name";
                break;
            case 3:
                $sortCol = "user_email";
                break;
            case 4:
                $sortCol = "blocked_user_name";
                break;
            case 5:
                $sortCol = "blocked_user_email";
                break;
            case 6:
                $sortCol = "blocked_date";
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
        $dcount = $this->bbum->countAllBlockedUser($keyword, $from_date, $to_date, $type);
        $ddata = $this->bbum->getAllBlockedUser($page, $pagesize, $sortCol, $sortDir, $keyword, $from_date, $to_date, $type);

        // foreach ($ddata as &$gd) {
        //     if (isset($gd->is_active)) {
		// 		$gd->is_active = $gd->is_active === "1" ? "Active" : "Inactive";
        //     }
		// }

        $this->__jsonDataTable($ddata, $dcount);
    }

	public function detail($id){
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

		$data = $this->grobu->getById($id);
		if(!isset($data->id)){
			$this->status = 400;
			$this->message = 'Invalid ID or Data has been deleted';
			$this->__json_out($data);
			die();
		}
		$this->status = 200;
		$this->message = 'Success';
		$this->__json_out($data);
	}

    public function delete($id) {
		$d = $this->__init();
		$data = array();

		$res = $this->grobu->del($id);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 902;
			$this->message = 'Failed while deleting data from database';
		}
		$this->__json_out($data);
	}
}
