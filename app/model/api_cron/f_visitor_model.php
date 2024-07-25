<?php
class F_Visitor_Model extends JI_Model{
	var $tbl = 'f_visitor';
	var $tbl_as = 'fv';
	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	public function set($di)
    {
        return $this->db->insert($this->tbl, $di, 0, 0);
    }
    
    public function getLastId($nation_code)
    {
        $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $d = $this->db->get_first('', 0);
        if (isset($d->last_id)) {
            return $d->last_id;
        }
        return 0;
    }

    public function getLatestVisit($nation_code, $mobile_type)
    {
        $this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", 'id', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.mobile_type", $this->db->esc($mobile_type));
        $this->db->order_by("$this->tbl_as.cdate","desc");
        return $this->db->get_first("", 0);
    }

}
