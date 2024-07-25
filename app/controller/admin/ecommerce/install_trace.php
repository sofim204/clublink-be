<?php
class Install_Trace extends JI_Controller {

	public function __construct() {
    	parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'ecommerce';
		$this->current_page = 'ecommerce_install_trace';
	}

	public function index() {
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'),0);
			die();
		}

		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}
		
		$this->setTitle("Install Trace ".$this->site_suffix_admin);

		$this->putThemeContent("ecommerce/install_trace/home_modal", $data);
		$this->putThemeContent("ecommerce/install_trace/home", $data);
		$this->putJsContent("ecommerce/install_trace/home_bottom", $data);
		$this->loadLayout('col-2-left', $data);
		$this->render();
	}
}