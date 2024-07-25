<?php
	class Default_Image extends JI_Controller{

	public function __construct(){
        parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'band';
		$this->current_page = 'band_default_image';
		$this->load("admin/i_group_default_image_model","igdim");
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

		$this->setTitle('Default Image '.$this->site_suffix_admin);

		$data['kategori'] = array();
		$data['user_role'] = $data['sess']->admin->user_role;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("band/default_image/home_modal",$data);
		$this->putThemeContent("band/default_image/home",$data);
		$this->putJsContent("band/default_image/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
	public function tambah(){
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
		
		$this->setTitle('Add Default Image '.$this->site_suffix_admin);

		//$this->debug($cats);
		//die();

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("band/default_image/tambah_modal",$data);
		$this->putThemeContent("band/default_image/tambah",$data);


		$this->putJsContent("band/default_image/tambah_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function edit($id){
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

		$default_image_data = $this->igdim->getById($pengguna->nation_code, $id);
		$this->setTitle('Edit Default Image'.$this->site_suffix_admin);

		//$this->debug($cats);
		//die();
		$data['default_image_data'] = $default_image_data;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("band/default_image/edit_modal",$data);
		$this->putThemeContent("band/default_image/edit",$data);
		$this->putJsContent("band/default_image/edit_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}