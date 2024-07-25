<?php
class Language extends JI_Controller{

    public function __construct(){
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_mobile/b_user_model", 'bu');
        $this->load("api_mobile/g_language_model","glm");
    }

    private function __callBlockChainUpdateLanguageNew($postdata){
        $dateBefore = date("Y-m-d H:i:s");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_URL, $this->blockchain_new_api_host."api/UpdateLanguage");

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

        $this->seme_log->write("api_mobile", "sebelum panggil api ".$dateBefore.", url untuk block chain server ". $this->blockchain_new_api_host."api/UpdateLanguage. data send to blockchain api ". json_encode($postdata).". isi response block chain server ". $result);
        return $result;
    }

    public function index(){
        $nation_code = $this->input->get("nation_code");
        $this->status = 200;
        $this->message = "Success";
        $data = $this->glm->getAll($nation_code);
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "language");
    }

    public function user(){

        //default result
        $data = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "language");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (empty($c)) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "language");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "language");
            die();
        }

        $data = $this->glm->getById($nation_code, $pelanggan->language_id);

        $this->status = 200;
        $this->message = 'Success';

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "language");
    }

    public function change(){

        //default result
        $data = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "language");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (empty($c)) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "language");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "language");
            die();
        }

        $language_id = $this->input->get('language_id');

        $du = array();
        $du["language_id"] = $language_id;
        $this->bu->update($nation_code, $pelanggan->id, $du);

        if($language_id == 2){
            $language = 'id';
        }else{
            $language = 'en';
        }

        $postdata = array(
          'userWalletCode' => $pelanggan->user_wallet_code_new,
          'LanguageIsoCode' => $language,
        );
        $this->__callBlockChainUpdateLanguageNew($postdata);

        $data = $this->glm->getAll($nation_code, $language_id);
        
        $this->status = 200;
        $this->message = 'Success';

        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "language");
    }
}
