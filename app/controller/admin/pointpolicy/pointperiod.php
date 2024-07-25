<?php
class Pointperiod extends JI_Controller {

	public function __construct() {
    	parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'pointpolicy';
		$this->current_page = 'pointpolicy_pointperiod';
		$this->load("admin/g_pointpolicypointperiod_model","gpppm_model");
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
		$code = "EL";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->gpppm_model->getByClassifiedAndCode($nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		//get app_config
		$data[$classified] = array();
		$leaderboardpoint = $this->gpppm_model->getByClassified($nation_code, $classified);
		foreach($leaderboardpoint as $ac){
			$data[$classified][$ac->classified.'_remark_'.$ac->code] = $ac->remark;
		}

		$this->setTitle("Point Policy | Point Period".$this->site_suffix_admin);
		$this->putThemeContent("pointpolicy/pointperiod/home_modal", $data);
		$this->putThemeContent("pointpolicy/pointperiod/home", $data);
		$this->putJsContent("pointpolicy/pointperiod/home_bottom", $data);
		$this->loadLayout('col-2-left', $data);
		$this->render();
	}
}