<?php
class Maintenance extends JI_Controller {

	public function __construct() {
    	parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'misc';
		$this->current_page = 'misc_maintenance';
		$this->load("admin/g_maintenance_model", 'maintenance_model');
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
		
    	$nation_code = $data['sess']->admin->nation_code;

		//declare config var
		$classified = "app_config";
		$code = "C2";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->maintenance_model->getByClassifiedAndCode($nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		$classified = "app_config";
		$code = "C23";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->maintenance_model->getByClassifiedAndCode($nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		//get app_config
		$data[$classified] = array();
		$app_configs = $this->maintenance_model->getByClassified($nation_code, $classified);
		foreach($app_configs as $ac){
			$data[$classified][$ac->classified.'_remark_'.$ac->code] = $ac->remark;
		}

		$this->setTitle("Maintenance App | $this->site_suffix_admin");
		// $this->putThemeContent("misc/maintenance/home_modal", $data);
		$this->putThemeContent("misc/maintenance/home", $data);
		$this->putJsContent("misc/maintenance/home_bottom", $data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}