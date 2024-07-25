<?php
class Buyandsell extends JI_Controller {

	public function __construct() {
    	parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'pointpolicy';
		$this->current_page = 'pointpolicy_buyandsell';
		$this->load("admin/g_pointpolicybuyandsell_model","gppbm_model");
	}

	public function index() {
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}

		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}
		
    	$nation_code = $data['sess']->admin->nation_code;

		//declare config var
		$classified = "leaderboard_point";
		$code = "EE";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->gppbm_model->getByClassifiedAndCode($nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		//get app_config
		$data[$classified] = array();
		$leaderboardpoint = $this->gppbm_model->getByClassified($nation_code, $classified);
		foreach($leaderboardpoint as $ac){
			$data[$classified][$ac->classified.'_remark_'.$ac->code] = $ac->remark;
		}

		$this->setTitle("Point Policy | Buy and Sell".$this->site_suffix_admin);
		$this->putThemeContent("pointpolicy/buyandsell/home_modal", $data);
		$this->putThemeContent("pointpolicy/buyandsell/home", $data);
		$this->putJsContent("pointpolicy/buyandsell/home_bottom", $data);
		$this->loadLayout('col-2-left', $data);
		$this->render();
	}
}