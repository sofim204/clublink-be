<?php
	class Wallet extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'pointpolicy';
		$this->current_page = 'pointpolicy_wallet';
		// $this->load("admin/h_ticket_shop_model","htsm");
		$this->load("admin/g_wallet_model","gwm");
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
        
		$this->setTitle('Wallet Point '.$this->site_suffix_admin);

		$data['kategori'] = array();
		$data['user_role'] = $data['sess']->admin->user_role;

		// ===== START Point Policy ===============
		//declare config var
		$classified = "leaderboard_point";
		$code = "E31";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->gwm->getByClassifiedAndCode($pengguna->nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		//get app_config
		$data[$classified] = array();		
		$leaderboardpoint = $this->gwm->getByClassified($pengguna->nation_code, $classified);
		foreach($leaderboardpoint as $ac){
			$data[$classified][$ac->classified.'_remark_'.$ac->code] = $ac->remark;
		}
		// ===== END Point Policy ===============


		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("pointpolicy/wallet/home_modal",$data);
		$this->putThemeContent("pointpolicy/wallet/home",$data);
		$this->putJsContent("pointpolicy/wallet/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
