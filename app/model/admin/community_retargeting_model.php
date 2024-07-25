<?php
class Community_Retargeting_Model extends JI_Model
{
	var $tbl = 'c_community_event_re_targeting';
	var $tbl_as = 'ccert';

	public function __construct()
	{
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

    public function getAll($b_user_id)
	{
		$this->db->select_as("$this->tbl_as.cdate_day_1", "day_1", 0);
		$this->db->select_as("$this->tbl_as.cdate_day_2", "day_2", 0);
		$this->db->select_as("$this->tbl_as.cdate_day_3", "day_3", 0);
		$this->db->select_as("$this->tbl_as.cdate_day_4", "day_4", 0);
		$this->db->select_as("$this->tbl_as.cdate_day_5", "day_5", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id), "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
		return $this->db->get("", 0);
    }

	public function getDataFromOldUser($b_user_id)
	{
		$this->db->select_as('cdate_day_1', 'cdate_day_1', 0);
		$this->db->select_as('cdate_day_2', 'cdate_day_2', 0);
		$this->db->select_as('cdate_day_3', 'cdate_day_3', 0);
		$this->db->select_as('cdate_day_4', 'cdate_day_4', 0);
		$this->db->select_as('cdate_day_5', 'cdate_day_5', 0);
        $this->db->select_as('cdate_redeem_pulsa', 'cdate_redeem_pulsa', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id), "AND", "=", 0, 0);
        return $this->db->get_first('', 0);
	}
}