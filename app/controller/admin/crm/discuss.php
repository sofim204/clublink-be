<?php
class Discuss extends JI_Controller{

	public function __construct(){
		parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'crm';
		$this->current_page = 'crm_discuss';
		$this->load("admin/b_user_model","bum");
		$this->load("admin/c_produk_model","cpm");
		$this->load("admin/d_order_model","dom");
		$this->load("admin/d_order_detail_model","dodm");
		$this->load("admin/d_order_detail_item_model","dodim");
		$this->load("admin/c_discuss_model","cdm");
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

		$count = $this->cdm->wadidaw();

		$data['count'] = $count;

		$this->setTitle('CRM: Discuss Product'.$this->site_suffix_admin);

		$this->putThemeContent("crm/discuss/home_modal",$data);
		$this->putThemeContent("crm/discuss/home",$data);

		$this->putJsReady("crm/discuss/home_bottom",$data);
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

		$this->setTitle('CRM: Discuss Product'.$this->site_suffix_admin);
		$data['ieid'] = $ieid;
		$this->putThemeContent("crm/discuss/detail_modal",$data);
		$this->putThemeContent("crm/discuss/detail",$data);

		$this->putJsReady("crm/discuss/detail_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function reported(){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}
		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

		$this->setTitle('CRM: Discuss Product'.$this->site_suffix_admin);
		$this->putThemeContent("crm/discuss/reported_modal",$data);
		$this->putThemeContent("crm/discuss/reported",$data);

		$this->putJsReady("crm/discuss/reported_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
