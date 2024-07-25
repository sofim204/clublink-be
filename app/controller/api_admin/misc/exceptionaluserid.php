<?php
class Exceptionaluserid extends JI_Controller {
    public $is_log = 1;

    public function __construct() {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_admin/a_exception_user_force_id_model", 'aeufim_model');
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
                $sortCol = "id";
                break;
            case 2:
                $sortCol = "b_user_id_sg";
                break;
            case 3:
                $sortCol = "b_user_id_id";
                break;
            case 4:
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
        $dcount = $this->aeufim_model->countAll($keyword);
        $ddata = $this->aeufim_model->getAll($page, $pagesize, $sortCol, $sortDir, $keyword);

        $this->__jsonDataTable($ddata, $dcount);
    }

    // by Muhammad Sofi 27 January 2022 16:42 | adding form add data
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

		$di = $_POST;
 
		// if(strlen($di['id'])>0){
		$this->aeufim_model->trans_start();
		$lastId = (int) $this->aeufim_model->getLastId();

		$di['id'] = $lastId;
		$di['b_user_id_sg'] = '';

		$res = $this->aeufim_model->set($di);
		if($res){
			$this->aeufim_model->trans_commit();
			$this->status = 200;
			$this->message = 'Data successfully added';
		}else{
			$this->aeufim_model->trans_rollback();
			$this->status = 900;
			$this->message = 'Can\'t add Data';
		}
		$this->aeufim_model->trans_end();
		// }
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
		if(!isset($du['b_user_id_id'])) $du['b_user_id_id'] = "";
		if($id > 0){
			$res = $this->aeufim_model->update($id, $du);
			if($res){
				$this->status = 200;
				$this->message = 'Data updated successfully';
			}else{
				$this->status = 901;
				$this->message = 'Failed to make data changes';
			}
		}
		$this->__json_out($data);
	}

	public function delete($id) {
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

		$res = $this->aeufim_model->del($id);
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
