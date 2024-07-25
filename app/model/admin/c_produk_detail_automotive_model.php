<?php
class C_Produk_Detail_Automotive_Model extends SENE_Model{
	var $tbl = 'c_produk_detail_automotive';
	var $tbl_as = 'cpda';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function getTableAlias(){
		return $this->tbl_as;
	}
	public function getByProdukId($nation_code, $c_produk_id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("c_produk_id",$c_produk_id);
		return $this->db->get_first();
	}
}
