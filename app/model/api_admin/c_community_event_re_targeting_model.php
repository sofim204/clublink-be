<?php
class C_Community_Event_Re_Targeting_Model extends SENE_Model {
	var $tbl = 'c_community_event_re_targeting';
	var $tbl_as = 'ccert';
    var $tbl2 = 'b_user';
	var $tbl2_as = 'bu';
    var $tbl3 = 'c_community';
	var $tbl3_as = 'cc1';
    var $tbl4 = 'c_community';
	var $tbl4_as = 'cc2';
    var $tbl5 = 'c_community';
	var $tbl5_as = 'cc3';
	

	public function __construct() {
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	public function getTableAlias(){
		return $this->tbl_as;
	}

    public function getTableAlias2(){
		return $this->tbl2_as;
	}

	public function getTableAlias3(){
		return $this->tbl3_as;
	}

    private function __joinTbl2()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl2_as.id");
        return $cps;
    }

    private function __joinTbl3()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.c_community_id_day_1", "=", "$this->tbl3_as.id");
        return $cps;
    }

    private function __joinTbl4()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.c_community_id_day_2", "=", "$this->tbl4_as.id");
        return $cps;
    }

    private function __joinTbl5()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl5_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.c_community_id_day_3", "=", "$this->tbl5_as.id");
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

	// public function getLastId($nation_code){
	// 	$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
	// 	$this->db->from($this->tbl, $this->tbl_as);
	// 	// $this->db->where("nation_code",$nation_code);
	// 	$this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
	// 	$d = $this->db->get_first('', 0);
	// 	if(isset($d->last_id)) return $d->last_id;
	// 	return 0;
	// }

	public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="cdate", $sortDir="", $keyword="", $fromDate="", $toDate="", $statusFilter="", $status_in_table=array()) {
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate DESC)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		$this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "user", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.telp"), "telp", 0);
        $this->db->select_as("$this->tbl_as.verif_telp_manual", "verif_telp_manual", 0);
        $this->db->select_as("$this->tbl3_as.id", "day1", 0);
        $this->db->select_as("$this->tbl4_as.id", "day2", 0);
        $this->db->select_as("$this->tbl5_as.id", "day3", 0);        
        $this->db->select_as("$this->tbl_as.task_day_4", "day4", 0);     
        $this->db->select_as("$this->tbl_as.task_day_5", "day5", 0);     
        $this->db->select_as("$this->tbl_as.status_redeem_pulsa", "status_redeem_pulsa", 0);
        $this->db->select_as("$this->tbl_as.cdate_redeem_pulsa", "cdate_redeem_pulsa", 0);
        $this->db->select_as("$this->tbl_as.cdate_day_1", "cdate_day_1", 0);
        $this->db->select_as("$this->tbl_as.cdate_day_2", "cdate_day_2", 0);
        $this->db->select_as("$this->tbl_as.cdate_day_3", "cdate_day_3", 0);
        $this->db->select_as("$this->tbl_as.cdate_day_4", "cdate_day_4", 0);
        $this->db->select_as("$this->tbl_as.cdate_day_5", "cdate_day_5", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp_event", 0);
		// $this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");

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

        if ($statusFilter == "1") {
            $this->db->where("$this->tbl_as.status_redeem_pulsa","pending","AND","=",1,1);
        }else if ($statusFilter == "2") {
            $this->db->where("$this->tbl_as.status_redeem_pulsa","accepted","AND","=",1,1);
        }else if ($statusFilter == "3") {				
            $this->db->where("$this->tbl_as.status_redeem_pulsa","rejected","AND","=",1,1);
        }else if ($statusFilter == "4") {
            $this->db->where("$this->tbl_as.status_redeem_pulsa","finished","AND","=",1,1);
        }else{
            if (count($status_in_table) > 0) {
                if(is_array($status_in_table)) if(count($status_in_table)) $this->db->where_in("$this->tbl_as.status_redeem_pulsa",$status_in_table);
            }
        }       		

		if(mb_strlen($keyword)>0) {
            // $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 0);
			// $this->db->where_as($this->__decrypt("$this->tbl3_as.email"), addslashes($keyword), "OR", "%like%", 0, 0);
			// $this->db->where_as($this->__decrypt("$this->tbl_as.telp"), addslashes($keyword), "OR", "%like%", 0, 1);

            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 1);
		}

        
		// $this->db->order_by("$this->tbl_as.prioritas", "ASC")->limit($page,$pagesize);
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object", 0);
	}

	public function countAll($nation_code, $keyword="", $fromDate="", $toDate="", $statusFilter="", $status_in_table=array()) {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl, $this->tbl_as);
        $this->db->from($this->tbl,$this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
        
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
		
		if ($statusFilter == "1") {
            $this->db->where("$this->tbl_as.status_redeem_pulsa","pending","AND","=",1,1);
        }else if ($statusFilter == "2") {
            $this->db->where("$this->tbl_as.status_redeem_pulsa","accepted","AND","=",1,1);
        }else if ($statusFilter == "3") {				
            $this->db->where("$this->tbl_as.status_redeem_pulsa","rejected","AND","=",1,1);
        }else if ($statusFilter == "4") {
            $this->db->where("$this->tbl_as.status_redeem_pulsa","finished","AND","=",1,1);
        }else{
            if (count($status_in_table) > 0) {
                if(is_array($status_in_table)) if(count($status_in_table)) $this->db->where_in("$this->tbl_as.status_redeem_pulsa",$status_in_table);
            }
        } 

		// if (count($status_in_table) > 0) {
		// 	if(is_array($status_in_table)) if(count($status_in_table)) $this->db->where_in("$this->tbl_as.status_redeem_pulsa",$status_in_table);
		// }

		if(mb_strlen($keyword)>0) {
            // $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 0);
			// $this->db->where_as($this->__decrypt("$this->tbl3_as.email"), addslashes($keyword), "OR", "%like%", 0, 0);
			// $this->db->where_as($this->__decrypt("$this->tbl_as.telp"), addslashes($keyword), "OR", "%like%", 0, 1);

            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 1);
		}

		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function getById($nation_code, $id, $status_in_table=array()) {
        $this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "user", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.email"), "email", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.telp"), "telp", 0);
        $this->db->select_as("$this->tbl_as.verif_telp_manual", "verif_telp_manual", 0);
        $this->db->select_as("$this->tbl3_as.id", "day1", 0);
        $this->db->select_as("$this->tbl4_as.id", "day2", 0);
        $this->db->select_as("$this->tbl5_as.id", "day3", 0);
        $this->db->select_as("$this->tbl3_as.title", "day1_title", 0);
        $this->db->select_as("$this->tbl4_as.title", "day2_title", 0);
        $this->db->select_as("$this->tbl5_as.title", "day3_title", 0); 
        $this->db->select_as("$this->tbl_as.task_day_4", "day4", 0);     
        $this->db->select_as("$this->tbl_as.task_day_5", "day5", 0);
        $this->db->select_as("$this->tbl_as.status_redeem_pulsa", "status_redeem_pulsa", 0);
        $this->db->select_as("$this->tbl_as.cdate_redeem_pulsa", "cdate_redeem_pulsa", 0);
        $this->db->select_as("$this->tbl_as.cdate_day_5", "cdate_day_5", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp_event", 0);
		// $this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
		$this->db->where("$this->tbl_as.nation_code", $nation_code);
		$this->db->where("$this->tbl_as.id", $id);
        if (count($status_in_table) > 0) {
			if(is_array($status_in_table)) if(count($status_in_table)) $this->db->where_in("$this->tbl_as.status_redeem_pulsa",$status_in_table);
		}
		return $this->db->get_first();
	}

	public function exportXls($nation_code, $keyword, $fromDate, $toDate, $type_list="", $statusFilter, $status_in_table=array(), $type_xls="") {
        $this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		// $this->db->select_as("$this->tbl_as.id", "id", 0);
        // $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		// $this->db->select_as("$this->tbl_as.custom_status_date", "custom_status_date", 0);
        // $this->db->select_as("$this->tbl2_as.name", "redemption_exchange_name", 0);
		// $this->db->select_as($this->__decrypt("$this->tbl3_as.email"), "email", 0);
		// $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "user", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        // $this->db->select_as("$this->tbl_as.cost_spt", "cost_spt", 0);
        // $this->db->select_as("$this->tbl_as.amount_get", "amount_get", 0);
        // $this->db->select_as("$this->tbl_as.status", "status", 0);
		// $this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		// $this->db->from($this->tbl, $this->tbl_as);
        // $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        // $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");

        $this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "user", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.email"), "email", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.telp"), "telp", 0);
        $this->db->select_as("$this->tbl3_as.id", "day1", 0);
        $this->db->select_as("$this->tbl4_as.id", "day2", 0);
        $this->db->select_as("$this->tbl5_as.id", "day3", 0);   
        $this->db->select_as("$this->tbl_as.task_day_4", "day4", 0);     
        $this->db->select_as("$this->tbl_as.task_day_5", "day5", 0);        
        $this->db->select_as("$this->tbl_as.status_redeem_pulsa", "status_redeem_pulsa", 0);
        $this->db->select_as("$this->tbl_as.cdate_redeem_pulsa", "cdate_redeem_pulsa", 0);
        $this->db->select_as("$this->tbl_as.cdate_day_5", "cdate_day_5", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp_event", 0);
        $this->db->select_as("$this->tbl_as.verif_telp_manual", "verif_telp_manual", 0);
		// $this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left");
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");






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

        if ($type_xls == 'agent') {
            $this->db->where("$this->tbl_as.status_redeem_pulsa","accepted","AND","=",1,1);
        }else if ($statusFilter == "1") {
            $this->db->where("$this->tbl_as.status_redeem_pulsa","pending","AND","=",1,1);
        }else if ($statusFilter == "2") {
            $this->db->where("$this->tbl_as.status_redeem_pulsa","accepted","AND","=",1,1);
        }else if ($statusFilter == "3") {				
            $this->db->where("$this->tbl_as.status_redeem_pulsa","rejected","AND","=",1,1);
        }else if ($statusFilter == "4") {
            $this->db->where("$this->tbl_as.status_redeem_pulsa","finished","AND","=",1,1);
        }else{
            if (count($status_in_table) > 0) {
                if(is_array($status_in_table)) if(count($status_in_table)) $this->db->where_in("$this->tbl_as.status_redeem_pulsa",$status_in_table);
            }
        }       		

		if(mb_strlen($keyword)>0) {
            // $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 0);
			// $this->db->where_as($this->__decrypt("$this->tbl3_as.email"), addslashes($keyword), "OR", "%like%", 0, 0);
			// $this->db->where_as($this->__decrypt("$this->tbl_as.telp"), addslashes($keyword), "OR", "%like%", 0, 1);

            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 1);
		}

		$this->db->order_by($this->__decrypt("$this->tbl2_as.telp"), "ASC");

		return $this->db->get("object", 0);
	}

	public function set($di) {
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl, $di, 0, 0);
	}

	public function update($nation_code, $id, $du) {
		if(!is_array($du)) return 0;
        if (isset($du['telp'])) {
            if (strlen($du['telp'])) {
                $du['telp'] = $this->__encrypt($du['telp']);
            }
        }
		$this->db->where("nation_code", $nation_code);
		$this->db->where("id", $id);
    return $this->db->update($this->tbl, $du, 0);
	}

	public function del($nation_code, $id){
		$this->db->where("nation_code", $nation_code);
		$this->db->where("id", $id);
		return $this->db->delete($this->tbl);
	}

    public function getDataCommunityById($nation_code, $id) {
        $this->db->flushQuery();
		$this->db->select_as("$this->tbl3_as.id", "id", 0);
        $this->db->select_as("$this->tbl3_as.title", "title", 0);
		$this->db->from($this->tbl3, $this->tbl3_as);
		$this->db->where("$this->tbl3_as.nation_code", $nation_code);
		$this->db->where("$this->tbl3_as.id", $id);
		return $this->db->get_first();
	}

    public function checkTelpEvent($nation_code, $telp)
    {
        $this->db->select_as($this->__decrypt("$this->tbl_as.telp"), "telp", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl_as.telp").'USING "utf8" ) ', addslashes($telp), "AND", "=", 1, 1);
        return $this->db->get_first();
    }

    public function checkTelpUser($nation_code, $telp)
    {
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "user", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.telp"), "telp", 0);
        $this->db->from($this->tbl2, $this->tbl2_as);
        $this->db->where("nation_code", $nation_code);
        $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.telp").'USING "utf8" ) ', addslashes($telp), "AND", "=", 1, 1);
        return $this->db->get_first();
    }
    
}
