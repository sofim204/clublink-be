<?php
	class Emoji extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'community';
		$this->current_page = 'community_emoji';
		$this->load("admin/community_emoji_model","emoji_model");
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

		$this->setTitle('Community Emojis '.$this->site_suffix_admin);

		$data['emoji'] = array();

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("community/emoji/home_modal",$data);
		$this->putThemeContent("community/emoji/home",$data);
		$this->putJsContent("community/emoji/home_js",$data);

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
		$this->setTitle('Tambah Category Community '.$this->site_suffix_admin);
		$this->setTitle('New Category '.$this->site_suffix_admin);
		$emojis = array();
		//$this->emoji_model->getEmoji();
		foreach($emojis as $emoji){
			if($emoji->b_kategori_id == "-" || $emoji->utype=="kategori"){
				$cats[$emoji->id] = $emoji;
				$cats[$emoji->id]->childs = array();
			}else if($emoji->utype=="kategori_sub"){
				if(!isset($cats[$emoji->b_kategori_id])){
					$cats[$emoji->b_kategori_id] = new stdClass();
					$cats[$emoji->b_kategori_id]->childs = array();
				}
				if(!isset($cats[$emoji->b_kategori_id]->childs[$emoji->id]))
					$cats[$emoji->b_kategori_id]->childs[$emoji->id] = new stdClass();

				$cats[$emoji->b_kategori_id]->childs[$emoji->id] = $emoji;
			}else{
				$cat[$emoji->id] = $emoji;
			}
		}
		$this->setTitle('Tambah Category Community '.$this->site_suffix_admin);

		// $this->debug($cats);
		//die();
		$data['emoji'] = $cats;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("community/emoji/add_new_modal",$data);
		$this->putThemeContent("community/emoji/add_new",$data);


		$this->putJsContent("community/emoji/add_new_js",$data);
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
			redir(base_url_admin('community/emoji/'));
			die();
		}
		$pengguna = $data['sess']->admin;

		$this->setTitle('Edit Category '.$this->site_suffix_admin);

		$cats = array();
		$cat = array();
		// $emojis = $this->emoji_model->getEmoji($pengguna->nation_code);
		// foreach($emojis as $emoji){
		// 	if($emoji->b_kategori_id == "-" || $emoji->utype=="kategori"){
		// 		$cats[$emoji->id] = $emoji;
		// 		$cats[$emoji->id]->childs = array();
		// 	}else if($emoji->utype=="kategori_sub"){
		// 		if(!isset($cats[$emoji->b_kategori_id])){
		// 			$cats[$emoji->b_kategori_id] = new stdClass();
		// 			$cats[$emoji->b_kategori_id]->childs = array();
		// 		}
		// 		if(!isset($cats[$emoji->b_kategori_id]->childs[$emoji->id]))
		// 			$cats[$emoji->b_kategori_id]->childs[$emoji->id] = new stdClass();
		// 		$cats[$emoji->b_kategori_id]->childs[$emoji->id] = $emoji;
		// 	}else{
		// 		$cat[$emoji->id] = $emoji;
		// 	}
		// }
		$kategori_data = $this->emoji_model->getById($pengguna->nation_code, $id);
		$this->setTitle('Edit '.$kategori_data->nama.' Category'.$this->site_suffix_admin);

		//$this->debug($cats);
		//die();
		$data['kategori'] = $cats;
		$data['kategori_data'] = $kategori_data;

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("community/emoji/edit_modal",$data);
		$this->putThemeContent("community/emoji/edit",$data);
		$this->putJsContent("community/emoji/edit_js",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
