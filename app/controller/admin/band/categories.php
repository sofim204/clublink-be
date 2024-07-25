<?php
	class Categories extends JI_Controller{

	public function __construct(){
        parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'band';
		$this->current_page = 'band_categories';
		$this->load("admin/i_group_category_model","igcm");
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

		$this->setTitle('Categories '.$this->site_suffix_admin);

		$data['kategori'] = array();
		$data['user_role'] = $data['sess']->admin->user_role;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("band/categories/home_modal",$data);
		$this->putThemeContent("band/categories/home",$data);
		$this->putJsContent("band/categories/home_bottom",$data);
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
		
		$this->setTitle('Add Category '.$this->site_suffix_admin);

		//$this->debug($cats);
		//die();

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("band/categories/tambah_modal",$data);
		$this->putThemeContent("band/categories/tambah",$data);


		$this->putJsContent("band/categories/tambah_bottom",$data);
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

		$category_data = $this->igcm->getById($pengguna->nation_code, $id);
		$this->setTitle('Edit '.html_entity_decode($category_data->nama).' Category'.$this->site_suffix_admin);
		
		// START by Yopie Hidayat 21 Juni 2023 14:56 | read special character like &#38 ;
		if(isset($category_data->nama)){
			$category_data->nama = html_entity_decode($category_data->nama);
		}
		// END by Yopie Hidayat 21 Juni 2023 14:56 | read special character like &#38 ;

		//$this->debug($cats);
		//die();
		$data['category_data'] = $category_data;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("band/categories/edit_modal",$data);
		$this->putThemeContent("band/categories/edit",$data);
		$this->putJsContent("band/categories/edit_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}