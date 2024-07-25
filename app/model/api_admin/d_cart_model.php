<?php
class D_Cart_Model extends SENE_Model{
	var $tbl = 'd_cart';
	var $tbl_as = 'dc';
	var $tbl2 = 'b_user';
	var $tbl2_as = 'bu';
	var $tbl3 = 'c_produk';
	var $tbl3_as = 'cp';
	var $tbl4 = 'b_kategori';
	var $tbl4_as = 'bk';
	var $tbl5 = 'b_berat';
	var $tbl5_as = 'bbi';
	var $tbl6 = 'b_kondisi';
	var $tbl6_as = 'bki';
	var $tbl7 = 'b_user';
	var $tbl7_as = 'bu2';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function delAllByProdukIds($nation_code,$c_produk_ids){
		$this->db->where("nation_code",$nation_code);
		$this->db->where_in("c_produk_id",$c_produk_ids);
		return $this->db->delete($this->tbl,0);
	}
}
