<?php
class G_Highlight_Community_Model extends SENE_Model {
	var $tbl = 'g_highlight_community';
	var $tbl_as = 'ghc';
	var $tbl2 = 'c_community';
	var $tbl2_as = 'comm_list';
	var $tbl3 = 'b_user';
	var $tbl3_as = 'master_user';
	// var $tbl5 = 'b_user_alamat_location';
	// var $tbl5_as = 'alamat_location';
	var $tbl6 = 'c_community_category';
	var $tbl6_as = 'comm_category';
	var $tbl7 = 'g_general_location_highlight_status';
	var $tbl7_as = 'gglhs';

	public function __construct(){
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

	public function getTableAlias4(){
		return $this->tbl4_as;
	}

	// public function getTableAlias5(){
	// 	return $this->tbl5_as;
	// }

    private function __joinTbl2() {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.c_community_id", "=", "$this->tbl2_as.id");
        return $cps;
    }

    private function __joinTbl3() {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl2_as.b_user_id", "=", "$this->tbl3_as.id");
        return $cps;
    }

	// by Muhammad Sofi 27 December 2021 13:51 | delete unused code

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

	public function getLastId($nation_code){
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}

	// public function getPostalDistrict($nation_code, $id) {
	public function getLocation($nation_code, $id) {
		$this->db->select_as("$this->tbl2_as.id", "c_community_id", 0);
		// $this->db->select_as("$this->tbl5_as.postal_district", "postal_district", 0);
		$this->db->select_as("$this->tbl2_as.kelurahan", "kelurahan", 0);
		$this->db->select_as("$this->tbl2_as.kecamatan", "kecamatan", 0);
		$this->db->select_as("$this->tbl2_as.kabkota", "kabkota", 0);
		$this->db->select_as("$this->tbl2_as.provinsi", "provinsi", 0);
		$this->db->from($this->tbl2, $this->tbl2_as);
		$this->db->where_as("$this->tbl2_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl2_as.id", $this->db->esc($id));
		return $this->db->get_first('', 0);
	}

	public function set($di){
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl,$di,0,0);
	}

	public function updatesetInactive($nation_code, $id, $du){
		if(!is_array($du)) return 0;
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
    	return $this->db->update($this->tbl,$du,0);
	}

	public function del($nation_code, $id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
		return $this->db->delete($this->tbl);
	}

	// change automatic to manual if there is change on selected highlight post in that general location
	// public function updateManualSystem($nation_code, $postal_district, $di){
	// 	if(!is_array($di)) return 0;
	// 	$this->db->where("nation_code",$nation_code);
	// 	$this->db->where("b_user_alamat_location_postal_district", $postal_district);
    // 	return $this->db->update($this->tbl7, $di, 0);
	// }

	// change manual to automatic system
	// public function updateAutomaticSystem($nation_code, $b_user_alamat_location_postal_district, $status) {
    //     return $this->db->exec("UPDATE `$this->tbl7` SET status = '$status'
    //         WHERE nation_code = '$nation_code' AND b_user_alamat_location_postal_district = '$b_user_alamat_location_postal_district'");
    // }

	public function updateByPriorityDesc($nation_code, $b_user_alamat_location_kelurahan='', $limit) {
        return $this->db->exec("UPDATE `$this->tbl` SET is_active = 0
            WHERE nation_code = '$nation_code' AND b_user_alamat_location_kelurahan = '$b_user_alamat_location_kelurahan' AND is_active = 1  ORDER BY priority desc LIMIT $limit;");
    }

    public function updatePriority($nation_code, $b_user_alamat_location_kelurahan='', $operator, $total) {
        return $this->db->exec("UPDATE `$this->tbl` SET priority = priority $operator $total
            WHERE nation_code = '$nation_code' AND b_user_alamat_location_kelurahan = '$b_user_alamat_location_kelurahan' AND is_active = 1;");
    }

	// public function getById($nation_code, $id) {   
    //     $this->db->exec("SET NAMES 'UTF8MB4' COLLATE 'utf8mb4_unicode_ci'");
    //     $this->db->select_as("$this->tbl_as.id", "id", 0);
    //     $this->db->select_as("$this->tbl_as.c_community_id", "c_community_id", 0);
    //     $this->db->select_as("$this->tbl_as.b_user_alamat_location_postal_kelurahan", "postal_district", 0);
	// 	$this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
	// 	$this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
    //     return $this->db->get_first('', 0);
	// }

	public function countAll($nation_code, $keyword="",  $startDate="", $location='', $is_active="") {
        $this->db->flushQuery();
        $this->db->exec("SET NAMES 'UTF8MB4' COLLATE 'utf8mb4_unicode_ci'");
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		
		if($startDate && $startDate !== "") { 
			$this->db->where("$this->tbl_as.start_date", $startDate, "AND", "%like%", 1, 1);
		}

		// by Muhammad Sofi 21 January 2022 17:59 | bug fix when change location ALL, got error show 0 to 0 entries
		// by Muhammad Sofi 21 January 2022 22:46 | bug fix on filter data
		// if ($location == '00') {
        //     // $this->db->where_as("SELECT * FROM g_highlight_community");
        // } else {
		$this->db->where_as("$this->tbl_as.b_user_alamat_location_kelurahan", $this->db->esc($location));
		// }
	
		if (strlen($is_active)>0) {
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc($is_active));
        }

		if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl2_as.title", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl2_as.deskripsi", addslashes($keyword), "OR", "%like%", 0, 1);
        }
		
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

