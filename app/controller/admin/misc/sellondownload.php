<?php
class Sellondownload extends JI_Controller {

	public function __construct(){
		parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'misc';
		$this->current_page = 'misc_sellondownload';
	}

	public function index(){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}

		// if(!$this->checkPermissionAdmin($this->current_page)){
		// 	redir(base_url_admin('forbidden'));
		// 	die();
		// }

		$this->setTitle("Sellon Download". $this->site_suffix_admin);
		$this->putThemeContent("misc/sellondownload/home_modal", $data);
		$this->putThemeContent("misc/sellondownload/home", $data);
		$this->putJsReady("misc/sellondownload/home_bottom", $data);
		$this->loadLayout('col-2-left', $data);
		$this->render();
	}

	public function list_qrcode(){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}

		// if(!$this->checkPermissionAdmin($this->current_page)){
		// 	redir(base_url_admin('forbidden'));
		// 	die();
		// }

		$this->setTitle("Sellon Download QRCode". $this->site_suffix_admin);
		$this->putThemeContent("misc/sellondownload/list_qrcode/home_modal", $data);
		$this->putThemeContent("misc/sellondownload/list_qrcode/home", $data);
		$this->putJsReady("misc/sellondownload/list_qrcode/home_bottom", $data);
		$this->loadLayout('col-2-left', $data);
		$this->render();
	}
}
