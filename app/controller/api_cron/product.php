<?php
class Product extends JI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_cron/c_produk_model", "cpm");
        $this->load("api_cron/c_product_attachment_video_list_model", "cpavlm");

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

    public function create_video_list()
    {
        $this->seme_log->write("api_cron", "API_Cron/Product/create_video_list::index Start");

        $nation_code = 62;

        $getList = $this->cpm->getAllVideoManualQuery($nation_code, 11000);

        $insertArray = array();
        $id = 1;
        foreach($getList AS $list){
            $di = array();
            $di['nation_code'] = $nation_code;
            $di['id'] = $id;
            $di['c_produk_id'] = $list->id;
            $di['c_produk_attachment_id'] = $list->video_id;
            $di['cdate'] = $list->cdate;
            $di['cron_cdate'] = "NOW()";
            $insertArray[] = $di;
            $id++;
        }
        unset($getList, $list, $id);

        if(count($insertArray) > 0){
            $this->cpavlm->del();

            $chunkInsertArray = array_chunk($insertArray,100);
            foreach($chunkInsertArray AS $chunk){
                $this->cpavlm->setMass($chunk);
            }
            unset($chunkInsertArray, $chunk);
        }
        unset($insertArray);

        $this->seme_log->write("api_cron", "API_Cron/Product/create_video_list::index Stop");

        die();
    }
}
