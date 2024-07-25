<?php
class Ticket_Shop extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->lib("seme_purifier");
		$this->load("api_admin/h_ticket_shop_model",'htsm');
		$this->current_parent = 'game';
		$this->current_page = 'game_ticket_shop';
		$this->load("api_admin/h_game_point_policy_model","hgppm");
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
				$sortCol = "earned_ticket";
				break;
			case 3:
				$sortCol = "cdate";
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
		$dcount = $this->htsm->countAll($nation_code,$keyword);
		$ddata = $this->htsm->getAll($nation_code,$page,$pagesize,$sortCol,$sortDir,$keyword);

		foreach($ddata as &$gd){

			if(isset($gd->icon)){
				// if(strlen($gd->icon)<=4) $gd->icon = 'media/icon/default-icon.png';
				// if($gd->icon == 'default.png' || $gd->icon== 'default.jpg') $gd->icon = 'media/icon/default-icon.png';
				// $gd->icon = base_url($gd->icon);
				if(strlen($gd->icon) > 4) {
					$gd->icon = '<img src="'.base_url($gd->icon).'" class="img-responsive" style="width: 64px;" />';
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
		if ($_POST['earned_ticket'] == '' || $_POST['earned_ticket'] == null){
			$this->status = 104;
			$this->message = 'Earned Ticket must be filled';
			$this->__json_out($data);
			die();
		}
		if ($_POST['price'] == '' || $_POST['price'] == null){
			$this->status = 104;
			$this->message = 'Price must be filled';
			$this->__json_out($data);
			die();
		}
        // ========= End Check $_POST =========================
        

		//start transaction
		$this->htsm->trans_start();
		
        $ticket_shop_id = $this->GUIDv4();

        $di = [
            'nation_code' => $pengguna->nation_code,
		    'id' => $ticket_shop_id,
            'earned_ticket' => $_POST['earned_ticket'],
            'price' => $_POST['price'],
            'is_active' => $_POST['is_active']
        ];
		//insert into db
		$res = $this->htsm->set($di);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
			$this->htsm->trans_commit();
		}else{
			$this->status = 110;
			$this->message = 'Failed insertin game to database';
			$this->htsm->trans_rollback();

		}
		$this->htsm->trans_end();
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
		$data = $this->htsm->getById($nation_code,$id);
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

		$gd = $this->htsm->getById($nation_code, $id);
		if(!isset($gd->id)){
			$this->status = 111;
			$this->message = 'Wrong ID, please refresh this page';
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
		if ($_POST['earned_ticket'] == '' || $_POST['earned_ticket'] == null){
			$this->status = 104;
			$this->message = 'Earned Ticket must be filled';
			$this->__json_out($data);
			die();
		}
		if ($_POST['price'] == '' || $_POST['price'] == null){
			$this->status = 104;
			$this->message = 'Price must be filled';
			$this->__json_out($data);
			die();
		}
        // ========= End Check $_POST =========================		

        $du = [
            'nation_code' => $pengguna->nation_code,
		    'id' => $id,
            'earned_ticket' => $_POST['earned_ticket'],
            'price' => $_POST['price'],
            'is_active' => $_POST['is_active']
        ];		

		$res = $this->htsm->update($nation_code, $id, $du);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 901;
			$this->message = 'Cant update ticket shop to database';
		}
		$this->__json_out($data);
	}

	public function hapus($id){
		$d = $this->__init();
		$data = array();

        //admin
		$pengguna = $d['sess']->admin;

		$gd = $this->htsm->getById($pengguna->nation_code, $id);
		if(!isset($gd->id)){
			$this->status = 111;
			$this->message = 'Wrong ID, please refresh this page';
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

		$ticket_shop_data = $this->htsm->getById($pengguna->nation_code, $id);
		if(!isset($ticket_shop_data->id)){
			$this->status = 520;
			$this->message = 'ID not found or has been deleted';
			$this->__json_out($data);
			die();
		}
		$res = $this->htsm->del($pengguna->nation_code, $id);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 902;
			$this->message = 'Cant delete ticket shop';
		}
		$this->__json_out($data);
	}


	// ====== START Point Policy ==================
	// by Yopie Hidayat 22 Juni 2023 13:15 | add API SignUp Type
	public function every_like_ticket() {
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Authorization required");
			$this->__json_out($data);
			die();
		}

		$nation_code = $d['sess']->admin->nation_code;

		//declare config variable
		$id = 136;
		$code = "E11";
		$classified = "leaderboard_point";
		$codename = 'every X like/dislike can get ticket';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->hgppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->hgppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->hgppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	// by Yopie Hidayat 22 Juni 2023 13:15 | add API SignUp Type
	public function total_get_ticket() {
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Authorization required");
			$this->__json_out($data);
			die();
		}

		$nation_code = $d['sess']->admin->nation_code;

		//declare config variable
		$id = 137;
		$code = "E12";
		$classified = "leaderboard_point";
		$codename = 'total ticket get from X like/dislike';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->hgppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->hgppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->hgppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}		
	public function max_ticket_get_like_dislike() {
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Authorization required");
			$this->__json_out($data);
			die();
		}

		$nation_code = $d['sess']->admin->nation_code;

		//declare config variable
		$id = 301;
		$code = "I2";
		$classified = "game";
		$codename = 'max ticket get from like/dislike';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->hgppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->hgppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->hgppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	// ======= free ticket for rock paper scissors ==============
	public function total_free_ticket_rock_paper_scissors() {
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Authorization required");
			$this->__json_out($data);
			die();
		}

		$nation_code = $d['sess']->admin->nation_code;

		//declare config variable
		$id = 300;
		$code = "I1";
		$classified = "game";
		$codename = 'total free ticket';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->hgppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->hgppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->hgppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	// ======= END free ticket for rock paper scissors ==============

	// ======= maintenance for rock paper scissors ==============
	public function maintenance_rock_paper_scissors() {
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Authorization required");
			$this->__json_out($data);
			die();
		}

		$nation_code = $d['sess']->admin->nation_code;

		//declare config variable
		$id = 667;
		$code = "C17";
		$classified = "app_config";
		$codename = 'maintenance game rock paper scissors';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->hgppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->hgppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->hgppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	// ======= END maintenance for rock paper scissors ==============

	// ======= free ticket for shooting fire ==============
	public function total_free_ticket_shooting_fire() {
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Authorization required");
			$this->__json_out($data);
			die();
		}

		$nation_code = $d['sess']->admin->nation_code;

		//declare config variable
		$id = 302;
		$code = "I3";
		$classified = "game";
		$codename = 'total free ticket shooting fire';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->hgppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->hgppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->hgppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	// ======= END free ticket for shooting fire ==============

	// ======= maintenance for shooting fire ==============
	public function maintenance_shooting_fire() {
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Authorization required");
			$this->__json_out($data);
			die();
		}

		$nation_code = $d['sess']->admin->nation_code;

		//declare config variable
		$id = 668;
		$code = "C18";
		$classified = "app_config";
		$codename = 'maintenance game shooting fire';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->hgppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->hgppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->hgppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	// ======= END maintenance for shooting fire ==============

	// ======= total free ticket sellon match ==============
	public function total_free_ticket_sellon_match() {
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Authorization required");
			$this->__json_out($data);
			die();
		}

		$nation_code = $d['sess']->admin->nation_code;

		//declare config variable
		$id = 303;
		$code = "I4";
		$classified = "game";
		$codename = 'total free ticket sellon match';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->hgppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->hgppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->hgppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	// ======= END total free ticket sellon match ==============
	
	public function max_win_rock_paper_scissors_per_day() {
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Authorization required");
			$this->__json_out($data);
			die();
		}

		$nation_code = $d['sess']->admin->nation_code;

		//declare config variable
		$id = 304;
		$code = "I5";
		$classified = "game";
		$codename = 'max win rock paper scissors per day(x/times)';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->hgppm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->hgppm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->hgppm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	
	// ====== END Point Policy ==================

}
