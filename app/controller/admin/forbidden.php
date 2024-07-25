<?php
class Forbidden extends JI_Controller{
	public function __constructx(){
    parent::__construct();
		$this->setTheme('admin/');
	}
	public function index(){
		$data = $this->__init();
		$this->setTheme('admin/');
		header("HTTP/1.0 403 Forbidden");
		$this->setTitle("Forbidden ".$this->site_title);
		$this->setDescription("Access denied, your privileges does not meet the requirement");
		//$this->putThemeContent("notfound",$data);
		$this->loadLayout('forbidden',$data);
		$this->render();
	}
}
