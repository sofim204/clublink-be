<?php
class B_Berat_Model extends SENE_Model{
	var $tbl = 'b_berat';
	var $tbl_as = 'bber';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function get($limit="100",$is_active="1"){
		$this->db->order_by('prioritas','asc');
		$this->db->where('is_active',$is_active);
		$this->db->limit($limit);
		return $this->db->get();
	}
}
