<?php
	class Campaign extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'campaign';
		$this->current_page = 'campaign';
		$this->load('admin/d_order_model','order');
	}
	public function index(){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'),0);
			die();
		}
		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}
		
		$this->setTitle("Sponsored ".$this->site_suffix_admin);

		$this->putThemeContent("campaign/home_modal",$data);
		$this->putThemeContent("campaign/home",$data);
		$this->putJsContent("campaign/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}