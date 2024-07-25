<?php
class Popular_Community extends JI_Controller {
	public function __construct(){
        parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'community';
		$this->current_page = 'community_popular';
		$this->load("admin/c_homepage_main_popular_model", "chmpm");
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

		$this->setTitle('Popular Community '.$this->site_suffix_admin);

		$data['user_role'] = $data['sess']->admin->user_role;

		$this->putThemeContent("community/popular_community_post/home_modal", $data);
		$this->putThemeContent("community/popular_community_post/home", $data);
		$this->putJsContent("community/popular_community_post/home_bottom", $data);
		$this->loadLayout('col-2-left', $data);
		$this->render();
	}
}