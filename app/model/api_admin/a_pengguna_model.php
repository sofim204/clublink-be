<?php
class A_Pengguna_Model extends SENE_Model{
	var $tbl = 'a_pengguna';
	var $tbl_as = 'ap';
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

	public function getAll($page=0,$pagesize=10,$sortCol="id",$sortDir="ASC",$keyword="",$is_active="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as('CONCAT(nation_code,"/",id)','id',0)
             ->select('foto')
             ->select('username')
             ->select('email')
						 ->select('nama')
						 ->select('user_role')
						 ->select('user_alias')
						 ->select('is_receive_email')
						 ->select('is_active')
    ;
		$this->db->from($this->tbl,$this->tbl_as);
		if(strlen($is_active)>0) $this->db->where_as("is_active",$this->db->esc($is_active));
		if(mb_strlen($keyword)>0){
			$this->db->where("username",$keyword,"OR","%like%",1,0);
			$this->db->where("email",$keyword,"OR","%like%",0,1);
		}
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}
	public function countAll($keyword="",$is_active="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		if(strlen($is_active)>0) $this->db->where_as("is_active",$this->db->esc($is_active));
		if(mb_strlen($keyword)>0){
			$this->db->where("username",$keyword,"OR","%like%",1,0);
			$this->db->where("email",$keyword,"OR","%like%",0,1);
		}
		$d = $this->db->from($this->tbl)->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
	public function getById($nation_code, $id){
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
	public function checkusername($nation_code,$username,$id=0){
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("username",$username);
		if(!empty($id)){
			if(!empty($nation_code)) $this->db->where("nation_code",$nation_code,'AND','==');
			$this->db->where("id",$id,'AND','!=');
		}
		$d = $this->db->from($this->tbl,$this->tbl_as)->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	//by Donny Dennison - 29 april 2021 14:06
    //add-void-and-refund-2c2p-after-reject-by-seller
	public function getEmailActive(){
		$this->db->select_as("$this->tbl_as.username, $this->tbl_as.nama, $this->tbl_as.email",'email',0);
		$this->db->where("is_receive_email",1);
		$this->db->where("is_active",1);
		return $this->db->get('',0);
	}

	public function getAdminName($nation_code, $keyword="", $is_active="") {
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.nama", "nama", 0);
		$this->db->select_as("$this->tbl_as.user_alias", "user_alias", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1'), "AND", "=", 0, 0);

        if (mb_strlen($keyword)>0) {
			$this->db->where("$this->tbl_as.nama", $keyword, "OR", "%like%", 1, 1);
        }

        return $this->db->get("object", 0);
	}
}
