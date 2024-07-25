<?php
class Pengaturan extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'dashboard';
		$this->current_page = 'dashboard';
	}
	public function index(){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}
		$this->putThemeContent("pengaturan/home",$data);
		$this->putJsContent("pengaturan/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
