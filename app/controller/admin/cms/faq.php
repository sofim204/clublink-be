<?php
class FAQ extends JI_Controller {

	public function __construct() {
		parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'cms';
		$this->current_page = 'cms_faq';
		$this->load("admin/g_faq_model", "faq_model");
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

		$this->setTitle("CMS: FAQ".$this->site_suffix_admin);
		//load ckeditor
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

        //get current admin
        $pengguna = $data['sess']->admin;
        $nation_code = $pengguna->nation_code;
		
		$list_faq = $this->faq_model->getAllResult($nation_code, 1);
		$data['list_faq'] = $list_faq;

		$list_faq_indo = $this->faq_model->getAllResult($nation_code, 2);
		$data['list_faq_indo'] = $list_faq_indo;

		$this->putThemeContent("cms/faq/home_modal",$data);
		$this->putThemeContent("cms/faq/home",$data);
		$this->putJsContent("cms/faq/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function faqMobile($language_id) {
		$data = $this->__init();

		$nation_code = "62";

		$data['language_id'] = $language_id;

		$list_faq = $this->faq_model->getAllResult($nation_code, $language_id);
		$data['list_faq'] = $list_faq;
		$this->setTitle("Frequently Asked Questions".$this->site_suffix_admin);
		$this->putThemeContent("cms/faq/faq_mobile", $data);
		$this->loadLayout('col-2-left-faqtnc',$data);
		$this->render();
	}
}
