<?php
class C_Community_Event_Status_History_Model extends SENE_Model {
	var $tbl = 'c_community_event_status_history';
	var $tbl_as = 'ccesh';

	public function __construct() {
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	public function getTableAlias(){
		return $this->tbl_as;
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

	public function set($di) {
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl, $di, 0, 0);
	}

	public function update($nation_code, $id, $du) {
		if(!is_array($du)) return 0;
		$this->db->where("nation_code", $nation_code);
		$this->db->where("id", $id);
    return $this->db->update($this->tbl, $du, 0);
	}

	public function del($nation_code, $id){
		$this->db->where("nation_code", $nation_code);
		$this->db->where("id", $id);
		return $this->db->delete($this->tbl);
	}

	public function getNoteHistory($nation_code, $id, $type, $status)
    {
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl_as.status_redeem_pulsa", "status_redeem_pulsa", 0);
		$this->db->select_as("$this->tbl_as.note", "note", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where("custom_id", $id);
        $this->db->where("type", $type);
        $this->db->where("status_redeem_pulsa", $status);
		$this->db->order_by("$this->tbl_as.cdate", "DESC");
        return $this->db->get_first();
    }
    
}
