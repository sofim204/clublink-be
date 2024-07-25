<?php
	class Discussion extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'community';
		$this->current_page = 'community_discussion';
		$this->load("admin/community_discussion_model","discussion_model");
		$this->load("admin/community_discussion_image_model","discuss_image_model");
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

		$this->setTitle('Community Categories '.$this->site_suffix_admin);

		$count = $this->discussion_model->count_reported();

		$data['count'] = $count;

		$data['discussion'] = array();

		//$this->loadCss(base_url('assets/css/datatables.min.css'));
		$this->putJsFooter(base_url('skin/admin/js/helpers/ckeditor/ckeditor'));

		$this->putThemeContent("community/discussion/home_modal",$data);
		$this->putThemeContent("community/discussion/home",$data);
		$this->putJsContent("community/discussion/home_js",$data);

		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function detail($id){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}
		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}
		
        //get current admin
        $pengguna = $data['sess']->admin;
        $nation_code = $pengguna->nation_code; //get nation_code from current admin

		$list_post = $this->discussion_model->detail($id);
		$post_image = $this->discuss_image_model->getByDiscussionId($nation_code, $id);

		$data['list_post'] = $list_post[0];
		$data['post_image'] = $post_image;

		$this->setTitle('Community: Detail Community'.$this->site_suffix_admin);

		$this->putThemeContent("community/discussion/detail",$data);
		$this->putJsContent("community/discussion/detail_js",$data);

		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function reported(){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}
		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}

		$this->setTitle('Community: Community Discussion'.$this->site_suffix_admin);

		$this->putThemeContent("community/discussion/reported_modal",$data);
		$this->putThemeContent("community/discussion/reported",$data);
		$this->putJsContent("community/discussion/reported_js",$data);

		$this->loadLayout('col-2-left',$data);
		$this->render();
	}
}
