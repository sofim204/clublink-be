<?php
class TNC extends JI_Controller {
	var $status = 0;
	var $treehtml = '';
	var $is_login_admin;
	var $module = "cms_tnc";
	var $is_login_user = "";
	var $page = "cms_tnc";

	public function __construct(){
		parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'cms';
		$this->current_page = 'cms_tnc';
		$this->load("admin/g_tnc_model", "tnc_model");
	}
	
	// by Muhammad Sofi 30 December 2021 10:00 | new change get tnc from database

	public function index() {
		$data = array();
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}
		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}
    	$this->setTitle("CMS: Terms and Conditions".$this->site_suffix_admin);

    	//load ckeditor
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("cms/tnc/home",$data);
		$this->putJsContent("cms/tnc/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function tncMobile($language_id) {
		$data = $this->__init();
		
		$nation_code = "62";

		$data['language_id'] = $language_id;

		$list_tnc = $this->tnc_model->getAll($nation_code, $language_id);
		$data['list_tnc'] = $list_tnc;
		$this->setTitle("Terms and Conditions".$this->site_suffix_admin);
		$this->putThemeContent("cms/tnc/tnc_mobile",$data);
		$this->loadLayout('col-2-left-tnc',$data);
		$this->render();
	}
}
