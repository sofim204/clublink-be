<?php
class G_Common_Code_Model extends SENE_Model {
	var $tbl = 'common_code';
	var $tbl_as = 'cc';

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
		$this->db->select_as("MAX($this->tbl_as.id)+1", "last_id", 0);
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
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY classified ASC)", "no");
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.classified", "classified", 0);
		$this->db->select_as("$this->tbl_as.code", "code", 0);
		$this->db->select_as("$this->tbl_as.codename", "codename", 0);
		$this->db->select_as("$this->tbl_as.use_yn", "use_yn", 0);
		$this->db->select_as("COALESCE($this->tbl_as.remark, '-')", "remark", 0);
		$this->db->from($this->tbl, $this->tbl_as);

		if(strlen($type)>0) {
			$this->db->where_as("$this->tbl_as.classified", $this->db->esc($type));
		}

		if(mb_strlen($keyword)>0) {
			$this->db->where("$this->tbl_as.codename", $keyword, "OR", "%like%", 1, 1);
		}

		$this->db->order_by("classified", "ASC");

		$this->db->limit($page, $pagesize);
		return $this->db->get("object", 0);
	}

	public function countAll($keyword="", $type="") {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)", "jumlah", 0);
		$this->db->from($this->tbl, $this->tbl_as);

		if(strlen($type)>0) {
			$this->db->where_as("$this->tbl_as.classified", $this->db->esc($type));
		}

		if(mb_strlen($keyword)>0) {
			$this->db->where("$this->tbl_as.codename", $keyword, "OR", "%like%", 1, 1);
		}

		$d = $this->db->get_first("object", 0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function getById($id){
		$this->db->where("id", $id);
		return $this->db->get_first();
	}

	public function getClassified($nation_code, $keyword="") {
		$this->db->flushQuery();
		$this->db->select_as("DISTINCT $this->tbl_as.classified", "id", 0);
		$this->db->select_as("$this->tbl_as.classified", "classified_list", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);

		if (mb_strlen($keyword)>0) {
			// by Muhammad Sofi 10 February 2022 15:31 | fix error when search user
			$this->db->where_as("$this->tbl_as.classified", $keyword, "OR", "%like%", 1, 1);;
		}

		return $this->db->get("object", 0);
	}

	public function getlastidcode($nation_code, $value_group) {
		$this->db->select_as("MAX($this->tbl_as.code)", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.classified", $this->db->esc($value_group), "AND", "=", 0, 0);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}
}
