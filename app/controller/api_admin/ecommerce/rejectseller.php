<?php
class RejectSeller extends JI_Controller
{
    public $is_email = 1;
    public $regards = 'Sellon.com';
    public $from_name = 'Sellon';
    public $from_email = '';
    public $from_subject = '';
    public $berat_faktor = 1200;

    public function __construct()
    {
        parent::__construct();
        $this->lib("seme_log");
        $this->load("api_admin/b_user_model", 'bum');
        $this->load("api_admin/c_produk_model", 'cpm');
        $this->load("api_admin/common_code_model", 'ccm');
        $this->load("api_admin/d_order_model", 'dom');
        $this->load("api_admin/d_order_detail_model", 'dodm');
        $this->load("api_admin/d_order_detail_item_model", 'dodim');
        $this->load("api_admin/e_rating_model", 'erm');
        $this->load("api_admin/qxpress_basic_model", 'qbm');
        $this->load("api_admin/qxpress_volume_model", 'qvm');
        $this->load("api_admin/qxpress_sameday_model", 'qsm');
        $this->current_parent = 'ecommerce';
        $this->current_page = 'ecommerce_rejectseller';
    }

    private function __convertToEmoji($text){
		$value = $text;
		$readTextWithEmoji = preg_replace("/\\\\u([0-9A-F]{2,5})/i", "&#x$1;", $value);
		return $readTextWithEmoji;
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
        $tbl_as = $this->dodm->getTableAlias();
        $tbl3_as = $this->dodm->getTableAlias3();
        $tbl4_as = $this->dodm->getTableAlias4();

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
        $sortCol = "invoice_code";
        $sortDir = strtoupper($sSortDir_0);
        if (empty($sortDir)) {
            $sortDir = "DESC";
        }
        if (strtolower($sortDir) != "desc") {
            $sortDir = "ASC";
        }
        switch ($iSortCol_0) {
            case 0:
                $sortCol = "$tbl_as.d_order_id $sortDir, $tbl_as.id";
                break;
            case 1:
                $sortCol = "COALESCE($tbl3_as.cdate,NOW())";
                break;
            case 2:
                $sortCol = "$tbl3_as.invoice_code";
                break;
            case 3:
                $sortCol = "$tbl_as.nama";
                break;
            case 4:
                $sortCol = "$tbl_as.total_item";
                break;
            case 5:
                $sortCol = "$tbl_as.sub_total";
                break;
            case 6:
                $sortCol = "$tbl_as.shipment_cost";
                break;
            case 7:
                $sortCol = "$tbl_as.refund_amount";
                break;
            case 8:
                $sortCol = "$tbl_as.settlement_status";
                break;
            default:
                $sortCol = "CONCAT($tbl_as.d_order_id,'/',$tbl_as.id)";
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
        $settlement_status = $this->input->post("settlement_status");
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
        $dcount = $this->dodm->countAllForRejectSeller($nation_code, $keyword, $cdate_start, $cdate_end, $settlement_status);
        $ddata = $this->dodm->getAllForRejectSeller($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $cdate_start, $cdate_end, $settlement_status);
        foreach ($ddata as &$dt) {
            if (isset($dt->cdate)) {
                $dt->cdate = date("d/M/y", strtotime($dt->cdate));
            }
            if(isset($dt->nama)){
				$dt->nama = $this->__convertToEmoji($dt->nama);
			}
            if (isset($dt->resolution)) {
                $dt->resolution = $this->__settlementStatusText($dt->resolution);
            }
            $dt->action = '<button class="btn btn-default" data-id="'.$dt->id.'">View Options</button>';
        }

        $this->status = '200';
        $this->message = 'Success';
        $this->__jsonDataTable($ddata, $dcount);
    }

    public function refund($d_order_id, $d_order_detail_id)
    {
        $d = $this->__init();
        $settlement_status = 'paid_to_buyer';
        $data = array();

        if (!$this->admin_login) {
            $this->status = 400;
            $this->message = 'Authorization required';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $pengguna = $d['sess']->admin;
        $nation_code = $pengguna->nation_code;

        $d_order_id = (int) $d_order_id;
        if ($d_order_id<=0) {
            $this->status = 6601;
            $this->message = 'Invalid d_order_id';
            $this->__json_out($data);
            die();
        }
        $d_order_detail_id = (int) $d_order_detail_id;
        if ($d_order_detail_id<=0) {
            $this->status = 6602;
            $this->message = 'Invalid d_order_detail_id';
            $this->__json_out($data);
            die();
        }
        $this->status = 440;
        $this->message = 'One or more parameter are required';

        $message = "";
        $settle = 0;
        $order = $this->dodm->getById($nation_code, $d_order_id, $d_order_detail_id);

        if ($order->seller_status != "rejected") {
            $this->status = 6610;
            $this->message = 'not rejected by the seller';
            $this->__json_out($data);
            die();
        }
        if ($order->settlement_status == "completed") {
            $this->status = 6611;
            $this->message = 'Cannot change settlement state because this settlement has been completed.';
            $this->__json_out($data);
            die();
        }
        if ($order->settlement_status == "paid_to_buyer") {
            $this->status = 6612;
            $this->message = 'Cannot change settlement state because this settlement already paid to buyer.';
            $this->__json_out($data);
            die();
        }
        if ($order->settlement_status == "paid_to_seller") {
            $this->status = 6613;
            $this->message = 'Cannot change settlement state because this settlement already paid to seller.';
            $this->__json_out($data);
            die();
        }
        // $this->dodm->setDebug(1);

        $du = array();
        $du['settlement_status'] = 'processing';
        $rf = $order->sub_total + $order->shipment_cost + $order->shipment_cost_add;
        if ($order->refund_amount < $rf) {
            $du['selling_fee'] = 0.00;
            $du['refund_amount'] = $rf;
        }
        $res = $this->dodm->update($nation_code, $d_order_id, $d_order_detail_id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';
            
            //update to d_order_detail_item
            $du = array();
            $du['settlement_status'] = $settlement_status;
            $this->dodim->updateByOrderDetailId($nation_code, $d_order_id, $d_order_detail_id, $du);
            
            //get all order per seller
            $sub_total = 0.00;
            $profit = 0.00;
            $refund = 0.00;
            $sellers = $this->dodm->getByOrderId($nation_code, $d_order_id);
            foreach ($sellers as $seller) {
                $profit += $seller->profit_amount;
                $refund += $seller->refund_amount;
                $sub_total += $seller->sub_total;
            }
            
            //update to d_order
            $order = $this->dom->getById($nation_code, $d_order_id);
            $du = array();
            $du['sub_total'] = $sub_total;
            $du['selling_fee'] = $sub_total * ($order->selling_fee_percent/100);
            $du['profit_amount'] = $profit - ($order->pg_fee + $order->pg_fee_vat);
            $du['refund_amount'] = $refund;
            if (count($du)>0) {
                $this->dom->update($nation_code, $d_order_id, $du);
            }
        } else {
            $this->status = 900;
            $this->message = 'Cannot update data to database';
        }
        $this->__json_out($data);
    }
}
