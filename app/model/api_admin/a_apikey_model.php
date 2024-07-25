<?php
class A_ApiKey_Model extends JI_Model{
	var $tbl = 'a_apikey';
	var $tbl_as = 'aa';

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
		$this->db->select_as('CONCAT(nation_code,"/",id)','id',0)
						 ->select_as($this->__decrypt("$this->tbl_as.username"), "username", 0)
             ->select_as($this->__decrypt("$this->tbl_as.password"), "password", 0)
						 ->select('is_active')
    ;
		$this->db->from($this->tbl,$this->tbl_as);
		if(strlen($is_active)>0) $this->db->where_as("is_active",$this->db->esc($is_active));
		if(mb_strlen($keyword)>0){
			$keyword = mb_strtolower($keyword);
			$this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_as.username").' USING utf8)',addslashes($keyword),"OR","%like%",1,0);
			$this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_as.password").' USING utf8)',addslashes($keyword),"OR","%like%",0,1);
		}
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}
	public function countAll($keyword="",$is_active="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		if(strlen($is_active)>0) $this->db->where_as("is_active",$this->db->esc($is_active));
		if(mb_strlen($keyword)>0){
			$keyword = mb_strtolower($keyword);
			$this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_as.username").' USING utf8)',addslashes($keyword),"OR","%like%",1,0);
			$this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_as.password").' USING utf8)',addslashes($keyword),"OR","%like%",0,1);
		}
		$this->db->from($this->tbl,$this->tbl_as);
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
	public function getById($nation_code, $id){
		$this->db->select('nation_code')
						 ->select('id')
						 ->select_as($this->__decrypt("$this->tbl_as.str"), "str", 0)
						 ->select_as($this->__decrypt("$this->tbl_as.code"), "code", 0)
						 ->select_as($this->__decrypt("$this->tbl_as.username"), "username", 0)
             ->select_as($this->__decrypt("$this->tbl_as.password"), "password", 0)
						 ->select('is_active')
						 ;
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->get_first();
	}
	public function set($di){
		if(!is_array($di)) return 0;
		if (isset($di['username'])) {
				if (mb_strlen($di['username'])) {
						$di['username'] = $this->__encrypt($di['username']);
				}
		}
		if (isset($di['password'])) {
				if (mb_strlen($di['password'])) {
						$di['password'] = $this->__encrypt($di['password']);
				}
		}
		if (isset($di['code'])) {
				if (mb_strlen($di['code'])) {
						$di['code'] = $this->__encrypt($di['code']);
				}
		}
		if (isset($di['str'])) {
				if (mb_strlen($di['str'])) {
						$di['str'] = $this->__encrypt($di['str']);
				}
		}
		return $this->db->insert($this->tbl,$di,0,0);
	}
	public function update($nation_code, $id,$du){
		if(!is_array($du)) return 0;
		if (isset($du['username'])) {
				if (mb_strlen($du['username'])) {
						$du['username'] = $this->__encrypt($du['username']);
				}
		}
		if (isset($du['password'])) {
				if (mb_strlen($du['password'])) {
						$du['password'] = $this->__encrypt($du['password']);
				}
		}
		if (isset($du['code'])) {
				if (mb_strlen($du['code'])) {
						$du['code'] = $this->__encrypt($du['code']);
				}
		}
		if (isset($du['str'])) {
				if (mb_strlen($du['str'])) {
						$du['str'] = $this->__encrypt($du['str']);
				}
		}
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
    return $this->db->update($this->tbl,$du,0);
	}
	public function del($nation_code, $id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->delete($this->tbl);
	}
	public function checkusername($nation_code,$username,$id=0){
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->where("nation_code",$nation_code);
		$this->db->where_as($this->__decrypt("$this->tbl_as.username"),$this->db->esc($username));
		if(!empty($id)){
			if(!empty($nation_code)) $this->db->where("nation_code",$nation_code,'AND','==');
			$this->db->where("id",$id,'AND','!=');
		}
		$d = $this->db->from($this->tbl,$this->tbl_as)->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
}
