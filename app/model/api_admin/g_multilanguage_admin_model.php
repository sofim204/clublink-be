<?php
class G_Multilanguage_Admin_Model extends SENE_Model {
	var $tbl = 'g_multilanguage';
	var $tbl_as = 'g_ml';

	public function __construct() {
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	public function trans_start(){
		$r = $this->db->autocommit(0);
		if($r) return $this->db->begin();
		return false;
	}

	public function trans_commit(){
		return $this->db->commit();
	}

	public function trans_rollback(){
		return $this->db->rollback();
	}

	public function trans_end(){
		return $this->db->autocommit(1);
	}

	public function update($id, $du){
		if(!is_array($du)) return 0;
		$this->db->where("id", $id);
    	return $this->db->update($this->tbl, $du, 0);
	}

	// START by Muhammad Sofi 27 January 2022 16:42 | adding form add data
	public function set($di) {
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl, $di, 0, 0);
	}

	public function getLastId() {
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}
	// END by Muhammad Sofi 27 January 2022 16:42 | adding form add data

	public function del($id) {
		$this->db->where("id", $id);
		return $this->db->delete($this->tbl);
	}

	public function getAll($page=0, $pagesize=10, $keyword="", $type="") {
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY variable_name ASC)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.variable_name", "variable_name", 0);
		$this->db->select_as("COALESCE($this->tbl_as.indonesia, '-')", "indonesia", 0);
		$this->db->select_as("COALESCE($this->tbl_as.english, '-')", "english", 0);
		$this->db->select_as("COALESCE($this->tbl_as.korea, '-')", "korea", 0);
		$this->db->select_as("COALESCE($this->tbl_as.thailand, '-')", "thailand", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		// $this->db->where_as("$this->tbl_as.type", $this->db->esc("mobile")); // by Muhammad Sofi 2 February 2022 11:14 | add where by type
		if(strlen($type)>0) {
			$this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
		}
		if(mb_strlen($keyword)>0) {
			$this->db->where("$this->tbl_as.variable_name", $keyword, "OR", "%like%", 1, 0);
			$this->db->where("$this->tbl_as.indonesia", $keyword, "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.english", $keyword, "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.korea", $keyword, "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.thailand", $keyword, "OR", "%like%", 0, 1);
		}

		$this->db->order_by("variable_name", "ASC");

		$this->db->limit($page, $pagesize);
		return $this->db->get("object", 0);
	}

	public function countAll($keyword="", $type="") {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)", "jumlah", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		// $this->db->where_as("$this->tbl_as.type", $this->db->esc("mobile")); // by Muhammad Sofi 2 February 2022 11:14 | add where by type
		if(strlen($type)>0) {
			$this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
		}
		if(mb_strlen($keyword)>0) {
			$this->db->where("$this->tbl_as.variable_name", $keyword, "OR", "%like%", 1, 0);
			$this->db->where("$this->tbl_as.indonesia", $keyword, "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.english", $keyword, "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.korea", $keyword, "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.thailand", $keyword, "OR", "%like%", 0, 1);
		}
		$d = $this->db->get_first("object", 0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function getById($id){
		$this->db->where("id", $id);
		return $this->db->get_first();
	}
}
