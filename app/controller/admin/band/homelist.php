<?php
class Homelist extends JI_Controller {
	public function __construct(){
        parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'band';
		$this->current_page = 'band_homelist';
		$this->load("admin/i_group_home_list_model", "ighlm");
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

		$this->setTitle('Event Registration '.$this->site_suffix_admin);

		$data['user_role'] = $data['sess']->admin->user_role;

		$this->putThemeContent("band/homelist/home_modal", $data);
		$this->putThemeContent("band/homelist/home", $data);
		$this->putJsContent("band/homelist/home_bottom", $data);
		$this->loadLayout('col-2-left', $data);
		$this->render();
	}

	public function add_list_page() {
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}

		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

		$this->setTitle('Add Event Registration '.$this->site_suffix_admin);

		$this->putThemeContent("band/homelist/tambah", $data);
		$this->putJsContent("band/homelist/tambah_bottom", $data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function edit_page($id, $type){
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

		$homelist_data = $this->ighlm->getById($pengguna->nation_code, $id, $type);
		$this->setTitle('Edit Event Registration '.$this->site_suffix_admin);

		//$this->debug($cats);
		//die();
		$data['homelist_data'] = $homelist_data;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("band/homelist/edit_modal", $data);
		$this->putThemeContent("band/homelist/edit", $data);
		$this->putJsContent("band/homelist/edit_bottom", $data);
		$this->loadLayout('col-2-left', $data);
		$this->render();
	}
}