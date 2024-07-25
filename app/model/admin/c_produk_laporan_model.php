<?php
class C_Produk_Laporan_Model extends SENE_Model {
	var $is_cacheable;
	var $tbl = 'c_produk_laporan';
	var $tbl_as = 'cpl';

	public function __construct(){
		parent::__construct();
		$this->is_cacheable = 0;
    $this->db->from($this->tbl,$this->tbl_as);
	}

	public function getByIds($nation_code, $pids=array()){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where('nation_code',$nation_code);
		$this->db->where_in('id',$pids);
		return $this->db->get();
	}


}
