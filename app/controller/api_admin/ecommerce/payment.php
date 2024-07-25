<?php
class Payment extends JI_Controller{
	var $is_email = 1;
	var $regards = 'Sellon.com';
	var $from_name = 'Sellon';
	var $from_email = '';
	var $from_subject = '';
	var $berat_faktor = 1200;

	public function __construct(){
  	parent::__construct();
		$this->load("api_admin/a_bank_model",'abm');
		$this->load("api_admin/a_negara_model",'anm');
		$this->load("api_admin/a_bank_trfcost_model",'abtcm');
		$this->load("api_admin/b_user_model",'bum');
		$this->load("api_admin/common_code_model",'ccm');
		$this->load("api_admin/c_produk_model",'cpm');
		$this->load("api_admin/d_order_model",'dom');
		$this->load("api_admin/d_order_detail_model",'dodm');
		$this->load("api_admin/d_order_detail_item_model",'dodim');
		$this->load("api_mobile/d_order_proses_model",'dopm');
		$this->load("api_admin/qxpress_basic_model",'qbm');
		$this->load("api_admin/qxpress_volume_model",'qvm');
		$this->load("api_admin/qxpress_sameday_model",'qsm');
		$this->current_parent = 'ecommerce';
		$this->current_page = 'ecommerce_payment';
	}

 	private function __feeCalculation($nation_code){
		//declare initial variable
		$admin_pg = 0.35; //payment gateway deduction value
		$admin_pg_jenis = 'percentage';
		$admin_fee = 0.65; //profit
		$admin_fee_jenis = 'percentage';
		$asuransi = 0.0; //insurance
		$asuransi_jenis = 'percentage';
		$admin_vat = 7;
		$selling_fee_percent = 0;

		//get preset from DB
		$fee = array();
		$presets = $this->ccm->getByClassified($nation_code,"product_fee");
		if(count($presets)>0){
			foreach($presets as $pre){
				$fee[$pre->code] = $pre;
			}
			unset($pre); //free some memory
			unset($presets); //free some memory
			$admin_pg = 0.0;
			$admin_fee = 0.0;
			$admin_vat = 0.0;
		}

		//passing into current var
		if(isset($fee['F0']->remark)) $admin_pg = round($fee['F0']->remark,2); //pg deduction value
		if(isset($fee['F1']->remark)) $admin_pg_jenis = $fee['F1']->remark; //pg deduction type
		if(isset($fee['F2']->remark)) $admin_fee = round($fee['F2']->remark,2); //admin deduction value
		if(isset($fee['F3']->remark)) $admin_fee_jenis = $fee['F3']->remark; //admin deduction type
		if(isset($fee['F4']->remark)) $asuransi = round($fee['F4']->remark,2); //insurance deduction value
		if(isset($fee['F5']->remark)) $asuransi_jenis = $fee['F5']->remark; //insurance deduction type
		if(isset($fee['F6']->remark)) $admin_vat = $fee['F6']->remark; //insurance deduction type
		if(isset($fee['F7']->remark)) $selling_fee_percent = $fee['F7']->remark; //insurance deduction type

		$fee = new stdClass();
		$fee->admin_pg = $admin_pg;
		$fee->admin_pg_jenis = $admin_pg_jenis;
		$fee->admin_fee = $admin_fee;
		$fee->admin_fee_jenis = $admin_fee_jenis;
		$fee->asuransi = $asuransi;
			$fee->asuransi_jenis = $asuransi_jenis;
			$fee->admin_vat = $admin_vat;
		$fee->selling_fee_percent = $selling_fee_percent;
		return $fee;
	}
	
	protected function __m($amount,$nation_code=""){
		$n = $this->anm->getByNationCode($nation_code);
		if(isset($n->simbol_mata_uang)){
			return $n->simbol_mata_uang.' '.number_format($amount,2,'.',',');
		}else{
			return number_format($amount,2,'.',',');
		}
	}

	private function __convertToEmoji($text){
		$value = $text;
		$readTextWithEmoji = preg_replace("/\\\\u([0-9A-F]{2,5})/i", "&#x$1;", $value);
		return $readTextWithEmoji;
	}

