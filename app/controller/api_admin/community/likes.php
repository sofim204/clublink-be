<?php
class Likes extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->lib("seme_purifier");
		$this->load("api_admin/c_community_likes_model",'likes_model');
		$this->current_parent = 'community';
		$this->current_page = 'community_likes';
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
		$dcount = $this->likes_model->countAll($nation_code,$keyword);
		$ddata = $this->likes_model->getAll($nation_code,$page,$pagesize,$sortCol,$sortDir,$keyword);

		foreach($ddata as &$gd){

			if(isset($gd->image_icon)){
				if(strlen($gd->image_icon)<=4) $gd->image_icon = 'media/icon/default-icon.png';
				if($gd->image_icon == 'default.png' || $gd->image_icon== 'default.jpg') $gd->image_icon = 'media/icon/default-icon.png';
				$gd->image_icon = base_url($gd->image_icon);
				$gd->image_icon = '<img src="'.$gd->image_icon.'" class="img-responsive" style="width: 64px;" />';
			}

            if (isset($gd->type)) {
                $type = $gd->type;
                $gd->type = '<span style="font-size: 1.2em; font-weight: bolder;">'.$type.'</span><br />';
                if (isset($gd->cdate)) {
                    $gd->cdate = date("d/M/y", strtotime($gd->cdate));
                    $gd->type .= '<small>Created Date: '.$gd->cdate.'</small><br />';
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
				if($gd->is_active) $status = 'Active';
				else $status = 'Inactive';
                $gd->is_active = '<span>'.$status.'</span><br />';
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
		$dcount = $this->likes_model->countAll($nation_code,$keyword);
		$ddata = $this->likes_model->getReported($nation_code,$page,$pagesize,$sortCol,$sortDir,$keyword);

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
				if(!empty($gd->is_active)) $status = 'Active';
				else $status = 'Inactive';
                $gd->is_active = '<span>'.$status.'</span><br />';

				if(!empty($gd->is_report)) $status = 'Yes';
				else $status = 'No';
				$gd->is_active .= '<span> Is Reported : '.$status.' </span><br />';

				if(!empty($gd->is_take_down)) $status = 'Yes';
				else $status = 'No';
				$gd->is_active .= '<span> Is Take Down : '.$status.' </span><br />';
            }
		}
		//sleep(3);
		$another = array();
		$this->__jsonDataTable($ddata,$dcount);
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
		$data = $this->likes_model->getById($nation_code,$id);
		if(!isset($data->id)){
			$data = new stdClass();
			$this->status = 441;
			$this->message = 'No Data';
			$this->__json_out($data);
			die();
		}
		$this->__json_out($data);
	}
	
    public function report($report, $id)
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

        $list = $this->likes_model->getById($nation_code, $id);
        if (!isset($list->id)) {
            $this->status = 451;
            $this->message = 'Invalid ID or Post has been deleted';
            $this->__json_out($data);
            die();
        }
        if (!empty($list->reported_status)) {
            $this->status = 457;
            $this->message = 'User already ignored/taken down';
            $this->__json_out($data);
            die();
        }
        $du = array("reported_status"=>$report);
        $res = $this->likes_model->update($nation_code, $id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->status = 920;
            $this->message = 'Failed change data to database';
        }
        $this->__json_out($data);
    }
}
