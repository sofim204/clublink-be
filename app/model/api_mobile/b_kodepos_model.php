<?php
class B_Kodepos_Model extends SENE_Model{
	var $tbl = 'b_kodepos';
	var $tbl_as = 'bkp';
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
	public function set($di){
		return $this->db->insert($this->tbl,$di,0,0);
	}
	public function update($nation_code, $id,$du){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->update($this->tbl,$du);
	}
	public function del($nation_code, $id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->delete($this->tbl);
	}

	public function getLastId($nation_code){
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}

	public function check($negara, $b_lokasi_id="null", $kode){
		//negara is object obtained from single row from a_negara.
		if(!isset($negara->nation_code)) {
			trigger_error("B_LOKASI_MODEL::CHECK requires negara object obtained from single row from a_negara");
			die();
		}
		$this->db->where("nation_code",$negara->nation_code);
		$this->db->where("b_lokasi_id",$b_lokasi_id);
    if(!empty($negara->is_kodepos)) $this->db->where_as("kode",$this->db->esc($kode),'AND','LIKE',0,0);
		$this->db->from($this->tbl,$this->tbl_as);
    return $this->db->get_first();
	}
}
