<?php
class Notfound extends JI_Controller{
	public function __constructx(){
    parent::__construct();
		$this->setTheme('admin/');
	}
	public function index(){
		$data = $this->__init();
		$this->setTheme('admin/');
		header("HTTP/1.0 404 Not Found");
		$this->setTitle("Not Found ".$this->site_title);
		$this->setDescription($this->site_description);
		//$this->putThemeContent("notfound",$data);
		$this->loadLayout('notfound',$data);
		$this->render();
	}
}
