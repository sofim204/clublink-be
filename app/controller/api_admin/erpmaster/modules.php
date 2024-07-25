<?php
class Modules extends JI_Controller{

	public function __construct(){
    parent::__construct();
		//$this->setTheme('frontx');
		//$this->load("api_admin/a_modules_model",'amod');
		$this->load("api_admin/a_pengguna_module_model","apmm");
		$this->load("api_admin/a_modules_model","amod");
	}
	public function index(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 401;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$nation_code = $d['sess']->admin->nation_code;

		//collect input
		$draw = $this->input->post("draw");
		$sval = $this->input->post("search");
		$sSearch = $this->input->request("sSearch");
		$sEcho = $this->input->post("sEcho");
		$page = $this->input->post("iDisplayStart");
		$pagesize = $this->input->request("iDisplayLength");
		$iSortCol_0 = $this->input->post("iSortCol_0");
		$sSortDir_0 = $this->input->post("sSortDir_0");

		//validating input
		$sortCol = "date";
		$sortDir = strtoupper($sSortDir_0);
		if(empty($sortDir)) $sortDir = "DESC";
		if(strtolower($sortDir) != "desc"){
			$sortDir = "ASC";
		}
		switch($iSortCol_0){
			case 0:
				$sortCol = "identifier";
				break;
			case 1:
				$sortCol = "name";
				break;
			case 2:
				$sortCol = "path";
				break;
			case 3:
				$sortCol = "level";
				break;
			case 3:
				$sortCol = "has_submenu";
				break;
			case 4:
				$sortCol = "priority";
				break;
			case 5:
				$sortCol = "is_visible";
				break;
			default:
				$sortCol = "identified";
		}
		if(empty($draw)) $draw = 0;
		if(empty($pagesize)) $pagesize=10;
		if(empty($page)) $page=0;
		$keyword = $sSearch;

		//render result
		$this->status = 200;
		$this->message = 'Success';
		$dcount = $this->amod->countAll($nation_code,$keyword);
		$ddata = $this->amod->getAll($nation_code,$page,$pagesize,$sortCol,$sortDir,$keyword);

		foreach($ddata as &$gd){
			if(isset($gd->is_visible)){
				$gd->is_visible = (int) $gd->is_visible;
				if($gd->is_visible == 1 || $gd->is_visible == "1"){
					$gd->is_visible = '<span class="label label-success">Iya</span>';
				}else{
					$gd->is_visible = '<span class="label label-default">Tidak</span>';
				}
			}
			if(isset($gd->is_active)){
				if(!empty($gd->is_active)){
					$gd->is_active = '<span class="label label-success">Aktif</span>';

				}else{
					$gd->is_active = '<span class="label label-alert">Tidak Aktif</span>';
				}
			}
		}
		//sleep(3);
		$another = array();
		$this->__jsonDataTable($ddata,$dcount);
	}
	public function tambah(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 401;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$nation_code = $d['sess']->admin->nation_code;

		//collect input
		$di = $_POST;
		if(!isset($di['identifier'])) $di['identifier'] = "";
		if(!isset($di['name'])) $di['name'] = "";
		if(strlen($di['name'])>1 && strlen($di['identifier'])>1){
			$di['nation_code'] = $nation_code;
			$res = $this->amod->set($di);
			if($res){
				$this->status = 200;
				$this->message = 'Data successfully added';
			}else{
				$this->status = 900;
				$this->message = 'Tidak dapat menyimpan data baru, silakan coba beberapa saat lagi';
			}
		}else{
			$this->status = 444;
			$this->message = 'Salah satu parameter ada yang tidak ada atau tidak sah';
		}
		$this->__json_out($data);
	}
	public function detail(){
		$id = $this->input->get('id');
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($id)){
			$this->status = 401;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$nation_code = $d['sess']->admin->nation_code;

		//render result
		$this->status = 200;
		$this->message = 'Success';
		$data = $this->amod->getById($nation_code,$id);
		$this->__json_out($data);
	}
	public function edit($id){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 401;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$nation_code = $d['sess']->admin->nation_code;

		//collect input
		$id = $this->input->get('id');
		$du = $_POST;
		if(!isset($du['identifier'])) $du['identifier'] = "";
		if(!isset($du['name'])) $du['name'] = "";
		if(strlen($id)>1 && strlen($du['name'])>1 && strlen($du['identifier'])>1){
			$res = $this->amod->update($nation_code,$id,$du);
			if($res){
				$this->status = 200;
				$this->message = 'Perubahan berhasil diterapkan';
			}else{
				$this->status = 901;
				$this->message = 'Failed to make data changes';
			}
		}else{
			$this->status = 444;
			$this->message = 'Salah satu parameter ada yang tidak ada atau tidak sah';
		}
		$this->__json_out($data);
	}
	public function hapus(){
		$id = $this->input->get('id');
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($id)){
			$this->status = 401;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$nation_code = $d['sess']->admin->nation_code;

		//render result
		$this->status = 200;
		$this->message = 'Success';
		$res = $this->amod->del($nation_code,$id);
		if(!$res){
			$this->status = 902;
			$this->message = 'Failed while deleting data from database';
		}
		$this->__json_out($data);
	}
	public function get(){
		$s = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 401;
			$this->message = 'Unauthorized access';
			$this->__json_out($data);
			die();
		}
		$nation_code = $s['sess']->admin->nation_code;

