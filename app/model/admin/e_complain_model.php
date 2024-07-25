<?php
class E_Complain_Model extends SENE_Model {
	var $tbl	= 'e_complain';
	var $tbl_as = 'ec';
	var $tbl2	= 'c_produk';
	var $tbl2_as = 'cp';
	var $tbl3 = 'd_order_detail_item';
	var $tbl3_as = 'dodi';
	var $tbl4 = 'd_order_detail';
	var $tbl4_as = 'dod';
	var $tbl5 = 'd_order';
	var $tbl5_as = 'dor';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	private function __joinTbl2(){
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl2_as.nation_code");
    $cps[] = $this->db->composite_create("$this->tbl_as.c_produk_id","=","$this->tbl2_as.id");
		return $cps;
	}

	private function __joinTbl3(){
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl3_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl_as.d_order_id","=","$this->tbl3_as.d_order_id");
		$cps[] = $this->db->composite_create("$this->tbl_as.d_order_detail_id","=","$this->tbl3_as.d_order_detail_id");
    $cps[] = $this->db->composite_create("$this->tbl_as.c_produk_id","=","$this->tbl3_as.c_produk_id");
		return $cps;
	}

	private function __joinTbl4(){
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl4_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl_as.d_order_id","=","$this->tbl4_as.d_order_id");
		$cps[] = $this->db->composite_create("$this->tbl_as.d_order_detail_id","=","$this->tbl4_as.id");
		return $cps;
	}

	private function __joinTbl5(){
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl5_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl_as.d_order_id","=","$this->tbl5_as.d_order_id");
		return $cps;
	}

  public function getByOrderId($nation_code,$d_order_id){
    $this->db->where_as("nation_code",$this->db->esc($nation_code));
    $this->db->where_as("d_order_id",$this->db->esc($d_order_id));
    return $this->db->get();
  }
  public function getByOrderDetailId($nation_code,$d_order_id,$d_order_detail_id){
		$this->db->select_as("$this->tbl_as.*, $this->tbl2_as.nama","nama");
		$this->db->select_as("$this->tbl2_as.foto","foto");
		$this->db->select_as("$this->tbl2_as.thumb","thumb");
		$this->db->select_as("$this->tbl3_as.harga_jual","harga_jual");
		$this->db->select_as("$this->tbl3_as.qty","qty");
		$this->db->select_as("$this->tbl4_as.seller_status","seller_status");
		$this->db->select_as("$this->tbl4_as.shipment_status","shipment_status");
		$this->db->select_as("$this->tbl4_as.buyer_confirmed","buyer_confirmed");
		$this->db->select_as("$this->tbl3_as.buyer_status","buyer_status");
		$this->db->select_as("$this->tbl3_as.settlement_status","settlement_status");
		$this->db->join_composite($this->tbl2,$this->tbl2_as,$this->__joinTbl2(),"inner");
		$this->db->join_composite($this->tbl3,$this->tbl3_as,$this->__joinTbl3(),"inner");
		$this->db->join_composite($this->tbl4,$this->tbl4_as,$this->__joinTbl4(),"inner");
    $this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
		$this->db->where_as("$this->tbl_as.d_order_id",$this->db->esc($d_order_id));
    $this->db->where_as("$this->tbl_as.d_order_detail_id",$this->db->esc($d_order_detail_id));
    return $this->db->get('');
  }
  public function getDetailByID($nation_code,$d_order_id,$b_user_id_buyer,$b_user_id_seller){
    $this->db->where_as("nation_code",$this->db->esc($nation_code));
    $this->db->where_as("d_order_id",$this->db->esc($d_order_id));
    $this->db->where_as("b_user_id_buyer",$this->db->esc($b_user_id_buyer));
    $this->db->where_as("b_user_id_seller",$this->db->esc($b_user_id_seller));
    return $this->db->get_first();
  }
}
