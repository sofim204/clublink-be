<?php
class TNC extends JI_Controller{

	public function __construct(){
    parent::__construct();
		//$this->setTheme('frontx');
		$this->lib("seme_purifier");
		$this->current_parent = 'cms';
		$this->current_page = 'cms_tnc';
		$this->load("api_admin/g_tnc_model", "tnc_model");
	}
	
	// by Muhammad Sofi 30 December 2021 10:00 | new change get tnc from database

	public function index(){
		$d = $this->__init();
		$data = array();
		
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code; 

		$data = $this->tnc_model->getAll($nation_code);
		$this->status = 200;
		$this->message = 'Success';
		$this->__json_out($data);
	}

	public function indonesia(){
		$d = $this->__init();
		$data = array();
		
		$pengguna = $d['sess']->admin;
		$nation_code = $pengguna->nation_code; 

		$data = $this->tnc_model->getAllIndo($nation_code);
		$this->status = 200;
		$this->message = 'Success';
		$this->__json_out($data);
	}

	// public function edit(){
	// 	$d = $this->__init();
	// 	$data = array();
	// 	if(!$this->admin_login){
	// 		$this->status = 400;
	// 		$this->message = 'Unauthorized access';
	// 		header("HTTP/1.0 400 Unauthorized");
	// 		$this->__json_out($data);
	// 		die();
	// 	}
	// 	$pengguna = $d['sess']->admin;
	// 	$nation_code = $pengguna->nation_code;


    // //get input
	// 	$tnc = new stdClass();
	// 	$tnc->tnc = "";
    // $tnc_isi = $this->input->post("tnc");
    // $tnc_isi = $this->seme_purifier->richtext($tnc_isi);
    // $tnc->tnc = $tnc_isi;

    // //get tnc
    // $file = SENEROOT."app/cache/tnc.json";
    // $fp = fopen($file,'w+');
    // fwrite($fp,json_encode($tnc));
    // fclose($fp);

    // $tncfile = fopen($file,"r+");
    // $jsontnc = fread($tncfile,filesize($file));
    // $tnc = json_decode($jsontnc);
    // fclose($tncfile);

    // $data['tnc'] = "";
	// 	if(isset($tnc->tnc)) $data['tnc'] = $tnc->tnc;

    // //buil result
	// 	$this->status = 200;
	// 	$this->message = 'Success';
	// 	$this->__json_out($data);
	// }

	public function edit(){
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

		$du = $_POST;
		foreach($du as $key=>&$val){
			if(is_string($val)){
				if($key == 'content'){
					$val = $this->seme_purifier->richtext($val);
				}else{
					$val = $this->__f($val);
				}
			}
		}
		if(isset($du['content'])) isset($du['content']);
		$this->tnc_model->update($nation_code, $du);

		$this->status = 200;
		$this->message = 'Perubahan berhasil diterapkan';
		
		$this->__json_out($data);
	}

	public function edit_indonesia(){
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

		$du = $_POST;
		foreach($du as $key=>&$val){
			if(is_string($val)){
				if($key == 'content'){
					$val = $this->seme_purifier->richtext($val);
				}else{
					$val = $this->__f($val);
				}
			}
		}
		if(isset($du['content'])) isset($du['content']);
		$this->tnc_model->update_indonesia($nation_code, $du);

		$this->status = 200;
		$this->message = 'Perubahan berhasil diterapkan';
		
		$this->__json_out($data);
	}
}
