<?php
class G_Daily_Track_Record_Model extends JI_Model {
	// var $tbl = 'f_visitor_history';
	// var $tbl_as = 'fvh';
	var $tbl = 'g_daily_track_record';
	var $tbl_as = 'gdtr';
	var $tbl_b_user = 'b_user';
	var $tbl_b_user_as = 'bu';

	public function __construct() {
		parent::__construct();
		$this->db->from($this->tbl, $this->tbl_as);
	}

	public function getTableAlias() {
		return $this->tbl_as;
	}
	// public function countAll($nation_code, $keyword="", $from_date="", $to_date="") {
    //     $this->db->flushQuery();
    //     $this->db->exec("SET NAMES 'UTF8MB4' COLLATE 'utf8mb4_unicode_ci'");
    //     // $this->db->select_as("COUNT(DISTINCT $this->tbl_as.b_user_id)", "jumlah", 0);
    //     $this->db->select_as("COUNT(DISTINCT DATE(cdate))", "jumlah", 0);
	// 	// $this->db->select_as("IF(DATE($this->tbl_as.cdate) <= '2022-10-21', (COUNT(DISTINCT CONCAT(cdate, '-', b_user_id, '-', mobile_type))), (SELECT COUNT(*) FROM f_visitor_count WHERE DATE(cdate) = DATE($this->tbl_as.cdate) AND (mobile_type = 'android' OR mobile_type = 'ios')))", "jumlah", 0);

	// 	// $this->db->select_as("(SELECT COUNT(DISTINCT(date(cdate))) as total from b_user WHERE DATE(cdate) = DATE($this->tbl_as.cdate))", "jumlah", 0);

    //     $this->db->from($this->tbl, $this->tbl_as);
	// 	$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE(NOW()) - INTERVAL 1 WEEK", "AND", ">=");
	// 	// $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
	// 	// $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));

    //     // if (strlen($from_date)==10 && strlen($to_date)==10) {
    //     //     $this->db->between("DATE($this->tbl_as.cdate)", "DATE('$from_date')", "DATE('$to_date')");
    //     // } elseif (strlen($from_date)==10 && strlen($to_date)!=10) {
    //     //     $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$from_date')", "AND", ">=");
    //     // } elseif (strlen($from_date)!=10 && strlen($to_date)==10) {
    //     //     $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$to_date')", "AND", "<=");
    //     // }


	// 	// by Muhammad Sofi 11 January 2022 18:28 | add search by keyword
	// 	// if (strlen($keyword)>0) {
	// 	// 	$this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', $keyword, "OR", "%like%", 1, 1);
    //     // }
		
    //     $d = $this->db->get_first("object", 0);
    //     if (isset($d->jumlah)) {
    //         return $d->jumlah;
    //     }
    //     return 0;
	// }
    
