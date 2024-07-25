<?php
class D_Order_Detail_Item_Model extends SENE_Model {
	var $is_cacheable;
	var $tbl = 'd_order_detail_item';
	var $tbl_as = 'dodi';
	var $tbl2 = 'd_order_detail';
	var $tbl2_as = 'dod';
	var $tbl3 = 'd_order';
	var $tbl3_as = 'dor';
	var $tbl4 = 'c_produk';
	var $tbl4_as = 'cp';
	var $tbl5 = 'b_user';
	var $tbl5_as = 'bu'; //seller alias
	var $tbl6 = 'b_user';
	var $tbl6_as = 'bu2'; //buyer alias
	var $tbl7 = 'b_user_alamat';
	var $tbl7_as = 'bua';
	var $tbl8 = 'd_order_alamat';
	var $tbl8_as = 'doa';
	var $tbl9 = 'b_user_bankacc';
	var $tbl9_as = 'buba1';
	var $tbl10 = 'b_user_bankacc';
	var $tbl10_as = 'buba2';
	var $tbl11 = 'a_bank';
	var $tbl11_as = 'ab1';
	var $tbl12 = 'a_bank';
	var $tbl12_as = 'ab2';

	public function __construct(){
		parent::__construct();
		$this->is_cacheable = 0;
    $this->db->from($this->tbl,$this->tbl_as);
	}

	private function __joinTbl2(){
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl2_as.nation_code");
    $cps[] = $this->db->composite_create("$this->tbl_as.d_order_id","=","$this->tbl2_as.d_order_id");
		$cps[] = $this->db->composite_create("$this->tbl_as.d_order_detail_id","=","$this->tbl2_as.id");
		return $cps;
	}
	private function __joinTbl3(){
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl3_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl_as.d_order_id","=","$this->tbl3_as.id");
		return $cps;
	}

	private function __joinTbl3_1(){
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl2_as.nation_code","=","$this->tbl3_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl2_as.d_order_id","=","$this->tbl3_as.id");
		return $cps;
	}

	//for product, requires joinTbl
	private function __joinTbl4(){
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl4_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl_as.c_produk_id","=","$this->tbl4_as.id");
		return $cps;
	}

	//for seller, requires joinTbl2
	private function __joinTbl5(){
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl2_as.nation_code","=","$this->tbl5_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl2_as.b_user_id","=","$this->tbl5_as.id");
		return $cps;
	}

	//for buyer, requires joinTbl3
	private function __joinTbl6(){
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl3_as.nation_code","=","$this->tbl6_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl3_as.b_user_id","=","$this->tbl6_as.id");
		return $cps;
	}

	//for seller address, requires joinTbl2
	private function __joinTbl7(){
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl7_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl_as.b_user_id","=","$this->tbl7_as.b_user_id");
		$cps[] = $this->db->composite_create("$this->tbl_as.b_user_alamat_id","=","$this->tbl7_as.id");
		return $cps;
	}

	//for buyer address, requires joinTbl3
	private function __joinTbl8(){
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl3_as.nation_code","=","$this->tbl8_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl3_as.id","=","$this->tbl8_as.d_order_id");
		return $cps;
	}

	public function getTableAlias(){
		return $this->tbl_as;
	}

	public function getTableAlias2(){
		return $this->tbl2_as;
	}

	public function getTableAlias3(){
		return $this->tbl3_as;
	}

	public function getTableAlias4(){
		return $this->tbl4_as;
	}

	public function getTableAlias5(){
		return $this->tbl5_as;
	}

	public function getTableAlias6(){
		return $this->tbl6_as;
	}

	public function getTableAlias7(){
		return $this->tbl7_as;
	}

