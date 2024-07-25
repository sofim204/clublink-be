<?php
class Whitelistip extends JI_Controller {

	public function __construct() {
    	parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'ecommerce';
		$this->current_page = 'ecommerce_whitelistip';
	}

	public function index() {
		$data = $this->__init();
		// if(!$this->admin_login){
		// 	redir(base_url_admin('login'),0);
		// 	die();
		// }

		$data['admin_name'] = $data['sess']->admin->user_alias;
		
		$this->setTitle("Whitelist IP ".$this->site_suffix_admin);

		$this->putThemeContent("ecommerce/whitelistip/home_modal", $data);
		$this->putThemeContent("ecommerce/whitelistip/home", $data);
		$this->putJsContent("ecommerce/whitelistip/home_bottom", $data);
		$this->loadLayout('col-2-left', $data);
		$this->render();
	}
}