<?php
class Kondisi extends JI_Controller{

	public function __construct(){
    parent::__construct();
		//$this->setTheme('frontx');
		$this->load("api_admin/b_kondisi_model",'bkonm');
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
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

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
				$sortCol = "nama";
				break;
			case 2:
				$sortCol = "nilai";
				break;
			case 3:
				$sortCol = "prioritas";
				break;
			case 4:
				$sortCol = "is_active";
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
		$dcount = $this->bkonm->countAll($nation_code, $keyword);
		$ddata = $this->bkonm->getAll($nation_code, $page,$pagesize,$sortCol,$sortDir,$keyword);

		foreach($ddata as &$gd){
			if(isset($gd->icon)){
				if(strlen($gd->icon)<=6) $gd->icon = 'media/icon/default-icon.png';
        $gd->icon = base_url($gd->icon);
        $gd->icon = '<img src="'.$gd->icon.'" class="img-responsive" style="max-width: 64px;" />';
      }
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
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

    //$this->debug($d);
		$di = $_POST;
		if(!isset($di['nama'])) $di['nama'] = "";
		if(!isset($di['nilai'])) $di['nilai'] = "";
		if(strlen($di['nama'])>0 && strlen($di['nilai'])>0){
			$this->bkonm->trans_start();
			$kondisi_id = (int) $this->bkonm->getLastId($nation_code);
			if($kondisi_id<=0) $kondisi_id=1;
	    $di['nation_code'] = $nation_code;
			$di['id'] = $kondisi_id;
	    $di['icon'] = "";
      $res = $this->bkonm->set($di);
			if($res){
				$this->bkonm->trans_commit();
				$this->status = 200;
				$this->message = 'Data successfully added';
				//update image icon if exists
				if(isset($_FILES['icon'])){
					$ext = 'jpg';
					$pi = pathinfo($_FILES['icon']['name']);
					if(isset($pi['extension'])) $ext = $pi['extension'];
					$target_dir = $this->media_icon;
					$target_file = "kondisi-$nation_code-$kondisi_id.$ext";
					$filename = $target_dir.$target_file;
					if(file_exists( SENEROOT.$filename)) unlink(SENEROOT.$filename);
					$mv = move_uploaded_file($_FILES["icon"]["tmp_name"],  SENEROOT.$filename);
					if($mv){
						$du = array();
						$du['icon'] = $filename;
						$res = $this->bkonm->update($nation_code, $kondisi_id, $du);
						if($res){
							$this->bkonm->trans_commit();
						}
					}
				}
			}else{
				$this->bkonm->trans_rollback();
				$this->status = 900;
				$this->message = 'Tidak dapat menyimpan data baru, silakan coba beberapa saat lagi';
			}
			$this->bkonm->trans_end();
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
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$data = $this->bkonm->getById($nation_code, $id);
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
	public function edit($id){
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
		$pengguna = $d['sess']->admin;
    $nation_code = $pengguna->nation_code;

		$this->status = 800;
		$this->message = 'One or more parameter are required';
		$du = $_POST;
		if(isset($du['id'])) unset($du['id']);
		if(!isset($du['nama'])) $du['nama'] = "";
		if(!isset($du['nilai'])) $di['nilai'] = "";
		if($id>0 && strlen($du['nama'])>0 && strlen($du['nilai'])>0){
			$res = $this->bkonm->update($nation_code,$id,$du);
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
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

    $kondisi = $this->bkonm->getById($nation_code, $id);
    if(isset($kondisi->id)){
      $res = $this->bkonm->del($nation_code,$id);
  		if($res){
				$this->status = 200;
				$this->message = 'Success';
			}else{
  			$this->status = 902;
  			$this->message = 'Failed while deleting data from database';

        //hapus icon
        if(strlen($kondisi->icon)>6){
          if(file_exists(SENEROOT.$kondisi->icon)){
            unlink(SENEROOT.$kondisi->icon);
          }
        }
  		}
    }else{
			$this->status = 1040;
			$this->message = 'Invalid ID or data has been deleted';
		}

		$this->__json_out($data);
	}
	public function change_icon($id){
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
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$this->status = 900;
		$this->message = 'Gagal';

		$kondisi = $this->bkonm->getById($nation_code, $id);
		if(!isset($kondisi->id)){
			$this->status = 520;
			$this->message = 'Invalid kategori ID';
			$this->__json_out($data);
			die();
		}

		if(isset($_FILES['icon'])){
			if($_FILES['icon']['size']>100000){
				$this->status = 1030;
				$this->message = 'Image file size too big, please try another image';
				$this->__json_out($data);
				die();
			}else if($_FILES['icon']['size']>0 && $_FILES['icon']['size']<=100000){
				if(mime_content_type($_FILES['icon']['tmp_name'])=='image/webp'){
					$this->status = 1031;
					$this->message = 'WebP image format currently unsupported on this system, please try another image';
					$this->__json_out($data);
					die();
				}
			}
			$ext = 'jpg';
			$pi = pathinfo($_FILES['icon']['name']);
			if(isset($pi['extension'])) $ext = $pi['extension'];
			if(!in_array($ext,array("jpg","png","jpeg"))){
				$this->status = 1032;
				$this->message = 'Invalid file extension, please try another image';
				$this->__json_out($data);
				die();
			}

			$target_dir = $this->media_icon;
			$target_file = "kondisi-$nation_code-$kondisi->id.$ext";
			$filename = $target_dir.$target_file;

			if(file_exists(SENEROOT.$filename)){
				unlink(SENEROOT.$filename);
				$target_file = "kondisi-$nation_code-$kondisi->id-".rand(0,999).".$ext";
				$filename = $target_dir.$target_file;
				if(file_exists($filename)) unlink(SENEROOT.$filename);
			}
			$mv = move_uploaded_file($_FILES["icon"]["tmp_name"],  SENEROOT.$filename);
			if($mv){
				$du = array();
				$du['icon'] = $filename;
				$res = $this->bkonm->update($nation_code, $kondisi->id,$du);
				if($res){
					$this->status = 200;
					$this->message = 'Success';
					if(strlen($kondisi->icon)>4){
						if(file_exists(SENEROOT.DIRECTORY_SEPARATOR.$kondisi->icon)) unlink(SENEROOT.DIRECTORY_SEPARATOR.$kondisi->icon);
					}
				}else{
					$this->status = 1034;
					$this->message = 'Failed updating to database';
				}
			}
		}
		$this->__json_out($data);
	}
}
