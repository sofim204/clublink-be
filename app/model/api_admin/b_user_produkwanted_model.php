<?php
class B_User_Produkwanted_Model extends SENE_Model{
	var $tbl = 'b_user_productwanted';
	var $tbl_as = 'bup';
	var $tbl2 = 'b_user';
	var $tbl2_as = 'bu';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	private function __joinTbl2(){
    $cps = array();
    $cps[] = $this->db->composite_create("$this->tbl2_as.nation_code","=","$this->tbl_as.nation_code");
    $cps[] = $this->db->composite_create("$this->tbl2_as.id","=","$this->tbl_as.b_user_id");
    return $cps;
  }

	public function getTableAlias(){
		return $this->tbl_as;
	}
	public function getTableAlias2(){
		return $this->tbl2_as;
	}
	public function getAll($nation_code,$page=0,$pagesize=10,$sortCol="",$sortDir="ASC",$keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.id",'id',0);
		$this->db->select_as("$this->tbl_as.nation_code",'nation_code',0);
    $this->db->select_as("$this->tbl_as.b_user_id","b_user_id",0);
		$this->db->select_as("COALESCE($this->tbl2_as.fnama, '-')","user_nama",0);
		$this->db->select_as("$this->tbl_as.keyword_text",'keyword_text',0);
		$this->db->select_as("COALESCE($this->tbl2_as.email,'-')",'b_user_email',0);
		$this->db->select_as("COALESCE($this->tbl2_as.telp,'-')",'b_user_telp',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join_composite($this->tbl2,$this->tbl2_as,$this->__joinTbl2(),"left");
		$this->db->where_as("$this->tbl_as.nation_code",$nation_code,"AND","=",0,0);
		if(mb_strlen($keyword)>1){
			$this->db->where_as("$this->tbl_as.id",addslashes($keyword),"OR","%like%",1,0);
      $this->db->where_as("$this->tbl_as.b_user_id",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("COALESCE($this->tbl2_as.fnama,'-')",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("$this->tbl_as.keyword_text",addslashes($keyword),"OR","%like%",0,1);
    }
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}
	public function countAll($nation_code,$keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join_composite($this->tbl2,$this->tbl2_as,$this->__joinTbl2(),"left");
		$this->db->where_as("$this->tbl_as.nation_code",$nation_code,"AND","=",0,0);
		if(mb_strlen($keyword)>1){
			$this->db->where_as("$this->tbl_as.id",addslashes($keyword),"OR","%like%",1,0);
      $this->db->where_as("$this->tbl_as.b_user_id",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("COALESCE($this->tbl2_as.fnama,'-')",addslashes($keyword),"OR","%like%",0,0);
			$this->db->where_as("$this->tbl_as.keyword_text",addslashes($keyword),"OR","%like%",0,1);
		}
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
	public function getById($nation_code,$id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->get_first();
	}
	public function set($di){
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl,$di,0,0);
	}
	public function update($nation_code,$id,$du){
		if(!is_array($du)) return 0;
    $this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
    return $this->db->update($this->tbl,$du,0);
	}
	public function del($nation_code,$id){
    $this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
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
	public function getLastId($nation_code){
		$this->db->select_as("MAX($this->tbl_as.id)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}


}
