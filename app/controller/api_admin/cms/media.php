<?php
class Media extends JI_Controller{
	var $treehtml = '';
	var $module = "cms_media";
	var $page = "cms_media";

  var $status = 400;
  var $message = 'Not found';

	public function __construct(){
    parent::__construct();
		$this->lib("sene_json_engine");
		$this->load("api_admin/a_media_model",'media');
		$this->setTheme("admin/");
		$this->user_login = 0;
		$this->admin_login = 0;
	}

  public function __json_out($dt){
    $data = array();
    $data['status'] = (int) $this->status;
    $data['message'] = $this->message;
    $data['data'] = $dt;

    header('Content-Type: application/json');
    echo json_encode($data);
  }

  private function __uploadCmsMedia(){
    $fldr = 'media/upload/';
    $folder = SENEROOT.DIRECTORY_SEPARATOR.$fldr.DIRECTORY_SEPARATOR;
	  $folder = str_replace('\\','/',$folder);
    $folder = str_replace('//','/',$folder);
		$ifol = realpath($folder);
		//die($folder);
		if(!$ifol){
			mkdir($folder);
		}
		$ifol = realpath($folder);
		//die($ifol);

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
			if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("jpg", "png"))) {
					header("HTTP/1.0 500 Invalid extension.");
					return 0;
			}

			// Create magento style media directory
			$temp['name'] = str_replace(" ","-",strtolower($temp['name']));
			$name  = $temp['name'];
			//if (strlen($name)==1) $name=$name.'-';
			$name1 = date("Y");
			$name2 = date("m");
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

			$f2 = explode(".",$temp['name']);
			$f2m = count($f2);
			$filetowrite2 = "";
			if($f2m==2){
				$f2l = $f2[($f2m-2)];
				$f2a = $f2[($f2m-1)];
				$filetowrite2 = $f2l."_thumb.".$f2a;
			}else{
				$i=0;
				foreach($f2 as $f){
					if($i==($f2m-2)){
						$filetowrite2 .= $f."_thumb.";
					}else if($i==($f2m-1)){
						$filetowrite2 .= $f;
					}else{
						$filetowrite2 .= $f.".";
					}
					$i++;
				}
			}



			if(file_exists($filetowrite)) unlink($filetowrite);
			move_uploaded_file($temp['tmp_name'], $filetowrite);
			if(file_exists($filetowrite)){
				$this->lib("wideimage/WideImage",'wideimage',"inc");

				if(file_exists($ifol.$filetowrite2)) unlink($ifol.$filetowrite2);
				WideImage::load($filetowrite)->resize(370)->saveToFile($ifol.$filetowrite2);
				//WideImage::load($ifol.$filetowrite2)->crop('center', 'top', 360, 240)->saveToFile($ifol.$filetowrite2);

				//WideImage::load($filetowrite)->resize(150)->saveToFile($filetowrite);
				//WideImage::load($filetowrite)->crop('center', 'center', 150, 150)->saveToFile($filetowrite);
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

	public function index($utype="kaskecil"){
    $this->status = 400;
    $this->message = 'Login';
    $s = $this->__init();

    $data = array();
    if($this->admin_login){
			$this->status = 100;
	    $this->message = 'Berhasil';
      $folder = $this->input->get('folder');
			if(empty($folder)) $folder = '';
			$folder = trim($folder,'/');
      $folder = '/'.$folder;

			$root = new stdClass();
			$root->folder = '/';
			$data['folders'] = $this->media->getFolder();
			if(empty($data['folders'])){
				$data['folders'] = array();
				$data['folders'][] = $root;
			}
      $files = $this->media->getByFolder($folder);
			foreach($files as &$f){
				$fe = explode('/',$f->nama);
				$f->filename = end($fe);
				$fd = $f->filename;
				$fn = basename($f->filename);
				$fe = explode('.', $f->filename);
				$ext = end($fe);
				$fn = str_replace('.'.$ext,'',$fd);
				$f->filethumb = $fn.'_thumb.'.$ext;
				$f->thumb = rtrim($f->nama,$f->filename).$f->filethumb;

				$f->tgl = date("l, j F Y",strtotime($f->cdate));
			}
			$data['files'] = $files;
    }
    $this->__json_out($data);
	}
	public function add(){
    $this->status = 400;
    $this->message = 'Login';
    $s = $this->__init();
		//$this->debug($s);
		//die();
    $data = new stdClass();
    if($this->admin_login){
      $filename = $this->__uploadCmsMedia();
      if($filename){
        $folder = $this->input->post('folder');
				if(empty($folder)) $folder = '';
				$folder = ltrim($folder,'/');
        $folder = '/'.$folder;

        $di = array();
        $di['b_user_id'] = $s['sess']->admin->id;
        $di['folder'] = $folder;
        $di['nama'] = str_replace('//','/',$filename);
        $di['cdate'] = 'NOW()';
        $di['is_active'] = 1;
        $res = $this->media->set($di);
        if($res){
          $this->status = 100;
          $this->message = 'Berhasil';
        }else{
          $this->message = 'Gagal insert ke database';
          $this->status = 101;
        }
      }else{
        $this->message = 'Gagal upload';
        $this->status = 102;
      }
    }
    $this->__json_out($data);
	}
	public function move(){
    $id = (int) $this->input->post('id');
    $folder = $this->input->post('folder');
		$folder = rtrim($folder,'/');
    $this->status = 400;
    $this->message = 'Login';
    $s = $this->__init();
    $data = new stdClass();
    if($this->admin_login && ($id)>0){
      $m = $this->media->getById($id);
      if(isset($m->id)){
        if(empty($folder)) $folder = '';
				$folder = '/'.$folder;
        $du = array();
        $du['folder'] = $folder;
        $res = $this->media->update($id,$du);
        if($res){
          $this->status = 100;
          $this->message = 'Berhasil';
        }else{
          $this->message = 'Gagal insert ke database';
          $this->status = 101;
        }
      }else{
        $this->message = 'Gagal upload';
        $this->status = 102;
      }
    }
    $this->__json_out($data);
	}
  public function del($id=""){
    $id = (int) $id;
    $this->status = 400;
    $this->message = 'Login';
    $s = $this->__init();
    $data = new stdClass();
    if($this->admin_login && ($id)>0){
      $m = $this->media->getById($id);
      if(isset($m->id)){
				$flnm  = $m->nama;
        $fldr  = SENEROOT.DIRECTORY_SEPARATOR.$flnm;
        if(file_exists($fldr)) unlink($fldr);

				$fe = explode('/',$m->nama);
				$filename = end($fe);
				$fd = $filename;
				$fn = basename($filename);
				$fe = explode('.', $filename);
				$ext = end($fe);
				$fn = str_replace('.'.$ext,'',$fd);
				$filethumb = $fn.'_thumb.'.$ext;
				$thumb = rtrim($m->nama,$filename).$filethumb;

				if(file_exists($thumb)) unlink($thumb);

        if(empty($folder)) $folder = '/';

        $res = $this->media->del($id);
        if($res){
          $this->status = 100;
          $this->message = 'Berhasil';
        }else{
          $this->message = 'Gagal hapus database';
          $this->status = 101;
        }
      }else{
        $this->message = 'media tidak ditemukan';
        $this->status = 102;
      }
    }
    $this->__json_out($data);
  }
	public function regenerate($utype="crop",$width="",$height=""){
    $this->status = 400;
    $this->message = 'Login';
    $s = $this->__init();
		$data = array();
		$data['jumlah'] = 0;
		$data['berhasil'] = 0;
		$data['gagal'] = 0;

		if($this->admin_login){
			$this->status = 100;
			$this->message = 'Berhasil';
			$media = $this->media->get();
			$data['jumlah'] = count($media);
			//$this->debug($media);
			//die();
			$this->lib("wideimage/WideImage",'wideimage',"inc");
			if(empty($width)) $width = 370;
			if(empty($height)) $height = 240;
			foreach($media as $med){
				$media_file = SENEROOT.DIRECTORY_SEPARATOR.$med->nama;
				$media_file = str_replace('\\','/',$media_file);
				$media_file = str_replace('//','/',$media_file);
				if(file_exists($media_file)){
					$ext = strtolower(pathinfo($media_file, PATHINFO_EXTENSION));
					$media_file_thumb = substr($media_file,0,strlen($media_file)-(strlen($ext)+1)).'_thumb.'.$ext;
					$media_file_crop = substr($media_file,0,strlen($media_file)-(strlen($ext)+1)).'_crop.'.$ext;

					if(file_exists($media_file_thumb)) unlink($media_file_thumb);
					WideImage::load($media_file)->resize($width)->saveToFile($media_file_thumb,90);
					if(strtolower($utype)=='crop'){
						if(file_exists($media_file_crop)) unlink($media_file_crop);
						if(false){
							// Load the original image
							$original = WideImage::load($media_file_thumb);
							$resized  = $original->resizeDown($width, null); // Do whatever resize or crop you need to do
							$original->destroy(); // free some memory (original image not needed any more)

							// Create an empty canvas with the resized image sizes
							$img = WideImage::createTrueColorImage($width, $height);
							$bg  = $img->allocateColor(255,255,255);
							$img->fill(0,0,$bg);

							// Finally merge and do whatever you need...
							$img->merge($resized)->saveToFile($media_file_crop);
							$resized->destroy();
							$img->destroy();
						}else{
							WideImage::load($media_file_thumb)->crop('center', 'top', $width, $height)->saveToFile($media_file_crop,90);
						}
					}
					$data['berhasil']++;
				}else{
					$data['gagal']++;
				}
			}
		}else{
		}
		$this->__json_out($data);
	}
}
