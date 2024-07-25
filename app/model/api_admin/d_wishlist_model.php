<?php
class D_Wishlist_Model extends SENE_Model{
	var $tbl = 'd_wishlist';
	var $tbl_as = 'dwl';
	var $tbl2 = 'c_produk';
	var $tbl2_as = 'cp';
	var $tbl3 = 'b_user';
	var $tbl3_as = 'bu';
	var $tbl4 = 'b_kategori';
	var $tbl4_as = 'bk';
	var $tbl5 = 'b_kondisi';
	var $tbl5_as = 'bko';
	var $tbl6 = 'b_berat';
	var $tbl6_as = 'bb';

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
