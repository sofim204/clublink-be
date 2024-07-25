<?php
	class Ticket_Shop extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'game';
		$this->current_page = 'game_ticket_shop';
		$this->load("admin/h_ticket_shop_model","htsm");
		$this->load("admin/h_game_point_policy_model","hgppm");
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
        
		$this->setTitle('Ticket Shop '.$this->site_suffix_admin);

		$data['kategori'] = array();
		$data['user_role'] = $data['sess']->admin->user_role;

		// ===== START Point Policy ===============
		//declare config var
		$classified = "leaderboard_point";
		$code = "E11";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->hgppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "leaderboard_point";
		$code = "E12";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->hgppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "game";
		$code = "I1";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->hgppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "game";
		$code = "I2";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->hgppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "game";
		$code = "I3";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->hgppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "game";
		$code = "I4";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->hgppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "app_config";
		$code = "C17";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->hgppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "app_config";
		$code = "C18";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->hgppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		//get app_config
		$data[$classified] = array();		
		$leaderboardpoint = $this->hgppm->getByClassified($pengguna->nation_code, $classified);
		foreach($leaderboardpoint as $ac){
			$data[$classified][$ac->classified.'_remark_'.$ac->code] = $ac->remark;
		}

		$classified = "game";
		$code = "I5";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->hgppm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;
		// ===== END Point Policy ===============


		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("game/ticket_shop/home_modal",$data);
		$this->putThemeContent("game/ticket_shop/home",$data);
		$this->putJsContent("game/ticket_shop/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
	public function tambah(){
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
		$this->setTitle('Add Ticket Shop '.$this->site_suffix_admin);

		//$this->debug($cats);
		//die();

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("game/ticket_shop/tambah_modal",$data);
		$this->putThemeContent("game/ticket_shop/tambah",$data);


		$this->putJsContent("game/ticket_shop/tambah_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function edit($id){
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

		$game_data = $this->htsm->getById($pengguna->nation_code, $id);
		// $this->setTitle('Edit '.html_entity_decode($game_data->earned_ticket).' Earned Ticket'.$this->site_suffix_admin);
		$this->setTitle('Edit Earned Ticket'.$this->site_suffix_admin);
		
		// START by Yopie Hidayat 21 Juni 2023 14:56 | read special character like &#38 ;
		if(isset($game_data->name)){
			$game_data->name = html_entity_decode($game_data->name);
		}
		// END by Yopie Hidayat 21 Juni 2023 14:56 | read special character like &#38 ;

		//$this->debug($cats);
		//die();
		$data['game_data'] = $game_data;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("game/ticket_shop/edit_modal",$data);
		$this->putThemeContent("game/ticket_shop/edit",$data);
		$this->putJsContent("game/ticket_shop/edit_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
