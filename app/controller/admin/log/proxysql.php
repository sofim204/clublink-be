<?php
	class Proxysql extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('admin');
		$this->lib("seme_purifier");
		$this->current_parent = 'log';
		$this->current_page = 'log_proxysql';
		// $this->load("admin/h_games_model","hgm");
		$this->load("api_admin/h_logs_model",'hlm');
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

		$this->setTitle('ProxySQL Log '.$this->site_suffix_admin);

		$data['user_role'] = $data['sess']->admin->user_role;		
		
		$data['array_tampung'] = $this->readLog();

		//$this->loadCss(base_url('assets/css/datatables.min.css'));

		$this->putThemeContent("log/proxysql/home_modal",$data);
		$this->putThemeContent("log/proxysql/home",$data);
		$this->putJsContent("log/proxysql/home_bottom",$data);
		$this->loadLayout('col-2-left',$data);
		$this->render();
	}

	public function readLog() {
		$name = 'proxysql';
		$data_db = $this->hlm->getData($name);
		$log_path = $data_db->log_path;
		$file = fopen($log_path,"r");
		$no=1;
		$array_tampung=array();
		//Output lines until EOF is reached
		while(! feof($file)) {
			$line = fgets($file);
			// array_push($array_tampung,$no." ".$line. "<br>");
			array_push($array_tampung,$line. "<br>");
			// echo $line. "<br>";
			$no++;
		}
		// rsort($array_tampung,1);
		// foreach($array_tampung as $at){
		// 	echo $at;
		// }
		fclose($file);

		return $array_tampung;
	}

	
}