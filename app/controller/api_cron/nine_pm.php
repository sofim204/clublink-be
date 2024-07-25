<?php
// class Nine_pm extends JI_Controller
// {
    
//     public function __construct()
//     {
//         parent::__construct();
//         $this->lib("seme_log");
//         $this->load("api_admin/b_user_model", 'bum');
//         $this->load("api_mobile/g_leaderboard_point_limit_model", 'glplm');
//         $this->load("api_mobile/common_code_model", "ccm");

//     }
//     public function index()
//     {
//         //open transaction
//         // $this->glplm->trans_start();

//         //change log filename
//         $this->seme_log->changeFilename("cron.log");

//         //put on log
//         $this->seme_log->write("api_cron", "API_Cron/nine_pm::index Start");

//         $nation_code = 62;

//         $listCustomer = $this->bum->getAll($nation_code, -1, -1, "", "", '', "", 1);
        
//         foreach($listCustomer as $customer){

//             $codes = array(
//                 0=> "EH",
//                 1=> "EJ"
//             );

//             foreach($codes AS $code){

//                 $limitExist = $this->glplm->getByUserId($nation_code, date("Y-m-d", strtotime("+1 day")), $customer->id, $code);

//                 if(!isset($limitExist->limit_plus)){

//                     $pointGet = $this->ccm->getByClassifiedAndCode($nation_code, "leaderboard_point", $code);

//                     $lastID = $this->glplm->getLastId($nation_code, date("Y-m-d", strtotime("+1 day")));

//                     $du = array();
//                     $du['nation_code'] = $customer->nation_code;
//                     $du['id'] = $lastID;
//                     $du['cdate'] = date("Y-m-d", strtotime("+1 day"));
//                     $du['b_user_id'] = $customer->id;
//                     $du['code'] = $code;
//                     $du['limit_plus'] = $pointGet->remark;
//                     $du['limit_minus'] = $pointGet->remark;
//                     $this->glplm->set($du);
//                     // $this->glplm->trans_commit();

//                 }
//             }

//         }

//         //end transacation
//         // $this->glplm->trans_end();

//         $this->seme_log->write("api_cron", "API_Cron/nine_pm::index Stop");
//     }
// }
