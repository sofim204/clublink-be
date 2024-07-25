<?php
class C_Community_Hashtag_Model extends SENE_Model {
	var $tbl = 'c_community_hashtag_list';
	var $tbl_as = 'cchl';

	public function __construct() {
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	public function getTableAlias(){
		return $this->tbl_as;
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

	public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="cdate", $sortDir="", $keyword="", $utype_in=array()) {
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate DESC)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.name", "name", 0);
		$this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		if(mb_strlen($keyword)>0) {
			$this->db->where_as("$this->tbl_as.name", addslashes($keyword), "OR", "%like%", 0, 0);
		}
		// $this->db->order_by("$this->tbl_as.prioritas", "ASC")->limit($page,$pagesize);
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object", 0);
	}

	public function countAll($nation_code, $keyword="") {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code",$nation_code,"AND","=",0,0);
		if(mb_strlen($keyword)>0) {
			$this->db->where_as("$this->tbl_as.name", addslashes($keyword), "OR", "%like%", 0, 0);
		}
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
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

	public function update($nation_code, $id, $du) {
		if(!is_array($du)) return 0;
		$this->db->where("nation_code", $nation_code);
		$this->db->where("id", $id);
        return $this->db->update($this->tbl, $du, 0);
	}

	public function del($nation_code, $id){
		$this->db->where("nation_code", $nation_code);
		$this->db->where("id", $id);
		return $this->db->delete($this->tbl);
	}
    
}
