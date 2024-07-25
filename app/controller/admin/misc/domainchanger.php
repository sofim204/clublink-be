<?php
class Domainchanger extends JI_Controller {

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'misc';
		$this->current_page = 'misc_domainchanger';
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
		$nation_code = $data['sess']->admin->nation_code;

		$this->setTitle('DOMAIN CHANGER '.$this->site_suffix);

		$this->setTitle("DOMAIN CHANGER ".$this->site_suffix_admin);
		$this->putThemeContent("misc/domainchanger/home_modal",$data);
		$this->putThemeContent("misc/domainchanger/home",$data);

		$this->putJsContent("misc/domainchanger/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}


}
