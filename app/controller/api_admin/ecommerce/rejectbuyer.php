<?php
class RejectBuyer extends JI_Controller
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
        //$this->setTheme('frontx');
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
        $this->current_page = 'ecommerce_rejectbuyer';
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
        $tbl_as = $this->dodim->getTableAlias();
        $tbl2_as = $this->dodim->getTableAlias2();
        $tbl3_as = $this->dodim->getTableAlias3();
        $tbl4_as = $this->dodim->getTableAlias4();

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
                $sortCol = "$tbl_as.d_order_id $sortDir, $tbl_as.d_order_detail_id $sortDir, $tbl_as.c_produk_id";
                break;
            case 1:
                $sortCol = "COALESCE($tbl3_as.cdate,NOW())";
                break;
            case 2:
                $sortCol = "$tbl3_as.invoice_code";
                break;
            case 3:
                $sortCol = "$tbl4_as.nama";
                break;
            case 4:
                $sortCol = "$tbl_as.harga_jual";
                break;
            case 5:
                $sortCol = "$tbl_as.qty";
                break;
            case 6:
                $sortCol = "($tbl_as.qty * $tbl_as.harga_jual)";
                break;
            case 7:
                $sortCol = "IF($tbl2_as.seller_status='rejected','Seller','Buyer')";
                break;
            case 8:
                $sortCol = "$tbl_as.settlement_status";
                break;
            case 9:
                $sortCol = "$tbl2_as.settlement_status";
                break;
            case 10:
                $sortCol = "$tbl2_as.settlement_status";
                break;
            default:
                $sortCol = "CONCAT($tbl_as.d_order_id,'/',$tbl_as.c_produk_id,'/',$tbl_as.c_produk_id)";
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

        // advanced filtering
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
        $dcount = $this->dodim->countAllForRejectBuyer($nation_code, $keyword, $cdate_start, $cdate_end, $settlement_status);
        $ddata = $this->dodim->getAllForRejectBuyer($nation_code, $page, $pagesize, $sortCol, $sortDir, $keyword, $cdate_start, $cdate_end, $settlement_status);
        foreach ($ddata as &$dt) {
            if (isset($dt->cdate)) {
                $dt->cdate = date("d/M/y", strtotime($dt->cdate));
            }
            if(isset($dt->nama)){
				$dt->nama = $this->__convertToEmoji($dt->nama);
			}
            if (isset($dt->resolution)) {
                $dt->resolution = $this->__settlementStatusText2($dt->resolution);
            }
            $dt->action = '<button class="btn btn-default" data-id="'.$dt->id.'">View Options</button>';
        }

        $this->status = '200';
        $this->message = 'Success';
        $this->__jsonDataTable($ddata, $dcount);
    }

    public function detail($id)
    {
        $id = (int) $id;
        $d = $this->__init();
        $data = array();
        if (!$this->admin_login && empty($id)) {
            $this->status = 400;
            $this->message = 'Unauthorized access';
            header("HTTP/1.0 400 Unauthorized");
            $this->__json_out($data);
            die();
        }
        $this->status = 200;
        $this->message = 'Success';
        $data = $this->cpm->getById($id);
        $this->__json_out($data);
    }


    public function change_status($id)
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
        $id = (int) $id;
        $du = $_POST;

        //var_dump($du); die;
        if (!isset($id)) {
            $id = "";
        }
        $nation_code = $d['sess']->admin->nation_code;

        if (isset($id)) {
            $orders = $this->dodm->getByOrderIdForCancellation($id, 1);
            foreach ($orders as $key => $order) {
                $payment_status = 'pending';
                if ($order->order_status == 'incompleted') {
                    $payment_status = 'refund';
                }
                if ($order->order_status == 'cancelled') {
                    $payment_status = 'void';
                }

                $res = $this->dom->updateStatusCancellation($order->d_order_id, $payment_status);
                if ($res) {
                    $this->status = 200;
                    $this->message = 'Success';
                } else {
                    $this->status = 901;
                    $this->message = 'Cannot process data to database';
                }
            }
        } else {
            $this->status = 440;
            $this->message = 'One or more parameter are required';
        }

        $this->__json_out($data);
    }

    public function set_status_settlement($d_order_id, $d_order_detail_id, $c_produk_id, $settlement_status)
    {
        $d = $this->__init();
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
            $this->status = 6001;
            $this->message = 'Invalid d_order_id';
            $this->__json_out($data);
            die();
        }
        $d_order_detail_id = (int) $d_order_detail_id;
        if ($d_order_detail_id<=0) {
            $this->status = 6002;
            $this->message = 'Invalid d_order_detail_id';
            $this->__json_out($data);
            die();
        }
        $c_produk_id = (int) $c_produk_id;
        if ($c_produk_id<=0) {
            $this->status = 6003;
            $this->message = 'Invalid c_produk_id';
            $this->__json_out($data);
            die();
        }
        $this->status = 440;
        $this->message = 'One or more parameter are required';

        $message = "";
        $settle = 0;
        $order = $this->dodim->getDetailByIdForCancellation($nation_code, $d_order_id, $d_order_detail_id, $c_produk_id);

        if ($order->seller_status == "rejected") {
            $this->status = 6006;
            $this->message = 'Cannot change settlement state item because this state issued by seller.';
            $this->__json_out($data);
            die();
        }
        if ($order->settlement_status2 == "completed") {
            $this->status = 6016;
            $this->message = 'Cannot change settlement state because this settlement has been completed.';
            $this->__json_out($data);
            die();
        }
        if ($order->settlement_status == "paid_to_buyer") {
            $this->status = 6016;
            $this->message = 'Cannot change settlement state because this settlement already paid to buyer.';
            $this->__json_out($data);
            die();
        }
        if ($order->settlement_status == "paid_to_seller") {
            $this->status = 6016;
            $this->message = 'Cannot change settlement state because this settlement already paid to seller.';
            $this->__json_out($data);
            die();
        }

        $du = array();
        $du['settlement_status'] = $settlement_status;
        if ($settlement_status == "solved_to_seller") {
            $du['buyer_status'] = "accepted";
        }
        if ($settlement_status == "solved_to_buyer") {
            $du['buyer_status'] = "rejected";
        }
        if ($settlement_status == "paid_to_seller") {
            $du['buyer_status'] = "accepted";
        }


        $res = $this->dodim->update($nation_code, $d_order_id, $d_order_detail_id, $c_produk_id, $du);
        if ($res) {
            $this->status = 200;
            $this->message = 'Success';

            //declare var
            $is_confirmed = 0;
            $ap = array();

            //get items
            $total_qty = 0;
            $total_item = 0;
            $cancel_fee = 0.0;
            $profit_amount = 0.0;
            $earning_total = 0.0;
            $refund_amount = 0.0;
            $items = $this->dodim->getByOrderDetailId($nation_code, $d_order_id, $d_order_detail_id);
            $ic = count($items);
            foreach ($items as $itm) {
                if ($itm->buyer_status != "wait") {
                    $is_confirmed++;
                    if ($itm->buyer_status == "accepted") {
                        $ap[] = $itm;
                        $earning_total += $itm->qty * $itm->harga_jual;
                        $total_qty += $itm->qty;
                        $total_item++;
                    } else {
                        $refund_amount += $itm->qty * $itm->harga_jual;
                    }
                }
            }
            
            //recalculation
            $sub_total = $earning_total;
            $selling_fee = $sub_total * ($order->selling_fee_percent/100);
            $profit_amount = $selling_fee;
            $profit_amount = $selling_fee;
            
            //update to d_order_detail
            $du = array();
            $du['total_item'] = $total_item;
            $du['total_qty'] = $total_qty;
            $du['sub_total'] = $earning_total;
            $du['grand_total'] = $du['sub_total'] + (($order->shipment_cost + $order->shipment_cost_add) - $order->shipment_cost_sub);
            $du['selling_fee'] = round($selling_fee, 2);
            $du['profit_amount'] = $profit_amount;
            $du['earning_total'] = $earning_total;
            $du['refund_amount'] = $refund_amount;
            if (isset($ap[0]->foto)) {
                $nama = '';
                foreach ($ap as $a) {
                    $nama .= ','.trim($a->nama);
                }
                $du['nama'] = trim($nama, ',');
                $du['foto'] = $ap[0]->foto;
                $du['thumb'] = $ap[0]->thumb;
                if ($is_confirmed == $ic) {
                    $du['buyer_confirmed'] = "confirmed";
                    $du['settlement_status'] = "unconfirmed";
                }
            }
            if (count($du)>0) {
                $this->dodm->update($nation_code, $d_order_id, $d_order_detail_id, $du);
            }
            
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
            $this->status = 901;
            $this->message = 'Cannot process data to database';
        }
        $this->__json_out($data);
    }
}
