<?php
class G_Leaderboard_Point_History_Model extends JI_Model {
    public $tbl = 'g_leaderboard_point_history';
    public $tbl_as = 'glph';
    public $tbl2 = 'b_user';
    public $tbl2_as = 'bu';
    public $tbl3 = 'b_user_alamat';
    public $tbl3_as = 'bua';
    // public $tbl4 = 'b_user_alamat_location';
    // public $tbl4_as = 'bual';

    public function __construct() {
        parent::__construct();
        $this->db->from($this->tbl, $this->tbl_as);
    }

    public function getTblAs() {
        return $this->tbl_as;
    }

    public function getTbl2As() {
        return $this->tbl2_as;
    }

    public function getTbl3As() {
        return $this->tbl3_as;
    }

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

    public function checkId($nation_code, $id)
    {
        $this->db->select_as("COUNT(*)", "jumlah");
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code);
        $this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    // public function getLastId($nation_code, $b_user_id, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta")
    // {
    //     // $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
    //     // $this->db->from($this->tbl, $this->tbl_as);
    //     // $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     // $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     // $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
    //     // $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
    //     // $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
    //     // $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
    //     // $d = $this->db->get_first('', 0);
    //     // if (isset($d->last_id)) {
    //     //     return (int) $d->last_id;
    //     // }
    //     // return 0;
    //     $sql ="SELECT COALESCE(MAX(id),0)+1 AS id FROM `".$this->tbl."` WHERE id >= (SELECT COALESCE(MAX(id),0) FROM `".$this->tbl."` WHERE nation_code = ".$this->db->esc($nation_code)." AND  b_user_id = ".$this->db->esc($b_user_id)." AND  LOWER(b_user_alamat_location_kelurahan) = ".$this->db->esc(strtolower($kelurahan))." AND  LOWER(b_user_alamat_location_kecamatan) = ".$this->db->esc(strtolower($kecamatan))." AND  LOWER(b_user_alamat_location_kabkota) = ".$this->db->esc(strtolower($kabkota))." AND  LOWER(b_user_alamat_location_provinsi) = ".$this->db->esc(strtolower($provinsi)).") AND nation_code = ".$this->db->esc($nation_code)." AND  b_user_id = ".$this->db->esc($b_user_id)." AND  LOWER(b_user_alamat_location_kelurahan) = ".$this->db->esc(strtolower($kelurahan))." AND  LOWER(b_user_alamat_location_kecamatan) = ".$this->db->esc(strtolower($kecamatan))." AND  LOWER(b_user_alamat_location_kabkota) = ".$this->db->esc(strtolower($kabkota))." AND  LOWER(b_user_alamat_location_provinsi) = ".$this->db->esc(strtolower($provinsi))." FOR UPDATE;";
    //     return $this->db->query($sql)[0]->id;
    // }

    // public function getLastIdLeaderboardPointHistory($nation_code, $b_user_id, $kelurahan="All", $kecamatan="All", $kabkota="All", $provinsi="DKI Jakarta")
    // {
    //     $this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
    //     $this->db->from($this->tbl, $this->tbl_as);
    //     $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
    //     $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
    //     $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kelurahan)", $this->db->esc(strtolower($kelurahan)));
    //     $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kecamatan)", $this->db->esc(strtolower($kecamatan)));
    //     $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_kabkota)", $this->db->esc(strtolower($kabkota)));
    //     $this->db->where_as("LOWER($this->tbl_as.b_user_alamat_location_provinsi)", $this->db->esc(strtolower($provinsi)));
    //     $d = $this->db->get_first();
    //     if (isset($d->last_id)) {
    //         return (int) $d->last_id;
    //     }
    //     return 0;
    // }

    public function set($di) {
        if (!is_array($di)) { return 0; }
        return $this->db->insert($this->tbl, $di, 0, 0);
    }

