<?php
	class Listing extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'game';
		$this->current_page = 'game_list';
		$this->load("admin/h_games_model","hgm");
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

		$this->setTitle('Games List '.$this->site_suffix_admin);

		$data['kategori'] = array();
		$data['user_role'] = $data['sess']->admin->user_role;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("game/listing/home_modal",$data);
		$this->putThemeContent("game/listing/home",$data);
		$this->putJsContent("game/listing/home_bottom",$data);
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
		
		$this->setTitle('Add Game '.$this->site_suffix_admin);

		//$this->debug($cats);
		//die();

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("game/listing/tambah_modal",$data);
		$this->putThemeContent("game/listing/tambah",$data);


		$this->putJsContent("game/listing/tambah_bottom",$data);
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

		$game_data = $this->hgm->getById($pengguna->nation_code, $id);
		$this->setTitle('Edit '.html_entity_decode($game_data->name).' Game'.$this->site_suffix_admin);
		
		// START by Yopie Hidayat 21 Juni 2023 14:56 | read special character like &#38 ;
		if(isset($game_data->name)){
			$game_data->name = html_entity_decode($game_data->name);
		}
		// END by Yopie Hidayat 21 Juni 2023 14:56 | read special character like &#38 ;

		//$this->debug($cats);
		//die();
		$data['game_data'] = $game_data;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("game/listing/edit_modal",$data);
		$this->putThemeContent("game/listing/edit",$data);
		$this->putJsContent("game/listing/edit_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
