<?php
class Discussion extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->lib("seme_purifier");
		$this->load("api_admin/c_community_discussion_model",'discussion_model');
		$this->load("api_admin/c_community_list_model",'community_model');
		$this->current_parent = 'community';
		$this->current_page = 'community_discussion';
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

	private function __convertToEmoji($text){
		$value = $text;
		$readTextWithEmoji = preg_replace("/\\\\u([0-9A-F]{2,5})/i", "&#x$1;", $value);
		return $readTextWithEmoji;
	}

	public function list($id){
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
				$sortCol = "image_icon";
				break;
			case 2:
				$sortCol = "title";
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
		$dcount = $this->discussion_model->countAll($nation_code,$keyword);
		$ddata = $this->discussion_model->getAllById($id,$nation_code,$page,$pagesize,$sortCol,$sortDir,$keyword);

		foreach($ddata as &$gd){

			if(isset($gd->image_icon)){
				if(strlen($gd->image_icon)<=4) $gd->image_icon = 'media/icon/default-icon.png';
				if($gd->image_icon == 'default.png' || $gd->image_icon== 'default.jpg') $gd->image_icon = 'media/icon/default-icon.png';
				$gd->image_icon = base_url($gd->image_icon);
				$gd->image_icon = '<img src="'.$gd->image_icon.'" class="img-responsive" style="width: 64px;" />';
			}

            if (isset($gd->cdate)) {
				$gd->cdate = date("d/M/y H:i:s", strtotime($gd->cdate));
            }

            if (isset($gd->title)) {
                $title = $gd->title;
                $gd->title = '<span style="font-size: 1.2em;">'.$this->__convertToEmoji($title).'</span><br />';
            }

            if (isset($gd->community)) {
                $community = $gd->community_title;
                $gd->community = '<span style="font-size: 1.2em; font-weight: bolder;">'.$community.'</span><br />';
                if (isset($gd->category)) $gd->community .= '<span>Category: '.$gd->category.'</span><br />';
                if (isset($gd->parent) && $gd->parent!=0) $gd->community .= '<span>Parent: '.$gd->parent_title.'</span><br />';
            }

            if (isset($gd->user)) {
                $user = $gd->user;
                $gd->user = '<span style="font-size: 1.2em;">'.$user.'</span><br />';
				// by Muhammad Sofi - 3 November 2021 10:00
				// remark code
                if (isset($gd->address2)) $gd->user .= '<span>Address: '.$gd->address2.'</span><br />';
            }

            if (isset($gd->is_active)) {
                $status = "";
				if(!empty($gd->is_active)) $status = 'Active';
				else $status = 'Inactive';

                $gd->is_active = '<span>'.$status.'</span><br />';
            }

            if (isset($gd->is_take_down)) {
                $status = '';
				if(!empty($gd->is_take_down)) $status = true;
				else $status = false;

                $gd->is_take_down = $status;
                // $gd->is_take_down = '<span> Status : '.$status.' </span><br />';
                // if (isset($gd->take_down_date)) {
                //     $gd->take_down_date = date("d/M/y", strtotime($gd->take_down_date));
                //     $gd->user .= '<span>Takedown Date: '.$gd->take_down_date.'</span><br />';
                // }
            }

            if (isset($gd->is_report)) {
                $status = '';
				if(!empty($gd->is_report)) $status = true;
				else $status = false;

                $gd->is_report = $status;
                // if (isset($gd->report_date)) {
                //     $gd->report_date = date("d/M/y", strtotime($gd->report_date));
                //     $gd->user .= '<span>Report Date: '.$gd->report_date.'</span><br />';
                // }
            }
		}
		//sleep(3);
		$another = array();
		$this->__jsonDataTable($ddata,$dcount);
	}

	public function reported(){
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
				$sortCol = "image_icon";
				break;
			case 2:
				$sortCol = "title";
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
		$dcount = $this->discussion_model->countAll($nation_code,$keyword);
		$ddata = $this->discussion_model->getReported($nation_code,$page,$pagesize,$sortCol,$sortDir,$keyword);

		foreach($ddata as &$gd){

			if(isset($gd->image_icon)){
				if(strlen($gd->image_icon)<=4) $gd->image_icon = 'media/icon/default-icon.png';
				if($gd->image_icon == 'default.png' || $gd->image_icon== 'default.jpg') $gd->image_icon = 'media/icon/default-icon.png';
				$gd->image_icon = base_url($gd->image_icon);
				$gd->image_icon = '<img src="'.$gd->image_icon.'" class="img-responsive" style="width: 64px;" />';
			}

            if (isset($gd->title)) {
                $title = $gd->title;
                $gd->title = '<span style="font-size: 1.2em; font-weight: bolder;">'.$title.'</span><br />';
                if (isset($gd->cdate)) {
                    $gd->cdate = date("d/M/y", strtotime($gd->cdate));
                    $gd->title .= '<small>Created Date: '.$gd->cdate.'</small><br />';
                }
            }

            if (isset($gd->user)) {
                $user = $gd->user;
                $gd->user = '<span style="font-size: 1.2em;">'.$user.'</span><br />';
				// by Muhammad Sofi - 3 November 2021 10:00
				// remark code
                if (isset($gd->address2)) $gd->user .= '<span>Alamat2: '.$gd->address2.'</span><br />';
            }

            if (isset($gd->is_active)) {
                $status = "";
				if(!empty($gd->is_active)) $status = 'Yes';
				else $status = 'No';
                $gd->is_active = '<span> Is Active : '.$status.' </span><br />';

				if(!empty($gd->is_take_down)) $status = 'Yes';
				else $status = 'No';
				$gd->is_active .= '<span> Is Take Down : '.$status.' </span><br />';

				if(!empty($gd->is_report)) $status = 'Yes';
				else $status = 'No';
				$gd->is_active .= '<span> Is Reported : '.$status.' </span><br />';

				if(!empty($gd->reported_status)) $status = $gd->reported_status;
				else $status = 'Action not taken yet';
				$gd->is_active .= '<span> Report Status : '.$status.' </span><br />';
            }

            if (isset($gd->is_take_down)) {
                $status = "";
				if(!empty($gd->is_take_down)) $status = 'Yes';
				else $status = 'No';

                $gd->is_take_down = '<span> Status : '.$status.' </span><br />';
                if (isset($gd->take_down_date)) {
                    $gd->take_down_date = date("d/M/y", strtotime($gd->take_down_date));
                    $gd->user .= '<span>Takedown Date: '.$gd->take_down_date.'</span><br />';
                }
            }

            if (isset($gd->is_report)) {
                $status = "";
				if(!empty($gd->is_report)) $status = 'Yes';
				else $status = 'No';

                $gd->is_report = '<span> Status : '.$status.' </span><br />';
                if (isset($gd->report_date)) {
                    $gd->report_date = date("d/M/y", strtotime($gd->report_date));
                    $gd->user .= '<span>Report Date: '.$gd->report_date.'</span><br />';
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
		if(!isset($di['nama'])) $di['nama'] = "";
		if(strlen($di['nama'])<=0){
			$this->status = 101;
			$this->message = 'One or more paramater required';
			$this->__json_out($data);
			die();
		}

		//image validation
		$this->__imageValidation("image_icon");

		//start transaction
		$this->discussion_model->trans_start();
		//get last id
		$bkm2_id = $this->discussion_model->getLastId($pengguna->nation_code);

		//build primary key
		$di['nation_code'] = $pengguna->nation_code;
		$di['id'] = $bkm2_id;
		$di['image'] = '';
		$di['image_cover'] = '';
		$di['image_icon'] = '';
		//insert into db
		$res = $this->discussion_model->set($di);
		if($res){
			$this->discussion_model->trans_commit();
			$this->status = 200;
			$this->message = 'Data successfully added';

			$ext = 'jpg';
			$pi = pathinfo($_FILES['image_icon']['name']);
			if(isset($pi['extension'])) $ext = $pi['extension'];
			$target_dir = $this->media_icon;
			$target_file = $this->__slugify($di['nama'])."$pengguna->nation_code-$bkm2_id-".date("His").".$ext";
			$filename = $target_dir.$target_file;
			if(file_exists( SENEROOT.$filename)) unlink(SENEROOT.$filename);
			$mv = move_uploaded_file($_FILES["image_icon"]["tmp_name"],  SENEROOT.$filename);
			$dux = array();
			$dux['image_icon'] = $filename;
			$res2 = $this->discussion_model->update($pengguna->nation_code, $bkm2_id, $dux);
			if($res2){
				$this->status = 200;
				$this->message = 'Success';
				$this->discussion_model->trans_commit();
			}else{
				$this->status = 109;
				$this->message = 'Failed updating image icon';
				$this->discussion_model->trans_rollback();
				//delete file
				if(file_exists( SENEROOT.$filename)) unlink(SENEROOT.$filename);
			}
		}else{
			$this->status = 110;
			$this->message = 'Failed insertin list to database';
			$this->discussion_model->trans_rollback();

		}
		$this->discussion_model->trans_end();
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
		$data = $this->discussion_model->getById($nation_code,$id);
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
		if(!isset($du['nama'])) $du['nama'] = "";
		if(strlen($du['nama'])<=0){
			$this->status = 110;
			$this->message = 'Category name are required';
			$this->__json_out($data);
			die();
		}

		$kategori = $this->discussion_model->getById($nation_code, $id);
		if(!isset($kategori->id)){
			$this->status = 111;
			$this->message = 'Wrong Category ID, please refresh this page';
			$this->__json_out($data);
			die();
		}

		$res = $this->discussion_model->update($pengguna->nation_code, $id, $du);
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

		$kategori = $this->discussion_model->getById($pengguna->nation_code, $id);
		if(!isset($kategori->id)){
			$this->status = 520;
			$this->message = 'ID not found or has been deleted';
			$this->__json_out($data);
			die();
		}
		$res = $this->discussion_model->del($pengguna->nation_code, $id);
		if($res){
			if(strlen($kategori->image_icon)>4){
				$image_icon = SENEROOT.DIRECTORY_SEPARATOR.$kategori->image_icon;
				if(file_exists($image_icon)) unlink($image_icon);
			}
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 902;
			$this->message = 'Cant delete list product';
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
		$kategori = $this->discussion_model->getById($pengguna->nation_code, $id);
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
		$target_file = $this->__slugify($kategori->nama)."-$kategori->nation_code-$kategori->id-".date("His").".$ext";
		$filename = $target_dir.$target_file;

		if(file_exists(SENEROOT.$filename)){
			unlink(SENEROOT.$filename);
			$filename = $this->__slugify($kategori->nama)."-$kategori->nation_code-$kategori->id-".rand(0,999).".$ext";
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
			$this->discussion_model->update($pengguna->nation_code, $kategori->id, $du);
			$this->status = 200;
			$this->message = 'Success';
		}
		$this->__json_out($data);
	}
	
    public function report($id)
    {
        $d = $this->__init();
        $data = array();
        $id = (int) $id;
        if ($id<=0) {
            $this->status = 450;
            $this->message = 'Invalid ID';
            $this->__json_out($data);
            die();
        }
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $list = $this->discussion_model->getById($nation_code, $id);
        if (!isset($list->id)) {
            $this->status = 451;
            $this->message = 'Invalid ID or Post has been deleted';
            $this->__json_out($data);
            die();
        }
        if (!empty($list->is_report)) {
            $this->status = 457;
            $this->message = 'User already Reported';
            $this->__json_out($data);
            die();
        }
        $du = array("is_report"=>true, "report_date"=>"NOW()");
        $res = $this->discussion_model->update($nation_code, $id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->status = 920;
            $this->message = 'Failed change data to database';
        }
        $this->__json_out($data);
    }
	
    public function takedown($id)
    {
        $d = $this->__init();
        $data = array();
        $id = (int) $id;
        if ($id<=0) {
            $this->status = 450;
            $this->message = 'Invalid ID';
            $this->__json_out($data);
            die();
        }
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $list = $this->discussion_model->getById($nation_code, $id);
        if (!isset($list->id)) {
            $this->status = 451;
            $this->message = 'Invalid ID or Post has been deleted';
            $this->__json_out($data);
            die();
        }
        if (!empty($list->is_take_down)) {
            $this->status = 457;
            $this->message = 'User already taken down';
            $this->__json_out($data);
            die();
        }
        $du = array("is_take_down"=>true, "take_down_date"=>"NOW()");
        $res = $this->discussion_model->update($nation_code, $id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';

			//update total_discussion in table c_community
			$this->community_model->updateTotalDiscussion($nation_code, $list->c_community_id, '-', 1);

			//if discussion is a parent, child also deleted
			if($list->parent_c_community_discussion_id == 0){

				$getTotalChildIsActive = $this->discussion_model->countAllChild($nation_code, $list->id, $list->c_community_id);

				//update total_discussion in table c_community
				$this->community_model->updateTotalDiscussion($nation_code, $list->c_community_id, '-', $getTotalChildIsActive);

				$di = array();
				$di['edate'] = 'NOW()';
				$di['is_active'] = 0;
				$this->discussion_model->updateByParentCommunityDiscussionId($nation_code, $list->id, $di);

			}

        } else {
            $this->status = 920;
            $this->message = 'Failed change data to database';
        }
        $this->__json_out($data);
    }
}
