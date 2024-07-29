<?php

class Marketingdailyprogress extends JI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lib('site_config');
        $this->lib("seme_log");
        $this->load("api_admin/g_daily_track_record_model", "gdtrm");
        $this->current_parent = 'marketingdailyprogress';
        $this->current_page = 'marketingdailyprogress';
        $this->media_user = $this->site_config->media_user;
    }

    private function __check_environment() {
        $this->__init();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out(array());
            die();
        }
    }

    public function index()
    {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $sSearch = $this->input->post("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $pagesize = $this->input->post("iDisplayLength");

        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");

        $from_date = $this->input->post("from_date");
        $to_date = $this->input->post("to_date");


        // $sortCol = "cdate";
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }

        if (empty($draw)) {
            $draw = 0;
        }
        if (empty($pagesize)) {
            $pagesize=10;
        }
        if (empty($page)) {
            $page=0;
        }
        $keyword = $sSearch;

        if (strlen($from_date)==10) {
            $from_date = date("Y-m-d", strtotime($from_date));
        } else {
            $from_date = "";
        }
        if (strlen($to_date)==10) {
            $to_date = date("Y-m-d", strtotime($to_date));
        } else {
            $to_date = "";
        }

        $this->status = 200;
        $this->message = 'Success';

        $dcount = $this->gdtrm->countAll($nation_code, $keyword, $from_date, $to_date);
        $data = $this->gdtrm->getAll($nation_code, $page, $pagesize, "cdate", $sortDir, $keyword, $from_date, $to_date);

        $ddata = array();
        foreach($data as $gd){

            $totalUserCommunityProduct = $this->gdtrm->sumTotal($nation_code, "", $gd->cdate);

            //ios
            $ddata[] = array(
                "cdate" => date("d M y", strtotime($gd->cdate)),
                "mobile_type" => "ios",
                "visit" => $gd->visit_ios,
                "visit_total" => '',
                "signuporlogin" => $gd->signup_ios,
                "signuporlogin_total" => '',
                "user" => '',
                "user_total" => '',
                "community" => '',
                "community_total" => '',
            );

            //android
            $ddata[] = array(
                "cdate" => date("d M y", strtotime($gd->cdate)),
                "mobile_type" => "android",
                "visit" => $gd->visit_android,
                "visit_total" => $gd->visit,
                "signuporlogin" => $gd->signup_android,
                "signuporlogin_total" => $gd->signup,
                "user" => $gd->signup,
                "user_total" => $totalUserCommunityProduct->sum_signup,
                "community" => $gd->community_post,
                "community_total" => $totalUserCommunityProduct->sum_community_post,
            );
        }

        $this->__jsonDataTable($ddata, $dcount);
    }

}
