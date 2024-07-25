<?php
class FAq extends JI_Controller {

	public function __construct() {
    	parent::__construct();
		$this->lib("seme_purifier");
		$this->current_parent = 'cms';
		$this->current_page = 'cms_faq';
		$this->load("api_admin/g_faq_model", 'gfm');
	}

	public function tambah() {
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$di = $_POST;
		if(!isset($di['title'])) $di['title'] = "";
		if(!isset($di['content'])) $di['content'] = "";
		// by Muhammad Sofi 21 January 2022 20:53 | add insert priority and sort by priority
		if(!isset($di['priority'])) $di['priority'] = ""; 
		if(!isset($di['language_id'])) $di['language_id'] = ""; 
		if(isset($di['priority'])){
			$this->gfm->trans_start();
			$lastId = (int) $this->gfm->getLastId($nation_code);

			$di['nation_code'] = $nation_code;
			$di['id'] = $lastId;

			$res = $this->gfm->set($di);
			if($res){
				$this->gfm->trans_commit();
				$this->status = 200;
				$this->message = 'Data successfully added';				
			} else {
				$this->gfm->trans_rollback();
				$this->status = 900;
				$this->message = 'Tidak dapat menyimpan data baru, silakan coba beberapa saat lagi';
			}
			$this->gfm->trans_end();
		}
		$this->__json_out($data);
	}

	public function detail($id){
		$id = (int) $id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($id)){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$data = $this->gfm->getById($nation_code, $id);
		if(!isset($data->id)){
			$this->status = 400;
			$this->message = 'Invalid ID or Data has been deleted';
			$this->__json_out($data);
			die();
		}
		$this->status = 200;
		$this->message = 'Success';
		$this->__json_out($data);
	}

	public function edit($id) {
		$d = $this->__init();
		$data = array();

		$id = (int) $id;
		if($id<=0){
			$this->status = 451;
			$this->message = 'Invalid ID';
			$this->__json_out($data);
			die();
		}
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
    	$nation_code = $pengguna->nation_code;

		$this->status = 800;
		$this->message = 'One or more parameter are required';
		$du = $_POST;
		if(isset($du['id'])) unset($du['id']);
		if(!isset($du['title'])) $du['title'] = "";
		if(!isset($du['content'])) $di['content'] = "";
		if(!isset($du['priority'])) $du['priority'] = "";
		if($id>0 && isset($du['priority'])){
			$res = $this->gfm->update($nation_code,$id,$du);
			if($res){
				$this->status = 200;
				$this->message = 'Perubahan berhasil diterapkan';
			}else{
				$this->status = 901;
				$this->message = 'Failed to make data changes';
			}
		}
		$this->__json_out($data);
	}

	public function hapus($id) {
		$id = (int) $id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($id)){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code;

		$res = $this->gfm->del($nation_code,$id);
		if($res){
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 902;
			$this->message = 'Failed while deleting data from database';
		}
		$this->__json_out($data);
	}
}