    public function countAll($nation_code, $keyword="",  $startDate="", $location="") {
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		
		if($startDate && $startDate !== "") { 
			$this->db->where("$this->tbl_as.cdate", $startDate, "AND", "%like%", 1, 1);
		}

		if (strlen($location)>0) {
            $this->db->where_as("$this->tbl_as.b_user_alamat_location_kelurahan", $this->db->esc($location));
        }
		
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

	public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="sku", $sortDir="DESC", $keyword="", $startDate="", $location="00000") {
        $this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate DESC)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		$this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		$this->db->select_as($this->__decrypt("$this->tbl2_as.fnama"), "user_name", 0);
		$this->db->select_as("$this->tbl_as.point", "point", 0);
		$this->db->select_as("$this->tbl_as.custom_text", "custom_text", 0);
        // $this->db->select_as("IF($this->tbl4_as.custom_name IS NULL OR $this->tbl4_as.custom_name = '', $this->tbl4_as.original_name, $this->tbl4_as.custom_name)", "general_location", 0);
        $this->db->select_as("CONCAT($this->tbl_as.b_user_alamat_location_kelurahan,', ',$this->tbl_as.b_user_alamat_location_kecamatan,', ',$this->tbl_as.b_user_alamat_location_kabkota)", "general_location", 0);
        $this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl3_as.alamat2"), "address2", 0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
		$this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");

        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);

		if($startDate && $startDate !== "") { 
			$this->db->where("$this->tbl_as.cdate", $startDate, "AND", "%like%", 1, 1);
		}	

		// by Muhammad Sofi 11 January 2022 10:14 | get postal district if it's not 00 code
        if($location != ''){
            // $this->db->where_as("$this->tbl_as.b_user_alamat_location_postal_district", $this->db->esc($location));
            $this->db->where_as("$this->tbl_as.b_user_alamat_location_kelurahan", $this->db->esc($location));
        }

        // $this->db->group_by("$this->tbl_as.id"); 

        switch ($sortCol) {
            case 0:
                $sortCol = "glph.cdate";
                break;
            case 1:
                $sortCol = "glph.point";
                break;
            default:
                $sortCol = "glph.cdate";
                break;
        }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get("object", 0);
    }

    public function getLeaderboardDataByCustomId($nation_code, $custom_id) {
        $this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl_as.point", "point", 0);
		$this->db->select_as("$this->tbl_as.custom_text", "custom_text", 0);
		$this->db->select_as("$this->tbl_as.custom_type", "custom_type", 0);
		$this->db->select_as("$this->tbl_as.custom_type_sub", "custom_type_sub", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id), "AND", "=", 0, 0);
        return $this->db->get("object", 0);
    }

    public function countPointCustomType($nation_code="", $b_user_id="", $date_min_1="", $date="",  $custom_type="", $custom_type_sub="") {
        $this->db->select_as("sum(concat(plusorminus, point))", "jumlah", 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id), "AND", "=", 0, 0);
        // $this->db->where_as("date($this->tbl_as.cdate)", $this->db->esc($date), "AND", "=", 0, 0);
        $this->db->between("DATE($this->tbl_as.cdate)", "DATE('$date_min_1')", "DATE('$date')");
        $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc($custom_type), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc($custom_type_sub), "AND", "=", 0, 0);
		
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

    public function getRecordGroupPost($nation_code, $custom_id)
    {
        $this->db->select_as("*, $this->tbl_as.cdate", "cdate", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
        $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc("club"));
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc("post"), "OR", "=", 1, 0);
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc("upload image"), "OR", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc("upload video"), "OR", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc("attendance sheet"), "OR", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc("location"), "OR", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc("commission"), "AND", "=", 0, 1);
        return $this->db->get('object', 0);
    }

    public function getRecordGroup($nation_code, $custom_id)
    {
        $this->db->select_as("*, $this->tbl_as.cdate", "cdate", 0);

        $this->db->from($this->tbl, $this->tbl_as);

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.custom_id", $this->db->esc($custom_id));
        $this->db->where_as("$this->tbl_as.custom_type", $this->db->esc("club"));
        $this->db->where_as("$this->tbl_as.custom_type_sub", $this->db->esc("create club"), "OR", "=", 0, 0);
        return $this->db->get_first('object', 0);
    }
}