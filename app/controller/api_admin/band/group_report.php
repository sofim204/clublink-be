<?php
class Group_Report extends JI_Controller{
	public function __construct(){
    parent::__construct();
		$this->lib("seme_purifier");
		$this->load("api_admin/i_group_model",'igm');
		$this->load("api_admin/g_leaderboard_point_history_model", 'glphm');
		$this->current_parent = 'band';
		$this->current_page = 'band_group';

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
                $sortCol = "image";
                break;
			case 3:
				$sortCol = "name";
				break;
			case 4:
				$sortCol = "category_name";
				break;
			case 5:
				$sortCol = "group_type";
				break;
            case 6:
                $sortCol = "total_people";
                break;            
            case 7:
                $sortCol = "creator";
                break;
            case 8:
                $sortCol = "cdate";
                break;
            case 9:
                $sortCol = "is_active";
                break;
			default:
				$sortCol = "cdate";
		}

		if(empty($draw)) $draw = 0;
		if(empty($pagesize)) $pagesize=10;
		if(empty($page)) $page=0;

		$keyword = $sSearch;

		$this->status = 200;
		$this->message = 'Success';
		$dcount = $this->igm->countAllReport($nation_code,$keyword);
		$ddata = $this->igm->getAllReport($nation_code,$page,$pagesize,$sortCol,$sortDir,$keyword);

