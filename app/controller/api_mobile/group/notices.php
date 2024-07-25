<?php
Class Notices extends JI_Controller {

    public function __construct(){
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_mobile/b_user_model",'bu');
        $this->load("api_mobile/group/i_group_notices_model",'ignm');
    }

    private function __page($page)
    {
        if (!is_int($page)) {
            $page = (int) $page;
        }
        if ($page<=0) {
            $page = 1;
        }
        return $page;
    }

    private function __pageSize($page_size)
    {
        $page_size = (int) $page_size;
        if ($page_size<=0) {
            $page_size = 10;
        }
        return $page_size;
    }

    public function index(){
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['notices'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if(empty($nation_code)){
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if(!$c){
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if(!isset($pelanggan->id)){
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }
        // if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Pemberitahuan::index --list");

        $page = $this->input->get("page");
        $page_size = $this->input->get("page_size");
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);

        //manipulator
        $dpem = $this->ignm->getAll($nation_code,$pelanggan->id,$page,$page_size,"cdate","desc");
        foreach($dpem as &$dp){

            $dp->judul = html_entity_decode($dp->judul,ENT_QUOTES);
            $dp->teks = html_entity_decode($dp->teks,ENT_QUOTES);

            $date = date_create($dp->cdate);
            $new_date = date_format($date, "M j, Y");
            $new_time = date_format($date, "H:i");
            $dp->cdate = $new_date.' at '.$new_time;
            
            if(strlen($dp->extras)<=2) $dp->extras = '{}';
            $obj = json_decode($dp->extras);
            if(is_object($obj)) $dp->extras = $obj;
            if(strlen($dp->gambar)>4){
                $dp->gambar = $this->cdn_url($dp->gambar);
            }

            if(isset($obj->product_id)){
                $obj->product_id = (string) $obj->product_id;
            }

        }
        //$this->ignm->updateUnRead($nation_code,$pelanggan->id);

        //success
        $this->status = 200;
        $this->message = 'Success';
        $data['notices'] = $dpem;
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    }

    public function indexv2(){
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['notices'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if(empty($nation_code)){
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
                die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if(!$c){
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if(!isset($pelanggan->id)){
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }
        // if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Pemberitahuan::index --list");

        $page = $this->input->get("page");
        $page_size = $this->input->get("page_size");
        $page = $this->__page($page);
        $page_size = $this->__pageSize($page_size);

        //manipulator
        $dpem = $this->ignm->getAllCustom($nation_code,$pelanggan->id,$page,$page_size,"cdate","desc");
        foreach($dpem as &$dp){

            $dp->judul = html_entity_decode($dp->judul,ENT_QUOTES);
            $dp->teks = html_entity_decode($dp->teks,ENT_QUOTES);

            $date = date_create($dp->cdate);
            $new_date = date_format($date, "M j, Y");
            $new_time = date_format($date, "H:i");
            $dp->cdate = $new_date.' at '.$new_time;
            
            if(strlen($dp->extras)<=2) $dp->extras = '{}';
            $obj = json_decode($dp->extras);
            if(is_object($obj)) $dp->extras = $obj;
            if(strlen($dp->gambar)>4){
                $dp->gambar = $this->cdn_url($dp->gambar);
            }

            if(isset($obj->product_id)){
                $obj->product_id = (string) $obj->product_id;
            }

        }

        //success
        $this->status = 200;
        $this->message = 'Success';
        $data['notices'] = $dpem;
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    }

    //get notification count
    public function hitung(){
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['pemberitahuan_count'] = 0;

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if(empty($nation_code)){
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if(!$c){
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if(!isset($pelanggan->id)){
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }
        // if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Pemberitahuan::hitung");

        //manipulator
        $pemberitahuan_count = (int) $this->ignm->countUnRead($nation_code,$pelanggan->id);
        $data['pemberitahuan_count'] = $pemberitahuan_count;

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    }

    //set read certain notification ID
    public function baca(){
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['notices'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if(empty($nation_code)){
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if(!$c){
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if(!isset($pelanggan->id)){
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }
        // if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Pemberitahuan::baca");

        //collect ID
        $id = (int) $this->input->post("id");

        if($id>0){
            $du = array("is_read"=>1);
            $dpem = $this->ignm->update($nation_code,$pelanggan->id,$id,$du);
        }


        //manipulator
        $dpem = $this->ignm->getAll($nation_code,$pelanggan->id,$page=0,$pageSize=100,"cdate","desc");
        foreach($dpem as &$dp){

            $dp->judul = html_entity_decode($dp->judul,ENT_QUOTES);
            $dp->teks = html_entity_decode($dp->teks,ENT_QUOTES);

            $date = date_create($dp->cdate);
            $new_date = date_format($date, "M j, Y");
            $new_time = date_format($date, "H:i");
            $dp->cdate = $new_date.' at '.$new_time;
            
            if(strlen($dp->extras)<=2) $dp->extras = '{}';
            $obj = json_decode($dp->extras);
            if(is_object($obj)) $dp->extras = $obj;
            if(strlen($dp->gambar)>4){
                $dp->gambar = $this->cdn_url($dp->gambar);
            }
        }

        //success
        $this->status = 200;
        $this->message = 'Success';
        $data['notices'] = $dpem;
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    }

    //by Donny Dennison - 15-07-2020 14:00
    //
    //set read certain notification ID
    public function bacaAll(){
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['notices'] = new stdClass();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if(empty($nation_code)){
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if(!$c){
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if(!isset($pelanggan->id)){
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }
        // if($this->is_log) $this->seme_log->write("api_mobile", "API_Mobile/Pemberitahuan::bacaAll");

        
        $this->ignm->updateUnRead($nation_code,$pelanggan->id);
        
        //manipulator
        $dpem = $this->ignm->getAll($nation_code,$pelanggan->id,$page=0,$pageSize=100,"cdate","desc");
        foreach($dpem as &$dp){

            $dp->judul = html_entity_decode($dp->judul,ENT_QUOTES);
            $dp->teks = html_entity_decode($dp->teks,ENT_QUOTES);

            $date = date_create($dp->cdate);
            $new_date = date_format($date, "M j, Y");
            $new_time = date_format($date, "H:i");
            $dp->cdate = $new_date.' at '.$new_time;
            
            if(strlen($dp->extras)<=2) $dp->extras = '{}';
            $obj = json_decode($dp->extras);
            if(is_object($obj)) $dp->extras = $obj;
            if(strlen($dp->gambar)>4){
                $dp->gambar = $this->cdn_url($dp->gambar);
            }
        }

        //success
        $this->status = 200;
        $this->message = 'Success';
        $data['notices'] = $dpem;
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "group");
    }
}