<?php
class B_Lokasi_Model extends SENE_Model{
	var $tbl = 'b_lokasi';
	var $tbl_as = 'bl';
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
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}

	public function get($nation_code,$keyword=""){
		$this->db->where("nation_code",$nation_code);
    if(strlen($keyword)){
      $this->db->where_as("provinsi",$keyword,'OR','LIKE%%',1,0);
      $this->db->where_as("kabkota",$keyword,'OR','LIKE%%',0,0);
      $this->db->where_as("kecamatan",$keyword,'OR','LIKE%%',0,0);
      $this->db->where_as("kelurahan",$keyword,'OR','LIKE%%',0,1);
    }
		$this->db->from($this->tbl,$this->tbl_as);
    return $this->db->get('',0);
	}
	public function count($nation_code,$keyword=""){
    $this->db->select_as("COUNT(*)","total",0);
		$this->db->where("nation_code",$nation_code);
    if(strlen($keyword)){
      $this->db->where_as("provinsi",$keyword,'OR','LIKE%%',1,0);
      $this->db->where_as("kabkota",$keyword,'OR','LIKE%%',0,0);
      $this->db->where_as("kecamatan",$keyword,'OR','LIKE%%',0,0);
      $this->db->where_as("kelurahan",$keyword,'OR','LIKE%%',0,1);
    }
		$this->db->from($this->tbl,$this->tbl_as);
    $d = $this->db->get_first();
    if(isset($d->total)) return $d->total;
    return 0;
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
	public function getById($nation_code, $id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->from($this->tbl)->get_first();
	}
	public function getByNationCode($nation_code){
		$this->db->where("$nation_code",$nation_code);
		$this->db->from($this->tbl,$this->tbl_as);
    return $this->db->get_first();
	}
	public function check($negara, $provinsi="", $kabkota="", $kecamatan="", $kelurahan=""){
		//negara is object obtained from single row from a_negara.
		if(!isset($negara->nation_code)) {
			trigger_error("B_LOKASI_MODEL::CHECK requires negara object obtained from single row from a_negara");
			die();
		}
		$this->db->where("nation_code",$negara->nation_code);
    if(!empty($negara->is_provinsi)) $this->db->where_as("provinsi",$this->db->esc($provinsi),'AND','LIKE',0,0);
    if(!empty($negara->is_kabkota)) $this->db->where_as("kabkota",$this->db->esc($kabkota),'AND','LIKE',0,0);
    if(!empty($negara->is_kecamatan)) $this->db->where_as("kecamatan",$this->db->esc($kecamatan),'AND','LIKE',0,0);
    if(!empty($negara->is_kelurahan)) $this->db->where_as("kelurahan",$this->db->esc($kelurahan),'AND','LIKE',0,0);
		$this->db->from($this->tbl,$this->tbl_as);
    return $this->db->get_first();
	}
	public function searchByKecamatan($nation_code, $kecamatan){
		$this->db->where("nation_code",$nation_code);
		if(strlen($kecamatan)) $this->db->where("kecamatan",$kecamatan);
    return $this->db->get();
	}
	public function searchByKecamatanDanKelurahan($nation_code, $kecamatan, $kelurahan){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("is_active",1);
		if(strlen($kecamatan)) $this->db->where("kecamatan",$kecamatan,'OR','%like%',0,0);
		if(strlen($kelurahan)) $this->db->where("kelurahan",$kelurahan,'OR','%like%',0,0);
    return $this->db->get();
	}
	public function search($nation_code, $keyword){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("is_active",1);
		if(strlen($keyword)){
			$this->db->where("provinsi",$keyword,'OR','%like%',1,0);
			$this->db->where("kabkota",$keyword,'OR','%like%',0,0);
			$this->db->where("kecamatan",$keyword,'OR','%like%',0,0);
			$this->db->where("kelurahan",$keyword,'OR','%like%',0,1);
		}
    return $this->db->get();
	}
}
