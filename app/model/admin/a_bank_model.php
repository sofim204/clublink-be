<?php
class A_Bank_Model extends SENE_Model{
	var $tbl = 'a_bank';
	var $tbl_as = 'ab';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function getAll($nation_code,$page=0,$pagesize=10,$sortCol="id",$sortDir="ASC",$keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select('nation_code');
		$this->db->select('id');
		$this->db->select('nama');
		$this->db->select('is_active');
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code,"AND","=",0,0);
		if(strlen($keyword)>0){
			$this->db->where("nama",$keyword,"OR","%like%");
    }
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}
	public function countAll($nation_code,$keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code,"AND","=",0,0);
		if(strlen($keyword)>0){
			$this->db->where("nama",$keyword,"OR","%like%");
		}
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
	public function getById($nation_code,$id){
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
    $this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
    return $this->db->update($this->tbl,$du,0);
	}
	public function del($nation_code,$id){
    $this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->delete($this->tbl);
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
	public function get($nation_code){
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("is_active",1);
		$this->db->where("nation_code",$nation_code);
		return $this->db->get();
	}
	public function count($nation_code){
		$this->db->select_as("COUNT(*)","total",0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("is_active",1);
		$this->db->where("nation_code",$nation_code);
		$d = $this->db->get_first();
		if(isset($d->total)) return $d->total;
		return 0;
	}
}
