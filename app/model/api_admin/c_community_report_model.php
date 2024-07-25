<?php
class C_Community_Report_Model extends JI_Model{
	var $tbl = 'c_community_report';
	var $tbl_as = 'ccr';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	public function getTableAlias(){
		return $this->tbl_as;
	}

	public function getLastId($nation_code){
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}

	public function getById($nation_code, $id){
		$this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.kelurahan", "kelurahan");

		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code",$nation_code);
		$this->db->where_as("$this->tbl_as.id",$id);
		return $this->db->get_first();
	}

	public function set($di){
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl,$di,0,0);
	}

	public function update($nation_code, $id, $du){
		if(!is_array($du)) return 0;
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
    return $this->db->update($this->tbl,$du,0);
	}

	public function del($nation_code, $id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->delete($this->tbl);
	}

	public function delete_by_community_id($nation_code, $c_community_id){
		$this->db->where("nation_code", $nation_code);
		$this->db->where("c_community_id", $c_community_id);
		return $this->db->delete($this->tbl);
	}
}
