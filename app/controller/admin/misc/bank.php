<?php
	class Bank extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'misc';
		$this->current_page = 'misc_bank';

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
		
		$this->setTitle("Bank List $this->site_suffix_admin");

		$this->putThemeContent("misc/bank/home_modal",$data);
		$this->putThemeContent("misc/bank/home",$data);
		$this->putJsReady("misc/bank/home_bottom",$data);

		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
