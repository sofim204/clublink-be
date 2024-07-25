<?php
class A_Pengguna_Model extends SENE_Model{
	var $tbl = 'a_pengguna';
	var $tbl_as = 'ap';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function auth($username){
		$this->db
			->select("*")
			->where_as("username",$this->db->esc($username));
		return $this->db->get_first('object',0);
	}
	public function update($nation_code, $id, $du){
		$this->db->where('nation_code',$nation_code);
		$this->db->where('id',$id);
		return $this->db->update($this->tbl,$du);
	}
	public function getById($nation_code, $id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->get_first();
	}
}
