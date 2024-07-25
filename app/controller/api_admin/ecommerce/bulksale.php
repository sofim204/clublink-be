<?php
class BulkSale extends JI_Controller{

	public function __construct(){
    parent::__construct();
		//$this->setTheme('frontx');
		$this->lib("seme_purifier");
		$this->load("api_admin/a_negara_model",'anm');
		$this->load("api_admin/c_bulksale_model",'cbsm');
		$this->load("api_admin/c_bulksale_foto_model",'cbsfm');
		$this->current_parent = 'ecommerce';
		$this->current_page = 'ecommerce_bulksale';
	}
	private function thumbParser($imgname){
		$imgnames = explode('.',$imgname);
		$imgname_last = $imgnames[count($imgnames)-1];
		return rtrim($imgname,'.'.$imgname_last).'_thumb.'.$imgname_last;
	}
	private function slugify($text){
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
		return $text;
	}
	public function index(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Access Denied';
			header("HTTP/1.0 400 Access Denied");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;
		$negara = $this->anm->getByNationCode($nation_code);
		if(!isset($negara->simbol_mata_uang)) $negara->simbol_mata_uang = '-';

		//get table alias
		$tbl_as = $this->cbsm->getTableAlias();
		$tbl2_as = $this->cbsm->getTableAlias2();

		//standard input
		$draw = $this->input->post("draw");
		$sval = $this->input->post("search");
		$keyword = $this->input->post("sSearch");
		$sEcho = $this->input->post("sEcho");
		$page = $this->input->post("iDisplayStart");
		$pagesize = $this->input->post("iDisplayLength");
		$iSortCol_0 = $this->input->post("iSortCol_0");
		$sSortDir_0 = $this->input->post("sSortDir_0");

		//standard input validation
		$sortCol = "date";
		$sortDir = strtoupper($sSortDir_0);
		if(empty($sortDir)) $sortDir = "DESC";
		if(strtolower($sortDir) != "desc") $sortDir = "ASC";
		switch($iSortCol_0){
			case 0:
				$sortCol = "id";
				break;
			case 1:
				$sortCol = "cdate";
				break;
			case 2:
				$sortCol = "company_name";
				break;
			case 3:
				$sortCol = "description_long";
				break;
			case 4:
				$sortCol = "address1";
				break;
			case 5:
				$sortCol = "action_status";
				break;
			case 6:
				$sortCol = "vdate";
				break;
			case 7:
				$sortCol = "price";
				break;
			default:
				$sortCol = "id";
		}
		if(empty($draw)) $draw = 0;
		if(empty($pagesize)) $pagesize=10;
		if(empty($page)) $page=0;

		//custom input
		$action_status = $this->input->post("action_status");
		$is_agent = $this->input->post("is_agent");
		$scdate = $this->input->post("scdate");
		$ecdate = $this->input->post("ecdate");
		$svdate = $this->input->post("svdate");
		$evdate = $this->input->post("evdate");

		//custom validation
		if($is_agent != "") $is_agent = (int) $is_agent;
		if(strlen($scdate)==10){
			$scdate = date("Y-m-d",strtotime($scdate));
		}else{
			$scdate = "";
		}
		if(strlen($ecdate)==10){
			$ecdate = date("Y-m-d",strtotime($ecdate));
		}else{
			$ecdate = "";
		}
		if(strlen($svdate)==10){
			$svdate = date("Y-m-d",strtotime($svdate));
		}else{
			$svdate = "";
		}
		if(strlen($evdate)==10){
			$evdate = date("Y-m-d",strtotime($evdate));
		}else{
			$evdate = "";
		}


		$this->status = 200;
		$this->message = 'Success';
		$dcount = $this->cbsm->countAll($nation_code,$keyword,$action_status,$is_agent,$scdate,$ecdate,$svdate,$evdate);
		$ddata = $this->cbsm->getAll($nation_code,$page,$pagesize,$sortCol,$sortDir,$keyword,$action_status,$is_agent,$scdate,$ecdate,$svdate,$evdate);

		foreach($ddata as &$gd){
			if(isset($gd->cdate)){
				$gd->cdate = date("d/M/y",strtotime($gd->cdate));
			}
			if(isset($gd->thumb)){
				if(strlen($gd->thumb)<=10) $gd->thumb = 'media/produk/default.png';
				$gd->thumb = '<img src="'.base_url($gd->thumb).'" class="img-responsive" style="max-width: 128px;" />';
			}
			if(isset($gd->description_long)){
				$gd->description_long = strip_tags($gd->description_long);
				if(strlen($gd->description_long)>60){
					$gd->description_long = substr($gd->description_long,0,60).'...';
				}
			}
			if(isset($gd->agent_name) && isset($gd->company_name)){
				if(strlen(trim($gd->company_name))){
					$gd->agent_status = $gd->company_name.' - '.$gd->agent_name;
				}else{
					$gd->agent_status = "Guest";
				}
			}
		}
		$this->__jsonDataTable($ddata,$dcount);
	}
	public function check(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Access Denied';
			header("HTTP/1.0 400 Access Denied");
			$this->__json_out($data);
			die();
		}
		$kolom = $this->input->request('kolom');
		$nilai = $this->input->request('nilai');

