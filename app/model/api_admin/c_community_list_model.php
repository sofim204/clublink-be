<?php
class C_Community_List_Model extends JI_Model{
	var $tbl = 'c_community';
	var $tbl_as = 'community_list';
	var $tbl2 = 'c_community_category';
	var $tbl2_as = 'comm_category';
	var $tbl3 = 'b_user';
	var $tbl3_as = 'master_user';
	var $tbl4 = 'b_user_alamat';
	var $tbl4_as = 'user_address';
	var $tbl5 = 'c_community_report';
	var $tbl5_as = 'comm_report';
	var $tbl6 = 'b_user';
	var $tbl6_as = 'reported_user';
	var $tbl7 = 'c_community_discussion';
	var $tbl7_as = 'community_discussion_list';
	var $tbl8 = 'c_community_discussion_report';
	var $tbl8_as = 'discuss_report';
	var $tbl9 = 'b_user';
	var $tbl9_as = 'reported_discussion_user';
	var $tbl10 = 'c_community_attachment';
	var $tbl10_as = 'cca';
	var $tbl11 = 'b_user';
	var $tbl11_as = 'reporter_user';
	var $tbl_community_attachment = 'c_community_attachment';
	var $tbl_community_attachment_as = 'cca';
	var $tbl_leaderboard_point_history = 'g_leaderboard_point_history';
	var $tbl_leaderboard_point_history_as = 'glph';

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

	public function getTableAlias5(){
		return $this->tbl5_as;
	}

