<?php
class Update extends JI_Controller{

	public function __construct(){
    parent::__construct();
		//$this->setTheme('frontx');
		$this->load("api_admin/f_update_info_model",'fui');
		//$this->load("api_admin/d_kabkota_model",'qvm');
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


		$sortCol = "cdate";
		$sortDir = strtoupper($sSortDir_0);
		if(empty($sortDir)) $sortDir = "DESC";
		if(strtolower($sortDir) != "desc"){
			$sortDir = "ASC";
		}
		switch($iSortCol_0){
			case 0:
				$sortCol = "no";
				break;
			case 1:
				$sortCol = "id";
				break;
			case 2:
				// $sortCol = "nation_code";
				$sortCol = "cdate";
				break;
			case 3:
				$sortCol = "device";
				break;
			case 4:
				$sortCol = "version";
				break;
			case 5:
				$sortCol = "type";
				break;
			case 6:
				$sortCol = "is_active";
				break;
			default:
				$sortCol = "no";
		}

		if(empty($draw)) $draw = 0;
		if(empty($pagesize)) $pagesize=10;
		if(empty($page)) $page=0;

		$keyword = $sSearch;

		$this->status = 200;
		$this->message = 'Success';
		$dcount = $this->fui->countAll($nation_code,$keyword);
		$ddata = $this->fui->getAll($nation_code,$page,$pagesize,$sortCol,$sortDir,$keyword);

		//sleep(3);
		$another = array();
		/*var_dump($ddata); die();*/
		$this->__jsonDataTable($ddata,$dcount);
	}
	public function tambah(){
		$d = $this->__init();

		$data = array();
		if(!$this->admin_login){
			$this->status = 401;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Harus Authorization required");
			$this->__json_out($data);
			die();
		}
		$this->fui->trans_start();
		$di = $_POST;
		//var_dump($di); die();

		$explode = explode('.',$_POST['version']);
		
		$di['version_start'] = $explode[0];
		$di['version_middle'] = $explode[1];
		$di['version_end'] = $explode[2];
		$di['nation_code'] = $d['sess']->admin->nation_code;
		$di['cdate'] = date('Y-m-d H:i:s');
		$di['id'] = $this->fui->getLastId($di['nation_code']);
		$res = $this->fui->set($di);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
			$this->fui->trans_commit();
		}else{
			$this->fui->trans_rollback();
			$this->status = 900;
			$this->message = 'Cannot insert into database';
		}
		$this->fui->trans_end();
		$this->__json_out($data);
	}
	public function detail($id){
		$id = (int) $id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 401;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Harus Authorization required");
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
		$data = $this->fui->getById($nation_code,$id);
		$this->__json_out($data);
	}
	public function edit($id){
		//die('edit');
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 401;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Harus Authorization required");
			$this->__json_out($data);
			die();
		}
		$id = (int) $id;
		$du = $_POST;
		if(!isset($du['version'])) $du['version'] = "";
    $nation_code = $d['sess']->admin->nation_code;
		if($id>0 && strlen($du['device'])>0 || strlen($du['version'])>0 || strlen($du['status'])>0 || strlen($du['cdate'])>0 || strlen($du['is_active'])>0){

				$explode = explode('.',$du['version']);
		
				$du['version_start'] = $explode[0];
				$du['version_middle'] = $explode[1];
				$du['version_end'] = $explode[2];
				$res = $this->fui->update($nation_code,$id,$du);
				if($res){
					$this->status = 200;
					$this->message = 'Success';
				}else{
					$this->status = 901;
					$this->message = 'Cannot edit data to database';
				}
		}else{
			$this->status = 440;
			$this->message = 'One or more paramater are missing';
		}
		$this->__json_out($data);

	}
	public function hapus($id){
		$id = (int) $id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 401;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Harus Authorization required");
			$this->__json_out($data);
			die();
		}
    $nation_code = $d['sess']->admin->nation_code;
		$this->status = 200;
		$this->message = 'Success';
		$res = $this->fui->del($nation_code,$id);
		if(!$res){
			$this->status = 902;
			$this->message = 'Cannot deleting data from database';
		}
		$this->__json_out($data);
	}
}
