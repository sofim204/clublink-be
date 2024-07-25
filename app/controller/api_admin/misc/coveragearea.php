<?php
class Coveragearea extends JI_Controller {
    public $is_log = 1;

    public function __construct() {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_admin/g_coverage_area_model", 'gcam');
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

		$type = $this->input->post("fltype_coverage");
		$provinsi = $this->input->post("select_provinsi");
		$kabkota = $this->input->post("select_kabkota");
		$kecamatan = $this->input->post("select_kecamatan");
		$kelurahan = $this->input->post("select_kelurahan");

        $sortCol = "";
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
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
				$sortCol = "type";
				break;	
			case 3:
				$sortCol = "provinsi";
				break;
			case 4:
				$sortCol = "kabkota";
				break;
			case 5:
				$sortCol = "kecamatan";
				break;
			case 6:
				$sortCol = "kelurahan";
				break;
			case 7:
				$sortCol = "jalan";
				break;
			case 8:
				$sortCol = "latitude";
				break;
			case 9:
				$sortCol = "longitude";
				break;
			case 10:
				$sortCol = "radius";
				break;
			case 11:
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
        $dcount = $this->gcam->countAll($keyword, $provinsi, $kabkota, $kecamatan, $kelurahan);
        $ddata = $this->gcam->getAll($page, $pagesize, $sortCol, $sortDir, $keyword, $provinsi, $kabkota, $kecamatan, $kelurahan);

		foreach($ddata as &$gd) {

			if (isset($gd->is_active)) {
                $status = "";
				if($gd->is_active==="1") $status = 'Active';
				else $status = 'Inactive';
                $gd->is_active = '<span>'.$status.'</span><br />';
            }

			if (isset($gd->latitude)) {
                $gd->latitude = number_format($gd->latitude, 6);	
            }

			if (isset($gd->longitude)) {
                $gd->longitude = number_format($gd->longitude, 6);	
            }

			if (isset($gd->radius)) {
                $gd->radius = $gd->radius. ' m';
            }

			if (isset($gd->edit_text)) {
                $gd->edit_text = '<button class="btn btn-warning btn-sm btn-action" data-id="'.$gd->id.'">Edit</button>';
            }
		}

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
		if(!isset($di['type'])) $di['type'] = "";
		if(!isset($di['jalan'])) $di['jalan'] = "";
		if(!isset($di['kelurahan'])) $di['kelurahan'] = "";
		if(!isset($di['kecamatan'])) $di['kecamatan'] = "";
		if(!isset($di['kabkota'])) $di['kabkota'] = "";
		if(!isset($di['provinsi'])) $di['provinsi'] = "";
		if(!isset($di['latitude'])) $di['latitude'] = 0;
		if(!isset($di['longitude'])) $di['longitude'] = 0;
		if(!isset($di['radius'])) $di['radius'] = 0;

		$type = $di['type'];
		$provinsi = $di['provinsi'];
		$kabkota = $di['kabkota'];
		$kecamatan = $di['kecamatan'];
		$kelurahan = $di['kelurahan'];
		$jalan = $di['jalan'];
		$latitude = $di['latitude'];
		$longitude = $di['longitude'];
		$radius = $di['radius'];

		$checkDataExistOrNot = $this->gcam->checkDataIfExist($type, $provinsi, $kabkota, $kecamatan, $kelurahan, $jalan, $latitude, $longitude, $radius);

		if (!empty($checkDataExistOrNot)) {
			$this->gcam->trans_rollback();
			$this->gcam->trans_end();
			$this->status = 1113;
			$this->message = 'Data already exist';
			$this->__json_out($data);
			die();
		} else {
			$this->gcam->trans_start();

			$lastId = (int) $this->gcam->getLastId();
			$di['id'] = $lastId;
			$res = $this->gcam->set($di);

			if (!$res) {
				$this->gcam->trans_rollback();
				$this->gcam->trans_end();
				$this->status = 1107;
				$this->message = "Error while add data, please try again later";
				$this->__json_out($data);
				die();
			}

			$this->gcam->trans_commit();
			$this->status = 200;
			$this->message = "Success";
			$this->message = 'Your data already save';

			$this->gcam->trans_end();
		}
		$this->__json_out($data);
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

		$data = $this->gcam->getById($id);
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
		if(!isset($du['type'])) $du['type'] = "";
		if(!isset($du['jalan'])) $du['jalan'] = "";
		if(!isset($du['kelurahan'])) $du['kelurahan'] = "";
		if(!isset($du['kecamatan'])) $du['kecamatan'] = "";
		if(!isset($du['kabkota'])) $du['kabkota'] = "";
		if(!isset($du['provinsi'])) $du['provinsi'] = "";
		if(!isset($du['latitude'])) $du['latitude'] = "";
		if(!isset($du['longitude'])) $du['longitude'] = "";
		if(!isset($du['radius'])) $du['radius'] = "";
		if($id > 0){
			$res = $this->gcam->update($id, $du);
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

		$res = $this->gcam->del($id);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 902;
			$this->message = 'Failed while deleting data from database';
		}
		$this->__json_out($data);
	}

