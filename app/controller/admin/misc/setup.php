<?php
	class Setup extends JI_Controller {

	public function __construct() {
    	parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'misc';
		$this->current_page = 'misc_setup';
		$this->load("admin/a_bank_model","abm");
		$this->load("admin/common_code_model","ccm");
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
		$classified = "app_config";
		$code = "C0";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->ccm->getByClassifiedAndCode($nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = (int) $config->remark;

		//get app_config
		$data[$classified] = array();
		$app_configgs = $this->ccm->getByClassified($nation_code, $classified);
		foreach($app_configgs as $acg){
			$data[$classified][$acg->classified.'_remark_'.$acg->code] = $acg->remark;
		}

		$data['bank_count'] = $this->abm->count($nation_code);
		$data['bank_list'] = $this->abm->get($nation_code);

		//get product_fee config
		$classified = 'product_fee';
		$data[$classified] = array();
		$product_fees = $this->ccm->getByClassified($nation_code, $classified);
		foreach($product_fees as $pf){
			$data[$classified][$pf->classified.'_remark_'.$pf->code] = $pf->remark;
		}
		//$this->debug($data[$classified]);
		//die();

		// by Muhammad Sofi 2 February 2022 09:24 | add Maintenance App configuration
		//declare config var
		$classified = "app_config";
		$code = "C2";
		$data['fs_'.$classified.'_remark_'.$code] = 0;
		$config = $this->ccm->getByClassifiedAndCode($nation_code,$classified,$code);
		if(isset($config->remark)) $data['fs_'.$classified.'_remark_'.$code] = $config->remark;

		//get app_config
		$data[$classified] = array();
		$app_configs = $this->ccm->getByClassified($nation_code, $classified);
		foreach($app_configs as $ac){
			$data[$classified][$ac->classified.'_remark_'.$ac->code] = $ac->remark;
		}

		$this->setTitle("Application Config / Setup $this->site_suffix_admin");
		$this->putThemeContent("misc/setup/home_modal",$data);
		$this->putThemeContent("misc/setup/home",$data);
		$this->putJsReady("misc/setup/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
