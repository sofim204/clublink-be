<?php
class H_Logs_model extends JI_Model{
	var $tbl = 'h_logs';
	var $tbl_as = 'hl';

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
	public function getData($name=""){
		$this->db->select('*');
        $this->db->where_as("$this->tbl_as.name", $this->db->esc($name));
		return $this->db->get_first();
	}
}
