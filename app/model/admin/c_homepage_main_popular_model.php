<?php
class C_Homepage_Main_Popular_Model extends SENE_Model{
	var $tbl = 'c_homepage_main_popular';
	var $tbl_as = 'chmp';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	
	public function getTableAlias(){
		return $this->tbl_as;
	}

    public function getById($nation_code, $id){
		$this->db->where("nation_code", $nation_code);
		$this->db->where("id", $id);
		return $this->db->get_first();
	}

}