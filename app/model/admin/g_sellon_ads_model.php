<?php
class G_Sellon_Ads_Model extends JI_Model{
	var $tbl = 'c_sellon_ads';
	var $tbl_as = 'csa';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	public function getTableAlias(){
		return $this->tbl_as;
	}

	public function detail($id, $nation_code) {
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.url", "url", 0);
		$this->db->select_as("$this->tbl_as.teks", "teks", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
		return $this->db->get_first();
    }
}
