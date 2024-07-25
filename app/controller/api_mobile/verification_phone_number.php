<?php

class Verification_phone_number extends JI_Controller
{
    public $email_send = 1;
    public $is_log = 1;

    public function __construct()
    {
        parent::__construct();
        //$this->setTheme('frontx');
        $this->lib("seme_log");
        $this->lib('seme_email');
        $this->load("api_mobile/b_user_model", "bu");
        $this->load("api_mobile/f_verification_phone_number_model", "fvpnm");
    }

    public function index()
    {
        // $this->send_sms_mega_media_digipro('123','85260783389');
    }

    public function send_sms_registration()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        // $data['order_total'] = 0;
        // $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
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

        //check email and phone number
        $email = strtolower(trim($this->input->post("email")));
        $phone_number = $this->input->post('phone_number');

        if(!empty($email)){

            $user = $this->bu->checkEmailTelp($nation_code, $email, $phone_number);
            if (!isset($user->id)) {
                $this->status = 102;
                $this->message = 'Email and phone number is different';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
                die();
            }

        }else{

            $user = $this->bu->checkTelp($nation_code, $phone_number);
            if (!isset($user->id)) {
                $this->status = 1703;
                $this->message = 'Phone number not found, please try another phone number';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
                die();
            }

        }

        $sms_code = $this->input->post('sms_code');

        //generate random and save it to db

        $random_generated = '';

        $keyspace = '123456789';

        $endDoWhile = 0;
        
        do{

            $random_generated = '';

            for ($i = 0; $i < 6; $i++) {
                $random_generated .= $keyspace[random_int(0, 8)];
            }

            $checkVerificationInDB = $this->fvpnm->checkVerificationNumber($nation_code, $random_generated);

        	if(!isset($checkVerificationInDB->id)){
                $endDoWhile = 1;
            }

        }while($endDoWhile == 0);


        $this->fvpnm->trans_start();

        //deactive previous verification code
        $this->fvpnm->deactiveByPhoneNumber($phone_number, array('is_active' => 0, 'edate' => date('Y-m-d')), $user->id);


        //build order history process
        $di = array();
        $di['nation_code'] = $nation_code;
        $di['b_user_id'] = $user->id;
        $di['telp'] = $phone_number;
        $di['verification_number'] = $random_generated;
        $di['cdate'] = "NOW()";

        $res = $this->fvpnm->set($di);
        if($res) {
            $this->fvpnm->trans_commit();
            $this->fvpnm->trans_end();

            //send the sms
            $this->send_sms_mega_media_digipro($random_generated,$phone_number,$sms_code);
            
            // $data['verification_number'] = $random_generated;
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");

        }else{
            //rollback table
            $this->fvpnm->trans_rollback();
            $this->fvpnm->trans_end();

            $this->status = 103;
            $this->message = 'Failed insert!';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");

        }

