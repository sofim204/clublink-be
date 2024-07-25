<?php
class Event_Banner extends JI_Controller {
	public function __construct(){
    	parent::__construct();
		$this->setTheme('admin');
		$this->current_parent = 'event_banner';
		$this->current_page = 'event_banner';
		$this->load("admin/g_event_banner_model", "event_banner_model");
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
		
		$this->setTitle("Event Banner ".$this->site_suffix_admin);

		$this->putThemeContent("event_banner/home_modal",$data);
		$this->putThemeContent("event_banner/home",$data);
		$this->putJsContent("event_banner/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

    // webview for description of event banner
	public function getDetailEventBanner($id) {
		$data = $this->__init();
		$nation_code = "62"; 

		$list_detail = $this->event_banner_model->detail($id, $nation_code);
		$data['list_detail'] = $list_detail;

		$this->putThemeContent("event_banner/event_banner_detail", $data);
		$this->loadLayout('col-2-left-eventbanner', $data);
		$this->render();
	}
}