<?php
class Produkwanted extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->load("api_admin/b_user_produkwanted_model",'bupm');
		$this->load("api_admin/b_user_model","bum");
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
		$tbl2_as = $this->bupm->getTableAlias2();

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
				$sortCol = "b_user_id";
				break;
			case 3:
				$sortCol = "$tbl2_as.fnama";
				break;
			case 4:
				$sortCol = "keyword_text";
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
		$dcount = $this->bupm->countAll($nation_code,$keyword);
		$ddata = $this->bupm->getAll($nation_code,$page,$pagesize,$sortCol,$sortDir,$keyword);

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

		$this->bupm->trans_start();
		$di = $_POST;
		$di['nation_code'] = $d['sess']->admin->nation_code;
		$di['id'] = $this->bupm->getLastId($di['nation_code']);
		$res = $this->bupm->set($di);
		if($res){
			$this->status = 200;
			$this->message = 'Data successfully added';
			$this->bupm->trans_commit();
		}else{
			$this->bupm->trans_rollback();
			$this->status = 900;
			$this->message = 'Tidak dapat menyimpan data baru, silakan coba beberapa saat lagi';
		}
		$this->bupm->trans_end();
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
		$data = $this->bupm->getById($nation_code,$id);
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
		if(!isset($du['keyword_text'])) $di['keyword_text'] = "";
    $nation_code = $d['sess']->admin->nation_code;
		if($id>0 && strlen($du['keyword_text'])>0){
				$res = $this->bupm->update($nation_code,$id,$du);
				if($res){
					$this->status = 200;
					$this->message = 'Perubahan berhasil diterapkan';
				}else{
					$this->status = 901;
					$this->message = 'Failed to make data changes';
				}
		}else{
			$this->status = 440;
			$this->message = 'Salah satu parameter ada yang invalid atau kurang parameter';
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
		$res = $this->bupm->del($nation_code,$id);
		if(!$res){
			$this->status = 902;
			$this->message = 'Failed while deleting data from database';
		}
		$this->__json_out($data);
	}
}
