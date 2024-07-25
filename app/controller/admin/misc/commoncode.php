<?php
class Commoncode extends JI_Controller {

	public function __construct() {
    	parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'misc';
		$this->current_page = 'misc_commoncode';
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
		
		$this->setTitle("Common Code ".$this->site_suffix_admin);

		$this->putThemeContent("misc/commoncode/home_modal", $data);
		$this->putThemeContent("misc/commoncode/home", $data);
		$this->putJsContent("misc/commoncode/home_bottom", $data);
		$this->loadLayout('col-2-left', $data);
		$this->render();
	}
}