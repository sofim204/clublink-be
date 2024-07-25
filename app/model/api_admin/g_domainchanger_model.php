<?php
class G_Domainchanger_Model extends JI_Model{
	var $tbl = 'g_url';
	var $tbl_as = 'gu';

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

	public function getAll($page=0,$pagesize=10,$sortCol="id",$sortDir="ASC",$keyword="",$is_active=""){
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.url", "url", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->from($this->tbl,$this->tbl_as);
		// if(strlen($is_active)>0) $this->db->where_as("is_active",$this->db->esc($is_active));
		if(mb_strlen($keyword)>0){
			$keyword = mb_strtolower($keyword);
			$this->db->where_as("$this->tbl_as.url",$keyword,"OR","%like%",1,0);
		}
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}
	public function countAll($keyword="",$is_active="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		// if(strlen($is_active)>0) $this->db->where_as("is_active",$this->db->esc($is_active));
		if(mb_strlen($keyword)>0){
			$keyword = mb_strtolower($keyword);
			$this->db->where_as("$this->tbl_as.url",$keyword,"OR","%like%",1,0);
		}
		$this->db->from($this->tbl,$this->tbl_as);
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
	public function getById($nation_code, $id){
		$this->db->select('*');
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->get_first();
	}
	public function set($di){
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl,$di,0,0);
	}
	public function update($nation_code, $id,$du){
		if(!is_array($du)) return 0;
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
    	return $this->db->update($this->tbl,$du,0);
	}
	public function del($nation_code, $id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->delete($this->tbl);
	}
	public function checkurl($nation_code,$url){
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->where("nation_code",$nation_code);
		$this->db->where_as("$this->tbl_as.url",$this->db->esc($url));
		$d = $this->db->from($this->tbl,$this->tbl_as)->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
	public function changeToInactive($nation_code) {
		return $this->db->exec("UPDATE `$this->tbl` SET is_active = 0
            WHERE nation_code = '$nation_code' AND is_active = 1");
	}
}
