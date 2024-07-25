<?php

class Multilanguage extends JI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_mobile/g_multilanguage_model", "gmm");
    }

    public function index()
    {

        //default result
        $data = array();

        // //check nation_code
        // $nation_code = $this->input->get('nation_code');
        // $nation_code = $this->nation_check($nation_code);
        // if (empty($nation_code)) {
        //     $this->status = 101;
        //     $this->message = 'Missing or invalid nation_code';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "multilanguage");
        //     die();
        // }

        // //check apikey
        // $apikey = $this->input->get('apikey');
        // $c = $this->apikey_check($apikey);
        // if (!$c) {
        //     $this->status = 400;
        //     $this->message = 'Missing or invalid API key';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "multilanguage");
        //     die();
        // }

        //check email and phone number
        $variable_name = trim($this->input->get("variable_name"));
        if(!$variable_name){
            $variable_name = "";
        }

        $dataTemp = $this->gmm->getAll($variable_name);

        foreach($dataTemp AS $temp){

            $data[$temp->variable_name] = array(
                'indonesia' => $temp->indonesia,
                'english' => $temp->english,
                'korea' => $temp->korea,
                'thailand' => $temp->thailand
            );
        }
        unset($temp, $dataTemp);

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "multilanguage");

    }

}
