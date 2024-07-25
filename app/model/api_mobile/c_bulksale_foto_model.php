<?php
class C_BulkSale_Foto_Model extends SENE_Model{
	var $tbl = 'c_bulksale_foto';
	var $tbl_as = 'cbsf';
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

	public function getLastId($nation_code,$c_bulksale_id){
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("c_bulksale_id",$c_bulksale_id);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}

	public function getById($nation_code, $c_bulksale_id, $id){
		$this->db->select()
						->from($this->tbl,$this->tbl_as)
						->where("$nation_code",$nation_code)
						->where("c_bulksale_id",$c_bulksale_id)
						->where("id",$id);
		return $this->db->get_first();
	}
	public function countByBulkSaleId($nation_code, $c_bulksale_id){
		$this->db->select_as("COUNT(*)",'total',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("c_bulksale_id",$c_bulksale_id);
		$d = $this->db->get_first();
		if(isset($d->total)) return $d->total;
		return 0;
	}
	public function getByBulkSaleId($nation_code, $c_bulksale_id){
		$this->db->select_as("$this->tbl_as.nation_code",'nation_code',0);
		$this->db->select_as("$this->tbl_as.c_bulksale_id",'c_bulksale_id',0);
		$this->db->select_as("$this->tbl_as.id",'id',0);
		$this->db->select_as("$this->tbl_as.url",'url',0);
		$this->db->select_as("$this->tbl_as.url_thumb",'url_thumb',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("c_bulksale_id",$c_bulksale_id);
		return $this->db->get();
	}
	public function getLastByProdukId($c_bulksale_id){
		$this->db->select_as("$this->tbl_as.id",'id',0);
		$this->db->select_as("$this->tbl_as.c_bulksale_id",'c_bulksale_id',0);
		$this->db->select_as("$this->tbl_as.url",'url',0);
		$this->db->select_as("$this->tbl_as.url_thumb",'url_thumb',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("c_bulksale_id",$c_bulksale_id);
		$this->db->order_by("id","asc");
		return $this->db->get_first();
	}
	public function getByIdBulkSaleId($nation_code, $id, $c_bulksale_id){
		$this->db->select_as("$this->tbl_as.id",'id',0);
		$this->db->select_as("$this->tbl_as.c_bulksale_id",'c_bulksale_id',0);
		$this->db->select_as("$this->tbl_as.url",'url',0);
		$this->db->select_as("$this->tbl_as.url_thumb",'url_thumb',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		$this->db->where("c_bulksale_id",$c_bulksale_id);
		return $this->db->get_first('',0);
	}
	public function getByBulkSaleIds($nation_code, $c_bulksale_ids){
		if(!is_array($c_bulksale_ids)) return array();
		$this->db->select_as("$this->tbl_as.id",'id',0);
		$this->db->select_as("$this->tbl_as.c_bulksale_id",'c_bulksale_id',0);
		$this->db->select_as("$this->tbl_as.url",'url',0);
		$this->db->select_as("$this->tbl_as.url_thumb",'url_thumb',0);
		$this->db->from($this->tbl,$this->tbl_as);
    $this->db->where("nation_code",$nation_code);
		$this->db->where_in("c_bulksale_id",$c_bulksale_ids);
		return $this->db->get();
	}
	public function set($dix){
		return $this->db->insert($this->tbl, $dix);
	}
	public function del($id){
		$this->db->where("id",$id);
		return $this->db->delete($this->tbl);
	}
	public function delByIdBulkSaleId($nation_code,$id,$c_bulksale_id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		$this->db->where("c_bulksale_id",$c_bulksale_id);
		return $this->db->delete($this->tbl);
	}
	public function delByBulkSaleId($nation_code,$c_bulksale_id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("c_bulksale_id",$c_bulksale_id);
		return $this->db->delete($this->tbl);
	}
}
