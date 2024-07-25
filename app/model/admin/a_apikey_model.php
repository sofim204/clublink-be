<?php
class A_ApiKey_Model extends SENE_Model{
	var $tbl = 'a_apikey';
	var $tbl_as = 'aa';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function getById($nation_code, $id){
		$this->db->where('nation_code',$nation_code);
		$this->db->where('id',$id);
		return $this->db->get_first();
	}
}
