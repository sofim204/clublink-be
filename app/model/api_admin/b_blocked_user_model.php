<?php
class B_Blocked_User_Model extends JI_Model {
	var $tbl = 'c_block';
	var $tbl_as = 'cb';
	var $tbl2 = 'b_user';
	var $tbl2_as = 'bu';
	var $tbl3 = 'b_user';
	var $tbl3_as = 'bu_blocked_user';

	public function __construct() {
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	private function __joinTbl2() {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl2_as.id");
        return $cps;
    }

	private function __joinTbl3() {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.custom_id", "=", "$this->tbl3_as.id");
        return $cps;
    }

	public function update($id, $du){
		if(!is_array($du)) return 0;
		$this->db->where("id", $id);
    	return $this->db->update($this->tbl, $du, 0);
	}

	public function set($di) {
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl, $di, 0, 0);
	}

	public function getLastId() {
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}

	public function del($id) {
		$this->db->where("id", $id);
		return $this->db->delete($this->tbl);
	}

	public function getAllBlockedUser($page=0, $pagesize=10, $sortCol="sku", $sortDir="ASC", $keyword="", $from_date="", $to_date="", $type="") {
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY $this->tbl_as.cdate desc)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		$this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "user_name");
        $this->db->select_as($this->__decrypt("$this->tbl2_as.email"), "user_email");
        $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "blocked_user_name");
        $this->db->select_as($this->__decrypt("$this->tbl3_as.email"), "blocked_user_email");
		$this->db->select_as("$this->tbl_as.cdate", "blocked_date", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->join_composite($this->tbl2, $this->tbl2_as,$this->__joinTbl2(),'inner');
		$this->db->join_composite($this->tbl3, $this->tbl3_as,$this->__joinTbl3(),'inner');

		// if (strlen($from_date)==10 && strlen($to_date)==10) {
		// 	$this->db->between("DATE($this->tbl_as.cdate)", "DATE('$from_date')", "DATE('$to_date')");
		// } else if (strlen($from_date)==10 && strlen($to_date)!=10) {
		// 	$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$from_date')", 'AND', '>=');
		// } else if (strlen($from_date)!=10 && strlen($to_date)==10) {
		// 	$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$to_date')", 'AND', '<=');
		// }
		
		if(strlen($type)>0) {
			$this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
		}

		if(mb_strlen($keyword)>0) {
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl3_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 1);
		}

		$this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
		return $this->db->get("object", 0);
	}

	public function countAllBlockedUser($keyword="", $from_date="", $to_date="", $type="") {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)", "jumlah", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->join_composite($this->tbl2, $this->tbl2_as,$this->__joinTbl2(),'inner');
		$this->db->join_composite($this->tbl3, $this->tbl3_as,$this->__joinTbl3(),'inner');

		// if (strlen($from_date)==10 && strlen($to_date)==10) {
		// 	$this->db->between("DATE($this->tbl_as.cdate)", "DATE('$from_date')", "DATE('$to_date')");
		// } else if (strlen($from_date)==10 && strlen($to_date)!=10) {
		// 	$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$from_date')", 'AND', '>=');
		// } else if (strlen($from_date)!=10 && strlen($to_date)==10) {
		// 	$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$to_date')", 'AND', '<=');
		// }
		
		if(strlen($type)>0) {
			$this->db->where_as("$this->tbl_as.type", $this->db->esc($type));
		}

		if(mb_strlen($keyword)>0) {
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl3_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 1);
		}
		
		$d = $this->db->get_first("object", 0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function getById($id){
		$this->db->where("id", $id);
		return $this->db->get_first();
	}

	public function m_delete_log($from_date="", $to_date="") {
		$sdate = 'DATE("'.$from_date.'")';
		$edate = 'DATE("'.$to_date.'")';
		$this->db->between("DATE($this->tbl.cdate)", $sdate, $edate);

		return $this->db->delete($this->tbl);
	}
}
