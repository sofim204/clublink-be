<?php
	class Hashtag extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'community';
		$this->current_page = 'community_hashtag';
		$this->load("admin/community_hashtag_model","hashtag_model");
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

		$this->setTitle('Community Hashtags '.$this->site_suffix_admin);

		$data['hashtag'] = array();

		$this->putThemeContent("community/hashtag/home_modal",$data);
		$this->putThemeContent("community/hashtag/home",$data);
		$this->putJsContent("community/hashtag/home_js",$data);

		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
	public function add_new(){
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
		$this->setTitle('Tambah Community Hashtag '.$this->site_suffix_admin);

		// $this->debug($cats);
		//die();
		$data['hashtag'] = $cats;

		$this->putThemeContent("community/hashtag/add_new_modal",$data);
		$this->putThemeContent("community/hashtag/add_new",$data);


		$this->putJsContent("community/hashtag/add_new_js",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
