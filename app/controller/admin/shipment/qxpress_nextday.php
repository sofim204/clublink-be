<?php
	class Qxpress_NextDay extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'shipment';
		$this->current_page = 'shipment_qxpress_nextday';

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
    $this->setTitle("Qxpress NextDay");

		$this->putThemeContent("shipment/qxpress/nextday/home_modal",$data);
		$this->putThemeContent("shipment/qxpress/nextday/home",$data);


		$this->putJsReady("shipment/qxpress/nextday/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
