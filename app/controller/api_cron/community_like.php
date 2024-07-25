<?php
class Community_like extends JI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_cron/c_community_model", "ccomm");
        $this->load("api_cron/c_community_fake_like_model", "ccflm");

    }

    public function index()
    {
        // //open transaction
        // // $this->fvm->trans_start();

        // //change log filename
        // // $this->seme_log->changeFilename("cron.log");

        // //put on log
        // $this->seme_log->write("api_cron", "API_Cron/Community_like::index Start");

        // $nation_code = 62;

        // //get
        // $getList = $this->ccomm->getAll($nation_code, 1, 100);
        // if(count($getList) > 0){

        //     foreach($getList AS $list){

        //         $lastID = $this->fvm->getLastId($nation_code);
        //         if(){

        //         }

        //         $du = array();
        //         $du['total_likes'] = $list->total_likesa;
        //         // $du['cdate'] = date("Y-m-d", strtotime("+1 day"));
        //         $this->ccomm->update($nation_code, $list->id, $du);

        //     }

        // }

        // // $this->fvm->trans_commit();

        // //end transacation
        // // $this->fvm->trans_end();

        // $this->seme_log->write("api_cron", "API_Cron/Community_like::index Stop");

        // die();
    }

    public function one_min()
    {
        //open transaction
        // $this->fvm->trans_start();

        //change log filename
        // $this->seme_log->changeFilename("cron.log");

        //put on log
        $this->seme_log->write("api_cron", "API_Cron/Community_like/one_min::index Start");

        $nation_code = 62;

        //increate total likes if below 100 after 1 hour
        $getList = $this->ccomm->getAll($nation_code, 25, "one_min");
        if(count($getList) > 0){

            foreach($getList AS $list){

                $random = rand(5,25);

                $di = array();
                $di['nation_code'] = $nation_code;
                $di['c_community_id'] = $list->id;
                $di['total_likes'] = $random;
                $this->ccflm->set($di);

                $list->total_likes += $random;

                $du = array();
                $du['total_likes'] = $list->total_likes;
                $this->ccomm->update($nation_code, $list->id, $du);

            }

        }

        // $this->fvm->trans_commit();

        //end transacation
        // $this->fvm->trans_end();

        $this->seme_log->write("api_cron", "API_Cron/Community_like/one_min::index Stop");

        die();
    }

    public function night()
    {
        //open transaction
        // $this->fvm->trans_start();

        //change log filename
        // $this->seme_log->changeFilename("cron.log");

        //put on log
        $this->seme_log->write("api_cron", "API_Cron/Community_like/night::index Start");

        $nation_code = 62;

        //increate total likes if below 500 yesterday data
        $getList = $this->ccomm->getAll($nation_code, 500, "night");
        if(count($getList) > 0){

            foreach($getList AS $list){

                $random = rand(100,500);

                $checkAlreadyInDB = $this->ccflm->checkAlreadyInDB($nation_code, $list->id);
                if(!isset($checkAlreadyInDB->nation_code)){

                    $di = array();
                    $di['nation_code'] = $nation_code;
                    $di['c_community_id'] = $list->id;
                    $di['total_likes'] = $random;
                    $this->ccflm->set($di);

                }else{

                    $du = array();
                    $du['total_likes'] = $checkAlreadyInDB->total_likes + $random;
                    $this->ccflm->update($nation_code, $list->id, $du);

                }

                $list->total_likes += $random;

                $du = array();
                $du['total_likes'] = $list->total_likes;
                $this->ccomm->update($nation_code, $list->id, $du);

            }

        }

        // $this->fvm->trans_commit();

        //end transacation
        // $this->fvm->trans_end();

        $this->seme_log->write("api_cron", "API_Cron/Community_like/night::index Stop");

        die();
    }

    public function custom($min, $max, $from, $to)
    {
        //open transaction
        // $this->fvm->trans_start();

        //change log filename
        // $this->seme_log->changeFilename("cron.log");

        //put on log
        $this->seme_log->write("api_cron", "API_Cron/Community_like/custom::index Start");

        $nation_code = 62;

        //check activation code
        $activation_code = $this->input->get('activation_code');
        if ($activation_code != '21F2E429ABD0404B46953A9BEE5E5FCEB279FDEC40788263991744E5931AA6D7P@$$w0rd!') {
            $this->status = 3000;
            $this->message = 'Wrong Activation Code';
            $this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "delete_order");
            die();
        }

        //increate total likes if custom condition
        $getList = $this->ccomm->getAll($nation_code, $max, "custom", $from, $to);
        if(count($getList) > 0){

            foreach($getList AS $list){

                $random = rand($min,$max);

                $checkAlreadyInDB = $this->ccflm->checkAlreadyInDB($nation_code, $list->id);
                if(!isset($checkAlreadyInDB->nation_code)){

                    $di = array();
                    $di['nation_code'] = $nation_code;
                    $di['c_community_id'] = $list->id;
                    $di['total_likes'] = $random;
                    $this->ccflm->set($di);

                }else{

                    $du = array();
                    $du['total_likes'] = $checkAlreadyInDB->total_likes + $random;
                    $this->ccflm->update($nation_code, $list->id, $du);

                }

                $list->total_likes += $random;

                $du = array();
                $du['total_likes'] = $list->total_likes;
                $this->ccomm->update($nation_code, $list->id, $du);

            }

        }

        // $this->fvm->trans_commit();

        //end transacation
        // $this->fvm->trans_end();

        $this->seme_log->write("api_cron", "API_Cron/Community_like/custom::index Stop");

        die();
    }

}
