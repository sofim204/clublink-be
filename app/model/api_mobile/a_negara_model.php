<?php
class A_Negara_Model extends SENE_Model{
	var $tbl = 'a_negara';
	var $tbl_as = 'an';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function get($nation_code){
		$this->db->order_by("nama","asc")->limit(300);
		return $this->db->get();
	}
	public function getByNationCode($nation_code){
		$this->db->where("nation_code",$nation_code);
		return $this->db->get_first('',0);
	}
}
