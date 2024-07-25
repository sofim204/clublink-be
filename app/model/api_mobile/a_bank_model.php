<?php
class A_Bank_Model extends SENE_Model{
	var $tbl = 'a_bank';
	var $tbl_as = 'ab';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
  public function getActive(){
    //$this->db->select_as("$this->tbl_as.*, AES_ENCRYPT(`code`,UNHEX('A2B1C3D4'))",'code',0);
		$this->db->from($this->tbl,$this->tbl_as);
    $this->db->where("is_active",1);
    return $this->db->get('',0);
  }
}