	public function getById($nation_code,$d_order_id,$d_order_detail_id,$c_produk_id){

		//by Donny dennison - 5 february 2021 - 17:31
		//change chat to open chatting
    	$this->db->from($this->tbl,$this->tbl_as);

		$this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
	    $this->db->where_as("$this->tbl_as.d_order_id",$this->db->esc($d_order_id));
	    $this->db->where_as("$this->tbl_as.d_order_detail_id",$this->db->esc($d_order_detail_id));
		$this->db->where_as("$this->tbl_as.c_produk_id",$this->db->esc($c_produk_id));
		return $this->db->get_first();
	}
	public function getByOrderDetailId($nation_code,$d_order_id,$d_order_detail_id){
		$this->db->select_as("$this->tbl_as.*, $this->tbl_as.c_produk_id","id",0);
		$this->db->select_as("$this->tbl_as.nama","nama",0);
		$this->db->select_as("COALESCE($this->tbl4_as.stok,0)","stok",0);
		$this->db->select_as("$this->tbl_as.foto","foto",0);
		$this->db->select_as("$this->tbl_as.thumb","thumb",0);
		$this->db->select_as("$this->tbl4_as.satuan","satuan",0);
		$this->db->select_as("$this->tbl_as.is_include_delivery_cost","is_include_delivery_cost",0);
    $this->db->from($this->tbl,$this->tbl_as);
    $this->db->join_composite($this->tbl4,$this->tbl4_as,$this->__joinTbl4(),'left');
		$this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
    $this->db->where_as("$this->tbl_as.d_order_id",$this->db->esc($d_order_id));
    $this->db->where_as("$this->tbl_as.d_order_detail_id",$this->db->esc($d_order_detail_id));
		return $this->db->get('',0);
	}
	public function getByOrderId($nation_code,$d_order_id){
		$this->db->select_as("$this->tbl_as.*, $this->tbl_as.c_produk_id","id",0);
		$this->db->select_as("$this->tbl_as.nama","nama",0);
		$this->db->select_as("COALESCE($this->tbl4_as.stok,0)","stok",0);
		$this->db->select_as("$this->tbl_as.harga_jual","harga_jual",0);
		$this->db->select_as("$this->tbl_as.foto","foto",0);
		$this->db->select_as("$this->tbl_as.thumb","thumb",0);
		$this->db->select_as("$this->tbl_as.satuan","satuan",0);
		$this->db->select_as("$this->tbl4_as.courier_services","courier_services",0);
		$this->db->select_as("$this->tbl_as.is_include_delivery_cost","is_include_delivery_cost",0);
		$this->db->from($this->tbl,$this->tbl_as);
    $this->db->join_composite($this->tbl4,$this->tbl4_as,$this->__joinTbl4(),'left');
		$this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
    $this->db->where_as("$this->tbl_as.d_order_id",$this->db->esc($d_order_id));
		return $this->db->get('',0);
	}
	public function getDetailByOrderId($nation_code,$d_order_id,$d_order_detail_id){
		$this->db->select_as("$this->tbl_as.*, $this->tbl_as.c_produk_id","id",0);
		$this->db->select_as("$this->tbl_as.nama","nama",0);
		$this->db->select_as("$this->tbl_as.harga_jual","harga_jual",0);
		$this->db->select_as("$this->tbl_as.foto","foto",0);
		$this->db->select_as("$this->tbl_as.thumb","thumb",0);
		$this->db->select_as("$this->tbl_as.satuan","satuan",0);
		$this->db->select_as("$this->tbl4_as.courier_services","courier_services",0);
		$this->db->select_as("$this->tbl_as.is_include_delivery_cost","is_include_delivery_cost",0);
		$this->db->select_as("COALESCE($this->tbl4_as.stok,0)","stok",0);
		$this->db->from($this->tbl,$this->tbl_as);
    $this->db->join_composite($this->tbl4,$this->tbl4_as,$this->__joinTbl4(),'left');
		$this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
		$this->db->where_as("$this->tbl_as.d_order_id",$this->db->esc($d_order_id));
    $this->db->where_as("$this->tbl_as.d_order_detail_id",$this->db->esc($d_order_detail_id));
		return $this->db->get('',0);
	}

