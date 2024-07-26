<?php
class Clublink_Ads extends JI_Controller {
	public function __construct(){
    	parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'clublink_ads';
		$this->current_page = 'clublink_ads';
		$this->load("admin/c_clublink_ads_model", "clublink_ads_model");
	}

	public function index() {
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'),0);
			die();
		}
		if(!$this->checkPermissionAdmin($this->current_page)) {
			redir(base_url_admin('forbidden'));
			die();
		}
		
		$this->setTitle("Clublink Advertisement ".$this->site_suffix_admin);

		$this->putThemeContent("clublink_ads/home_modal",$data);
		$this->putThemeContent("clublink_ads/home",$data);
		$this->putJsContent("clublink_ads/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

    // webview for description of event banner
	public function getDetailAds($id) {
		$data = $this->__init();
		$nation_code = "62"; 

		$list_detail = $this->clublink_ads_model->detail($id, $nation_code);
		$data['list_detail'] = $list_detail;

		$this->putThemeContent("clublink_ads/clublink_ads_detail", $data);
		$this->loadLayout('col-2-left-eventbanner', $data);
		$this->render();
	}
}