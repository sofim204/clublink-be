<?php
class Promo extends JI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //$this->setTheme('frontx');
        $this->load("api_mobile/b_user_model", 'bu');
        $this->load("api_mobile/c_promo_model", "cprom");
    }
    public function index()
    {
        //initial
        $token = '';
        $user_id = 0;
        $register_success = 0;
        $user = new stdClass();
        $dt = $this->__init();

        //default response
        $data = array();
        $data['banner'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "promo");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (empty($c)) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "promo");
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

        $data['banner'] = $this->cprom->getList($nation_code);
        foreach ($data['banner'] as &$banner) {
            if (isset($banner->gambar)) {
                if (strlen($banner->gambar)<=4) {
                    $banner->gambar = 'media/promo/default.png';
                }
                
                // by Muhammad Sofi - 28 October 2021 11:00
                // if user img & banner not exist or empty, change to default image
                // $banner->gambar = $this->cdn_url($banner->gambar);
                if(file_exists(SENEROOT.$banner->gambar)){
                    $banner->gambar = $this->cdn_url($banner->gambar);
                } else {
                    $banner->gambar = $this->cdn_url('media/user/default.png');
                }
                
                $banner->judul = html_entity_decode($banner->judul,ENT_QUOTES);
            }
            $banner->cdate=substr($banner->cdate,0,10);
        }
        $this->status = 200;
        $this->message = "Success";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "promo");
    }
    public function detail($id)
    {
        //initial
        $token = '';
        $user_id = 0;
        $register_success = 0;
        $user = new stdClass();
        $dt = $this->__init();

        //default response
        $data = array();
        $data['banner'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "promo");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (empty($c)) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "promo");
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

        $banner = $this->cprom->getById($nation_code, $id);
        if (isset($banner->gambar)) {
            if (strlen($banner->gambar)<=4) {
                $banner->gambar = 'media/promo/default.png';
            }
            
            // by Muhammad Sofi - 28 October 2021 11:00
            // if user img & banner not exist or empty, change to default image
            // $banner->gambar = $this->cdn_url($banner->gambar);
            if(file_exists(SENEROOT.$banner->gambar)){
                $banner->gambar = $this->cdn_url($banner->gambar);
            } else {
                $banner->gambar = $this->cdn_url('media/user/default.png');
            }
            
            //by Donny Dennison - 30 September 2020 17:12
            //bug fixing ' or " become emoji
            // $banner->teks = html_entity_decode($banner->teks);
            $banner->teks = html_entity_decode($banner->teks,ENT_QUOTES);
            $banner->judul = html_entity_decode($banner->judul,ENT_QUOTES);

            $banner->cdate=substr($banner->cdate,0,10);
            $data['banner'] = $banner;
        }

        $this->status = 200;
        $this->message = "Success";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "promo");
    }
}
