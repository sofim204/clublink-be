<?php
	class Likes extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'community';
		$this->current_page = 'community_likes';
		$this->load("admin/community_likes_model","likes_model");
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

		$count = $this->likes_model->count_reported();

		$data['count'] = $count;

		$pengguna = $data['sess']->admin;
		$cats = array();
		$cat = array();

		$this->setTitle('Community Likes '.$this->site_suffix_admin);

		$data['likes'] = array();

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		// $this->putThemeContent("community/likes/home_modal",$data);
		$this->putThemeContent("community/likes/home",$data);
		$this->putJsContent("community/likes/home_js",$data);

		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function detail($id){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}
		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

		$like_post = $this->likes_model->detail($id);

		$data['like_post'] = $like_post[0];

		$this->setTitle('Community: Detail Community'.$this->site_suffix_admin);

		$this->putThemeContent("community/likes/detail",$data);

		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function reported(){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}
		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

		$this->setTitle('Community: Community Post'.$this->site_suffix_admin);

		$this->putThemeContent("community/likes/reported_modal",$data);
		$this->putThemeContent("community/likes/reported",$data);
		$this->putJsContent("community/likes/reported_js",$data);

		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
