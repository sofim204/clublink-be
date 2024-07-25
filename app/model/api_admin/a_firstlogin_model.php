<?php
class A_Firstlogin_Model extends JI_Model {
	var $tbl = 'a_first_time_login_image';
	var $tbl_as = 'aftli';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	public function getLastId($nation_code){
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}

	public function set($di){
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl,$di,0,0);
	}

	public function update($nation_code, $id,$du){
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

	public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="kode", $sortDir="ASC", $keyword="", $is_active="") {
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY priority ASC)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.priority", "priority", 0);
		$this->db->select_as("$this->tbl_as.url", "url", 0);
		$this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		if(strlen($is_active)) $this->db->where_as("$this->tbl_as.is_active",$this->db->esc($is_active));
		if(mb_strlen($keyword)>0) {
			$this->db->where("$this->tbl_as.judul",$keyword,"OR","%like%",1,1);
		}
		$this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
		return $this->db->get("object", 0);
	}

	public function countAll($nation_code, $keyword="", $is_active="") {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)", "jumlah", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		if(strlen($is_active)) $this->db->where_as("$this->tbl_as.is_active", $this->db->esc($is_active));
		if(mb_strlen($keyword)>0){
			$this->db->where("$this->tbl_as.judul", $keyword, "OR", "%like%", 1, 1);
		}
		$d = $this->db->get_first("object", 0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function getById($nation_code, $id){
		$this->db->select_as("$this->tbl_as.*, COALESCE(cdate,CURRENT_DATE())","cdate");
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->get_first();
	}
}