    private function __joinTbl2()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.c_community_category_id", "=", "$this->tbl2_as.id");
        return $cps;
    }

    private function __joinTbl3()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl3_as.id");
        return $cps;
    }

	// by Muhammad Sofi - 12 November 2021 16:13 | not used
    // private function __joinTbl4()
    // {
    //     $cps = array();
    //     $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
    //     $cps[] = $this->db->composite_create("$this->tbl_as.b_user_alamat_id", "=", "$this->tbl4_as.id");
    //     $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl4_as.b_user_id");
    //     return $cps;
    // }

    // private function __joinTbl5()
    // {
    //     $cps = array();
    //     $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl5_as.nation_code");
    //     $cps[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl5_as.c_community_id");
    //     return $cps;
    // }

    private function __joinTbl5()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl5_as.nation_code", "=", "$this->tbl_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl5_as.c_community_id", "=", "$this->tbl_as.id");
        return $cps;
    }
	
	private function __joinTbl6()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl6_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl6_as.id");
        return $cps;
    }

	// private function __joinTbl7()
    // {
    //     $cps = array();
    //     $cps[] = $this->db->composite_create("$this->tbl5_as.nation_code", "=", "$this->tbl7_as.nation_code");
    //     $cps[] = $this->db->composite_create("$this->tbl5_as.b_user_id", "=", "$this->tbl7_as.b_user_id");
    //     return $cps;
    // }

	// join with table c_community to get title and description
	private function __joinTbl7(){
		$cps = array();
        $cps[] = $this->db->composite_create("$this->tbl5_as.nation_code", "=", "$this->tbl_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl5_as.c_community_id", "=", "$this->tbl_as.id");
        return $cps;
	}

	// join with table c_community_discussion to get text of discussion
	private function __joinTbl8(){
		$cps = array();
        $cps[] = $this->db->composite_create("$this->tbl8_as.nation_code", "=", "$this->tbl7_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl8_as.c_community_discussion_id", "=", "$this->tbl7_as.id");
        return $cps;
	}

	// get user name from community discussion table
	private function __joinTbl9(){
		$cps = array();
        $cps[] = $this->db->composite_create("$this->tbl8_as.nation_code", "=", "$this->tbl9_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl8_as.b_user_id", "=", "$this->tbl9_as.id");
        return $cps;
	}

	private function __joinTbl10()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl10_as.nation_code", "=", "$this->tbl_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl10_as.c_community_id", "=", "$this->tbl_as.id");
        return $cps;
    }

	private function __joinTbl11()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl5_as.nation_code", "=", "$this->tbl11_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl5_as.b_user_id", "=", "$this->tbl11_as.id");
        return $cps;
    }

	private function __joinTbl12()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl5_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl5_as.b_user_id", "=", "$this->tbl4_as.b_user_id");
        $cps[] = $this->db->composite_create("1", "=", "$this->tbl4_as.is_default");
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

	public function getLastId($nation_code){
		$this->db->select_as("COALESCE(MAX($this->tbl_as.id),0)+1", "last_id", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$d = $this->db->get_first('',0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}

	public function getAll($nation_code, $page=0,$pagesize=10,$sortCol="sku",$sortDir="ASC", $keyword="", $fromDate="", $toDate="", $userId="", $statusFilter="active"){
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		$this->db->select_as("'-'", "url_thumb", 0);
		$this->db->select_as("$this->tbl_as.title", "title", 0);
		$this->db->select_as("$this->tbl_as.deskripsi", "description", 0);
		$this->db->select_as("$this->tbl2_as.nama", "category_name", 0); // by Muhammad Sofi 29 December 2021 11:34 | show community category on list and detail
        $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "user", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0);
		$this->db->select_as($this->__decrypt("$this->tbl3_as.email"), "email_user", 0);
        $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "creator_name", 0);

		$this->db->select_as("$this->tbl_as.ldate", "ldate", 0);
		// by Muhammad Sofi - 4 November 2021 10:00
        // remark code
        // $this->db->select_as($this->__decrypt("$this->tbl4_as.alamat"), "address", 0);
        // $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "address2", 0);
		$this->db->select_as("CONCAT($this->tbl_as.kelurahan,', ',$this->tbl_as.kecamatan,', ',$this->tbl_as.kabkota)", "address2", 0);
		// $this->db->select_as("CAST(SUBSTRING(".$this->__decrypt("$this->tbl_as.alamat2").", 1, IF(LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").") != 0, LOCATE(',', ".$this->__decrypt("$this->tbl_as.alamat2").")-1, CHAR_LENGTH(".$this->__decrypt("$this->tbl_as.alamat2")."))) AS CHAR(22))", "address2", 0);
		$this->db->select_as("$this->tbl_as.kelurahan", "kelurahan", 0);
		$this->db->select_as("$this->tbl_as.kecamatan", "kecamatan", 0);
		$this->db->select_as("$this->tbl_as.kabkota", "kabkota", 0);
		$this->db->select_as("$this->tbl_as.provinsi", "provinsi", 0);
		$this->db->select_as("$this->tbl_as.is_report", "is_report", 0);
		$this->db->select_as("$this->tbl_as.is_take_down", "is_take_down", 0);
		$this->db->select_as($this->__decrypt("$this->tbl3_as.email"), "email_creator", 0);
		$this->db->select_as("$this->tbl_as.total_likes", "total_likes", 0);

		$this->db->from($this->tbl,$this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        // $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left"); // by Muhammad Sofi - 12 November 2021 16:13 | not used

		$this->db->where("$this->tbl_as.nation_code",$nation_code,"AND","=",0,0);
		// $this->db->where("$this->tbl3_as.is_permanent_inactive","1","AND","=",0,0);
		$this->db->where("$this->tbl3_as.is_active","1","AND","=",0,0);

		// if($fromDate && $fromDate!=="") $this->db->where("$this->tbl_as.cdate",$fromDate,"AND",">=",1,1);
		// if($toDate && $toDate!=="") $this->db->where("$this->tbl_as.cdate",$toDate,"AND","<=",1,1);
		if($userId && $userId!=="") $this->db->where("$this->tbl_as.b_user_id",$userId,"AND","=",1,1);

		// START by Muhammad Sofi 16 February 2022 14:32 | change filter start date to end date
		if (strlen($fromDate)==10 && strlen($toDate)==10) {
			$this->db->between("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", "DATE('$toDate')");
		} else if (strlen($fromDate)==10 && strlen($toDate)!=10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", 'AND', '>=');
		} else if (strlen($fromDate)!=10 && strlen($toDate)==10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$toDate')", 'AND', '<=');
		}
		// END by Muhammad Sofi 16 February 2022 14:32 | change filter start date to end date
		
		switch ($statusFilter) {
			case "active":
				$this->db->where("$this->tbl_as.is_active","1","AND","=",1,1);
			break;
			case "inactive":
				$this->db->where("$this->tbl_as.is_active","1","AND","<>",1,1);
			break;
			case "reported":
				$this->db->where("$this->tbl_as.is_report","1","AND",">=",1,1);
			break;
			case "takedown":
				$this->db->where("$this->tbl_as.is_take_down","1","AND",">=",1,1);
			break;
			
			default:
				$this->db->where("$this->tbl_as.is_active","1","AND","=",1,1);
			break;
		}

		if(mb_strlen($keyword)>0){
			$this->db->where("$this->tbl_as.title",$keyword,"OR","%like%",1,0);
			$this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl3_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.deskripsi",$keyword,"OR","%like%",0,1);
		}

		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object", 0);
	}

	public function countAll($nation_code, $keyword="", $fromDate="", $toDate="", $userId="", $statusFilter=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
		$this->db->where("$this->tbl_as.nation_code",$nation_code,"AND","=",0,0);
		// $this->db->where("$this->tbl3_as.is_permanent_inactive","1","AND","=",0,0);
		$this->db->where("$this->tbl3_as.is_active","1","AND","=",0,0);
		// $this->db->where("$this->tbl3_as.is_permanent_inactive",$this->db->esc(1),"AND","=",0,0);
		// if($fromDate && $fromDate!=="") $this->db->where("$this->tbl_as.cdate",$fromDate,"AND",">=",1,1);
		// if($toDate && $toDate!=="") $this->db->where("$this->tbl_as.cdate",$toDate,"AND","<=",1,1);
		if($userId && $userId!=="") $this->db->where("$this->tbl_as.b_user_id",$userId,"AND","=",1,1);

		// START by Muhammad Sofi 16 February 2022 14:32 | change filter start date to end date
		if (strlen($fromDate)==10 && strlen($toDate)==10) {
			$this->db->between("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", "DATE('$toDate')");
		} else if (strlen($fromDate)==10 && strlen($toDate)!=10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$fromDate')", 'AND', '>=');
		} else if (strlen($fromDate)!=10 && strlen($toDate)==10) {
			$this->db->where_as("DATE($this->tbl_as.cdate)", "DATE('$toDate')", 'AND', '<=');
		}
		// END by Muhammad Sofi 16 February 2022 14:32 | change filter start date to end date
	
		switch ($statusFilter) {
			case "active":
				$this->db->where("$this->tbl_as.is_active","1","AND","=",1,1);
			break;
			case "inactive":
				$this->db->where("$this->tbl_as.is_active","1","AND","<>",1,1);
			break;
			case "reported":
				$this->db->where("$this->tbl_as.is_report","1","AND",">=",1,1);
			break;
			case "takedown":
				$this->db->where("$this->tbl_as.is_take_down","1","AND",">=",1,1);
			break;
			
			default:
				$this->db->where("$this->tbl_as.is_active","1","AND","=",1,1);
			break;
		}

		if(mb_strlen($keyword)>0){
			$this->db->where("$this->tbl_as.title",$keyword,"OR","%like%",1,0);
			$this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl3_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
			$this->db->where("$this->tbl_as.deskripsi",$keyword,"OR","%like%",0,1);
		}
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	// START by Muhammad Sofi 14 January 2022 18:09 | change query to get reported community post data
	public function getReportedPost($nation_code, $page=0, $pagesize=10, $sortCol="sku", $sortDir="ASC", $keyword="", $fromDate="", $toDate="", $userId="", $statusFilter="active"){
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate DESC)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		// $this->db->select_as("$this->tbl5_as.id", "id", 0); // comm_report_id
		$this->db->select_as("$this->tbl5_as.c_community_id", "c_community_id", 0); // comm_report_comm_id
		$this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0); // comm_b_user_id
		// $this->db->select_as("$this->tbl_as.id", "comm_comm_id", 0); // comm_comm_id
		$this->db->select_as("$this->tbl_as.is_take_down", "check_takedown", 0);
		$this->db->select_as("$this->tbl5_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl_as.title", "title", 0);
		$this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
		// $this->db->select_as("$this->tbl5_as.b_user_id", "b_user_id_reporter", 0);
		// $this->db->select_as($this->__decrypt("$this->tbl11_as.fnama"), "reporter_user_name", 0);
		$this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl6_as.fnama").",'-')", 'reported_post_owner', 0);
		$this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl11_as.fnama").",'-')", 'reporter_user_name', 0);
		$this->db->select_as("COALESCE($this->tbl5_as.admin_name, '0')", "admin_name", 0);

		$this->db->select_as("$this->tbl5_as.is_active", "is_active", 0);

		$this->db->select_as("COUNT($this->tbl5_as.c_community_id)", "total_reported_post", 0);
		
		// $this->db->select_as("$this->tbl_as.ldate", "ldate", 0);
        // $this->db->select_as("$this->tbl_as.is_published", "is_published", 0);
		$this->db->select_as("$this->tbl_as.is_report", "is_report", 0);
		// $this->db->select_as("$this->tbl_as.report_date", "report_date", 0);
		$this->db->select_as("$this->tbl_as.is_take_down", "is_take_down", 0);
		
		// $this->db->select_as("$this->tbl_as.take_down_date", "take_down_date", 0);
		// $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "address2", 0);
		// $this->db->select_as($this->__decrypt("$this->tbl6_as.email"), "email", 0);
		$this->db->select_as($this->__decrypt("$this->tbl6_as.email"), "reported_post_owner_email", 0);
		// $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "reported_post_owner_address", 0);
		$this->db->select_as("CONCAT($this->tbl_as.kelurahan,', ',$this->tbl_as.kecamatan,', ',$this->tbl_as.kabkota)", "reported_post_owner_address", 0);
		$this->db->select_as("$this->tbl11_as.id", "reporter_id", 0);
		$this->db->select_as("$this->tbl5_as.b_user_id", "b_user_id_reporter", 0);

		$this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl11_as.email").",'-')", 'reporter_user_email', 0);
		$this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl4_as.alamat2").",'-')", 'reporter_user_address', 0);

		$this->db->from($this->tbl5, $this->tbl5_as);
        // $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        // $this->db->join_composite($this->tbl, $this->tbl_as, $this->__joinTbl5(), "left");
        $this->db->join_composite($this->tbl, $this->tbl_as, $this->__joinTbl5(), "left");
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "left");
        $this->db->join_composite($this->tbl11, $this->tbl11_as, $this->__joinTbl11(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl12(), "left");

		$this->db->where("$this->tbl5_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where("$this->tbl5_as.admin_name", $userId, "AND", "=", 0, 0);
		
		// START by Muhammad Sofi 16 February 2022 14:32 | change filter start date to end date
		if (strlen($fromDate)==10 && strlen($toDate)==10) {
			$this->db->between("DATE($this->tbl5_as.cdate)", "DATE('$fromDate')", "DATE('$toDate')");
		} else if (strlen($fromDate)==10 && strlen($toDate)!=10) {
			$this->db->where_as("DATE($this->tbl5_as.cdate)", "DATE('$fromDate')", 'AND', '>=');
		} else if (strlen($fromDate)!=10 && strlen($toDate)==10) {
			$this->db->where_as("DATE($this->tbl5_as.cdate)", "DATE('$toDate')", 'AND', '<=');
		}
		// END by Muhammad Sofi 16 February 2022 14:32 | change filter start date to end date
		//$this->db->where("$this->tbl_as.is_report", 1, "OR", "=", 0, 0);
		//$this->db->where("$this->tbl_as.is_take_down", 1, "AND", "=", 0, 0);

		// $this->db->where_as("$this->tbl_as.is_report", $this->db->esc('1'));
		// if(mb_strlen($keyword)>1){
		// 	$this->db->where("title",$keyword,"OR","%like%",1,0);
		// 	$this->db->where("deskripsi",$keyword,"OR","%like%",0,1);
		// }
		
		// by Muhammad Sofi 27 December 2021 19:00 | bug fix in where clause is ambiguous	
		if(mb_strlen($keyword)>0){
			$this->db->where_as("$this->tbl_as.title",addslashes($keyword),"OR","%like%",1,0);
			$this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl11_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
			$this->db->where_as("$this->tbl_as.deskripsi",addslashes($keyword),"OR","%like%",0,1);
		}

		$this->db->group_by("CONCAT($this->tbl5_as.c_community_id,'-',$this->tbl5_as.b_user_id)");

		// $this->db->order_by("total_reported_post",$sortDir)->limit($page,$pagesize);
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		$this->db->limit($page,$pagesize);
		return $this->db->get("object",0);
	}

	public function getAllBy($admin_name, $fromDate="", $toDate="")
	{
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate DESC)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		$this->db->select_as("$this->tbl5_as.c_community_id", "c_community_id", 0); // comm_report_comm_id
		$this->db->select_as("$this->tbl_as.b_user_id", "b_user_id", 0); // comm_b_user_id
		$this->db->select_as("$this->tbl_as.is_take_down", "check_takedown", 0);
		$this->db->select_as("$this->tbl5_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl_as.title", "title", 0);
		$this->db->select_as("$this->tbl_as.deskripsi", "deskripsi", 0);
		$this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl6_as.fnama").",'-')", 'reported_post_owner', 0);
		$this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl11_as.fnama").",'-')", 'reporter_user_name', 0);
		$this->db->select_as("COALESCE($this->tbl5_as.admin_name, '0')", "admin_name", 0);

		$this->db->select_as("$this->tbl5_as.is_active", "is_active", 0);

		$this->db->select_as("COUNT($this->tbl5_as.c_community_id)", "total_reported_post", 0);
		
		$this->db->select_as("$this->tbl_as.is_report", "is_report", 0);
		$this->db->select_as("$this->tbl_as.is_take_down", "is_take_down", 0);
		
		$this->db->select_as($this->__decrypt("$this->tbl6_as.email"), "reported_post_owner_email", 0);
		$this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "reported_post_owner_address", 0);
		$this->db->select_as("$this->tbl11_as.id", "reporter_id", 0);
		$this->db->select_as("$this->tbl5_as.b_user_id", "b_user_id_reporter", 0);

		$this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl11_as.email").",'-')", 'reporter_user_email', 0);
		$this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl4_as.alamat2").",'-')", 'reporter_user_address', 0);

		$this->db->from($this->tbl5, $this->tbl5_as);
  
		$this->db->join_composite($this->tbl, $this->tbl_as, $this->__joinTbl5(), "left");
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "left");
        $this->db->join_composite($this->tbl11, $this->tbl11_as, $this->__joinTbl11(), "left");
        $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl12(), "left");

		$this->db->where("$this->tbl5_as.nation_code", 62, "AND", "=", 0, 0);
		$this->db->where("$this->tbl5_as.admin_name", $admin_name, "AND", "=", 0, 0);
		
		if (strlen($fromDate)==10 && strlen($toDate)==10) {
			$this->db->between("DATE($this->tbl5_as.cdate)", "DATE('$fromDate')", "DATE('$toDate')");
		} else if (strlen($fromDate)==10 && strlen($toDate)!=10) {
			$this->db->where_as("DATE($this->tbl5_as.cdate)", "DATE('$fromDate')", 'AND', '>=');
		} else if (strlen($fromDate)!=10 && strlen($toDate)==10) {
			$this->db->where_as("DATE($this->tbl5_as.cdate)", "DATE('$toDate')", 'AND', '<=');
		}

		$this->db->group_by("CONCAT($this->tbl5_as.c_community_id,'-',$this->tbl5_as.b_user_id)");

		return $this->db->get("object",0);
	}

	public function countReportedPost($nation_code, $keyword="", $fromDate="", $toDate="", $userId="", $statusFilter=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(DISTINCT CONCAT($this->tbl5_as.c_community_id, '-', $this->tbl5_as.b_user_id))","jumlah",0);
		$this->db->from($this->tbl5, $this->tbl5_as);
		$this->db->join_composite($this->tbl, $this->tbl_as, $this->__joinTbl7(), "inner");
		$this->db->join_composite($this->tbl11, $this->tbl11_as, $this->__joinTbl11(), "left");
		// if($fromDate && $fromDate!=="") $this->db->where("$this->tbl_as.cdate",$fromDate,"AND",">=",1,1);
		// if($toDate && $toDate!=="") $this->db->where("$this->tbl_as.cdate",$toDate,"AND","<=",1,1);
		// if($userId && $userId!=="") $this->db->where("$this->tbl_as.b_user_id",$userId,"AND","=",1,1);
	
		// switch ($statusFilter) {
		// 	case "active":
		// 		$this->db->where("$this->tbl_as.is_active","1","AND","=",1,1);
		// 	break;
		// 	case "inactive":
		// 		$this->db->where("$this->tbl_as.is_active","1","AND","<>",1,1);
		// 	break;
		// 	case "reported":
		// 		$this->db->where("$this->tbl_as.is_report","1","AND",">=",1,1);
		// 	break;
		// 	case "takedown":
		// 		$this->db->where("$this->tbl_as.is_take_down","1","AND",">=",1,1);
		// 	break;
			
		// 	default:
		// 		$this->db->where("$this->tbl_as.is_active","1","AND","=",1,1);
		// 	break;
		// }

		$this->db->where_as("$this->tbl5_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where("$this->tbl5_as.admin_name", $userId, "AND", "=", 0, 0);
		
		// START by Muhammad Sofi 16 February 2022 14:32 | change filter start date to end date
		if (strlen($fromDate)==10 && strlen($toDate)==10) {
			$this->db->between("DATE($this->tbl5_as.cdate)", "DATE('$fromDate')", "DATE('$toDate')");
		} else if (strlen($fromDate)==10 && strlen($toDate)!=10) {
			$this->db->where_as("DATE($this->tbl5_as.cdate)", "DATE('$fromDate')", 'AND', '>=');
		} else if (strlen($fromDate)!=10 && strlen($toDate)==10) {
			$this->db->where_as("DATE($this->tbl5_as.cdate)", "DATE('$toDate')", 'AND', '<=');
		}
		// END by Muhammad Sofi 16 February 2022 14:32 | change filter start date to end date

		// $this->db->where_as("$this->tbl_as.is_report", $this->db->esc('0'));
		// $this->db->where_as("$this->tbl5_as.is_active", $this->db->esc('0'));

		if(mb_strlen($keyword)>0){
			$this->db->where_as("$this->tbl_as.title",addslashes($keyword),"OR","%like%",1,0);
			$this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl11_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 0, 0);
			$this->db->where_as("$this->tbl_as.deskripsi",addslashes($keyword),"OR","%like%",0,1);
		}
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
	// END by Muhammad Sofi 14 January 2022 18:09 | change query to get reported community post data

	public function getById($nation_code, $id){
		$this->db->select_as("$this->tbl_as.*, $this->tbl_as.id", "id", 0);
        $this->db->select_as("$this->tbl_as.kelurahan", "kelurahan");

		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code",$nation_code);
		$this->db->where_as("$this->tbl_as.id",$this->db->esc($id));
		return $this->db->get_first();
	}

	public function set($di){
		if(!is_array($di)) return 0;
		return $this->db->insert($this->tbl,$di,0,0);
	}

	public function update($nation_code, $id, $du){
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

	public function updateTotalDiscussion($nation_code, $id, $plusormin, $total)
    {
        return $this->db->exec("UPDATE `$this->tbl` SET total_discussion = IF('$plusormin' = '+', total_discussion $plusormin $total, IF(total_discussion <= 0,0,total_discussion $plusormin $total)) WHERE nation_code = '$nation_code' AND id = '$id';");
    }

	// START by Muhammad Sofi 14 January 2022 18:09 | change query to get reported community post data
	public function updateStatusIgnore($nation_code, $c_community_id, $du) {
		if(!is_array($du)) return 0;
		$this->db->where("$this->tbl.nation_code", $nation_code);
		$this->db->where("$this->tbl.id", $c_community_id);
    	return $this->db->update($this->tbl, $du, 0);
	}

	public function updateStatusTakedown($nation_code, $c_community_id, $du) {
		if(!is_array($du)) return 0;
		$this->db->where("$this->tbl.nation_code", $nation_code);
		$this->db->where("$this->tbl.id", $c_community_id);
    	return $this->db->update($this->tbl, $du, 0);
	}

	public function updateStatusTakedownReport($nation_code, $c_community_id, $di) {
		if(!is_array($di)) return 0;
		$this->db->where("$this->tbl5.nation_code", $nation_code);
		$this->db->where("$this->tbl5.c_community_id", $c_community_id);
    	return $this->db->update($this->tbl5, $di, 0);
	}
	// END by Muhammad Sofi 14 January 2022 18:09 | change query to get reported community post data

	// START by Muhammad Sofi 14 January 2022 18:09 | move function to get function data reported discussion to community/listing
	public function getReportedDiscussion($nation_code, $page=0, $pagesize=10, $sortCol="sku", $sortDir="ASC", $keyword=""){
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl8_as.id", "id", 0);
		$this->db->select_as("$this->tbl8_as.c_community_discussion_id", "c_community_discussion_id", 0);
		$this->db->select_as("$this->tbl8_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl7_as.text", "text", 0);
		$this->db->select_as($this->__decrypt("$this->tbl9_as.fnama"), "user", 0);
		$this->db->select_as("$this->tbl8_as.is_active", "is_active", 0);
		$this->db->select_as("$this->tbl7_as.is_report", "is_report", 0);
		$this->db->select_as("$this->tbl7_as.is_take_down", "is_take_down", 0);
		$this->db->select_as($this->__decrypt("$this->tbl7_as.alamat2"), "address2", 0);

		$this->db->from($this->tbl8, $this->tbl8_as);
		$this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl8(), "inner");
        $this->db->join_composite($this->tbl9, $this->tbl9_as, $this->__joinTbl9(), "inner");

		$this->db->where_as("$this->tbl8_as.nation_code", $nation_code, "AND", "=", 0, 0);
		if(mb_strlen($keyword)>0){
			$this->db->where("$this->tbl7_as.text", $keyword, "OR", "%like%", 1, 1);
		}
		$this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
		return $this->db->get("object",0);
	}

	public function countReportedDiscussion($nation_code, $keyword=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl8, $this->tbl8_as);
		$this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl8(), "inner");
		$this->db->where_as("$this->tbl8_as.nation_code", $nation_code, "AND", "=", 0, 0);

		if(mb_strlen($keyword)>0){
			$this->db->where("$this->tbl7_as.text", $keyword, "OR", "%like%", 1, 1);
		}

		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function updateStatusDiscussionIgnore($nation_code, $c_community_discussion_id, $du) {
		if(!is_array($du)) return 0;
		$this->db->where("$this->tbl7.nation_code", $nation_code);
		$this->db->where("$this->tbl7.id", $c_community_discussion_id);
    	return $this->db->update($this->tbl7, $du, 0);
	}

	public function updateStatusDiscussionTakedown($nation_code, $c_community_discussion_id, $du) {
		if(!is_array($du)) return 0;
		$this->db->where("$this->tbl7.nation_code", $nation_code);
		$this->db->where("$this->tbl7.id", $c_community_discussion_id);
    	return $this->db->update($this->tbl7, $du, 0);
	}
	// END by Muhammad Sofi 14 January 2022 18:09 | move function to get function data reported discussion to community/listing

	// by Muhammad Sofi 26 January 2022 13:37 | get data user(b_user_id) from table c_community
	public function getCustomer($nation_code, $keyword="", $is_active="") {
		$this->db->flushQuery();
		$this->db->select_as("DISTINCT $this->tbl_as.b_user_id", "user_id", 0);
		$this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "user_name", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);

		if (mb_strlen($is_active)) {
            $this->db->where_as("$this->tbl_as.is_active", $this->db->esc($is_active), "AND", "=", 0, 0);
        }

        if (mb_strlen($keyword)>0) {
			// by Muhammad Sofi 10 February 2022 15:31 | fix error when search user
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl3_as.fnama").'USING "utf8" ) ', addslashes($keyword), "OR", "%like%", 1, 1);
        }

        return $this->db->get("object", 0);
	}

	public function countTotalVideoCommunity($nation_code) {
        $this->db->select_as("COUNT(*)", 'total', 0);
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl10, $this->tbl10_as, $this->__joinTbl10(), 'left');
        $this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl10_as.jenis", $this->db->esc("video"), "AND", "=", 0, 0);
        // $this->db->where_as("$this->tbl10_as.convert_status", $this->db->esc("processed"), "AND", "=", 0, 0); // ignore convert_status
        $this->db->where_as("$this->tbl_as.is_active",  $this->db->esc(1), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl_as.is_published",  $this->db->esc(1), "AND", "=", 0, 0);
        $d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

	public function getByCommunityId($nation_code, $community_id, $getType="all", $type=""){
		$this->db->select_as("$this->tbl_community_attachment_as.id",'id',0);
		$this->db->select_as("$this->tbl_community_attachment_as.jenis",'jenis',0);
		$this->db->select_as("$this->tbl_community_attachment_as.url",'url',0);
		$this->db->select_as("$this->tbl_community_attachment_as.url_thumb",'url_thumb',0);
		$this->db->select_as("$this->tbl_community_attachment_as.convert_status",'convert_status',0);
        $this->db->select_as("COALESCE(".$this->__decrypt("$this->tbl_community_attachment_as.location_address").",'')", "location_address", 0);
		$this->db->from($this->tbl_community_attachment, $this->tbl_community_attachment_as);
		$this->db->where_as("$this->tbl_community_attachment_as.nation_code",$nation_code);
		$this->db->where("$this->tbl_community_attachment_as.c_community_id",$community_id);
		
		if($type != ""){
			$this->db->where("$this->tbl_community_attachment_as.jenis",$type);
		}

		$this->db->where("$this->tbl_community_attachment_as.is_active",1);

		$this->db->order_by("$this->tbl_community_attachment_as.id","ASC");

		if($getType == "first"){
			return $this->db->get_first();
		}else{
			return $this->db->get();
		}
	}

	public function updateByCommunityId($nation_code, $c_community_id, $du){
		$this->db->where_as("nation_code", $nation_code);
		$this->db->where_as("c_community_id", $this->db->esc($c_community_id));
		return $this->db->update($this->tbl_community_attachment, $du,0);
	}
	
	public function countTotalCommunity($nation_code, $b_user_id) {
        $this->db->select_as("COUNT(*)", 'total', 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code", $nation_code);
		$this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
		$d = $this->db->get_first();
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
	}

	public function countAllByUserId($nation_code, $b_user_id)
    {
        $this->db->select_as("COUNT(DISTINCT $this->tbl_as.id)", "total", 0);
        
        $this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), 'left');

        $this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
        $this->db->where_as("$this->tbl_as.b_user_id", $this->db->esc($b_user_id));
        $this->db->where_as("$this->tbl_as.is_published", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl3_as.is_active", $this->db->esc(1));
        $this->db->where_as("$this->tbl_as.is_take_down", $this->db->esc('0'));

        $d = $this->db->get_first('object', 0);
        if (isset($d->total)) {
            return $d->total;
        }
        return 0;
    }

	public function getUserIdReporterByCommunityId($nation_code, $c_community_id)
    {
		$this->db->select_as("$this->tbl5_as.b_user_id", "b_user_id", 0);

		$this->db->from($this->tbl5, $this->tbl5_as);
		$this->db->where_as("$this->tbl5_as.nation_code", $this->db->esc($nation_code));
		$this->db->where_as("$this->tbl5_as.c_community_id", $this->db->esc($c_community_id));
		return $this->db->get_first();
    }

	public function getImageThumbnail($nation_code, $c_community_id)
    {
		$this->db->select_as("COALESCE($this->tbl10_as.url_thumb, '')", "image_thumb", 0);
		// $this->db->select_as("$this->tbl10_as.url_thumb", "image_thumb", 0);

		$this->db->from($this->tbl10, $this->tbl10_as);
		$this->db->where_as("$this->tbl10_as.nation_code", $this->db->esc($nation_code));
		$this->db->where_as("$this->tbl10_as.c_community_id", $this->db->esc($c_community_id));
		// $this->db->where_as("$this->tbl10_as.jenis", $this->db->esc("image"));
		// $this->db->where_as("$this->tbl10_as.convert_status", $this->db->esc("uploading"), "AND", "!=", 0, 0);
		return $this->db->get_first('object', 0);
    }


}
