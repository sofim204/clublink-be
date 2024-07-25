<?php
class Community_Image_Model extends SENE_Model{
	var $tbl = 'c_community_attachment';
	var $tbl_as = 'comm_attachment';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	public function getTableAlias(){
		return $this->tbl_as;
	}
	public function getByCommunityId($nation_code, $id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("c_community_id",$id);
		return $this->db->get();
	}
	public function setMass($payload){
		$this->db->insert_multi($this->tbl, $payload);
	}
	
	public function getVideoByCommunityId($nation_code, $c_community_id){
		$this->db->select_as("SUBSTRING_INDEX($this->tbl_as.url, '.', -1)","url_ext", 0);
		$this->db->select_as("$this->tbl_as.url","url",0);
		$this->db->select_as("$this->tbl_as.jenis","jenis",0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.c_community_id", $this->db->esc($c_community_id), "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.jenis", $this->db->esc("video"), "AND", "=", 0, 0);
		$this->db->order_by("url_ext");
		return $this->db->get();
	}

	public function getTotalUploadVideo($nation_code, $c_community_id, $operator){
		$this->db->flushQuery();
        $this->db->select_as("COUNT(*)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.c_community_id", $this->db->esc($c_community_id), "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.jenis", $this->db->esc("video"), "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.url", ".png", "AND", "%$operator%", 0, 0);
        $d = $this->db->get_first("object", 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
	}
}
