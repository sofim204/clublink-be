<?php
class F_Visitor_Model extends JI_Model {
	public $is_cacheable;
    public $tbl = 'f_visitor';
    public $tbl_as = 'fv';
    public $tbl2 = 'f_visitor_history';
    public $tbl2_as = 'fvh';

	public function __construct(){
		parent::__construct();
		$this->is_cacheable = 0;
    	$this->db->from($this->tbl,$this->tbl_as);
	}

    public function exportXlsVisitorTotal($nation_code, $keyword="", $cdate_start="", $cdate_end="", $mobile_type="") {
        $this->db->select_as("DATE($this->tbl2_as.cdate)", "cdate", 0);
        $this->db->select_as("$this->tbl2_as.mobile_type", "mobile_type", 0);
        $this->db->select_as("IF(DATE($this->tbl2_as.cdate) <= '2022-10-19', (COUNT(DISTINCT CONCAT(cdate, '-', b_user_id, '-', mobile_type))), (SELECT COUNT(*) FROM f_visitor_count WHERE DATE(cdate) = DATE($this->tbl2_as.cdate) AND mobile_type = $this->tbl2_as.mobile_type))", "visitor_count_from_ud_id", 0);
        $this->db->select_as("COUNT(DISTINCT CONCAT(DATE(cdate), '-', udid, '-', mobile_type))", "visitor_count", 0);
        $this->db->select_as("IF(DATE($this->tbl2_as.cdate) <= '2022-10-19', (SELECT COUNT(*) FROM f_visitor_count WHERE DATE(cdate) = DATE($this->tbl2_as.cdate) AND mobile_type = $this->tbl2_as.mobile_type), (COUNT(DISTINCT CONCAT(cdate, '-', b_user_id, '-', mobile_type))))", "total_visit", 0);
        $this->db->from($this->tbl2, $this->tbl2_as);
        // $this->db->where_as("$this->tbl2_as.nation_code", $nation_code, "AND", "=", 0, 0);

		if(strlen($cdate_start)==10 && strlen($cdate_end)==10) {
			$this->db->between("DATE($this->tbl2_as.cdate)","DATE('$cdate_start')","DATE('$cdate_end')");
		} else if(strlen($cdate_start)==10 && strlen($cdate_end)!=10){
			$this->db->where_as("DATE($this->tbl2_as.cdate)","DATE('$cdate_start')",'AND','>=');
		} else if(strlen($cdate_start)!=10 && strlen($cdate_end)==10){
			$this->db->where_as("DATE($this->tbl2_as.cdate)","DATE('$cdate_end')",'AND','<=');
		} else {}
        
		if(strlen($mobile_type) > 0) {
			$this->db->where_as("$this->tbl2_as.mobile_type", $this->db->esc($mobile_type));
		} else {
			$this->db->where_in("$this->tbl2_as.mobile_type", array("android", "ios"));
		}

        $this->db->group_by("CONCAT(DATE($this->tbl2_as.cdate), '-',$this->tbl2_as.mobile_type)");

        $this->db->order_by("DATE($this->tbl2_as.cdate)", "desc");
        return $this->db->get('', 0);
    }
}