		foreach($ddata as &$gd){

			if(isset($gd->image)){
				// if(strlen($gd->icon)<=4) $gd->icon = 'media/icon/default-icon.png';
				// if($gd->icon == 'default.png' || $gd->icon== 'default.jpg') $gd->icon = 'media/icon/default-icon.png';
				// $gd->icon = base_url($gd->icon);
				if(strlen($gd->image) > 4) {
					$gd->image = '<img src="'.base_url($gd->image).'" class="img-responsive" style="width: 64px;" />';
				}
			}

			if (isset($gd->group_type)) {
                $group_type = "";
				if($gd->group_type == 'public') {
                    $group_type = '<label class="label label-info">'.$gd->group_type.'</label>';
                } else if($gd->group_type == 'private') {
                    $group_type = '<label class="label label-primary">'.$gd->group_type.'</label>';
                } else {
                    $group_type = '<label class="label label-default">'.$gd->group_type.'</label>';
                }				 
                $gd->group_type = '<center><span>'.$group_type.'</span></center><div style="margin-bottom: 3px;"></div>';
            }

            if (isset($gd->creator)) {
                $nama = $gd->creator;
                $gd->creator = '<span style="font-size: 1.2em; font-weight: bolder;">'.$nama.'</span><br />';
                if (isset($gd->telp) && $gd->telp != '') {
                    $gd->creator .= '<small><i class="fa fa-phone"></i> '.$gd->telp.'</small><br />';
                }
                if (isset($gd->email) && $gd->email != '') {
                    $gd->creator .= '<small><i class="fa fa-envelope"></i> '.$gd->email.'</small><br />';
                }
                if (isset($gd->verif_telp_manual) && $gd->verif_telp_manual != '') {
                    if ($gd->verif_telp_manual == 1) {
                        $gd->creator .= '<b style="color:green;">Confirmed</b> <img src="'.base_url("media/icon/verified.png").'" class="img-responsive" style="width: 20px;" />';
                    }else{
                        $gd->creator .= '<b style="color:red;">Not Yet Confirmed</b>';
                    }
                    
                }
            }

			if (isset($gd->is_active)) {
                $is_active = "";
				if($gd->is_active == '1') {
                    $is_active = '<label class="label label-success">Yes</label>';
                } else {
                    $is_active = '<label class="label label-danger">No</label>';
                }				 
                $gd->is_active = '<center><span>'.$is_active.'</span></center><div style="margin-bottom: 3px;"></div>';
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
			$this->message = 'Nama (Englist) must be filled';
			$this->__json_out($data);
			die();
		}
		if ($_POST['indonesia'] == '' || $_POST['indonesia'] == null){
			$this->status = 104;
			$this->message = 'Nama (Indonesia) must be filled';
			$this->__json_out($data);
			die();
		}
		if ($_POST['korea'] == '' || $_POST['korea'] == null){
			$this->status = 104;
			$this->message = 'Nama (Korea) must be filled';
			$this->__json_out($data);
			die();
		}
		if ($_POST['thailand'] == '' || $_POST['thailand'] == null){
			$this->status = 104;
			$this->message = 'Nama (Thailand) must be filled';
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
		if ($_POST['prioritas_indonesia'] == '' || $_POST['prioritas_indonesia'] == null){
			$this->status = 104;
			$this->message = 'Indonesia Priority must be filled';
			$this->__json_out($data);
			die();
		}
        // ========= End Check $_POST =========================
        

		//start transaction
		$this->igm->trans_start();
		
        $category_id = $this->GUIDv4();

        $di = [
            'nation_code' => $pengguna->nation_code,
		    'id' => $category_id,
            'image_icon' => '',
            'nama' => $_POST['nama'],
            'indonesia' => $_POST['indonesia'],
            'korea' => $_POST['korea'],
            'thailand' => $_POST['thailand'],
            'prioritas' => $_POST['prioritas'],
            'prioritas_indonesia' => $_POST['prioritas_indonesia'],
            'is_visible' => $_POST['is_visible'],
			'cdate' => date('Y-m-d H:i:s'),
            'is_active' => $_POST['is_active']
        ];
		//insert into db
		$res = $this->igm->set($di);
		if($res){
			$ext = 'jpg';
			$pi = pathinfo($_FILES['image_icon']['name']);
			if(isset($pi['extension'])) $ext = $pi['extension'];
			$target_dir = $this->media_group.'category/';
            //check folder
            if(!is_dir(SENEROOT.$target_dir)) mkdir(SENEROOT.$target_dir,0777);

			$target_file = $this->__slugify($di['nama'])."-$pengguna->nation_code-$category_id-".date("His").".$ext";
			$filename = $target_dir.$target_file;
			if(file_exists( SENEROOT.$filename)) unlink(SENEROOT.$filename);
			$mv = move_uploaded_file($_FILES["image_icon"]["tmp_name"],  SENEROOT.$filename);
			$dux = array();
			$dux['image_icon'] = $filename;
			$res2 = $this->igm->update($pengguna->nation_code, $category_id, $dux);
			if($res2){
				$this->status = 200;
				$this->message = 'Success';
				$this->igm->trans_commit();
			}else{
				$this->status = 109;
				$this->message = 'Failed updating image icon';
				$this->igm->trans_rollback();
				//delete file
				if(file_exists( SENEROOT.$filename)) unlink(SENEROOT.$filename);
			}
		}else{
			$this->status = 110;
			$this->message = 'Failed insertin category to database';
			$this->igm->trans_rollback();

		}
		$this->igm->trans_end();
		$this->__json_out($data);
	}
	public function detail($id){
		// $id = (int) $id;
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
		$data = $this->igm->getById($nation_code,$id);
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

		$gd = $this->igm->getById($nation_code, $id);
		if(!isset($gd->id)){
			$this->status = 111;
			$this->message = 'Wrong Category ID, please refresh this page';
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
			$this->message = 'Nama (Englist) must be filled';
			$this->__json_out($data);
			die();
		}
		if ($_POST['indonesia'] == '' || $_POST['indonesia'] == null){
			$this->status = 104;
			$this->message = 'Nama (Indonesia) must be filled';
			$this->__json_out($data);
			die();
		}
		if ($_POST['korea'] == '' || $_POST['korea'] == null){
			$this->status = 104;
			$this->message = 'Nama (Korea) must be filled';
			$this->__json_out($data);
			die();
		}
		if ($_POST['thailand'] == '' || $_POST['thailand'] == null){
			$this->status = 104;
			$this->message = 'Nama (Thailand) must be filled';
			$this->__json_out($data);
			die();
		}
        if ($_POST['prioritas'] == '' || $_POST['prioritas'] == null){
			$this->status = 104;
			$this->message = 'Priority must be filled';
			$this->__json_out($data);
			die();
		}
		if ($_POST['prioritas_indonesia'] == '' || $_POST['prioritas_indonesia'] == null){
			$this->status = 104;
			$this->message = 'Indonesia Priority must be filled';
			$this->__json_out($data);
			die();
		}
        // ========= End Check $_POST =========================	

        $du = [
            'nation_code' => $nation_code,
		    'id' => $id,
            'nama' => $_POST['nama'],
            'indonesia' => $_POST['indonesia'],
            'korea' => $_POST['korea'],
            'thailand' => $_POST['thailand'],
            'prioritas' => $_POST['prioritas'],
            'prioritas_indonesia' => $_POST['prioritas_indonesia'],
            'is_visible' => $_POST['is_visible'],
            'is_active' => $_POST['is_active']
        ];
		

		$res = $this->igm->update($nation_code, $id, $du);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 901;
			$this->message = 'Cant update category to database';
		}
		$this->__json_out($data);
	}

