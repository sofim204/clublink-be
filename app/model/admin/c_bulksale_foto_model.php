<?php
class C_BulkSale_Foto_Model extends SENE_Model{
	var $tbl = 'c_bulksale_foto';
	var $tbl_as = 'cbsf';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function getTableAlias(){
		return $this->tbl_as;
	}
	public function getByBulkSaleId($nation_code, $c_bulksale_id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("c_bulksale_id",$c_bulksale_id);
		return $this->db->get();
	}
	public function setMass($cpfs_data){
		$this->db->insert_multi($this->tbl, $cpfs_data);
	}
}
