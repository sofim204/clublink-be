<?php
class Popular_Club extends JI_Controller {
	public function __construct(){
        parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'band';
		$this->current_page = 'band_popular';
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

		$this->setTitle('Popular Club '.$this->site_suffix_admin);

		$data['user_role'] = $data['sess']->admin->user_role;

		$this->putThemeContent("band/popular_club/home_modal", $data);
		$this->putThemeContent("band/popular_club/home", $data);
		$this->putJsContent("band/popular_club/home_bottom", $data);
		$this->loadLayout('col-2-left', $data);
		$this->render();
	}

	public function list() {
		$data = $this->__init();
		$this->current_parent = 'band';
		$this->current_page = 'band_popular_club';
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}

		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

		$this->setTitle('Popular Club List'.$this->site_suffix_admin);

		$data['user_role'] = $data['sess']->admin->user_role;

		$this->putThemeContent("band/group_popular/home_modal", $data);
		$this->putThemeContent("band/group_popular/home", $data);
		$this->putJsContent("band/group_popular/home_bottom", $data);
		$this->loadLayout('col-2-left', $data);
		$this->render();
	}
}