<?php
class Visitorcount extends JI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_admin/f_visitor_model", 'fvm');
        $this->current_parent = 'ecommerce';
        $this->current_page = 'ecommerce_visitorcount';
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

        //get table alias
        $tbl_as = $this->fvm->getTableAlias();
        $tbl2_as = $this->fvm->getTableAlias2();
        // $tbl3_as = $this->dodm->getTableAlias3();
        // $tbl4_as = $this->dodm->getTableAlias4();

        //standard input
        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $keyword = $this->input->post("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $pagesize = $this->input->post("iDisplayLength");
        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");

        //standard validation
        $sortCol = "cdate";
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }
        switch ($iSortCol_0) {
            case 0:
                $sortCol = "$tbl2_as.cdate";
                break;
            case 1:
                $sortCol = "$tbl2_as.id";
                break;
            case 2:
                $sortCol = "$tbl2_as.mobile_type";
                break;
            case 3:
                $sortCol = "total_visit";
                break;
            case 4:
                $sortCol = "COALESCE($tbl2_as.cdate,NOW())";
                break;
            default:
                $sortCol = "$tbl2_as.cdate";
            // case 0:
            //     $sortCol = "$tbl_as.cdate";
            //     break;
            // case 1:
            //     $sortCol = "$tbl_as.id";
            //     break;
            // case 2:
            //     $sortCol = "$tbl_as.mobile_type";
            //     break;
            // case 3:
            //     $sortCol = "total_visit";
            //     break;
            // case 4:
            //     $sortCol = "COALESCE($tbl_as.cdate,NOW())";
            //     break;
            // default:
            //     $sortCol = "$tbl_as.cdate";
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

        // advanced filter
        $mobile_type = $this->input->post("mobile_type");
        $cdate_start = $this->input->post("cdate_start");
        $cdate_end = $this->input->post("cdate_end");

        //validating date interval
        if (strlen($cdate_start)==10) {
            $cdate_start = date("Y-m-d", strtotime($cdate_start));
        } else {
            $cdate_start = '';
        }
        if (strlen($cdate_end)==10) {
            $cdate_end = date("Y-m-d", strtotime($cdate_end));
        } else {
            $cdate_end = '';
        }

        //get data
        $dcount = $this->fvm->countAllForVisitorCount($nation_code, $keyword, $cdate_start, $cdate_end, $mobile_type);
        $ddata = $this->fvm->getAllForVisitorCount($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $cdate_start, $cdate_end, $mobile_type);
        foreach ($ddata as &$dt) {
            // if(isset($dt->cdate)){
            //     $dt->cdate = date("d F Y", strtotime($dt->cdate));
			// }
        }

        $this->status = '200';
        $this->message = 'Success';
        $this->__jsonDataTable($ddata, $dcount);
    }

	public function detail_data_log() {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access, please login';
            header("HTTP/1.0 400 Unauthorized access, please login");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $sSearch = $this->input->request("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $pagesize = $this->input->request("iDisplayLength");

        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");

		$mobile_type = $this->input->post('mobile_type');
        $detail_date = $this->input->post('detail_date');

        $sortCol = "date";
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }

        switch ($iSortCol_0) {
            case 0:
                $sortCol = "id";
                break;
            default:
                $sortCol = "id";
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

        $this->status = 200;
        $this->message = 'Success';
		$dcount = $this->fvm->countDetailAll($keyword, $mobile_type, $detail_date);
		$data = $this->fvm->getDetailAll($page, $pagesize, $keyword, $mobile_type, $detail_date);

		foreach($data as &$gd){
            if(isset($gd->fnama)) {
                if($gd->fnama == "0") {
                    $gd->fnama = "Guest";
                }
            }
            if(isset($gd->location)) {
                $gd->location = str_replace("Kecamatan","", $gd->location);
            }
		}

        $this->__jsonDataTable($data, $dcount);
    }

    public function getTotal($mobile_type, $detail_date){
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access, please login';
            header("HTTP/1.0 400 Unauthorized access, please login");
            $this->__json_out($data);
            die();
        }
        
        $data['totalUser'] = 0;

        $data['totalUser'] = $this->fvm->getTotalUser($mobile_type, $detail_date);

        $this->status = '200';
        $this->message = 'Success';
        $this->__json_out($data);
    }

    public function getData($mobile_type, $cdate) {
        // $id means DATE(cdate)
        //$id = (int) $id;
        $d = $this->__init();
        $data = array();
        // if (!$this->admin_login && empty($id)) {
        //     $this->status = 400;
        //     $this->message = 'Unauthorized access, please login';
        //     header("HTTP/1.0 400 Unauthorized access, please login");
        //     $this->__json_out($data);
        //     die();
        // }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $this->status = 200;
        $this->message = 'Success';
        $data = $this->fvm->getById($nation_code, $mobile_type, $cdate);

        $this->__json_out($data);
    }

    public function totalvisitbydevice()
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

        $cdate_start = $this->input->post("start_date");
        $cdate_end = $this->input->post("end_date");
        $mobile_type = $this->input->post("mobile_type");

        //validating date interval
        if (strlen($cdate_start)==10) {
            $cdate_start = date("Y-m-d", strtotime($cdate_start));
        } else {
            $cdate_start = '';
        }
        if (strlen($cdate_end)==10) {
            $cdate_end = date("Y-m-d", strtotime($cdate_end));
        } else {
            $cdate_end = '';
        }

        //get data
        $data['totalAndroid'] = 0;
        $data['totalIOS'] = 0;

        if($mobile_type == 'android'){
            $data['totalAndroid'] = $this->fvm->countTotalVisit($nation_code, '', $cdate_start, $cdate_end, $mobile_type);
        }else if($mobile_type == 'ios'){
            $data['totalIOS'] = $this->fvm->countTotalVisit($nation_code, '', $cdate_start, $cdate_end, $mobile_type);
        }else{

            $data['totalAndroid'] = $this->fvm->countTotalVisit($nation_code, '', $cdate_start, $cdate_end, 'android');
            $data['totalIOS'] = $this->fvm->countTotalVisit($nation_code, '', $cdate_start, $cdate_end, 'ios');

        }

        $this->status = '200';
        $this->message = 'Success';
        $this->__json_out($data);
    }

}
