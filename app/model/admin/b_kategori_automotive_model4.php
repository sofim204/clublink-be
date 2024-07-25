<?php
class B_Kategori_Automotive_Model4 extends SENE_Model{
	var $tbl = 'b_kategori';
	var $tbl_as = 'bk';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	public function getById($nation_code, $id){
		// by Muhammad Sofi 22 December 2021 10:00 | change query where
		// $this->db->where("nation_code",$nation_code);
		// $this->db->where("id",$id);
		$this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
		$this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
		return $this->db->get_first();
	}
}
