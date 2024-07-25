<?php
class Event_banner extends JI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load("api_mobile/b_user_model", 'bu');
        $this->load("api_mobile/c_event_banner_model", "cebm");
    }

    public function index()
    {
        //initial
        $dt = $this->__init();

        //default response
        $data = array();
        $data['event_banner'] = array();

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

        // random order index by ali -- 25 jan 2023
        $arr = $this->cebm->getAllNew($nation_code);
        if (count($arr) > 0) {
            $rand = rand(1, count($arr));
            $last_element = $arr[count($arr) - $rand];

            for($i = count($arr) - $rand; $i > 0; $i--) {
                $arr[$i] = $arr[$i - 1];
            }

            $arr[0] = $last_element;
        }
        // end

        $data['event_banner'] = $arr;
        foreach ($data['event_banner'] as &$banner) {
            $banner->url = $this->cdn_url($banner->url);
            $banner->img_thumbnail = $this->cdn_url($banner->img_thumbnail);

            if($banner->type_event_banner == "webview"){
                $banner->teks .= (($pelanggan->language_id == 2) ? "ID" : "EN")."/".((isset($pelanggan->id)) ? $pelanggan->id : "")."/"; 
            }
        }

        $this->status = 200;
        $this->message = "Success";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "event_banner");
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

        $data['event_banner_detail'] = $this->cebm->getById($nation_code, $id);
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
        // $data['event_banner_detail']->teks = html_entity_decode($data['event_banner_detail']->teks,ENT_QUOTES);
        $data['event_banner_detail']->url = $this->cdn_url($data['event_banner_detail']->url);
        $data['event_banner_detail']->img_thumbnail = $this->cdn_url($data['event_banner_detail']->img_thumbnail);
        // $data['event_banner_detail']->cdate = $this->customTimezone($data['event_banner_detail']->cdate, $timezone);

        // if($pelanggan->language_id = 2){ //indonesia
        //     $data['event_banner_detail']->cdate = $this->__dateIndonesia($data['event_banner_detail']->cdate,'tanggal');
        // }else if($pelanggan->language_id = 3){ //korea
        //     $data['event_banner_detail']->cdate = date('d F Y',strtotime($data['event_banner_detail']->cdate));
        // }else if($pelanggan->language_id = 4){ //thailand
        //     $data['event_banner_detail']->cdate = date('d F Y',strtotime($data['event_banner_detail']->cdate));
        // }else {
        //     $data['event_banner_detail']->cdate = date('d F Y',strtotime($data['event_banner_detail']->cdate));
        // }

        if($data['event_banner_detail']->type_event_banner == "webview"){
            $data['event_banner_detail']->teks .= (($pelanggan->language_id == 2) ? "ID" : "EN")."/".((isset($pelanggan->id)) ? $pelanggan->id : "")."/"; 
        }

        $this->status = 200;
        $this->message = "Success";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "event_banner");
    }
}