	public function index(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
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
		$tbl2_as = $this->dodm->getTableAlias2();
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
		if(empty($sortDir)) $sortDir = "DESC";
		if(strtolower($sortDir) != "desc") $sortDir = "ASC";
		switch($iSortCol_0){
			case 0:
				$sortCol = "CONCAT($tbl3_as.id,'-',$tbl_as.id)";
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
				$sortCol = "$tbl_as.total_qty";
				break;
			case 6:
				$sortCol = "$tbl_as.sub_total";
				break;
			case 7:
				$sortCol = "($tbl_as.shipment_cost+$tbl_as.shipment_cost_add)";
				break;
			case 8:
				$sortCol = "$tbl_as.profit_amount";
				break;
			case 9:
				$sortCol = "$tbl_as.earning_total";
				break;
			case 10:
				$sortCol = "$tbl_as.refund_amount";
				break;
			case 11:
				$sortCol = "$tbl_as.settlement_status";
				break;
			default:
				$sortCol = "CONCAT($tbl3_as.id,'-',$tbl_as.id)";
		}
		if(empty($draw)) $draw = 0;
		if(empty($pagesize)) $pagesize=10;
		if(empty($page)) $page=0;

		//custom input
		$cdate_start = $this->input->post("cdate_start");
		$cdate_end = $this->input->post("cdate_end");
		$order_status = $this->input->post("order_status");
		$payment_status = $this->input->post("payment_status");
		$seller_status = $this->input->post("seller_status");
		$shipment_status = $this->input->post("shipment_status");
		$buyer_confirmed = $this->input->post("buyer_confirmed");
		$settlement_status = $this->input->post("settlement_status");

		//validating date interval
		if(strlen($cdate_start)==10){
			$cdate_start = date("Y-m-d",strtotime($cdate_start));
		}else{
			$cdate_start = "";
		}
		if(strlen($cdate_end)==10){
			$cdate_end = date("Y-m-d",strtotime($cdate_end));
		}else{
			$cdate_end = "";
		}

		//get data
		//EDIT by Aditya Adi Prabowo 28 July 2020 11:18
        // Request By Mr. Jackie to change same with display and report excel.
        // Improve Start
		$dcount = $this->dodm->countAllForPayment($nation_code,$keyword,$seller_status,$buyer_confirmed,$settlement_status,$cdate_start,$cdate_end);

		$ddata = $this->dodm->getAllForPayment($nation_code,$page,$pagesize,$sortCol,$sortDir,$keyword,$seller_status,$buyer_confirmed,$settlement_status,$cdate_start,$cdate_end);
		// improve end(array)
		/*var_dump($ddata); die();*/
		foreach($ddata as &$dt){
			if($dt->d_order_detail_id==1){$value=$this->__m($dt->profit_amount,$nation_code);}else{$value=0;}
			if(isset($dt->cdate)) $dt->cdate = date("d/M/y",strtotime($dt->cdate));
			if(isset($dt->sub_total)) $dt->sub_total = $this->__m($dt->sub_total,$nation_code);
			if(isset($dt->grand_total)) $dt->grand_total = $this->__m($dt->grand_total,$nation_code);
			if(isset($dt->shipment_cost)) $dt->shipment_cost = $this->__m($dt->shipment_cost,$nation_code);
			if(isset($dt->earning_total)) $dt->earning_total = $this->__m($dt->earning_total,$nation_code);
			if(isset($dt->refund_amount)) $dt->refund_amount = $this->__m($dt->refund_amount,$nation_code);
			//if(isset($dt->profit_amount)) $dt->profit_amount = $this->__m($dt->profit_amount,$nation_code);
			if(isset($dt->profit_amount)) $dt->profit_amount = $value;//By Aditya Adi Prabowo make profit not shown twice, added comment 27 october 2020 10:44
			$dt->action = '<button id="btn_payment_process_'.$dt->id.'" class="btn btn-success btn-payment-now" data-id="'.$dt->id.'">Pay now</button>';
			if(isset($dt->settlement_status)){
				$dt->settlement_status = $this->__settlementStatusText($dt->settlement_status);
			}
			if(isset($dt->nama)){
				$dt->nama = $this->__convertToEmoji($dt->nama);
			}
		}

		/*$return = array();
        foreach ($ddata as $key => $dts) {
            $return[$key]['date'] = $dts->cdate;
            $return[$key]['invoice_code'] = $dts->invoice_code;
            $return[$key]['nama'] = $dts->nama;
            $return[$key]['total_item'] = $dts->total_item;
            $return[$key]['total_qty'] = $dts->total_qty;
            $return[$key]['sub_total'] = $dts->sub_total;
            $return[$key]['shipment_cost'] = $dts->shipment_cost;
            $return[$key]['profit_amount'] = $dts->profit_amount;
            $return[$key]['earning_total'] = $dts->earning_total;
            $return[$key]['refund_amount'] = $dts->refund_amount;
            $return[$key]['seller_status'] = $dts->seller_status;
            $return[$key]['buyer_confirmed'] = $dts->buyer_confirmed;
            $return[$key]['settlement_status'] = $dts->settlement_status;
            // End Edit
        }*/

		//render output
		$this->status = 200;
		$this->message = 'Success';
		$this->__jsonDataTable($ddata,$dcount);
	}

