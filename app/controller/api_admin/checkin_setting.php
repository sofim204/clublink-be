<?php
class Checkin_Setting extends JI_Controller {
    public $is_log = 1;

    public function __construct() {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_admin/g_checkin_setting_model", 'gcsm');
        $this->load("api_admin/g_pointpolicycheckin_model",'ppc_model');
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
        $path = $this->input->post("path");

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
                $sortCol = "start_date";
                break;
            case 3:
                $sortCol = "end_date";
                break;
            // case 4:
            //     $sortCol = "period";
            //     break;
            case 4:
                $sortCol = "cdate";
                break;
            case 5:
                $sortCol = "is_active";
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
        $dcount = $this->gcsm->countAll($keyword, $from_date, $to_date, $path);
        $ddata = $this->gcsm->getAll($page, $pagesize, $sortCol, $sortDir, $keyword, $from_date, $to_date, $path);

        foreach ($ddata as &$gd) {
            if (isset($gd->is_active)) {
				$gd->is_active = $gd->is_active === "1" ? "Active" : "Inactive";
            }
		}

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

		$data = $this->gcsm->getById($id);
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

		$res = $this->gcsm->del($id);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 902;
			$this->message = 'Failed while deleting data from database';
		}
		$this->__json_out($data);
	}

    public function add() {
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
        $nation_code = $d['sess']->admin->nation_code;

		$di = $_POST;
		if(!isset($di['start_date'])) $di['start_date'] = "";
		if(!isset($di['period'])) $di['period'] = "";
		if(!isset($di['end_date'])) $di['end_date'] = "";
 
		$this->gcsm->trans_start();
		$lastId = (int) $this->gcsm->getLastId();

		$di['id'] = $lastId;
        $di['nation_code'] = $nation_code;
        $di['cdate'] = "NOW()";
        $di['is_active'] = "1";

		$res = $this->gcsm->set($di);
		if($res){
			$this->gcsm->trans_commit();
			$this->status = 200;
			$this->message = 'Data successfully added';
		}else{
			$this->gcsm->trans_rollback();
			$this->status = 900;
			$this->message = 'Can\'t add Data';
		}
		$this->gcsm->trans_end();
		
		$this->__json_out($data);
	}

    public function edit($id) {
		$d = $this->__init();
		$data = array();

		$id = (int) $id;
		if($id<=0){
			$this->status = 451;
			$this->message = 'Invalid ID';
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

		$this->status = 800;
		$this->message = 'One or more parameter are required';
		$du = $_POST;
		if(isset($du['id'])) unset($du['id']);
		if($id > 0){
			$res = $this->gcsm->update($id, $du);
			if($res){
				$this->status = 200;
				$this->message = 'Perubahan berhasil diterapkan';
			}else{
				$this->status = 901;
				$this->message = 'Failed to make data changes';
			}
		}
		$this->__json_out($data);
	}

    public function checkin_daily() {
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Authorization required");
			$this->__json_out($data);
			die();
		}
		$nation_code = $d['sess']->admin->nation_code;

		//declare config variable
		$id = 126;
		$code = "E1";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppc_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppc_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

    public function checkin_weekly() {
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Authorization required");
			$this->__json_out($data);
			die();
		}
		$nation_code = $d['sess']->admin->nation_code;

		//declare config variable
		$id = 127;
		$code = "E2";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppc_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppc_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

    public function checkin_monthly() {
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Authorization required");
			$this->__json_out($data);
			die();
		}
		$nation_code = $d['sess']->admin->nation_code;

		//declare config variable
		$id = 128;
		$code = "E3";
		$classified = "leaderboard_point";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ppc_model->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ppc_model->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
}