		$this->status = 200;
		$this->message = 'Success';
		$data = $this->amod->get($nation_code);
		$this->__json_out($data);
	}

	public function reload(){
		$data= array();
		$s = $this->__init();
		if(!$this->admin_login){
			$this->status = 401;
			$this->message = 'Unauthorized access';
			$this->__json_out($data);
			die();
		}

		//get session
		$sess = $s['sess'];
		$nation_code = $sess->admin->nation_code;
		if(!is_object($sess)) $sess = new stdClass();
		if(!isset($sess->admin)) $sess->admin = new stdClass();
		$sess->admin->menus = new stdClass();
		$sess->admin->menus->left = array();

		//get modules
		$allowed_all = 0;
		$modules = array();
		$sess->admin->modules = $this->apmm->getUserModules($nation_code,$sess->admin->id);
		foreach($sess->admin->modules as $m){
			$m->identifier = $m->a_modules_identifier;
			$id = $m->identifier;
			if(!isset($modules[$id])) $modules[$id] = new stdClass();
			$modules[$id] = $m;
			if(empty($id) && $m->rule == 'allowed_except'){
				$allowed_all = 1;
				break;
			}else if(!empty($id) && $m->rule == 'allowed'){
				$modules[$id] = $m;
			}
		}
		$sess->admin->modules = $modules; unset($modules,$m);
		$sess->admin->menus = new stdClass();
		$sess->admin->menus->left = array();

		//building menu: left
		$parmod = $this->amod->getAllParent($nation_code);
		if($allowed_all){
			$sess->admin->modules = array();
			foreach($parmod as $pm){
				$pmid = $pm->identifier;
				if(!isset($sess->admin->menus->left[$pmid])) $sess->admin->menus->left[$pmid] = new stdClass();
				$sess->admin->menus->left[$pmid] = $pm;
				$sess->admin->menus->left[$pmid]->childs = array();
				$chimod = $this->amod->getChild($nation_code,$pm->identifier);
				if(count($chimod)>0){
					foreach($chimod as $cm){
						$cmid = $cm->identifier;
						if(!isset($sess->admin->menus->left[$pmid]->childs[$cmid])) $sess->admin->menus->left[$pmid]->childs[$cmid] = new stdClass();
						$sess->admin->menus->left[$pmid]->childs[$cmid] = $cm;
						$sess->admin->modules[$cmid] = $cm;
					}
				}
				$sess->admin->modules[$pmid] = $pm;
			}
		}else{
			foreach($parmod as $pm){
				$pmid = $pm->identifier;
				if(!isset($sess->admin->modules[$pmid])) continue;
				if($sess->admin->modules[$pmid]->rule != 'allowed') continue;
				if(!isset($sess->admin->menus->left[$pmid])) $sess->admin->menus->left[$pmid] = new stdClass();
				$sess->admin->menus->left[$pmid] = $pm;
				$sess->admin->menus->left[$pmid]->childs = array();
				$chimod = $this->amod->getChild($nation_code,$pm->identifier);
				if(count($chimod)>0){
					foreach($chimod as $cm){
						$cmid = $cm->identifier;
						if(!isset($sess->admin->modules[$cmid])) continue;
						if($sess->admin->modules[$cmid]->rule != 'allowed') continue;
						if(!isset($sess->admin->menus->left[$pmid]->childs[$cmid])) $sess->admin->menus->left[$pmid]->childs[$cmid] = new stdClass();
						$sess->admin->menus->left[$pmid]->childs[$cmid] = $cm;
						$sess->admin->modules[$cmid] = $cm;
					}
				}
				$sess->admin->modules[$pmid] = $pm;
			}
		}
		$this->setKey($sess);

		//render
		$this->status = 200;
		$this->message = 'Success';
		$this->__json_out($data);
	}
}
