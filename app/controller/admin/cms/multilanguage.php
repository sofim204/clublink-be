<?php
class Multilanguage extends JI_Controller {

	public function __construct() {
    	parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'multilanguage';
		$this->current_page = 'cms_multilanguage';
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
		
		$this->setTitle("Multi Language ".$this->site_suffix_admin);

		$this->putThemeContent("cms/multilanguage/home_modal", $data);
		$this->putThemeContent("cms/multilanguage/home", $data);
		$this->putJsContent("cms/multilanguage/home_bottom", $data);
		$this->loadLayout('col-2-left', $data);
		$this->render();
	}
}