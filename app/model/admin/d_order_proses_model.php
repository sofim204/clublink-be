<?php
class D_Order_Proses_Model extends SENE_Model {
	var $is_cacheable;
	var $tbl = 'd_order_proses';
	var $tbl_as = 'dorp';
	var $tbl2 = 'd_order';
	var $tbl2_as = 'dor';

	public function __construct(){
		parent::__construct();
		$this->is_cacheable = 0;
    $this->db->from($this->tbl,$this->tbl_as);
	}
	public function getTableAlias(){
		return $this->tbl_as;
	}
  public function getByOrderId($nation_code,$d_order_id){
    $this->db->where("nation_code",$nation_code);
    $this->db->where("d_order_id",$d_order_id);
    $this->db->order_by("id","desc");
    return $this->db->get();
  }
  public function getDetailByID($nation_code,$d_order_id,$c_produk_id){
		$this->db->where("nation_code",$nation_code);
    $this->db->where("d_order_id",$d_order_id);
    $this->db->where("c_produk_id",$c_produk_id);
    $this->db->order_by("id","desc");
    return $this->db->get();
  }
}
