<?php
class D_Order_Pembayaran_Model extends SENE_Model {
	var $is_cacheable;
	var $maks_data = 9999999;
	var $tbl = 'd_order';
	var $tbl_as = 'dor';
	var $tbl2 = 'd_order_detail';
	var $tbl2_as = 'dod';
	var $tbl3 = 'c_produk';
	var $tbl3_as = 'cp';
	var $tbl4 = 'b_user';
	var $tbl4_as = 'bu';
	var $tbl5 = 'a_pengguna';
	var $tbl5_as = 'ap';
	var $tbl6 = 'b_kategori';
	var $tbl6_as = 'bk';
	var $hashkey = 'uwmbeebbuwosas';
	public function __construct(){
		parent::__construct();
		$this->is_cacheable = 0;
    $this->db->from($this->tbl,$this->tbl_as);
	}
  public function set($di){
    return $this->db->insert($this->tbl,$di);
  }
  public function update($nation_code,$d_order_id,$id,$du){
    $this->db->where("nation_code",$nation_code);
    $this->db->where("d_order_id",$d_order_id);
    $this->db->where("id",$id);
    return $this->db->update($this->tbl,$du);
  }
}
