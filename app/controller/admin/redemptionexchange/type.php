<?php
	class Type extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'redemptionexchange';
		$this->current_page = 'redemptionexchange_type';
		$this->load("admin/h_redemptionexchange_type_model","hrtm");
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

		$this->setTitle('Redemption Exchange Setting '.$this->site_suffix_admin);

		$data['kategori'] = array();
		$data['user_role'] = $data['sess']->admin->user_role;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("redemptionexchange/type/home_modal",$data);
		$this->putThemeContent("redemptionexchange/type/home",$data);
		$this->putJsContent("redemptionexchange/type/home_bottom",$data);
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
        $data['types'] = $this->hrtm->getType();
		
		$this->setTitle('Add Setting '.$this->site_suffix_admin);

		//$this->debug($cats);
		//die();

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("redemptionexchange/type/tambah_modal",$data);
		$this->putThemeContent("redemptionexchange/type/tambah",$data);


		$this->putJsContent("redemptionexchange/type/tambah_bottom",$data);
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

		$game_data = $this->hrtm->getById($pengguna->nation_code, $id);
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

		$this->putThemeContent("redemptionexchange/type/edit_modal",$data);
		$this->putThemeContent("redemptionexchange/type/edit",$data);
		$this->putJsContent("redemptionexchange/type/edit_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
