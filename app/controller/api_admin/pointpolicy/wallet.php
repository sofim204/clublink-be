<?php
class Wallet extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->lib("seme_purifier");
		// $this->load("api_admin/h_ticket_shop_model",'htsm');
		$this->current_parent = 'pointpolicy';
		$this->current_page = 'pointpolicy_wallet';
		$this->load("api_admin/g_wallet_model","gwm");
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
	}


	// ====== START Point Policy ==================
	// by Yopie Hidayat 25 Januari 2024 13:15 | 

	// ======= ADS - Get SPT when create password and email in wallet ==============
	public function get_spt_create_email_password() {
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
		$id = 157;
		$code = "E31";
		$classified = "leaderboard_point";
		$codename = 'ADS - Get SPT when create password and email in wallet';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->gwm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->gwm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->gwm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	// ======= END ADS - Get SPT when create password and email in wallet ==============
	
	// ====== END Point Policy ==================

}
