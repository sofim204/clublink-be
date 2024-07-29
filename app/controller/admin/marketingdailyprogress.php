<?php
	class Marketingdailyprogress extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'marketingdailyprogress';
		$this->current_page = 'marketingdailyprogress';
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

		$this->setTitle("Marketing Daily Progress ".$this->site_suffix_admin);
		
		$data['api_url'] = base_url('api_admin/alamatongkir/');
		$data['user_role'] = $data['sess']->admin->user_role;
		$data['user_alias'] = $data['sess']->admin->user_alias;
		$data['from_date'] = "";
		$data['to_date'] = "";

		$this->putThemeContent("marketingdailyprogress/home_modal",$data);
		$this->putThemeContent("marketingdailyprogress/home",$data);
		$this->putJsContent("marketingdailyprogress/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

}
