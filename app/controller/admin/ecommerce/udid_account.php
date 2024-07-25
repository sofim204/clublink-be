<?php
class Udid_Account extends JI_Controller {

	public function __construct() {
    	parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'ecommerce';
		$this->current_page = 'ecommerce_udid_account';
	}

	public function index() {
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'),0);
			die();
		}
		
		$this->setTitle("UDID & Account ".$this->site_suffix_admin);

		$this->putThemeContent("ecommerce/udid_account/home_modal", $data);
		$this->putThemeContent("ecommerce/udid_account/home", $data);
		$this->putJsContent("ecommerce/udid_account/home_bottom", $data);
		$this->loadLayout('col-2-left', $data);
		$this->render();
	}
}