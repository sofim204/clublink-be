<?php
	class Post_Like_Category extends JI_Controller{

	public function __construct(){
        parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'band';
		$this->current_page = 'band_post_like_category';
		$this->load("admin/i_group_post_like_category_model","igplcm");
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

		$this->setTitle('Post Like Category '.$this->site_suffix_admin);

		$data['kategori'] = array();
		$data['user_role'] = $data['sess']->admin->user_role;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("band/post_like_category/home_modal",$data);
		$this->putThemeContent("band/post_like_category/home",$data);
		$this->putJsContent("band/post_like_category/home_bottom",$data);
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
		
		$this->setTitle('Add Post Like Category '.$this->site_suffix_admin);

		//$this->debug($cats);
		//die();

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("band/post_like_category/tambah_modal",$data);
		$this->putThemeContent("band/post_like_category/tambah",$data);


		$this->putJsContent("band/post_like_category/tambah_bottom",$data);
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

		$post_like_category_data = $this->igplcm->getById($pengguna->nation_code, $id);
		$this->setTitle('Edit Post Like Category'.$this->site_suffix_admin);

		//$this->debug($cats);
		//die();
		$data['post_like_category_data'] = $post_like_category_data;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("band/post_like_category/edit_modal",$data);
		$this->putThemeContent("band/post_like_category/edit",$data);
		$this->putJsContent("band/post_like_category/edit_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}