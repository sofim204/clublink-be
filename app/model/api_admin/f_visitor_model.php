<?php
class f_visitor_Model extends JI_Model
{
    public $is_cacheable;
    public $tbl = 'f_visitor';
    public $tbl_as = 'fv';
    public $tbl2 = 'f_visitor_history';
    public $tbl2_as = 'fvh';
    public $tbl3 = "b_user";
    public $tbl3_as = "bu";
    public $tbl4 = "b_user_alamat";
    public $tbl4_as = "bua";

    public function __construct()
    {
        parent::__construct();
        $this->is_cacheable = 0;
        $this->db->from($this->tbl, $this->tbl_as);
    }

    private function __joinTbl3(){
		$cps = array();
		$cps[] = $this->db->composite_create("$this->tbl2_as.b_user_id","=","$this->tbl3_as.id");
		return $cps;
	}

    private function __joinTbl4(){
		$cps = array();
		// $cps[] = $this->db->composite_create("$this->tbl3_as.nation_code","=","$this->tbl4_as.nation_code");
		$cps[] = $this->db->composite_create("$this->tbl3_as.id","=","$this->tbl4_as.b_user_id");
        $cps[] = $this->db->composite_create("1", "=", "$this->tbl4_as.is_default");
		return $cps;
	}

    public function getTableAlias()
    {
        return $this->tbl_as;
    }
    public function getTableAlias2()
    {
        return $this->tbl2_as;
    }
    public function trans_start()
    {
        $r = $this->db->autocommit(0);
        if ($r) {
            return $this->db->begin();
        }
        return false;
    }
    public function trans_commit()
    {
        return $this->db->commit();
    }
    public function trans_rollback()
    {
        return $this->db->rollback();
    }
    public function trans_end()
    {
        return $this->db->autocommit(1);
    }
        
    public function setDebug($is_debug=0)
    {
        $this->db->setDebug($is_debug);
    }