	// public function getAll($nation_code, $page=0,$pagesize=10,$sortCol="sku",$sortDir="DESC", $keyword="", $from_date="", $to_date="") {
	// 	$this->db->flushQuery();
    //     $this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate DESC)", "no");
	// 	$this->db->select_as("DATE($this->tbl_as.cdate)", "cdate_data", 0);
	// 	// $this->db->select_as("IF(DATE($this->tbl_as.cdate) <= '2022-10-21', (COUNT(DISTINCT CONCAT(cdate, '-', b_user_id, '-', mobile_type))), (SELECT COUNT(*) FROM f_visitor_count WHERE DATE(cdate) = DATE($this->tbl_as.cdate) AND mobile_type = $this->tbl_as.mobile_type))", "visitor_count_from_ud_id", 0);
	// 	// $this->db->select_as("(SELECT COUNT(*) as total from c_community group by date(cdate) order by cdate desc)", "community_total", 0);
	// 	// $this->db->select_as("(SELECT COUNT(*) as total from c_produk group by date(cdate) order by cdate desc)", "product_total", 0);
	// 	// $this->db->select_as("(SELECT COUNT(*) as total from c_community WHERE DATE(cdate) = DATE($this->tbl_community.cdate) group by date($this->tbl_community.cdate))", "community_total", 0);
	// 	// $this->db->select_as("(SELECT COUNT(*) as total from c_produk WHERE DATE(cdate) = DATE($this->tbl_product.cdate) group by date($this->tbl_product.cdate))", "product_total", 0);
	// 	$this->db->select_as("(SELECT COUNT(*) as total_signup from b_user WHERE DATE(cdate) = DATE($this->tbl_as.cdate) )", "total_signup", 0);
	// 	$this->db->select_as("(SELECT COUNT(*) as total_community from c_community WHERE DATE(cdate) = DATE($this->tbl_as.cdate) )", "total_community", 0);
	// 	$this->db->select_as("(SELECT COUNT(*) as total_product from c_produk WHERE DATE(cdate) = DATE($this->tbl_as.cdate) )", "total_product", 0);
	// 	// $this->db->select_as("IF(DATE($this->tbl_as.cdate) <= '2022-10-21', (SELECT COUNT(*) FROM f_visitor_count WHERE DATE(cdate) = DATE($this->tbl_as.cdate) AND mobile_type = $this->tbl_as.mobile_type), (COUNT(DISTINCT CONCAT(cdate, '-', b_user_id, '-', mobile_type))))", "total_visit", 0);
	// 	// $this->db->select_as("COUNT(DISTINCT CONCAT(cdate, '-', b_user_id, '-', mobile_type))", "total_visit", 0);
	// 	// $this->db->select_as("(SELECT COUNT(*) FROM f_visitor_count WHERE DATE(cdate) = DATE($this->tbl_as.cdate) AND (mobile_type = 'android' OR mobile_type = 'ios'))", "visitor_count_from_ud_id", 0);
	// 	$this->db->select_as("IF(DATE($this->tbl_as.cdate) <= '2022-10-21', (COUNT(DISTINCT CONCAT(cdate, '-', b_user_id, '-', mobile_type))), (SELECT COUNT(*) FROM f_visitor_count WHERE DATE(cdate) = DATE($this->tbl_as.cdate) AND (mobile_type = 'android' OR mobile_type = 'ios')))", "visitor_count_from_ud_id", 0);
	// 	$this->db->select_as("(SELECT COUNT(*) as total_community_video from c_community_attachment WHERE DATE(cdate) = DATE($this->tbl_as.cdate) AND jenis = 'video' AND is_active = 1)", "total_community_video", 0);
	// 	$this->db->select_as("(SELECT COUNT(*) as total_product_video from c_produk_foto WHERE DATE(cdate) = DATE($this->tbl_as.cdate) AND jenis = 'video' AND is_active = 1 )", "total_product_video", 0);

	// 	$this->db->from($this->tbl, $this->tbl_as);
	// 	$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE(NOW()) - INTERVAL 1 WEEK", "AND", ">=");

    //     if (strlen($from_date)==10 && strlen($to_date)==10) {
    //         $this->db->between("DATE($this->tbl_as.cdate)", "DATE('$from_date')", "DATE('$to_date')");
    //     } elseif (strlen($from_date)==10 && strlen($to_date)!=10) {
    //         $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$from_date')", "AND", ">=");
    //     } elseif (strlen($from_date)!=10 && strlen($to_date)==10) {
    //         $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$to_date')", "AND", "<=");
    //     }

    //     // if (mb_strlen($keyword)>0) {
	// 	// 	$this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', $keyword, "OR", "%like%", 1, 1);
    //     // }

	// 	// $this->db->group_by("CONCAT(DATE($this->tbl_as.cdate), '-', DATE($this->tbl_community_as.cdate))", 0);
    //     $this->db->group_by("DATE($this->tbl_as.cdate)");
	// 	// $this->db->group_by("CONCAT(DATE($this->tbl_as.cdate), '-', DATE($this->tbl_community_as.cdate))");
	// 	// $this->db->order_by("SUM($this->tbl_as.total_sales_seller)", "DESC");
	// 	$this->db->order_by($sortCol, $sortDir);

    //     $this->db->limit($page, $pagesize);
    //     return $this->db->get("object", 0);
	// }

