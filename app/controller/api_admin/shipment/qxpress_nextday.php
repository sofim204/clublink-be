<?php
class Qxpress_NextDay extends JI_Controller{

	public function __construct(){
    parent::__construct();
		//$this->setTheme('frontx');
		$this->load("api_admin/qxpress_basic_model",'qbm');
		//$this->load("api_admin/d_kabkota_model",'qbm');
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
		$nation_code = $d['sess']->admin->nation_code;

		$draw = $this->input->post("draw");
		$sval = $this->input->post("search");
		$sSearch = $this->input->post("sSearch");
		$sEcho = $this->input->post("sEcho");
		$page = $this->input->post("iDisplayStart");
		$pagesize = $this->input->post("iDisplayLength");

		$iSortCol_0 = $this->input->post("iSortCol_0");
		$sSortDir_0 = $this->input->post("sSortDir_0");


		$sortCol = "date";
		$sortDir = strtoupper($sSortDir_0);
		if(empty($sortDir)) $sortDir = "DESC";
		if(strtolower($sortDir) != "desc"){
			$sortDir = "ASC";
		}
		switch($iSortCol_0){
			case 0:
				$sortCol = "id";
				break;
			case 1:
				$sortCol = "nation_code";
				break;
			case 2:
				$sortCol = "weight";
				break;
			case 3:
				$sortCol = "weight_unit";
				break;
			case 4:
				$sortCol = "cost";
				break;
			default:
				$sortCol = "id";
		}

		if(empty($draw)) $draw = 0;
		if(empty($pagesize)) $pagesize=10;
		if(empty($page)) $page=0;

		$keyword = $sSearch;

		$this->status = 200;
		$this->message = 'Success';
		$dcount = $this->qbm->countAll($nation_code,$keyword);
		$ddata = $this->qbm->getAll($nation_code,$page,$pagesize,$sortCol,$sortDir,$keyword);

		//sleep(3);
		$another = array();
		$this->__jsonDataTable($ddata,$dcount);
	}
	public function tambah(){
		$d = $this->__init();

		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$this->qbm->trans_start();
		$di = $_POST;
		$di['nation_code'] = $d['sess']->admin->nation_code;
		$di['id'] = $this->qbm->getLastId($di['nation_code']);
		$res = $this->qbm->set($di);
		if($res){
			$this->status = 200;
			$this->message = 'Data successfully added';
			$this->qbm->trans_commit();
		}else{
			$this->qbm->trans_rollback();
			$this->status = 900;
			$this->message = 'Tidak dapat menyimpan data baru, silakan coba beberapa saat lagi';
		}
		$this->qbm->trans_end();
		$this->__json_out($data);
	}
	public function detail($id){
		$id = (int) $id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		if($id<=0){
			$this->status = 591;
			$this->message = 'Invalid ID';
			$this->__json_out($data);
			die();
		}

    $nation_code = $d['sess']->admin->nation_code;
		$this->status = 200;
		$this->message = 'Success';
		$data = $this->qbm->getById($nation_code,$id);
		$this->__json_out($data);
	}
	public function edit($id){
		//die('edit');
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$id = (int) $id;
		$du = $_POST;
		if(!isset($du['weight'])) $di['weight'] = "";
    $nation_code = $d['sess']->admin->nation_code;
		if($id>0 && strlen($du['weight'])>0 && strlen($du['cost'])>0){
				$res = $this->qbm->update($nation_code,$id,$du);
				if($res){
					$this->status = 200;
					$this->message = 'Data updated successfully';
				}else{
					$this->status = 901;
					$this->message = 'Failed to make data changes';
				}
		}else{
			$this->status = 440;
			$this->message = 'One or more parameter are missing or required';
		}
		$this->__json_out($data);

	}
	public function hapus($id){

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
    $nation_code = $d['sess']->admin->nation_code;
		$this->status = 200;
		$this->message = 'Success';
		$res = $this->qbm->del($nation_code,$id);
		if(!$res){
			$this->status = 902;
			$this->message = 'Failed while deleting data from database';
		}
		$this->__json_out($data);
	}
}
