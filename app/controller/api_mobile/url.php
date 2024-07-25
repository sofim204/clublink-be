<?php
class Url extends JI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load("api_mobile/g_url", "gu");
    }

    public function index()
    {
        //initial
        $dt = $this->__init();

        //default response
        $data = array();
        $data['url_list'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_ads");
            die();
        }

        //check apikey
        // $apikey = $this->input->get('apikey');
        // $c = $this->apikey_check($apikey);
        // if (empty($c)) {
        //     $this->status = 400;
        //     $this->message = 'Missing or invalid API key';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_ads");
        //     die();
        // }

        $data['url_list'] = $this->gu->getListUrl($nation_code);

        $this->status = 200;
        $this->message = "Berhasil";
        $this->__json_out($data);
    }

    public function active()
    {
        //initial
        $dt = $this->__init();

        //default response
        $data = array();
        // $data['url_list'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_ads");
            die();
        }

        //check apikey
        // $apikey = $this->input->get('apikey');
        // $c = $this->apikey_check($apikey);
        // if (empty($c)) {
        //     $this->status = 400;
        //     $this->message = 'Missing or invalid API key';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "sellon_ads");
        //     die();
        // }

        $data = $this->gu->getListUrlActive($nation_code);

        $this->status = 200;
        $this->message = "Berhasil";
        $this->__json_out($data);
    }

}