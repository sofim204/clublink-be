<?php
class A_Negara_Model extends SENE_Model {
	var $tbl = 'a_negara';
	var $tbl_as = 'an';
  public function __construct(){
		parent::__construct();
		$this->is_cacheable = 0;
    $this->db->from($this->tbl,$this->tbl_as);
	}
	public function getHashkey(){
		return $this->hashkey;
	}
	public function getTableAlias(){
		return $this->tbl_as;
	}
  public function get($is_active=""){
		$this->db->order_by('nama','asc');
    if(strlen($is_active)) $this->db->where('is_active',$is_active);
    return $this->db->get();
  }
	public function getByNationCode($nation_code){
		$this->db->where("nation_code",$nation_code);
		return $this->db->get_first();
	}
}
