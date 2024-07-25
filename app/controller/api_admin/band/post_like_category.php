<?php
class Post_Like_Category extends JI_Controller{
	public function __construct(){
    parent::__construct();
		$this->lib("seme_purifier");
		$this->load("api_admin/i_group_post_like_category_model",'igplcm');
		$this->current_parent = 'band';
		$this->current_page = 'band_post_like_category';

		if(!is_dir(SENEROOT.'storage')) mkdir(SENEROOT.'storage',0777);
		if(!is_dir(SENEROOT.'storage/images')) mkdir(SENEROOT.'storage/images',0777);
		if(!is_dir(SENEROOT.'storage/images/group')) mkdir(SENEROOT.'storage/images/group',0777);
	}

    //credit: https://www.php.net/manual/en/function.com-create-guid.php#119168
    private function GUIDv4($trim = true)
    {
        // Windows
        if (function_exists('com_create_guid') === true) {
            if ($trim === true)
                return trim(com_create_guid(), '{}');
            else
                return com_create_guid();
        }

        // OSX/Linux
        if (function_exists('openssl_random_pseudo_bytes') === true) {
            $data = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }

        // Fallback (PHP 4.2+)
        mt_srand((double)microtime() * 10000);
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);                  // "-"
        $lbrace = $trim ? "" : chr(123);    // "{"
        $rbrace = $trim ? "" : chr(125);    // "}"
        $guidv4 = $lbrace.
                  substr($charid,  0,  8).$hyphen.
                  substr($charid,  8,  4).$hyphen.
                  substr($charid, 12,  4).$hyphen.
                  substr($charid, 16,  4).$hyphen.
                  substr($charid, 20, 12).
                  $rbrace;
        return $guidv4;
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
			// by Yopie Hidayat 10 Juni 2023 14:40 | add & edit input priority, show priority in datatable
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
				$sortCol = "prioritas";
				break;
            case 5:
                $sortCol = "is_active";
                break;
			default:
				$sortCol = "prioritas";
		}

		if(empty($draw)) $draw = 0;
		if(empty($pagesize)) $pagesize=10;
		if(empty($page)) $page=0;

		$keyword = $sSearch;

		$this->status = 200;
		$this->message = 'Success';
		$dcount = $this->igplcm->countAll($nation_code,$keyword);
		$ddata = $this->igplcm->getAll($nation_code,$page,$pagesize,$sortCol,$sortDir,$keyword);

		foreach($ddata as &$gd){

			if(isset($gd->image_icon)){
				// if(strlen($gd->icon)<=4) $gd->icon = 'media/icon/default-icon.png';
				// if($gd->icon == 'default.png' || $gd->icon== 'default.jpg') $gd->icon = 'media/icon/default-icon.png';
				// $gd->icon = base_url($gd->icon);
				if(strlen($gd->image_icon) > 4) {
					$gd->image_icon = '<img src="'.base_url($gd->image_icon).'" class="img-responsive" style="width: 64px;" />';
				}
			}

			if(isset($gd->is_active)){
				if(!empty($gd->is_active)){
					$gd->is_active = 'Yes';
				}else{
					$gd->is_active = 'No';
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

        // ====== check $_POST ==========================
        if ($_POST['nama'] == '' || $_POST['nama'] == null){
			$this->status = 104;
			$this->message = 'Name must be filled';
			$this->__json_out($data);
			die();
		}
        //  check image_icon
        if ($_FILES["image_icon"]["tmp_name"] == '' || $_FILES["image_icon"]["tmp_name"] == null){
            $this->status = 104;
			$this->message = 'Icon must be filled';
			$this->__json_out($data);
			die();
        }
		$this->__imageValidation("image_icon");
        // end check image_icon
        if ($_POST['prioritas'] == '' || $_POST['prioritas'] == null){
			$this->status = 104;
			$this->message = 'Priority must be filled';
			$this->__json_out($data);
			die();
		}
        // ========= End Check $_POST =========================
        

		//start transaction
		$this->igplcm->trans_start();
		
        $post_like_category_id = $this->GUIDv4();

        $di = [
            'nation_code' => $pengguna->nation_code,
		    'id' => $post_like_category_id,
            'nama' => $_POST['nama'],
            'image_icon' => '',
            'prioritas' => $_POST['prioritas'],
            'is_active' => $_POST['is_active']
        ];
		//insert into db
		$res = $this->igplcm->set($di);
		if($res){
			$ext = 'jpg';
			$pi = pathinfo($_FILES['image_icon']['name']);
			if(isset($pi['extension'])) $ext = $pi['extension'];
			$target_dir = $this->media_group.'post_like_category/';
            //check folder
            if(!is_dir(SENEROOT.$target_dir)) mkdir(SENEROOT.$target_dir,0777);

			$target_file = $this->__slugify($post_like_category_id)."-$pengguna->nation_code-$post_like_category_id-".date("His").".$ext";
			$filename = $target_dir.$target_file;
			if(file_exists( SENEROOT.$filename)) unlink(SENEROOT.$filename);
			$mv = move_uploaded_file($_FILES["image_icon"]["tmp_name"],  SENEROOT.$filename);
			$dux = array();
			$dux['image_icon'] = $filename;
			$res2 = $this->igplcm->update($pengguna->nation_code, $post_like_category_id, $dux);
			if($res2){
				$this->status = 200;
				$this->message = 'Success';
				$this->igplcm->trans_commit();
			}else{
				$this->status = 109;
				$this->message = 'Failed updating image icon';
				$this->igplcm->trans_rollback();
				//delete file
				if(file_exists( SENEROOT.$filename)) unlink(SENEROOT.$filename);
			}
		}else{
			$this->status = 110;
			$this->message = 'Failed insertin category to database';
			$this->igplcm->trans_rollback();

		}
		$this->igplcm->trans_end();
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
		$data = $this->igplcm->getById($nation_code,$id);
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
        $pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$data = array();

		$gd = $this->igplcm->getById($nation_code, $id);
		if(!isset($gd->id)){
			$this->status = 111;
			$this->message = 'Wrong Post Like Category ID, please refresh this page';
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

        // ====== check $_POST ==========================
        if ($_POST['nama'] == '' || $_POST['nama'] == null){
			$this->status = 104;
			$this->message = 'Name must be filled';
			$this->__json_out($data);
			die();
		}
        if ($_POST['prioritas'] == '' || $_POST['prioritas'] == null){
			$this->status = 104;
			$this->message = 'Priority must be filled';
			$this->__json_out($data);
			die();
		}
        // ========= End Check $_POST =========================	

        $du = [
            'nation_code' => $nation_code,
		    'id' => $id,
            'nama' => $_POST['nama'],
            'prioritas' => $_POST['prioritas'],
            'is_active' => $_POST['is_active']
        ];
		

		$res = $this->igplcm->update($nation_code, $id, $du);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 901;
			$this->message = 'Cant update Post Like Category to database';
		}
		$this->__json_out($data);
	}

	// uncomment (Not use)
	// public function hapus($id){
	// 	$d = $this->__init();
	// 	$data = array();

    //     //admin
	// 	$pengguna = $d['sess']->admin;

	// 	$gd = $this->igplcm->getById($pengguna->nation_code, $id);
	// 	if(!isset($gd->id)){
	// 		$this->status = 111;
	// 		$this->message = 'Wrong Category ID, please refresh this page';
	// 		$this->__json_out($data);
	// 		die();
	// 	}
		
	// 	if(!$this->admin_login && empty($id)){
	// 		$this->status = 400;
	// 		$this->message = 'Unauthorized access';
	// 		header("HTTP/1.0 400 Unauthorized");
	// 		$this->__json_out($data);
	// 		die();
	// 	}

	// 	$category_data = $this->igplcm->getById($pengguna->nation_code, $id);
	// 	if(!isset($category_data->id)){
	// 		$this->status = 520;
	// 		$this->message = 'ID not found or has been deleted';
	// 		$this->__json_out($data);
	// 		die();
	// 	}
	// 	$res = $this->igplcm->del($pengguna->nation_code, $id);
	// 	if($res){
	// 		if(strlen($category_data->image_icon)>4){
	// 			$image = SENEROOT.DIRECTORY_SEPARATOR.$category_data->image;
	// 			if(file_exists($image)) unlink($image);
	// 		}
	// 		$this->status = 200;
	// 		$this->message = 'Success';
	// 	}else{
	// 		$this->status = 902;
	// 		$this->message = 'Cant delete category';
	// 	}
	// 	$this->__json_out($data);
	// }

	public function change_icon($id){		
		$d = $this->__init();
		$data = array();

        //admin
		$pengguna = $d['sess']->admin;

		$gd = $this->igplcm->getById($pengguna->nation_code, $id);
		if(!isset($gd->id)){
			$this->status = 111;
			$this->message = 'Wrong Post Like Category ID, please refresh this page';
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

		$target_dir = $this->media_group.'post_like_category/';
        //check folder
        if(!is_dir(SENEROOT.$target_dir)) mkdir(SENEROOT.$target_dir,0777);
        $target_file = $this->__slugify($gd->id)."-$pengguna->nation_code-$id-".date("His").".$ext";
		$filename = $target_dir.$target_file;

        

		if(file_exists(SENEROOT.$filename)){
			unlink(SENEROOT.$filename);
			$filename = $this->__slugify($gd->id)."-$gd->nation_code-$gd->id-".rand(0,999).".$ext";
			$filename = $target_dir.$target_file;
			if(file_exists($filename)) unlink(SENEROOT.$filename);
		}
		$mv = move_uploaded_file($_FILES["image_icon"]["tmp_name"],  SENEROOT.$filename);
		if($mv){
			if(strlen($gd->image_icon)>4){
				$image = SENEROOT.DIRECTORY_SEPARATOR.$gd->image_icon;
				if(file_exists($image)) unlink($image);
			}
			$du = array();
			$du['image_icon'] = $filename;
			$this->igplcm->update($pengguna->nation_code, $gd->id, $du);
			$this->status = 200;
			$this->message = 'Success';
		}
		$this->__json_out($data);
	}
}