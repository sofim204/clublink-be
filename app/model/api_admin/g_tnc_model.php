<?php
class G_TNC_Model extends SENE_Model{
	var $tbl = 'g_tnc';
	var $tbl_as = 'gt';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	public function getTableAlias(){
		return $this->tbl_as;
	}

	public function update($nation_code, $du) {
		if(!is_array($du)) return 0;
		$this->db->where("nation_code", $nation_code);
		$this->db->where("language_id", 1);
    	return $this->db->update($this->tbl, $du, 0);
	}
	
	public function update_indonesia($nation_code, $du) {
		if(!is_array($du)) return 0;
		$this->db->where("nation_code", $nation_code);
		$this->db->where("language_id", 2);
    	return $this->db->update($this->tbl, $du, 0);
	}

    public function getAll($nation_code) {
		$this->db->flushQuery();
		// $this->db->select_as("$this->tbl_as.content", "content", 0);
		$this->db->where("nation_code", $nation_code);
		$this->db->where("language_id", 1);
		return $this->db->get_first();
    }

	public function getAllIndo($nation_code) {
		$this->db->flushQuery();
		// $this->db->select_as("$this->tbl_as.content", "content", 0);
		$this->db->where("nation_code", $nation_code);
		$this->db->where("language_id", 2);
		return $this->db->get_first();
    }
}
