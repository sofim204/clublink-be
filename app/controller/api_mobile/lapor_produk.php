<?php
class Lapor_Produk extends JI_Controller
{
    public $is_soft_delete=1;
    public $is_log = 1;
    public $imgQueue;

    public function __construct()
    {
        parent::__construct();
        //$this->setTheme('frontx');
        $this->lib("seme_log");
        $this->lib("seme_email");
        $this->lib("seme_purifier");
        $this->load("api_mobile/a_notification_model", "anot");
        $this->load("api_mobile/b_user_model", "bu");
        $this->load("api_mobile/common_code_model", "ccm");
        $this->load("api_mobile/c_produk_model", "cpm");
        $this->load("api_mobile/c_produk_laporan_model", "cplm");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Header: *");
    }
    public function index()
    {
        //initial
        $dt = $this->__init();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "lapor_produk");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "lapor_produk");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "lapor_produk");
            die();
        }
        $this->status = 404;
        $this->message = 'Not found';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "lapor_produk");
    }
    public function kategori()
    {
        //initial
        $dt = $this->__init();
        $data = array();
    
        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "lapor_produk");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "lapor_produk");
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
    
        //initial
        $dt = $this->__init();
        $this->status = 200;
        $this->message = 'Success';
        $ccm = $this->ccm->getByClassified($nation_code, "product_report");
        $data = array();
        foreach ($ccm as $c) {
            $cm = new stdClass();
            $cm->kategori = $c->codename;
            $cm->deskripsi = $c->remark;
            $data[] = $cm;
        }
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "lapor_produk");
    }
    public function check($c_produk_id)
    {
        //initial
        $dt = $this->__init();
        $data = array();
    
        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "lapor_produk");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "lapor_produk");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "lapor_produk");
            die();
        }
        $this->status = 200;
        $this->message = 'This product has not been reported';
        
        //check already take down
        $cplm = $this->cpm->checkTakeDown($nation_code, $c_produk_id);
        if (isset($cplm->nation_code)) {
            $this->status = 947;
            $this->message = 'this product has been convicted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "lapor_produk");
            die();
        }
    
        //check already reported
        $cplm = $this->cplm->check($nation_code, $pelanggan->id, $c_produk_id);
        if (isset($cplm->nation_code)) {
            $this->status = 946;
            $this->message = 'You have reported this product';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "lapor_produk");
            die();
        }
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "lapor_produk");
    }
    public function baru()
    {
        //initial
        $dt = $this->__init();
        $data = array();
        
        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "lapor_produk");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "lapor_produk");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "lapor_produk");
            die();
        }
    
        //collecting input
        $c_produk_id = $this->input->post("c_produk_id");
        $kategori = strip_tags($this->input->post("kategori"));
        $kategori_sub = strip_tags($this->input->post("kategori_sub"));
        $deskripsi = strip_tags($this->input->post("deskripsi"));
    
        //sanitize
        if (empty($kategori)) {
            $kategori = '';
        }
        if (empty($kategori_sub)) {
            $kategori_sub = '';
        }
        if (empty($deskripsi)) {
            $deskripsi = '';
        }
    
        //check produk
        $getProductType = $this->cpm->getProductType($nation_code, $c_produk_id);
        if(!isset($getProductType->product_type)){
            $this->status = 948;
            $this->message = 'Product does not exist';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "lapor_produk");
            die();
        }

        $getProductType = $getProductType->product_type;

        $cpm = $this->cpm->getById($nation_code, $c_produk_id, $pelanggan, $getProductType);
        if (!isset($cpm->id)) {
            $this->status = 948;
            $this->message = 'Product does not exist';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "lapor_produk");
            die();
        }
    
        //check already take down
        $cplm = $this->cpm->checkTakeDown($nation_code, $c_produk_id);
        if (isset($cplm->nation_code)) {
            $this->status = 947;
            $this->message = 'this product has been convicted';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "lapor_produk");
            die();
        }
    
        //check already reported
        $cplm = $this->cplm->check($nation_code, $pelanggan->id, $c_produk_id);
        if (isset($cplm->nation_code)) {
            $this->status = 946;
            $this->message = 'You have reported this product';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "lapor_produk");
            die();
        }
    
        //insert
        $di = array();
        $di['nation_code'] = $nation_code;
        $di['b_user_id'] = $pelanggan->id;
        $di['c_produk_id'] = $c_produk_id;
        $di['kategori'] = $kategori;
        $di['kategori_sub'] = $kategori_sub;
        $di['deskripsi'] = $deskripsi;
        $di['cdate'] = 'NOW()';
    
        $res = $this->cplm->set($di);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
        } else {
            $this->status = 900;
            $this->message = 'cannot report the product at this time';
        }
    
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "lapor_produk");
    }

    //by Donny Dennison 26 november 2021 17:42
    //after success report product, redirect to empty page
    public function successredirect()
    {
        
    }

}
