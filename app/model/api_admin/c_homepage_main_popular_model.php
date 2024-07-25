<?php
class C_Homepage_Main_Popular_Model extends JI_Model{
	var $tbl = 'c_homepage_main_popular';
	var $tbl_as = 'chmp';
	var $tbl2 = 'i_group';
	var $tbl2_as = 'ig';
	var $tbl3 = 'c_community';
	var $tbl3_as = 'cc';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}
	
	public function getTableAlias(){
		return $this->tbl_as;
	}

	public function update($nation_code, $id, $du){
		$this->db->where("nation_code", $nation_code);
		$this->db->where("id", $id);
		return $this->db->update($this->tbl, $du, 0);
	}

	public function getById($nation_code, $id) {
		$this->db->where("nation_code", $nation_code);
		$this->db->where("id", $id);
		return $this->db->get_first();
	}

	public function set($di){
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl, $di, 0, 0);
	}

	private function __joinTbl_Popular_With_Club()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.custom_id", "=", "$this->tbl2_as.id");
        return $cps;
    }

	private function __joinTbl_Popular_With_Community()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.custom_id", "=", "$this->tbl3_as.id");
        return $cps;
    }

	public function getAllPopularClub($nation_code, $page=0, $pagesize=10, $sortCol="cdate", $sortDir="", $keyword="") {
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY priority)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		// $this->db->select_as("$this->tbl_as.id", "no", 0);
		$this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl2_as.name", "name", 0);
        $this->db->select_as("$this->tbl_as.is_active", "status_active", 0);
        $this->db->select_as("$this->tbl_as.priority", "priority", 0);
        $this->db->select_as("$this->tbl_as.start_date", "start_date", 0);
        $this->db->select_as("$this->tbl_as.end_date", "end_date", 0);
        $this->db->select_as("$this->tbl_as.a_pengguna_id", "a_pengguna_id", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.custom_id", "custom_id", 0);
        $this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl_Popular_With_Club(), "left");
		$this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.type", $this->db->esc("club"), "AND", "=", 0, 0);
		if(mb_strlen($keyword)>0) {
			$this->db->where_as("$this->tbl2_as.name", addslashes($keyword), "OR", "%like%", 1, 1);
		}
		$this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
		return $this->db->get("object", 0);
	}

	public function countAllPopularClub($nation_code, $keyword="") {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl_Popular_With_Club(), "left");
		$this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.type", $this->db->esc("club"), "AND", "=", 0, 0);
		if(mb_strlen($keyword)>0) {
			$this->db->where_as("$this->tbl2_as.name", addslashes($keyword), "OR", "%like%", 1, 1);
		}
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function getClubList($nation_code, $keyword="", $is_active="") {
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl2_as.id", "id", 0);
		$this->db->select_as("$this->tbl2_as.name", "name", 0);
		$this->db->from($this->tbl2, $this->tbl2_as);
		$this->db->where_as("$this->tbl2_as.nation_code", $nation_code, "AND", "=", 0, 0);

		if (mb_strlen($is_active)) {
            $this->db->where_as("$this->tbl2_as.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
        }

        if (mb_strlen($keyword)>0) {
            $this->db->where_as("$this->tbl2_as.name", addslashes($keyword), "OR", "%like%", 0, 0);
        }

		$this->db->order_by("$this->tbl2_as.total_people", "DESC")->limit(0, 10);
        return $this->db->get("object", 0);
	}

	public function getAllPopularCommunityPost($nation_code, $page=0, $pagesize=10, $sortCol="cdate", $sortDir="", $keyword="") {
		$this->db->flushQuery();
		// $this->db->select_as("ROW_NUMBER() OVER (ORDER BY priority)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		$this->db->select_as("$this->tbl_as.id", "no", 0);
		$this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl3_as.title", "title", 0);
		$this->db->select_as("$this->tbl_as.is_active", "status_active", 0);
        $this->db->select_as("$this->tbl_as.priority", "priority", 0);
		$this->db->select_as("$this->tbl_as.start_date", "start_date", 0);
        $this->db->select_as("$this->tbl_as.end_date", "end_date", 0);
        $this->db->select_as("$this->tbl_as.a_pengguna_id", "a_pengguna_id", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.custom_id", "custom_id", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl_Popular_With_Community(), "left");
		$this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.type", $this->db->esc("community"), "AND", "=", 0, 0);
		if(mb_strlen($keyword)>0) {
			$this->db->where_as("$this->tbl3_as.title", addslashes($keyword), "OR", "%like%", 1, 1);
		}
		$this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
		return $this->db->get("object", 0);
	}

	public function countAllPopularCommunityPost($nation_code, $keyword="") {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl_Popular_With_Club(), "left");
		$this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.type", $this->db->esc("community"), "AND", "=", 0, 0);
		if(mb_strlen($keyword)>0) {
			$this->db->where_as("$this->tbl3_as.title", addslashes($keyword), "OR", "%like%", 1, 1);
		}
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function getCommunityList($nation_code, $keyword="", $is_active="") {
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl3_as.id", "id", 0);
		$this->db->select_as("$this->tbl3_as.title", "title", 0);
		$this->db->from($this->tbl3, $this->tbl3_as);
		$this->db->where_as("$this->tbl3_as.nation_code", $nation_code, "AND", "=", 0, 0);

		if (mb_strlen($is_active)) {
            $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
        }

        if (mb_strlen($keyword)>0) {
            $this->db->where_as("$this->tbl3_as.title", addslashes($keyword), "OR", "%like%", 0, 0);
        }

		$this->db->order_by("$this->tbl3_as.title", "ASC");
        return $this->db->get("object", 0);
	}

	public function checkPopularClubAlreadyRegistered($custom_id) {
		$this->db->where("custom_id", $custom_id);
		return $this->db->get_first('', 0);
	}

	public function updateBy($nation_code, $priority, $du){
		$this->db->where("nation_code", $nation_code);
		$this->db->where("priority", $priority);
		return $this->db->update($this->tbl, $du, 0);
	}

	public function getAllData($nation_code, $type) {
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl3_as.title", "title", 0);
        $this->db->select_as("$this->tbl_as.priority", "priority", 0);
        $this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as("$this->tbl_as.a_pengguna_id", "a_pengguna_id", 0);
        $this->db->select_as("$this->tbl_as.custom_id", "custom_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl_Popular_With_Community(), "left");
		$this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.type", $this->db->esc($type), "AND", "=", 0, 0);
		$this->db->order_by("$this->tbl_as.priority", "ASC");
		return $this->db->get("object", 0);
	}

	public function checkDuplicatePriorityAndStartEndDate($nation_code, $priority, $start_date, $end_date, $type="") {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.priority", $this->db->esc($priority), "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.start_date", $this->db->esc($start_date), "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.end_date", $this->db->esc($end_date), "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.is_active", $this->db->esc("1"), "AND", "=", 0, 0);
		if($type == "community") {
			$this->db->where_as("$this->tbl_as.type", $this->db->esc("community"), "AND", "=", 0, 0);
		} else if($type == "club") {
			$this->db->where_as("$this->tbl_as.type", $this->db->esc("club"), "AND", "=", 0, 0);
		}
		$d = $this->db->get_first("object", 0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
}