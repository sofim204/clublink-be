<?php
	class Hashtag_Trending extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'community';
		$this->current_page = 'community_hashtag_trending';
		$this->load("admin/community_hashtag_history_model","hashtag_history_model");
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

		$this->setTitle('Community Hashtag Trendings '.$this->site_suffix_admin);

		$data['hashtag'] = array();
		$data['user_role'] = $data['sess']->admin->user_role;
		$data['user_alias'] = $data['sess']->admin->user_alias;

		$this->putThemeContent("community/hashtag_trending/home_modal",$data);
		$this->putThemeContent("community/hashtag_trending/home",$data);
		$this->putJsContent("community/hashtag_trending/home_js",$data);

		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
