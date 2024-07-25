<?php
class D_Order_Pembayaran_Model extends SENE_Model {
	var $is_cacheable;
	var $maks_data = 9999999;
	var $tbl = 'd_order_pembayaran';
	var $tbl_as = 'dob';
	var $tbl2 = 'd_order';
	var $tbl2_as = 'dor';
	
	public function __construct(){
		parent::__construct();
		$this->is_cacheable = 0;
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
	public function getLastId($nation_code,$d_order_id){
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("d_order_id",$d_order_id);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}
  public function set($di){
    return $this->db->insert($this->tbl,$di);
  }
  public function update($nation_code,$d_order_id,$id,$du){
    $this->db->where("nation_code",$nation_code);
    $this->db->where("d_order_id",$d_order_id);
    $this->db->where("id",$id);
    return $this->db->update($this->tbl,$du);
  }
	public function del($nation_code, $d_order_id, $id){
    $this->db->where("nation_code",$nation_code);
		$this->db->where("d_order_id",$d_order_id);
		$this->db->where("id",$id);
		return $this->db->delete($this->tbl);
	}
  public function getByOrderid($nation_code,$d_order_id){
    $this->db->where("nation_code",$nation_code);
    $this->db->where("d_order_id",$d_order_id);
    $this->db->order_by("id","asc");
    return $this->db->get();
  }
}
