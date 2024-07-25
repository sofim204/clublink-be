<?php
class Slider extends JI_Controller{
	var $media_brand = 'media/slider/';
	public function __construct(){
    parent::__construct();
		//$this->setTheme('frontx');
		$this->load("api_admin/d_slider_model",'dsm');
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

		$draw = $this->input->post("draw");
		$sval = $this->input->post("search");
		$sSearch = $this->input->request("sSearch");
		$sEcho = $this->input->post("sEcho");
		$page = $this->input->post("iDisplayStart");
		$pagesize = $this->input->request("iDisplayLength");

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
				$sortCol = "priority";
				break;
			case 2:
				$sortCol = "gambar";
				break;
			case 3:
				$sortCol = "caption";
				break;
			case 4:
				$sortCol = "utype";
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


		$this->status = 200;
		$this->message = 'Success';
		$dcount = $this->dsm->countAll($keyword);
		$ddata = $this->dsm->getAll($page,$pagesize,$sortCol,$sortDir,$keyword);

		foreach($ddata as &$gd){
			if(isset($gd->image)){
				$utype = 'external';
				if(isset($gd->utype)){
					$utype = $gd->utype;
				}
				if(strlen($gd->image)>4){
					if($utype == 'internal'){
						$gd->image = '<img src="'.base_url($gd->image).'" class="img-responsive" />';
					}else{
						$gd->image = '<img src="'.($gd->image).'" class="img-responsive" />';
					}
				}else{
					$gd->image = '<img src="'.base_url('media/brand/default.png').'" class="img-responsive" />';
				}
			}
			if(isset($gd->is_active)){
				if(!empty($gd->is_active)){
					$gd->is_active = '<span class="label label-success">Active</span>';
				}else{
					$gd->is_active = '<span class="label label-default">Not Active</span>';
				}
			}
			if(isset($gd->utype)){
				$gd->utype = 'link '.$gd->utype;
			}
		}
		//sleep(3);
		$another = array();
		$this->__jsonDataTable($ddata,$dcount);
	}
	private function __uploadFoto($temp){
		//building path target
		$fldr = $this->media_brand;
		$folder = SENEROOT.DIRECTORY_SEPARATOR.$fldr.DIRECTORY_SEPARATOR;
		$folder = str_replace('\\','/',$folder);
		$folder = str_replace('//','/',$folder);
		$ifol = realpath($folder);

		//check folder
		if(!$ifol) mkdir($folder); //create folder
		$ifol = realpath($folder); //get current realpath

		reset($_FILES);
		$temp = current($_FILES);
		if (is_uploaded_file($temp['tmp_name'])){
			if (isset($_SERVER['HTTP_ORIGIN'])) {
				// same-origin requests won't set an origin. If the origin is set, it must be valid.
				header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
			}
			header('Access-Control-Allow-Credentials: true');
			header('P3P: CP="There is no P3P policy."');

			// Sanitize input
			if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
					header("HTTP/1.0 500 Invalid file name.");
					return 0;
			}
			// Verify extension
			$ext = strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION));
			if (!in_array($ext, array("jpg", "png"))) {
					header("HTTP/1.0 500 Invalid extension.");
					return 0;
			}

			// Create magento style media directory
			$temp['name'] = md5(rand()).date('is').'.'.$ext;
			$name  = $temp['name'];
			$name1 = date("Y");
			$name2 = date("m");

			//building directory structure
			if(PHP_OS == "WINNT"){
				if(!is_dir($ifol)) mkdir($ifol);
				$ifol = $ifol.DIRECTORY_SEPARATOR.$name1.DIRECTORY_SEPARATOR;
				if(!is_dir($ifol)) mkdir($ifol);
				$ifol = $ifol.DIRECTORY_SEPARATOR.$name2.DIRECTORY_SEPARATOR;
				if(!is_dir($ifol)) mkdir($ifol);
			}else{
				if(!is_dir($ifol)) mkdir($ifol,0775);
				$ifol = $ifol.DIRECTORY_SEPARATOR.$name1.DIRECTORY_SEPARATOR;
				if(!is_dir($ifol)) mkdir($ifol,0775);
				$ifol = $ifol.DIRECTORY_SEPARATOR.$name2.DIRECTORY_SEPARATOR;
				if(!is_dir($ifol)) mkdir($ifol,0775);
			}

			// Accept upload if there was no origin, or if it is an accepted origin

			$filetowrite = $ifol . $temp['name'];

			if(file_exists($filetowrite)) unlink($filetowrite);
			move_uploaded_file($temp['tmp_name'], $filetowrite);
			if(file_exists($filetowrite)){
				$this->lib("wideimage/WideImage",'wideimage',"inc");
				WideImage::load($filetowrite)->resize(800)->crop('center', 'center', 800, 320)->saveToFile($filetowrite);
				return $fldr."/".$name1."/".$name2."/".$name;
			}else{
				return 0;
			}
		} else {
			// Notify editor that the upload failed
			//header("HTTP/1.0 500 Server Error");
			return 0;
		}
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
		$di = $_POST;
		$fi = $_FILES;
		$upImg = true;
		$dataImg = null;
		if($fi["gambar"]["size"] > 0){
			$dataImg = $this->__uploadFoto($fi);
		}else{
			$upImg = false;
		}
		if($upImg){
			$di["image"] = $dataImg;
			$res = $this->dsm->set($di);
			if($res){
				$this->status = 200;
				$this->message = 'Data successfully added';
			}else{
				$this->status = 900;
				$this->message = 'Tidak dapat menyimpan data baru, silakan coba beberapa saat lagi';
				unlink(realpath($dataImg));
			}
		}else{
			$this->status = 108;
			$this->message = 'Gambar Gagal di Upload, Cek Tipe Data Gambar';
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
		$this->status = 200;
		$this->message = 'Success';
		$data = $this->dsm->getById($id);
		$this->__json_out($data);
	}
	public function edit(){
    $d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$du = $_POST;
		$fi = $_FILES;
		$id = 0;
		if(isset($du['id'])){
			$id = (int) $du['id'];
			unset($du['id']);
		}
		if(!empty($id)){
			$resGet = $this->dsm->getById($id);
			$dataImg = $this->__uploadFoto($fi);
			if(!empty($dataImg)){
				$du["image"] = $dataImg;
				if(strlen($resGet->image)>4) unlink(realpath($resGet->image));
			}
			$res = $this->dsm->update($id,$du);
			if($res){
				$this->status = 200;
				$this->message = 'Perubahan berhasil diterapkan';
			}else{
				$this->status = 901;
				$this->message = 'Failed to make data changes';
				unlink(realpath($dataImg));
			}
		}else{
			$this->status = 444;
			$this->message = 'One or more parameter required';
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
		$resGet = $this->dsm->getById($id);
		if(!empty($resGet)){
			if(strlen($resGet->image)>4 && ($resGet->utype == 'internal')){
				unlink(realpath($resGet->image));
			}
		}
		$this->status = 200;
		$this->message = 'Success';
		$res = $this->dsm->del($id);
		if(!$res){
			$this->status = 902;
			$this->message = 'Failed while deleting data from database';
		}

		$this->__json_out($data);
	}
}
