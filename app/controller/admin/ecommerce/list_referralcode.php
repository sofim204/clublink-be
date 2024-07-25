<?php
class list_Referralcode extends JI_Controller {

	public function __construct() {
    	parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'ecommerce_list_referralcode';
		$this->current_page = 'ecommerce_list_referralcode';
	}

	public function index() {
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'),0);
			die();
		}
		// if(!$this->checkPermissionAdmin($this->current_page)) {
		// 	redir(base_url_admin('forbidden'));
		// 	die();
		// }
		
		$this->setTitle("Recommendation Statistics ".$this->site_suffix_admin);

		$this->putThemeContent("ecommerce/list_referralcode/home_modal",$data);
		$this->putThemeContent("ecommerce/list_referralcode/home",$data);
		$this->putJsContent("ecommerce/list_referralcode/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}