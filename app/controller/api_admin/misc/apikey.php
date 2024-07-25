<?php
class ApiKey extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->lib('conumtext');
		$this->load("api_admin/a_apikey_model",'aam');
	}

	private function __codeGen($nation_code)
	{
			$this->lib("conumtext");
			$token = $this->conumtext->genRand($type="str", $min=6, $max=14);
			return $nation_code.''.$token;
	}

	public function index(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Unauthorized");
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
				$sortCol = "username";
				break;
			case 2:
				$sortCol = "password";
				break;
      case 3:
        $sortCol = "is_active";
        break;
			default:
				$sortCol = "id";
		}

		if(empty($draw)) $draw = 0;
		if(empty($pagesize)) $pagesize=10;
		if(empty($page)) $page=0;

		$keyword = $sSearch;

		//advanced filter
		$is_active = $this->input->post("is_active");
		if($is_active == "1"){
		}else if($is_active == "0"){
		}else{
			$is_active = "";
		}

		$this->status = 200;
		$this->message = 'Success';
		$jenis_count = $this->aam->countAll($keyword,$is_active);
		$jenis_data = $this->aam->getAll($page,$pagesize,$sortCol,$sortDir,$keyword,$is_active);

		foreach($jenis_data as &$gd){
			if(isset($gd->is_active)){
				if(!empty($gd->is_active)){
					$gd->is_active = 'Active';
				}else{
					$gd->is_active = 'Inactive';
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
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$di = $_POST;
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;
		if(!isset($di['username'])) $di['username'] = "";
		if(strlen($di['username'])>1){
			$check = $this->aam->checkusername($nation_code,$di['username']); //1 = sudah digunakan
			if(empty($check)){
				if(isset($di['password'])) $di['password'] = strtolower(hash('sha256',$di['password']));
				$di['str'] = $this->__codeGen($di['nation_code']);
				$di['code'] = strtolower(hash('sha256',$di['str']));
				$di['id'] = $this->aam->getLastId($nation_code);
				$res = $this->aam->set($di);
				if($res){
					$last_pengguna_id = $di['id'];
					$this->status = 200;
					$this->message = 'Administrator account successfully created';
				}else{
					$this->status = 900;
					$this->message = 'Cannot add data, please try again later';
				}
			}else{
				$this->status = 104;
				$this->message = 'Username already in used, please choose another username';
			}
		}
		$this->__json_out($data);
	}
	public function detail($nation_code,$id){
		$id = (int) $id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($id)){
			$this->status = 400;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;

		$this->status = 200;
		$this->message = 'Success';
		$data = $this->aam->getById($nation_code,$id);
		$this->__json_out($data);
	}
	public function edit($nation_code,$id){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;
		$du = $_POST;
		if(!isset($du['id'])) $du['id'] = 0;
		if(!isset($du['nation_code'])) $du['nation_code'] = $pengguna->nation_code;;
		$id = (int) $du['id'];
		unset($du['id']);
		if(strlen($du['nation_code'])>0 && $id>0){
			$check = 0;
			if(isset($du['username'])){
				$check = $this->aam->checkusername($du['nation_code'],$du['username'],$id); //1 = sudah digunakan
			}
			if(empty($check)){
				$du['str'] = $this->__codeGen($du['nation_code']);
				$du['code'] = strtolower(hash('sha256',$du['str']));
				$res = $this->aam->update($du['nation_code'],$id,$du);
				if($res){
					$this->status = 200;
					$this->message = 'Changes successfully applied';
				}else{
					$this->status = 901;
					$this->message = 'Failed while updating data, please try again later';
				}
			}else{
				$this->status = 104;
				$this->message = 'Username already taken, please try another username';
			}
		}else{
			$this->status = 448;
			$this->message = 'ID not found';
		}
		$this->__json_out($data);
	}

	public function editpass(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}

		$nation_code = $this->input->post("nation_code");
		if(strlen($nation_code)==0){
			$this->status = 599;
			$this->message = 'Invalid Nation Code';
			$this->__json_out($data);
			die();
		}
		$id = (int) $this->input->post("id");
		if($id<=0){
			$this->status = 598;
			$this->message = 'Invalid PenggunaID';
			$this->__json_out($data);
			die();
		}
		$du = array();
		$du['password'] = $this->input->post("password");
		if(strlen($du['password'])){
			$du['str'] = $this->__codeGen($nation_code);
			$du['code'] = strtolower(hash('sha256',$du['str']));
			$du['password'] = strtolower(hash('sha256',$du['password']));
			$res = $this->aam->update($nation_code,$id,$du);
			$this->status = 200;
			$this->message = 'Perubahan berhasil diterapkan';
		}else{
			$this->status = 901;
			$this->message = 'Password terlalu pendek';
		}
		$this->__json_out($data);
	}

	public function hapus($nation_code,$id){
		$id = (int) $id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($id)){
			$this->status = 401;
			$this->message = 'Access denied';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		if(empty($nation_code) || $nation_code<=0){
			$this->status = 400;
			$this->message = 'Wrong nation code';
			$this->__json_out($data);
			die();
		}

		$this->status = 200;
		$this->message = 'Success';
		$res = $this->aam->del($nation_code,$id);
		if(!$res){
			$this->status = 902;
			$this->message = 'Failed deleting data from database';
		}
		$this->__json_out($data);
	}

}
