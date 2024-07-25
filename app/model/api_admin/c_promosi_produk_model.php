<?php
class C_Promosi_Produk_Model extends SENE_Model{
	var $tbl = 'c_promosi_produk';
	var $tbl_as = 'cprp';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
  public function truncate(){
    $sql = 'TRUNCATE TABLE '.$this->tbl.';';
    return $this->db->exec($sql);
  }
  public function setMass($produks){
    return $this->db->insert_multi($this->tbl,$produks,0);
  }
  public function deleteAll($id){
    $this->db->where('c_promosi_id',$id);
    return $this->db->delete($this->tbl);
  }
}
