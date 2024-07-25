<?php
class kategori_automotive extends JI_Controller {

	public function __construct() {
    	parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'ecommerce';
		$this->current_page = 'ecommerce_kategori_automotive';
		$this->load("admin/b_kategori_automotive_model4","bkam4");
	}

	public function index() {
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
		// START by Muhammad Sofi 22 December 2021 10:00 | remark unused code
		// $cats = array();
		// $cat = array();

		$this->setTitle('Product Automotive Categories '.$this->site_suffix_admin);
		$data['user_role'] = $data['sess']->admin->user_role;

		// $data['kategori_automotive'] = array();

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		// $this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));
		// END by Muhammad Sofi 22 December 2021 10:00 | remark unused code

		$this->putThemeContent("ecommerce/kategori_automotive/home_modal",$data);
		$this->putThemeContent("ecommerce/kategori_automotive/home",$data);
		$this->putJsContent("ecommerce/kategori_automotive/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function tambah() {
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}

		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}
		// START by Muhammad Sofi 22 December 2021 10:00 | remark unused code
		$pengguna = $data['sess']->admin;
		// $cats = array();
		// $cat = array();
		// $this->setTitle('Tambah Kategori Produk Automotive Ecommerce '.$this->site_suffix_admin);
		// $this->setTitle('New Category '.$this->site_suffix_admin);
		// $kategories = array();//$this->bkam4->getKategori();
		// foreach($kategories as $kategori){
		// 	if($kategori->b_kategori_id == "-" || $kategori->utype=="brand"){
		// 		$cats[$kategori->id] = $kategori;
		// 		$cats[$kategori->id]->childs = array();
		// 	}else if($kategori->utype=="kategori_sub"){
		// 		if(!isset($cats[$kategori->b_kategori_id])){
		// 			$cats[$kategori->b_kategori_id] = new stdClass();
		// 			$cats[$kategori->b_kategori_id]->childs = array();
		// 		}
		// 		if(!isset($cats[$kategori->b_kategori_id]->childs[$kategori->id]))
		// 			$cats[$kategori->b_kategori_id]->childs[$kategori->id] = new stdClass();

		// 		$cats[$kategori->b_kategori_id]->childs[$kategori->id] = $kategori;
		// 	}else{
		// 		$cat[$kategori->id] = $kategori;
		// 	}
		// }
		$this->setTitle('Add Product Automotive Category '.$this->site_suffix_admin);

		//$this->debug($cats);
		//die();
		// $data['kategori'] = $cats;
		// END by Muhammad Sofi 22 December 2021 10:00 | remark unused code

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("ecommerce/kategori_automotive/tambah_modal", $data);
		$this->putThemeContent("ecommerce/kategori_automotive/tambah", $data);
		$this->putJsContent("ecommerce/kategori_automotive/tambah_bottom", $data);
		$this->loadLayout('col-2-left', $data);
		$this->render();
	}
    
	public function edit($id) {
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
			redir(base_url_admin('ecommerce/kategori_automotive/'));
			die();
		}

		$pengguna = $data['sess']->admin;
		$this->setTitle('Edit Product Automotive Category '.$this->site_suffix_admin);
		// START by Muhammad Sofi 22 December 2021 10:00 | remark unused code
		// $cats = array();
		// $cat = array();
		// $kategories = $this->bkam4->getKategori($pengguna->nation_code);
		// foreach($kategories as $kategori){
		// 	if($kategori->b_kategori_id == "-" || $kategori->utype=="brand"){
		// 		$cats[$kategori->id] = $kategori;
		// 		$cats[$kategori->id]->childs = array();
		// 	}else if($kategori->utype=="kategori_sub"){
		// 		if(!isset($cats[$kategori->b_kategori_id])){
		// 			$cats[$kategori->b_kategori_id] = new stdClass();
		// 			$cats[$kategori->b_kategori_id]->childs = array();
		// 		}
		// 		if(!isset($cats[$kategori->b_kategori_id]->childs[$kategori->id]))
		// 			$cats[$kategori->b_kategori_id]->childs[$kategori->id] = new stdClass();
		// 		$cats[$kategori->b_kategori_id]->childs[$kategori->id] = $kategori;
		// 	}else{
		// 		$cat[$kategori->id] = $kategori;
		// 	}
		// }
        
		$kategori_data = $this->bkam4->getById($pengguna->nation_code, $id);
		// $this->setTitle('Edit '.$kategori_data->nama.' Category'.$this->site_suffix_admin);
		// END by Muhammad Sofi 22 December 2021 10:00 | remark unused code

		//$this->debug($cats);
		//die();
		// $data['kategori'] = $cats;
		$data['kategori_data'] = $kategori_data;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("ecommerce/kategori_automotive/edit_modal", $data);
		$this->putThemeContent("ecommerce/kategori_automotive/edit", $data);
		$this->putJsContent("ecommerce/kategori_automotive/edit_bottom", $data);
		$this->loadLayout('col-2-left', $data);
		$this->render();
	}
}
