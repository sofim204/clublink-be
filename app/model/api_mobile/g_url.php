<?php
class G_Url extends SENE_Model {
	var $tbl = 'g_url';
	var $tbl_as = 'gu';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
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

    public function getListUrl($nation_code) {
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.url", "url", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
		$this->db->order_by("$this->tbl_as.id", "ASC"); // by Muhammad Sofi 21 January 2022 17:46 | add sort by priority
        return $this->db->get();
    }

    public function getListUrlActive($nation_code) {
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.url", "url", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
		$this->db->order_by("$this->tbl_as.id", "ASC"); // by Muhammad Sofi 21 January 2022 17:46 | add sort by priority
        return $this->db->get_first();
    }

}