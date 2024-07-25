<?php
	class Daily_Re_Targeting extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'event';
		$this->current_page = 'event_daily_re_targeting';
		$this->load("admin/c_community_event_re_targeting_model","ccertm");
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

		$this->setTitle('Daily New User '.$this->site_suffix_admin);

		$data['kategori'] = array();
		$data['user_role'] = $data['sess']->admin->user_role;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		// $this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("event/daily_re_targeting/home_modal",$data);
		$this->putThemeContent("event/daily_re_targeting/home",$data);
		$this->putJsContent("event/daily_re_targeting/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}