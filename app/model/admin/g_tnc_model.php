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

    public function getAll($nation_code, $language_id) {
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.content", "content", 0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where("$this->tbl_as.language_id", $language_id, "AND", "=", 0, 0);
		return $this->db->get("", 0);
    }
}
