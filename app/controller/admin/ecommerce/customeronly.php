<?php
	class Customeronly extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'ecommerce';
		$this->current_page = 'ecommerce_customeronly';
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

		$pengguna = $data['sess']->admin;

		$this->setTitle("Customers ".$this->site_suffix_admin);
		
		$data['api_url'] = base_url('api_admin/alamatongkir/');
		$data['user_role'] = $data['sess']->admin->user_role;
		$data['user_alias'] = $data['sess']->admin->user_alias;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));

		$this->putThemeContent("ecommerce/customeronly/home_modal",$data);
		$this->putThemeContent("ecommerce/customeronly/home",$data);
		$this->putJsContent("ecommerce/customeronly/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

}
