<?php
	class Kondisi extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'ecommerce';
		$this->current_page = 'ecommerce_kondisi';
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
		

    $this->setTitle("Condition ".$this->site_suffix_admin);
		//$this->loadCss(base_url('assets/css/datatables.min.css'));

		$this->putThemeContent("ecommerce/kondisi/home_modal",$data);
		$this->putThemeContent("ecommerce/kondisi/home",$data);


		$this->putJsContent("ecommerce/kondisi/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