	public function getProvinsi() {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;

        //standar input
        $search = $this->input->post("search");

        $ddata = $this->gcam->getProvinsiData($nation_code, $search, 1);

        $data = array();

        foreach ($ddata as $gd) {
            $data[] = array("id"=>$gd->provinsi_id, "text"=>$gd->provinsi);
        }
        echo json_encode($data);
    }

	public function getKabupatenkota() {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;

        //standar input
        $search = $this->input->post("search");
        $provinsi_id = $this->input->post("provinsi_id");

        $ddata = $this->gcam->getKabupatenkotaData($nation_code, $search, 1, $provinsi_id);

        $data = array();

        foreach ($ddata as $gd) {
            $data[] = array("id"=>$gd->kabkota, "text"=>$gd->kabkota);
        }
        echo json_encode($data);
    }

	public function getKecamatan() {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;

        //standar input
        $search = $this->input->post("search");
        $provinsi_id = $this->input->post("provinsi_id");
		$kabkota_id = $this->input->post("kabkota_id");

        $ddata = $this->gcam->getKecamatanData($nation_code, $search, 1, $provinsi_id, $kabkota_id);

        $data = array();

        foreach ($ddata as $gd) {
            $data[] = array("id"=>$gd->kecamatan, "text"=>$gd->kecamatan);
        }
        echo json_encode($data);
    }

	public function getKelurahan() {
        $data = $this->__init();
        if (!$this->admin_login) {
            redir(base_url_admin('login'));
            die();
        }
        $nation_code = $data['sess']->admin->nation_code;

        //standar input
        $search = $this->input->post("search");
        $provinsi_id = $this->input->post("provinsi_id");
		$kabkota_id = $this->input->post("kabkota_id");
		$kecamatan_id = $this->input->post("kecamatan_id");

        $ddata = $this->gcam->getKelurahanData($nation_code, $search, 1, $provinsi_id, $kabkota_id, $kecamatan_id);

        $data = array();

        foreach ($ddata as $gd) {
            $data[] = array("id"=>$gd->kelurahan, "text"=>$gd->kelurahan);
        }
        echo json_encode($data);
    }

	public function setInactive($id){
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

		$du = array();
		$du['is_active'] = 0;
		$this->gcam->update($id, $du);

		$this->status = 200;
		$this->message = 'Success';		

		$this->__json_out($data);
	}

	public function setActive($id){
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

		$du = array();
		$du['is_active'] = 1;
		$this->gcam->update($id, $du);

		$this->status = 200;
		$this->message = 'Success';		

		$this->__json_out($data);
	}

	public function setDelete($id){
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

		$this->gcam->del($id);

		$this->status = 200;
		$this->message = 'Success';		

		$this->__json_out($data);
	}
}
