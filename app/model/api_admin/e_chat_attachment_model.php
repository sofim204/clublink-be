<?php
class E_Chat_Attachment_Model extends SENE_Model{
	var $tbl = 'e_chat_attachment';
	var $tbl_as = 'eca';
	var $tbl2 = 'e_chat';
	var $tbl2_as = 'ec';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	public function getLastId($nation_code,$d_order_id,$c_produk_id,$e_chat_id){
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("d_order_id",$d_order_id);
		$this->db->where("c_produk_id",$c_produk_id);
		$this->db->where("e_chat_id",$e_chat_id);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
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
}