	public function detail($id){
		$id = (int) $id;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login && empty($id)){
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

	public function normalize(){
		$this->status = 200;
		$this->message = 'Success';
		$this->dom->normalizeOrder();
		$data = array();
		$this->__json_out($data);
	}

	public function process($d_order_id,$d_order_detail_id){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
    $nation_code = $d['sess']->admin->nation_code;

		//get current bank configuration
		$app_bank_id = 0;
		$config = $this->ccm->getByClassifiedAndCode($nation_code,"app_config","C0");
		if(isset($config->remark)) $app_bank_id = (int) $config->remark;
		$app_bank = $this->abm->getById($nation_code,$app_bank_id);
		if(!isset($app_bank->id)){
			$this->status = 511;
			$this->message = 'Wrong app config for current bank, please setup it first';
			$this->__json_out($data);
			die();
		}

    //validation

		$d_order_id = (int) $d_order_id;
		if($d_order_id<=0){
			$this->status = 6001;
			$this->message = 'Invalid d_order_id';
			$this->__json_out($data);
			die();
		}
		$d_order_detail_id = (int) $d_order_detail_id;
		if($d_order_detail_id<=0){
			$this->status = 6002;
			$this->message = 'Invalid d_order_detail_id';
			$this->__json_out($data);
			die();
		}

		//default error message
		$this->status = 440;
		$this->message = 'One or more parameter are required';

		//get order detail
		$op = $this->dodm->getDetailByID($nation_code,$d_order_id,$d_order_detail_id);
		if(!isset($op->nation_code)){
			$this->status = 477;
			$this->message = 'Data with supplied ID not found';
			$this->__json_out($data);
			die();
		}
		if($op->settlement_status == "completed"){
			$this->status = 940;
			$this->message = 'This order already paid';
			$this->__json_out($data);
			die();
		}
		if($op->payment_status != 'paid'){
			$this->status = 943;
			$this->message = 'This order currently unpaid, process aborted';
			$this->__json_out($data);
			die();
		}
		if($op->payment_status == 'unconfirmed'){
			$this->status = 942;
			$this->message = 'This order currently still waiting confirmation from seller';
			$this->__json_out($data);
			die();
		}
		$items = $this->dodim->getByOrderIdDetailId($nation_code,$d_order_id,$d_order_detail_id);
		$item_count = count($items);
		if($item_count<=0){
			$this->status = 941;
			$this->message = 'This order has no item left on table, please contact your system administrator';
			$this->__json_out($data);
			die();
		}

		//re init var(s)
		$total_amount = 0;
		$settlement_amount = 0;
		$refund_amount = 0;
		$is_buyer_confirmed = 0;
		foreach($items as $itm){
			$st = $itm->qty * $itm->harga_jual;
			if($itm->buyer_status == "accepted"){
				$settlement_amount += $st;
				$is_buyer_confirmed++;
			}elseif($itm->buyer_status == "rejected"){
				$refund_amount += $st;
				$is_buyer_confirmed++;
			}else{

			}
			$total_amount += $st;
		}
		if($op->seller_status == "confirmed"){
			if($is_buyer_confirmed != $item_count){
				$du = array();
				$du['buyer_confirmed'] = "unconfirmed";
				$res = $this->dodm->update($nation_code,$op->d_order_id,$op->d_order_detail_id,$du);
				$this->status = 945;
				$this->message = 'Cannot process because the buyer hasn\'t confirmed all items';
				$this->__json_out($data);
				die();
			}
		}else if($op->seller_status == "rejected"){
			$refund_amount = $total_amount;
		}else{
			$this->status = 960;
			$this->message = 'This transaction are waiting for seller confirmation';
			$this->__json_out($data);
			die();
		}

		$du = array();
		$du['is_calculated'] = "1";
		$du['settlement_status'] = "completed";
		$res = $this->dodm->update($nation_code,$op->d_order_id,$op->d_order_detail_id,$du);
		if($res){
			$this->status = 200;
			$this->message = 'Sucess';
			if($refund_amount>0){
				$ops = array();
				$ops['nation_code'] = $nation_code;
				$ops['d_order_id'] = $op->d_order_id;
				$ops['c_produk_id'] = $op->d_order_detail_id;
				$ops['id'] = $this->dopm->getLastId($nation_code,$op->d_order_id,$op->d_order_detail_id);
				$ops['initiator'] = "Admin";
				$ops['nama'] = "Refund";
				$ops['deskripsi'] = "Your order with invoice number: $op->invoice_code ($op->nama) has been refunded successfully";
				$ops['cdate'] = "NOW()";
				$this->dopm->set($ops);
			}
			if($settlement_amount>0){
				$ops = array();
				$ops['nation_code'] = $nation_code;
				$ops['d_order_id'] = $op->d_order_id;
				$ops['c_produk_id'] = $op->d_order_detail_id;
				$ops['id'] = $this->dopm->getLastId($nation_code,$op->d_order_id,$op->d_order_detail_id);
				$ops['initiator'] = "Admin";
				$ops['nama'] = "Settled";
				$ops['deskripsi'] = "Your order with invoice number: $op->invoice_code ($op->nama) has been settled successfully";
				$ops['cdate'] = "NOW()";
				$this->dopm->set($ops);
			}
		}else{
			$this->status = 999;
			$this->message = 'Failed, please try again';
			$du['settlement_status'] = "unconfirmed";
			$res = $this->dodm->update($nation_code,$op->d_order_id,$op->d_order_detail_id,$du);
		}

		//render output
		$this->__json_out($data);
	}

	public function mass_process(){
		$i = 0;
		$s = 0;
		$f = 0;
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$nation_code = $d['sess']->admin->nation_code;

		//open transaction
		$this->dom->trans_start();

		//collect input
		$s = 0;
		$oids = $this->input->post("oids");
		$max = count($oids);
		if(is_array($oids) && $max>0){
			$f = $this->__feeCalculation($nation_code);
			foreach($oids as $oid){
				$d_order_id = 0;
				$c_produk_id = 0;
				$o = explode("/",$oid);
				if(isset($o[0])) $d_order_id = (int) $o[0];
				if(isset($o[1])) $c_produk_id = (int) $o[1];
				$d_order_detail_id = $c_produk_id;
				//begin

				//get order detail
				$op = $this->dodm->getDetailByID($nation_code,$d_order_id,$d_order_detail_id);
				if(!isset($op->nation_code)){
					$this->status = 477;
					$this->message = 'Data with supplied ID not found on index-'.$i;
					continue;
				}
				if($op->settlement_status == "completed"){
					$this->status = 940;
					$this->message = 'This order already paid on index-'.$i;
					continue;
				}
				if($op->payment_status != 'paid'){
					$this->status = 943;
					$this->message = 'This order currently unpaid, process aborted on index-'.$i;
					continue;
				}
				if($op->payment_status == 'unconfirmed'){
					$this->status = 942;
					$this->message = 'This order currently still waiting confirmation from seller on index-'.$i;
					$du = array();
					$du['buyer_confirmed'] = "unconfirmed";
					$res = $this->dodm->update($nation_code,$op->d_order_id,$op->d_order_detail_id,$du);
					continue;
				}
				$items = $this->dodim->getByOrderIdDetailId($nation_code,$d_order_id,$d_order_detail_id);
				$item_count = count($items);
				if($item_count<=0){
					$this->status = 941;
					$this->message = 'This order has no item left on table, please contact your system administrator on index-'.$i;
					continue;
				}

				//re init var(s)
				$total_amount = 0;
				$settlement_amount = 0;
				$refund_amount = 0;
				$is_buyer_confirmed = 0;
				foreach($items as $itm){
					$st = $itm->qty * $itm->harga_jual;
					if($itm->buyer_status == "accepted"){
						$settlement_amount += $st;
						$is_buyer_confirmed++;
					}elseif($itm->buyer_status == "rejected"){
						$refund_amount += $st;
						$is_buyer_confirmed++;
					}else{

					}
					$total_amount += $st;
				}
				if($op->seller_status == "confirmed"){
					if($is_buyer_confirmed != $item_count){
						$this->status = 940;
						$this->message = 'Cannot process because the buyer hasn\'t confirmed all items on index-'.$i;
						continue;
					}
				}else if($op->seller_status == "rejected"){
					$refund_amount = $total_amount;
				}else{
					$this->status = 960;
					$this->message = 'This transaction are waiting for seller confirmation on index-'.$i;
					continue;
				}

				//get calcuation fee
				if(!isset($f->admin_pg_jenis) || !isset($f->admin_pg) || !isset($f->admin_fee) || !isset($f->admin_fee_jenis)){
					$this->status = 904;
					$this->message = 'Cannot processing payment, because all fee criteria(s) unconfigured on index-'.$i;
					continue;
				} 
				

				$du = array();
				$du['settlement_status'] = "completed";
				$du['is_calculated'] = "1";
				$res = $this->dodm->update($nation_code,$op->d_order_id,$op->d_order_detail_id,$du);
				if($res){
					$s++;
					$this->status = 200;
					$this->message = 'Sucess';
					if($refund_amount>0){
						$ops = array();
						$ops['nation_code'] = $nation_code;
						$ops['d_order_id'] = $op->d_order_id;
						$ops['c_produk_id'] = $op->d_order_detail_id;
						$ops['id'] = $this->dopm->getLastId($nation_code,$op->d_order_id,$op->d_order_detail_id);
						$ops['initiator'] = "Admin";
						$ops['nama'] = "Refund";
						$ops['deskripsi'] = "Your order with invoice number: $op->invoice_code ($op->nama) has been refunded successfully";
						$ops['cdate'] = "NOW()";
						$this->dopm->set($ops);
					}
					if($settlement_amount>0){
						$ops = array();
						$ops['nation_code'] = $nation_code;
						$ops['d_order_id'] = $op->d_order_id;
						$ops['c_produk_id'] = $op->d_order_detail_id;
						$ops['id'] = $this->dopm->getLastId($nation_code,$op->d_order_id,$op->d_order_detail_id);
						$ops['initiator'] = "Admin";
						$ops['nama'] = "Settled";
						$ops['deskripsi'] = "Your order with invoice number: $op->invoice_code ($op->nama) has been settled successfully";
						$ops['cdate'] = "NOW()";
						$this->dopm->set($ops);
					}
				}else{
					$this->status = 999;
					$this->message = 'Failed, please try again';
					$du['settlement_status'] = "unconfirmed";
					$res = $this->dodm->update($nation_code,$op->d_order_id,$op->d_order_detail_id,$du);
				}
				//end

				$i++;
			}
		}else{
			$this->status = 1020;
			$this->message = 'Please select at least one Order';
		}

		if(is_array($oids) && $max>0){
			$this->status = 200;
			$this->message = "$s of $max order(s) payment successfully processed";
		}else{
			$this->status = 1044;
			$this->message = "Please select at least one order";
		}

		//close transaction
		$this->dom->trans_end();

		//render output
		$this->__json_out($data);
	}

	public function updateStatusSettlement($id){
		//die('updateStatusPayment');
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Unauthorized access';
			header("HTTP/1.0 400 Unauthorized");
			$this->__json_out($data);
			die();
		}
		$id = (int) $id;
		$du = $_POST;

		if(!isset($du['orderid'])) $du['orderid'] = "";
    	$nation_code = $d['sess']->admin->nation_code;

		if (is_array($du['orderid'])) {
			if($du['orderid']>0 && $du['orderid'] != ""){
				foreach ($du['orderid'] as $key => $orderid) {
					$orders = $this->dodm->getByOrderIdForPayment($orderid,1);
					foreach ($orders as $key => $order) {
						//$this->debug($order->d_order_id); die;

						$settlement_status = 'paid_to_seller';

						$res = $this->dodm->updateStatusSettlement($order->d_order_id, $settlement_status);
						if($res){
							$this->status = 200;
							$this->message = 'Perubahan berhasil diterapkan';
						}else{
							$this->status = 901;
							$this->message = 'Failed to make data changes';
						}
					}
				}
			}else{
				$this->status = 440;
				$this->message = 'Salah satu parameter ada yang invalid atau kurang parameter';
			}
		} else {
			if(isset($du['orderid'])){
				$orders = $this->dodm->getByOrderIdForPayment($du['orderid'],1);
				foreach ($orders as $key => $order) {
					//$this->debug($order->d_order_id); die;

					$settlement_status = 'paid_to_seller';

					$res = $this->dodm->updateStatusSettlement($order->d_order_id,$settlement_status);
					if($res){
						$this->status = 200;
						$this->message = 'Perubahan berhasil diterapkan';
					}else{
						$this->status = 901;
						$this->message = 'Failed to make data changes';
					}
				}
			} else {
				$this->status = 440;
				$this->message = 'Salah satu parameter ada yang invalid atau kurang parameter';
			}

		}
		$this->__json_out($data);
	}
}
