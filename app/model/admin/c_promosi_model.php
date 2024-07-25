<?php
class C_Promosi_Model extends SENE_Model{
	var $tbl = 'c_promosi';
	var $tbl_as = 'cp';
	var $tbl2 = 'd_order';
	var $tbl2_as = 'dor';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
  public function get($a_company_id=""){
		if(strlen($a_company_id)) $this->db->where_as("COALESCE(a_company_id,'$a_company_id')",$a_company_id);
    $this->db->where("is_active",1);
    return $this->db->get();
  }
	public function getById($id){
		$this->db->where('id',$id);
		return $this->db->get_first();
	}
}
