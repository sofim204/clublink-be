<?php
class Bank extends JI_Controller
{
    public $is_log = 1;

    public function __construct()
    {
        parent:: __construct();
        $this->lib("seme_log");
        $this->load("api_mobile/a_bank_model", "abm");
        $this->load("api_mobile/b_user_model", "bu");
        $this->load("api_mobile/b_user_bankacc_model", "bubam");
    }
    public function index()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['bank'] = new stdClass();
        $data['bank']->bank = '';
        $data['bank']->nama = '';
        $data['bank']->norek = '';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bank");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bank");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bank");
            die();
        }
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/Bank::index --UserID: $pelanggan->id");
        }

        $count = $this->bubam->countByUserId($nation_code, $pelanggan->id);
        if ($count==1) {
            $dt = $this->bubam->getByUserId($nation_code, $pelanggan->id);
            if (isset($dt->a_bank_id)) {
                $data['bank']->a_bank_id = $dt->a_bank_id;
            }
            if (isset($dt->bank)) {
                $data['bank']->bank = $dt->bank;
            }
            if (isset($dt->nama)) {
                $data['bank']->nama = $dt->nama;
            }
            if (isset($dt->norek)) {
                $data['bank']->norek = $dt->norek;
            }
            if ($this->is_log) {
                $this->seme_log->write("api_mobile", "API_Mobile/Bank::index --bankAccount DONE ");
            }
        }

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bank");
    }

    public function daftar()
    {
        //init
        $dt = $this->__init();

        //default result format
        $data = array();
        $data['bank'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bank");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bank");
            die();
        }
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/Bank::list --bankList DONE");
        }


        $this->status = 200;
        $this->message = "Success";

        $data = array();
        $data['bank'] = $this->abm->getActive($nation_code);
        foreach ($data['bank'] as &$b) {
            if (isset($b->nama)) {
                $enc = mb_detect_encoding($b->nama);
                $b->nama = iconv($enc, 'ISO-8859-1//IGNORE', utf8_encode($b->nama));
            }
        }
        $this->status = 200;
        $this->message = "Success";
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bank");
    }
    public function set()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        $data['bank'] = new stdClass();
        $data['bank']->bank = '';
        $data['bank']->nama = '';
        $data['bank']->norek = '';

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bank");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bank");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bank");
            die();
        }
        if ($this->is_log) {
            $this->seme_log->write("api_mobile", "API_Mobile/Bank::set --UserID: $pelanggan->id");
        }

        //collect input
        $nama = $this->input->post("nama");
        $nomor = $this->input->post("nomor");

        //string length validation
        if ($this->__mbLen($nama)>=32) {
            $this->status = 2001;
            $this->message = 'Bank account name (holder) too long';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bank");
            die();
        }
        if ($this->__mbLen($nomor)>=64) {
            $this->status = 2002;
            $this->message = 'Bank account number too long';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bank");
            die();
        }


        $count = $this->bubam->countByUserId($nation_code, $pelanggan->id);
        if ($count==1) {
            $du = array();
            $du['nation_code'] = $nation_code;
            $du['b_user_id'] = $pelanggan->id;
            $du['a_bank_id'] = (int) $this->input->post("a_bank_id");
            $du['nama'] = $nama;
            $du['nomor'] = $nomor;
            if ($du['a_bank_id']>0) {
                $this->bubam->update($nation_code, $pelanggan->id, $du);
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Bank::set Bank Account UPDATED: DONE");
                }
            }
        } else {
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['b_user_id'] = $pelanggan->id;
            $di['a_bank_id'] = (int) $this->input->post("a_bank_id");
            $di['nama'] = $nama;
            $di['nomor'] = $nomor;
            if ($di['a_bank_id']>0) {
                $this->bubam->set($di);
                if ($this->is_log) {
                    $this->seme_log->write("api_mobile", "API_Mobile/Bank::set Bank Account INSERTED: DONE");
                }
            }
        }
        $dt = $this->bubam->getByUserId($nation_code, $pelanggan->id);
        if (isset($dt->bank)) {
            $enc = mb_detect_encoding($dt->bank);
            $data['bank']->bank = iconv($enc, 'ISO-8859-1//IGNORE', utf8_encode($dt->bank));
        }
        if (isset($dt->a_bank_id)) {
            $data['bank']->a_bank_id = $dt->a_bank_id;
        }
        if (isset($dt->nama)) {
            $data['bank']->nama = $dt->nama;
        }
        if (isset($dt->nama)) {
            $data['bank']->nama = $dt->nama;
        }
        if (isset($dt->norek)) {
            $data['bank']->norek = $dt->norek;
        }

        $this->status = 200;
        $this->message = 'Success';
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "bank");
    }
}
