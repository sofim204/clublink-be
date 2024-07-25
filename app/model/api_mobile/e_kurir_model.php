<?php
class E_Kurir_Model extends SENE_Model{
	var $tbl = 'e_kurir';
	var $tbl_as = 'ekur';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function get($limit="100",$is_active="1"){
    $this->db->select_as("nama","nama",0);
    $this->db->select_as("tarif","tarif",0);
    $this->db->select_as("dayest","dayest",0);
		$this->db->order_by('prioritas','asc');
		$this->db->where('is_active',$is_active);
		$this->db->limit($limit);
		return $this->db->get();
	}
}
