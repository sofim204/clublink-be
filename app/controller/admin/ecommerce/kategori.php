<?php
	class Kategori extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'ecommerce';
		$this->current_page = 'ecommerce_kategori';
		$this->load("admin/b_kategori_model4","bkm4");
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

		$this->setTitle('Product Categories '.$this->site_suffix_admin);

		$data['kategori'] = array();
		$data['user_role'] = $data['sess']->admin->user_role;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("ecommerce/kategori/home_modal",$data);
		$this->putThemeContent("ecommerce/kategori/home",$data);
		$this->putJsContent("ecommerce/kategori/home_bottom",$data);
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
		$cats = array();
		$cat = array();
		$kategories = array();//$this->bkm4->getKategori();
		foreach($kategories as $kategori){
			if($kategori->b_kategori_id == "-" || $kategori->utype=="kategori"){
				$cats[$kategori->id] = $kategori;
				$cats[$kategori->id]->childs = array();
			}else if($kategori->utype=="kategori_sub"){
				if(!isset($cats[$kategori->b_kategori_id])){
					$cats[$kategori->b_kategori_id] = new stdClass();
					$cats[$kategori->b_kategori_id]->childs = array();
				}
				if(!isset($cats[$kategori->b_kategori_id]->childs[$kategori->id]))
					$cats[$kategori->b_kategori_id]->childs[$kategori->id] = new stdClass();

				$cats[$kategori->b_kategori_id]->childs[$kategori->id] = $kategori;
			}else{
				$cat[$kategori->id] = $kategori;
			}
		}
		$this->setTitle('Add Product Category '.$this->site_suffix_admin);

		//$this->debug($cats);
		//die();
		$data['kategori'] = $cats;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("ecommerce/kategori/tambah_modal",$data);
		$this->putThemeContent("ecommerce/kategori/tambah",$data);


		$this->putJsContent("ecommerce/kategori/tambah_bottom",$data);
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
			redir(base_url_admin('ecommerce/kategori/'));
			die();
		}
		$pengguna = $data['sess']->admin;

		$cats = array();
		$cat = array();
		$kategories = $this->bkm4->getKategori($pengguna->nation_code);
		foreach($kategories as $kategori){
			if($kategori->b_kategori_id == "-" || $kategori->utype=="kategori"){
				$cats[$kategori->id] = $kategori;
				$cats[$kategori->id]->childs = array();
			}else if($kategori->utype=="kategori_sub"){
				if(!isset($cats[$kategori->b_kategori_id])){
					$cats[$kategori->b_kategori_id] = new stdClass();
					$cats[$kategori->b_kategori_id]->childs = array();
				}
				if(!isset($cats[$kategori->b_kategori_id]->childs[$kategori->id]))
					$cats[$kategori->b_kategori_id]->childs[$kategori->id] = new stdClass();
				$cats[$kategori->b_kategori_id]->childs[$kategori->id] = $kategori;
			}else{
				$cat[$kategori->id] = $kategori;
			}
		}
		$kategori_data = $this->bkm4->getById($pengguna->nation_code, $id);
		$this->setTitle('Edit '.html_entity_decode($kategori_data->nama).' Category'.$this->site_suffix_admin);
		
		// START by Muhammad Sofi 11 January 2022 13:33 | read special character like &#38 ;
		if(isset($kategori_data->nama)){
			$kategori_data->nama = html_entity_decode($kategori_data->nama);
		}

		if(isset($kategori_data->deskripsi)){
			$kategori_data->deskripsi = html_entity_decode($kategori_data->deskripsi);
		}
		// END by Muhammad Sofi 11 January 2022 13:33 | read special character like &#38 ;

		//$this->debug($cats);
		//die();
		$data['kategori'] = $cats;
		$data['kategori_data'] = $kategori_data;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("ecommerce/kategori/edit_modal",$data);
		$this->putThemeContent("ecommerce/kategori/edit",$data);
		$this->putJsContent("ecommerce/kategori/edit_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
