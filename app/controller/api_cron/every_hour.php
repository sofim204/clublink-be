<?php
class Every_hour extends JI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        // $this->load("api_cron/a_notification_model", "anot");
        // $this->load("api_cron/a_pengguna_model", "apm");
        // $this->load("api_cron/b_user_model", "bu");
        // $this->load("api_cron/b_user_setting_model", "busm");
        // $this->load("api_cron/common_code_model", "ccm");

        $this->load('api_cron/a_apikey_model','aakm');
    }

    private function __codeGen($nation_code)
    {
            $this->lib("conumtext");
            $token = $this->conumtext->genRand($type="str", $min=6, $max=14);
            return $nation_code.''.$token;
    }

    public function index()
    {
        $this->seme_log->write("api_cron", "API_Cron/Every_hour::index START");

        //start transaction
        // $this->order->trans_start();

        //update apikey
        $apikeys = $this->aakm->getActive();
        if(is_array($apikeys) && count($apikeys)>0){
          foreach($apikeys as $apikey){
            $du = array();
            $du['str'] = $this->__codeGen($apikey->nation_code);
            $du['code'] = hash('sha256',$du['str']);
            $this->aakm->update($apikey->nation_code,$apikey->id,$du);
            $this->seme_log->write("api_cron", 'API_Cron/Every_hour::index -- INFO apikey re-generated: OK');
          }
          unset($apikeys, $apikey);
        }

        //close transaction
        // $this->order->trans_end();

        $this->seme_log->write("api_cron", "API_Cron/Every_hour::index END");

        die();
    }

}
