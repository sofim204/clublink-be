<?php
class Banner_Carousel extends JI_Controller {

	public function __construct() {
    	parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'banner_carousel';
		$this->current_page = 'banner_carousel';
	}

	public function index() {
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'),0);
			die();
		}
		// if(!$this->checkPermissionAdmin($this->current_page)){
		// 	redir(base_url_admin('forbidden'));
		// 	die();
		// }
		
		$this->setTitle("Banner Carousel ".$this->site_suffix_admin);

		$this->putThemeContent("banner_carousel/home_modal",$data);
		$this->putThemeContent("banner_carousel/home",$data);
		$this->putJsContent("banner_carousel/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}