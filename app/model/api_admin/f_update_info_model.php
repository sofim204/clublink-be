<?php
class F_Update_Info_model extends SENE_Model{
	var $tbl = 'f_version_mobile';
	var $tbl_as = 'fvm';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function getAll($nation_code,$page=0,$pagesize=10,$sortCol="sku",$sortDir="",$keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate desc)", "no");
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl_as.device", "device", 0);
		$this->db->select_as("$this->tbl_as.version", "version", 0);
		$this->db->select_as("IF($this->tbl_as.status >1,'Major','Minor')",'status',0);
		$this->db->select_as("IF($this->tbl_as.is_active=1,'Active','Inactive')",'is_active',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		if(mb_strlen($keyword)>1){
			$this->db->where("id",$keyword,"OR","%like%",1,0);
			$this->db->where("device",$keyword,"OR","%like%",0,0);
			$this->db->where("version",$keyword,"OR","%like%",0,0);
			$this->db->where("cdate",$keyword,"OR","%like%",0,1);
    	}
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object", 0);
	}
	public function countAll($nation_code,$keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code,"AND","=",0,0);
		if(mb_strlen($keyword)>1){
  			$this->db->where("id",$keyword,"OR","%like%",1,0);
  			$this->db->where("device",$keyword,"OR","%like%",0,0);
        $this->db->where("version",$keyword,"OR","%like%",0,0);
        $this->db->where("cdate",$keyword,"OR","%like%",0,1);
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
	public function update($nation_code,$id,$du){
		if(!is_array($du)) return 0;
    $this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
    return $this->db->update($this->tbl,$du,0);
	}
	public function set($di){
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl,$di,0,0);
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
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}
}