	public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="sku", $sortDir="ASC", $keyword="", $startDate="", $location='', $is_active="") {
        $this->db->flushQuery();
		// by Muhammad Sofi 22 December 2021 18:16 | change charset to show emoji
		$this->db->exec("SET NAMES 'UTF8MB4' COLLATE 'utf8mb4_unicode_ci'");
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY priority ASC)", "no"); // by Muhammad Sofi 23 December 2021 10:00 | show row number
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.start_date", "start_date", 0);
		$this->db->select_as("$this->tbl2_as.title", "title", 0);
		$this->db->select_as("$this->tbl2_as.deskripsi", "description", 0);
		$this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "user", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->select_as("CONCAT($this->tbl_as.b_user_alamat_location_kelurahan,', ',$this->tbl_as.b_user_alamat_location_kecamatan,', ',$this->tbl_as.b_user_alamat_location_kabkota)", "general_location", 0);
		$this->db->select_as("$this->tbl_as.priority", "priority", 0);
		$this->db->select_as($this->__decrypt("$this->tbl2_as.alamat2"), "address2", 0);
		$this->db->select_as("$this->tbl_as.b_user_alamat_location_kelurahan", "kelurahan", 0);
		$this->db->select_as("$this->tbl_as.b_user_alamat_location_kecamatan", "kecamatan", 0);
		$this->db->select_as("$this->tbl_as.b_user_alamat_location_kabkota", "kabkota", 0);
		$this->db->select_as("$this->tbl_as.b_user_alamat_location_provinsi", "provinsi", 0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
		$this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
		// $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl11(), "left");

        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		// $this->db->where_as("DATEDIFF($this->tbl_as.end_date, $this->tbl_as.start_date)", "1", "AND", "=", 0, 0); // by Muhammad Sofi 10 January 2022 13:58 | data highlight community not show
		
		if($startDate && $startDate !== "") { 
			$this->db->where("$this->tbl_as.start_date", $startDate, "AND", "%like%", 1, 1);
		}	
        
		// by Muhammad Sofi 11 January 2022 10:14 | get postal district if it's not 00 code
		// by Muhammad Sofi 21 January 2022 22:46 | bug fix on filter data
		// if ($location == '00') {
        //     // $this->db->where_as("SELECT * FROM g_highlight_community WHERE is_active = '1'");
        // } else {
		$this->db->where_as("$this->tbl_as.b_user_alamat_location_kelurahan", $this->db->esc($location));
		// }

        if (strlen($is_active)>1) {
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc($is_active));
        }

        if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl2_as.title", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl2_as.deskripsi", addslashes($keyword), "OR", "%like%", 0, 1);
        }

        $this->db->group_by("$this->tbl_as.id");

        switch ($sortCol) {
            case 0:
                $sortCol = "ghc.start_date";
                break;
            case 1:
                $sortCol = "ghc.id";
                break;
			case 1:
				$sortCol = "ghc.is_active";
				break;
            default:
                $sortCol = "ghc.start_date";
                break;
        }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get("object", 0);
    }

	public function countAllCommunity($nation_code, $keyword="", $location='00', $is_active="") {
		$this->db->flushQuery();
		$this->db->exec("SET NAMES 'UTF8MB4' COLLATE 'utf8mb4_unicode_ci'");
		$this->db->select_as("COUNT(*)", "jumlah", 0);
		$this->db->from($this->tbl2, $this->tbl2_as);
		// START by Muhammad Sofi 17 January 2022 15:42 | fix function count data
		$this->db->where_as("$this->tbl2_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl2_as.id", "(SELECT $this->tbl.c_community_id FROM $this->tbl WHERE $this->tbl.is_active = 1)", "AND", "notin", 0, 0);

		// if($location != 00){
        //     $this->db->where_as("$this->tbl5_as.postal_district", $this->db->esc($location));
        // }

		// if (strlen($location)>0) {
        //     $this->db->where_as("$this->tbl5_as.postal_district", $this->db->esc($location));
        // }

		// END by Muhammad Sofi 17 January 2022 15:42 | fix function count data
	
		if (strlen($is_active)>0) {
            $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc($is_active));
        }

		if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl2_as.title", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl2_as.deskripsi", addslashes($keyword), "OR", "%like%", 0, 1);
        }
		
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	// by Muhammad Sofi 27 December 2021 12:04 | add comp "notin" for WHERE NOT IN
	// get data from community_list
	public function getAllCommunity($nation_code, $page=0,$pagesize=10,$sortCol="sku",$sortDir="ASC", $keyword="", $location='00', $is_active="") {
		$this->db->flushQuery();
		$this->db->exec("SET NAMES 'UTF8MB4' COLLATE 'utf8mb4_unicode_ci'");
		$this->db->select_as("$this->tbl2_as.id", "id", 0);
		$this->db->select_as("$this->tbl2_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl2_as.title", "title", 0);
		$this->db->select_as("$this->tbl2_as.deskripsi", "description", 0);
		$this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "user", 0);
		$this->db->select_as("$this->tbl2_as.is_active", "is_active", 0);
		$this->db->select_as($this->__decrypt("$this->tbl2_as.alamat2"), "address2", 0);
		// $this->db->select_as("CAST(SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2")."))) AS CHAR(22))", "address2", 0);
		$this->db->select_as("$this->tbl2_as.kelurahan", "kelurahan", 0);
		$this->db->select_as("$this->tbl2_as.kecamatan", "kecamatan", 0);
		$this->db->select_as("$this->tbl2_as.kabkota", "kabkota", 0);
		$this->db->select_as("$this->tbl2_as.provinsi", "provinsi", 0);
		$this->db->from($this->tbl2, $this->tbl2_as);
		$this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
		$this->db->where_as("$this->tbl2_as.id", "(SELECT $this->tbl.c_community_id FROM $this->tbl WHERE $this->tbl.is_active = 1)", "AND", "notin", 0, 0);
		$this->db->where_as("$this->tbl2_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl2_as.is_active", $this->db->esc('1'));

        // if (strlen($location)>0) {
        //     $this->db->where_as("$this->tbl5_as.postal_district", $this->db->esc($location));
        // }

		// by Muhammad Sofi 11 January 2022 10:14 | get postal district if it's not 00 code
        // if($location != 00){
        //     $this->db->where_as("$this->tbl5_as.postal_district", $this->db->esc($location));
        // }

		// if (strlen($location)>0) {
        //     $this->db->where_as("$this->tbl5_as.postal_district", $this->db->esc($location));
        // }

        if (strlen($is_active)>1) {
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc($is_active));
        }

        if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl2_as.title", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl2_as.deskripsi", addslashes($keyword), "OR", "%like%", 0, 1);
        }

        $this->db->group_by("$this->tbl2_as.id");
        switch ($sortCol) {
            case 0:
                $sortCol = "$this->tbl2_as.cdate";
                break;
            case 1:
                $sortCol = "$this->tbl2_as.id";
                break;
			case 1:
				$sortCol = "$this->tbl2_as.is_active";
				break;
            default:
                $sortCol = "$this->tbl2_as.cdate";
                break;
        }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get("object", 0);
    }

	// using default query
	// public function getAllCommunity($nation_code, $page=0,$pagesize=10,$sortCol="sku",$sortDir="ASC", $keyword="", $location="", $is_active=""){
	// 	$sql = "SELECT
	// 		c_community.id as id,
	// 		c_community.cdate as cdate,
	// 		c_community.title as title,
	// 		c_community.deskripsi as description,
	// 		".$this->__decrypt("b_user.fnama")." as user,
	// 		c_community.is_active as is_active,
	// 		IF(b_user_alamat_location.custom_name IS NULL OR b_user_alamat_location.custom_name = '', b_user_alamat_location.original_name, b_user_alamat_location.custom_name) as general_location,
	// 		b_user_alamat_location.postal_district as postal_district,
	// 		c_community.ldate as ldate,
	// 		".$this->__decrypt("c_community.alamat2")." as address2,
	// 		c_community.is_report as is_report,
	// 		c_community.is_take_down as is_take_down
	// 		FROM c_community 
	// 		LEFT JOIN b_user_alamat_location
	// 					ON c_community.nation_code = b_user_alamat_location.nation_code AND
	// 					SUBSTR(c_community.kodepos,1,2) = b_user_alamat_location.postal_sector
	// 		INNER JOIN c_community_category
	// 					ON c_community.nation_code = c_community_category.nation_code AND
	// 					c_community.c_community_category_id = c_community_category.id
	// 		INNER JOIN b_user
	// 					ON c_community.nation_code = b_user.nation_code AND
	// 					c_community.b_user_id = b_user.id
	// 		WHERE
	// 			c_community.id NOT IN (SELECT g_highlight_community.c_community_id FROM g_highlight_community WHERE g_highlight_community.is_active = 1) AND
	// 			c_community.nation_code = '62' AND c_community.is_active = '1'
	// 	";

	// 	if (strlen($location)>0) {
	// 		$sql .= "AND b_user_alamat_location.postal_district = ".$this->db->esc($location)." ";
	// 	}

	// 	if (strlen($is_active)>1) {
    //         $sql .= "AND c_community.is_active = ".$this->db->esc($is_active)." ";
    //     }

	// 	if (strlen($keyword)>0) {
	// 		$sql .= "AND (c_community.title LIKE '%$keyword%' OR c_community.deskripsi LIKE '%$keyword%')";
    //     }

	// 	$sql .= " ORDER BY c_community.id ".$sortDir." LIMIT
    //     ".$page.", ".$pagesize;

	// 	return $this->db->query($sql);
	// }

	// original
	// get general location
	// public function getAllGeneralLocation($nation_code, $page=0, $pagesize=10, $keyword="", $is_active="") {
    //     $this->db->flushQuery();
	// 	$this->db->select_as("DISTINCT $this->tbl5_as.postal_district", "postal_district", 0);
	// 	$this->db->select_as("IF($this->tbl5_as.custom_name IS NULL OR $this->tbl5_as.custom_name = '', $this->tbl5_as.original_name, $this->tbl5_as.custom_name)", "general_location", 0);
	// 	$this->db->select_as("$this->tbl5_as.is_active", "is_active", 0);
    //     $this->db->from($this->tbl5, $this->tbl5_as);
    //     $this->db->where_as("$this->tbl5_as.nation_code", $nation_code, "AND", "=", 0, 0);

    //     if (mb_strlen($is_active)) {
    //         $this->db->where_as("$this->tbl5_as.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
    //     }

    //     if (strlen($keyword)>0) {
	// 		$this->db->where_as("IF($this->tbl5_as.custom_name IS NULL OR $this->tbl5_as.custom_name = '', $this->tbl5_as.original_name, $this->tbl5_as.custom_name)", addslashes($keyword), "OR", "%like%", 0, 0);
    //     }
    //     return $this->db->get("", 0);
    // }

	// get general location
	public function getAllGeneralLocation($nation_code, $page=0, $pagesize=10, $keyword="", $is_active="") {
        $this->db->flushQuery();
		$this->db->select_as("DISTINCT $this->tbl_as.b_user_alamat_location_kelurahan", "id", 0);
		$this->db->select_as("CONCAT($this->tbl_as.b_user_alamat_location_kelurahan,', ', $this->tbl_as.b_user_alamat_location_kecamatan,', ', $this->tbl_as.b_user_alamat_location_kabkota)", "general_location", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);

        if (mb_strlen($is_active)) {
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
        }

		if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl_as.b_user_alamat_location_kelurahan", addslashes($keyword), "OR", "%like%", 1, 0);
            $this->db->where_as("$this->tbl_as.b_user_alamat_location_kecamatan", addslashes($keyword), "OR", "%like%", 0, 0);
            $this->db->where_as("$this->tbl_as.b_user_alamat_location_kabkota", addslashes($keyword), "OR", "%like%", 0, 1);
        }
        return $this->db->get("", 0);
    }

	// count to limit inserting to 10 highlight post based on kelurahan
	public function countPostalDistrict($nation_code, $kelurahan='') {
        $this->db->select_as("COUNT($this->tbl_as.b_user_alamat_location_kelurahan)", "jumlahPostalDistrict", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1')); 
        $this->db->where_as("$this->tbl_as.b_user_alamat_location_kelurahan", $this->db->esc($kelurahan), "AND", "=", 0, 0);
		// $this->db->where_as("DATEDIFF($this->tbl_as.end_date, $this->tbl_as.start_date)", "1", "AND", "=", 0, 0);
		// by Muhammad Sofi 11 January 2022 10:14 | change date diff
		$this->db->where_as("$this->tbl_as.start_date", $this->db->esc(date('Y-m-d')),'AND','<=');
        $this->db->where_as("$this->tbl_as.end_date", $this->db->esc(date('Y-m-d')),'AND','>=');
		return $this->db->get_first("object", 0);
    } 

	// by Muhammad Sofi - 13 December 2021 15:42 | add checking to set priority before insert data
	public function countAllByLocation($nation_code, $location='') {
        $this->db->exec("SET NAMES 'UTF8MB4'");
        $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc('1')); 
        $this->db->where_as("$this->tbl_as.b_user_alamat_location_kelurahan", $this->db->esc($location));
        $this->db->where_as("$this->tbl_as.start_date", $this->db->esc(date('Y-m-d')),'AND','<=');
        $this->db->where_as("$this->tbl_as.end_date", $this->db->esc(date('Y-m-d')),'AND','>=');

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }
}
