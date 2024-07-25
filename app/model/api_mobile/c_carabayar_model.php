<?php
class C_Carabayar_Model extends SENE_Model{
	var $tbl = 'c_carabayar';
	var $tbl_as = 'ccb';
	var $tbl2 = 'b_bank';
	var $tbl2_as = 'bb';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	private function __join_on(){
		$join_on   = array();
		$join_on[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl2_as.nation_code","AND");
		$join_on[] = $this->db->composite_create("$this->tbl_as.id","=","$this->tbl2_as.id","AND");
		return $join_on;
	}
	public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="kode", $sortDir="ASC", $keyword="", $sdate="", $edate=""){
		$this->db->flushQuery();

		$this->db->select_as("$this->tbl_as.id, COALESCE($this->tbl2_as.bank_nama,'-'),$this->tbl_as.utype,$this->tbl_as.kode,$this->tbl_as.nama,$this->tbl_as.is_active",'is_active',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join_composite($this->tbl2,$this->tbl2_as,$this->__join_on(),"");
		$this->db->where_as("$this->tbl_as.nation_code",$nation_code);
		if(strlen($keyword)>0){
			$this->db->where_as("$this->tbl_as.paymeny_method",addslashes($keyword),"OR","%like%",1,0);
			$this->db->where_as("$this->tbl_as.nama",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("$this->tbl_as.deskripsi",addslashes($keyword),"OR","%like%",0,1);
		}
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",1);
	}
	public function countAll($nation_code, $keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join_composite($this->tbl2,$this->tbl2_as,$this->__join_on(),"");
		$this->db->where_as("$this->tbl_as.nation_code",$nation_code);
		if(strlen($keyword)>0){
			$this->db->where_as("$this->tbl_as.paymeny_method",addslashes($keyword),"OR","%like%",1,0);
			$this->db->where_as("$this->tbl_as.nama",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("$this->tbl_as.deskripsi",addslashes($keyword),"OR","%like%",0,1);
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
	public function get($nation_code){
		$this->db->select_as("$this->tbl_as.*, COALESCE($this->tbl2_as.bank_nama,'-') bank, COALESCE($this->tbl2_as.rekening_nomor,'-') nomor_rekening, COALESCE($this->tbl2_as.rekening_nama,'-')",'atas_nama',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join_composite($this->tbl2,$this->tbl2_as,$this->__join_on(),"");
		$this->db->where_as("$this->tbl_as.nation_code",$nation_code);
		return $this->db->get();
	}
}
