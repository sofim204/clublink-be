<?php
class C_Community_Hashtag_History_Model extends SENE_Model {
	var $tbl = 'c_community_hashtag_history';
	var $tbl_as = 'cchh';
	var $tbl3 = 'b_user';
	var $tbl3_as = 'bu';

	public function __construct() {
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	public function getTableAlias(){
		return $this->tbl_as;
	}

	public function getTableAlias3(){
		return $this->tbl3_as;
	}

    private function __joinTbl3()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl3_as.id");
        return $cps;
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

	public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="cdate", $sortDir="", $keyword="", $fromDate="", $toDate="", $type_list="", $statusFilter="", $status_in_table=array()) {
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY jumlah desc)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
        $this->db->select_as("$this->tbl_as.hashtag", "hashtag", 0);
        $this->db->select_as("COUNT(*)","jumlah",0);		
		$this->db->from($this->tbl, $this->tbl_as);
        // $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");

		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);

        // START by Yopie Hidayat 26 July 2022 17:29 | change filter start date to end date
		if (strlen($fromDate)==10 && strlen($toDate)==10) {
			$this->db->between("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", "DATE('$toDate')");
		} else if (strlen($fromDate)==10 && strlen($toDate)!=10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", 'AND', '>=');
		} else if (strlen($fromDate)!=10 && strlen($toDate)==10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$toDate')", 'AND', '<=');
		}
		// END by Yopie Hidayat 26 July 2022 17:29 | change filter start date to end date        		

		if(mb_strlen($keyword)>0) {
            $this->db->where_as("$this->tbl_as.hashtag", addslashes($keyword), "OR", "%like%", 0, 0);
		}

        
		// $this->db->order_by("$this->tbl_as.prioritas", "ASC")->limit($page,$pagesize);
        $this->db->group_by("$this->tbl_as.hashtag");
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object", 0);
	}

	public function countAll($nation_code, $keyword="", $fromDate="", $toDate="", $type_list="", $statusFilter="", $status_in_table=array()) {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(DISTINCT hashtag)","jumlah",0);
		$this->db->from($this->tbl, $this->tbl_as);
        // $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        
		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);

        // START by Yopie Hidayat 26 July 2022 17:29 | change filter start date to end date
		if (strlen($fromDate)==10 && strlen($toDate)==10) {
			$this->db->between("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", "DATE('$toDate')");
		} else if (strlen($fromDate)==10 && strlen($toDate)!=10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", 'AND', '>=');
		} else if (strlen($fromDate)!=10 && strlen($toDate)==10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$toDate')", 'AND', '<=');
		}
		// END by Yopie Hidayat 26 July 2022 17:29 | change filter start date to end date

		if(mb_strlen($keyword)>0) {
			$this->db->where_as("$this->tbl_as.hashtag", addslashes($keyword), "OR", "%like%", 0, 0);
		}

		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function set($di) {
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl, $di, 0, 0);
	}

	public function update($nation_code, $id, $du) {
		if(!is_array($du)) return 0;
		$this->db->where("nation_code", $nation_code);
		$this->db->where("id", $id);
    return $this->db->update($this->tbl, $du, 0);
	}

	public function del($nation_code, $id){
		$this->db->where("nation_code", $nation_code);
		$this->db->where("id", $id);
		return $this->db->delete($this->tbl);
	}
    
}
