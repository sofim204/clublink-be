<?php
class A_Pengguna_Model extends SENE_Model{
	var $tbl = 'a_pengguna';
	var $tbl_as = 'ap';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function getFirst(){
		return $this->db->get_first();
	}
	public function getEmailActive(){
		$this->db->select_as("$this->tbl_as.username, $this->tbl_as.nama, $this->tbl_as.email",'email',0);
		$this->db->where("is_receive_email",1);
		$this->db->where("is_active",1);
		return $this->db->get('',0);
	}
}
