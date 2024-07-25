<?php
class G_Leaderboard_Model extends SENE_Model {
	var $tbl = 'g_leaderboard_point_area';
	var $tbl_as = 'glpa';
	var $tbl2 = 'b_user';
	var $tbl2_as = 'bu';
	var $tbl3 = 'b_user_alamat';
	var $tbl3_as = 'bua';
	// var $tbl4 = 'b_user_alamat_location';
	// var $tbl4_as = 'bual';

	public function __construct() {
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

	public function getTableAlias() {
		return $this->tbl_as;
	}

	public function getTableAlias2() {
		return $this->tbl2_as;
	}

	public function getTableAlias3() {
		return $this->tbl3_as;
	}

	// public function getTableAlias4() {
	// 	return $this->tbl4_as;
	// }
	
	private function __joinTbl2() {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl2_as.id");
        return $cps;
    }

	private function __joinTbl3() {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl2_as.id", "=", "$this->tbl3_as.b_user_id");
        $cps[] = $this->db->composite_create("1", "=", "$this->tbl3_as.is_default");
        return $cps;
    }

	public function trans_start() {
		$r = $this->db->autocommit(0);
		if($r) return $this->db->begin();
		return false;
	}

	public function trans_commit() {
		return $this->db->commit();
	}

	public function trans_rollback() {
		return $this->db->rollback();
	}

	public function trans_end() {
		return $this->db->autocommit(1);
	}

	public function getLastId($nation_code) {
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}

	public function countAll($nation_code, $keyword="", $location='') {
        $this->db->flushQuery();
        $this->db->exec("SET NAMES 'UTF8MB4' COLLATE 'utf8mb4_unicode_ci'");
        // $this->db->select_as("COUNT(*)", "jumlah", 0);
		// by Muhammad Sofi 12 January 2022 11:00 | Change logic on show user rank based on total point
        $this->db->select_as("COUNT(DISTINCT $this->tbl_as.b_user_id)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
		$this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
		$this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'inner');
		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));

		// if (strlen($location)>0) {
        //     $this->db->where_as("$this->tbl_as.b_user_alamat_location_postal_district", $this->db->esc($location));
        // }
		if($location != ''){
            $this->db->where_as("$this->tbl_as.b_user_alamat_location_kelurahan", $this->db->esc($location));
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

	public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="", $sortDir="asc", $keyword="", $location='') {
        $this->db->flushQuery();
        $this->db->exec("SET NAMES 'UTF8MB4' COLLATE 'utf8mb4_unicode_ci'");
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY total_point DESC)", "ranking"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl2_as.image", "user_image", 0);
		$this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "user_name", 0);
		// $this->db->select_as("$this->tbl_as.total_post", "total_post", 0);
		// $this->db->select_as("$this->tbl_as.total_point", "total_point", 0);

		// by Muhammad Sofi 12 January 2022 11:00 | Change logic on show user rank based on total point
		$this->db->select_as("COALESCE(SUM($this->tbl_as.total_post),0)", "total_post", 0);
		$this->db->select_as("COALESCE(SUM($this->tbl_as.total_point),0)", "total_point", 0);
		$this->db->select_as("CONCAT($this->tbl_as.b_user_alamat_location_kelurahan,', ',$this->tbl_as.b_user_alamat_location_kecamatan,', ',$this->tbl_as.b_user_alamat_location_kabkota)", "general_location", 0);
		$this->db->select_as($this->__decrypt("$this->tbl3_as.alamat2"), "address2", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		// $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner"); // by Muhammad Sofi 20 December 2021 12:00 | remove g_leaderboard_point_ranking
		$this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
		$this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl2_as.is_active", $this->db->esc(1));
		
		// by Muhammad Sofi 11 January 2022 10:14 | get postal district if it's not 00 code
        if($location != ''){
            // $this->db->where_as("$this->tbl_as.b_user_alamat_location_postal_district", $this->db->esc($location));
			$this->db->where_as("$this->tbl_as.b_user_alamat_location_kelurahan", $this->db->esc($location));
        }

		// by Muhammad Sofi 11 January 2022 18:28 | add search by keyword
		if (strlen($keyword)>0) {
			$this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl2_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 1);
        }

		// START by Muhammad Sofi 19 January 2022 11:50 | fix query to sort ranking by total point
		$this->db->group_by("$this->tbl_as.b_user_id", 0);
		$this->db->order_by("SUM($this->tbl_as.total_point)", "DESC");

        // switch ($sortCol) {
        //     case 0:
        //         $sortCol = "SUM($this->tbl_as.total_point)";
        //         break;
        //     case 0:
        //         $sortCol = "$this->tbl_as.id";
        //         break;
        //     default:
        //         $sortCol = "SUM($this->tbl_as.total_point)";
        //         break;
        // }
        // $this->db->order_by("SUM($this->tbl_as.total_point)", $sortDir)->limit($page, $pagesize);
        $this->db->limit($page, $pagesize);
		// END by Muhammad Sofi 19 January 2022 11:50 | fix query to sort ranking by total point
        return $this->db->get("object", 0);
    }

	// original 
	// get general location
	// public function getAllGeneralLocation($nation_code, $page=0, $pagesize=10, $keyword="", $is_active="") {
    //     $this->db->flushQuery();
	// 	$this->db->select_as("DISTINCT $this->tbl4_as.postal_district", "postal_district", 0);
	// 	$this->db->select_as("IF($this->tbl4_as.custom_name IS NULL OR $this->tbl4_as.custom_name = '', $this->tbl4_as.original_name, $this->tbl4_as.custom_name)", "general_location", 0);
	// 	$this->db->select_as("$this->tbl4_as.is_active", "is_active", 0);
    //     $this->db->from($this->tbl4, $this->tbl4_as);
    //     $this->db->where_as("$this->tbl4_as.nation_code", $nation_code, "AND", "=", 0, 0);

    //     if (mb_strlen($is_active)) {
    //         $this->db->where_as("$this->tbl4_as.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
    //     }

    //     if (strlen($keyword)>0) {
	// 		$this->db->where_as("IF($this->tbl4_as.custom_name IS NULL OR $this->tbl4_as.custom_name = '', $this->tbl4_as.original_name, $this->tbl4_as.custom_name)", addslashes($keyword), "OR", "%like%", 0, 0);
    //     }
    //     return $this->db->get("", 0);
    // }

	// get kodepos from g_highlight_community
	// get general location
	public function getAllGeneralLocation($nation_code, $page=0, $pagesize=10, $keyword="", $is_active="") {
        $this->db->flushQuery();
		$this->db->select_as("DISTINCT $this->tbl_as.b_user_alamat_location_kelurahan", "id", 0);
		$this->db->select_as("CONCAT($this->tbl_as.b_user_alamat_location_kelurahan,', ',$this->tbl_as.b_user_alamat_location_kecamatan,', ',$this->tbl_as.b_user_alamat_location_kabkota)", "general_location", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);

		if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl_as.b_user_alamat_location_kelurahan", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl_as.b_user_alamat_location_kecamatan", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.b_user_alamat_location_kabkota", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        return $this->db->get("", 0);
    }

    public function updateTotal($nation_code, $b_user_id, $location, $parameter, $operator, $total)
    {
        return $this->db->exec("UPDATE `$this->tbl` SET $parameter = $parameter $operator $total
            WHERE nation_code = '$nation_code' AND b_user_id = '$b_user_id' AND b_user_alamat_location_kelurahan = '$location';");
    }
    
}