    public function confirmation($id){
		$d = $this->__init();
		$data = array();

        //admin
		$pengguna = $d['sess']->admin;

		$gd = $this->igm->getById($pengguna->nation_code, $id);
		if(!isset($gd->id)){
			$this->status = 111;
			$this->message = 'Wrong Group ID, please refresh this page';
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

		$category_data = $this->igm->getById($pengguna->nation_code, $id);
		if(!isset($category_data->id)){
			$this->status = 520;
			$this->message = 'ID not found or has been deleted';
			$this->__json_out($data);
			die();
		}

		$du = array();
		$du['verif_telp_manual'] = 1;		

		$res = $this->igm->update($pengguna->nation_code, $id, $du);
		if($res){
			// if(strlen($category_data->image_icon)>4){
			// 	$image_icon = SENEROOT.DIRECTORY_SEPARATOR.$category_data->image_icon;
			// 	if(file_exists($image_icon)) unlink($image_icon);
			// }
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 902;
			$this->message = 'Cant takedown group';
		}
		$this->__json_out($data);
	}

    public function restore($id){
		$d = $this->__init();
		$data = array();

        //admin
		$pengguna = $d['sess']->admin;

		$gd = $this->igm->getById($pengguna->nation_code, $id);
		if(!isset($gd->id)){
			$this->status = 111;
			$this->message = 'Wrong Group ID, please refresh this page';
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

		$category_data = $this->igm->getById($pengguna->nation_code, $id);
		if(!isset($category_data->id)){
			$this->status = 520;
			$this->message = 'ID not found or has been deleted';
			$this->__json_out($data);
			die();
		}

		$du = array();
		$du['is_active'] = 1;
		$du['verif_telp_manual'] = 0;
		$du['report_date'] = 'NOW()';
		$du['is_report'] = 0;
		$du['is_take_down'] = 0;
		$du['take_down_date'] = "NOW()";		

		$res = $this->igm->update($pengguna->nation_code, $id, $du);
		if($res){
			// if(strlen($category_data->image_icon)>4){
			// 	$image_icon = SENEROOT.DIRECTORY_SEPARATOR.$category_data->image_icon;
			// 	if(file_exists($image_icon)) unlink($image_icon);
			// }
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 902;
			$this->message = 'Cant takedown group';
		}
		$this->__json_out($data);
	}

	public function takedown($id){
		$d = $this->__init();
		$data = array();

        //admin
		$pengguna = $d['sess']->admin;

		$gd = $this->igm->getById($pengguna->nation_code, $id);
		if(!isset($gd->id)){
			$this->status = 111;
			$this->message = 'Wrong Group ID, please refresh this page';
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

		$category_data = $this->igm->getById($pengguna->nation_code, $id);
		if(!isset($category_data->id)){
			$this->status = 520;
			$this->message = 'ID not found or has been deleted';
			$this->__json_out($data);
			die();
		}

		$du = array();
		$du['is_active'] = 0;
		$du['report_date'] = 'NOW()';
		$du['is_report'] = 1;
		$du['is_take_down'] = 1;
		$du['take_down_date'] = "NOW()";		

		$res = $this->igm->update($pengguna->nation_code, $id, $du);
		if($res){
			// if(strlen($category_data->image_icon)>4){
			// 	$image_icon = SENEROOT.DIRECTORY_SEPARATOR.$category_data->image_icon;
			// 	if(file_exists($image_icon)) unlink($image_icon);
			// }

			// kurangi SPT
			$historysGetSPT = $this->glphm->getRecordGroup($pengguna->nation_code, $id);
			if($historysGetSPT){
				$di = array();
				$di['nation_code'] = $pengguna->nation_code;
				$di['b_user_alamat_location_kelurahan'] = "All";
				$di['b_user_alamat_location_kecamatan'] = "All";
				$di['b_user_alamat_location_kabkota'] = "All";
				$di['b_user_alamat_location_provinsi'] = "All";
				$di['b_user_id'] = $historysGetSPT->b_user_id;
				$di['plusorminus'] = "-";
				$di['point'] = $historysGetSPT->point;
				$di['custom_id'] = $id;
				$di['custom_type'] = $historysGetSPT->custom_type;
				$di['custom_type_sub'] = $historysGetSPT->custom_type_sub;
				$di['custom_text'] = $pengguna->nama.' has delete '.$di['custom_type'].' '.$di['custom_type_sub'].' and deduct '.$di['point'].' point(s)';
				$endDoWhile = 0;
				do{
				$leaderBoardHistoryId = $this->GUIDv4();
				$checkId = $this->glphm->checkId($pengguna->nation_code, $leaderBoardHistoryId);
				if($checkId == 0){
					$endDoWhile = 1;
				}
				}while($endDoWhile == 0);
				$di['id'] = $leaderBoardHistoryId;
				$this->glphm->set($di);
				// $this->glrm->updateTotal($pengguna->nation_code, $pelanggan->id, 'total_point', '+', $di['point']);
			}
			// END kurangi SPT

			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 902;
			$this->message = 'Cant takedown group';
		}
		$this->__json_out($data);
	}

	public function change_icon($id){		
		$d = $this->__init();
		$data = array();

        //admin
		$pengguna = $d['sess']->admin;

		$gd = $this->igm->getById($pengguna->nation_code, $id);
		if(!isset($gd->id)){
			$this->status = 111;
			$this->message = 'Wrong Category ID, please refresh this page';
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

		$target_dir = $this->media_group.'category/';
        //check folder
        if(!is_dir(SENEROOT.$target_dir)) mkdir(SENEROOT.$target_dir,0777);
        $target_file = $this->__slugify($gd->nama)."-$pengguna->nation_code-$id-".date("His").".$ext";
		$filename = $target_dir.$target_file;

        

		if(file_exists(SENEROOT.$filename)){
			unlink(SENEROOT.$filename);
			$filename = $this->__slugify($gd->nama)."-$gd->nation_code-$gd->id-".rand(0,999).".$ext";
			$filename = $target_dir.$target_file;
			if(file_exists($filename)) unlink(SENEROOT.$filename);
		}
		$mv = move_uploaded_file($_FILES["image_icon"]["tmp_name"],  SENEROOT.$filename);
		if($mv){
			if(strlen($gd->image_icon)>4){
				$image_icon = SENEROOT.DIRECTORY_SEPARATOR.$gd->image_icon;
				if(file_exists($image_icon)) unlink($image_icon);
			}
			$du = array();
			$du['image_icon'] = $filename;
			$this->igm->update($pengguna->nation_code, $gd->id, $du);
			$this->status = 200;
			$this->message = 'Success';
		}
		$this->__json_out($data);
	}

	public function detail_group_post($id)
    {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }

        if ($id<=0) {
            $this->status = 591;
            $this->message = 'Invalid ID';
            $this->__json_out($data);
            die();
        }

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
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
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

        switch($iSortCol_0){
			// by Yopie Hidayat 10 Juni 2023 14:40 | add & edit input priority, show priority in datatable
			case 0:
				$sortCol = "no";
				break;
			case 1:
				$sortCol = "id";
				break;
            case 2:
                $sortCol = "user";
                break;
			case 3:
				$sortCol = "deskripsi";
				break;
			case 4:
				$sortCol = "cdate";
				break;
			case 5:
				$sortCol = "is_active";
				break;
			default:
				$sortCol = "cdate";
		}
        
        $keyword = $sSearch;

        $nation_code = $d['sess']->admin->nation_code;
        $this->status = 200;
        $this->message = 'Success';
        $ddata = $this->igm->getByIds($nation_code, $id, $page, $pagesize, $sortCol, $sortDir, $keyword);
        $dcount = $this->igm->countAlls($nation_code, $id, $keyword);

		// var_dump($data);die;

		foreach($ddata as &$gd){

			// if(isset($gd->image)){
			// 	// if(strlen($gd->icon)<=4) $gd->icon = 'media/icon/default-icon.png';
			// 	// if($gd->icon == 'default.png' || $gd->icon== 'default.jpg') $gd->icon = 'media/icon/default-icon.png';
			// 	// $gd->icon = base_url($gd->icon);
				
			// 	if(strlen($gd->image) > 4) {
			// 		$gd->image = '<img src="'.base_url($gd->image).'" class="img-responsive" style="width: 64px;" />';
			// 	}
			// }

			if (isset($gd->is_active)) {
				$gd->is_active_ori = $gd->is_active;
                $is_active = "";
				if($gd->is_active == '1') {
                    $is_active = '<label class="label label-success">Yes</label>';
                } else {
                    $is_active = '<label class="label label-danger">No</label>';
                }				 
                $gd->is_active = '<center><span>'.$is_active.'</span></center><div style="margin-bottom: 3px;"></div>';
            }
		}

        // foreach ($data as &$dt) {
        //     if (isset($dt->cdate)) {
        //         $dt->cdate = date("m/d/y H:i", strtotime($dt->cdate));
        //     }
        //     $dt->action = '<button class="btn btn-info" data-id="'.$dt->id.'">View Options</button>';
        // }
        // $return = array();
        // foreach ($data as $key => $dts) {
        //     $return[$key]['id'] = $dts->id;
        //     $return[$key]['nama'] = $this->__convertToEmoji($dts->nama);
        //     $return[$key]['b_user_fnama'] = $dts->b_user_fnama;
        //     $return[$key]['user_type'] = $dts->user_type;
        //     $return[$key]['cdate'] = $dts->cdate;
        //     if($dts->is_takedown==0)
        //     {
        //         $status = "Active";
        //     }
        //     else
        //     {
        //         $status = "Takedown";
        //     }
        //     $return[$key]['takedown'] = $status;
        //     $return[$key]['message'] = $this->__convertToEmoji($dts->message);
        //     $return[$key]['action'] = $dts->action;
        //     /*$return[$key]['action'] = $dt->action;*/
        //     // End Edit
        // }
        //sleep(3);
        $another = array();
        $this->__jsonDataTable($ddata, $dcount);
    }

	public function detail_participant_group($id)
    {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }

        // if ($id<=0) {
        //     $this->status = 591;
        //     $this->message = 'Invalid ID';
        //     $this->__json_out($data);
        //     die();
        // }
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;
		$gd = $this->igm->getById($pengguna->nation_code, $id);
		if(!isset($gd->id)){
			$this->status = 111;
			$this->message = 'Wrong Group ID, please refresh this page';
			$this->__json_out($data);
			die();
		}

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
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
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

        switch($iSortCol_0){
			// by Yopie Hidayat 10 Juni 2023 14:40 | add & edit input priority, show priority in datatable
			case 0:
				$sortCol = "no";
				break;
			case 1:
				$sortCol = "user_id";
				break;
            case 2:
                $sortCol = "user";
                break;
            case 3:
                $sortCol = "is_owner";
                break;
            case 4:
                $sortCol = "is_co_admin";
                break;
            case 5:
                $sortCol = "join_date";
                break;
			default:
				$sortCol = "user";
		}
        
        $keyword = $sSearch;

        $nation_code = $d['sess']->admin->nation_code;
        $this->status = 200;
        $this->message = 'Success';
        $ddata = $this->igm->getParticipantByGroupID($nation_code, $id, $page, $pagesize, $sortCol, $sortDir, $keyword);
        $dcount = $this->igm->countParticipantByGroupID($nation_code, $id, $keyword);

		// var_dump($data);die;

		foreach($ddata as &$gd){

			// if(isset($gd->image)){
			// 	// if(strlen($gd->icon)<=4) $gd->icon = 'media/icon/default-icon.png';
			// 	// if($gd->icon == 'default.png' || $gd->icon== 'default.jpg') $gd->icon = 'media/icon/default-icon.png';
			// 	// $gd->icon = base_url($gd->icon);
				
			// 	if(strlen($gd->image) > 4) {
			// 		$gd->image = '<img src="'.base_url($gd->image).'" class="img-responsive" style="width: 64px;" />';
			// 	}
			// }

			if (isset($gd->is_owner)) {
                $is_owner = "";
				if($gd->is_owner == '1') {
                    $is_owner = '<label class="label label-success">Yes</label>';
                } else {
                    $is_owner = '<label class="label label-danger">No</label>';
                }				 
                $gd->is_owner = '<center><span>'.$is_owner.'</span></center><div style="margin-bottom: 3px;"></div>';
            }

			if (isset($gd->is_co_admin)) {
                $is_co_admin = "";
				if($gd->is_co_admin == '1') {
                    $is_co_admin = '<label class="label label-success">Yes</label>';
                } else {
                    $is_co_admin = '<label class="label label-danger">No</label>';
                }				 
                $gd->is_co_admin = '<center><span>'.$is_co_admin.'</span></center><div style="margin-bottom: 3px;"></div>';
            }

			if (isset($gd->is_active)) {
                $is_active = "";
				if($gd->is_active == '1') {
                    $is_active = '<label class="label label-success">Yes</label>';
                } else {
                    $is_active = '<label class="label label-danger">No</label>';
                }				 
                $gd->is_active = '<center><span>'.$is_active.'</span></center><div style="margin-bottom: 3px;"></div>';
            }
		}

        // foreach ($data as &$dt) {
        //     if (isset($dt->cdate)) {
        //         $dt->cdate = date("m/d/y H:i", strtotime($dt->cdate));
        //     }
        //     $dt->action = '<button class="btn btn-info" data-id="'.$dt->id.'">View Options</button>';
        // }
        // $return = array();
        // foreach ($data as $key => $dts) {
        //     $return[$key]['id'] = $dts->id;
        //     $return[$key]['nama'] = $this->__convertToEmoji($dts->nama);
        //     $return[$key]['b_user_fnama'] = $dts->b_user_fnama;
        //     $return[$key]['user_type'] = $dts->user_type;
        //     $return[$key]['cdate'] = $dts->cdate;
        //     if($dts->is_takedown==0)
        //     {
        //         $status = "Active";
        //     }
        //     else
        //     {
        //         $status = "Takedown";
        //     }
        //     $return[$key]['takedown'] = $status;
        //     $return[$key]['message'] = $this->__convertToEmoji($dts->message);
        //     $return[$key]['action'] = $dts->action;
        //     /*$return[$key]['action'] = $dt->action;*/
        //     // End Edit
        // }
        //sleep(3);
        $another = array();
        $this->__jsonDataTable($ddata, $dcount);
    }

	public function takedown_post($id){
		$d = $this->__init();
		$data = array();

        //admin
		$pengguna = $d['sess']->admin;

		$gd = $this->igm->getById_post($pengguna->nation_code, $id);
		if(!isset($gd->id)){
			$this->status = 111;
			$this->message = 'Wrong Group Post ID, please refresh this page';
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

		$category_data = $this->igm->getById_post($pengguna->nation_code, $id);
		if(!isset($category_data->id)){
			$this->status = 520;
			$this->message = 'ID not found or has been deleted';
			$this->__json_out($data);
			die();
		}

		$du = array();
		$du['is_active'] = 0;
		$du['report_date'] = 'NOW()';
		$du['is_report'] = 1;
		$du['is_take_down'] = 1;
		$du['take_down_date'] = "NOW()";		

		$res = $this->igm->update_post($pengguna->nation_code, $id, $du);
		if($res){
			// if(strlen($category_data->image_icon)>4){
			// 	$image_icon = SENEROOT.DIRECTORY_SEPARATOR.$category_data->image_icon;
			// 	if(file_exists($image_icon)) unlink($image_icon);
			// }

			// kurangi SPT
			$historysGetSPT = $this->glphm->getRecordGroupPost($pengguna->nation_code, $id);
			if($historysGetSPT){
				foreach ($historysGetSPT as $key => $value) {
					$di = array();
					$di['nation_code'] = $pengguna->nation_code;
					$di['b_user_alamat_location_kelurahan'] = "All";
					$di['b_user_alamat_location_kecamatan'] = "All";
					$di['b_user_alamat_location_kabkota'] = "All";
					$di['b_user_alamat_location_provinsi'] = "All";
					$di['b_user_id'] = $value->b_user_id;
					$di['plusorminus'] = "-";
					$di['point'] = $value->point;
					$di['custom_id'] = $id;
					$di['custom_type'] = $value->custom_type;
					$di['custom_type_sub'] = $value->custom_type_sub;
					$di['custom_text'] = $pengguna->nama.' has delete '.$di['custom_type'].' '.$di['custom_type_sub'].' and deduct '.$di['point'].' point(s)';
					$endDoWhile = 0;
					do{
					$leaderBoardHistoryId = $this->GUIDv4();
					$checkId = $this->glphm->checkId($pengguna->nation_code, $leaderBoardHistoryId);
					if($checkId == 0){
						$endDoWhile = 1;
					}
					}while($endDoWhile == 0);
					$di['id'] = $leaderBoardHistoryId;
					$this->glphm->set($di);
					// $this->glrm->updateTotal($pengguna->nation_code, $pelanggan->id, 'total_point', '+', $di['point']);
				}
			}
			// END kurangi SPT

			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 902;
			$this->message = 'Cant takedown group post';
		}
		$this->__json_out($data);
	}

	public function detail_post($id){
		// $id = (int) $id;
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

		$data_post = $this->igm->getById_post($pengguna->nation_code, $id);
		if(!isset($data_post->id)){
			$this->status = 520;
			$this->message = 'ID not found or has been deleted';
			$this->__json_out($data);
			die();
		}

		$this->status = 200;
		$this->message = 'Success';
		$data_post_attachs = $this->igm->getByPostId_postAttachment($nation_code,$id);
		if(count($data_post_attachs) <= 0){
			$data = new stdClass();
			$data->post_desc = $data_post->deskripsi;
			$data->url = '-';
		}else{
			$data = new stdClass();	
			$data->post_desc = $data_post->deskripsi;
			$gabungan_data = '';
			$data->url = '';
			foreach($data_post_attachs as $post_attachment) {
				if ($post_attachment->jenis == "attendance sheet") {
					$dataSheet = $this->igm->getByPostAttachmentId_postAttachmentAttendanceSheet($nation_code,$post_attachment->id);
					if(!isset($dataSheet->id)){
						$data = new stdClass();
						$this->status = 441;
						$this->message = 'No Sheet Data';
						$this->__json_out($data);
						die();
					}
					$gabungan_data .= '<br><b>Title</b> <br>'.$dataSheet->title.'<br>'
								.'<b>Response Option</b> <br>'.$dataSheet->response_option.'<br>'
								.'<b>Member</b> <br>'
								;
					$dataSheetMember = $this->igm->getByPostAttachmentId_postAttachmentAttendanceSheetMember($nation_code,$dataSheet->id);
					if(!isset($dataSheetMember)){
						// $member_sheet = array();
						$gabungan_data .= 'No Member';
					}else{
						$no = 1;
						foreach($dataSheetMember as $member) {
							$option = $member->present_or_absent == '' ? '<i>no response</i>' : $member->present_or_absent;
							$gabungan_data .= $no.' '.$member->user.' ('.$option.')<br>';
							$no++;
						}				
					}
					$data->url .= $gabungan_data;
				}elseif($post_attachment->jenis == "location") {
					if(isset($post_attachment->url)) {
						// $data->url = $data->location_nama;
						$latitude = $post_attachment->location_latitude;
						$longitude = $post_attachment->location_longitude;
						$data->url .= '<a href="https://www.google.com/maps/search/?api=1&query='.$latitude.','.$longitude.'" target="_blank">'.$post_attachment->location_nama.'</a><br>';
						
					}
				}elseif($post_attachment->jenis == "video") {
					if(isset($post_attachment->url)) {
						$data->url .= '
							<video width="320px" height="240px" controls>
								<source src="'.base_url($post_attachment->url).'" type="video/mp4">
							</video>
							';				
					}
				}elseif($post_attachment->jenis == "file") {
					if(isset($post_attachment->url)) {
						// $data->url = '<img src="'.base_url($data->url).'" class="img-responsive" style="width: 512px;" />';	
						$data->url .= '<a href="'.base_url($post_attachment->url).'" target="_blank">'.$post_attachment->url.'</a><br>';		
					}
				}else{
					if(isset($post_attachment->url)) {
						$data->url .= '<img src="'.base_url($post_attachment->url).'" class="img-responsive" style="width: 512px;" />';
					}
				}
			}
		}			
		
		// var_dump($data);die;
		$this->__json_out($data);
	}
}