<?php
class History_transaction extends JI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
    	$this->load("api_mobile/b_user_model", "bu");
        $this->load("api_for_wallet/g_leaderboard_point_history_model", 'glphm');
        $this->load("api_mobile/g_leaderboard_point_total_model", "glptm");
    }

    public function index()
    {
        //default result
        $data = array();
        $data["user_data"] = new stdClass();
        $data["transaction_data"] = array();

        //response message
        $this->status = 200;
        $this->message = 'Success';

        $nation_code = "62";

        $pass = $this->input->get('pass');
        if ($pass != 'igvd0lklr2') {
            $this->status = 200;
            $this->message = "Success";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $user_wallet_code = $this->input->get('user_wallet_code');
        $pelanggan = $this->bu->getByUserWalletCodeNew($nation_code, $user_wallet_code);
        if (!isset($pelanggan->id)) {
          $this->status = 200;
          $this->message = 'Success';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
          die();
        }

        $pelanggan->spt_balance = "0";
        $getPointNow = $this->glptm->getByUserId($nation_code, $pelanggan->id);
        if(isset($getPointNow->b_user_id)){
            $pelanggan->spt_balance = (string) number_format($getPointNow->total_point, 0, ',', '.');
        }
        unset($getPointNow);

        $pelanggan->recommenderFnama = "";
        $pelanggan->recommenderEmail = "";
        $pelanggan->recommenderTelp = "";
        $pelanggan->recommenderCdate = "";
        if($pelanggan->b_user_id_recruiter != '0'){
            $recommenderData = $this->bu->getById($nation_code, $pelanggan->b_user_id_recruiter);
            if(isset($recommenderData->kode_referral)){
                $pelanggan->recommenderFnama = $recommenderData->fnama;
                $pelanggan->recommenderEmail = $recommenderData->email;
                $pelanggan->recommenderTelp = $recommenderData->telp;
                $pelanggan->recommenderCdate = $recommenderData->cdate;
            }
            unset($recommenderData);
        }

        $data["user_data"] = array(
            "fnama" => $pelanggan->fnama,
            "email" => $pelanggan->email,
            "telp" => $pelanggan->telp,
            "cdate" => $pelanggan->cdate,
            "spt_balance" => $pelanggan->spt_balance,
            "recommenderFnama" => $pelanggan->recommenderFnama,
            "recommenderEmail" => $pelanggan->recommenderEmail,
            "recommenderTelp" => $pelanggan->recommenderTelp,
            "recommenderCdate" => $pelanggan->recommenderCdate,
        );

        $data["transaction_data"] = $this->glphm->getCustom($nation_code, $pelanggan->id);

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "history_transaction");
    }

    public function getbalance()
    {
        //default result
        $data = "0";
        // $data["spt_balance"] = new stdClass();
        // $data["transaction_data"] = array();

        //response message
        $this->status = 200;
        $this->message = 'Success';

        $nation_code = "62";

        $pass = $this->input->get('pass');
        if ($pass != 'f24q4wx3s7') {
            $this->status = 200;
            $this->message = "Success";
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
            die();
        }

        $user_wallet_code = $this->input->get('user_wallet_code');
        $pelanggan = $this->bu->getByUserWalletCodeNew($nation_code, $user_wallet_code);
        if (!isset($pelanggan->id)) {
          $this->status = 200;
          $this->message = 'Success';
          $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "general");
          die();
        }

        $getPointNow = $this->glptm->getByUserId($nation_code, $pelanggan->id);
        if(isset($getPointNow->b_user_id)){
            $data = (string) number_format($getPointNow->total_point, 0, ',', '.');
        }
        unset($getPointNow);

        //render as json
        $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "history_transaction");
    }
}
