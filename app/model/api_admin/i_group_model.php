<?php
class I_Group_Model extends SENE_Model {
	var $tbl = 'i_group';
	var $tbl_as = 'ig';
    var $tbl2 = 'i_group_category';
	var $tbl2_as = 'igc';
	var $tbl3 = 'b_user';
	var $tbl3_as = 'bu';
	var $tbl4 = 'i_group_post';
	var $tbl4_as = 'igp';
	var $tbl5 = 'i_group_participant';
	var $tbl5_as = 'igpc';
	var $tbl6 = 'i_group_post_attachment';
	var $tbl6_as = 'igpa';
	var $tbl7 = 'i_group_post_attachment_attendance_sheet';
	var $tbl7_as = 'igpas';
	var $tbl8 = 'i_group_post_attachment_attendance_sheet_member';
	var $tbl8_as = 'igpasm';

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

	public function getTableAlias4(){
		return $this->tbl3_as;
	}

	public function getTableAlias5(){
		return $this->tbl3_as;
	}

    private function __joinTbl2()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.i_group_category_id", "=", "$this->tbl2_as.id");
        return $cps;
    }

    private function __joinTbl3()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl3_as.id");
        return $cps;
    }

	private function __joinTbl3_with_post()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl4_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl4_as.b_user_id", "=", "$this->tbl3_as.id");
        return $cps;
    }

	private function __joinTbl3_with_participant()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl5_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl5_as.b_user_id", "=", "$this->tbl3_as.id");
        return $cps;
    }

	private function __joinTbl3_with_sheet_member()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl8_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl8_as.b_user_id", "=", "$this->tbl3_as.id");
        return $cps;
    }

    private function __joinTbl4()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl4_as.id");
        return $cps;
    }

	private function __joinTbl5()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl5_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl5_as.id");
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
		// $this->db->where("nation_code",$nation_code);
		$this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
		$d = $this->db->get_first('', 0);
		if(isset($d->last_id)) return $d->last_id;
		return 0;
	}
	
	// by Muhammad Sofi 28 December 2021 10:10 | improvement query
	// public function getAll($nation_code, $page=0,$pagesize=10,$sortCol="sku",$sortDir="ASC",$keyword="",$utype_in=array(),$edate=""){
	// 	$this->db->flushQuery();
	// 	$this->db->select('id');
	// 	$this->db->select('image_icon');
	// 	$this->db->select('nama');
	// 	$this->db->select('is_fashion');
	// 	$this->db->select('is_active');
	// 	$this->db->from($this->tbl,$this->tbl_as);
	// 	$this->db->where("nation_code",$nation_code,"AND","=",0,0);
	// 	$this->db->where('parent_b_kategori_id', "IS NULL"); // by Muhammad Sofi 23 December 2021 10:00 | don't show category automotive in category product
	// 	$this->db->where("$this->tbl_as.utype", "brand", "AND", "!=", 1, 1); 
	// 	if(is_array($utype_in)) if(count($utype_in)) $this->db->where_in('utype',$utype_in);
	// 	if(mb_strlen($keyword)>1){
	// 		$this->db->where("nama",$keyword,"OR","%like%",1,0);
	// 		$this->db->where("deskripsi",$keyword,"OR","%like%",0,1);
	// 	}
	// 	$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
	// 	return $this->db->get("object",0);
	// }

	public function getAll($nation_code, $page=0, $pagesize=10, $sortCol="cdate", $sortDir="", $keyword="", $utype_in=array()) {
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.image", "image", 0);
		$this->db->select_as("$this->tbl_as.name", "name", 0);
		$this->db->select_as("$this->tbl2_as.nama", "category_name", 0);
		$this->db->select_as("$this->tbl_as.group_type", "group_type", 0);
		$this->db->select_as("$this->tbl_as.total_people", "total_people", 0);
        $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "creator", 0);
		$this->db->select_as("(SELECT igp.cdate FROM i_group_post igp WHERE igp.i_group_id = $this->tbl_as.id ORDER BY igp.cdate DESC LIMIT 1 )", "last_post_date", 0);
		$this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->select_as("$this->tbl_as.is_report", "is_report", 0);
		$this->db->select_as("$this->tbl_as.report_date", "report_date", 0);
		$this->db->select_as("$this->tbl_as.is_take_down", "is_take_down", 0);
		$this->db->select_as("$this->tbl_as.take_down_date", "take_down_date", 0);
		$this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("1", "1", 'or', '<>', 1, 0);
		$this->db->where_as("$this->tbl_as.is_report",1,"OR","<>",0,0);
		$this->db->where_as("$this->tbl_as.is_report",1,"AND","=",1,0);
		$this->db->where_as("$this->tbl_as.is_take_down",1,"OR","=",0,1);
		$this->db->where_as("1", "1", 'and', '<>', 0, 1);
		if(mb_strlen($keyword)>0) {
			$this->db->where_as("$this->tbl_as.name", addslashes($keyword), "OR", "%like%", 0, 0);
		}
		// $this->db->order_by("$this->tbl_as.prioritas", "ASC")->limit($page,$pagesize);
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object", 0);
	}

	public function countAll($nation_code, $keyword="") {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
		$this->db->where_as("$this->tbl_as.nation_code",$nation_code,"AND","=",0,0);
		$this->db->where_as("1", "1", 'or', '<>', 1, 0);
		$this->db->where_as("$this->tbl_as.is_report",1,"OR","<>",0,0);
		$this->db->where_as("$this->tbl_as.is_report",1,"AND","=",1,0);
		$this->db->where_as("$this->tbl_as.is_take_down",1,"OR","=",0,1);
		$this->db->where_as("1", "1", 'and', '<>', 0, 1);
		if(mb_strlen($keyword)>0) {
			$this->db->where_as("$this->tbl_as.name", addslashes($keyword), "OR", "%like%", 0, 0);
		}
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function getById($nation_code, $id) {
		$this->db->where("nation_code", $nation_code);
		$this->db->where("id", $id);
		return $this->db->get_first();
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

	public function getByIds($nation_code, $id, $page=0, $pagesize=10, $sortCol="", $sortDir="DESC", $keyword="")
    {
        $this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate DESC)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		$this->db->select_as("$this->tbl4_as.id", "id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "user", 0);
		$this->db->select_as("$this->tbl4_as.deskripsi", "deskripsi", 0);
		$this->db->select_as("'-'", "url_thumb", 0);
		$this->db->select_as("$this->tbl4_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl4_as.is_active", "is_active", 0);
		$this->db->select_as("$this->tbl4_as.is_take_down", "is_take_down", 0);
        $this->db->from($this->tbl4, $this->tbl4_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3_with_post(), "left");
        $this->db->where_as("$this->tbl4_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl4_as.i_group_id", $this->db->esc($id), "AND", "=", 0, 0);
        // $this->db->where_as("$this->tbl4_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
        // $this->db->where_as("$this->tbl4_as.is_report", $this->db->esc(0), "AND", "=", 0, 0);
        if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl4_as.deskripsi", addslashes($keyword), "AND", "%like%", 0, 0);
        }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get('',0);
    }

    public function countAlls($nation_code, $id, $keyword)
    {
        $this->db->flushQuery();
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl4, $this->tbl4_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3_with_post(), "left");
        $this->db->where_as("$this->tbl4_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl4_as.i_group_id", $this->db->esc($id), "AND", "=", 0, 0);
        // $this->db->where_as("$this->tbl4_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
        // $this->db->where_as("$this->tbl4_as.is_report", $this->db->esc(0), "AND", "=", 0, 0);
        if (strlen($keyword)>0) {
            $this->db->where_as("$this->tbl4_as.deskripsi", addslashes($keyword), "AND", "%like%", 0, 0);
        }
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }

	public function getById_post($nation_code, $id) {
		$this->db->from($this->tbl4, $this->tbl4_as);
		$this->db->where("nation_code", $nation_code);
		$this->db->where("id", $id);
		return $this->db->get_first();
	}

	public function update_post($nation_code, $id, $du) {
		if(!is_array($du)) return 0;
		$this->db->where("nation_code", $nation_code);
		$this->db->where("id", $id);
    	return $this->db->update($this->tbl4, $du, 0);
	}

	// ===== GROUP REPORT ===================
	public function getAllReport($nation_code, $page=0, $pagesize=10, $sortCol="cdate", $sortDir="", $keyword="", $utype_in=array()) {
		$this->db->flushQuery();
		$this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.image", "image", 0);
		$this->db->select_as("$this->tbl_as.name", "name", 0);
		$this->db->select_as("$this->tbl2_as.nama", "category_name", 0);
		$this->db->select_as("$this->tbl_as.group_type", "group_type", 0);
		$this->db->select_as("$this->tbl_as.total_people", "total_people", 0);
        $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "creator", 0);
		$this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->select_as("$this->tbl_as.is_report", "is_report", 0);
		$this->db->select_as("$this->tbl_as.report_date", "report_date", 0);
		$this->db->select_as("$this->tbl_as.is_take_down", "is_take_down", 0);
		$this->db->select_as("$this->tbl_as.take_down_date", "take_down_date", 0);
		$this->db->select_as($this->__decrypt("$this->tbl3_as.telp"), "telp", 0);
		$this->db->select_as($this->__decrypt("$this->tbl3_as.email"), "email", 0);
		$this->db->select_as("$this->tbl_as.verif_telp_manual", "verif_telp_manual", 0);
		$this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("$this->tbl_as.is_active",1,"AND","=",0,0);
		$this->db->where_as("$this->tbl_as.is_report",1,"AND","=",0,0);
		if(mb_strlen($keyword)>0) {
			$this->db->where_as("$this->tbl_as.name", addslashes($keyword), "OR", "%like%", 0, 0);
		}
		// $this->db->order_by("$this->tbl_as.prioritas", "ASC")->limit($page,$pagesize);
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object", 0);
	}

	public function countAllReport($nation_code, $keyword="") {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
		$this->db->where_as("$this->tbl_as.nation_code",$nation_code,"AND","=",0,0);
		$this->db->where_as("$this->tbl_as.is_active",1,"AND","=",0,0);
		$this->db->where_as("$this->tbl_as.is_report",1,"AND","=",0,0);
		if(mb_strlen($keyword)>0) {
			$this->db->where_as("$this->tbl_as.name", addslashes($keyword), "OR", "%like%", 0, 0);
		}
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
	// ==== END GROUP REPORT =======


	// ===== PARTICIPANT GROUP ======
	public function getParticipantByGroupID($nation_code, $id, $page=0, $pagesize=10, $sortCol="", $sortDir="DESC", $keyword="")
    {
        $this->db->select_as("ROW_NUMBER() OVER (ORDER BY user asc)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		$this->db->select_as("$this->tbl5_as.b_user_id", "user_id", 0);
        $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "user", 0);		
		$this->db->select_as("$this->tbl5_as.is_owner", "is_owner", 0);
		$this->db->select_as("$this->tbl5_as.is_co_admin", "is_co_admin", 0);
		$this->db->select_as("$this->tbl5_as.cdate", "join_date", 0);
		// $this->db->select_as("$this->tbl4_as.deskripsi", "deskripsi", 0);
		// $this->db->select_as("$this->tbl4_as.cdate", "cdate", 0);
		// $this->db->select_as("$this->tbl4_as.is_active", "is_active", 0);
        $this->db->from($this->tbl5, $this->tbl5_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3_with_participant(), "left");
        $this->db->where_as("$this->tbl5_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl5_as.i_group_id", $this->db->esc($id), "AND", "=", 0, 0);
        // $this->db->where_as("$this->tbl4_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
        // $this->db->where_as("$this->tbl4_as.is_report", $this->db->esc(0), "AND", "=", 0, 0);
        if (strlen($keyword)>0) {
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl3_as.fnama").'USING "utf8" ) ', addslashes($keyword), "AND", "%like%", 0, 0);
        }
        $this->db->order_by($sortCol, $sortDir)->limit($page, $pagesize);
        return $this->db->get('',0);
    }

    public function countParticipantByGroupID($nation_code, $id, $keyword)
    {
        $this->db->flushQuery();
        $this->db->select_as("COUNT(*)", "jumlah", 0);
        $this->db->from($this->tbl5, $this->tbl5_as);
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3_with_participant(), "left");
        $this->db->where_as("$this->tbl5_as.nation_code", $this->db->esc($nation_code), "AND", "=", 0, 0);
        $this->db->where_as("$this->tbl5_as.i_group_id", $this->db->esc($id), "AND", "=", 0, 0);
        // $this->db->where_as("$this->tbl5_as.is_active", $this->db->esc(1), "AND", "=", 0, 0);
        // $this->db->where_as("$this->tbl5_as.is_report", $this->db->esc(0), "AND", "=", 0, 0);
        if (strlen($keyword)>0) {
            $this->db->where_as('CONVERT('.$this->__decrypt("$this->tbl3_as.fnama").'USING "utf8" ) ', addslashes($keyword), "AND", "%like%", 0, 0);
        }
        $d = $this->db->get_first("object", 0);
        if (isset($d->jumlah)) {
            return $d->jumlah;
        }
        return 0;
    }
	// ===== END PARTICIPANT GROUP =====


	// ===== POST DETAIL ===============
	public function getByPostId_postAttachment($nation_code, $id) {
		$this->db->from($this->tbl6, $this->tbl6_as);
		$this->db->where("nation_code", $nation_code);
		$this->db->where("i_group_post_id", $id);
		return $this->db->get('object',0);
	}
	public function getByPostAttachmentId_postAttachmentAttendanceSheet($nation_code, $id) {
		$this->db->from($this->tbl7, $this->tbl7_as);
		$this->db->where("nation_code", $nation_code);
		$this->db->where("i_group_post_attachment_id", $id);
		return $this->db->get_first();
	}
	// public function getByPostAttachmentId_postAttachmentAttendanceSheetMember($nation_code, $id) {
	// 	$this->db->from($this->tbl8, $this->tbl8_as);
	// 	$this->db->where("nation_code", $nation_code);
	// 	$this->db->where("i_group_post_attachment_attendance_sheet_id", $id);
	// 	return $this->db->get_first();
	// }
	public function getByPostAttachmentId_postAttachmentAttendanceSheetMember($nation_code, $id) {
		$this->db->select_as("$this->tbl8_as.id", "id", 0);
		$this->db->select_as("$this->tbl8_as.jenis", "jenis", 0);
		$this->db->select_as("$this->tbl8_as.present_or_absent", "present_or_absent", 0);
		$this->db->select_as("$this->tbl8_as.b_user_id", "user_id", 0);
		$this->db->select_as("$this->tbl8_as.cdate", "cdate", 0);
        $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "user", 0);
		$this->db->from($this->tbl8, $this->tbl8_as);
		$this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3_with_sheet_member(), "left");
		$this->db->where("$this->tbl8_as.nation_code", $nation_code);
		$this->db->where("i_group_post_attachment_attendance_sheet_id", $id);
		return $this->db->get('',0);
	}

	public function getImageByPostId($nation_code, $id) {
		$this->db->select_as("COALESCE($this->tbl6_as.url_thumb, '')", "url_thumb", 0);
		$this->db->from($this->tbl6, $this->tbl6_as);
		$this->db->where_as("$this->tbl6_as.nation_code", $this->db->esc($nation_code));
		$this->db->where_as("$this->tbl6_as.i_group_post_id", $this->db->esc($id));
		$this->db->where_as("$this->tbl6_as.jenis", $this->db->esc('image'));
		return $this->db->get_first('object', 0);
	}
	// ===== END POST DETAIL ===============

	public function getJoinedClubByUserId($nation_code, $user_id) {
		$this->db->select_as("$this->tbl5_as.i_group_id", "i_group_id", 0);
		$this->db->select_as("''", "club_name", 0);
		$this->db->select_as("''", "count_total_post", 0);
		$this->db->from($this->tbl5, $this->tbl5_as);
		$this->db->where_as("$this->tbl5_as.nation_code", $this->db->esc($nation_code));
		$this->db->where_as("$this->tbl5_as.b_user_id", $this->db->esc($user_id));
		return $this->db->get('', 0);
	}

	public function countTotalPostHeJoinedInClub($nation_code, $group_id, $user_id) {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl4, $this->tbl4_as);
		$this->db->where_as("$this->tbl4_as.nation_code",$this->db->esc($nation_code),"AND","=",0,0);
		$this->db->where_as("$this->tbl4_as.i_group_id",$this->db->esc($group_id),"AND","=",0,0);
		$this->db->where_as("$this->tbl4_as.b_user_id",$this->db->esc($user_id),"AND","=",0,0);
		$d = $this->db->get_first("object", 0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function getByIdByWith($nation_code, $id) {
		$this->db->select_as("COALESCE($this->tbl_as.name, '')", "name", 0);
		$this->db->from($this->tbl, $this->tbl_as);
		$this->db->where_as("$this->tbl_as.nation_code", $this->db->esc($nation_code));
		$this->db->where_as("$this->tbl_as.id", $this->db->esc($id));
		return $this->db->get_first('object', 0);
	}

	public function getAllPopularClub($nation_code, $page=0, $pagesize=10, $sortCol="cdate", $sortDir="", $keyword="", $condition="") {
		$this->db->flushQuery();
		// $this->db->select_as("ROW_NUMBER() OVER (ORDER BY cdate)", "no"); // show row number | refer to https://www.webcodeexpert.com/2018/09/sql-server-generate-row-numberserial.html 
		$this->db->select_as("$this->tbl_as.id", "no", 0);
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.image", "image", 0);
		$this->db->select_as("$this->tbl_as.name", "name", 0);
		$this->db->select_as("$this->tbl2_as.nama", "category_name", 0);
		$this->db->select_as("$this->tbl_as.group_type", "group_type", 0);
		$this->db->select_as("$this->tbl_as.total_people", "total_people", 0);
        $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "creator", 0);
		$this->db->select_as("(SELECT igp.cdate FROM i_group_post igp WHERE igp.i_group_id = $this->tbl_as.id ORDER BY igp.cdate DESC LIMIT 1 )", "last_post_date", 0);
		$this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);
		$this->db->select_as("$this->tbl_as.is_report", "is_report", 0);
		$this->db->select_as("$this->tbl_as.report_date", "report_date", 0);
		$this->db->select_as("$this->tbl_as.is_take_down", "is_take_down", 0);
		$this->db->select_as("$this->tbl_as.take_down_date", "take_down_date", 0);
		$this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
		$this->db->where_as("$this->tbl_as.nation_code", $nation_code, "AND", "=", 0, 0);
		$this->db->where_as("1", "1", 'or', '<>', 1, 0);
		$this->db->where_as("$this->tbl_as.is_report",1,"OR","<>",0,0);
		$this->db->where_as("$this->tbl_as.is_report",1,"AND","=",1,0);
		$this->db->where_as("$this->tbl_as.is_take_down",1,"OR","=",0,1);
		$this->db->where_as("1", "1", 'and', '<>', 0, 1);
		if(mb_strlen($keyword)>0) {
			$this->db->where_as("$this->tbl_as.name", addslashes($keyword), "OR", "%like%", 0, 0);
		}

		if($condition == "popular"){
            $this->db->order_by("$this->tbl_as.total_people", "DESC")->limit($page, $pagesize);
        } else {}
		return $this->db->get("object", 0);
	}

	public function countAllPopularClub($nation_code, $keyword="", $condition="") {
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl, $this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "left");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "left");
		$this->db->where_as("$this->tbl_as.nation_code",$nation_code,"AND","=",0,0);
		$this->db->where_as("1", "1", 'or', '<>', 1, 0);
		$this->db->where_as("$this->tbl_as.is_report",1,"OR","<>",0,0);
		$this->db->where_as("$this->tbl_as.is_report",1,"AND","=",1,0);
		$this->db->where_as("$this->tbl_as.is_take_down",1,"OR","=",0,1);
		$this->db->where_as("1", "1", 'and', '<>', 0, 1);
		if(mb_strlen($keyword)>0) {
			$this->db->where_as("$this->tbl_as.name", addslashes($keyword), "OR", "%like%", 0, 0);
		}
		// if($condition == "popular"){
        //     $this->db->order_by("$this->tbl_as.total_people", "DESC");
        // } else {}
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}
}