<?php
class C_Produk_Foto_Model extends SENE_Model{
	var $tbl = 'c_produk_foto';
	var $tbl_as = 'cpf';
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

	public function getLastId($nation_code,$c_produk_id){
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("c_produk_id",$c_produk_id);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}

	public function getTableAlias(){
		return $this->tbl_as;
	}
	public function getById($nation_code, $id,$c_produk_id){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->get_first();
	}
	public function getByIdProdukId($nation_code, $id,$c_produk_id){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		$this->db->where("c_produk_id",$c_produk_id);
		return $this->db->get_first();
	}
	public function getByProdukId($nation_code, $c_produk_id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("c_produk_id",$c_produk_id);
		return $this->db->get();
	}
	public function setMass($cpfs_data){
		$this->db->insert_multi($this->tbl, $cpfs_data);
	}
	public function delByProdukId($nation_code,$c_produk_id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("c_produk_id",$c_produk_id);
		$this->db->delete($this->tbl);
	}
	public function set($di){
		return $this->db->insert($this->tbl, $di);
	}
	public function delByIdProdukId($nation_code, $c_produk_id, $id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("c_produk_id",$c_produk_id);
		$this->db->where("id",$id);
		return $this->db->delete($this->tbl,0,0);
	}
	public function delByUrlGambar($nation_code,$url){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("url",$url);
		$this->db->delete($this->tbl);
	}
}
