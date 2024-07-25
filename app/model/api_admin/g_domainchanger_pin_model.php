<?php
class G_Domainchanger_Pin_Model extends JI_Model{
	var $tbl = 'g_url_pin';
	var $tbl_as = 'gup';

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
	public function getPin(){
		$this->db->select('*');
		return $this->db->get_first();
	}
}
