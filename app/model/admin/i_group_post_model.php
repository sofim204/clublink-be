<?php
class I_Group_Post_Model extends SENE_Model{
	var $tbl = 'i_group_post';
	var $tbl_as = 'igp';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	
	public function getTableAlias(){
		return $this->tbl_as;
	}

    public function getById($nation_code, $id){
		$this->db->where("nation_code", $nation_code);
		$this->db->where("id", $id);
		return $this->db->get_first();
	}

    public function countTotalClubPost()
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }
}