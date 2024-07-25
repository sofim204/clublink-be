<?php
class Faq extends JI_Controller{

	public function index(){
		//initial
		$dt = $this->__init();

		//default result
		$data = array();
		$data['faq'] = array();

    //check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
    if(empty($nation_code)){
      $this->status = 101;
	    $this->message = 'Missing or invalid nation_code';
      $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "faq");
      die();
    }

		//check apikey
		$apikey = $this->input->get('apikey');
		$c = $this->apikey_check($apikey);
		if(!$c){
			$this->status = 400;
			$this->message = 'Missing or invalid API key';
			$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "faq");
			die();
		}
		
  	$this->status = 200;
    $this->message = "Success";
    $file = SENEROOT."app/cache/faq.json";
    if(file_exists($file)){
      $faqfile = fopen($file,"r+");
      $jsonfaq = fread($faqfile,fileSize($file));
      $faq_old = json_decode($jsonfaq);
      fclose($faqfile);
      if(is_array($faq_old)) $data['faq'] = $faq_old;
    }
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "faq");
	}
}
