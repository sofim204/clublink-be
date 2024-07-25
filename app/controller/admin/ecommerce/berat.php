<?php
	class Berat extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'ecommerce';
		$this->current_page = 'ecommerce_berat';
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

    $this->setTitle("Weight ".$this->site_suffix_admin);
		//$this->loadCss(base_url('assets/css/datatables.min.css'));

		$this->putThemeContent("ecommerce/berat/home_modal",$data);
		$this->putThemeContent("ecommerce/berat/home",$data);


		$this->putJsContent("ecommerce/berat/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
