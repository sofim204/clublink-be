<?php
	class Point extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'band';
		$this->current_page = 'band_point';
		// $this->load("admin/h_ticket_shop_model","htsm");
		$this->load("admin/i_group_point_policy_model","igppm");
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
        
		$this->setTitle('Club Point '.$this->site_suffix_admin);

		$data['kategori'] = array();
		$data['user_role'] = $data['sess']->admin->user_role;

		// ===== START Point Policy ===============
		//declare config var
		$classified = "leaderboard_point";
		$code = "E17";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->igppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "leaderboard_point";
		$code = "E18";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->igppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "leaderboard_point";
		$code = "E19";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->igppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "leaderboard_point";
		$code = "E20";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->igppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "leaderboard_point";
		$code = "E21";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->igppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "leaderboard_point";
		$code = "E22";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->igppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "leaderboard_point";
		$code = "E23";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->igppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "leaderboard_point";
		$code = "E24";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->igppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "leaderboard_point";
		$code = "E25";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->igppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "leaderboard_point";
		$code = "E26";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->igppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "leaderboard_point";
		$code = "E27";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->igppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "leaderboard_point";
		$code = "E28";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->igppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "leaderboard_point";
		$code = "E29";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->igppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "leaderboard_point";
		$code = "E30";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->igppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		// Club - Owner group commission in % if member create post (text, photo, video)
		$classified = "leaderboard_point";
		$code = "E32";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->igppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		// Club - Create club limit
		$classified = "leaderboard_point";
		$code = "E33";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->igppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		//get app_config
		$data[$classified] = array();		
		$leaderboardpoint = $this->igppm->getByClassified($pengguna->nation_code, $classified);
		foreach($leaderboardpoint as $ac){
			$data[$classified][$ac->classified.'_remark_'.$ac->code] = $ac->remark;
		}
		// ===== END Point Policy ===============


		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("band/point/home_modal",$data);
		$this->putThemeContent("band/point/home",$data);
		$this->putJsContent("band/point/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
