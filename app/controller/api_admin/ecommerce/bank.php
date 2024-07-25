<?php
class Bank extends JI_Controller{

	public function __construct(){
    parent::__construct();
		//$this->setTheme('frontx');
		$this->load("api_admin/c_bank_model",'cbm');
	}
	public function index(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Harus login';
			header("HTTP/1.0 400 Harus login");
			$this->__json_out($data);
			die();
		}

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
				$sortCol = "bank_nama";
				break;
			case 2:
				$sortCol = "cabang_nama";
				break;
			case 3:
				$sortCol = "rekening_nama";
				break;
			case 4:
				$sortCol = "rekening_nomor";
				break;
			case 5:
				$sortCol = "is_active";
				break;
			default:
				$sortCol = "id";
		}

		if(empty($draw)) $draw = 0;
		if(empty($pagesize)) $pagesize=10;
		if(empty($page)) $page=0;

		$keyword = $sSearch;


		$this->status = 100;
		$this->message = 'Berhasil';
		$jenis_count = $this->cbm->countAll($keyword);
		$jenis_data = $this->cbm->getAll($page,$pagesize,$sortCol,$sortDir,$keyword);

		foreach($jenis_data as &$gd){
			if(isset($gd->is_active)){
				if(!empty($gd->is_active)){
					$gd->is_active = '<span class="label label-success">Active</span>';
				}else{
					$gd->is_active = '<span class="label label-default">Not Active</span>';
				}
			}
		}
		//sleep(3);
		$another = array();
		$this->__jsonDataTable($jenis_data,$jenis_count);
	}
	public function tambah(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Harus login';
			header("HTTP/1.0 400 Harus login");
			$this->__json_out($data);
			die();
		}
		$di = $_POST;
		if(!isset($di['bank_nama'])) $di['bank_nama'] = "";
		if(!isset($di['rekening_nomor'])) $di['rekening_nomor'] = "";
		if(strlen($di['bank_nama'])>1 && strlen($di['rekening_nomor'])>1){
			$res = $this->cbm->set($di);
			if($res){
				$this->status = 100;
				$this->message = 'Data baru berhasil ditambahkan';
			}else{
				$this->status = 900;
				$this->message = 'Tidak dapat menyimpan data baru, silakan coba beberapa saat lagi';
			}
		}
		$this->__json_out($data);
	}
	public function detail($id){
		$id = (int) $id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($id)){
			$this->status = 400;
			$this->message = 'Harus login';
			header("HTTP/1.0 400 Harus login");
			$this->__json_out($data);
			die();
		}
		$this->status = 100;
		$this->message = 'Berhasil';
		$data = $this->cbm->getById($id);
		$this->__json_out($data);
	}
	public function edit($id){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Harus login';
			header("HTTP/1.0 400 Harus login");
			$this->__json_out($data);
			die();
		}
		$du = $_POST;

		$id = (int) $id;
		if(empty($id) && isset($du['id'])){
			$id = (int) $du['id'];
			unset($du['id']);
		}

		if(isset($du['id'])) unset($du['id']);
		if(!isset($du['bank_nama'])) $du['bank_nama'] = "";
		if(!isset($du['rekening_nomor'])) $di['rekening_nomor'] = "";
		if($id>0 && strlen($du['rekening_nomor'])>1 && strlen($du['rekening_nomor'])>1){
			$res = $this->cbm->update($id,$du);
			if($res){
				$this->status = 100;
				$this->message = 'Perubahan berhasil diterapkan';
			}else{
				$this->status = 901;
				$this->message = 'Tidak dapat melakukan perubahan ke basis data';
			}
		}
		$this->__json_out($data);
	}
	public function hapus($id){
		$id = (int) $id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($id)){
			$this->status = 400;
			$this->message = 'Harus login';
			header("HTTP/1.0 400 Harus login");
			$this->__json_out($data);
			die();
		}
		$this->status = 100;
		$this->message = 'Berhasil';
		$res = $this->cbm->del($id);
		if(!$res){
			$this->status = 902;
			$this->message = 'Data gagal dihapus';
		}
		$this->__json_out($data);
	}
}