    // public function getAllForVisitorCount($nation_code, $page=0, $pagesize=10, $sortCol="id", $sortDir="desc", $keyword="", $cdate_start="", $cdate_end="", $mobile_type="")
    // {
    //     $this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate desc)", "no"); // by Muhammad Sofi 22 December 2021 10:00 | add row number
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.mobile_type", "mobile_type", 0);
    //     $this->db->select_as("$this->tbl_as.total_visit", "total_visit", 0);
    //     $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     if (strlen($mobile_type)>0) {
    //         $this->db->where_as("$this->tbl_as.mobile_type", $this->db->esc($mobile_type));
    //     }
    //     if (strlen($cdate_start)==10 && strlen($cdate_end)==10) {
    //         $this->db->between("DATE($this->tbl_as.cdate)", "DATE('$cdate_start')", "DATE('$cdate_end')");
    //     } elseif (strlen($cdate_start)==10 && strlen($cdate_end)!=10) {
    //         $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$cdate_start')", "AND", ">=");
    //     } elseif (strlen($cdate_start)!=10 && strlen($cdate_end)==10) {
    //         $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$cdate_end')", "AND", "<=");
    //     }
    //     if (strlen($keyword)>0) {
    //         // by Muhammad Sofi 21 December 2021 15:26 | fix bug on search box
    //         // $this->db->where_as("$this->tbl_as.mobile_type", addslashes($keyword), "OR", "%like%", 1, 0);
    //         $this->db->where_as("$this->tbl_as.mobile_type", addslashes($keyword), "OR", "%like%", 0, 0);
    //     }
    //     $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
    //     return $this->db->get('', 0);
    // }
    public function getAllForVisitorCount($nation_code, $page=0, $pagesize=10, $sortCol="id", $sortDir="desc", $keyword="", $cdate_start="", $cdate_end="", $mobile_type="")
    {
        $this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate desc)", "no"); // by Muhammad Sofi 22 December 2021 10:00 | add row number
        $this->db->select_as("$this->tbl2_as.id", "id", 0);
        $this->db->select_as("$this->tbl2_as.mobile_type", "mobile_type", 0);
        $this->db->select_as("IF(DATE($this->tbl2_as.cdate) <= '2022-10-21', (COUNT(DISTINCT CONCAT(cdate, '-', b_user_id, '-', mobile_type))), (SELECT COUNT(*) FROM f_visitor_count WHERE DATE(cdate) = DATE($this->tbl2_as.cdate) AND mobile_type = $this->tbl2_as.mobile_type))", "visitor_count_from_ud_id", 0);
        //$this->db->select_as("COUNT(DISTINCT CONCAT(DATE(cdate), '-', udid, '-', mobile_type))", "visitor_count", 0);
        $this->db->select_as("IF(DATE($this->tbl2_as.cdate) <= '2022-10-21', (SELECT COUNT(*) FROM f_visitor_count WHERE DATE(cdate) = DATE($this->tbl2_as.cdate) AND mobile_type = $this->tbl2_as.mobile_type), (COUNT(DISTINCT CONCAT(cdate, '-', b_user_id, '-', mobile_type))))", "total_visit", 0);
        $this->db->select_as("DATE($this->tbl2_as.cdate)", "cdate", 0);
        $this->db->from($this->tbl2, $this->tbl2_as);
        // $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        // $this->db->where_as("DATE_ADD(COALESCE($this->tbl2_as.cdate,NOW()), INTERVAL 1 WEEK)", "NOW()", "AND", "<=");
        $this->db->where_as("$this->tbl2_as.cdate", "DATE(NOW()) - INTERVAL 2 WEEK", "AND", ">=");
        if (strlen($mobile_type)>0) {
            $this->db->where_as("$this->tbl2_as.mobile_type", $this->db->esc($mobile_type));
        }
        if (strlen($cdate_start)==10 && strlen($cdate_end)==10) {
            $this->db->between("DATE($this->tbl2_as.cdate)", "DATE('$cdate_start')", "DATE('$cdate_end')");
        } elseif (strlen($cdate_start)==10 && strlen($cdate_end)!=10) {
            $this->db->where_as("DATE($this->tbl2_as.cdate)", "DATE('$cdate_start')", "AND", ">=");
        } elseif (strlen($cdate_start)!=10 && strlen($cdate_end)==10) {
            $this->db->where_as("DATE($this->tbl2_as.cdate)", "DATE('$cdate_end')", "AND", "<=");
        }
        if (strlen($keyword)>0) {
            // by Muhammad Sofi 21 December 2021 15:26 | fix bug on search box
            // $this->db->where_as("$this->tbl_as.mobile_type", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl2_as.mobile_type", addslashes($keyword), "OR", "%like%", 0, 0);
        }

        $this->db->group_by("CONCAT(DATE($this->tbl2_as.cdate), '-',$this->tbl2_as.mobile_type)");

