<?php
class Offer_Detail extends JI_Controller {
    public $is_log = 1;

    public function __construct() {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_admin/b_user_detail_offer_model", 'budom');
    }

    // public function index() {
    //     $d = $this->__init();
    //     $data = array();
    //     if (!$this->admin_login) {
    //         $this->status = 400;
    //         $this->message = 'Unauthorized access, please login';
    //         header("HTTP/1.0 400 Unauthorized access, please login");
    //         $this->__json_out($data);
    //         die();
    //     }

    //     $draw = $this->input->post("draw");
    //     $sval = $this->input->post("search");
    //     $sSearch = $this->input->request("sSearch");
    //     $sEcho = $this->input->post("sEcho");
    //     $page = $this->input->post("iDisplayStart");
    //     $pagesize = $this->input->request("iDisplayLength");

    //     $iSortCol_0 = $this->input->post("iSortCol_0");
    //     $sSortDir_0 = $this->input->post("sSortDir_0");

    //     $from_date = $this->input->post("cdate_start");
    //     $to_date = $this->input->post("cdate_end");
    //     $path = $this->input->post("path");

    //     $sortCol = "cdate";
    //     $sortDir = strtoupper($sSortDir_0);
    //     if (empty($sortDir)) {
    //         $sortDir = "DESC";
    //     }
    //     if (strtolower($sortDir) != "desc") {
    //         $sortDir = "ASC";
    //     }

    //     //validating date interval
    //     if (strlen($from_date)==10) {
    //         $from_date = date("Y-m-d", strtotime($from_date));
    //     } else {
    //         $from_date = "";
    //     }
    //     if (strlen($to_date)==10) {
    //         $to_date = date("Y-m-d", strtotime($to_date));
    //     } else {
    //         $to_date = "";
    //     }

    //     switch ($iSortCol_0) {
    //         case 0:
    //             $sortCol = "no";
    //             break;
    //         case 1:
    //             $sortCol = "id";
    //             break;
    //         case 2:
    //             $sortCol = "cdate";
    //             break;
    //         case 3:
    //             $sortCol = "path";
    //             break;
    //         case 4:
    //             $sortCol = "log_text";
    //             break;
    //         default:
    //             $sortCol = "no";
    //     }

    //     if (empty($draw)) {
    //         $draw = 0;
    //     }
    //     if (empty($pagesize)) {
    //         $pagesize=10;
    //     }
    //     if (empty($page)) {
    //         $page=0;
    //     }

    //     $keyword = $sSearch;

    //     $this->status = 200;
    //     $this->message = 'Success';
    //     $dcount = $this->budom->countAll($keyword, $from_date, $to_date, $path);
    //     $ddata = $this->budom->getAll($page, $pagesize, $sortCol, $sortDir, $keyword, $from_date, $to_date, $path);

    //     foreach ($ddata as &$gd) {
    //         if (isset($gd->cdate)) {
	// 			$gd->cdate = date("d F Y H:i:s", strtotime($gd->cdate));
    //         }
	// 	}

    //     $this->__jsonDataTable($ddata, $dcount);
    // }

    public function index() {
        http_response_code("404");
        $this->__json_out("Thank you for using Seme Framework");
    }

    public function seller() {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access, please login';
            header("HTTP/1.0 400 Unauthorized access, please login");
            $this->__json_out($data);
            die();
        }

        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $sSearch = $this->input->request("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $pagesize = $this->input->request("iDisplayLength");

        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");

        $from_date = $this->input->post("cdate_start");
        $to_date = $this->input->post("cdate_end");
        $b_user_id = $this->input->post("b_user_id_seller");

        $sortCol = "cdate";
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }

        //validating date interval
        // if (strlen($from_date)==10) {
        //     $from_date = date("Y-m-d", strtotime($from_date));
        // } else {
        //     $from_date = "";
        // }
        // if (strlen($to_date)==10) {
        //     $to_date = date("Y-m-d", strtotime($to_date));
        // } else {
        //     $to_date = "";
        // }

