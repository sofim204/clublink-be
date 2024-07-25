<?php
class Campaign extends JI_Controller{
	var $media_brand = 'media/promo';
	public function __construct(){
    parent::__construct();
		//$this->setTheme('frontx');
		$this->load("api_admin/c_promo_model",'cpm');
	}

	public function index(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
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
				$sortCol = "judul";
				break;
			case 3:
				$sortCol = "gambar";
				break;
			case 4:
				$sortCol = "utype";
				break;
			case 5:
				$sortCol = "edate";
				break;
			case 6:
				$sortCol = "is_active";
				break;
			default:
				$sortCol = "id";
		}

		if(empty($draw)) $draw = 0;
		if(empty($pagesize)) $pagesize=10;
		if(empty($page)) $page=0;

		$keyword = $sSearch;


		$this->status = '100';
		$this->message = 'Berhasil';
		$dcount = $this->cpm->countAll($keyword);
		$ddata = $this->cpm->getAll($page,$pagesize,$sortCol,$sortDir,$keyword);

		foreach($ddata as &$gd){
			if(isset($gd->utype)){
				$gd->utype = 'link '.$gd->utype;
			}
			if(isset($gd->gambar)){
				if(strlen($gd->gambar)>4){
					$gd->gambar = '<img src="'.base_url($gd->gambar).'" class="img-responsive" />';
				}else{
					$gd->gambar = '<img src="'.base_url('media/brand/default.png').'" class="img-responsive" />';
				}
			}
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
		$this->__jsonDataTable($ddata,$dcount);
	}
	private function __uploadFoto($temp,$id=""){
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
		$temp = current($temp);
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
			if (!in_array($ext, array("jpg", "png","jpeg"))) {
					header("HTTP/1.0 500 Invalid extension.");
					return 0;
			}
			if($ext == 'jpeg') $ext = "jpg";

			// Create magento style media directory
			$temp['name'] = md5(rand()).date('is').'.'.$ext;

			$name  = $temp['name'];
			$id = (int) $id;
			if($id>0) $name = $id.'.'.$ext;
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
			$filetowrite = $ifol . $name;
			$filetowrite = str_replace('//','/',$filetowrite);

			if(file_exists($filetowrite)){
				unlink($filetowrite);
				$name = '';
				$rand = substr(md5(microtime()),rand(0,26),5);
				$name = 'promo-'.$rand.'.'.$ext;
				if($id>0) $name = $id.'-'.$rand.'.'.$ext;
				$filetowrite = $ifol.$name;
				$filetowrite = str_replace('//','/',$filetowrite);
				if(file_exists($filetowrite)) unlink($filetowrite);
			}
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
			$this->message = 'Unauthorized access, please login';
			header("HTTP/1.0 400 Unauthorized access, please login");
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
		if($fi["gambar"]["size"] > 2000000){
			$upImg = false;
			$this->status = 118;
			$this->message = 'Image file size too big, please try another image';
			$this->__json_out($data);
			die();
		}
		if($upImg){
			if(!isset($di['judul'])) $di['judul'] = "";
			if(strlen($di['judul'])>0){
				$di["gambar"] = $dataImg;
				$res = $this->cpm->set($di);
				if($res){
					$this->status = 100;
					$this->message = 'Success';
				}else{
					$this->status = 900;
					$this->message = 'Cant add promotion data to database, please try again later';
					unlink(realpath($dataImg));
				}
			}else{
				$this->status = 109;
				$this->message = 'Title is required, please check again';
			}
		}else{
			$this->status = 108;
			$this->message = 'Failed upload image, please try again or resize the current image';
		}
		$this->__json_out($data);
	}
	public function detail($id){
		$id = (int) $id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($id)){
			$this->status = 400;
			$this->message = 'Unauthorized access, please login';
			header("HTTP/1.0 400 Unauthorized access, please login");
			$this->__json_out($data);
			die();
		}
		$this->status = 100;
		$this->message = 'Berhasil';
		$data = $this->cpm->getById($id);
		$this->__json_out($data);
	}
	public function edit($id){
		$id = (int) $id;
    $d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access, please login';
			header("HTTP/1.0 400 Unauthorized access, please login");
			$this->__json_out($data);
			die();
		}
		$du = $_POST;
		$fi = $_FILES;
		if(isset($du['id'])) unset($du['id']);
		if(!isset($du['judul'])) $du['judul'] = "";
		if(!empty($id) && strlen($du['judul'])>0){
			$dataImg = $this->__uploadFoto($fi);
			if(!empty($dataImg)){
				$du["gambar"] = $dataImg;
				$resGet = $this->cpm->getById($id);
				if(strlen($resGet->gambar)>4) unlink(realpath($resGet->gambar));
			}
			$res = $this->cpm->update($id,$du);
			if($res){
				$this->status = 100;
				$this->message = 'Perubahan berhasil diterapkan';
			}else{
				$this->status = 901;
				$this->message = 'Tidak dapat melakukan perubahan ke basis data';
				unlink(realpath($dataImg));
			}
		}else{
			$this->status = 444;
			$this->message = 'One or more parameter required';
			unlink(realpath($dataImg));
		}
		$this->__json_out($data);
	}
	public function hapus($id){
		$id = (int) $id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access, please login';
			header("HTTP/1.0 400 Unauthorized access, please login");
			$this->__json_out($data);
			die();
		}
		if($id<=0){
			$this->status = 450;
			$this->message = 'Invalid ID';
			$this->__json_out($data);
			die();
		}
		$promo = $this->cpm->getById($id);
		if(isset($promo->id)){
			if(strlen($promo->gambar)>4){
				if(file_exists(SENEROOT.$promo->gambar)){
					unlink(SENEROOT.$promo->gambar);
				}
			}
			$res = $this->cpm->del($id);
			if($res){
				$this->status = 100;
				$this->message = 'Success';
			}else{
				$this->status = 902;
				$this->message = 'Failed removing data from database, please try again later';
			}
		}else{
			$this->status = 441;
			$this->message = 'Data with supplied ID not found or already deleted';
		}
		$this->__json_out($data);
	}
}
