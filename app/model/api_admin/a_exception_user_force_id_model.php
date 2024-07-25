<?php
class A_Exception_User_Force_Id_Model extends SENE_Model {
	var $tbl = 'a_exception_user_force_id';
	var $tbl_as = 'aeufi';

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

	public function getAll($page=0, $pagesize=10, $sortCol="cdate", $sortDir="ASC", $keyword="") {
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		// $this->db->select_as("$this->tbl_as.id", "no", 0);
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		// $this->db->select_as("$this->tbl_as.b_user_id_sg", "b_user_id_sg", 0);
		$this->db->select_as("$this->tbl_as.b_user_id_id", "b_user_id_id", 0);
		$this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		$this->db->from($this->tbl, $this->tbl_as);

		if(mb_strlen($keyword)>0) {
			$this->db->where("$this->tbl_as.b_user_id_sg", $keyword, "OR", "%like%", 1, 0);
			$this->db->where("$this->tbl_as.b_user_id_id", $keyword, "OR", "%like%", 0, 1);
		}

		$this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
		return $this->db->get("object", 0);
	}

	public function countAll($keyword="") {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)", "jumlah", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		
		if(mb_strlen($keyword)>0) {
			$this->db->where("$this->tbl_as.b_user_id_sg", $keyword, "OR", "%like%", 1, 0);
			$this->db->where("$this->tbl_as.b_user_id_id", $keyword, "OR", "%like%", 0, 1);
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
