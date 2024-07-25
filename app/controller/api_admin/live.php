<?php
class Live extends JI_Controller{
	var $user_id;
	var $user_nama;
	public function __construct(){
    parent::__construct();
		//$this->setTheme('frontx');
		$this->load("api_web/b_user_modelx",'bu');

		$this->user_id = '';
		$this->user_nama = '';

		$this->setTheme('front');
		$this->lib("seme_chat");
		$dt = $this->__init();
		if($this->admin_login){
			$user = $dt['sess']->admin;
			$user_id = $user->id;
			$user_nama = $user->nama;

			$this->user_id = $user_id;
			$this->user_nama = $user_nama;

			$user_picture = '';
			$this->seme_chat->sender($user_id,$user_nama,$user_picture="");
		}
	}
	public function index(){
    $dt = $this->__init();
    $data = array();
		if($this->admin_login){
			$data['user'] = $this->seme_chat->get_user_by_id($this->user_id);
			$data['gm_list'] = $this->seme_chat->group_list($this->user_id);
			$data['gm_list'] = $this->seme_chat->group_list($this->user_id);
			$data['users'] = $this->seme_chat->get_user();

			$this->status = 100;
			$this->message = 'Berhasil';
		}else{
			$this->status = 400;
			$this->message = 'Akses ditolak, harus login terlebih dahulu';
			header("HTTP/1.0 401 You must login to access this feature");
		}
		$this->__json_out($data);
	}
}
