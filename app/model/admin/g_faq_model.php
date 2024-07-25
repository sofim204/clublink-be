<?php
class G_FAQ_Model extends SENE_Model{
	var $tbl = 'g_faq';
	var $tbl_as = 'gf';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	public function getTableAlias(){
		return $this->tbl_as;
	}

    public function getAllResult($nation_code, $language_id) {
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.title", "title", 0);
		$this->db->select_as("$this->tbl_as.content", "content", 0);
		$this->db->select_as("$this->tbl_as.priority", "priority", 0);
		$this->db->select_as("$this->tbl_as.language_id", "language_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where("$this->tbl_as.language_id", $language_id, "AND", "=", 0, 0);
		$this->db->order_by("$this->tbl_as.priority", "ASC"); // by Muhammad Sofi 21 January 2022 19:35 | add order data by priority ASC
		return $this->db->get("", 0);
    }
}
