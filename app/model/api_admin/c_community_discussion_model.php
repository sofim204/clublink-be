<?php
class C_Community_Discussion_Model extends SENE_Model{
	var $tbl = 'c_community_discussion';
	var $tbl_as = 'comm_discussion';
	var $tbl2 = 'c_community';
	var $tbl2_as = 'comm';
	var $tbl3 = 'b_user';
	var $tbl3_as = 'comm_user';
	var $tbl4 = 'b_user_alamat';
	var $tbl4_as = 'user_address';
	var $tbl5 = 'c_community';
	var $tbl5_as = 'parent_community';
	var $tbl6 = 'c_community_category';
	var $tbl6_as = 'comm_category';
	var $tbl7 = 'c_community_discussion_report';
	var $tbl7_as = 'discuss_report';
	var $tbl8 = 'b_user';
	var $tbl8_as = 'report_user';

	public function __construct(){
		parent::__construct();
		$this->db->from($this->tbl,$this->tbl_as);
	}

    private function __joinTbl2()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl2_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.c_community_id", "=", "$this->tbl2_as.id");
        return $cps;
    }

    private function __joinTbl3()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl3_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.b_user_id", "=", "$this->tbl3_as.id");
        return $cps;
    }

	// by Muhammad Sofi - 12 November 2021 21:40 | not used
    // private function __joinTbl4()
    // {
    //     $cps = array();
    //     $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl4_as.nation_code");
    //     $cps[] = $this->db->composite_create("$this->tbl_as.b_user_alamat_id", "=", "$this->tbl4_as.id");
    //     $cps[] = $this->db->composite_create("$this->tbl3_as.id", "=", "$this->tbl4_as.b_user_id");
    //     return $cps;
    // }

    private function __joinTbl5()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl5_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.parent_c_community_discussion_id", "=", "$this->tbl5_as.id");
        return $cps;
    }

    private function __joinTbl6()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl2_as.nation_code", "=", "$this->tbl6_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl2_as.c_community_category_id", "=", "$this->tbl6_as.id");
        return $cps;
    }

    private function __joinTbl7()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl_as.nation_code", "=", "$this->tbl7_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl_as.id", "=", "$this->tbl7_as.c_community_discussion_id");
        return $cps;
    }

    private function __joinTbl8()
    {
        $cps = array();
        $cps[] = $this->db->composite_create("$this->tbl7_as.nation_code", "=", "$this->tbl8_as.nation_code");
        $cps[] = $this->db->composite_create("$this->tbl7_as.b_user_id", "=", "$this->tbl8_as.id");
        return $cps;
    }

	public function getTableAlias(){
		return $this->tbl_as;
	}

	public function getTableAlias2(){
		return $this->tbl2_as;
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

	public function getAll($nation_code, $page=0,$pagesize=10,$sortCol="sku",$sortDir="ASC",$keyword="",$utype_in=array(),$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.text", "title", 0);
		$this->db->select_as("$this->tbl_as.c_community_id", "community", 0);
        $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "user", 0);
		$this->db->select_as("$this->tbl_as.is_take_down", "is_take_down", 0);
		$this->db->select_as("$this->tbl_as.is_report", "is_report", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);

		$this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl_as.edate", "edate", 0);
		$this->db->select_as("$this->tbl2_as.title", "community_title", 0);
		$this->db->select_as("$this->tbl_as.parent_c_community_discussion_id", "parent", 0);
		$this->db->select_as("$this->tbl5_as.title", "parent_title", 0);
		$this->db->select_as("$this->tbl6_as.nama", "category", 0);
		$this->db->select_as("$this->tbl_as.user_type", "user_type", 0);
		// by Muhammad Sofi - 4 November 2021 10:00
        // remark code
        // $this->db->select_as($this->__decrypt("$this->tbl4_as.alamat"), "address", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "address2", 0);
		$this->db->select_as("$this->tbl_as.take_down_date", "take_down_date", 0);
		$this->db->select_as("$this->tbl_as.report_date", "report_date", 0);

		$this->db->from($this->tbl,$this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "inner");
        // $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left"); // by Muhammad Sofi - 12 November 2021 21:40 | not used
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
		$this->db->where("$this->tbl_as.nation_code",$nation_code,"AND","=",0,0);
		if(is_array($utype_in)) if(count($utype_in)) $this->db->where_in('utype',$utype_in);
		if(mb_strlen($keyword)>1){
			$this->db->where("nama",$keyword,"OR","%like%",1,0);
			$this->db->where("deskripsi",$keyword,"OR","%like%",0,1);
		}
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}

	public function getAllById($id, $nation_code, $page=0,$pagesize=10,$sortCol="sku",$sortDir="ASC",$keyword="",$utype_in=array(),$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.cdate", "cdate", 0);
        $this->db->select_as($this->__decrypt("$this->tbl3_as.fnama"), "user", 0);
		$this->db->select_as("$this->tbl_as.text", "title", 0);
		$this->db->select_as("$this->tbl_as.is_active", "is_active", 0);

		$this->db->select_as("$this->tbl_as.is_report", "is_report", 0);
		$this->db->select_as("$this->tbl_as.is_take_down", "is_take_down", 0);
		$this->db->select_as("$this->tbl_as.parent_c_community_discussion_id", "parent_c_community_discussion_id", 0);
		$this->db->select_as("$this->tbl_as.edate", "edate", 0);
		$this->db->select_as("$this->tbl_as.c_community_id", "community", 0);
		$this->db->select_as("$this->tbl2_as.title", "community_title", 0);
		$this->db->select_as("$this->tbl_as.parent_c_community_discussion_id", "parent", 0);
		$this->db->select_as("$this->tbl5_as.title", "parent_title", 0);
		$this->db->select_as("$this->tbl6_as.nama", "category", 0);
		$this->db->select_as("$this->tbl_as.user_type", "user_type", 0);
		// by Muhammad Sofi - 4 November 2021 10:00
        // remark code
        // $this->db->select_as($this->__decrypt("$this->tbl4_as.alamat"), "address", 0);
        $this->db->select_as($this->__decrypt("$this->tbl_as.alamat2"), "address2", 0);
		$this->db->select_as("$this->tbl_as.take_down_date", "take_down_date", 0);
		$this->db->select_as("$this->tbl_as.report_date", "report_date", 0);

		$this->db->from($this->tbl,$this->tbl_as);
        $this->db->join_composite($this->tbl2, $this->tbl2_as, $this->__joinTbl2(), "inner");
        $this->db->join_composite($this->tbl3, $this->tbl3_as, $this->__joinTbl3(), "inner");
        $this->db->join_composite($this->tbl6, $this->tbl6_as, $this->__joinTbl6(), "inner");
        // $this->db->join_composite($this->tbl4, $this->tbl4_as, $this->__joinTbl4(), "left"); // by Muhammad Sofi - 12 November 2021 21:40 | not used
        $this->db->join_composite($this->tbl5, $this->tbl5_as, $this->__joinTbl5(), "left");
		$this->db->where("$this->tbl_as.nation_code",$nation_code,"AND","=",0,0);
		$this->db->where("$this->tbl_as.c_community_id",$id,"AND","=",0,0);
		if(is_array($utype_in)) if(count($utype_in)) $this->db->where_in('utype',$utype_in);
		if(mb_strlen($keyword)>1){
			$this->db->where("nama",$keyword,"OR","%like%",1,0);
			$this->db->where("deskripsi",$keyword,"OR","%like%",0,1);
		}
		$this->db->order_by("$this->tbl_as.cdate","asc")->limit($page,$pagesize);
		return $this->db->get("object",0);
	}

	public function getReported($nation_code, $page=0,$pagesize=10,$sortCol="sku",$sortDir="ASC",$keyword="",$utype_in=array(),$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("$this->tbl_as.id", "id", 0);
		$this->db->select_as("$this->tbl_as.text", "text", 0);
		$this->db->select_as("$this->tbl_as.user_type", "user_type", 0);
        $this->db->select_as($this->__decrypt("$this->tbl8_as.fnama"), "user", 0);
		$this->db->select_as("$this->tbl7_as.is_active", "is_active", 0);
		$this->db->select_as("$this->tbl7_as.cdate", "cdate", 0);
		$this->db->select_as("$this->tbl7_as.edate", "edate", 0);
		$this->db->select_as("$this->tbl_as.is_report", "is_report", 0);
		$this->db->select_as("$this->tbl_as.report_date", "report_date", 0);
		$this->db->select_as("$this->tbl_as.is_take_down", "is_take_down", 0);
		$this->db->select_as("$this->tbl_as.take_down_date", "take_down_date", 0);

		$this->db->from($this->tbl,$this->tbl_as);
        $this->db->join_composite($this->tbl7, $this->tbl7_as, $this->__joinTbl7(), "inner");
        $this->db->join_composite($this->tbl8, $this->tbl8_as, $this->__joinTbl8(), "inner");

		$this->db->where("$this->tbl_as.nation_code",$nation_code,"AND","=",0,0);
		if(is_array($utype_in)) if(count($utype_in)) $this->db->where_in('utype',$utype_in);
		// if(mb_strlen($keyword)>1){
		// 	$this->db->where("title",$keyword,"OR","%like%",1,0);
		// 	$this->db->where("deskripsi",$keyword,"OR","%like%",0,1);
		// }

		// by Muhammad Sofi 27 December 2021 20:27 | bug fix table not show data and filter search by keyword
		if(mb_strlen($keyword)>0){
			$this->db->where("text",$keyword,"OR","%like%",0,0);
		}
		$this->db->order_by($sortCol,$sortDir)->limit($page,$pagesize);
		return $this->db->get("object",0);
	}

	public function countAll($nation_code, $keyword="",$utype_in=array(),$edate=""){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code,"AND","=",0,0);
		if(is_array($utype_in)) if(count($utype_in)) $this->db->where_in('utype',$utype_in);
		// if(mb_strlen($keyword)>1){
		// 	$this->db->where("nama",$keyword,"OR","%like%",1,0);
		// 	$this->db->where("deskripsi",$keyword,"OR","%like%",0,1);
		// }
		
		// by Muhammad Sofi 27 December 2021 20:27 | bug fix table not show data and filter search by keyword
		if(mb_strlen($keyword)>0){
			$this->db->where("text",$keyword,"OR","%like%",0,0);
		}
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

	public function getById($nation_code, $id){
		$this->db->where("nation_code",$nation_code);
		$this->db->where("id",$id);
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

	public function getKategori($nation_code){
		$this->db->select("id")
			->select("utype")
			->select("nama")
			->select("is_active")
			->select("is_visible")
			->select("is_fashion")
			->select_as("COALESCE(parent_b_kategori_id,'-')",'parent_b_kategori_id',0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where("is_active",'1');
		$this->db->where("utype",'kategori',"OR","like",1,0);
		$this->db->where("utype",'kategori_sub',"OR","like",0,0);
		$this->db->where("utype",'kategori_sub_sub',"OR","like",0,1);
		$this->db->order_by("utype","asc");
		$this->db->limit(100);
		return $this->db->get('object',0);
	}

	public function getParentKategori($nation_code){
		$this->db->select();
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code);
		$this->db->where_as("b_kategori_id", "is null");
		$this->db->where("utype",'kategori',"OR","like",1,0);
		$this->db->where("utype",'kategori_sub',"OR","like",0,0);
		$this->db->where("utype",'kategori_sub_sub',"OR","like",0,1);
		$this->db->order_by("prioritas","asc")->order_by("nama","asc");
		$this->db->limit(100);
		return $this->db->get('object',0);
	}

	public function getSubKategori($nation_code, $b_kategori_id){
		$this->db->select();
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where_as("parent_nation_code", $nation_code);
		$this->db->where_as("parent_b_kategori_id", $b_kategori_id);
		$this->db->where("utype",'kategori_sub',"AND","like",0,0);
		$this->db->limit(100);
		return $this->db->get('',0);
	}

	public function countAllChild($nation_code, $parent_discussion_id, $c_community_id){
		$this->db->flushQuery();
		$this->db->select_as("COUNT(*)","jumlah",0);
		$this->db->from($this->tbl,$this->tbl_as);
		$this->db->where("nation_code",$nation_code,"AND","=",0,0);
		$this->db->where("parent_c_community_discussion_id",$parent_discussion_id,"AND","=",0,0);
		$this->db->where("c_community_id",$c_community_id,"AND","=",0,0);
        $this->db->where("is_active", '1');
        $this->db->where("is_take_down", $this->db->esc('0'));
		$d = $this->db->get_first("object",0);
		if(isset($d->jumlah)) return $d->jumlah;
		return 0;
	}

    public function updateByParentCommunityDiscussionId($nation_code, $parent_c_community_discussion_id, $du)
    {
        if (!is_array($du)) {
            return 0;
        }
        $this->db->where("nation_code", $nation_code);
        $this->db->where('parent_c_community_discussion_id', $parent_c_community_discussion_id);
        $this->db->where("is_active", 1);
        return $this->db->update($this->tbl, $du, 0);
    }

}
