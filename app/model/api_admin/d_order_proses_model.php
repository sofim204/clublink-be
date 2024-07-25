<?php
class D_Order_Proses_Model extends SENE_Model{
	var $tbl = 'd_order_proses';
	var $tbl_as = 'dop';

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
	public function getLastId($nation_code,$d_order_id,$c_produk_id){
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("d_order_id",$d_order_id);
		$this->db->where("c_produk_id",$c_produk_id);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return (int) $d->last_id;
		return 0;
	}
	public function setMass($dis){
		if(!is_array($dis)) return 0;
		return $this->db->insert_multi($this->tbl,$dis,0);
	}
	public function set($di){
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl,$di,0,0);
	}
	public function update($nation_code, $d_order_id, $id, $du){
		if(!is_array($du)) return 0;
		$this->db->where_as("nation_code",$nation_code);
		$this->db->where("d_order_id",$d_order_id);
		$this->db->where("id",$id);
		return $this->db->update($this->tbl,$du,0);
	}
	public function del($nation_code, $d_order_id, $id, $du){
		$this->db->where_as("nation_code",$nation_code);
		$this->db->where("d_order_id",$d_order_id);
		$this->db->where("id",$id);
		return $this->db->delete($this->tbl);
	}
  public function getById($nation_code,$d_order_id){
    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->where("nation_code",$nation_code);
    $this->db->where("d_order_id",$d_order_id);
    return $this->db->get_first();
  }
  public function getByOrderId($nation_code,$d_order_id){
    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->where("nation_code",$nation_code);
    $this->db->where("d_order_id",$d_order_id);
    $this->db->order_by("id","desc");
    return $this->db->get();
  }
  public function getByOrderIdProdukId($nation_code,$d_order_id,$c_produk_id){
    $this->db->from($this->tbl, $this->tbl_as);
    $this->db->where("nation_code",$nation_code);
    $this->db->where("d_order_id",$d_order_id);
    $this->db->where("c_produk_id",$c_produk_id);
    $this->db->order_by("id","desc");
    return $this->db->get();
  }
}
