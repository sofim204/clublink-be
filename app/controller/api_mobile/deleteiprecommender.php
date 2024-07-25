<?php

// require_once (SENEROOT.'kero/lib/PHP-FFMpeg-1.0.1/src/FFMpeg/FFMpeg.php');

class Deleteiprecommender extends JI_Controller {

	//By Yopie Hidayat - 09 Mei 2023 - 14:50
	//Requested by Mr Jackie to make function that can delete ip recommender from DB

	public function __construct(){
    	parent::__construct();
    	$this->lib("seme_log");
		$this->load("api_mobile/delete_ip_recommender_model",'dirm');
	}

	public function index(){
		
		$data= array();

	    //check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
	    if(empty($nation_code)){
	      	$this->status = 101;
	  		$this->message = 'Missing or invalid nation_code';
	      	$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");
	      	die();
	    }

	    //check activation code
		$activation_code = $this->input->get('activation_code');
        if ($activation_code != '21F2E429ABD0404B46953A9BEE5E5FCEB279FDEC40788263991744E5931AA6D7P@$$w0rd!') {
            $this->status = 3000;
            $this->message = 'Wrong Activation Code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");
            die();
        }

        $data['total_recommender'] = 0;
        $data_recommenders = $this->dirm->getRecommender($this->input->get('year'), $this->input->get('month'));
        if($data_recommenders) {
            foreach($data_recommenders as $dr){
                // delete ip recommender
                $res = $this->dirm->deleteIpByID($dr->id);
                if($res){ // jika berhasil dihapus
                    $data['total_recommender'] += 1;
                }                
                // delete ip in recomendee
                $res = $this->dirm->deleteIpByRecommenderID($dr->id);
            }
        }else{
            // $this->__json_out('No Data');
        }

		$this->status = 200;
		$this->message = 'Success';
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_ip_address_recommender_recommendee");
	
	}

}