		if(strlen($kolom)>1 && strlen($nilai)>1){
			$res = $this->cbsm->check($kolom,$nilai);
			if($res){
				$this->status = 443;
				$this->message = 'Sudah Digunakan';
			}else{
				$this->status = 442;
				$this->message = 'Belum Digunakan';
			}
		}else{
			$this->status = 444;
			$this->message = 'Parameter kurang';
		}
		$this->__json_out($data);
	}
	public function tambah(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Access Denied';
			header("HTTP/1.0 400 Access Denied");
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
		if(!isset($di['nama'])) $di['nama'] = "";
		if(isset($di['image'])) unset($di['image']);
		if(isset($di['caption'])) unset($di['caption']);
		if(isset($di['produk_items'])) unset($di['produk_items']);
		if(strlen($di['nama'])>1 && strlen($di['sku'])>1){
			$check = $this->cbsm->check('sku',$di['sku']); //1 = sudah digunakan
			if(empty($check)){
				$nama = $di['nama'];
				$sku = $di['sku'];
				$slug = $nama.'-'.$sku;
				if(isset($di['slug'])) if(!empty($di['slug'])) $slug = $di['slug'];
				$slug = $this->slugify($slug);
				$slug_check = $this->cbsm->checkSlug($slug);
				$try =0;
				while(($slug_check > 0) && ( $try <= 5)){
					$slug .= $slug.'-'.rand(0,999);
					$slug_check = $this->cbsm->checkSlug($slug);
					$try++;
				}
				$di['slug'] = $slug;

				if(isset($di['foto'])){
					if(strlen($di['foto'])>3){
						$di['thumb'] = $this->thumbParser($di['thumb']);
					}
				}
				$di['sku'] = strtoupper($di['sku']);
				//$this->debug($di);
				//die();
				$res = $this->cbsm->set($di);
				if($res){
					//foto dan caption
					$fotos = array();
					$dgi = array(); //for produk fotos
					$dgc = array(); //for produk fotos caption
					if(is_array($this->input->post('image'))) $dgi = $this->input->post('image');
					if(is_array($this->input->post('caption'))) $dgc = $this->input->post('image');$i=0;

					$i=0;
					foreach($dgi as $it){
						$gi = array();
						$gi['c_produk_id'] = $res;
						$gi['url'] = str_replace('//','/',$it);
						$gi['url_thumb'] = $this->thumbParser(str_replace('//','/',$it));
						$gi['caption'] = '';
						$fotos[$i] = $gi;
						$i++;
					}

					$i=0;
					foreach($dgc as $it){
						if(isset($fotos[$i]['caption'])){
							$fotos[$i]['caption'] = $it;
							$i++;
						}
					}
					if(count($fotos)) $res2 = $this->cbsfm->setMass($fotos);

					//set default thumb & img
					if(isset($fotos[0]['url'])){
						$dx = array();
						$dx['foto'] = $fotos[0]['url'];
						$dx['thumb'] = $fotos[0]['url_thumb'];
						$this->cbsm->update($res,$dx);
					}

					//bundling produk
					$this->status = 200;
					$this->message = 'Data successfully added';
				}else{
					$this->status = 900;
					$this->message = 'Tidak dapat menyimpan data baru, silakan coba beberapa saat lagi';
				}
			}else{
				$this->status = 104;
				$this->message = 'Code already used, please try another code';
			}
		}
		$this->__json_out($data);
	}
	public function detail($id){
		$id = (int) $id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($id)){
			$this->status = 400;
			$this->message = 'Access Denied';
			header("HTTP/1.0 400 Access Denied");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$this->status = 200;
		$this->message = 'Success';
		$data = $this->cbsm->getById($nation_code, $id);
		$this->__json_out($data);
	}
	public function edit($id=""){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Access Denied';
			header("HTTP/1.0 400 Access Denied");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		//populate post
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
		$id = (int) $id;
		if(empty($id)){
			if(isset($du['id'])){
				$id = (int) $du['id'];
			}
			unset($du['id']);
		}

		if(!isset($du['nama'])) $di['nama'] = "";
		if(isset($du['image'])) unset($du['image']);
		if(isset($du['caption'])) unset($du['caption']);
		if(isset($du['produk_items'])) unset($du['produk_items']);
		if($id>0 && strlen($du['nama'])>0){
			//$check = $this->cbsm->checkSku($du['sku'],$id); //1 = sudah digunakan
			$check = 0;
			if(empty($check)){
				//echo json_encode($du);
				//die();
				$res = $this->cbsm->update($nation_code,$id,$du);
				if($res){
					//foto dan caption
					$fotos = array();
					$dgi = array(); //for produk fotos
					$dgc = array(); //for produk fotos caption
					if(is_array($this->input->post('image'))) $dgi = $this->input->post('image');
					if(is_array($this->input->post('caption'))) $dgc = $this->input->post('image');$i=0;

					$i=0;
					foreach($dgi as $it){
						$gi = array();
						$gi['c_produk_id'] = $id;
						$gi['url'] = str_replace('//','/',$it);
						$gi['url_thumb'] = $this->thumbParser(str_replace('//','/',$it));
						$gi['caption'] = '';
						$fotos[$i] = $gi;
						$i++;
					}

					$i=0;
					foreach($dgc as $it){
						if(isset($fotos[$i]['caption'])){
							$fotos[$i]['caption'] = $it;
							$i++;
						}
					}
					if(is_array($fotos) && count($fotos)){
						$this->cbsfm->delByProdukId($id);
						$res2 = $this->cbsfm->setMass($fotos);
					}

					//set default thumb & img
					if(isset($fotos[0]['url'])){
						$dx = array();
						$dx['foto'] = $fotos[0]['url'];
						$dx['thumb'] = $fotos[0]['url_thumb'];
						$this->cbsm->update($nation_code,$id,$dx);
					}

					$this->status = 200;
					$this->message = 'Perubahan berhasil diterapkan';
				}else{
					$this->status = 901;
					$this->message = 'Failed to make data changes';
				}
			}else{
				$this->status = 104;
				$this->message = 'Code already used, please try another code';
			}
		}
		$this->__json_out($data);
	}
	public function hapus($id){
		$id = (int) $id;
		$d = $this->__init();
		$data = array();
		if($id<=0){
			$this->status = 500;
			$this->message = 'ID tidak valid';
			$this->__json_out($data);
			die();
		}
		if(!$this->admin_login && empty($id)){
			$this->status = 400;
			$this->message = 'Access Denied';
			header("HTTP/1.0 400 Access Denied");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;
		$this->status = 200;
		$this->message = 'Success';
		$res = $this->cbsm->del($nation_code, $id);
		if(!$res){
			$this->status = 902;
			$this->message = 'Failed while deleting data from database';
		}
		$this->__json_out($data);
	}
	public function image($id){
		$id = (int) $id;
		$d = $this->__init();
		$data = array();
		$data['images'] = array();
		if(!$this->admin_login && empty($id)){
			$this->status = 400;
			$this->message = 'Access Denied';
			header("HTTP/1.0 400 Access Denied");
			$this->__json_out($data);
			die();
		}
    $pengguna = $d['sess']->admin;
    $nation_code = $pengguna->nation_code;

		//get from db
		$images = $this->cbsfm->getByProdukId($nation_code, $id);
		if(is_array($images)){
			$this->status = 200;
			$this->message = 'Success';
			foreach($images as &$im){
				if($im->utype == 'internal'){
					$im->url = ($im->url);
					$im->url_thumb = ($im->url_thumb);
				}
			}
			$data['images'] = $images;
		}
		$this->__json_out($data);
	}
	public function gambar_upload($c_bulksale_id){
		$c_bulksale_id = (int) $c_bulksale_id;
		$d = $this->__init();
		$data = array();
		$data['images'] = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Access Denied';
			header("HTTP/1.0 400 Access Denied");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		if($c_bulksale_id<=0){
			$this->status = 700;
			$this->message = 'Invalid BulkSale ID';
			$this->__json_out($data);
			die();
		}
		$bulksale = $this->cbsm->getById($nation_code, $c_bulksale_id);
		if(!isset($bulksale->id)){
			$this->status = 451;
			$this->message = 'BulkSale not found or has been deleted';
			$this->__json_out($data);
			die();
		}

		if(isset($_FILES['image'])){
			$ext = 'jpg';
			$pi = pathinfo($_FILES['image']['name']);
			if(isset($pi['extension'])) $ext = strtolower($pi['extension']);
			$exts = array("jpg","jpeg","png");
			if(in_array($ext,$exts)){
				if($_FILES['image']['size']>500000){
					$this->status = 1959;
					$this->message = 'Image file size too big, please try another image';
					$this->__json_out($data);
					die();
				}
				if($_FILES['image']['size']>0 && $_FILES['image']['size']<=500000){
					if(mime_content_type($_FILES['image']['tmp_name']) == 'image/webp'){
						$this->status = 1958;
						$this->message = 'WebP image format currently unsupported on this system';
						$this->__json_out($data);
						die();
					}
				}
				$target_dir = $this->media_produk;
				$ifol = SENEROOT.DIRECTORY_SEPARATOR.$target_dir;
				if(!is_dir($ifol)){
					if(PHP_OS == "WINNT"){
						if(!is_dir($ifol)) mkdir($ifol);
					}else{
						if(!is_dir($ifol)) mkdir($ifol,0775,true);
					}
				}
				$target_dir = $target_dir.DIRECTORY_SEPARATOR.date("Y");
				$ifol = SENEROOT.DIRECTORY_SEPARATOR.$target_dir;
				if(!is_dir($ifol)){
					if(PHP_OS == "WINNT"){
						if(!is_dir($ifol)) mkdir($ifol);
					}else{
						if(!is_dir($ifol)) mkdir($ifol,0775,true);
					}
				}
				$target_dir = $target_dir.DIRECTORY_SEPARATOR.date("m");
				$ifol = SENEROOT.DIRECTORY_SEPARATOR.$target_dir;
				if(!is_dir($ifol)){
					if(PHP_OS == "WINNT"){
						if(!is_dir($ifol)) mkdir($ifol);
					}else{
						if(!is_dir($ifol)) mkdir($ifol,0775,true);
					}
				}
				$rand = rand(100,999);
				$filename = $nation_code.'-'.$c_bulksale_id.'-'.$rand.'.'.$ext;
				$filethumb = $nation_code.'-'.$c_bulksale_id.'-'.$rand.'-thumb.'.$ext;
				$filetarget = $target_dir.DIRECTORY_SEPARATOR.$filename;

				if(file_exists(SENEROOT.$filetarget)){
					$rand = rand(100,999);
					$filename = $nation_code.'-'.$c_bulksale_id.'-'.$rand.'.'.$ext;
					$filethumb = $nation_code.'-'.$c_bulksale_id.'-'.$rand.'-thumb.'.$ext;
					$filetarget = $target_dir.DIRECTORY_SEPARATOR.$filename;
					if(file_exists(SENEROOT.$filetarget)){
						$rand = rand(1000,9999);
						$filename = $nation_code.'-'.$c_bulksale_id.'-'.$rand.'.'.$ext;
						$filethumb = $nation_code.'-'.$c_bulksale_id.'-'.$rand.'-thumb.'.$ext;
						$filetarget = $target_dir.DIRECTORY_SEPARATOR.$filename;
					}
				}
				$filetargetthumb = $target_dir.DIRECTORY_SEPARATOR.$filethumb;
				$filetarget = str_replace('//','/',$filetarget);

				move_uploaded_file($_FILES["image"]["tmp_name"], SENEROOT.$filetarget);
				if(file_exists(SENEROOT.$filetarget)){
					$this->lib("wideimage/WideImage","inc");
					$filetargetthumb = str_replace('//','/',$filetargetthumb);

					$f1 = str_replace("//","/",SENEROOT.$filetarget);
					$f2 = str_replace("//","/",SENEROOT.$filetargetthumb);
					WideImage::load($f1)->resize(300)->saveToFile($f2);
					WideImage::load($f2)->crop('center', 'center', 300, 300)->saveToFile($f2);

					$filetarget = ltrim($filetarget,'/');
					$filetargetthumb = ltrim($filetargetthumb,'/');

					$di = array();
          $di['nation_code'] = $nation_code;
					$di['c_produk_id'] = $c_bulksale_id;
					$di['id'] = $this->cbsfm->getLastId($nation_code,$c_bulksale_id);
					$di['utype'] = 'internal';
					$di['jenis'] = 'foto';
					$di['url'] = $filetarget;
					$di['url_thumb'] = $filetargetthumb;
					$di['is_active'] = 1;
					$res = $this->cbsfm->set($di);
					if($res){
						$this->status = 200;
						$this->message = 'Success';
					}else{
						$this->status = 902;
						$this->message = 'Failed upload data';
					}
				}else{
					$this->status = 903;
					$this->message = 'Failed upload data';
				}
			}else{
				$this->status = 904;
				$this->message = 'Only JPG, JPEG, PNG extension allowed';
			}
		}else{
			$this->status = 905;
			$this->message = 'Image parameter not delivered, please check again';
		}
		$this->__json_out($data);
	}
	public function gambar_hapus($c_bulksale_id,$c_produk_foto_id){
		$c_bulksale_id = (int) $c_bulksale_id;
		$c_produk_foto_id = (int) $c_produk_foto_id;
		$d = $this->__init();
		$data = array();
		$data['images'] = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Access Denied';
			header("HTTP/1.0 400 Access Denied");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		if($c_bulksale_id<=0){
			$this->status = 701;
			$this->message = 'Invalid BulkSale ID';
			$this->__json_out($data);
			die();
		}
		if($c_produk_foto_id<=0){
			$this->status = 702;
			$this->message = 'ID Foto Produk tidak sah';
			$this->__json_out($data);
			die();
		}
		$bulksale = $this->cbsm->getById($nation_code, $c_bulksale_id);
		if(!isset($bulksale->id)){
			$this->status = 451;
			$this->message = 'BulkSale not found or has been deleted';
			$this->__json_out($data);
			die();
		}
		$bulksale_foto = $this->cbsfm->getByIdProdukId($nation_code, $c_produk_foto_id,$c_bulksale_id);
		if(!isset($bulksale_foto->id)){
			$this->status = 452;
			$this->message = 'Image BulkSale not found or has been deleted';
			$this->__json_out($data);
			die();
		}
		$res = $this->cbsfm->delByIdProdukId($nation_code, $c_bulksale_id, $c_produk_foto_id);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
			if(file_exists(SENEROOT.'/'.$bulksale_foto->url)){
				unlink(SENEROOT.'/'.$bulksale_foto->url);
			}
			if(file_exists(SENEROOT.'/'.$bulksale_foto->url_thumb)){
				unlink(SENEROOT.'/'.$bulksale_foto->url_thumb);
			}
		}else{
			$this->status = 900;
			$this->message = 'Failed deleted image from database';
		}
		$this->__json_out($data);
	}
	public function gambar_cover($c_bulksale_id,$c_produk_foto_id){
		$c_bulksale_id = (int) $c_bulksale_id;
		$c_produk_foto_id = (int) $c_produk_foto_id;
		$d = $this->__init();
		$data = array();
		$data['images'] = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Access Denied';
			header("HTTP/1.0 400 Access Denied");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		if($c_bulksale_id<=0){
			$this->status = 701;
			$this->message = 'Invalid BulkSale ID';
			$this->__json_out($data);
			die();
		}
		if($c_produk_foto_id<=0){
			$this->status = 702;
			$this->message = 'ID Foto Produk tidak sah';
			$this->__json_out($data);
			die();
		}
		$bulksale = $this->cbsm->getById($nation_code, $c_bulksale_id);
		if(!isset($bulksale->id)){
			$this->status = 451;
			$this->message = 'BulkSale not found or has been deleted';
			$this->__json_out($data);
			die();
		}
		$bulksale_foto = $this->cbsfm->getByIdProdukId($nation_code, $c_produk_foto_id,$c_bulksale_id);
		if(!isset($bulksale_foto->id)){
			$this->status = 452;
			$this->message = 'Image BulkSale not found or has been deleted';
			$this->__json_out($data);
			die();
		}
		$du = array();
		$du['foto'] = $bulksale_foto->url;
		$du['thumb'] = $bulksale_foto->url_thumb;
		$res = $this->cbsm->update($nation_code,$c_bulksale_id,$du);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 900;
			$this->message = 'Failed deleted image from database';
		}
		$this->__json_out($data);
	}

	public function set_pending($c_bulksale_id){
		$c_bulksale_id = (int) $c_bulksale_id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($c_bulksale_id)){
			$this->status = 400;
			$this->message = 'Access Denied';
			header("HTTP/1.0 400 Access Denied");
			$this->__json_out($data);
			die();
		}
		if($c_bulksale_id<=0){
			$this->status = 400;
			$this->message = 'Invalid BulkSale ID';
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$bulksale = $this->cbsm->getById($nation_code, $c_bulksale_id);
		if(!isset($bulksale->id)){
			$this->status = 400;
			$this->message = 'BulkSale not found';
			$this->__json_out($data);
			die();
		}
		$du = array("action_status"=>'pending');
		$res = $this->cbsm->update($nation_code,$c_bulksale_id,$du);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 188;
			$this->message = 'Failed updating data bulksale';
		}
		$this->__json_out($data);
	}

	public function set_leaved($c_bulksale_id){
		$c_bulksale_id = (int) $c_bulksale_id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($c_bulksale_id)){
			$this->status = 400;
			$this->message = 'Access Denied';
			header("HTTP/1.0 400 Access Denied");
			$this->__json_out($data);
			die();
		}
		if($c_bulksale_id<=0){
			$this->status = 400;
			$this->message = 'Invalid BulkSale ID';
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$bulksale = $this->cbsm->getById($nation_code, $c_bulksale_id);
		if(!isset($bulksale->id)){
			$this->status = 400;
			$this->message = 'BulkSale not found';
			$this->__json_out($data);
			die();
		}
		$du = array("action_status"=>'leaved');
		$res = $this->cbsm->update($nation_code,$c_bulksale_id,$du);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 188;
			$this->message = 'Failed updating data bulksale';
		}
		$this->__json_out($data);
	}

	public function set_visited($c_bulksale_id){
		$c_bulksale_id = (int) $c_bulksale_id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($c_bulksale_id)){
			$this->status = 400;
			$this->message = 'Access Denied';
			header("HTTP/1.0 400 Access Denied");
			$this->__json_out($data);
			die();
		}
		if($c_bulksale_id<=0){
			$this->status = 400;
			$this->message = 'Invalid BulkSale ID';
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$bulksale = $this->cbsm->getById($nation_code, $c_bulksale_id);
		if(!isset($bulksale->id)){
			$this->status = 400;
			$this->message = 'BulkSale not found';
			$this->__json_out($data);
			die();
		}
		$du = array("vdate"=>'now()',"action_status"=>'visited');
		$res = $this->cbsm->update($nation_code,$c_bulksale_id,$du);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 188;
			$this->message = 'Failed updating data bulksale';
		}
		$this->__json_out($data);
	}

	public function set_completed($c_bulksale_id){
		$c_bulksale_id = (int) $c_bulksale_id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($c_bulksale_id)){
			$this->status = 400;
			$this->message = 'Access Denied';
			header("HTTP/1.0 400 Access Denied");
			$this->__json_out($data);
			die();
		}
		if($c_bulksale_id<=0){
			$this->status = 400;
			$this->message = 'Invalid BulkSale ID';
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$bulksale = $this->cbsm->getById($nation_code, $c_bulksale_id);
		if(!isset($bulksale->id)){
			$this->status = 400;
			$this->message = 'BulkSale not found';
			$this->__json_out($data);
			die();
		}
		$du = array("action_status"=>'completed');
		$res = $this->cbsm->update($nation_code,$c_bulksale_id,$du);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 188;
			$this->message = 'Failed updating data bulksale';
		}
		$this->__json_out($data);
	}

	public function active($c_bulksale_id){
		$c_bulksale_id = (int) $c_bulksale_id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($c_bulksale_id)){
			$this->status = 400;
			$this->message = 'Access Denied';
			header("HTTP/1.0 400 Access Denied");
			$this->__json_out($data);
			die();
		}
		if($c_bulksale_id<=0){
			$this->status = 400;
			$this->message = 'Invalid BulkSale ID';
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$bulksale = $this->cbsm->getById($nation_code, $c_bulksale_id);
		if(!isset($bulksale->id)){
			$this->status = 400;
			$this->message = 'BulkSale not found';
			$this->__json_out($data);
			die();
		}
		$du = array("is_active"=>1);
		$res = $this->cbsm->update($nation_code,$c_bulksale_id,$du);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 188;
			$this->message = 'Failed updating data bulksale';
		}
		$this->__json_out($data);
	}

	public function inactive($c_bulksale_id){
		$c_bulksale_id = (int) $c_bulksale_id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($c_bulksale_id)){
			$this->status = 400;
			$this->message = 'Access Denied';
			header("HTTP/1.0 400 Access Denied");
			$this->__json_out($data);
			die();
		}
		if($c_bulksale_id<=0){
			$this->status = 400;
			$this->message = 'Invalid BulkSale ID';
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$bulksale = $this->cbsm->getById($nation_code, $c_bulksale_id);
		if(!isset($bulksale->id)){
			$this->status = 400;
			$this->message = 'BulkSale not found';
			$this->__json_out($data);
			die();
		}
		$du = array("is_active"=>0);
		$res = $this->cbsm->update($nation_code,$c_bulksale_id,$du);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 188;
			$this->message = 'Failed updating data bulksale';
		}
		$this->__json_out($data);
	}
	public function visit_date(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($c_bulksale_id)){
			$this->status = 400;
			$this->message = 'Access Denied';
			header("HTTP/1.0 400 Access Denied");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		//collect input
		$c_bulksale_id = (int) $this->input->post("c_bulksale_id");
		if($c_bulksale_id<=0){
			$this->status = 600;
			$this->message = 'Invalid BulkSale ID';
			$this->__json_out($data);
			die();
		}
		$visit_date = $this->input->post("visit_date");
		if(strlen($visit_date)!=10) $visit_date = date("Y-m-d");
		$du = array("vdate"=>$visit_date,"action_status"=>"visited");
		$res = $this->cbsm->update($nation_code,$c_bulksale_id,$du);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 188;
			$this->message = 'Failed updating data bulksale';
		}
		$this->__json_out($data);
	}

	public function change_status(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Access Denied';
			header("HTTP/1.0 400 Access Denied");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		//collect input
		$c_bulksale_id = (int) $this->input->post("c_bulksale_id");
		if($c_bulksale_id<=0){
			$this->status = 600;
			$this->message = 'Invalid BulkSale ID';
			$this->__json_out($data);
			die();
		}
		$bulksale = $this->cbsm->getById($nation_code,$c_bulksale_id);
		if(!isset($bulksale->id)){
			$this->status = 660;
			$this->message = 'BulkSale with supplied ID not found';
			$this->__json_out($data);
			die();
		}
		$change_status = strtolower($this->input->post("change_status"));
		$reason = strip_tags($this->input->post("reason"),'<br>');
		if(empty($reason)) $reason='';
		$du = array();
		$du["reason"] = $reason;
		$du["action_status"] = $change_status;
		switch ($change_status) {
			case 'visited':
				if($bulksale->vdate == '-') $du["vdate"] = date("Y-m-d");
				break;
			case 'completed':
				if($bulksale->vdate == '-') $du["vdate"] = date("Y-m-d");
				break;
			case 'pending':
				$du["vdate"] = "NULL";
				break;
			default:
				//do nothing
				break;
		}
		$res = $this->cbsm->update($nation_code,$c_bulksale_id,$du);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 188;
			$this->message = 'Failed updating data bulksale';
		}
		$this->__json_out($data);
	}

	public function input_price(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($c_bulksale_id)){
			$this->status = 400;
			$this->message = 'Access Denied';
			header("HTTP/1.0 400 Access Denied");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		//collect input
		$c_bulksale_id = (int) $this->input->post("c_bulksale_id");
		if($c_bulksale_id<=0){
			$this->status = 600;
			$this->message = 'Invalid BulkSale ID';
			$this->__json_out($data);
			die();
		}
		$input_price = (float) $this->input->post("input_price");
		$du = array("price"=>$input_price);
		$res = $this->cbsm->update($nation_code,$c_bulksale_id,$du);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 188;
			$this->message = 'Failed updating data bulksale';
		}
		$this->__json_out($data);
	}
}
