<?php
	class Modules extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'erpmaster';
		$this->current_page = 'erpmaster_modules';
		$this->load('admin/a_modules_model','amm');
	}
	public function index($abcd=""){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}
		if(strlen($abcd)<=11){
			if(!$this->checkPermissionAdmin($this->current_page)){
				redir(base_url_admin('forbidden'));
				die();
			}
		}

		$data['modules'] = $this->amm->getVisibleAndActive();

		$this->putThemeContent("erpmaster/modules/home_modal",$data);
		$this->putThemeContent("erpmaster/modules/home",$data);

		$this->putJsContent("erpmaster/modules/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
