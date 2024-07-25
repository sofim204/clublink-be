<?php
class A_Toko_Model extends SENE_Model{
	var $tbl = 'a_toko';
	var $tbl_as = 'at';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function getFirst(){
		return $this->db->get_first();
	}
  public function getAll($page=0,$pagesize=10,$sortCol="id",$sortDir="ASC",$keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
    $this->db->select("nama_toko");
		$this->db->from($this->tbl,$this->tbl_as);
		
		return $this->db->get("object",0);
	}
}
