<?php
class I_Group_Category_Model extends SENE_Model{
	var $tbl = 'i_group_category';
	var $tbl_as = 'igc';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	public function getTableAlias(){
		return $this->tbl_as;
	}

	public function getById($nation_code, $id){
		$this->db->where("nation_code", $nation_code);
		$this->db->where("id", $id);
		return $this->db->get_first();
	}

	public function update($nation_code, $id,$du){
		if(!is_array($du)) return 0;
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->update($this->tbl,$du,0);
	}
}