<?php
class C_Promosi_Model extends SENE_Model{
	var $tbl = 'c_promosi';
	var $tbl_as = 'cpr';
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
	public function getAll($page=0,$pagesize=10,$sortCol="kode",$sortDir="ASC",$keyword="",$in_utype="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.id",'id',0);
		$this->db->select_as("$this->tbl_as.nama",'nama',0);
		$this->db->select_as("COALESCE($this->tbl_as.adate,0)",'adate',0);
		$this->db->select_as("COALESCE($this->tbl_as.edate,0)",'edate',0);
		$this->db->select_as("$this->tbl_as.kode",'kode',0);
		$this->db->select_as("$this->tbl_as.prioritas",'prioritas',0);
		$this->db->select_as("$this->tbl_as.is_active",'is_active',0);
		$this->db->select_as("$this->tbl_as.persentase",'persentase',0);
		$this->db->select_as("$this->tbl_as.nominal",'nominal',0);
		$this->db->select_as("$this->tbl_as.max_jml",'max_jml',0);
		$this->db->select_as("$this->tbl_as.max_nominal",'max_nominal',0);
		$this->db->select_as("$this->tbl_as.min_order",'min_order',0);
		$this->db->select_as("$this->tbl_as.max_order",'max_order',0);
		$this->db->select_as("$this->tbl_as.ptype",'ptype',0);
		$this->db->select_as("$this->tbl_as.is_after_diskon",'is_after_diskon',0);
		$this->db->select_as("$this->tbl_as.is_gratis_order",'is_gratis_order',0);
		$this->db->select_as("$this->tbl_as.is_gratis_ongkir",'is_gratis_ongkir',0);
		$this->db->select_as("$this->tbl_as.is_batas_user",'is_batas_user',0);
		$this->db->select_as("$this->tbl_as.is_batas_tgl",'is_batas_tgl',0);
		$this->db->select_as("$this->tbl_as.is_chained",'is_chained',0);
		$this->db->select_as("COALESCE($this->tbl3_as.nama,0)",'a_company_id',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join($this->tbl3,$this->tbl3_as,'id',$this->tbl_as,'a_company_id','left');
		if(strlen($keyword)>1){
			$this->db->where_as("$this->tbl_as.kode",addslashes($keyword),"OR","%like%",1,0);
			$this->db->where_as("$this->tbl_as.nama",addslashes($keyword),"OR","%like%",0,1);
		}

		if(is_array($in_utype) && count($in_utype)){
			$this->db->where_in("utype",$in_utype);
		}

		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}
	public function countAll($keyword="",$in_utype="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		if(strlen($keyword)>1){
			$this->db->where_as("$this->tbl_as.kode",addslashes($keyword),"OR","%like%",1,0);
			$this->db->where_as("$this->tbl_as.nama",addslashes($keyword),"OR","%like%",0,1);
		}

		if(is_array($in_utype) && count($in_utype)){
			$this->db->where_in("utype",$in_utype);
		}
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join($this->tbl3,$this->tbl3_as,'id',$this->tbl_as,'a_company_id','left');
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
	public function getById($id){
		$this->db->where("id",$id);
		return $this->db->get_first();
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
	public function getLatest(){
		$this->db->select_as("MAX(prioritas)+1",'prioritas');
		$d = $this->db->get_first();
		if(isset($d->prioritas)) return $d->prioritas;
		return 0;
	}
}
