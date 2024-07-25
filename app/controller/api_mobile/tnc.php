<?php
class TnC extends JI_Controller{

	public function index(){
		//initial
		$dt = $this->__init();

		//default result
		$data = array();
		$data['tnc'] = new stdClass();

    //check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
    if(empty($nation_code)){
      $this->status = 101;
  		$this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "tnc");
      die();
    }

		//check apikey
		$apikey = $this->input->get('apikey');
		$c = $this->apikey_check($apikey);
		if(!$c){
			$this->status = 400;
			$this->message = 'Missing or invalid API key';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "tnc");
			die();
		}

		$this->status = 200;
    $this->message = "Success";
    $data = array();
    $data['tnc'] = '';
    $file = SENEROOT."app/cache/tnc.json";
    if(file_exists($file)){
      $tncfile = fopen($file,"r+");
      $jsontnc = fread($tncfile,fileSize($file));
      $tnc = json_decode($jsontnc);
      if(isset($tnc->tnc)) $data['tnc'] = $tnc->tnc;
      fclose($tncfile);
    }
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "tnc");
	}
}
