<?php
class Blocked_User extends JI_Controller {

	public function __construct() {
    	parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'reportorblock';
		$this->current_page = 'reportorblock_blocked_user';
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

		$nation_code = $data['sess']->admin->nation_code;
		
		$this->setTitle("Blocked User ".$this->site_suffix_admin);

		$this->putThemeContent("reportorblock/blocked_user/home_modal", $data);
		$this->putThemeContent("reportorblock/blocked_user/home", $data);
		$this->putJsContent("reportorblock/blocked_user/home_bottom", $data);
		$this->loadLayout('col-2-left', $data);
		$this->render();
	}
}