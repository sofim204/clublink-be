<?php
class Home extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('lp3');
	}
	public function index(){
		redir(base_url_admin("login"),0,0);
		exit;
		$data = $this->__init();
		$this->setTitle($this->site_title);
		$this->setAuthor($this->site_name);
		$this->setKeyword($this->site_name);
		$this->setDescription('Sell and Buy product from '.$this->site_name.' to get interesting product.');

		$this->putThemeContent("home/home",$data);
		$this->loadLayout("col-1",$data);
		$this->render();
	}

}
