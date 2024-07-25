<?php
class A_Bank_TrfCost_Model extends SENE_Model{
	var $tbl = 'a_bank_trfcost';
	var $tbl_as = 'abtc';
	var $tbl2 = 'a_bank';
	var $tbl2_as = 'ab1';
	var $tbl3 = 'a_bank';
	var $tbl3_as = 'ab2';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	private function __joinTbl2(){
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl2_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl_as.a_bank_id_from","=","$this->tbl2_as.id");
		return $cps;
	}
	private function __joinTbl3(){
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl_as.nation_code","=","$this->tbl3_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl_as.a_bank_id_to","=","$this->tbl3_as.id");
		return $cps;
	}

  public function getTableAlias(){
    return $this->tbl_as;
  }
  public function getTableAlias2(){
    return $this->tbl2_as;
  }
  public function getTableAlias3(){
    return $this->tbl3_as;
  }

	public function getAll($nation_code,$page=0,$pagesize=10,$sortCol="id",$sortDir="ASC",$keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("CONCAT($this->tbl2_as.id,'/',$this->tbl3_as.id)",'id');
		$this->db->select_as("$this->tbl2_as.nama",'dari',0);
		$this->db->select_as("$this->tbl3_as.nama",'ke',0);
		$this->db->select_as("$this->tbl_as.utype",'utype',0);
		$this->db->select_as("$this->tbl_as.cost",'cost',0);
		$this->db->select_as("$this->tbl_as.is_active",'is_active',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join_composite($this->tbl2,$this->tbl2_as,$this->__joinTbl2(),'inner');
		$this->db->join_composite($this->tbl3,$this->tbl3_as,$this->__joinTbl3(),'inner');
		$this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code),"AND","=",0,0);
		if(strlen($keyword)>0){
			$this->db->where_as("$this->tbl2_as.nama",addslashes($keyword),"OR","%like%",1,0);
  		$this->db->where_as("$this->tbl3_as.nama",addslashes($keyword),"OR","%like%",0,1);
    }
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}
	public function countAll($nation_code,$keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join_composite($this->tbl2,$this->tbl2_as,$this->__joinTbl2(),'inner');
		$this->db->join_composite($this->tbl3,$this->tbl3_as,$this->__joinTbl3(),'inner');
		$this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code),"AND","=",0,0);
		if(strlen($keyword)>0){
			$this->db->where_as("$this->tbl2_as.nama",addslashes($keyword),"OR","%like%",1,0);
  		$this->db->where_as("$this->tbl3_as.nama",addslashes($keyword),"OR","%like%",0,1);
		}
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
	public function getById($nation_code,$a_bank_id_from,$a_bank_id_to){
    $this->db->select_as("$this->tbl_as.a_bank_id_from",'a_bank_id_from',0);
    $this->db->select_as("$this->tbl_as.a_bank_id_to",'a_bank_id_to',0);
    $this->db->select_as("$this->tbl2_as.nama",'a_bank_nama_from',0);
    $this->db->select_as("$this->tbl3_as.nama",'a_bank_nama_to',0);
    $this->db->select_as("$this->tbl_as.utype",'utype',0);
    $this->db->select_as("$this->tbl_as.cost",'cost',0);
    $this->db->select_as("$this->tbl_as.is_active",'is_active',0);
    $this->db->from($this->tbl,$this->tbl_as);
		$this->db->join_composite($this->tbl2,$this->tbl2_as,$this->__joinTbl2(),'inner');
		$this->db->join_composite($this->tbl3,$this->tbl3_as,$this->__joinTbl3(),'inner');
    $this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code),"AND","=",0,0);
		$this->db->where("a_bank_id_from",$a_bank_id_from);
		$this->db->where("a_bank_id_to",$a_bank_id_to);
		return $this->db->get_first();
	}
	public function set($di){
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl,$di,0,0);
	}
	public function update($nation_code,$a_bank_id_from,$a_bank_id_to,$du){
		if(!is_array($du)) return 0;
    $this->db->where("nation_code",$nation_code);
		$this->db->where("a_bank_id_from",$a_bank_id_from);
		$this->db->where("a_bank_id_to",$a_bank_id_to);
    return $this->db->update($this->tbl,$du,0);
	}
	public function del($nation_code,$a_bank_id_from,$a_bank_id_to){
    $this->db->where("nation_code",$nation_code);
		$this->db->where("a_bank_id_from",$a_bank_id_from);
		$this->db->where("a_bank_id_to",$a_bank_id_to);
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
}
