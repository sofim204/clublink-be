<?php
class Leaderboard extends JI_Controller {
	public function __construct() {
		parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'community';
		$this->current_page = 'community_leaderboard';
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

		$this->setTitle('Leaderboard on Your Area '.$this->site_suffix_admin);
		$data['list'] = array();
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("community/leaderboard/home",$data);
		$this->putJsContent("community/leaderboard/home_js",$data);

		$this->loadLayout('col-2-left', $data);
		$this->render();
	}

	public function leaderboard_history() {
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

		$this->setTitle('Leaderboard Point History'.$this->site_suffix_admin);
		$data['list'] = array();

		$this->putThemeContent("community/leaderboard/history", $data);
		$this->putJsContent("community/leaderboard/history_js", $data);

		$this->loadLayout('col-2-left', $data);
		$this->render();
	}
}