        // $this->db->order_by($sortCol, $sortDir);
        $this->db->order_by("DATE($this->tbl2_as.cdate)", "desc");
        $this->db->limit($page, $pagesize);
        return $this->db->get('', 0);
    }

    public function countAllForVisitorCount($nation_code, $keyword="", $cdate_start="", $cdate_end="", $mobile_type="")
    {
        // $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->select_as("COUNT(DISTINCT CONCAT(cdate, '-', mobile_type))", "jumlah", 0);
        $this->db->from($this->tbl2, $this->tbl2_as);
        // $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        // $this->db->where_as("DATE_ADD(COALESCE($this->tbl2_as.cdate,NOW()), INTERVAL 1 WEEK)", "NOW()", "AND", "<=");
        if (strlen($mobile_type)>0) {
            $this->db->where_as("$this->tbl2_as.mobile_type", $this->db->esc($mobile_type));
        }
        if (strlen($cdate_start)==10 && strlen($cdate_end)==10) {
            $this->db->between("DATE($this->tbl2_as.cdate)", "DATE('$cdate_start')", "DATE('$cdate_end')");
        } elseif (strlen($cdate_start)==10 && strlen($cdate_end)!=10) {
            $this->db->where_as("DATE($this->tbl2_as.cdate)", "DATE('$cdate_start')", "AND", ">=");
        } elseif (strlen($cdate_start)!=10 && strlen($cdate_end)==10) {
            $this->db->where_as("DATE($this->tbl2_as.cdate)", "DATE('$cdate_end')", "AND", "<=");
        }
        if (strlen($keyword)>0) {
            // by Muhammad Sofi 21 December 2021 15:26 | fix bug on searchbox
            // $this->db->where_as("$this->tbl_as.mobile_type", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl2_as.mobile_type", addslashes($keyword), "OR", "%like%", 0, 0);
        }
        $this->db->group_by("CONCAT(DATE($this->tbl2_as.cdate), '-',$this->tbl2_as.mobile_type)");

        $d = $this->db->get_first();
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function countTotalVisit($nation_code, $keyword="", $cdate_start="", $cdate_end="", $mobile_type="")
    {
        $this->db->select_as("SUM(total_visit)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        if (strlen($mobile_type)>0) {
            $this->db->where_as("$this->tbl_as.mobile_type", $this->db->esc($mobile_type));
        }
        if (strlen($cdate_start)==10 && strlen($cdate_end)==10) {
            $this->db->between("DATE($this->tbl_as.cdate)", "DATE('$cdate_start')", "DATE('$cdate_end')");
        } elseif (strlen($cdate_start)==10 && strlen($cdate_end)!=10) {
            $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$cdate_start')", "AND", ">=");
        } elseif (strlen($cdate_start)!=10 && strlen($cdate_end)==10) {
            $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$cdate_end')", "AND", "<=");
        }
        if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl_as.mobile_type", addslashes($keyword), "OR", "%like%", 1, 0);
        }
        $d = $this->db->get_first();
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function getDetailAll($page=0, $pagesize=10, $keyword="", $mobile_type="", $detail_date="") {
		$this->db->flushQuery();
        $this->db->select_as("ROW_NUMBER() OVER (ORDER BY $this->tbl2_as.cdate desc)", "no"); // by Muhammad Sofi 22 December 2021 10:00 | add row number
        // $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "fnama", 0);
        $this->db->select_as("IF($this->tbl2_as.b_user_id = 0, $this->tbl2_as.b_user_id, ".$this->__decrypt("$this->tbl3_as.fnama").")", "fnama", 0);
		$this->db->select_as("$this->tbl2_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl2_as.mobile_type", "mobile_type", 0);
        $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl3_as.email").",'-')", "email", 0);
		// $this->db->select_as("CONCAT($this->tbl2_as.latitude, ', ', $this->tbl2_as.longitude)", "latlong", 0);
        // $this->db->select_as("COALESCE(CONCAT($this->tbl4_as.kecamatan,', ',$this->tbl4_as.provinsi), '-')","Location",0);
		// $this->db->select_as("$this->tbl2_as.location", "location", 0);
		// $this->db->select_as("$this->tbl2_as.province", "province", 0);
		$this->db->select_as("CONCAT($this->tbl2_as.location,', ',$this->tbl2_as.province)", "location", 0);

		$this->db->from($this->tbl2, $this->tbl2_as);
        $this->db->join_composite($this->tbl3,$this->tbl3_as,$this->__joinTbl3(),'left');
        // $this->db->join_composite($this->tbl4,$this->tbl4_as,$this->__joinTbl4(),'left');
		$this->db->where_as("DATE($this->tbl2_as.cdate)", "DATE('$detail_date')", 'AND', '=');
		$this->db->where_as("$this->tbl2_as.mobile_type", $this->db->esc($mobile_type));

		$this->db->order_by("$this->tbl2_as.cdate", "desc");

        // if($this->__decrypt("$this->tbl3_as.email") == "") {
            // $this->db->group_by("CONCAT($this->tbl2_as.cdate, '-',$this->tbl3_as.email)");
            $this->db->group_by("CONCAT($this->tbl2_as.cdate, '-',$this->tbl2_as.b_user_id)");
            // $this->db->group_by("$this->tbl2_as.cdate");
        // } else {
            // $this->db->group_by("CONCAT($this->tbl2_as.cdate, '-',$this->tbl2_as.latitude, '-', $this->tbl2_as.longitude)");
            // $this->db->group_by("CONCAT($this->tbl2_as.cdate, '-',$this->tbl3_as.email)");
            // $this->db->group_by("$this->tbl3_as.email");
        // }
        // $this->db->group_by("CONCAT($this->tbl2_as.cdate, '-',$this->tbl3_as.email)");

		$this->db->limit($page, $pagesize);
		return $this->db->get("object", 0);
	}

	public function countDetailAll($keyword="",  $mobile_type="", $detail_date="") {
		$this->db->flushQuery();
		// $this->db->select_as("COUNT(DISTINCT cdate, b_user_id)", "jumlah", 0);
		$this->db->select_as("COUNT(DISTINCT CONCAT(cdate, '-', b_user_id, '-', mobile_type))", "jumlah", 0);
		// $this->db->select_as("COUNT(*)", "jumlah", 0);
		$this->db->from($this->tbl2, $this->tbl2_as);
        // $this->db->join_composite($this->tbl3,$this->tbl3_as,$this->__joinTbl3(),'left');
		$this->db->where_as("DATE($this->tbl2_as.cdate)", "DATE('$detail_date')", 'AND', '=');
		$this->db->where_as("$this->tbl2_as.mobile_type", $this->db->esc($mobile_type));

        // if($this->__decrypt("$this->tbl3_as.email") == "") {
        //     $this->db->group_by("CONCAT($this->tbl2_as.cdate, '-',$this->tbl3_as.email)");
        // } else {
        //     $this->db->group_by("CONCAT($this->tbl2_as.cdate, '-',$this->tbl2_as.latitude, '-', $this->tbl2_as.longitude)");
        //     // $this->db->group_by("CONCAT($this->tbl2_as.cdate, '-',$this->tbl3_as.email)");
        //     // $this->db->group_by("$this->tbl3_as.email");
        // }

		$d = $this->db->get_first("object", 0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

    public function getTotalUser($mobile_type="", $detail_date="") {
        $this->db->flushQuery();
		// $this->db->select_as("COUNT(DISTINCT cdate)", "jumlah", 0);
        // $this->db->select_as("COUNT(*)", "jumlah", 0);
		$this->db->select_as("COUNT(DISTINCT CONCAT(cdate, '-', b_user_id, '-', mobile_type))", "jumlah", 0);
		// $this->db->select_as("COUNT(*)", "jumlah", 0);
		$this->db->from($this->tbl2, $this->tbl2_as);
        // $this->db->join_composite($this->tbl3,$this->tbl3_as,$this->__joinTbl3(),'left');
		$this->db->where_as("DATE($this->tbl2_as.cdate)", "DATE('$detail_date')", 'AND', '=');
		$this->db->where_as("$this->tbl2_as.mobile_type", $this->db->esc($mobile_type));

        // $this->db->order_by("$this->tbl2_as.cdate", "desc");
        // $this->db->group_by("CONCAT($this->tbl2_as.cdate, '-',$this->tbl2_as.latitude, '-', $this->tbl2_as.longitude)");

        $d = $this->db->get_first("", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    // public function getById($nation_code, $id) {
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where("$this->tbl_as.nation_code", $nation_code);
    //     $this->db->where("$this->tbl_as.id", $id);
    //     return $this->db->get_first();
    // }
    public function getById($nation_code, $mobile_type, $cdate) {
        $this->db->select_as("DATE($this->tbl2_as.cdate)", "cdate", 0);
        $this->db->select_as("$this->tbl2_as.mobile_type", "mobile_type", 0);
        $this->db->from($this->tbl2, $this->tbl2_as);
        // $this->db->where("$this->tbl_as.nation_code", $nation_code);
        // $this->db->where("$this->tbl2_as.id", $id);
        // $this->db->where("DATE($this->tbl2_as.cdate))", "DATE('$id')");
        $this->db->where_as("$this->tbl2_as.mobile_type", $this->db->esc($mobile_type), "AND", "=");
        $this->db->where_as("DATE($this->tbl2_as.cdate)", "DATE('$cdate')", "AND", "=");
        return $this->db->get_first();
    }
}
