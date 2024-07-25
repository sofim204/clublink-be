<?php
	class Group extends JI_Controller{

	public function __construct(){
        parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'band';
		$this->current_page = 'band_group';
		$this->load("admin/i_group_model","igm");
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

		$this->setTitle('Group '.$this->site_suffix_admin);

		$data['kategori'] = array();
		$data['user_role'] = $data['sess']->admin->user_role;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("band/group/home_modal",$data);
		$this->putThemeContent("band/group/home",$data);
		$this->putJsContent("band/group/home_bottom",$data);
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
		
		$this->setTitle('Add Group '.$this->site_suffix_admin);

		//$this->debug($cats);
		//die();

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("band/group/tambah_modal",$data);
		$this->putThemeContent("band/group/tambah",$data);


		$this->putJsContent("band/group/tambah_bottom",$data);
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

		$group_data = $this->igm->getById($pengguna->nation_code, $id);
		$this->setTitle('Edit '.html_entity_decode($group_data->name).' Group'.$this->site_suffix_admin);
		
		// START by Yopie Hidayat 21 Juni 2023 14:56 | read special character like &#38 ;
		if(isset($group_data->name)){
			$group_data->name = html_entity_decode($group_data->name);
		}
		// END by Yopie Hidayat 21 Juni 2023 14:56 | read special character like &#38 ;

		//$this->debug($cats);
		//die();
		$data['group_data'] = $group_data;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("band/group/edit_modal",$data);
		$this->putThemeContent("band/group/edit",$data);
		$this->putJsContent("band/group/edit_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function detail_group_post($ieid){
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

		$data_group = $this->igm->getById($pengguna->nation_code, $ieid);

		$data['title'] = $data_group->name != '' ? "Club [".ucfirst($data_group->group_type)."] : ".$data_group->name : "Club";

		$this->setTitle('Detail Group '.$this->site_suffix_admin);
		$data['ieid'] = $ieid;
		$this->putThemeContent("band/group/detail_modal",$data);
		$this->putThemeContent("band/group/detail",$data);

		$this->putJsReady("band/group/detail_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function popular_club(){
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

		$this->setTitle('Popular Club '.$this->site_suffix_admin);

		$data['kategori'] = array();
		$data['user_role'] = $data['sess']->admin->user_role;

		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));
		$this->putThemeContent("band/group_popular/home_modal",$data);
		$this->putThemeContent("band/group_popular/home",$data);
		$this->putJsContent("band/group_popular/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}