        switch ($iSortCol_0) {
            case 0:
                $sortCol = "no";
                break;
            case 1:
                $sortCol = "chat_room_id";
                break;
            case 2:
                $sortCol = "c_produk_nama";
                break;
            case 3:
                $sortCol = "product_type";
                break;
            case 4:
                // $sortCol = "harga_jual";
                $sortCol = "message";
                break;
            case 5:
                $sortCol = "bu.fnama";
                break;
            case 6:
                $sortCol = "offer_status_update_date";
                break;
            default:
                $sortCol = "no";
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

        $dcount = $this->budom->countAllDetailAsSeller("offer", "seller", $keyword, $from_date, $to_date, $b_user_id, "buyer");
        $ddata = $this->budom->getAllDetailAsSeller("62", "offer", "seller", $page, $pagesize, $sortCol, $sortDir, $from_date, $to_date, $b_user_id, "All", "ongoing", "buyer");

        foreach ($ddata as &$gd) {
            // if (isset($gd->cdate)) {
			// 	$gd->cdate = date("d F Y H:i:s", strtotime($gd->cdate));
            // }

            $num = $gd->message;
            $convert_float = (float)$num;
            if (isset($convert_float)) {
				$gd->message = number_format($convert_float, 2, ",", ".");
            }
		}

        $this->__jsonDataTable($ddata, $dcount);
    }

    public function buyer() {
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Unauthorized access, please login';
            header("HTTP/1.0 400 Unauthorized access, please login");
            $this->__json_out($data);
            die();
        }

        $draw = $this->input->post("draw");
        $sval = $this->input->post("search");
        $sSearch = $this->input->request("sSearch");
        $sEcho = $this->input->post("sEcho");
        $page = $this->input->post("iDisplayStart");
        $pagesize = $this->input->request("iDisplayLength");

        $iSortCol_0 = $this->input->post("iSortCol_0");
        $sSortDir_0 = $this->input->post("sSortDir_0");

        $from_date = $this->input->post("cdate_start");
        $to_date = $this->input->post("cdate_end");
        $b_user_id = $this->input->post("b_user_id_buyer");

        $sortCol = "cdate";
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }

        //validating date interval
        // if (strlen($from_date)==10) {
        //     $from_date = date("Y-m-d", strtotime($from_date));
        // } else {
        //     $from_date = "";
        // }
        // if (strlen($to_date)==10) {
        //     $to_date = date("Y-m-d", strtotime($to_date));
        // } else {
        //     $to_date = "";
        // }

        switch ($iSortCol_0) {
            case 0:
                $sortCol = "no";
                break;
            case 1:
                $sortCol = "chat_room_id";
                break;
            case 2:
                $sortCol = "c_produk_nama";
                break;
            case 3:
                $sortCol = "product_type";
                break;
            case 4:
                // $sortCol = "harga_jual";
                $sortCol = "message";
                break;
            case 5:
                $sortCol = "bu2.fnama";
                break;
            case 6:
                $sortCol = "offer_status_update_date";
                break;
            default:
                $sortCol = "no";
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

        $dcount = $this->budom->countAllDetailAsBuyer("offer", "buyer", $keyword, $from_date, $to_date, $b_user_id, "seller");
        $ddata = $this->budom->getAllDetailAsBuyer("62", "offer", "buyer", $page, $pagesize, $sortCol, $sortDir, $from_date, $to_date, $b_user_id, "All", "ongoing", "seller");

        foreach ($ddata as &$gd) {
            // if (isset($gd->cdate)) {
			// 	$gd->cdate = date("d F Y H:i:s", strtotime($gd->cdate));
            // }

            $num = $gd->message;
            $convert_float = (float)$num;
            if (isset($convert_float)) {
				$gd->message = number_format($convert_float, 2, ",", ".");
            }
		}

        $this->__jsonDataTable($ddata, $dcount);
    }
}
