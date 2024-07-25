<?php
class Setup extends JI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load("api_admin/a_bank_model",'abm');
		$this->load("api_admin/a_bank_trfcost_model",'abtcm');
		$this->load("api_admin/common_code_model",'ccm');
	}

	public function index(){

	}

    private function __uploadFoto($temp, $id="")
    {
        //building path target
        $fldr = $this->media_firstlogin;
        $folder = SENEROOT.DIRECTORY_SEPARATOR.$fldr.DIRECTORY_SEPARATOR;
        $folder = str_replace('\\', '/', $folder);
        $folder = str_replace('//', '/', $folder);
        $ifol = realpath($folder);

        //check folder
        if (!$ifol) {
            mkdir($folder);
        } //create folder
        $ifol = realpath($folder); //get current realpath

        reset($_FILES);
        $temp = current($temp);
        if (is_uploaded_file($temp['tmp_name'])) {
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                // same-origin requests won't set an origin. If the origin is set, it must be valid.
                header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
            }
            header('Access-Control-Allow-Credentials: true');
            header('P3P: CP="There is no P3P policy."');

            // by Muhammad Sofi 23 January 2022 21:58 | comment code sanitize file input
            // // Sanitize input
            // if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
            //     header("HTTP/1.0 500 Invalid file name.");
            //     return 0;
            // }
            if (mime_content_type($temp['tmp_name']) == 'image/webp') {
                header("HTTP/1.0 500 Unsupported file format");
                return 0;
            }
            // Verify extension
            $ext = strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, array("jpg", "jpeg", "png", "gif"))) {
                header("HTTP/1.0 500 Invalid extension.");
                return 0;
            }
            if ($ext == 'jpeg') {
                $ext = "jpg";
            }

            // Create magento style media directory
            $temp['name'] = 'sharesellon-'.date('dmy').rand(10,1000).'.'.$ext;

            $name  = $temp['name'];
            $id = (int) $id;
            if ($id>0) {
                $name = $id.'.'.$ext;
            }
            $name1 = date("Y");
            $name2 = date("m");

            //building directory structure
            if (PHP_OS == "WINNT") {
                if (!is_dir($ifol)) {
                    mkdir($ifol);
                }
                $ifol = $ifol.DIRECTORY_SEPARATOR.$name1.DIRECTORY_SEPARATOR;
                if (!is_dir($ifol)) {
                    mkdir($ifol);
                }
                $ifol = $ifol.DIRECTORY_SEPARATOR.$name2.DIRECTORY_SEPARATOR;
                if (!is_dir($ifol)) {
                    mkdir($ifol);
                }
            } else {
                if (!is_dir($ifol)) {
                    mkdir($ifol, 0775);
                }
                $ifol = $ifol.DIRECTORY_SEPARATOR.$name1.DIRECTORY_SEPARATOR;
                if (!is_dir($ifol)) {
                    mkdir($ifol, 0775);
                }
                $ifol = $ifol.DIRECTORY_SEPARATOR.$name2.DIRECTORY_SEPARATOR;
                if (!is_dir($ifol)) {
                    mkdir($ifol, 0775);
                }
            }

            // Accept upload if there was no origin, or if it is an accepted origin
            $filetowrite = $ifol . $name;
            $filetowrite = str_replace('//', '/', $filetowrite);

            if (file_exists($filetowrite) && is_file($filetowrite)) {
                unlink($filetowrite);
                $name = '';
                $rand = substr(md5(microtime()), rand(0, 26), 5);
                $name = 'promo-'.$rand.'.'.$ext;
                if ($id>0) {
                    $name = $id.'-'.$rand.'.'.$ext;
                }
                $filetowrite = $ifol.$name;
                $filetowrite = str_replace('//', '/', $filetowrite);
                if (file_exists($filetowrite) && is_file($filetowrite)) {
                    unlink($filetowrite);
                }
            }
            move_uploaded_file($temp['tmp_name'], $filetowrite);
            if (file_exists($filetowrite)) {
                return $fldr."/".$name1."/".$name2."/".$name;
            } else {
                return 0;
            }
        } else {
            // Notify editor that the upload failed
            //header("HTTP/1.0 500 Server Error");
            return 0;
        }
    }

	public function app_config(){
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
		$id = 60;
		$code = "C0";
		$classified = "app_config";
		$codename = 'A Bank ID configuration';
		$remark = (int) $this->input->post($classified."_remark_$code");
		if($remark<=0) $remark = 0;

		//get current config
		$config = $this->ccm->getByClassifiedAndCode($nation_code,$classified,$code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ccm->update($nation_code,$id,$du);
		}else{
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->ccm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function product_fee(){
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
		
		//declare config var for admin fee type
		$id = 56;
		$code = "F6";
		$classified = "product_fee";
		$codename = 'VAT';
		$remark = (int) $this->input->post($classified."_remark_$code");
		if($remark<=0) $remark = 7;

		//get config data
		$config = $this->ccm->getByClassifiedAndCode($nation_code,$classified,$code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ccm->update($nation_code,$id,$du);
		}else{
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->ccm->set($di);
		}
		
		//declare config var for admin fee type
		$id = 57;
		$code = "F7";
		$classified = "product_fee";
		$codename = 'selling fee percent';
		$remark = (int) $this->input->post($classified."_remark_$code");
		if($remark<=0) $remark = 10;

		//get config data
		$config = $this->ccm->getByClassifiedAndCode($nation_code,$classified,$code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ccm->update($nation_code,$id,$du);
		}else{
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->ccm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	// by Muhammad Sofi 2 February 2022 09:24 | add Maintenance App configuration
	public function app_config_maintenance() {
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
		$id = 62;
		$code = "C2";
		$classified = "app_config";
		$codename = 'maintenance';
		$remark = $this->input->post($classified."_remark_$code");
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->ccm->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->ccm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->ccm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function protection_product(){
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
		$id = 63;
		$code = "C3";
		$classified = "app_config";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ccm->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ccm->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function default_setting_gnb(){
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
		$id = 65;
		$code = "C5";
		$classified = "app_config";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ccm->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ccm->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function total_allowed_account_in_same_device(){
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
		$id = 66;
		$code = "C6";
		$classified = "app_config";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ccm->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ccm->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	//START by Donny Dennison 2022 - 25 december 2022 9:36
	//add response wallet_active in api pelanggan/check_version_mobile_app
	public function app_config_wallet_active() {
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
		$id = 67;
		$code = "C7";
		$classified = "app_config";
		$codename = 'wallet active';
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ccm->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->ccm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->ccm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
	//END by Donny Dennison - 25 december 2022 9:36
	//add response wallet_active in api pelanggan/check_version_mobile_app

	public function total_allowed_account_in_same_ip(){
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
		$id = 68;
		$code = "C8";
		$classified = "app_config";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ccm->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $remark;
			$this->ccm->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	// by Muhammad Sofi 20 January 2022 16:06 | bug fixing when change image thumbnail
    public function share_sellon_image()
    {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access, please login';
            header("HTTP/1.0 400 Unauthorized access, please login");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

                
        //files upload
        $fi = $_FILES;
        $dataImg = $this->media_firstlogin.'default.png';
        if ($fi["app_config_remark_C9"]["size"]>5120000) {
            $this->status = 1020;
            $this->message = 'Image file size too big, please try another image size';
            $this->__json_out($data);
            die();
        } elseif ($fi["app_config_remark_C9"]["size"] > 0 && $fi["app_config_remark_C9"]["size"] <= 5120000) {
            $ext = strtolower(pathinfo($fi["app_config_remark_C9"]['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, array("jpg", "jpeg", "png", "gif"))) {
                $this->status = 1026;
                $this->message = 'Invalid image file extension, only allowed JPG and PNG file format';
                $this->__json_out($data);
                die();
            }
            if (mime_content_type($fi["app_config_remark_C9"]["tmp_name"]) == 'image/webp') {
                $this->status = 1021;
                $this->message = 'WebP image file format currently unsupported on this system';
                $this->__json_out($data);
                die();
            }
            $dataImg = $this->__uploadFoto($fi);
        }
		$id = 69;
		$code = "C9";
		$classified = "app_config";
		$remark = $this->input->post($classified."_remark_$code");

		//get current config
		$config = $this->ccm->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)){
			$du = array();
			$du['remark'] = $dataImg;
			$this->ccm->update($nation_code,$id,$du);
		} else {}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";

        $this->__json_out($data);
    }

	// by Yopie Hidayat 22 May 2023 14:15 | add Facebook Login
	public function facebook_login() {
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
		$id = 664;
		$code = "C14";
		$classified = "app_config";
		$codename = 'Facebook Login';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->ccm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->ccm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->ccm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	// by Yopie Hidayat 20 July 2023 14:15 | add show_ads_on_every_page
	public function show_ads_on_every_page() {
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
		$id = 665;
		$code = "C15";
		$classified = "app_config";
		$codename = 'show ads at array X on every page(product and community)';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->ccm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->ccm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->ccm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	// by Yopie Hidayat 20 July 2023 14:15 | add show_ads_after_play_game
	public function show_ads_after_play_game() {
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
		$id = 666;
		$code = "C16";
		$classified = "app_config";
		$codename = 'show ads after play game X times';
		$remark = $this->input->post($classified."_remark_$code");		
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->ccm->getByClassifiedAndCode($nation_code, $classified, $code);		
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->ccm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->ccm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function app_config_singapore_server() {
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
		$id = 673;
		$code = "C23";
		$classified = "app_config";
		$codename = 'singapore server status';
		$remark = $this->input->post($classified."_remark_$code");
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->ccm->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->ccm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->ccm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function register_via_email_need_verif_phone() {
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
		$id = 674;
		$code = "C24";
		$classified = "app_config";
		$codename = 'register via email need phone verification';
		$remark = $this->input->post($classified."_remark_$code");
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->ccm->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->ccm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->ccm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function register_via_phone_number_need_verif_phone() {
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
		$id = 675;
		$code = "C25";
		$classified = "app_config";
		$codename = 'register via phone number need phone verification';
		$remark = $this->input->post($classified."_remark_$code");
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->ccm->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->ccm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->ccm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}

	public function dynamic_setting_wallet_access() {
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
		$id = 676;
		$code = "C26";
		$classified = "app_config";
		$codename = 'wallet access';
		$remark = $this->input->post($classified."_remark_$code");
		// if($remark<=0) $remark = 0;

		//get current config
		$config = $this->ccm->getByClassifiedAndCode($nation_code, $classified, $code);
		if(isset($config->remark)) {
			$du = array();
			$du['remark'] = $remark;
			$this->ccm->update($nation_code, $id, $du);
		} else {
			$di = array();
			$di['nation_code'] = $nation_code;
			$di['id'] = $id;
			$di['classified'] = $classified;
			$di['code'] = $code;
			$di['codename'] = $codename;
			$di['remark'] = $remark;
			$this->ccm->set($di);
		}

		$this->status = 200;
		$this->message = "Configuration has been saved successfully";
		$this->__json_out($data);
	}
}
