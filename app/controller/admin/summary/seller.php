<?php
class Seller extends JI_Controller{
  public function __construct(){
    parent::__construct();
    $this->setTheme("admin");
		$this->current_parent = 'summary';
		$this->current_page = 'summary_seller';
    $this->load("admin/b_user_model","bu");
  }
  public function index($b_user_id=""){
		$data = $this->__init();
		if(!$this->admin_login){
			redir(base_url_admin('login'));
			die();
		}

		if(!$this->checkPermissionAdmin($this->current_page)){
			redir(base_url_admin('forbidden'));
			die();
		}
		
    //get nation code from current admin;
    $nation_code = $data['sess']->admin->nation_code;

    //validate b_user_id
    $b_user_id = (int) $b_user_id;
    if($b_user_id<=0){
      redir(base_url_admin(""));
      die();
    }
    $data['pelanggan'] = $this->bu->getById($nation_code,$b_user_id);

    $this->setTitle("Seller Summary ". $this->site_suffix_admin);
		$this->putThemeContent("summary/seller/home_modal",$data);
		$this->putThemeContent("summary/seller/home",$data);
		$this->putJsReady("summary/seller/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
  }
}
