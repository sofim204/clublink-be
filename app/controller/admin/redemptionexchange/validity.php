<?php
	class Validity extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'redemptionexchange';
		$this->current_page = 'redemptionexchange_validity';
		$this->load("admin/h_redemptionexchange_validity_model","hrvm");
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

		$pengguna = $data['sess']->admin;
		$cats = array();
		$cat = array();

		$this->setTitle('Redemption Exchange Awaiting Confirmation '.$this->site_suffix_admin);

		$data['kategori'] = array();
		$data['user_role'] = $data['sess']->admin->user_role;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("redemptionexchange/validity/home_modal",$data);
		$this->putThemeContent("redemptionexchange/validity/home",$data);
		$this->putJsContent("redemptionexchange/validity/home_js",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}