<?php
class C_CaraKirim_Model extends SENE_Model{
	var $tbl = 'c_carakirim';
	var $tbl_as = 'cck';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
  public function get(){
    $this->db->from($this->tbl,$this->tbl_as);
    $this->db->where("is_active",1);
    return $this->db->get();
  }
}
