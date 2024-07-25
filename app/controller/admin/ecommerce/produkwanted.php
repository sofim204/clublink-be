<?php
	class Produkwanted extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'ecommerce';
		$this->current_page = 'ecommerce_productwanted';
		$this->load("admin/b_user_model","bum");
		$this->load("api_admin/b_user_produkwanted_model", "bupm");

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
		$nation_code = $pengguna->nation_code;
    $this->putThemeContent("ecommerce/produkwanted/home_modal",$data);
		$this->putThemeContent("ecommerce/produkwanted/home",$data);
		$this->putJsContent("ecommerce/produkwanted/home_bottom",$data);
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

		$this->setKey($data['sess']);

		$pengguna = $data['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$this->setTitle('Tambah Produk Wanted '.$this->site_suffix_admin);
		$this->setTitle('New Products Wanted '.$this->site_suffix_admin);


		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));
		$this->putJsFooter(base_url('assets/js/jquery.priceformat.min'));

		$this->putThemeContent("ecommerce/produkwanted/tambah_modal",$data);
		$this->putThemeContent("ecommerce/produkwanted/tambah",$data);

		$this->putJsContent("ecommerce/produkwanted/tambah_bottom",$data);
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
			redir(base_url_admin('ecommerce/produkwanted'));
      die();
    }
		$this->setKey($data['sess']);
    $pengguna = $data['sess']->admin;
    $nation_code = $pengguna->nation_code;

		$data['produkwanted'] = $this->bupm->getById($nation_code, $id);
		if(!isset($data['produkwanted']->id)){
			redir(base_url('ecommerce/produkwanted/'));
			die();
		}

		$data['user'] = $this->bum->getById($nation_code, $data['produkwanted']->b_user_id);
		if(!isset($data['user']->id)){
			$data['user'] = new stdClass();
			$data['user']->id = "null";
			$data['user']->fnama = "-";
		}

		//$this->debug($data['produk']);
		//die();
		//handled by API
		//$data['produk']->fotos = $this->cpfm->getByProdukId($nation_code, $data['produk']->id);

		$this->setTitle('Edit Product Wanted'.' '.$this->site_suffix_admin);
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));
		$this->putJsFooter(base_url('assets/js/jquery.priceformat.min'));

		$this->putThemeContent("ecommerce/produkwanted/edit_modal",$data);
		$this->putThemeContent("ecommerce/produkwanted/edit",$data);


		$this->putJsContent("ecommerce/produkwanted/edit_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
