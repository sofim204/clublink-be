<?php
class List_Referralcode extends JI_Controller {
    public $is_log = 1;

    public function __construct() {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_admin/g_user_referral_code_model", 'gurcm');
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
        $user_status = $this->input->post("user_status");

        $sortCol = "cdate";
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }

        //validating date interval
        // if (strlen($from_date)==10) {
        //     $from_date = date("Y-m-d", strtotime($from_date));
        // } else {
        //     $from_date = "";
        // }
        // if (strlen($to_date)==10) {
        //     $to_date = date("Y-m-d", strtotime($to_date));
        // } else {
        //     $to_date = "";
        // }

        switch ($iSortCol_0) {
            case 0:
                $sortCol = "id";
                break;
            case 1:
                $sortCol = "fnama";
                break;
            case 2:
                $sortCol = "email";
                break;
            case 3:
                $sortCol = "is_active";
                break;
            case 4:
                $sortCol = "kode_referral";
                break;
            case 5:
                $sortCol = "total_recruited";
                break;
            case 6:
                $sortCol = "cdate";
                break;
            default:
                $sortCol = "id";
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

        $dcount = $this->gurcm->countAll($keyword, $from_date, $to_date, $user_status);
        $ddata = $this->gurcm->getAll($page, $pagesize, $sortCol, $sortDir, $keyword, $from_date, $to_date, $user_status);

        foreach ($ddata as &$gd) {
            if(isset($gd->is_active)) {
                if($gd->is_active != "0") {
                    $gd->is_active = "Active";
                } else {
                    $gd->is_active = "Inactive";
                }
            }
		}

        $this->__jsonDataTable($ddata, $dcount);
    }

    public function detail($id){
		// $id = (int) $id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($id)){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}

		$data = $this->gurcm->getById($id);
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
    
	public function detail_list_referral_code() {
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

		$b_user_id_recruiter = $this->input->post('id_user_recruiter');
		$referral_type = $this->input->post('filter_referral_type');

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
                $sortCol = "b_user_id_recruiter";
                break;
            case 1:
                $sortCol = "fnama";
                break;
            case 2:
                $sortCol = "referral_type";
                break;
            case 3:
                $sortCol = "cdate";
                break;
            case 4:
                $sortCol = "register_place_alamat2";
                break;
            default:
                $sortCol = "b_user_id_recruiter";
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
		$dcount = $this->gurcm->countDetailAll($keyword, $b_user_id_recruiter, $referral_type);
		$data = $this->gurcm->getDetailAll($page, $pagesize, $sortCol, $sortDir, $keyword, $b_user_id_recruiter, $referral_type);

		foreach($data as &$gd){

		}

        $this->__jsonDataTable($data, $dcount);
    }
}
