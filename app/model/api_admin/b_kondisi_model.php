<?php
class B_Kondisi_Model extends SENE_Model{
	var $tbl = 'b_kondisi';
	var $tbl_as = 'bk';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
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
	public function getLastId($nation_code){
		$this->db->select_as("MAX($this->tbl_as.id)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}

	public function getAll($nation_code, $page=0,$pagesize=10,$sortCol="",$sortDir="ASC",$keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY prioritas)", "no"); // by Muhammad Sofi 21 December 2021 15:06 | add row number
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.nama", "nama", 0);
		$this->db->select_as("$this->tbl_as.prioritas", "prioritas", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code",$nation_code,"AND","=", 0, 0);
		if(mb_strlen($keyword)>0){
			$this->db->where("nama",$keyword,"OR","%like%",1,0);
			$this->db->where("nilai",$keyword,"OR","%like%",0,1);
		}
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}
	public function countAll($nation_code, $keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code,"AND","=",0,0);
		if(mb_strlen($keyword)>0){
			$this->db->where("nama",$keyword,"OR","%like%",1,0);
			$this->db->where("nilai",$keyword,"OR","%like%",0,1);
		}
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
	public function getById($nation_code, $id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->get_first();
	}
	public function set($di){
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl,$di,0,0);
	}
	public function update($nation_code,$id,$du){
		if(!is_array($du)) return 0;
		$this->db->where("id",$id);
		$this->db->where("nation_code",$nation_code);
    return $this->db->update($this->tbl,$du,0);
	}
	public function del($nation_code,$id){
		$this->db->where("id",$id);
		$this->db->where("nation_code", $nation_code);
		return $this->db->delete($this->tbl);
	}
	public function getActive(){
		$this->db->where_as("$this->tbl_as.is_active",1);
		$this->db->order_by("nama","asc");
		return $this->db->get();
	}
}
