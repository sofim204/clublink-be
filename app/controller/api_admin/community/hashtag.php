<?php
class Hashtag extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->lib("seme_purifier");
		$this->load("api_admin/c_community_hashtag_model",'hashtag_model');
		$this->current_parent = 'community';
		$this->current_page = 'community_hashtag';
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


		$sortCol = "date";
		$sortDir = strtoupper($sSortDir_0);
		if(empty($sortDir)) $sortDir = "DESC";
		if(strtolower($sortDir) != "desc"){
			$sortDir = "ASC";
		}

		switch($iSortCol_0){
            case 0:
				$sortCol = "no";
				break;
			case 1:
				$sortCol = "id";
				break;
			case 2:
				$sortCol = "name";
				break;
			case 3:
				$sortCol = "cdate";
				break;
			case 4:
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
		$dcount = $this->hashtag_model->countAll($nation_code,$keyword);
		$ddata = $this->hashtag_model->getAll($nation_code,$page,$pagesize,$sortCol,$sortDir,$keyword);

		foreach($ddata as &$gd){

			if(isset($gd->is_active)){
                $status = "";
				if(!empty($gd->is_active)){
                    $status = '<label class="label label-success">Yes</label>';
				}else{
                    $status = '<label class="label label-danger">No (Deleted)</label>';
				}
                $gd->is_active = '<center><span>'.$status.'</span></center><div style="margin-bottom: 3px;"></div>';
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
		// foreach($di as $key=>&$val){
		// 	if(is_string($val)){
		// 		if($key == 'deskripsi'){
		// 			$val = $this->seme_purifier->richtext($val);
		// 		}else{
		// 			$val = $this->__f($val);
		// 		}
		// 	}
		// }

		// ====== check $_POST ==========================
		if ($_POST['name'] == '' || $_POST['name'] == null){
			$this->status = 104;
			$this->message = 'Name must be filled';
			$this->__json_out($data);
			die();
		}
        // ====== END check $_POST ==========================

		//start transaction
		$this->hashtag_model->trans_start();
		
        $hashtag_id = $this->GUIDv4();

        $di = [
            'nation_code' => $pengguna->nation_code,
		    'id' => $hashtag_id,
            'name' => $this->__checkHashtag($_POST['name']),
            'cdate' => date('Y-m-d H:i:s'),
            'is_active' => $_POST['is_active']
        ];
		//insert into db
		$res = $this->hashtag_model->set($di);
		if($res){
			$this->status = 200;
            $this->message = 'Success';
            $this->hashtag_model->trans_commit();
		}else{
			$this->status = 110;
			$this->message = 'Failed insertin hashtag to database';
			$this->hashtag_model->trans_rollback();

		}
		$this->hashtag_model->trans_end();
		$this->__json_out($data);
	}

    public function detail($id)
    {
		// $id = (int) $id;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($id)) {
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

        $data = $this->hashtag_model->getById($nation_code, $id);
        if (!isset($data->id)) {
            $this->status = 451;
            $this->message = 'Invalid ID or ID has been deleted';
            $this->__json_out($data);
            die();
        }

        $this->__json_out($data);
    }

	public function hapus($id){
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

		$kategori = $this->hashtag_model->getById($pengguna->nation_code, $id);
		if(!isset($kategori->id)){
			$this->status = 520;
			$this->message = 'ID not found or has been deleted';
			$this->__json_out($data);
			die();
		}
        $du = [
            'is_active' => 0
        ];
		$res = $this->hashtag_model->update($pengguna->nation_code, $id, $du);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 902;
			$this->message = 'Cant delete hashtag';
		}
		$this->__json_out($data);
	}

    private function __checkHashtag($input_string) { 
        return "#".str_replace(' ','',str_replace("#","",$input_string));
    }
}