	public function exportXlsCancellation($nation_code,$keyword="",$settlement_status="",$sdate="",$edate=""){
		$this->db->select_as("CONCAT($this->tbl_as.d_order_id,'-',$this->tbl_as.c_produk_id)","id",0);
		$this->db->select_as("$this->tbl3_as.cdate","cdate",0);
		$this->db->select_as("$this->tbl3_as.invoice_code","invoice_code",0);
		$this->db->select_as("$this->tbl_as.nama","nama",0);
		$this->db->select_as("$this->tbl_as.harga_jual","harga_jual",0);
		$this->db->select_as("$this->tbl_as.qty","qty",0);
		$this->db->select_as("$this->tbl2_as.sub_total","sub_total",0);
		$this->db->select_as("$this->tbl2_as.grand_total","grand_total",0);
		$this->db->select_as("COALESCE($this->tbl5_as.fnama,'-')","seller_name",0);
		$this->db->select_as("COALESCE($this->tbl2_as.cancel_fee,'-')","cancel_fee",0);
		$this->db->select_as("$this->tbl3_as.payment_status","payment_status",0);
		$this->db->select_as("('buyer')","rejected_by",0);
		$this->db->select_as("$this->tbl_as.settlement_status","resolution",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join_composite($this->tbl2,$this->tbl2_as,$this->__joinTbl2(),"inner");
		$this->db->join_composite($this->tbl3,$this->tbl3_as,$this->__joinTbl3(),"inner");
		$this->db->join_composite($this->tbl5,$this->tbl5_as,$this->__joinTbl5(),"left");
		$this->db->where_as("$this->tbl2_as.seller_status",$this->db->esc("confirmed"),"AND","=",0,0);
		$this->db->where_as("$this->tbl2_as.shipment_status",$this->db->esc("succeed"),"AND","=",0,0);
		$this->db->where_as("$this->tbl_as.buyer_status",$this->db->esc("rejected"),"AND","=",0,0);

		$this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
		$this->db->where_as("$this->tbl2_as.seller_status",$this->db->esc("confirmed"),"AND","=",0,0);
		$this->db->where_as("$this->tbl_as.buyer_status",$this->db->esc("rejected"),"AND","=",0,0);

		if(strlen($settlement_status)>0){
			$this->db->where_as("$this->tbl_as.settlement_status",$this->db->esc($settlement_status));
		}else{
			$this->db->where_in("$this->tbl_as.settlement_status",array("complain","wait"));
		}
		if(strlen($sdate)==10 && strlen($edate)==10){
			$this->db->between("DATE($this->tbl3_as.cdate)","DATE('$sdate')","DATE('$edate')");
		}else if(strlen($sdate)==10 && strlen($edate)!=10){
			$this->db->where_as("DATE($this->tbl3_as.cdate)","DATE('$sdate')",'AND','>=');
		}else if(strlen($sdate)!=10 && strlen($edate)==10){
			$this->db->where_as("DATE($this->tbl3_as.cdate)","DATE('$edate')",'AND','<=');
		}

		if(strlen($keyword)>0){
			$this->db->where_as("COALESCE($this->tbl3_as.invoice_code,'-')",addslashes($keyword),"OR","%like%",1,0);
			$this->db->where_as("CONCAT(COALESCE($this->tbl3_as.invoice_code,'-'),'-',$this->tbl_as.d_order_detail_id,'-',$this->tbl_as.c_produk_id)",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("$this->tbl4_as.nama",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("COALESCE($this->tbl3_as.order_status,'-')",addslashes($keyword),"OR","%like%",0,1);
		}
		$this->db->order_by("$this->tbl3_as.id","asc");
		$this->db->order_by("$this->tbl2_as.id","asc");
		$this->db->order_by("$this->tbl_as.c_produk_id","asc");
		return $this->db->get('',0);
	}
	
	// by Muhammad Sofi 9 February 2022 10:00 | fix button export to excel
	public function exportXlsRejectBuyer($nation_code, $keyword="", $cdate_start="", $cdate_end="", $settlement_status="") {
        $this->db->flushQuery();
        $this->db->select_as("CONCAT($this->tbl_as.d_order_id,'-',$this->tbl_as.d_order_detail_id,'-',$this->tbl_as.c_produk_id)", "order_id", 0);
		$this->db->select_as("$this->tbl3_as.cdate", "cdate", 0);
        $this->db->select_as("CONCAT(COALESCE($this->tbl3_as.invoice_code,'-'),'-',$this->tbl_as.d_order_detail_id,'-',$this->tbl_as.c_produk_id)", "invoice_number", 0);
        $this->db->select_as("$this->tbl_as.nama", "product_name", 0);
        $this->db->select_as("$this->tbl_as.harga_jual", "price", 0);
        $this->db->select_as("$this->tbl_as.qty", "qty", 0);
		$this->db->select_as("($this->tbl_as.harga_jual*$this->tbl_as.qty)", "sub_total", 0);
        $this->db->select_as("$this->tbl_as.settlement_status", "settlement_status", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);

		if(strlen($cdate_start)==10 && strlen($cdate_end)==10) {
			$this->db->between("DATE($this->tbl3_as.cdate)","DATE('$cdate_start')","DATE('$cdate_end')");
		} else if(strlen($cdate_start)==10 && strlen($cdate_end)!=10){
			$this->db->where_as("DATE($this->tbl3_as.cdate)","DATE('$cdate_start')",'AND','>=');
		} else if(strlen($cdate_start)!=10 && strlen($cdate_end)==10){
			$this->db->where_as("DATE($this->tbl3_as.cdate)","DATE('$cdate_end')",'AND','<=');
		} else {}
        
		if(strlen($settlement_status) > 0) {
			$this->db->where_as("$this->tbl_as.settlement_status", $this->db->esc($settlement_status));
		} else {
			$this->db->where_in("$this->tbl_as.settlement_status", array("complain", "wait"));
		}
        
        // if (strlen($keyword)>1) {
        //     $this->db->where_as($this->__decrypt("$this->tbl_as.fnama"), addslashes($keyword), "OR", "%like%", 1, 0);
        //     $this->db->where_as($this->__decrypt("$this->tbl_as.telp"), addslashes($keyword), "OR", "%like%", 0, 0);
        //     $this->db->where_as($this->__decrypt("$this->tbl_as.email"), addslashes($keyword), "OR", "%like%", 0, 1);
        // }
        return $this->db->get('', 0);
    }

	// by Muhammad Sofi 9 February 2022 10:00 | fix button export to excel
	public function exportXlsRejectSeller($nation_code, $keyword="", $cdate_start="", $cdate_end="", $settlement_status="") {
        $this->db->flushQuery();
        $this->db->select_as("CONCAT($this->tbl2_as.d_order_id,'-',$this->tbl2_as.id)", "id", 0);
        $this->db->select_as("$this->tbl3_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl3_as.invoice_code", "invoice_number", 0);
        $this->db->select_as("$this->tbl2_as.nama", "product_name", 0);
        $this->db->select_as("$this->tbl2_as.total_item", "total_item", 0);
        $this->db->select_as("$this->tbl2_as.sub_total", "sub_total", 0);
        $this->db->select_as("($this->tbl2_as.shipment_cost + $this->tbl2_as.shipment_cost_add)", "shipping_cost", 0);
        $this->db->select_as("$this->tbl2_as.refund_amount", "refund_amount", 0);
        $this->db->select_as("$this->tbl2_as.settlement_status", "resolution", 0);
        $this->db->from($this->tbl2, $this->tbl2_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3_1(), "inner");
        $this->db->where_as("$this->tbl2_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl2_as.seller_status", $this->db->esc("rejected"), "AND", "=", 0, 0);

		if(strlen($cdate_start)==10 && strlen($cdate_end)==10) {
			$this->db->between("DATE($this->tbl3_as.cdate)","DATE('$cdate_start')","DATE('$cdate_end')");
		} else if(strlen($cdate_start)==10 && strlen($cdate_end)!=10){
			$this->db->where_as("DATE($this->tbl3_as.cdate)","DATE('$cdate_start')",'AND','>=');
		} else if(strlen($cdate_start)!=10 && strlen($cdate_end)==10){
			$this->db->where_as("DATE($this->tbl3_as.cdate)","DATE('$cdate_end')",'AND','<=');
		} else {}
        
		if(strlen($settlement_status) > 0) {
			$this->db->where_as("$this->tbl2_as.settlement_status", $this->db->esc($settlement_status));
		} else {
			$this->db->where_in("$this->tbl2_as.settlement_status", array("complain", "wait"));
		}
        
        // if (strlen($keyword)>1) {
        //     $this->db->where_as($this->__decrypt("$this->tbl_as.fnama"), addslashes($keyword), "OR", "%like%", 1, 0);
        //     $this->db->where_as($this->__decrypt("$this->tbl_as.telp"), addslashes($keyword), "OR", "%like%", 0, 0);
        //     $this->db->where_as($this->__decrypt("$this->tbl_as.email"), addslashes($keyword), "OR", "%like%", 0, 1);
        // }
        return $this->db->get('', 0);
    }
}
