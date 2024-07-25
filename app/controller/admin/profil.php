<?php
class Profil extends JI_Controller{
	var $media_pengguna = 'media/pengguna/';

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'dashboard';
		$this->current_page = 'dashboard';
		$this->load('admin/a_pengguna_model','apm');
	}

  private function __uploadFoto($admin_id){
		//building path target
    $fldr = $this->media_pengguna;
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

			if($temp["size"]>500000){
				header("HTTP/1.0 500 Image file cant be more than 50 hundred bytes.");
				return 0;
			}

			if(mime_content_type($temp['tmp_name']) == 'image/webp'){
				header("HTTP/1.0 500 WebP currently unsupported.");
				return 0;
			}
			// Verify extension
			$ext = strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION));
			if (!in_array($ext, array("jpg", "png"))) {
					header("HTTP/1.0 500 Invalid extension.");
					return 0;
			}

			// Create magento style media directory
			$temp['name'] = md5($admin_id).date('is').'.'.$ext;
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
				WideImage::load($filetowrite)->resize(320)->saveToFile($filetowrite);
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
	public function index(){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}
		//$this->debug($data);
		//die();
		$this->putThemeContent("profil/home_modal",$data);
		$this->putThemeContent("profil/home",$data);

		$this->putJsReady("profil/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
	public function edit_foto(){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}
		$admin_id = $data['sess']->admin->id;

		$data['notif'] = 'Error: Failed changing profile picture, maybe format unsupported or image file size too big';
		$foto = $this->__uploadFoto($admin_id); //handling file upload
		if(strlen($foto)>3){
			//delete existing foto
			$admin_foto = $data['sess']->admin->foto;
			if(strlen($admin_foto)>3){
				$admin_foto = str_replace('//','/',$admin_foto);
				$admin_foto_path = SENEROOT.DIRECTORY_SEPARATOR.$admin_foto;
				if(file_exists($admin_foto_path)) unlink($admin_foto_path);
			}

			//set to current session
			$data['sess']->admin->foto = '';
			$this->setKey($data['sess']);

			//replace double slash
			$foto = str_replace('//','/',$foto);

			//update new foto to database;
			$du = array('foto'=>$foto);
			$res = $this->apm->update($admin_id,$du);
			if($res){
				$data['sess']->admin->foto = $foto;
				$this->setKey($data['sess']);
				$data['notif'] = 'Success';
			}
		}

		$this->putThemeContent("profil/home_modal",$data);
		$this->putThemeContent("profil/home",$data);

		$this->putJsReady("profil/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
