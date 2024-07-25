<?php
class D_Pesan_File_Model extends SENE_Model {
	var $tbl = 'd_pesan_file';
	var $tbl_as = 'dpfm';
  public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
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
	public function getByPesanId($d_pesan_id){
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where('d_pesan_id',$d_pesan_id);
		return $this->db->get();
	}
	public function getByPesanIds($d_pesan_ids=array()){
		$this->db->select_as("$this->tbl_as.*, CONCAT('".base_url()."',$this->tbl_as.url)",'url',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where_in('d_pesan_id',$d_pesan_ids);
		return $this->db->get();
	}
}

