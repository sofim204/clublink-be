<?php
class B_Berat_Model extends SENE_Model{
	var $tbl = 'b_berat';
	var $tbl_as = 'bber';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function get($nation_code, $limit="100",$is_active="1"){
		$this->db->where('nation_code',$nation_code);
		$this->db->where('is_active',$is_active);
		$this->db->order_by('prioritas','asc');
		$this->db->limit($limit);
		return $this->db->get();
	}
	public function getById($nation_code,$id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where_as("id",$this->db->esc($id));
		return $this->db->get_first();
	}
}
