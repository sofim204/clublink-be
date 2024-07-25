<?php
class Kategori extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->lib("seme_purifier");
		$this->load("api_admin/b_kategori_model2",'bkm2');
		$this->current_parent = 'ecommerce';
		$this->current_page = 'ecommerce_kategori';
	}

	private function __imageValidation($imgkey){
		$data = array();
		//image validation
		if(!isset($_FILES[$imgkey])){
			$this->status = 102;
			$this->message = 'Image icon file are required';
			$this->__json_out($data);
			die();
		}
		if(empty($_FILES[$imgkey]['tmp_name'])){
			$this->status = 103;
			$this->message = 'Failed upload image icon';
			$this->__json_out($data);
			die();
		}
		if($_FILES[$imgkey]['size']<=0){
			$this->status = 104;
			$this->message = 'Failed upload image icon';
			$this->__json_out($data);
			die();
		}
		if($_FILES[$imgkey]['size']>100000){
			$this->status = 105;
			$this->message = 'Image icon file size too big, please try another image';
			$this->__json_out($data);
			die();
		}
		if(mime_content_type($_FILES[$imgkey]['tmp_name']) == "image/webp"){
			$this->status = 106;
			$this->message = 'WebP file format currently unsupported by this system, please try another image';
			$this->__json_out($data);
			die();
		}
		if(mime_content_type($_FILES[$imgkey]['tmp_name']) == "image/webp"){
			$this->status = 106;
			$this->message = 'WebP file format currently unsupported by this system, please try another image';
			$this->__json_out($data);
			die();
		}
		$ext = strtolower(pathinfo($_FILES[$imgkey]['name'], PATHINFO_EXTENSION));
		if (!in_array($ext, array("jpg", "png","jpeg"))) {
			$this->status = 107;
			$this->message = 'Invalid file extension, only supported PNG or JPG extension';
			$this->__json_out($data);
			die();
		}
	}

	private function __slugify($text){
	  // replace non letter or digits by -
	  $text = preg_replace('~[^\pL\d]+~u', '-', $text);
	  // transliterate
	  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
	  // remove unwanted characters
	  $text = preg_replace('~[^-\w]+~', '', $text);
	  // trim
	  $text = trim($text, '-');
	  // remove duplicate -
	  $text = preg_replace('~-+~', '-', $text);
	  // lowercase
	  $text = strtolower($text);
	  if (empty($text)) {
	    return 'n-a';
	  }
	  return $text;
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


		$sortCol = "";
		$sortDir = strtoupper($sSortDir_0);
		if(empty($sortDir)) $sortDir = "DESC";
		if(strtolower($sortDir) != "desc"){
			$sortDir = "ASC";
		}

		switch($iSortCol_0){
			// by Muhammad Sofi 11 January 2022 9:47 | add & edit input priority, show priority in datatable
			case 0:
				$sortCol = "no";
				break;
			case 1:
				$sortCol = "id";
				break;
			case 2:
				$sortCol = "image_icon";
				break;
			case 3:
				$sortCol = "nama";
				break;
			case 4:
				$sortCol = "indonesia";
				break;
			case 5:
				$sortCol = "prioritas";
				break;
			case 6:
				$sortCol = "prioritas_indonesia";
				break;
			case 7:
				$sortCol = "is_fashion";
				break;
			case 8:
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
		$dcount = $this->bkm2->countAll($nation_code,$keyword);
		$ddata = $this->bkm2->getAll($nation_code,$page,$pagesize,$sortCol,$sortDir,$keyword);

		foreach($ddata as &$gd){
			
			// by Muhammad Sofi 11 January 2022 13:33 | read special character like &#38 ;
			if(isset($gd->indonesia)) {
				$gd->indonesia = htmlspecialchars_decode($gd->indonesia);
			}

			if(isset($gd->image_icon)){
				// if(strlen($gd->image_icon)<=4) $gd->image_icon = 'media/icon/default-icon.png';
				// if($gd->image_icon == 'default.png' || $gd->image_icon== 'default.jpg') $gd->image_icon = 'media/icon/default-icon.png';
				// $gd->image_icon = base_url($gd->image_icon);
				if(strlen($gd->image_icon) > 4) {
					$gd->image_icon = '<img src="'.base_url($gd->image_icon).'" class="img-responsive" style="width: 64px;" />';
				}
			}

			if(isset($gd->is_fashion)){
				if(!empty($gd->is_fashion)){
					$gd->is_fashion = 'Yes';
				}else{
					$gd->is_fashion = 'No';
				}
			}

			if(isset($gd->is_active)){
				if(!empty($gd->is_active)){
					$gd->is_active = 'Yes';
				}else{
					$gd->is_active = 'No';
				}
			}
			if(isset($gd->is_visible)){
				if(!empty($gd->is_visible)){
					$gd->is_visible = 'Visible';
				}else{
					$gd->is_visible = 'Hidden';
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

		$di = $_POST;
		foreach($di as $key=>&$val){
			if(is_string($val)){
				if($key == 'deskripsi'){
					$val = $this->seme_purifier->richtext($val);
				}else{
					$val = $this->__f($val);
				}
			}
		}
		$di['image_icon'] = $this->media_icon.'default.png';
		if(!isset($di['indonesia'])) $di['indonesia'] = "";
		if(strlen($di['indonesia'])<=0){
			$this->status = 101;
			$this->message = 'One or more paramater required';
			$this->__json_out($data);
			die();
		}

		//image validation
		$this->__imageValidation("image_icon");

		//start transaction
		$this->bkm2->trans_start();
		//get last id
		$bkm2_id = $this->bkm2->getLastId($pengguna->nation_code);

		//build primary key
		$di['nation_code'] = $pengguna->nation_code;
		$di['id'] = $bkm2_id;
		$di['image'] = '';
		$di['image_cover'] = '';
		$di['image_icon'] = '';
		//insert into db
		$res = $this->bkm2->set($di);
		if($res){
			$this->bkm2->trans_commit();
			$this->status = 200;
			$this->message = 'Data successfully added';

			$ext = 'jpg';
			$pi = pathinfo($_FILES['image_icon']['name']);
			if(isset($pi['extension'])) $ext = $pi['extension'];
			$target_dir = $this->media_icon;
			$target_file = $this->__slugify($di['indonesia'])."$pengguna->nation_code-$bkm2_id-".date("His").".$ext";
			$filename = $target_dir.$target_file;
			if(file_exists( SENEROOT.$filename)) unlink(SENEROOT.$filename);
			$mv = move_uploaded_file($_FILES["image_icon"]["tmp_name"],  SENEROOT.$filename);
			$dux = array();
			$dux['image_icon'] = $filename;
			$res2 = $this->bkm2->update($pengguna->nation_code, $bkm2_id, $dux);
			if($res2){
				$this->status = 200;
				$this->message = 'Success';
				$this->bkm2->trans_commit();
			}else{
				$this->status = 109;
				$this->message = 'Failed updating image icon';
				$this->bkm2->trans_rollback();
				//delete file
				if(file_exists( SENEROOT.$filename)) unlink(SENEROOT.$filename);
			}
		}else{
			$this->status = 110;
			$this->message = 'Failed insertin category to database';
			$this->bkm2->trans_rollback();

		}
		$this->bkm2->trans_end();
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

		$this->status = 200;
		$this->message = 'Success';
		$data = $this->bkm2->getById($nation_code,$id);
		if(!isset($data->id)){
			$data = new stdClass();
			$this->status = 441;
			$this->message = 'No Data';
			$this->__json_out($data);
			die();
		}
		$this->__json_out($data);
	}
	public function edit($id){
		$d = $this->__init();
		$data = array();

		$id = (int) $id;
		if($id<=0){
			$this->status = 444;
			$this->message = 'Invalid Category ID';
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

		$pengguna = $d['sess']->admin;

		$du = $_POST;
		foreach($du as $key=>&$val){
			if(is_string($val)){
				if($key == 'deskripsi'){
					$val = $this->seme_purifier->richtext($val);
				}else{
					$val = $this->__f($val);
				}
			}
		}
		if(isset($du['id'])) unset($du['id']);
		if(!isset($du['indonesia'])) $du['indonesia'] = "";
		if(strlen($du['indonesia'])<=0){
			$this->status = 110;
			$this->message = 'Category name are required';
			$this->__json_out($data);
			die();
		}

		$kategori = $this->bkm2->getById($nation_code, $id);
		if(!isset($kategori->id)){
			$this->status = 111;
			$this->message = 'Wrong Category ID, please refresh this page';
			$this->__json_out($data);
			die();
		}

		$res = $this->bkm2->update($pengguna->nation_code, $id, $du);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 901;
			$this->message = 'Cant insert product to database';
		}
		$this->__json_out($data);
	}
	public function hapus($id){
		$id = (int) $id;
		$d = $this->__init();
		$data = array();
		if($id<=0){
			$this->status = 500;
			$this->message = 'Invalid ID';
			$this->__json_out($data);
			die();
		}
		if(!$this->admin_login && empty($id)){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;

		$kategori = $this->bkm2->getById($pengguna->nation_code, $id);
		if(!isset($kategori->id)){
			$this->status = 520;
			$this->message = 'ID not found or has been deleted';
			$this->__json_out($data);
			die();
		}
		$res = $this->bkm2->del($pengguna->nation_code, $id);
		if($res){
			if(strlen($kategori->image_icon)>4){
				$image_icon = SENEROOT.DIRECTORY_SEPARATOR.$kategori->image_icon;
				if(file_exists($image_icon)) unlink($image_icon);
			}
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 902;
			$this->message = 'Cant delete category product';
		}
		$this->__json_out($data);
	}
	public function change_icon($id){
		$id = (int) $id;
		$d = $this->__init();
		$data = array();

		if($id<=0){
			$this->status = 400;
			$this->message = 'Invalid ID';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}

		if(!$this->admin_login && empty($id)){
			$this->status = 400;
			$this->message = 'Access denied, please login';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		//admin
		$pengguna = $d['sess']->admin;

		//admin
		$this->status = 900;
		$this->message = 'Failed updating icon to database';

		//get current kategori
		$kategori = $this->bkm2->getById($pengguna->nation_code, $id);
		if(!isset($kategori->id)){
			$this->status = 520;
			$this->message = 'Invalid ID';
			$this->__json_out($data);
			die();
		}
		//image validation
		$this->__imageValidation("image_icon");

		// ngamaklum
		$ext = 'jpg';
		$pi = pathinfo($_FILES['image_icon']['name']);
		if(isset($pi['extension'])) $ext = $pi['extension'];
		$ext_allow = array("jpeg","jpg","png");
		if(!in_array($ext,$ext_allow)){
			$this->status = 571;
			$this->message = 'Extension not allowed, please check image file extension';
			$this->__json_out($data);
			die();
		}

		$target_dir = $this->media_icon;
		$target_file = $this->__slugify($kategori->indonesia)."-$kategori->nation_code-$kategori->id-".date("His").".$ext";
		$filename = $target_dir.$target_file;

		if(file_exists(SENEROOT.$filename)){
			unlink(SENEROOT.$filename);
			$filename = $this->__slugify($kategori->indonesia)."-$kategori->nation_code-$kategori->id-".rand(0,999).".$ext";
			$filename = $target_dir.$target_file;
			if(file_exists($filename)) unlink(SENEROOT.$filename);
		}
		$mv = move_uploaded_file($_FILES["image_icon"]["tmp_name"],  SENEROOT.$filename);
		if($mv){
			if(strlen($kategori->image_icon)>4){
				$image_icon = SENEROOT.DIRECTORY_SEPARATOR.$kategori->image_icon;
				if(file_exists($image_icon)) unlink($image_icon);
			}
			$du = array();
			$du['image_icon'] = $filename;
			$this->bkm2->update($pengguna->nation_code, $kategori->id, $du);
			$this->status = 200;
			$this->message = 'Success';
		}
		$this->__json_out($data);
	}
}
