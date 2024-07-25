<?php
	class Category extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'community';
		$this->current_page = 'community_category';
		$this->load("admin/community_category_model","category_model");
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

		$this->setTitle('Community Categories '.$this->site_suffix_admin);

		$data['category'] = array();

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("community/category/home_modal",$data);
		$this->putThemeContent("community/category/home",$data);
		$this->putJsContent("community/category/home_js",$data);

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

		$categories = array();
		//$this->category_model->getCategory();
		foreach($categories as $category){
			if($category->b_kategori_id == "-" || $category->utype=="kategori"){
				$cats[$category->id] = $category;
				$cats[$category->id]->childs = array();
			}else if($category->utype=="kategori_sub"){
				if(!isset($cats[$category->b_kategori_id])){
					$cats[$category->b_kategori_id] = new stdClass();
					$cats[$category->b_kategori_id]->childs = array();
				}
				if(!isset($cats[$category->b_kategori_id]->childs[$category->id]))
					$cats[$category->b_kategori_id]->childs[$category->id] = new stdClass();

				$cats[$category->b_kategori_id]->childs[$category->id] = $category;
			}else{
				$cat[$category->id] = $category;
			}
		}
		$this->setTitle('Add Category Community '.$this->site_suffix_admin);

		// $this->debug($cats);
		//die();
		$data['category'] = $cats;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("community/category/add_new_modal",$data);
		$this->putThemeContent("community/category/add_new",$data);


		$this->putJsContent("community/category/add_new_js",$data);
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
		
		$id = (int) $id;
		if($id<=0){
			redir(base_url_admin('community/category/'));
			die();
		}
		$pengguna = $data['sess']->admin;

		$cats = array();
		$cat = array();
		// $categories = $this->category_model->getCategory($pengguna->nation_code);
		// foreach($categories as $category){
		// 	if($category->b_kategori_id == "-" || $category->utype=="kategori"){
		// 		$cats[$category->id] = $category;
		// 		$cats[$category->id]->childs = array();
		// 	}else if($category->utype=="kategori_sub"){
		// 		if(!isset($cats[$category->b_kategori_id])){
		// 			$cats[$category->b_kategori_id] = new stdClass();
		// 			$cats[$category->b_kategori_id]->childs = array();
		// 		}
		// 		if(!isset($cats[$category->b_kategori_id]->childs[$category->id]))
		// 			$cats[$category->b_kategori_id]->childs[$category->id] = new stdClass();
		// 		$cats[$category->b_kategori_id]->childs[$category->id] = $category;
		// 	}else{
		// 		$cat[$category->id] = $category;
		// 	}
		// }
		$kategori_data = $this->category_model->getById($pengguna->nation_code, $id);
		$this->setTitle('Edit '.html_entity_decode($kategori_data->nama).' Category'.$this->site_suffix_admin);

		// START by Muhammad Sofi 21 January 2022 22:03 | read special character like &#38 ;
		if(isset($kategori_data->nama)){
			$kategori_data->nama = html_entity_decode($kategori_data->nama);
		}

		if(isset($kategori_data->deskripsi)){
			$kategori_data->deskripsi = html_entity_decode($kategori_data->deskripsi);
		}
		// END by Muhammad Sofi 21 January 2022 22:03 | read special character like &#38 ;

		//$this->debug($cats);
		//die();
		$data['kategori'] = $cats;
		$data['kategori_data'] = $kategori_data;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("community/category/edit_modal",$data);
		$this->putThemeContent("community/category/edit",$data);
		$this->putJsContent("community/category/edit_js",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
