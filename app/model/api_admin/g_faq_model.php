<?php
class G_FAQ_Model extends JI_Model {
	var $tbl = 'g_faq';
	var $tbl_as = 'gf';

	public function __construct() {
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	public function getTableAlias() {
		return $this->tbl_as;
	}

	public function getLastId($nation_code) {
		$this->db->select_as("MAX($this->tbl_as.id)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code", $nation_code);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}

	public function getById($nation_code, $id) {
		$this->db->where("nation_code", $nation_code);
		$this->db->where("id", $id);
		return $this->db->get_first();
	}

	public function set($di) {
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl, $di, 0, 0);
	}

	public function del($nation_code,$id) {
		$this->db->where("id",$id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete($this->tbl);
	}

	public function update($nation_code, $id, $du) {
		if(!is_array($du)) return 0;
		$this->db->where("id",$id);
		$this->db->where("nation_code", $nation_code);
   	 	return $this->db->update($this->tbl, $du, 0);
	}
}