        $this->seme_log->write("api_mobile", 'SEND SMS REGISTRATION, POST DATA = '. json_encode($_POST).' , VERIFICATION NUMBER = '.$random_generated.' ,RESPONSE = '. $this->status.' '.$this->message.' '.json_encode($data));
    }

    public function confirm_sms_registration()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
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

        //check email and phone number
        $email = strtolower(trim($this->input->post("email")));
        $phone_number = $this->input->post('phone_number');

        if(!empty($email)){

            $user = $this->bu->checkEmailTelp($nation_code, $email, $phone_number);
            if (!isset($user->id)) {
                $this->status = 102;
                $this->message = 'Email and phone number is different';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
                die();
            }

        }else{

            $user = $this->bu->checkTelp($nation_code, $phone_number);
            if (!isset($user->id)) {
                $this->status = 1703;
                $this->message = 'Phone number not found, please try another phone number';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
                die();
            }

        }

        //check verification number
        $verification_number = $this->input->post('verification_number');
        if (empty($verification_number)) {
            $this->status = 107;
            $this->message = 'Missing or invalid Verification Number';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();
        }

        
        $checkVerificationInDB = $this->fvpnm->checkVerificationNumber($nation_code, $verification_number, $phone_number, $user->id);

        if(!isset($checkVerificationInDB->id)){
            $this->status = 109;
            $this->message = 'Wrong Verification Number! Please resend Verification!';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();

        }

        $this->fvpnm->trans_start();

        //build order history process
        $di = array();
        $di['is_confirmed'] = 1;
        $di['adate'] = "NOW()";

        $res = $this->fvpnm->update($checkVerificationInDB->id, $di);
        if($res) {
            $this->fvpnm->trans_commit();
            $this->fvpnm->trans_end();

            //populating input
            $du = array();
            $du['telp_is_verif'] = 1;
            $res = $this->bu->update($nation_code, $user->id, $du);

            $data['telp_is_verif'] = 1;
            $data['newest_phone_number'] = $phone_number;

            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");

        }else{
            //rollback table
            $this->fvpnm->trans_rollback();
            $this->fvpnm->trans_end();

            $this->status = 108;
            $this->message = 'Failed Verif!';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");

        }

        $this->seme_log->write("api_mobile", 'CONFIRM SMS REGISTRATION, POST DATA = '. json_encode($_POST).' ,RESPONSE = '. $this->status.' '.$this->message.' '.json_encode($data));
    }

    public function send_sms_edit_profile()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();
        // $data['order_total'] = 0;
        // $data['orders'] = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();
        }

        //check phone number
        $phone_number = $this->input->post('phone_number');
        $check_phone_number = $this->bu->checkTelpIgnoreActive($nation_code, $phone_number);
        if (isset($check_phone_number->id)) {
            $this->status = 103;
            $this->message = 'Phone Number already registered';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();
        }

        $sms_code = $this->input->post('sms_code');

        //generate random and save it to db

        $random_generated = '';

        $keyspace = '123456789';

        $endDoWhile = 0;
        
        do{

            $random_generated = '';

            for ($i = 0; $i < 6; $i++) {
                $random_generated .= $keyspace[random_int(0, 8)];
            }

            $checkVerificationInDB = $this->fvpnm->checkVerificationNumber($nation_code, $random_generated);

        	if(!isset($checkVerificationInDB->id)){
                $endDoWhile = 1;
            }

        }while($endDoWhile == 0);


        $this->fvpnm->trans_start();

        //deactive previous verification code
        $this->fvpnm->deactiveByPhoneNumber($phone_number, array('is_active' => 0, 'edate' => date('Y-m-d')), $pelanggan->id);


        //build order history process
        $di = array();
        $di['nation_code'] = $nation_code;
        $di['b_user_id'] = $pelanggan->id;
        $di['telp'] = $phone_number;
        $di['verification_number'] = $random_generated;
        $di['cdate'] = "NOW()";

        $res = $this->fvpnm->set($di);
        if($res) {
            $this->fvpnm->trans_commit();
            $this->fvpnm->trans_end();

            //populating input
            // $du = array();
            // $du['telp_is_verif'] = 0;
            // $this->bu->update($nation_code, $pelanggan->id, $du);

            //send the sms
            $this->send_sms_mega_media_digipro($random_generated,$phone_number,$sms_code);

            // $data['verification_number'] = $random_generated;
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");

        }else{
            //rollback table
            $this->fvpnm->trans_rollback();
            $this->fvpnm->trans_end();

            $this->status = 103;
            $this->message = 'Failed insert!';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");

        }
    }

    public function confirm_sms_edit_profile()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();
        }

        //check apisess
        $apisess = $this->input->get('apisess');
        $pelanggan = $this->bu->getByToken($nation_code, $apisess, 'api_mobile');
        if (!isset($pelanggan->id)) {
            $this->status = 401;
            $this->message = 'Missing or invalid API session';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();
        }

        //check phone number
        $phone_number = $this->input->post('phone_number');
        if (empty($phone_number)) {
            $this->status = 104;
            $this->message = 'Missing or invalid Phone Number';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();
        }

        //check verification number
        $verification_number = $this->input->post('verification_number');
        if (empty($verification_number)) {
            $this->status = 103;
            $this->message = 'Missing or invalid Verification Number';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();
        }

        
        $checkVerificationInDB = $this->fvpnm->checkVerificationNumber($nation_code, $verification_number, $phone_number, $pelanggan->id);

        if(!isset($checkVerificationInDB->id)){
            $this->status = 109;
            $this->message = 'Wrong Verification Number! Please resend Verification!';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();

        }

        $this->fvpnm->trans_start();

        //build order history process
        $di = array();
        $di['is_confirmed'] = 1;
        $di['adate'] = "NOW()";

        $res = $this->fvpnm->update($checkVerificationInDB->id, $di);
        if($res) {
            $this->fvpnm->trans_commit();
            $this->fvpnm->trans_end();

            //populating input
            $du = array();
            $du['telp_is_verif'] = 1;
            $this->bu->update($nation_code, $pelanggan->id, $du);

            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");

        }else{
            //rollback table
            $this->fvpnm->trans_rollback();
            $this->fvpnm->trans_end();

            $this->status = 103;
            $this->message = 'Failed Verif!';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");

        }
    }

    public function change_phone_number()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();

        $this->seme_log->write("api_mobile", 'START CHANGE PHONE NUMBER, POST DATA = '. json_encode($_POST));

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
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

        //check email and phone number old
        $email = strtolower(trim($this->input->post("email")));
        $phone_number_old = $this->input->post('phone_number_old');

        if(!empty($email)){

            $user = $this->bu->checkEmailTelp($nation_code, $email, $phone_number_old);
            if (!isset($user->id)) {
                $this->status = 105;
                $this->message = 'Email and phone number old is different';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
                die();
            }

        }else{

            $user = $this->bu->checkTelp($nation_code, $phone_number_old);
            if (!isset($user->id)) {
                $this->status = 1703;
                $this->message = 'Phone number not found, please try another phone number';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
                die();
            }

        }

        $phone_number_new = $this->input->post('phone_number_new');
        $check_phone_number = $this->bu->checkTelpIgnoreActive($nation_code, $phone_number_new);
        if (isset($check_phone_number->id)) {
            $this->status = 103;
            $this->message = 'Phone Number already registered';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();
        }

        $this->fvpnm->trans_start();

        //deactive previous verification code
        $this->fvpnm->deactiveByPhoneNumber($phone_number_old, array('is_active' => 0, 'edate' => date('Y-m-d')), $user->id);

        //build order history process
        $di = array();
        $di['telp'] = $phone_number_new;
        $di['telp_is_verif'] = 0;

        $res = $this->bu->update($nation_code, $user->id, $di);
        if($res) {
            $this->fvpnm->trans_commit();
            $this->fvpnm->trans_end();

            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
        }else{
            //rollback table
            $this->fvpnm->trans_rollback();
            $this->fvpnm->trans_end();

            $this->status = 106;
            $this->message = 'Failed insert!';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
        }

        $this->seme_log->write("api_mobile", 'END CHANGE PHONE NUMBER, RESPONSE = '. $this->status.' '.$this->message.' '.json_encode($data));
    }

    public function send_sms_normal()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
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

        //check phone number
        $phone_number = $this->input->post('phone_number');
        $user = $this->bu->checkTelp($nation_code, $phone_number);
        if (!isset($user->id)) {
            $this->status = 1703;
            $this->message = 'Phone number not found, please try another phone number';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();
        }

        $sms_code = $this->input->post('sms_code');

        //generate random and save it to db

        $random_generated = '';

        $keyspace = '123456789';

        $endDoWhile = 0;
        
        do{

            $random_generated = '';

            for ($i = 0; $i < 6; $i++) {
                $random_generated .= $keyspace[random_int(0, 8)];
            }

            $checkVerificationInDB = $this->fvpnm->checkVerificationNumber($nation_code, $random_generated);

            if(!isset($checkVerificationInDB->id)){
                $endDoWhile = 1;
            }

        }while($endDoWhile == 0);

        $this->fvpnm->trans_start();

        //deactive previous verification code
        $this->fvpnm->deactiveByPhoneNumber($phone_number, array('is_active' => 0, 'edate' => date('Y-m-d')), $user->id);


        //build order history process
        $di = array();
        $di['nation_code'] = $nation_code;
        $di['b_user_id'] = $user->id;
        $di['telp'] = $phone_number;
        $di['verification_number'] = $random_generated;
        $di['cdate'] = "NOW()";

        $res = $this->fvpnm->set($di);
        if($res) {
            $this->fvpnm->trans_commit();
            $this->fvpnm->trans_end();

            //send the sms
            $this->send_sms_mega_media_digipro($random_generated,$phone_number,$sms_code);

            // $data['verification_number'] = $random_generated;
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");

        }else{
            //rollback table
            $this->fvpnm->trans_rollback();
            $this->fvpnm->trans_end();

            $this->status = 103;
            $this->message = 'Failed insert!';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");

        }

        $this->seme_log->write("api_mobile", 'SEND SMS, POST DATA = '. json_encode($_POST).' , VERIFICATION NUMBER = '.$random_generated.' ,RESPONSE = '. $this->status.' '.$this->message.' '.json_encode($data));
    }

    public function confirm_sms_normal()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
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

        //check phone number
        $phone_number = $this->input->post('phone_number');
        $user = $this->bu->checkTelp($nation_code, $phone_number);
        if (!isset($user->id)) {
            $this->status = 1703;
            $this->message = 'Phone number not found, please try another phone number';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();
        }

        //check verification number
        $verification_number = $this->input->post('verification_number');
        if (empty($verification_number)) {
            $this->status = 107;
            $this->message = 'Missing or invalid Verification Number';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();
        }

        $checkVerificationInDB = $this->fvpnm->checkVerificationNumber($nation_code, $verification_number, $phone_number, $user->id);

        if(!isset($checkVerificationInDB->id)){
            $this->status = 109;
            $this->message = 'Wrong Verification Number! Please resend Verification!';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();

        }

        // $this->fvpnm->trans_start();

        // //build order history process
        $di = array();
        $di['is_confirmed'] = 1;
        $di['adate'] = "NOW()";

        $res = $this->fvpnm->update($checkVerificationInDB->id, $di);
        // if($res) {
        //     $this->fvpnm->trans_commit();

            // //populating input
            // $du = array();
            // $du['telp_is_verif'] = 1;
            // $res = $this->bu->update($nation_code, $user->id, $du);

            // $data['telp_is_verif'] = 1;
            $data['newest_phone_number'] = $phone_number;

            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");

        // }else{
        //     //rollback table
        //     $this->fvpnm->trans_rollback();

        //     $this->status = 108;
        //     $this->message = 'Failed Verif!';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");

        // }

        $this->seme_log->write("api_mobile", 'CONFIRM SMS, POST DATA = '. json_encode($_POST).' ,RESPONSE = '. $this->status.' '.$this->message.' '.json_encode($data));

        //release table
        // $this->fvpnm->trans_end();
    }

    public function send_sms_registration_v2()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
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

        //check email
        $email = strtolower(trim($this->input->post("email")));
        if(!empty($email)){

            $user = $this->bu->checkEmailIgnoreActive($nation_code, $email);
            if (isset($user->id)) {
                $this->status = 1702;
                $this->message = 'Email is already registered. Please try again with another email';
                $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
                die();
            }

        }

        //check phone number
        $phone_number = $this->input->post('phone_number');
        $user = $this->bu->checkTelpIgnoreActive($nation_code, $phone_number);
        if (isset($user->id)) {
            $this->status = 1703;
            $this->message = 'Phone number already registered, please try another phone number';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        $sms_code = $this->input->post('sms_code');

        //generate random and save it to db

        $random_generated = '';

        $keyspace = '123456789';

        $endDoWhile = 0;
        
        do{

            $random_generated = '';

            for ($i = 0; $i < 6; $i++) {
                $random_generated .= $keyspace[random_int(0, 8)];
            }

            $checkVerificationInDB = $this->fvpnm->checkVerificationNumber($nation_code, $random_generated);

            if(!isset($checkVerificationInDB->id)){
                $endDoWhile = 1;
            }

        }while($endDoWhile == 0);

        $this->fvpnm->trans_start();

        //deactive previous verification code
        $this->fvpnm->deactiveByPhoneNumber($phone_number, array('is_active' => 0, 'edate' => date('Y-m-d')));

        //build order history process
        $di = array();
        $di['nation_code'] = $nation_code;
        $di['telp'] = $phone_number;
        $di['verification_number'] = $random_generated;
        $di['cdate'] = "NOW()";

        $res = $this->fvpnm->set($di);
        if($res) {
            $this->fvpnm->trans_commit();
            $this->fvpnm->trans_end();

            //send the sms
            $this->send_sms_mega_media_digipro($random_generated,$phone_number,$sms_code);

            // $data['verification_number'] = $random_generated;
            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");

        }else{
            //rollback table
            $this->fvpnm->trans_rollback();
            $this->fvpnm->trans_end();

            $this->status = 103;
            $this->message = 'Failed insert!';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");

        }

        $this->seme_log->write("api_mobile", 'SEND SMS, POST DATA = '. json_encode($_POST).' , VERIFICATION NUMBER = '.$random_generated.' ,RESPONSE = '. $this->status.' '.$this->message.' '.json_encode($data));
    }

    public function confirm_sms_registration_v2()
    {
        //initial
        $dt = $this->__init();

        //default result
        $data = array();

        //check nation_code
        $nation_code = $this->input->get('nation_code');
        $nation_code = $this->nation_check($nation_code);
        if (empty($nation_code)) {
            $this->status = 101;
            $this->message = 'Missing or invalid nation_code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();
        }

        //check apikey
        $apikey = $this->input->get('apikey');
        $c = $this->apikey_check($apikey);
        if (!$c) {
            $this->status = 400;
            $this->message = 'Missing or invalid API key';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
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

        //check phone number
        $phone_number = $this->input->post('phone_number');
        $user = $this->bu->checkTelp($nation_code, $phone_number);
        if (isset($user->id)) {
            $this->status = 1703;
            $this->message = 'Phone number already registered, please try another phone number';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "pelanggan");
            die();
        }

        //check verification number
        $verification_number = $this->input->post('verification_number');
        if (empty($verification_number)) {
            $this->status = 107;
            $this->message = 'Missing or invalid Verification Number';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();
        }

        $checkVerificationInDB = $this->fvpnm->checkVerificationNumber($nation_code, $verification_number, $phone_number);

        if(!isset($checkVerificationInDB->id)){
            $this->status = 109;
            $this->message = 'Wrong Verification Number! Please resend Verification!';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");
            die();

        }

        // $this->fvpnm->trans_start();

        // //build order history process
        $di = array();
        $di['is_confirmed'] = 1;
        $di['adate'] = "NOW()";

        $res = $this->fvpnm->update($checkVerificationInDB->id, $di);
        // if($res) {
        //     $this->fvpnm->trans_commit();

            // //populating input
            // $du = array();
            // $du['telp_is_verif'] = 1;
            // $res = $this->bu->update($nation_code, $user->id, $du);

            // $data['telp_is_verif'] = 1;
            $data['newest_phone_number'] = $phone_number;

            $this->status = 200;
            $this->message = 'Success';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");

        // }else{
        //     //rollback table
        //     $this->fvpnm->trans_rollback();

        //     $this->status = 108;
        //     $this->message = 'Failed Verif!';
        //     $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "verification_phone_number");

        // }

        $this->seme_log->write("api_mobile", 'CONFIRM SMS, POST DATA = '. json_encode($_POST).' ,RESPONSE = '. $this->status.' '.$this->message.' '.json_encode($data));

        //release table
        // $this->fvpnm->trans_end();
    }
}
