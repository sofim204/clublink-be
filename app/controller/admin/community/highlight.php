<?php
class Highlight extends JI_Controller {

	public function __construct(){
		parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'community';
		$this->current_page = 'community_highlight';
		$this->load("admin/g_highlight_community_model","ghcm");
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

		$this->setTitle('Community Highlight '.$this->site_suffix_admin);
		$data['list'] = array();
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("community/highlight/home_modal",$data);
		$this->putThemeContent("community/highlight/home",$data);
		$this->putJsContent("community/highlight/home_js",$data);

		$this->loadLayout('col-2-left', $data);
		$this->render();
	}
}
