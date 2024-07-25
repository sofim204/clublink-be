<?php
class Midnight extends JI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_cron/f_visitor_model", "fvm");

        // $this->load("api_mobile/g_highlight_community_model", "ghcm");

        $this->load("api_mobile/g_leaderboard_point_limit_model", 'glplm');

        $this->load("api_mobile/custom_log_model", "clm");

        //by Donny Dennison 24 december 2022 23:30
        //only keep one month data in bell notification
        $this->load("api_cron/d_pemberitahuan_model", "dpem");
        $this->load("api_mobile/g_daily_track_record_model", 'gdtrm');

        $this->load("api_cron/b_user_model", "bu");
        $this->load("api_cron/c_community_like_history_model", "cclhm");
        $this->load("api_cron/common_code_model", "ccm");
    }

    public function index()
    {
        //open transaction
        // $this->fvm->trans_start();

        //change log filename
        // $this->seme_log->changeFilename("cron.log");

        //put on log
        $this->seme_log->write("api_cron", "API_Cron/Midnight::index Start");

        $nation_code = 62;

        //android
        $check = 0;
        $getLatestVisit = $this->fvm->getLatestVisit($nation_code, 'android');
        if(!isset($getLatestVisit->cdate)){

            $check = 1;

        }else{

            if($getLatestVisit->cdate != date("Y-m-d", strtotime("+1 day"))){

                $check = 1;

            }

        }

        if($check == 1){
            
            //insert visitor date for android
            $lastID = $this->fvm->getLastId($nation_code);
            $du = array();
            $du['nation_code'] = $nation_code;
            $du['id'] =$lastID;
            $du['mobile_type'] = 'android';
            $du['cdate'] = date("Y-m-d", strtotime("+1 day"));
            $this->fvm->set($du);
            // $this->fvm->trans_commit();

        }

        //ios
        $check = 0;
        $getLatestVisit = $this->fvm->getLatestVisit($nation_code, 'ios');
        if(!isset($getLatestVisit->cdate)){

            $check = 1;

        }else{

            if($getLatestVisit->cdate != date("Y-m-d", strtotime("+1 day"))){

                $check = 1;

            }

        }

        if($check == 1){
             
            //insert visitor date for ios
            $lastID = $this->fvm->getLastId($nation_code);
            $du = array();
            $du['nation_code'] = $nation_code;
            $du['id'] =$lastID;
            $du['mobile_type'] = 'ios';
            $du['cdate'] = date("Y-m-d", strtotime("+1 day"));
            $this->fvm->set($du);
            // $this->fvm->trans_commit();
        }

        //inactive highlight community past end date
        // $this->ghcm->inactiveExpired();
        // $this->fvm->trans_commit();

        // start by muhammad sofi 9 January 2023 | create data g_daily_track_record

        $check = 0;
        $getLatestRecord = $this->gdtrm->getLatestRecord($nation_code);
        if(!isset($getLatestRecord->cdate)){

            $check = 1;

        }else{

            if($getLatestRecord->cdate != date("Y-m-d", strtotime("+1 day"))){

                $check = 1;

            }

        }

        if($check == 1){
            $lastID = $this->gdtrm->getLastId($nation_code);
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['id'] = $lastID;
            $di['cdate'] = date("Y-m-d", strtotime("+1 day"));
            $di['signup'] = '0';
            $di['signup_android'] = '0';
            $di['signup_ios'] = '0';
            $di['community_post'] = '0';
            $di['community_video'] = '0';
            $di['product_post'] = '0';
            $di['product_video'] = '0';
            $di['visit'] = '0';
            $di['visit_android'] = '0';
            $di['visit_ios'] = '0';
            $this->gdtrm->set($di);
        }    

        // end by muhammad sofi 9 January 2023 | create data g_daily_track_record

        //delete old point limit
        $this->glplm->del($nation_code, date("Y-m-d", strtotime("-2 days")));
        // $this->fvm->trans_commit();

        //end transacation
        // $this->fvm->trans_end();

        //delete temporary file more than 24 hours
        //reference : https://stackoverflow.com/a/2205784/7578520
        $path = $this->media_temporary;
        if ($handle = opendir($path)) {

            while (false !== ($file = readdir($handle))) { 
                $filelastmodified = filemtime($path . $file);
                //24 hours in a day * 3600 seconds per hour
                if((time() - $filelastmodified) > 24*3600 && is_file($path .$file)){
                   unlink($path . $file);
                }

            }

            closedir($handle); 
        }

        // Start by Muhammad Sofi 23 December 2022 | change delete log more than 3 days
        //delete log more than 7 days
        // $this->clm->del($nation_code, date("Y-m-d", strtotime("-3 days")));

        //by Donny Dennison 24 december 2022 23:30
        //only keep one month data in bell notification
        // $this->dpem->delKeepOneMonth($nation_code);

        $this->seme_log->write("api_cron", "API_Cron/Midnight::index Stop");

        die();
    }

    public function gamefm5jrmbg0c()
    {
        $this->seme_log->write("api_cron", "API_Cron/Midnight/game::index Start");

        $nation_code = 62;

        //truncate
        $this->cclhm->del();

        //reset point to 20
        $free_ticket_rock_paper_scissors = $this->ccm->getByClassifiedAndCode($nation_code, "game", "I1");
        if (!isset($free_ticket_rock_paper_scissors->remark)) {
          $free_ticket_rock_paper_scissors = new stdClass();
          $free_ticket_rock_paper_scissors->remark = 3;
        }

        //reset point to 5
        $free_ticket_shooting_fire = $this->ccm->getByClassifiedAndCode($nation_code, "game", "I3");
        if (!isset($free_ticket_shooting_fire->remark)) {
          $free_ticket_shooting_fire = new stdClass();
          $free_ticket_shooting_fire->remark = 1;
        }

        $du = array(
            "free_ticket_rock_paper_scissors" => $free_ticket_rock_paper_scissors->remark,
            "free_ticket_shooting_fire" => $free_ticket_shooting_fire->remark
        );
        $this->bu->updateFreeTicket($nation_code, $du);

        $this->seme_log->write("api_cron", "API_Cron/Midnight/game::index Stop");

        die();
    }

}
