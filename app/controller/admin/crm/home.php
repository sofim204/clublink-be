<?php
	class Home extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'crm';
		$this->current_page = 'crm';

	}
	public function index(){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin());
			die();
		}
	}
}
