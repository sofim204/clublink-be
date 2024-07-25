<?php
	class Outbounding extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'crm_outbounding';
		$this->current_page = 'crm_outbounding';
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
		
		$this->setTitle("Customer Outbounding ".$this->site_suffix_admin);

		$this->putThemeContent("crm/outbounding/home_modal",$data);
		$this->putThemeContent("crm/outbounding/home",$data);
		$this->putJsContent("crm/outbounding/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function default($ieid){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}
		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

		$this->setTitle('CRM: Detail Outbounding'.$this->site_suffix_admin);
		$data['ieid'] = $ieid;
		$this->putThemeContent("crm/outbounding/detail_modal",$data);
		$this->putThemeContent("crm/outbounding/detail",$data);

		$this->putJsReady("crm/outbounding/detail_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
