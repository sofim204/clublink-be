<?php

class Marketingdailyprogress extends JI_Controller
{
    // public $media_user = '';
    // public $kode_pattern = '%010d';
    // public $email_send = 1;

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
        // $tbl_as = $this->gmdpm->getTblAlias();

        // switch ($iSortCol_0) {
        //     case 0:
        //         $sortCol = "$tbl_as.cdate";
        //         break;
        //     case 1:
        //         $sortCol = "$tbl_as.mobile_type";
        //         break;
        //     case 2:
        //         $sortCol = "$tbl_as.visit";
        //         break;
        //     case 3:
        //         $sortCol = "$tbl_as.visit_total";
        //         break;
        //     case 4:
        //         $sortCol = "$tbl_as.signuporlogin";
        //         break;
        //     case 5:
        //         $sortCol = "$tbl_as.signuporlogin_total";
        //         break;
        //     case 6:
        //         $sortCol = "$tbl_as.user";
        //         break;
        //     case 7:
        //         $sortCol = "$tbl_as.user_total";
        //         break;
        //     case 8:
        //         $sortCol = "$tbl_as.community";
        //         break;
        //     case 9:
        //         $sortCol = "$tbl_as.community_total";
        //         break;
        //     case 10:
        //         $sortCol = "$tbl_as.product";
        //         break;
        //     case 11:
        //         $sortCol = "$tbl_as.product_total";
        //         break;
        //     default:
        //         $sortCol = "$tbl_as.cdate";
        // }

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

        //advanced filter
        // $is_published = "";
        // $is_active = "";
        // $pelanggan_status = $this->input->post("pelanggan_status");
        // switch ($pelanggan_status) {
        //     case 'active':
        //         $is_published=1;
        //         $is_active=1;
        //         break;
        //     case 'inactive':
        //         $is_published = "";
        //         $is_active=0;
        //         break;
        //     default:
        //         $is_published = "";
        //         $is_active = "";
        //         break;
        // }
        // $is_confirmed = $this->input->post("is_confirmed");
        // if (strlen($is_confirmed)>0) {
        //     $is_confirmed = intval($is_confirmed);
        //     if (!empty($is_confirmed)) {
        //         $is_confirmed=1;
        //     }
        // } else {
        //     $is_confirmed="";
        // }

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
                "product" => '',
                "product_total" => ''
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
                "product" => $gd->product_post,
                "product_total" => $totalUserCommunityProduct->sum_product_post
            );

        }

        $this->__jsonDataTable($ddata, $dcount);
    }

}
