<?php
class Sellon_Ads extends JI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_mobile/c_sellon_ads_model", "csam");
        $this->load("api_mobile/b_user_model", "bu");
    }

    private function __callBlockChainCheckValidationUser($postdata){
        $dateBefore = date("Y-m-d H:i:s");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_URL, $this->blockchain_new_api_host."api/User/CheckValidationUserBannerAds");

        $headers = array();
        $headers[] = 'Content-Type:  application/json';
        $headers[] = 'Accept:  application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
          return 0;
          //echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        $this->seme_log->write("api_mobile", "sebelum panggil api ".$dateBefore.", url untuk block chain server ". $this->blockchain_new_api_host."api/User/CheckValidationUserBannerAds. data send to blockchain api ". json_encode($postdata).". isi response block chain server ". $result);
        return $result;
    }

    public function index()
    {
        //initial
        $dt = $this->__init();

        //default response
        $data = array();
        $data['sellon_ads'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (empty($c)) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        // check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $pelanggan = new stdClass();
            if($nation_code == 62){ //indonesia
                $pelanggan->language_id = 2;
            }else if($nation_code == 82){ //korea
                $pelanggan->language_id = 3;
            }else if($nation_code == 66){ //thailand
                $pelanggan->language_id = 4;
            }else {
                $pelanggan->language_id = 1;
            }
        }

        $timezone = $this->input->get("timezone");
        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        $device_id = $this->input->post("device_id");

        $ads_data = $this->csam->getAll($nation_code);
        foreach ($ads_data as $ads) {
            $check = $this->csam->getDataByDeviceId($nation_code, $ads->id, $device_id);

            if(empty($check)){
                $ads->url = $this->cdn_url($ads->url);
                $ads->img_thumbnail = $this->cdn_url($ads->img_thumbnail);
                $ads->cdate = $this->customTimezone($ads->cdate, $timezone);
                $data['sellon_ads'][] = $ads;
            }

            if($ads->type_ads == "webview"){
                $ads->teks .= (($pelanggan->language_id == 2) ? "ID" : "EN")."/".((isset($pelanggan->id)) ? $pelanggan->id : "")."/"; 
            }
        }
        unset($ads_data, $ads);

        $this->status = 200;
        $this->message = "Success";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_ads");
    }

    public function detail($id)
    {
        //initial
        $dt = $this->__init();

        //default response
        $data = array();
        $data['event_banner_detail'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (empty($c)) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        // check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $pelanggan = new stdClass();
            if($nation_code == 62){ //indonesia
                $pelanggan->language_id = 2;
            }else if($nation_code == 82){ //korea
                $pelanggan->language_id = 3;
            }else if($nation_code == 66){ //thailand
                $pelanggan->language_id = 4;
            }else {
                $pelanggan->language_id = 1;
            }
        }

        $timezone = $this->input->get("timezone");
        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        $data['event_banner_detail'] = $this->csam->getById($nation_code, $id);
        // if (isset($banner->gambar)) {
        //     if (strlen($banner->gambar)<=4) {
        //         $banner->gambar = 'media/promo/default.png';
        //     }
            
        //     // by Muhammad Sofi - 28 October 2021 11:00
        //     // if user img & banner not exist or empty, change to default image
        //     // $banner->gambar = $this->cdn_url($banner->gambar);
        //     if(file_exists(SENEROOT.$banner->gambar)){
        //         $banner->gambar = $this->cdn_url($banner->gambar);
        //     } else {
        //         $banner->gambar = $this->cdn_url('media/user/default.png');
        //     }
            
        //     //by Donny Dennison - 30 September 2020 17:12
        //     //bug fixing ' or " become emoji
        //     // $banner->teks = html_entity_decode($banner->teks);
        //     $banner->teks = html_entity_decode($banner->teks,ENT_QUOTES);
        //     $banner->judul = html_entity_decode($banner->judul,ENT_QUOTES);

        //     $banner->cdate=substr($banner->cdate,0,10);
        //     $data['banner'] = $banner;
        // }

        // $data['event_banner_detail']->judul = html_entity_decode($data['event_banner_detail']->judul,ENT_QUOTES);
        $data['event_banner_detail']->teks = html_entity_decode($data['event_banner_detail']->teks,ENT_QUOTES);
        $data['event_banner_detail']->url = $this->cdn_url($data['event_banner_detail']->url);
        $data['event_banner_detail']->img_thumbnail = $this->cdn_url($data['event_banner_detail']->img_thumbnail);
        $data['event_banner_detail']->cdate = $this->customTimezone($data['event_banner_detail']->cdate, $timezone);

        if($pelanggan->language_id = 2){ //indonesia
            $data['event_banner_detail']->cdate = $this->__dateIndonesia($data['event_banner_detail']->cdate,'tanggal');
        }else if($pelanggan->language_id = 3){ //korea
            $data['event_banner_detail']->cdate = date('d F Y',strtotime($data['event_banner_detail']->cdate));
        }else if($pelanggan->language_id = 4){ //thailand
            $data['event_banner_detail']->cdate = date('d F Y',strtotime($data['event_banner_detail']->cdate));
        }else {
            $data['event_banner_detail']->cdate = date('d F Y',strtotime($data['event_banner_detail']->cdate));
        }

        $this->status = 200;
        $this->message = "Success";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_ads");
    }

    public function dont_show_ads() {
        //initial
        $dt = $this->__init();

        //default response
        $data = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (empty($c)) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        // init
        $device_id = $this->input->post("device_id");
        $ads_id = $this->input->post("ads_id");

        // check if data already registered
        $check = $this->csam->checkData($nation_code, $device_id, $ads_id);
        if (!empty($check)) {
			$this->status = 1109;
			$this->message = 'User Already Hide This Ads';
			$this->__json_out($data);
			die();
		}

        //start transaction and lock table
        $this->csam->trans_start();

        //get last id for first time
        $last_id = $this->csam->getLastId($nation_code);
        $di = array();
        $di['nation_code'] = $nation_code;
        $di['id'] = $last_id;
        $di['device_id'] = $device_id;
        $di['ads_id'] = $ads_id;
        $di['cdate'] = 'NOW()';
        $res = $this->csam->set($di);
        if (!$res) {
            $this->csam->trans_rollback();
            $this->csam->trans_end();
            $this->status = 1107;
            $this->message = "Error while insert";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_ads");
            die();
        }

		$this->status = 200;
		$this->message = "Success";

		//end transaction
		$this->csam->trans_end();

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_ads");
    }

    public function special()
    {
        //initial
        $dt = $this->__init();

        //default response
        $data = array();
        $data['sellon_ads'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (empty($c)) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        // check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $pelanggan = new stdClass();
            if($nation_code == 62){ //indonesia
                $pelanggan->language_id = 2;
            }else if($nation_code == 82){ //korea
                $pelanggan->language_id = 3;
            }else if($nation_code == 66){ //thailand
                $pelanggan->language_id = 4;
            }else {
                $pelanggan->language_id = 1;
            }
        }

        $timezone = $this->input->get("timezone");
        if($this->isValidTimezoneId($timezone) === false){
          $timezone = $this->default_timezone;
        }

        $device_id = $this->input->post("device_id");

        $type = $this->input->post('type');
        if(!$type){
            $this->status = 200;
            $this->message = "Success";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_ads");
        }

        if($type == "webview_wallet_ads"){
            if(isset($pelanggan->id)){
                $postdata = array(
                  'walletCode' => $pelanggan->user_wallet_code_new
                );
                $response = json_decode($this->__callBlockChainCheckValidationUser($postdata));
                if(isset($response->status)){
                    if($response->status != 200){
                        $this->status = 200;
                        $this->message = "Success";
                        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_ads");
                    }
                }else{
                    $this->status = 200;
                    $this->message = "Success";
                    $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_ads");
                }
            }else{
                $this->status = 200;
                $this->message = "Success";
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_ads");
            }
        }

        $ads_data = $this->csam->getByType($nation_code, $type);
        foreach ($ads_data as $ads) {
            $check = $this->csam->getDataByDeviceId($nation_code, $ads->id, $device_id);
            if(empty($check)){
                $ads->url = $this->cdn_url($ads->url);
                $ads->img_thumbnail = $this->cdn_url($ads->img_thumbnail);
                $ads->cdate = $this->customTimezone($ads->cdate, $timezone);
                $data['sellon_ads'][] = $ads;
            }

            if($ads->type_ads == "webview"){
                $ads->teks .= (($pelanggan->language_id == 2) ? "ID" : "EN")."/".((isset($pelanggan->id)) ? $pelanggan->id : "")."/"; 
            }
        }
        unset($ads_data, $ads);

        $this->status = 200;
        $this->message = "Success";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_ads");
    }
}
