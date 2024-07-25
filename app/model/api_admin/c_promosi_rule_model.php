<?php
class C_Promosi_Rule_Model extends SENE_Model{
	var $tbl = 'c_promosi_rule';
	var $tbl_as = 'cprr';
	var $tbl2 = 'c_produk';
	var $tbl2_as = 'cp';
	var $tbl3 = 'a_company';
	var $tbl3_as = 'ac';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function getTableAlias(){
		return $this->tbl_as;
	}
	public function getTableAlias2(){
		return $this->tbl2_as;
	}
	public function getAll($page=0,$pagesize=10,$sortCol="kode",$sortDir="ASC",$keyword="",$c_promosi_id="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select('id');
		$this->db->select('prioritas');
		$this->db->select('utype');
		$this->db->select('qty_limit');
		$this->db->select('is_active');
		$this->db->select('nama_target_utype');
		$this->db->select('is_get');
		$this->db->select('promo_jenis');
		$this->db->select('promo_nilai');
		$this->db->from($this->tbl,$this->tbl_as);
		if(strlen($c_promosi_id)) $this->db->where_as("$this->tbl_as.c_promosi_id",$c_promosi_id,"and","=",0,0);
		if(strlen($keyword)>1){
			$this->db->where_as("$this->tbl_as.utype",addslashes($keyword),"OR","%like%",1,0);
			$this->db->where_as("$this->tbl_as.nama_target_utype",addslashes($keyword),"OR","%like%",0,1);
		}

		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}
	public function countAll($keyword="",$c_promosi_id="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		if(strlen($c_promosi_id)) $this->db->where_as("$this->tbl_as.c_promosi_id",$c_promosi_id,"and","=",0,0);
		if(strlen($keyword)>1){
			$this->db->where_as("$this->tbl_as.utype",addslashes($keyword),"OR","%like%",1,0);
			$this->db->where_as("$this->tbl_as.nama_target_utype",addslashes($keyword),"OR","%like%",0,1);
		}
		$this->db->from($this->tbl,$this->tbl_as);
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
	public function getById($id){
		$this->db->where("id",$id);
		return $this->db->get_first();
	}
	public function getByPromosiId($id){
		$this->db->where("c_promosi_id",$id);
		$this->db->where("is_active",1);
		return $this->db->get();
	}
	public function set($di){
		if(!is_array($di)) return 0;
		$this->db->insert($this->tbl,$di,0,0);
		return $this->db->last_id;
	}
	public function update($id,$du){
		if(!is_array($du)) return 0;
		$this->db->where("id",$id);
    return $this->db->update($this->tbl,$du,0);
	}
	public function del($id){
		$this->db->where("id",$id);
		return $this->db->delete($this->tbl);
	}
	public function checkKode($kode,$id=0){
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->where("kode",$kode);
		if(!empty($id)) $this->db->where("id",$id,'AND','!=');
		$d = $this->db->from($this->tbl)->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
	public function checkInisial($inisial,$id=0){
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->where("inisial",$inisial);
		if(!empty($id)) $this->db->where("id",$id,'AND','!=');
		$d = $this->db->from($this->tbl)->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function select2($keyword=""){
		$this->db->select("id");
		$this->db->select("nama");
		$this->db->select("kode");
		$this->db->from($this->tbl,$this->tbl_as);
		if(strlen($keyword)>1){
			$this->db->where("nama",$keyword,"OR","%like%",1,0);
			$this->db->where("kode",$keyword,"OR","%like%",0,1);
		}
		return $this->db->get("object",0);
	}

	public function get(){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->order_by('utype','desc')->order_by('nama','asc');
		return $this->db->get();
	}
	public function getLatest($id=""){
		$this->db->select_as('MAX(prioritas)+1','prioritas');
		if(strlen($id)>0) $this->db->where("c_promosi_id",$id);
		$d = $this->db->get_first();
		if(isset($d->prioritas)) return $d->prioritas;
		return 0;
	}
}
