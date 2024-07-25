<?php
class B_User_Offer_Sales_Admin_Model extends JI_Model {
	var $tbl = 'b_user_offer_sales';
	var $tbl_as = 'buos';
	var $tbl2 = 'b_user';
	var $tbl2_as = 'bu';

	public function __construct() {
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	public function getTableAlias() {
		return $this->tbl_as;
	}

    private function __joinTbl2() {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl2_as.id");
        return $cps;
    }

	public function countAll($nation_code, $keyword="", $from_date="", $to_date="") {
        $this->db->flushQuery();
        $this->db->exec("SET NAMES 'UTF8MB4' COLLATE 'utf8mb4_unicode_ci'");
        $this->db->select_as("COUNT(DISTINCT $this->tbl_as.b_user_id)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
		$this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));

		$year_from_date = date('Y', strtotime($from_date));
		$month_from_date = date('m', strtotime($from_date));

		$year_to_date = date('Y', strtotime($to_date));
		$month_to_date = date('m', strtotime($to_date));

        // if (strlen($from_date)==7 && strlen($to_date)==7) {

		// 	$this->db->between("$this->tbl_as.year", "'$year_from_date'", "'$year_to_date'");
		// 	$this->db->between("$this->tbl_as.month", "'$month_from_date'", "'$month_to_date'");

		// 	// $this->db->where("$this->tbl_as.year", "'$year_from_date'", "and",">=");
		// 	// $this->db->where("$this->tbl_as.month", "'$month_from_date'", "and",">=");

		// 	// $this->db->where("$this->tbl_as.year", "'$year_to_date'", "and","<=");
		// 	// $this->db->where("$this->tbl_as.month", "'$month_to_date'", "and","<=");
		// } 

		if (strlen($from_date)==7 && strlen($to_date)==7) {
			$this->db->where_as("DATE(CONCAT($this->tbl_as.year,'-',$this->tbl_as.month,'-','01'))", "DATE(CONCAT($year_from_date,'-',$month_from_date,'-','01'))", 'AND', '>=');
			$this->db->where_as("DATE(CONCAT($this->tbl_as.year,'-',$this->tbl_as.month,'-','28'))", "DATE(CONCAT($year_to_date,'-',$month_to_date,'-','28'))", 'AND', '<=');
		} else if (strlen($from_date)==7 && strlen($to_date)!=7) {
			$this->db->where_as("DATE(CONCAT($this->tbl_as.year,'-',$this->tbl_as.month,'-','01'))", "DATE(CONCAT($year_from_date,'-',$month_from_date,'-','01'))", 'AND', '>=');
		} else if (strlen($from_date)!=7 && strlen($to_date)==7) {
			$this->db->where_as("DATE(CONCAT($this->tbl_as.year,'-',$this->tbl_as.month,'-','28'))", "DATE(CONCAT($year_to_date,'-',$month_to_date,'-','28'))", 'AND', '<=');
		}

		// by Muhammad Sofi 11 January 2022 18:28 | add search by keyword
		if (strlen($keyword)>0) {
			$this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 1);
        }
		
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
	}
    
	public function getAll($nation_code, $page=0,$pagesize=10,$sortCol="sku",$sortDir="ASC", $keyword="", $from_date="", $to_date="") {
		$this->db->flushQuery();
        $this->db->select_as("ROW_NUMBER() OVER (ORDER BY SUM($this->tbl_as.total_sales_seller) DESC)", "no");
		$this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "user_name", 0);
        $this->db->select_as("COALESCE(SUM($this->tbl_as.total_sales_seller), 0)", "total_sales_seller", 0);
        $this->db->select_as("COALESCE(SUM($this->tbl_as.total_transaction_seller), 0)", "total_transaction_seller", 0);
        $this->db->select_as("COALESCE(SUM($this->tbl_as.total_sales_buyer), 0)", "total_sales_buyer", 0);
        $this->db->select_as("COALESCE(SUM($this->tbl_as.total_transaction_buyer), 0)", "total_transaction_buyer", 0);

		$this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");

		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);

		$year_from_date = date('Y', strtotime($from_date));
		$month_from_date = date('m', strtotime($from_date));

		$year_to_date = date('Y', strtotime($to_date));
		$month_to_date = date('m', strtotime($to_date));

		// if (strlen($from_date)==7 && strlen($to_date)==7) {
		// 	$this->db->between("$this->tbl_as.year", "'$year_from_date'", "'$year_to_date'");
		// 	$this->db->between("$this->tbl_as.month", "'$month_from_date'", "'$month_to_date'");
		// } 

		// try new filter year-month

		if (strlen($from_date)==7 && strlen($to_date)==7) {
			$this->db->where_as("DATE(CONCAT($this->tbl_as.year,'-',$this->tbl_as.month,'-','01'))", "DATE(CONCAT($year_from_date,'-',$month_from_date,'-','01'))", 'AND', '>=');
			$this->db->where_as("DATE(CONCAT($this->tbl_as.year,'-',$this->tbl_as.month,'-','28'))", "DATE(CONCAT($year_to_date,'-',$month_to_date,'-','28'))", 'AND', '<=');
		} else if (strlen($from_date)==7 && strlen($to_date)!=7) {
			$this->db->where_as("DATE(CONCAT($this->tbl_as.year,'-',$this->tbl_as.month,'-','01'))", "DATE(CONCAT($year_from_date,'-',$month_from_date,'-','01'))", 'AND', '>=');
		} else if (strlen($from_date)!=7 && strlen($to_date)==7) {
			$this->db->where_as("DATE(CONCAT($this->tbl_as.year,'-',$this->tbl_as.month,'-','28'))", "DATE(CONCAT($year_to_date,'-',$month_to_date,'-','28'))", 'AND', '<=');
		}

        if (mb_strlen($keyword)>0) {
			$this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 1);
        }

		$this->db->group_by("$this->tbl_as.b_user_id", 0);
		// $this->db->order_by("SUM($this->tbl_as.total_sales_seller)", "DESC");
		$this->db->order_by($sortCol, $sortDir);

        $this->db->limit($page, $pagesize);
        return $this->db->get("object", 0);
	}

	// public function countTotalSalesSellerMonth($nation_code, $year, $month) {
	// 	$this->db->select_as("COALESCE(SUM($this->tbl_as.total_sales_seller), 0)", "total_sales_seller", 0);
	// 	$this->db->from($this->tbl, $this->tbl_as);
	// 	$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
	// 	$this->db->where_as("$this->tbl_as.year", $year, "AND", "=", 0, 0);
	// 	$this->db->where_as("$this->tbl_as.month", $month, "AND", "=", 0, 0);
    //     $d = $this->db->get_first("", 0);
    //     if (isset($d->total_sales_seller)) {
    //         return $d->total_sales_seller;
    //     }
    //     return 0;
	// }

	// public function countTotalTransactionSellerMonth($nation_code, $year, $month) {
	// 	$this->db->select_as("COALESCE(SUM($this->tbl_as.total_transaction_seller), 0)", "total_transaction_seller", 0);
	// 	$this->db->from($this->tbl, $this->tbl_as);
	// 	$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
	// 	$this->db->where_as("$this->tbl_as.year", $year, "AND", "=", 0, 0);
	// 	$this->db->where_as("$this->tbl_as.month", $month, "AND", "=", 0, 0);
    //     $d = $this->db->get_first("", 0);
    //     if (isset($d->total_transaction_seller)) {
    //         return $d->total_transaction_seller;
    //     }
    //     return 0;
	// }

	public function countTotalSalesSellerMonth($nation_code, $from_date="", $to_date="") {
		$this->db->select_as("COALESCE(SUM($this->tbl_as.total_sales_seller), 0)", "total_sales_seller", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$year_from_date = date('Y', strtotime($from_date));
		$month_from_date = date('m', strtotime($from_date));

		$year_to_date = date('Y', strtotime($to_date));
		$month_to_date = date('m', strtotime($to_date));

		// if (strlen($from_date)==7 && strlen($to_date)==7) {
			$this->db->where_as("DATE(CONCAT($this->tbl_as.year,'-',$this->tbl_as.month,'-','01'))", "DATE(CONCAT($year_from_date,'-',$month_from_date,'-','01'))", 'AND', '>=');
			$this->db->where_as("DATE(CONCAT($this->tbl_as.year,'-',$this->tbl_as.month,'-','28'))", "DATE(CONCAT($year_to_date,'-',$month_to_date,'-','28'))", 'AND', '<=');
		// } else if (strlen($from_date)==7 && strlen($to_date)!=7) {
		// 	$this->db->where_as("DATE(CONCAT($this->tbl_as.year,'-',$this->tbl_as.month,'-','01'))", "DATE(CONCAT($year_from_date,'-',$month_from_date,'-','01'))", 'AND', '>=');
		// } else if (strlen($from_date)!=7 && strlen($to_date)==7) {
		// 	$this->db->where_as("DATE(CONCAT($this->tbl_as.year,'-',$this->tbl_as.month,'-','28'))", "DATE(CONCAT($year_to_date,'-',$month_to_date,'-','28'))", 'AND', '<=');
		// }
		
        $d = $this->db->get_first("", 0);
        if (isset($d->total_sales_seller)) {
            return $d->total_sales_seller;
        }
        return 0;
	}

	public function countTotalTransactionSellerMonth($nation_code, $from_date, $to_date) {
		$this->db->select_as("COALESCE(SUM($this->tbl_as.total_transaction_seller), 0)", "total_transaction_seller", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$year_from_date = date('Y', strtotime($from_date));
		$month_from_date = date('m', strtotime($from_date));

		$year_to_date = date('Y', strtotime($to_date));
		$month_to_date = date('m', strtotime($to_date));

		// if (strlen($from_date)==7 && strlen($to_date)==7) {
			$this->db->where_as("DATE(CONCAT($this->tbl_as.year,'-',$this->tbl_as.month,'-','01'))", "DATE(CONCAT($year_from_date,'-',$month_from_date,'-','01'))", 'AND', '>=');
			$this->db->where_as("DATE(CONCAT($this->tbl_as.year,'-',$this->tbl_as.month,'-','28'))", "DATE(CONCAT($year_to_date,'-',$month_to_date,'-','28'))", 'AND', '<=');
		// } else if (strlen($from_date)==7 && strlen($to_date)!=7) {
		// 	$this->db->where_as("DATE(CONCAT($this->tbl_as.year,'-',$this->tbl_as.month,'-','01'))", "DATE(CONCAT($year_from_date,'-',$month_from_date,'-','01'))", 'AND', '>=');
		// } else if (strlen($from_date)!=7 && strlen($to_date)==7) {
		// 	$this->db->where_as("DATE(CONCAT($this->tbl_as.year,'-',$this->tbl_as.month,'-','28'))", "DATE(CONCAT($year_to_date,'-',$month_to_date,'-','28'))", 'AND', '<=');
		// }
        $d = $this->db->get_first("", 0);
        if (isset($d->total_transaction_seller)) {
            return $d->total_transaction_seller;
        }
        return 0;
	}

	public function countTotalSalesAll($nation_code) {
		$this->db->select_as("COALESCE(SUM($this->tbl_as.total_sales_seller), 0)", "total_sales_seller", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
        $d = $this->db->get_first("", 0);
        if (isset($d->total_sales_seller)) {
            return $d->total_sales_seller;
        }
        return 0;
	}

}
