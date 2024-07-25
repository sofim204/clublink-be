<?php
	class Convertspt extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'pointpolicy';
		$this->current_page = 'pointpolicy_convertspt';
		// $this->load("admin/h_ticket_shop_model","htsm");
		$this->load("admin/g_convertspt_model","gcm");
	}
	public function index(){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}

		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

		$pengguna = $data['sess']->admin;
        
		$this->setTitle('SPT Point '.$this->site_suffix_admin);

		$data['kategori'] = array();
		$data['user_role'] = $data['sess']->admin->user_role;

		// ===== START Point Policy ===============
		//declare config var
		$classified = "app_config";
		$code = "C19";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->gcm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "app_config";
		$code = "C20";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->gcm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "app_config";
		$code = "C21";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->gcm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		//get app_config
		$data[$classified] = array();		
		$leaderboardpoint = $this->gcm->getByClassified($pengguna->nation_code, $classified);
		foreach($leaderboardpoint as $ac){
			$data[$classified][$ac->classified.'_remark_'.$ac->code] = $ac->remark;
		}
		// ===== END Point Policy ===============


		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("pointpolicy/convertspt/home_modal",$data);
		$this->putThemeContent("pointpolicy/convertspt/home",$data);
		$this->putJsContent("pointpolicy/convertspt/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