	public function countAll($nation_code, $keyword="", $from_date="", $to_date="") {
        $this->db->flushQuery();
        $this->db->exec("SET NAMES 'UTF8MB4' COLLATE 'utf8mb4_unicode_ci'");
		$this->db->select_as("COUNT(*)","jumlah",0);

        $this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=");
		$this->db->where_as("$this->tbl_as.cdate", $this->db->esc("2022-12-01"), "AND", ">=");

        if (strlen($from_date)==10 && strlen($to_date)==10) {
            $this->db->between("DATE($this->tbl_as.cdate)", "DATE('$from_date')", "DATE('$to_date')");
        } elseif (strlen($from_date)==10 && strlen($to_date)!=10) {
            $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$from_date')", "AND", ">=");
        } elseif (strlen($from_date)!=10 && strlen($to_date)==10) {
            $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$to_date')", "AND", "<=");
        }

        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
	}

	public function getAll($nation_code, $page=0,$pagesize=10,$sortCol="sku",$sortDir="DESC", $keyword="", $from_date="", $to_date="") {
		$this->db->flushQuery();
        $this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate DESC)", "no");
		// $this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		// $this->db->select_as("$this->tbl_as.signup", "signup", 0);
		$this->db->select_as("($this->tbl_as.signup_android+$this->tbl_as.signup_ios)", "signup", 0);
		$this->db->select_as("$this->tbl_as.community_post", "community_post", 0);
		$this->db->select_as("$this->tbl_as.club_create", "club_create", 0);
		// $this->db->select_as("$this->tbl_as.visit", "visit", 0);
		$this->db->select_as("($this->tbl_as.visit_android+$this->tbl_as.visit_ios)", "visit", 0);
		$this->db->select_as("$this->tbl_as.signup_android", "signup_android", 0);
		$this->db->select_as("$this->tbl_as.signup_ios", "signup_ios", 0);
		$this->db->select_as("$this->tbl_as.visit_android", "visit_android", 0);
		$this->db->select_as("$this->tbl_as.visit_ios", "visit_ios", 0);
		$this->db->select_as("$this->tbl_as.community_video", "community_video", 0);
		$this->db->select_as("$this->tbl_as.club_post", "club_post", 0);
		$this->db->select_as("$this->tbl_as.temp_android", "temp_android", 0);

		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=");
		$this->db->where_as("$this->tbl_as.cdate", $this->db->esc("2022-01-01"), "AND", ">=");

        if (strlen($from_date)==10 && strlen($to_date)==10) {
            $this->db->between("DATE($this->tbl_as.cdate)", "DATE('$from_date')", "DATE('$to_date')");
        } elseif (strlen($from_date)==10 && strlen($to_date)!=10) {
            $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$from_date')", "AND", ">=");
        } elseif (strlen($from_date)!=10 && strlen($to_date)==10) {
            $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$to_date')", "AND", "<=");
        }

		$this->db->order_by($sortCol, $sortDir);

        $this->db->limit($page, $pagesize);
        return $this->db->get("object", 0);
	}

	public function sumTotal($nation_code, $from_date="", $to_date="") {
		$this->db->select_as("SUM($this->tbl_as.signup_android+$this->tbl_as.signup_ios)", "sum_signup", 0);
		$this->db->select_as("SUM($this->tbl_as.community_post)", "sum_community_post", 0);
		$this->db->select_as("SUM($this->tbl_as.product_post)", "sum_product_post", 0);

		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.cdate", $this->db->esc("2022-12-01"), "AND", ">=");

        if (strlen($from_date)==10 && strlen($to_date)==10) {
            $this->db->between("DATE($this->tbl_as.cdate)", "DATE('$from_date')", "DATE('$to_date')");
        } elseif (strlen($from_date)==10 && strlen($to_date)!=10) {
            $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$from_date')", "AND", ">=");
        } elseif (strlen($from_date)!=10 && strlen($to_date)==10) {
            $this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$to_date')", "AND", "<=");
        }

        return $this->db->get_first("object", 0);
	}

}
