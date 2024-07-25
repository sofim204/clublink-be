<?php
class Community_Discussion_Image_Model extends SENE_Model{
	var $tbl = 'c_community_discussion_attachment';
	var $tbl_as = 'disscuss_attachment';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function getTableAlias(){
		return $this->tbl_as;
	}
	public function getByDiscussionId($nation_code, $id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("c_community_discussion_id",$id);
		return $this->db->get();
	}
	public function setMass($payload){
		$this->db->insert_multi($this->tbl, $payload);
	}
}
