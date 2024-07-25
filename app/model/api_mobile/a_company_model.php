<?php
class A_Company_Model extends SENE_Model{
	var $tbl = 'a_company';
	var $tbl_as = 'ac';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function getAlias(){
		return $this->tbl_as;
	}
	public function get($utype="",$is_active="1"){
		$this->db->select('id')
						 ->select('kode')
						 ->select('nama')
						 ->select('alamat')
						 ->select('telp')
						 ->select('latitude')
						 ->select('longitude');
						 
		if(strlen($utype)) $this->db->where('utype',$utype);
		if(strlen($is_active)) $this->db->where('is_active',$is_active);
		$this->db->order_by('id','asc');
		return $this->db->get('object',0);
	}
	public function getById($id,$is_active="1"){
		$this->db->select('id')
						 ->select('kode')
						 ->select('nama')
						 ->select('alamat')
						 ->select('telp')
						 ->select('latitude')
						 ->select('longitude');
						 
		$this->db->where('id',$id);
		if(strlen($is_active)) $this->db->where('is_active',$is_active);
		return $this->db->get_first();
	}
}