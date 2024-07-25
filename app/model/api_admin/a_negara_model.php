<?php
class A_Negara_Model extends SENE_Model{
	var $tbl = 'a_negara';
	var $tbl_as = 'an';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function getAll($page=0,$pagesize=10,$sortCol="kode",$sortDir="ASC",$keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select('id');
		$this->db->select('kode');
		$this->db->select('nama');
		$this->db->select('harga');
		//$this->db->select_as('CONCAT(harga_dasar,"/",qty_dasar," ",satuan_dasar)','harga',0);
		$this->db->select('harga_rp');
    $this->db->select('kurir_default');
		$this->db->from($this->tbl,$this->tbl_as);
		if(mb_strlen($keyword)>1){
			//$this->db->where("utype",$keyword,"OR","%like%",1,0);
			$this->db->where("kode",$keyword,"OR","%like%",1,0);
			$this->db->where("nama",$keyword,"OR","%like%",0,1);
			//$this->db->where("deskripsi",$keyword,"OR","%like%",0,1);
		}
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}
	public function countAll($keyword="",$sdate="",$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		if(mb_strlen($keyword)>1){
			$this->db->where("kode",$keyword,"OR","%like%",1,0);
			$this->db->where("nama",$keyword,"OR","%like%",0,1);
			//$this->db->where("nama",$keyword,"OR","%like%",0,0);
			//$this->db->where("deskripsi",$keyword,"OR","%like%",0,1);
		}
		$d = $this->db->from($this->tbl)->get_first("object",0);
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
	public function checkKode($id){
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->where("id",$id);
		$d = $this->db->from($this->tbl)->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
	public function select2(){
		$this->db->select("id");
		$this->db->select("nama");
		$this->db->from($this->tbl,$this->tbl_as);
		return $this->db->get("object",0);
	}
	public function getByNationCode($nation_code){
		$this->db->where("nation_code",$nation_code);
		return $this->db->get_first();
	}
}
