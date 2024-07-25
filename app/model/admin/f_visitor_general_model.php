<?php
class F_Visitor_General_Model extends JI_Model {
	public $is_cacheable;
    public $tbl = 'f_visitor';
    public $tbl_as = 'fv';

	public function __construct(){
		parent::__construct();
		$this->is_cacheable = 0;
    	$this->db->from($this->tbl,$this->tbl_as);
	}

    public function exportXlsVisitorTotal($nation_code, $keyword="", $cdate_start="", $cdate_end="", $mobile_type="") {
        $this->db->flushQuery();
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.mobile_type", "mobile_type", 0);
        $this->db->select_as("$this->tbl_as.total_visit", "total_visit", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);

		if(strlen($cdate_start)==10 && strlen($cdate_end)==10) {
			$this->db->between("DATE($this->tbl_as.cdate)","DATE('$cdate_start')","DATE('$cdate_end')");
		} else if(strlen($cdate_start)==10 && strlen($cdate_end)!=10){
			$this->db->where_as("DATE($this->tbl_as.cdate)","DATE('$cdate_start')",'AND','>=');
		} else if(strlen($cdate_start)!=10 && strlen($cdate_end)==10){
			$this->db->where_as("DATE($this->tbl_as.cdate)","DATE('$cdate_end')",'AND','<=');
		} else {}
        
		if(strlen($mobile_type) > 0) {
			$this->db->where_as("$this->tbl_as.mobile_type", $this->db->esc($mobile_type));
		} else {
			$this->db->where_in("$this->tbl_as.mobile_type", array("android", "ios"));
		}

        return $this->db->get('', 0);
    }
}