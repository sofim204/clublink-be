<?php
class Blacklistuser extends JI_Controller {

	public function __construct() {
    	parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'ecommerce';
		$this->current_page = 'ecommerce_blacklistuser';
	}

	public function index() {
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'),0);
			die();
		}

		$data['admin_name'] = $data['sess']->admin->user_alias;
		
		$this->setTitle("Blacklisted User ".$this->site_suffix_admin);

		$this->putThemeContent("ecommerce/blacklistuser/home_modal", $data);
		$this->putThemeContent("ecommerce/blacklistuser/home", $data);
		$this->putJsContent("ecommerce/blacklistuser/home_bottom", $data);
		$this->loadLayout('col-2-left', $data);
		$this->render();
	}
}