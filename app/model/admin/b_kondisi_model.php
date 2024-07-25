<?php
class B_Kondisi_Model extends SENE_Model{
	var $tbl = 'b_kondisi';
	var $tbl_as = 'bkon';
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
	public function getActive($nation_code){
		$this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
		$this->db->where_as("$this->tbl_as.is_active",1);
		$this->db->order_by("nama","asc");
		return $this->db->get();
	}
	public function getById($nation_code,$b_kondisi_id){
		$this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
		$this->db->where_as("$this->tbl_as.id",$this->db->esc($b_kondisi_id));
		$this->db->order_by("nama","asc");
		return $this->db->get_first();
	}
}
