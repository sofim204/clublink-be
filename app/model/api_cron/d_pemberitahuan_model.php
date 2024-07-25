<?php
//api_cron
class D_Pemberitahuan_Model extends JI_Model{
	var $tbl = 'd_pemberitahuan';
	var $tbl_as = 'dpem';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	
	public function getLastId($nation_code,$b_user_id){
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("b_user_id",$b_user_id);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}

	public function get($nation_code,$b_user_id){
		$this->db->select_as("$this->tbl_as.nation_code","nation_code",0);
		$this->db->select_as("$this->tbl_as.b_user_id","b_user_id",0);
		$this->db->select_as("$this->tbl_as.id","id",0);
		$this->db->select_as("$this->tbl_as.judul","judul",0);
		$this->db->select_as("$this->tbl_as.teks","teks",0);
		$this->db->select_as("$this->tbl_as.type","type",0);
		$this->db->select_as("COALESCE($this->tbl_as.gambar,'')","gambar",0);
		$this->db->select_as("COALESCE($this->tbl_as.extras,'{}')","extras",0);
		$this->db->select_as("$this->tbl_as.cdate","cdate",0);
		$this->db->select_as("$this->tbl_as.is_read","is_read",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("b_user_id",$b_user_id);
		$this->db->order_by("id","desc");
		return $this->db->get();
	}
	public function getAll($nation_code,$b_user_id,$page=0,$pagesize=10,$sortCol="kode",$sortDir="ASC",$keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.nation_code","nation_code",0);
		$this->db->select_as("$this->tbl_as.b_user_id","b_user_id",0);
		$this->db->select_as("$this->tbl_as.id","id",0);
		$this->db->select_as("$this->tbl_as.judul","judul",0);
		$this->db->select_as("$this->tbl_as.teks","teks",0);
		$this->db->select_as("$this->tbl_as.type","type",0);
		$this->db->select_as("COALESCE($this->tbl_as.gambar,'')","gambar",0);
		$this->db->select_as("COALESCE($this->tbl_as.extras,'{}')","extras",0);
		$this->db->select_as("$this->tbl_as.cdate","cdate",0);
		$this->db->select_as("$this->tbl_as.is_read","is_read",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("b_user_id",$b_user_id);
		if(strlen($keyword)>1){
			$this->db->where("judul",$keyword,"OR","%like%",1,0);
			$this->db->where("teks",$keyword,"OR","%like%",0,0);
			$this->db->where("type",$keyword,"OR","%like%",0,1);
		}
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}
	public function countAll($nation_code,$b_user_id,$keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","total",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("b_user_id",$b_user_id);
		if(strlen($keyword)>1){
			$this->db->where("judul",$keyword,"OR","%like%",1,0);
			$this->db->where("teks",$keyword,"OR","%like%",0,0);
			$this->db->where("type",$keyword,"OR","%like%",0,1);
		}
		$d = $this->db->get_first("object",0);
		if(isset($d->total)) return $d->total;
		return 0;
	}
	public function getById($nation_code,$b_user_id,$id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("b_user_id",$b_user_id);
		$this->db->where("id",$id);
		return $this->db->get_first();
	}
	public function set($di){
		if(!is_array($di)) return 0;
		if(!isset($di['is_read'])) $di['is_read'] = 0;
		$this->db->insert($this->tbl,$di,0,0);
		return $this->db->last_id;
	}
	public function update($nation_code,$b_user_id,$id,$du){
		if(!is_array($du)) return 0;
		$this->db->where("nation_code",$nation_code);
		$this->db->where("b_user_id",$b_user_id);
		$this->db->where("id",$id);
    return $this->db->update($this->tbl,$du,0);
	}
	public function del($nation_code,$b_user_id,$id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("b_user_id",$b_user_id);
		$this->db->where("id",$id);
		return $this->db->delete($this->tbl);
	}
	public function getUnRead($nation_code,$b_user_id){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
		$this->db->where_as("$this->tbl_as.b_user_id",$this->db->esc($b_user_id));
		$this->db->where_as("$this->tbl_as.is_read",$this->db->esc("0"));
		return $this->db->get();
	}
	public function countUnRead($nation_code,$b_user_id){
		$this->db->select_as("COUNT(*)","total",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code",$this->db->esc($nation_code));
		$this->db->where_as("$this->tbl_as.b_user_id",$this->db->esc($b_user_id));
		$this->db->where_as("$this->tbl_as.is_read",$this->db->esc("0"));
		$d = $this->db->get_first();
		if(isset($d->total)) return $d->total;
		return 0;
	}
	public function updateUnRead($nation_code,$b_user_id){
		$du=array("is_read"=>1);
		$this->db->where_as("nation_code",$this->db->esc($nation_code));
		$this->db->where_as("b_user_id",$this->db->esc($b_user_id));
		return $this->db->update($this->tbl,$du);
	}
	public function setAsRead($nation_code,$b_user_id){
		$du=array("is_read"=>1);
		$this->db->where_as("nation_code",$this->db->esc($nation_code));
		$this->db->where_as("b_user_id",$this->db->esc($b_user_id));
		return $this->db->update($this->tbl,$du);
	}
	public function delByUserId($nation_code,$b_user_id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("b_user_id",$b_user_id);
		return $this->db->delete($this->tbl);
	}

    //START by Donny Dennison 24 december 2022 23:30
    //only keep one month data in bell notification
	public function delKeepOneMonth($nation_code){
		$this->db->where("nation_code",$nation_code);
		$this->db->where_as("DATE(cdate)", $this->db->esc(date("Y-m-d", strtotime("-1 months"))), "AND", "<");
		return $this->db->delete($this->tbl);
	}
    //END by Donny Dennison 24 december 2022 23:30
    //only keep one month data in bell notification

}
