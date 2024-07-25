<?php
class Proxysql extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->lib('conumtext');
		$this->load("api_admin/h_logs_model",'hlm');
	}

    public function index(){
		
	}

    public function security(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Authorization required';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}        
		$convert = strtolower(hash('sha256',$_POST['pin']));
        $name = 'proxysql';
		$data_db = $this->hlm->getData($name);
		if ($convert == $data_db->pin_page) {
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 900;
			$this->message = 'Failed';
		}
		$this->__json_out($data);
	}

    public function readLogJq() {
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

		if (!empty($array_tampung)) {
			$this->status = 200;
			$this->message = 'Success';
		}else{
			$this->status = 900;
			$this->message = 'Failed';
		}
		$this->__json_out($array_tampung);
	}

}