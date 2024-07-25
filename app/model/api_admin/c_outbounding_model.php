<?php
class C_Outbounding_Model extends JI_Model{
	var $tbl = 'c_outbounding';
	var $tbl_as = 'co';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	public function getLastId($nation_code, $param){
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("judul",$param);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}

	public function set($sum){
		if(!is_array($sum)) return 0;
		return $this->db->insert($this->tbl,$sum,0,0);
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

	public function getAll($nation_code,$page=0,$pagesize=10,$sortCol="kode",$sortDir="ASC",$keyword="",$is_active="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate DESC)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl_as.judul", "judul", 0);
        $this->db->select_as("$this->tbl_as.teks", "teks", 0);
        $this->db->select_as("$this->tbl_as.total_clicked", "total_clicked", 0);
        $this->db->select_as("$this->tbl_as.is_active", "active", 0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code,"AND","=",0,0);
		if(strlen($is_active)) $this->db->where_as("$this->tbl_as.is_active",$this->db->esc($is_active));
		if(strlen($keyword)>0){
			$this->db->where("judul",$keyword,"OR","%like%",1,0);
			$this->db->where("teks",$keyword,"OR","%like%",0,1);
		}
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get('',0);
	}
	public function countAll($nation_code,$keyword="",$is_active="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code,"AND","=",0,0);
		if(strlen($is_active)) $this->db->where_as("$this->tbl_as.is_active",$this->db->esc($is_active));
		if(strlen($keyword)>0){
			$this->db->where("judul",$keyword,"OR","%like%",1,0);
			$this->db->where("teks",$keyword,"OR","%like%",0,1);
		}
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
	public function getById($nation_code, $id){
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.judul", "judul", 0);
        $this->db->select_as("$this->tbl_as.teks", "teks", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.is_active", "active", 0);
        $this->db->select_as("$this->tbl_as.is_notif", "notif", 0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->get_first();
	}

	public function getIsActive($nation_code, $id, $param)
	{
		$this->db->select_as("$this->tbl_as.is_active", "active", 0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		$this->db->where("judul",$param);
		return $this->db->get_first();
	}

	public function getByIsNotif($is_notif=0)
	{
		$this->db->select_as("*,$this->tbl_as.id", "id", 0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where_as("$this->tbl_as.is_active",$this->db->esc(1));
		$this->db->where_as("$this->tbl_as.is_notif",$this->db->esc($is_notif));
		return $this->db->get();
	}
}
